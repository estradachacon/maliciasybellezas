<?= $this->extend('Layouts/mainbody') ?>
<?= $this->section('content') ?>

<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between">
        <h5>Casilleros Externos</h5>
        <?php if (tienePermiso('crear_casilleros_externos')): ?>
            <button class="btn btn-primary btn-sm" id="btnNew">
                Nuevo
            </button>
        <?php endif; ?>
    </div>

    <div class="card-body">
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Activo</th>
                    <th width="150">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($locations as $loc): ?>
                    <tr>
                        <td><?= $loc->id ?></td>
                        <td><?= esc($loc->nombre) ?></td>
                        <td><?= esc($loc->descripcion) ?></td>
                        <td>
                            <?php if ($loc->activo): ?>
                                <span class="badge bg-success">Activo</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Inactivo</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (tienePermiso('editar_casilleros_externos')): ?>
                                <button class="btn btn-sm btn-info btnEdit"
                                    data-id="<?= $loc->id ?>">
                                    <i class="fa-solid fa-edit"></i>
                                </button>
                            <?php endif; ?>

                            <?php if (tienePermiso('eliminar_casilleros_externos')): ?>
                                <button class="btn btn-sm btn-danger btnDelete"
                                    data-id="<?= $loc->id ?>">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="locationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="locationForm">
                <div class="modal-header">
                    <h5 class="modal-title">Ubicación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">

                    <input type="hidden" id="location_id" name="id">

                    <div class="mb-3">
                        <label>Nombre</label>
                        <input type="text" id="nombre" name="nombre" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Descripción</label>
                        <textarea id="descripcion" name="descripcion" class="form-control"></textarea>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" id="activo" name="activo" value="1" class="form-check-input" checked>
                        <label class="form-check-label">Activo</label>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        const modal = new bootstrap.Modal(document.getElementById('locationModal'));

        /* =========================
           NUEVO
        ========================= */
        document.getElementById('btnNew').addEventListener('click', () => {
            document.getElementById('locationForm').reset();
            document.getElementById('location_id').value = '';
            modal.show();
        });

        /* =========================
           EDITAR
        ========================= */
        document.querySelectorAll('.btnEdit').forEach(btn => {
            btn.addEventListener('click', function() {

                const id = this.dataset.id;

                fetch(`<?= base_url('external-locations/get') ?>/${id}`)
                    .then(res => res.json())
                    .then(data => {

                        document.getElementById('location_id').value = data.id;
                        document.getElementById('nombre').value = data.nombre;
                        document.getElementById('descripcion').value = data.descripcion;
                        document.getElementById('activo').checked = data.activo == 1;

                        modal.show();
                    });
            });
        });

        /* =========================
           GUARDAR
        ========================= */
        document.getElementById('locationForm').addEventListener('submit', function(e) {

            e.preventDefault();

            const id = document.getElementById('location_id').value;

            const url = id ?
                `<?= base_url('external-locations/update') ?>/${id}` :
                `<?= base_url('external-locations/store') ?>`;

            const formData = new FormData(this);

            fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(res => res.json())
                .then(data => {

                    if (data.success) {

                        modal.hide();

                        Swal.fire({
                            icon: 'success',
                            title: id ? 'Actualizado' : 'Creado',
                            text: 'Registro guardado correctamente',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });

                    } else {

                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message ?? 'Ocurrió un problema'
                        });

                    }

                });

        });

    });
</script>
<script>
    document.querySelectorAll('.btnDelete').forEach(btn => {

        btn.addEventListener('click', function() {

            const id = this.dataset.id;

            Swal.fire({
                title: '¿Eliminar registro?',
                text: "Esta acción no se puede deshacer",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {

                if (result.isConfirmed) {

                    fetch(`<?= base_url('external-locations/delete') ?>/${id}`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(() => {

                            Swal.fire({
                                icon: 'success',
                                title: 'Eliminado',
                                text: 'Registro eliminado correctamente',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });

                        });

                }

            });

        });

    });
</script>
<?= $this->endSection() ?>