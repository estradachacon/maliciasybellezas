<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\PackageModel;
use App\Models\SellerModel;
use App\Models\SettledPointModel;
use Dompdf\Dompdf;
use Dompdf\Options;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

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
        $fecha_inicio = $this->request->getGet('fecha_inicio');
        $fecha_fin    = $this->request->getGet('fecha_fin');
        $builder = $model;
        if (!empty($cliente)) {
            $builder = $builder->like('cliente_nombre', $cliente);
        }

        if ($fecha_inicio && $fecha_fin) {
            $builder->where('DATE(dia_entrega) >=', $fecha_inicio)
                ->where('DATE(dia_entrega) <=', $fecha_fin);
        }
        $builder = $builder->orderBy('id', 'DESC');
        $paquetes = $builder->paginate(12);
        $pager = $builder->pager;
        if ($this->request->isAJAX()) {
            $html = view('packages/_cards', ['paquetes' => $paquetes]);

            return $this->response->setJSON([
                'html' => view('packages/_cards', ['paquetes' => $paquetes]),
                'pager' => $pager->links('default', 'bitacora_pagination')
            ]);
        }
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
        $codigo = $this->generarCodigoInterno();

        return view('packages/new', [
            'codigoqr' => $codigo
        ]);
    }

    public function generarEtiqueta()
    {
        $settingModel = new \App\Models\SettingModel();
        $settings = $settingModel->first();
        $horaInicio = $this->request->getGet('hora_inicio');
        $horaFin = $this->request->getGet('hora_fin');
        $codigoQR = $this->request->getGet('codigoqr');

        $builder = new Builder(
            writer: new PngWriter(),
            data: $codigoQR,
            size: 90,
            margin: 0
        );

        $result = $builder->build();

        $qrBase64 = 'data:image/png;base64,' . base64_encode($result->getString());

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
            'qr' => $qrBase64,
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
                'tipo_venta' => $this->request->getPost('tipo_venta') ?? 'detalle',
                'estado1'    => 'pendiente',
            ];

            $data['total'] = floatval(str_replace(',', '', $this->request->getPost('total') ?? 0));

            do {
                $codigo = $this->request->getPost('codigoqr');
                $existe = $model->where('codigoqr', $codigo)->first();

                if (!$existe) break;

                // regenerar si ya existe
                $codigo = $this->generarCodigoInterno();
            } while (true);

            $data['codigoqr'] = $codigo;

            // FOTO
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

            // INSERT
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
            
            addPackLog($id, 'Paquete creado');

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
    public function generarCodigo()
    {
        $model = new PackageModel();

        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

        do {
            $codigo = '';
            for ($i = 0; $i < 6; $i++) {
                $codigo .= $chars[random_int(0, strlen($chars) - 1)];
            }

            $existe = $model->where('codigoqr', $codigo)->first();
        } while ($existe);

        return $this->response->setJSON([
            'codigo' => $codigo
        ]);
    }
    private function generarCodigoInterno()
    {
        $model = new PackageModel();

        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

        do {
            $codigo = '';
            for ($i = 0; $i < 6; $i++) {
                $codigo .= $chars[random_int(0, strlen($chars) - 1)];
            }

            $existe = $model->where('codigoqr', $codigo)->first();
        } while ($existe);

        return $codigo;
    }
    public function exportar()
    {
        $model = new PackageModel();

        $cliente = $this->request->getGet('cliente');
        $estado = $this->request->getGet('estado');
        $fecha_inicio = $this->request->getGet('fecha_inicio');
        $fecha_fin    = $this->request->getGet('fecha_fin');

        $builder = $model;

        // filtros
        if (!empty($cliente)) {
            $builder->like('cliente_nombre', $cliente);
        }

        if ($fecha_inicio && $fecha_fin) {
            $builder->where('dia_entrega >=', $fecha_inicio)
                ->where('dia_entrega <=', $fecha_fin);
        }

        // 🔥 traer TODO (sin paginar)
        $data = $builder->orderBy('id', 'DESC')->findAll();

        // =========================
        // EXCEL
        // =========================

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // encabezados
        $sheet->setCellValue('A1', '#');
        $sheet->setCellValue('B1', 'Cliente');
        $sheet->setCellValue('C1', 'Destino');
        $sheet->setCellValue('D1', 'Entrega');
        $sheet->setCellValue('E1', 'Total');
        $sheet->setCellValue('F1', 'Estado');

        // data
        $row = 2;

        foreach ($data as $p) {

            $estadoTexto = trim(($p->estado1 ?? '') . ' ' . ($p->estado2 ?? ''));

            $sheet->setCellValue('A' . $row, $p->id);
            $sheet->setCellValue('B' . $row, $p->cliente_nombre);
            $sheet->setCellValue('C' . $row, $p->destino);
            $sheet->setCellValue('D' . $row, $p->dia_entrega);
            $sheet->setCellValue('E' . $row, $p->total);
            $sheet->setCellValue('F' . $row, $estadoTexto);
            $sheet->setCellValue('E' . $row, $p->total);

            // formato moneda
            $sheet->getStyle('E' . $row)
                ->getNumberFormat()
                ->setFormatCode('"$"#,##0.00');

            $row++;
        }

        // auto size
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension('A')->setWidth(8);   // #
            $sheet->getColumnDimension('B')->setWidth(30);  // Cliente
            $sheet->getColumnDimension('C')->setWidth(35);  // Destino
            $sheet->getColumnDimension('D')->setWidth(15);  // Fecha
            $sheet->getColumnDimension('E')->setWidth(15);  // Total
            $sheet->getColumnDimension('F')->setWidth(25);  // Estado
        }

        // descargar
        $writer = new Xlsx($spreadsheet);

        $filename = 'paquetes_' . date('Ymd_His') . '.xlsx';

        if (ob_get_length()) ob_end_clean();

        ob_start();
        $writer->save('php://output');
        $excelOutput = ob_get_clean();

        return $this->response
            ->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setHeader('Cache-Control', 'max-age=0')
            ->setHeader('Pragma', 'public')
            ->setHeader('Expires', '0')
            ->setBody($excelOutput);
    }

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
