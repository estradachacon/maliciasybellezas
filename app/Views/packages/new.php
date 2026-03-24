<?= $this->extend('Layouts/mainbody') ?>
<?= $this->section('content') ?>
<?php
$logoUrl = setting('logo')
    ? base_url('upload/settings/' . setting('logo'))
    : '';
?>
<style>
    #overlayPreview {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
        z-index: 2000;
        display: none;
        align-items: center;
        justify-content: center;
    }

    .video-camara {
        width: 100%;
        height: 60vh;
        object-fit: cover;
        border-radius: 15px;
    }

    #previewCard {
        background: white;
        padding: 15px;
        border-radius: 10px;
        width: 340px;
    }

    .blur {
        filter: blur(3px);
        pointer-events: none;
        opacity: 0.6;
    }

    * {
        box-sizing: border-box;
    }

    #previewCard {
        transform: scale(1.5);
    }

    /* ===== ETIQUETA ===== */
    .etiqueta {
        width: 4in;
        height: 2in;
        border: 1px solid #000;
        font-family: Arial, sans-serif;
        font-size: 8px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 2px;
        position: relative;
    }

    /* CONTENEDOR */
    .contenedor {
        display: flex;
        flex: 1;
    }

    /* LOGO */
    .col-logo {
        width: 30%;
        display: flex;
        align-items: center;
        justify-content: center;
        border-right: 1px solid #000;
    }

    .logo {
        width: 100%;
        max-height: 40px;
        object-fit: contain;
    }

    /* DATOS */
    .col-data {
        width: 70%;
        padding-left: 3px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    /* EMPRESA */
    .empresa {
        font-weight: bold;
        font-size: 8px;
        border-bottom: 1px solid #000;
        margin-bottom: 2px;
    }

    /* FILAS */
    .fila {
        display: flex;
        justify-content: space-between;
        border-bottom: 1px dotted #ccc;
        padding: 0;
        line-height: 1.1;
    }

    /* FOOTER */
    .footer {
        display: flex;
        border-top: 1px solid #000;
        font-size: 7px;
    }

    .footer {
        display: flex;
        border-top: 1px solid #000;
        font-size: 6.5px;
        /* 🔥 más compacto */
    }

    .total {
        font-weight: bold;
    }

    .card {
        border-radius: 15px;
        border: none;
    }

    .card-header {
        border-top-left-radius: 15px !important;
        border-top-right-radius: 15px !important;
    }

    .form-control {
        border-radius: 10px;
        padding: 10px;
        font-size: 15px;
    }

    label {
        font-weight: 600;
        margin-bottom: 3px;
    }

    input:focus {
        box-shadow: 0 0 0 2px rgba(13, 110, 253, .2);
    }

    .btn {
        border-radius: 10px;
        padding: 10px 15px;
    }

    #seccionFinal {
        animation: fadeIn 0.3s ease-in-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .switch-container {
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 600;
    }

    .estado {
        font-size: 13px;
        opacity: 0.5;
        transition: 0.3s;
    }

    .estado.activo {
        color: #198754;
    }

    .estado.cancelado {
        color: #dc3545;
    }

    /* SWITCH */
    .switch {
        position: relative;
        width: 50px;
        height: 26px;
    }

    .switch input {
        display: none;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        background-color: #198754;
        border-radius: 50px;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        transition: .3s;
    }

    /* bolita */
    .slider:before {
        content: "";
        position: absolute;
        height: 20px;
        width: 20px;
        left: 3px;
        top: 3px;
        background: white;
        border-radius: 50%;
        transition: .3s;
    }

    /* estado cancelado */
    .switch input:checked+.slider {
        background-color: #dc3545;
    }

    .switch input:checked+.slider:before {
        transform: translateX(24px);
    }

    #miniPreview>div {
        pointer-events: none;
    }

    /* 📱 MODO CELULAR */
    @media (max-width: 768px) {

        #miniPreview {
            width: 100%;
            overflow: hidden;
        }

        #miniPreview .etiqueta {
            width: 100%;
            height: auto;
            transform: none;
            /* 🔥 quita escalas raras */
        }

        .switch-container {
            justify-content: center;
            flex-wrap: wrap;
            text-align: center;
        }
    }

    #miniPreview {
        width: 50%;
        margin: 0 auto;
        /* centrado */
        display: flex;
        justify-content: center;
    }

    #miniPreview .etiqueta {
        transform: scale(1);
        /* puedes ajustar si quieres más grande */
        transform-origin: top center;
    }

    #video {
        border-radius: 15px;
        transition: transform 0.2s;
    }

    #video:active {
        transform: scale(0.98);
    }

    .form-control.is-valid {
        border-color: #198754;
        padding-right: 2.5rem;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='%23198754' viewBox='0 0 16 16'%3E%3Cpath d='M13.485 1.929l.707.707L6 10.828 1.808 6.636l.707-.707L6 9.414z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 0.75rem center;
        background-size: 16px;
    }
</style>

<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm">

            <div class="card-header bg-primary text-white">
                <h5 class="mb-0 fw-bold">📦 Nuevo paquete</h5>
            </div>

            <div class="card-body">

                <form id="formPaquete">
                    <input type="hidden" name="codigoqr" id="codigoqr" value="<?= $codigoqr ?>">
                    <div class="row g-3">

                        <div class="col-md-6">
                            <label>Cliente</label>
                            <input type="text" name="cliente_nombre" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label>Teléfono</label>
                            <input type="text" name="cliente_telefono" class="form-control" required>
                        </div>

                        <div class="col-md-6 mt-2">
                            <label>Día de entrega</label>
                            <input type="date" name="dia_entrega" class="form-control" required>
                        </div>
                        <div class="col-md-3 mt-2">
                            <label>Hora inicio</label>
                            <input type="time" name="hora_inicio" class="form-control">
                        </div>

                        <div class="col-md-3 mt-2">
                            <label>Hora fin</label>
                            <input type="time" name="hora_fin" class="form-control">
                        </div>
                        <div class="col-md-12 mt-2">
                            <label>Destino</label>
                            <input type="text" name="destino" class="form-control" required>
                        </div>

                        <div class="col-md-12 mt-2">
                            <label>Encomendista</label>
                            <input type="text" name="encomendista_nombre" class="form-control" required>
                        </div>

                        <div class="col-md-12 mt-3">
                            <div class="p-3 border rounded-lg bg-light">
                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Total</label>
                                        <input type="text" name="total" id="total" class="form-control bg-light fw-bold">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-2 mt-2 col-md-12">

                            <!-- 🔹 SWITCH CANCELADO -->
                            <div class="col-md-6">
                                <div class="switch-container">
                                    Remunerar paquete:

                                    <span id="estadoTexto" class="estado activo">Cobrar total</span>

                                    <label class="switch">
                                        <input type="checkbox" id="cancelado">
                                        <span class="slider"></span>
                                    </label>

                                    <span class="estado cancelado">Paq. Cancelado</span>
                                </div>
                            </div>

                            <!-- 🔹 SWITCH TIPO VENTA -->
                            <div class="col-md-6">
                                <div class="switch-container">
                                    Venta tipo:

                                    <span id="tipoVentaTexto" class="estado activo">Detalle</span>

                                    <label class="switch">
                                        <input type="checkbox" id="tipo_venta">
                                        <span class="slider"></span>
                                    </label>

                                    <span class="estado cancelado">Mayoreo</span>
                                </div>
                            </div>

                        </div>

                        <div class="col-md-12 text-end">
                            <button type="button" id="btnGuardar" class="btn btn-success">
                                Guardar
                            </button>
                        </div>

                    </div>

                </form>

            </div>
        </div>
    </div>
</div>
<div id="seccionFinal" style="display:none; margin-top:20px;">

    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">
            Confirmación de paquete
        </div>

        <div class="card-body">
            <?php $codigoVendedor = session('codigo_vendedor'); ?>

            <!-- 🔙 VOLVER ARRIBA -->
            <div class="d-flex justify-content-start mb-2">
                <button id="btnVolver" class="btn btn-outline-secondary">
                    ← Volver
                </button>

                <small id="hintCamara" class="text-muted d-block text-center mb-2">
                    Presione la imagen para reiniciar cámara
                </small>
            </div>

            <!-- 🧾 PREVIEW -->
            <div class="mb-2 text-center">
                <div id="miniPreview"></div>
            </div>

            <!-- 🖨️ IMPRIMIR ABAJO DERECHA -->
            <div class="d-flex justify-content-end mb-3">
                <button id="btnImprimirFinal" class="btn btn-secondary">
                    🖨️ Imprimir
                </button>
            </div>

            <!-- FOTO -->
            <div class="text-center">
                <video id="video" autoplay playsinline class="video-camara"></video>
                <button type="button" id="btnSubirFoto" class="btn btn-secondary w-100 mt-2" style="display:none;">
                    📁 Subir foto
                </button>
                <button type="button" id="btnCapturar" class="btn btn-dark w-100 mt-2">
                    📸 Capturar foto
                </button>
                <div class="d-flex align-items-center justify-content-center gap-3 mb-2 mt-2">

                    <span class="fw-bold">📷 Cámara</span>

                    <label class="switch">
                        <input type="checkbox" id="modoArchivo">
                        <span class="slider"></span>
                    </label>

                    <span class="fw-bold">📁 Archivo</span>

                </div>
                <canvas id="canvas" style="display:none;"></canvas>
                <input type="file" id="fileFoto" accept="image/*" style="display:none;">
                <img id="previewFoto"
                    class="img-thumbnail mt-3"
                    style="max-width:200px; display:none; cursor:pointer;">
            </div>

            <!-- BOTONES -->
            <div class="mt-3 d-flex gap-2">
                <button id="btnGuardarFinal" class="btn btn-success">
                    Guardar paquete
                </button>
            </div>

        </div>
    </div>

</div>
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
    document.addEventListener('DOMContentLoaded', function() {
        /* Variables globales */
        let imagenWebp = null;
        let imagenURL = null;
        let imagenFile = null;
        let stream = null;
        let procesandoImagen = false;
        /* Fin de Variables globales */

        $('#btnImprimirFinal').click(function() {

            let data = $('#formPaquete').serialize();

            // 🔥 redirige a tu controlador de impresión
            window.open("<?= base_url('paquetes/etiqueta') ?>?" + data, '_blank');

        });

        // =========================
        // FORMULARIO
        // =========================


        $('#btnGuardar').click(function() {

            let errores = [];

            let cliente = $('[name="cliente_nombre"]').val().trim();
            let telefono = $('[name="cliente_telefono"]').val().trim();
            let fecha = $('[name="dia_entrega"]').val();
            let destino = $('[name="destino"]').val().trim();
            let encomendista = $('[name="encomendista_nombre"]').val().trim();
            let total = limpiarNumero($('#total').val());
            let cancelado = $('#cancelado').is(':checked');

            // 🔥 VALIDACIONES
            if (!cliente) errores.push('Debe ingresar el cliente');

            if (!telefono) {
                errores.push('Debe ingresar el teléfono');
            } else if (!/^\d{8}$/.test(telefono)) {
                errores.push('El teléfono debe tener 8 dígitos');
            }

            if (!fecha) errores.push('Debe seleccionar fecha de entrega');

            if (!destino) errores.push('Debe ingresar destino');

            if (!encomendista) errores.push('Debe ingresar encomendista');

            if (!cancelado && total <= 0) {
                errores.push('Debe ingresar un total o marcar como cancelado');
            }

            // 🚫 SI HAY ERRORES
            if (errores.length > 0) {

                Swal.fire({
                    icon: 'warning',
                    title: 'Validación',
                    html: `
                <div style="text-align:left">
                    <ul>
                        ${errores.map(e => `<li>${e}</li>`).join('')}
                    </ul>
                </div>
            `
                });

                return;
            }

            // ✅ TODO OK
            let data = $('#formPaquete').serializeArray();
            let obj = {};
            data.forEach(x => obj[x.name] = x.value);

            Swal.fire({
                title: '¿Confirmar datos?',
                text: 'Revisá antes de continuar',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, continuar'
            }).then((result) => {

                if (result.isConfirmed) {

                    $('#formPaquete').addClass('blur');
                    generarPreview(obj);

                    $('#seccionFinal').fadeIn();

                    $('html, body').animate({
                        scrollTop: $('#seccionFinal').offset().top
                    }, 500);

                    iniciarCamara();
                }
            });

        });
        $('[name="cliente_telefono"]').on('input', function() {

            let val = this.value.replace(/\D/g, '');
            this.value = val;

            let input = $(this);

            // quitar estado previo
            input.removeClass('is-valid is-invalid');

            if (val.length === 8) {
                input.addClass('is-valid');
            } else if (val.length > 0) {
                input.addClass('is-invalid');
            }

        });
        $('#btnVolver').click(function() {
            $('#seccionFinal').hide();
            $('#formPaquete').removeClass('blur');
            detenerCamara();
        });

        const video = document.getElementById('video');

        video.addEventListener('click', () => {

            if ($('#modoArchivo').is(':checked')) return; // 🔥 evita bug

            if (!stream || !stream.active) {
                iniciarCamara();
            } else {
                stream.getTracks().forEach(track => track.stop());
                iniciarCamara();
            }
        });

        // =========================
        // CÁMARA
        // =========================
        async function iniciarCamara() {
            try {

                let devices = await navigator.mediaDevices.enumerateDevices();
                let hayCamara = devices.some(d => d.kind === 'videoinput');

                if (!hayCamara) {

                    $('#modoArchivo').prop('checked', true).trigger('change');

                    Swal.fire({
                        icon: 'warning',
                        title: 'Cámara no disponible',
                        text: 'Se activó modo subida de archivo.'
                    });

                    return;
                }

                stream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: "environment"
                    }
                });

                document.getElementById('video').srcObject = stream;

            } catch (err) {

                console.error(err);

                // 🔥 MISMA LÓGICA
                $('#modoArchivo').prop('checked', true).trigger('change');

                Swal.fire({
                    icon: 'warning',
                    title: 'Cámara no disponible',
                    text: 'Se activó modo subida de archivo.'
                });
            }
        }

        function generarPreview(p) {

            let fecha = new Date(p.dia_entrega);
            let vendedor = "<?= $codigoVendedor ?>";
            let logo = "<?= $logoUrl ?>";

            let horaInicio = p.hora_inicio || '';
            let horaFin = p.hora_fin || '';

            let rangoHora = (horaInicio && horaFin) ?
                `${formatearHora(horaInicio)} - ${formatearHora(horaFin)}` :
                '';

            let dia = fecha.toLocaleDateString('es-SV', {
                weekday: 'short',
                day: 'numeric'
            });

            // Generar Codigo QR
            let codigo = $('#codigoqr').val();
            let urlQR = codigo;
            let qrImg = `https://api.qrserver.com/v1/create-qr-code/?size=80x80&data=${encodeURIComponent(urlQR)}`;

            //MiniPreview
            $('#miniPreview').html(`
    <div>

    <div class="etiqueta">

        <table style="width:100%;">

<tr>
    <!-- LOGO + QR -->
    <td style="width:30%; text-align:center; vertical-align:top;">

        ${logo ? `
            <img src="${logo}" style="width:100%; max-height:50px; object-fit:contain;">
        ` : ``}

        <img src="${qrImg}" style="width:70px; margin-top:5px;">
    </td>

                <!-- CONTENIDO -->
                <td style="width:70%; padding-left:5px;">

                    <!-- HEADER -->
                    <table style="width:100%; border-bottom:1px solid #000; margin-bottom:2px;">
                        <tr>
                            <td style="font-size:10px; font-weight:bold;">
                                MALICIAS Y BELLEZAS
                            </td>
                            <td style="text-align:right; font-size:6px;">
                                #${vendedor}
                            </td>
                        </tr>
                    </table>

                    <!-- CLIENTE + TEL -->
                    <table style="width:100%; border-bottom:1px dotted #ccc;">
                        <tr>
                            <td style="width:65%;">
                                <b>Cliente:</b> ${p.cliente_nombre || ''}
                            </td>
                            <td style="width:35%;">
                                <b>Tel:</b> ${p.cliente_telefono || ''}
                            </td>
                        </tr>
                    </table>

                    <!-- FECHA + ENCOM -->
                    <table style="width:100%; border-bottom:1px dotted #ccc;">
                        <tr>
                            <td style="width:40%;">
                                <b>Fecha:</b> ${dia}
                            </td>
                            <td style="width:60%;">
                                <b>Encomend:</b> ${p.encomendista_nombre || '—'}
                            </td>
                        </tr>
                    </table>

                </td>
            </tr>

        </table>

        <!-- DESTINO -->
        <table style="width:100%; border-bottom:1px dotted #ccc;">
            <tr>
                <td>
                    <b>Destino:</b> ${p.destino || ''}
                    ${rangoHora ? `(${rangoHora})` : ''}
                </td>
            </tr>
        </table>

        <!-- FOOTER -->
        <table style="width:100%; margin-top:3px;">
            <tr>
                <td style="text-align:right; font-weight:bold;">
                    Total: $${p.total || '0.00'}
                </td>
            </tr>
        </table>

    </div>
    </div>
    `);
        }

        function activarModoArchivo() {
            $('#video').hide();
            $('#btnCapturar').hide();
            $('#btnSubirFoto').show()
                .removeClass('btn-secondary')
                .addClass('btn-warning');
        }

        function formatearHora(hora) {
            if (!hora) return '';
            let [h, m] = hora.split(':');
            let suffix = h >= 12 ? 'PM' : 'AM';
            h = h % 12 || 12;
            return `${h}:${m} ${suffix}`;
        }

        function detenerCamara() {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
            }
        }

        // =========================
        // CAPTURAR FOTO
        // =========================
        $('#btnCapturar').click(function() {

            let video = document.getElementById('video');

            // 🔥 VALIDACIÓN CLAVE
            if (!video.videoWidth) {
                Swal.fire('Espera', 'La cámara aún no está lista', 'info');
                return;
            }

            let canvas = document.getElementById('canvas');

            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;

            let ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0);

            canvas.toBlob(function(blob) {

                imagenWebp = new File([blob], "foto.webp", {
                    type: "image/webp"
                });

                if (imagenURL) {
                    URL.revokeObjectURL(imagenURL);
                }

                imagenURL = URL.createObjectURL(blob);

                $('#previewFoto')
                    .attr('src', imagenURL)
                    .show();

            }, 'image/webp', 0.7);

        });
        $('#modoArchivo').change(function() {

            if (this.checked) {
                // 📁 MODO ARCHIVO
                detenerCamara();

                $('#video, #btnCapturar').fadeOut(200, function() {
                    activarModoArchivo();
                    $('#btnSubirFoto').fadeIn(200);
                });

                $('#hintCamara')
                    .text('Seleccione una imagen desde su dispositivo')
                    .fadeIn(200);

            } else {
                // 📷 MODO CÁMARA

                $('#btnSubirFoto').fadeOut(200, function() {

                    $('#video, #btnCapturar').fadeIn(200);

                    iniciarCamara();
                });

                $('#hintCamara')
                    .text('Presione la imagen para reiniciar cámara')
                    .fadeIn(200);
            }

        });

        $('#btnSubirFoto').click(function() {
            $('#fileFoto').click();
        });
        // =========================
        // MODAL IMAGEN
        // =========================
        $('#previewFoto').click(function() {

            if (!imagenURL) return;

            $('#imagenGrande').attr('src', imagenURL);

            let modal = new bootstrap.Modal(document.getElementById('modalImagen'));
            modal.show();
        });

        // =========================
        // GUARDAR
        // =========================
        $('#btnGuardarFinal').click(function() {
            let codigo = $('#codigoqr').val();

            if (!codigo) {
                Swal.fire('Error', 'No se generó el código QR', 'error');
                return;
            }
            // 🔒 evitar múltiples clicks
            if ($(this).data('loading')) return;

            if (!imagenWebp && !imagenFile) {
                Swal.fire('Falta foto', 'Debes tomar una foto', 'warning');
                return;
            }

            let btn = $(this);
            btn.data('loading', true);
            btn.prop('disabled', true);

            let formData = new FormData($('#formPaquete')[0]);
            // Tipo de venta
            let tipoVenta = $('#tipo_venta').is(':checked') ? 'mayoreo' : 'detalle';
            formData.append('tipo_venta', tipoVenta);

            if (imagenWebp) {
                formData.append('foto', imagenWebp);
            } else if (imagenFile) {
                formData.append('foto', imagenFile);
            }

            formData.append('total', limpiarNumero($('#total').val()));

            // 🔥 LOADING
            Swal.fire({
                title: 'Guardando paquete...',
                text: 'Por favor espera',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                    $('#video').addClass('blur');
                }
            });

            $.ajax({
                url: '<?= base_url('paquetes/guardar') ?>',
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(res) {

                    if (res.status === 'ok') {

                        Swal.fire({
                            icon: 'success',
                            title: 'Guardado',
                            text: 'Paquete registrado correctamente'
                        }).then(() => location.reload());

                    } else {

                        btn.data('loading', false);
                        btn.prop('disabled', false);

                        Swal.fire(
                            'Error',
                            JSON.stringify(res.errors || res.msg || res),
                            'error'
                        );
                        $('#video').removeClass('blur');
                    }
                },
                error: function() {

                    btn.data('loading', false);
                    btn.prop('disabled', false);

                    Swal.fire('Error', 'Error en el servidor', 'error');
                }
            });

        });

        // =========================
        // DINERO
        // =========================
        function limpiarNumero(valor) {
            return parseFloat(valor.replace(/[^0-9.]/g, '')) || 0;
        }

        function formatearMoneda(numero) {
            return numero.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        $('#precio, #envio').on('blur', function() {
            if ($('#cancelado').is(':checked')) return;

            let precio = limpiarNumero($('#precio').val());
            let envio = limpiarNumero($('#envio').val());

            let total = precio + envio;

            $('#total').val(formatearMoneda(total));
        });

        // TOTAL
        function calcularTotal() {

            if ($('#cancelado').is(':checked')) {
                $('#total').val('0.00');
                return;
            }

            let precio = limpiarNumero($('#precio').val());
            let envio = limpiarNumero($('#envio').val());

            let total = precio + envio;

            $('#total').val(formatearMoneda(total));
        }

        $('#fileFoto').change(function(e) {

            let file = e.target.files[0];
            if (!file) return;

            if (!file.type.startsWith('image/')) {
                Swal.fire('Error', 'Solo imágenes', 'error');
                return;
            }

            procesandoImagen = true; // 🔥 bloqueamos guardar

            let reader = new FileReader();

            reader.onload = function(ev) {

                let img = new Image();

                img.onload = function() {

                    let maxWidth = 800;
                    let width = img.width;
                    let height = img.height;

                    if (width > maxWidth) {
                        let scale = maxWidth / width;
                        width = maxWidth;
                        height = height * scale;
                    }

                    let canvas = document.getElementById('canvas');
                    canvas.width = width;
                    canvas.height = height;

                    let ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, width, height);

                    canvas.toBlob(function(blob) {

                        if (!blob) {
                            procesandoImagen = false;
                            Swal.fire('Error', 'No se pudo procesar la imagen', 'error');
                            return;
                        }

                        imagenWebp = new File([blob], "foto.webp", {
                            type: "image/webp"
                        });

                        if (imagenURL) URL.revokeObjectURL(imagenURL);

                        imagenURL = URL.createObjectURL(blob);

                        $('#previewFoto')
                            .attr('src', imagenURL)
                            .show();

                        procesandoImagen = false; // ✅ listo

                    }, 'image/webp', 0.7);
                };

                img.src = ev.target.result;
            };

            reader.readAsDataURL(file);
        });

        function obtenerNumero(valor) {
            return parseFloat(valor.replace(/,/g, '')) || 0;
        }

        // =========================
        // CANCELADO
        // =========================
        $('#cancelado').change(function() {

            let activo = $('.estado.activo');
            let cancelado = $('.estado.cancelado');

            if (this.checked) {

                activo.css('opacity', '0.3');
                cancelado.css('opacity', '1');

                $('#total').val('0.00');

                // 🔥 bloquear TODO
                $('#total').prop('disabled', true)
                    .addClass('bg-light');

            } else {

                activo.css('opacity', '1');
                cancelado.css('opacity', '0.3');

                // 🔥 desbloquear
                $('#total').prop('disabled', false)
                    .removeClass('bg-light');

                // 🔥 recalcular solo al volver
                calcularTotal();
            }

        });
        $('#tipo_venta').change(function() {

            let detalle = $('#tipoVentaTexto');
            let mayoreo = $(this).closest('.switch-container').find('.estado.cancelado');

            if (this.checked) {
                // 👉 MAYOREO
                detalle.css('opacity', '0.3');
                mayoreo.css('opacity', '1');
            } else {
                // 👉 DETALLE
                detalle.css('opacity', '1');
                mayoreo.css('opacity', '0.3');
            }

        });

    });
</script>
<?= $this->endSection() ?>