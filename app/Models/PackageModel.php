<?php

namespace App\Models;

use CodeIgniter\Model;

class PackageModel extends Model
{
    protected $table = 'paquetes';
    protected $primaryKey = 'id';

    protected $returnType = 'object';

    protected $useTimestamps = true;

    protected $allowedFields = [
        'cliente_nombre',
        'cliente_telefono',
        'dia_entrega',
        'hora_inicio',
        'hora_fin',
        'destino',
        'encomendista_nombre',
        'precio',
        'envio',
        'total',
        'foto',
        'tipo_venta',
        'estado1',
        'estado2',
        'codigoqr',
        'packlog'
    ];

    // (Opcional pero recomendado)
    protected $useSoftDeletes = false;

    // Formato de fechas
    protected $dateFormat = 'datetime';

    // Validaciones básicas (opcional pero 🔥 recomendado)
    protected $validationRules = [
        'cliente_nombre' => 'required|min_length[3]',
        'precio' => 'permit_empty|decimal',
        'envio' => 'permit_empty|decimal',
        'total' => 'permit_empty|decimal'
    ];

    protected $validationMessages = [
        'cliente_nombre' => [
            'required' => 'El nombre del cliente es obligatorio'
        ]
    ];
}
