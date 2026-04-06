<?= $this->extend('Layouts/mainbody') ?>
<?= $this->section('content') ?>

<style>
    .info-label {
        font-size: 12px;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
    }

    .info-value {
        font-size: 18px;
        font-weight: 700;
        color: #212529;
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
        transition: 0.2s;
    }

    .card-section:hover {
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    }

    .estado-badge {
        font-size: 11px;
        padding: 4px 10px;
        border-radius: 10px;
        font-weight: 600;
    }

    .estado-en_ruta {
        background: #dcfce7;
        color: #166534;
    }

    .estado-pendiente {
        background: #fef9c3;
        color: #854d0e;
    }

    .estado-cancelado {
        background: #fee2e2;
        color: #991b1b;
    }

    .estado-default {
        background: #e5e7eb;
        color: #374151;
    }

    .estado-en_casillero {
        background: #e0f2fe;
        color: #0369a1;
    }
</style>

<?php
$totalReal = 0;
$totalDepositado = 0;

foreach ($detalles as $p) {
    $totalReal += (float) $p->total_real;
    $totalDepositado += (float) $p->valor_paquete;
}

$total = $totalDepositado;
$totalCobrar = $totalDepositado;
?>

<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm">

            <!-- HEADER -->
            <div class="card-header d-flex justify-content-between">

                <div>
                    <h4 class="mb-0">
                        Asignación
                        <span class="badge bg-primary text-white">
                            #<?= $deposito->id ?>
                        </span>
                    </h4>
                    <small class="text-muted">Detalle de depósito</small>
                </div>

                <a href="<?= base_url('packages-assignation') ?>" class="btn btn-secondary btn-sm mb-4">
                    ← Volver
                </a>

            </div>

            <div class="card-body">

                <div class="row">

                    <!-- IZQUIERDA -->
                    <div class="col-lg-9">

                        <!-- INFO GENERAL -->
                        <div class="card-section mb-3">
                            <div class="row">

                                <div class="col-md-4">
                                    <div class="info-label">Fecha</div>
                                    <div class="info-value">
                                        <?= date('d/m/Y', strtotime($deposito->fecha)) ?>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="info-label">Encomendistas</div>
                                    <div class="info-sub">
                                        <?= esc($deposito->encomendistas ?? '—') ?>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="info-label">Flete total pagado</div>
                                    <div class="info-value text-success">
                                        $<?= number_format($deposito->flete_total, 2) ?>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- DETALLE -->
                        <div class="card-section mb-3">

                            <div class="info-label mb-2">Paquetes asignados</div>

                            <!-- DESKTOP -->
                            <div class="d-none d-md-block">
                                <table class="table table-sm table-borderless">
                                    <thead style="font-size:12px;">
                                        <tr>
                                            <th>#</th>
                                            <th>ID</th>
                                            <th>Cliente</th>
                                            <th>Destino</th>
                                            <th>Encomendista</th>
                                            <th>Estado</th>
                                            <th class="text-end">Valor real</th>
                                            <th class="text-end">Valor Remunerar</th>
                                            <th class="text-center">%</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($detalles as $i => $p): ?>
                                            <tr>
                                                <td><?= $i + 1 ?></td>

                                                <td>
                                                    <a href="<?= base_url('packages/show/' . $p->id) ?>"
                                                        class="badge badge-lg bg-dark text-white text-decoration-none d-inline-flex align-items-center gap-1"
                                                        title="Ver paquete">

                                                        Ver: #<?= $p->id ?>

                                                    </a>
                                                </td>

                                                <td><?= esc($p->cliente_nombre) ?></td>

                                                <td><?= esc($p->destino) ?></td>

                                                <td><?= esc($p->encomendista_name ?? '—') ?></td>

                                                <td>
                                                    <?php
                                                    function estadoBonito($estado)
                                                    {
                                                        return ucfirst(str_replace('_', ' ', $estado));
                                                    }
                                                    ?>

                                                    <span class="estado-badge estado-<?= $p->nuevo_estado ?>">
                                                        <?= esc(estadoBonito($p->nuevo_estado)) ?>
                                                    </span>
                                                </td>

                                                <td class="text-end">
                                                    $<?= number_format($p->total_real, 2) ?>
                                                </td>

                                                <td class="text-end">
                                                    <span class="fw-bold text-success">
                                                        $<?= number_format($p->valor_paquete, 2) ?>
                                                    </span>
                                                </td>

                                                <td class="text-center">
                                                    <?= number_format($p->porcentaje, 1) ?>%
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- MOBILE -->
                            <div class="d-block d-md-none">
                                <?php foreach ($detalles as $i => $p): ?>
                                    <div class="border rounded p-2 mb-2 shadow-sm">

                                        <div class="fw-bold">
                                            #<?= $p->id ?> - <?= esc($p->cliente_nombre) ?>
                                        </div>

                                        <div class="text-muted small">
                                            <?= esc($p->destino) ?>
                                        </div>

                                        <div class="small mt-1">
                                            🚚 <?= esc($p->encomendista_name ?? '—') ?>
                                        </div>

                                        <div class="mt-1">
                                            <span class="estado-badge estado-<?= $p->nuevo_estado ?>">
                                                <?= esc($p->nuevo_estado) ?>
                                            </span>
                                        </div>

                                        <div class="d-flex justify-content-between mt-2">
                                            <small>Real: $<?= number_format($p->total_real, 2) ?></small>
                                            <small class="text-success fw-bold">
                                                Dep: $<?= number_format($p->valor_paquete, 2) ?>
                                            </small>
                                        </div>

                                        <div class="text-end small">
                                            <?= number_format($p->porcentaje, 1) ?>%
                                        </div>

                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- RESUMEN -->
                            <div class="border-top pt-2 mt-2">

                                <div class="d-flex justify-content-between">
                                    <span>Paquetes</span>
                                    <span><?= count($detalles) ?></span>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <span>Total real</span>
                                    <span>$<?= number_format($totalReal, 2) ?></span>
                                </div>

                                <div class="d-flex justify-content-between fw-bold">
                                    <span class="text-success">Total depositado</span>
                                    <span class="text-success">
                                        $<?= number_format($totalDepositado, 2) ?>
                                    </span>
                                </div>

                            </div>

                        </div>

                    </div>

                    <!-- DERECHA -->
                    <div class="col-lg-3">
                        <div class="card-section text-center">

                            <div class="info-label mb-2">Resumen</div>

                            <div class="mb-2">
                                <div class="info-sub">Paquetes</div>
                                <div class="info-value"><?= count($detalles) ?></div>
                            </div>

                            <div class="info-sub">Total depositado</div>
                            <div class="info-value text-success">
                                $<?= number_format($totalDepositado, 2) ?>
                            </div>

                            <div>
                                <div class="info-sub">Total real</div>
                                <div class="info-value">
                                    $<?= number_format($totalReal, 2) ?>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>
</div>

<?= $this->endSection() ?>