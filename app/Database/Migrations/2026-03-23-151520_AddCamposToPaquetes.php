<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCamposToPaquetes extends Migration
{
    public function up()
    {
        $fields = [

            // 🔹 Tipo de venta (detalle / mayoreo / etc)
            'tipo_venta' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'detalle',
                'after'      => 'total',
            ],

            // 🔹 Estado 1 (ej: pagado, pendiente, etc)
            'estado1' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'after'      => 'tipo_venta',
            ],

            // 🔹 Estado 2 (ej: entregado, en ruta, etc)
            'estado2' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'after'      => 'estado1',
            ],
        ];

        $this->forge->addColumn('paquetes', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('paquetes', ['tipo_venta', 'estado1', 'estado2']);
    }
}