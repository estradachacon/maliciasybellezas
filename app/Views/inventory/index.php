<?= $this->extend('Layouts/mainbody') ?>
<?= $this->section('content') ?>

<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">📦 Productos / Inventario</h5>
    </div>

    <div class="card-body">
        <?php if (tienePermiso('crear_producto')): ?>
            <div class="mb-3 text-end">
                <button class="btn btn-success" id="btnNuevoProducto">
                    + Nuevo producto
                </button>
            </div>
        <?php endif; ?>
        <div class="row mb-3">
            <div class="col-md-4">
                <input
                    type="text"
                    id="buscador"
                    class="form-control"
                    placeholder="Buscar producto o ID..."
                    value="<?= esc($q) ?>">
            </div>
        </div>
        <table class="table table-bordered table-sm">
            <thead>
                <tr>
                    <th width="80">ID</th>
                    <th>Producto</th>

                    <?php foreach ($branches as $b): ?>
                        <th><?= esc($b->branch_name) ?></th>
                    <?php endforeach; ?>

                    <th width="120">Acción</th>
                </tr>
            </thead>
            <tbody>

                <?php foreach ($productos as $p): ?>
                    <tr>
                        <td class="text-center">
                            <?= $p->id ?>
                        </td>

                        <td><?= esc($p->nombre) ?></td>

                        <?php foreach ($branches as $b):
                            $stock = $inventario[$p->id][$b->id] ?? 0;
                        ?>
                            <td class="text-center">
                                <span class="badge bg-dark">
                                    <?= $stock ?>
                                </span>
                            </td>
                        <?php endforeach; ?>

                        <td class="text-center">
                            <button
                                class="btn btn-primary btn-sm btnEditarProducto"
                                data-id="<?= $p->id ?>">
                                Editar
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>

            </tbody>
        </table>
        <div class="mt-3">
            <?= $pager->links() ?>
        </div>
    </div>

</div>
<div class="modal fade" id="modalProducto">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 id="tituloModalProducto">Nuevo Producto</h5>
            </div>

            <div class="modal-body">

                <input type="hidden" id="producto_id">

                <div class="mb-2">
                    <label>Nombre</label>
                    <input type="text" id="producto_nombre" class="form-control">
                </div>

                <div class="mb-2">
                    <label>Proveedor</label>
                    <input type="text" id="producto_proveedor" class="form-control">
                </div>

                <div class="mb-2">
                    <label>Costo</label>
                    <input type="number" step="0.01" id="producto_costo" class="form-control">
                </div>

                <div class="mb-2">
                    <label>Precio</label>
                    <input type="number" step="0.01" id="producto_precio" class="form-control">
                </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button class="btn btn-success" id="guardarProducto">
                    Guardar
                </button>
            </div>

        </div>
    </div>
</div>
<script>
    $(document).on('click', '.btnEditarProducto', function() {

        let id = $(this).data('id');

        $('#tituloModalProducto').text('Editar Producto');
        $('#producto_id').val(id);

        // 🔥 traer datos
        $.get('<?= base_url('productos/get') ?>/' + id, function(res) {

            $('#producto_nombre').val(res.nombre);
            $('#producto_proveedor').val(res.proveedor);
            $('#producto_costo').val(res.costo_inicial);
            $('#producto_precio').val(res.precio_venta);

            $('#modalProducto').modal('show');

        }, 'json');

    });
    $('#btnNuevoProducto').click(function() {

        $('#producto_id').val(''); // 🔥 IMPORTANTE

        $('#tituloModalProducto').text('Nuevo Producto');

        $('#producto_nombre').val('');
        $('#producto_proveedor').val('');
        $('#producto_costo').val('');
        $('#producto_precio').val('');

        $('#modalProducto').modal('show');
    });
    $('#buscador').on('keyup', function(e) {

        if (e.keyCode === 13) { // enter
            let q = $(this).val();
            window.location.href = "<?= base_url('inventory') ?>?q=" + encodeURIComponent(q);
        }

    });
    $('#guardarProducto').click(function() {

        let id = $('#producto_id').val();

        let data = {
            nombre: $('#producto_nombre').val().trim(),
            proveedor: $('#producto_proveedor').val().trim(),
            costo_inicial: $('#producto_costo').val(),
            precio_venta: $('#producto_precio').val()
        };

        if (!data.nombre) {
            Swal.fire({
                icon: 'warning',
                title: 'Validación',
                text: 'El nombre es obligatorio'
            });
            return;
        }

        if (!data.precio_venta || data.precio_venta <= 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Validación',
                text: 'Precio inválido'
            });
            return;
        }

        let url = id ?
            '<?= base_url('productos/update') ?>/' + id :
            '<?= base_url('productos/create') ?>';

        $.post(url, data, function(res) {

            if (res.status === 'success') {

                $('#modalProducto').modal('hide');

                setTimeout(() => {

                    Swal.fire({
                        icon: 'success', // ✅ ahora sí correcto
                        title: id ? 'Producto actualizado' : 'Producto creado',
                        text: 'Se guardó correctamente',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload();
                    });

                }, 400); // 👈 le damos más tiempo al modal

            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: res.message || 'Error inesperado'
                });
            }

        }, 'json');

    });
</script>
<?= $this->endSection('content') ?>