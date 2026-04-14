<?= $this->extend('Layouts/mainbody') ?>
<?= $this->section('content') ?>

<style>
    .img-header-producto {
        transition: all 0.2s ease;
    }

    .img-header-producto:hover {
        transform: scale(1.05);
    }

    /* CONTENEDOR */
    .nav-tabs {
        border-bottom: 1px solid #e5e7eb;
        gap: 10px;
    }

    /* TAB BASE */
    .nav-tabs .nav-link {
        border: none !important;
        background: transparent !important;
        color: #6c757d;
        font-weight: 500;
        padding: 10px 40px;
        display: flex;
        align-items: center;
        gap: 6px;
        border-radius: 6px;
    }

    /* ICONOS */
    .nav-tabs .nav-link i {
        font-size: 22px;
        opacity: 0.7;
    }

    textarea.auto-resize {
        overflow: hidden;
        resize: none;
    }

    /* HOVER */
    .nav-tabs .nav-link:hover {
        color: #111;
        background: #f8f9fa;
    }

    /* ACTIVO */
    .nav-tabs .nav-link.active {
        color: #111;
        font-weight: 600;
        background: #f8f9fa;
    }

    /* LINEA SUTIL */
    .nav-tabs .nav-link.active::after {
        content: '';
        position: absolute;
        left: 10px;
        right: 10px;
        bottom: -1px;
        height: 2px;
        background: #6c757d;
    }

    /* FIX bootstrap */
    .nav-tabs .nav-link:focus,
    .nav-tabs .nav-link:active {
        outline: none;
        box-shadow: none;
    }

    .border-bottom {
        border-color: #eee !important;
    }

    .d-flex span {
        font-size: 18px;
    }

    .border.rounded:hover {
        background: #f8f9fa;
        transition: 0.2s;
    }
</style>

<div class="row">
    <div class="col-md-12">
        <div class="card">

            <div class="card-body">

                <div class="row">

                    <!-- 🟦 IZQUIERDA (DATOS + TABS) -->
                    <div class="col-md-8">

                        <!-- 📊 INFO GENERAL -->
                        <div class="p-3 border rounded mb-3 bg-light">

                            <!-- 🧾 HEADER -->
                            <div class="d-flex justify-content-between mb-2">

                                <div>
                                    <h5 class="mb-0"><?= esc($producto->nombre) ?></h5>

                                    <small class="text-muted">Producto #<?= $producto->id ?></small>
                                </div>

                                <div class="text-end">
                                    <div class="text-muted" style="font-size:13px;">
                                        Precio
                                    </div>
                                    <div class="fw-bold text-success" style="font-size:20px;">
                                        $<?= number_format($producto->precio, 2) ?>
                                    </div>
                                </div>
                                <?php if (tienePermiso('editar_producto')): ?>
                                    <button
                                        class="btn btn-sm btn-primary mt-2 mb-2"
                                        data-producto='<?= json_encode($producto) ?>'
                                        onclick="editarProductoDesdeBtn(this)">
                                        <i class="fa fa-edit"></i> Editar </button>
                                <?php endif; ?>
                            </div>

                            <hr class="my-2">

                            <!-- 📊 GRID TIPO TABLA -->
                            <div class="row">

                                <div class="col-md-6">
                                    <div class="d-flex justify-content-between border-bottom py-1">
                                        <span class="text-muted">Marca</span>
                                        <strong><?= esc($producto->marca ?: 'N/D') ?></strong>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="d-flex justify-content-between border-bottom py-1">
                                        <span class="text-muted">Presentación</span>
                                        <strong><?= esc($producto->presentacion ?: 'N/D') ?></strong>
                                    </div>
                                </div>

                            </div>

                        </div>

                        <!-- 📝 DESCRIPCIÓN -->
                        <div class="p-3 border rounded mb-3 bg-light">

                            <!-- HEADER -->
                            <div class="d-flex justify-content-between mb-1">

                                <span class="text-muted">Descripción</span>

                                <!-- 📦 STOCK TOTAL -->
                                <div class="text-end">

                                    <small class="text-muted d-block">Stock total</small>

                                    <span class="fw-bold 
                                    <?= $totalStock > 0 ? 'text-success' : 'text-danger' ?>"
                                        style="font-size:22px;">

                                        <?= $totalStock ?>
                                    </span>

                                </div>

                            </div>

                            <!-- CONTENIDO -->
                            <div class="mt-1">
                                <?= esc($producto->descripcion ?: 'Sin descripción') ?>
                            </div>

                        </div>

                        <!-- 🔥 TABS -->
                        <ul class="nav nav-tabs mb-2">

                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#compras">
                                    <i class="fa-solid fa-file-invoice me-1"></i> Compras
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#stock">
                                    <i class="fa-solid fa-boxes-stacked me-1"></i> Stock
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#kardex">
                                    <i class="fa-solid fa-chart-line me-1"></i> Movimientos
                                </a>
                            </li>

                        </ul>

                        <div class="tab-content">

                            <!-- 🧾 COMPRAS -->
                            <div class="tab-pane fade show active" id="compras">

                                <?php if (!empty($compras)): ?>
                                    <?php foreach ($compras as $c): ?>

                                        <div class="border rounded px-3 py-2 mb-2">

                                            <div class="d-flex justify-content-between">

                                                <!-- 🧾 IZQUIERDA -->
                                                <div class="d-flex align-items-center flex-wrap" style="gap:20px;">

                                                    <div class="mr-4">
                                                        <div class="fw-bold" style="font-size:15px;">
                                                            <strong><?= esc($c->proveedor) ?></strong>
                                                        </div>
                                                        <small class="text-muted">
                                                            Fecha aplicada: <?= date('d/m/Y', strtotime($c->fecha_compra ?? $c->created_at)) ?>
                                                        </small>
                                                    </div>

                                                    <div class="mr-4">
                                                        <small class="text-muted d-block">Cantidad</small>
                                                        <span class="fw-semibold">
                                                            <?= $c->cantidad ?>
                                                        </span>
                                                    </div>

                                                    <div class="mr-4">
                                                        <small class="text-muted d-block">Precio</small>
                                                        <span class="fw-semibold">
                                                            $<?= number_format($c->precio_unitario, 2) ?>
                                                        </span>
                                                    </div>

                                                    <div>
                                                        <small class="text-muted d-block">Subtotal</small>
                                                        <span class="fw-semibold">
                                                            $<?= number_format($c->total_producto, 2) ?>
                                                        </span>
                                                    </div>

                                                </div>

                                                <!-- 💰 DERECHA -->
                                                <div class="d-flex align-items-center" style="gap:10px;">

                                                    <div class="fw-bold text-success" style="font-size:18px;">
                                                        $<?= number_format($c->total_producto, 2) ?>
                                                    </div>

                                                    <a href="<?= base_url('compras/' . $c->id) ?>"
                                                        class="btn btn-sm btn-outline-secondary">
                                                        <i class="fa fa-eye"></i>
                                                    </a>

                                                </div>

                                            </div>

                                        </div>

                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="text-muted">Sin compras registradas</p>
                                <?php endif; ?>

                            </div>

                            <!-- 📦 STOCK -->
                            <div class="tab-pane fade" id="stock">

                                <?php if (!empty($stockPorSucursal)): ?>

                                    <?php foreach ($stockPorSucursal as $s): ?>

                                        <div class="border rounded px-3 py-2 mb-2">

                                            <div class="d-flex justify-content-between">

                                                <!-- 🏢 SUCURSAL -->
                                                <div>
                                                    <div class="fw-bold" style="font-size:15px;">
                                                        <strong><?= esc($s->branch_name ?? 'Sucursal #' . $s->branch_id) ?></strong>
                                                    </div>

                                                    <small class="text-muted">
                                                        Inventario actual
                                                    </small>
                                                </div>

                                                <!-- 📦 STOCK -->
                                                <div class="text-end">

                                                    <div class="fw-bold 
                            <?= $s->stock > 0 ? 'text-success' : 'text-danger' ?>"
                                                        style="font-size:18px;">

                                                        <?= $s->stock ?>
                                                    </div>

                                                    <small class="text-muted">
                                                        unidades
                                                    </small>

                                                </div>

                                            </div>

                                        </div>

                                    <?php endforeach; ?>

                                <?php else: ?>
                                    <p class="text-muted">Sin movimientos de inventario</p>
                                <?php endif; ?>

                            </div>

                            <!-- 📊 KARDEX -->
                            <div class="tab-pane fade" id="kardex">

                                <!-- 🔍 FILTROS -->
                                <div class="row mb-2">

                                    <div class="col-md-3">
                                        <select id="filtroTipo" class="form-control form-control-sm">
                                            <option value="">Todos</option>
                                            <option value="entrada">Entradas</option>
                                            <option value="salida">Salidas</option>
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <input type="date" id="filtroFechaDesde" class="form-control form-control-sm">
                                    </div>

                                    <div class="col-md-3">
                                        <input type="date" id="filtroFechaHasta" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-md-3">
                                        <select id="filtroLimite" class="form-control form-control-sm">
                                            <option value="10">Mostrar 10</option>
                                            <option value="20">Mostrar 20</option>
                                            <option value="50">Mostrar 50</option>
                                            <option value="100">Mostrar 100</option>
                                            <option value="todos">Todos</option>
                                        </select>
                                    </div>

                                </div>

                                <!-- 🔥 CONTENEDOR CON SCROLL -->
                                <div style="max-height:400px; overflow-y:auto; border:1px solid #eee; border-radius:6px;">

                                    <table class="table table-sm table-hover mb-0">

                                        <thead style="position: sticky; top:0; background:white; z-index:1;">
                                            <tr>
                                                <th>Fecha de registro</th>
                                                <th>Tipo</th>
                                                <th>Cantidad</th>
                                                <th>Origen</th>
                                                <th></th>
                                            </tr>
                                        </thead>

                                        <tbody id="kardexBody">

                                            <?php foreach ($movimientos as $m): ?>
                                                <tr data-tipo="<?= $m->tipo ?>" data-fecha="<?= date('Y-m-d', strtotime($m->created_at)) ?>">

                                                    <td><?= date('d/m/Y H:i', strtotime($m->created_at)) ?></td>

                                                    <td>
                                                        <span class="badge <?= $m->tipo == 'entrada' ? 'badge-success' : 'badge-danger' ?>">
                                                            <?= ucfirst($m->tipo) ?>
                                                        </span>
                                                    </td>

                                                    <td><?= $m->cantidad ?></td>

                                                    <td><?= ucfirst($m->origen) ?></td>

                                                    <td class="text-right">

                                                        <?php if ($m->origen == 'compra'): ?>
                                                            <a href="<?= base_url('compras/' . $m->origen_id) ?>"
                                                                class="btn btn-sm btn-outline-secondary">
                                                                <i class="fa fa-eye"></i>
                                                            </a>
                                                        <?php endif; ?>
                                                        <?php if ($m->origen == 'paquete'): ?>
                                                            <a href="<?= base_url('packages/' . $m->origen_id) ?>"
                                                                class="btn btn-sm btn-outline-secondary">
                                                                <i class="fa fa-eye"></i>
                                                            </a>
                                                        <?php endif; ?>
                                                        <?php if ($m->origen == 'venta'): ?>
                                                            <a href="<?= base_url('ventas/' . $m->origen_id) ?>"
                                                                class="btn btn-sm btn-outline-secondary">
                                                                <i class="fa fa-eye"></i>
                                                            </a>
                                                        <?php endif; ?>
                                                        <!-- puedes agregar más tipos aquí -->
                                                    </td>

                                                </tr>
                                            <?php endforeach; ?>

                                        </tbody>

                                    </table>

                                </div>

                            </div>

                        </div>

                    </div>

                    <!-- 🟩 DERECHA (IMAGEN FULL ALTURA) -->
                    <div class="col-md-4">

                        <div class="h-100 d-flex align-items-center justify-content-center border rounded">

                            <?php if (!empty($producto->imagen)): ?>
                                <img
                                    src="<?= base_url('upload/productos/' . $producto->imagen) ?>"
                                    class="img-header-producto"
                                    data-img="<?= base_url('upload/productos/' . $producto->imagen) ?>"
                                    style="
                                width:100%;
                                height:100%;
                                max-height:400px;
                                object-fit:cover;
                                border-radius:10px;
                                cursor:pointer;
                            ">
                            <?php else: ?>
                                <div class="text-muted">Sin imagen</div>
                            <?php endif; ?>

                        </div>

                    </div>

                </div>

            </div>

        </div>
    </div>
</div>
<div class="modal fade" id="productoModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productoModalTitle">Editar Producto</h5>
            </div>
            <form id="productoForm" enctype="multipart/form-data">
                <input type="hidden" name="id" id="producto_id">
                <div class="modal-body">
                    <div class="row"> <!-- Nombre -->
                        <div class="col-md-6"> <label>Nombre</label> <input type="text" name="nombre" class="form-control" required> </div> <!-- Marca -->
                        <div class="col-md-6"> <label>Marca</label> <input type="text" name="marca" class="form-control"> </div> <!-- Presentación -->
                        <div class="col-md-6 mt-2"> <label>Presentación</label> <input type="text" name="presentacion" class="form-control"> </div> <!-- Precio -->
                        <div class="col-md-6 mt-2"> <label>Precio</label> <input type="number" step="0.01" name="precio" class="form-control" required> </div> <!-- Descripción -->
                        <div class="col-md-6 mt-2">
                            <label>Código de barras</label>
                            <div class="input-group">
                                <input type="text" name="codigo_barras" id="codigo_barras" class="form-control">
                                <button type="button" class="btn btn-outline-secondary" onclick="abrirScanner()">
                                    <i class="fa fa-camera"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-12 mt-2"> <label>Descripción</label>
                            <textarea name="descripcion" class="form-control auto-resize"></textarea>
                        </div>
                        <div class="col-md-12 mt-3">
                            <label>Imagen del producto</label>
                            <small class="text-muted d-block">Dejar vacío para mantener la actual</small>
                            <input type="file" name="imagen" class="form-control" accept="image/*">
                            <img id="previewImg" style="max-width:200px; margin-top:10px; display:none;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">
                        Guardar cambios
                    </button>

                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="scannerModal" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Escanear código</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <div id="reader" style="width:100%;"></div>
            </div>
        </div>
    </div>
</div>
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    let html5QrCode;
    let productoModalAbierto = false;

    function abrirScanner() {

        // 👇 verificar si estaba abierto
        if ($('#productoModal').hasClass('show')) {
            productoModalAbierto = true;
            $('#productoModal').modal('hide');
        }

        $('#scannerModal').modal('show');

        $('#scannerModal').off('shown.bs.modal').on('shown.bs.modal', function() {

            html5QrCode = new Html5Qrcode("reader");

            html5QrCode.start({
                    facingMode: "environment"
                }, {
                    fps: 10,
                    qrbox: 250
                },
                (decodedText) => {

                    document.getElementById('codigo_barras').value = decodedText;

                    html5QrCode.stop().then(() => {
                        $('#scannerModal').modal('hide');
                    });

                }
            );
        });
    }

    $('#scannerModal').on('hidden.bs.modal', function() {

        if (html5QrCode) {
            html5QrCode.stop().catch(() => {});
        }

        // 👇 volver a abrir producto si estaba abierto
        if (productoModalAbierto) {
            $('#productoModal').modal('show');
            productoModalAbierto = false;
        }
    });
</script>
<script>
    function editarProducto(producto) {

        document.getElementById('productoModalTitle').innerText = 'Editar Producto';

        document.getElementById('producto_id').value = producto.id;
        document.querySelector('[name="nombre"]').value = producto.nombre;
        document.querySelector('[name="marca"]').value = producto.marca ?? '';
        document.querySelector('[name="presentacion"]').value = producto.presentacion ?? '';
        document.querySelector('[name="precio"]').value = producto.precio;
        document.querySelector('[name="descripcion"]').value = producto.descripcion ?? '';
        document.querySelector('[name="codigo_barras"]').value = producto.codigo_barras ?? '';

        if (producto.imagen) {
            const img = document.getElementById('previewImg');
            img.src = "<?= base_url('upload/productos/') ?>/" + producto.imagen;
            img.style.display = 'block';
        }

        $('#productoModal').modal('show');
    }

    function editarProductoDesdeBtn(btn) {
        const producto = JSON.parse(btn.dataset.producto);
        editarProducto(producto);
    }

    function autoResizeTextarea(el) {
        el.style.height = 'auto';
        el.style.height = el.scrollHeight + 'px';
    }
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tipo = document.getElementById('filtroTipo');
        const desde = document.getElementById('filtroFechaDesde');
        const hasta = document.getElementById('filtroFechaHasta');
        // Inicializar todos
        document.querySelectorAll('.auto-resize').forEach(el => {
            autoResizeTextarea(el);

            el.addEventListener('input', function() {
                autoResizeTextarea(this);
            });
        });

        function filtrar() {

            const limite = document.getElementById('filtroLimite').value;
            let contador = 0;

            document.querySelectorAll('#kardexBody tr').forEach(row => {

                const rowTipo = row.dataset.tipo;
                const rowFecha = row.dataset.fecha;

                let visible = true;

                if (tipo.value && rowTipo !== tipo.value) {
                    visible = false;
                }

                if (desde.value && rowFecha < desde.value) {
                    visible = false;
                }

                if (hasta.value && rowFecha > hasta.value) {
                    visible = false;
                }

                // 👇 aplicar límite
                if (visible) {
                    contador++;

                    if (limite !== 'todos' && contador > parseInt(limite)) {
                        visible = false;
                    }
                }

                row.style.display = visible ? '' : 'none';
            });
        }

        [tipo, desde, hasta].forEach(el => {
            el.addEventListener('change', filtrar);
        });

        document.querySelectorAll('.img-header-producto').forEach(img => {
            img.addEventListener('click', function() {
                document.getElementById('imagenPreviewModal').src = this.dataset.img;
                $('#imagenModal').modal('show');
            });
        });

        document.getElementById('productoForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const form = this;
            const formData = new FormData(form);

            // 🔥 CONFIRMACIÓN
            Swal.fire({
                title: '¿Guardar cambios?',
                text: 'Se actualizará la información del producto',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, guardar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d'
            }).then((result) => {

                if (!result.isConfirmed) return;

                // 🔄 LOADING
                Swal.fire({
                    title: 'Guardando...',
                    text: 'Por favor espera',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // REQUEST
                fetch("<?= base_url('inventario/update') ?>", {
                        method: 'POST',
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {

                        if (data.success) {

                            // ✅ ÉXITO
                            Swal.fire({
                                icon: 'success',
                                title: 'Actualizado',
                                text: 'El producto fue actualizado correctamente',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });

                        } else {

                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.msg || 'No se pudo guardar'
                            });

                        }

                    })
                    .catch(() => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error de conexión'
                        });
                    });

            });
        });

        const limite = document.getElementById('filtroLimite');
        [tipo, desde, hasta, limite].forEach(el => {
            el.addEventListener('change', filtrar);
        });

        $('#productoModal').on('shown.bs.modal', function() {

            const textarea = document.querySelector('[name="descripcion"]');

            if (textarea) {
                autoResizeTextarea(textarea);
            }

        });
    });
</script>

<?= $this->endSection() ?>