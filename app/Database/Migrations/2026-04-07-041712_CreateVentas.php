<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateVentas extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'auto_increment' => true,
            ],
            'fecha' => [
                'type' => 'DATE',
            ],
            'cliente_id' => [
                'type' => 'INT',
                'null' => true,
            ],
            'branch_id' => [
                'type' => 'INT',
            ],
            'tipo_venta' => [
                'type'       => 'ENUM',
                'constraint' => ['contado', 'credito'],
                'default'    => 'contado',
            ],
            'total' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0,
            ],
            'total_pagado' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0,
            ],
            'saldo' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0,
            ],
            'estado' => [
                'type'       => 'ENUM',
                'constraint' => ['pendiente', 'parcial', 'pagado'],
                'default'    => 'pendiente',
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
        $this->forge->createTable('ventas');
    }

    public function down()
    {
        $this->forge->dropTable('ventas');
    }
}