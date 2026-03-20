<?= $this->extend('Layouts/mainbody') ?>
<?= $this->section('content') ?>

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
</style>

<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm">

            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Nuevo paquete</h5>
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

                        <div class="col-md-4">
                            <input type="number" name="precio" placeholder="Precio" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <input type="number" name="envio" placeholder="Envío" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <input type="number" name="total" id="total" placeholder="Total" class="form-control">
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

<!-- OVERLAY -->
<div id="overlayPreview">
    <div id="previewCard">
        <?php
        $favicon = setting('favicon');
        if ($favicon && file_exists(FCPATH . 'upload/settings/' . $favicon)) {
            $faviconUrl = base_url('upload/settings/' . $favicon);
        } else {
            $faviconUrl = base_url('favicon.ico');
        }
        ?>

        <!-- VIÑETA -->
        <div id="printArea" class="etiqueta">

            <div class="contenedor">

                <!-- LOGO -->
                <div class="col-logo">
                    <img src="<?= $faviconUrl ?>" class="logo" id="logoPrint">
                </div>

                <!-- DATOS -->
                <div class="col-data">

                    <div class="empresa">MALICIAS Y BELLEZAS</div>

                    <div class="fila">
                        <b>CLIENTE:</b> <span id="p_cliente"></span>
                    </div>

                    <div class="fila">
                        <b>TEL:</b> <span id="p_tel"></span>
                    </div>

                    <div class="fila">
                        <b>DESTINO:</b> <span id="p_destino"></span>
                    </div>

                    <div class="fila">
                        <b>ENTREGA:</b> <span id="p_fecha"></span>
                    </div>

                    <div class="fila">
                        <b>ENCOM:</b> <span id="p_encomendista"></span>
                    </div>

                </div>

            </div>

            <!-- FOOTER -->
            <div class="footer">
                <div class="box">
                    <div class="titulo">PRECIO</div>
                    <div>$<span id="p_precio"></span></div>
                </div>

                <div class="box">
                    <div class="titulo">ENVÍO</div>
                    <div>$<span id="p_envio"></span></div>
                </div>

                <div class="box total">
                    <div class="titulo">TOTAL</div>
                    <div>$<span id="p_total"></span></div>
                </div>
            </div>

        </div>

        <button id="btnPrint" class="btn btn-secondary w-100 mt-2">Imprimir</button>
        <button id="btnEditar" class="btn btn-warning w-100 mt-2">Editar</button>
        <button id="btnFinalizar" class="btn btn-success w-100 mt-2">Finalizar</button>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        $('#cancelado').change(function() {
            $('#total').prop('disabled', this.checked);
        });

        $('#btnGuardar').click(function() {

            let data = $('#formPaquete').serializeArray();
            let obj = {};
            data.forEach(x => obj[x.name] = x.value);

            bloquearFormulario();
            mostrarPreview(obj);

        });

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

    });
</script>

<?= $this->endSection() ?>