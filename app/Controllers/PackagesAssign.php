<?php

namespace App\Controllers;

use App\Models\PackageDepositModel;
use App\Models\PackageDepositDetailModel;
use App\Models\PackageModel;
use CodeIgniter\Controller;

class PackagesAssign extends Controller
{
    protected $depositModel;
    protected $detailModel;
    protected $packageModel;
    protected $db;

    public function __construct()
    {
        $this->depositModel = new PackageDepositModel();
        $this->detailModel  = new PackageDepositDetailModel();
        $this->packageModel = new PackageModel();
        $this->db = \Config\Database::connect();
    }

    // Vista principal
    public function index()
    {
        return view('packages/assign/index'); // crea este view luego
    }

    // Buscar paquete por QR
    public function buscarPorQR()
    {
        $qr = $this->request->getPost('codigoqr');

        if (!$qr) {
            return $this->response->setJSON([
                'status' => 'error',
                'msg' => 'QR vacío'
            ]);
        }

        $paquete = $this->packageModel
            ->where('codigoqr', $qr)
            ->first();

        if (!$paquete) {
            return $this->response->setJSON([
                'status' => 'error',
                'msg' => 'Paquete no encontrado'
            ]);
        }

        return $this->response->setJSON([
            'status' => 'ok',
            'data' => $paquete
        ]);
    }

    // Guardar depósito masivo
    public function guardar()
    {
        $data = $this->request->getJSON(true);

        if (!$data) {
            return $this->response->setJSON([
                'status' => 'error',
                'msg' => 'Datos inválidos'
            ]);
        }

        $paquetes = $data['paquetes'] ?? [];
        $fleteTotal = floatval($data['flete_total'] ?? 0);

        if (empty($paquetes)) {
            return $this->response->setJSON([
                'status' => 'error',
                'msg' => 'No hay paquetes'
            ]);
        }

        $this->db->transStart();

        // CABECERA
        $depositId = $this->depositModel->insert([
            'flete_total' => $fleteTotal,
            'cantidad_paquetes' => count($paquetes),
            'fecha' => date('Y-m-d H:i:s'),
            'usuario_id' => session('id')
        ]);

        // 🔥 SEPARAR
        $paquetesConValor = [];
        $paquetesSinValor = [];

        foreach ($paquetes as $p) {
            if (floatval($p['valor']) > 0) {
                $paquetesConValor[] = $p;
            } else {
                $paquetesSinValor[] = $p;
            }
        }

        $costoFijo = 3;
        $detalles = [];

        $todosSinValor = count($paquetesConValor) === 0;

        // 🔥 CASO 1: TODOS SIN VALOR
        if ($todosSinValor) {

            $cantidad = count($paquetes);
            $fleteUnitario = $cantidad > 0 ? $fleteTotal / $cantidad : 0;

            foreach ($paquetes as $p) {

                $tipo = $p['estado'] === 'casillero' ? 'en_casillero' : 'en_ruta';

                $detalles[] = [
                    'deposit_id' => $depositId,
                    'package_id' => $p['id'],
                    'codigo_qr' => $p['codigoqr'],
                    'valor_paquete' => 0,
                    'porcentaje' => 0,
                    'flete_asignado' => round($fleteUnitario, 2),
                    'nuevo_estado' => $tipo,
                    'foto' => $p['foto'] ?? null
                ];

                $this->packageModel->update($p['id'], [
                    'estado1' => 'depositado',
                    'estado2' => $tipo
                ]);

                addPackLog(
                    $p['id'],
                    'Asignado (' . $tipo . ') - Flete: $' . number_format($fleteUnitario, 2)
                );
            }
        } else {

            // 🔥 CASO MIXTO / CON VALOR

            $totalSinValor = count($paquetesSinValor) * $costoFijo;
            $fleteRestante = $fleteTotal - $totalSinValor;

            if ($fleteRestante < 0) {
                $fleteRestante = 0;
            }

            $totalValor = array_sum(array_column($paquetesConValor, 'valor'));

            foreach ($paquetes as $p) {

                $valor = floatval($p['valor']);
                $tipo = $p['estado'] === 'casillero' ? 'en_casillero' : 'en_ruta';

                if ($valor <= 0) {
                    $fleteAsignado = $costoFijo;
                    $porcentaje = 0;
                } else {
                    $porcentaje = $totalValor > 0 ? ($valor / $totalValor) : 0;
                    $fleteAsignado = $porcentaje * $fleteRestante;
                }

                $detalles[] = [
                    'deposit_id' => $depositId,
                    'package_id' => $p['id'],
                    'codigo_qr' => $p['codigoqr'],
                    'valor_paquete' => $valor,
                    'porcentaje' => $porcentaje * 100,
                    'flete_asignado' => round($fleteAsignado, 2),
                    'nuevo_estado' => $tipo,
                    'foto' => $p['foto'] ?? null
                ];

                $this->packageModel->update($p['id'], [
                    'estado1' => 'depositado',
                    'estado2' => $tipo
                ]);

                addPackLog(
                    $p['id'],
                    'Asignado (' . $tipo . ') - Flete: $' . number_format($fleteAsignado, 2)
                );
            }
        }

        // INSERT MASIVO
        $this->detailModel->insertBatch($detalles);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return $this->response->setJSON([
                'status' => 'error',
                'msg' => 'Error al guardar'
            ]);
        }

        return $this->response->setJSON([
            'status' => 'ok',
            'deposit_id' => $depositId
        ]);
    }
}
