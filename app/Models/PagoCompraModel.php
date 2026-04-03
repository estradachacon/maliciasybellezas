<?php

namespace App\Models;

use CodeIgniter\Model;

class PagoCompraModel extends Model
{
    protected $table            = 'pagos_compra';
    protected $primaryKey      = 'id';

    protected $useAutoIncrement = true;

    protected $returnType      = 'object';
    protected $useSoftDeletes  = false;

    protected $allowedFields = [
        'compra_id',
        'cuenta_id',
        'monto',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // 🔥 Obtener pagos de una compra
    public function getByCompra($compraId)
    {
        return $this->where('compra_id', $compraId)->findAll();
    }

    // 🔥 Total pagado de una compra
    public function getTotalPagado($compraId)
    {
        return $this->selectSum('monto')
                    ->where('compra_id', $compraId)
                    ->first()
                    ->monto ?? 0;
    }
}