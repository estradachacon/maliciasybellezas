<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProductoPrecios extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'producto_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'cantidad_minima' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'precio' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);

        // 🔥 índice para consultas rápidas
        $this->forge->addKey('producto_id');

        $this->forge->createTable('producto_precios');
    }

    public function down()
    {
        $this->forge->dropTable('producto_precios');
    }
}