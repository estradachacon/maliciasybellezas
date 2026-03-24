<?= $this->extend('Layouts/mainbody') ?>
<?= $this->section('content') ?>

<style>
    .resumen-box {
        background: #fff;
        border-radius: 12px;
        padding: 15px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    }

    .resumen-box small {
        color: #6c757d;
        display: block;
        font-size: 11px;
    }

    .resumen-box strong {
        font-size: 18px;
    }

    .paquete-card {
        background: #fff;
        border-radius: 12px;
        padding: 12px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }

    .estado-badge {
        font-size: 11px;
        padding: 4px 10px;
        border-radius: 20px;
    }

    .estado-ruta {
        background: #dcfce7;
        color: #166534;
    }

    .estado-casillero {
        background: #e0f2fe;
        color: #0369a1;
    }
</style>

<div class="container-fluid">

    <!-- HEADER -->
    <div class="card shadow-sm mb-3">
        <div class="card-header d-flex justify-content-between">

            <div>
                <h5 class="mb-0">
                    Seguimiento
                    <span class="badge badge-info ml-2">
                        #<?= $deposit->id ?>
                    </span>
                </h5>
                <small class="text-muted">
                    Registro de asignación de paquetes
                </small>
            </div>

        </div>

        <div class="card-body">

            <!-- 🔥 RESUMEN -->
            <div class="row">

                <div class="col-md-3">
                    <div class="resumen-box">
                        <small>Fecha</small>
                        <strong><?= date('d/m/Y H:i', strtotime($deposit->fecha)) ?></strong>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="resumen-box">
                        <small>Total paquetes</small>
                        <strong><?= $deposit->cantidad_paquetes ?></strong>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="resumen-box">
                        <small>Flete total</small>
                        <strong>$<?= number_format($deposit->flete_total, 2) ?></strong>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="resumen-box">
                        <small>Usuario</small>
                        <strong><?= session('user_name') ?? '—' ?></strong>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <!-- 📦 LISTADO -->
    <div class="card shadow-sm">
        <div class="card-header">
            <h6 class="mb-0">Paquetes asignados</h6>
        </div>

        <div class="card-body">

            <?php if (empty($detalles)): ?>
                <div class="text-center text-muted">
                    No hay paquetes
                </div>
            <?php endif; ?>

            <?php foreach ($detalles as $d): ?>

                <?php
                    $estadoClass = $d->nuevo_estado === 'en_casillero'
                        ? 'estado-casillero'
                        : 'estado-ruta';
                ?>

                <div class="paquete-card">

                    <!-- IZQUIERDA -->
                    <div>
                        <div class="fw-bold">
                            #<?= $d->package_id ?> - <?= esc($d->cliente_nombre) ?>
                        </div>

                        <div class="text-muted small">
                            <?= esc($d->destino) ?>
                        </div>

                        <span class="estado-badge <?= $estadoClass ?>">
                            <?= $d->nuevo_estado === 'en_casillero' ? 'Casillero' : 'En ruta' ?>
                        </span>
                    </div>

                    <!-- DERECHA -->
                    <div class="text-end">

                        <div>
                            <small class="text-muted">Valor</small><br>
                            <strong>$<?= number_format($d->valor_paquete, 2) ?></strong>
                        </div>

                        <div class="mt-1">
                            <small class="text-muted">Flete</small><br>
                            <strong>$<?= number_format($d->flete_asignado, 2) ?></strong>
                        </div>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>
    </div>

</div>

<?= $this->endSection() ?>