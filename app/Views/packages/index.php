<?= $this->extend('Layouts/mainbody') ?>
<?= $this->section('content') ?>

<style>
    .paquete-card {
        border-radius: 14px;
        transition: 0.2s;
    }

    .paquete-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
    }

    .paquete-card {
        border-radius: 12px;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.08);
        transition: all 0.2s ease;
        background: #f4f1f8ea;
    }

    /* hover solo en desktop */
    @media (hover: hover) {
        .paquete-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }
    }

    /* efecto al pasar mouse (desktop) */
    .paquete-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }

    .paquete-card {
        border-left: 4px solid #f4f1f8ea;
    }

    .paquete-card {
        position: relative;
        overflow: hidden;
    }

    .watermark-check {
        position: absolute;
        bottom: -10px;
        right: -10px;
        font-size: 120px;
        opacity: 0.08;
        pointer-events: none;
        z-index: 0;
    }

    .watermark-entregado {
        color: #0dcaf0;
        /* celeste */
    }

    .watermark-remunerado {
        color: #198754;
        /* verde */
    }

    .select2-container .select2-selection--single {
        height: 38px !important;
        /* altura estándar Bootstrap */
        border: 1px solid #ced4da;
        border-radius: .375rem;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 36px !important;
        /* centra texto */
        padding-left: .75rem;
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
                            📥 Excel
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
                        <small>Encomendista</small>
                        <select id="encomendistaFiltro" class="form-control"></select>
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
                <div class="row g-2" id="contenedorPaquetes">
                    <?= view('packages/_cards', ['paquetes' => $paquetes]) ?>
                </div>
                <div id="pagerContainer">
                    <?= $pager->links('default', 'bitacora_pagination') ?>
                </div>
            </div>

        </div>
    </div>
</div>
<!-- MODAL IMAGEN -->
<div class="modal fade" id="modalImagen" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-dark text-center">
            <div class="modal-body p-2">
                <img id="imagenGrande" class="img-fluid rounded">
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

        $('#encomendistaFiltro').select2({
            language: 'es',
            minimumInputLength: 1,
            placeholder: 'Buscar encomendista...',
            allowClear: true,
            width: '100%',
            ajax: {
                url: '<?= base_url('encomendistas-buscar') ?>',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        term: params.term
                    };
                },
                processResults: function(data) {
                    return {
                        results: data
                    };
                }
            }
        });

        // FILTRAR
        $('#btnLimpiar').on('click', function() {

            $('#clienteFiltro').val('');
            $('#estadoFiltro').val('');
            $('#encomendistaFiltro').val(null).trigger('change');

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
    setInterval(() => {
        location.reload();
    }, 15000);
</script>
<script>
    function verImagen(src) {
        document.getElementById('imagenGrande').src = src;
        $('#modalImagen').modal('show'); // Bootstrap 4
    }

    function cargarPaquetes(url = null) {

        let cliente = $('#clienteFiltro').val();
        let fechaRango = $('#fechaRango').val();
        let encomendista = $('#encomendistaFiltro').val();

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
            fecha_fin,
            encomendista
        });

        let endpoint = url || "<?= base_url('packages') ?>";

        fetch(endpoint + '?' + params.toString(), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(r => r.json())
            .then(data => {
                $('#contenedorPaquetes').html(data.html);
                $('#pagerContainer').html(data.pager);
            });
    }
    // listeners
    $('#clienteFiltro').on('keyup', cargarPaquetes);
    $('#fechaRango, #estadoFiltro').on('change', cargarPaquetes);
    $('#btnFiltrar').on('click', function() {
        cargarPaquetes();
    });
    $('#encomendistaFiltro').on('change', function() {
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
                $('#contenedorPaquetes').html(data.html);
                $('#pagerContainer').html(data.pager);
            });

    });
</script>

<?= $this->endSection() ?>