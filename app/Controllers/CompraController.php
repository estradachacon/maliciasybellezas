<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CompraModel;
use App\Models\CompraDetalleModel;
use App\Models\InventarioHistoricoModel;
use App\Models\ProveedorModel;
use App\Models\ProductoModel;
use App\Models\BranchModel;
use App\Models\PagoCompraModel;
use App\Models\TransactionModel;

class CompraController extends BaseController
{
    public function index()
    {
        $compraModel = new CompraModel();

        $compras = $compraModel
            ->select('
                compras.*, 
                proveedores.nombre as proveedor,
                COUNT(compra_detalle.id) as total_items
            ')
            ->join('proveedores', 'proveedores.id = compras.proveedor_id')
            ->join('compra_detalle', 'compra_detalle.compra_id = compras.id', 'left')
            ->groupBy('compras.id')
            ->orderBy('compras.id', 'DESC')
            ->paginate(10);

        return view('compras/index', [
            'compras' => $compras,
            'pager'   => $compraModel->pager
        ]);
    }

    public function searchAjax()
    {
        $model = new CompraModel();

        $q = $this->request->getGet('q');
        $fechaDesde = $this->request->getGet('fecha_desde');
        $fechaHasta = $this->request->getGet('fecha_hasta');
        $aplicadaDesde = $this->request->getGet('aplicada_desde');
        $aplicadaHasta = $this->request->getGet('aplicada_hasta');
        $sort = $this->request->getGet('sort') ?? 'compras.id';
        $order = $this->request->getGet('order') ?? 'DESC';
        $perPage = $this->request->getGet('perPage') ?? 10;

        $builder = $model
            ->select('
            compras.*, 
            proveedores.nombre as proveedor,
            COUNT(compra_detalle.id) as total_items
        ')
            ->join('proveedores', 'proveedores.id = compras.proveedor_id')
            ->join('compra_detalle', 'compra_detalle.compra_id = compras.id', 'left');

        // 🔍 BUSCADOR
        if (!empty($q)) {
            $builder->groupStart()
                ->like('proveedores.nombre', $q)
                ->orLike('compras.id', $q)
                ->groupEnd();
        }

        // 📅 FECHA CREACIÓN
        if (!empty($fechaDesde)) {
            $builder->where('DATE(compras.created_at) >=', $fechaDesde);
        }

        if (!empty($fechaHasta)) {
            $builder->where('DATE(compras.created_at) <=', $fechaHasta);
        }

        // 📅 FECHA APLICADA
        if (!empty($aplicadaDesde)) {
            $builder->where('DATE(compras.fecha_compra) >=', $aplicadaDesde);
        }

        if (!empty($aplicadaHasta)) {
            $builder->where('DATE(compras.fecha_compra) <=', $aplicadaHasta);
        }

        // 🧠 GROUP
        $builder->groupBy('compras.id');

        switch ($sort) {

            case 'id':
                $builder->orderBy('compras.id', $order);
                break;

            case 'total':
                $builder->orderBy('compras.total', $order);
                break;

            case 'items':
                $builder->orderBy('total_items', $order);
                break;

            case 'fecha_aplicada':
                $builder->orderBy('compras.fecha_compra', $order);
                break;

            default:
                $builder->orderBy('compras.created_at', $order);
                break;
        }

        $compras = $builder->paginate($perPage);

        return view('compras/_compras_list', [
            'compras' => $compras,
            'pager'   => $model->pager
        ]);
    }

    public function create()
    {
        $proveedorModel = new ProveedorModel();
        $productoModel = new ProductoModel();
        $branchModel = new BranchModel(); // 👈 nuevo

        return view('compras/create', [
            'proveedores' => $proveedorModel->findAll(),
            'productos'   => $productoModel->findAll(),
            'branches'    => $branchModel->findAll() // 👈 esto faltaba
        ]);
    }

    public function store()
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $compraModel = new CompraModel();
        $detalleModel = new CompraDetalleModel();
        $inventarioModel = new InventarioHistoricoModel();
        $pagoModel = new PagoCompraModel();
        $transactionModel = new TransactionModel();

        $data = $this->request->getJSON(true);

        $proveedor_id = $data['proveedor_id'] ?? null;
        $productos    = $data['productos'] ?? [];
        $branch_id    = $data['branch_id'] ?? null;
        $observacion  = $data['observacion'] ?? '';
        $fecha_compra = $data['fecha_compra'] ?? date('Y-m-d');
        $pagos        = $data['pagos'] ?? [];

        // 🔥 VALIDACIONES
        if (!$productos || count($productos) === 0) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Debe agregar al menos un producto'
            ]);
        }

        if (!$proveedor_id || !$branch_id) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Datos incompletos'
            ]);
        }

        if (empty($pagos)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Debe agregar al menos un pago'
            ]);
        }

        // 🔥 CALCULAR TOTAL
        $total = 0;
        foreach ($productos as $p) {
            $total += $p['cantidad'] * $p['precio'];
        }

        // 🔥 VALIDAR PAGOS
        $totalPagado = 0;
        foreach ($pagos as $p) {

            if (empty($p['cuenta_id']) || empty($p['monto'])) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Pagos incompletos'
                ]);
            }

            $totalPagado += $p['monto'];
        }

        if (round($totalPagado, 2) !== round($total, 2)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Los pagos no cuadran con el total'
            ]);
        }

        // 🔥 GUARDAR COMPRA
        $compraId = $compraModel->insert([
            'proveedor_id' => $proveedor_id,
            'total' => $total,
            'branch_id'   => $branch_id,
            'usuario_id'  => session()->get('id'),
            'fecha_compra' => $fecha_compra,
            'observacion' => $observacion
        ]);

        // 🔥 DETALLE + INVENTARIO
        foreach ($productos as $p) {

            $subtotal = $p['cantidad'] * $p['precio'];

            $detalleModel->insert([
                'compra_id' => $compraId,
                'producto_id' => $p['producto_id'],
                'cantidad' => $p['cantidad'],
                'precio_unitario' => $p['precio'],
                'total' => $subtotal
            ]);

            $inventarioModel->insert([
                'producto_id' => $p['producto_id'],
                'tipo'        => 'entrada',
                'cantidad'    => $p['cantidad'],
                'origen'      => 'compra',
                'origen_id'   => $compraId,
                'referencia'  => 'Compra #' . $compraId,
                'branch_id'   => $branch_id,
                'usuario_id'  => session()->get('id'),
                'created_at'  => $fecha_compra . ' ' . date('H:i:s')
            ]);
        }

        // 🔥 PAGOS + TRANSACCIONES
        foreach ($pagos as $p) {

            // pago
            $pagoModel->insert([
                'compra_id' => $compraId,
                'cuenta_id' => $p['cuenta_id'],
                'monto'     => $p['monto'],
            ]);

            // 💰 salida de dinero
            $transactionModel->addSalida(
                $p['cuenta_id'],
                $p['monto'],
                'compra',
                $compraId
            );
        }

        // 🔥 BITÁCORA
        registrar_bitacora(
            'Creación de compra',
            'Compras',
            'Se creó la compra #' . $compraId . ' por $' . number_format($total, 2),
            session()->get('id')
        );

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error al guardar la compra'
            ]);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Compra registrada correctamente'
        ]);
    }

    public function show($id)
    {
        $compraModel = new CompraModel();
        $detalleModel = new CompraDetalleModel();
        $pagoModel = new PagoCompraModel();

        $compra = $compraModel
            ->select('compras.*, proveedores.nombre as proveedor_nombre, branches.branch_name, users.user_name as usuario')
            ->join('proveedores', 'proveedores.id = compras.proveedor_id')
            ->join('branches', 'branches.id = compras.branch_id')
            ->join('users', 'users.id = compras.usuario_id', 'left')
            ->where('compras.id', $id)
            ->first();

        $detalles = $detalleModel
            ->select('compra_detalle.*, productos.nombre as producto_nombre')
            ->join('productos', 'productos.id = compra_detalle.producto_id')
            ->where('compra_id', $id)
            ->findAll();

        $pagos = $pagoModel
            ->select('pagos_compra.*, accounts.name as cuenta_nombre')
            ->join('accounts', 'accounts.id = pagos_compra.cuenta_id', 'left')
            ->where('compra_id', $id)
            ->findAll();

        return view('compras/show', [
            'compra' => $compra,
            'detalles' => $detalles,
            'pagos' => $pagos
        ]);
    }
}
