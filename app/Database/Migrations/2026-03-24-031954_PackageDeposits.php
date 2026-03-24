<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePackageDeposits extends Migration
{
    public function up()
    {
        $this->forge->addField([

            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],

            'encomendista_nombre' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
            ],

            'flete_total' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00,
            ],

            'cantidad_paquetes' => [
                'type' => 'INT',
                'default' => 0,
            ],

            'fecha' => [
                'type' => 'DATETIME',
            ],

            'usuario_id' => [
                'type' => 'INT',
                'unsigned' => true,
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
        $this->forge->addKey('usuario_id');

        $this->forge->createTable('package_deposits');
    }

    public function down()
    {
        $this->forge->dropTable('package_deposits');
    }
}