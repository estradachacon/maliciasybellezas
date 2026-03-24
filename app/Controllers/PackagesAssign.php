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
        $fleteTotal = $data['flete_total'] ?? 0;
        $encomendista = $data['encomendista'] ?? '';
        $tipo = $data['tipo'] ?? 'en_transito'; // o en_casillero

        if (empty($paquetes)) {
            return $this->response->setJSON([
                'status' => 'error',
                'msg' => 'No hay paquetes'
            ]);
        }

        // TRANSACCIÓN
        $this->db->transStart();

        // 1. CABECERA
        $depositId = $this->depositModel->insert([
            'encomendista_nombre' => $encomendista,
            'flete_total' => $fleteTotal,
            'cantidad_paquetes' => count($paquetes),
            'fecha' => date('Y-m-d H:i:s'),
            'usuario_id' => session('id')
        ]);

        // CALCULAR FLETE
        $totalValor = array_sum(array_column($paquetes, 'valor'));

        $detalles = [];

        foreach ($paquetes as $p) {

            $porcentaje = $totalValor > 0 ? ($p['valor'] / $totalValor) : 0;
            $fleteAsignado = $porcentaje * $fleteTotal;

            $detalles[] = [
                'deposit_id' => $depositId,
                'package_id' => $p['id'],
                'codigo_qr' => $p['codigoqr'],
                'valor_paquete' => $p['valor'],
                'porcentaje' => $porcentaje * 100,
                'flete_asignado' => $fleteAsignado,
                'nuevo_estado' => $tipo,
                'foto' => $p['foto'] ?? null
            ];

            // UPDATE PACKAGE
            $this->packageModel->update($p['id'], [
                'estado' => 'depositado',
                'sub_estado' => $tipo
            ]);
        }

        // 2. DETALLE MASIVO
        $this->detailModel->insertBatch($detalles);

        $this->db->transComplete();

        // ERROR
        if ($this->db->transStatus() === false) {
            return $this->response->setJSON([
                'status' => 'error',
                'msg' => 'Error al guardar'
            ]);
        }
        
        addPackLog($p['id'], 'Asignado a Encomendista por QR');
        // OK
        return $this->response->setJSON([
            'status' => 'ok',
            'deposit_id' => $depositId
        ]);
    }
}