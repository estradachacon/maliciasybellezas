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

    .log-box {
        max-height: 220px;
        overflow-y: auto;
        font-size: 12px;
        background: #0f172a;
        color: #e2e8f0;
        padding: 10px;
        border-radius: 10px;
        font-family: monospace;
    }

    .estado-box {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        border: 1px solid transparent;
    }

    /* variantes */
    .estado-depositado {
        background: #e0f2fe;
        color: #0369a1;
    }

    .estado-en_ruta {
        background: #dcfce7;
        color: #166534;
    }

    .estado-pendiente {
        background: #fef9c3;
        color: #854d0e;
    }

    .estado-cancelado {
        background: #fee2e2;
        color: #991b1b;
    }

    .estado-default {
        background: #e5e7eb;
        color: #374151;
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

            <div class="card-header">

                <div class="d-flex justify-content-between flex-wrap">

                    <!-- IZQUIERDA -->
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
                    <div class="mt-3 mt-md-0 d-flex justify-content-end">

                        <div class="d-flex" style="gap:10px;">
                            <!-- 🔄 BOX ACTUALIZAR -->
                            <?php if (
                                !empty($tieneAsignacion) &&
                                $paquete->estado1 !== 'entregado' &&
                                tienePermiso('actualizar_estado_paquete_en_detalle')
                            ): ?>
                                <div class="border rounded px-3 py-2 bg-light" style="min-width:220px;">

                                    <small class="text-muted d-block mb-2">Actualizar</small>

                                    <form action="<?= base_url('paquetes/actualizar-estado') ?>" method="post">
                                        <input type="hidden" name="paquete_id" value="<?= $paquete->id ?>">

                                        <select name="nuevo_estado" class="form-control form-control-sm mb-2" required>
                                            <option value="">Seleccionar</option>
                                            <option value="reenvio">Reenvío</option>
                                            <option value="entregado">Entregado</option>
                                            <option value="no_retirado">No retirado</option>
                                        </select>

                                        <button type="submit" class="btn btn-primary btn-sm w-100">
                                            Actualizar
                                        </button>
                                    </form>

                                </div>
                            <?php endif; ?>
                            <!-- 📦 BOX ESTADOS -->
                            <div class="border rounded px-3 py-2 bg-light" style="min-width:220px;">

                                <?php if (!empty($paquete->estado1)): ?>
                                    <div class="d-flex align-items-center mb-1">
                                        <small class="text-muted">Estado</small>

                                        <span class="badge ml-auto px-2 py-1 text-white"
                                            style="background: #0dcaf0;">
                                            <?= esc($paquete->estado1) ?>
                                        </span>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($paquete->estado2)): ?>
                                    <div class="d-flex align-items-center">
                                        <small class="text-muted">Ubicación</small>

                                        <span class="badge ml-auto px-2 py-1 text-white"
                                            style="background: #198754;">
                                            <?= esc($paquete->estado2) ?>
                                        </span>
                                    </div>
                                <?php endif; ?>

                            </div>

                        </div>

                    </div>

                </div>

                <div class="card-body">

                    <div class="row">

                        <!-- 🔵 COLUMNA IZQUIERDA -->
                        <div class="col-md-8">

                            <!-- CLIENTE -->
                            <div class="card-section mb-3">
                                <div class="row">

                                    <!-- IZQUIERDA -->
                                    <div class="col-md-6">
                                        <div class="info-label">Cliente</div>
                                        <div class="info-value">
                                            <?= esc($paquete->cliente_nombre) ?>
                                        </div>

                                        <div class="info-label mt-1">Teléfono</div>
                                        <div class="info-sub">
                                            <?= esc($paquete->cliente_telefono) ?>
                                        </div>
                                    </div>

                                    <!-- DERECHA -->
                                    <div class="col-md-6">
                                        <div class="info-label">Tipo de venta</div>
                                        <div class="info-sub">
                                            <?= ucfirst($paquete->tipo_venta ?? 'detalle') ?>
                                        </div>
                                    </div>

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

                                        <div class="info-label mt-1">Horario</div>
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

                                        <div class="info-label mt-1">Encomendista</div>
                                        <div class="info-sub">
                                            <?= esc($paquete->encomendista_nombre ?: '—') ?>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <!-- 🧾 LOG DEL PAQUETE -->
                            <?php if (!empty($paquete->packlog)): ?>
                                <div class="card-section mb-1">

                                    <div class="info-label mb-1">Historial del paquete</div>

                                    <div style="
                                                    max-height:200px;
                                                    overflow-y:auto;
                                                    font-size:12px;
                                                    background:#f8f9fa;
                                                    padding:10px;
                                                    border-radius:8px;
                                                ">
                                        <?= nl2br(esc($paquete->packlog)) ?>
                                    </div>

                                </div>
                            <?php endif; ?>
                            <?php
                            function badgeColor($estado)
                            {
                                return match ($estado) {
                                    'pagado' => 'success text-white',
                                    'depositado' => 'info',
                                    'pendiente' => 'warning',
                                    'cancelado' => 'danger text-white',
                                    'entregado' => 'primary',
                                    'en_ruta' => 'success text-white',
                                    default => 'secondary'
                                };
                            }
                            function estadoClass($estado)
                            {
                                return match ($estado) {
                                    'depositado' => 'estado-depositado',
                                    'en_ruta' => 'estado-en_ruta',
                                    'pendiente' => 'estado-pendiente',
                                    'cancelado' => 'estado-cancelado',
                                    default => 'estado-default'
                                };
                            }
                            ?>

                        </div>

                        <!-- COLUMNA DERECHA (FOTO) -->
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