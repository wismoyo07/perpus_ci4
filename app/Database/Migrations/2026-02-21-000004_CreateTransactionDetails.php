<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTransactionDetails extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'transaction_id' => [
                'type' => 'INT',
            ],
            'book_id' => [
                'type' => 'INT',
            ],
            'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['transaction_id', 'book_id'], false, true); // unique
        $this->forge->addKey('book_id');

        $this->forge->addForeignKey('transaction_id', 'transactions', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('book_id', 'books', 'id', 'RESTRICT', 'CASCADE');

        $this->forge->addColumn('transaction_details', [
            'is_lost' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0
            ],
            'lost_settlement_status' => [
                'type' => "ENUM('pending','replaced')",
                'null' => true
            ],
            'lost_note' => [
                'type' => 'TEXT',
                'null' => true
            ],
        ]);

        $this->forge->createTable('transaction_details', true);
    }

    public function down()
    {
        $this->forge->dropTable('transaction_details', true);
    }
}