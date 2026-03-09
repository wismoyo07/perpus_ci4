<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBooks extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true
            ],
            'judul' => [
                'type' => 'VARCHAR',
                'constraint' => 150
            ],
            'pengarang' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true
            ],
            'penerbit' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true
            ],
            'about' => [
                'type' => 'TEXT',
                'null' => true
            ],
            'is_active' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1
            ],
            'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
            'updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('is_active');

        $this->forge->createTable('books');
    }

    public function down()
    {
        $this->forge->dropTable('books');
    }
}