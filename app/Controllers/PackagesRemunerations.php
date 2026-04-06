<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\PackageModel;
use App\Models\EncomendistasModel;
use App\Models\RemuneracionesModel;
use App\Models\RemuneracionesDetalleModel;
use App\Models\PackageDetailModel;
use App\Models\TransactionModel;

class PackagesRemunerations extends BaseController
{
    public function index()
    {
        return view('packages_remunerations/index');
    }

    public function create()
    {
        $packageModel = new PackageModel();
        $encModel     = new EncomendistasModel();
        $detalleModel = new PackageDetailModel();

        // 🔥 paquetes filtrados
        $paquetes = $packageModel
            ->where('estado1', 'entregado')
            ->where('estado2', 'pendiente_remu')
            ->findAll();

        // 🔥 encomendistas
        $encomendistas = $encModel->findAll();

        // 🔥 mapa id → nombre
        $mapEnc = [];
        foreach ($encomendistas as $e) {
            $mapEnc[$e->id] = $e->encomendista_name;
        }

        // 🔥 agrupar
        $agrupados = [];

        foreach ($paquetes as $p) {

            $detalles = $detalleModel
                ->select('
                paquete_detalle.producto_id,
                paquete_detalle.cantidad,
                paquete_detalle.precio,
                paquete_detalle.subtotal,
                productos.nombre as producto_nombre
            ')
                ->join('productos', 'productos.id = paquete_detalle.producto_id', 'left')
                ->where('paquete_id', $p->id)
                ->findAll();

            // 🔥 limpiar objetos → array plano
            $productos = [];

            foreach ($detalles as $d) {
                $productos[] = [
                    'producto_id' => $d->producto_id,
                    'nombre'      => $d->producto_nombre,
                    'cantidad'    => $d->cantidad,
                    'precio'      => $d->precio,
                    'subtotal'    => $d->subtotal
                ];
            }

            $p->productos = $productos;

            $encId = $p->encomendista_nombre ?? 0;
            $encNombre = $mapEnc[$encId] ?? 'Sin asignar';

            if (!isset($agrupados[$encId])) {
                $agrupados[$encId] = [
                    'encomendista' => $encNombre,
                    'items' => []
                ];
            }

            $agrupados[$encId]['items'][] = $p;
        }

        return view('packages_remunerations/create', [
            'agrupados' => $agrupados
        ]);
    }
    public function store()
    {
        try {

            $request = service('request');
            $data = $request->getJSON(true);

            if (!$data) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'msg' => 'Datos vacíos'
                ]);
            }

            $paquetesIds = $data['paquetes'] ?? [];
            $cuenta      = $data['cuenta'] ?? null;
            $observaciones = $data['observaciones'] ?? null;
            $cuentaTexto = $data['cuenta_texto'] ?? $cuenta;

            if (empty($paquetesIds)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'msg' => 'No hay paquetes seleccionados'
                ]);
            }

            if (!$cuenta) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'msg' => 'Debe seleccionar una cuenta'
                ]);
            }

            $packageModel = new PackageModel();
            $remModel     = new RemuneracionesModel();
            $detModel     = new RemuneracionesDetalleModel();
            $transactionModel = new TransactionModel();

            // 🔥 traer paquetes reales
            $paquetes = $packageModel
                ->select('paquetes.*, encomendistas.encomendista_name as encomendista_nombre')
                ->join('encomendistas', 'encomendistas.id = paquetes.encomendista_nombre', 'left')
                ->whereIn('paquetes.id', $paquetesIds)
                ->where('estado1', 'entregado')
                ->where('estado2', 'pendiente_remu')
                ->findAll();

            if (empty($paquetes)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'msg' => 'No hay paquetes válidos para remunerar'
                ]);
            }

            // 🔥 calcular total
            $total = 0;
            foreach ($paquetes as $p) {
                $total += floatval($p->total);
            }

            $db = \Config\Database::connect();
            $db->transStart();

            // 🧾 1. HEAD
            $remId = $remModel->insert([
                'fecha' => date('Y-m-d H:i:s'),
                'total' => $total,
                'cuenta' => $cuenta,
                'usuario_id' => session()->get('id') ?? null,
                'observaciones' => $observaciones
            ]);

            // TRANSACCIÓN 
            $transactionModel->addEntrada(
                $cuenta,
                $total,
                'remuneracion',
                $remId
            );

            // 🔥 AGRUPAR POR ENCOMENDISTA
            $resumen = [];

            foreach ($paquetes as $p) {

                $enc = $p->encomendista_nombre ?? 'Sin asignar';
                $monto = floatval($p->total);

                if (!isset($resumen[$enc])) {
                    $resumen[$enc] = [
                        'cantidad' => 0,
                        'total' => 0
                    ];
                }

                $resumen[$enc]['cantidad']++;
                $resumen[$enc]['total'] += $monto;
            }

            // 🔥 ARMAR TEXTO DETALLADO
            $detalleLineas = "";

            foreach ($resumen as $enc => $r) {

                $detalleLineas .=
                    $enc . ': ' .
                    $r['cantidad'] . ' paquetes = $' .
                    number_format($r['total'], 2) . "\n";
            }

            // BITÁCORA FINAL
            $detalleBitacora =
                "Remuneración registrada\n" .
                "----------------------------------\n" .
                "ID: #$remId\n\n" .
                $detalleLineas . "\n" .
                "TOTAL: $" . number_format($total, 2) . "\n" .
                "Cuenta: $cuentaTexto\n" .
                (!empty($observaciones)
                    ? "Nota: $observaciones\n"
                    : "");

            registrar_bitacora(
                'Remuneración #' . $remId,
                'Remuneraciones',
                $detalleBitacora,
                session()->get('id')
            );

            // 📦 2. DETALLE + LOG
            foreach ($paquetes as $p) {

                $detModel->insert([
                    'remuneracion_id' => $remId,
                    'paquete_id' => $p->id,
                    'encomendista_id' => $p->encomendista_nombre,
                    'monto' => $p->total
                ]);

                // 🔥 LOG DEL PAQUETE
                $mensaje = '💰 Remunerado - ID #' . $remId .
                    ' - Cuenta: ' . $cuentaTexto .
                    ' - $' . number_format($p->total, 2);

                // opcional: agregar comentario
                if (!empty($observaciones)) {
                    $mensaje .= ' - Nota: ' . $observaciones;
                }

                addPackLog($p->id, $mensaje);
            }

            // 🔄 3. actualizar paquetes
            $packageModel
                ->whereIn('id', array_map(fn($p) => $p->id, $paquetes))
                ->set(['estado1' => 'finalizado'])
                ->set(['estado2' => 'remunerado'])
                ->update();
            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'msg' => 'Error al guardar'
                ]);
            }

            return $this->response->setJSON([
                'status' => 'ok',
                'msg' => 'Remuneración guardada correctamente',
                'remuneracion_id' => $remId
            ]);
        } catch (\Throwable $e) {

            return $this->response->setJSON([
                'status' => 'error',
                'msg' => $e->getMessage()
            ]);
        }
    }
}
