<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
    protected $helpers = [];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);
        session()->remove('_permisos_refrescados');

        // Preload any models, libraries, etc, here.
        date_default_timezone_set('America/El_Salvador');

        // 2) Forzar timezone en la conexión MySQL a -06:00 (más fiable en hosting compartido)
        $db = \Config\Database::connect();
        $db->query("SET time_zone = '-06:00'");

        // (Opcional) comprobar qué hora devuelve la BD y registrar en logs
        try {
            $row = $db->query("SELECT NOW() AS mysql_now")->getRow();
            if ($row) {
                log_message('info', 'MySQL NOW after SET time_zone: ' . $row->mysql_now);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error comprobando time_zone en la BD: ' . $e->getMessage());
        }

        if (session()->get('id')) {
            $this->runSystemTasks();
        }
    }
    protected function runSystemTasks()
    {
        $this->ejecutarBackup();
    }
    protected function ejecutarBackup()
    {
        $db = \Config\Database::connect();

        $tarea = $db->table('tareas_sistema')
            ->where('nombre', 'backup_sistema')
            ->get()
            ->getRow();

        $now = new \DateTime();

        if ($tarea && $tarea->ultima_ejecucion) {
            $lastRun = new \DateTime($tarea->ultima_ejecucion);
            $diff = $now->getTimestamp() - $lastRun->getTimestamp();

            if ($diff < 1800) {
                return;
            }
        }

        // 🔒 evitar ejecuciones múltiples
        $lockFile = WRITEPATH . 'backup.lock';

        if (file_exists($lockFile)) {
            return;
        }

        file_put_contents($lockFile, 'running');

        try {
            command('backup:run');

            if ($tarea) {
                $db->table('tareas_sistema')
                    ->where('nombre', 'backup_sistema')
                    ->update([
                        'ultima_ejecucion' => date('Y-m-d H:i:s')
                    ]);
            } else {
                $db->table('tareas_sistema')->insert([
                    'nombre' => 'backup_sistema',
                    'ultima_ejecucion' => date('Y-m-d H:i:s')
                ]);
            }
        } catch (\Throwable $e) {
            log_message('error', 'Error en backup: ' . $e->getMessage());
        } finally {
            if (file_exists($lockFile)) {
                unlink($lockFile);
            }
        }
    }
}
