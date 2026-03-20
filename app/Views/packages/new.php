<?= $this->extend('Layouts/mainbody') ?>
<?= $this->section('content') ?>

<style>
    #overlayPreview {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.7);
        z-index: 2000;
        display: none;
        align-items: center;
        justify-content: center;
    }

    #previewCard {
        background: white;
        padding: 20px;
        border-radius: 10px;
        width: 320px;
    }

    @media print {
        body * {
            visibility: hidden;
        }

        #printArea, #printArea * {
            visibility: visible;
        }

        #printArea {
            width: 4in;
            height: 2in;
        }
    }
</style>

<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm">

            <div class="card-header bg-primary text-white d-flex justify-content-between">
                <h5 class="mb-0">Nuevo paquete rápido</h5>
                <a href="<?= base_url('packages') ?>" class="btn btn-light btn-sm">Volver</a>
            </div>

            <div class="card-body">

                <!-- FORMULARIO (FASE 1) -->
                    <form id="formPaquete">

                    <div class="row g-3">

                        <!-- VENDEDOR -->
                        <div class="col-md-6">
                            <label>Vendedor</label>
                            <select id="seller_id" name="seller_id" class="form-select"></select>
                        </div>

                        <!-- CLIENTE -->
                        <div class="col-md-6">
                            <label>Cliente</label>
                            <input type="text" name="cliente_nombre" class="form-control">
                        </div>

                        <!-- TEL -->
                        <div class="col-md-6">
                            <label>Teléfono</label>
                            <input type="text" name="cliente_telefono" class="form-control">
                        </div>

                        <!-- DIA -->
                        <div class="col-md-6">
                            <label>Día de entrega</label>
                            <input type="date" name="dia_entrega" id="dia_entrega" class="form-control">
                        </div>

                        <!-- DESTINO -->
                        <div class="col-md-12">
                            <label>Destino</label>
                            <input type="text" name="destino" class="form-control">
                        </div>

                        <!-- ENCOMENDISTA -->
                        <div class="col-md-12">
                            <label>Encomendista</label>
                            <input type="text" name="encomendista_nombre" class="form-control">
                        </div>

                        <!-- PRECIOS -->
                        <div class="col-md-4">
                            <input type="number" name="precio" placeholder="Precio" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <input type="number" name="envio" placeholder="Envío" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <input type="number" name="total" id="total" placeholder="Total" class="form-control" required>
                        </div>

                        <!-- CANCELADO -->
                        <div class="col-md-12">
                            <div class="form-check">
                                <input type="checkbox" id="cancelado" class="form-check-input">
                                <label class="form-check-label">Paquete cancelado</label>
                            </div>
                        </div>

                        <div class="col-md-12 text-end">
                            <button type="button" id="btnGuardar" class="btn btn-success">
                                Guardar paquete
                            </button>
                        </div>

                    </div>

                </form>

            </div>
        </div>
    </div>
</div>

<!-- ========================= -->
<!-- OVERLAY (FASE 2) -->
<!-- ========================= -->

<div id="overlayPreview">
    <div id="previewCard">

        <div id="printArea"></div>

        <input type="file" id="foto" accept="image/*" capture="environment" class="form-control mt-2">

        <button id="btnPrint" class="btn btn-secondary w-100 mt-2">🖨️ Imprimir</button>
        <button id="btnFinalizar" class="btn btn-success w-100 mt-2">Finalizar</button>

    </div>
</div>

<!-- ========================= -->
<!-- JS -->
<!-- ========================= -->

<script>
document.addEventListener('DOMContentLoaded', function(){

    // SELECT2 vendedor
    $('#seller_id').select2({
        theme: 'bootstrap4',
        ajax: {
            url: '<?= base_url('sellers-search') ?>',
            dataType: 'json',
            delay: 250,
            data: params => ({ q: params.term }),
            processResults: data => ({ results: data })
        }
    });

    // CANCELADO
    $('#cancelado').change(function(){
        $('#total').prop('disabled', this.checked);
    });

    // GUARDAR AJAX
    $('#btnGuardar').click(function(){

        let formData = $('#formPaquete').serialize();

        $.post('<?= base_url('packages/store') ?>', formData, function(res){

            if(res.status === 'ok'){
                mostrarPreview(res.paquete);
            }

        }, 'json');

    });

    function mostrarPreview(p){

        $('#overlayPreview').css('display','flex');

        let fecha = new Date(p.dia_entrega);
        let dia = fecha.toLocaleDateString('es-SV', {
            weekday: 'long',
            day: 'numeric'
        });

        $('#printArea').html(`
            <strong>Cliente:</strong> ${p.cliente_nombre}<br>
            ${p.cliente_telefono}<br>
            ${p.destino}<br>
            ${dia}<br>
            <strong>Total: $${p.total}</strong>
        `);
    }

    // IMPRIMIR
    $('#btnPrint').click(function(){
        window.print();
    });

    // FINALIZAR
    $('#btnFinalizar').click(function(){
        location.reload();
    });

});
</script>

<?= $this->endSection() ?>