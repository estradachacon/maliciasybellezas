<?php

namespace App\Models;

use CodeIgniter\Model;

class PagoModel extends Model
{
    protected $table      = 'pagos';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'venta_id',
        'account_id',
        'monto',
        'created_at'
    ];

    protected $returnType = 'object';

    protected $useTimestamps = false;
}