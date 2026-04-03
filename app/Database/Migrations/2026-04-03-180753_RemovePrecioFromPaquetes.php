<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemovePrecioFromPaquetes extends Migration
{
    public function up()
    {
        $this->forge->dropColumn('paquetes', 'precio');
    }

    public function down()
    {
        $this->forge->addColumn('paquetes', [
            'precio' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0,
                'after' => 'encomendista_nombre'
            ]
        ]);
    }
}