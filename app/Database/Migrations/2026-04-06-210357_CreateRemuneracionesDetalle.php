<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRemuneracionesDetalle extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'auto_increment' => true,
            ],
            'remuneracion_id' => [
                'type' => 'INT',
            ],
            'paquete_id' => [
                'type' => 'INT',
            ],
            'encomendista_id' => [
                'type' => 'INT',
                'null' => true,
            ],
            'monto' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'created_at DATETIME DEFAULT CURRENT_TIMESTAMP',
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('remuneracion_id');
        $this->forge->addKey('paquete_id');

        $this->forge->createTable('remuneraciones_detalle');
    }

    public function down()
    {
        $this->forge->dropTable('remuneraciones_detalle');
    }
}