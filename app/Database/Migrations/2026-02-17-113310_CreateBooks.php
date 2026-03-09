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
                'auto_increment' => true,
            ],
            'kode_buku' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'unique' => true,
                'null' => true,
            ],
            'judul' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
            ],
            'pengarang' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'penerbit' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'stok_total' => [
                'type' => 'INT',
                'default' => 0,
            ],
            'stok_tersedia' => [
                'type' => 'INT',
                'default' => 0,
            ],
            'is_active' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
            ],
            'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
            'updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('is_active');

        $this->forge->createTable('books', true);

        $this->db->query("
            ALTER TABLE books
            ADD CONSTRAINT chk_stok
            CHECK (
                stok_total >= 0 AND
                stok_tersedia >= 0 AND
                stok_tersedia <= stok_total
            )
        ");
    }

    public function down()
    {
        $this->forge->dropTable('books', true);
    }
}