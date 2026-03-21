<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCodigoToUsers extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'codigo' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'user_name',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'codigo');
    }
}
