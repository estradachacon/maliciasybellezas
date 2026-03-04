<?= $this->extend('Layouts/mainbody') ?>
<?= $this->section('content') ?>
<style>
    .muteado {
        background-color: #e9ecef !important;
        color: #6c757d !important;
        text-decoration: line-through;
    }

    .bg-danger-light {
        background-color: #ffe5e5 !important;
    }

    .bg-warning-light {
        background-color: #fff3cd !important;
    }

    .bg-info-light {
        background-color: #d1e7dd !important;
        color: #0f5132;
    }

    .badge-pill {
        display: inline-block;
        padding: 0.25em 0.6em;
        font-size: 0.75rem;
        font-weight: 600;
        border-radius: 999px;
        margin-left: 0.5rem;
    }

    .bg-success-light {
        background-color: #d4edda !important;
    }

    .bg-info-light {
        background-color: #cce5ff !important;
    }

    /* Este es para el selector de casillero externo */
    .casillero-container {
        max-height: 0;
        overflow: hidden;
        transition: all .3s ease;
        margin-top: 5px;
    }

    .casillero-container.active {
        max-height: 60px;
    }

    .bg-pago-directo {
        background-color: #eaf7ea !important;
    }

    .select-muteado {
        background-color: #e9ecef !important;
        pointer-events: none;
        opacity: .7;
    }
</style>
<div class="row">
    <div class="col-md-12">

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h4>Rendición del motorista: <?= esc($motoristaNombre) ?></h4>
            </div>
            <div class="card-body">
                <form method="post"
                    action="<?= base_url('tracking-rendicion/save') ?>"
                    onsubmit="return confirmarRendicion(event);">

                    <input type="hidden" name="tracking_id" value="<?= $tracking->id ?>">
                    <input type="hidden" name="total_efectivo" id="input-total-efectivo">
                    <input type="hidden" name="total_otras_cuentas" id="input-total-otras">
                    <input type="hidden" name="total_directo" id="input-total-directo">


                    <h5>Seleccionar estado de los paquetes</h5>

                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>

                                <th style="width:5%" class="text-center">
                                    Fallido
                                </th>

                                <th style="width:5%" class="text-center">
                                    Solo<br>Recolecta
                                </th>

                                <th style="width:7%" class="text-center">
                                    Casillero<br>externo
                                </th>
                                <th style="width:7%" class="text-center">
                                    Cliente pagó al vendedor
                                </th>
                                <th style="width:6%">
                                    ID<br>Paquete
                                </th>

                                <th style="width:22%">
                                    Vendedor → Cliente
                                </th>

                                <th style="width:22%">
                                    Destino / Tipo
                                </th>

                                <th style="width:8%" class="text-right">
                                    Monto
                                </th>

                                <th style="width:10%" class="text-right">
                                    Aporte<br>Rendición
                                </th>

                                <th style="width:8%">
                                    Cuenta
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($detalles as $p): ?>
                                <?php
                                $destino = '';
                                $destinoPartes = [];

                                switch ((int) $p->tipo_servicio) {
                                    case 1:
                                        $destino = 'Punto fijo → ' . ($p->puntofijo_nombre ?? 'Sin info');
                                        $destinoPartes[] = $destino;
                                        break;
                                    case 2:
                                        $destino = 'Personalizado → ' . ($p->destino_personalizado ?? 'Sin info');
                                        $destinoPartes[] = $destino;
                                        break;
                                    case 3:
                                        $destino = 'Recolección → ' . ($p->lugar_recolecta_paquete ?? 'Sin info');
                                        $destinoPartes[] = 'Recolección'; // Marcador de parte
                                        if (!empty($p->destino_personalizado)) {
                                            $destino .= ' → Entregar en: ' . $p->destino_personalizado;
                                            $destinoPartes[] = 'Entrega Personalizada'; // Marcador de parte
                                        }
                                        if (!empty($p->puntofijo_nombre)) {
                                            $destino .= ' → Punto fijo: ' . $p->puntofijo_nombre;
                                            $destinoPartes[] = 'Entrega Punto Fijo'; // Marcador de parte
                                        }
                                        break;
                                    default:
                                        $destino = 'No definido';
                                }

                                // Recalculamos el conteo de destinos aquí
                                $destinoCount = count($destinoPartes);

                                // Clase inicial y tooltip
                                $rowClass = '';
                                $tooltip = '';
                                if ($p->status == 'regresado') {
                                    if ($p->tipo_servicio == 3) {
                                        $rowClass = ($destinoCount > 1) ? 'bg-warning' : 'bg-danger-light';
                                        $tooltip = ($destinoCount > 1) ? 'No retirado' : 'Recolección fallida';
                                    } else {
                                        $rowClass = 'bg-warning';
                                        $tooltip = 'No retirado';
                                    }
                                }

                                // Badge tipo entrega
                                $tipoBadge = '';
                                $badgeColor = '';
                                if ($p->tipo_servicio == 3) {
                                    if ($destinoCount === 1) {
                                        $tipoBadge = 'Recolección Única';
                                        $badgeColor = 'bg-danger-light';
                                    } else {
                                        $tipoBadge = 'Recol. + Entrega';
                                        $badgeColor = 'bg-info-light'; // Cambio a éxito total
                                    }
                                } elseif ($p->tipo_servicio == 1) {
                                    $tipoBadge = 'Punto Fijo';
                                    $badgeColor = 'bg-info-light';
                                } elseif ($p->tipo_servicio == 2) {
                                    $tipoBadge = 'Personalizado';
                                    $badgeColor = 'bg-info-light';
                                }
                                ?>
                                <tr class="paquete-row <?= $rowClass ?>" title="<?= $tooltip ?>"
                                    data-tipo="<?= $p->tipo_servicio ?>"
                                    data-destinos="<?= $destinoCount ?>"
                                    data-monto="<?= $p->monto ?>"
                                    data-toggle="<?= $p->toggle_pago_parcial ?>"
                                    data-flete-total="<?= $p->flete_total ?>"
                                    data-flete-pagado="<?= $p->flete_pagado ?>"
                                    data-flete-rendido="<?= (int) $p->flete_rendido ?>">

                                    <td class="text-center aporte-monto">
                                        <input type="checkbox" class="regresado-checkbox" name="regresados[]"
                                            value="<?= $p->id ?>" data-monto="<?= $p->monto ?? 0 ?>"
                                            <?= ($p->status == 'regresado' ? 'checked' : '') ?>>
                                    </td>

                                    <td class="text-center">
                                        <?php
                                        $isRecolectaMultiple = (
                                            $p->tipo_servicio == 3
                                            && $destinoCount >= 2
                                            && $p->package_status !== 'asignado_para_entrega'   // ✔ Aquí ya usas el status REAL del paquete
                                        );

                                        if ($isRecolectaMultiple):
                                        ?>
                                            <input type="checkbox" class="recolectado-solo-checkbox" name="recolectados_solo[]"
                                                value="<?= $p->id ?>" data-id="<?= $p->id ?>"
                                                title="Marcar si el paquete fue recolectado pero la entrega final está pendiente."
                                                <?= ($p->status == 'recolectado' ? 'checked' : '') ?>>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center casillero-cell">
                                        <div class="casillero-wrapper">
                                            <input type="checkbox"
                                                class="casillero-checkbox"
                                                data-id="<?= $p->package_id ?>">

                                            <div class="casillero-container">
                                                <select name="external_location[<?= $p->package_id ?>]"
                                                    class="form-control select2-location casillero-select">
                                                </select>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox"
                                            class="pagado-vendedor-checkbox"
                                            name="pagado_vendedor[]"
                                            value="<?= $p->package_id ?>"
                                            data-id="<?= $p->package_id ?>">
                                    </td>
                                    <td><?= $p->package_id ?></td>
                                    <td><?= esc($p->vendedor . ' → ' . $p->cliente) ?></td>
                                    <td>
                                        <?= esc($destino) ?>
                                        <?php if (!empty($tipoBadge)): ?>
                                            <span class="badge-pill <?= $badgeColor ?>"><?= $tipoBadge ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center paquete-monto-celda">
                                        <?php
                                        // Condición inicial para el paquete (asumiendo que 'recolectado' aplica a "Solo Recolectado")
                                        $isRecolectadoSolo = ($p->status == 'recolectado' && $isRecolectaMultiple);
                                        $muteClass = $isRecolectadoSolo ? 'muteado' : '';
                                        ?>
                                        <strong class="paquete-monto-total <?= $muteClass ?>">
                                            $<?= number_format($p->monto ?? 0, 2) ?>
                                        </strong>
                                    </td>
                                    <td class="aporte-rendicion">
                                        <?php if ($p->tipo_servicio == 3): ?>

                                            <?php if ($p->flete_rendido): ?>
                                                <span class="muteado">
                                                    <?= '$' . number_format(
                                                        $p->toggle_pago_parcial == 1 ? $p->flete_pagado : $p->flete_total,
                                                        2
                                                    ) ?>
                                                </span>
                                                <br>
                                                <small class="text-muted">Flete ya fue recolectado</small>
                                            <?php else: ?>
                                                <?= '$' . number_format(
                                                    $p->toggle_pago_parcial == 1 ? $p->flete_pagado : $p->flete_total,
                                                    2
                                                ) ?>
                                            <?php endif; ?>

                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <select name="cuenta_asignada[<?= $p->id ?>]"
                                            class="form-control select2-account">
                                        </select>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <div class="card bg-light p-3 mb-3 shadow-sm">
                        <h5 class="mb-0">Total a entregar: <strong id="total-entregar">$0.00</strong></h5>
                        <small class="text-muted">Solo se suman los paquetes exitosos (no marcados como no
                            entregados/regresados)</small>
                    </div>
                    <div class="card bg-light p-3 mb-3 shadow-sm">
                        <h5 class="mb-0">Total otras cuentas: <strong id="total-otras">$0.00</strong></h5>
                        <small class="text-muted">Solo paquetes exitosos que NO estén en cuenta efectivo</small>
                    </div>
                    <div class="card bg-light p-3 mb-3 shadow-sm">
                        <h5 class="mb-0">Pagado directo al vendedor: <strong id="total-directo">$0.00</strong></h5>
                        <small class="text-muted">Paquetes cancelados al delivery porque el cliente pagó directo</small>
                    </div>

                    <button type="submit" id="btnRendir" class="btn btn-success">
                        Guardar rendición
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    function confirmarRendicion(e) {

        e.preventDefault();

        const totalEfectivo = document.getElementById('total-entregar').innerText;
        const totalOtras = document.getElementById('total-otras').innerText;

        // 📦 Contar paquetes dejados en casillero externo
        let totalCasilleros = 0;

        document.querySelectorAll('.casillero-checkbox').forEach(cb => {
            if (cb.checked) {
                totalCasilleros++;
            }
        });

        // Solo mostrar si hay al menos uno
        const lineaCasillero = totalCasilleros > 0 ?
            `<p><strong>Paquetes en casilleros externos:</strong> ${totalCasilleros}</p>` :
            '';

        Swal.fire({
            title: 'Confirmar rendición',
            html: `
                <div style="text-align:left; font-size:15px;">
                    <p><strong>Total en efectivo:</strong> ${totalEfectivo}</p>
                    <p><strong>Total otras cuentas:</strong> ${totalOtras}</p>
                    ${lineaCasillero}
                    <hr>
                    <p>¿Deseas continuar con la rendición?</p>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#dc3545',
            confirmButtonText: 'Sí, confirmar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then((result) => {

            if (result.isConfirmed) {

                const btn = document.getElementById('btnRendir');
                btn.disabled = true;

                Swal.fire({
                    title: 'Procesando rendición',
                    text: 'Por favor espera…',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                const limpioEfectivo = totalEfectivo.replace('$', '');
                const limpioOtras = totalOtras.replace('$', '');
                const totalDirecto = document.getElementById('total-directo').innerText;

                document.getElementById('input-total-directo').value = totalDirecto.replace('$', '');
                document.getElementById('input-total-efectivo').value = limpioEfectivo;
                document.getElementById('input-total-otras').value = limpioOtras;

                e.target.submit();
            }
        });

        return false;
    }
</script>

<script>
    $(document).ready(function() {
        $('.select2-location').select2({
            theme: 'bootstrap4',
            width: '100%',
            placeholder: 'Seleccionar casillero',
            minimumInputLength: 0,
            ajax: {
                url: "<?= base_url('external-locations-list') ?>",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.map(item => ({
                            id: item.id,
                            text: item.nombre +
                                (item.descripcion ? ' - ' + item.descripcion : '')
                        }))
                    };
                }
            }
        });

        // Inicializar Select2 para selección de cuentas
        $('.select2-account').select2({
            theme: 'bootstrap4',
            width: '100%',
            placeholder: 'Buscar cuenta...',
            allowClear: true,
            minimumInputLength: 1,
            language: {
                inputTooShort: function() {
                    return 'Ingrese 1 o más caracteres';
                }
            },
            ajax: {
                url: "<?= base_url('accounts-list') ?>",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.map(item => ({
                            id: item.id,
                            text: item.name
                        }))
                    };
                }
            }
        });

        // 🟢 Obtener desde el servidor la cuenta con ID 1
        $.ajax({
            url: "<?= base_url('accounts-list') ?>",
            data: {
                q: "efectivo"
            }, // cualquier valor, el backend lo ignora si devuelves siempre la lista
            dataType: "json",
            success: function(data) {

                // buscar cuenta ID = 1
                const cuenta = data.find(item => item.id == 1);

                if (!cuenta) return; // si no existe, no ponemos nada

                // Colocar como selección inicial en todos los select2
                $('.select2-account').each(function() {
                    let option = new Option(cuenta.name, cuenta.id, true, true);
                    $(this).append(option).trigger('change');
                });
            }
        });

    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        function setMuteMonto(strongMonto, estado) {
            if (!strongMonto) return;

            if (estado) {
                strongMonto.classList.add('muteado');
            } else {
                strongMonto.classList.remove('muteado');
            }
        }

        function aplicarRegresado(row, refs) {

            const {
                cbRegresado,
                cbRecolectadoSolo,
                cbCasillero,
                cbPagadoVendedor,
                strongMonto
            } = refs;

            if (!cbRegresado) return false;

            if (cbRegresado.checked) {

                row.classList.add('bg-danger-light');

                if (strongMonto) {
                    strongMonto.classList.add('muteado');
                }

                // Desactivar opciones incompatibles
                if (cbRecolectadoSolo) {
                    cbRecolectadoSolo.checked = false;
                    cbRecolectadoSolo.disabled = true;
                }

                if (cbCasillero) {
                    cbCasillero.checked = false;
                    cbCasillero.disabled = true;
                }

                if (cbPagadoVendedor) {
                    cbPagadoVendedor.checked = false;
                    cbPagadoVendedor.disabled = true;
                }

                return true;
            }

            // Si se desmarca FALLIDO → restaurar
            if (cbRecolectadoSolo) cbRecolectadoSolo.disabled = false;
            if (cbCasillero) cbCasillero.disabled = false;
            if (cbPagadoVendedor) cbPagadoVendedor.disabled = false;

            return false;
        }

        function aplicarPagadoVendedor(row, refs, totales) {

            const {
                cbPagadoVendedor,
                strongMonto,
                selectCuenta,
                montoPaquete
            } = refs;

            if (!cbPagadoVendedor.checked) return false;

            row.classList.add('bg-pago-directo');

            strongMonto.classList.add('muteado');

            if (selectCuenta) {
                selectCuenta.classList.add('select-muteado');
                selectCuenta.disabled = true;
                selectCuenta.value = '';
            }

            totales.directo += montoPaquete;

            return true;
        }

        function aplicarCasillero(row, refs) {

            const {
                cbCasillero,
                selectCasillero,
                cbRegresado,
                cbRecolectadoSolo
            } = refs;

            if (!cbCasillero.checked) {
                if (selectCasillero) {
                    selectCasillero.closest('.casillero-container').classList.remove('active');
                }
                return false;
            }

            row.classList.add('bg-info-light');

            if (selectCasillero) {
                selectCasillero.closest('.casillero-container').classList.add('active');
            }

            if (cbRegresado) {
                cbRegresado.checked = false;
                cbRegresado.disabled = true;
            }

            if (cbRecolectadoSolo) {
                cbRecolectadoSolo.checked = false;
                cbRecolectadoSolo.disabled = true;
            }

            return true;
        }

        function calcularTotales(row, refs, totales) {

            const {
                strongMonto,
                selectCuenta,
                tipo,
                destinos,
                montoPaquete,
                montoVendedor,
                fleteRendido
            } = refs;

            let subtotal = 0;

            strongMonto.classList.remove('muteado');

            if (tipo === 3) {

                subtotal += montoPaquete;

                if (!fleteRendido) {
                    subtotal += montoVendedor;
                }

            } else {

                subtotal += montoPaquete;

            }

            const cuentaSeleccionada = parseInt(selectCuenta?.value) || 0;

            if (cuentaSeleccionada === 1) {
                totales.efectivo += subtotal;
            } else if (cuentaSeleccionada > 1) {
                totales.otras += subtotal;
            }

            row.classList.add('bg-success-light');
        }

        function actualizarEstadoYTotal() {

            let totales = {
                efectivo: 0,
                otras: 0,
                directo: 0
            };

            document.querySelectorAll('.paquete-row').forEach(row => {

                row.classList.remove(
                    'bg-warning',
                    'bg-danger-light',
                    'bg-success-light',
                    'bg-info-light',
                    'bg-pago-directo'
                );

                const refs = {
                    cbRegresado: row.querySelector('.regresado-checkbox'),
                    cbRecolectadoSolo: row.querySelector('.recolectado-solo-checkbox'),
                    strongMonto: row.querySelector('.paquete-monto-total'),
                    selectCuenta: row.querySelector('.select2-account'),
                    cbCasillero: row.querySelector('.casillero-checkbox'),
                    selectCasillero: row.querySelector('.casillero-select'),
                    cbPagadoVendedor: row.querySelector('.pagado-vendedor-checkbox'),

                    tipo: parseInt(row.dataset.tipo),
                    destinos: parseInt(row.dataset.destinos),
                    montoPaquete: Number(row.dataset.monto) || 0,

                    togglePago: parseInt(row.dataset.toggle),
                    fleteTotal: Number(row.dataset.fleteTotal) || 0,
                    fletePagado: Number(row.dataset.fletePagado) || 0,
                    fleteRendido: parseInt(row.dataset.fleteRendido) === 1
                };

                refs.montoVendedor =
                    refs.togglePago === 0 ? refs.fleteTotal : refs.fletePagado;

                // PRIORIDAD 1: FALLIDO
                if (aplicarRegresado(row, refs)) {
                    return;
                }

                // PRIORIDAD 2: PAGADO AL VENDEDOR
                const esPagado = aplicarPagadoVendedor(row, refs, totales);

                // PRIORIDAD 3: CASILLERO
                const esCasillero = aplicarCasillero(row, refs);

                // PRIORIDAD 4: NORMAL
                if (!esPagado && !esCasillero) {
                    calcularTotales(row, refs, totales);
                }

            });

            document.getElementById('total-entregar').innerText = '$' + totales.efectivo.toFixed(2);
            document.getElementById('total-otras').innerText = '$' + totales.otras.toFixed(2);
            document.getElementById('total-directo').innerText = '$' + totales.directo.toFixed(2);
        }

        actualizarEstadoYTotal();


        /*
        *Listeners para recalcular totales y actualizar estados visuales
        */
        
        //Recalcular cuando cambie la cuenta (Select2)
        $(document).on('change', '.select2-account', function() {
            actualizarEstadoYTotal();
        });

        document.querySelectorAll('.regresado-checkbox').forEach(cb => {
            cb.addEventListener('change', actualizarEstadoYTotal);
        });

        document.querySelectorAll('.recolectado-solo-checkbox').forEach(cb => {
            cb.addEventListener('change', actualizarEstadoYTotal);
        });

        document.querySelectorAll('.casillero-checkbox').forEach(cb => {
            cb.addEventListener('change', actualizarEstadoYTotal);
        });

        document.querySelectorAll('.pagado-vendedor-checkbox').forEach(cb => {
            cb.addEventListener('change', actualizarEstadoYTotal);
        });
    });
</script>

<?= $this->endSection() ?>