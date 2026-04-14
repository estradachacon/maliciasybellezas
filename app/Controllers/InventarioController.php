<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\BranchModel;
use App\Models\ProductoModel;
use App\Models\InventarioHistoricoModel;

class InventarioController extends BaseController
{
    protected $productoModel;
    protected $branchModel;

    public function __construct()
    {
        $this->productoModel = new ProductoModel();
        $this->branchModel   = new BranchModel();
    }

    // 🧾 Vista principal de inventario
    public function index()
    {
        $chk = requerirPermiso('ver_inventario');
        if ($chk !== true) return $chk;

        $db = \Config\Database::connect();

        $builder = $db->table('productos p');

        $builder->select("
        p.*,
        COALESCE(SUM(
            CASE 
                WHEN ih.tipo = 'entrada' THEN ih.cantidad
                WHEN ih.tipo = 'salida' THEN -ih.cantidad
            END
        ), 0) as stock
    ");

        $builder->join('inventario_historico ih', 'ih.producto_id = p.id', 'left');
        $builder->groupBy('p.id');
        $builder->orderBy('p.id', 'DESC');

        // 🔥 PAGINACIÓN INICIAL
        $perPage = 10;
        $page = 1;

        // 🔢 TOTAL (para pager)
        $countBuilder = clone $builder;
        $countBuilder->select('COUNT(DISTINCT p.id) as total');
        $row = $countBuilder->get()->getRow();
        $total = $row->total ?? 0;

        // 📄 DATA
        $productos = $builder
            ->limit($perPage, 0)
            ->get()
            ->getResult();

        // 📚 PAGER
        $pager = \Config\Services::pager();
        $pager->makeLinks($page, $perPage, $total, 'default_full');

        return view('inventario/index', [
            'productos' => $productos,
            'pager'     => $pager,
            'branches'  => $this->branchModel->where('status', 1)->findAll()
        ]);
    }

    public function store()
    {
        $session = session();
        try {

            $request = $this->request;

            $rules = [
                'nombre' => 'required|min_length[3]',
                'precio' => 'required|decimal'
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => $this->validator->getErrors()
                ]);
            }

            $data = [
                'nombre'        => trim($request->getPost('nombre')),
                'marca'         => trim($request->getPost('marca')),
                'presentacion'  => trim($request->getPost('presentacion')),
                'descripcion'   => trim($request->getPost('descripcion')),
                'precio'        => $request->getPost('precio'),
                'codigo_barras' => trim($request->getPost('codigo_barras')),
            ];

            $file = $request->getFile('imagen');

            if ($file && $file->isValid() && !$file->hasMoved()) {

                $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];

                if (!in_array($file->getMimeType(), $allowedTypes)) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Formato de imagen no permitido'
                    ]);
                }

                if ($file->getSize() > 2 * 1024 * 1024) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'La imagen supera los 2MB'
                    ]);
                }

                $newName = $file->getRandomName();
                $file->move('upload/productos', $newName);

                $data['imagen'] = $newName;
            }

            $this->productoModel->insert($data);

            $id = $this->productoModel->getInsertID();

            registrar_bitacora(
                'Creación de producto',
                'Inventario',
                'Se creó el producto con ID ' . esc($id) . '.',
                $session->get('user_id')
            );

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Producto creado correctamente'
            ]);
        } catch (\Throwable $e) {

            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error interno: ' . $e->getMessage()
            ]);
        }
    }

    public function update()
    {
        $id = $this->request->getPost('id');

        if (!$id) {
            return $this->response->setJSON([
                'success' => false,
                'msg' => 'ID no recibido'
            ]);
        }

        $productoActual = $this->productoModel->find($id);

        $data = [
            'nombre'        => $this->request->getPost('nombre'),
            'marca'         => $this->request->getPost('marca'),
            'presentacion'  => $this->request->getPost('presentacion'),
            'precio'        => $this->request->getPost('precio'),
            'descripcion'   => $this->request->getPost('descripcion'),
            'codigo_barras' => trim($this->request->getPost('codigo_barras')),
        ];

        $cambios = [];

        foreach ($data as $campo => $nuevoValor) {

            $valorAnterior = $productoActual->$campo ?? null;

            if ($valorAnterior == $nuevoValor) {
                continue;
            }

            $cambios[] = ucfirst($campo) . ': "' . $valorAnterior . '" → "' . $nuevoValor . '"';
        }

        // Imagen
        $imagen = $this->request->getFile('imagen');

        if ($imagen && $imagen->isValid() && !$imagen->hasMoved()) {

            $nombreImagen = $imagen->getRandomName();
            $imagen->move('upload/productos', $nombreImagen);

            $data['imagen'] = $nombreImagen;

            $cambios[] = 'Imagen actualizada';
        }

        $this->productoModel->update($id, $data);

        $detalle = !empty($cambios)
            ? implode(', ', $cambios)
            : 'Sin cambios relevantes';

        $session = session();

        registrar_bitacora(
            'Actualización de producto',
            'Inventario',
            'Producto ID ' . $id . ': ' . $detalle,
            $session->get('user_id')
        );

        return $this->response->setJSON([
            'success' => true
        ]);
    }

    public function searchAjax()
    {
        $db = \Config\Database::connect();

        $q       = $this->request->getGet('q');
        $order   = $this->request->getGet('order');
        $stock   = $this->request->getGet('stock');
        $perPage = $this->request->getGet('perPage') ?? 10;
        $page    = $this->request->getGet('page') ?? 1;

        $builder = $db->table('productos p');

        $builder->select("
        p.*,
        COALESCE(SUM(
            CASE 
                WHEN ih.tipo = 'entrada' THEN ih.cantidad
                WHEN ih.tipo = 'salida' THEN -ih.cantidad
            END
        ), 0) as stock
    ");

        $builder->join('inventario_historico ih', 'ih.producto_id = p.id', 'left');

        // 🔍 BUSQUEDA
        if (!empty($q)) {
            $builder->groupStart()
                ->like('p.nombre', $q)
                ->orLike('p.descripcion', $q)
                ->groupEnd();
        }

        $builder->groupBy('p.id');

        // 📦 STOCK
        if ($stock === 'con_stock') {
            $builder->having('stock >', 0);
        }

        if ($stock === 'sin_stock') {
            $builder->having('stock', 0);
        }

        // 🔽 ORDEN
        switch ($order) {
            case 'precio_asc':
                $builder->orderBy('p.precio', 'ASC');
                break;

            case 'precio_desc':
                $builder->orderBy('p.precio', 'DESC');
                break;

            case 'stock_asc':
                $builder->orderBy('stock', 'ASC');
                break;

            case 'stock_desc':
                $builder->orderBy('stock', 'DESC');
                break;

            case 'alpha_asc':
                $builder->orderBy('p.nombre', 'ASC');
                break;

            case 'alpha_desc':
                $builder->orderBy('p.nombre', 'DESC');
                break;

            default:
                $builder->orderBy('p.id', 'DESC');
        }

        // 🧠 CLON PARA CONTAR
        $countBuilder = clone $builder;
        $total = count($countBuilder->get()->getResult());

        // 📄 PAGINACIÓN
        $offset = ($page - 1) * $perPage;

        $productos = $builder
            ->limit($perPage, $offset)
            ->get()
            ->getResult();

        // 🧾 CREAR PAGER MANUAL
        $pager = \Config\Services::pager();

        $pager->makeLinks($page, $perPage, $total, 'default_full');

        return view('inventario/_productos_table', [
            'productos' => $productos,
            'pager'     => $pager
        ]);
    }

    public function ver($id)
    {
        $db = \Config\Database::connect();

        // 🧾 PRODUCTO
        $producto = $this->productoModel->find($id);

        if (!$producto) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // TAB 1: ÚLTIMAS COMPRAS
        $compras = $db->table('compra_detalle cd')
            ->select("
                c.id,
                c.fecha_compra,
                p.nombre as proveedor,
                cd.cantidad,
                cd.precio_unitario,
                (cd.cantidad * cd.precio_unitario) as total_producto
            ")
            ->join('compras c', 'c.id = cd.compra_id')
            ->join('proveedores p', 'p.id = c.proveedor_id')
            ->where('cd.producto_id', $id)
            ->orderBy('c.fecha_compra', 'DESC')
            ->limit(5)
            ->get()
            ->getResult();

        // TAB 2: STOCK POR SUCURSAL
        $stockPorSucursal = $db->table('inventario_historico ih')
            ->select("
                ih.branch_id,
                b.branch_name,
                SUM(
                    CASE 
                        WHEN ih.tipo = 'entrada' THEN ih.cantidad
                        WHEN ih.tipo = 'salida' THEN -ih.cantidad
                    END
                ) as stock
            ")
            ->join('branches b', 'b.id = ih.branch_id', 'left')
            ->where('ih.producto_id', $id)
            ->groupBy('ih.branch_id')
            ->orderBy('stock', 'DESC')
            ->get()
            ->getResult();

        // TAB 3: KARDEX
        $movimientos = $db->table('inventario_historico')
            ->where('producto_id', $id)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getResult();

        $totalStock = array_sum(array_map(fn($s) => $s->stock, $stockPorSucursal));
        return view('inventario/ver', [
            'producto' => $producto,
            'compras' => $compras,
            'stockPorSucursal' => $stockPorSucursal,
            'movimientos' => $movimientos,
            'totalStock' => $totalStock
        ]);
    }
}
