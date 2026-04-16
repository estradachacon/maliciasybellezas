<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\PackageModel;
use App\Models\PackageDetailModel;
use App\Models\PackageDepositDetailModel;
use App\Models\EncomendistasModel;
use App\Models\SettledPointModel;
use App\Models\TransactionModel;
use App\Models\UserModel;
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
    protected $settledPointModel;
    protected $packages;
    public function __construct()
    {
        $this->packageModel = new PackageModel();
        $this->settledPointModel = new SettledPointModel();
        $this->packages = new PackageModel();
    }

    public function index()
    {
        $model = new PackageModel();
        $encomendistasModel     = new EncomendistasModel();
        $usersModel             = new UserModel(); // o como se llame
        $encomendistas = $encomendistasModel->findAll();
        $vendedores = $usersModel->findAll();

        $cliente = $this->request->getGet('cliente');
        $fecha_inicio = $this->request->getGet('fecha_inicio');
        $fecha_fin    = $this->request->getGet('fecha_fin');
        $encomendista = $this->request->getGet('encomendista');
        $vendedor     = $this->request->getGet('vendedor');

        // 🔥 limpiar strings "null"
        if ($encomendista === 'null' || $encomendista === '') {
            $encomendista = null;
        }

        if ($vendedor === 'null' || $vendedor === '') {
            $vendedor = null;
        }

        $model = $model->select('paquetes.*');

        // 🔥 join encomendista SOLO si se usa o siempre si lo ocupás en vista
        $model->join('encomendistas', 'encomendistas.id = paquetes.encomendista_nombre', 'left');
        $model->select('encomendistas.encomendista_name as encomendista_nombre');

        $model->join('users', 'users.id = paquetes.vendedor_id', 'left');
        $model->select('users.user_name as vendedor_nombre');

        if (!empty($cliente)) {
            $model->like('paquetes.cliente_nombre', $cliente);
        }

        if ($fecha_inicio && $fecha_fin) {
            $model->where('DATE(paquetes.dia_entrega) >=', $fecha_inicio)
                ->where('DATE(paquetes.dia_entrega) <=', $fecha_fin);
        }

        if ($encomendista !== null && is_numeric($encomendista) && (int)$encomendista > 0) {
            $model->where('paquetes.encomendista_nombre', (int)$encomendista);
        }

        if ($vendedor !== null && is_numeric($vendedor) && (int)$vendedor > 0) {
            $model->where('paquetes.vendedor_id', (int)$vendedor);
        }

        $model = $model->orderBy('paquetes.id', 'DESC');

        $paquetes = $model->orderBy('paquetes.id', 'DESC')->paginate(12);
        $pager = $model->pager;

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'html' => view('packages/_cards', ['paquetes' => $paquetes]),
                'pager' => $pager->links('default', 'bitacora_pagination'),
                'encomendistas' => $encomendistas,
                'vendedores' => $vendedores
            ]);
        }

        return view('packages/index', [
            'paquetes' => $paquetes,
            'pager' => $pager,
            'encomendistas' => $encomendistas,
            'vendedores' => $vendedores
        ]);
    }

    public function show($id)
    {
        $model = new PackageModel();
        $depositModel = new PackageDepositDetailModel();

        $paquete = $model
            ->select('
                paquetes.*, 
                users.user_name as vendedor_nombre,
                e.encomendista_name as encomendista_nombre
            ')
            ->join('users', 'users.id = paquetes.vendedor_id', 'left')
            ->join('encomendistas e', 'e.id = paquetes.encomendista_nombre', 'left')
            ->where('paquetes.id', $id)
            ->first();

        if (!$paquete) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Paquete no encontrado");
        }

        $detalleModel = new PackageDetailModel();

        $detalles = $detalleModel
            ->select('paquete_detalle.*, productos.nombre as producto_nombre')
            ->join('productos', 'productos.id = paquete_detalle.producto_id', 'left')
            ->where('paquete_id', $id)
            ->findAll();

        $asignacion = $depositModel
            ->where('package_id', $id)
            ->first();

        return view('packages/show', [
            'paquete' => $paquete,
            'tieneAsignacion' => !empty($asignacion),
            'detalles' => $detalles
        ]);
    }
    public function actualizarEstado()
    {
        $paqueteId = $this->request->getPost('paquete_id');
        $estado    = $this->request->getPost('nuevo_estado');

        $model = new PackageModel();

        // mapa de estados
        $map = [
            'reenvio'      => 'Cliente solicitó reenvío',
            'entregado'    => 'Entregado',
            'no_retirado'  => 'No retirado',
            'devuelto'     => 'Paquete devuelto',
        ];

        if (!isset($map[$estado])) {
            return redirect()->back()->with('error', 'Estado inválido');
        }

        // 🔎 validar existencia
        $paquete = $model->find($paqueteId);

        if (!$paquete) {
            return redirect()->back()->with('error', 'Paquete no encontrado');
        }

        $dataUpdate = [
            'estado1' => $estado
        ];

        switch ($estado) {

            case 'entregado':
                $dataUpdate['estado2'] = 'pendiente_remu';
                break;

            case 'reenvio':
                $model->set('reenvios', 'reenvios + 1', false);
                $dataUpdate['estado1'] = 'en_encomendista';
                $dataUpdate['estado2'] = 'reenvio';
                break;

            case 'no_retirado':
                $dataUpdate['estado1'] = 'en_encomendista';
                $dataUpdate['estado2'] = 'no_retirado';
                break;

            case 'devuelto':
                $dataUpdate['estado1'] = 'finalizado';
                $dataUpdate['estado2'] = 'no_retirado_y_devuelto';
                break;
        }

        // actualizar
        $model->update($paqueteId, $dataUpdate);

        // 🧾 log bonito
        $mensaje = "Estado actualizado a: " . $map[$estado];

        addPackLog($paqueteId, $mensaje);

        return redirect()->back()->with('success', 'Estado actualizado correctamente');
    }

    public function reenvioConCambios()
    {
        try {
            $data = $this->request->getJSON(true);
            $id   = (int)($data['paquete_id'] ?? 0);

            if (!$id) {
                return $this->response->setJSON(['status' => 'error', 'msg' => 'ID inválido']);
            }

            $model   = new PackageModel();
            $paquete = $model->find($id);

            if (!$paquete) {
                return $this->response->setJSON(['status' => 'error', 'msg' => 'Paquete no encontrado']);
            }

            // Misma lógica de reenvío que actualizarEstado()
            $model->set('reenvios', 'reenvios + 1', false);
            $model->update($id, [
                'estado1'          => 'en_encomendista',
                'estado2'          => 'reenvio',
                'cliente_nombre'   => $data['cliente_nombre'],
                'cliente_telefono' => $data['cliente_telefono'],
                'destino'          => $data['destino'],
                'dia_entrega'      => $data['dia_entrega'],
                'hora_inicio'      => $data['hora_inicio'],
                'hora_fin'         => $data['hora_fin'],
            ]);

            addPackLog($id, 'Estado actualizado a: Cliente solicitó reenvío (con datos actualizados)');

            registrar_bitacora(
                'Reenvío con cambios — Paquete ID ' . $id,
                'Paquetes',
                'Cliente: ' . $data['cliente_nombre'] . ' | Destino: ' . $data['destino'],
                session('id')
            );

            return $this->response->setJSON(['status' => 'ok']);
        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'msg'    => $e->getMessage()
            ]);
        }
    }

    public function actualizarFoto()
    {
        try {
            $id   = (int)$this->request->getPost('paquete_id');
            $model = new PackageModel();

            $paquete = $model->find($id);
            if (!$paquete) {
                return $this->response->setJSON(['status' => 'error', 'msg' => 'Paquete no encontrado']);
            }

            $file = $this->request->getFile('foto');

            if (!$file || !$file->isValid() || $file->hasMoved()) {
                return $this->response->setJSON(['status' => 'error', 'msg' => 'Archivo inválido']);
            }

            $nombre = $file->getRandomName();
            $path   = ROOTPATH . 'public/upload/paquetes/';

            if (!is_dir($path)) {
                mkdir($path, 0777, true);
            }

            $file->move($path, $nombre);

            $model->update($id, ['foto' => $nombre]);

            addPackLog($id, 'Foto del paquete actualizada');

            return $this->response->setJSON([
                'status' => 'ok',
                'nueva_foto' => base_url('upload/paquetes/' . $nombre)
            ]);
        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'msg'    => $e->getMessage(),
                'line'   => $e->getLine()
            ]);
        }
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
        $rutaFuente = FCPATH . 'fonts/Ribeye-Regular.ttf';

        $ancho = 600;
        $alto = 50;

        $imagen = imagecreatetruecolor($ancho, $alto);

        imagesavealpha($imagen, true);
        $transparente = imagecolorallocatealpha($imagen, 0, 0, 0, 127);
        imagefill($imagen, 0, 0, $transparente);

        $color = imagecolorallocate($imagen, 102, 51, 153);

        $size = 25;

        $bbox = imagettfbbox($size, 0, $rutaFuente, $texto);
        $textWidth = $bbox[2] - $bbox[0];
        $textHeight = $bbox[1] - $bbox[7];

        $x = 8;
        $y = $textHeight + 5;

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
            $db = \Config\Database::connect();
            $db->transStart();

            $model = new PackageModel();
            $detalleModel = new PackageDetailModel();
            $branchId = session('branch_id');
            $userId   = session('id');
            // 🔥 RECIBIR PAYLOAD
            $payload = json_decode($this->request->getPost('data'), true);

            if (!$payload) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'msg' => 'Payload vacío o inválido'
                ]);
            }

            // CABECERA
            $data = [
                'cliente_nombre'      => $payload['cliente']['nombre'],
                'cliente_telefono'    => $payload['cliente']['telefono'],
                'dia_entrega'         => $payload['entrega']['fecha'] ?: null,
                'hora_inicio'         => $payload['entrega']['hora_inicio'] ?: null,
                'hora_fin'            => $payload['entrega']['hora_fin'] ?: null,
                'destino'             => $payload['entrega']['destino'],
                'encomendista_nombre' => (int)$payload['operacion']['encomendista_id'],
                'tipo_venta'          => $payload['operacion']['tipo_venta'],
                'estado1'             => 'pendiente',

                // TOTALES
                'envio'               => $payload['totales']['envio'],
                'descuento_global'    => $payload['totales']['descuento_global'],
                'total_real'          => $payload['totales']['total_real'],
                'total'               => $payload['totales']['total_remunerar'],

                'vendedor_id'         => session('id')
            ];

            // CÓDIGO QR
            do {
                $codigo = $payload['operacion']['codigoqr'];
                $existe = $model->where('codigoqr', $codigo)->first();

                if (!$existe) break;

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

            // INSERT PAQUETE
            if (!$model->insert($data)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'errors' => $model->errors()
                ]);
            }

            $paqueteId = $model->getInsertID();

            // DETALLE
            foreach ($payload['productos'] as $p) {

                try {

                    $detalleModel->insert([
                        'paquete_id' => $paqueteId,
                        'producto_id' => (int)$p['producto_id'],
                        'cantidad'   => $p['cantidad'],
                        'precio'     => $p['precio'],
                        'descuento'  => 0, // 🔥 ya no usas descuento por producto
                        'subtotal'   => ($p['cantidad'] * $p['precio'])
                    ]);

                    $db->table('inventario_historico')->insert([
                        'producto_id' => (int)$p['producto_id'],
                        'branch_id'   => (int)$p['branch_id'],
                        'tipo'        => 'salida',
                        'cantidad'    => (int)$p['cantidad'],
                        'origen'      => 'paquete',
                        'origen_id'   => $paqueteId,
                        'usuario_id'  => $userId,
                        'created_at'  => date('Y-m-d H:i:s')
                    ]);
                } catch (\Throwable $e) {
                    throw new \Exception('Error en producto ID ' . $p['producto_id'] . ': ' . $e->getMessage());
                }
            }

            $pago = $payload['pago'] ?? null;

            if (!$pago) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'msg' => 'Información de pago requerida'
                ]);
            }

            // 🚨 SI ESTÁ PAGADO → CUENTA OBLIGATORIA
            if ($pago['cancelado'] && empty($pago['cuenta_id'])) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'msg' => 'Debe seleccionar una cuenta para el pago'
                ]);
            }

            if ($pago['cancelado'] && !empty($pago['cuenta_id'])) {

                $montoProductos = $payload['totales']['subtotal'] - $payload['totales']['descuento_global'];
                $montoEnvio = $payload['totales']['envio'];
                $cuentaId = (int)$pago['cuenta_id'];
                $transactionModel = new TransactionModel();

                $transactionModel->addEntrada(
                    $cuentaId,
                    $montoProductos,
                    'paquete',
                    $paqueteId
                );

                if ($montoEnvio > 0) {
                    $transactionModel->addEntrada(
                        $cuentaId,
                        $montoEnvio,
                        'envio_paquete',
                        $paqueteId
                    );
                }
            }

            $totalReal = (float)$data['total_real'];
            $totalCobrado = (float)$data['total'];
            $pagado = $payload['pago']['cancelado'] ?? false;

            // TEXTO ESTADO
            $estadoPago = $pagado ? 'Pagado' : 'Pendiente de cobro de remuneración';

            // ARMAR TEXTO DE TOTALES
            if ($totalReal != $totalCobrado) {
                $textoTotales = 'Total real: $' . number_format($totalReal, 2) .
                    ' | Cobrado: $' . number_format($totalCobrado, 2) .
                    ' | ' . $estadoPago;
            } else {
                $textoTotales = 'Total: $' . number_format($totalCobrado, 2) .
                    ' | ' . $estadoPago;
            }

            // BITÁCORA
            registrar_bitacora(
                'Creación de paquete ID ' . $paqueteId,
                'Paquetes',
                'Cliente: ' . esc($data['cliente_nombre']) .
                    ' | Destino: ' . esc($data['destino']) .
                    ' | ' . $textoTotales,
                session('id')
            );

            addPackLog($paqueteId, 'Paquete creado');
            if ($db->error()['code'] != 0) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'db_error' => $db->error(),
                    'last_query' => (string)$db->getLastQuery()
                ]);
            }
            // FINALIZAR
            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'msg' => 'Error en transacción'
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
        $fecha_inicio = $this->request->getGet('fecha_inicio');
        $fecha_fin    = $this->request->getGet('fecha_fin');

        $builder = $model
            ->select('
            paquetes.*,
            e.encomendista_name,
            paquetes.cliente_telefono AS encomendista_telefono,
            pdd.flete_asignado,
            pd.fecha AS fecha_deposito
        ')
            ->join('(
            SELECT p1.*
            FROM package_deposit_details p1
            INNER JOIN (
                SELECT package_id, MAX(id) as max_id
                FROM package_deposit_details
                GROUP BY package_id
            ) p2 ON p1.id = p2.max_id
        ) pdd', 'pdd.package_id = paquetes.id', 'left', false)

            ->join('package_deposits pd', 'pd.id = pdd.deposit_id', 'left')
            ->join('encomendistas e', 'e.id = paquetes.encomendista_nombre', 'left');

        if (!empty($cliente)) {
            $builder->like('cliente_nombre', $cliente);
        }

        if ($fecha_inicio && $fecha_fin) {
            $builder->where('dia_entrega >=', $fecha_inicio)
                ->where('dia_entrega <=', $fecha_fin);
        }

        $data = $builder->orderBy('paquetes.id', 'DESC')->findAll();

        // =========================
        // EXCEL
        // =========================

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Resumen');

        // HEADERS
        $headers = [
            'A1' => '#',
            'B1' => 'Cliente',
            'C1' => 'Teléfono',
            'D1' => 'Destino',
            'E1' => 'Encomendista',
            'F1' => 'Envío (Pagado a Encom)',
            'G1' => 'Fecha depósito',
            'H1' => 'Entrega',
            'I1' => 'Total real (venta sin envío)',
            'J1' => 'Envío (cliente)',
            'K1' => 'Descuento',
            'L1' => 'Total venta (ya con envío)',
            'M1' => 'Cancelado',
            'N1' => 'Total remunerar',
            'O1' => 'Estado 1',
            'P1' => 'Estado 2',
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        $row = 2;

        foreach ($data as $p) {

            $total = floatval($p->total_real ?? 0);
            $totalVenta = floatval($p->total_real ?? 0);
            $flete = floatval($p->envio ?? 0);
            $descuento = floatval($p->descuento_global ?? 0);

            $pagado = $total <= 0 ? 'SI' : 'NO';

            $fechaDeposito = !empty($p->fecha_deposito)
                ? date('d/m/Y H:i', strtotime($p->fecha_deposito))
                : '—';

            $sheet->setCellValue('A' . $row, $p->id);
            $sheet->setCellValue('B' . $row, $p->cliente_nombre);
            $sheet->setCellValue('C' . $row, $p->encomendista_telefono);
            $sheet->setCellValue('D' . $row, $p->destino);
            $sheet->setCellValue('E' . $row, $p->encomendista_name);
            $sheet->setCellValue('F' . $row, $p->flete_asignado);
            $sheet->setCellValue('G' . $row, $fechaDeposito);
            $sheet->setCellValue('H' . $row, $p->dia_entrega);
            $sheet->setCellValue('I' . $row, $totalVenta - $flete);
            $sheet->setCellValue('J' . $row, $flete);
            $sheet->setCellValue('K' . $row, $descuento);
            $sheet->setCellValue('L' . $row, $totalVenta);
            $sheet->setCellValue('M' . $row, $pagado);
            $sheet->setCellValue('N' . $row, $total);
            $sheet->setCellValue('O' . $row, $p->estado1);
            $sheet->setCellValue('P' . $row, $p->estado2);

            foreach (['F', 'I', 'J', 'K', 'L', 'N'] as $col) {
                $sheet->getStyle($col . $row)
                    ->getNumberFormat()
                    ->setFormatCode('"$"#,##0.00');
            }

            $row++;
        }

        // =========================
        // HOJA 2 DETALLE
        // =========================

        $detalleSheet = $spreadsheet->createSheet();
        $detalleSheet->setTitle('Detalle');

        $detalleSheet->setCellValue('A1', 'Paquete');
        $detalleSheet->setCellValue('B1', 'Cliente');
        $detalleSheet->setCellValue('C1', 'Producto ID');
        $detalleSheet->setCellValue('D1', 'Cantidad');
        $detalleSheet->setCellValue('E1', 'Precio');
        $detalleSheet->setCellValue('F1', 'Descuento');
        $detalleSheet->setCellValue('G1', 'Subtotal');

        // 🔥 SOLO paquetes exportados
        $packageIds = array_map(fn($p) => $p->id, $data);

        if (!empty($packageIds)) {

            $db = \Config\Database::connect();

            $detalles = $db->table('paquete_detalle pd')
                ->select('
                        pd.*,
                        p.cliente_nombre,
                        pr.nombre AS producto_nombre
                    ')
                ->join('paquetes p', 'p.id = pd.paquete_id', 'left')
                ->join('productos pr', 'pr.id = pd.producto_id', 'left') // 👈 ESTA LÍNEA
                ->whereIn('pd.paquete_id', $packageIds)
                ->orderBy('pd.paquete_id', 'ASC')
                ->get()
                ->getResult();

            $rowDetalle = 2;
            $last = null;

            foreach ($detalles as $d) {

                // separador visual
                if ($last !== $d->paquete_id) {
                    $detalleSheet->setCellValue('A' . $rowDetalle, '---');
                    $rowDetalle++;
                    $last = $d->paquete_id;
                }

                $detalleSheet->setCellValue('A' . $rowDetalle, $d->paquete_id);
                $detalleSheet->setCellValue('B' . $rowDetalle, $d->cliente_nombre);
                $detalleSheet->setCellValue('C' . $rowDetalle, $d->producto_nombre ?? $d->producto_id);
                $detalleSheet->setCellValue('D' . $rowDetalle, $d->cantidad);
                $detalleSheet->setCellValue('E' . $rowDetalle, $d->precio);
                $detalleSheet->setCellValue('F' . $rowDetalle, $d->descuento ?? 0);
                $detalleSheet->setCellValue('G' . $rowDetalle, $d->subtotal);

                foreach (['E', 'F', 'G'] as $col) {
                    $detalleSheet->getStyle($col . $rowDetalle)
                        ->getNumberFormat()
                        ->setFormatCode('"$"#,##0.00');
                }

                $rowDetalle++;
            }
        }

        // autosize todo
        foreach (range('A', 'O') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
            $detalleSheet->getColumnDimension($col)->setAutoSize(true);
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

    public function simuladorLiquidacion()
    {
        $db = \Config\Database::connect();

        $builder = $db->table('paquetes p');
        $builder->select('
            p.id,
            p.codigo,
            p.monto_cobro,
            p.estado,
            p.fecha_entrega,
            c.nombre as cliente,
            e.id as encomendista_id,
            e.nombre as encomendista
        ');
        $builder->join('clientes c', 'c.id = p.cliente_id');
        $builder->join('encomendistas e', 'e.id = p.encomendista_id');

        $builder->where('p.estado', 'ENTREGADO');
        $builder->where('p.remunerado', 0);

        $paquetes = $builder->get()->getResult();

        // Agrupar por encomendista
        $agrupado = [];

        foreach ($paquetes as $p) {
            $agrupado[$p->encomendista_id]['nombre'] = $p->encomendista;
            $agrupado[$p->encomendista_id]['paquetes'][] = $p;
        }

        return view('liquidaciones/simulador', [
            'data' => $agrupado
        ]);
    }

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
