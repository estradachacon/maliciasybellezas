<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CompraModel;
use App\Models\CompraDetalleModel;
use App\Models\InventarioHistoricoModel;
use App\Models\ProveedorModel;
use App\Models\ProductoModel;
use App\Models\BranchModel;

class CompraController extends BaseController
{
    public function index()
    {
        $compraModel = new CompraModel();

        $compras = $compraModel
            ->select('compras.*, proveedores.nombre as proveedor')
            ->join('proveedores', 'proveedores.id = compras.proveedor_id')
            ->orderBy('compras.id', 'DESC')
            ->paginate(10); 

        return view('compras/index', [
            'compras' => $compras,
            'pager'   => $compraModel->pager 
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

        $data = $this->request->getJSON(true);

        $proveedor_id = $data['proveedor_id'] ?? null;
        $productos    = $data['productos'] ?? [];
        $branch_id    = $data['branch_id'] ?? null;
        $observacion  = $data['observacion'] ?? '';
        $fecha_compra = $data['fecha_compra'] ?? date('Y-m-d');

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

        $total = 0;

        foreach ($productos as $p) {
            $total += $p['cantidad'] * $p['precio'];
        }

        // guardar compra
        $compraId = $compraModel->insert([
            'proveedor_id' => $proveedor_id,
            'total' => $total,
            'branch_id'   => $branch_id,
            'usuario_id'  => session()->get('id'),
            'fecha_compra' => $fecha_compra,
            'observacion' => $observacion
        ]);

        // 📦 detalle + inventario
        foreach ($productos as $p) {

            $subtotal = $p['cantidad'] * $p['precio'];

            // detalle
            $detalleModel->insert([
                'compra_id' => $compraId,
                'producto_id' => $p['producto_id'],
                'cantidad' => $p['cantidad'],
                'precio_unitario' => $p['precio'],
                'total' => $subtotal
            ]);

            // inventario (KARDEX)
            $inventarioModel->insert([
                'producto_id' => $p['producto_id'],
                'tipo'        => 'entrada',
                'cantidad'    => $p['cantidad'],
                'origen'      => 'compra',
                'origen_id'   => $compraId,
                'referencia'  => 'Compra #' . $compraId,
                'branch_id'   => $branch_id,
                'usuario_id'  => session()->get('id'),
                'created_at' => $fecha_compra . ' ' . date('H:i:s')
            ]);
        }

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
    public function searchAjax()
    {
        $model = new CompraModel();
        $q = $this->request->getGet('q');

        $compras = $model
            ->select('compras.*, proveedores.nombre as proveedor')
            ->join('proveedores', 'proveedores.id = compras.proveedor_id')
            ->groupStart()
            ->like('proveedores.nombre', $q)
            ->orLike('compras.id', $q)
            ->groupEnd()
            ->orderBy('compras.id', 'DESC')
            ->paginate(10);

        return view('compras/_compras_list', [
            'compras' => $compras,
            'pager'   => $model->pager
        ]);
    }
    public function show($id)
    {
        $compraModel = new CompraModel();
        $detalleModel = new CompraDetalleModel();

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

        return view('compras/show', [
            'compra' => $compra,
            'detalles' => $detalles
        ]);
    }
}
