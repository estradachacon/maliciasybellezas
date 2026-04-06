<?php

use App\Models\TransactionModel;

use App\Models\AccountModel;

function actualizarSaldoCuenta($accountId)
{
    $accountModel = new AccountModel();

    // 🔹 ENTRADAS
    $transactionModelEntradas = new TransactionModel();
    $entradasData = $transactionModelEntradas
        ->where('account_id', $accountId)
        ->where('tipo', 'entrada')
        ->selectSum('monto')
        ->first();

    $entradas = $entradasData->monto ?? 0;

    // 🔹 SALIDAS
    $transactionModelSalidas = new TransactionModel();
    $salidasData = $transactionModelSalidas
        ->where('account_id', $accountId)
        ->where('tipo', 'salida')
        ->selectSum('monto')
        ->first();

    $salidas = $salidasData->monto ?? 0;

    $nuevoSaldo = floatval($entradas) - floatval($salidas);

    return $accountModel->update($accountId, [
        'balance' => $nuevoSaldo
    ]);
}

function registrarEntrada($accountId, $monto, $origen = null, $origen_id = null)
{
    $model = new TransactionModel();

    $monto = floatval($monto);

    // ❌ No registrar montos inválidos
    if ($monto <= 0) {
        return false;
    }

    return $model->insert([
        'account_id' => $accountId,
        'tipo'       => 'entrada',
        'monto'      => $monto,
        'origen'     => $origen,
        'origen_id'  => $origen_id,
    ]);
}

function registrarSalida($accountId, $monto, $origen = null, $origen_id = null)
{
    $model = new TransactionModel();

    $monto = floatval($monto);

    // ❌ No registrar montos vacíos o cero
    if ($monto <= 0) {
        return false;
    }

    $model->insert([
        'account_id' => $accountId,
        'tipo' => 'salida',
        'monto' => $monto,
        'origen' => $origen,
        'origen_id' => $origen_id,
    ]);
}
