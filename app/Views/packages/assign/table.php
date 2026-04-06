<?= $this->extend('Layouts/mainbody') ?>
<?= $this->section('content') ?>

<style>
    .badge-estado {
        font-size: 0.75rem;
        padding: 5px 12px;
        border-radius: 10px;
        font-weight: 500;
    }
</style>

<div class="row">
    <div class="col-md-12">
        <div class="card">

            <div class="card-header d-flex justify-content-between">
                <h4 class="header-title">📦 Asignación de paquetes</h4>
                <?php if (tienePermiso('depositar_por_codigo')): ?>
                    <a href="<?= base_url('packages-assign') ?>"
                        class="btn btn-primary btn-sm ml-auto">
                        <i class="fa-solid fa-plus"></i> Nuevo
                    </a>
                <?php endif; ?>
            </div>

            <div class="card-body">

                <!-- FILTROS -->
                <form onsubmit="return false" class="mb-3">
                    <div class="row">

                        <div class="col-md-4">
                            <small class="text-muted">Encomendista</small>
                            <input type="text" id="nombreFiltro" class="form-control">
                        </div>

                        <div class="col-md-3">
                            <small class="text-muted">Fecha</small>
                            <input type="date" id="fechaFiltro" class="form-control">
                        </div>

                    </div>
                </form>

                <!-- TABLA -->
                <table class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Encomendista</th>
                            <th>Cantidad</th>
                            <th>Total Flete</th>
                            <th>Fecha</th>
                            <th>Registro</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (!empty($deposits)): ?>
                            <?php foreach ($deposits as $d): ?>
                                <tr>
                                    <td class="text-center"><?= $d->id ?></td>

                                    <td>
                                        <?php if (!empty($d->encomendistas)): ?>
                                            <?php foreach (explode(',', $d->encomendistas) as $e): ?>
                                                <span class="badge bg-primary text-white mr-1 mb-1">
                                                    <?= esc(trim($e)) ?>
                                                </span>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            —
                                        <?php endif; ?>
                                    </td>

                                    <td class="text-center">
                                        <span class="badge badge-estado bg-info text-white">
                                            <?= $d->cantidad_paquetes ?>
                                        </span>
                                    </td>

                                    <td class="text-end">
                                        $ <?= number_format($d->flete_total, 2) ?>
                                    </td>

                                    <td class="text-center">
                                        <?= date('d/m/Y', strtotime($d->fecha)) ?>
                                    </td>

                                    <td class="text-center">
                                        <small class="text-muted">
                                            <?= date('d/m/Y H:i', strtotime($d->created_at)) ?>
                                        </small>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">
                                    No hay registros
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <div id="pagerContainer">
                    <?= $pager->links('default', 'bitacora_pagination') ?>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    function cargarDatos() {

        let nombre = $('#nombreFiltro').val();
        let fecha = $('#fechaFiltro').val();

        const params = new URLSearchParams({
            nombre: nombre || '',
            fecha: fecha || ''
        });

        fetch('<?= base_url('packages-assignation') ?>?' + params.toString(), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(r => r.json())
            .then(data => {
                $('tbody').html(data.tbody);
                $('#pagerContainer').html(data.pager);
            });
    }

    // ================= LISTENERS =================
    $('#nombreFiltro').on('input', cargarDatos);
    $('#fechaFiltro').on('change', cargarDatos);

    // ================= PAGINACIÓN AJAX =================
    $(document).on('click', '#pagerContainer a', function(e) {

        e.preventDefault();

        const url = $(this).attr('href');

        fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(r => r.json())
            .then(data => {
                $('tbody').html(data.tbody);
                $('#pagerContainer').html(data.pager);
            });

    });
</script>

<?= $this->endSection() ?>