<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\PackageModel;

class PaymentController extends BaseController
{
    public function packagesBySeller($sellerId)
    {
        $packageModel = new PackageModel();
        return $this->response->setJSON(
            $packageModel->getPackagesPendingPaymentBySeller((int)$sellerId)
        );
    }

    public function paySeller()
    {
        helper(['form']);
        $session = session();

        $db = db_connect();
        $data = $this->request->getJSON(true);

        if (
            !$data ||
            !isset($data['seller_id']) ||
            !isset($data['packages'])
        ) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Datos incompletos'
            ]);
        }

        if (count($data['packages']) === 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No hay paquetes seleccionados'
            ]);
        }

        $sellerId = (int) $data['seller_id'];
        $packages = $data['packages'];

        // ===============================
        // 1️⃣ VALIDAR SESIÓN DE CAJA
        // ===============================

        $cashierSession = $db->table('cashier_sessions')
            ->where('status', 'open')
            ->where('user_id', $session->get('id'))
            ->get()
            ->getRowArray();

        if (!$cashierSession) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No hay una sesión de caja abierta'
            ]);
        }

        $cashier = $db->table('cashier')
            ->where('id', $cashierSession['cashier_id'])
            ->get()
            ->getRowArray();

        if (!$cashier) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Caja no encontrada'
            ]);
        }

        // ===============================
        // 2️⃣ CALCULAR TOTALES
        // ===============================

        $totalSalida  = 0;
        $totalEntrada = 0;
        $packagesDB = [];

        foreach ($packages as $pkg) {

            $rawId = $pkg['id'];

            if (strpos($rawId, 'flete-') === 0) {
                $rawId = str_replace('flete-', '', $rawId);
            }

            $packageId = (int) $rawId;

            $package = $db->table('packages')
                ->where('id', $packageId)
                ->where('vendedor', $sellerId)
                ->get()
                ->getRowArray();

            if (!$package) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Paquete inválido: #' . $pkg['id']
                ]);
            }

            $monto     = (float) ($package['monto'] ?? 0);
            $pendiente = (float) ($package['flete_pendiente'] ?? 0);

            $totalSalida  += $monto;
            $totalEntrada += $pendiente;

            $packagesDB[] = [
                'id' => $packageId,
                'monto' => $monto,
                'pendiente' => $pendiente,
                'estatus' => $package['estatus'] ?? null
            ];
        }

        // ===============================
        // 3️⃣ VALIDAR CAJA
        // ===============================

        $totalNeto = $totalSalida - $totalEntrada;

        if ($totalNeto <= 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'El total de pago no es válido por ser menor o igual a cero'
            ]);
        }

        if ($totalNeto > (float)$cashier['current_balance']) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Saldo insuficiente en caja'
            ]);
        }

        // ===============================
        // 4️⃣ INICIAR TRANSACCIÓN
        // ===============================

        $db->transStart();

        foreach ($packagesDB as $package) {

            $builder = $db->table('packages')
                ->where('id', $package['id'])
                ->set('amount_paid', $package['monto'])
                ->set('flete_pagado', "COALESCE(flete_pagado,0) + {$package['pendiente']}", false)
                ->set('flete_pendiente', 0)
                ->set('metodo_remu', 'caja')
                ->set('remu_user_id', $session->get('id'));

            // SOLO FINALIZAR SI YA ESTABA ENTREGADO
            if ($package['estatus'] === 'entregado') {

                $builder
                    ->set('estatus', 'finalizado')
                    ->set('estatus2', 'remunerado')
                    ->set('fecha_remu', date('Y-m-d H:i:s'));
            }

            $builder->update();
        }

        $cashierMovementModel = new \App\Models\CashierMovementModel();

        $currentBalance = (float) $cashier['current_balance'];

        // ===============================
        // 5️⃣ SALIDA BRUTA
        // ===============================

        $balanceAfterOut = $currentBalance - $totalSalida;

        if ($totalSalida > 0) {
            $cashierMovementModel->insert([
                'cashier_id'         => $cashier['id'],
                'cashier_session_id' => $cashierSession['id'],
                'user_id'            => $session->get('id'),
                'branch_id'          => $session->get('branch_id'),
                'type'               => 'out',
                'amount'             => $totalSalida,
                'balance_after'      => $balanceAfterOut,
                'concept'            => 'Pago bruto vendedor #' . $sellerId,
                'reference_type'     => 'Remuneraciones',
                'created_at'         => date('Y-m-d H:i:s'),
            ]);
        }

        // ===============================
        // 6️⃣ ENTRADA POR FLETES
        // ===============================

        $balanceAfterIn = $balanceAfterOut + $totalEntrada;

        if ($totalEntrada > 0) {
            $cashierMovementModel->insert([
                'cashier_id'         => $cashier['id'],
                'cashier_session_id' => $cashierSession['id'],
                'user_id'            => $session->get('id'),
                'branch_id'          => $session->get('branch_id'),
                'type'               => 'in',
                'amount'             => $totalEntrada,
                'balance_after'      => $balanceAfterIn,
                'concept'            => 'Cobro fletes vendedor #' . $sellerId,
                'reference_type'     => 'Fletes',
                'created_at'         => date('Y-m-d H:i:s'),
            ]);
        }

        // ===============================
        // 7️⃣ ACTUALIZAR SALDO CAJA
        // ===============================

        $db->table('cashier')
            ->where('id', $cashier['id'])
            ->update([
                'current_balance' => $balanceAfterIn
            ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al procesar el pago'
            ]);
        }

        registrar_bitacora(
            'Pago a vendedor ID ' . esc($sellerId),
            'Remuneraciones',
            'Salida: $' . number_format($totalSalida, 2) .
                ' | Entrada por fletes: $' . number_format($totalEntrada, 2) .
                ' | Neto: $' . number_format($totalNeto, 2),
            $session->get('id')
        );

        return $this->response->setJSON([
            'success'     => true,
            'total_paid'  => $totalNeto,
            'new_balance' => $balanceAfterIn
        ]);
    }

    public function paySellerbyAccount()
    {
        helper(['form']);
        $session = session();

        $db = db_connect();
        $data = $this->request->getJSON(true);

        if (
            !$data ||
            empty($data['seller_id']) ||
            empty($data['packages']) ||
            empty($data['cuenta_id'])
        ) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Datos incompletos'
            ]);
        }

        $sellerId = (int) $data['seller_id'];
        $packages = $data['packages'];
        $accountId = (int) $data['cuenta_id'];

        $db->transStart();

        $totalSalida  = 0; // Total que sale
        $totalEntrada = 0; // Total que entra (fletes)

        foreach ($packages as $pkg) {

            $rawId = $pkg['id'];

            // 🔹 Limpiar si viene como flete-123
            if (strpos($rawId, 'flete-') === 0) {
                $rawId = str_replace('flete-', '', $rawId);
            }

            $packageId = (int) $rawId;

            $package = $db->table('packages')
                ->where('id', $packageId)
                ->where('vendedor', $sellerId)
                ->get()
                ->getRowArray();

            if (!$package) {
                $db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Paquete inválido: #' . $pkg['id']
                ]);
            }

            $monto     = (float) ($package['monto'] ?? 0);
            $pendiente = (float) ($package['flete_pendiente'] ?? 0);

            $totalSalida  += $monto;
            $totalEntrada += $pendiente;

            $packagesDB[] = [
                'id' => $packageId,
                'monto' => $monto,
                'pendiente' => $pendiente,
                'estatus' => $package['estatus'] ?? null
            ];

            $builder = $db->table('packages')
                ->where('id', $packageId)
                ->set('amount_paid', $monto)
                ->set('flete_pagado', "COALESCE(flete_pagado,0) + {$pendiente}", false)
                ->set('flete_pendiente', 0)
                ->set('metodo_remu', 'cuenta')
                ->set('remu_user_id', $session->get('id'));

            // SOLO FINALIZAR SI YA ESTABA ENTREGADO
            if ($package['estatus'] === 'entregado') {

                $builder
                    ->set('estatus', 'finalizado')
                    ->set('estatus2', 'remunerado')
                    ->set('fecha_remu', date('Y-m-d H:i:s'));
            }

            $builder->update();
        }

        // 🔹 Validar cuenta
        $account = $db->table('accounts')
            ->where('id', $accountId)
            ->get()
            ->getRowArray();

        if (!$account) {
            $db->transRollback();
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Cuenta seleccionada no encontrada'
            ]);
        }

        // 🔹 Actualizar balance correctamente
        $db->table('accounts')
            ->where('id', $accountId)
            ->set('balance', "balance - {$totalSalida} + {$totalEntrada}", false)
            ->update();

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al procesar el pago'
            ]);
        }

        // 🔹 Registrar movimientos separados

        if ($totalSalida > 0) {
            registrarSalida(
                $accountId,
                $totalSalida,
                "Remuneración vendedor ID {$sellerId}",
                "Pago de paquetes: ID " . implode(', ', array_column($packages, 'id')),
                '-'
            );
        }

        if ($totalEntrada > 0) {
            registrarEntrada(
                $accountId,
                $totalEntrada,
                "Cobro fletes vendedor ID {$sellerId}, paquetes: ID " . implode(', ', array_column($packages, 'id')),
                "Descuento por flete pendiente",
                '-'
            );
        }

        $totalNeto = $totalSalida - $totalEntrada;

        registrar_bitacora(
            'Pago a vendedor ID ' . esc($sellerId),
            'Remuneraciones por cuenta',
            'Salida: $' . number_format($totalSalida, 2) .
                ' | Entrada por fletes: $' . number_format($totalEntrada, 2) .
                ' | Neto: $' . number_format($totalNeto, 2),
            $session->get('id')
        );

        return $this->response->setJSON([
            'success'    => true,
            'total_paid' => $totalNeto
        ]);
    }

    public function fletesPendientesBySeller($sellerId)
    {
        $packageModel = new PackageModel();

        $data = $packageModel->getFletesPendientesModal((int) $sellerId);

        return $this->response->setJSON($data);
    }
}
