<?= $this->extend('Layouts/mainbody') ?>
<?= $this->section('content') ?>
<style>
    .info-label {
        font-size: 12px;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
    }

    .info-value {
        font-size: 18px;
        font-weight: 700;
        color: #212529;
    }

    .info-sub {
        font-size: 14px;
        color: #495057;
    }

    .card-section {
        padding: 16px;
        border-radius: 12px;
        border: 1px solid #e9ecef;
        background: #fff;
        transition: 0.2s;
    }

    .card-section:hover {
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    }

    .total-box {
        background: #f8f9fa;
        border-radius: 12px;
    }

    .total-value {
        font-size: 26px;
        font-weight: 800;
        color: #198754;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm">

            <!-- HEADER -->
            <div class="card-header d-flex">

                <div>
                    <h4 class="mb-0">
                        Paquete
                        <span class="badge bg-primary ms-2 text-white">
                            #<?= $paquete->id ?>
                        </span>
                    </h4>

                    <small class="text-muted">
                        Registro de envío
                    </small>
                </div>

            </div>

            <div class="card-body">

                <div class="row">

                    <!-- 🔵 COLUMNA IZQUIERDA -->
                    <div class="col-md-8">

                        <!-- CLIENTE -->
                        <div class="card-section mb-3">
                            <div class="info-label">Cliente</div>
                            <div class="info-value">
                                <?= esc($paquete->cliente_nombre) ?>
                            </div>

                            <div class="info-label mt-2">Teléfono</div>
                            <div class="info-sub">
                                <?= esc($paquete->cliente_telefono) ?>
                            </div>

                            <div class="info-label mt-2">Tipo de venta</div>
                            <div class="info-sub">
                                <?= ucfirst($paquete->tipo_venta ?? 'detalle') ?>
                            </div>
                        </div>

                        <div class="card-section mb-3">
                            <div class="row">

                                <!-- 📅 COLUMNA IZQUIERDA -->
                                <div class="col-md-6">
                                    <div class="info-label">Fecha de entrega</div>
                                    <div class="info-value">
                                        <?= date('d/m/Y', strtotime($paquete->dia_entrega)) ?>
                                    </div>

                                    <div class="info-label mt-2">Horario</div>
                                    <div class="info-sub">
                                        <?= $paquete->hora_inicio ?> - <?= $paquete->hora_fin ?>
                                    </div>
                                </div>

                                <!-- 📍 COLUMNA DERECHA -->
                                <div class="col-md-6">
                                    <div class="info-label">Destino</div>
                                    <div class="info-value">
                                        <?= esc($paquete->destino) ?>
                                    </div>

                                    <div class="info-label mt-2">Encomendista</div>
                                    <div class="info-sub">
                                        <?= esc($paquete->encomendista_nombre ?: '—') ?>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <?php
                        function badgeColor($estado)
                        {
                            return match ($estado) {
                                'pagado' => 'success',
                                'pendiente' => 'warning',
                                'cancelado' => 'danger',
                                'entregado' => 'primary',
                                'en_ruta' => 'info',
                                default => 'secondary'
                            };
                        }
                        ?>

                        <!-- ESTADOS -->
                        <?php if (!empty($paquete->estado1) || !empty($paquete->estado2)): ?>
                            <div class="card-section mb-3">

                                <?php if (!empty($paquete->estado1)): ?>
                                    <div class="info-label">Estado de Paquete</div>
                                    <div>
                                        <span class="badge bg-<?= badgeColor($paquete->estado1) ?>">
                                            <?= esc($paquete->estado1) ?>
                                        </span>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($paquete->estado2)): ?>
                                    <div class="info-label mt-2">Estado secundario</div>
                                    <div>
                                        <span class="badge bg-<?= badgeColor($paquete->estado2) ?>">
                                            <?= esc($paquete->estado2) ?>
                                        </span>
                                    </div>
                                <?php endif; ?>

                            </div>
                        <?php endif; ?>

                    </div>

                    <!-- 🟡 COLUMNA DERECHA (FOTO) -->
                    <div class="col-md-4">
                        <div class="p-3 border rounded text-center h-100" style="position: sticky; top:20px; border-radius:12px;">

                            <div class="info-label mb-2">Foto del paquete</div>

                            <?php if (!empty($paquete->foto)): ?>
                                <img src="<?= base_url('upload/paquetes/' . $paquete->foto) ?>"
                                    class="img-fluid rounded shadow-sm"
                                    style="max-height:350px; cursor:pointer;"
                                    onclick="verImagen(this.src)">
                            <?php else: ?>
                                <div class="text-muted">
                                    Sin imagen
                                </div>
                            <?php endif; ?>

                        </div>

                    </div>

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
    function verImagen(src) {
        $('#imagenGrande').attr('src', src);
        new bootstrap.Modal(document.getElementById('modalImagen')).show();
    }
</script>

<?= $this->endSection() ?>