<div class="row">

    <?php foreach ($remuneraciones as $r): ?>
        <div class="col-12 mb-3">

            <div class="card shadow-sm remu-card">
                <div class="card-body position-relative">

                    <!-- 📱 MOBILE -->
                    <div class="d-block d-md-none">

                        <div class="mb-1">
                            <small class="text-muted">Remuneración:</small>
                            <strong>#<?= $r->id ?></strong>
                        </div>

                        <div class="mb-1">
                            <small class="text-muted">Fecha:</small>
                            <?= date('d/m/Y', strtotime($r->fecha)) ?>
                        </div>

                        <div class="mb-1">
                            <small class="text-muted">Cuenta:</small>
                            <?= esc($r->cuenta_nombre ?? '—') ?>
                        </div>

                        <div>
                            <small class="text-muted">Usuario:</small>
                            <?= esc($r->usuario_nombre ?? '—') ?>
                        </div>

                        <?php if (!empty($r->encomendistas)): ?>
                            <div class="mt-1">
                                <small class="text-muted">Encomendistas:</small><br>

                                <?php foreach ($r->encomendistas as $nombre): ?>
                                    <span class="badge badge-primary mr-1">
                                        <?= esc($nombre) ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <div>
                            <small class="text-muted">Obs:</small>
                            <?= esc($r->observaciones ?: '—') ?>
                        </div>

                        <div class="d-flex justify-content-between mb-2">

                            <div>
                                <small class="text-muted">Total:</small>
                                <strong>$<?= number_format($r->total, 2) ?></strong>
                            </div>

                            <div>
                                <a href="<?= base_url('packages-remunerations/' . $r->id) ?>"
                                    class="btn btn-sm btn-outline-secondary">
                                    Ver <i class="fa fa-eye"></i>
                                </a>
                            </div>

                        </div>

                    </div>

                    <!-- 💻 DESKTOP -->
                    <div class="d-none d-md-flex align-items-center justify-content-between">

                        <div style="width:10%;">
                            <small class="text-muted">ID:</small>
                            <strong>#<?= $r->id ?></strong>
                        </div>

                        <div style="width:20%;" class="text-center">
                            <small class="text-muted">Encomendistas:</small><br>

                            <?php if (!empty($r->encomendistas)): ?>
                                <?php foreach ($r->encomendistas as $nombre): ?>
                                    <span class="badge badge-primary mr-1">
                                        <?= esc($nombre) ?>
                                    </span>
                                <?php endforeach; ?>
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </div>

                        <div style="width:15%;">
                            <small class="text-muted">Fecha:</small>
                            <?= date('d/m/Y', strtotime($r->fecha)) ?>
                        </div>

                        <div style="width:20%;">
                            <small class="text-muted">Cuenta:</small>
                            <?= esc($r->cuenta_nombre ?? '—') ?>
                        </div>

                        <div style="width:20%;">
                            <small class="text-muted">Total:</small>
                            $<?= number_format($r->total, 2) ?>
                        </div>

                        <div style="width:20%;">
                            <small class="text-muted">Usuario:</small>
                            <?= esc($r->cuenta_nombre ?? '—') ?>
                        </div>

                        <div style="width:10%;" class="text-right">

                            <a href="<?= base_url('packages-remunerations/' . $r->id) ?>"
                                class="btn btn-sm btn-outline-secondary">
                                <i class="fa fa-eye"></i>
                            </a>

                        </div>

                    </div>

                </div>
            </div>

        </div>
    <?php endforeach; ?>

</div>