<?php

namespace App\Models;

use CodeIgniter\Model;

class CompraModel extends Model
{
    protected $table = 'compras';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'proveedor_id',
        'branch_id',
        'total',
        'fecha_compra',
        'usuario_id',
        'observacion'
    ];

    protected $useTimestamps = true;

    protected $returnType = 'object';
}