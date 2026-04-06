<?= $this->extend('Layouts/mainbody') ?>
<?= $this->section('content') ?>

<style>
    body {
        background: #f5f6fa;
    }

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

    .card {
        border-radius: 15px;
        border: none;
    }

    .grupo {
        margin-bottom: 15px;
    }

    .grupo-header {
        background: #212529;
        color: #fff;
        padding: 10px;
        border-radius: 10px;
        font-weight: 600;
    }

    .paquete {
        cursor: pointer;
        transition:
            transform 0.18s cubic-bezier(0.22, 1, 0.36, 1),
            box-shadow 0.18s ease,
            background-color 0.18s ease,
            border 0.12s ease;
        will-change: transform;
    }

    .paquete:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
    }

    .paquete.activo {
        background: #e9f7ef;
        transform: scale(0.97);
        box-shadow: 
            0 0 0 2px #28a745,   /* simula border */
            0 6px 14px rgba(0,0,0,0.12);
    }

    .item-paquete {
        background: #fff;
        border-radius: 14px;
        padding: 12px 14px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.06);
        margin-top: 8px;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    /* TOP */
    .item-top {
        display: flex;
        flex-direction: column;
    }

    .item-cliente {
        font-weight: 600;
        font-size: 15px;
    }

    .item-destino {
        font-size: 12px;
        color: #6c757d;
    }

    /* BOTTOM */
    .item-bottom {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* PRECIO */
    .item-valor {
        font-weight: 700;
        font-size: 18px;
        color: #28a745;
    }

    .item-left {
        display: flex;
        flex-direction: column;
    }

    .resumen {
        display: flex;
        gap: 12px;
        margin-bottom: 15px;
    }

    .resumen div {
        flex: 1;
        background: white;
        padding: 14px 10px;
        border-radius: 12px;
        text-align: center;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.05);
    }

    .resumen small {
        display: block;
        font-size: 12px;
        color: #6c757d;
        margin-bottom: 4px;
    }

    .resumen strong {
        font-size: 22px;
        font-weight: 700;
    }

    .swal2-image {
        max-height: 80vh;
        object-fit: contain;
        border-radius: 10px;
    }
</style>

<div class="row">

    <!-- IZQUIERDA -->
    <div class="col-md-8">

        <div class="card shadow-sm mb-3">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Remunerar paquetes</h5>
            </div>

            <div class="card-body">

                <!-- RESUMEN -->
                <div class="resumen">
                    <div>
                        <h5>Paquetes Seleccionados</h5><br>
                        <strong id="totalPaquetes">0</strong>
                    </div>
                    <div>
                        <h5 class="mt-4">Total $</h5><br>
                        <strong id="totalDinero">$0.00</strong>
                    </div>
                </div>

                <!-- LISTA -->
                <?php if (empty($agrupados)): ?>
                    <div class="text-center text-muted">
                        No hay paquetes pendientes
                    </div>
                <?php endif; ?>

                <?php foreach ($agrupados as $grupo): ?>

                    <div class="grupo">

                        <div class="grupo-header">
                            <?= esc($grupo['encomendista']) ?>
                        </div>

                        <?php foreach ($grupo['items'] as $p): ?>

                            <div class="item-paquete paquete"
                                data-id="<?= $p->id ?>"
                                data-valor="<?= $p->total ?>"
                                data-encomendista="<?= esc($grupo['encomendista']) ?>">

                                <div class="item-top">
                                    <div class="item-cliente">
                                        <?= esc($p->cliente_nombre) ?>
                                    </div>

                                    <div class="item-destino">
                                        <?= esc($p->destino) ?>
                                    </div>
                                </div>

                                <div class="item-bottom">

                                    <div class="item-valor">
                                        $<?= number_format($p->total, 2) ?>
                                    </div>

                                    <button class="btn btn-sm btn-light"
                                        onclick='verDetalle(
                                            <?= json_encode($p->cliente_nombre) ?>,
                                            <?= json_encode($p->destino) ?>,
                                            <?= json_encode(number_format($p->total, 2)) ?>,
                                            <?= json_encode($p->productos, JSON_HEX_APOS | JSON_HEX_QUOT) ?>,
                                            <?= json_encode(!empty($p->foto) ? base_url("upload/paquetes/" . $p->foto) : "") ?>
                                        )'>
                                        👁 Ver detalle
                                    </button>

                                </div>

                            </div>

                        <?php endforeach; ?>

                    </div>

                <?php endforeach; ?>

            </div>
        </div>

    </div>

    <!-- DERECHA -->
    <div class="col-md-4">

        <div class="card shadow-sm">
            <div class="card-body">

                <label>Cuenta destino</label>
                <br>
                <select id="cuentaDestino" class="form-control mb-2"></select>
                <br>
                <label>Comentarios</label>
                <textarea id="observaciones" class="form-control mb-2" rows="3"
                    placeholder="Opcional... notas sobre este pago"></textarea>
                <br>
                <button id="btnProcesar" class="btn btn-success w-100">
                    Procesar remuneración
                </button>
            </div>

        </div>

    </div>

</div>

<script>
    $(document).ready(function() {

        $('#cuentaDestino').select2({
            language: 'es',
            placeholder: 'Buscar cuenta...',
            width: '100%',
            ajax: {
                url: '<?= base_url('accounts-listAjax') ?>',
                dataType: 'json',
                delay: 250,
                data: params => ({
                    term: params.term
                }),
                processResults: data => ({
                    results: data
                })
            }
        });

        // 🔥 SET DEFAULT → EFECTIVO (id = 1)
        setTimeout(() => {

            let option = new Option('Efectivo', 1, true, true);

            $('#cuentaDestino')
                .append(option)
                .trigger('change');

        }, 100);

    });

    function verDetalle(cliente, destino, total, productos, foto) {

        let productosHTML = '<div class="text-muted">Sin productos</div>';

        try {

            if (Array.isArray(productos) && productos.length > 0) {

                productosHTML = productos.map(i => `
                <div style="
                    display:flex;
                    justify-content:space-between;
                    font-size:13px;
                    margin-bottom:4px;
                ">
                    <div>
                        ${i.nombre}<br>
                        <small>x${i.cantidad} · $${parseFloat(i.precio).toFixed(2)}</small>
                    </div>
                    <div style="font-weight:600;">
                        $${parseFloat(i.subtotal || 0).toFixed(2)}
                    </div>
                </div>
            `).join('');

            }

        } catch (e) {
            console.warn('Error productos', e);
        }

        Swal.fire({
            title: 'Detalle del paquete',
            html: `
                <div style="text-align:left">

                    ${foto ? `
                        <div style="text-align:center; margin-bottom:10px;">
                            <img src="${foto}" 
                                style="max-width:100%; border-radius:10px; cursor:pointer;"
                                onclick="verFoto('${foto}')">
                        </div>
                    ` : ''}

                    <div><b>Cliente:</b> ${cliente}</div>
                    <div><b>Destino:</b> ${destino}</div>

                    <hr>

                    <div><b>Productos:</b></div>
                    <div style="margin-bottom:10px;">
                        ${productosHTML}
                    </div>

                    <hr>

                    <div><b>Total:</b> $${total}</div>

                </div>
            `,
            showCloseButton: true,
            showConfirmButton: false,
            width: 400
        });
    }

    function verFoto(url) {

        Swal.fire({
            imageUrl: url,
            imageAlt: 'Foto del paquete',
            showCloseButton: true,
            showConfirmButton: false,
            background: '#000',
            backdrop: 'rgba(0,0,0,0.9)',
            width: 'auto',
            padding: '10px'
        });

    }

    function actualizarResumen() {

        let total = 0;
        let count = 0;

        document.querySelectorAll('.paquete').forEach(item => {

            if (item.dataset.selected === 'true') {

                total += parseFloat(item.dataset.valor);
                count++;

            }

        });

        document.getElementById('totalPaquetes').innerText = count;
        document.getElementById('totalDinero').innerText = '$' + total.toFixed(2);
    }

    document.querySelectorAll('.check-paquete').forEach(chk => {
        chk.addEventListener('change', actualizarResumen);
    });

    document.querySelectorAll('.paquete').forEach(item => {

        item.addEventListener('click', function(e) {

            // 🚫 evitar botón detalle
            if (e.target.closest('button')) return;

            let isSelected = this.dataset.selected === 'true';

            // toggle
            this.dataset.selected = !isSelected;

            this.classList.toggle('activo', !isSelected);

            actualizarResumen();
        });

    });

    document.getElementById('btnProcesar').addEventListener('click', () => {

        let paquetes = [];
        let observaciones = document.getElementById('observaciones').value.trim();

        document.querySelectorAll('.paquete').forEach(item => {

            if (item.dataset.selected === 'true') {
                paquetes.push(item.dataset.id);
            }

        });

        if (paquetes.length === 0) {
            Swal.fire('Sin selección', 'Selecciona al menos uno', 'warning');
            return;
        }

        let cuentaId = $('#cuentaDestino').val();
        let cuentaText = $('#cuentaDestino option:selected').text();

        if (!cuentaId) {
            Swal.fire('Falta cuenta', 'Selecciona la cuenta destino', 'warning');
            return;
        }

        let resumen = {};
        let totalGeneral = 0;

        document.querySelectorAll('.paquete').forEach(item => {

            if (item.dataset.selected !== 'true') return;

            let enc = item.dataset.encomendista || 'Sin asignar';
            let valor = parseFloat(item.dataset.valor);

            totalGeneral += valor;

            if (!resumen[enc]) {
                resumen[enc] = {
                    total: 0,
                    cantidad: 0
                };
            }

            resumen[enc].total += valor;
            resumen[enc].cantidad++;
        });

        let resumenHTML = '';

        Object.keys(resumen).forEach(enc => {

            let r = resumen[enc];

            resumenHTML += `
                <div style="
                    padding:8px;
                    border-bottom:1px solid #eee;
                    display:flex;
                    justify-content:space-between;
                    align-items:center;
                ">
                    <div>
                        <b>${enc}</b><br>
                        <small>${r.cantidad} paquetes</small>
                    </div>
                    <div style="color:#28a745; font-weight:600;">
                        $${r.total.toFixed(2)}
                    </div>
                </div>
            `;
        });

        Swal.fire({
            title: 'Confirmar remuneración',
            html: `
                <div style="text-align:left">

                    <div><b>Cuenta:</b> ${cuentaText}</div>
                    <div><b>Total:</b> $${totalGeneral.toFixed(2)}</div>

                    ${observaciones ? `<div><b>Nota:</b> ${observaciones}</div>` : ''}

                    <hr>

                    <div><b>Resumen por encomendista:</b></div>

                    <div style="max-height:200px; overflow:auto;">
                        ${resumenHTML}
                    </div>

                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Confirmar',
            cancelButtonText: 'Cancelar'
        }).then(result => {

            if (!result.isConfirmed) return;

            fetch("<?= base_url('packages-remunerations/store') ?>", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        paquetes,
                        cuenta: cuentaId,
                        cuenta_texto: cuentaText,
                        observaciones
                    })
                })
                .then(res => res.json())
                .then(res => {

                    if (res.status === 'ok') {
                        Swal.fire('Listo', 'Remuneración procesada', 'success')
                            .then(() => location.reload());
                    } else {
                        Swal.fire('Error', res.msg, 'error');
                    }

                });

        });

    });
</script>

<?= $this->endSection() ?>