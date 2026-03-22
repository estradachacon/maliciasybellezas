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

    public function index()
    {
        $model = new PackageModel();

        $cliente = $this->request->getGet('cliente');
        $fecha   = $this->request->getGet('fecha');
        $estado  = $this->request->getGet('estado');

        // 🔥 QUERY BASE
        $builder = $model;

        // =========================
        // FILTROS
        // =========================

        if (!empty($cliente)) {
            $builder = $builder->like('cliente_nombre', $cliente);
        }

        if (!empty($fecha)) {
            $builder = $builder->where('dia_entrega', $fecha);
        }

        // 👇 este depende de tu lógica real (ahorita no tienes campo estado)
        if ($estado !== '' && $estado !== null) {

            if ($estado == '1') {
                $builder = $builder->where('total', 0); // cancelado
            } else {
                $builder = $builder->where('total >', 0); // activo
            }
        }

        // =========================
        // PAGINACIÓN
        // =========================
        $builder = $builder->orderBy('id', 'DESC');
        $paquetes = $builder->paginate(10);
        $pager = $builder->pager;

        // =========================
        // AJAX
        // =========================

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'tbody' => view('packages/_tbody', ['paquetes' => $paquetes]),
                'pager' => $pager->links('default', 'bitacora_pagination')
            ]);
        }

        // =========================
        // NORMAL
        // =========================

        return view('packages/index', [
            'paquetes' => $paquetes,
            'pager' => $pager
        ]);
    }

    public function show($id)
    {
        $model = new PackageModel();

        $paquete = $model->find($id);

        if (!$paquete) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Paquete no encontrado");
        }

        return view('packages/show', [
            'paquete' => $paquete
        ]);
    }

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

        $nombre = $settings->company_name ?? 'Mi Empresa';
        $nombreFormateado = mb_convert_case($nombre, MB_CASE_TITLE, "UTF-8");

        $tituloImg = $this->generarTituloImagen($nombreFormateado);

        $data['titulo_img'] = $tituloImg;
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
            'titulo_img' => $tituloImg,
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
    private function generarTituloImagen($texto)
    {
        $rutaFuente = FCPATH . 'fonts/FleurDeLeah-Regular.ttf';

        $ancho = 600;
        $alto = 67;

        $imagen = imagecreatetruecolor($ancho, $alto);

        imagesavealpha($imagen, true);
        $transparente = imagecolorallocatealpha($imagen, 0, 0, 0, 127);
        imagefill($imagen, 0, 0, $transparente);

        $color = imagecolorallocate($imagen, 102, 51, 153);

        $size = 34;

        $bbox = imagettfbbox($size, 0, $rutaFuente, $texto);
        $textWidth = $bbox[2] - $bbox[0];
        $textHeight = $bbox[1] - $bbox[7];

        $x = 8;
        $y = $textHeight - 22;

        imagettftext($imagen, $size, 0, $x, $y, $color, $rutaFuente, $texto);

        // 🔥 CAPTURAR IMAGEN EN MEMORIA
        ob_start();
        imagepng($imagen);
        $imagenData = ob_get_clean();

        imagedestroy($imagen);

        // 🔥 CONVERTIR A BASE64
        return 'data:image/png;base64,' . base64_encode($imagenData);
    }

    public function guardar()
    {
        try {

            $model = new PackageModel();

            $data = [
                'cliente_nombre'       => $this->request->getPost('cliente_nombre'),
                'cliente_telefono'     => $this->request->getPost('cliente_telefono'),
                'dia_entrega'          => $this->request->getPost('dia_entrega') ?: null,
                'hora_inicio'          => $this->request->getPost('hora_inicio') ?: null,
                'hora_fin'             => $this->request->getPost('hora_fin') ?: null,
                'destino'              => $this->request->getPost('destino'),
                'encomendista_nombre'  => $this->request->getPost('encomendista_nombre'),
            ];

            $data['total'] = floatval(str_replace(',', '', $this->request->getPost('total') ?? 0));

            // =========================
            // FOTO
            // =========================
            $file = $this->request->getFile('foto');

            if ($file && $file->isValid() && !$file->hasMoved()) {

                $nombre = $file->getRandomName();

                $path = ROOTPATH . 'public/upload/paquetes/';

                if (!is_dir($path)) {
                    mkdir($path, 0777, true);
                }

                $file->move($path, $nombre);

                $data['foto'] = $nombre;
            }

            // =========================
            // INSERT
            // =========================
            if (!$model->insert($data)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'errors' => $model->errors()
                ]);
            }

            // ID INSERTADO
            $id = $model->getInsertID();

            // BITÁCORA 
            $session = session();

            registrar_bitacora(
                'Creación de paquete ID ' . $id,
                'Paquetes',
                'Cliente: ' . esc($data['cliente_nombre']) .
                    ' | Destino: ' . esc($data['destino']) .
                    ' | Total: $' . number_format($data['total'], 2),
                $session->get('id')
            );

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
