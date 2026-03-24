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
            d.*,
            p.cliente_nombre,
            p.destino
        ')
            ->from('package_deposit_details d')
            ->join('paquetes p', 'p.id = d.package_id', 'left')
            ->where('d.deposit_id', $deposit_id)
            ->groupBy('d.package_id') 
            ->orderBy('d.id', 'DESC')
            ->findAll();
    }
}
