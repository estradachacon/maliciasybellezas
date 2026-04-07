<div class="row">

    <?php foreach ($ventas as $v): ?>
        <div class="col-12">

            <div class="card shadow-sm venta-card">
                <div class="card-body">

                    <!-- 📱 MOBILE -->
                    <div class="d-block d-md-none">

                        <div class="mb-1 fw-bold">
                            #<?= $v->id ?> - <?= esc($v->cliente ?? 'Clientes varios') ?>
                        </div>

                        <div class="d-flex justify-content-between mb-1">

                            <div>
                                <small class="text-muted">Total:</small>
                                <strong>$<?= number_format($v->total, 2) ?></strong>
                            </div>

                            <div>
                                <small class="text-muted">Items:</small>
                                <?= $v->items ?? 0 ?>
                            </div>

                        </div>

                        <div class="mb-1">
                            <small class="text-muted">Fecha:</small>
                            <?= date('d/m/Y H:i', strtotime($v->created_at)) ?>
                        </div>

                        <div class="d-flex justify-content-end mt-2">
                            <a href="<?= base_url('ventas/' . $v->id) ?>"
                               class="btn btn-sm btn-outline-secondary">
                                <i class="fa fa-eye"></i>
                            </a>
                        </div>

                    </div>

                    <!-- 💻 DESKTOP -->
                    <div class="d-none d-md-flex align-items-center justify-content-between">

                        <div style="width:10%;">
                            <strong>#<?= $v->id ?></strong>
                        </div>

                        <div style="width:35%;">
                            <?= esc($v->cliente ?? 'Clientes varios') ?>
                        </div>

                        <div style="width:15%;" class="text-center">
                            $<?= number_format($v->total, 2) ?>
                        </div>

                        <div style="width:20%;" class="text-center">
                            <?= date('d/m/Y H:i', strtotime($v->created_at)) ?>
                        </div>

                        <div style="width:10%;" class="text-center">
                            <?= $v->items ?? 0 ?>
                        </div>

                        <div style="width:10%;" class="text-right">
                            <a href="<?= base_url('ventas/' . $v->id) ?>"
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