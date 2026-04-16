<?= $this->extend('Layouts/mainbody') ?>
<?= $this->section('content') ?>

<style>
    #orderSelect {
        min-height: 38px;
    }

    #productos-table td,
    #productos-table th {
        padding: 5px 10px;
        vertical-align: middle;
    }

    #productos-table {
        font-size: 16px;
    }

    .producto-card {
        transition: all 0.2s ease;
        border-radius: 10px;
    }

    .producto-card:hover {
        transform: scale(1.01);
    }

    .badge {
        font-size: 14px;
        padding: 6px 10px;
    }

    .modal-dialog {
        max-width: 95%;
    }

    .producto-card .card-body {
        padding: 10px 15px;
    }

    .producto-card {
        margin-bottom: 8px;
    }

    small.text-muted {
        font-size: 13px;
        line-height: 1;
        margin-right: 8px;
    }

    .producto-card h5 {
        font-size: 16px;
    }

    .stock-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        font-size: 12px;
        padding: 5px 8px;
        border-radius: 7px;
    }

    .producto-row>div {
        border-right: 1px solid #eee;
        padding-right: 10px;
    }

    .producto-row>div:last-child {
        border-right: none;
    }
</style>

<div class="row">
    <div class="col-md-12">
        <div class="card">

            <div class="card-header d-flex">
                <h4 class="header-title">Inventario - Productos</h4>

                <?php if (tienePermiso('crear_producto')): ?>
                    <button class="btn btn-primary btn-sm ml-auto" data-toggle="modal" data-target="#productoModal">
                        <i class="fa-solid fa-plus"></i> Nuevo Producto
                    </button>
                <?php endif; ?>
            </div>

            <div class="card-body">

                <!-- 🔍 FILTROS -->
                <div class="row mb-3 align-items-end">

                    <div class="col-md-6">
                        <label for="searchInput">Buscar producto</label>
                        <div class="input-group">
                            <input type="text" id="searchInput" class="form-control"
                                placeholder="Nombre o descripción">

                            <div class="input-group-append">
                                <span class="input-group-text" id="loading-spinner" style="display: none;">
                                    <i class="fa fa-spinner fa-spin"></i>
                                </span>

                                <button class="btn btn-secondary" id="clearSearchBtn" style="display: none;">
                                    <i class="fa fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Orden -->
                    <div class="col-md-2">
                        <label>Orden</label>
                        <select id="orderSelect" class="form-control">
                            <option value="recent">Más recientes</option>
                            <option value="alpha_asc">A → Z</option>
                            <option value="alpha_desc">Z → A</option>
                            <option value="precio_asc">Precio ↑</option>
                            <option value="precio_desc">Precio ↓</option>
                            <option value="stock_asc">Stock ↑</option>
                            <option value="stock_desc">Stock ↓</option>
                        </select>
                    </div>

                    <!-- 📊 Resultados -->
                    <div class="col-md-2">
                        <div class="d-flex justify-content-end align-items-center">
                            <label class="mr-2 mb-0">Resultados:</label>
                            <select id="perPageSelect" class="form-control">
                                <option value="10">10</option>
                                <option value="20">20</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label>Stock</label>
                        <select id="stockFilter" class="form-control">
                            <option value="">Todos</option>
                            <option value="con_stock">Con stock</option>
                            <option value="sin_stock">Sin stock</option>
                        </select>
                    </div>
                </div>

                <!-- 📋 TABLA -->
                <div id="table-container">
                    <?= $this->include('inventario/_productos_table') ?>
                </div>

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
                                <input type="text" name="codigo_barras" id="codigo_barras" class="form-control" required>

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
                            <input type="file" name="imagen" class="form-control" accept="image/*">
                            <img id="previewImg" style="max-width:200px; margin-top:10px; display:none;">
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
<div class="modal fade" id="imagenModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-body text-center">
                <img id="imagenPreviewModal" style="max-width:100%; border-radius:10px;">
            </div>

        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode"></script>

<script>
    let html5QrCode = null;
    let scannerActivo = false;

    // 📷 SCANNER
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
            html5QrCode.stop().then(() => {
                html5QrCode.clear();
            }).catch(() => {});
        }

        container.style.display = 'none';
        scannerActivo = false;
    }

    // ✔ feedback
    function mostrarCheck() {
        const check = document.getElementById('scanCheck');
        check.style.display = 'inline';

        setTimeout(() => {
            check.style.display = 'none';
        }, 2000);
    }

    // ⚙️ GENERAR CÓDIGO
    function generarCodigo() {

        const input = document.getElementById('codigo_barras');

        Swal.fire({
            title: 'Generar código',
            text: 'Se generará automáticamente',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Generar'
        }).then(result => {

            if (!result.isConfirmed) return;

            const codigo = 'P' + Date.now(); // único simple

            input.value = codigo;

            Swal.fire({
                icon: 'success',
                title: 'Código generado',
                text: codigo,
                timer: 1200,
                showConfirmButton: false
            });
        });
    }

    // 🚀 FORM
    document.addEventListener('DOMContentLoaded', function() {

        const form = document.getElementById('productoForm');
        const codigoInput = document.getElementById('codigo_barras');
        const submitBtn = form.querySelector('button[type="submit"]');

        // limpiar error al escribir
        codigoInput.addEventListener('input', function() {
            this.classList.remove('is-invalid');
        });

        form.addEventListener('submit', function(e) {
            e.preventDefault();

            submitBtn.disabled = true;
            submitBtn.innerText = 'Guardando...';

            const formData = new FormData(form);

            // 🔥 VALIDACIONES
            if (!form.nombre.value.trim()) {
                Swal.fire('Error', 'El nombre es obligatorio', 'warning');
                resetBtn();
                return;
            }

            if (!form.precio.value || form.precio.value <= 0) {
                Swal.fire('Error', 'Precio inválido', 'warning');
                resetBtn();
                return;
            }

            // 🔥 CÓDIGO REQUERIDO
            if (!codigoInput.value.trim()) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Código requerido',
                    text: 'Escanea o genera un código de barras'
                });

                codigoInput.classList.add('is-invalid');
                codigoInput.focus();
                resetBtn();
                return;
            }

            fetch("<?= base_url('inventario/store') ?>", {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {

                    resetBtn();

                    if (data.status === 'success') {

                        Swal.fire({
                            icon: 'success',
                            title: 'Producto creado',
                            timer: 1200,
                            showConfirmButton: false
                        });

                        $('#productoModal').modal('hide');
                        form.reset();

                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }

                })
                .catch(() => {
                    resetBtn();
                    Swal.fire('Error', 'Error de conexión', 'error');
                });

            function resetBtn() {
                submitBtn.disabled = false;
                submitBtn.innerText = 'Guardar';
            }
        });

        // 🧹 limpiar al cerrar modal
        $('#productoModal').on('hidden.bs.modal', function() {
            form.reset();
            cerrarScanner();
            document.getElementById('scanCheck').style.display = 'none';
        });

    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        const inputImg = document.querySelector('input[name="imagen"]');
        const preview = document.getElementById('previewImg');

        let webpFile = null; // 👈 aquí guardamos la imagen convertida

        inputImg.addEventListener('change', function() {

            const file = this.files[0];

            if (!file) return;

            if (!file.type.startsWith('image/')) {
                Swal.fire('Error', 'El archivo debe ser una imagen', 'warning');
                this.value = '';
                preview.style.display = 'none';
                return;
            }

            const reader = new FileReader();

            reader.onload = function(event) {

                const img = new Image();

                img.onload = function() {

                    const canvas = document.createElement('canvas');

                    // 🔥 tamaño máximo (opcional)
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

                    // 🔥 convertir a webp
                    canvas.toBlob(function(blob) {

                        webpFile = new File([blob], 'producto.webp', {
                            type: 'image/webp'
                        });

                        // preview
                        preview.src = URL.createObjectURL(blob);
                        preview.style.display = 'block';

                    }, 'image/webp', 0.8); // calidad 80%
                };

                img.src = event.target.result;
            };

            reader.readAsDataURL(file);
        });


        const form = document.getElementById('productoForm');
        const submitBtn = form.querySelector('button[type="submit"]');

        submitBtn.innerText = 'Guardar';

        form.addEventListener('submit', function(e) {
            e.preventDefault();

            submitBtn.disabled = true;
            submitBtn.innerText = 'Guardando...';

            const formData = new FormData(form);

            // reemplazar imagen por webp
            if (webpFile) {
                formData.set('imagen', webpFile);
            }

            if (!form.nombre.value.trim()) {
                Swal.fire('Error', 'El nombre es obligatorio', 'warning');
                submitBtn.disabled = false;
                submitBtn.innerText = 'Guardar';
                return;
            }

            if (!form.precio.value || form.precio.value <= 0) {
                Swal.fire('Error', 'Precio inválido', 'warning');
                submitBtn.disabled = false;
                submitBtn.innerText = 'Guardar';
                return;
            }

            if (inputImg.files.length && !webpFile) {
                Swal.fire('Espere', 'Procesando imagen...', 'info');
                submitBtn.disabled = false;
                submitBtn.innerText = 'Guardar';
                return;
            }

            fetch("<?= base_url('inventario/store') ?>", {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {

                    // 🔓 reactivar botón
                    submitBtn.disabled = false;
                    submitBtn.innerText = 'Guardar';

                    if (data.status === 'success') {

                        Swal.fire({
                            icon: 'success',
                            title: 'Producto creado',
                            timer: 1500,
                            showConfirmButton: false
                        });

                        $('#productoModal').modal('hide');
                        form.reset();

                        loadResults(''); // 👈 mejor que dispatchEvent
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }

                })
                .catch(() => {

                    submitBtn.disabled = false;
                    submitBtn.innerText = 'Guardar';

                    Swal.fire('Error', 'Error de conexión', 'error');
                });
        });


        const searchInput = document.getElementById('searchInput');
        const tableContainer = document.getElementById('table-container');
        const loadingSpinner = document.getElementById('loading-spinner');
        const clearSearchBtn = document.getElementById('clearSearchBtn');

        const baseUrl = '<?= base_url('inventario/searchAjax') ?>';

        let searchTimeout;

        function loadResults(query = '', page = 1) {
    const perPage = document.getElementById('perPageSelect').value;
    const order   = document.getElementById('orderSelect').value;
    const stock   = document.getElementById('stockFilter').value;

    const params  = new URLSearchParams({
        q:       query,
        page:    page,
        perPage: perPage,
        order:   order,
        stock:   stock,
    });

    const url = `${baseUrl}?${params.toString()}`;

    loadingSpinner.style.display = 'block';

    fetch(url)
        .then(res => res.text())
        .then(html => {
            tableContainer.innerHTML = html;
            loadingSpinner.style.display = 'none';
            updateClearButton(query);
            rebindEvents();
            searchInput.focus();
        });
}

function rebindEvents() {
    // Interceptar TODOS los links de paginación
    // y extraer solo el número de página, re-lanzando loadResults
    // con los filtros actuales del DOM (no del href)
    document.querySelectorAll('#pagination-links a').forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();

            // El href puede ser ?page=2 o /ruta?page=2
            const url  = new URL(this.href, window.location.origin);
            const page = url.searchParams.get('page') ?? 1;

            loadResults(searchInput.value.trim(), page);
        });
    });
}

        // 🔄 cambios
        document.getElementById('perPageSelect').addEventListener('change', () => loadResults(searchInput.value));
        document.getElementById('orderSelect').addEventListener('change', () => loadResults(searchInput.value));

        // 🔍 búsqueda
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();

            searchTimeout = setTimeout(() => {
                loadResults(query);
            }, 300);
        });

        clearSearchBtn.addEventListener('click', function() {
            searchInput.value = '';
            loadResults('');
            updateClearButton('');
        });

        function updateClearButton(query) {
            clearSearchBtn.style.display = query.length > 0 ? 'block' : 'none';
        }

        function rebindEvents() {
            document.querySelectorAll('#pagination-links a').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const url = new URL(this.href);
                    const page = url.searchParams.get('page');
                    loadResults(searchInput.value.trim(), page);
                });
            });
        }

        // init
        rebindEvents();

        document.addEventListener('click', function(e) {

            const btn = e.target.closest('.ver-imagen-btn');
            if (!btn) return;

            const img = btn.dataset.img;

            if (!img) {
                Swal.fire('Sin imagen', 'Este producto no tiene imagen', 'info');
                return;
            }

            document.getElementById('imagenPreviewModal').src = img;
            $('#imagenModal').modal('show');

        });
        // Listeners
        $('#productoModal').on('hidden.bs.modal', function() {
            form.reset();
            preview.src = '';
            preview.style.display = 'none';
            webpFile = null;
        });
        document.getElementById('stockFilter')
            .addEventListener('change', () => loadResults(searchInput.value));
    });
</script>

<?= $this->endSection() ?>