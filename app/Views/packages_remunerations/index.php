<?= $this->extend('Layouts/mainbody') ?>
<?= $this->section('content') ?>

<style>
    .remu-card {
        transition: all 0.2s ease;
        border-radius: 10px;
    }

    .remu-card:hover {
        transform: scale(1.01);
    }

    .remu-card .card-body {
        padding: 10px 15px;
    }

    .remu-card {
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
                <h4 class="header-title">Remuneraciones</h4>

                <a href="<?= base_url('packages-remunerations/create') ?>" 
                   class="btn btn-primary btn-sm ml-auto">
                    <i class="fa-solid fa-plus"></i> Nueva
                </a>
            </div>

            <div class="card-body">

                <!-- 🔍 BUSCADOR -->
                <div class="mb-3">
                    <input type="text" id="searchInput" class="form-control"
                        placeholder="Buscar por observaciones o ID...">
                </div>

                <!-- 📋 LISTADO -->
                <div id="table-container">
                    <?= $this->include('packages_remunerations/_list') ?>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {

    const searchInput = document.getElementById('searchInput');
    const tableContainer = document.getElementById('table-container');

    let timeout;

    searchInput.addEventListener('input', function() {
        clearTimeout(timeout);

        timeout = setTimeout(() => {
            fetch(`<?= base_url('packages-remunerations/searchAjax') ?>?q=${this.value}`)
                .then(res => res.text())
                .then(html => tableContainer.innerHTML = html);
        }, 300);
    });

});
</script>

<?= $this->endSection() ?>