<?php if (!empty($paquetes)): ?>
    <?php foreach ($paquetes as $p): ?>
        <tr>

            <td class="text-center">
                #<?= $p->id ?>
            </td>

            <td>
                <?= esc($p->cliente_nombre) ?>
                <br>
                <small class="text-muted">
                    <?= esc($p->cliente_telefono) ?>
                </small>
            </td>

            <td>
                <?= esc($p->destino) ?>
                <br>
                <small class="text-muted">
                    <?= esc($p->encomendista_nombre) ?>
                </small>
            </td>

            <td class="text-center">
                <?= date('d/m/Y', strtotime($p->dia_entrega)) ?>
                <br>
                <small class="text-muted">
                    <?= $p->hora_inicio ?> - <?= $p->hora_fin ?>
                </small>
            </td>

            <td class="text-end">
                $ <?= number_format($p->total, 2) ?>
            </td>

            <td>
                <?php if (!empty($p->estado1)): ?>
                    <?= statusBadge($p->estado1) ?>
                <?php endif; ?>

                <?php if (!empty($p->estado2)): ?>
                    <?= statusBadge($p->estado2) ?>
                <?php endif; ?>
            </td>

            <td class="text-center">
                <a href="<?= base_url('packages/' . $p->id) ?>" class="btn btn-sm btn-info">
                    👁
                </a>
            </td>

        </tr>
    <?php endforeach; ?>
<?php else: ?>
    <tr>
        <td colspan="7" class="text-center">
            No hay paquetes
        </td>
    </tr>
<?php endif; ?>