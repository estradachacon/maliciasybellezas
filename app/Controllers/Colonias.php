<?php

namespace App\Controllers;

use App\Models\DepartamentoModel;
use App\Models\MunicipioModel;
use App\Models\ColoniaModel;

class Colonias extends BaseController
{
    public function index()
    {
        $departamentoModel = new DepartamentoModel();

        $data['departamentos'] = $departamentoModel->getAll();

        return view('colonias/index', $data);
    }

    /**
     * Devuelve municipios por departamento (AJAX)
     */
    public function municipios($departamentoId)
    {
        $municipioModel = new MunicipioModel();
        $municipios = $municipioModel->getByDepartamento((int)$departamentoId);

        return $this->response->setJSON($municipios);
    }

    /**
     * Listado general (opcional para tabla)
     */
    public function listar()
    {
        $coloniaModel = new ColoniaModel();

        $data = $coloniaModel
            ->select('colonias.*, municipios.nombre as municipio, departamentos.nombre as departamento')
            ->join('municipios', 'municipios.id = colonias.municipio_id')
            ->join('departamentos', 'departamentos.id = municipios.departamento_id')
            ->orderBy('departamentos.nombre')
            ->orderBy('municipios.nombre')
            ->orderBy('colonias.nombre')
            ->findAll();

        return $this->response->setJSON($data);
    }
    public function filtrar($departamentoId, $municipioId)
    {
        $coloniaModel = new ColoniaModel();

        $data = $coloniaModel
            ->select('colonias.*, municipios.nombre as municipio, departamentos.nombre as departamento')
            ->join('municipios', 'municipios.id = colonias.municipio_id')
            ->join('departamentos', 'departamentos.id = municipios.departamento_id')
            ->where('municipios.id', $municipioId)
            ->where('departamentos.id', $departamentoId)
            ->orderBy('colonias.nombre')
            ->findAll();

        return $this->response->setJSON($data);
    }

    public function get($id)
    {
        $model = new ColoniaModel();
        return $this->response->setJSON($model->find($id));
    }

    public function update($id)
    {
        $model = new ColoniaModel();

        $model->update($id, [
            'nombre' => $this->request->getPost('nombre')
        ]);

        return $this->response->setJSON(['status' => 'ok']);
    }

    public function delete($id)
    {
        $model = new ColoniaModel();
        $model->delete($id);

        return $this->response->setJSON(['status' => 'ok']);
    }
    public function create()
{
    $model = new ColoniaModel();

    $municipioId = $this->request->getPost('municipio_id');
    $nombre      = trim($this->request->getPost('nombre'));

    if (!$municipioId || !$nombre) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Datos incompletos'
        ]);
    }

    $model->insert([
        'municipio_id' => $municipioId,
        'nombre'       => $nombre
    ]);

    return $this->response->setJSON([
        'status' => 'ok'
    ]);
}
}
