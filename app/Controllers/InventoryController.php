<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\BranchModel;
use App\Models\ProductosModel;
use App\Models\InventarioModel;

class InventoryController extends BaseController
{
    public function index()
    {
        $productoModel = new ProductosModel();
        $branchModel = new BranchModel();

        $q = trim($this->request->getGet('q') ?? '');

        // 🔥 BUSCADOR
        if ($q !== '') {
            $productoModel = $productoModel
                ->groupStart()
                ->like('nombre', $q)
                ->orLike('id', $q)
                ->orLike('proveedor', $q)
                ->groupEnd();
        }


        $productos = $productoModel
            ->orderBy('id', 'DESC')
            ->paginate(10);
        $productoModel->pager->setPath('inventory');
        $branches = $branchModel->findAll();

        // 🔥 INVENTARIO
        $inventarioModel = new InventarioModel();

        $inventarioRaw = $inventarioModel->findAll();
        $inventario = [];

        foreach ($inventarioRaw as $i) {
            $inventario[$i->producto_id][$i->sucursal_id] = $i->stock;
        }

        return view('inventory/index', [
            'productos' => $productos,
            'branches' => $branches,
            'inventario' => $inventario,
            'pager' => $productoModel->pager,
            'q' => $q
        ]);
    }

    public function create()
    {
        $model = new ProductosModel();

        $data = [
            'nombre' => trim($this->request->getPost('nombre')),
            'proveedor' => trim($this->request->getPost('proveedor')),
            'costo_inicial' => $this->request->getPost('costo_inicial'),
            'precio_venta' => $this->request->getPost('precio_venta'),
        ];

        if (!$data['nombre']) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Nombre requerido'
            ]);
        }

        $existe = $model->where('nombre', $data['nombre'])->first();

        if ($existe) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'El producto ya existe'
            ]);
        }

        $model->insert($data);

        return $this->response->setJSON([
            'status' => 'success'
        ]);
    }
}
