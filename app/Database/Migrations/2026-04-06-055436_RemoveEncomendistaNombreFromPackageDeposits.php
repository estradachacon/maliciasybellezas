<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveEncomendistaNombreFromPackageDeposits extends Migration
{
    public function up()
    {
        // 🔥 eliminar columna
        $this->forge->dropColumn('package_deposits', 'encomendista_nombre');
    }

    public function down()
    {
        // 🔄 rollback (por si necesitas volver atrás)
        $this->forge->addColumn('package_deposits', [
            'encomendista_nombre' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'id', // opcional, ajusta según tu estructura
            ],
        ]);
    }
}