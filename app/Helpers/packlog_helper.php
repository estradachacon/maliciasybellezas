<?php

if (!function_exists('addPackLog')) {

    function addPackLog($packageId, $mensaje)
    {
        $db = \Config\Database::connect();

        // Obtener log actual
        $builder = $db->table('packages');
        $paquete = $builder->select('packlog')->where('id', $packageId)->get()->getRow();

        $logActual = $paquete->packlog ?? '';

        // Fecha bonita
        $fecha = date('Y-m-d H:i:s');

        // Usuario (opcional)
        $usuario = session('usuario') ?? 'Sistema';

        // Nuevo log
        $nuevo = "[{$fecha}] ({$usuario}) {$mensaje}";

        // Concatenar
        $logFinal = $logActual
            ? $logActual . '<br>' . $nuevo
            : $nuevo;

        // Guardar
        $builder->where('id', $packageId)
                ->update(['packlog' => $logFinal]);
    }
}