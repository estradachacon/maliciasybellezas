<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\TransactionModel;


class TransactionsController extends BaseController
{
    public function index()
    {
        $chk = requerirPermiso('ver_transacciones');
        if ($chk !== true) return $chk;

        $perPage = 10;

        $model = new TransactionModel();

        $origen     = $this->request->getGet('origen');
        $tipo       = $this->request->getGet('tipo');
        $origen_id  = $this->request->getGet('origen_id');

        $model
            ->select('transactions.*, accounts.name as account_name')
            ->join('accounts', 'accounts.id = transactions.account_id', 'left')
            ->orderBy('transactions.created_at', 'DESC');

        if ($origen) {
            $model->where('transactions.origen', $origen);
        }

        if ($tipo) {
            $model->where('transactions.tipo', $tipo);
        }

        if ($origen_id) {
            $model->where('transactions.origen_id', $origen_id);
        }

        $transactions = $model->paginate($perPage);
        $pager = $model->pager;

        return view('transactions/index', [
            'transactions' => $transactions,
            'transactions2' => $transactions,
            'pager' => $pager
        ]);
    }

    public function addSalida()
    {
        helper(['form', 'bitacora', 'account']);
        $session = session();
        $request = service('request');

        $accountId = $request->getPost('account');
        $monto     = $request->getPost('gastoMonto');
        $origen = $request->getPost('gastoDescripcion');

        // Validación simple
        if (!$accountId || !$monto) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Datos incompletos'
            ], 400);
        }

        $montoSalida = -abs($monto);
        $balanceUpdate = updateAccountBalance($accountId, $montoSalida);
        if (!$balanceUpdate['status']) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No se pudo actualizar la cuenta: ' . $balanceUpdate['message']
            ]);
        }

        $transaction = new TransactionModel();

        // Registrar SALIDA
        $transaction->insert([
            'account_id'  => $accountId,
            'tracking_id' => null,
            'tipo'        => 'salida',
            'monto'       => $monto,
            'origen'      => $origen,
            'referencia'   => 'Gasto/Salida',
        ]);

        registrar_bitacora(
            'Creación de Gasto/Salida',
            'Finanzas',
            'Se registró un gasto/salida de  $' . $monto . '  en la cuenta ID ' . $accountId,
            $session->get('user_id')
        );
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Gasto registrado',
            'newBalance' => $balanceUpdate['newBalance']
        ]);
    }
}
