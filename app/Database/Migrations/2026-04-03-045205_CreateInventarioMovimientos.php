<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateInventarioMovimientos extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],

            'producto_id' => ['type' => 'INT', 'unsigned' => true],
            'branch_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],

            'tipo' => [
                'type' => 'ENUM',
                'constraint' => ['entrada', 'salida', 'ajuste']
            ],
            'origen' => [
                'type' => 'VARCHAR',
                'constraint' => 50
            ],

            'origen_id' => [
                'type' => 'INT',
                'null' => true
            ],

            'cantidad' => ['type' => 'INT'],
            'usuario_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],

            'created_at' => ['type' => 'DATETIME'],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['producto_id', 'branch_id']);

        $this->forge->createTable('inventario_historico');
    }

    public function down()
    {
        $this->forge->dropTable('inventario_historico');
    }
}