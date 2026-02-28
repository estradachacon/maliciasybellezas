<?= $this->extend('Layouts/mainbody') ?>
<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex">
                <h4 class="mb-4">Mantenimiento de Colonias</h4>
            </div>
            <div class="card-body">

                <div class="row">

                    <div class="col-md-4">
                        <label>Departamento</label>
                        <select id="departamento" class="form-control">
                            <option value="">Seleccione</option>
                            <?php foreach ($departamentos as $d): ?>
                                <option value="<?= $d['id'] ?>">
                                    <?= esc($d['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label>Municipio</label>
                        <select id="municipio" class="form-control" disabled>
                            <option value="">Seleccione</option>
                        </select>
                    </div>

                </div>

            </div>
        </div>

        <div class="card mt-4">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-md-6">
                        <button class="btn btn-success" id="btnNueva">
                            Nueva Colonia
                        </button>
                    </div>
                    <div class="col-md-6 text-end">
                        <input type="text" id="buscar" class="form-control" placeholder="Buscar colonia...">
                    </div>
                </div>
                <table class="table table-bordered table-sm" id="tablaColonias">
                    <thead class="table-light">
                        <tr>
                            <th>Departamento</th>
                            <th>Municipio</th>
                            <th>Colonia</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>

            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Colonia</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="editId">
                <div class="mb-3">
                    <label>Nombre</label>
                    <input type="text" id="editNombre" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" id="btnUpdate">Guardar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalCreate" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nueva Colonia</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label>Nombre</label>
                    <input type="text" id="createNombre" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" id="btnGuardarNueva">Guardar</button>
            </div>
        </div>
    </div>
</div>
<!-- Toast advertencia -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1080">
    <div id="toastMunicipio" class="toast align-items-center text-bg-warning border-0" role="alert">
        <div class="d-flex">
            <div class="toast-body">
                Debe seleccionar un municipio primero para poder buscar.
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {

        let departamentoId = null;
        let municipioId = null;

        function limpiarTabla() {
            $('#tablaColonias tbody').html('');
        }

        function cargarColonias() {

            if (!departamentoId || !municipioId) {
                limpiarTabla();
                return;
            }

            $.get(`/colonias/filtrar/${departamentoId}/${municipioId}`, function(data) {

                let filas = '';

                data.forEach(function(c) {

                    filas += `
                    <tr>
                        <td>${c.departamento}</td>
                        <td>${c.municipio}</td>
                        <td>${c.nombre}</td>
                        <td>
                            <button class="btn btn-sm btn-warning btnEdit" data-id="${c.id}">Editar</button>
                            <button class="btn btn-sm btn-danger btnDelete" data-id="${c.id}">Borrar</button>
                        </td>
                    </tr>
                `;
                });

                $('#tablaColonias tbody').html(filas);

            });
        }

        // Cascada
        $('#departamento').change(function() {
            departamentoId = $(this).val();
            municipioId = null;
            limpiarTabla();

            $('#municipio').prop('disabled', true).html('<option>Cargando...</option>');

            if (departamentoId) {

                $.get('/colonias/municipios/' + departamentoId, function(data) {

                    let opciones = '<option value="">Seleccione</option>';

                    data.forEach(function(m) {
                        opciones += `<option value="${m.id}">${m.nombre}</option>`;
                    });

                    $('#municipio').html(opciones).prop('disabled', false);
                });
            }
        });
        // Abrir modal crear
        $('#btnNueva').click(function() {

            if (!departamentoId) {
                Swal.fire({
                    icon: 'info',
                    title: 'Seleccione un departamento',
                    text: 'Debe elegir un departamento antes de continuar'
                });
                return;
            }

            if (!municipioId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Seleccione un municipio',
                    text: 'Debe elegir un municipio antes de crear una colonia'
                });
                return;
            }

            $('#createNombre').val('');
            $('#modalCreate').modal('show');

        });

        // Guardar nueva colonia
        $('#btnGuardarNueva').click(function() {

            let nombre = $('#createNombre').val().trim();

            if (nombre === '') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Campo requerido',
                    text: 'Debe ingresar el nombre de la colonia'
                });
                return;
            }

            Swal.fire({
                title: '¿Crear colonia?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, crear',
                cancelButtonText: 'Cancelar'
            }).then((result) => {

                if (result.isConfirmed) {

                    $.post('/colonias/create', {
                        municipio_id: municipioId,
                        nombre: nombre
                    }, function(response) {

                        if (response.status === 'ok') {

                            $('#modalCreate').modal('hide');
                            cargarColonias();

                            Swal.fire({
                                icon: 'success',
                                title: 'Creado',
                                text: 'Colonia creada correctamente',
                                timer: 1500,
                                showConfirmButton: false
                            });

                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }

                    });

                }

            });

        });
        $('#municipio').change(function() {
            municipioId = $(this).val();
            cargarColonias();
        });

        // Buscador real con validación
        $('#buscar').on('input', function() {

            if (!municipioId) {

                const toastElement = document.getElementById('toastMunicipio');
                const toast = new bootstrap.Toast(toastElement, {
                    delay: 2000
                });

                toast.show();

                $(this).val('');
                return;
            }

            let value = $(this).val().toLowerCase();

            $("#tablaColonias tbody tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });

        });

        // EDIT
        $(document).on('click', '.btnEdit', function() {

            let id = $(this).data('id');

            $.get('/colonias/get/' + id, function(data) {

                $('#editId').val(data.id);
                $('#editNombre').val(data.nombre);

                $('#modalEdit').modal('show');
            });
        });

        $('#btnUpdate').click(function() {

            let id = $('#editId').val();
            let nombre = $('#editNombre').val().trim();

            if (nombre === '') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Campo requerido',
                    text: 'Debe ingresar el nombre'
                });
                return;
            }

            Swal.fire({
                title: '¿Guardar cambios?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, guardar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {

                if (result.isConfirmed) {

                    $.post('/colonias/update/' + id, {
                        nombre: nombre
                    }, function() {

                        $('#modalEdit').modal('hide');
                        cargarColonias();

                        Swal.fire({
                            icon: 'success',
                            title: 'Actualizado',
                            text: 'Colonia actualizada correctamente',
                            timer: 1500,
                            showConfirmButton: false
                        });

                    });

                }

            });

        });

        // DELETE
        $(document).on('click', '.btnDelete', function() {

            let id = $(this).data('id');

            Swal.fire({
                title: '¿Eliminar colonia?',
                text: 'Esta acción no se puede deshacer',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {

                if (result.isConfirmed) {

                    $.ajax({
                        url: '/colonias/delete/' + id,
                        type: 'DELETE',
                        success: function() {

                            cargarColonias();

                            Swal.fire({
                                icon: 'success',
                                title: 'Eliminado',
                                text: 'Colonia eliminada correctamente',
                                timer: 1500,
                                showConfirmButton: false
                            });

                        }
                    });

                }

            });

        });

    });
</script>

<?= $this->endSection() ?>