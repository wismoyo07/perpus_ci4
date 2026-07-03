<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateLostSettlementColumns extends Migration
{
    public function up()
    {
        // 1. Ubah ENUM lost_settlement_status
        $this->db->query("ALTER TABLE transaction_details MODIFY COLUMN lost_settlement_status ENUM('pending', 'replaced', 'paid', 'written_off') DEFAULT NULL");

        // 2. Tambah kolom baru ke transaction_details
        $this->forge->addColumn('transaction_details', [
            'lost_settlement_amount' => [
                'type'       => 'INT',
                'null'       => true,
                'after'      => 'lost_note'
            ],
            'settled_by' => [
                'type'       => 'INT',
                'null'       => true,
                'after'      => 'lost_settlement_amount'
            ],
            'settled_at' => [
                'type'       => 'DATETIME',
                'null'       => true,
                'after'      => 'settled_by'
            ]
        ]);

        // 3. Tambah Foreign Key untuk settled_by ke users.id
        $this->db->query("ALTER TABLE transaction_details ADD CONSTRAINT fk_transaction_details_settled_by FOREIGN KEY (settled_by) REFERENCES users(id) ON DELETE RESTRICT ON UPDATE CASCADE");
    }

    public function down()
    {
        // Drop foreign key
        try {
            $this->db->query("ALTER TABLE transaction_details DROP FOREIGN KEY fk_transaction_details_settled_by");
        } catch (\Exception $e) {
            // Abaikan jika key tidak ditemukan
        }

        // Drop kolom-kolom baru
        $this->forge->dropColumn('transaction_details', 'settled_at');
        $this->forge->dropColumn('transaction_details', 'settled_by');
        $this->forge->dropColumn('transaction_details', 'lost_settlement_amount');

        // Kembalikan ENUM ke semula
        $this->db->query("ALTER TABLE transaction_details MODIFY COLUMN lost_settlement_status ENUM('pending', 'replaced') DEFAULT NULL");
    }
}
