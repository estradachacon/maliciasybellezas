<?php

namespace App\Models;

use CodeIgniter\Model;

class CompraDetalleModel extends Model
{
    protected $table = 'compra_detalle';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'compra_id',
        'producto_id',
        'cantidad',
        'precio_unitario',
        'total'
    ];

    protected $useTimestamps = false; 

    protected $returnType = 'object';
}