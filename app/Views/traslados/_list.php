<?php if (!empty($traslados)): ?>

    <!-- DESKTOP -->
    <div class="d-none d-md-block">
        <table class="table table-hover table-sm">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Origen</th>
                    <th>Destino</th>
                    <th>Productos</th>
                    <th>Costo traslado</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($traslados as $t): ?>
                    <tr>
                        <td><?= $t->id ?></td>
                        <td><?= esc($t->origen_nombre) ?></td>
                        <td><?= esc($t->destino_nombre) ?></td>
                        <td class="text-center"><?= $t->total_productos ?></td>
                        <td>
                            <?php if ($t->costo_traslado > 0): ?>
                                <span class="text-danger">$<?= number_format($t->costo_traslado, 2) ?></span>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php
                            $badgeColor = match($t->estado) {
                                'completado' => 'success',
                                'pendiente'  => 'warning',
                                'cancelado'  => 'danger',
                                default      => 'secondary'
                            };
                            ?>
                            <span class="badge badge-<?= $badgeColor ?>">
                                <?= ucfirst($t->estado) ?>
                            </span>
                        </td>
                        <td><?= date('d/m/Y', strtotime($t->created_at)) ?></td>
                        <td class="text-right">
                            <a href="<?= base_url('traslados/' . $t->id) ?>"
                                class="btn btn-sm btn-outline-secondary">
                                <i class="fa fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- MOBILE -->
    <div class="d-block d-md-none">
        <?php foreach ($traslados as $t): ?>
            <div class="card compra-card border mb-2">
                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-start mb-1">
                        <div>
                            <span class="font-weight-bold">#<?= $t->id ?></span>
                            <small class="text-muted ml-1"><?= date('d/m/Y', strtotime($t->created_at)) ?></small>
                        </div>
                        <?php
                        $badgeColor = match($t->estado) {
                            'completado' => 'success',
                            'pendiente'  => 'warning',
                            'cancelado'  => 'danger',
                            default      => 'secondary'
                        };
                        ?>
                        <span class="badge badge-<?= $badgeColor ?>">
                            <?= ucfirst($t->estado) ?>
                        </span>
                    </div>

                    <div class="d-flex align-items-center mb-1" style="gap:6px;">
                        <span class="badge badge-light border"><?= esc($t->origen_nombre) ?></span>
                        <i class="fa fa-arrow-right text-muted" style="font-size:11px;"></i>
                        <span class="badge badge-light border"><?= esc($t->destino_nombre) ?></span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <div>
                            <small class="text-muted"><?= $t->total_productos ?> producto(s)</small>
                            <?php if ($t->costo_traslado > 0): ?>
                                <small class="text-danger ml-2">
                                    Flete: $<?= number_format($t->costo_traslado, 2) ?>
                                </small>
                            <?php endif; ?>
                        </div>
                        <a href="<?= base_url('traslados/' . $t->id) ?>"
                            class="btn btn-sm btn-outline-secondary">
                            <i class="fa fa-eye"></i>
                        </a>
                    </div>

                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- PAGINACIÓN -->
    <div id="pagination-links">
        <?= $pager->links('traslados', 'default_full') ?>
    </div>

<?php else: ?>
    <p class="text-muted text-center py-3">Sin traslados registrados</p>
<?php endif; ?>