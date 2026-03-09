<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateActivityLogs extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'user_id' => [
                'type' => 'INT',
                'null' => true,
            ],
            'aktivitas' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'SET NULL', 'CASCADE');

        $this->forge->createTable('activity_logs', true);
    }

    public function down()
    {
        $this->forge->dropTable('activity_logs', true);
    }
}