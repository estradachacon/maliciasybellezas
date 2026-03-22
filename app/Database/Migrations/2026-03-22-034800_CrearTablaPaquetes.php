<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CrearTablaPaquetes extends Migration
{
public function up()
{
    $this->forge->addField([
        'id' => ['type' => 'INT', 'auto_increment' => true],
        'cliente_nombre' => ['type' => 'VARCHAR', 'constraint' => 150],
        'cliente_telefono' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
        'dia_entrega' => ['type' => 'DATE', 'null' => true],
        'hora_inicio' => ['type' => 'TIME', 'null' => true],
        'hora_fin' => ['type' => 'TIME', 'null' => true],
        'destino' => ['type' => 'TEXT', 'null' => true],
        'encomendista_nombre' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
        'precio' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0],
        'envio' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0],
        'total' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0],
        'foto' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
        'created_at' => ['type' => 'DATETIME', 'null' => true],
        'updated_at' => ['type' => 'DATETIME', 'null' => true],
    ]);

    $this->forge->addKey('id', true);
    $this->forge->createTable('paquetes');
}

    public function down()
    {
        $this->forge->dropTable('paquetes');        
    }
}
