<?= $this->extend('Layouts/mainbody') ?>
<?= $this->section('content') ?>

<style>
    /* ── CARD DE PRODUCTO ───────────────────────────────────── */
    .producto-card {
        border: 1px solid #dee2e6;
        border-radius: 10px;
        padding: 12px 14px;
        margin-bottom: 10px;
        background: #fff;
        box-shadow: 0 1px 3px rgba(0,0,0,.06);
        position: relative;
    }

    .producto-card:hover {
        border-color: #b0c4de;
        box-shadow: 0 2px 6px rgba(0,0,0,.10);
    }

    .producto-card .card-num {
        font-size: 11px;
        font-weight: 700;
        color: #adb5bd;
        margin-bottom: 6px;
    }

    .producto-card .del {
        position: absolute;
        top: 10px;
        right: 10px;
        border: none;
        background: transparent;
        color: #dc3545;
        font-size: 18px;
        line-height: 1;
        padding: 0 4px;
        cursor: pointer;
    }

    .producto-card .del:hover { color: #a71d2a; }

    .producto-card .card-fields {
        display: flex;
        gap: 8px;
        margin-top: 8px;
    }

    .producto-card .card-fields .f-cant   { flex: 1;   min-width: 0; }
    .producto-card .card-fields .f-precio { flex: 2;   min-width: 0; }
    .producto-card .card-fields .f-total  { flex: 1.5; min-width: 0; }

    .producto-card .field-label {
        font-size: 10px;
        color: #6c757d;
        font-weight: 600;
        text-transform: uppercase;
        margin-bottom: 2px;
    }

    /* badge "Último costo" */
    .badge-sugerido {
        font-size: 10px;
        background: #198754;
        color: #fff;
        padding: 1px 6px;
        border-radius: 6px;
        margin-left: 4px;
        vertical-align: middle;
    }

    .precio-sugerido { background-color: #e9f7ef !important; }

    /* ── CARD DE PAGO ────────────────────────────────────────── */
    .pago-card {
        border: 1px solid #dee2e6;
        border-radius: 10px;
        padding: 10px 14px;
        margin-bottom: 8px;
        background: #fff;
        box-shadow: 0 1px 3px rgba(0,0,0,.06);
        display: flex;
        gap: 8px;
        align-items: flex-end;
        position: relative;
    }

    .pago-card .card-num {
        font-size: 11px;
        font-weight: 700;
        color: #adb5bd;
        min-width: 18px;
        padding-bottom: 8px;
        flex-shrink: 0;
    }

    .pago-card .pc-cuenta { flex: 1; min-width: 0; }

    .pago-card .pc-monto {
        width: 120px;
        flex-shrink: 0;
    }

    .pago-card .del {
        border: none;
        background: transparent;
        color: #dc3545;
        font-size: 18px;
        line-height: 1;
        padding: 0 4px;
        margin-bottom: 6px;
        flex-shrink: 0;
        cursor: pointer;
    }

    .pago-card .del:hover { color: #a71d2a; }

    .pago-card .field-label {
        font-size: 10px;
        color: #6c757d;
        font-weight: 600;
        text-transform: uppercase;
        margin-bottom: 2px;
    }

    /* ── SHARED ─────────────────────────────────────────────── */
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
        padding: 0 4px;
        cursor: pointer;
    }

    .ver-img-btn:hover { color: #0056b3; }

    .select2-container .select2-selection--single {
        height: 38px !important;
        border: 1px solid #ced4da;
        border-radius: .375rem;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 36px !important;
        padding-left: .75rem;
    }

    .select2-selection__arrow { display: none; }
</style>

<div class="row">
    <div class="col-md-12">
        <div class="card">

            <div class="card-header d-flex align-items-center">
                <h4 class="header-title mb-0">Nueva Compra</h4>
                <a href="<?= base_url('compras') ?>" class="btn btn-secondary btn-sm ml-auto">
                    Volver
                </a>
            </div>

            <div class="card-body">

                <form id="compraForm">

                    <!-- Cabecera del documento -->
                    <div class="row mb-3">

                        <div class="col-md-2">
                            <label>Fecha de compra</label>
                            <input type="date" name="fecha_compra" id="fecha_compra"
                                   class="form-control" value="<?= date('Y-m-d') ?>">
                        </div>

                        <div class="col-md-4">
                            <label>Proveedor</label>
                            <select id="proveedor_id" name="proveedor_id" class="form-control" required></select>
                        </div>

                        <div class="col-md-4">
                            <label>Sucursal receptora</label>
                            <select name="branch_id" class="form-control">
                                <?php foreach ($branches ?? [] as $b): ?>
                                    <option value="<?= $b->id ?>"><?= $b->branch_name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-12 mt-2">
                            <label>Observación</label>
                            <textarea name="observacion" id="observacion" class="form-control"
                                      rows="2" placeholder="Notas de la compra..."></textarea>
                        </div>

                    </div>

                    <!-- PRODUCTOS -->
                    <h6 class="text-muted mb-2" style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;">
                        Productos
                    </h6>

                    <div id="productosTable"></div>

                    <div class="mt-1 mb-3">
                        <a href="#" id="addRowBtn" class="add-line-link">
                            <i class="fa fa-plus"></i> Agregar línea
                        </a>
                    </div>

                    <div class="text-right mt-2">
                        <h4>Total: $<span id="totalCompra">0.00</span></h4>
                    </div>

                    <!-- PAGOS -->
                    <h6 class="text-muted mb-2 mt-4" style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;">
                        Pagos
                    </h6>

                    <div id="pagosTable"></div>

                    <div class="mt-1">
                        <a href="#" id="addPagoBtn" class="add-line-link">
                            <i class="fa fa-plus"></i> Agregar pago
                        </a>
                    </div>

                    <div class="text-right mt-2">
                        <b>Total Pagado: $<span id="totalPagado">0.00</span></b>
                    </div>

                    <div class="mt-4 text-right">
                        <button type="submit" class="btn btn-success">
                            Guardar Compra
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>

<!-- Modal Proveedor -->
<div class="modal fade" id="modalProveedor">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5>Nuevo Proveedor</h5>
            </div>
            <div class="modal-body">
                <label>Nombre</label>
                <input type="text" id="edit_nombre" class="form-control" required>
            </div>
            <div class="modal-body">
                <label>Teléfono</label>
                <input type="number" id="edit_telefono" class="form-control" step="1">
            </div>
            <div class="modal-body">
                <label>Dirección</label>
                <textarea id="edit_direccion" class="form-control"></textarea>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success" id="guardarProveedor">Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Crear Producto -->
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

                        <div class="col-md-6">
                            <label>Nombre</label>
                            <input type="text" name="nombre" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label>Marca</label>
                            <input type="text" name="marca" class="form-control">
                        </div>

                        <div class="col-md-6 mt-2">
                            <label>Presentación</label>
                            <input type="text" name="presentacion" class="form-control">
                        </div>

                        <div class="col-md-6 mt-2">
                            <label>Precio</label>
                            <input type="number" step="0.01" name="precio" class="form-control" required>
                        </div>

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
                                    <span id="scanCheck" class="input-group-text text-success" style="display:none;">✔</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 mt-2" id="scannerContainer" style="display:none;">
                            <div class="border rounded p-2 text-center bg-light">
                                <div id="reader" style="width:100%; max-width:400px; margin:auto;"></div>
                                <button type="button" class="btn btn-sm btn-danger mt-2" onclick="cerrarScanner()">
                                    Cancelar
                                </button>
                            </div>
                        </div>

                        <div class="col-md-12 mt-2">
                            <label>Descripción</label>
                            <textarea name="descripcion" class="form-control"></textarea>
                        </div>

                        <!-- Imagen -->
                        <div class="col-md-12 mt-3">
                            <label>Imagen del producto</label>

                            <div class="d-flex gap-2 mb-2">
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="abrirSelectorArchivo()">
                                    <i class="fa fa-upload"></i> Subir imagen
                                </button>
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="abrirCamara()">
                                    <i class="fa fa-camera"></i> Usar cámara
                                </button>
                            </div>

                            <input type="file" id="imagenInput" accept="image/*" style="display:none;">

                            <div id="cameraContainer" style="display:none;" class="mt-2 text-center">
                                <video id="cameraPreview" autoplay playsinline
                                       style="width:100%; max-width:400px; border-radius:10px;"></video>
                                <div class="mt-2">
                                    <button type="button" class="btn btn-success btn-sm" onclick="capturarFoto()">
                                        📸 Tomar foto
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="cerrarCamara()">
                                        Cancelar
                                    </button>
                                </div>
                            </div>

                            <img id="previewImg" style="max-width:200px; margin-top:10px; display:none; border-radius:8px;">
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Guardar</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- Modal ver imagen producto -->
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

<!-- ── Scanner de código de barras del modal producto ──────── -->
<script>
    let html5QrCode   = null;
    let scannerActivo = false;

    function toggleScanner() {
        if (scannerActivo) { cerrarScanner(); return; }

        document.getElementById('scannerContainer').style.display = 'block';
        html5QrCode = new Html5Qrcode('reader');
        html5QrCode.start(
            { facingMode: 'environment' },
            { fps: 10, qrbox: 250 },
            (decodedText) => {
                document.getElementById('codigo_barras').value = decodedText;
                mostrarCheck();
                cerrarScanner();
            }
        ).catch(err => console.error(err));
        scannerActivo = true;
    }

    function cerrarScanner() {
        if (html5QrCode) html5QrCode.stop().then(() => html5QrCode.clear()).catch(() => {});
        document.getElementById('scannerContainer').style.display = 'none';
        scannerActivo = false;
    }

    function mostrarCheck() {
        const check = document.getElementById('scanCheck');
        check.style.display = 'inline';
        setTimeout(() => check.style.display = 'none', 2000);
    }

    function generarCodigo() {
        const codigo = 'P' + Date.now();
        document.getElementById('codigo_barras').value = codigo;
        Swal.fire({ icon: 'success', title: 'Código generado', text: codigo, timer: 1200, showConfirmButton: false });
    }
</script>

<!-- ── Cámara / imagen del modal producto ──────────────────── -->
<script>
    let webpFile = null;
    let cameraStream = null;

    function abrirSelectorArchivo() {
        document.getElementById('imagenInput').click();
    }

    document.getElementById('imagenInput').addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;
        if (!file.type.startsWith('image/')) {
            Swal.fire('Error', 'El archivo debe ser una imagen', 'warning');
            this.value = '';
            return;
        }
        convertirAWebp(file);
    });

    function convertirAWebp(file) {
        const reader = new FileReader();
        reader.onload = function (e) {
            const img = new Image();
            img.onload = function () {
                const canvas   = document.createElement('canvas');
                const maxWidth = 800;
                let w = img.width, h = img.height;
                if (w > maxWidth) { h *= maxWidth / w; w = maxWidth; }
                canvas.width  = w;
                canvas.height = h;
                canvas.getContext('2d').drawImage(img, 0, 0, w, h);
                canvas.toBlob(function (blob) {
                    webpFile = new File([blob], 'producto.webp', { type: 'image/webp' });
                    const preview = document.getElementById('previewImg');
                    preview.src   = URL.createObjectURL(blob);
                    preview.style.display = 'block';
                }, 'image/webp', 0.8);
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }

    function abrirCamara() {
        const container = document.getElementById('cameraContainer');
        const video     = document.getElementById('cameraPreview');
        navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
            .then(function (s) {
                cameraStream = s;
                video.srcObject = s;
                container.style.display = 'block';
            })
            .catch(function () {
                Swal.fire('Error', 'No se pudo acceder a la cámara', 'error');
            });
    }

    function cerrarCamara() {
        if (cameraStream) cameraStream.getTracks().forEach(t => t.stop());
        cameraStream = null;
        document.getElementById('cameraContainer').style.display = 'none';
    }

    function capturarFoto() {
        const video  = document.getElementById('cameraPreview');
        const canvas = document.createElement('canvas');
        canvas.width  = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0);
        canvas.toBlob(function (blob) {
            webpFile = new File([blob], 'producto.webp', { type: 'image/webp' });
            const preview = document.getElementById('previewImg');
            preview.src   = URL.createObjectURL(blob);
            preview.style.display = 'block';
            cerrarCamara();
        }, 'image/webp', 0.8);
    }

    $('#productoModal').on('hidden.bs.modal', function () {
        cerrarCamara();
        webpFile = null;
        const preview = document.getElementById('previewImg');
        preview.src = '';
        preview.style.display = 'none';
        document.getElementById('imagenInput').value = '';
    });
</script>

<!-- ── Lógica principal ─────────────────────────────────────── -->
<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ════════════════════════════════════════
       PROVEEDOR select2
    ════════════════════════════════════════ */
    $('#proveedor_id').select2({
        language: 'es',
        placeholder: 'Buscar proveedor...',
        width: '100%',
        minimumInputLength: 1,
        ajax: {
            url: '<?= base_url('proveedores/searchAjaxSelect') ?>',
            dataType: 'json',
            delay: 250,
            data: params => ({ term: params.term }),
            processResults: function (data, params) {
                let results = data;
                if (!results || results.length === 0) {
                    results = [{ id: '__new__', text: `➕ Crear "${params.term}"`, newTag: true }];
                }
                return { results };
            }
        }
    });

    $('#proveedor_id').on('select2:select', function (e) {
        let data = e.params.data;
        if (data.id === '__new__') {
            let term = data.text.replace('➕ Crear "', '').replace('"', '');
            $('#edit_nombre').val(term);
            $('#modalProveedor').modal('show');
        }
    });

    /* ════════════════════════════════════════
       GUARDAR PROVEEDOR (modal)
    ════════════════════════════════════════ */
    $('#guardarProveedor').click(function () {
        let nombre    = $('#edit_nombre').val().trim();
        let telefono  = $('#edit_telefono').val().trim();
        let direccion = $('#edit_direccion').val().trim();

        if (!nombre) { Swal.fire('Error', 'Ingrese nombre', 'warning'); return; }

        const btn = $(this);
        btn.prop('disabled', true).text('Guardando...');

        $.post('<?= base_url('proveedores/storeAjax') ?>', { nombre, telefono, direccion }, function (res) {
            btn.prop('disabled', false).text('Guardar');
            if (res.status === 'success') {
                $('#proveedor_id').append(new Option(nombre, res.id, true, true)).trigger('change');
                $('#modalProveedor').modal('hide');
                $('#edit_nombre, #edit_telefono, #edit_direccion').val('');
                Swal.fire({ icon: 'success', title: 'Proveedor creado', timer: 1200, showConfirmButton: false });
            } else {
                Swal.fire('Error', res.message || 'Error al guardar', 'error');
            }
        }, 'json').fail(() => {
            btn.prop('disabled', false).text('Guardar');
            Swal.fire('Error', 'Error de conexión', 'error');
        });
    });

    /* ════════════════════════════════════════
       PRODUCTOS — helpers de formato select2
    ════════════════════════════════════════ */
    function formatProducto(producto) {
        if (producto.id === '__new__') {
            return $(`<div style="padding:8px;font-weight:600;color:#0d6efd;">${producto.text}</div>`);
        }
        if (!producto.id) return producto.text;

        let foto  = producto.imagen
            ? "<?= base_url('upload/productos/') ?>/" + producto.imagen
            : 'https://via.placeholder.com/55';
        let stock = producto.stock ?? 0;

        return $(`
            <div style="display:flex;gap:10px;align-items:center;">
                <img src="${foto}" style="width:50px;height:50px;object-fit:cover;border-radius:8px;">
                <div style="flex:1;">
                    <div style="font-weight:600;">${producto.text}</div>
                    <div style="display:flex;justify-content:space-between;font-size:12px;margin-top:3px;">
                        <span style="color:#666;">$${producto.precio || '0.00'}</span>
                        <span style="font-weight:bold;padding:2px 6px;border-radius:6px;
                            background:${stock > 0 ? '#e9f7ef' : '#fdecea'};
                            color:${stock > 0 ? '#198754' : '#dc3545'};">
                            STOCK: ${stock}
                        </span>
                    </div>
                </div>
            </div>
        `);
    }

    function formatProductoSeleccion(producto) {
        if (producto.id === '__new__') {
            return $(`<div style="padding:10px;background:#eef6ff;border-radius:6px;text-align:center;font-weight:bold;color:#0d6efd;">${producto.text}</div>`);
        }
        if (!producto.id) return producto.text;
        return `<span style="font-weight:600;">${producto.text || ''}</span>`;
    }

    /* ════════════════════════════════════════
       PRODUCTOS — tabla de cards
    ════════════════════════════════════════ */
    const productosTable = document.getElementById('productosTable');
    const totalCompraEl  = document.getElementById('totalCompra');

    function addRow() {
        const card = document.createElement('div');
        card.className = 'producto-card';

        card.innerHTML = `
            <div class="card-num"></div>
            <button type="button" class="del" title="Quitar">×</button>

            <select class="form-control producto-select w-100"></select>

            <div class="card-fields">
                <div class="f-cant">
                    <div class="field-label">Cantidad</div>
                    <input type="number" class="form-control cantidad" min="1">
                </div>
                <div class="f-precio">
                    <div class="field-label">
                        Precio costo
                        <span class="badge-sugerido" style="display:none;">Último costo</span>
                    </div>
                    <input type="number" class="form-control precio" step="0.01">
                </div>
                <div class="f-total">
                    <div class="field-label">Total</div>
                    <input type="number" class="form-control total" step="0.01" readonly>
                </div>
            </div>

            <button type="button" class="btn btn-sm btn-light ver-img-btn mt-2 d-none">
                <i class="fa fa-eye"></i> Ver imagen
            </button>
        `;

        productosTable.appendChild(card);
        actualizarIndices();
        bindRowEvents(card);
    }

    function actualizarIndices() {
        document.querySelectorAll('#productosTable .producto-card').forEach((card, i) => {
            card.querySelector('.card-num').innerText = `Producto #${i + 1}`;
        });
    }

    function calcularTotal() {
        let t = 0;
        document.querySelectorAll('#productosTable .total').forEach(input => {
            t += parseFloat(input.value) || 0;
        });
        totalCompraEl.innerText = t.toFixed(2);
    }

    function bindRowEvents(card) {
        const select   = card.querySelector('.producto-select');
        const cantidad = card.querySelector('.cantidad');
        const precio   = card.querySelector('.precio');
        const total    = card.querySelector('.total');
        const badge    = card.querySelector('.badge-sugerido');
        let editandoDesdeTotal = false;

        total.addEventListener('focus',  () => editandoDesdeTotal = true);
        precio.addEventListener('focus', () => editandoDesdeTotal = false);

        precio.addEventListener('input', function () {
            const sugerido = parseFloat(card.dataset.sugerido || 0);
            const actual   = parseFloat(precio.value || 0);

            if (Math.abs(actual - sugerido) < 0.001) {
                badge.style.display = '';
                precio.classList.add('precio-sugerido');
            } else {
                badge.style.display = 'none';
                precio.classList.remove('precio-sugerido');
            }
        });

        function calcularDesdeTotal() {
            const cant = parseFloat(cantidad.value) || 0;
            const tot  = parseFloat(total.value)    || 0;
            if (cant > 0) {
                precio.value = (tot / cant).toFixed(4);
                precio.dispatchEvent(new Event('input'));
            }
            calcularTotal();
        }

        function calcularFila() {
            if (editandoDesdeTotal) return;
            total.value = ((parseFloat(cantidad.value) || 0) * (parseFloat(precio.value) || 0)).toFixed(2);
            calcularTotal();
        }

        // select2 en el select de esta card
        $(select).select2({
            language: 'es',
            minimumInputLength: 1,
            placeholder: 'Buscar producto...',
            width: '100%',
            templateResult: formatProducto,
            templateSelection: formatProductoSeleccion,
            escapeMarkup: m => m,
            ajax: {
                url: '<?= base_url('productos/searchAjaxSelect') ?>',
                dataType: 'json',
                delay: 250,
                data: params => ({ term: params.term }),
                processResults: function (data) {
                    let results = data || [];
                    let term = $(select).data('select2').dropdown.$search.val();
                    if (term && term.length >= 1) {
                        results.push({ id: '__new__', text: `➕ Crear "${term}"`, newTag: true });
                    }
                    return { results };
                }
            }
        });

        $(select).on('select2:select', function (e) {
            const data = e.params.data;

            if (data.id === '__new__') {
                currentRow = card;
                const term = $('.select2-search__field').val();
                $('#productoModal').modal('show');
                $('input[name="nombre"]').val(term);
                return;
            }

            const sugerido = data.ultimo_costo || data.precio || 0;
            card.dataset.sugerido = sugerido;
            card.dataset.stock    = data.stock  || 0;
            card.dataset.imagen   = data.imagen || '';

            precio.value = sugerido;
            precio.classList.add('precio-sugerido');
            badge.style.display = '';

            const btnImg = card.querySelector('.ver-img-btn');
            if (data.imagen) {
                btnImg.classList.remove('d-none');
                btnImg.onclick = function () {
                    document.getElementById('imagenProductoPreview').src =
                        "<?= base_url('upload/productos/') ?>/" + data.imagen;
                    $('#imagenProductoModal').modal('show');
                };
            } else {
                btnImg.classList.add('d-none');
            }

            cantidad.focus();
        });

        cantidad.addEventListener('input', calcularFila);
        precio.addEventListener('input',   calcularFila);
        total.addEventListener('input',    calcularDesdeTotal);

        card.querySelector('.del').addEventListener('click', function () {
            card.remove();
            actualizarIndices();
            calcularTotal();
        });
    }

    document.getElementById('addRowBtn').addEventListener('click', function (e) {
        e.preventDefault();
        addRow();
    });

    // fila inicial
    addRow();

    /* ════════════════════════════════════════
       PAGOS — cards
    ════════════════════════════════════════ */
    const pagosTable    = document.getElementById('pagosTable');
    const totalPagadoEl = document.getElementById('totalPagado');

    function addPagoRow() {
        const card = document.createElement('div');
        card.className = 'pago-card';

        card.innerHTML = `
            <span class="card-num"></span>
            <div class="pc-cuenta">
                <div class="field-label">Cuenta</div>
                <select class="cuenta-select w-100"></select>
            </div>
            <div class="pc-monto">
                <div class="field-label">Monto</div>
                <input type="number" class="form-control monto" step="0.01">
            </div>
            <button class="del" type="button" title="Quitar">×</button>
        `;

        pagosTable.appendChild(card);
        actualizarIndicesPagos();
        bindPagoEvents(card);
    }

    function actualizarIndicesPagos() {
        document.querySelectorAll('#pagosTable .pago-card').forEach((card, i) => {
            card.querySelector('.card-num').innerText = i + 1;
        });
    }

    function calcularTotalPagado() {
        let total = 0;
        document.querySelectorAll('#pagosTable .monto').forEach(input => {
            total += parseFloat(input.value) || 0;
        });
        totalPagadoEl.innerText = total.toFixed(2);
    }

    function bindPagoEvents(card) {
        $(card).find('.cuenta-select').select2({
            placeholder: 'Buscar cuenta...',
            width: '100%',
            ajax: {
                url: '<?= base_url('accounts-listAjax') ?>',
                dataType: 'json',
                delay: 250,
                data: params => ({ term: params.term }),
                processResults: data => ({ results: data })
            }
        });

        card.querySelector('.monto').addEventListener('input', calcularTotalPagado);

        card.querySelector('.del').addEventListener('click', function () {
            card.remove();
            actualizarIndicesPagos();
            calcularTotalPagado();
        });
    }

    document.getElementById('addPagoBtn').addEventListener('click', function (e) {
        e.preventDefault();
        addPagoRow();
    });

    // pago inicial
    addPagoRow();

    /* ════════════════════════════════════════
       SUBMIT
    ════════════════════════════════════════ */
    document.getElementById('compraForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const proveedor_id   = this.proveedor_id.value;
        const proveedor_text = $('#proveedor_id option:selected').text();
        const branch_id      = this.branch_id.value;
        const branch_text    = this.branch_id.options[this.branch_id.selectedIndex]?.text;
        const observacion    = document.getElementById('observacion').value;
        const fecha_compra   = document.getElementById('fecha_compra').value;

        let errores       = [];
        let productosData = [];
        let totalCompra   = 0;
        let pagosData     = [];
        let totalPagado   = 0;

        if (!proveedor_id) errores.push('Debe seleccionar proveedor');
        if (!branch_id)    errores.push('Debe seleccionar sucursal');

        // productos
        document.querySelectorAll('#productosTable .producto-card').forEach((card, index) => {
            const producto_id   = card.querySelector('.producto-select').value;
            const producto_text = $(card).find('.producto-select option:selected').text();
            const cant          = parseFloat(card.querySelector('.cantidad').value);
            const prec          = parseFloat(card.querySelector('.precio').value);

            if (!producto_id)           errores.push(`Producto #${index + 1}: falta seleccionar`);
            if (!cant || cant <= 0)     errores.push(`Producto #${index + 1}: cantidad inválida`);
            if (!prec || prec <= 0)     errores.push(`Producto #${index + 1}: precio inválido`);

            const tot = cant * prec;
            totalCompra += tot;

            productosData.push({ producto_id, producto_text, cantidad: cant, precio: prec, total: tot });
        });

        if (productosData.length === 0) errores.push('Debe agregar al menos un producto');

        // pagos
        document.querySelectorAll('#pagosTable .pago-card').forEach((card, index) => {
            const cuenta_id = $(card).find('.cuenta-select').val();
            const monto     = parseFloat(card.querySelector('.monto').value);

            if (!monto || monto <= 0) {
                errores.push(`Pago #${index + 1}: monto inválido`);
                return;
            }

            totalPagado += monto;
            pagosData.push({ cuenta_id, monto });
        });

        if (Math.abs(totalPagado - totalCompra) > 0.01) {
            errores.push(`Los pagos ($${totalPagado.toFixed(2)}) no cuadran con el total ($${totalCompra.toFixed(2)})`);
        }

        if (errores.length > 0) {
            Swal.fire({ icon: 'warning', title: 'Validación', html: errores.map(e => `• ${e}`).join('<br>') });
            return;
        }

        // preview
        const resumenHTML = `
            <div style="text-align:left;font-size:14px;">
                <div><b>Proveedor:</b> ${proveedor_text}</div>
                <div><b>Sucursal:</b> ${branch_text}</div>
                <div><b>Fecha:</b> ${fecha_compra}</div>
                <div><b># Productos:</b> ${productosData.length}</div>
                <hr>
                <div><b>Total compra:</b> $${totalCompra.toFixed(2)}</div>
                <div><b>Total pagado:</b> $${totalPagado.toFixed(2)}</div>
                <hr>
                <div style="color:green;"><b>✔ Cuadrado</b></div>
            </div>
        `;

        Swal.fire({
            title: 'Confirmar compra',
            html: resumenHTML,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, guardar',
            cancelButtonText: 'Revisar',
        }).then(result => {
            if (!result.isConfirmed) return;

            fetch("<?= base_url('compras/store') ?>", {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ proveedor_id, branch_id, observacion, fecha_compra, productos: productosData, pagos: pagosData })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({ icon: 'success', title: 'Compra registrada', timer: 1500, showConfirmButton: false });
                    setTimeout(() => window.location.href = "<?= base_url('compras') ?>", 1500);
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            })
            .catch(() => Swal.fire('Error', 'Error de conexión', 'error'));
        });
    });

    /* ════════════════════════════════════════
       GUARDAR PRODUCTO (modal)
    ════════════════════════════════════════ */
    document.getElementById('productoForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const form = this;
        const btn  = form.querySelector('button[type="submit"]');

        if (!form.nombre.value.trim()) {
            Swal.fire('Error', 'El nombre es obligatorio', 'warning'); return;
        }
        if (!form.precio.value || form.precio.value <= 0) {
            Swal.fire('Error', 'Precio inválido', 'warning'); return;
        }

        btn.disabled  = true;
        btn.innerText = 'Guardando...';

        const formData = new FormData(form);
        if (webpFile) formData.set('imagen', webpFile);

        fetch("<?= base_url('productos/storeAjax') ?>", { method: 'POST', body: formData })
            .then(async res => {
                const text = await res.text();
                try { return JSON.parse(text); }
                catch (e) { console.error('Respuesta cruda:', text); throw new Error('JSON inválido'); }
            })
            .then(data => {
                if (data.status === 'success') {
                    const producto = data.producto;

                    if (currentRow) {
                        const select    = $(currentRow).find('.producto-select');
                        const newOption = new Option(producto.nombre, producto.id, true, true);
                        select.append(newOption).trigger('change');
                        $(currentRow).find('.precio').val(producto.precio || 0).trigger('input');
                    }

                    $('#productoModal').modal('hide');
                    form.reset();
                    Swal.fire({ icon: 'success', title: 'Producto creado', timer: 1200, showConfirmButton: false });
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            })
            .catch(() => Swal.fire('Error', 'Fallo inesperado', 'error'))
            .finally(() => { btn.disabled = false; btn.innerText = 'Guardar'; });
    });

});
</script>

<?= $this->endSection() ?>