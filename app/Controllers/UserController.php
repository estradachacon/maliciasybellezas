<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UserModel;

class UserController extends BaseController
{
    protected $cashierModel;
    protected $branchModel;
    protected $userModel;
    protected $roleModel;

    public function __construct()
    {
        // Inicializa los modelos para su uso en las funciones
        $this->cashierModel = new \App\Models\CashierModel();
        $this->branchModel = new \App\Models\BranchModel();
        $this->userModel = new UserModel();
        $this->roleModel = new \App\Models\RoleModel();
    }
    public function index()
    {
        $chk = requerirPermiso('ver_usuarios');
        if ($chk !== true) return $chk;

        $userModel = new UserModel();

        // 👇 obtenemos filtros del request
        $roles = $this->request->getGet('roles');

        $builder = $userModel
            ->select('users.id, users.user_name, users.email, roles.nombre AS role_name, branches.branch_name AS branch_name, users.codigo AS codigo')
            ->join('roles', 'roles.id = users.role_id')
            ->join('branches', 'branches.id = users.branch_id');

        // 👇 aplicar filtro si viene algo
        if (!empty($roles)) {
            $builder->whereIn('roles.nombre', $roles);
        }

        $users = $builder->findAll();

        $data = [
            'users' => $users,
            'title' => 'Lista de Usuarios',
            'rolesSelected' => $roles // 👈 importante para mantener selección
        ];

        return view('users/index', $data);
    }
    public function new()
    {
        $chk = requerirPermiso('crear_usuarios');
        if ($chk !== true) return $chk;

        $branches = $this->branchModel->findAll();
        $roles = $this->roleModel->findAll();
        $users = $this->userModel->findAll();

        $data = [
            'title' => 'Crear usuario',
            'branches' => $branches,
            'roles' => $roles,
            'users' => $users
        ];
        return view('users/new', $data);
    }
    public function create()
    {
        $chk = requerirPermiso('crear_usuarios');
        if ($chk !== true) return $chk;

        helper(['form']);
        $session = session();

        $codigo = $this->request->getPost('codigo');

        // ✅ REGLAS
        $rules = [
            'user_name'     => 'required',
            'email'         => 'required|valid_email',
            'user_password' => 'required|min_length[4]',
            'role_id'       => 'required',
            'branch_id'     => 'required',
        ];

        if (!empty($codigo)) {
            $rules['codigo'] = 'min_length[2]|is_unique[users.codigo]';
        }

        // ✅ MENSAJES PERSONALIZADOS
        $messages = [
            'user_name' => [
                'required' => 'El nombre de usuario es obligatorio.',
            ],
            'email' => [
                'required'    => 'El correo electrónico es obligatorio.',
                'valid_email' => 'Debe ingresar un correo electrónico válido.',
            ],
            'user_password' => [
                'required'   => 'La contraseña es obligatoria.',
                'min_length' => 'La contraseña debe tener al menos 4 caracteres.',
            ],
            'codigo' => [
                'min_length' => 'El código debe tener al menos 2 caracteres.',
                'is_unique'  => 'El código ya está en uso.',
            ],
            'role_id' => [
                'required' => 'Debe seleccionar un rol.',
            ],
            'branch_id' => [
                'required' => 'Debe seleccionar una sucursal.',
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $password = $this->request->getPost('user_password');
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $data = [
            'user_name' => $this->request->getPost('user_name'),
            'email' => $this->request->getPost('email'),
            'user_password' => $hashedPassword,
            'role_id' => $this->request->getPost('role_id'),
            'branch_id' => $this->request->getPost('branch_id'),
            'codigo' => $codigo,
        ];

        $this->userModel->insert($data);

        registrar_bitacora(
            'Crear usuario',
            'Usuarios',
            'Se creó un nuevo usuario.',
            $session->get('user_id')
        );

        return redirect()->to('/users')->with('success', 'Usuario creado exitosamente.');
    }

    public function edit($id)
    {
        // 1. Obtener la caja a editar
        $users = $this->userModel->find($id);

        if (!$users) {
            return redirect()->to('/users')->with('error', 'Usuario no encontrado.');
        }

        // 2. Obtener la lista de ramas y usuarios (para los dropdowns)
        $branches = $this->branchModel->findAll();
        $roles = $this->roleModel->findAll();

        $data = [
            'user' => $users,
            'branches' => $branches,
            'roles' => $roles,
        ];

        // Se asume que tienes una vista en 'users/edit'
        return view('users/edit', $data);
    }

    public function update($id)
    {
        helper(['form']);
        $session = session();

        if (
            !$this->validate([
                'user_name' => 'required|min_length[3]|max_length[100]',
                'email' => 'required|min_length[3]|max_length[100]',
                'codigo' => 'required|min_length[2]|max_length[100]',
                'branch_id' => 'required|integer',
                'role_id' => 'required|integer',
            ])
        ) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $data = [
            'user_name' => $this->request->getPost('user_name'),
            'email' => $this->request->getPost('email'),
            'branch_id' => $this->request->getPost('branch_id'),
            'codigo' => $this->request->getPost('codigo'),
            'role_id' => $this->request->getPost('role_id'),
        ];

        $password = $this->request->getPost('user_password');
        if (!empty($password)) {
            $data['user_password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $this->userModel->update($id, $data);

        registrar_bitacora(
            'Editar usuario',
            'Usuarios',
            'Se editó el usuario con ID ' . esc($id) . '.',
            $session->get('user_id')
        );

        return redirect()->to('/users')->with('success', 'Usuario actualizado exitosamente.');
    }

    public function delete()
    {

        helper(['form']);
        $session = session();
        $id = $this->request->getPost('id');
        $userModel = new UserModel();

        if (!$id) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ID inválido.']);
        }

        if ($userModel->delete($id)) {
            registrar_bitacora(
                'Eliminó usuario',
                'Usuarios',
                'Se eliminó el usuario con ID ' . esc($id) . '.',
                $session->get('user_id')
            );
            return $this->response->setJSON(['status' => 'success', 'message' => 'Usuario eliminado correctamente.']);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'No se pudo eliminar el usuario.']);
    }
    public function search()
    {
        $term = $this->request->getGet('term');

        $userModel = new UserModel();
        if (
            !$this->validate(
                [
                    'user_name' => 'required|min_length[3]|max_length[100]',
                    'email' => 'required|valid_email',
                    'codigo' => 'required|min_length[2]|max_length[100]',
                    'branch_id' => 'required|integer',
                    'role_id' => 'required|integer',
                ],
                [
                    'user_name' => [
                        'required' => 'El nombre de usuario es obligatorio.',
                        'min_length' => 'El nombre debe tener al menos 3 caracteres.',
                        'max_length' => 'El nombre no puede exceder 100 caracteres.',
                    ],
                    'email' => [
                        'required' => 'El correo es obligatorio.',
                        'valid_email' => 'Debe ingresar un correo válido.',
                    ],
                    'codigo' => [
                        'required' => 'El código es obligatorio.',
                        'min_length' => 'El código debe tener al menos 2 caracteres.',
                    ],
                    'branch_id' => [
                        'required' => 'Debe seleccionar una sucursal.',
                        'integer' => 'Sucursal inválida.',
                    ],
                    'role_id' => [
                        'required' => 'Debe seleccionar un rol.',
                        'integer' => 'Rol inválido.',
                    ],
                ]
            )
        ) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }
        $users = $userModel
            ->select('id, user_name, email, codigo')
            ->groupStart()
            ->like('user_name', $term)
            ->orLike('email', $term)
            ->orLike('codigo', $term)
            ->groupEnd()
            ->findAll(10);

        $result = [];

        foreach ($users as $user) {
            $result[] = [
                'id' => $user['id'],
                'text' => $user['user_name'] . ' - ' . $user['email']
                    . (!empty($user['codigo']) ? ' (' . $user['codigo'] . ')' : '')
            ];
        }

        return $this->response->setJSON([
            'results' => $result
        ]);
    }
}
