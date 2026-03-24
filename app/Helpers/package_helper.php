<?php
function trackingBadge($status)
{
    $map = [
        'pendiente' => 'warning',
        'reenvio' => 'orange',
        'devuelto' => 'success',
        'entregado' => 'success',
        'asignado_para_entrega' => 'info',
        'asignado_para_recolecta' => 'info',
        'recolecta_fallida' => 'warning',
        'asignado' => 'info',
        'no_retirado' => 'danger',
        'en_casillero' => 'info',
        'en_casillero_externo' => 'info',
        'recolectado' => 'info',
        'finalizado' => 'success',
        'remunerado' => 'success',
        'Activo' => 'success',
        'Inactivo' => 'danger',
        'Efectivo' => 'success',
        'Banco' => 'info',
        'depositado' => 'info',
        'en_ruta' => 'success',
    ];

    $color = $map[$status] ?? 'secondary';

    $label = str_replace('_', ' ', ucfirst($status));

    return '<span class="badge badge-' . $color . '">' . $label . '</span>';
}

function serviceLabel($tipo)
{
    $labels = [
        1 => 'Punto Fijo',
        2 => 'Personalizado',
        3 => 'Recolección',
        4 => 'Casillero'
    ];

    return $labels[$tipo] ?? 'Desconocido';
}

function statusBadge($status)
{
    $map = [
        'pendiente'     => 'warning',
        'reenvio'     => 'orange',
        'devuelto'      => 'success',
        'entregado'     => 'success',
        'asignado_para_entrega'      => 'info',
        'asignado_para_recolecta'      => 'info',
        'recolecta_fallida'      => 'warning',
        'asignado'      => 'info',
        'no_retirado'      => 'danger',
        'en_casillero'  => 'info',
        'en_casillero_externo'  => 'info',
        'recolectado'  => 'info',
        'finalizado'  => 'success',
        'remunerado'  => 'success',
        'Activo'  => 'success',
        'Inactivo'  => 'danger',
        'Efectivo'  => 'success',
        'Banco'  => 'info',
        'depositado' => 'info',
        'en_ruta' => 'success',
    ];

    $color = $map[$status] ?? 'secondary';

    // Transformar el texto para mostrarlo bonito
    $label = ucwords(str_replace('_', ' ', $status));

    return "<span class='badge badge-$color'>$label</span>";
}

function trackingHeaderBadge($status)
{
    $map = [
        'asignado' => ['Asignado', 'badge-secondary'],
        'en_ruta' => ['En ruta', 'badge-primary'],
        'finalizado' => ['Finalizado', 'badge-success'],
        'rendido' => ['Rendido', 'badge-success'],
        'cancelado' => ['Cancelado', 'badge-danger']
    ];

    $label = $map[$status][0] ?? ucfirst($status);
    $class = $map[$status][1] ?? 'badge-dark';

    return "<span class='badge {$class}'>{$label}</span>";
}
