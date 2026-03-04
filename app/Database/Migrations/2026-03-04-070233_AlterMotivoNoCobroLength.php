<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterMotivoNoCobroLength extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn('packages', [

            'motivo_no_cobro' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],

        ]);
    }

    public function down()
    {
        $this->forge->modifyColumn('packages', [

            'motivo_no_cobro' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],

        ]);
    }
}
