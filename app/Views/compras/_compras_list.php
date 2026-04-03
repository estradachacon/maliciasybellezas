<div class="row">

    <?php foreach ($compras as $c): ?>
        <div class="col-12 mb-3">

            <div class="card shadow-sm compra-card">
                <div class="card-body position-relative">

                    <!-- 📱 MOBILE -->
                    <div class="d-block d-md-none">

                        <div class="mb-1">
                            <small class="text-muted">Compra:</small>
                            <strong>#<?= $c->id ?></strong>
                        </div>

                        <div class="mb-1">
                            <small class="text-muted">Proveedor:</small>
                            <?= esc($c->proveedor) ?>
                        </div>

                        <div class="d-flex justify-content-between mb-2">

                            <!-- 💰 TOTAL -->
                            <div>
                                <small class="text-muted">Total:</small>
                                <strong>$<?= number_format($c->total, 2) ?></strong>
                            </div>

                            <!-- 🔘 BOTONES -->
                            <div class="d-flex gap-2">
                                <a href="<?= base_url('compras/' . $c->id) ?>"
                                    class="btn btn-sm btn-outline-secondary">
                                    Ver <i class="fa fa-eye"></i>
                                </a>
                            </div>

                        </div>

                        <div>
                            <small class="text-muted">Fecha creación:</small>
                            <?= date('d/m/Y H:i', strtotime($c->created_at)) ?>
                        </div>

                        <div>
                            <small class="text-muted">Fecha Aplicada:</small>
                            <?= $c->fecha_compra 
                                ? date('d/m/Y', strtotime($c->fecha_compra)) 
                                : '—' ?>
                        </div>

                        <div>
                            <small class="text-muted">Productos:</small>
                            <?= $c->total_items ?? 0 ?>
                        </div>

                    </div>

                    <!-- 💻 DESKTOP -->
                    <div class="d-none d-md-flex align-items-center justify-content-between compra-row">

                        <div style="width:10%;">
                            <small class="text-muted">Compra:</small>
                            <strong>#<?= $c->id ?></strong>
                        </div>

                        <div style="width:35%;">
                            <small class="text-muted">Proveedor:</small>
                            <?= esc($c->proveedor) ?>
                        </div>

                        <div style="width:20%;" class="text-center">
                            <small class="text-muted">Total:</small>
                            $<?= number_format($c->total, 2) ?>
                        </div>

                        <div style="width:35%;" class="text-center">
                            <small class="text-muted">Fecha creación:</small>
                            <?= date('d/m/Y H:i', strtotime($c->created_at)) ?>

                            <br>
                            <small class="text-muted">Fecha Aplicada:</small>
                            <?= $c->fecha_compra 
                                ? date('d/m/Y', strtotime($c->fecha_compra)) 
                                : '—' ?>
                        </div>

                        <div style="width:8%;" class="text-center">
                            <small class="text-muted">Items:</small>
                            <span class="badge badge-primary badge-lg">
                                <?= $c->total_items ?? 0 ?>
                            </span>
                        </div>

                        <div style="width:7%;" class="text-right">

                            <a href="<?= base_url('compras/' . $c->id) ?>"
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

<!-- 📄 PAGINACIÓN -->
<div id="pagination-links" class="mt-3">
    <?= $pager->links('default', 'bitacora_pagination') ?>
</div>