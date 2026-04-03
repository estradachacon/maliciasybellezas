<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveUnusedFieldsFromTransactions extends Migration
{
    public function up()
    {
        $this->forge->dropColumn('transactions', [
            'tracking_id',
            'cashier_session_id',
            'referencia',
        ]);
    }

    public function down()
    {
        $this->forge->addColumn('transactions', [
            'tracking_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'cashier_session_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'referencia' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
        ]);
    }
}
