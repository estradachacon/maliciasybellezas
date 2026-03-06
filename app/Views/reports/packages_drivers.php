<?= $this->extend('Layouts/mainbody') ?>
<?= $this->section('content') ?>
<?php
$hasFilters = !empty($filters['driver_id']) ||
    !empty($filters['fecha_desde']) ||
    !empty($filters['fecha_hasta']);
?>
<style>
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

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px !important;
    }

    /* focus igual que form-control */
    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: #86b7fe;
        box-shadow: 0 0 0 .25rem rgba(13, 110, 253, .25);
    }
</style>
<div class="row">

    <div class="col-md-12">
        <div class="card">

            <div class="card-header d-flex align-items-center">

                <h4 class="header-title mb-0">
                    Reporte de Paquetería por Conductor
                </h4>

                <div class="ml-auto d-flex align-items-center">

                    <?php if ($hasFilters && !empty($packages)): ?>

                        <a href="<?= base_url('reports/packages-drivers/excel?' . http_build_query($filters)) ?>"
                            class="btn btn-success btn-sm mr-2">
                            <i class="fa-solid fa-file-excel"></i> Excel
                        </a>

                        <a href="<?= base_url('reports/packages-drivers/pdf?' . http_build_query($filters)) ?>"
                            class="btn btn-danger btn-sm mr-2">
                            <i class="fa-solid fa-file-pdf"></i> PDF
                        </a>

                    <?php endif; ?>

                    <a href="<?= base_url('reports') ?>" class="btn btn-primary btn-sm">
                        <i class="fa-solid fa-arrow-left"></i> Volver
                    </a>

                </div>

            </div>

            <div class="card-body">

                <form method="get">

                    <div class="row">

                        <div class="col-md-3">
                            <label>Conductor</label>
                            <select name="driver_id" class="form-control select2">

                                <option value="">Todos</option>

                                <?php foreach ($drivers as $d): ?>

                                    <option value="<?= $d['id'] ?>"
                                        <?= (!empty($filters['driver_id']) && $filters['driver_id'] == $d['id']) ? 'selected' : '' ?>>

                                        <?= esc($d['user_name']) ?>

                                    </option>

                                <?php endforeach; ?>

                            </select>
                        </div>

                        <div class="col-md-2">
                            <label>Fecha desde</label>
                            <input type="date"
                                name="fecha_desde"
                                class="form-control"
                                value="<?= $filters['fecha_desde'] ?? '' ?>">
                        </div>

                        <div class="col-md-2">
                            <label>Fecha hasta</label>
                            <input type="date"
                                name="fecha_hasta"
                                class="form-control"
                                value="<?= $filters['fecha_hasta'] ?? date('Y-m-d') ?>">
                        </div>

                        <div class="col-md-3">
                            <label>Estatus</label>
                            <select name="estatus" class="form-control">

                                <option value="">Todos</option>

                                <option value="asignado" <?= ($filters['estatus'] ?? '') == 'asignado' ? 'selected' : '' ?>>
                                    Asignado
                                </option>

                                <option value="asignado_para_entrega" <?= ($filters['estatus'] ?? '') == 'asignado_para_entrega' ? 'selected' : '' ?>>
                                    Asignado para entrega
                                </option>

                                <option value="entregado" <?= ($filters['estatus'] ?? '') == 'entregado' ? 'selected' : '' ?>>
                                    Entregado
                                </option>

                                <option value="no_retirado" <?= ($filters['estatus'] ?? '') == 'no_retirado' ? 'selected' : '' ?>>
                                    No retirado
                                </option>

                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button class="btn btn-success btn-block">
                                <i class="fa-solid fa-search"></i> Generar
                            </button>
                        </div>

                    </div>

                </form>

                <hr>
                <?php if ($hasFilters): ?>
                    <div class="alert alert-info">
                        Mostrando resultados para:
                        <?php if (!empty($filters['driver_id'])): ?>
                            <strong>Conductor:</strong> <?= esc($drivers[array_search($filters['driver_id'], array_column($drivers, 'id'))]['user_name']) ?>
                        <?php endif; ?>
                        <?php if (!empty($filters['fecha_desde'])): ?>
                            <strong>Desde:</strong> <?= esc($filters['fecha_desde']) ?>
                        <?php endif; ?>
                        <?php if (!empty($filters['fecha_hasta'])): ?>
                            <strong>Hasta:</strong> <?= esc($filters['fecha_hasta']) ?>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                <?php if ($hasFilters && !empty($packages)): ?>

                    <div class="table-responsive">

                        <table class="table table-bordered table-sm table-hover">

                            <thead class="thead-light">

                                <tr>
                                    <th>#</th>
                                    <th>Cliente</th>
                                    <th>Conductor</th>
                                    <th>Servicio</th>
                                    <th>Fecha ingreso</th>
                                    <th>Estatus</th>
                                    <th class="text-right">Flete</th>
                                    <th class="text-right">Monto</th>
                                </tr>

                            </thead>

                            <tbody>

                                <?php foreach ($packages as $pkg): ?>

                                    <tr>

                                        <td><?= $pkg['id'] ?></td>

                                        <td><?= esc($pkg['cliente']) ?></td>

                                        <td><?= esc($pkg['motorista']) ?></td>

                                        <td><?= serviceLabel($pkg['tipo_servicio']) ?></td>

                                        <td>
                                            <?= date('d/m/Y', strtotime($pkg['fecha_ingreso'])) ?>
                                        </td>

                                        <td><?= statusBadge($pkg['estatus']) ?></td>

                                        <td class="text-right">
                                            $<?= number_format($pkg['flete_total'], 2) ?>
                                        </td>

                                        <td class="text-right">
                                            $<?= number_format($pkg['monto'], 2) ?>
                                        </td>

                                    </tr>

                                <?php endforeach ?>

                            </tbody>

                        </table>

                    </div>

                    <div class="d-flex justify-content-center mt-3">
                        <?= $pager->links('packages_drivers', 'bitacora_pagination') ?>
                    </div>

                <?php else: ?>

                    <?php if (!$hasFilters): ?>

                        <p class="text-center text-muted">
                            Seleccione al menos un filtro para generar el reporte.
                        </p>

                    <?php else: ?>

                        <p class="text-center text-muted">
                            No se encontraron resultados para los filtros seleccionados.
                        </p>

                    <?php endif; ?>

                <?php endif ?>

            </div>

        </div>

    </div>

</div>

<?= $this->endSection() ?>