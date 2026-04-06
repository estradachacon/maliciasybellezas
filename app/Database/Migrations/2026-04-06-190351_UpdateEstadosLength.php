<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateEstadosLength extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn('paquetes', [
            'estado1' => [
                'type' => 'VARCHAR',
                'constraint' => 30,
                'null' => true,
            ],
            'estado2' => [
                'type' => 'VARCHAR',
                'constraint' => 30,
                'null' => true,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->modifyColumn('paquetes', [
            'estado1' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'estado2' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
        ]);
    }
}