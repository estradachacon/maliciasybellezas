<?php foreach ($paquetes as $p): ?>

    <div class="col-12 col-md-6 col-lg-4">

        <div class="card shadow-sm h-100 border-0 paquete-card">
            <?php
            $estado = strtolower($p->estado1 ?? '');
            $remunerado = strtolower($p->estado2 ?? '') === 'remunerado'; // ajusta si aplica
            ?>

            <?php if ($remunerado): ?>
                <div class="watermark-check watermark-remunerado">
                    ✔
                </div>
            <?php elseif ($estado === 'entregado'): ?>
                <div class="watermark-check watermark-entregado">
                    ✔
                </div>
            <?php endif; ?>
            <div class="card-body p-2">

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

<?php
$totalReal = (float)($p->total_real ?? 0);
$totalRemunerar = (float)($p->total ?? 0);
?>

<div class="row mt-1" style="font-size:13px;">

    <!-- 🔹 IZQUIERDA -->
    <div class="col-6">

        <div>
            <small class="text-muted">Destino:</small><br>
            <span><?= esc($p->destino) ?></span>
        </div>

        <div class="mt-1">
            <small class="text-muted">Entrega:</small><br>
            <?= date('d/m/Y', strtotime($p->dia_entrega)) ?>
        </div>

    </div>

    <!-- 🔹 DERECHA -->
    <div class="col-6 text-end">

        <div>
            <small class="text-muted">Total real:</small><br>
            <span class="fw-bold text-dark">
                $<?= number_format($totalReal, 2) ?>
            </span>
        </div>

        <div class="mt-1">
            <?php if ($totalRemunerar > 0): ?>
                <small class="text-muted">Por cobrar Remu:</small><br>
                <span class="fw-bold text-success">
                    $<?= number_format($totalRemunerar, 2) ?>
                </span>
            <?php else: ?>
                <span class="badge badge-success">
                    ✔ Ya pagado
                </span>
            <?php endif; ?>
        </div>

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