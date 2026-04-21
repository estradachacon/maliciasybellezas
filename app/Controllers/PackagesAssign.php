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

        $paquete = $this->db->table('paquetes p')
            ->select('
            p.*,
            e.encomendista_name AS encomendista_nombre_texto
        ')
            ->join('encomendistas e', 'e.id = p.encomendista_nombre', 'left')
            ->where('p.codigoqr', $qr)
            ->get()
            ->getRow();
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

        registrarSalida(
            1,
            $fleteTotal,
            'deposito_paquetes',
            $depositId
        );

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

                $valor = 0;
                $porcentaje = 0;
                $fleteAsignado = $fleteUnitario;

                $detalles[] = [
                    'deposit_id' => $depositId,
                    'package_id' => $p['package_id'],
                    'valor_paquete' => $valor,
                    'porcentaje' => $porcentaje,
                    'flete_asignado' => round($fleteAsignado, 2),
                    'nuevo_estado' => $tipo,
                    'created_at' => date('Y-m-d H:i:s')
                ];

                $this->packageModel->update($p['package_id'], [
                    'estado1' => 'depositado',
                    'estado2' => $tipo,
                    'encomendista_nombre' => $p['encomendista_id'] ?? null
                ]);

                // 🔥 LOG UNIFICADO
                $nombreNuevo = $p['encomendista_nombre'] ?? 'Sin asignar';
                $nombreOriginal = $p['encomendista_nombre_original'] ?? 'Sin asignar';

                $mensaje = 'Asignado (' . $tipo . ') - Flete: $' .
                    number_format($fleteAsignado, 2);

                if (!empty($p['reasignado']) && $nombreNuevo !== $nombreOriginal) {
                    $mensaje .= ' - Encomendista reasignado: ' .
                        $nombreOriginal . ' → ' . $nombreNuevo;
                } else {
                    $mensaje .= ' - Encomendista: ' . $nombreNuevo;
                }

                addPackLog($p['package_id'], $mensaje);
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
                    'package_id' => $p['package_id'],
                    'valor_paquete' => $valor,
                    'porcentaje' => $porcentaje * 100,
                    'flete_asignado' => round($fleteAsignado, 2),
                    'nuevo_estado' => $tipo,
                    'created_at' => date('Y-m-d H:i:s')
                ];

                $this->packageModel->update($p['package_id'], [
                    'estado1' => 'depositado',
                    'estado2' => $tipo,
                    'encomendista_nombre' => $p['encomendista_id'] ?? null
                ]);

                $nombreNuevo = $p['encomendista_nombre'] ?? 'Sin asignar';
                $nombreOriginal = $p['encomendista_nombre_original'] ?? 'Sin asignar';

                $mensaje = 'Asignado (' . $tipo . ') - Flete: $' .
                    number_format($fleteAsignado, 2);

                if (!empty($p['reasignado']) && $nombreNuevo !== $nombreOriginal) {
                    $mensaje .= ' - Encomendista reasignado: ' .
                        $nombreOriginal . ' → ' . $nombreNuevo;
                } else {
                    $mensaje .= ' - Encomendista: ' . $nombreNuevo;
                }

                addPackLog($p['package_id'], $mensaje);
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
                pdd.id AS detalle_id,
                p.id,
                p.cliente_nombre,
                p.destino,
                p.total_real,

                pdd.valor_paquete,
                pdd.nuevo_estado,
                pdd.porcentaje,
                pdd.flete_asignado,

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
    public function actualizarFlete($id)
    {
        if (!tienePermiso('editar_flete_asignacion')) {
            return $this->response->setJSON(['status' => 'error', 'msg' => 'Sin permiso para editar el flete']);
        }

        $data = $this->request->getJSON(true);
        $modo = $data['modo'] ?? 'total';

        $deposito = $this->depositModel->find($id);
        if (!$deposito) {
            return $this->response->setJSON(['status' => 'error', 'msg' => 'Depósito no encontrado']);
        }

        $this->db->transStart();

        if ($modo === 'individual') {
            $detallesInput = $data['detalles'] ?? [];

            if (empty($detallesInput)) {
                return $this->response->setJSON(['status' => 'error', 'msg' => 'No se enviaron detalles']);
            }

            $nuevoTotal = 0;
            foreach ($detallesInput as $d) {
                $nuevoTotal += floatval($d['flete'] ?? 0);
            }

            $this->depositModel->update($id, ['flete_total' => round($nuevoTotal, 2)]);

            $this->db->table('transactions')
                ->where('origen', 'deposito_paquetes')
                ->where('origen_id', (int) $id)
                ->update(['monto' => round($nuevoTotal, 2)]);

            foreach ($detallesInput as $d) {
                $detalleId   = (int) ($d['id'] ?? 0);
                $fleteAsignado = round(floatval($d['flete'] ?? 0), 2);
                $porcentaje    = $nuevoTotal > 0 ? round(($fleteAsignado / $nuevoTotal) * 100, 2) : 0;

                $this->detailModel->update($detalleId, [
                    'flete_asignado' => $fleteAsignado,
                    'porcentaje'     => $porcentaje,
                ]);

                $det = $this->detailModel->find($detalleId);
                if ($det) {
                    addPackLog($det->package_id, 'Flete editado individualmente: $' . number_format($fleteAsignado, 2) . ' (total asignación: $' . number_format($nuevoTotal, 2) . ')');
                }
            }
        } else {
            // modo === 'total': recalcula proporcionalmente
            $nuevoFlete = floatval($data['flete_total'] ?? 0);

            if ($nuevoFlete <= 0) {
                return $this->response->setJSON(['status' => 'error', 'msg' => 'El monto debe ser mayor a 0']);
            }

            $detalles = $this->detailModel->where('deposit_id', $id)->findAll();
            if (empty($detalles)) {
                return $this->response->setJSON(['status' => 'error', 'msg' => 'No hay paquetes en este depósito']);
            }

            $conValor      = array_filter($detalles, fn($d) => floatval($d->valor_paquete) > 0);
            $sinValor      = array_filter($detalles, fn($d) => floatval($d->valor_paquete) <= 0);
            $todosSinValor = count($conValor) === 0;
            $costoFijo     = 3;

            $this->depositModel->update($id, ['flete_total' => $nuevoFlete]);

            $this->db->table('transactions')
                ->where('origen', 'deposito_paquetes')
                ->where('origen_id', (int) $id)
                ->update(['monto' => $nuevoFlete]);

            if ($todosSinValor) {
                $cantidad      = count($detalles);
                $fleteUnitario = $cantidad > 0 ? $nuevoFlete / $cantidad : 0;

                foreach ($detalles as $d) {
                    $this->detailModel->update($d->id, [
                        'flete_asignado' => round($fleteUnitario, 2),
                        'porcentaje'     => 0,
                    ]);
                    addPackLog($d->package_id, 'Flete recalculado por total: $' . number_format($fleteUnitario, 2) . ' (nuevo total: $' . number_format($nuevoFlete, 2) . ')');
                }
            } else {
                $totalSinValor = count($sinValor) * $costoFijo;
                $fleteRestante = max(0, $nuevoFlete - $totalSinValor);
                $totalValor    = 0;

                foreach ($conValor as $d) {
                    $totalValor += floatval($d->valor_paquete);
                }

                foreach ($detalles as $d) {
                    $valor = floatval($d->valor_paquete);
                    if ($valor <= 0) {
                        $fleteAsignado = $costoFijo;
                        $porcentaje    = 0;
                    } else {
                        $porcentaje    = $totalValor > 0 ? ($valor / $totalValor) : 0;
                        $fleteAsignado = $porcentaje * $fleteRestante;
                    }

                    $this->detailModel->update($d->id, [
                        'flete_asignado' => round($fleteAsignado, 2),
                        'porcentaje'     => round($porcentaje * 100, 2),
                    ]);
                    addPackLog($d->package_id, 'Flete recalculado por total: $' . number_format($fleteAsignado, 2) . ' (nuevo total: $' . number_format($nuevoFlete, 2) . ')');
                }
            }
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return $this->response->setJSON(['status' => 'error', 'msg' => 'Error al actualizar el flete']);
        }

        return $this->response->setJSON(['status' => 'ok', 'msg' => 'Flete actualizado correctamente']);
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
