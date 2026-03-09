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
            'username' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'unique' => true
            ],
            'password' => [
                'type' => 'VARCHAR',
                'constraint' => 255
            ],
            'role' => [
                'type' => "ENUM('admin','staff','member')",
                'default' => 'member'
            ],
            'jenis_pengguna' => [
                'type' => "ENUM('siswa','guru','pegawai')",
                'default' => 'siswa'
            ],
            'nomor_induk' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'unique' => true
            ],
            'is_validated' => [
                'type' => 'TINYINT',
                'default' => 0
            ],
            'is_archived' => [
                'type' => 'TINYINT',
                'default' => 0
            ],
            'must_change_password' => [
                'type' => 'TINYINT',
                'default' => 0
            ],
            'created_at DATETIME DEFAULT CURRENT_TIMESTAMP',
            'updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('role');
        $this->forge->addKey('jenis_pengguna');
        $this->forge->addKey('is_validated');

        $this->forge->createTable('users', true);

        // CHECK constraint (MySQL 8)
        $this->db->query("
            ALTER TABLE users
            ADD CONSTRAINT chk_staff_role
            CHECK (
                (role != 'staff')
                OR
                (jenis_pengguna IN ('guru','pegawai'))
            )
        ");
    }

    public function down()
    {
        $this->forge->dropTable('users', true);
    }
}