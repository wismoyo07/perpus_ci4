<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPhase2Columns extends Migration
{
    public function up()
    {
        // 1. Truncate transactions and books data to start fresh (requested by user)
        $this->db->query("SET FOREIGN_KEY_CHECKS=0");
        $this->db->table('transaction_details')->truncate();
        $this->db->table('transactions')->truncate();
        $this->db->table('book_items')->truncate();
        $this->db->table('books')->truncate();
        $this->db->query("SET FOREIGN_KEY_CHECKS=1");

        // 2. Add jenis_peminjaman to transactions table
        $this->forge->addColumn('transactions', [
            'jenis_peminjaman' => [
                'type'       => 'ENUM',
                'constraint' => ['pendek', 'panjang'],
                'default'    => 'pendek',
                'after'      => 'kode_transaksi'
            ]
        ]);

        // 3. Add book_item_id to transaction_details table
        $this->forge->addColumn('transaction_details', [
            'book_item_id' => [
                'type'       => 'INT',
                'null'       => true,
                'after'      => 'book_id'
            ]
        ]);

        // 4. Add Foreign Key for book_item_id in transaction_details
        $this->db->query("ALTER TABLE transaction_details ADD CONSTRAINT fk_transaction_details_book_item FOREIGN KEY (book_item_id) REFERENCES book_items(id) ON DELETE RESTRICT ON UPDATE CASCADE");
    }

    public function down()
    {
        // Drop foreign key
        try {
            $this->db->query("ALTER TABLE transaction_details DROP FOREIGN KEY fk_transaction_details_book_item");
        } catch (\Exception $e) {
            // Ignore if key doesn't exist
        }

        // Drop columns
        $this->forge->dropColumn('transaction_details', 'book_item_id');
        $this->forge->dropColumn('transactions', 'jenis_peminjaman');
    }
}
