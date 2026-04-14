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
        font-size: 16px;
        font-weight: 700;
        color: #212529;
    }
    .card-section {
        padding: 16px;
        border-radius: 12px;
        border: 1px solid #e9ecef;
        background: #fff;
    }
    .card-section:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .flow-arrow {
        font-size: 22px;
        color: #adb5bd;
        align-self: center;
    }
</style>

<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm">

            <div class="card-header d-flex align-items-center">
                <div>
                    <h4 class="mb-0">
                        Traslado
                        <span class="badge bg-primary text-white">#<?= $traslado->id ?></span>
                    </h4>
                    <small class="text-muted">
                        <?= date('d/m/Y H:i', strtotime($traslado->created_at)) ?>
                        · <?= esc($traslado->usuario_nombre) ?>
                    </small>
                </div>

                <?php
                $badgeColor = match($traslado->estado) {
                    'completado' => 'success',
                    'pendiente'  => 'warning',
                    'cancelado'  => 'danger',
                    default      => 'secondary'
                };
                ?>
                <span class="badge badge-<?= $badgeColor ?> ml-auto" style="font-size:14px;">
                    <?= ucfirst($traslado->estado) ?>
                </span>

                <a href="<?= base_url('traslados') ?>"
                    class="btn btn-secondary btn-sm ml-2">
                    <i class="fa fa-arrow-left"></i> Volver
                </a>
            </div>

            <div class="card-body">
                <div class="row">

                    <!-- IZQUIERDA -->
                    <div class="col-12 col-lg-8">

                        <!-- ORIGEN → DESTINO -->
                        <div class="card-section mb-3">
                            <div class="d-flex justify-content-around align-items-center flex-wrap" style="gap:12px;">

                                <div class="text-center">
                                    <div class="info-label">Origen</div>
                                    <div class="info-value"><?= esc($traslado->origen_nombre) ?></div>
                                </div>

                                <div class="flow-arrow">→</div>

                                <div class="text-center">
                                    <div class="info-label">Destino</div>
                                    <div class="info-value"><?= esc($traslado->destino_nombre) ?></div>
                                </div>

                            </div>
                        </div>

                        <!-- PRODUCTOS DESKTOP -->
                        <div class="card-section mb-3">
                            <div class="info-label mb-2">Productos trasladados</div>

                            <div class="d-none d-md-block">
                                <table class="table table-sm table-borderless">
                                    <thead style="font-size:12px;">
                                        <tr>
                                            <th>#</th>
                                            <th>Producto</th>
                                            <th>Código</th>
                                            <th class="text-center">Cantidad</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($detalles as $i => $d): ?>
                                            <tr>
                                                <td><?= $i + 1 ?></td>
                                                <td><?= esc($d->producto_nombre) ?></td>
                                                <td>
                                                    <small class="text-muted">
                                                        <?= esc($d->codigo_barras ?: '—') ?>
                                                    </small>
                                                </td>
                                                <td class="text-center font-weight-bold">
                                                    <?= $d->cantidad ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- PRODUCTOS MOBILE -->
                            <div class="d-block d-md-none">
                                <?php foreach ($detalles as $i => $d): ?>
                                    <div class="border rounded p-2 mb-2">
                                        <div class="d-flex justify-content-between">
                                            <span class="font-weight-bold"><?= esc($d->producto_nombre) ?></span>
                                            <span class="badge badge-primary"><?= $d->cantidad ?> u.</span>
                                        </div>
                                        <?php if ($d->codigo_barras): ?>
                                            <small class="text-muted"><?= esc($d->codigo_barras) ?></small>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                        </div>

                        <!-- NOTAS -->
                        <?php if (!empty($traslado->notas)): ?>
                            <div class="card-section mb-3">
                                <div class="info-label mb-1">Notas</div>
                                <div style="font-size:14px;"><?= nl2br(esc($traslado->notas)) ?></div>
                            </div>
                        <?php endif; ?>

                    </div>

                    <!-- DERECHA: RESUMEN -->
                    <div class="col-12 col-lg-4">
                        <div class="card-section" style="position:sticky; top:20px;">

                            <div class="info-label mb-3">Resumen</div>

                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Total productos</span>
                                <span class="font-weight-bold"><?= count($detalles) ?></span>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Total unidades</span>
                                <span class="font-weight-bold">
                                    <?= array_sum(array_column((array)$detalles, 'cantidad')) ?>
                                </span>
                            </div>

                            <hr>

                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Costo traslado</span>
                                <?php if ($traslado->costo_traslado > 0): ?>
                                    <span class="font-weight-bold text-danger">
                                        $<?= number_format($traslado->costo_traslado, 2) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted">Sin costo</span>
                                <?php endif; ?>
                            </div>

                            <?php if ($traslado->cuenta_id): ?>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Cuenta</span>
                                    <span class="font-weight-bold">
                                        <?= esc($traslado->cuenta_nombre ?? '—') ?>
                                    </span>
                                </div>
                            <?php endif; ?>

                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<?= $this->endSection() ?>