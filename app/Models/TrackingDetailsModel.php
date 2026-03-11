<?php

namespace App\Models;

use CodeIgniter\Model;

class TrackingDetailsModel extends Model
{
    protected $table = 'tracking_details';
    protected $primaryKey = 'id';

    protected $useTimestamps = true;
    protected $returnType = 'object';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $allowedFields = [
        'tracking_header_id',
        'package_id',
        'status',
        'delivered_at',
        'note'
    ];

    /**
     * Obtiene los detalles de un header específico junto con info del paquete
     */
    public function getDetailsWithPackages($trackingHeaderId)
    {
        return $this->select('
        tracking_details.*,
        packages.cliente,
        packages.tipo_servicio,
        packages.monto,
        packages.destino_personalizado,
        packages.toggle_pago_parcial,
        packages.flete_total,
        packages.flete_pagado,
        packages.lugar_recolecta_paquete,
        packages.id_puntofijo,
        packages.pago_cuenta,
        packages.estatus AS package_status,
        packages.flete_rendido AS flete_rendido,
        settled_points.point_name AS puntofijo_nombre,
        sellers.seller AS vendedor,
        accounts.name AS cuenta_nombre
    ')
            ->join('packages', 'packages.id = tracking_details.package_id', 'left')
            ->join('settled_points', 'settled_points.id = packages.id_puntofijo', 'left')
            ->join('sellers', 'sellers.id = packages.vendedor', 'left')

            // 🔥 ESTE ES EL JOIN NUEVO
            ->join('accounts', 'accounts.id = packages.pago_cuenta', 'left')

            ->where('tracking_details.tracking_header_id', $trackingHeaderId)
            ->orderBy('tracking_details.id', 'ASC')
            ->findAll();
    }

    public function getTrackingHistoryByPackage($packageId)
    {
        $horaLimite = date('Y-m-d') . ' 21:00:00';
        return $this->db->table('tracking_details')
            ->select('
            tracking_details.*,
            tracking_header.id AS tracking_id,
            tracking_header.date AS tracking_date,
            tracking_details.status AS tracking_status,
            users.user_name AS motorista,
            routes.route_name
        ')
            ->join('tracking_header', 'tracking_header.id = tracking_details.tracking_header_id', 'left')
            ->join('users', 'users.id = tracking_header.user_id', 'left')
            ->join('routes', 'routes.id = tracking_header.route_id', 'left')
            ->where('tracking_details.package_id', $packageId)
            ->where('tracking_details.updated_at >=', $horaLimite)
            ->orderBy('tracking_header.date', 'DESC')
            ->get()
            ->getResult();
    }
}
