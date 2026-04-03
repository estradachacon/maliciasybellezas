<div class="row">

<?php foreach ($proveedores as $p): ?>
    <div class="col-12 col-md-6 mb-2">

        <div class="card proveedor-card shadow-sm">
            <div class="card-body py-2 px-3">

                <div class="d-flex justify-content-between align-items-start">

                    <!-- INFO -->
                    <div>
                        <h4><strong><?= esc($p->nombre) ?></strong></h4>

                        <div class="text-muted">
                            📞 <?= esc($p->telefono ?: 'N/D') ?>
                        </div>

                        <div class="text-muted">
                            📍 <?= esc($p->direccion ?: 'N/D') ?>
                        </div>

                    </div>

                    <!-- BOTÓN -->
                    <button class="btn btn-sm btn-outline-primary editar-proveedor-btn"
                        data-id="<?= $p->id ?>"
                        data-nombre="<?= esc($p->nombre) ?>"
                        data-telefono="<?= esc($p->telefono) ?>"
                        data-direccion="<?= esc($p->direccion) ?>">
                        <i class="fa fa-pen"></i>
                    </button>

                </div>

            </div>
        </div>

    </div>
<?php endforeach; ?>

</div>