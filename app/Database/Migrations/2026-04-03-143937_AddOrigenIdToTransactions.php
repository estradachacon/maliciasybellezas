<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddOrigenIdToTransactions extends Migration
{
    public function up()
    {
        $this->forge->addColumn('transactions', [
            'origen_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'after'      => 'origen',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('transactions', 'origen_id');
    }
}
