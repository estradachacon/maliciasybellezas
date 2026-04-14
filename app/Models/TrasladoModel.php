<?php

namespace App\Models;

use CodeIgniter\Model;

class TrasladoModel extends Model
{
    protected $table      = 'traslados';
    protected $primaryKey = 'id';

    protected $returnType     = 'object';
    protected $useSoftDeletes = false;
    protected $useTimestamps  = true;

    protected $allowedFields = [
        'origen_branch_id',
        'destino_branch_id',
        'usuario_id',
        'costo_traslado',
        'cuenta_id',
        'notas',
        'estado',
    ];

    // ── Traslados con nombres de sucursales y usuario ──────────────
    public function conRelaciones()
    {
        return $this->db->table('traslados t')
        ->select('
            t.*,
            o.branch_name AS origen_nombre,
            d.branch_name AS destino_nombre,
            u.user_name   AS usuario_nombre,
            COUNT(td.id)  AS total_productos
        ')
            ->join('traslado_detalles td', 'td.traslado_id = t.id', 'left')
            ->groupBy('t.id')
            ->join('branches o', 'o.id = t.origen_branch_id',  'left')
            ->join('branches d', 'd.id = t.destino_branch_id', 'left')
            ->join('users u',    'u.id = t.usuario_id',        'left')
            ->orderBy('t.created_at', 'DESC');
    }

    public function findConRelaciones(int $id)
    {
        return $this->db->table('traslados t')
            ->select('
                t.*,
                o.branch_name AS origen_nombre,
                d.branch_name AS destino_nombre,
                u.user_name   AS usuario_nombre,
                a.name        AS cuenta_nombre
            ')
            ->join('branches o',  'o.id = t.origen_branch_id',  'left')
            ->join('branches d',  'd.id = t.destino_branch_id', 'left')
            ->join('users u',     'u.id = t.usuario_id',        'left')
            ->join('accounts a',  'a.id = t.cuenta_id',         'left')
            ->where('t.id', $id)
            ->get()
            ->getRowObject();
    }
}
