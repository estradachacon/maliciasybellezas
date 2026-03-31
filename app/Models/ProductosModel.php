<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductosModel extends Model
{
    protected $table = 'productos';
    protected $primaryKey = 'id';

    protected $returnType = 'object';

    protected $allowedFields = [
        'nombre',
        'proveedor',
        'costo_inicial',
        'precio_venta',
        'estado'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // 🔥 VALIDACIONES
    protected $validationRules = [
        'nombre' => 'required|min_length[2]|max_length[150]',
        'precio_venta' => 'required|decimal'
    ];

    protected $validationMessages = [
        'nombre' => [
            'required' => 'El nombre es obligatorio'
        ],
        'precio_venta' => [
            'required' => 'El precio es obligatorio',
            'decimal' => 'El precio debe ser numérico'
        ]
    ];

    // =========================
    // 🔎 BÚSQUEDA (para futuro select2)
    // =========================
    public function search($term)
    {
        return $this->like('nombre', $term)
                    ->orLike('proveedor', $term)
                    ->orderBy('nombre', 'ASC')
                    ->findAll(10);
    }

    // =========================
    // 🔒 EVITAR DUPLICADOS
    // =========================
    public function existeProducto($nombre)
    {
        return $this->where('nombre', $nombre)->first();
    }

    // =========================
    // 📦 ACTIVOS
    // =========================
    public function activos()
    {
        return $this->where('estado', 1)
                    ->orderBy('nombre', 'ASC')
                    ->findAll();
    }
}