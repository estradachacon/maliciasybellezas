<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCodigoBarrasToProductos extends Migration
{
    public function up()
    {
        $this->forge->addColumn('productos', [
            'codigo_barras' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'unique'     => true,
                'after'      => 'nombre',
            ],
            'tipo_codigo' => [
                'type'       => 'ENUM',
                'constraint' => ['EAN13', 'CODE128', 'CUSTOM'],
                'default'    => 'CODE128',
                'after'      => 'codigo_barras',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('productos', ['codigo_barras', 'tipo_codigo']);
    }
}