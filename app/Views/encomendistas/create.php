<?= $this->extend('Layouts/mainbody') ?>
<?= $this->section('content') ?>
<?= csrf_field() ?>
<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm">
            <div class="card-header d-flex">
                <h4 class="mb-3">Nuevo encomendista</h4>
            </div>  
            <div class="card-body">
                <?php if (session()->getFlashdata('errors')): ?>
                    <div class="alert alert-danger">
                        <ul>
                            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form id="encomendistaForm" action="<?= base_url('encomendistas') ?>" method="post" novalidate>

                    <div class="mb-3">
                        <label for="encomendista_name" class="form-label">Nombre del Encomendista</label>
                        <input type="text" name="encomendista_name" id="encomendista_name" class="form-control" minlength="3" required>
                        <div class="invalid-feedback">
                            El nombre debe tener al menos 3 caracteres.
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success">Guardar</button>
                    <a href="<?= base_url('sellers') ?>" class="btn btn-secondary">Cancelar</a>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    (() => {
        'use strict';
        const form = document.getElementById('encomendistaForm');

        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault();  // ❌ evita enviar si hay errores
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    })();
</script>

<?= $this->endSection() ?>