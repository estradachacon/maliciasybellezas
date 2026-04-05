<?= $this->extend('Layouts/mainbody') ?>
<?= $this->section('content') ?>
<?php
$logoUrl = setting('logo')
    ? base_url('upload/settings/' . setting('logo'))
    : '';
?>
<style>
    .select2-container .select2-selection--single {
        height: 38px !important;
        /* altura estándar Bootstrap */
        border: 1px solid #ced4da;
        border-radius: .375rem;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 36px !important;
        /* centra texto */
        padding-left: .75rem;
    }

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
.grid-valores {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 6px;
}

.grid-valores .item {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 6px;
    text-align: center;
    border: 1px solid #eee;
}

.grid-valores .item small {
    display: block;
    font-size: 11px;
    color: #6c757d;
}

.grid-valores .item span {
    font-weight: bold;
    font-size: 14px;
}

.grid-valores .item.total {
    background: #e9f7ef;
    border-color: #c3e6cb;
}
    #previewCard {
        transform: scale(1.5);
    }

    /* ===== ETIQUETA ===== */
    .etiqueta {
        border: 1px solid #000;
        font-family: Arial, sans-serif;
        font-size: 8px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 2px;
        position: relative;
    }

    #modalImagen .modal-content {
        border-radius: 12px;
    }

    #tituloImagen {
        font-size: 14px;
        padding: 6px 10px;
    }

    #modalImagen .modal-header {
        background: #f8f9fa;
    }

    #imagenGrande {
        max-height: 70vh;
        object-fit: contain;
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
        justify-content: space-between;
        gap: 10px;
        padding: 6px 10px;
        border-radius: 10px;
        background: #f8f9fa;
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
            max-width: 400px;
            width: 100%;
            overflow: hidden;
        }

        #miniPreview .etiqueta {
            max-width: 400px;
            width: 100%;
            height: auto;
            transform: none;
        }

        .switch-container {
            justify-content: center;
            flex-wrap: wrap;
            text-align: center;
        }

        .producto-nombre {
            white-space: normal;
            /* permite múltiples líneas */
            word-break: break-word;
            /* corta palabras largas */
            line-height: 1.2;
            /* más compacto */
        }

        .producto-info {
            padding-right: 45px;
            min-width: 0; /* 🔥 ESTA ES LA MAGIA */
            /* espacio para botón */
        }
        #listaProductos .producto-nombre {
            text-indent: 25px;
        }
    }

    #miniPreview {
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

    #listaProductos .card {
        border: none;
        border-left: 4px solid #0d6efd;
        border-radius: 7px;
        background: linear-gradient(80deg, #f4ececd5, #f8f9fa);
        box-shadow: 1 2px 6px rgba(0, 0, 0, 0.05);
    }

    #listaProductos .card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        transform: translateY(-1px);
    }

    #listaProductos .card-body {
        padding: 3px 8px;
        /* 🔥 más compacto */
    }
    #listaProductos .card {
        position: relative;
        overflow: visible; /* 🔥 importante para que la imagen se salga */
    }
    #listaProductos .row {
        margin-top: 4px !important;
    }

    #listaProductos small {
        font-size: 16px;
    }

    .btn-eliminar {
        position: absolute;
        top: 4px;
        right: 4px;
        width: 22px;
        height: 22px;
        padding: 0;
        font-size: 14px;
        border-radius: 50%;
        line-height: 20px;
    }

    /* ITEM CONTENEDOR */
    .producto-item {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* IMAGEN */
    .producto-img {
        width: 55px;
        height: 55px;
        object-fit: cover;
        border-radius: 8px;
    }

    /* TEXTO */
    .producto-nombre {
        font-weight: 600;
        color: #000;
    }

    .producto-precio {
        color: #666;
    }

    /* 🔥 CUANDO ESTÁ SELECCIONADO (hover azul de select2) */
    .select2-results__option--highlighted .producto-nombre,
    .select2-results__option--highlighted .producto-precio {
        color: #fff !important;
    }

    /* opcional: suavizar fondo */
    .select2-results__option--highlighted {
        background-color: #0d6efd !important;
    }

.img-producto-mini {
    position: absolute;
    left: -50px; /* 🔥 se sale hacia la izquierda */
    top: 50%;
    transform: translateY(-50%);
    
    width: 55px;
    height: 55px;
    
    object-fit: cover;
    border-radius: 8px;
    border: 2px solid #fff;
    box-shadow: 0 2px 6px rgba(0,0,0,0.15);
}

    .img-producto-mini:hover {
        transform: scale(1.05);
        border-color: #0d6efd;
    }
</style>

<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm">

            <div class="card-header bg-primary text-white">
                <h5 class="mb-0 fw-bold">📦 Nuevo envío de Paquete</h5>
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
                            <label>Teléfono (8 a 15 dígitos)</label>
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
                            <div class="d-flex gap-2">
                                <select id="encomendista_id" name="encomendista_id" class="form-control" required></select>
                            </div>
                        </div>
                        <div class="col-md-12 mt-3">
                            <div class="card shadow-sm border">
                                <div class="card-header bg-light fw-bold">
                                    🛒 Productos del paquete
                                </div>

                                <div class="card-body">

                                    <div class="row g-2 mb-2">
                                        <div class="col-md-4">
                                            <label>Producto</label>
                                            <select id="producto_id" class="form-control"></select>
                                        </div>

                                        <div class="col-md-2">
                                            <label>Cantidad</label>
                                            <input type="number" id="cantidad" class="form-control" min="1" value="1">
                                        </div>

                                        <div class="col-md-2">
                                            <label>Precio</label>
                                            <input type="text" id="precio" class="form-control">
                                        </div>
                                        <div class="col-md-2">
                                            <label>Descuento en ítem</label>
                                            <input type="text" id="descuento_item" class="form-control" value="0">
                                        </div>

                                        <div class="col-md-2 align-items-end">
                                            <label></label>
                                            <button type="button" id="btnAgregarProducto" class="btn btn-primary w-100">
                                                + Agregar
                                            </button>
                                        </div>
                                    </div>

                                    <div id="listaProductos" class="mt-2"></div>
                                    <div class="row mt-3 ml-1 g-2 mt-2">
                                        <div class="col-md-4">
                                            <label>Descuento global</label>
                                            <input type="text" id="descuento_global" class="form-control" value="0">
                                        </div>

                                        <div class="col-md-4">
                                            <label>Envío</label>
                                            <input type="text" id="envio" class="form-control" value="0">
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 mt-3">
                            <div class="p-3 border rounded-lg bg-light">
                                <div class="row g-2">

                                    <!-- 💰 TOTAL FINAL -->
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Total a remunerar</label>
                                        <input type="text" name="total" id="total"
                                            class="form-control bg-light fw-bold"
                                            style="font-size:18px; text-align:right;"
                                            readonly>
                                    </div>

                                    <!-- 🧾 TOTAL REAL -->
                                    <div class="col-md-6" id="contenedorTotalReal" style="display:none;">
                                        <label class="form-label fw-bold text-muted">Total real</label>
                                        <input type="text" name="total_real" id="total_real"
                                            class="form-control bg-white fw-bold text-muted"
                                            style="font-size:18px; text-align:right;"
                                            readonly>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="row mb-2 mt-2">

                            <!-- 🔹 REMUNERAR -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Remunerar paquete</label>

                                <div class="switch-container justify-content-between">

                                    <span id="estadoTexto" class="estado activo">Cobrar total</span>

                                    <label class="switch">
                                        <input type="checkbox" id="cancelado">
                                        <span class="slider"></span>
                                    </label>

                                    <span class="estado cancelado">Cancelado</span>

                                </div>
                            </div>

                            <!-- 🔹 CUENTA -->
                            <div class="col-md-6 mt-2" id="contenedorCuenta" style="display:none;">
                                <label class="form-label fw-bold">Cuenta destino</label>
                                <select id="cuenta_id" class="form-control"></select>
                            </div>

                            <!-- 🔹 TIPO DE VENTA -->
                            <div class="col-md-6 mt-3">
                                <label class="form-label fw-bold">Tipo de venta</label>

                                <div class="switch-container justify-content-between">

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

            <!-- VOLVER ARRIBA -->
            <div class="d-flex justify-content-start mb-2">
                <button id="btnVolver" class="btn btn-outline-secondary">
                    Volver
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
        <div class="modal-content shadow">

            <!-- 🔥 HEADER -->
            <div class="modal-header">

                <span id="tituloImagen" class="badge badge-primary">
                    Producto
                </span>

                <!-- ✅ BOTÓN CORRECTO -->
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>

            </div>

            <!-- 🖼️ IMAGEN -->
            <div class="modal-body text-center">
                <img id="imagenGrande" class="img-fluid rounded shadow-sm">
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="modalEncomendista" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5>Nuevo Encomendista</h5>
            </div>
            <div class="modal-body">
                <input type="text" id="nuevoEncomendista" class="form-control" placeholder="Nombre">
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button class="btn btn-success" id="guardarEncomendista">Guardar</button>
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
        let productos = [];
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
            let encomendista = $('#encomendista_id').val();
            let total = limpiarNumero($('#total').val());
            let cancelado = $('#cancelado').is(':checked');

            // 🔥 VALIDACIONES
            if (!cliente) errores.push('Debe ingresar el cliente');

            if (!telefono) {
                errores.push('Debe ingresar el teléfono');
            } else if (!/^\d{8,15}$/.test(telefono)) {
                errores.push('El teléfono debe tener 8 dígitos');
            }

            if (!fecha) errores.push('Debe seleccionar fecha de entrega');

            if (!destino) errores.push('Debe ingresar destino');

            if (!encomendista) errores.push('Debe seleccionar encomendista');

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

            if (val.length >= 8 && val.length <= 15) {
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

        function formatProducto(producto) {

            if (!producto.id) return producto.text;

            let foto = producto.imagen ?
                `<?= base_url('upload/productos/') ?>/${producto.imagen}` :
                'https://via.placeholder.com/55';

            let stock = producto.stock ?? 0;

            return $(`
        <div class="producto-item">
            <img src="${foto}" class="producto-img">
            
            <div class="producto-info w-100">
                <div class="producto-nombre">${producto.text}</div>

                <div class="d-flex justify-content-between mt-1">
                    <small class="producto-precio">$${producto.precio || '0.00'}</small>
                    
                    <span style="
                        font-size:16px;
                        font-weight:bold;
                        padding:2px 8px;
                        border-radius:6px;
                        background:${stock > 0 ? '#e9f7ef' : '#fdecea'};
                        color:${stock > 0 ? '#198754' : '#dc3545'};
                    ">
                        STOCK: ${stock}
                    </span>
                </div>
            </div>
        </div>
    `);
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
            let textoEncomendista = $('#encomendista_id option:selected').text();

            let data = $('#formPaquete').serialize() +
                '&encomendista_nombre=' + encodeURIComponent(textoEncomendista);

            window.open("<?= base_url('paquetes/etiqueta') ?>?" + data, '_blank');

            //MiniPreview
            $('#miniPreview').html(`
            <div>

                <div class="etiqueta">
                        <table style="width:100%;">
                <tr>
                    <!-- LOGO + QR -->
                <td style="width:30%; text-align:center; vertical-align:top;">
                    ${logo ? `
                        <img src="${logo}" style="width:100%; max-height:80px; object-fit:contain; transform: scale(1.1);">
                    ` : ``}
                </td>
                <!-- CONTENIDO -->
                <td style="width:80%; padding-left:5px;">

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
                                <b>Encomend:</b> ${textoEncomendista || '—'}
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

            let payload = construirPayload();

            let formData = new FormData();

            // 🔥 mandar TODO el objeto
            formData.append('data', JSON.stringify(payload));

            if (imagenWebp) {
                formData.append('foto', imagenWebp);
            } else if (imagenFile) {
                formData.append('foto', imagenFile);
            }

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

        window.eliminarProducto = function(index) {
            productos.splice(index, 1);
            renderTabla();
            calcularTotalProductos();
        }

        function renderTabla() {

            let html = '';

            productos.forEach((p, i) => {

                let subtotal = (p.cantidad * p.precio) - p.descuento;

                let foto = p.imagen ?
                    `<?= base_url('upload/productos/') ?>/${p.imagen}` :
                    'https://via.placeholder.com/60';

                html += `
                <div class="card mb-2 shadow-sm border-0 position-relative">

                    <!-- 🔢 INDEX -->
                    <span class="badge bg-primary text-white position-absolute"
                        style="top:5px; left:5px;">
                        ${i + 1}
                    </span>

                    <button type="button"
                        class="btn btn-danger btn-sm btn-eliminar btn-delete"
                        data-index="${i}">
                        ×
                    </button>

                    <div class="card-body p-2">

                        <div class="d-flex gap-2">

                            <!-- 🖼️ IMAGEN -->
                            <img src="${foto}" 
                                class="img-producto-mini"
                                onclick="verImagen('${foto}', ${i + 1}, ${p.cantidad}, ${p.precio})">

                            <!-- 📦 INFO -->
                            <div class="flex-grow-1 producto-info">

                                <div class="fw-semibold producto-nombre">
                                    ${p.nombre}
                                </div>

                                <div class="grid-valores mt-2">
                                    
                                    <div class="item">
                                        <small>Cant</small>
                                        <span>${p.cantidad}</span>
                                    </div>

                                    <div class="item">
                                        <small>Precio</small>
                                        <span>$${formatearMoneda(p.precio)}</span>
                                    </div>

                                    <div class="item">
                                        <small>Desc</small>
                                        <span>$${formatearMoneda(p.descuento)}</span>
                                    </div>

                                    <div class="item total">
                                        <small>Sub</small>
                                        <span>$${formatearMoneda(subtotal)}</span>
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>
                </div>
                `;
            });

            $('#listaProductos').html(html);
        }

        function construirPayload() {

            let subtotal = productos.reduce((sum, p) => {
                return sum + ((p.cantidad * p.precio) - p.descuento);
            }, 0);

            let descuentoGlobal = limpiarNumero($('#descuento_global').val());
            let envio = limpiarNumero($('#envio').val());

            let totalReal = subtotal - descuentoGlobal + envio;
            if (totalReal < 0) totalReal = 0;

            let cancelado = $('#cancelado').is(':checked');
            let totalFinal = cancelado ? 0 : totalReal;

            // 🔥 PRODUCTOS CON SUBTOTAL
            let productosDetalle = productos.map(p => ({
                producto_id: p.producto_id,
                branch_id: p.branch_id, // 🔥 NUEVO
                nombre: p.nombre,
                cantidad: p.cantidad,
                precio: p.precio,
                descuento: p.descuento,
                subtotal: (p.cantidad * p.precio) - p.descuento
            }));

            return {
                cliente: {
                    nombre: $('[name="cliente_nombre"]').val().trim(),
                    telefono: $('[name="cliente_telefono"]').val().trim()
                },
                entrega: {
                    fecha: $('[name="dia_entrega"]').val(),
                    hora_inicio: $('[name="hora_inicio"]').val(),
                    hora_fin: $('[name="hora_fin"]').val(),
                    destino: $('[name="destino"]').val().trim()
                },
                operacion: {
                    encomendista_id: $('#encomendista_id').val(),
                    tipo_venta: $('#tipo_venta').is(':checked') ? 'mayoreo' : 'detalle',
                    codigoqr: $('#codigoqr').val()
                },
                productos: productosDetalle,
                totales: {
                    subtotal: subtotal,
                    descuento_global: descuentoGlobal,
                    envio: envio,
                    total_real: totalReal,
                    total_remunerar: totalFinal
                },
                pago: {
                    cancelado: cancelado,
                    cuenta_id: cancelado ? $('#cuenta_id').val() : null
                }
            };
        }
        window.verImagen = function(src, index, cantidad, precio) {

            $('#imagenGrande').attr('src', src);

            let texto = `#${index} • ${cantidad} und • $${precio.toFixed(2)}`;
            $('#tituloImagen').text(texto);

            // ✅ Bootstrap 4
            $('#modalImagen').modal('show');
        }

        function calcularTotalProductos() {

            let subtotal = productos.reduce((sum, p) => {
                return sum + ((p.cantidad * p.precio) - p.descuento);
            }, 0);

            let descuentoGlobal = limpiarNumero($('#descuento_global').val());
            let envio = limpiarNumero($('#envio').val());

            let totalReal = subtotal - descuentoGlobal + envio;

            if (totalReal < 0) totalReal = 0;

            let cancelado = $('#cancelado').is(':checked');

            let totalFinal = cancelado ? 0 : totalReal;

            // 💰 lo que se cobra
            $('#total').val(formatearMoneda(totalFinal));

            // 🧾 lo real (siempre visible)
            $('#total_real').val(formatearMoneda(totalReal));
        }

        // =========================
        // CANCELADO
        // =========================
        $('#cancelado').change(function() {

            let container = $(this).closest('.switch-container');

            let activo = container.find('.estado.activo');
            let cancelado = container.find('.estado.cancelado');

            if (this.checked) {

                activo.css('opacity', '0.3');
                cancelado.css('opacity', '1');

                $('#total').val('0.00');

                // ✅ mostrar cuenta
                $('#contenedorCuenta').slideDown(150);

                // 🔥 mostrar total real
                $('#contenedorTotalReal').slideDown(150);

                setTimeout(() => {
                    $('#cuenta_id').select2('open');
                }, 200);

            } else {

                activo.css('opacity', '1');
                cancelado.css('opacity', '0.3');

                calcularTotalProductos();

                // ocultar cuenta
                $('#contenedorCuenta').slideDown(150);

                $('#cuenta_id').val(null).trigger('change');

                // 🔥 ocultar total real
                $('#contenedorTotalReal').slideUp(150);
            }

        });

        $(document).on('click', '.btn-delete', function() {
            let index = $(this).data('index');
            productos.splice(index, 1);
            renderTabla();
            calcularTotalProductos();
        });

        $('#cuenta_id').select2({
            placeholder: 'Buscar cuenta...',
            dropdownParent: $('#contenedorCuenta'),
            width: '100%',
            ajax: {
                url: '<?= base_url('accounts-listAjax') ?>',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        term: params.term
                    };
                },
                processResults: function(data) {
                    return {
                        results: data
                    };
                }
            }
        });
        $('#encomendista_id').select2({
            language: 'es',
            minimumInputLength: 2,
            placeholder: 'Buscar encomendista...',
            width: '100%',
            ajax: {
                url: '<?= base_url('encomendistas-buscar') ?>',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        term: params.term
                    };
                },
                processResults: function(data) {

                    let results = data;

                    let term = $('.select2-search__field').val();

                    // 🔥 SOLO SI escribió algo
                    if (results.length === 0 && term && term.length >= 2) {
                        results.push({
                            id: '__new__',
                            text: `➕ Crear "${term}"`,
                            newTag: true
                        });
                    }

                    return {
                        results
                    };
                }
            }
        });
        $('#encomendista_id').on('select2:select', function(e) {

            let data = e.params.data;

            if (data.id === '__new__') {

                let term = $('.select2-search__field').val();

                // 🔥 abrir modal con nombre precargado
                $('#nuevoEncomendista').val(term);
                $('#modalEncomendista').modal('show');

                // 🔥 limpiar selección fake
                $('#encomendista_id').val(null).trigger('change');
            }

        });
        $('#btnNuevoEncomendista').click(function() {
            $('#nuevoEncomendista').val('');
            $('#modalEncomendista').modal('show');
        });

        $('#guardarEncomendista').click(function() {

            let nombre = $('#nuevoEncomendista').val().trim();

            if (!nombre) {
                Swal.fire('Error', 'Ingrese nombre', 'warning');
                return;
            }

            $.post('<?= base_url('encomendistas-create-ajax') ?>', {
                encomendista_name: nombre
            }, function(res) {

                if (res.status === 'success') {

                    let newOption = new Option(res.data.text, res.data.id, true, true);
                    $('#encomendista_id').append(newOption).trigger('change');

                    $('#modalEncomendista').modal('hide');

                } else {
                    Swal.fire('Error', res.message, 'error');
                }

            }, 'json');

        });

        $('#producto_id').select2({
            language: 'es',
            placeholder: 'Buscar producto...',
            width: '100%',
            minimumInputLength: 1,

            templateResult: formatProducto, // 🔥 lista con imagen
            templateSelection: formatProductoSeleccion, // 🔥 input limpio

            escapeMarkup: function(markup) {
                return markup;
            },

            ajax: {
                url: '<?= base_url('productos/searchAjaxSelectStock') ?>',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        term: params.term
                    };
                },
                processResults: function(data) {
                    return {
                        results: data
                    };
                }
            }
        });

        function formatProductoSeleccion(producto) {

            if (!producto.id) return producto.text;

            let stock = producto.stock ?? 0;

            return `
        <div style="display:flex; justify-content:space-between; align-items:center; width:100%;">
            
            <span style="font-weight:600; color:#000;">
                ${producto.text}
            </span>

            <span style="
                font-size:14px;
                font-weight:bold;
                color:${stock > 0 ? '#198754' : '#dc3545'};
                margin-left:10px;
            ">
                ${stock}
            </span>

        </div>
    `;
        }
        $('#tipo_venta').change(function() {

            let container = $(this).closest('.switch-container');

            let activo = container.find('.estado.activo');
            let cancelado = container.find('.estado.cancelado');

            if (this.checked) {
                activo.css('opacity', '0.3');
                cancelado.css('opacity', '1');
            } else {
                activo.css('opacity', '1');
                cancelado.css('opacity', '0.3');
            }

        });

        $('#producto_id').on('select2:select', function(e) {
            let data = e.params.data;

            $('#precio').val(data.precio || '0.00');

            // 🔥 NUEVO
            $('#producto_id').data('stock', data.stock || 0);
            $('#producto_id').data('branch_id', data.branch_id || null);
            $('#producto_id').data('producto_id_real', data.producto_id || data.id);

            $('#cantidad').focus();
        });

        $('#descuento_global, #envio').on('input', function() {
            calcularTotalProductos();
        });

        if (this.checked) {
            $('#total_real').addClass('text-danger');
        } else {
            $('#total_real').removeClass('text-danger');
        }

        $('#cuenta_id').select2({
            placeholder: 'Buscar cuenta...',
            width: '100%',
            ajax: {
                url: '<?= base_url('accounts-listAjax') ?>',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        term: params.term
                    };
                },
                processResults: function(data) {
                    return {
                        results: data
                    };
                }
            }
        });

        $('#btnAgregarProducto').click(function() {

            let productoSelect = $('#producto_id').select2('data')[0];
            let cantidad = parseFloat($('#cantidad').val());
            let precio = limpiarNumero($('#precio').val());

            let stock = $('#producto_id').data('stock') || 0;
            let branch_id = $('#producto_id').data('branch_id');
            let producto_id_real = $('#producto_id').data('producto_id_real');

            if (!productoSelect) {
                Swal.fire('Error', 'Seleccione un producto', 'warning');
                return;
            }

            if (cantidad <= 0) {
                Swal.fire('Error', 'Cantidad inválida', 'warning');
                return;
            }

            if (cantidad > stock) {
                Swal.fire(
                    'Stock insuficiente',
                    `Solo hay ${stock} unidades disponibles`,
                    'warning'
                );
                return;
            }

            if (precio <= 0) {
                Swal.fire('Error', 'Precio inválido', 'warning');
                return;
            }

            let descuento = limpiarNumero($('#descuento_item').val());

            if (!branch_id) {
                Swal.fire('Error', 'El producto no tiene sucursal asignada', 'error');
                return;
            }

            let producto = {
                producto_id: producto_id_real,
                branch_id: branch_id,
                nombre: productoSelect.text,
                cantidad: cantidad,
                precio: precio,
                descuento: descuento,
                imagen: productoSelect.imagen || null,
                stock: stock
            };

            // 🔥 BUSCAR EXISTENTE (IMPORTANTE: producto + sucursal)
            let existente = productos.find(p =>
                p.producto_id == producto.producto_id &&
                p.branch_id == producto.branch_id
            );

            if (existente) {

                let nuevaCantidad = existente.cantidad + cantidad;

                if (nuevaCantidad > stock) {
                    Swal.fire(
                        'Stock insuficiente',
                        `Ya tienes ${existente.cantidad} en lista y solo hay ${stock}`,
                        'warning'
                    );
                    return;
                }

                existente.cantidad = nuevaCantidad;
                existente.descuento += descuento;

            } else {
                productos.push(producto);
            }

            renderTabla();
            calcularTotalProductos();

            // limpiar
            $('#producto_id').val(null).trigger('change');
            $('#cantidad').val(1);
            $('#precio').val('');
            $('#descuento_item').val('0');
        });
    });
</script>
<?= $this->endSection() ?>