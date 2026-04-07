<?php

namespace App\Controllers;

use App\Models\ClienteModel;

class ClientesController extends BaseController
{
    // 📋 LISTADO
    public function index()
    {
        $model = new ClienteModel();
        $data['clientes'] = $model->findAll();

        return view('clientes/index', $data);
    }

    // ➕ FORM NUEVO
    public function new()
    {
        return view('clientes/new');
    }

    // 💾 GUARDAR
    public function create()
    {
        $model = new ClienteModel();

        $model->save([
            'nombre'    => $this->request->getPost('nombre'),
            'telefono'  => $this->request->getPost('telefono'),
            'email'     => $this->request->getPost('email'),
            'direccion' => $this->request->getPost('direccion'),
        ]);

        return redirect()->to('/clientes');
    }

    // ✏️ EDITAR
    public function edit($id)
    {
        $model = new ClienteModel();

        $data['cliente'] = $model->find($id);

        if (!$data['cliente']) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Cliente no encontrado");
        }

        return view('clientes/edit', $data);
    }

    // 🔄 ACTUALIZAR
    public function update($id)
    {
        $model = new ClienteModel();

        $model->update($id, [
            'nombre'    => $this->request->getPost('nombre'),
            'telefono'  => $this->request->getPost('telefono'),
            'email'     => $this->request->getPost('email'),
            'direccion' => $this->request->getPost('direccion'),
        ]);

        return redirect()->to('/clientes');
    }

    // 🗑️ ELIMINAR (soft delete)
    public function delete($id)
    {
        $model = new ClienteModel();

        $model->delete($id);

        return redirect()->to('/clientes');
    }

    // 🔍 BUSCAR (AJAX)
    public function buscar()
    {
        $term = $this->request->getGet('term');

        $model = new ClienteModel();

        $clientes = $model
            ->groupStart()
            ->like('nombre', $term)
            ->orLike('telefono', $term)
            ->groupEnd()
            ->findAll(10);

        $data = [];

        foreach ($clientes as $c) {
            $data[] = [
                'id'   => $c->id,
                'text' => $c->nombre . ($c->telefono ? ' - ' . $c->telefono : '')
            ];
        }

        return $this->response->setJSON($data);
    }
}
