<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterAvaliacoesCardapioNullable extends Migration
{
    public function up()
    {
        // Tornar cardapio_id nullable
        $this->forge->modifyColumn('avaliacoes_cardapio', [
            'cardapio_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
        ]);
    }

    public function down()
    {
        // Reverter para NOT NULL
        $this->forge->modifyColumn('avaliacoes_cardapio', [
            'cardapio_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
        ]);
    }
}
