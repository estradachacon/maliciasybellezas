<?php

namespace App\Models;

use CodeIgniter\Model;

class InventarioHistoricoModel extends Model
{
    protected $table = 'inventario_historico';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'producto_id',
        'branch_id',
        'tipo',
        'cantidad',
        'origen',
        'origen_id',
        'usuario_id',
        'created_at'
    ];

    protected $useTimestamps = false;
    protected $returnType = 'object';
}