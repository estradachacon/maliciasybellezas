<?php

namespace App\Models;

use CodeIgniter\Model;

class VentaModel extends Model
{
    protected $table      = 'ventas';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'fecha',
        'cliente_id',
        'branch_id',
        'tipo_venta',
        'total',
        'total_pagado',
        'saldo',
        'estado'
    ];

    protected $useTimestamps = true;

    protected $returnType = 'object';
}