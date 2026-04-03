<?= $this->extend('Layouts/mainbody') ?>
<?= $this->section('content') ?>

<style>
    .proveedor-card {
        transition: all 0.2s ease;
        border-radius: 10px;
    }

    .proveedor-card:hover {
        transform: scale(1.01);
    }

    .card-body p {
        margin-bottom: 6px;
    }
</style>

<div class="row">
    <div class="col-md-12">
        <div class="card">

            <!-- HEADER -->
            <div class="card-header d-flex">
                <h4 class="header-title">Proveedores</h4>

                <button class="btn btn-primary btn-sm ml-auto"
                    data-toggle="modal"
                    data-target="#proveedorModal">
                    <i class="fa-solid fa-plus"></i> Nuevo
                </button>
            </div>

            <div class="card-body">

                <!-- 🔍 BUSCADOR -->
                <div class="row mb-3">

                    <div class="col-md-6">
                        <label>Buscar proveedor</label>
                        <div class="input-group">

                            <input type="text" id="searchInput" class="form-control"
                                placeholder="Nombre o teléfono">

                            <div class="input-group-append">
                                <span class="input-group-text" id="loading-spinner" style="display:none;">
                                    <i class="fa fa-spinner fa-spin"></i>
                                </span>

                                <button class="btn btn-secondary" id="clearSearchBtn" style="display:none;">
                                    <i class="fa fa-times"></i>
                                </button>
                            </div>

                        </div>
                    </div>

                </div>

                <!-- LISTADO -->
                <div id="table-container">
                    <?= $this->include('proveedores/_proveedores_list') ?>
                </div>

            </div>

        </div>
    </div>
</div>

<!-- 🪟 MODAL CREAR -->
<div class="modal fade" id="proveedorModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Nuevo Proveedor</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <form id="proveedorForm">

                <div class="modal-body">

                    <div class="form-group">
                        <label>Nombre</label>
                        <input type="text" name="nombre" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Teléfono</label>
                        <input type="text" name="telefono" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Dirección</label>
                        <textarea name="direccion" class="form-control"></textarea>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-success" type="submit">Guardar</button>
                    <button class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                </div>

            </form>

        </div>
    </div>
</div>
<div class="modal fade" id="editarProveedorModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Editar Proveedor</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <form id="editarProveedorForm">

                <input type="hidden" name="id" id="edit_id">

                <div class="modal-body">

                    <div class="form-group">
                        <label>Nombre</label>
                        <input type="text" name="nombre" id="edit_nombre" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Teléfono</label>
                        <input type="text" name="telefono" id="edit_telefono" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Dirección</label>
                        <textarea name="direccion" id="edit_direccion" class="form-control"></textarea>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-success" type="submit">Actualizar</button>
                    <button class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                </div>

            </form>

        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        const searchInput = document.getElementById('searchInput');
        const tableContainer = document.getElementById('table-container');
        const loadingSpinner = document.getElementById('loading-spinner');
        const clearSearchBtn = document.getElementById('clearSearchBtn');

        const baseUrl = '<?= base_url('proveedores/searchAjax') ?>';

        let timeout;

        function loadResults(query = '') {

            loadingSpinner.style.display = 'block';

            fetch(`${baseUrl}?q=${encodeURIComponent(query)}`)
                .then(res => res.text())
                .then(html => {
                    tableContainer.innerHTML = html;
                    loadingSpinner.style.display = 'none';
                    updateClearButton(query);

                    bindEditButtons(); 
                });
        }

        searchInput.addEventListener('input', function() {
            clearTimeout(timeout);
            const query = this.value.trim();

            timeout = setTimeout(() => loadResults(query), 300);
        });

        clearSearchBtn.addEventListener('click', function() {
            searchInput.value = '';
            loadResults('');
            updateClearButton('');
        });

        function bindEditButtons() {
            document.querySelectorAll('.editar-proveedor-btn').forEach(btn => {

                btn.addEventListener('click', function() {

                    document.getElementById('edit_id').value = this.dataset.id;
                    document.getElementById('edit_nombre').value = this.dataset.nombre;
                    document.getElementById('edit_telefono').value = this.dataset.telefono;
                    document.getElementById('edit_direccion').value = this.dataset.direccion;

                    $('#editarProveedorModal').modal('show');
                });

            });
        }

        function updateClearButton(query) {
            clearSearchBtn.style.display = query.length > 0 ? 'block' : 'none';
        }

        const editForm = document.getElementById('editarProveedorForm');

        editForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(editForm);

            fetch("<?= base_url('proveedores/update') ?>", {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {

                if (data.status === 'success') {

                    Swal.fire({
                        icon: 'success',
                        title: 'Proveedor actualizado',
                        timer: 1500,
                        showConfirmButton: false
                    });

                    $('#editarProveedorModal').modal('hide');

                    loadResults('');
                }

            });
        });

        // GUARDAR PROVEEDOR
        const form = document.getElementById('proveedorForm');

        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(form);

            fetch("<?= base_url('proveedores/store') ?>", {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {

                    if (data.status === 'success') {

                        Swal.fire({
                            icon: 'success',
                            title: 'Proveedor creado',
                            timer: 1500,
                            showConfirmButton: false
                        });

                        $('#proveedorModal').modal('hide');
                        form.reset();

                        loadResults('');
                    }

                });

        });
        bindEditButtons();
    });
</script>

<?= $this->endSection() ?>