<?php

namespace App\Models;

use CodeIgniter\Model;

class PackageModel extends Model
{
    protected $table = 'packages';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'vendedor',
        'cliente',
        'tipo_servicio',
        'destino_personalizado',
        'lugar_recolecta_paquete',
        'id_puntofijo',
        'fecha_ingreso',
        'fecha_entrega_personalizado',
        'fecha_entrega_puntofijo',
        'flete_total',
        'toggle_pago_parcial',
        'flete_pagado',
        'flete_pendiente',
        'nocobrar_pack_cancelado',
        'monto',
        'amount_paid',
        'foto',
        'comentarios',
        'fragil',
        'fecha_pack_entregado',
        'estatus',
        'estatus2',
        'pago_cuenta',
        'flete_rendido',
        'reenvios',
        'branch',
        'colonia_id',
        'user_id',
        'external_location_id'
    ];
    protected $updatedField = 'updated_at';
    protected $createdField = 'created_at';

    public function getFullPackage($id)
    {
        return $this->select('
            packages.*,
            puntos_fijos.nombre AS puntofijo_nombre
        ')
            ->join('puntos_fijos', 'puntos_fijos.id = packages.id_puntofijo', 'left')
            ->where('packages.id', $id)
            ->first();
    }
    public function getPackagesPendingPaymentBySeller(int $sellerId)
    {
        return $this->select([
            'id',
            'cliente',
            'monto',
            'flete_pendiente',
            'foto',
            'fecha_pack_entregado',
            'updated_at',
            'fecha_ingreso'
        ])
            ->where('vendedor', $sellerId)
            ->where('estatus', 'entregado')
            ->groupStart()
            ->where('monto >', 0)
            
            ->orWhere('flete_pendiente >', 0)
            ->groupEnd()
            ->orderBy('fecha_ingreso', 'ASC')
            ->findAll();
    }

    public function getMunicipiosConPaquetesPersonalizados()
    {
        return $this->db->table('packages p')
            ->select('m.id, m.nombre')
            ->join('colonias c', 'p.colonia_id = c.id')
            ->join('municipios m', 'c.municipio_id = m.id')
            ->where('p.destino_personalizado IS NOT NULL')
            ->groupBy('m.id, m.nombre')
            ->orderBy('m.nombre', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function getFletesPendientesModal(int $sellerId)
    {
        return $this->select([
                'id',
                'cliente',
                'COALESCE(flete_pendiente,0) AS flete_pendiente',
                'foto',
                'estatus',
                'fecha_ingreso',
                'COALESCE(monto,0) AS monto'
            ])
            ->where('vendedor', $sellerId)
            ->where('flete_rendido', 0)
            ->where('COALESCE(flete_pendiente,0) >', 0)
            ->where('COALESCE(monto,0) =', 0)
            ->orderBy('fecha_ingreso', 'ASC')
            ->findAll();
    }
}
