<?php

namespace App\Models;

use CodeIgniter\Model;

class TrasladoDetalleModel extends Model
{
    protected $table      = 'traslado_detalles';
    protected $primaryKey = 'id';

    protected $returnType     = 'object';
    protected $useSoftDeletes = false;
    protected $useTimestamps  = true;

    protected $allowedFields = [
        'traslado_id',
        'producto_id',
        'cantidad',
    ];

    // ── Detalles con nombre de producto ───────────────────────────
    public function porTraslado(int $trasladoId)
    {
        return $this->db->table('traslado_detalles td')
            ->select('
                td.*,
                p.nombre AS producto_nombre,
                p.codigo_barras
            ')
            ->join('productos p', 'p.id = td.producto_id', 'left')
            ->where('td.traslado_id', $trasladoId)
            ->get()
            ->getResultObject();
    }
}