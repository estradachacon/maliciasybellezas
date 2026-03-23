<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCodigoToPackages extends Migration
{
    public function up()
    {
        $this->forge->addColumn('paquetes', [
            'codigoqr' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
                'after'      => 'id',
            ],
        ]);

        // 🔥 índice único
        $this->forge->addUniqueKey('codigoqr');
    }

    public function down()
    {
        $this->forge->dropColumn('paquetes', 'codigoqr');
    }
}