<?= $this->extend('Layouts/mainbody') ?>
<?= $this->section('content') ?>

<style>
    .cliente-card {
        transition: all 0.2s ease;
        border-radius: 10px;
        margin-bottom: 8px;
    }

    .cliente-card:hover {
        transform: scale(1.01);
    }

    .cliente-row>div {
        border-right: 1px solid #eee;
        padding-right: 10px;
    }

    .cliente-row>div:last-child {
        border-right: none;
    }

    small.text-muted {
        font-size: 13px;
        margin-right: 6px;
    }

    @media (max-width: 768px) {
        .cliente-card .card-body {
            padding-left: 8px;
            padding-right: 8px;
        }

        #clientes-container .col-12 {
            padding-left: 0;
            padding-right: 0;
        }

        .gap-2 {
            gap: 6px;
        }
    }

    @media (max-width: 768px) {
        .cliente-card .card-body {
            padding: 8px;
        }

        .cliente-card .mb-1 {
            margin-bottom: 4px !important;
        }

        .cliente-card small {
            display: inline-block;
            min-width: 60px;
        }
    }
</style>

<div class="row">
    <div class="col-md-12">

        <div class="card">

            <!-- HEADER -->
            <div class="card-header d-flex">
                <h4 class="header-title">Clientes</h4>

                <button class="btn btn-primary btn-sm ml-auto" data-toggle="modal" data-target="#clienteModal">
                    <i class="fa-solid fa-plus"></i> Nuevo Cliente
                </button>
            </div>

            <div class="card-body">

                <!-- 🔍 BUSCADOR -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>Buscar cliente</label>
                        <input type="text" id="searchInput" class="form-control"
                            placeholder="Nombre o teléfono">
                    </div>
                </div>

                <!-- 📋 LISTADO -->
                <div id="clientes-container">

                    <?php foreach ($clientes as $c): ?>
                        <div class="col-12">

                            <div class="card shadow-sm cliente-card">

                                <div class="card-body">

                                    <!-- 📱 MOBILE -->
                                    <div class="d-block d-md-none">

                                        <div class="mb-1">
                                            <small class="text-muted">Cliente:</small>
                                            <?= $c->nombre ?>
                                        </div>

                                        <div class="mb-1">
                                            <small class="text-muted">Tel:</small>
                                            <?= $c->telefono ?>
                                        </div>

                                        <div class="mb-1">
                                            <small class="text-muted">Email:</small>
                                            <?= $c->email ?>
                                        </div>

                                        <div class="d-flex justify-content-end gap-2 mt-2">

                                            <button class="btn btn-sm btn-outline-warning btn-edit-cliente"
                                                data-id="<?= $c->id ?>"
                                                data-nombre="<?= esc($c->nombre) ?>"
                                                data-telefono="<?= esc($c->telefono) ?>"
                                                data-email="<?= esc($c->email) ?>"
                                                data-direccion="<?= esc($c->direccion) ?>">
                                                <i class="fa fa-pen"></i>
                                            </button>

                                            <?php if ($c->nombre !== 'Clientes varios'): ?>
                                                <a href="#"
                                                    class="btn btn-sm btn-outline-danger btn-delete-cliente"
                                                    data-id="<?= $c->id ?>">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            <?php endif; ?>

                                        </div>
                                    </div>

                                    <!-- 💻 DESKTOP -->
                                    <div class="d-none d-md-flex align-items-center justify-content-between cliente-row">

                                        <div style="width:35%;">
                                            <small class="text-muted">Cliente:</small>
                                            <?= $c->nombre ?>
                                        </div>

                                        <div style="width:20%;" class="text-center">
                                            <small class="text-muted">Tel:</small>
                                            <?= $c->telefono ?>
                                        </div>

                                        <div style="width:30%;" class="text-center">
                                            <small class="text-muted">Email:</small>
                                            <?= $c->email ?>
                                        </div>

                                        <div style="width:15%;" class="text-right">

                                            <a href="/clientes/edit/<?= $c->id ?>"
                                                class="btn btn-sm btn-outline-warning">
                                                <i class="fa fa-pen"></i>
                                            </a>

                                            <?php if ($c->nombre !== 'Clientes varios'): ?>
                                                <a href="/clientes/delete/<?= $c->id ?>"
                                                    class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('¿Eliminar?')">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            <?php endif; ?>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>
                    <?php endforeach; ?>

                </div>

            </div>
        </div>

    </div>
</div>

<!-- 🧾 MODAL CREAR -->
<div class="modal fade" id="clienteModal">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Nuevo Cliente</h5>
                <button class="close" data-dismiss="modal">&times;</button>
            </div>

            <form id="clienteForm" method="post">
                <div class="modal-body">

                    <input type="hidden" name="id" id="cliente_id">

                    <div class="mb-2">
                        <label>Nombre</label>
                        <input type="text" name="nombre" id="nombre" class="form-control" required>
                    </div>

                    <div class="mb-2">
                        <label>Teléfono</label>
                        <input type="text" name="telefono" id="telefono" class="form-control">
                    </div>

                    <div class="mb-2">
                        <label>Email</label>
                        <input type="email" name="email" id="email" class="form-control">
                    </div>

                    <div class="mb-2">
                        <label>Dirección</label>
                        <textarea name="direccion" id="direccion" class="form-control"></textarea>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-success">Guardar</button>
                    <button class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                </div>
            </form>

        </div>
    </div>
</div>
<script>
document.querySelectorAll('.btn-delete-cliente').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();

        const id = this.dataset.id;

        Swal.fire({
            title: '¿Eliminar cliente?',
            text: 'Esta acción no se puede deshacer',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {

                // 👉 redirección normal (simple)
                window.location.href = '/clientes/delete/' + id;

            }
        });
    });
});
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        const modal = $('#clienteModal');
        const form = document.getElementById('clienteForm');
        const title = document.getElementById('modalTitle');

        const inputId = document.getElementById('cliente_id');
        const nombre = document.getElementById('nombre');
        const telefono = document.getElementById('telefono');
        const email = document.getElementById('email');
        const direccion = document.getElementById('direccion');

        // 👉 BOTÓN NUEVO
        document.querySelector('[data-target="#clienteModal"]').addEventListener('click', () => {
            title.innerText = 'Nuevo Cliente';
            form.action = '/clientes/create';

            form.reset();
            inputId.value = '';
        });

        // 👉 BOTÓN EDITAR
        document.querySelectorAll('.btn-edit-cliente').forEach(btn => {
            btn.addEventListener('click', function() {

                title.innerText = 'Editar Cliente';
                form.action = '/clientes/update/' + this.dataset.id;

                inputId.value = this.dataset.id;
                nombre.value = this.dataset.nombre;
                telefono.value = this.dataset.telefono;
                email.value = this.dataset.email;
                direccion.value = this.dataset.direccion;

                modal.modal('show');
            });
        });

    });
</script>
<!-- 🔥 BUSCADOR FRONT -->
<script>
    document.getElementById('searchInput').addEventListener('input', function() {
        let term = this.value.toLowerCase();
        let cards = document.querySelectorAll('.cliente-card');

        cards.forEach(card => {
            let text = card.innerText.toLowerCase();
            card.style.display = text.includes(term) ? '' : 'none';
        });
    });
</script>

<?= $this->endSection() ?>