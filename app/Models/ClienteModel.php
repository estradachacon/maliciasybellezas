<?php

namespace App\Models;

use CodeIgniter\Model;

class ClienteModel extends Model
{
    protected $table = 'clientes';
    protected $primaryKey = 'id';

    // 🔹 Retornar como objeto
    protected $returnType = 'object';

    protected $allowedFields = [
        'nombre',
        'telefono',
        'email',
        'direccion',
    ];

    // 🔹 Timestamps
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}