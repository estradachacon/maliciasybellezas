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

                <div class="row g-2 mb-2">

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
                            <option value="1">Cancelados (total = $0)</option>
                        </select>
                    </div>

                    <!-- 🔥 BOTONES -->
                    <div class="col-md-2 d-flex justify-content-between align-items-end gap-2">
                        <button id="btnFiltrar" class="btn btn-primary w-99">
                            🔍 Filtrar
                        </button>
                        <button id="btnLimpiar" class="btn btn-outline-secondary w-99">
                            Limpiar
                        </button>
                    </div>

                </div>

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
    document.addEventListener('DOMContentLoaded', function() {
        // 🔍 FILTRAR
        $('#btnFiltrar').on('click', function() {
            cargarPaquetes();
        });

        // 🧹 LIMPIAR
        $('#btnLimpiar').on('click', function() {

            $('#clienteFiltro').val('');
            $('#fechaFiltro').val('');
            $('#estadoFiltro').val('');

            cargarPaquetes(); // recarga limpio
        });
        $('#clienteFiltro').on('keypress', function(e) {
            if (e.which === 13) {
                cargarPaquetes();
            }
        });
    });
</script>
<script>
    function cargarPaquetes(url = null) {

        let cliente = $('#clienteFiltro').val();
        let fecha = $('#fechaFiltro').val();
        let estado = $('#estadoFiltro').val();

        const params = new URLSearchParams({
            cliente,
            fecha,
            estado
        });

        let endpoint = url || "<?= base_url('packages') ?>";

        fetch(endpoint + '?' + params.toString(), {
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

        let url = new URL($(this).attr('href'));

        let cliente = $('#clienteFiltro').val();
        let fecha = $('#fechaFiltro').val();
        let estado = $('#estadoFiltro').val();

        url.searchParams.set('cliente', cliente || '');
        url.searchParams.set('fecha', fecha || '');
        url.searchParams.set('estado', estado || '');

        fetch(url.toString(), {
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