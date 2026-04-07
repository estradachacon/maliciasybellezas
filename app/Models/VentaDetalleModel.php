<?php

namespace App\Models;

use CodeIgniter\Model;

class VentaDetalleModel extends Model
{
    protected $table      = 'venta_detalle';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'venta_id',
        'producto_id',
        'cantidad',
        'precio_unitario',
        'total'
    ];

    protected $returnType = 'object';
}