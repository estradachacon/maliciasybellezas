<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPacklogToPackages extends Migration
{
    public function up()
    {
        $this->forge->addColumn('paquetes', [
            'packlog' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'total' 
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('paquetes', 'packlog');
    }
}
