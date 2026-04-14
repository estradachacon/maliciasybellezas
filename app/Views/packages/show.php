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

    @media (max-width: 768px) {
        .card-body {
            padding-left: 0px !important;
            padding-right: 0px !important;
        }

        .card-header .d-flex>div {
            margin-bottom: 8px;
        }
    }

    @media (max-width: 768px) {
        .card-header {
            padding-left: 12px !important;
            padding-right: 12px !important;
        }
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

    .producto-titulo {
        font-weight: 600;
        padding-bottom: 4px;
        border-bottom: 1px solid #e9ecef;
        margin-bottom: 6px;
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

    @media (max-width: 768px) {
        .card-section {
            padding: 12px;
        }
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
<?php
$subtotal = 0;
$cantidadTotal = 0;

foreach ($detalles as $d) {
    $subtotal += $d->subtotal;
    $cantidadTotal += $d->cantidad;
}
?>
<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm">

            <div class="card-header">

                <div class="d-flex flex-column flex-md-row align-items-start justify-content-md-between">

                    <!-- IZQUIERDA -->
                    <div class="d-flex flex-column">
                        <h4 class="mb-0">
                            Paquete
                            <span class="badge bg-primary text-white">
                                #<?= $paquete->id ?>
                            </span>
                        </h4>

                        <small class="text-muted">
                            Registro de envío
                        </small>
                    </div>
                    <div class="mt-2 mt-md-0 d-flex flex-wrap justify-content-start justify-content-md-end" style="gap:10px;">

                        <div class="d-flex flex-wrap w-100" style="gap:10px;">
                            <!-- BOX ACTUALIZAR -->
                            <?php
                            $estado1 = trim(strtolower($paquete->estado1 ?? ''));
                            ?>

                            <?php if (
                                !in_array($estado1, ['finalizado', 'pendiente']) &&
                                tienePermiso('actualizar_estado_paquete_en_detalle')
                            ): ?>
                                <div class="border rounded px-3 py-2 bg-light flex-grow-1" style="min-width:250px;">

                                    <small class="text-muted d-block mb-2">Actualizar estado</small>

                                    <select id="selectNuevoEstado" class="form-control form-control-sm mb-2">
                                        <option value="">Seleccionar</option>
                                        <?php if ($paquete->estado2 === 'no_retirado'): ?>
                                            <option value="reenvio">Reenvio</option>
                                        <?php endif; ?>
                                        <option value="entregado">Entregado</option>
                                        <?php if ($paquete->estado2 != 'no_retirado'): ?>
                                            <option value="no_retirado">No retirado</option>
                                        <?php endif; ?>
                                        <?php if ($paquete->estado2 === 'no_retirado'): ?>
                                            <option value="devuelto">Devuelto</option>
                                        <?php endif; ?>
                                    </select>

                                    <button type="button" class="btn btn-primary btn-sm w-100"
                                        onclick="procesarEstado()">
                                        Actualizar
                                    </button>

                                </div>
                            <?php endif; ?>
                            <!-- 📦 BOX ESTADOS -->
                            <div class="border rounded px-3 py-2 bg-light flex-grow-1" style="min-width: 300px;">
                                <?php
                                function estadoTexto($estado)
                                {
                                    return match ($estado) {
                                        'depositado' => 'En encomendista',
                                        'en_ruta'    => 'En ruta',
                                        default      => ucfirst(str_replace('_', ' ', $estado))
                                    };
                                }
                                ?>
                                <?php if (!empty($paquete->estado1)): ?>
                                    <div class="d-flex align-items-center mb-1 mt-1">
                                        <small class="text-muted">Estado</small>

                                        <span class="badge ml-auto px-2 py-1 text-white"
                                            style="background: #0dcaf0;">
                                            <?= esc(estadoTexto($paquete->estado1)) ?>
                                        </span>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($paquete->estado2)): ?>
                                    <?php
                                    $bgColor = ($paquete->estado2 === 'no_retirado') ? '#dc3545' : '#198754';
                                    ?>

                                    <div class="d-flex align-items-center mb-1">
                                        <small class="text-muted mt-1">Ubicación</small>

                                        <span class="badge ml-auto px-2 py-1 text-white"
                                            style="background: <?= $bgColor ?>;">
                                            <?= esc(estadoTexto($paquete->estado2)) ?>
                                        </span>
                                    </div>
                                <?php endif; ?>
                                <div class="d-flex align-items-center mb-1">
                                    <small class="text-muted">Vendedor</small>

                                    <span class="badge ml-auto px-2 py-1 text-white"
                                        style="background: #0dcaf0;">
                                        <?= esc(estadoTexto($paquete->vendedor_nombre)) ?>
                                    </span>
                                </div>
                                <?php if ($paquete->reenvios > 0): ?>
                                    <div class="d-flex align-items-center mb-1">
                                        <small class="text-muted">Reenvíos</small>

                                        <span class="badge ml-auto px-2 py-1 text-white"
                                            style="background: #0dcaf0;">
                                            <?= esc($paquete->reenvios) ?>
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
                        <div class="col-12 col-md-12 col-lg-9">

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
                                        <?php
                                        $query = http_build_query([
                                            'codigoqr' => $paquete->codigoqr,
                                            'cliente_nombre' => $paquete->cliente_nombre,
                                            'cliente_telefono' => $paquete->cliente_telefono,
                                            'dia_entrega' => $paquete->dia_entrega,
                                            'hora_inicio' => $paquete->hora_inicio,
                                            'hora_fin' => $paquete->hora_fin,
                                            'destino' => $paquete->destino,
                                            'encomendista_id' => $paquete->encomendista_nombre,
                                            'total' => $paquete->total,
                                            'total_real' => $paquete->total_real,
                                            'encomendista_nombre' => $paquete->encomendista_nombre,
                                        ]);
                                        ?>

                                        <a href="<?= base_url('paquetes/etiqueta?' . $query) ?>"
                                            target="_blank"
                                            class="btn btn-dark btn-sm">
                                            🏷️ Viñeta
                                        </a>
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

                            <div class="card-section mb-3">

                                <div class="info-label mb-2">Detalle del paquete</div>

                                <!-- 💻 DESKTOP (TABLA) -->
                                <div class="d-none d-md-block">
                                    <table class="table table-sm table-borderless">
                                        <thead style="font-size:12px;">
                                            <tr>
                                                <th>#</th>
                                                <th>Producto</th>
                                                <th class="text-center">Cant</th>
                                                <th class="text-end">Precio</th>
                                                <th class="text-end">Desc</th>
                                                <th class="text-end">Sub</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($detalles as $i => $d): ?>

                                                <tr>
                                                    <td><?= $i + 1 ?></td>
                                                    <td><?= esc($d->producto_nombre) ?></td>
                                                    <td class="text-center"><?= $d->cantidad ?></td>
                                                    <td class="text-end">$<?= number_format($d->precio, 2) ?></td>
                                                    <td class="text-end text-danger">$<?= number_format($d->descuento, 2) ?></td>
                                                    <td class="text-end fw-bold">$<?= number_format($d->subtotal, 2) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- 📱 MOBILE (CARDS) -->
                                <div class="d-block d-md-none">

                                    <?php foreach ($detalles as $i => $d): ?>

                                        <div class="border rounded mb-2 p-2 shadow-sm">

                                            <!-- HEADER -->
                                            <div class="d-flex justify-content-between">

                                                <span class="badge badge-primary">
                                                    #<?= $i + 1 ?>
                                                </span>

                                                <span class="fw-bold text-success">
                                                    $<?= number_format($d->subtotal, 2) ?>
                                                </span>

                                            </div>

                                            <!-- NOMBRE -->
                                            <div class="producto-titulo">
                                                <?= esc($d->producto_nombre) ?>
                                            </div>

                                            <!-- DETALLE -->
                                            <div class="d-flex justify-content-between mt-1 text-center">

                                                <div>
                                                    <small class="text-muted">Cant</small><br>
                                                    <?= $d->cantidad ?>
                                                </div>

                                                <div>
                                                    <small class="text-muted">Precio</small><br>
                                                    $<?= number_format($d->precio, 2) ?>
                                                </div>

                                                <div>
                                                    <small class="text-muted text-danger">Desc</small><br>
                                                    $<?= number_format($d->descuento, 2) ?>
                                                </div>

                                            </div>

                                        </div>

                                    <?php endforeach; ?>

                                </div>

                                <!-- 💰 RESUMEN -->
                                <?php
                                $descuentoGlobal = (float)($paquete->descuento_global ?? 0);
                                $envio = (float)($paquete->envio ?? 0);
                                $totalReal = (float)($paquete->total_real ?? 0);
                                $totalRemunerar = (float)($paquete->total ?? 0);
                                ?>

                                <div class="border-top pt-2 mt-2">

                                    <div class="d-flex justify-content-between">
                                        <span>Items</span>
                                        <span><?= $cantidadTotal ?></span>
                                    </div>

                                    <div class="d-flex justify-content-between">
                                        <span>Subtotal</span>
                                        <span>$<?= number_format($subtotal, 2) ?></span>
                                    </div>

                                    <div class="d-flex justify-content-between text-danger">
                                        <span>Descuento</span>
                                        <span>- $<?= number_format($descuentoGlobal, 2) ?></span>
                                    </div>

                                    <div class="d-flex justify-content-between">
                                        <span>Envío cobrado</span>
                                        <span>$<?= number_format($envio, 2) ?></span>
                                    </div>

                                    <hr>

                                    <div class="d-flex justify-content-between fw-bold">
                                        <span>Total real</span>
                                        <span>$<?= number_format($totalReal, 2) ?></span>
                                    </div>

                                    <div class="d-flex justify-content-between mt-1">

                                        <span class="fw-bold text-success">Por cobrar</span>

                                        <?php if ($totalRemunerar > 0): ?>
                                            <span class="fw-bold text-success">
                                                $<?= number_format($totalRemunerar, 2) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge badge-success">
                                                ✔ Pagado
                                            </span>
                                        <?php endif; ?>

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
                        <div class="col-12 col-md-12 col-lg-3">
                            <div class="p-3 border rounded text-center h-100" style="position: sticky; top:20px; border-radius:12px;">

                                <div class="info-label mb-2">Foto del paquete</div>

                                <?php if (!empty($paquete->foto)): ?>
                                    <img id="fotoActual"
                                        src="<?= base_url('upload/paquetes/' . $paquete->foto) ?>"
                                        class="img-fluid rounded shadow-sm"
                                        style="max-height:350px; cursor:pointer;"
                                        onclick="verImagen(this.src)">

                                    <button
                                        class="btn btn-sm btn-outline-primary mt-2 w-100"
                                        onclick="compartirImagen('<?= base_url('upload/paquetes/' . $paquete->foto) ?>')">
                                        📤 Compartir imagen
                                    </button>
                                <?php else: ?>
                                    <div id="sinFotoMsg" class="text-muted mb-2">Sin imagen</div>
                                    <img id="fotoActual" src="" class="img-fluid rounded shadow-sm mb-2"
                                        style="max-height:350px; cursor:pointer; display:none;"
                                        onclick="verImagen(this.src)">
                                <?php endif; ?>

                                <?php if (tienePermiso('actualizar_foto_paquete')): ?>
                                    <!-- Botón trigger -->
                                    <button class="btn btn-sm btn-outline-secondary mt-2 w-100"
                                        onclick="document.getElementById('inputNuevaFoto').click()">
                                        📷 Cambiar foto
                                    </button>

                                    <!-- Input oculto -->
                                    <input type="file" id="inputNuevaFoto" accept="image/*"
                                        style="display:none;"
                                        onchange="subirNuevaFoto(this)">
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
    <div class="modal fade" id="modalReenvio" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fa fa-redo mr-2 text-primary"></i> Reenvío — Actualizar datos
                    </h5>
                </div>

                <div class="modal-body">

                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label class="info-label">Cliente</label>
                            <input type="text" id="r_cliente_nombre"
                                class="form-control form-control-sm"
                                value="<?= esc($paquete->cliente_nombre) ?>">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="info-label">Teléfono</label>
                            <input type="text" id="r_cliente_telefono"
                                class="form-control form-control-sm"
                                value="<?= esc($paquete->cliente_telefono) ?>">
                        </div>
                        <div class="col-md-12 mb-2">
                            <label class="info-label">Destino</label>
                            <input type="text" id="r_destino"
                                class="form-control form-control-sm"
                                value="<?= esc($paquete->destino) ?>">
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="info-label">Fecha entrega</label>
                            <input type="date" id="r_dia_entrega"
                                class="form-control form-control-sm"
                                value="<?= $paquete->dia_entrega ?>">
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="info-label">Hora inicio</label>
                            <input type="time" id="r_hora_inicio"
                                class="form-control form-control-sm"
                                value="<?= $paquete->hora_inicio ?>">
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="info-label">Hora fin</label>
                            <input type="time" id="r_hora_fin"
                                class="form-control form-control-sm"
                                value="<?= $paquete->hora_fin ?>">
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-success btn-sm"
                        onclick="confirmarReenvio(true)">
                        <i class="fa fa-check mr-1"></i> Guardar y reenviar
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm"
                        data-dismiss="modal">
                        Cancelar
                    </button>
                </div>

            </div>
        </div>
    </div>
    <script>
        function subirNuevaFoto(input) {
            const file = input.files[0];
            if (!file) return;

            // Preview inmediato
            const reader = new FileReader();
            reader.onload = e => {
                const img = document.getElementById('fotoActual');
                img.src = e.target.result;
                img.style.display = 'block';

                const sinFoto = document.getElementById('sinFotoMsg');
                if (sinFoto) sinFoto.style.display = 'none';
            };
            reader.readAsDataURL(file);

            // Confirmar antes de subir
            Swal.fire({
                title: '¿Actualizar foto?',
                text: 'La foto anterior se mantendrá en el servidor',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, subir',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#0d6efd',
                cancelButtonColor: '#6c757d',
            }).then(result => {
                if (!result.isConfirmed) {
                    // Revertir preview si cancela
                    input.value = '';
                    return;
                }

                const formData = new FormData();
                formData.append('paquete_id', PAQUETE_ID);
                formData.append('foto', file);

                Swal.fire({
                    title: 'Subiendo foto...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                fetch('<?= base_url('paquetes/actualizar-foto') ?>', {
                        method: 'POST',
                        body: formData
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.status === 'ok') {
                            // Actualizar src con la URL real del servidor
                            document.getElementById('fotoActual').src = data.nueva_foto;

                            Swal.fire({
                                icon: 'success',
                                title: 'Foto actualizada',
                                timer: 1500,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                html: '<pre style="text-align:left;font-size:12px;">' +
                                    JSON.stringify(data, null, 2) +
                                    '</pre>'
                            });
                        }
                        input.value = '';
                    })
                    .catch(err => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error de conexión',
                            text: err.message
                        });
                        input.value = '';
                    });
            });
        }
        const PAQUETE_ID = <?= $paquete->id ?>;

        function procesarEstado() {
            const estado = document.getElementById('selectNuevoEstado').value;

            if (!estado) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Selecciona un estado',
                    timer: 1500,
                    showConfirmButton: false
                });
                return;
            }

            // Si NO es reenvío → flujo normal
            if (estado !== 'reenvio') {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '<?= base_url('paquetes/actualizar-estado') ?>';

                [
                    ['paquete_id', PAQUETE_ID],
                    ['nuevo_estado', estado]
                ].forEach(([name, val]) => {
                    const i = document.createElement('input');
                    i.type = 'hidden';
                    i.name = name;
                    i.value = val;
                    form.appendChild(i);
                });

                document.body.appendChild(form);
                form.submit();
                return;
            }

            // Es reenvío → preguntar
            Swal.fire({
                title: 'Reenvío #<?= (int)$paquete->reenvios + 1 ?>',
                text: '¿Deseas actualizar los datos del paquete o mantener los actuales?',
                icon: 'question',
                showDenyButton: true,
                showCancelButton: true,
                confirmButtonText: '✏️ Actualizar datos',
                denyButtonText: '📦 Mantener datos',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#0d6efd',
                denyButtonColor: '#6c757d',
            }).then(result => {
                if (result.isConfirmed) {
                    $('#modalReenvio').modal('show');
                } else if (result.isDenied) {
                    confirmarReenvio(false);
                }
            });
        }

        function confirmarReenvio(conCambios) {

            if (!conCambios) {
                // ── Mismo flujo que los demás estados → form POST ──
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '<?= base_url('paquetes/actualizar-estado') ?>';

                [
                    ['paquete_id', PAQUETE_ID],
                    ['nuevo_estado', 'reenvio']
                ].forEach(([name, val]) => {
                    const i = document.createElement('input');
                    i.type = 'hidden';
                    i.name = name;
                    i.value = val;
                    form.appendChild(i);
                });

                document.body.appendChild(form);
                form.submit();
                return;
            }

            // ── Con cambios → validar y fetch ──────────────────────
            const payload = {
                paquete_id: PAQUETE_ID,
                cliente_nombre: document.getElementById('r_cliente_nombre').value.trim(),
                cliente_telefono: document.getElementById('r_cliente_telefono').value.trim(),
                destino: document.getElementById('r_destino').value.trim(),
                dia_entrega: document.getElementById('r_dia_entrega').value,
                hora_inicio: document.getElementById('r_hora_inicio').value,
                hora_fin: document.getElementById('r_hora_fin').value,
            };

            if (!payload.cliente_nombre || !payload.destino || !payload.dia_entrega) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Campos requeridos',
                    text: 'Cliente, destino y fecha son obligatorios',
                    timer: 2000,
                    showConfirmButton: false
                });
                return;
            }

            $('#modalReenvio').modal('hide');

            Swal.fire({
                title: 'Procesando reenvío...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            fetch('<?= base_url('paquetes/reenvio-con-cambios') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(payload)
                })
                .then(r => r.json())
                .then(data => {
                    if (data.status === 'ok') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Reenvío registrado',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => location.reload());
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.msg || 'No se pudo procesar'
                        });
                    }
                })
                .catch(() => Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión'
                }));
        }

        function verImagen(src) {
            $('#imagenGrande').attr('src', src);
            new bootstrap.Modal(document.getElementById('modalImagen')).show();
        }
        async function compartirImagen(url) {
            try {
                // 🔥 Intentar usar Web Share API (Android / móviles)
                if (navigator.share) {

                    // Convertir imagen a archivo
                    const response = await fetch(url);
                    const blob = await response.blob();
                    const file = new File([blob], "paquete.jpg", {
                        type: blob.type
                    });

                    await navigator.share({
                        title: 'Paquete',
                        text: 'Paquete compartido desde Malicias y Bellezas',
                        files: [file]
                    });

                } else {
                    // 💻 PC → descargar imagen
                    const link = document.createElement('a');
                    link.href = url;
                    link.download = 'paquete.jpg';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                }

            } catch (error) {
                console.error('Error al compartir:', error);

                // fallback descarga
                const link = document.createElement('a');
                link.href = url;
                link.download = 'paquete.jpg';
                link.click();
            }
        }
    </script>

    <?= $this->endSection() ?>