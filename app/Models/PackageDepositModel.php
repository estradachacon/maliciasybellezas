<?php

namespace App\Models;

use CodeIgniter\Model;

class PackageDepositModel extends Model
{
    protected $table            = 'package_deposits';
    protected $primaryKey       = 'id';

    protected $useAutoIncrement = true;
    protected $returnType       = 'object';

    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'encomendista_nombre',
        'flete_total',
        'cantidad_paquetes',
        'fecha',
        'usuario_id'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    //MÉTODOS ÚTILES

    public function obtenerConDetalles($id)
    {
        return $this->select('package_deposits.*')
            ->where('package_deposits.id', $id)
            ->first();
    }
}