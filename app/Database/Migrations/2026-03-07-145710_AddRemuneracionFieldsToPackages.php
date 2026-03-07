<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRemuneracionFieldsToPackages extends Migration
{
    public function up()
    {
        $fields = [

            'fecha_remu' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'estatus2'
            ],

            'metodo_remu' => [
                'type' => 'VARCHAR',
                'constraint' => 30,
                'null' => true,
                'after' => 'fecha_remu'
            ],

            'remu_user_id' => [
                'type' => 'INT',
                'null' => true,
                'after' => 'metodo_remu'
            ],

        ];

        $this->forge->addColumn('packages', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('packages', [
            'fecha_remu',
            'metodo_remu',
            'remu_user_id'
        ]);
    }
}
