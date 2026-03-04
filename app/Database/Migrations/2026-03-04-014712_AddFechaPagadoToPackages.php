<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFechaPagadoToPackages extends Migration
{
    public function up()
    {
        $this->forge->addColumn('packages', [

            'cliente_pago_directo' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'null'       => false,
                'after'      => 'nocobrar_pack_cancelado',
            ],

            'fecha_cliente_pago' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'cliente_pago_directo',
            ],

            'motivo_no_cobro' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'fecha_cliente_pago',
            ],

        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('packages', [
            'cliente_pago_directo',
            'fecha_cliente_pago',
            'motivo_no_cobro'
        ]);
    }
}