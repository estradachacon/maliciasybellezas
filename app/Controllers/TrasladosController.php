<?php

namespace App\Controllers;

use App\Models\TrasladoModel;
use App\Models\TrasladoDetalleModel;

class TrasladosController extends BaseController
{
    // ── LISTADO ───────────────────────────────────────────────────
    public function index()
    {
        $model    = new TrasladoModel();
        $traslados = $model->conRelaciones()->get()->getResultObject();

        return view('traslados/index', compact('traslados'));
    }

    // ── FORMULARIO CREAR ─────────────────────────────────────────
    public function crear()
    {
        $db       = \Config\Database::connect();
        $branches = $db->table('branches')->get()->getResultObject();
        $cuentas  = $db->table('accounts')->get()->getResultObject();

        return view('traslados/crear', compact('branches', 'cuentas'));
    }

    // ── GUARDAR ──────────────────────────────────────────────────
    public function guardar()
    {
        try {
            $db             = \Config\Database::connect();
            $model          = new TrasladoModel();
            $detalleModel   = new TrasladoDetalleModel();
            $userId         = session('id');

            $data = $this->request->getJSON(true);

            if (empty($data['productos'])) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'msg'    => 'Debes agregar al menos un producto'
                ]);
            }

            if ((int)$data['origen_branch_id'] === (int)$data['destino_branch_id']) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'msg'    => 'El origen y destino no pueden ser la misma sucursal'
                ]);
            }

            $db->transStart();

            // ── 1. Insertar cabecera ──────────────────────────────
            $costoTraslado = (float)($data['costo_traslado'] ?? 0);
            $cuentaId      = !empty($data['cuenta_id']) ? (int)$data['cuenta_id'] : null;

            // Si hay costo, cuenta es obligatoria
            if ($costoTraslado > 0 && !$cuentaId) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'msg'    => 'Debes seleccionar una cuenta para el gasto del traslado'
                ]);
            }

            $trasladoId = $model->insert([
                'origen_branch_id'  => (int)$data['origen_branch_id'],
                'destino_branch_id' => (int)$data['destino_branch_id'],
                'usuario_id'        => $userId,
                'costo_traslado'    => $costoTraslado,
                'cuenta_id'         => $cuentaId,
                'notas'             => $data['notas'] ?? null,
                'estado'            => 'completado',
            ]);

            if (!$trasladoId) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'msg'    => 'Error al crear el traslado',
                    'errors' => $model->errors()
                ]);
            }

            // ── 2. Insertar detalles + mover inventario ───────────
            foreach ($data['productos'] as $p) {
                $productoId = (int)$p['producto_id'];
                $cantidad   = (int)$p['cantidad'];

                // Validar stock disponible en origen
$result = $db->table('inventario_historico')
    ->select("
        SUM(CASE 
            WHEN LOWER(tipo) = 'entrada' THEN cantidad 
            WHEN LOWER(tipo) = 'salida'  THEN -cantidad
            ELSE 0
        END) AS stock
    ")
    ->where('producto_id', $productoId)
    ->where('branch_id',   (int)$data['origen_branch_id'])
    ->get()
    ->getRow();

$stockDisponible = (int)($result->stock ?? 0);

if ($stockDisponible < $cantidad) {
    $db->transRollback();
    return $this->response->setJSON([
        'status' => 'error',
        'msg'    => "Stock insuficiente para el producto ID {$productoId}. Disponible: {$stockDisponible}"
    ]);
}

                // Insertar detalle
                $detalleModel->insert([
                    'traslado_id' => $trasladoId,
                    'producto_id' => $productoId,
                    'cantidad'    => $cantidad,
                ]);

                $now = date('Y-m-d H:i:s');

                // Salida del origen
                $db->table('inventario_historico')->insert([
                    'producto_id' => $productoId,
                    'branch_id'   => (int)$data['origen_branch_id'],
                    'tipo'        => 'salida',
                    'cantidad'    => $cantidad,
                    'origen'      => 'traslado',
                    'origen_id'   => $trasladoId,
                    'usuario_id'  => $userId,
                    'created_at'  => $now,
                ]);

                // Entrada al destino
                $db->table('inventario_historico')->insert([
                    'producto_id' => $productoId,
                    'branch_id'   => (int)$data['destino_branch_id'],
                    'tipo'        => 'entrada',
                    'cantidad'    => $cantidad,
                    'origen'      => 'traslado',
                    'origen_id'   => $trasladoId,
                    'usuario_id'  => $userId,
                    'created_at'  => $now,
                ]);
            }

            // ── 3. Registrar gasto si hay costo ───────────────────
            if ($costoTraslado > 0 && $cuentaId) {
                $transactionModel = new \App\Models\TransactionModel();
                $transactionModel->addSalida(
                    $cuentaId,
                    $costoTraslado,
                    'traslado',
                    $trasladoId
                );
            }

            // ── 4. Bitácora ───────────────────────────────────────
            registrar_bitacora(
                'Traslado creado ID ' . $trasladoId,
                'Traslados',
                'Origen: ' . $data['origen_branch_id'] .
                    ' → Destino: ' . $data['destino_branch_id'] .
                    ' | Productos: ' . count($data['productos']) .
                    ' | Costo: $' . number_format($costoTraslado, 2),
                $userId
            );

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'msg'    => 'Error en la transacción'
                ]);
            }

            return $this->response->setJSON([
                'status' => 'ok',
                'id'     => $trasladoId
            ]);
        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'msg'    => $e->getMessage(),
                'line'   => $e->getLine()
            ]);
        }
    }

    // ── DETALLE / VER ─────────────────────────────────────────────
    public function ver(int $id)
    {
        $model        = new TrasladoModel();
        $detalleModel = new TrasladoDetalleModel();

        $traslado = $model->findConRelaciones($id);

        if (!$traslado) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $detalles = $detalleModel->porTraslado($id);

        return view('traslados/ver', compact('traslado', 'detalles'));
    }

    // ── BUSCAR PRODUCTOS (para el autocomplete del formulario) ────
    public function buscarProductos()
    {
        $q        = $this->request->getGet('q');
        $branchId = (int)$this->request->getGet('branch_id');

        if (!$q || !$branchId) {
            return $this->response->setJSON([]);
        }

        $db = \Config\Database::connect();

        $productos = $db->table('productos p')
            ->select('
                p.id,
                p.nombre,
                p.codigo_barras,
                COALESCE(SUM(CASE WHEN ih.tipo = "entrada" THEN ih.cantidad ELSE -ih.cantidad END), 0) AS stock
            ')
            ->join('inventario_historico ih', 'ih.producto_id = p.id AND ih.branch_id = ' . $branchId, 'left')
            ->groupStart()
            ->like('p.nombre', $q)
            ->orLike('p.codigo_barras', $q)
            ->groupEnd()
            ->groupBy('p.id')
            ->having('stock >', 0)
            ->limit(10)
            ->get()
            ->getResultObject();

        return $this->response->setJSON($productos);
    }
    public function searchAjax()
    {
        $model    = new TrasladoModel();
        $q        = $this->request->getGet('q');
        $estado   = $this->request->getGet('estado');
        $desde    = $this->request->getGet('fecha_desde');
        $hasta    = $this->request->getGet('fecha_hasta');
        $order    = $this->request->getGet('order') ?? 'DESC';
        $perPage  = (int)($this->request->getGet('perPage') ?? 10);

        $builder = $model->conRelaciones();

        if ($q) {
            $builder->groupStart()
                ->like('o.branch_name', $q)
                ->orLike('d.branch_name', $q)
                ->groupEnd();
        }

        if ($estado)  $builder->where('t.estado', $estado);
        if ($desde)   $builder->where('t.created_at >=', $desde . ' 00:00:00');
        if ($hasta)   $builder->where('t.created_at <=', $hasta . ' 23:59:59');

        $builder->orderBy('t.created_at', $order);

        // Contar total para pager
        $total     = (clone $builder)->countAllResults(false);
        $page      = (int)($this->request->getGet('page') ?? 1);
        $offset    = ($page - 1) * $perPage;

        $traslados = $builder->limit($perPage, $offset)->get()->getResultObject();

        // Pager manual
        $pager = \Config\Services::pager();
        $pager->makeLinks($page, $perPage, $total, 'default_full', 0, 'traslados');

        return view('traslados/_list', compact('traslados', 'pager'));
    }
}
