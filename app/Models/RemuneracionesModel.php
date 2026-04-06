<?php

namespace App\Models;

use CodeIgniter\Model;

class RemuneracionesModel extends Model
{
    protected $table = 'remuneraciones';
    protected $primaryKey = 'id';

    protected $returnType = 'object';

    protected $useTimestamps = true;

    protected $allowedFields = [
        'fecha',
        'total',
        'cuenta',
        'usuario_id',
        'observaciones'
    ];
}