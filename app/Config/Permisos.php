<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Permisos extends BaseConfig
{
    public array $modulos = [
        'Ventas locales' => [
            'ver_ventas',
            'crear_venta',
            'ver_clientes',
            'venta_credito'
        ],

        'Ajustes del sistema' => [
            'ver_configuracion',
            'ver_sucursales',
            'ver_almacenamiento',
            'ver_bitacora',
        ],

        'Gestión de usuarios' => [
            'ver_usuarios',
            'crear_usuarios',
            'editar_usuarios',
            'eliminar_usuarios',
            'ver_roles',
            'editar_roles',
            'eliminar_roles',
            'crear_roles',
            'asignar_permisos',
        ],

        'Paquetería' => [
            'crear_paquetes',
            'ver_paquetes',
            'exportar_paquetes_a_excel',
            'ver_asignaciones',
            'depositar_por_codigo',  
            'actualizar_estado_paquete_en_detalle',
            'descargar_de_todos_los_stock',
            'ver_remuneraciones'       
        ],

        'Inventario' => [
            'ver_inventario',
            'crear_producto',
            'editar_producto',
            'ver_promociones_producto',
            'crear_promocion_producto',
            'actualizar_foto_paquete',
            'ver_traslados_entre_sucursales',
            'crear_traslado',
            'anular_traslado',
        ],

        'Compras' => [
            'ver_compras',
            'ingresar_compra',
        ],

        'Proveedores' => [
            'ver_proveedores',
            'crear_proveedor',
        ],

        'Finanzas' => [
            'ver_transacciones',
            'ver_cuentas',
            'crear_cuenta',
            'registrar_gasto',
            'registrar_transferencia',
        ],

        'Encomendistas' => [
            'ver_encomendistas',
            'crear_encomendista',
            'editar_encomendista',
        ],

        /*
        'Finanzas' => [
            'ver_transacciones',
            'ver_cajas',
            'crear_caja',
            'editar_caja',
            'eliminar_caja',
            'hacer_corte',
            'ver_cuentas',
            'crear_cuenta',
            'ver_historicos_de_caja',
            'registrar_gasto',
            'registrar_transferencia',
        ],

        'Paquetería' => [
            'ver_tracking',
            'crear_tracking',
        ],

        'Remuneraciones' => [
            'remunerar_paquetes',
            'devolver_paquetes',
            'remunerar_paquetes_por_cuenta',
        ],

        'Puntos fijos y rutas' => [
            'ver_puntosfijos',
            'crear_puntofijo',
            'editar_puntofijo',
            'eliminar_puntofijo',
            'ver_rutas',
            'crear_ruta',
            'editar_ruta',
            'eliminar_ruta',
            'ver_colonias',
            'ver_casilleros_externos',
            'crear_casilleros_externos',
            'editar_casilleros_externos',
            'eliminar_casilleros_externos',
        ],

        'Solicitudes' => [
            'invalidar_pago',
            'invalidar_flete',
        ],

        'Reportes' => [
            'ver_reportes',
        ],

        'Vendedores' => [
            'ver_vendedores',
            'crear_vendedor',
            'editar_vendedor',
            'eliminar_vendedor',
        ],
        */
    ];
}
