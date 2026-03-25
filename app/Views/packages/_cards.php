<?php foreach ($paquetes as $p): ?>

    <div class="col-12 col-md-6 col-lg-4">

        <div class="card shadow-sm h-100 border-0 paquete-card">

            <div class="card-body p-2"> <!-- 🔥 menos padding -->

                <!-- HEADER -->
                <div class="d-flex justify-content-between mb-1">

                    <span class="fw-bold text-primary">#<?= $p->id ?></span>

                    <div class="d-flex flex-column align-items-end">
                        <div class="mb-1">
                            <?= statusBadge($p->estado1 ?? '') ?>
                        </div>
                        <?= statusBadge($p->estado2 ?? '') ?>
                    </div>
                </div>

                <!-- CLIENTE -->
                <div class="mb-1">
                    <small class="text-muted">Cliente:</small>
                    <span class="fw-bold">
                        <?= esc($p->cliente_nombre) ?>
                    </span>
                </div>

                <!-- DESTINO -->
                <div class="mb-1">
                    <small class="text-muted">Destino:</small>
                    <span>
                        <?= esc($p->destino) ?>
                    </span>
                </div>

                <!-- INFO -->
                <div class="d-flex justify-content-between mt-1" style="font-size:13px;">

                    <div class="text-end">
                        <small class="text-muted">Entrega:</small>
                        <?= date('d/m/Y', strtotime($p->dia_entrega)) ?>
                    </div>

                    <div class="text-end">
                        <small class="text-muted">Total:</small>
                        <span class="fw-bold text-success">
                            $<?= number_format($p->total, 2) ?>
                        </span>
                    </div>

                </div>

            </div>

            <!-- FOOTER -->
            <div class="card-footer bg-white border-0 d-flex justify-content-between p-2">

                <?php if (!empty($p->foto)): ?>
                    <button
                        class="btn btn-sm btn-outline-secondary py-1 px-2"
                        onclick="verImagen('<?= base_url('upload/paquetes/' . $p->foto) ?>')">
                        📷 Ver foto
                    </button>
                <?php endif; ?>

                <a href="<?= base_url('packages/' . $p->id) ?>" class="btn btn-sm btn-primary py-1 px-2">
                    Ver
                </a>

            </div>

        </div>

    </div>

<?php endforeach; ?>