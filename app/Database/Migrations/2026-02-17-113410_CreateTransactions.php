<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTransactions extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'kode_transaksi' => [
                'type' => 'VARCHAR',
                'constraint' => 30,
                'unique' => true,
            ],
            'user_id' => [
                'type' => 'INT',
            ],
            'borrowed_by' => [
                'type' => 'INT',
                'null' => false,
            ],
            'returned_by' => [
                'type' => 'INT',
                'null' => true,
            ],
            'tanggal_pinjam' => [
                'type' => 'DATE',
            ],
            'jatuh_tempo' => [
                'type' => 'DATE',
            ],
            'tanggal_kembali' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'status' => [
                'type' => "ENUM('dipinjam','terlambat','selesai')",
                'default' => 'dipinjam',
            ],
            'total_denda' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'default' => 0,
            ],
            'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
            'updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->addForeignKey('borrowed_by', 'users', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('returned_by', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addKey('status');
        $this->forge->addKey('jatuh_tempo');

        $this->forge->addForeignKey('user_id', 'users', 'id', 'RESTRICT', 'CASCADE');

        $this->forge->createTable('transactions', true);
    }

    public function down()
    {
        $this->forge->dropTable('transactions', true);
    }
}