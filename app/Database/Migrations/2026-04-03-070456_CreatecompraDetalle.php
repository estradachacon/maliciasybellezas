<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatecompraDetalle extends Migration
{
public function up()
{
    $this->forge->addField([
        'id' => [
            'type' => 'INT',
            'auto_increment' => true
        ],
        'compra_id' => [
            'type' => 'INT',
            'unsigned' => true
        ],
        'producto_id' => [
            'type' => 'INT',
            'unsigned' => true
        ],
        'cantidad' => [
            'type' => 'INT'
        ],
        'precio_unitario' => [
            'type' => 'DECIMAL',
            'constraint' => '10,2'
        ],
        'total' => [
            'type' => 'DECIMAL',
            'constraint' => '10,2'
        ]
    ]);

    $this->forge->addKey('id', true);
    $this->forge->createTable('compra_detalle');
}

public function down()
{
    $this->forge->dropTable('compra_detalle');
}
}
