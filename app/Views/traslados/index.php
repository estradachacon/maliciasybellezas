<?= $this->extend('Layouts/mainbody') ?>
<?= $this->section('content') ?>

<div class="row">
    <div class="col-md-12">
        <div class="card">

            <div class="card-header d-flex align-items-center">
                <h4 class="header-title mb-0">Traslados</h4>
                <?php if (tienePermiso('crear_traslado')): ?>
                    <a href="<?= base_url('traslados/crear') ?>"
                        class="btn btn-primary btn-sm ml-auto">
                        <i class="fa-solid fa-plus"></i> Nuevo traslado
                    </a>
                <?php endif; ?>
            </div>

            <div class="card-body">

                <!-- FILTROS -->
                <div class="row mb-3 align-items-end">

                    <div class="col-md-2">
                        <label>Origen</label>
                        <div class="input-group">
                            <input type="text" id="searchOrigen" class="form-control" placeholder="Sucursal origen">
                            <div class="input-group-append">
                                <button class="btn btn-secondary" id="clearOrigenBtn" style="display:none;">
                                    <i class="fa fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <label>Destino</label>
                        <div class="input-group">
                            <input type="text" id="searchDestino" class="form-control" placeholder="Sucursal destino">
                            <div class="input-group-append">
                                <span id="loading-spinner" class="input-group-text" style="display:none;">
                                    <i class="fa fa-spinner fa-spin"></i>
                                </span>
                                <button class="btn btn-secondary" id="clearDestinoBtn" style="display:none;">
                                    <i class="fa fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <label>Estado</label>
                        <select id="filtroEstado" class="form-control">
                            <option value="">Todos</option>
                            <option value="completado">Completado</option>
                            <option value="pendiente">Pendiente</option>
                            <option value="cancelado">Cancelado</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label>Desde</label>
                        <input type="date" id="fechaDesde" class="form-control">
                    </div>

                    <div class="col-md-2">
                        <label>Hasta</label>
                        <input type="date" id="fechaHasta" class="form-control">
                    </div>

                    <div class="col-md-1">
                        <label>Orden</label>
                        <select id="order" class="form-control">
                            <option value="DESC">↓</option>
                            <option value="ASC">↑</option>
                        </select>
                    </div>

                    <div class="col-md-1">
                        <label>Mostrar</label>
                        <select id="perPage" class="form-control">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                    </div>

                </div>

                <!-- LISTADO -->
                <div id="table-container">
                    <?= $this->include('traslados/_list') ?>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {

    const searchOrigen   = document.getElementById('searchOrigen');
    const searchDestino  = document.getElementById('searchDestino');
    const tableContainer = document.getElementById('table-container');
    const spinner        = document.getElementById('loading-spinner');
    const clearOrigenBtn = document.getElementById('clearOrigenBtn');
    const clearDestinoBtn = document.getElementById('clearDestinoBtn');
    const baseUrl        = '<?= base_url('traslados/searchAjax') ?>';

    let searchTimeout;

    function loadResults(page = 1) {
        const params = new URLSearchParams({
            q_origen:    searchOrigen.value.trim(),
            q_destino:   searchDestino.value.trim(),
            page:        page,
            estado:      document.getElementById('filtroEstado').value,
            fecha_desde: document.getElementById('fechaDesde').value,
            fecha_hasta: document.getElementById('fechaHasta').value,
            order:       document.getElementById('order').value,
            perPage:     document.getElementById('perPage').value,
        });

        spinner.style.display = 'block';

        fetch(`${baseUrl}?${params.toString()}`)
            .then(r => r.text())
            .then(html => {
                tableContainer.innerHTML = html;
                spinner.style.display = 'none';
                rebindPagination();
            });
    }

    function rebindPagination() {
        document.querySelectorAll('#pagination-links a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const page = new URL(this.href).searchParams.get('page');
                loadResults(page);
            });
        });
    }

    function bindSearchInput(input, clearBtn) {
        input.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            clearBtn.style.display = this.value.trim().length > 0 ? 'block' : 'none';
            searchTimeout = setTimeout(() => loadResults(), 300);
        });
        clearBtn.addEventListener('click', function() {
            input.value = '';
            clearBtn.style.display = 'none';
            loadResults();
        });
    }

    bindSearchInput(searchOrigen, clearOrigenBtn);
    bindSearchInput(searchDestino, clearDestinoBtn);

    document.querySelectorAll('#filtroEstado, #fechaDesde, #fechaHasta, #order, #perPage')
        .forEach(el => el.addEventListener('change', () => loadResults()));

    rebindPagination();
    loadResults();
});
</script>

<?= $this->endSection() ?>