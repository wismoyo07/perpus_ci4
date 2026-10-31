<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Denda keterlambatan per buku:
 * - batas maksimal hari denda (Pengaturan),
 * - tanggal laporan buku hilang (denda berhenti di tanggal ini) dan penyelesaian "ditemukan",
 * - tabel fines: satu baris denda per buku, dengan status pembayaran.
 */
class LateFinesPerBook extends Migration
{
    public function up()
    {
        // 1. Batas maksimal hari denda (0 = tanpa batas)
        $this->forge->addColumn('app_settings', [
            'maks_hari_denda' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
                'default'    => 10,
                'after'      => 'kode_barang_buku',
            ],
        ]);

        // 2. Tanggal laporan hilang + penyelesaian "ditemukan"
        $this->forge->addColumn('transaction_details', [
            'lost_reported_at' => [
                'type'  => 'DATE',
                'null'  => true,
                'after' => 'is_lost',
            ],
        ]);
        $this->db->query("ALTER TABLE transaction_details MODIFY COLUMN lost_settlement_status ENUM('pending', 'replaced', 'paid', 'written_off', 'found') DEFAULT NULL");

        // 3. Denda per buku
        $this->forge->addField([
            'id'                    => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'transaction_id'        => ['type' => 'INT', 'constraint' => 11],
            'transaction_detail_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'user_id'               => ['type' => 'INT', 'constraint' => 11],
            'hari_terlambat'        => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'hari_dikenakan'        => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'tarif'                 => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'jumlah_awal'           => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0],
            'jumlah'                => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0],
            'status'                => ['type' => 'ENUM', 'constraint' => ['ditahan', 'belum_lunas', 'lunas', 'dibebaskan'], 'default' => 'belum_lunas'],
            'catatan'               => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'diproses_by'           => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'diproses_at'           => ['type' => 'DATETIME', 'null' => true],
            'created_at'            => ['type' => 'DATETIME', 'null' => true],
            'updated_at'            => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('transaction_detail_id');
        $this->forge->addKey(['user_id', 'status']);
        $this->forge->addForeignKey('transaction_id', 'transactions', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('transaction_detail_id', 'transaction_details', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('diproses_by', 'users', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('fines');

        // 4. Data lama
        // Buku hilang sebelumnya selalu ditandai saat pengembalian
        $this->db->query('UPDATE transaction_details td JOIN transactions t ON t.id = td.transaction_id
                          SET td.lost_reported_at = t.tanggal_kembali
                          WHERE td.is_lost = 1 AND td.lost_reported_at IS NULL');

        // Denda lama (per transaksi) dianggap belum dilunasi
        $now = date('Y-m-d H:i:s');
        foreach ($this->db->table('transactions')->where('total_denda >', 0)->get()->getResultArray() as $t) {
            $this->db->table('fines')->insert([
                'transaction_id' => $t['id'],
                'user_id'        => $t['user_id'],
                'jumlah_awal'    => $t['total_denda'],
                'jumlah'         => $t['total_denda'],
                'status'         => 'belum_lunas',
                'catatan'        => 'Denda lama (dihitung per transaksi)',
                'created_at'     => $now,
                'updated_at'     => $now,
            ]);
        }
    }

    public function down()
    {
        $this->forge->dropTable('fines', true);

        $this->db->query("UPDATE transaction_details SET lost_settlement_status = 'pending' WHERE lost_settlement_status = 'found'");
        $this->db->query("ALTER TABLE transaction_details MODIFY COLUMN lost_settlement_status ENUM('pending', 'replaced', 'paid', 'written_off') DEFAULT NULL");
        $this->forge->dropColumn('transaction_details', 'lost_reported_at');

        $this->forge->dropColumn('app_settings', 'maks_hari_denda');
    }
}
