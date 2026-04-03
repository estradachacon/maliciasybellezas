<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateComprasHead extends Migration
{
public function up()
{
    $this->forge->addField([
        'id' => [
            'type' => 'INT',
            'auto_increment' => true
        ],
        'proveedor_id' => [
            'type' => 'INT',
            'unsigned' => true
        ],
        'branch_id' => [
            'type' => 'INT',
            'unsigned' => true
        ],
        'total' => [
            'type' => 'DECIMAL',
            'constraint' => '10,2'
        ],
        'observacion' => [
            'type' => 'TEXT',
            'null' => true
        ],
        'usuario_id' => [
            'type' => 'INT',
            'unsigned' => true
        ],
        'fecha_compra' => [
            'type' => 'DATE',
            'null' => true
        ],
        'created_at' => [
            'type' => 'DATETIME',
            'null' => true
        ],
        'updated_at' => [
            'type' => 'DATETIME',
            'null' => true
        ]
    ]);

    $this->forge->addKey('id', true);
    $this->forge->createTable('compras');
}

public function down()
{
    $this->forge->dropTable('compras');
}
}
