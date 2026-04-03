<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCamposPaquetes extends Migration
{
    public function up()
    {
        $this->forge->addColumn('paquetes', [
            'envio' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0,
            ],

            'descuento_global' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0,
            ],

            'total_real' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0,
            ],

            'vendedor_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],

        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('paquetes', ['descuento_global', 'envio', 'total_real', 'vendedor_id']);
    }
}
