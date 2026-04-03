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
        $model = new ProductoModel();

        $q = $this->request->getGet('term');

        $productos = $model
            ->like('nombre', $q)
            ->findAll(10);

        return $this->response->setJSON(array_map(function ($p) {
            return [
                'id' => $p->id,
                'text' => $p->nombre,
                'precio' => $p->precio,
                'imagen' => $p->imagen
            ];
        }, $productos));
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
