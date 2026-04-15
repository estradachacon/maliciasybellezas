<?= $this->extend('Layouts/mainbody') ?>
<?= $this->section('content') ?>

<style>
    /* Desktop: inputs minimalistas en tabla de productos */
    @media (min-width: 768px) {
        #productosTable input,
        #productosTable .select2-selection {
            border: none !important;
            border-bottom: 1px solid #ccc !important;
            box-shadow: none !important;
        }
        #productosTable input:not(.precio) { background: transparent !important; }
        #productosTable input { height: 38px !important; line-height: 38px; padding: 0 8px; }
        #productosTable input:focus { border-bottom: 2px solid #007bff !important; }
        .producto-row:hover { background: #f9fafb; }
    }

    #pagosTable input { height: 38px !important; padding: 0 8px; }

    .producto, .cantidad, .precio, .total { height: 38px !important; }

    .add-line-link {
        color: #007bff;
        cursor: pointer;
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

    .ofertas-display { line-height: 1.9; min-height: 0; }
    .oferta-pill {
        font-size: 10px;
        padding: 1px 5px;
        border-radius: 3px;
        border: 1px solid;
        cursor: pointer;
        display: inline-block;
        margin: 1px 2px 0 0;
        white-space: nowrap;
        transition: opacity .15s;
        user-select: none;
    }
    .oferta-pill:hover { opacity: .75; }
    .oferta-activa    { background:#e9f7ef; border-color:#198754 !important; color:#198754; font-weight:700; }
    .oferta-alcanzable{ background:#f8f9fa; border-color:#ced4da !important; color:#495057; }
    .oferta-pendiente { background:#f8f9fa; border-color:#dee2e6 !important; color:#adb5bd; }

    #barcodeInput:focus { border-color: #198754; box-shadow: 0 0 0 .2rem rgba(25,135,84,.25); }
    #barcodeInput.is-invalid { border-color: #dc3545 !important; }

    /* ── PRODUCTO ROW — desktop ─────────────────────────── */
    .producto-row {
        display: flex;
        align-items: flex-start;
        gap: 6px;
        padding: 6px 0;
        border-bottom: 1px solid #f0f0f0;
    }
    .producto-row .i {
        min-width: 22px;
        padding-top: 9px;
        font-size: 13px;
        color: #6c757d;
        flex-shrink: 0;
    }
    .producto-row .pr-select { flex: 1; min-width: 0; }
    .producto-row .pr-select .select2-container { width: 100% !important; }
    .producto-row .pr-subfields {
        display: flex;
        gap: 6px;
        align-items: flex-start;
        flex-shrink: 0;
    }
    .producto-row .pr-subfields > div:first-child  { width: 70px;  flex-shrink:0; }
    .producto-row .pr-precio-wrap                  { width: 110px; flex-shrink:0; }
    .producto-row .pr-subfields > div:last-child   { width: 85px;  flex-shrink:0; }
    .producto-row .del { flex-shrink: 0; margin-top: 2px; }

    /* ── PRODUCTO ROW — mobile ──────────────────────────── */
    @media (max-width: 767px) {

        .producto-row {
            flex-wrap: wrap;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            padding: 10px;
            margin-bottom: 8px;
            border-bottom: 1px solid #dee2e6 !important;
            position: relative;
        }
        .producto-row .i { display: none; }

        /* Producto ocupa todo menos botón eliminar */
        .producto-row .pr-select { width: calc(100% - 44px); flex: none; }
        .producto-row .pr-select .select2-container { width: 100% !important; }

        /* Campos debajo del producto, en fila */
        .producto-row .pr-subfields {
            width: 100%;
            margin-top: 8px;
        }
        .producto-row .pr-subfields > div:first-child { flex: 1;   width: auto !important; min-width: 0; }
        .producto-row .pr-precio-wrap                { flex: 2;   width: auto !important; min-width: 0; }
        .producto-row .pr-subfields > div:last-child { flex: 1.5; width: auto !important; min-width: 0; }

        /* Botón eliminar: esquina superior derecha */
        .producto-row .del { position: absolute; top: 8px; right: 8px; margin-top: 0; }

        /* Ofertas ocupa fila completa */
        .producto-row .ofertas-display { width: 100%; }

        /* Inputs táctiles */
        .producto-row input.form-control,
        .producto-row .select2-selection { min-height: 40px !important; }

        /* Total venta más visible */
        #totalVenta { font-size: 36px !important; }

        /* Botón guardar full width */
        #ventaForm .btn-success { width: 100%; padding: .6rem; font-size: 1.1rem; }

        /* Agregar producto centrado */
        .add-line-link { font-size: 1rem; display: block; text-align: center; padding: 10px 0; }
    }
</style>

<div class="row">
    <div class="col-md-12">

        <div class="card">

            <!-- HEADER -->
            <div class="card-header d-flex align-items-center">
                <h5 class="mb-0">
                    <i class="fa fa-cash-register"></i> Nueva Venta Local
                </h5>

                <a href="<?= base_url('ventas') ?>" class="btn btn-secondary btn-sm ml-auto">
                    Volver
                </a>
            </div>

            <div class="card-body">

                <form id="ventaForm">

                    <div class="row mb-3">

                        <div class="col-6 col-md-2">
                            <label>Fecha</label>
                            <input type="date" name="fecha" class="form-control"
                                value="<?= date('Y-m-d') ?>">
                        </div>

                        <div class="col-12 col-md-4 order-md-0 order-last mt-2 mt-md-0">
                            <label>Cliente</label>
                            <select id="cliente_id" name="cliente_id" class="form-control"></select>
                        </div>

                        <div class="col-6 col-md-3">
                            <label>Agencia</label>
                            <input type="text" class="form-control"
                                value="<?= session('branch_name') ?>" readonly>
                        </div>
                    </div>

                    <!-- ESCÁNER DE CÓDIGO DE BARRAS -->
                    <div class="row mb-2">
                        <div class="col-12 col-md-5">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-white">
                                        <i class="fa fa-barcode"></i>
                                    </span>
                                </div>
                                <input type="text" id="barcodeInput" class="form-control"
                                    placeholder="Escanear código de barras..." autocomplete="off">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-secondary" id="btnCamara" title="Usar cámara">
                                        <i class="fa fa-camera"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Visor de cámara -->
                            <div id="camaraContainer" style="display:none; margin-top:8px;">
                                <div id="camaraReader" style="width:100%; border-radius:8px; overflow:hidden;"></div>
                                <button type="button" class="btn btn-sm btn-danger mt-1" id="btnCerrarCamara">
                                    <i class="fa fa-times"></i> Cerrar cámara
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- PRODUCTOS -->
                    <div class="d-none d-md-flex text-muted pb-1 mb-1"
                        style="font-size:12px;font-weight:600;border-bottom:2px solid #dee2e6;gap:6px;">
                        <span style="min-width:22px;">#</span>
                        <span style="flex:1;">Producto</span>
                        <span style="width:70px;">Cant</span>
                        <span style="width:110px;">Precio</span>
                        <span style="width:85px;">Total</span>
                        <span style="width:36px;"></span>
                    </div>
                    <div id="productosTable"></div>

                    <a id="addRowBtn" class="add-line-link">
                        <i class="fa fa-plus"></i> Agregar producto
                    </a>

                    <!-- TOTAL -->
                    <div class="text-right mt-4">
                        <div style="font-size:13px;">TOTAL</div>
                        <div style="font-size:30px; font-weight:bold; color:#198754;">
                            $<span id="totalVenta">0.00</span>
                        </div>
                    </div>

                    <div class="row mt-4">

                        <!-- 🧾 TABLA PAGOS (2/3) -->
                        <div class="col-12 col-md-8">

                            <h5>Pagos</h5>

                            <table class="table table-sm" id="pagosTable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th style="width:65%">Cuenta</th>
                                        <th>Monto</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>

                            <a id="addPagoBtn" class="add-line-link">
                                <i class="fa fa-plus"></i> Agregar pago
                            </a>

                        </div>

                        <!-- 💰 RESUMEN (1/3) -->
                        <div class="col-12 col-md-4 mt-3 mt-md-0">

                            <div class="card p-3 shadow-sm">

                                <?php if (tienePermiso('venta_credito')): ?>
                                    <select id="tipoVenta" class="form-control">
                                        <option value="contado" selected>Contado</option>
                                        <option value="credito">Crédito</option>
                                    </select>
                                <?php else: ?>
                                    <input type="text" class="form-control text-success font-weight-bold"
                                        value="Contado" readonly>
                                    <input type="hidden" id="tipoVenta" value="contado">
                                <?php endif; ?>

                                <hr>

                                <div class="d-flex justify-content-between">
                                    <span>Total venta</span>
                                    <b>$<span id="resumenTotal">0.00</span></b>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <span>Total pagado</span>
                                    <b>$<span id="totalPagado">0.00</span></b>
                                </div>

                                <div class="d-flex justify-content-between mt-2">
                                    <span>Diferencia</span>
                                    <b id="diferencia" style="color:#dc3545;">$0.00</b>
                                </div>

                                <div class="d-flex justify-content-between mt-2">
                                    <span>Cambio</span>
                                    <b id="cambio" style="color:#198754;">$0.00</b>
                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="text-right mt-4">
                        <button class="btn btn-success btn-lg">
                            Guardar Venta
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        // 🔥 CLIENTES
        $('#cliente_id').select2({
            language: 'es',
            minimumInputLength: 1,
            placeholder: 'Buscar cliente...',
            width: '100%',
            ajax: {
                url: '<?= base_url('clientes/buscar') ?>',
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

        // cliente default
        let def = new Option('Clientes varios', 1, true, true);
        $('#cliente_id').append(def).trigger('change');

        // 📦 PRODUCTOS
        const table = document.getElementById('productosTable');
        const totalVenta = document.getElementById('totalVenta');
        const resumenTotal = document.getElementById('resumenTotal');
        const diferenciaEl = document.getElementById('diferencia');
        const cambioEl = document.getElementById('cambio');

        function actualizarResumen() {

            let total = parseFloat(totalVenta.innerText) || 0;
            let pagado = parseFloat(totalPagado.innerText) || 0;

            resumenTotal.innerText = total.toFixed(2);

            let diferencia = total - pagado;

            if (diferencia > 0) {
                diferenciaEl.innerText = '$' + diferencia.toFixed(2);
                diferenciaEl.style.color = '#dc3545';
                cambioEl.innerText = '$0.00';
            } else {
                diferenciaEl.innerText = '$0.00';
                cambioEl.innerText = '$' + Math.abs(diferencia).toFixed(2);
            }
        }

        function addRow(preData = null) {

            let row = document.createElement('div');
            row.className = 'producto-row';

            row.innerHTML = `
                <span class="i"></span>
                <div class="pr-select">
                    <select class="producto w-100"></select>
                </div>
                <div class="pr-subfields">
                    <div style="flex:1;min-width:0;">
                        <small class="d-md-none text-muted" style="font-size:10px;">Cant</small>
                        <input type="number" class="cantidad form-control form-control-sm" min="1">
                    </div>
                    <div class="pr-precio-wrap" style="flex:2;min-width:0;">
                        <small class="d-md-none text-muted" style="font-size:10px;">Precio</small>
                        <input type="number" class="precio form-control form-control-sm" step="0.01" min="0" placeholder="Precio">
                        <div class="ofertas-display"></div>
                    </div>
                    <div style="flex:1.5;min-width:0;">
                        <small class="d-md-none text-muted" style="font-size:10px;">Total</small>
                        <input type="text" class="total form-control form-control-sm" readonly>
                    </div>
                </div>
                <button type="button" class="btn btn-danger btn-sm del">×</button>
            `;

            table.appendChild(row);
            index();

            // ✅ PRIMERO definir select
            let select = $(row).find('.producto');

            // ✅ LUEGO inicializar select2
            select.select2({
                language: 'es',
                minimumInputLength: 1,
                placeholder: 'Buscar producto...',
                width: '100%',

                templateResult: formatProducto,
                templateSelection: formatProductoSeleccion,
                escapeMarkup: m => m,

                ajax: {
                    url: '<?= base_url('productos/searchAjaxSelectStock') ?>',
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

            // ✅ AHORA sí usar select
            select.on('select2:select', function(e) {

                let data = e.params.data;

                let inputPrecio = row.querySelector('.precio');

                inputPrecio.value = parseFloat(data.precio || 0).toFixed(2);
                inputPrecio.placeholder = 'Precio: $' + (data.precio || 0);

                row.dataset.precioSugerido = data.precio || 0;
                row.dataset.stock          = data.stock || 0;
                row.dataset.branch         = data.branch_id || null;
                row.dataset.productoId     = data.producto_id || data.id;

                aplicarColorPrecio(row);
                fetchOfertas(row, parseInt(data.producto_id || data.id));
            });

            // eventos
            row.querySelector('.cantidad').addEventListener('input', function() {
                applyMejorOferta(row, parseFloat(this.value) || 0);
                calc();
            });
            row.querySelector('.precio').addEventListener('input', function() {
                aplicarColorPrecio(row);
                calc();
            });

            row.querySelector('.del').onclick = () => {
                row.remove();
                index();
                calcTotal();
            };

            function calc() {

                let c = parseFloat(row.querySelector('.cantidad').value) || 0;
                let p = parseFloat(row.querySelector('.precio').value) || 0;
                let stock = parseFloat(row.dataset.stock);

                // ⚠️ SOLO validar si ya hay producto
                if (!isNaN(stock) && stock >= 0) {

                    if (c > stock) {

                        Swal.fire({
                            icon: 'warning',
                            title: 'Stock insuficiente',
                            text: `Solo hay ${stock} unidades disponibles`
                        });

                        row.querySelector('.cantidad').value = stock;
                        c = stock;
                    }
                }

                row.querySelector('.total').value = (c * p).toFixed(2);
                calcTotal();
            }

            function aplicarColorPrecio(row) {

                let input = row.querySelector('.precio');

                let sugerido = parseFloat(row.dataset.precioSugerido);
                let actual = parseFloat(input.value);

                // reset
                input.style.background = '';
                input.style.borderBottom = '2px solid #ccc';
                input.style.color = '#000';
                input.style.fontWeight = 'normal';

                if (isNaN(sugerido) || isNaN(actual)) return;

                // 🟢 IGUAL
                if (Math.abs(actual - sugerido) < 0.01) {
                    input.style.background = '#e9f7ef';
                    input.style.borderBottom = '2px solid #198754';
                    input.style.color = '#198754';
                    input.style.fontWeight = 'bold';
                }

                // 🟡 MENOR
                else if (actual < sugerido) {
                    input.style.background = '#fff3cd';
                    input.style.borderBottom = '2px solid #ffc107';
                    input.style.color = '#856404';
                    input.style.fontWeight = 'bold';
                }

                // 🔵 MAYOR
                else {
                    input.style.background = '#d1ecf1';
                    input.style.borderBottom = '2px solid #0dcaf0';
                    input.style.color = '#055160';
                    input.style.fontWeight = 'bold';
                }
            }

            // Si viene con datos pre-cargados (barcode), rellenar el row
            if (preData) fillRow(row, preData);

            return row;
        }

        function index() {
            document.querySelectorAll('#productosTable .producto-row').forEach((r, i) => {
                r.querySelector('.i').innerText = i + 1;
            });
        }

        function calcTotal() {
            let t = 0;
            document.querySelectorAll('.total').forEach(i => {
                t += parseFloat(i.value) || 0;
            });
            totalVenta.innerText = t.toFixed(2);

            actualizarResumen();
        }

        // ── Rellenar un row con datos pre-cargados (barcode / addRow) ──
        function fillRow(row, data) {
            let select = $(row).find('.producto');
            let option = new Option(data.text, data.id, true, true);
            select.append(option).trigger('change');

            let inputPrecio = row.querySelector('.precio');
            inputPrecio.value          = parseFloat(data.precio || 0).toFixed(2);
            inputPrecio.placeholder    = 'Precio: $' + (data.precio || 0);
            row.dataset.precioSugerido = data.precio || 0;
            row.dataset.stock          = data.stock  || 0;
            row.dataset.branch         = data.branch_id || null;
            row.dataset.productoId     = data.producto_id;

            row.querySelector('.cantidad').value = 1;
            row.querySelector('.total').value    = parseFloat(data.precio || 0).toFixed(2);
            calcTotal();
            aplicarColorPrecio(row);

            if (data.ofertas && data.ofertas.length > 0) {
                row.dataset.ofertas = JSON.stringify(data.ofertas);
                showOfertas(row, data.ofertas, 1);
            } else {
                fetchOfertas(row, data.producto_id);
            }
        }

        // ── Obtener ofertas del servidor ─────────────────────────────
        function fetchOfertas(row, productoId) {
            if (!productoId) return;
            fetch(`<?= base_url('productos/ofertasPorProducto') ?>?producto_id=${productoId}`)
                .then(r => r.json())
                .then(ofertas => {
                    row.dataset.ofertas = JSON.stringify(ofertas);
                    let cantidad = parseFloat(row.querySelector('.cantidad').value) || 1;
                    showOfertas(row, ofertas, cantidad);
                });
        }

        // ── Renderizar pills de ofertas ──────────────────────────────
        function showOfertas(row, ofertas, cantidad) {
            let div = row.querySelector('.ofertas-display');
            if (!ofertas || ofertas.length === 0) { div.innerHTML = ''; return; }

            // Mejor oferta aplicable = mayor cantidad_minima ≤ cantidad actual
            let mejorMin = 0;
            ofertas.forEach(o => {
                if (cantidad >= o.cantidad_minima && o.cantidad_minima > mejorMin)
                    mejorMin = o.cantidad_minima;
            });

            div.innerHTML = ofertas.map(o => {
                let cls = cantidad >= o.cantidad_minima
                    ? (o.cantidad_minima === mejorMin ? 'oferta-activa' : 'oferta-alcanzable')
                    : 'oferta-pendiente';
                return `<span class="oferta-pill ${cls}"
                    data-precio="${o.precio}" data-min="${o.cantidad_minima}">
                    ${o.cantidad_minima}+ → $${parseFloat(o.precio).toFixed(2)}
                </span>`;
            }).join('');

            div.querySelectorAll('.oferta-pill').forEach(pill => {
                pill.addEventListener('click', function() {
                    let inputPrecio = row.querySelector('.precio');
                    inputPrecio.value = parseFloat(this.dataset.precio).toFixed(2);
                    inputPrecio.dispatchEvent(new Event('input')); // dispara calc + colorPrecio
                });
            });
        }

        // ── Auto-aplicar la mejor oferta según cantidad ──────────────
        function applyMejorOferta(row, cantidad) {
            let ofertas = JSON.parse(row.dataset.ofertas || '[]');
            if (!ofertas.length) return;

            let mejor = null;
            ofertas.forEach(o => {
                if (cantidad >= o.cantidad_minima)
                    if (!mejor || o.cantidad_minima > mejor.cantidad_minima) mejor = o;
            });

            let inputPrecio = row.querySelector('.precio');
            if (mejor) {
                inputPrecio.value = parseFloat(mejor.precio).toFixed(2);
            } else {
                inputPrecio.value = parseFloat(row.dataset.precioSugerido || 0).toFixed(2);
            }
            aplicarColorPrecio(row);
            showOfertas(row, ofertas, cantidad);
        }

        document.getElementById('addRowBtn').onclick = e => {
            e.preventDefault();
            addRow();
        };

        // ── Lógica de búsqueda por código (compartida entre input y cámara) ─
        const barcodeInput = document.getElementById('barcodeInput');

        function buscarCodigo(codigo) {
            if (!codigo) return;

            fetch(`<?= base_url('productos/buscarPorCodigo') ?>?codigo=${encodeURIComponent(codigo)}`)
                .then(r => r.json())
                .then(data => {
                    if (!data.found) {
                        barcodeInput.classList.add('is-invalid');
                        barcodeInput.placeholder = data.msg || `No encontrado: ${codigo}`;
                        setTimeout(() => {
                            barcodeInput.classList.remove('is-invalid');
                            barcodeInput.placeholder = 'Escanear código de barras...';
                        }, 2500);
                        return;
                    }

                    // ¿Ya está en la tabla? → incrementar cantidad
                    let existingRow = null;
                    document.querySelectorAll('#productosTable .producto-row').forEach(r => {
                        if (r.dataset.productoId == data.producto_id) existingRow = r;
                    });

                    if (existingRow) {
                        let cantInput = existingRow.querySelector('.cantidad');
                        let newCant   = (parseFloat(cantInput.value) || 0) + 1;
                        let stock     = parseFloat(existingRow.dataset.stock) || 0;
                        if (newCant > stock) {
                            Swal.fire({ icon: 'warning', title: 'Stock insuficiente', text: `Solo hay ${stock} unidades` });
                            return;
                        }
                        cantInput.value = newCant;
                        cantInput.dispatchEvent(new Event('input'));
                        return;
                    }

                    // ¿Hay una fila vacía disponible? → reutilizarla
                    let emptyRow = null;
                    document.querySelectorAll('#productosTable .producto-row').forEach(r => {
                        if (!r.dataset.productoId && !emptyRow) emptyRow = r;
                    });

                    if (emptyRow) {
                        fillRow(emptyRow, data);
                    } else {
                        addRow(data);
                    }
                });
        }

        // Input manual / lector físico (Enter)
        barcodeInput.addEventListener('keydown', function(e) {
            if (e.key !== 'Enter') return;
            e.preventDefault();
            let codigo = this.value.trim();
            this.value = '';
            buscarCodigo(codigo);
        });

        // ── Cámara ───────────────────────────────────────────────────
        let html5QrCode = null;

        document.getElementById('btnCamara').addEventListener('click', function() {
            let container = document.getElementById('camaraContainer');
            container.style.display = 'block';
            this.disabled = true;

            if (!html5QrCode) {
                html5QrCode = new Html5Qrcode('camaraReader');
            }

            html5QrCode.start(
                { facingMode: 'environment' },
                { fps: 10, qrbox: { width: 260, height: 100 } },
                (codigoLeido) => {
                    html5QrCode.stop().then(() => {
                        container.style.display = 'none';
                        document.getElementById('btnCamara').disabled = false;
                        buscarCodigo(codigoLeido);
                    });
                },
                () => {} // errores de frame ignorados
            ).catch(err => {
                container.style.display = 'none';
                document.getElementById('btnCamara').disabled = false;
                Swal.fire('Error', 'No se pudo acceder a la cámara: ' + err, 'error');
            });
        });

        document.getElementById('btnCerrarCamara').addEventListener('click', function() {
            if (html5QrCode && html5QrCode.isScanning) {
                html5QrCode.stop().then(() => {
                    document.getElementById('camaraContainer').style.display = 'none';
                    document.getElementById('btnCamara').disabled = false;
                });
            } else {
                document.getElementById('camaraContainer').style.display = 'none';
                document.getElementById('btnCamara').disabled = false;
            }
        });

        addRow();

        // 💰 PAGOS
        const pagosTable = document.querySelector('#pagosTable tbody');
        const totalPagado = document.getElementById('totalPagado');

        function addPago() {

            let row = document.createElement('tr');

            row.innerHTML = `
                <td class="i"></td>
                <td><select class="cuenta"></select></td>
                <td><input type="number" class="monto" step="0.01" min="0"></td>
                <td><button class="btn btn-danger btn-sm del">X</button></td>
            `;

            pagosTable.appendChild(row);

            $(row).find('.cuenta').select2({
                placeholder: 'Cuenta',
                width: '100%',
                ajax: {
                    url: '<?= base_url('accounts-listAjax') ?>',
                    dataType: 'json',
                    data: p => ({
                        term: p.term
                    }),
                    processResults: d => ({
                        results: d
                    })
                }
            });

            row.querySelector('.monto').oninput = calcPagos;

            row.querySelector('.del').onclick = () => {
                row.remove();
                calcPagos();
            };
        }

        function formatProducto(producto) {

            if (!producto.id) return producto.text;

            let foto = producto.imagen ?
                "<?= base_url('upload/productos/') ?>/" + producto.imagen :
                'https://via.placeholder.com/50';

            let stock = producto.stock ?? 0;

            return $(`
                <div style="display:flex; gap:10px; align-items:center;">

                    <img src="${foto}" style="
                        width:45px;
                        height:45px;
                        object-fit:cover;
                        border-radius:6px;
                    ">

                    <div style="flex:1;">
                        <div style="font-weight:600;">
                            ${producto.text}
                        </div>

                        <div style="display:flex; justify-content:space-between; font-size:12px;">

                            <span style="color:#666;">
                                $${producto.precio || '0.00'}
                            </span>

                            <span style="
                                font-weight:bold;
                                padding:2px 6px;
                                border-radius:6px;
                                background:${stock > 0 ? '#e9f7ef' : '#fdecea'};
                                color:${stock > 0 ? '#198754' : '#dc3545'};
                            ">
                                ${stock}
                            </span>

                        </div>
                    </div>
                </div>
            `);
        }

        function formatProductoSeleccion(producto) {

            if (!producto.id) return producto.text;

            return `
                <span style="font-weight:600;">
                    ${producto.text}
                </span>
            `;
        }

        function calcPagos() {
            let t = 0;
            document.querySelectorAll('.monto').forEach(i => {
                t += parseFloat(i.value) || 0;
            });
            totalPagado.innerText = t.toFixed(2);

            actualizarResumen();
        }

        document.getElementById('ventaForm').addEventListener('submit', function(e) {
            e.preventDefault();

            let errores = [];

            let tipoVenta = document.getElementById('tipoVenta').value;
            let total = parseFloat(totalVenta.innerText) || 0;
            let pagado = parseFloat(totalPagado.innerText) || 0;

            // =========================
            // 🔥 VALIDAR PRODUCTOS
            // =========================
            document.querySelectorAll('#productosTable .producto-row').forEach((row, i) => {

                let producto = $(row).find('.producto').val();
                let cantidad = parseFloat(row.querySelector('.cantidad').value) || 0;
                let precio = parseFloat(row.querySelector('.precio').value) || 0;
                let stock = parseFloat(row.dataset.stock) || 0;

                if (!producto) errores.push(`Fila ${i+1}: seleccione producto`);
                if (cantidad <= 0) errores.push(`Fila ${i+1}: cantidad inválida`);
                if (precio <= 0) errores.push(`Fila ${i+1}: precio inválido`);

                if (cantidad > stock) {
                    errores.push(`Fila ${i+1}: supera stock (${stock})`);
                }
            });

            // =========================
            // 💰 VALIDAR PAGOS
            // =========================
            if (tipoVenta === 'contado') {

                if (pagado <= 0) {
                    errores.push('Debe registrar un pago para ventas de contado');
                }

                if (pagado < total) {
                    errores.push(`Venta de contado incompleta. Faltan $${(total - pagado).toFixed(2)}`);
                }
            }

            if (errores.length > 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Validación',
                    html: errores.map(e => `• ${e}`).join('<br>')
                });
                return;
            }

            // =========================
            // 🧾 ARMAR OBJETO
            // =========================

            let venta = {
                fecha: document.querySelector('[name="fecha"]').value,
                cliente_id: $('#cliente_id').val(),
                tipo_venta: tipoVenta,
                total: total,
                total_pagado: pagado
            };

            let detalle = [];
            let detalleHtml = '';

            document.querySelectorAll('#productosTable .producto-row').forEach(row => {

                let producto_id = $(row).find('.producto').val();
                let nombre = $(row).find('.producto').select2('data')[0]?.text || 'Producto';
                let cantidad = parseFloat(row.querySelector('.cantidad').value) || 0;
                let precio = parseFloat(row.querySelector('.precio').value) || 0;
                let totalLinea = parseFloat(row.querySelector('.total').value) || 0;

                if (producto_id) {
                    detalle.push({
                        producto_id: producto_id,
                        cantidad: cantidad,
                        precio_unitario: precio,
                        total: totalLinea,
                        branch_id: row.dataset.branch
                    });

                    detalleHtml += `• ${nombre} x${cantidad} = $${totalLinea.toFixed(2)}<br>`;
                }
            });

            let pagos = [];

            document.querySelectorAll('#pagosTable tbody tr').forEach(row => {

                let cuenta = $(row).find('.cuenta').val();
                let monto = parseFloat(row.querySelector('.monto').value) || 0;

                if (cuenta && monto > 0) {
                    pagos.push({
                        account_id: cuenta,
                        monto: monto
                    });
                }
            });

            let data = {
                venta: venta,
                detalle: detalle,
                pagos: pagos
            };

            // =========================
            // 🎨 ESTADO VISUAL
            // =========================
            let saldo = total - pagado;
            let estadoPago = '';

            if (saldo > 0) {
                estadoPago = `<span style="color:#dc3545;">Pendiente: $${saldo.toFixed(2)}</span>`;
            } else if (saldo < 0) {
                estadoPago = `<span style="color:#198754;">Cambio: $${Math.abs(saldo).toFixed(2)}</span>`;
            } else {
                estadoPago = `<span style="color:#198754;">Pagado completo</span>`;
            }

            // =========================
            // 🔥 CONFIRMACIÓN
            // =========================
            Swal.fire({
                icon: 'info',
                title: 'Resumen de venta',
                width: 600,
                html: `
            <div style="text-align:left; font-size:14px">

                <b>Detalle:</b><br>
                ${detalleHtml || 'Sin productos'} 

                <hr>

                <div style="display:flex; justify-content:space-between;">
                    <span>Total:</span>
                    <b>$${total.toFixed(2)}</b>
                </div>

                <div style="display:flex; justify-content:space-between;">  
                    <span>Pagado:</span>
                    <b>$${pagado.toFixed(2)}</b>
                </div>

                <div style="display:flex; justify-content:space-between;">
                    <span>Estado:</span>
                    <b>${estadoPago}</b>
                </div>

            </div>
        `,
                showCancelButton: true,
                confirmButtonText: 'Guardar venta',
                cancelButtonText: 'Revisar'
            }).then(result => {

                if (!result.isConfirmed) return;

                // =========================
                // 🚀 ENVÍO REAL
                // =========================
                fetch("<?= base_url('ventas/store') ?>", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(data)
                    })
                    .then(res => res.json())
                    .then(resp => {

                        if (resp.success) {

                            Swal.fire({
                                icon: 'success',
                                title: 'Venta guardada',
                                text: 'Se registró correctamente',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = "<?= base_url('ventas') ?>";
                            });

                        } else {
                            Swal.fire('Error', resp.message || 'No se pudo guardar', 'error');
                        }

                    })
                    .catch(() => {
                        Swal.fire('Error', 'Error de conexión', 'error');
                    });

            });

        });
        document.getElementById('addPagoBtn').onclick = e => {
            e.preventDefault();
            addPago();
        };

        addPago();

    });
</script>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<?= $this->endSection() ?>