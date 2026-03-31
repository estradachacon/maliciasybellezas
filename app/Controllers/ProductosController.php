<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\ProductosModel;
use App\Models\BranchModel;

class ProductosController extends BaseController
{
    public function get($id)
    {
        $model = new ProductosModel();

        return $this->response->setJSON(
            $model->find($id)
        );
    }
    public function update($id)
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

        $model->update($id, $data);

        return $this->response->setJSON([
            'status' => 'success'
        ]);
    }
    public function create()
    {
        try {

            $productoModel = new ProductosModel();
            $branchModel = new BranchModel();

            $data = [
                'nombre' => trim($this->request->getPost('nombre')),
                'proveedor' => trim($this->request->getPost('proveedor')),
                'costo_inicial' => $this->request->getPost('costo_inicial') ?? 0,
                'precio_venta' => $this->request->getPost('precio_venta'),
                'estado' => 1
            ];

            if (!$data['nombre']) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Nombre requerido'
                ]);
            }

            if (!$data['precio_venta'] || $data['precio_venta'] <= 0) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Precio inválido'
                ]);
            }

            $existe = $productoModel->where('nombre', $data['nombre'])->first();

            if ($existe) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'El producto ya existe'
                ]);
            }

            $productoId = $productoModel->insert($data);

            if (!$productoId) {
                throw new \Exception('Error al insertar producto');
            }

            // 🔥 INVENTARIO
            $branches = $branchModel->findAll();
            $db = \Config\Database::connect();

            foreach ($branches as $b) {
                $db->table('inventario')->insert([
                    'producto_id' => $productoId,
                    'sucursal_id' => $b->id,
                    'stock' => 0
                ]);
            }

            return $this->response->setJSON([
                'status' => 'success'
            ]);
        } catch (\Throwable $e) {

            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}
