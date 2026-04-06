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
        'codigoqr',
        'cliente_nombre',
        'cliente_telefono',
        'dia_entrega',
        'hora_inicio',
        'hora_fin',
        'destino',
        'encomendista_nombre',
        'precio',
        'total',
        'packlog',
        'tipo_venta',
        'estado1',
        'estado2',
        'foto',
        'envio',
        'descuento_global',
        'total_real',
        'vendedor_id',
        'reenvios'
    ];

    // (Opcional pero recomendado)
    protected $useSoftDeletes = false;

    // Formato de fechas
    protected $dateFormat = 'datetime';


}
