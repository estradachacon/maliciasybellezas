<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePackageDepositDetails extends Migration
{
    public function up()
    {
        $this->forge->addField([

            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],

            'deposit_id' => [
                'type' => 'INT',
                'unsigned' => true,
            ],

            'package_id' => [
                'type' => 'INT',
                'unsigned' => true,
            ],

            'codigo_qr' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],

            'valor_paquete' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00,
            ],

            'porcentaje' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'comment' => '% respecto al total',
            ],

            'flete_asignado' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00,
            ],

            'nuevo_estado' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'comment' => 'en_transito / en_casillero',
            ],

            'foto' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],

            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],

        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('deposit_id');
        $this->forge->addKey('package_id');

        $this->forge->createTable('package_deposit_details');
    }

    public function down()
    {
        $this->forge->dropTable('package_deposit_details');
    }
}