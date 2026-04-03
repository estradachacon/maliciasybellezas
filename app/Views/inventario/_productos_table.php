<div class="row">

    <?php foreach ($productos as $p): ?>
        <div class="col-12 mb-3">

            <div class="card shadow-sm producto-card">

                <div class="card-body position-relative">

                    <!-- 📱 MOBILE (stack) -->
                    <div class="d-block d-md-none">
                        <span class="badge badge-primary stock-badge d-md-none">
                            <?= $p->stock ?? 0 ?>
                        </span>
                        <div class="mb-1">
                            <small class="text-muted">ID:</small>
                            <strong>#<?= $p->id ?></strong>
                        </div>

                        <div class="mb-1">
                            <small class="text-muted">Producto:</small>
                            <?= $p->nombre ?>
                        </div>

                        <div class="d-flex justify-content-between mb-2">

                            <!-- 💰 PRECIO -->
                            <div>
                                <small class="text-muted">Precio:</small>
                                <strong>$<?= number_format($p->precio, 2) ?></strong>
                            </div>

                            <!-- 🔘 BOTONES -->
                            <div class="d-flex gap-2">

                                <button class="btn btn-sm btn-outline-primary ver-imagen-btn mr-2"
                                    data-img="<?= base_url('upload/productos/' . ($p->imagen ?? '')) ?>">Foto
                                    <i class="fa fa-image"></i>
                                </button>

                                <a href="<?= base_url('inventario/' . $p->id) ?>"
                                    class="btn btn-sm btn-outline-secondary">Detalle
                                    <i class="fa fa-eye"></i>
                                </a>

                            </div>

                        </div>
                    </div>

                    <!-- 💻 DESKTOP (tipo fila) -->
                    <div class="d-none d-md-flex align-items-center justify-content-between producto-row">

                        <div style="width:7%;">
                            <small class="text-muted">ID:</small>
                            <strong>#<?= $p->id ?></strong>
                        </div>

                        <div style="width:43%;">
                            <small class="text-muted">Producto:</small>
                            <?= $p->nombre ?>
                        </div>

                        <div style="width:20%;" class="text-center">
                            <small class="text-muted">Precio:</small>
                            $<?= number_format($p->precio, 2) ?>
                        </div>

                        <div style="width:20%;" class="text-center">
                            <small class="text-muted">Stock:</small>
                            <span class="badge badge-primary">
                                <?= $p->stock ?? 0 ?>
                            </span>
                        </div>

                        <div style="width:10%;" class="text-right">

                            <button class="btn btn-sm btn-outline-primary ver-imagen-btn"
                                data-img="<?= base_url('upload/productos/' . ($p->imagen ?? '')) ?>">
                                Foto <i class="fa fa-image"></i>
                            </button>

                            <a href="<?= base_url('inventario/' . $p->id) ?>"
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