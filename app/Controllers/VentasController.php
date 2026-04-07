<?php

namespace App\Controllers;

use App\Models\VentaModel;
use App\Models\VentaDetalleModel;
use App\Models\PagoModel;
use App\Models\InventarioHistoricoModel;
use App\Models\TransactionModel;
use App\Controllers\BaseController;

class VentasController extends BaseController
{
    public function index()
    {
        $q      = $this->request->getGet('q');
        $desde  = $this->request->getGet('desde');
        $hasta  = $this->request->getGet('hasta');
        $estado = $this->request->getGet('estado');

        $ventaModel = new VentaModel();

        $builder = $ventaModel
            ->select('ventas.*, clientes.nombre as cliente, COUNT(venta_detalle.id) as items')
            ->join('clientes', 'clientes.id = ventas.cliente_id', 'left')
            ->join('venta_detalle', 'venta_detalle.venta_id = ventas.id', 'left')
            ->groupBy('ventas.id')
            ->orderBy('ventas.id', 'DESC');

        // 🔍 BUSCADOR
        if (!empty($q)) {
            $builder->groupStart()
                ->like('clientes.nombre', $q)
                ->orLike('ventas.id', $q)
                ->groupEnd();
        }

        // 📅 FECHAS
        if (!empty($desde)) {
            $builder->where('DATE(ventas.created_at) >=', $desde);
        }

        if (!empty($hasta)) {
            $builder->where('DATE(ventas.created_at) <=', $hasta);
        }

        // 💰 ESTADO
        if (!empty($estado)) {
            $builder->where('ventas.estado', $estado);
        }

        // 🔥 PAGINACIÓN
        $ventas = $builder->paginate(10);
        $pager  = $ventaModel->pager;

        return view('ventas/index', [
            'ventas' => $ventas,
            'pager'  => $pager
        ]);
    }

    public function nueva()
    {
        return view('ventas/nueva');
    }

    public function store()
    {
        $data = $this->request->getJSON(true);

        $venta   = $data['venta'] ?? [];
        $detalle = $data['detalle'] ?? [];
        $pagos   = $data['pagos'] ?? [];

        try {

            $db = \Config\Database::connect();
            $db->transStart();

            $ventaModel   = new VentaModel();
            $detalleModel = new VentaDetalleModel();
            $pagoModel    = new PagoModel();
            $transactionModel = new TransactionModel();
            $invModel     = new InventarioHistoricoModel();

            // =========================
            // 🔒 VALIDACIONES
            // =========================

            if (empty($detalle)) {
                throw new \Exception('La venta no tiene productos');
            }

            $total  = $venta['total'] ?? 0;
            $pagado = $venta['total_pagado'] ?? 0;

            if (($venta['tipo_venta'] ?? 'contado') === 'contado' && $pagado < $total) {
                throw new \Exception('Venta de contado incompleta');
            }

            // =========================
            // 🧾 INSERT VENTA
            // =========================

            $saldo = $total - $pagado;

            if ($saldo <= 0) {
                $estado = 'pagado';
            } elseif ($pagado > 0) {
                $estado = 'parcial';
            } else {
                $estado = 'pendiente';
            }

            $ventaId = $ventaModel->insert([
                'fecha'         => $venta['fecha'],
                'cliente_id'    => $venta['cliente_id'],
                'branch_id'     => session('branch_id'),
                'tipo_venta'    => $venta['tipo_venta'],
                'total'         => $total,
                'total_pagado'  => $pagado,
                'saldo'         => $saldo,
                'estado'        => $estado,
            ]);

            // =========================
            // 📦 DETALLE + INVENTARIO
            // =========================

            foreach ($detalle as $d) {

                if (empty($d['branch_id'])) {
                    throw new \Exception('Sucursal no definida en producto ID ' . $d['producto_id']);
                }

                // detalle venta
                $detalleModel->insert([
                    'venta_id'        => $ventaId,
                    'producto_id'     => $d['producto_id'],
                    'cantidad'        => $d['cantidad'],
                    'precio_unitario' => $d['precio_unitario'],
                    'total'           => $d['total'],
                ]);

                // inventario histórico (kardex)
                $invModel->insert([
                    'producto_id' => $d['producto_id'],
                    'branch_id'   => $d['branch_id'],
                    'tipo'        => 'salida',
                    'cantidad'    => $d['cantidad'],
                    'origen'      => 'venta',
                    'origen_id'   => $ventaId,
                    'usuario_id'  => session('id'),
                    'created_at'  => date('Y-m-d H:i:s'),
                ]);
            }

            // =========================
            // 💰 PAGOS + TRANSACTIONS
            // =========================

            foreach ($pagos as $p) {

                if (empty($p['account_id']) || $p['monto'] <= 0) {
                    continue;
                }

                // guardar pago
                $pagoModel->insert([
                    'venta_id'   => $ventaId,
                    'account_id' => $p['account_id'],
                    'monto'      => $p['monto'],
                    'created_at' => date('Y-m-d H:i:s'),
                ]);

                // registrar entrada de dinero
                $transactionModel->addEntrada(
                    $p['account_id'],
                    $p['monto'],
                    'venta',
                    $ventaId
                );
            }

            // =========================
            // 📝 BITÁCORA
            // =========================

            registrar_bitacora(
                'Registro de venta',
                'Ventas',
                'Venta #' . $ventaId . ' por $' . number_format($total, 2),
                $ventaId
            );

            $db->transComplete();

            return $this->response->setJSON([
                'success'  => true,
                'venta_id' => $ventaId
            ]);
        } catch (\Exception $e) {

            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function show($id)
    {
        $ventaModel   = new VentaModel();
        $detalleModel = new VentaDetalleModel();
        $pagoModel    = new PagoModel();

        // 🧾 VENTA
        $venta = $ventaModel
            ->select('ventas.*, clientes.nombre as cliente')
            ->join('clientes', 'clientes.id = ventas.cliente_id', 'left')
            ->where('ventas.id', $id)
            ->first();

        if (!$venta) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Venta no encontrada");
        }

        // 📦 DETALLE
        $detalle = $detalleModel
            ->select('venta_detalle.*, productos.nombre as producto')
            ->join('productos', 'productos.id = venta_detalle.producto_id', 'left')
            ->where('venta_id', $id)
            ->findAll();

        // 💰 PAGOS
        $pagos = $pagoModel
            ->select('pagos.*, accounts.name as cuenta')
            ->join('accounts', 'accounts.id = pagos.account_id', 'left')
            ->where('venta_id', $id)
            ->findAll();

        return view('ventas/show', [
            'venta'   => $venta,
            'detalle' => $detalle,
            'pagos'   => $pagos
        ]);
    }
}
