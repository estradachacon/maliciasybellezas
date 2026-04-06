<?php

namespace App\Models;

use CodeIgniter\Model;

class RemuneracionesDetalleModel extends Model
{
    protected $table = 'remuneraciones_detalle';
    protected $primaryKey = 'id';

    protected $returnType = 'object';

    protected $useTimestamps = false;

    protected $allowedFields = [
        'remuneracion_id',
        'paquete_id',
        'encomendista_id',
        'monto'
    ];
}