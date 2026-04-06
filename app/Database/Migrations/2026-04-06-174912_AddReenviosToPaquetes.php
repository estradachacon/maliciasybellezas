<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddReenviosToPaquetes extends Migration
{
    public function up()
    {
        $this->forge->addColumn('paquetes', [
            'reenvios' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
                'null'       => false,
                'after'      => 'estado2' // puedes cambiar la posición si quieres
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('paquetes', 'reenvios');
    }
}