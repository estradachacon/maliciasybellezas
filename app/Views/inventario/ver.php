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

    .modal-backdrop {
        z-index: 1040 !important;
    }

    #scannerModal {
        z-index: 1055 !important;
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

    /* ── PRECIOS POR VOLUMEN ── */
    .precio-row {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 10px 14px;
        margin-bottom: 8px;
        transition: border-color 0.2s, box-shadow 0.2s;
        position: relative;
    }

    @media (max-width: 767px) {

        .precio-row {
            flex-wrap: wrap;
        }

        .precio-row .pv-field {
            flex: 1;
            min-width: 0;
        }

        /* 🔥 LA CLAVE */
        .precio-row .pv-arrow {
            flex: 0 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 16px;
        }

        /* 🔥 fila inferior */
        .precio-row .pv-badge {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 6px;
        }

        .precio-row .pv-actions {
            position: static;
            margin: 0;
        }
    }

    .precio-row:hover {
        border-color: #adb5bd;
    }

    .precio-row.is-new {
        border-color: #0d6efd;
        border-style: dashed;
    }

    .precio-row.is-saving {
        opacity: 0.5;
        pointer-events: none;
    }

    .precio-row.is-error {
        border-color: #dc3545 !important;
        background: #fff8f8;
    }

    .precio-row.is-saved {
        border-color: #28a745;
    }

    .precio-row .pv-field {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }
.precios-panel {
    overflow-x: hidden;
}
    .precio-row .pv-label {
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #9ca3af;
        font-weight: 600;
    }

    .precio-row .pv-input {
        width: 90px;
        text-align: center;
        font-weight: 600;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        padding: 4px 8px;
        font-size: 14px;
        transition: border-color 0.15s;
    }

    .precio-row .pv-input:focus {
        outline: none;
        border-color: #0d6efd;
        box-shadow: 0 0 0 2px rgba(13, 110, 253, .15);
    }

    .precio-row .pv-arrow {
        color: #9ca3af;
        font-size: 14px;
        margin-top: 14px;
    }

    .precio-row .pv-badge {
        flex: 1;
        font-size: 12px;
        color: #6c757d;
        margin-top: 14px;
    }

    .precio-row .pv-badge .badge {
        font-size: 11px;
        font-weight: 600;
        padding: 3px 8px;
    }

    .precio-row .pv-actions {
        display: flex;
        gap: 6px;
        margin-top: 14px;
        margin-left: auto;
    }

    .pv-btn {
        width: 30px;
        height: 30px;
        border-radius: 6px;
        border: 1px solid;
        background: transparent;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 13px;
        transition: all 0.15s;
    }

    .pv-btn-save {
        border-color: #28a745;
        color: #28a745;
    }

    .pv-btn-save:hover {
        background: #28a745;
        color: #fff;
    }

    .pv-btn-del {
        border-color: #dc3545;
        color: #dc3545;
    }

    .pv-btn-del:hover {
        background: #dc3545;
        color: #fff;
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
                            <div class="mb-2">

                                <!-- 🔝 FILA PRINCIPAL -->
                                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start">

                                    <!-- 🧾 INFO PRODUCTO -->
                                    <div>
                                        <h5 class="mb-0"><?= esc($producto->nombre) ?></h5>
                                        <small class="text-muted">Producto #<?= $producto->id ?></small>
                                    </div>

                                    <!-- 💰 PRECIO -->
                                    <div class="mt-2 mt-md-0 text-md-end">
                                        <div class="text-muted" style="font-size:13px;">
                                            Precio
                                        </div>
                                        <div class="fw-bold text-success" style="font-size:20px;">
                                            $<?= number_format($producto->precio, 2) ?>
                                        </div>

                                    </div>

                                </div>

                                <!-- 🔘 BOTÓN -->
                                <?php if (tienePermiso('editar_producto')): ?>
                                    <div class="mt-2">
                                        <button
                                            class="btn btn-sm btn-primary w-100 w-md-auto"
                                            data-producto='<?= json_encode($producto) ?>'
                                            onclick="editarProductoDesdeBtn(this)">
                                            <i class="fa fa-edit"></i> Editar
                                        </button>
                                    </div>
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

                            <?php if (tienePermiso('ver_promociones_producto')): ?>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#precios">
                                        <i class="fa-solid fa-tags me-1"></i> Precios por volumen
                                    </a>
                                </li>
                            <?php endif; ?>

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
                            <!-- TAB PANE (reemplaza el div id="promociones") -->
                            <?php if (tienePermiso('ver_promociones_producto')): ?>
                                <div class="tab-pane fade" id="precios">

                                    <div class="precios-panel">

                                        <!-- Header -->
                                        <div class="precios-header d-flex justify-content-between mb-3">
                                            <div>
                                                <span class="text-muted" style="font-size:13px;">
                                                    Precio base: <strong class="text-dark">$<?= number_format($producto->precio, 2) ?></strong>
                                                </span>
                                            </div>
                                            <?php if (tienePermiso('crear_promocion_producto')): ?>
                                                <button class="btn btn-sm btn-primary px-3" onclick="PreciosVolumen.agregar()">
                                                    <i class="fa fa-plus mr-1"></i> Nueva regla
                                                </button>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Tabla de reglas -->
                                        <div id="preciosContainer">
                                            <?php if (!empty($promociones)): ?>
                                                <?php foreach ($promociones as $p): ?>
                                                    <div class="precio-row" data-id="<?= $p->id ?>">
                                                        <?= /* se renderiza con JS */ '' ?>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Estado vacío -->
                                        <div id="preciosVacio" class="text-center py-4" style="display:none;">
                                            <i class="fa-solid fa-tags text-muted" style="font-size:32px; opacity:0.3;"></i>
                                            <p class="text-muted mt-2 mb-0">Sin reglas de precios por volumen</p>
                                            <small class="text-muted">Crea una regla para ofrecer descuentos por cantidad</small>
                                        </div>

                                    </div>

                                </div>
                            <?php endif; ?>
                        </div>

                    </div>

                    <!-- 🟩 DERECHA (IMAGEN FULL ALTURA) -->
                    <div class="col-md-4 mt-4">

                        <!-- 🖼️ IMAGEN -->
                        <div class="mb-3 text-center border rounded p-2">

                            <?php if (!empty($producto->imagen)): ?>
                                <img
                                    src="<?= base_url('upload/productos/' . $producto->imagen) ?>"
                                    class="img-header-producto"
                                    style="width:100%; max-height:200px; object-fit:cover; border-radius:10px;">
                            <?php else: ?>
                                <div class="text-muted">Sin imagen</div>
                            <?php endif; ?>

                        </div>

                        <!-- 🏷️ ETIQUETA -->
                        <div class="border rounded p-3 text-center bg-white" id="etiquetaProducto">

                            <!-- Nombre -->
                            <div style="font-size:14px; font-weight:600; line-height:1.2;">
                                <?= esc($producto->nombre) ?>
                            </div>

                            <!-- Código -->
                            <?php if (!empty($producto->codigo_barras)): ?>
                                <svg id="barcode" style="margin-top:5px;"></svg>
                                <div style="font-size:12px;">
                                    <?= esc($producto->codigo_barras) ?>
                                </div>
                            <?php else: ?>
                                <div class="text-muted mt-2" style="font-size:12px;">
                                    Sin código
                                </div>
                            <?php endif; ?>

                        </div>

                        <!-- 🖨️ BOTÓN -->
                        <button class="btn btn-dark btn-sm w-100 mt-2" onclick="imprimirEtiqueta()">
                            <i class="fa fa-print"></i> Imprimir etiqueta
                        </button>

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

                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-secondary" onclick="toggleScanner()">
                                        <i class="fa fa-camera"></i>
                                    </button>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-outline-success" onclick="generarCodigo()">
                                            <i class="fa fa-barcode"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- ✅ feedback -->
                                <div class="input-group-append">
                                    <span id="scanCheck" class="input-group-text text-success" style="display:none;">
                                        ✔
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- ✅ SCANNER (FUERA del input-group) -->
                        <div class="col-md-12 mt-2" id="scannerContainer" style="display:none;">
                            <div class="border rounded p-2 text-center bg-light">

                                <div id="reader" style="width:100%; max-width:400px; margin:auto;"></div>

                                <button type="button" class="btn btn-sm btn-danger mt-2" onclick="cerrarScanner()">
                                    Cancelar
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

<script src="https://unpkg.com/html5-qrcode"></script>
<script src="https://cdn.jsdelivr.net/npm/jsbarcode/dist/JsBarcode.all.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        const codigo = "<?= $producto->codigo_barras ?>";

        if (codigo) {
            JsBarcode("#barcode", codigo, {
                format: "CODE128",
                width: 2,
                height: 50,
                displayValue: false
            });
        }

    });
</script>
<script>
    // ── Datos iniciales desde PHP ──────────────────────────────────────────
    const PV_CONFIG = {
        productoId: <?= $producto->id ?>,
        precioBase: <?= (float)$producto->precio ?>,
        urlSave: "<?= base_url('producto-promociones/save') ?>",
        urlDelete: "<?= base_url('producto-promociones/delete') ?>",
        canEdit: <?= (tienePermiso('editar_promocion_producto') || tienePermiso('crear_promocion_producto')) ? 'true' : 'false' ?>,
        inicial: <?= json_encode(array_map(fn($p) => [
                        'id'              => $p->id,
                        'cantidad_minima' => (int)$p->cantidad_minima,
                        'precio'          => (float)$p->precio,
                    ], $promociones ?? [])) ?>
    };

    // ── Módulo PreciosVolumen ──────────────────────────────────────────────
    const PreciosVolumen = (() => {

        const container = () => document.getElementById('preciosContainer');
        const vacio = () => document.getElementById('preciosVacio');

        // ── Render de una fila ────────────────────────────────────────────
        function renderRow({
            id = '',
            cantidad_minima = '',
            precio = ''
        } = {}, isNew = false) {

            const row = document.createElement('div');
            row.className = 'precio-row' + (isNew ? ' is-new' : '');
            if (id) row.dataset.id = id;

            const descPct = precio && PV_CONFIG.precioBase ?
                Math.round((1 - precio / PV_CONFIG.precioBase) * 100) :
                0;

            const badgeHtml = precio && cantidad_minima ?
                `<span class="badge badge-success">-${descPct}%</span>
               <span class="ml-1">desde ${cantidad_minima} u.</span>` :
                `<span class="text-muted">Nueva regla</span>`;

            const actionsHtml = PV_CONFIG.canEdit ? `
            <div class="pv-actions">
                <button class="pv-btn pv-btn-save" title="Guardar" onclick="PreciosVolumen.guardar(this)">
                    <i class="fa fa-check"></i>
                </button>
                <button class="pv-btn pv-btn-del" title="Eliminar" onclick="PreciosVolumen.eliminar(this)">
                    <i class="fa fa-times"></i>
                </button>
            </div>` : '';

            row.innerHTML = `
            <div class="pv-field">
                <span class="pv-label">Cantidad mín.</span>
                <input class="pv-input pv-qty" type="number" min="1" step="1"
                    value="${cantidad_minima}" placeholder="Ej: 6"
                    inputmode="numeric"
                    onkeypress="return event.key!='.'&&event.key!=','">
            </div>
            <div class="pv-arrow">→</div>
            <div class="pv-field">
                <span class="pv-label">Precio unitario</span>
                <input class="pv-input pv-price" type="number" step="0.01" min="0.01"
                    value="${precio}" placeholder="$0.00">
            </div>
            <div class="pv-badge">${badgeHtml}</div>
            ${actionsHtml}
        `;

            // live preview del badge
            row.querySelector('.pv-qty')?.addEventListener('input', () => actualizarBadge(row));
            row.querySelector('.pv-price')?.addEventListener('input', () => actualizarBadge(row));

            return row;
        }

        function actualizarBadge(row) {
            const qty = parseInt(row.querySelector('.pv-qty').value);
            const price = parseFloat(row.querySelector('.pv-price').value);
            const badge = row.querySelector('.pv-badge');
            if (qty > 0 && price > 0) {
                const pct = Math.round((1 - price / PV_CONFIG.precioBase) * 100);
                badge.innerHTML = pct > 0 ?
                    `<span class="badge badge-success">-${pct}%</span> <span class="ml-1">desde ${qty} u.</span>` :
                    `<span class="badge badge-secondary">desde ${qty} u.</span>`;
            } else {
                badge.innerHTML = `<span class="text-muted">Nueva regla</span>`;
            }
        }

        // ── Validar fila ──────────────────────────────────────────────────
        function validar(row) {
            const qty = parseInt(row.querySelector('.pv-qty').value);
            const price = parseFloat(row.querySelector('.pv-price').value);

            if (!Number.isInteger(qty) || qty < 1)
                return 'La cantidad mínima debe ser un número entero positivo';
            if (isNaN(price) || price <= 0)
                return 'El precio debe ser mayor a 0';
            if (price >= PV_CONFIG.precioBase)
                return `El precio ($${price.toFixed(2)}) debe ser menor al precio base ($${PV_CONFIG.precioBase.toFixed(2)})`;

            // duplicados
            const propioId = row.dataset.id || null;
            const duplicado = [...document.querySelectorAll('.precio-row')].some(r => {
                if (r === row) return false;
                return parseInt(r.querySelector('.pv-qty').value) === qty;
            });
            if (duplicado) return `Ya existe una regla para ${qty} unidades`;

            // orden lógico: a más cantidad → menor precio
            const todasFilas = [...document.querySelectorAll('.precio-row')]
                .map(r => ({
                    qty: parseInt(r.querySelector('.pv-qty').value),
                    price: parseFloat(r.querySelector('.pv-price').value),
                    el: r
                }))
                .filter(v => Number.isInteger(v.qty) && !isNaN(v.price) && v.el !== row);

            todasFilas.push({
                qty,
                price
            });
            todasFilas.sort((a, b) => a.qty - b.qty);

            for (let i = 1; i < todasFilas.length; i++) {
                if (todasFilas[i].price >= todasFilas[i - 1].price) {
                    return 'Al aumentar la cantidad, el precio debe disminuir';
                }
            }

            return null; // sin error
        }

        // ── Ordenar filas en el DOM ───────────────────────────────────────
        function ordenar() {
            const c = container();
            [...c.querySelectorAll('.precio-row')]
            .sort((a, b) => {
                    const av = parseInt(a.querySelector('.pv-qty').value) || 0;
                    const bv = parseInt(b.querySelector('.pv-qty').value) || 0;
                    return av - bv;
                })
                .forEach(r => c.appendChild(r));
        }

        function checkVacio() {
            const filas = container().querySelectorAll('.precio-row').length;
            vacio().style.display = filas === 0 ? 'block' : 'none';

            // 🔒 Ocultar botón si hay 5 reglas
            const btnAgregar = document.querySelector('[onclick="PreciosVolumen.agregar()"]');
            if (btnAgregar) btnAgregar.style.display = filas >= 5 ? 'none' : '';
        }

        // ── API ───────────────────────────────────────────────────────────
        function agregar() {
            // 🔒 Máximo 5 reglas
            if (container().querySelectorAll('.precio-row').length >= 5) {
                Swal.fire({
                    icon: 'info',
                    title: 'Límite alcanzado',
                    text: 'Solo se permiten hasta 5 reglas de precio por volumen',
                    timer: 2000,
                    showConfirmButton: false
                });
                return;
            }

            const row = renderRow({}, true);
            container().appendChild(row);
            row.querySelector('.pv-qty').focus();
            checkVacio();
        }

        function guardar(btn) {
            const row = btn.closest('.precio-row');
            const error = validar(row);

            row.classList.remove('is-error', 'is-saved');

            if (error) {
                row.classList.add('is-error');
                Swal.fire({
                    icon: 'warning',
                    title: 'Revisa la regla',
                    text: error,
                    timer: 2500,
                    showConfirmButton: false
                });
                return;
            }

            const id = row.dataset.id || null;
            const qty = parseInt(row.querySelector('.pv-qty').value);
            const price = parseFloat(row.querySelector('.pv-price').value);

            row.classList.add('is-saving');

            fetch(PV_CONFIG.urlSave, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id,
                        producto_id: PV_CONFIG.productoId,
                        cantidad_minima: qty,
                        precio: price
                    })
                })
                .then(r => r.json())
                .then(data => {
                    row.classList.remove('is-saving', 'is-new');
                    if (data.success) {
                        row.dataset.id = data.id;
                        row.classList.add('is-saved');
                        setTimeout(() => row.classList.remove('is-saved'), 1200);
                        ordenar();
                    } else {
                        row.classList.add('is-error');
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.msg || 'No se pudo guardar'
                        });
                    }
                })
                .catch(() => {
                    row.classList.remove('is-saving');
                    row.classList.add('is-error');
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de conexión',
                        text: 'Inténtalo nuevamente'
                    });
                });
        }

        function eliminar(btn) {
            const row = btn.closest('.precio-row');
            const id = row.dataset.id;

            if (!id) {
                row.remove();
                checkVacio();
                return;
            }

            Swal.fire({
                title: '¿Eliminar regla?',
                text: 'Esta acción no se puede deshacer',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d'
            }).then(r => {
                if (!r.isConfirmed) return;
                row.classList.add('is-saving');
                fetch(PV_CONFIG.urlDelete, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            id
                        })
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            row.remove();
                            checkVacio();
                        } else Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'No se pudo eliminar'
                        });
                    })
                    .catch(() => {
                        row.classList.remove('is-saving');
                        Swal.fire({
                            icon: 'error',
                            title: 'Error de conexión'
                        });
                    });
            });
        }

        // ── Init ──────────────────────────────────────────────────────────
        function init() {
            const c = container();
            // limpiar los divs vacíos que renderizó PHP
            c.innerHTML = '';
            PV_CONFIG.inicial.forEach(item => c.appendChild(renderRow(item)));
            ordenar();
            checkVacio();
        }

        return {
            agregar,
            guardar,
            eliminar,
            init
        };
    })();

    document.addEventListener('DOMContentLoaded', () => PreciosVolumen.init());
</script>
<script>
    function marcarError(row, msg) {
        row.classList.add('error');

        Swal.fire({
            icon: 'warning',
            title: msg,
            timer: 1500,
            showConfirmButton: false
        });
    }

    function limpiarError(row) {
        row.classList.remove('error');
    }

    function imprimirEtiqueta() {

        const nombre = `<?= esc($producto->nombre) ?>`;
        const codigo = `<?= $producto->codigo_barras ?>`;

        const ventana = window.open('', '', 'width=400,height=600');

        ventana.document.write(`
        <html>
        <head>
            <title>Etiqueta</title>

            <script src="https://cdn.jsdelivr.net/npm/jsbarcode/dist/JsBarcode.all.min.js"><\/script>

            <style>
                body {
                    margin: 0;
                    padding: 0;
                    font-family: Arial;
                }

                #contenedor {
                    width: 2in;
                    height: 1in;
                    padding: 4px;
                    box-sizing: border-box;

                    display: flex;
                    flex-direction: column;
                    justify-content: space-between;
                    align-items: center;
                }

                .nombre {
                    font-size: 8px;
                    text-align: center;
                    line-height: 1;
                }

                svg {
                    width: 100%;
                    height: 55px; /* 🔥 más grande */
                }

                .codigo {
                    font-size: 10px;
                    font-weight: bold;
                    letter-spacing: 1px;
                }

            </style>
        </head>

        <body>

            <div id="contenedor">

                <div class="nombre">${nombre}</div>

                <svg id="barcodePrint"></svg>

                <div class="codigo">${codigo}</div>

            </div>

            <script>
                JsBarcode("#barcodePrint", "${codigo}", {
                    format: "CODE128",
                    width: 2,
                    height: 55,
                    displayValue: false
                });
            <\/script>

        </body>
        </html>
    `);

        ventana.document.close();
        ventana.print();
    }
</script>
<script>
    function generarCodigo() {

        const input = document.getElementById('codigo_barras');
        const id = document.getElementById('producto_id').value;

        Swal.fire({
            title: 'Generar código',
            text: 'Este código será el identificador del producto. ¿Deseas generarlo automáticamente?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, generar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#28a745'
        }).then((result) => {

            if (!result.isConfirmed) return;

            if (!id) {
                Swal.fire('Error', 'El producto debe estar guardado primero', 'warning');
                return;
            }

            // 👉 generar código tipo P000123
            const codigo = 'P' + id.toString().padStart(6, '0');

            input.value = codigo;

            Swal.fire({
                icon: 'success',
                title: 'Código generado',
                text: codigo,
                timer: 1500,
                showConfirmButton: false
            });

        });
    }
</script>
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

                // 👉 llenar input
                document.getElementById('codigo_barras').value = decodedText;

                mostrarCheck();

                cerrarScanner();
            }
        ).catch(err => {
            console.error("Error cámara:", err);
        });

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

    // ✅ feedback visual
    function mostrarCheck() {

        const check = document.getElementById('scanCheck');

        check.style.display = 'inline';

        setTimeout(() => {
            check.style.display = 'none';
        }, 2000);
    }
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
        $(document).on('submit', '#productoForm', function(e) {
            e.preventDefault();

            const form = this;
            const formData = new FormData(form);

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

                // 🔥 REQUEST
                fetch("<?= base_url('inventario/update') ?>", {
                        method: 'POST',
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {

                        if (data.success) {

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