<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCamposToPaquetes extends Migration
{
    public function up()
    {
        $fields = [

            'tipo_venta' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'detalle',
                'after'      => 'total',
            ],

            'estado1' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'after'      => 'tipo_venta',
            ],

            'estado2' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'after'      => 'estado1',
            ],
        ];

        $this->forge->addColumn('paquetes', $fields);
        $this->forge->addUniqueKey('codigoqr');
    }

    public function down()
    {
        $this->forge->dropColumn('paquetes', ['tipo_venta', 'estado1', 'estado2', 'codigoqr']);
    }
}