<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductoPreciosModel extends Model
{
    protected $table            = 'producto_precios';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;

    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'producto_id',
        'cantidad_minima',
        'precio'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}