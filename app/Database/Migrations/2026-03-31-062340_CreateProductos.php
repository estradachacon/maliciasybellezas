<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProductos extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true
            ],

            'nombre' => [
                'type' => 'VARCHAR',
                'constraint' => 150
            ],

            'proveedor' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => true
            ],

            'costo_inicial' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0
            ],

            'precio_venta' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0
            ],

            'estado' => [
                'type' => 'TINYINT',
                'default' => 1
            ],

            'created_at' => [
                'type' => 'DATETIME',
                'null' => true
            ],

            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true
            ],
        ]);

        $this->forge->addKey('id', true);

        // 🔥 índice útil (búsqueda rápida por nombre)
        $this->forge->addKey('nombre');

        $this->forge->createTable('productos');
    }

    public function down()
    {
        $this->forge->dropTable('productos');
    }
}