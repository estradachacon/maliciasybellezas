<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\PackageModel;
use App\Models\SellerModel;
use App\Models\SettledPointModel;
use Dompdf\Dompdf;
use Dompdf\Options;


class PackageController extends BaseController
{
    protected $packageModel;
    protected $sellerModel;
    protected $settledPointModel;
    protected $packages;
    public function __construct()
    {
        $this->packageModel = new PackageModel();
        $this->settledPointModel = new SettledPointModel();
        $this->sellerModel = new SellerModel();
        $this->packages = new PackageModel();
    }

    public function index() {}

    public function show($id = null) {}

    public function new()
    {
        return view('packages/new');
    }
    public function generarEtiqueta()
    {
        $settingModel = new \App\Models\SettingModel();
        $settings = $settingModel->first();
        $horaInicio = $this->request->getGet('hora_inicio');
        $horaFin = $this->request->getGet('hora_fin');

        $data = [
            'logo' => !empty($settings->logo)
                ? base_url('upload/settings/' . $settings->logo)
                : null,

            'codigo' => session('codigo_vendedor'),
            'cliente' => $this->request->getGet('cliente_nombre'),
            'telefono' => $this->request->getGet('cliente_telefono'),
            'destino' => $this->request->getGet('destino'),
            'fecha' => $this->request->getGet('dia_entrega'),
            'hora' => trim(
                ($horaInicio ? $this->formatearHora($horaInicio) : '') .
                    ($horaInicio && $horaFin ? ' - ' : '') .
                    ($horaFin ? $this->formatearHora($horaFin) : '')
            ),
            'total' => $this->request->getGet('total'),
            'encomendista' => $this->request->getGet('encomendista_nombre'),
        ];

        $html = view('packages/pdf/etiqueta', $data);

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper([0, 0, 288, 144], 'portrait');

        $dompdf->render();

        return $this->response
            ->setContentType('application/pdf')
            ->setBody($dompdf->output());
    }

    public function formatearHora($hora)
    {
        if (empty($hora)) return '';

        return date('g:i A', strtotime($hora));
    }
    public function guardar()
    {
        try {

            $model = new PackageModel();

            // 🔥 SOLO TOMAR LOS CAMPOS VÁLIDOS (evita basura)
            $data = [
                'cliente_nombre'       => $this->request->getPost('cliente_nombre'),
                'cliente_telefono'     => $this->request->getPost('cliente_telefono'),
                'dia_entrega'          => $this->request->getPost('dia_entrega') ?: null,
                'hora_inicio'          => $this->request->getPost('hora_inicio') ?: null,
                'hora_fin'             => $this->request->getPost('hora_fin') ?: null,
                'destino'              => $this->request->getPost('destino'),
                'encomendista_nombre'  => $this->request->getPost('encomendista_nombre'),
            ];

            // 🔥 LIMPIAR NUMÉRICOS (muy importante)
            $data['total']  = floatval(str_replace(',', '', $this->request->getPost('total') ?? 0));

            // 🔥 FOTO
            $file = $this->request->getFile('foto');

            if ($file && $file->isValid() && !$file->hasMoved()) {

                $nombre = $file->getRandomName();

                $path = ROOTPATH . 'public/upload/paquetes/';

                // Crear carpeta si no existe
                if (!is_dir($path)) {
                    mkdir($path, 0777, true);
                }

                $file->move($path, $nombre);

                $data['foto'] = $nombre;
            }

            // 🔥 INSERT CON VALIDACIÓN
            if (!$model->insert($data)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'errors' => $model->errors()
                ]);
            }

            return $this->response->setJSON([
                'status' => 'ok'
            ]);
        } catch (\Throwable $e) {

            return $this->response->setJSON([
                'status' => 'exception',
                'msg' => $e->getMessage(),
                'line' => $e->getLine()
            ]);
        }
    }

    public function subirImagen() {}
    public function edit($id) {}

    public function update($id) {}

    public function setDestino() {}

    public function setReenvio() {}

    public function getPackageData($id) {}
    public function getDestinoInfo($id) {}
    public function devolver($id) {}
    public function entregar($id) {}

    public function showReturnPackages() {}
    public function quickLoad() {}
    public function quickStore() {}
    public function updateFlete() {}

    public function updatePagoParcial() {}
    public function updateFleteCompleto() {}
    public function marcarNoRetirado($id) {}
}
