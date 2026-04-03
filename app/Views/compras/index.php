<?= $this->extend('Layouts/mainbody') ?>
<?= $this->section('content') ?>

<style>
    .compra-card {
        transition: all 0.2s ease;
        border-radius: 10px;
    }

    .compra-card:hover {
        transform: scale(1.01);
    }

    .compra-card .card-body {
        padding: 10px 15px;
    }

    .compra-card {
        margin-bottom: 8px;
    }

    small.text-muted {
        font-size: 13px;
        line-height: 1;
        margin-right: 8px;
    }
</style>

<div class="row">
    <div class="col-md-12">
        <div class="card">

            <!-- HEADER -->
            <div class="card-header d-flex">
                <h4 class="header-title">Compras</h4>

                <?php if (tienePermiso('ingresar_compra')): ?>
                    <a href="<?= base_url('compras/create') ?>" class="btn btn-primary btn-sm ml-auto">
                        <i class="fa-solid fa-plus"></i> Nueva Compra
                    </a>
                <?php endif; ?>
            </div>

            <div class="card-body">

                <!-- 🔍 BUSCADOR -->
                <div class="row mb-3 align-items-end">

                    <div class="col-md-6">
                        <label for="searchInput">Buscar compra</label>
                        <div class="input-group">

                            <input type="text" id="searchInput" class="form-control"
                                placeholder="Proveedor o número de compra">

                            <div class="input-group-append">
                                <span class="input-group-text" id="loading-spinner" style="display:none;">
                                    <i class="fa fa-spinner fa-spin"></i>
                                </span>

                                <button class="btn btn-secondary" id="clearSearchBtn" style="display:none;">
                                    <i class="fa fa-times"></i>
                                </button>
                            </div>

                        </div>
                    </div>

                </div>

                <!-- 📋 LISTADO DINÁMICO -->
                <div id="table-container">
                    <?= $this->include('compras/_compras_list') ?>
                </div>

            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {

    const searchInput = document.getElementById('searchInput');
    const tableContainer = document.getElementById('table-container');
    const loadingSpinner = document.getElementById('loading-spinner');
    const clearSearchBtn = document.getElementById('clearSearchBtn');

    const baseUrl = '<?= base_url('compras/searchAjax') ?>';

    let searchTimeout;

    function loadResults(query = '', page = 1) {

        const url = `${baseUrl}?q=${encodeURIComponent(query)}&page=${page}`;

        loadingSpinner.style.display = 'block';

        fetch(url)
            .then(res => res.text())
            .then(html => {
                tableContainer.innerHTML = html;
                loadingSpinner.style.display = 'none';
                updateClearButton(query);
                rebindEvents();
            });
    }

    // 🔍 búsqueda
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();

        searchTimeout = setTimeout(() => {
            loadResults(query);
        }, 300);
    });

    clearSearchBtn.addEventListener('click', function() {
        searchInput.value = '';
        loadResults('');
        updateClearButton('');
    });

    function updateClearButton(query) {
        clearSearchBtn.style.display = query.length > 0 ? 'block' : 'none';
    }

    function rebindEvents() {
        document.querySelectorAll('#pagination-links a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const url = new URL(this.href);
                const page = url.searchParams.get('page');
                loadResults(searchInput.value.trim(), page);
            });
        });
    }

    // init
    rebindEvents();

});
</script>
<?= $this->endSection() ?>