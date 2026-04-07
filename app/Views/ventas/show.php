<?= $this->extend('Layouts/mainbody') ?>
<?= $this->section('content') ?>

<style>
    .info-label {
        font-size: 12px;
        color: #6c757d;
        text-transform: uppercase;
        font-weight: 600;
    }

    .info-value {
        font-size: 18px;
        font-weight: 700;
    }

    .info-sub {
        font-size: 14px;
        color: #495057;
    }

    .card-section {
        padding: 16px;
        border-radius: 12px;
        border: 1px solid #e9ecef;
        background: #fff;
    }

    .total-value {
        font-size: 26px;
        font-weight: 800;
        color: #198754;
    }
</style>

<div class="row">
    <div class="col-md-12">

        <div class="card shadow-sm">

            <!-- HEADER -->
            <div class="card-header d-flex justify-content-between">
                <h4 class="mb-0">
                    Venta
                    <span class="badge bg-primary text-white">#<?= $venta->id ?></span>
                </h4>

                <a href="<?= base_url('ventas') ?>" class="btn btn-secondary btn-sm">
                    Volver
                </a>
            </div>

            <div class="card-body">

                <div class="row">

                    <!-- 🔵 IZQUIERDA -->
                    <div class="col-lg-9">

                        <!-- 🧾 INFO GENERAL -->
                        <div class="card-section mb-3">
                            <div class="row">

                                <div class="col-md-6">
                                    <div class="info-label">Cliente</div>
                                    <div class="info-value">
                                        <?= esc($venta->cliente ?? 'Clientes varios') ?>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="info-label">Fecha</div>
                                    <div class="info-sub">
                                        <?= date('d/m/Y H:i', strtotime($venta->created_at)) ?>
                                    </div>
                                </div>

                                <div class="col-md-6 mt-2">
                                    <div class="info-label">Tipo</div>
                                    <div class="info-sub">
                                        <?= ucfirst($venta->tipo_venta) ?>
                                    </div>
                                </div>

                                <div class="col-md-6 mt-2">
                                    <div class="info-label">Estado</div>

                                    <?php
                                    $color = match($venta->estado) {
                                        'pagado' => 'success',
                                        'parcial' => 'warning',
                                        default => 'danger'
                                    };
                                    ?>

                                    <span class="badge text-white bg-<?= $color ?>">
                                        <?= ucfirst($venta->estado) ?>
                                    </span>
                                </div>

                            </div>
                        </div>

                        <!-- 📦 DETALLE -->
                        <div class="card-section mb-3">

                            <div class="info-label mb-2">Detalle de venta</div>

                            <div class="table-responsive">
                                <table class="table table-sm table-borderless">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Producto</th>
                                            <th class="text-center">Cant</th>
                                            <th class="text-end">Precio</th>
                                            <th class="text-end">Total</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php foreach ($detalle as $i => $d): ?>
                                            <tr>
                                                <td><?= $i + 1 ?></td>
                                                <td><?= esc($d->producto) ?></td>
                                                <td class="text-center"><?= $d->cantidad ?></td>
                                                <td class="text-end">$<?= number_format($d->precio_unitario, 2) ?></td>
                                                <td class="text-end fw-bold">$<?= number_format($d->total, 2) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                        </div>

                        <!-- 💰 PAGOS -->
                        <div class="card-section mb-3">

                            <div class="info-label mb-2">Pagos realizados</div>

                            <?php if (!empty($pagos)): ?>

                                <div class="table-responsive">
                                    <table class="table table-sm table-borderless">
                                        <thead>
                                            <tr>
                                                <th>Cuenta</th>
                                                <th class="text-end">Monto</th>
                                                <th class="text-end">Fecha</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php foreach ($pagos as $p): ?>
                                                <tr>
                                                    <td><?= esc($p->cuenta) ?></td>
                                                    <td class="text-end text-success fw-bold">
                                                        $<?= number_format($p->monto, 2) ?>
                                                    </td>
                                                    <td class="text-end">
                                                        <?= date('d/m/Y H:i', strtotime($p->created_at)) ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>

                            <?php else: ?>
                                <div class="text-muted">No hay pagos registrados</div>
                            <?php endif; ?>

                        </div>

                    </div>

                    <!-- 🟢 DERECHA (RESUMEN) -->
                    <div class="col-lg-3">

                        <div class="card-section text-center" style="position: sticky; top:20px;">

                            <div class="info-label">Total</div>
                            <div class="total-value mb-2">
                                $<?= number_format($venta->total, 2) ?>
                            </div>

                            <div class="info-label">Pagado</div>
                            <div class="info-sub mb-2">
                                $<?= number_format($venta->total_pagado, 2) ?>
                            </div>

                            <div class="info-label">Saldo</div>
                            <div class="<?= $venta->saldo > 0 ? 'text-danger' : 'text-success' ?> fw-bold">
                                $<?= number_format($venta->saldo, 2) ?>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>
</div>

<?= $this->endSection() ?>