<?php

if (!function_exists('addPackLog')) {

    function addPackLog($packageId, $mensaje)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('paquetes');

        // Obtener log actual
        $paquete = $builder
            ->select('packlog')
            ->where('id', $packageId)
            ->get()
            ->getRow();

        $logActual = $paquete->packlog ?? '';

        // Fecha
        $fecha = date('Y-m-d H:i:s');

        // 🔥 USUARIO CORRECTO
        $session = session();
        $usuario = $session->get('user_name') ?? 'Sistema';

        // Nuevo log
        $nuevo = "[{$fecha}] ({$usuario}) {$mensaje}";

        // Concatenar (mejor con \n)
        $logFinal = $logActual
            ? $logActual . "\n" . $nuevo
            : $nuevo;

        // Guardar
        $builder->where('id', $packageId)
                ->update(['packlog' => $logFinal]);
    }
}