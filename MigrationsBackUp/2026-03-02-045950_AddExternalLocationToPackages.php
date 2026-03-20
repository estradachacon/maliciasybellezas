<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddExternalLocationToPackages extends Migration
{
    public function up()
    {
        $this->forge->addColumn('packages', [
            'external_location_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('packages', 'external_location_id');
    }
}