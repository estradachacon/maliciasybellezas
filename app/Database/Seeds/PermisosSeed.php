<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PermisosSeed extends Seeder
{
    public function run()
    {
        // ID DEL ROL ADMINISTRADOR
        $adminRoleId = 1;

        $permisos = [
            // gestion de usuarios
            'ver_usuarios',
            'crear_usuarios',
            'editar_usuarios',
            'eliminar_usuarios',
            'ver_roles',
            'editar_roles',
            'eliminar_roles',
            'crear_roles',
            'asignar_permisos',
            
            // configuraciones
            'ver_configuracion',
            'ver_sucursales',
            'ver_almacenamiento',
            'ver_bitacora',
            'ajustes_multimedia',
        ];

        foreach ($permisos as $accion) {

            $exists = $this->db->table('permisos_rol')
                ->where('role_id', $adminRoleId)
                ->where('nombre_accion', $accion)
                ->get()
                ->getRow();

            if (!$exists) {
                $this->db->table('permisos_rol')->insert([
                    'role_id'       => $adminRoleId,
                    'nombre_accion' => $accion,
                    'habilitado'    => 1,
                ]);
            }
        }
    }
}
