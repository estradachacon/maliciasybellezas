<table class="table table-striped table-bordered table-hover" id="sellers-table">
    <thead>
        <tr>
            <th class="col-1">ID</th>
            <th class="col-7">Nombre</th>
            <th class="col-1">Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($encomendistas)): ?>
            <?php foreach ($encomendistas as $encomendista): ?>
                <tr>
                    <td class="text-center"><?= esc($encomendista->id) ?></td>
                    <td><?= esc($encomendista->encomendista_name) ?></td>
                    <td>
                        <?php if (tienePermiso('editar_encomendista')): ?>
                            <a href="<?= base_url('encomendistas/edit/' . $encomendista->id) ?>" class="btn btn-sm btn-info"><i
                                    class="fa-solid fa-edit"></i></a>
                        <?php endif; ?>
                        <?php if (tienePermiso('eliminar_vendedor')): ?>
                            <button class="btn btn-sm btn-danger delete-btn" data-id="<?= $encomendista->id ?>">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="4" class="text-center">No hay encomendistas registrados<?= !empty($q) ? ' para la búsqueda "' . esc($q) . '"' : '' ?></td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
<div class="mt-3" id="pagination-links">
    <?= $pager->links('default', 'bitacora_pagination') ?>
</div>