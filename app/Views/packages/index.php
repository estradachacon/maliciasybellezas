<?= $this->extend('Layouts/mainbody') ?>
<?= $this->section('content') ?>

<style>
    .badge-estado {
        font-size: 0.7rem;
        padding: 4px 10px;
        border-radius: 8px;
    }
</style>

<div class="row">
    <div class="col-md-12">

        <div class="card shadow-sm">

            <div class="card-header d-flex justify-content-between">
                <h5 class="mb-0">📦 Listado de paquetes</h5>

                <?php if (tienePermiso('crear_paquetes')): ?>
                    <a href="<?= base_url('packages/new') ?>" class="btn btn-primary btn-sm ms-auto">
                        + Nuevo
                    </a>
                <?php endif; ?>
            </div>

            <div class="card-body">

                <!-- 🔍 FILTROS -->
                <form onsubmit="return false" class="mb-3">
                    <div class="row g-2">

                        <div class="col-md-4">
                            <small>Cliente</small>
                            <input type="text" id="clienteFiltro" class="form-control">
                        </div>

                        <div class="col-md-3">
                            <small>Fecha</small>
                            <input type="date" id="fechaFiltro" class="form-control">
                        </div>

                        <div class="col-md-3">
                            <small>Estado</small>
                            <select id="estadoFiltro" class="form-control">
                                <option value="">Todos</option>
                                <option value="0">Activos</option>
                                <option value="1">Cancelados</option>
                            </select>
                        </div>

                    </div>
                </form>

                <!-- 📋 TABLA -->
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Cliente</th>
                            <th>Destino</th>
                            <th>Entrega</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Menú</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?= view('packages/_tbody', ['paquetes' => $paquetes]) ?>
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
    function cargarPaquetes() {

        let cliente = $('#clienteFiltro').val();
        let fecha = $('#fechaFiltro').val();
        let estado = $('#estadoFiltro').val();

        const params = new URLSearchParams({
            cliente: cliente || '',
            fecha: fecha || '',
            estado: estado || ''
        });

        fetch('<?= base_url('paquetes') ?>?' + params.toString(), {
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

    // listeners
    $('#clienteFiltro').on('keyup', cargarPaquetes);
    $('#fechaFiltro, #estadoFiltro').on('change', cargarPaquetes);

    // paginación AJAX
    $(document).on('click', '#pagerContainer a', function(e) {
        e.preventDefault();

        fetch($(this).attr('href'), {
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