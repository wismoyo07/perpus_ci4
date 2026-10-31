<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Kwitansi pembayaran denda: satu pembayaran (kwitansi) bisa melunasi beberapa denda
 * milik anggota yang sama. Nomor kwitansi berurutan per tahun (KW/2026/0001).
 * Denda yang dilunasi sebelum migrasi ini tidak memiliki kwitansi.
 */
class CreateFinePayments extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'nomor_kwitansi' => ['type' => 'VARCHAR', 'constraint' => 30],
            'tahun'          => ['type' => 'SMALLINT', 'constraint' => 4],
            'urutan'         => ['type' => 'INT', 'constraint' => 11],
            'user_id'        => ['type' => 'INT', 'constraint' => 11],
            'total'          => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0],
            'dibayar_at'     => ['type' => 'DATETIME'],
            'diterima_by'    => ['type' => 'INT', 'constraint' => 11],
            'catatan'        => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'jumlah_cetak'   => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('nomor_kwitansi');
        $this->forge->addUniqueKey(['tahun', 'urutan']);
        $this->forge->addKey('user_id');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('diterima_by', 'users', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('fine_payments');

        $this->forge->addColumn('fines', [
            'payment_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'after'      => 'diproses_at',
            ],
        ]);
        $this->db->query('ALTER TABLE fines ADD CONSTRAINT fines_payment_id_foreign FOREIGN KEY (payment_id) REFERENCES fine_payments (id) ON DELETE RESTRICT ON UPDATE CASCADE');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE fines DROP FOREIGN KEY fines_payment_id_foreign');
        $this->forge->dropColumn('fines', 'payment_id');
        $this->forge->dropTable('fine_payments');
    }
}
