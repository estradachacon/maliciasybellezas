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

    public function searchAjaxSelectCompras()
    {
        $term = $this->request->getGet('term');

        $db = \Config\Database::connect();

        $builder = $db->table('productos p');

        $builder->select("
            p.id,
            p.nombre as text,
            p.precio,
            p.imagen,

            (
                SELECT cd.precio
                FROM compras_detalle cd
                WHERE cd.producto_id = p.id
                ORDER BY cd.id DESC
                LIMIT 1
            ) as ultimo_costo,

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

    public function searchAjaxSelectStockBranch()
    {
        $db       = \Config\Database::connect();
        $q        = $this->request->getGet('term');
        $branchId = (int)$this->request->getGet('branch_id');

        if (!$q || !$branchId) {
            return $this->response->setJSON([]);
        }

        $rows = $db->table('productos p')
            ->select("
            p.id          AS producto_id,
            p.nombre,
            p.precio,
            p.imagen,
            i.branch_id,
            b.branch_name AS sucursal_nombre,
            SUM(
                CASE
                    WHEN LOWER(i.tipo) = 'entrada' THEN  i.cantidad
                    WHEN LOWER(i.tipo) = 'salida'  THEN -i.cantidad
                    ELSE 0
                END
            ) AS stock
        ")
            ->join('inventario_historico i', 'i.producto_id = p.id AND i.branch_id = ' . $branchId, 'inner')
            ->join('branches b',             'b.id = i.branch_id', 'left')
            ->groupStart()
            ->like('p.nombre',         $q)
            ->orLike('p.codigo_barras', $q)
            ->groupEnd()
            ->where('i.branch_id', $branchId)
            ->groupBy('p.id, p.nombre, p.precio, p.imagen, i.branch_id, b.branch_name')
            ->having('stock >', 0)
            ->get()
            ->getResult();

        $result = [];

        foreach ($rows as $r) {
            $result[] = [
                'id'          => $r->producto_id,
                'producto_id' => $r->producto_id,
                'branch_id'   => $r->branch_id,
                'text'        => $r->nombre,
                'precio'      => $r->precio,
                'stock'       => (int)$r->stock,
                'imagen'      => $r->imagen,
            ];
        }

        return $this->response->setJSON($result);
    }
    public function storeAjax()
    {
        try {

            $model = new ProductoModel();

            $nombre = trim($this->request->getPost('nombre'));
            $precio = $this->request->getPost('precio');
            $codigo = trim($this->request->getPost('codigo_barras'));

            // 🔥 VALIDACIONES
            if (!$nombre) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'El nombre es obligatorio'
                ]);
            }

            if (!$precio || $precio <= 0) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Precio inválido'
                ]);
            }

            // 🔥 GENERAR CÓDIGO SI NO VIENE
            if (!$codigo) {
                $codigo = 'P' . time();
            }

            // 🔥 VALIDAR DUPLICADO
            $existe = $model->where('codigo_barras', $codigo)->first();

            if ($existe) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'El código de barras ya existe'
                ]);
            }

            // 🖼️ IMAGEN
            $imagen = $this->request->getFile('imagen');
            $nombreImagen = null;

            if ($imagen && $imagen->isValid() && !$imagen->hasMoved()) {

                try {

                    $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];

                    if (!in_array($imagen->getMimeType(), $allowedTypes)) {
                        throw new \Exception('Formato de imagen no permitido');
                    }

                    $nombreImagen = uniqid() . '.webp';

                    $tempPath = WRITEPATH . 'uploads/' . $imagen->getName();
                    $imagen->move(WRITEPATH . 'uploads');

                    $contenido = file_get_contents($tempPath);

                    if (!$contenido) {
                        throw new \Exception('No se pudo leer la imagen');
                    }

                    $source = @imagecreatefromstring($contenido);

                    if (!$source) {
                        throw new \Exception('Error al procesar imagen');
                    }

                    if (!imagewebp($source, FCPATH . 'upload/productos/' . $nombreImagen, 80)) {
                        throw new \Exception('Error al guardar imagen');
                    }

                    imagedestroy($source);
                    unlink($tempPath);
                } catch (\Throwable $e) {

                    log_message('error', 'IMG ERROR: ' . $e->getMessage());

                    // 🔥 NO ROMPER EL FLUJO
                    $nombreImagen = null;
                }
            }

            // 💾 INSERT
            $id = $model->insert([
                'nombre'         => $nombre,
                'marca'          => $this->request->getPost('marca'),
                'presentacion'   => $this->request->getPost('presentacion'),
                'precio'         => $precio,
                'descripcion'    => $this->request->getPost('descripcion'),
                'codigo_barras'  => $codigo,
                'imagen'         => $nombreImagen
            ]);

            return $this->response->setJSON([
                'status' => 'success',
                'producto' => [
                    'id' => $id,
                    'nombre' => $nombre,
                    'precio' => $precio,
                    'imagen' => $nombreImagen,
                    'codigo_barras' => $codigo
                ]
            ]);
        } catch (\Throwable $e) {

            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    // ── BUSCAR POR CÓDIGO DE BARRAS (para escáner en venta) ──────
    public function buscarPorCodigo()
    {
        $codigo    = $this->request->getGet('codigo');
        $branchId  = (int)session('branch_id');
        $puedeTodo = requerirPermiso('descargar_de_todos_los_stock') === true;

        if (!$codigo) {
            return $this->response->setJSON(['found' => false]);
        }

        $db = \Config\Database::connect();

        $select = "
        p.id          AS producto_id,
        p.nombre,
        p.precio,
        p.imagen,
        p.codigo_barras,
        i.branch_id,
        b.branch_name AS sucursal_nombre,
        SUM(
            CASE
                WHEN LOWER(i.tipo) = 'entrada' THEN  i.cantidad
                WHEN LOWER(i.tipo) = 'salida'  THEN -i.cantidad
                ELSE 0
            END
        ) AS stock
    ";

        // 🔒 Buscar primero en sucursal del usuario
        $row = $db->table('productos p')
            ->select($select)
            ->join('inventario_historico i', 'i.producto_id = p.id', 'inner')
            ->join('branches b', 'b.id = i.branch_id', 'left')
            ->where('p.codigo_barras', $codigo)
            ->where('i.branch_id', $branchId)
            ->groupBy('p.id, p.nombre, p.precio, p.imagen, p.codigo_barras, i.branch_id, b.branch_name')
            ->having('stock >', 0)
            ->get()
            ->getRowObject();

        // 🔥 Si no hay en su sucursal y tiene permiso
        if (!$row && $puedeTodo) {
            $row = $db->table('productos p')
                ->select($select)
                ->join('inventario_historico i', 'i.producto_id = p.id', 'inner')
                ->join('branches b', 'b.id = i.branch_id', 'left')
                ->where('p.codigo_barras', $codigo)
                ->groupBy('p.id, p.nombre, p.precio, p.imagen, p.codigo_barras, i.branch_id, b.branch_name')
                ->having('stock >', 0)
                ->orderBy('stock', 'DESC')
                ->limit(1)
                ->get()
                ->getRowObject();
        }

        if (!$row) {
            $msg = $puedeTodo
                ? 'Producto no encontrado o sin stock'
                : 'Producto no encontrado en esta sucursal';

            return $this->response->setJSON([
                'found' => false,
                'msg'   => $msg
            ]);
        }

        // 🔥 FORMATEAR TEXTO IGUAL QUE SELECT2
        $text = $row->nombre;

        if ($puedeTodo && !empty($row->sucursal_nombre)) {
            $text .= " - {$row->sucursal_nombre}";
        }

        // 🔥 OFERTAS LIMPIAS
        $ofertas = $db->table('producto_precios')
            ->select('cantidad_minima, precio')
            ->where('producto_id', $row->producto_id)
            ->where('cantidad_minima >', 0)
            ->where('precio >', 0)
            ->orderBy('cantidad_minima', 'ASC')
            ->limit(5)
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'found'       => true,
            'id'          => $puedeTodo
                ? ($row->producto_id . '_' . $row->branch_id)
                : $row->producto_id,

            'producto_id' => (int)$row->producto_id,
            'branch_id'   => (int)$row->branch_id,
            'text'        => $text, // 🔥 ya con sucursal
            'precio'      => (float)$row->precio,
            'stock'       => (int)($row->stock ?? 0),
            'imagen'      => $row->imagen,
            'ofertas'     => $ofertas
        ]);
    }

    // ── OFERTAS DE PRECIOS POR PRODUCTO ──────────────────────────
    public function ofertasPorProducto()
    {
        $productoId = (int)$this->request->getGet('producto_id');

        if (!$productoId) {
            return $this->response->setJSON([]);
        }

        $db = \Config\Database::connect();

        $ofertas = $db->table('producto_precios')
            ->where('producto_id', $productoId)
            ->orderBy('cantidad_minima', 'ASC')
            ->limit(5)
            ->get()
            ->getResultArray();

        return $this->response->setJSON($ofertas);
    }
}
