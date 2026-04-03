<?php

namespace App\Models;

use CodeIgniter\Model;

class TransactionModel extends Model
{
    protected $table      = 'transactions';
    protected $primaryKey = 'id';

    protected $useTimestamps = true;
    protected $returnType    = 'object';

    protected $allowedFields = [
        'account_id',
        'tipo',
        'monto',
        'origen',
        'origen_id',
    ];
    public function addEntrada($accountId, $monto, $origen = null, $origen_id = null)
    {
        return $this->insert([
            'account_id'  => $accountId,
            'tipo'        => 'entrada',
            'monto'       => $monto,
            'origen'      => $origen,
            'origen_id'  => $origen_id,
        ]);
    }

    public function addSalida($accountId, $monto, $origen = null, $origen_id = null)
    {
        return $this->insert([
            'account_id'  => $accountId,
            'tipo'        => 'salida',
            'monto'       => $monto,
            'origen'      => $origen,
            'origen_id'  => $origen_id,
        ]);
    }

    public function getByAccount($accountId)
    {
        return $this->where('account_id', $accountId)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }
    public function getTransactionsWithAccountName()
    {
        return $this->select('transactions.*, accounts.name AS account_name')
            ->join('accounts', 'accounts.id = transactions.account_id', 'left')
            ->orderBy('transactions.created_at', 'DESC')
            ->findAll();
    }
        public function getTransactionsWithAccountNamePaginated($perPage = 10)
    {
        return $this->select('transactions.*, accounts.name as account_name')
            ->join('accounts', 'accounts.id = transactions.account_id', 'left')
            ->orderBy('transactions.created_at', 'DESC')
            ->paginate($perPage);
    }
}
