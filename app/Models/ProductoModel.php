<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductoModel extends Model
{
    protected $table = 'productos';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'nombre',
        'descripcion',
        'precio',
        'marca',
        'presentacion',
        'imagen',
        'codigo_barras',
    ];

    protected $useTimestamps = true;

    protected $returnType = 'object';
}