<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDdcToBooks extends Migration
{
    public function up()
    {
        $this->forge->addColumn('books', [
            // Kelas utama DDC (tabel category)
            'category_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'after'      => 'about',
            ],
            // Nomor DDC rinci, misalnya 005.133
            'ddc_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'after'      => 'category_id',
            ],
        ]);

        $this->db->query('ALTER TABLE books ADD KEY idx_books_category_id (category_id)');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE books DROP INDEX idx_books_category_id');
        $this->forge->dropColumn('books', ['category_id', 'ddc_number']);
    }
}
