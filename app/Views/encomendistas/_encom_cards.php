<?php if (!empty($encomendistas)): ?>
<style>
    .card {
    border-radius: 10px;
}

.card h6 {
    font-size: 15px;
}

.card small {
    font-size: 12px;
}
.card:active {
    transform: scale(0.98);
    transition: 0.1s;
}
</style>
    <div class="row">
        <?php foreach ($encomendistas as $encomendista): ?>
            <div class="col-12 mb-2">
                <div class="card shadow-sm border-0">
                    <div class="card-body py-2 px-3">

                        <!-- Header -->
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0 font-weight-bold">
                                    <?= esc($encomendista->encomendista_name) ?>
                                </h6>
                                <small class="text-muted">
                                    ID: <?= esc($encomendista->id) ?>
                                </small>
                            </div>

                            <!-- Acciones -->
                            <div class="text-right">
                                <?php if (tienePermiso('editar_encomendista')): ?>
                                    <a href="<?= base_url('encomendistas/edit/' . $encomendista->id) ?>"
                                       class="btn btn-sm btn-info">
                                        <i class="fa-solid fa-edit"></i>
                                    </a>
                                <?php endif; ?>

                                <?php if (tienePermiso('eliminar_vendedor')): ?>
                                    <button class="btn btn-sm btn-danger delete-btn"
                                            data-id="<?= $encomendista->id ?>">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Paginación -->
    <div class="mt-2" id="pagination-links">
        <?= $pager->links('default', 'bitacora_pagination') ?>
    </div>

<?php else: ?>
    <div class="alert alert-warning text-center">
        No hay encomendistas registrados
        <?= !empty($q) ? ' para la búsqueda "' . esc($q) . '"' : '' ?>
    </div>
<?php endif; ?>