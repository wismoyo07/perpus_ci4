<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Pengembalian per buku: setiap buku dalam satu transaksi punya tanggal kembali sendiri.
 * Transaksi baru "selesai" setelah semua bukunya dikembalikan atau dinyatakan hilang.
 */
class AddPartialReturnToTransactionDetails extends Migration
{
    public function up()
    {
        $this->forge->addColumn('transaction_details', [
            'returned_at' => [
                'type'  => 'DATE',
                'null'  => true,
                'after' => 'book_item_id',
            ],
            'returned_by' => [
                'type'  => 'INT',
                'null'  => true,
                'after' => 'returned_at',
            ],
        ]);

        $this->db->query('ALTER TABLE transaction_details ADD CONSTRAINT fk_transaction_details_returned_by FOREIGN KEY (returned_by) REFERENCES users(id) ON DELETE RESTRICT ON UPDATE CASCADE');

        // Data lama: buku (yang tidak hilang) pada transaksi selesai dikembalikan bersamaan
        $this->db->query("UPDATE transaction_details td JOIN transactions t ON t.id = td.transaction_id
                          SET td.returned_at = t.tanggal_kembali, td.returned_by = t.returned_by
                          WHERE t.status = 'selesai' AND td.is_lost = 0 AND td.returned_at IS NULL");
    }

    public function down()
    {
        $this->db->query('ALTER TABLE transaction_details DROP FOREIGN KEY fk_transaction_details_returned_by');
        $this->forge->dropColumn('transaction_details', ['returned_at', 'returned_by']);
    }
}
