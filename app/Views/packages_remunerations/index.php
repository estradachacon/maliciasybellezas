<?= $this->extend('Layouts/mainbody') ?>
<?= $this->section('content') ?>

<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm">
            
        <div class="card-header d-flex justify-content-between">
            <h4 class="header-title mb-0">
                <i class="fa-solid fa-hand-holding-dollar"></i>
                Remuneración de Paquetes
            </h4>
            <a href="<?= base_url('packages-remunerations/create') ?>" class="btn btn-sm btn-primary">
                <i class="fa-solid fa-plus"></i> Nuevo
            </a>
        </div>

            <div class="card-body">

                <!-- Aquí puedes meter filtros después -->
                <div class="mb-3">
                    <input type="text" class="form-control" placeholder="Buscar paquete...">
                </div>

                <!-- Tabla -->
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Tracking</th>
                                <th>Cliente</th>
                                <th>Estado</th>
                                <th>Estado 2</th>
                                <th>Monto</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tabla-paquetes">
                            <tr>
                                <td colspan="7" class="text-center text-muted">
                                    No hay datos aún
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>

        </div>
    </div>
</div>

<?= $this->endSection() ?>