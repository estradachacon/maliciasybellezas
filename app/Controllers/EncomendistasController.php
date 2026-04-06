<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\EncomendistasModel;

class EncomendistasController extends BaseController
{
    protected $EncomendistasModel;

    public function __construct()
    {
        $this->EncomendistasModel = new EncomendistasModel();
    }

    public function index()
    {
        $chk = requerirPermiso('ver_encomendistas');
        if ($chk !== true) return $chk;

        $q = trim($this->request->getGet('q') ?? '');
        $alpha = trim($this->request->getGet('alpha') ?? '');
        $perPage = intval($this->request->getGet('perPage') ?? 10);

        $builder = $this->EncomendistasModel;

        // BÚSQUEDA GENERAL
        if ($q !== '') {
            $builder = $builder
                ->groupStart()
                ->like('encomendista_name', $q)
                ->orLike('id', $q)
                ->groupEnd();
        }

        // FILTRO ALFABÉTICO
        if ($alpha !== '') {
            $builder = $builder->like('encomendista_name', $alpha, 'after');
        }

        $data = [
            'q' => $q,
            'alpha' => $alpha,
            'perPage' => $perPage,
            'encomendistas' => $builder->paginate($perPage),
            'pager' => $builder->pager,
        ];

        return view('encomendistas/index', $data);
    }



    public function new()
    {
        $chk = requerirPermiso('crear_encomendista');
        if ($chk !== true) return $chk;

        return view('encomendistas/create');
    }

    public function create()
    {
        helper(['form']);
        $session = session();
        $rules = [
            'encomendista_name' => 'required|min_length[3]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->EncomendistasModel->save([
            'encomendista_name' => $this->request->getPost('encomendista_name')
        ]);

        registrar_bitacora(
            'Crear encomendista',
            'Encomendistas',
            'Se creó un nuevo encomendista con ID ' . esc($this->EncomendistasModel->insertID()) . '.',
            $session->get('user_id')
        );

        return redirect()->to('/encomendistas')->with('success', 'Encomendista creado correctamente.');
    }

    public function edit($id)
    {
        $chk = requerirPermiso('editar_encomendista');
        if ($chk !== true) return $chk;

        // Obtener encomendista
        $encomendista = $this->EncomendistasModel->find($id);

        if (!$encomendista) {
            return redirect()->to('/encomendistas')
                ->with('error', 'Encomendista no encontrado.');
        }

        return view('encomendistas/edit', [
            'encomendista' => $encomendista
        ]);
    }

    /**
     * Procesa y actualiza los datos de la caja.
     * @param int $id El ID de la caja a actualizar (viene del segmento de la URL).
     */
    public function update($id)
    {
        helper(['form']);
        $session = session();

        // Validación
        if (
            !$this->validate([
                'encomendista_name' => 'required|min_length[3]|max_length[100]'
            ])
        ) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        // Datos
        $data = [
            'encomendista_name' => $this->request->getPost('encomendista_name')
        ];

        // Update
        $this->EncomendistasModel->update($id, $data);

        registrar_bitacora(
            'Editar encomendista',
            'Encomendistas',
            'Se editó el encomendista con ID ' . esc($id) . '.',
            $session->get('user_id')
        );

        return redirect()->to('/encomendistas')
            ->with('success', 'Encomendista actualizado correctamente.');
    }

    public function delete()
    {
        helper(['form']);
        $session = session();
        $id = $this->request->getPost('id');
        $sellerModel = new SellerModel();
        $db = db_connect();

        if (!$id) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'ID inválido.'
            ]);
        }

        // SI NO TIENE → eliminar
        if ($sellerModel->delete($id)) {

            registrar_bitacora(
                'Eliminó vendedor',
                'Vendedores',
                'Se eliminó el vendedor con ID ' . esc($id) . '.',
                $session->get('user_id')
            );

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Registro de vendedor eliminado correctamente.'
            ]);
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'No se pudo eliminar el vendedor.'
        ]);
    }

    public function search()
    {
        $term = $this->request->getGet('term');

        $model = new EncomendistasModel();
        $data = $model->searchEncomendista($term);

        return $this->response->setJSON(
            array_map(function ($e) {
                return [
                    'id' => $e->id,
                    'text' => $e->encomendista_name
                ];
            }, $data)
        );
    }

    public function createAjax()
    {
        $model = new EncomendistasModel();

        $nombre = trim($this->request->getPost('encomendista_name'));

        if (!$nombre) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'El nombre es obligatorio.'
            ]);
        }

        try {

            $id = $model->insert([
                'encomendista_name' => $nombre
            ]);

            return $this->response->setJSON([
                'status' => 'success',
                'data' => [
                    'id' => $id,
                    'text' => $nombre
                ]
            ]);
        } catch (\Throwable $e) {

            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
    public function searchAjax()
    {
        $q = trim($this->request->getGet('q') ?? '');
        $perPage = intval($this->request->getGet('perPage') ?? 10);

        $model = new EncomendistasModel();

        $builder = $model;

        // 🔍 BÚSQUEDA
        if ($q !== '') {
            $builder = $builder
                ->groupStart()
                ->like('encomendista_name', $q)
                ->orLike('id', $q)
                ->groupEnd();
        }

        $builder = $builder->orderBy('id', 'DESC');

        $data = [
            'q' => $q,
            'encomendistas' => $builder->paginate($perPage),
            'pager' => $builder->pager,
        ];

        return view('encomendistas/_table', $data);
    }
    public function searchAjaxAssign()
    {
        $q = trim($this->request->getPost('term') ?? '');

        $model = new EncomendistasModel();

        if ($q !== '') {
            $model = $model
                ->groupStart()
                ->like('encomendista_name', $q)
                ->orLike('id', $q)
                ->groupEnd();
        }

        $encomendistas = $model
            ->orderBy('id', 'DESC')
            ->findAll(10);

        $result = array_map(function ($e) {
            return [
                'id' => $e->id,
                'text' => $e->encomendista_name
            ];
        }, $encomendistas);

        return $this->response->setJSON($result);
    }
}
