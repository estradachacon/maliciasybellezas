<?= $this->extend('Layouts/mainbody') ?>
<?= $this->section('content') ?>
<?= csrf_field() ?>
<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm">
            <div class="card-header d-flex">
                <h4 class="header-title mb-0">Crear nuevo usuario</h4>
            </div>

            <div class="card-body">
                <?php if (session()->get('errors')): ?>
                    <div class="alert alert-danger">
                        <?php foreach (session('errors') as $error): ?>
                            <div><?= esc($error) ?></div>
                        <?php endforeach ?>
                    </div>
                <?php endif ?>

                <form action="<?= base_url('users/create') ?>" method="post">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="user_name" class="form-label">Nombre de usuario</label>
                            <input type="text" class="form-control" id="user_name" name="user_name" value="<?= old('user_name') ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Correo electrónico</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= old('email') ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="user_password" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="user_password" name="user_password" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="codigo" class="form-label">Código</label>
                            <input type="text"
                                class="form-control"
                                id="codigo"
                                name="codigo"
                                placeholder="Ej: USR-001"
                                minlength="2"
                                value="<?= old('codigo') ?>"
                                required
                                oninvalid="this.setCustomValidity('El código debe tener al menos 2 caracteres')"
                                oninput="this.setCustomValidity('')">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="role_id" class="form-label">Asignar Rol</label>
                            <select class="form-select" name="role_id" required>
                                <option value="">Seleccione un rol</option>

                                <?php foreach ($roles as $role): ?>
                                    <option value="<?= esc($role['id']) ?>"
                                        <?= old('role_id') == $role['id'] ? 'selected' : '' ?>>
                                        <?= esc($role['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>

                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="branch_id" class="form-label">Asignar sucursal</label>
                            <select class="form-select" id="branch_id" name="branch_id" required>
                                <option value="">Seleccione una sucursal</option>

                                <?php foreach ($branches as $branch): ?>
                                    <option value="<?= esc($branch->id) ?>"
                                        <?= old('branch_id') == $branch->id ? 'selected' : '' ?>>
                                        <?= esc($branch->branch_name) ?>
                                    </option>
                                <?php endforeach; ?>

                            </select>
                        </div>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Crear usuario</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>