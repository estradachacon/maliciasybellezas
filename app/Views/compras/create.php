<?= $this->extend('Layouts/mainbody') ?>
<?= $this->section('content') ?>

<style>
    .table td {
        vertical-align: middle;
    }

    /* estilo general inputs */
    #productosTable input,
    #productosTable .select2-selection {
        border: none !important;
        border-bottom: 1px solid #ccc !important;
        border-radius: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
        font-size: 14px;
    }

    /* focus estilo odoo */
    #productosTable input:focus,
    #productosTable .select2-selection:focus {
        border-bottom: 2px solid #007bff !important;
        outline: none;
    }

    /* select2 fix altura */
    .select2-container--default .select2-selection--single {
        height: 34px;
        display: flex;
        align-items: center;
    }

    /* quitar flecha fea */
    .select2-selection__arrow {
        display: none;
    }

    /* hover fila tipo odoo */
    #productosTable tbody tr:hover {
        background-color: #f9fafb;
    }

    /* inputs centrados visualmente */
    #productosTable td {
        padding: 6px 8px;
    }

    #productosTable {
        font-size: 14px;
    }

    #productosTable th {
        font-weight: 600;
        font-size: 13px;
        color: #666;
        background: #f8f9fa;
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

    .badge-sugerido {
        position: absolute;
        top: -6px;
        right: 4px;
        background: #198754;
        color: #fff;
        font-size: 10px;
        padding: 2px 6px;
        border-radius: 6px;
        pointer-events: none;
    }

    .precio-wrapper {
        position: relative;
    }

    .precio-sugerido {
        background-color: #e9f7ef !important;
    }

    .add-line-link {
        color: #007bff;
        font-size: 14px;
        text-decoration: none;
        cursor: pointer;
    }

    .add-line-link:hover {
        text-decoration: underline;
        color: #0056b3;
    }

    .ver-img-btn {
        border: none;
        background: transparent;
        color: #007bff;
    }

    .ver-img-btn:hover {
        color: #0056b3;
    }
</style>

<div class="row">
    <div class="col-md-12">

        <div class="card">

            <!-- HEADER -->
            <div class="card-header d-flex">
                <h4 class="header-title">Nueva Compra</h4>

                <a href="<?= base_url('compras') ?>" class="btn btn-secondary btn-sm ml-auto">
                    Volver
                </a>
            </div>

            <div class="card-body">

                <!--FORM -->
                <form id="compraForm">

                    <div class="row mb-3">

                        <div class="col-md-2">
                            <label>Fecha de compra</label>
                            <input type="date" name="fecha_compra" id="fecha_compra"
                                class="form-control"
                                value="<?= date('Y-m-d') ?>">
                        </div>

                        <!-- Proveedor -->
                        <div class="col-md-4">
                            <label>Proveedor</label>
                            <select id="proveedor_id" name="proveedor_id" class="form-control" required></select>
                        </div>

                        <!-- Sucursal -->
                        <div class="col-md-4">
                            <label>Sucursal receptora</label>
                            <select name="branch_id" class="form-control">
                                <?php foreach ($branches ?? [] as $b): ?>
                                    <option value="<?= $b->id ?>">
                                        <?= $b->branch_name ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-12 mt-2">
                            <label>Observación</label>
                            <textarea name="observacion" id="observacion" class="form-control" rows="2"
                                placeholder="Notas de la compra..."></textarea>
                        </div>
                    </div>

                    <!-- 📦 TABLA PRODUCTOS -->
                    <div class="table-responsive">
                        <table class="table table-bordered" id="productosTable">

                            <thead>
                                <tr>
                                    <th style="width:5%">#</th>
                                    <th style="width:40%">Producto</th>
                                    <th>Cantidad</th>
                                    <th>Precio Costo</th>
                                    <th>Total</th>
                                    <th></th>
                                </tr>
                            </thead>

                            <tbody></tbody>

                        </table>
                    </div>

                    <div class="mt-2">
                        <a href="#" id="addRowBtn" class="add-line-link">
                            <i class="fa fa-plus"></i> Agregar línea
                        </a>
                    </div>

                    <!-- TOTAL -->
                    <div class="text-right mt-3">
                        <h4>Total: $<span id="totalCompra">0.00</span></h4>
                    </div>
                    <!-- 💰 PAGOS -->
                    <div class="mt-4">

                        <h5 class="mb-2">Pagos</h5>

                        <div class="table-responsive">
                            <table class="table table-bordered" id="pagosTable">
                                <thead>
                                    <tr>
                                        <th style="width:5%">#</th>
                                        <th>Cuenta</th>
                                        <th>Monto</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>

                        <a href="#" id="addPagoBtn" class="add-line-link">
                            <i class="fa fa-plus"></i> Agregar pago
                        </a>

                        <div class="text-right mt-2">
                            <b>Total Pagado: $<span id="totalPagado">0.00</span></b>
                        </div>

                    </div>
                    <!-- BOTÓN -->
                    <div class="mt-3 text-right">
                        <button type="submit" class="btn btn-success">
                            Guardar Compra
                        </button>
                    </div>

                </form>

            </div>

        </div>

    </div>
</div>
<div class="modal fade" id="modalProveedor">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5>Nuevo Proveedor</h5>
            </div>

            <div class="modal-body">
                <label>Nombre</label>
                <input type="text" name="nombre" id="edit_nombre" class="form-control" required>
            </div>

            <div class="modal-body">
                <label>Teléfono</label>
                <input type="number" name="telefono" id="edit_telefono" class="form-control" step="1">
            </div>

            <div class="modal-body">
                <label>Dirección</label>
                <textarea name="direccion" id="edit_direccion" class="form-control"></textarea>
            </div>

            <div class="modal-footer">
                <button class="btn btn-success" id="guardarProveedor">Guardar</button>
            </div>

        </div>
    </div>
</div>
<div class="modal fade" id="productoModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Crear Producto</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <form id="productoForm" enctype="multipart/form-data">

                <div class="modal-body">

                    <div class="row">

                        <!-- Nombre -->
                        <div class="col-md-6">
                            <label>Nombre</label>
                            <input type="text" name="nombre" class="form-control" required>
                        </div>

                        <!-- Marca -->
                        <div class="col-md-6">
                            <label>Marca</label>
                            <input type="text" name="marca" class="form-control">
                        </div>

                        <!-- Presentación -->
                        <div class="col-md-6 mt-2">
                            <label>Presentación</label>
                            <input type="text" name="presentacion" class="form-control">
                        </div>

                        <!-- Precio -->
                        <div class="col-md-6 mt-2">
                            <label>Precio</label>
                            <input type="number" step="0.01" name="precio" class="form-control" required>
                        </div>

                        <!-- 🔥 CÓDIGO DE BARRAS -->
                        <div class="col-md-6 mt-2">
                            <label>Código de barras</label>

                            <div class="input-group">
                                <input type="text" name="codigo_barras" id="codigo_barras" class="form-control">

                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-secondary" onclick="toggleScanner()">
                                        <i class="fa fa-camera"></i>
                                    </button>

                                    <button type="button" class="btn btn-outline-success" onclick="generarCodigo()">
                                        <i class="fa fa-barcode"></i>
                                    </button>
                                </div>

                                <div class="input-group-append">
                                    <span id="scanCheck" class="input-group-text text-success" style="display:none;">
                                        ✔
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- 🔥 SCANNER -->
                        <div class="col-md-12 mt-2" id="scannerContainer" style="display:none;">
                            <div class="border rounded p-2 text-center bg-light">
                                <div id="reader" style="width:100%; max-width:400px; margin:auto;"></div>

                                <button type="button" class="btn btn-sm btn-danger mt-2" onclick="cerrarScanner()">
                                    Cancelar
                                </button>
                            </div>
                        </div>

                        <!-- Descripción -->
                        <div class="col-md-12 mt-2">
                            <label>Descripción</label>
                            <textarea name="descripcion" class="form-control"></textarea>
                        </div>

                        <!-- Imagen -->
                        <div class="col-md-12 mt-3">
                            <label>Imagen del producto</label>
                            <div class="input-group">
                                <input type="file" name="imagen" id="imagenInput" class="form-control" accept="image/*" capture="environment">

                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-secondary" onclick="abrirCamara()">
                                        <i class="fa fa-camera"></i>
                                    </button>
                                </div>
                            </div>

                            <img id="previewImg" style="max-width:200px; margin-top:10px; display:none; border-radius:8px;">
                        </div>

                    </div>

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">
                        Guardar
                    </button>

                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Cancelar
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
<div class="modal fade" id="imagenProductoModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-body text-center">
                <img id="imagenProductoPreview" style="max-width:100%; border-radius:10px;">
            </div>

        </div>
    </div>
</div>
<script>
    let currentRow = null;
</script>
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    let html5QrCode = null;
    let scannerActivo = false;

    function toggleScanner() {
        const container = document.getElementById('scannerContainer');

        if (scannerActivo) {
            cerrarScanner();
            return;
        }

        container.style.display = 'block';

        html5QrCode = new Html5Qrcode("reader");

        html5QrCode.start({
                facingMode: "environment"
            }, {
                fps: 10,
                qrbox: 250
            },
            (decodedText) => {
                document.getElementById('codigo_barras').value = decodedText;
                mostrarCheck();
                cerrarScanner();
            }
        ).catch(err => console.error(err));

        scannerActivo = true;
    }

    function cerrarScanner() {
        const container = document.getElementById('scannerContainer');

        if (html5QrCode) {
            html5QrCode.stop().then(() => html5QrCode.clear()).catch(() => {});
        }

        container.style.display = 'none';
        scannerActivo = false;
    }

    function mostrarCheck() {
        const check = document.getElementById('scanCheck');
        check.style.display = 'inline';

        setTimeout(() => check.style.display = 'none', 2000);
    }

    function generarCodigo() {
        const input = document.getElementById('codigo_barras');

        const codigo = 'P' + Date.now();
        input.value = codigo;

        Swal.fire({
            icon: 'success',
            title: 'Código generado',
            text: codigo,
            timer: 1200,
            showConfirmButton: false
        });
    }
    document.addEventListener('DOMContentLoaded', function() {
        const pagosTable = document.querySelector('#pagosTable tbody');
        const totalPagadoEl = document.getElementById('totalPagado');

        function addPagoRow() {
            const row = document.createElement('tr');

            row.innerHTML = `
                <td class="row-index text-center"></td>

                <td>
                    <select class="form-control cuenta-select"></select>
                </td>

                <td>
                    <input type="number" class="form-control monto" step="0.01">
                </td>

                <td>
                    <button class="btn btn-danger btn-sm removePago">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            `;

            pagosTable.appendChild(row);
            actualizarIndicesPagos();
            bindPagoEvents(row);
        }

        function actualizarIndicesPagos() {
            document.querySelectorAll('#pagosTable tbody tr').forEach((row, index) => {
                row.querySelector('.row-index').innerText = index + 1;
            });
        }

        function bindPagoEvents(row) {

            const monto = row.querySelector('.monto');

            // 🔥 SELECT2 CUENTAS (usa tu endpoint)
            $(row).find('.cuenta-select').select2({
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

            monto.addEventListener('input', calcularTotalPagado);

            row.querySelector('.removePago').addEventListener('click', function() {
                row.remove();
                actualizarIndicesPagos();
                calcularTotalPagado();
            });
        }

        function calcularTotalPagado() {
            let total = 0;

            document.querySelectorAll('.monto').forEach(input => {
                total += parseFloat(input.value) || 0;
            });

            totalPagadoEl.innerText = total.toFixed(2);
        }

        document.getElementById('addPagoBtn').addEventListener('click', function(e) {
            e.preventDefault();
            addPagoRow();
        });

        // iniciar con 1 pago
        addPagoRow();

        const productos = <?= json_encode($productos) ?>;
        const table = document.querySelector('#productosTable tbody');
        const totalCompra = document.getElementById('totalCompra');

        function addRow() {

            const row = document.createElement('tr');

            row.innerHTML = `
            <td class="row-index text-center"></td>
            <td>
                <select class="form-control producto-select"></select>
            </td>

            <td>
                <input type="number" class="form-control cantidad" value="" min="1">
            </td>

            <td>
                <div class="precio-wrapper">
                    <input type="number" class="form-control precio" step="0.01">
                </div>
            </td>

            <td>
                <input type="number" class="form-control total" step="0.01">
            </td>

            <td class="text-right">

                <button type="button" class="btn btn-light btn-sm ver-img-btn d-none">
                    <i class="fa fa-eye"></i>
                </button>

                <button type="button" class="btn btn-danger btn-sm removeRow">
                    <i class="fa fa-trash"></i>
                </button>

            </td>
        `;

            table.appendChild(row);
            actualizarIndices();
            bindRowEvents(row);
        }

        function actualizarIndices() {
            document.querySelectorAll('#productosTable tbody tr').forEach((row, index) => {
                row.querySelector('.row-index').innerText = index + 1;
            });
        }

        function mostrarBadgePrecio(row) {

            let wrapper = row.querySelector('.precio-wrapper');

            // eliminar anterior
            let old = wrapper.querySelector('.badge-sugerido');
            if (old) old.remove();

            let badge = document.createElement('span');
            badge.className = 'badge-sugerido';
            badge.innerText = 'Último costo';

            wrapper.appendChild(badge);
        }

        function renderAddRowLine() {
            const existing = document.querySelector('.add-row-line');
            if (existing) existing.remove();

            const tr = document.createElement('tr');
            tr.classList.add('add-row-line');

            tr.innerHTML = `
                <td colspan="6">
                    <a href="#" class="add-line-link">
                        <i class="fa fa-plus"></i> Agregar línea
                    </a>
                </td>
            `;

            tr.querySelector('a').addEventListener('click', function(e) {
                e.preventDefault();
                addRow();
            });

            table.appendChild(tr);
        }

        function bindRowEvents(row) {

            const select = row.querySelector('.producto-select');
            const cantidad = row.querySelector('.cantidad');
            const precio = row.querySelector('.precio');
            const total = row.querySelector('.total');
            let editandoDesdeTotal = false;

            total.addEventListener('focus', () => editandoDesdeTotal = true);
            precio.addEventListener('focus', () => editandoDesdeTotal = false);
            precio.addEventListener('input', function() {

                let sugerido = parseFloat(row.dataset.sugerido || 0);
                let actual = parseFloat(precio.value || 0);

                let wrapper = row.querySelector('.precio-wrapper');
                let badge = wrapper.querySelector('.badge-sugerido');

                if (actual !== sugerido) {
                    // quitar badge
                    if (badge) badge.remove();
                    precio.classList.remove('precio-sugerido');
                } else {
                    // volver a mostrar
                    if (!badge) mostrarBadgePrecio(row);
                    precio.classList.add('precio-sugerido');
                }
            });

            function calcularDesdeTotal() {

                let cant = parseFloat(cantidad.value) || 0;
                let tot = parseFloat(total.value) || 0;

                if (cant > 0) {
                    let nuevoPrecio = tot / cant;

                    precio.value = nuevoPrecio.toFixed(4);
                    precio.dispatchEvent(new Event('input'));
                }

                calcularTotal();
            }
            $(select).on('select2:select', function(e) {

                let data = e.params.data;

                if (data.id === '__new__') {
                    currentRow = row;

                    let term = $('.select2-search__field').val();

                    $('#productoModal').modal('show');
                    $('input[name="nombre"]').val(term);
                    return;
                }

                row.style.backgroundColor = '#eef6ff';
                row.dataset.stock = data.stock || 0;
                row.dataset.imagen = data.imagen || '';

                const btnImg = row.querySelector('.ver-img-btn');

                if (data.imagen) {
                    btnImg.classList.remove('d-none');

                    let imgUrl = "<?= base_url('upload/productos/') ?>/" + data.imagen;

                    btnImg.onclick = function() {
                        document.getElementById('imagenProductoPreview').src = imgUrl;
                        $('#imagenProductoModal').modal('show');
                    };

                } else {
                    btnImg.classList.add('d-none');
                }

                let sugerido = data.ultimo_costo || data.precio || 0;

                // guardar sugerido en dataset
                row.dataset.sugerido = sugerido;

                // asignar valor
                precio.value = sugerido;

                // marcar como sugerido
                precio.classList.add('precio-sugerido');

                // mostrar badge
                mostrarBadgePrecio(row);

                // focus
                cantidad.focus();
            });

            function formatProducto(producto) {

                // 🔥 CASO CREAR PRODUCTO
                if (producto.id === '__new__') {
                    return $(`
                        <div style="
                            padding:8px;
                            font-weight:600;
                            color:#0d6efd;
                        ">
                            ${producto.text}
                        </div>
                    `);
                }

                if (!producto.id) return producto.text;

                let foto = producto.imagen ?
                    "<?= base_url('upload/productos/') ?>/" + producto.imagen :
                    'https://via.placeholder.com/55';

                let stock = producto.stock ?? 0;

                return $(`
                    <div style="display:flex; gap:10px; align-items:center;">
                        
                        <img src="${foto}" style="
                            width:50px;
                            height:50px;
                            object-fit:cover;
                            border-radius:8px;
                        ">

                        <div style="flex:1;">
                            <div style="font-weight:600;">
                                ${producto.text}
                            </div>

                            <div style="display:flex; justify-content:space-between; font-size:12px; margin-top:3px;">
                                
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
                                    STOCK: ${stock}
                                </span>

                            </div>
                        </div>
                    </div>
                `);
            }

            function formatProductoSeleccion(producto) {

                // 🔥 IMPORTANTE
                if (producto.id === '__new__') {
                    return $(`
                        <div style="
                            padding:10px;
                            background:#eef6ff;
                            border-radius:6px;
                            text-align:center;
                            font-weight:bold;
                            color:#0d6efd;
                        ">
                            ${producto.text}
                        </div>
                    `);
                }

                if (!producto.id) return producto.text;

                // 🔥 FORZAR TEXTO LIMPIO
                let nombre = producto.text || '';

                return `
                    <span style="font-weight:600;">
                        ${nombre}
                    </span>
                `;
            }

            $(select).select2({
                language: 'es',
                minimumInputLength: 1,
                placeholder: 'Buscar producto...',
                width: '100%',

                templateResult: formatProducto,
                templateSelection: formatProductoSeleccion,
                escapeMarkup: function(markup) {
                    return markup;
                },

                ajax: {
                    url: '<?= base_url('productos/searchAjaxSelect') ?>',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            term: params.term
                        };
                    },
                    processResults: function(data) {

                        let results = data;

                        let term = $(select).data('select2').dropdown.$search.val();

                        if (!results || results.length === 0) {
                            results = [];
                        }

                        if (term && term.length >= 1) {
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

            cantidad.addEventListener('input', calcularFila);
            precio.addEventListener('input', calcularFila);
            total.addEventListener('input', calcularDesdeTotal);

            row.querySelector('.removeRow').addEventListener('click', function() {
                row.remove();
                actualizarIndices();
                calcularTotal();
            });

            function calcularFila() {

                if (editandoDesdeTotal) return;

                const t = (cantidad.value * precio.value) || 0;
                total.value = t.toFixed(2);

                calcularTotal();
            }
        }

        function calcularTotal() {
            let total = 0;

            document.querySelectorAll('.total').forEach(input => {
                total += parseFloat(input.value) || 0;
            });

            totalCompra.innerText = total.toFixed(2);
        }

        document.getElementById('addRowBtn').addEventListener('click', function(e) {
            e.preventDefault();
            addRow();
        });

        // iniciar con una fila
        actualizarIndices();
        addRow();

        // 🧾 SUBMIT
        document.getElementById('compraForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const proveedor_id = this.proveedor_id.value;
            const proveedor_text = $('#proveedor_id option:selected').text();
            const branch_id = this.branch_id.value;
            const branch_text = this.branch_id.options[this.branch_id.selectedIndex]?.text;
            const observacion = document.getElementById('observacion').value;
            const fecha_compra = document.getElementById('fecha_compra').value;

            let errores = [];
            let productosData = [];
            let totalCompra = 0;

            let pagosData = [];
            let totalPagado = 0;

            // 🔥 VALIDACIONES BASE
            if (!proveedor_id) errores.push('Debe seleccionar proveedor');
            if (!branch_id) errores.push('Debe seleccionar sucursal');

            // 🔥 PRODUCTOS
            document.querySelectorAll('#productosTable tbody tr').forEach((row, index) => {

                const producto_id = row.querySelector('.producto-select').value;
                const producto_text = $(row).find('.producto-select option:selected').text();

                const cantidad = parseFloat(row.querySelector('.cantidad').value);
                const precio = parseFloat(row.querySelector('.precio').value);

                if (!producto_id) {
                    errores.push(`Fila ${index + 1}: falta producto`);
                    return;
                }

                if (!cantidad || cantidad <= 0) {
                    errores.push(`Fila ${index + 1}: cantidad inválida`);
                }

                if (!precio || precio <= 0) {
                    errores.push(`Fila ${index + 1}: precio inválido`);
                }

                const total = cantidad * precio;
                totalCompra += total;

                productosData.push({
                    producto_id,
                    producto_text,
                    cantidad,
                    precio,
                    total
                });

            });

            if (productosData.length === 0) {
                errores.push('Debe agregar al menos un producto');
            }

            // 🔥 PAGOS (DESPUÉS de calcular totalCompra)
            document.querySelectorAll('#pagosTable tbody tr').forEach((row, index) => {

                const cuenta_id = $(row).find('.cuenta-select').val();
                const monto = parseFloat(row.querySelector('.monto').value);

                if (!monto || monto <= 0) {
                    errores.push(`Pago ${index + 1}: monto inválido`);
                    return;
                }

                totalPagado += monto;

                pagosData.push({
                    cuenta_id: cuenta_id,
                    monto: monto
                });
            });

            // 🔥 VALIDACIÓN FINAL
            if (Math.abs(totalPagado - totalCompra) > 0.01) {
                errores.push(`Los pagos ($${totalPagado.toFixed(2)}) no cuadran con el total ($${totalCompra.toFixed(2)})`);
            }

            // ❌ SI HAY ERRORES
            if (errores.length > 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Validación',
                    html: errores.map(e => `• ${e}`).join('<br>')
                });
                return;
            }

            // 🧾 PREVIEW ANTES DE GUARDAR
            let resumenHTML = `
                <div style="text-align:left; font-size:14px">

                    <div><b>Proveedor:</b> ${proveedor_text}</div>
                    <div><b>Sucursal:</b> ${branch_text}</div>
                    <div><b>Fecha:</b> ${fecha_compra}</div>
                    <div><b># Productos:</b> ${productosData.length}</div>

                    <hr>

                    <div><b>Total compra:</b> $${totalCompra.toFixed(2)}</div>
                    <div><b>Total pagado:</b> $${totalPagado.toFixed(2)}</div>

                    <hr>

                    <div style="color:${Math.abs(totalPagado - totalCompra) <= 0.01 ? 'green' : 'red'}">
                        <b>${Math.abs(totalPagado - totalCompra) <= 0.01 ? '✔ Cuadrado' : '✖ Diferencia detectada'}</b>
                    </div>

                </div>
            `;

            Swal.fire({
                title: 'Confirmar compra',
                html: resumenHTML,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, guardar',
                cancelButtonText: 'Revisar',
            }).then((result) => {

                if (!result.isConfirmed) return;

                // ✅ AQUÍ recién guardamos
                fetch("<?= base_url('compras/store') ?>", {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            proveedor_id,
                            branch_id,
                            observacion,
                            fecha_compra,
                            productos: productosData,
                            pagos: pagosData
                        })
                    })
                    .then(res => res.json())
                    .then(data => {

                        if (data.status === 'success') {

                            Swal.fire({
                                icon: 'success',
                                title: 'Compra registrada',
                                timer: 1500,
                                showConfirmButton: false
                            });

                            setTimeout(() => {
                                window.location.href = "<?= base_url('compras') ?>";
                            }, 1500);

                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }

                    })
                    .catch(() => {
                        Swal.fire('Error', 'Error de conexión', 'error');
                    });

            });

        });

        $('#proveedor_id').on('select2:select', function(e) {

            let data = e.params.data;

            if (data.id === '__new__') {

                let term = data.text.replace('➕ Crear "', '').replace('"', '');

                $('#edit_nombre').val(term);
                $('#modalProveedor').modal('show');
            }

        });

        $('#proveedor_id').select2({
            language: 'es',
            placeholder: 'Buscar proveedor...',
            width: '100%',
            minimumInputLength: 1,
            ajax: {
                url: '<?= base_url('proveedores/searchAjaxSelect') ?>',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        term: params.term
                    };
                },
                processResults: function(data, params) {

                    let results = data;

                    if (!results || results.length === 0) {

                        let term = params.term || '';

                        results = [{
                            id: '__new__',
                            text: `➕ Crear "${term}"`,
                            newTag: true
                        }];
                    }

                    return {
                        results: results
                    };
                }
            }
        });

        function abrirCamara() {
            document.getElementById('imagenInput').click();
        }
        $('#guardarProveedor').click(function() {

            let nombre = $('#edit_nombre').val().trim();
            let telefono = $('#edit_telefono').val().trim();
            let direccion = $('#edit_direccion').val().trim();

            if (!nombre) {
                Swal.fire('Error', 'Ingrese nombre', 'warning');
                return;
            }

            const btn = $(this);
            btn.prop('disabled', true).text('Guardando...');

            $.post('<?= base_url('proveedores/storeAjax') ?>', {
                nombre: nombre,
                telefono: telefono,
                direccion: direccion
            }, function(res) {

                btn.prop('disabled', false).text('Guardar');

                if (res.status === 'success') {

                    let newOption = new Option(nombre, res.id, true, true);

                    $('#proveedor_id')
                        .append(newOption)
                        .trigger('change');

                    $('#proveedor_id').trigger({
                        type: 'select2:select',
                        params: {
                            data: {
                                id: res.id,
                                text: nombre
                            }
                        }
                    });

                    $('#modalProveedor').modal('hide');

                    // limpiar modal
                    $('#edit_nombre').val('');
                    $('#edit_telefono').val('');
                    $('#edit_direccion').val('');

                    Swal.fire({
                        icon: 'success',
                        title: 'Proveedor creado',
                        timer: 1200,
                        showConfirmButton: false
                    });

                } else {
                    Swal.fire('Error', res.message || 'Error al guardar', 'error');
                }

            }, 'json').fail(() => {

                btn.prop('disabled', false).text('Guardar');
                Swal.fire('Error', 'Error de conexión', 'error');

            });

        });
    });
</script>
<script>
    let webpFile = null;

    document.getElementById('imagenInput').addEventListener('change', function() {

        const file = this.files[0];
        if (!file) return;

        if (!file.type.startsWith('image/')) {
            Swal.fire('Error', 'El archivo debe ser imagen', 'warning');
            this.value = '';
            return;
        }

        const reader = new FileReader();

        reader.onload = function(e) {

            const img = new Image();

            img.onload = function() {

                const canvas = document.createElement('canvas');

                const maxWidth = 800;
                let width = img.width;
                let height = img.height;

                if (width > maxWidth) {
                    height *= maxWidth / width;
                    width = maxWidth;
                }

                canvas.width = width;
                canvas.height = height;

                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, width, height);

                canvas.toBlob(function(blob) {

                    webpFile = new File([blob], 'producto.webp', {
                        type: 'image/webp'
                    });

                    const preview = document.getElementById('previewImg');
                    preview.src = URL.createObjectURL(blob);
                    preview.style.display = 'block';

                }, 'image/webp', 0.8);

            };

            img.src = e.target.result;
        };

        reader.readAsDataURL(file);
    });
    document.getElementById('productoForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const form = this;
        const btn = form.querySelector('button[type="submit"]');

        btn.disabled = true;
        btn.innerText = 'Guardando...';

        const formData = new FormData(form);

        // 🔥 VALIDACIONES
        if (!form.nombre.value.trim()) {
            Swal.fire('Error', 'El nombre es obligatorio', 'warning');
            return reset();
        }

        if (!form.precio.value || form.precio.value <= 0) {
            Swal.fire('Error', 'Precio inválido', 'warning');
            return reset();
        }

        // 🔥 reemplazar imagen por webp optimizado
        if (webpFile) {
            formData.set('imagen', webpFile);
        }

        fetch("<?= base_url('productos/storeAjax') ?>", {
                method: 'POST',
                body: formData
            })
            .then(async res => {

                let text = await res.text();

                try {
                    return JSON.parse(text);
                } catch (e) {

                    console.error("RESPUESTA CRUDA:", text);

                    Swal.fire({
                        icon: 'error',
                        title: 'Error del servidor',
                        text: 'Respuesta inválida del backend'
                    });

                    throw new Error("JSON inválido");
                }
            })
            .then(data => {

                if (data.status === 'success') {

                    let producto = data.producto;

                    // 🔥 insertar en la fila actual
                    if (currentRow) {

                        let select = $(currentRow).find('.producto-select');

                        let newOption = new Option(
                            producto.nombre,
                            producto.id,
                            true,
                            true
                        );

                        select.append(newOption).trigger('change');

                        // 🔥 setear precio automáticamente
                        $(currentRow).find('.precio')
                            .val(producto.precio || 0)
                            .trigger('input');
                    }

                    $('#productoModal').modal('hide');

                    form.reset();

                    Swal.fire({
                        icon: 'success',
                        title: 'Producto creado',
                        timer: 1200,
                        showConfirmButton: false
                    });

                } else {
                    Swal.fire('Error', data.message, 'error');
                }

                reset();
            })
            .catch(err => {

                console.error("ERROR:", err);

                Swal.fire('Error', 'Fallo inesperado', 'error');

                reset();
            });

        function reset() {
            btn.disabled = false;
            btn.innerText = 'Guardar';
        }
    });
</script>
<?= $this->endSection() ?>