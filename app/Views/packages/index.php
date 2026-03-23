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

                <div class="d-flex">

                    <?php if (tienePermiso('exportar_paquetes_a_excel')): ?>
                        <a href="#" id="btnExportar" class="btn btn-success btn-sm mr-2">
                            📥 Exportar Excel
                        </a>
                    <?php endif; ?>

                    <?php if (tienePermiso('crear_paquetes')): ?>
                        <a href="<?= base_url('packages/new') ?>" class="btn btn-primary btn-sm">
                            + Nuevo
                        </a>
                    <?php endif; ?>

                </div>
            </div>

            <div class="card-body">

                <div class="row g-2 mb-2">

                    <div class="col-md-3">
                        <small>Cliente</small>
                        <input type="text" id="clienteFiltro" class="form-control">
                    </div>

                    <div class="col-md-3">
                        <small>Rango de fechas</small>
                        <input type="text" id="fechaRango" class="form-control" placeholder="Selecciona rango">
                    </div>

                    <div class="col-md-3">
                        <small>&nbsp;</small>
                        <div class="d-grid gap-2">
                            <button id="btnFiltrar" class="btn btn-primary">
                                🔍 Filtrar
                            </button>
                            <button id="btnLimpiar" class="btn btn-outline-secondary">
                                Limpiar
                            </button>
                        </div>
                    </div>

                </div>

                <table class="table table-bordered table-striped table-sm align-middle">
                    <colgroup>
                        <col style="width: 5%;">
                        <col style="width: 29%;">
                        <col style="width: 29%;">
                        <col style="width: 12%;">
                        <col style="width: 10%;">
                        <col style="width: 8%;">
                        <col style="width: 7%;">
                    </colgroup>
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
    let fp;

    document.addEventListener('DOMContentLoaded', function() {
        $('#btnFiltrar').on('click', function() {
            cargarPaquetes();
        });

        fp = flatpickr("#fechaRango", {
            mode: "range",
            dateFormat: "Y-m-d",
            locale: "es",
            allowInput: true,
            rangeSeparator: " to "
        });

        $('#btnExportar').on('click', function(e) {
            e.preventDefault();

            let cliente = $('#clienteFiltro').val();
            let estado = $('#estadoFiltro').val();

            let fecha_inicio = '';
            let fecha_fin = '';

            if (fp && fp.selectedDates.length === 2) {
                fecha_inicio = fp.formatDate(fp.selectedDates[0], "Y-m-d");
                fecha_fin = fp.formatDate(fp.selectedDates[1], "Y-m-d");
            }

            const params = new URLSearchParams({
                cliente,
                estado,
                fecha_inicio,
                fecha_fin
            });

            window.open("<?= base_url('packages-exportar') ?>?" + params.toString(), '_blank');
        });

        // FILTRAR
        $('#btnLimpiar').on('click', function() {

            $('#clienteFiltro').val('');
            $('#estadoFiltro').val('');

            if (fp) fp.clear();

            cargarPaquetes();
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
        let fechaRango = $('#fechaRango').val();

        let fecha_inicio = '';
        let fecha_fin = '';

        if (fechaRango.includes(' to ')) {
            let partes = fechaRango.split(' to ');
            fecha_inicio = partes[0];
            fecha_fin = partes[1];
        }
        if (fp && fp.selectedDates.length === 2) {
            fecha_inicio = fp.formatDate(fp.selectedDates[0], "Y-m-d");
            fecha_fin = fp.formatDate(fp.selectedDates[1], "Y-m-d");
        }

        let estado = $('#estadoFiltro').val();

        const params = new URLSearchParams({
            cliente,
            estado,
            fecha_inicio,
            fecha_fin
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
    $('#fechaRango, #estadoFiltro').on('change', cargarPaquetes);
    $('#btnFiltrar').on('click', function() {
        cargarPaquetes();
    });

    // paginación AJAX
    $(document).on('click', '#pagerContainer a', function(e) {
        e.preventDefault();

        let url = new URL($(this).attr('href'));

        let cliente = $('#clienteFiltro').val();
        let estado = $('#estadoFiltro').val();
        let fecha_inicio = '';
        let fecha_fin = '';

        if (fp && fp.selectedDates.length === 2) {
            fecha_inicio = fp.formatDate(fp.selectedDates[0], "Y-m-d");
            fecha_fin = fp.formatDate(fp.selectedDates[1], "Y-m-d");
        }
        url.searchParams.set('cliente', cliente || '');
        url.searchParams.set('fecha_inicio', fecha_inicio || '');
        url.searchParams.set('fecha_fin', fecha_fin || '');
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