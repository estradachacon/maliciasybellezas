<?php

namespace App\Models;

use CodeIgniter\Model;

class ExternalLocationModel extends Model
{
    protected $table      = 'external_locations';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'nombre',
        'descripcion',
        'activo'
    ];

    protected $useTimestamps = true;

    protected $returnType     = 'object';

    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}