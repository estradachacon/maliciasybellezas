<?= $this->extend('Layouts/mainbody') ?>
<?= $this->section('content') ?>

<style>
    .table td {
        vertical-align: middle;
    }
</style>

<?php
$totalCompra = 0;

foreach ($detalles as $d) {
    $totalCompra += $d->cantidad * $d->precio_unitario;
}
?>

<div class="row">
    <div class="col-md-12">
        <div class="card">

            <!-- HEADER -->
            <div class="card-header d-flex justify-content-between">

                <div>
                    <h4 class="mb-0">
                        Compra
                        <span class="badge bg-primary text-white ms-2">
                            #<?= $compra->id ?>
                        </span>
                    </h4>

                    <div class="fw-bold text-uppercase mt-1" style="letter-spacing: 1px;">
                        Registro de compra
                    </div>
                </div>

                <div class="text-end border rounded bg-light px-3 py-2"
                    style="min-width: 300px;">

                    <!-- 📅 FECHA APLICADA -->
                    <div class="d-flex justify-content-between">

                        <small class="text-muted">
                            Fecha aplicada
                        </small>

                        <span class="fw-bold">
                            <?= !empty($compra->fecha_compra)
                                ? date('d/m/Y', strtotime($compra->fecha_compra))
                                : 'N/D' ?>
                        </span>

                    </div>

                    <!-- 🕒 FECHA REGISTRO -->
                    <div class="d-flex justify-content-between mt-1">

                        <small class="text-muted">
                            Fecha registro
                        </small>

                        <span>
                            <?= date('d/m/Y', strtotime($compra->created_at)) ?>
                        </span>

                    </div>

                    <!-- 💰 TOTAL -->
                    <div class="mt-2 d-flex justify-content-between">

                        <small class="text-muted">
                            Total
                        </small>

                        <span class="fw-bold text-success" style="font-size:18px;">
                            $<?= number_format($totalCompra, 2) ?>
                        </span>

                    </div>

                </div>

            </div>

            <div class="card-body">

                <!-- INFO PRINCIPAL -->
                <div class="row mb-4">

                    <div class="col-md-6">
                        <div class="p-3 border rounded h-100">

                            <small class="text-muted">Proveedor</small>
                            <div class="fw-semibold">
                                <strong><?= esc($compra->proveedor_nombre) ?></strong>
                            </div>

                            <small class="text-muted mt-2 d-block">Sucursal</small>
                            <div class="fw-semibold">
                                <?= esc($compra->branch_name) ?>
                            </div>

                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="p-3 border rounded h-100">

                            <small class="text-muted">Usuario</small>
                            <div class="fw-semibold">
                                <?= esc($compra->usuario ?? 'N/D') ?>
                            </div>

                            <small class="text-muted mt-2 d-block">Observación</small>
                            <div class="fw-semibold">
                                <?= $compra->observacion ? esc($compra->observacion) : '—' ?>
                            </div>

                        </div>
                    </div>

                </div>

                <!-- TABLA DETALLE -->
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">

                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Producto</th>
                                <th class="text-end">Cantidad</th>
                                <th class="text-end">Costo</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php foreach ($detalles as $i => $d): ?>

                                <tr>
                                    <td><?= $i + 1 ?></td>

                                    <td>
                                        <strong><?= esc($d->producto_nombre) ?></strong>
                                    </td>

                                    <td class="text-end">
                                        <?= number_format($d->cantidad, 2) ?>
                                    </td>

                                    <td class="text-end">
                                        $<?= number_format($d->precio_unitario, 2) ?>
                                    </td>

                                    <td class="text-end">
                                        $<?= number_format($d->cantidad * $d->precio_unitario, 2) ?>
                                    </td>
                                </tr>

                            <?php endforeach ?>

                        </tbody>

                    </table>
                </div>

                <!-- TOTALES -->
                <div class="row mt-4">

                    <div class="col-md-4 offset-md-8">

                        <table class="table table-borderless">

                            <tr>
                                <th class="text-end">Total compra:</th>
                                <td class="text-end fs-5 fw-bold text-success">
                                    $<?= number_format($totalCompra, 2) ?>
                                </td>
                            </tr>

                        </table>

                    </div>

                </div>
                <?php if (!empty($pagos)): ?>

                    <div class="mt-4">

                        <button class="btn btn-outline-secondary btn-sm"
                            type="button"
                            data-toggle="collapse"
                            data-target="#tablaPagosCompra"
                            aria-expanded="false"
                            aria-controls="tablaPagosCompra">

                            <i class="fa fa-money-bill-wave mr-1"></i>
                            Pagos aplicados
                            <span class="badge badge-success ml-1">
                                <?= count($pagos) ?>
                            </span>

                        </button>

                        <div class="collapse mt-3" id="tablaPagosCompra">

                            <div class="table-responsive">

                                <table class="table table-sm table-bordered table-hover align-middle">

                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Cuenta</th>
                                            <th class="text-end">Monto</th>
                                        </tr>
                                    </thead>

                                    <?php $totalPagado = 0; ?>

                                    <tbody>

                                        <?php foreach ($pagos as $i => $p):
                                            $totalPagado += $p->monto;
                                        ?>

                                            <tr>

                                                <td>
                                                    <span class="badge badge-secondary">
                                                        #<?= $i + 1 ?>
                                                    </span>
                                                </td>

                                                <td>
                                                    <?= esc($p->cuenta_nombre ?? 'Cuenta #' . $p->cuenta_id) ?>
                                                </td>

                                                <td class="text-end">
                                                    $<?= number_format($p->monto, 2) ?>
                                                </td>

                                            </tr>

                                        <?php endforeach ?>

                                    </tbody>

                                    <tfoot>

                                        <tr class="table-light">
                                            <th colspan="2" class="text-end">Total pagado</th>
                                            <th class="text-end text-success">
                                                $<?= number_format($totalPagado, 2) ?>
                                            </th>
                                        </tr>

                                    </tfoot>

                                </table>

                            </div>

                        </div>

                    </div>

                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>