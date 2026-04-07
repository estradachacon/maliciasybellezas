<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePagos extends Migration
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
            'account_id' => [
                'type' => 'INT',
            ],
            'monto' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('venta_id');

        $this->forge->createTable('pagos');
    }

    public function down()
    {
        $this->forge->dropTable('pagos');
    }
}