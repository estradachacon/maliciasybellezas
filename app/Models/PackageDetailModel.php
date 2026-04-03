<?php

namespace App\Models;

use CodeIgniter\Model;

class PackageDetailModel extends Model
{
    protected $table = 'paquete_detalle';
    protected $primaryKey = 'id';

    protected $returnType = 'object';

    protected $useTimestamps = true;

    protected $allowedFields = [
        'paquete_id',
        'producto_id',
        'cantidad',
        'precio',
        'descuento',
        'subtotal'
    ];
}