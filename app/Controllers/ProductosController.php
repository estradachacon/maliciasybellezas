<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\ProductoModel;
use App\Models\BranchModel;

class ProductosController extends BaseController
{
    public function searchAjaxSelect()
    {
        $term = $this->request->getGet('term');

        $db = \Config\Database::connect();

        $builder = $db->table('productos p');

        $builder->select("
            p.id,
            p.nombre as text,
            p.precio,
            p.imagen,
            COALESCE(SUM(
                CASE 
                    WHEN ih.tipo = 'entrada' THEN ih.cantidad
                    WHEN ih.tipo = 'salida' THEN -ih.cantidad
                END
            ), 0) as stock
        ");

        $builder->join('inventario_historico ih', 'ih.producto_id = p.id', 'left');

        if ($term) {
            $builder->like('p.nombre', $term);
        }

        $builder->groupBy('p.id');
        $builder->limit(20);

        $productos = $builder->get()->getResult();

        return $this->response->setJSON($productos);
    }

    public function searchAjaxSelectStock()
    {
        $db = \Config\Database::connect();

        $q = $this->request->getGet('term');

        $puedeTodo = requerirPermiso('descargar_de_todos_los_stock') === true;
        $sucursalUsuario = session('branch_id');

        $builder = $db->table('productos p')
            ->select("
            p.id as producto_id,
            p.nombre,
            p.precio,
            p.imagen,
            i.branch_id,
            b.branch_name as sucursal_nombre,
            SUM(
                CASE 
                    WHEN LOWER(i.tipo) = 'entrada' THEN i.cantidad
                    WHEN LOWER(i.tipo) = 'salida' THEN -i.cantidad
                    ELSE 0
                END
            ) as stock
        ")
            ->join('inventario_historico i', 'i.producto_id = p.id', 'left')
            ->join('branches b', 'b.id = i.branch_id', 'left')
            ->like('p.nombre', $q)
            ->groupBy('p.id, p.nombre, p.precio, p.imagen, i.branch_id, b.branch_name');

        // 🔒 FILTRO POR SUCURSAL
        if (!$puedeTodo) {
            $builder->where('i.branch_id', $sucursalUsuario);
        }

        // 🔥 SOLO CON STOCK
        $builder->having('stock >', 0);

        $rows = $builder->get()->getResult();

        $result = [];

        foreach ($rows as $r) {

            $id = $puedeTodo
                ? $r->producto_id . '_' . $r->branch_id
                : $r->producto_id;

            $text = $r->nombre;

            if ($puedeTodo && !empty($r->sucursal_nombre)) {
                $text .= " - {$r->sucursal_nombre}";
            }

            $result[] = [
                'id' => $id,
                'producto_id' => $r->producto_id,
                'branch_id' => $r->branch_id,
                'text' => $text,
                'precio' => $r->precio,
                'stock' => (int) $r->stock,
                'imagen' => $r->imagen
            ];
        }

        return $this->response->setJSON($result);
    }
    public function storeAjax()
    {
        $model = new ProductoModel();

        $nombre = trim($this->request->getPost('nombre'));

        if (!$nombre) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'El nombre es obligatorio'
            ]);
        }

        $imagen = $this->request->getFile('imagen');
        $nombreImagen = null;

        if ($imagen && $imagen->isValid() && !$imagen->hasMoved()) {

            // 🔥 nombre único
            $nombreImagen = uniqid() . '.webp';

            // mover temporal
            $tempPath = WRITEPATH . 'uploads/' . $imagen->getName();
            $imagen->move(WRITEPATH . 'uploads');

            // 🔥 convertir a webp
            $source = imagecreatefromstring(file_get_contents($tempPath));
            imagewebp($source, FCPATH . 'upload/productos/' . $nombreImagen, 80);

            imagedestroy($source);
            unlink($tempPath);
        }

        $id = $model->insert([
            'nombre' => $nombre,
            'marca' => $this->request->getPost('marca'),
            'presentacion' => $this->request->getPost('presentacion'),
            'precio' => $this->request->getPost('precio') ?? 0,
            'descripcion' => $this->request->getPost('descripcion'),
            'imagen' => $nombreImagen
        ]);

        return $this->response->setJSON([
            'status' => 'success',
            'producto' => [
                'id' => $id,
                'nombre' => $nombre,
                'precio' => $this->request->getPost('precio') ?? 0,
                'imagen' => $nombreImagen
            ]
        ]);
    }
}
