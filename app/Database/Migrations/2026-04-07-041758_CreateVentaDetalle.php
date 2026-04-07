<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateVentaDetalle extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'auto_increment' => true,
            ],
            'venta_id' => [
                'type' => 'INT',
            ],
            'producto_id' => [
                'type' => 'INT',
            ],
            'cantidad' => [
                'type' => 'INT',
            ],
            'precio_unitario' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
            ],
            'total' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('venta_id');

        $this->forge->createTable('venta_detalle');
    }

    public function down()
    {
        $this->forge->dropTable('venta_detalle');
    }
}