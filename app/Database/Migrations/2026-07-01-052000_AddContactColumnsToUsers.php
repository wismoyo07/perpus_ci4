<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddContactColumnsToUsers extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'phone' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'after'      => 'class'
            ],
            'whatsapp' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'after'      => 'phone'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'phone');
        $this->forge->dropColumn('users', 'whatsapp');
    }
}
