<?= $this->extend('Layouts/mainbody') ?>
<?= $this->section('content') ?>
<?php
$tiposServicio = [
    1 => 'Punto fijo',
    2 => 'Personalizado',
    3 => 'Recolecta de paquete',
    4 => 'Casillero'
];
?>
<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm mb-4">
            <div class="card-header d-flex align-items-center bg-primary text-white">
                <h5 class="mb-0 d-flex align-items-center">

                    Detalle de Tracking #<?= $tracking->id ?>

                    <span class="ml-2">
                        <?= trackingHeaderBadge($tracking->status ?? 'pendiente') ?>
                    </span>

                </h5>
                <a href="<?= base_url('tracking-pdf/' . $tracking->id) ?>" target="_blank"
                    class="btn btn-light btn-sm ml-auto">
                    <i class="fa-solid fa-file-pdf"></i> Exportar PDF
                </a>
            </div>
            <div class="card-body">
                <?php
                $totalEfectivo = $tracking->efectivo ?? null;
                $totalOtras    = $tracking->otras_cuentas ?? null;

                function formatearMonto($valor)
                {
                    if ($valor === null) {
                        return '<span class="text-muted">No registrado</span>';
                    }
                    return '$' . number_format($valor, 2);
                }
                ?>
                <div class="d-flex justify-content-between align-items-start flex-wrap mb-3">

                    <div>
                        <p class="mb-1"><strong>Motorista:</strong> <?= $tracking->motorista_name ?? 'N/A' ?></p>
                        <p class="mb-1"><strong>Creación:</strong> <?= date('d/m/Y H:i', strtotime($tracking->created_at)) ?></p>
                        <p class="mb-0"><strong>Fecha de entrega:</strong> <?= date('d/m/Y', strtotime($tracking->date)) ?></p>
                    </div>

                    <div class="px-3 py-2 bg-light border rounded small text-right">
                        <div>
                            <span class="text-muted">Efectivo:</span>
                            <strong><?= formatearMonto($totalEfectivo) ?></strong>
                        </div>

                        <div>
                            <span class="text-muted">Otras cuentas:</span>
                            <strong><?= formatearMonto($totalOtras) ?></strong>
                        </div>
                    </div>

                </div>

                <hr>
                <?php
                $totalPaquetes = count($detalles);

                $noRetirados = 0;
                $casilleros = 0;
                $pagadoVendedor = 0;
                $exitosos = 0;

                foreach ($detalles as $d) {

                    if ($d->status == 'no_retirado' || $d->status == 'recolecta_fallida') {
                        $noRetirados++;
                    }

                    if ($d->status == 'en_casillero_externo') {
                        $casilleros++;
                    }

                    if ($d->cliente_pago_directo ?? false) {
                        $pagadoVendedor++;
                    }

                    if ($d->status == 'entregado') {
                        $exitosos++;
                    }
                }
                ?>
                <div class="card bg-light p-3 mb-3 shadow-sm">
                    <h6 class="mb-3">Estadísticas del Tracking</h6>

                    <div class="row text-center">

                        <div class="col-md-2">
                            <div class="stat-box">
                                <div class="stat-number stat-primary"><?= $totalPaquetes ?></div>
                                <div class="stat-label">Total paquetes</div>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="stat-box">
                                <div class="stat-number stat-exito"><?= $exitosos ?></div>
                                <div class="stat-label">Exitosos</div>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="stat-box">
                                <div class="stat-number stat-warning"><?= $noRetirados ?></div>
                                <div class="stat-label">No retirados</div>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="stat-box">
                                <div class="stat-number stat-info"><?= $casilleros ?></div>
                                <div class="stat-label">Casilleros externos</div>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="stat-box">
                                <div class="stat-number stat-danger"><?= $pagadoVendedor ?></div>
                                <div class="stat-label">Pagado vendedor</div>
                            </div>
                        </div>

                    </div>
                </div>

                <?php
                $porcentaje = $totalPaquetes > 0
                    ? round(($exitosos / $totalPaquetes) * 100)
                    : 0;
                ?>

                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">

                        <div class="d-flex justify-content-between mb-2">
                            <strong>Progreso de la ruta</strong>
                            <span class="text-muted">
                                <?= $exitosos ?> / <?= $totalPaquetes ?> Exitosos
                            </span>
                        </div>

                        <div class="progress" style="height:18px;">
                            <div class="progress-bar bg-success"
                                role="progressbar"
                                style="width: <?= $porcentaje ?>%">
                                <?= $porcentaje ?>%
                            </div>
                        </div>

                    </div>
                </div>
                <h6>Paquetes</h6>

                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>PAQ ID</th>
                            <th class="col-md-1">Tipo</th>
                            <th class="col-md-3">Vendedor</th>
                            <th class="col-md-3">Cliente</th>
                            <th class="col-md-3">Destino / Recolección</th>
                            <th class="col-md-1">Estado</th>
                            <th class="col-md-1">Monto</th>
                            <th class="col-md-1">Cuenta</th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($detalles)): ?>
                            <?php foreach ($detalles as $d): ?>

                                <?php
                                $tipo = $tiposServicio[$d->tipo_servicio] ?? 'Desconocido';

                                // Servicio 1 — Punto fijo
                                if ($d->tipo_servicio == 1) {
                                    $destino = $d->puntofijo_nombre ?? 'Punto no encontrado';
                                }

                                // Servicio 2 — Personalizado
                                elseif ($d->tipo_servicio == 2) {
                                    $destino = $d->destino_personalizado ?: 'N/A';
                                }

                                // Servicio 4 — Casillero (temporal similar a punto fijo)
                                elseif ($d->tipo_servicio == 4) {
                                    $destino = $d->puntofijo_nombre ?: 'Casillero';
                                }

                                // Servicio 3 — Recolecta
                                elseif ($d->tipo_servicio == 3) {
                                    $recolecta = $d->lugar_recolecta_paquete ?: 'Pendiente';
                                    $entrega = $d->destino_personalizado ?: 'Pendiente';
                                }
                                ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">

                                            <span class="font-weight-bold mr-2">
                                                #<?= esc($d->package_id) ?>
                                            </span>

                                            <a href="<?= base_url('packages/show/' . $d->package_id) ?>"
                                                class="btn btn-sm btn-outline-primary"
                                                title="Ver paquete"
                                                target="_blank">

                                                <i class="fa-solid fa-box"></i>
                                            </a>

                                        </div>
                                    </td>
                                    <td><?= esc($tipo) ?></td>
                                    <td><?= esc($d->vendedor) ?></td>
                                    <td><?= esc($d->cliente) ?></td>
                                    <td>
                                        <?php if ($d->tipo_servicio == 3): ?>
                                            <strong>Recolecta:</strong> <?= esc($recolecta) ?><br>
                                            <strong>Entrega:</strong> <?= esc($entrega) ?>
                                        <?php else: ?>
                                            <?= esc($destino) ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?= trackingBadge($d->status) ?>
                                    </td>
                                    <td>
                                        <?php if (empty($d->monto) || $d->monto == 0): ?>
                                            <span class="text-danger fw-bold">Cancelado</span>
                                        <?php else: ?>
                                            $ <?= number_format($d->monto, 2) ?>
                                        <?php endif; ?>
                                    </td>

                                    <td class="text-muted small">
                                        <?= $d->cuenta_nombre ?? '-' ?>
                                    </td>
                                </tr>

                                <?php if (!empty($d->note)): ?>
                                    <tr>
                                        <td colspan="8" class="bg-light text-muted small">
                                            <i class="fa-solid fa-comment-dots"></i>
                                            <?= esc($d->note) ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>

                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center">No hay paquetes asignados</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <a href="<?= base_url('tracking') ?>" class="btn btn-secondary">Volver</a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>