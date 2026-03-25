<?php if (!empty($deposits)): ?>
    <?php foreach ($deposits as $d): ?>
        <tr>
            <td class="text-center"><?= $d->id ?></td>

            <td><?= esc($d->encomendista_nombre) ?></td>

            <td class="text-center">
                <span class="badge bg-info text-white">
                    <?= $d->cantidad_paquetes ?>
                </span>
            </td>

            <td class="text-end">
                $ <?= number_format($d->flete_total, 2) ?>
            </td>

            <td class="text-center">
                <?= date('d/m/Y', strtotime($d->fecha)) ?>
            </td>

            <td class="text-center">
                <small class="text-muted">
                    <?= date('d/m/Y H:i', strtotime($d->created_at)) ?>
                </small>
            </td>
        </tr>
    <?php endforeach; ?>
<?php else: ?>
    <tr>
        <td colspan="6" class="text-center">
            No hay registros
        </td>
    </tr>
<?php endif; ?>