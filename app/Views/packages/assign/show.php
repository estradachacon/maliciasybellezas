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

    .flete-item {
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        transition: 0.2s;
    }

    .flete-item:hover {
        background: #f1f5f9;
        border-color: #d1d5db;
    }

    .flete-label {
        font-size: 13px;
        font-weight: 600;
        color: #374151;
        max-width: 240px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .flete-input input {
        border-radius: 8px;
        font-weight: 600;
    }

    .flete-input .input-group-text {
        border-radius: 8px 0 0 8px;
        background: #f3f4f6;
    }

    .flete-input input:focus {
        box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.25);
        border-color: #f59e0b;
    }

    .card-section:hover {
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    }

    .estado-badge {
        font-size: 11px;
        padding: 4px 10px;
        border-radius: 10px;
        font-weight: 600;
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

    .estado-en_casillero {
        background: #e0f2fe;
        color: #0369a1;
    }
</style>

<?php
$totalReal = 0;
$totalDepositado = 0;

foreach ($detalles as $p) {
    $totalReal += (float) $p->total_real;
    $totalDepositado += (float) $p->valor_paquete;
}

$total = $totalDepositado;
$totalCobrar = $totalDepositado;
?>

<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm">

            <!-- HEADER -->
            <div class="card-header d-flex justify-content-between">

                <div>
                    <h4 class="mb-0">
                        Asignación
                        <span class="badge bg-primary text-white">
                            #<?= $deposito->id ?>
                        </span>
                    </h4>
                    <small class="text-muted">Detalle de depósito</small>
                </div>

                <a href="<?= base_url('packages-assignation') ?>" class="btn btn-secondary btn-sm mb-4">
                    ← Volver
                </a>

            </div>

            <div class="card-body">

                <div class="row">

                    <!-- IZQUIERDA -->
                    <div class="col-lg-9">

                        <!-- INFO GENERAL -->
                        <div class="card-section mb-3">
                            <div class="row">

                                <div class="col-md-4">
                                    <div class="info-label">Fecha</div>
                                    <div class="info-value">
                                        <?= date('d/m/Y', strtotime($deposito->fecha)) ?>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="info-label">Encomendistas</div>
                                    <div class="info-sub">
                                        <?= esc($deposito->encomendistas ?? '—') ?>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="info-label">Flete total pagado</div>
                                    <div class="info-value text-success d-flex gap-2 justify-content-between">
                                        <span id="fleteDisplay">$<?= number_format($deposito->flete_total, 2) ?></span>
                                        <?php if (tienePermiso('editar_flete_asignacion')): ?>
                                            <button type="button" class="btn btn-outline-warning btn-sm py-0 px-2" id="btnEditarFlete" title="Editar flete">
                                                <small>Editar</small>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- DETALLE -->
                        <div class="card-section mb-3">

                            <div class="info-label mb-2">Paquetes asignados</div>

                            <!-- DESKTOP -->
                            <div class="d-none d-md-block">
                                <table class="table table-sm table-borderless">
                                    <thead style="font-size:12px;">
                                        <tr>
                                            <th>#</th>
                                            <th>ID</th>
                                            <th>Cliente</th>
                                            <th>Destino</th>
                                            <th>Encomendista</th>
                                            <th>Estado</th>
                                            <th class="text-end">Valor real</th>
                                            <th class="text-end">Valor Remunerar</th>
                                            <th class="text-end">Flete asignado</th>
                                            <th class="text-center">%</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($detalles as $i => $p): ?>
                                            <tr>
                                                <td><?= $i + 1 ?></td>

                                                <td>
                                                    <a href="<?= base_url('packages/show/' . $p->id) ?>"
                                                        class="badge badge-lg bg-dark text-white text-decoration-none d-inline-flex gap-1"
                                                        title="Ver paquete">

                                                        Ver: #<?= $p->id ?>

                                                    </a>
                                                </td>

                                                <td><?= esc($p->cliente_nombre) ?></td>

                                                <td><?= esc($p->destino) ?></td>

                                                <td><?= esc($p->encomendista_name ?? '—') ?></td>

                                                <td>
                                                    <?php if (!function_exists('estadoBonito')): ?>
                                                        <?php
                                                        function estadoBonito($estado)
                                                        {
                                                            return ucfirst(str_replace('_', ' ', $estado));
                                                        }
                                                        ?>
                                                    <?php endif; ?>

                                                    <span class="estado-badge estado-<?= $p->nuevo_estado ?>">
                                                        <?= esc(estadoBonito($p->nuevo_estado)) ?>
                                                    </span>
                                                </td>

                                                <td class="text-end">
                                                    $<?= number_format($p->total_real, 2) ?>
                                                </td>

                                                <td class="text-end">
                                                    <span class="fw-bold text-success">
                                                        $<?= number_format($p->valor_paquete, 2) ?>
                                                    </span>
                                                </td>

                                                <td class="text-end">
                                                    <span class="text-danger fw-bold">
                                                        $<?= number_format($p->flete_asignado ?? 0, 2) ?>
                                                    </span>
                                                </td>

                                                <td class="text-center">
                                                    <?= number_format($p->porcentaje, 1) ?>%
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- MOBILE -->
                            <div class="d-block d-md-none">
                                <?php foreach ($detalles as $i => $p): ?>
                                    <div class="border rounded p-2 mb-2 shadow-sm">

                                        <div class="fw-bold">
                                            #<?= $p->id ?> - <?= esc($p->cliente_nombre) ?>
                                        </div>

                                        <div class="text-muted small">
                                            <?= esc($p->destino) ?>
                                        </div>

                                        <div class="small mt-1">
                                            🚚 <?= esc($p->encomendista_name ?? '—') ?>
                                        </div>

                                        <div class="mt-1">
                                            <span class="estado-badge estado-<?= $p->nuevo_estado ?>">
                                                <?= esc($p->nuevo_estado) ?>
                                            </span>
                                        </div>

                                        <div class="d-flex justify-content-between mt-2">
                                            <small>Real: $<?= number_format($p->total_real, 2) ?></small>
                                            <small class="text-success fw-bold">
                                                Dep: $<?= number_format($p->valor_paquete, 2) ?>
                                            </small>
                                        </div>
                                        <div class="text-end">
                                            <small class="text-danger fw-bold">
                                                Flete: $<?= number_format($p->flete_asignado ?? 0, 2) ?>
                                            </small>
                                        </div>

                                        <div class="text-end small">
                                            <?= number_format($p->porcentaje, 1) ?>%
                                        </div>

                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- RESUMEN -->
                            <div class="border-top pt-2 mt-2">

                                <div class="d-flex justify-content-between">
                                    <span>Paquetes</span>
                                    <span><?= count($detalles) ?></span>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <span>Total real</span>
                                    <span>$<?= number_format($totalReal, 2) ?></span>
                                </div>

                                <div class="d-flex justify-content-between fw-bold">
                                    <span class="text-success">Total a remunerar</span>
                                    <span class="text-success">
                                        $<?= number_format($totalDepositado, 2) ?>
                                    </span>
                                </div>

                            </div>

                        </div>

                    </div>

                    <!-- DERECHA -->
                    <div class="col-lg-3">
                        <div class="card-section text-center">

                            <div class="info-label mb-2">Resumen</div>

                            <div class="mb-2">
                                <div class="info-sub">Paquetes</div>
                                <div class="info-value"><?= count($detalles) ?></div>
                            </div>

                            <div class="info-sub">Total a remunerar</div>
                            <div class="info-value text-success">
                                $<?= number_format($totalDepositado, 2) ?>
                            </div>

                            <div>
                                <div class="info-sub">Total real</div>
                                <div class="info-value">
                                    $<?= number_format($totalReal, 2) ?>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>
</div>

<?php if (tienePermiso('editar_flete_asignacion')): ?>
    <script>
        (function() {
            const FLETE_ACTUAL = <?= json_encode((float) $deposito->flete_total) ?>;
            const DEPOSIT_ID = <?= json_encode((int) $deposito->id) ?>;
            const URL_ACTUALIZAR = '<?= base_url('packages-assign/actualizar-flete/' . $deposito->id) ?>';

            const PAQUETES = <?= json_encode(array_map(fn($p) => [
                                    'id'       => $p->detalle_id,
                                    'label'    => '#' . $p->id . ' — ' . $p->cliente_nombre,
                                    'flete'    => (float) ($p->flete_asignado ?? 0),
                                ], $detalles)) ?>;

            function enviarActualizacion(payload) {
                Swal.fire({
                    title: 'Actualizando...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                fetch(URL_ACTUALIZAR, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify(payload)
                    })
                    .then(r => r.json())
                    .then(res => {
                        if (res.status === 'ok') {
                            Swal.fire({
                                    icon: 'success',
                                    title: 'Listo',
                                    text: res.msg,
                                    timer: 1800,
                                    showConfirmButton: false
                                })
                                .then(() => location.reload());
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: res.msg || 'Error desconocido'
                            });
                        }
                    })
                    .catch(() => Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error de conexión'
                    }));
            }

            function buildIndividualRows() {
                return PAQUETES.map((p, i) => `
            <div class="flete-item d-flex justify-content-between mb-2 p-2 rounded">

                <div class="flete-info">
                    <div class="flete-label" title="${p.label}">
                        ${p.label}
                    </div>
                </div>

                <div class="flete-input">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">$</span>
                        <input type="number"
                            class="form-control flete-ind-input text-end"
                            data-idx="${i}"
                            data-id="${p.id}"
                            min="0"
                            step="0.01"
                            value="${p.flete.toFixed(2)}">
                    </div>
                </div>

            </div>
        `).join('');
            }

            document.getElementById('btnEditarFlete')?.addEventListener('click', function() {
                let modoActivo = 'total';

                const htmlSwal = `
        <div style="text-align:left;">

            <!-- TABS -->
            <div class="d-flex mb-3 border-bottom">
                <button type="button" id="tabTotal"
                    class="btn btn-sm fw-semibold px-3 py-1 me-1 tab-btn active-tab"
                    style="border:none;border-bottom:2px solid #f59e0b;background:transparent;color:#b45309;">
                    Por total
                </button>
                <button type="button" id="tabIndividual"
                    class="btn btn-sm fw-semibold px-3 py-1 tab-btn"
                    style="border:none;border-bottom:2px solid transparent;background:transparent;color:#6c757d;">
                    Por paquete
                </button>
            </div>

            <!-- PANEL TOTAL -->
            <div id="panelTotal">
                <p class="mb-1 text-muted" style="font-size:12px;">
                    Flete actual: <strong>$${FLETE_ACTUAL.toFixed(2)}</strong>
                </p>
                <label class="form-label" style="font-size:13px;">Nuevo flete total</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text">$</span>
                    <input type="number" id="swalFleteTotal" class="form-control"
                           min="0.01" step="0.01" value="${FLETE_ACTUAL.toFixed(2)}">
                </div>
                <p class="text-warning mt-2" style="font-size:11px;">
                    Los fletes de cada paquete serán recalculados proporcionalmente.
                </p>
            </div>

            <!-- PANEL INDIVIDUAL -->
            <div id="panelIndividual" style="display:none;">
                <div style="max-height:280px;overflow-y:auto;padding-right:4px;">
                    ${buildIndividualRows()}
                </div>
                <div class="d-flex justify-content-between mt-2 pt-2 border-top fw-semibold" style="font-size:13px;">
                    <span>Total</span>
                    <span id="swalTotalInd">$0.00</span>
                </div>
            </div>

        </div>`;

                Swal.fire({
                    title: 'Editar flete',
                    html: htmlSwal,
                    showCancelButton: true,
                    confirmButtonText: 'Actualizar',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#f59e0b',
                    width: 520,
                    didOpen: () => {
                        function activarTab(tab) {
                            modoActivo = tab;
                            const isTotal = tab === 'total';

                            document.getElementById('panelTotal').style.display = isTotal ? '' : 'none';
                            document.getElementById('panelIndividual').style.display = isTotal ? 'none' : '';

                            document.getElementById('tabTotal').style.borderBottomColor = isTotal ? '#f59e0b' : 'transparent';
                            document.getElementById('tabTotal').style.color = isTotal ? '#b45309' : '#6c757d';
                            document.getElementById('tabIndividual').style.borderBottomColor = isTotal ? 'transparent' : '#f59e0b';
                            document.getElementById('tabIndividual').style.color = isTotal ? '#6c757d' : '#b45309';
                        }

                        document.getElementById('tabTotal').addEventListener('click', () => activarTab('total'));
                        document.getElementById('tabIndividual').addEventListener('click', () => activarTab('individual'));

                        function recalcTotal() {
                            let sum = 0;
                            document.querySelectorAll('.flete-ind-input').forEach(inp => {
                                sum += parseFloat(inp.value) || 0;
                            });
                            document.getElementById('swalTotalInd').textContent = '$' + sum.toFixed(2);
                        }

                        recalcTotal();
                        document.querySelectorAll('.flete-ind-input').forEach(inp => {
                            inp.addEventListener('input', recalcTotal);
                        });
                    },
                    preConfirm: () => {
                        if (modoActivo === 'total') {
                            const val = parseFloat(document.getElementById('swalFleteTotal').value);
                            if (!val || val <= 0) {
                                Swal.showValidationMessage('Ingrese un monto válido mayor a 0');
                                return false;
                            }
                            return {
                                modo: 'total',
                                flete_total: val
                            };
                        } else {
                            const detalles = [];
                            let hayError = false;
                            document.querySelectorAll('.flete-ind-input').forEach(inp => {
                                const flete = parseFloat(inp.value);
                                if (isNaN(flete) || flete < 0) {
                                    hayError = true;
                                    return;
                                }
                                detalles.push({
                                    id: parseInt(inp.dataset.id),
                                    flete
                                });
                            });
                            if (hayError) {
                                Swal.showValidationMessage('Todos los valores deben ser números mayores o iguales a 0');
                                return false;
                            }
                            return {
                                modo: 'individual',
                                detalles
                            };
                        }
                    }
                }).then(result => {
                    if (!result.isConfirmed) return;
                    enviarActualizacion(result.value);
                });
            });
        }());
    </script>
<?php endif; ?>

<?= $this->endSection() ?>