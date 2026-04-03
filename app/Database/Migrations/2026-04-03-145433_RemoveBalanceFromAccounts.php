<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveBalanceFromAccounts extends Migration
{
    public function up()
    {
        $this->forge->dropColumn('accounts', 'balance');
    }

    public function down()
    {
        $this->forge->addColumn('accounts', [
            'balance' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0,
                'null'       => false,
            ],
        ]);
    }
}
