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
        /* 🔥 NECESARIO */
        /* 🔥 bajar padding */
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

    #miniPreview .etiqueta {
        width: 262pt;
        height: 110pt;
        margin: 4pt auto;
        padding: 5pt;
        border: 2px solid #000;
        border-radius: 4%;
        font-family: sans-serif;
        font-size: 9pt;
    }

    #miniPreview>div {
        pointer-events: none;
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

                    <div class="row g-3">

                        <div class="col-md-6">
                            <label>Cliente</label>
                            <input type="text" name="cliente_nombre" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label>Teléfono</label>
                            <input type="text" name="cliente_telefono" class="form-control">
                        </div>

                        <div class="col-md-6 mt-2">
                            <label>Día de entrega</label>
                            <input type="date" name="dia_entrega" class="form-control">
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
                            <input type="text" name="destino" class="form-control">
                        </div>

                        <div class="col-md-12 mt-2">
                            <label>Encomendista</label>
                            <input type="text" name="encomendista_nombre" class="form-control">
                        </div>

                        <div class="col-md-12 mt-3">
                            <div class="p-3 border rounded-lg bg-light">
                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <label class="form-label">Precio</label>
                                        <input type="text" name="precio" id="precio" class="form-control money">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Envío</label>
                                        <input type="text" name="envio" id="envio" class="form-control money">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Total</label>
                                        <input type="text" name="total" id="total" class="form-control bg-light fw-bold">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 mb-2 mt-2">
                            <div class="switch-container">

                                <span id="estadoTexto" class="estado activo">Cobrar total</span>

                                <label class="switch">
                                    <input type="checkbox" id="cancelado">
                                    <span class="slider"></span>
                                </label>

                                <span class="estado cancelado">Paq. Cancelado</span>
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

        let imagenWebp = null;
        let imagenURL = null;
        let imagenFile = null;
        let stream = null;
        let procesandoImagen = false;

        $('#btnImprimirFinal').click(function() {

            let data = $('#formPaquete').serialize();

            // 🔥 redirige a tu controlador de impresión
            window.open("<?= base_url('paquetes/etiqueta') ?>?" + data, '_blank');

        });

        // =========================
        // FORMULARIO
        // =========================


        $('#btnGuardar').click(function() {

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

        $('#btnVolver').click(function() {
            $('#seccionFinal').hide();
            $('#formPaquete').removeClass('blur');
            detenerCamara();
        });

        const video = document.getElementById('video');

        video.addEventListener('click', () => {
            if (!stream || !stream.active) {
                iniciarCamara();
            } else {
                // opcional: reinicio manual siempre
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

                    activarModoArchivo();

                    Swal.fire({
                        icon: 'info',
                        title: 'Modo archivo',
                        text: 'No se detectó cámara. Debes subir una foto.'
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

                activarModoArchivo();

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

            $('#miniPreview').html(`
    <div style="zoom:1.2;">

    <div class="etiqueta">

        <table style="width:100%;">

            <tr>
                <!-- LOGO -->
                <td style="width:30%; text-align:center; vertical-align:top;">
                    ${logo ? `
                        <img src="${logo}" style="width:100%; max-height:80px; object-fit:contain;">
                    ` : ``}
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
                <td style="width:33%;">Precio: $${p.precio || '0.00'}</td>
                <td style="width:33%;">Envío: $${p.envio || '0.00'}</td>
                <td style="width:34%; text-align:right; font-weight:bold;">
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
            $('#btnSubirFoto').show();
            $('#btnSubirFoto').removeClass('btn-secondary').addClass('btn-warning');
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

            if (imagenWebp) {
                formData.append('foto', imagenWebp);
            } else if (imagenFile) {
                formData.append('foto', imagenFile);
            }

            formData.append('precio', limpiarNumero($('#precio').val()));
            formData.append('envio', limpiarNumero($('#envio').val()));
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

        $('.money').on('input', function() {
            let limpio = $(this).val()
                .replace(/[^0-9.]/g, '')
                .replace(/(\..*)\./g, '$1');

            $(this).val(limpio);
        });
        $('.money').on('blur', function() {
            let numero = limpiarNumero($(this).val());
            $(this).val(formatearMoneda(numero));
        });

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
                $('#precio, #envio, #total').prop('disabled', true)
                    .addClass('bg-light');

            } else {

                activo.css('opacity', '1');
                cancelado.css('opacity', '0.3');

                // 🔥 desbloquear
                $('#precio, #envio, #total').prop('disabled', false)
                    .removeClass('bg-light');

                // 🔥 recalcular solo al volver
                calcularTotal();
            }

        });

    });
</script>

<?= $this->endSection() ?>