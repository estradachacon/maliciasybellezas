<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateInventario extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true
            ],
            'producto_id' => [
                'type' => 'INT',
                'unsigned' => true
            ],
            'sucursal_id' => [
                'type' => 'INT',
                'unsigned' => true
            ],
            'stock' => [
                'type' => 'INT',
                'default' => 0
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);

        // 🔗 índices importantes
        $this->forge->addKey(['producto_id', 'sucursal_id'], false, true);

        $this->forge->createTable('inventario');
    }

    public function down()
    {
        $this->forge->dropTable('inventario');
    }
}