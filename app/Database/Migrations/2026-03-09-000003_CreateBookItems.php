<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBookItems extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true
            ],
            'book_id' => [
                'type' => 'INT'
            ],
            'kode_inventaris' => [
                'type' => 'VARCHAR',
                'constraint' => 100
            ],
            'sumber_dana' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true
            ],
            'tahun_pengadaan' => [
                'type' => 'YEAR',
                'null' => true
            ],
            'nomor_urut' => [
                'type' => 'INT',
                'null' => true
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['available','borrowed','lost','damaged'],
                'default' => 'available'
            ],
            'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
            'updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('kode_inventaris');
        $this->forge->addKey('book_id');

        $this->forge->addForeignKey('book_id','books','id','CASCADE','CASCADE');

        $this->forge->createTable('book_items');
    }

    public function down()
    {
        $this->forge->dropTable('book_items');
    }
}