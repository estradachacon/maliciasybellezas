<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;
use App\Libraries\BackupGenerator;

class BackupSystem extends BaseCommand
{
    protected $group       = 'Custom';
    protected $name        = 'backup:run';
    protected $description = 'Genera backup del sistema cada 2 horas y limpia backups antiguos';

    public function run(array $params)
    {
        $db = Database::connect();

        // 🔹 1. Obtener último backup
        $row = $db->table('tareas_sistema')
            ->where('nombre', 'backup_sistema')
            ->get()
            ->getRow();

        $now = new \DateTime();

        if ($row && $row->ultima_ejecucion) {
            $lastRun = new \DateTime($row->ultima_ejecucion);
            $diff = $now->getTimestamp() - $lastRun->getTimestamp();

            if ($diff < 1800) {
                CLI::write('Aún no han pasado 2 horas', 'yellow');
                return;
            }
        }

        CLI::write('Generando backup...', 'green');

        // 🔹 2. Crear carpeta
        $dateFolder = date('Y-m-d');
        $basePath = WRITEPATH . 'backups/' . $dateFolder . '/';

        if (!is_dir($basePath)) {
            mkdir($basePath, 0777, true);
        }

        // 🔹 3. Backup DB (NUEVO MÉTODO)

        $generator = new BackupGenerator();
        $backup = $generator->generate();

        $fileName = 'backup_' . date('H-i-s') . '.sql';
        $filePath = $basePath . $fileName;

        file_put_contents($filePath, $backup);

        if (file_exists($filePath) && filesize($filePath) > 1000) {
            CLI::write('Backup guardado: ' . $fileName, 'green');
            $this->enviarARemoto($filePath, $fileName);
        } else {
            CLI::error('Error al generar el backup');
        }

        // 🔹 4. Limpiar antiguos
        $this->cleanOldBackups();

        // 🔹 5. Guardar ejecución
        if ($row) {
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

        CLI::write('🧹 Limpieza completada', 'blue');
    }

    private function cleanOldBackups()
    {
        $path = WRITEPATH . 'backups/';

        if (!is_dir($path)) return;

        $folders = scandir($path);

        foreach ($folders as $folder) {
            if ($folder === '.' || $folder === '..') continue;

            $folderPath = $path . $folder;

            if (is_dir($folderPath)) {
                $folderDate = \DateTime::createFromFormat('Y-m-d', $folder);

                if ($folderDate) {
                    $now = new \DateTime();
                    $diff = $now->diff($folderDate)->days;

                    if ($diff > 5) {
                        $this->deleteFolder($folderPath);
                        CLI::write("🗑️ Eliminado backup antiguo: $folder", 'red');
                    }
                }
            }
        }
    }

    private function deleteFolder($folder)
    {
        $files = array_diff(scandir($folder), ['.', '..']);

        foreach ($files as $file) {
            $fullPath = $folder . '/' . $file;

            if (is_dir($fullPath)) {
                $this->deleteFolder($fullPath);
            } else {
                unlink($fullPath);
            }
        }

        rmdir($folder);
    }

    private function enviarARemoto(string $filePath, string $fileName): void
    {
        $apiKey = env('CARYTEL_API_KEY');

        if (!$apiKey || $apiKey === 'TU_API_KEY_AQUI') {
            CLI::write('[Remoto] CARYTEL_API_KEY no configurada, omitiendo envío.', 'yellow');
            return;
        }

        if (!function_exists('curl_init')) {
            log_message('error', '[BackupRemoto] cURL no está disponible en este servidor.');
            return;
        }

        $url  = 'https://carytel.com/api/frameworks/backup';
        $meta = json_encode([
            'sistema'   => env('CARYTEL_SISTEMA', 'Maliciasybellezas'),
            'db_nombre' => env('database.default.database', 'maliciasdb'),
            'notas'     => 'Backup automático ' . date('Y-m-d H:i:s'),
        ]);

        try {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_HTTPHEADER     => ['X-Api-Key: ' . $apiKey],
                CURLOPT_POSTFIELDS     => [
                    'archivo' => new \CURLFile($filePath, 'application/sql', $fileName),
                    'meta'    => $meta,
                ],
                CURLOPT_TIMEOUT        => 120,
                CURLOPT_SSL_VERIFYPEER => true,
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlErr  = curl_error($ch);
            curl_close($ch);

            if ($curlErr) {
                CLI::write('[Remoto] Error cURL: ' . $curlErr, 'red');
                log_message('error', '[BackupRemoto] cURL: ' . $curlErr);
                return;
            }

            $data = json_decode($response, true);

            if ($httpCode === 200 && ($data['ok'] ?? false)) {
                CLI::write('[Remoto] Backup enviado a carytel.com (ID: ' . ($data['id'] ?? '?') . ')', 'green');
                log_message('info', '[BackupRemoto] OK. ID remoto: ' . ($data['id'] ?? '?'));
            } else {
                CLI::write('[Remoto] Respuesta inesperada (HTTP ' . $httpCode . '): ' . $response, 'red');
                log_message('error', '[BackupRemoto] HTTP ' . $httpCode . ' — ' . $response);
            }
        } catch (\Throwable $e) {
            CLI::write('[Remoto] Excepción: ' . $e->getMessage(), 'red');
            log_message('error', '[BackupRemoto] Excepción: ' . $e->getMessage());
        }
    }
}
