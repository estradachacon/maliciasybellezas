<?= $this->extend('Layouts/mainbody') ?>
<?= $this->section('content') ?>

<style>
    .producto-item {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 10px 14px;
        margin-bottom: 8px;
        background: #fff;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .producto-select2 .select2-container {
        width: 100% !important;
    }

    .producto-item .prod-nombre {
        flex: 1;
        font-weight: 600;
        font-size: 14px;
    }

    .producto-item .prod-stock {
        font-size: 12px;
        color: #6c757d;
    }

    .producto-item .prod-qty {
        width: 80px;
        text-align: center;
    }
</style>

<div class="row">
    <div class="col-md-12">
        <div class="card">

            <div class="card-header d-flex">
                <h4 class="header-title mb-0">Nuevo Traslado</h4>
            </div>

            <div class="card-body">
                <div class="row">

                    <!-- IZQUIERDA: FORMULARIO -->
                    <div class="col-md-7">

                        <!-- SUCURSALES -->
                        <div class="p-3 border rounded mb-3 bg-light">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="font-weight-bold">Sucursal origen</label>
                                    <select id="origenBranch" class="form-control" onchange="onOrigenChange()">
                                        <option value="">Seleccionar</option>
                                        <?php foreach ($branches as $b): ?>
                                            <option value="<?= $b->id ?>">
                                                <?= esc($b->branch_name) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="font-weight-bold">Sucursal destino</label>
                                    <select id="destinoBranch" class="form-control">
                                        <option value="">Seleccionar</option>
                                        <?php foreach ($branches as $b): ?>
                                            <option value="<?= $b->id ?>">
                                                <?= esc($b->branch_name) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- GASTO / NOTAS -->
                        <div class="p-3 border rounded mb-3 bg-light">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="font-weight-bold">
                                        Costo de traslado
                                        <small class="text-muted font-weight-normal">(opcional)</small>
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">$</span>
                                        </div>
                                        <input type="number" id="costoTraslado"
                                            class="form-control" step="0.01" min="0"
                                            value="0" onchange="onCostoChange()">
                                    </div>
                                </div>
                                <div class="col-md-6" id="cuentaContainer" style="display:none;">
                                    <label class="font-weight-bold">Cuenta del gasto</label>
                                    <select id="cuentaId" class="form-control">
                                        <option value="">Seleccionar cuenta</option>
                                        <?php foreach ($cuentas as $c): ?>
                                            <option value="<?= $c->id ?>">
                                                <?= esc($c->name) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-12 mt-2">
                                    <label class="font-weight-bold">
                                        Notas
                                        <small class="text-muted font-weight-normal">(opcional)</small>
                                    </label>
                                    <textarea id="notas" class="form-control" rows="2"
                                        placeholder="Observaciones del traslado..."></textarea>
                                </div>
                            </div>
                        </div>
                        <!-- BUSCADOR PRODUCTOS -->
                        <div class="p-3 border rounded mb-3 bg-light">
                            <label class="font-weight-bold">Agregar productos</label>

                            <select id="producto_id" class="form-control" disabled></select>
                            <small class="text-muted">Selecciona primero la sucursal origen</small>

                            <button class="btn btn-primary btn-sm mt-2 w-100"
                                id="btnAgregarProducto"
                                onclick="agregarDesdeSelect()"
                                disabled>
                                <i class="fa fa-plus mr-1"></i> Agregar
                            </button>
                        </div>

                    </div>

                    <!-- DERECHA: RESUMEN -->
                    <div class="col-md-5">

                        <div class="p-3 border rounded bg-light" style="position:sticky; top:20px;">

                            <div class="d-flex justify-content-between mb-2">
                                <h6 class="mb-0 font-weight-bold">Productos a trasladar</h6>
                                <span id="totalProductosBadge" class="badge badge-primary">0</span>
                            </div>

                            <div id="productosContainer"></div>

                            <hr>

                            <!-- RESUMEN COSTO -->
                            <div id="resumenCosto" style="display:none;" class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Costo traslado</span>
                                    <span class="text-danger font-weight-bold" id="resumenCostoValor">$0.00</span>
                                </div>
                            </div>

                            <button class="btn btn-success btn-sm w-100"
                                onclick="guardarTraslado()"
                                id="btnGuardar" disabled>
                                <i class="fa fa-check mr-1"></i> Confirmar traslado
                            </button>

                        </div>

                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<script>
    const BASE_URL = '<?= base_url() ?>';
    let productos = [];

    // ── Costo cambió ─────────────────────────────────────────────────
    function onCostoChange() {
        const costo = parseFloat(document.getElementById('costoTraslado').value) || 0;
        const container = document.getElementById('cuentaContainer');
        const resumen = document.getElementById('resumenCosto');
        const valor = document.getElementById('resumenCostoValor');

        container.style.display = costo > 0 ? 'block' : 'none';
        resumen.style.display = costo > 0 ? 'block' : 'none';
        valor.textContent = '$' + costo.toFixed(2);
    }

    // ── Origen cambió ─────────────────────────────────────────────────
    function onOrigenChange() {
        const branchId = document.getElementById('origenBranch').value;
        const selectEl = document.getElementById('producto_id');
        const btnEl = document.getElementById('btnAgregarProducto');

        if (branchId) {
            selectEl.disabled = false;
            if ($.fn.select2 && $(selectEl).data('select2')) {
                $('#producto_id').val(null).trigger('change');
            } else {
                initSelect2();
            }
        } else {
            selectEl.disabled = true;
            btnEl.disabled = true;
        }

        productos = [];
        renderProductos();
    }

    // ── Init Select2 ─────────────────────────────────────────────────
    function initSelect2() {
        $('#producto_id').select2({
            language: 'es',
            placeholder: 'Buscar producto...',
            width: '100%',
            minimumInputLength: 1,
            escapeMarkup: markup => markup,

            templateResult: function(p) {
                if (p.loading) return 'Buscando...';
                return `
                <div class="d-flex" style="gap:8px;">
                    ${p.imagen
                        ? `<img src="${BASE_URL}upload/productos/${p.imagen}"
                               style="width:32px;height:32px;object-fit:cover;border-radius:4px;">`
                        : `<div style="width:32px;height:32px;background:#f1f1f1;border-radius:4px;
                               display:flex;align-items:center;justify-content:center;">
                               <i class="fa fa-box text-muted" style="font-size:12px;"></i>
                           </div>`
                    }
                    <div>
                        <div style="font-size:13px;font-weight:600;">${p.text}</div>
                        <small class="text-muted">Stock: ${p.stock ?? 0}</small>
                    </div>
                </div>`;
            },

            templateSelection: p => p.text || p.id,

            ajax: {
                url: `${BASE_URL}productos/searchAjaxSelectStockBranch`,
                dataType: 'json',
                delay: 250,
                data: params => ({
                    term: params.term,
                    branch_id: document.getElementById('origenBranch').value
                }),

                processResults: function(data) {
                    return {
                        results: data.map(p => ({
                            id: p.id,
                            text: p.text,
                            stock: p.stock,
                            imagen: p.imagen
                        }))
                    };
                },

                cache: true
            },

            escapeMarkup: function(m) {
                return m;
            }
        });

        $('#producto_id').on('select2:select', function(e) {
            let data = e.params.data;

            let option = $(this).find(`option[value="${data.id}"]`);

            option.data('stock', data.stock);
            option.data('imagen', data.imagen);

            document.getElementById('btnAgregarProducto').disabled = false;
        });
    }

    // ── Agregar desde select2 ─────────────────────────────────────────
    function agregarDesdeSelect() {
        const select = $('#producto_id');
        const selected = select.select2('data')[0];

        if (!selected || !selected.id) return;

        // 🔥 fallback sólido
        const optionEl = select.find(`option[value="${selected.id}"]`);

        const id = parseInt(selected.id);

        const nombre =
            selected.text ||
            optionEl.text() ||
            'Producto';

        const stock =
            selected.stock ??
            optionEl.data('stock') ??
            0;

        if (stock <= 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Sin stock',
                text: 'Este producto no tiene stock disponible en esta sucursal',
                timer: 1800,
                showConfirmButton: false
            });
            return;
        }

        const existente = productos.find(p => p.producto_id === id);
        if (existente) {
            if (existente.cantidad < existente.stock) {
                existente.cantidad++;
                renderProductos();
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Stock insuficiente',
                    text: `Solo hay ${stock} unidades disponibles`,
                    timer: 1800,
                    showConfirmButton: false
                });
            }
            return;
        }

        productos.push({
            producto_id: id,
            nombre,
            cantidad: 1,
            stock
        });
        renderProductos();

        select.val(null).trigger('change');
        document.getElementById('btnAgregarProducto').disabled = true;
    }

    // ── Render lista ──────────────────────────────────────────────────
    function renderProductos() {
        const container = document.getElementById('productosContainer');
        const badge = document.getElementById('totalProductosBadge');
        const btnGuardar = document.getElementById('btnGuardar');

        badge.textContent = productos.length;
        btnGuardar.disabled = productos.length === 0;

        if (!productos.length) {
            container.innerHTML = `
            <p class="text-muted text-center py-3">
                Sin productos agregados
            </p>
        `;
            return;
        }

        container.innerHTML = productos.map((p, i) => `
            <div class="producto-item">
                <div style="flex:1;">
                    <div class="prod-nombre">${escapeHtml(p.nombre)}</div>
                    <div class="prod-stock">Stock disponible: ${p.stock}</div>
                </div>
                <input type="number"
                    class="form-control prod-qty"
                    value="${p.cantidad}"
                    min="1"
                    max="${p.stock}"
                    onchange="cambiarCantidad(${i}, this.value)">
                <button class="btn btn-sm btn-outline-danger"
                    onclick="eliminarProducto(${i})">
                    <i class="fa fa-times"></i>
                </button>
            </div>
        `).join('');
    }

    function cambiarCantidad(idx, val) {
        const qty = parseInt(val);
        if (qty < 1 || qty > productos[idx].stock) {
            Swal.fire({
                icon: 'warning',
                title: 'Cantidad inválida',
                text: `Debe ser entre 1 y ${productos[idx].stock}`,
                timer: 1800,
                showConfirmButton: false
            });
            renderProductos();
            return;
        }
        productos[idx].cantidad = qty;
    }

    function eliminarProducto(idx) {
        productos.splice(idx, 1);
        renderProductos();
    }

    // ── Guardar ───────────────────────────────────────────────────────
    function guardarTraslado() {
        const origenId = document.getElementById('origenBranch').value;
        const destinoId = document.getElementById('destinoBranch').value;
        const costo = parseFloat(document.getElementById('costoTraslado').value) || 0;
        const cuentaId = document.getElementById('cuentaId').value;
        const notas = document.getElementById('notas').value.trim();

        if (!origenId) return Swal.fire({
            icon: 'warning',
            title: 'Selecciona la sucursal origen',
            timer: 1800,
            showConfirmButton: false
        });
        if (!destinoId) return Swal.fire({
            icon: 'warning',
            title: 'Selecciona la sucursal destino',
            timer: 1800,
            showConfirmButton: false
        });
        if (origenId === destinoId) return Swal.fire({
            icon: 'warning',
            title: 'Origen y destino no pueden ser iguales',
            timer: 1800,
            showConfirmButton: false
        });
        if (!productos.length) return Swal.fire({
            icon: 'warning',
            title: 'Agrega al menos un producto',
            timer: 1800,
            showConfirmButton: false
        });
        if (costo > 0 && !cuentaId) return Swal.fire({
            icon: 'warning',
            title: 'Selecciona una cuenta para el gasto',
            timer: 1800,
            showConfirmButton: false
        });

        Swal.fire({
            title: '¿Confirmar traslado?',
            html: `<div style="text-align:left;font-size:14px;">
            <strong>${productos.length} producto(s)</strong> serán trasladados.<br>
            ${costo > 0
                ? `Costo: <span class="text-danger">$${costo.toFixed(2)}</span>`
                : 'Sin costo de traslado.'}
        </div>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, trasladar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
        }).then(result => {
            if (!result.isConfirmed) return;

            Swal.fire({
                title: 'Procesando...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            fetch(`${BASE_URL}traslados/guardar`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        origen_branch_id: parseInt(origenId),
                        destino_branch_id: parseInt(destinoId),
                        costo_traslado: costo,
                        cuenta_id: cuentaId || null,
                        notas,
                        productos: productos.map(p => ({
                            producto_id: p.producto_id,
                            cantidad: p.cantidad,
                        }))
                    })
                })
                .then(r => r.json())
                .then(data => {
                    if (data.status === 'ok') {
                        Swal.fire({
                                icon: 'success',
                                title: 'Traslado registrado',
                                timer: 1500,
                                showConfirmButton: false
                            })
                            .then(() => window.location.href = `${BASE_URL}traslados/${data.id}`);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            html: '<pre style="text-align:left;font-size:12px;">' + JSON.stringify(data, null, 2) + '</pre>'
                        });
                    }
                })
                .catch(err => Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: err.message
                }));
        });
    }

    function escapeHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }
</script>
<?= $this->endSection() ?>