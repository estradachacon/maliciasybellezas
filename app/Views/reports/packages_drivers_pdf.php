<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">

<style>

body{
    font-family: DejaVu Sans, sans-serif;
    font-size:12px;
}

.header{
    background:#2c7be5;
    color:white;
    padding:10px;
    text-align:center;
    margin-bottom:10px;
}

.filters{
    margin-bottom:15px;
    font-size:11px;
}

table{
    width:100%;
    border-collapse:collapse;
}

th, td{
    border:1px solid #ccc;
    padding:6px;
}

th{
    background:#f1f3f5;
}

.text-right{
    text-align:right;
}

.text-center{
    text-align:center;
}

tfoot td{
    font-weight:bold;
    background:#f8f9fa;
}

</style>

</head>

<body>

<div class="header">
    <h2>Reporte de Paquetería por Conductor</h2>
</div>

<div class="filters">

<?php
$totalFlete = 0;
$totalMonto = 0;

$driverName = '';

if (!empty($filters['driver_id']) && !empty($packages)) {
    $driverName = $packages[0]->motorista;
}
?>

<?php if (!empty($driverName)): ?>
<strong>Motorista:</strong> <?= esc($driverName) ?>
<?php endif; ?>

<?php if (!empty($filters['fecha_desde'])): ?>
&nbsp;&nbsp; <strong>Desde:</strong>
<?= date('d/m/Y', strtotime($filters['fecha_desde'])) ?>
<?php endif; ?>

<?php if (!empty($filters['fecha_hasta'])): ?>
&nbsp;&nbsp; <strong>Hasta:</strong>
<?= date('d/m/Y', strtotime($filters['fecha_hasta'])) ?>
<?php endif; ?>

</div>

<table>

<thead>
<tr>
<th width="40">#</th>
<th>Cliente</th>
<th>Motorista</th>
<th>Servicio</th>
<th width="90">Fecha</th>
<th width="100">Estatus</th>
<th width="80">Flete</th>
<th width="80">Monto</th>
</tr>
</thead>

<tbody>

<?php if (!empty($packages)): ?>

<?php foreach ($packages as $pkg): ?>

<?php
$totalFlete += $pkg->flete_total;
$totalMonto += $pkg->monto;
?>

<tr>

<td class="text-center"><?= $pkg->id ?></td>

<td><?= esc($pkg->cliente) ?></td>

<td><?= esc($pkg->motorista) ?></td>

<td><?= serviceLabel($pkg->tipo_servicio) ?></td>

<td class="text-center">
<?= date('d/m/Y', strtotime($pkg->fecha_ingreso)) ?>
</td>

<td class="text-center">
<?= ucfirst(str_replace('_',' ',$pkg->estatus)) ?>
</td>

<td class="text-right">
$<?= number_format($pkg->flete_total,2) ?>
</td>

<td class="text-right">
$<?= number_format($pkg->monto,2) ?>
</td>

</tr>

<?php endforeach; ?>

</tbody>

<tfoot>

<tr>
<td colspan="6" class="text-right">TOTALES</td>

<td class="text-right">
$<?= number_format($totalFlete,2) ?>
</td>

<td class="text-right">
$<?= number_format($totalMonto,2) ?>
</td>

</tr>

</tfoot>

<?php else: ?>

<tr>
<td colspan="8" class="text-center">
No hay resultados para los filtros seleccionados
</td>
</tr>

<?php endif; ?>

</table>

</body>
</html>