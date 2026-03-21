<?= $this->extend('Layouts/mainbody') ?>
<?= $this->section('content') ?>
<?php
$faviconUrl = base_url('favicon.ico');
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

                        <div class="col-md-6">
                            <label>Día de entrega</label>
                            <input type="date" name="dia_entrega" class="form-control">
                        </div>

                        <div class="col-md-12">
                            <label>Destino</label>
                            <input type="text" name="destino" class="form-control">
                        </div>

                        <div class="col-md-12">
                            <label>Encomendista</label>
                            <input type="text" name="encomendista_nombre" class="form-control">
                        </div>

                        <div class="col-md-12 mt-2">
                            <div class="p-3 border rounded-3 bg-light">
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
                                        <input type="text" name="total" id="total" class="form-control bg-light fw-bold" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-check">
                                <input type="checkbox" id="cancelado" class="form-check-input">
                                <label class="form-check-label">Cancelado</label>
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

            <!-- MINI PREVIEW -->
            <div style="transform: scale(0.8); transform-origin: top left;">
                <div id="miniPreview"></div>
            </div>
            <button id="btnVolver" class="btn btn-outline-secondary">
                ← Volver
            </button>
            <!-- FOTO -->
            <div class="mt-3">
                <label>Foto del paquete</label>
                <input
                    type="file"
                    id="fotoPaquete"
                    accept="image/*"
                    capture="environment"
                    style="display:none;">

                <button type="button" id="btnCamara" class="btn btn-dark w-100">
                    📷 Tomar foto del paquete
                </button>

                <div class="mt-3 text-center">
                    <img id="previewFoto"
                        class="img-thumbnail shadow-sm"
                        style="max-width: 200px; cursor:pointer; display:none;">
                </div>
            </div>

            <!-- BOTONES -->
            <div class="mt-3 d-flex gap-2">
                <button id="btnImprimirFinal" class="btn btn-secondary">
                    Imprimir
                </button>

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

        $('#cancelado').change(function() {
            if (this.checked) {
                $('#total').val('0.00');
                $('#precio, #envio').prop('disabled', true);
            } else {
                $('#precio, #envio').prop('disabled', false);
                calcularTotal();
            }
        });

        $('#btnImprimirFinal').click(function() {

            let contenido = document.getElementById('miniPreview').innerHTML;

            // detectar Android
            let esAndroid = /Android/i.test(navigator.userAgent);

            if (esAndroid) {

                // 🔥 ANDROID → imprimir directo (SIN popup)
                let original = document.body.innerHTML;

                document.body.innerHTML = contenido;

                window.print();

                document.body.innerHTML = original;

                location.reload();

            } else {

                // PC → usar popup (como ya tenías)
                let ventana = window.open('', '', 'width=400,height=300');

                ventana.document.write(`
            <html>
            <head>
                <title>Imprimir</title>
                <style>
                    body { margin:0; padding:0; }
                    .etiqueta {
                        width: 4in;
                        height: 2in;
                        font-family: Arial;
                    }
                </style>
            </head>
            <body>
                ${contenido}
            </body>
            </html>
        `);

                ventana.document.close();

                ventana.onload = function() {
                    ventana.print();
                    ventana.close();
                };
            }

        });
        $('#btnVolver').click(function() {
            $('#seccionFinal').hide();
            $('#formPaquete').removeClass('blur');
        });
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

                    // aplicar blur
                    $('#formPaquete').addClass('blur');

                    // generar preview
                    generarPreview(obj);

                    // mostrar sección final
                    $('#seccionFinal').fadeIn();
                    $('html, body').animate({
                        scrollTop: $('#seccionFinal').offset().top
                    }, 500);
                }

            });

        });
        $('#btnGuardarFinal').click(function() {

            let file = $('#fotoPaquete')[0].files[0];

            if (!file) {
                Swal.fire('Falta foto', 'Debes subir una foto del paquete', 'warning');
                return;
            }

            let formData = new FormData($('#formPaquete')[0]);
            formData.append('foto', file);

            $.ajax({
                url: '<?= base_url('paquetes/guardar') ?>',
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(res) {
                    Swal.fire('Guardado', 'Paquete registrado correctamente', 'success')
                        .then(() => location.reload());
                }
            });

        });

        function formatearMoneda(valor) {
            let numero = parseFloat(valor.replace(/[^0-9.-]+/g, "")) || 0;
            return numero.toLocaleString('en-US', {
                minimumFractionDigits: 2
            });
        }

        function obtenerNumero(valor) {
            return parseFloat(valor.replace(/,/g, '')) || 0;
        }

        $('.money').on('input', function() {
            let cursor = this.selectionStart;

            let valor = $(this).val();
            let limpio = valor.replace(/[^0-9.]/g, '');

            $(this).val(formatearMoneda(limpio));

            calcularTotal();
        });

        function calcularTotal() {
            let precio = obtenerNumero($('#precio').val());
            let envio = obtenerNumero($('#envio').val());

            let total = precio + envio;

            $('#total').val(formatearMoneda(total.toString()));
        }

        function generarPreview(p) {

            let fecha = new Date(p.dia_entrega);
            let dia = fecha.toLocaleDateString('es-SV', {
                weekday: 'long',
                day: 'numeric'
            });

            $('#miniPreview').html(`
        <div class="etiqueta">
            <div class="contenedor">
                <div class="col-logo">
                    <img src="<?= $faviconUrl ?>" class="logo">
                </div>
                <div class="col-data">
                    <div class="empresa">MALICIAS Y BELLEZAS</div>

                    <div class="fila"><b>CLIENTE:</b> ${p.cliente_nombre || ''}</div>
                    <div class="fila"><b>TEL:</b> ${p.cliente_telefono || ''}</div>
                    <div class="fila"><b>DESTINO:</b> ${p.destino || ''}</div>
                    <div class="fila"><b>ENTREGA:</b> ${dia || ''}</div>
                    <div class="fila"><b>ENCOM:</b> ${p.encomendista_nombre || ''}</div>
                </div>
            </div>

            <div class="footer">
                <div class="box">PRECIO $${p.precio || '0.00'}</div>
                <div class="box">ENVÍO $${p.envio || '0.00'}</div>
                <div class="box total">TOTAL $${p.total || '0.00'}</div>
            </div>
        </div>
    `);
        }

        function bloquearFormulario() {
            $('#formPaquete :input').prop('disabled', true);
        }

        function desbloquearFormulario() {
            $('#formPaquete :input').prop('disabled', false);
        }

        function mostrarPreview(p) {

            $('#overlayPreview').css('display', 'flex');

            let fecha = new Date(p.dia_entrega);
            let dia = fecha.toLocaleDateString('es-SV', {
                weekday: 'long',
                day: 'numeric'
            });

            $('#p_cliente').text(p.cliente_nombre || '');
            $('#p_tel').text(p.cliente_telefono || '');
            $('#p_destino').text(p.destino || '');
            $('#p_fecha').text(dia || '');
            $('#p_encomendista').text(p.encomendista_nombre || '');
            $('#p_precio').text(p.precio || '0.00');
            $('#p_envio').text(p.envio || '0.00');
            $('#p_total').text(p.total || '0.00');
        }

        $('#btnEditar').click(function() {
            $('#overlayPreview').hide();
            desbloquearFormulario();
        });

        $('#btnPrint').click(function() {

            const img = document.getElementById('logoPrint');

            // 🔥 convertir a base64
            const base64Logo = getBase64Image(img);

            // clonar contenido
            let contenido = document.getElementById('printArea').outerHTML;

            // reemplazar src del logo por base64
            contenido = contenido.replace(img.src, base64Logo);

            let ventana = window.open('', '', 'width=400,height=300');

            ventana.document.write(`
        <html>
        <head>
            <title>Imprimir</title>

            <style>
                * { box-sizing: border-box; }

                html, body {
                    margin: 0;
                    padding: 0;
                    width: 4in;
                    height: 2in;
                    overflow: hidden;
                }

                body {
                    display: block;
                }

                /* 🔥 CLAVE: pegar a la esquina */
                #printArea {
                    position: absolute;
                    top: 0;
                    left: 0;
                }

                /* 🔥 micro ajuste (calibración real) */
                #printArea {
                    top: -4px;
                    left: -4px;
                }

                .etiqueta {
                    width: 4in;
                    height: 2in;
                    border: 1px solid #000;
                    font-family: Arial, sans-serif;
                    font-size: 9px;
                    display: flex;
                    flex-direction: column;
                    justify-content: space-between;
                    padding: 3px;
                }

                .contenedor {
                    display: flex;
                    flex: 1;
                }

                .col-logo {
                    width: 32%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    border-right: 1px solid #000;
                }

                .logo {
                    width: 100%;
                    max-height: 80px;
                    object-fit: contain;
                }

                .col-data {
                    width: 68%;
                    padding-left: 4px;
                    display: flex;
                    flex-direction: column;
                    justify-content: space-between;
                }

                .empresa {
                    font-weight: bold;
                    font-size: 10px;
                    border-bottom: 1px solid #000;
                }

                .fila {
                    display: flex;
                    justify-content: space-between;
                    border-bottom: 1px dotted #ccc;
                    line-height: 1.2;
                }

                .footer {
                    display: flex;
                    border-top: 1px solid #000;
                }

                .box {
                    flex: 1;
                    text-align: center;
                    font-size: 8px;
                }

                .titulo {
                    font-weight: bold;
                    font-size: 7px;
                }

                .total {
                    font-weight: bold;
                    font-size: 9px;
                }

                @page {
                    size: 4in 2in;
                    margin: 0;
                }
            </style>

        </head>

        <body>
            ${contenido}
        </body>
        </html>
    `);

            ventana.document.close();

            ventana.onload = function() {
                setTimeout(() => {
                    ventana.print();
                    ventana.close();
                }, 200);
            };

        });

        function getBase64Image(img) {
            const canvas = document.createElement("canvas");
            const ctx = canvas.getContext("2d");

            canvas.width = img.naturalWidth;
            canvas.height = img.naturalHeight;

            ctx.drawImage(img, 0, 0);

            return canvas.toDataURL("image/png");
        }

        $('#btnFinalizar').click(function() {
            location.reload();
        });
        let imagenWebp = null;

        $('#btnCamara').click(function() {
            $('#fotoPaquete').click();
        });

        $('#fotoPaquete').change(function(e) {

            let file = e.target.files[0];
            if (!file) return;

            if (!file.type.startsWith('image/')) {
                Swal.fire('Error', 'Solo imágenes', 'error');
                return;
            }

            let reader = new FileReader();

            reader.onload = function(ev) {

                let img = new Image();

                img.onload = function() {

                    // 🔥 REDIMENSIONAR (máx 800px)
                    let maxWidth = 800;
                    let scale = maxWidth / img.width;

                    let canvas = document.createElement('canvas');
                    canvas.width = maxWidth;
                    canvas.height = img.height * scale;

                    let ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, canvas.width, canvas.height);

                    // 🔥 CONVERTIR A WEBP (calidad 0.7)
                    canvas.toBlob(function(blob) {

                        imagenWebp = new File([blob], "foto.webp", {
                            type: "image/webp"
                        });

                        // 🔥 PREVIEW
                        let url = URL.createObjectURL(blob);

                        $('#previewFoto')
                            .attr('src', url)
                            .show();

                    }, 'image/webp', 0.7);

                };

                img.src = ev.target.result;
            };

            reader.readAsDataURL(file);
        });
        $('#previewFoto').click(function() {
    $('#imagenGrande').attr('src', $(this).attr('src'));
    $('#modalImagen').modal('show');
});
    });
</script>

<?= $this->endSection() ?>