<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsers extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 100
            ],
            'member_code' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true
            ],
            'member_code_nation' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true
            ],
            'class' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true
            ],
            'username' => [
                'type' => 'VARCHAR',
                'constraint' => 50
            ],
            'password' => [
                'type' => 'VARCHAR',
                'constraint' => 255
            ],
            'role' => [
                'type' => 'ENUM',
                'constraint' => ['admin','staff','member'],
                'default' => 'member'
            ],
            'jenis_pengguna' => [
                'type' => 'ENUM',
                'constraint' => ['siswa','guru','pegawai'],
                'default' => 'siswa'
            ],
            'is_validated' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0
            ],
            'is_archived' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0
            ],
            'must_change_password' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0
            ],
            'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
            'updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
            'last_login' => [
                'type' => 'DATETIME',
                'null' => true
            ]
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('username');
        $this->forge->addUniqueKey('member_code');
        $this->forge->addUniqueKey('member_code_nation');

        $this->forge->addKey('role');
        $this->forge->addKey('is_validated');
        $this->forge->addKey('jenis_pengguna');

        $this->forge->createTable('users');
    }

    public function down()
    {
        $this->forge->dropTable('users');
    }
}