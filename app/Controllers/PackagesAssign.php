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

        // 🔥 VALIDAR SI YA ESTÁ ASIGNADO
        $existe = $this->db->table('package_deposit_details')
            ->select('deposit_id')
            ->where('package_id', $paquete->id)
            ->orderBy('id', 'DESC')
            ->get()
            ->getRow();

        if ($existe) {
            return $this->response->setJSON([
                'status' => 'used',
                'msg' => 'Este paquete ya fue asignado',
                'deposit_id' => $existe->deposit_id
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
    public function show($id)
    {
        $db = \Config\Database::connect();

        // 🔥 CABECERA
        $deposito = $db->table('package_deposits pd')
            ->select("
                pd.*,
                GROUP_CONCAT(DISTINCT e.encomendista_name SEPARATOR ', ') as encomendistas
            ")
            ->join('package_deposit_details pdd', 'pdd.deposit_id = pd.id', 'left')
            ->join('paquetes p', 'p.id = pdd.package_id', 'left')
            ->join('encomendistas e', 'e.id = p.encomendista_nombre', 'left')
            ->where('pd.id', $id)
            ->groupBy('pd.id')
            ->get()
            ->getRow();

        if (!$deposito) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("No encontrado");
        }

        $detalles = $db->table('package_deposit_details pdd')
            ->select("
                p.id,
                p.cliente_nombre,
                p.destino,
                p.total_real,

                pdd.valor_paquete,
                pdd.nuevo_estado,
                pdd.porcentaje,

                e.encomendista_name
            ")
            ->join('paquetes p', 'p.id = pdd.package_id')
            ->join('encomendistas e', 'e.id = p.encomendista_nombre', 'left')
            ->where('pdd.deposit_id', $id)
            ->get()
            ->getResult();

        return view('packages/assign/show', [
            'deposito' => $deposito,
            'detalles' => $detalles
        ]);
    }
    public function table()
    {
        $model = new PackageDepositModel();

        $builder = $model
            ->select("
                package_deposits.id,
                package_deposits.fecha,
                package_deposits.flete_total,
                package_deposits.created_at,

                COUNT(DISTINCT pdd.id) as cantidad_paquetes,

                GROUP_CONCAT(DISTINCT e.encomendista_name ORDER BY e.encomendista_name SEPARATOR ', ') as encomendistas,

                SUM(
                    CASE 
                        WHEN p.estado1 != 'cancelado' AND p.total IS NOT NULL THEN p.total
                        ELSE 0
                    END
                ) as total_cobrar
            ")
            ->join('package_deposit_details pdd', 'pdd.deposit_id = package_deposits.id', 'left')
            ->join('paquetes p', 'p.id = pdd.package_id', 'left')
            ->join('encomendistas e', 'e.id = p.encomendista_nombre', 'left')
            ->groupBy('package_deposits.id');

        // FILTROS
        $nombre = $this->request->getGet('nombre');
        $fecha  = $this->request->getGet('fecha');

        if ($nombre) {
            $builder->like('e.encomendista_name', $nombre);
        }

        if ($fecha) {
            $builder = $builder->where('DATE(fecha)', $fecha);
        }

        // PAGINACIÓN
        $deposits = $builder
            ->orderBy('id', 'DESC')
            ->paginate(10);

        $pager = $model->pager;

        // AJAX RESPONSE
        if ($this->request->isAJAX()) {

            $tbody = view('packages_assignation/_table_body', [
                'deposits' => $deposits
            ]);

            return $this->response->setJSON([
                'tbody' => $tbody,
                'pager' => $pager->links('default', 'bootstrap_full')
            ]);
        }

        // VIEW NORMAL
        return view('packages/assign/table', [
            'deposits' => $deposits,
            'pager'    => $pager
        ]);
    }
    
}
