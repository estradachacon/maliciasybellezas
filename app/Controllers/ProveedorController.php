<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\ProveedorModel;

class ProveedorController extends BaseController
{
    public function index()
    {
        $model = new ProveedorModel();

        $data['proveedores'] = $model->findAll();

        return view('proveedores/index', $data);
    }

    public function store()
    {
        $model = new ProveedorModel();

        $model->insert([
            'nombre' => $this->request->getPost('nombre'),
            'telefono' => $this->request->getPost('telefono'),
            'direccion' => $this->request->getPost('direccion'),
        ]);

        return $this->response->setJSON([
            'status' => 'success'
        ]);
    }
    public function searchAjax()
    {
        $model = new ProveedorModel();
        $q = $this->request->getGet('q');

        $proveedores = $model
            ->like('nombre', $q)
            ->orLike('telefono', $q)
            ->findAll();

        return view('proveedores/_proveedores_list', [
            'proveedores' => $proveedores
        ]);
    }

    public function update()
    {
        $model = new ProveedorModel();

        $model->update($this->request->getPost('id'), [
            'nombre' => $this->request->getPost('nombre'),
            'telefono' => $this->request->getPost('telefono'),
            'direccion' => $this->request->getPost('direccion'),
        ]);

        return $this->response->setJSON([
            'status' => 'success'
        ]);
    }
    public function searchAjaxSelect()
    {
        $model = new ProveedorModel();

        $q = $this->request->getGet('term');

        $data = $model
            ->like('nombre', $q)
            ->findAll(10);

        return $this->response->setJSON(array_map(function ($p) {
            return [
                'id' => $p->id,
                'text' => $p->nombre
            ];
        }, $data));
    }
    public function storeAjax()
    {
        $model = new ProveedorModel();

        $id = $model->insert([
            'nombre' => $this->request->getPost('nombre'),
            'telefono' => $this->request->getPost('telefono'),
            'direccion' => $this->request->getPost('direccion'),
        ]);

        return $this->response->setJSON([
            'status' => 'success',
            'id' => (int)$id // 🔥 ESTA LÍNEA ES LA QUE TE SALVA TODO
        ]);
    }
}
