<?= $this->extend('Layouts/mainbody') ?>
<?= $this->section('content') ?>

<style>
    .venta-card {
        transition: all 0.2s ease;
        border-radius: 10px;
    }

    .venta-card:hover {
        transform: scale(1.01);
    }

    .venta-card .card-body {
        padding: 8px 12px; /* 🔥 más compacto */
    }

    .venta-card {
        margin-bottom: 6px;
    }

    small.text-muted {
        font-size: 12px;
        line-height: 1;
        margin-right: 6px;
    }

    @media (max-width: 768px) {
        .venta-card .card-body {
            padding: 8px;
        }

        .venta-card .mb-1 {
            margin-bottom: 4px !important;
        }
    }
</style>

<div class="row">
    <div class="col-md-12">
        <div class="card">

            <!-- HEADER -->
            <div class="card-header d-flex">
                <h4 class="header-title">Ventas</h4>

                <?php if (tienePermiso('crear_venta')): ?>
                    <a href="<?= base_url('ventas/nueva') ?>" class="btn btn-primary btn-sm ml-auto">
                        <i class="fa-solid fa-plus"></i> Nueva Venta
                    </a>
                <?php endif; ?>
            </div>

            <div class="card-body">

                <!-- 🔍 BUSCADOR -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>Buscar venta</label>
                        <input type="text" id="searchInput" class="form-control"
                               placeholder="Cliente o número de venta">
                    </div>
                </div>

                <!-- 📋 LISTADO -->
                <div id="ventas-container">

                    <!-- 🔥 VACÍO (por ahora) -->
                    <div class="text-center text-muted py-4">
                        <i class="fa-solid fa-receipt fa-2x mb-2"></i>
                        <div>No hay ventas registradas</div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>