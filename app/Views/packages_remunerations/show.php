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
        font-size: 20px;
        font-weight: 700;
    }

    .card-section {
        padding: 16px;
        border-radius: 12px;
        border: 1px solid #e9ecef;
        background: #fff;
        margin-bottom: 12px;
    }

    .badge-enc {
        margin-right: 5px;
        margin-bottom: 5px;
    }

    .total-box {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 15px;
    }

    .total-value {
        font-size: 26px;
        font-weight: 800;
        color: #198754;
    }

    .card-section .border:hover {
        transform: scale(1.01);
        transition: 0.2s;
    }
</style>

<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm">

            <!-- HEADER -->
            <div class="card-header d-flex justify-content-between">
                <div>
                    <h4 class="mb-0">
                        Remuneración
                        <span class="badge bg-primary text-white">#<?= $remuneracion->id ?></span>
                    </h4>
                    <small class="text-muted">Detalle de remuneración</small>
                </div>
            </div>

            <div class="card-body">

                <div class="row">

                    <!-- IZQUIERDA -->
                    <div class="col-lg-8">

                        <!-- INFO GENERAL -->
                        <div class="card-section">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-label">Fecha</div>
                                    <div class="info-value">
                                        <?= date('d/m/Y H:i', strtotime($remuneracion->fecha)) ?>
                                    </div>

                                    <div class="info-label mt-2">Cuenta</div>
                                    <div><?= esc($remuneracion->cuenta_nombre) ?></div>
                                </div>

                                <div class="col-md-6">
                                    <div class="info-label">Usuario</div>
                                    <div><?= esc($remuneracion->usuario_nombre) ?></div>

                                    <div class="info-label mt-2">Observaciones</div>
                                    <div><?= esc($remuneracion->observaciones ?: '—') ?></div>
                                </div>
                            </div>
                        </div>

                        <!-- DETALLE -->
                        <div class="card-section">

                            <div class="info-label mb-2">Detalle</div>

                            <div class="row">
                                <?php foreach ($detalles as $d): ?>
                                    <div class="col-12 mb-2">

                                        <div class="border rounded p-3 shadow-sm d-flex justify-content-between flex-wrap">

                                            <!-- IZQUIERDA -->
                                            <div>
                                                <div>
                                                    <span class="info-label">Paquete</span><br>

                                                    <a href="<?= base_url('packages/' . $d->paquete_id) ?>"
                                                       class="badge bg-dark text-white"
                                                       style="text-decoration:none;">
                                                        Ver paquete #<?= $d->paquete_id ?>
                                                    </a>
                                                </div>

                                                <div class="mt-2">
                                                    <span class="info-label">Encomendista</span><br>

                                                    <span class="badge bg-primary text-white badge-enc">
                                                        <?= esc($d->encomendista_name ?? '—') ?>
                                                    </span>
                                                </div>
                                            </div>

                                            <!-- DERECHA -->
                                            <div class="text-end mt-2 mt-md-0">
                                                <div class="info-label">Monto</div>
                                                <div class="info-value text-success">
                                                    $<?= number_format($d->monto, 2) ?>
                                                </div>
                                            </div>

                                        </div>

                                    </div>
                                <?php endforeach; ?>
                            </div>

                        </div>

                    </div> <!-- ✅ CIERRE CORRECTO -->

                    <!-- DERECHA -->
                    <div class="col-lg-4">

                        <div class="total-box text-center">
                            <div class="info-label">Total remunerado</div>
                            <div class="total-value">
                                $<?= number_format($remuneracion->total, 2) ?>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>