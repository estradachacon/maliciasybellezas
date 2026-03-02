<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ExternalLocationModel;

class ExternalLocations extends BaseController
{
    protected ExternalLocationModel $locationModel;

    public function __construct()
    {
        $this->locationModel = new ExternalLocationModel();
    }

    public function index()
    {
        $data['locations'] = $this->locationModel->findAll();
        return view('external_locations/index', $data);
    }

    public function store()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $location = new \stdClass();

        $location->nombre      = $this->request->getPost('nombre');
        $location->descripcion = $this->request->getPost('descripcion');
        $location->activo      = $this->request->getPost('activo') ? 1 : 0;

        $this->locationModel->insert($location);

        return $this->response->setJSON([
            'success' => true
        ]);
    }

    public function update($id)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $location = $this->locationModel->find($id);

        if (!$location) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Registro no encontrado'
            ]);
        }

        $location->nombre      = $this->request->getPost('nombre');
        $location->descripcion = $this->request->getPost('descripcion');
        $location->activo      = $this->request->getPost('activo') ? 1 : 0;

        $this->locationModel->save($location);

        return $this->response->setJSON([
            'success' => true
        ]);
    }

    public function delete($id)
    {
        $location = $this->locationModel->find($id);

        if ($location) {
            $this->locationModel->delete($id);
        }

        return redirect()->to('/external-locations')
            ->with('success', 'Ubicación eliminada');
    }

    public function get($id)
    {
        $location = $this->locationModel->find($id);

        if (!$location) {
            return $this->response->setJSON([
                'success' => false
            ]);
        }

        return $this->response->setJSON($location);
    }
    public function listAjax()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $term = $this->request->getGet('q');

        $builder = $this->locationModel
            ->select('id, nombre')
            ->where('activo', 1);

        if (!empty($term)) {
            $builder->groupStart()
                ->like('nombre', $term)
                ->orLike('descripcion', $term)
                ->groupEnd();
        }

        $locations = $builder
            ->orderBy('nombre', 'ASC')
            ->findAll(20); // limit opcional

        return $this->response->setJSON($locations);
    }
}
