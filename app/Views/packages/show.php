<?= $this->extend('Layouts/mainbody') ?>
<?= $this->section('content') ?>
<style>
    /* Panel de edición de flete */

    .flete-editor {
        text-align: left;
        padding: 10px 5px;
    }

    .flete-row {
        margin-bottom: 12px;
    }

    .flete-label {
        font-weight: 600;
        font-size: 0.9rem;
        color: #555;
        margin-bottom: 3px;
    }

    .flete-input {
        width: 100%;
        padding: 6px 8px;
        border-radius: 6px;
        border: 1px solid #ced4da;
        font-size: 0.95rem;
    }

    .flete-input:focus {
        border-color: #007bff;
        outline: none;
        box-shadow: 0 0 3px rgba(0, 123, 255, 0.4);
    }

    .flete-preview {
        margin-top: 10px;
        padding: 8px;
        border-radius: 6px;
        background: #f8f9fa;
        font-size: 0.95rem;
    }

    .flete-preview strong {
        color: #dc3545;
    }

    .flete-preview span {
        font-weight: bold;
        font-size: 1rem;
    }

    .tracking-btn {
        transition: all .2s ease;
    }

    .tracking-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
    }
</style>
<?php
/**
 * Función auxiliar para formatear la fecha a día/mes/año (d/m/Y).
 * @param string|null $fecha
 * @return string
 */
function formatDateDMY($fecha)
{
    if (empty($fecha))
        return 'Pendiente';
    $d = new DateTime($fecha);
    return $d->format('d/m/Y');
}

// Asegúrate de que las funciones serviceLabel() y statusBadge() 
// estén definidas en tus Helpers o estén disponibles globalmente.
?>

<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Detalle del Paquete #<?= esc($package['id']) ?></h4>
            </div>

            <div class="card-body">

                <h5 class="text-secondary mb-3 border-bottom pb-1">Información General</h5>

                <div class="row">

                    <div class="col-md-8">
                        <table class="table table-striped" style="font-size: 1rem;">
                            <tbody>
                                <tr>
                                    <th style="width: 40%">Cliente</th>
                                    <td><?= esc($package['cliente']) ?></td>
                                </tr>
                                <tr>
                                    <th>Vendedor</th>
                                    <td><?= esc($package['seller_name'] ?? 'N/A') ?></td>
                                </tr>
                                <tr>
                                    <th>Tipo de Servicio</th>
                                    <td><?= serviceLabel($package['tipo_servicio']) ?></td>
                                </tr>
                                <tr>
                                    <th>Monto</th>
                                    <td><strong>$<?= number_format($package['monto'], 2) ?></strong></td>
                                </tr>

                                <tr>
                                    <th>Estatus</th>
                                    <td>

                                        <div style="display:flex; gap:10px; align-items:center;">

                                            <?php if (!empty($package['estatus'])): ?>
                                                <div style="border-right:1px solid #ddd; padding-right:10px;">
                                                    <?= statusBadge($package['estatus']) ?>
                                                </div>
                                            <?php endif; ?>

                                            <?php if (!empty($package['estatus2'])): ?>
                                                <div>
                                                    <?= statusBadge($package['estatus2']) ?>
                                                </div>
                                            <?php endif; ?>

                                        </div>

                                    </td>
                                </tr>

                                <?php if (!empty($package['fecha_remu'])): ?>
                                    <tr>
                                        <th>Pago al vendedor</th>
                                        <td>

                                            <div class="row text-center">

                                                <div class="col-md-4">
                                                    <small class="text-muted d-block">Fecha</small>
                                                    <?= formatDateDMY($package['fecha_remu']) ?>
                                                </div>

                                                <div class="col-md-4">
                                                    <small class="text-muted d-block">Método</small>

                                                    <?php if ($package['metodo_remu'] == 'caja'): ?>
                                                        <span class="badge badge-success">Caja</span>
                                                    <?php elseif ($package['metodo_remu'] == 'cuenta'): ?>
                                                        <span class="badge badge-primary">Cuenta</span>
                                                    <?php else: ?>
                                                        <?= esc($package['metodo_remu']) ?>
                                                    <?php endif; ?>

                                                </div>

                                                <div class="col-md-4">
                                                    <small class="text-muted d-block">Usuario</small>
                                                    <?= esc($package['remu_user_nombre'] ?? 'N/A') ?>
                                                </div>

                                            </div>

                                        </td>
                                    </tr>
                                <?php endif; ?>
                                <tr>
                                    <th>Cobro de paquete recibido en cuenta:</th>
                                    <td>

                                        <?php if (!empty($package['cliente_pago_directo'])): ?>

                                            <span class="text-muted small">
                                                Valor pagado directamente al vendedor por el cliente, posterior a la recepción.
                                            </span>

                                        <?php elseif (!empty($package['pago_cuenta_nombre'])): ?>

                                            <span class="badge badge-info">
                                                <?= esc($package['pago_cuenta_nombre']) ?>
                                            </span>

                                        <?php else: ?>

                                            <span class="badge badge-light">Pendiente</span>

                                        <?php endif; ?>

                                    </td>
                                </tr>
                                <tr>
                                    <th>¿Es Frágil?</th>
                                    <td>
                                        <?php if (!empty($package['fragil'])): ?>
                                            <span class="badge badge-danger">SÍ</span>
                                        <?php else: ?>
                                            <span class="badge badge-success">NO</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="col-md-4 text-center">
                        <?php
                        $fotoFile = isset($package['foto']) ? $package['foto'] : null;
                        $fotoURL = (!empty($fotoFile) && $fotoFile !== 'null')
                            ? base_url('upload/paquetes/' . $fotoFile)
                            : base_url('upload/no-image.png');
                        ?>
                        <img src="<?= esc($fotoURL) ?>" alt="Foto del Paquete"
                            class="img-thumbnail shadow-sm rounded-lg"
                            style="width: 100%; max-width: 250px; height: 250px; object-fit: cover; cursor: pointer;"
                            data-toggle="modal" data-target="#modalFotoPaquete">

                        <p class="text-muted mt-2 small">Click para ver en grande</p>
                    </div>

                </div>

                <hr class="my-4">

                <h5 class="text-secondary mb-3 border-bottom pb-1">Flete y Pago</h5>
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless" style="font-size: 1rem;">
                            <tbody>
                                <tr>
                                    <th>Flete Total</th>
                                    <td>
                                        <span id="fleteTotalEdit"
                                            class="editable-money"
                                            data-total="<?= $package['flete_total'] ?>"
                                            data-pagado="<?= $package['flete_pagado'] ?>"
                                            data-toggle="<?= $package['toggle_pago_parcial'] ?>"
                                            data-id="<?= $package['id'] ?>">

                                            <strong>$<?= number_format($package['flete_total'], 2) ?></strong>

                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Pago Parcial Activo</th>
                                    <td id="badgePagoParcial">
                                        <?= !empty($package['toggle_pago_parcial'])
                                            ? '<span class="badge badge-info">SÍ</span>'
                                            : '<span class="badge badge-light">NO</span>' ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless" style="font-size: 1rem;">
                            <tbody>
                                <tr>
                                    <th>Flete Pagado</th>
                                    <td class="text-success">
                                        <span class="editable-money"
                                            data-field="flete_pagado"
                                            data-id="<?= $package['id'] ?>">
                                            $<?= number_format($package['flete_pagado'], 2) ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Flete Pendiente</th>
                                    <td class="text-danger">
                                        <strong id="fletePendiente">
                                            $<?= number_format($package['flete_pendiente'], 2) ?>
                                        </strong>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-12">
                        <table class="table table-sm table-borderless" style="font-size: 1rem;">
                            <tbody>
                                <tr>
                                    <th>Reenvios realizados</th>
                                    <td class="text-success">
                                        <strong><?= number_format($package['reenvios']) ?></strong>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <hr class="my-4">

                <h5 class="text-secondary mb-3 border-bottom pb-1">Destino y Logística</h5>
                <table class="table table-sm table-bordered" style="font-size: 1rem;">
                    <tbody>
                        <tr>
                            <th style="width: 40%">Fecha de Ingreso</th>
                            <td><?= formatDateDMY($package['fecha_ingreso']) ?></td>
                        </tr>

                        <?php if ($package['tipo_servicio'] == 1): // Punto Fijo 
                        ?>
                            <tr>
                                <th>Punto Fijo</th>
                                <td><?= esc($package['point_name'] ?? 'N/A') ?></td>
                            </tr>
                            <tr>
                                <th>Fecha Estimada de Entrega (Punto Fijo)</th>
                                <td><?= formatDateDMY($package['fecha_entrega_puntofijo'] ?? null) ?></td>
                            </tr>

                        <?php elseif ($package['tipo_servicio'] == 2): // Destino Personalizado 
                        ?>
                            <tr>
                                <th>Destino Personalizado</th>
                                <td><?= esc($package['destino_personalizado'] ?? 'N/A') ?></td>
                            </tr>
                            <tr>
                                <th>Fecha Estimada de Entrega (Personalizado)</th>
                                <td><?= formatDateDMY($package['fecha_entrega_personalizado'] ?? null) ?></td>
                            </tr>
                            <tr>
                                <th>Ubicación</th>
                                <td class="text-muted">
                                    <?= esc($package['ubicacion_completa'] ?? 'Ubicación no definida') ?>
                                </td>
                            </tr>


                        <?php elseif ($package['tipo_servicio'] == 3): // Recolección y Entrega Final 
                        ?>
                            <tr>
                                <th>Lugar de Recolección</th>
                                <td><?= esc($package['lugar_recolecta_paquete'] ?? 'N/A') ?></td>
                            </tr>
                            <tr>
                                <th>Destino Final</th>
                                <td><?= esc($package['destino_entrega_final'] ?? 'Pendiente') ?></td>
                            </tr>
                            <tr>
                                <th>Fecha de Entrega (Personalizado)</th>
                                <td><?= esc($package['fecha_entrega_personalizado'] ?? 'Pendiente') ?></td>
                            </tr>
                        <?php elseif ($package['tipo_servicio'] == 4): // Casillero 
                        ?>
                            <tr>
                                <th>Número de Casillero</th>
                                <td><?= esc($package['numero_casillero'] ?? 'N/A') ?></td>
                            </tr>
                            <tr>
                                <th>Destino</th>
                                <td><?= esc($package['destino_personalizado'] ?? 'N/A') ?></td>
                            </tr>
                        <?php endif; ?>
                        <?php if (!empty($package['external_location_nombre'])): ?>
                            <tr>
                                <th>Casillero Externo</th>
                                <td>
                                    <span class="badge badge-info">
                                        <?= esc($package['external_location_nombre']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <?php if (!empty($trackingHistory)): ?>

                    <hr class="my-4">

                    <h5 class="text-secondary mb-3 border-bottom pb-1">
                        Historial de Seguimiento
                    </h5>

                    <table class="table table-sm table-bordered">

                        <thead>
                            <tr>
                                <th>Tracking</th>
                                <th>Fecha</th>
                                <th>Motorista</th>
                                <th>Estado</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php foreach ($trackingHistory as $t): ?>

                                <tr>

                                    <td>
                                        <a href="<?= base_url('tracking/' . $t->tracking_id) ?>"
                                            class="btn btn-sm btn-primary"
                                            title="Ver seguimiento completo">
                                            <i class="fa-solid fa-route"></i> #<?= $t->tracking_id ?>
                                        </a>
                                    </td>

                                    <td><?= date('d/m/Y', strtotime($t->tracking_date)) ?></td>

                                    <td><?= esc($t->motorista) ?></td>

                                    <td><?= trackingBadge($t->tracking_status) ?></td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>
                    </table>

                <?php endif; ?>
                <hr class="my-4">

                <h5 class="text-secondary mb-3 border-bottom pb-1">Comentarios y Tiempos</h5>
                <table class="table table-sm table-bordered" style="font-size: 1rem;">
                    <tbody>
                        <tr>
                            <th style="width: 40%">Comentarios / Nota</th>
                            <td><?= !empty($package['comentarios']) ? esc($package['comentarios']) : 'Sin comentarios' ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Paquete cancelado previamente:</th>
                            <td><?= !empty($package['nocobrar_pack_cancelado']) ? '<span class="badge badge-warning">SÍ</span>' : '<span class="badge badge-light">NO</span>' ?>
                            </td>
                        </tr>
                        <?php if (!empty($package['cliente_pago_directo'])): ?>
                            <tr>
                                <th>Pago directo al vendedor</th>
                                <td>
                                    <span class="badge badge-success">Cliente pagó directamente</span>

                                    <?php if (!empty($package['fecha_cliente_pago'])): ?>
                                        <div class="small text-muted mt-1">
                                            Registrado el <?= formatDateDMY($package['fecha_cliente_pago']) ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($package['motivo_no_cobro'])): ?>
                                        <div class="small text-muted">
                                            <?= esc($package['motivo_no_cobro']) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endif; ?>

                        </td>
                        </tr>
                        <tr>
                            <th>Creado</th>
                            <td><?= formatDateDMY($package['created_at']) ?></td>
                        </tr>
                        <tr>
                            <th>Última Actualización</th>
                            <td><?= formatDateDMY($package['updated_at']) ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>
</div>
<div class="modal fade" id="modalFotoPaquete" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-body p-0 text-center">
                <img src="<?= esc($fotoURL) ?>" alt="Foto ampliada" style="width: 100%; border-radius: 4px;">
            </div>

        </div>
    </div>
</div>
<script>
    document.getElementById('togglePagoParcial').addEventListener('change', function() {

        const id = this.dataset.id
        const value = this.value

        Swal.fire({
            title: 'Confirmar cambio',
            text: '¿Deseas actualizar la configuración de pago parcial?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, guardar'
        }).then((result) => {

            if (!result.isConfirmed) {
                location.reload()
                return
            }

            fetch("<?= base_url('packages/updatePagoParcial') ?>", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id: id,
                        toggle: value
                    })
                })
                .then(r => r.json())
                .then(data => {

                    if (data.status === 'ok') {

                        document.querySelector('[data-field="flete_pagado"]').innerText = '$' + data.pagado.toFixed(2)

                        document.getElementById('fletePendiente')
                            .innerText = '$' + data.pendiente.toFixed(2)

                        Swal.fire('Actualizado', '', 'success')
                    }

                })

        })

    })
</script>
<script>
    document.getElementById('fleteTotalEdit').addEventListener('click', function() {

        const id = this.dataset.id
        const total = parseFloat(this.dataset.total)
        const pagado = parseFloat(this.dataset.pagado)
        const toggle = parseInt(this.dataset.toggle)

        Swal.fire({

            title: 'Editar Flete',

            html: `
                <div class="flete-editor">

                <div class="flete-row">
                <div class="flete-label">Total del flete</div>
                <input id="editTotal" type="number"
                step="0.01"
                class="flete-input"
                value="${total}">
                </div>

                <div class="flete-row">
                <div class="flete-label">Pago parcial</div>
                <select id="editToggle" class="flete-input">
                <option value="0" ${toggle==0?'selected':''}>Pago completo</option>
                <option value="1" ${toggle==1?'selected':''}>Pago parcial</option>
                </select>
                </div>

                <div class="flete-row">
                <div class="flete-label">Monto pagado</div>
                <input id="editPagado" type="number"
                step="0.01"
                class="flete-input"
                value="${pagado}">
                </div>

                <div class="flete-preview">
                Pendiente por cobrar:
                <strong>$<span id="previewPendiente">0.00</span></strong>
                </div>

                </div>
                `,

            didOpen: () => {

                const totalInput = document.getElementById('editTotal')
                const pagadoInput = document.getElementById('editPagado')
                const toggleInput = document.getElementById('editToggle')
                const pendientePreview = document.getElementById('previewPendiente')

                function recalcular() {

                    let total = parseFloat(totalInput.value) || 0
                    let pagado = parseFloat(pagadoInput.value) || 0
                    let toggle = parseInt(toggleInput.value)

                    if (toggle == 0) {

                        pagadoInput.value = total
                        pagadoInput.disabled = true

                        pendientePreview.innerText = '$0.00'

                    } else {

                        pagadoInput.disabled = false

                        let pendiente = total - pagado
                        if (pendiente < 0) pendiente = 0

                        pendientePreview.innerText = '$' + pendiente.toFixed(2)

                    }

                }

                totalInput.addEventListener('input', recalcular)
                pagadoInput.addEventListener('input', recalcular)
                toggleInput.addEventListener('change', recalcular)

                recalcular()

            },

            showCancelButton: true,
            confirmButtonText: 'Guardar',

            preConfirm: () => {

                const total = parseFloat(document.getElementById('editTotal').value)
                const toggle = parseInt(document.getElementById('editToggle').value)
                const pagado = parseFloat(document.getElementById('editPagado').value)

                if (pagado > total) {

                    Swal.showValidationMessage(
                        'El pagado no puede ser mayor al total'
                    )

                    return false
                }

                return {
                    total,
                    toggle,
                    pagado
                }

            }

        }).then((result) => {

            if (!result.isConfirmed) return

            fetch("<?= base_url('packages/updateFleteCompleto') ?>", {

                    method: 'POST',

                    headers: {
                        'Content-Type': 'application/json'
                    },

                    body: JSON.stringify({

                        id: id,
                        total: result.value.total,
                        toggle: result.value.toggle,
                        pagado: result.value.pagado

                    })

                })
                .then(r => r.json())
                .then(data => {

                    if (data.status == 'ok') {

                        location.reload()

                    }

                })

        })

    })
</script>
<?= $this->endSection() ?>