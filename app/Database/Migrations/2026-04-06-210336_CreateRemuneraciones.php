<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRemuneraciones extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'auto_increment' => true,
            ],
            'fecha' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'total' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0
            ],
            'cuenta' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'usuario_id' => [
                'type' => 'INT',
                'null' => true,
            ],
            'observaciones' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at DATETIME DEFAULT CURRENT_TIMESTAMP',
            'updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('remuneraciones');
    }

    public function down()
    {
        $this->forge->dropTable('remuneraciones');
    }
}