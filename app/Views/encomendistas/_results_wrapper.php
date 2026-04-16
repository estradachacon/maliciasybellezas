<div class="d-none d-md-block">
    <?= view('encomendistas/_encom_table', [
        'encomendistas' => $encomendistas,
        'pager' => $pager,
        'q' => $q ?? null
    ]) ?>
</div>

<div class="d-block d-md-none">
    <?= view('encomendistas/_encom_cards', [
        'encomendistas' => $encomendistas,
        'pager' => $pager,
        'q' => $q ?? null
    ]) ?>
</div>