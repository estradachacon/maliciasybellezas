<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DropFleteFromPaquetes extends Migration
{
    public function up()
    {
        // 🔥 Eliminar columna
        $this->forge->dropColumn('paquetes', 'envio');
    }

    public function down()
    {
        // 🔁 Volver a crearla por si haces rollback
        $this->forge->addColumn('paquetes', [
            'envio' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
                'default' => 0
            ]
        ]);
    }
}