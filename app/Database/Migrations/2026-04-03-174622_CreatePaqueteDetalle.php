<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePaqueteDetalle extends Migration
{
    public function up()
    {
        $this->forge->addField([

            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true
            ],

            'paquete_id' => [
                'type' => 'INT',
                'unsigned' => true,
            ],

            'producto_id' => [
                'type' => 'INT',
                'unsigned' => true,
            ],

            'cantidad' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0
            ],

            'precio' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0
            ],

            'descuento' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0
            ],

            'subtotal' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0
            ],

            'created_at DATETIME DEFAULT CURRENT_TIMESTAMP',
            'updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
        ]);

        $this->forge->addKey('id', true);

        $this->forge->createTable('paquete_detalle');
    }

    public function down()
    {
        $this->forge->dropTable('paquete_detalle');
    }
}
