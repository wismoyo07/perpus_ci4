<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAppSettings extends Migration
{
    public function up()
    {
        $this->forge->addField([

            'id' => [
                'type' => 'INT',
                'auto_increment' => true
            ],

            'logo' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true
            ],

            'kode_lembaga' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true
            ],

            'nama_lembaga' => [
                'type' => 'VARCHAR',
                'constraint' => 200
            ],

            'status_akreditasi' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => true
            ],

            'alamat' => [
                'type' => 'TEXT',
                'null' => true
            ],

            'kepala_sekolah' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => true
            ],

            'wakil_kepala_sekolah' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => true
            ],

            'website_url' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true
            ],

            'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
            'updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'

        ]);

        $this->forge->addKey('id', true);

        $this->forge->createTable('app_settings');
    }

    public function down()
    {
        $this->forge->dropTable('app_settings');
    }
}