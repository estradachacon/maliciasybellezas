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
        padding: 8px 12px;
        /* 🔥 más compacto */
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

                <form method="GET">

                    <div class="row mb-3">

                        <div class="col-md-3">
                            <label class="form-label">Buscar</label>
                            <input type="text" name="q" class="form-control"
                                placeholder="Cliente o # venta"
                                value="<?= esc($_GET['q'] ?? '') ?>">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Desde</label>
                            <input type="date" name="desde" class="form-control"
                                value="<?= esc($_GET['desde'] ?? '') ?>">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Hasta</label>
                            <input type="date" name="hasta" class="form-control"
                                value="<?= esc($_GET['hasta'] ?? '') ?>">
                        </div>

                        <div class="col-md-2 mb-2">
                            <label class="form-label">Estado</label>
                            <select name="estado" class="form-control">
                                <option value="">Todos</option>
                                <option value="pagado" <?= (@$_GET['estado'] == 'pagado') ? 'selected' : '' ?>>Pagado</option>
                                <option value="parcial" <?= (@$_GET['estado'] == 'parcial') ? 'selected' : '' ?>>Parcial</option>
                                <option value="pendiente" <?= (@$_GET['estado'] == 'pendiente') ? 'selected' : '' ?>>Pendiente</option>
                            </select>
                        </div>

                        <div class="col-md-2 d-flex align-items-end">
                            <button class="btn btn-primary w-100">
                                <i class="fa fa-search"></i> Filtrar
                            </button>
                        </div>

                    </div>

                </form>

                <!-- 📋 LISTADO -->
                <div id="ventas-container">

                    <div id="ventas-container">

                        <?= view('ventas/_ventas_list', ['ventas' => $ventas]) ?>

                    </div>
                    <div class="mt-3">
                        <?= $pager->links('default', 'bitacora_pagination') ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>