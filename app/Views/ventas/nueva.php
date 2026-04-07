<?= $this->extend('Layouts/mainbody') ?>
<?= $this->section('content') ?>

<style>
    #productosTable input,
    #productosTable .select2-selection {
        border: none !important;
        border-bottom: 1px solid #ccc !important;
        box-shadow: none !important;
    }

    /* 🔥 SOLO inputs normales */
    #productosTable input:not(.precio) {
        background: transparent !important;
    }

    #pagosTable input {
        height: 38px !important;
        padding: 0 8px;
    }

    .producto,
    .cantidad,
    .precio,
    .total {
        height: 38px !important;
    }

    #productosTable input {
        height: 38px !important;
        line-height: 38px;
        padding: 0 8px;
    }

    #productosTable input:focus {
        border-bottom: 2px solid #007bff !important;
    }

    #productosTable tbody tr:hover {
        background: #f9fafb;
    }

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

                        <div class="col-md-2">
                            <label>Fecha</label>
                            <input type="date" name="fecha" class="form-control"
                                value="<?= date('Y-m-d') ?>">
                        </div>

                        <div class="col-md-4">
                            <label>Cliente</label>
                            <select id="cliente_id" name="cliente_id" class="form-control"></select>
                        </div>

                        <div class="col-md-3">
                            <label>Agencia</label>
                            <input type="text" class="form-control"
                                value="<?= session('branch_name') ?>" readonly>
                        </div>
                    </div>

                    <!-- PRODUCTOS -->
                    <table class="table table-sm" id="productosTable">
                        <thead>
                            <tr>
                                <th style="width:5%">#</th>
                                <th style="width:50%">Producto</th>
                                <th style="width:10%">Cant</th>
                                <th style="width:15%">Precio</th>
                                <th style="width:15%">Total</th>
                                <th style="width:5%"></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>

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
                        <div class="col-md-8">

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
                        <div class="col-md-4">

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
        const table = document.querySelector('#productosTable tbody');
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

        function addRow() {

            let row = document.createElement('tr');

            row.innerHTML = `
                <td class="i"></td>
                <td><select class="producto"></select></td>
                <td><input type="number" class="cantidad" min="1"></td>
                <td>
                    <input type="number" class="precio" step="0.01" min="0" placeholder="Sugerido">
                </td>
                <td><input type="text" class="total" readonly></td>
                <td><button class="btn btn-danger btn-sm del">X</button></td>
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

                inputPrecio.value = data.precio || 0;
                inputPrecio.placeholder = 'Sugerido: $' + (data.precio || 0);

                row.dataset.precioSugerido = data.precio || 0;
                row.dataset.stock = data.stock || 0;

                aplicarColorPrecio(row);
            });

            // eventos
            row.querySelector('.cantidad').oninput = calc;
            row.querySelector('.precio').oninput = calc;

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
        }

        function index() {
            document.querySelectorAll('#productosTable tbody tr').forEach((r, i) => {
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

        document.getElementById('addRowBtn').onclick = e => {
            e.preventDefault();
            addRow();
        };

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

            document.querySelectorAll('#productosTable tbody tr').forEach((row, i) => {

                let producto = $(row).find('.producto').val();
                let cantidad = parseFloat(row.querySelector('.cantidad').value) || 0;
                let precio = parseFloat(row.querySelector('.precio').value) || 0;
                let stock = parseFloat(row.dataset.stock) || 0;
                let tipoVenta = document.getElementById('tipoVenta').value;
                let total = parseFloat(totalVenta.innerText) || 0;
                let pagado = parseFloat(totalPagado.innerText) || 0;

                let saldoPendiente = total - pagado;

                if (tipoVenta === 'contado') {

                    if (pagado <= 0) {
                        errores.push('Debe registrar un pago para ventas de contado');
                    }

                    // 🔥 CLAVE: bloquear si no está completo
                    if (pagado < total) {
                        errores.push(`Venta de contado incompleta. Faltan $${(total - pagado).toFixed(2)}`);
                    }

                    // opcional (seguridad)
                    if (pagado < 0) {
                        errores.push('Monto pagado inválido');
                    }
                }

                if (!producto) errores.push(`Fila ${i+1}: seleccione producto`);
                if (cantidad <= 0) errores.push(`Fila ${i+1}: cantidad inválida`);
                if (precio <= 0) errores.push(`Fila ${i+1}: precio inválido`);

                if (cantidad > stock) {
                    errores.push(`Fila ${i+1}: supera stock (${stock})`);
                }

            });

            if (errores.length > 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Validación',
                    html: errores.map(e => `• ${e}`).join('<br>')
                });
                return;
            }

            let total = parseFloat(totalVenta.innerText) || 0;
            let pagado = parseFloat(totalPagado.innerText) || 0;
            let saldo = total - pagado;

            let estadoPago = '';

            // 🎨 lógica visual
            if (saldo > 0) {
                estadoPago = `<span style="color:#dc3545;">Pendiente: $${saldo.toFixed(2)}</span>`;
            } else if (saldo < 0) {
                estadoPago = `<span style="color:#198754;">Cambio: $${Math.abs(saldo).toFixed(2)}</span>`;
            } else {
                estadoPago = `<span style="color:#198754;">Pagado completo</span>`;
            }

            // 🧾 resumen productos
            let detalle = '';
            document.querySelectorAll('#productosTable tbody tr').forEach(row => {

                let nombre = $(row).find('.producto').select2('data')[0]?.text || 'Producto';
                let cantidad = row.querySelector('.cantidad').value;
                let totalLinea = row.querySelector('.total').value;

                detalle += `• ${nombre} x${cantidad} = $${totalLinea}<br>`;
            });
            if (tipoVenta === 'contado') {

                if (pagado <= 0) {
                    errores.push('Debe registrar un pago para ventas de contado');
                }

                // 🔥 CLAVE: bloquear si no está completo
                if (pagado < total) {
                    errores.push(`Venta de contado incompleta. Faltan $${(total - pagado).toFixed(2)}`);
                }

                // opcional (seguridad)
                if (pagado < 0) {
                    errores.push('Monto pagado inválido');
                }
            }
            // 🚀 SWEET ALERT PRO
            Swal.fire({
                icon: 'info',
                title: 'Resumen de venta',
                width: 600,
                html: `
                    <div style="text-align:left; font-size:14px">

                        <b>Detalle:</b><br>
                        ${detalle || 'Sin productos'} 

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

                if (result.isConfirmed) {
                    // 🔥 aquí haces el submit real (ajax o normal)
                    Swal.fire('Guardado', 'Venta registrada correctamente', 'success');
                }

            });
        });
        document.getElementById('addPagoBtn').onclick = e => {
            e.preventDefault();
            addPago();
        };

        addPago();

    });
</script>

<?= $this->endSection() ?>