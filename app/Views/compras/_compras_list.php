<div class="row">

    <?php foreach ($compras as $c): ?>
        <div class="col-12 mb-3">

            <div class="card shadow-sm compra-card">
                <div class="card-body">

                    <!-- MOBILE -->
                    <div class="d-block d-md-none">

                        <div class="mb-1">
                            <small class="text-muted">Compra:</small>
                            <strong>#<?= $c->id ?></strong>
                        </div>

                        <div class="mb-1">
                            <small class="text-muted">Proveedor:</small>
                            <?= esc($c->proveedor) ?>
                        </div>

                        <div class="mb-1">
                            <small class="text-muted">Total:</small>
                            $<?= number_format($c->total, 2) ?>
                        </div>

                        <div class="mb-2">
                            <small class="text-muted">Fecha:</small>
                            <?= date('d/m/Y H:i', strtotime($c->created_at)) ?>
                        </div>

                    </div>

                    <!-- DESKTOP -->
                    <div class="d-none d-md-flex justify-content-between align-items-center">

                        <div style="width:10%;">
                            <small class="text-muted">Compra:</small>
                            <strong>#<?= $c->id ?></strong>
                        </div>

                        <div style="width:30%;">
                            <small class="text-muted">Proveedor:</small>
                            <?= esc($c->proveedor) ?>
                        </div>

                        <div style="width:20%;">
                            <small class="text-muted">Total:</small>
                            $<?= number_format($c->total, 2) ?>
                        </div>

                        <div style="width:20%;">
                            <small class="text-muted">Fecha:</small>
                            <?= date('d/m/Y H:i', strtotime($c->created_at)) ?>
                        </div>

                        <a href="<?= base_url('compras/' . $c->id) ?>" 
                            class="btn btn-sm btn-light mt-1">
                                <i class="fa fa-eye"></i>
                            </a>
                    </div>

                </div>
            </div>

        </div>
    <?php endforeach; ?>

</div>

<!-- PAGINACIÓN -->
<div id="pagination-links" class="mt-3">
    <?= $pager->links('default', 'bitacora_pagination') ?>
</div>