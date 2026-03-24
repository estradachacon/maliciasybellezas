<?php

namespace App\Models;

use CodeIgniter\Model;

class PackageDepositDetailModel extends Model
{
    protected $table            = 'package_deposit_details';
    protected $primaryKey       = 'id';

    protected $useAutoIncrement = true;
    protected $returnType       = 'object';

    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'deposit_id',
        'package_id',
        'codigo_qr',
        'valor_paquete',
        'porcentaje',
        'flete_asignado',
        'nuevo_estado',
        'foto'
    ];

    protected $useTimestamps = false;

    //MÉTODOS ÚTILES

    public function obtenerPorDeposito($deposit_id)
    {
        return $this->where('deposit_id', $deposit_id)
            ->findAll();
    }

    public function obtenerConPaquete($deposit_id)
    {
        return $this->select('
                package_deposit_details.*,
                packages.cliente_nombre,
                packages.destino
            ')
            ->join('packages', 'packages.id = package_deposit_details.package_id')
            ->where('deposit_id', $deposit_id)
            ->findAll();
    }
}