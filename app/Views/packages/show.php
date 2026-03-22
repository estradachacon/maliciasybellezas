<?= $this->extend('Layouts/mainbody') ?>
<?= $this->section('content') ?>

<div class="row">
    <div class="col-md-12">

        <div class="card shadow-sm">

            <!-- HEADER -->
            <div class="card-header d-flex">

                <div>
                    <h4 class="mb-0">
                        Paquete
                        <span class="badge bg-primary ms-2 text-white">
                            #<?= $paquete->id ?>
                        </span>
                    </h4>

                    <small class="text-muted">
                        Registro de envío
                    </small>
                </div>

            </div>

            <div class="card-body">

                <!-- INFO PRINCIPAL -->
                <div class="row mb-4">

                    <!-- CLIENTE -->
                    <div class="col-md-6">
                        <div class="p-3 border rounded h-100">

                            <small class="text-muted">Cliente</small>
                            <div class="fw-bold">
                                <?= esc($paquete->cliente_nombre) ?>
                            </div>

                            <small class="text-muted mt-2 d-block">Teléfono</small>
                            <div>
                                <?= esc($paquete->cliente_telefono) ?>
                            </div>

                        </div>
                    </div>

                    <!-- ENTREGA -->
                    <div class="col-md-6">
                        <div class="p-3 border rounded h-100">

                            <small class="text-muted">Fecha de entrega</small>
                            <div class="fw-bold">
                                <?= date('d/m/Y', strtotime($paquete->dia_entrega)) ?>
                            </div>

                            <small class="text-muted mt-2 d-block">Horario</small>
                            <div>
                                <?= $paquete->hora_inicio ?> - <?= $paquete->hora_fin ?>
                            </div>

                        </div>
                    </div>

                </div>

                <!-- DESTINO -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="p-3 border rounded">

                            <small class="text-muted">Destino</small>
                            <div class="fw-semibold">
                                <?= esc($paquete->destino) ?>
                            </div>

                            <small class="text-muted mt-2 d-block">Encomendista</small>
                            <div>
                                <?= esc($paquete->encomendista_nombre ?: '—') ?>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- FOTO -->
                <?php if (!empty($paquete->foto)): ?>
                    <div class="row mb-4">
                        <div class="col-md-12 text-center">

                            <small class="text-muted d-block mb-2">Foto del paquete</small>

                            <img src="<?= base_url('uploads/paquetes/' . $paquete->foto) ?>"
                                class="img-fluid rounded shadow"
                                style="max-height:300px; cursor:pointer;"
                                onclick="verImagen(this.src)">

                        </div>
                    </div>
                <?php endif; ?>

                <!-- TOTAL -->
                <div class="row">

                    <div class="col-md-4 offset-md-8">

                        <div class="p-3 border rounded text-end bg-light">

                            <small class="text-muted">Total</small>

                            <div class="fs-4 fw-bold text-success">
                                $<?= number_format($paquete->total, 2) ?>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>
</div>

<!-- MODAL IMAGEN -->
<div class="modal fade" id="modalImagen" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-dark text-center">
            <div class="modal-body p-2">
                <img id="imagenGrande" class="img-fluid rounded">
            </div>
        </div>
    </div>
</div>

<script>
function verImagen(src) {
    $('#imagenGrande').attr('src', src);
    new bootstrap.Modal(document.getElementById('modalImagen')).show();
}
</script>

<?= $this->endSection() ?>