<?php foreach ($paquetes as $p): ?>

    <div class="col-12 col-md-6 col-lg-4">

        <div class="card shadow-sm h-100 border-0 paquete-card">

            <div class="card-body p-3">

                <!-- HEADER -->
                <div class="d-flex justify-content-between mb-2">
                    <span class="fw-bold text-primary">#<?= $p->id ?></span>

                    <?= statusBadge($p->estado1 ?? '') ?>
                    <?= statusBadge($p->estado2 ?? '') ?>
                </div>

                <!-- CLIENTE -->
                <div class="mb-1">
                    <small class="text-muted">Cliente</small>
                    <div class="fw-bold">
                        <?= esc($p->cliente_nombre) ?>
                    </div>
                </div>

                <!-- DESTINO -->
                <div class="mb-1">
                    <small class="text-muted">Destino</small>
                    <div>
                        <?= esc($p->destino) ?>
                    </div>
                </div>

                <!-- INFO -->
                <div class="d-flex justify-content-between mt-1">

                    <div>
                        <small class="text-muted">Entrega</small><br>
                        <?= date('d/m/Y', strtotime($p->dia_entrega)) ?>
                    </div>

                    <div class="text-end">
                        <small class="text-muted">Total</small><br>
                        <span class="fw-bold text-success">
                            $<?= number_format($p->total, 2) ?>
                        </span>
                    </div>

                </div>

            </div>

            <!-- FOOTER -->
            <div class="card-footer bg-white border-0 d-flex justify-content-end">

                <a href="<?= base_url('packages/' . $p->id) ?>" class="btn btn-sm btn-primary">
                    Ver
                </a>

            </div>

        </div>

    </div>

<?php endforeach; ?>