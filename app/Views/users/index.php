<?= $this->extend('Layouts/mainbody') ?>
<?= $this->section('content') ?>
<style>
    .select2-container .select2-selection--single {
        height: 38px !important;
        /* altura estándar Bootstrap */
        border: 1px solid #ced4da;
        border-radius: .375rem;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 36px !important;
        /* centra texto */
        padding-left: .75rem;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex">
                <h4 class="header-title">Lista de usuarios</h4>

                <?php if (tienePermiso('crear_usuarios')): ?>
                    <a class="btn btn-primary btn-sm ml-auto" href="<?= base_url('users/new') ?>">
                        <i class="fa-solid fa-plus"></i> Crear usuario
                    </a>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <label>Buscar usuario</label>
                        <select class="form-control select2-user" style="width:100%"></select>
                    </div>
                </div>
                <table class="table table-bordered" id="users-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre de Usuario</th>
                            <th>Email</th>
                            <th>Código</th>
                            <th class="col-2">Rol</th>
                            <th class="col-2">Sucursal</th>
                            <th class="col-1 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($users)): ?>
                            <tr>
                                <td colspan="7" class="text-center">No hay usuarios registrados.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?= esc($user['id']) ?></td>
                                    <td><?= esc($user['user_name']) ?></td>
                                    <td><?= esc($user['email']) ?></td>
                                    <td class="text-center">
                                        <?php if (!empty($user['codigo'])): ?>
                                            <span class="badge bg-primary text-white px-3 py-2 fs-6">
                                                <?= esc($user['codigo']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-light text-muted border px-3 py-2 fs-6">
                                                Sin código
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php
                                        $roleName = esc($user['role_name'] ?? 'Sin rol');

                                        $rolesStyles = [
                                            'Gerente'   => 'bg-dark text-white',
                                            'Pagador'   => 'bg-success text-white',
                                            'Digitador' => 'bg-info text-white',
                                            'Motorista' => 'bg-secondary text-white',
                                        ];

                                        if (isset($rolesStyles[$roleName])) {
                                            $class = $rolesStyles[$roleName];
                                        } else {
                                            $colors = ['primary', 'secondary', 'success', 'danger', 'warning', 'info'];
                                            $index = crc32($roleName) % count($colors);
                                            $class = 'bg-' . $colors[$index] . ' text-white';
                                        }
                                        ?>
                                        <span class="badge <?= $class ?> rounded-pill px-3 py-2 shadow-sm">
                                            <?= $roleName ?>
                                        </span>
                                    </td>
                                    <td><?= esc($user['branch_name']) ?></td>
                                    <td class="text-center">

                                        <?php if (tienePermiso('editar_usuarios')): ?>
                                            <a href="<?= base_url('users/edit/' . $user['id']) ?>"
                                                class="btn btn-sm btn-info"
                                                title="Editar">
                                                <i class="fa-solid fa-edit"></i>
                                            </a>
                                        <?php endif; ?>

                                        <?php if (tienePermiso('eliminar_usuarios')): ?>
                                            <button class="btn btn-sm btn-danger delete-btn"
                                                data-id="<?= $user['id'] ?>">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        <?php endif; ?>

                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Botones eliminar
        const deleteButtons = document.querySelectorAll('.delete-btn');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: 'Esta acción no se puede deshacer.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        const csrfHeader = document.querySelector('meta[name="csrf-header"]').getAttribute('content');
                        fetch("<?= base_url('users/delete') ?>", {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded',
                                    'X-Requested-With': 'XMLHttpRequest',
                                    [csrfHeader]: csrfToken
                                },
                                body: new URLSearchParams({
                                    id: id
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                Swal.fire({
                                    title: data.status === 'success' ? 'Éxito' : 'Error',
                                    text: data.message,
                                    icon: data.status,
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                                if (data.status === 'success') {
                                    const row = button.closest('tr');
                                    if (row) row.remove();
                                    setTimeout(() => {
                                        window.location.reload();
                                    }, 1500);
                                }
                            })
                            .catch(err => {
                                Swal.fire('Error', 'Ocurrió un problema en la petición.', 'error');
                            });
                    }
                });
            });
        });
    });
</script>
<script>
    $(document).ready(function() {

        $('.select2-user').select2({
            placeholder: "Buscar usuario...",
            allowClear: true,
            minimumInputLength: 1,
            ajax: {
                url: "<?= base_url('users/search') ?>",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        term: params.term
                    };
                },
                processResults: function(data) {
                    return data; // 👈 ya viene con {results:[]}
                }
            }
        });

        $('.select2-user').on('change', function() {

            let userId = $(this).val();

            $('#users-table tbody tr').each(function() {
                let row = $(this);
                let id = row.find('td:eq(0)').text().trim();

                if (!userId) {
                    row.show();
                    return;
                }

                if (id == userId) {
                    row.show();
                } else {
                    row.hide();
                }
            });

        });
    });
</script>
<?= $this->endSection() ?>