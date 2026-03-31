<?php

namespace App\Models;

use CodeIgniter\Model;

class InventarioModel extends Model
{
    protected $table = 'inventario';
    protected $primaryKey = 'id';
    protected $returnType = 'object';

    protected $allowedFields = [
        'producto_id',
        'branch_id',
        'stock'
    ];
}