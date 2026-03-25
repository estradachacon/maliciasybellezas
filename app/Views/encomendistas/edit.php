<?= $this->extend('Layouts/mainbody') ?>
<?= $this->section('content') ?>

<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm">
            <div class="card-header d-flex">
                <h4 class="header-title mb-0">
                    Editar encomendista: <?= esc($encomendista->encomendista_name) ?>
                </h4>
            </div>

            <div class="card-body">
                <!-- Formulario -->
                <form action="<?= base_url('encomendistas/update/' . $encomendista->id) ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="encomendista_name" class="form-label">Nombre</label>
                            <input 
                                type="text" 
                                class="form-control" 
                                id="encomendista_name" 
                                name="encomendista_name" 
                                value="<?= esc($encomendista->encomendista_name) ?>" 
                                required
                            >
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-success">
                            Guardar cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>