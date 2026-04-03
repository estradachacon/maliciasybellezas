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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tipo = document.getElementById('filtroTipo');
        const desde = document.getElementById('filtroFechaDesde');
        const hasta = document.getElementById('filtroFechaHasta');

        function filtrar() {

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

    });
</script>

<?= $this->endSection() ?>