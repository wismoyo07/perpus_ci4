<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKodeBarangToAppSettings extends Migration
{
    public function up()
    {
        // Kode barang aset untuk buku (awal kode buku), sebelumnya di-hardcode di BookService
        $this->forge->addColumn('app_settings', [
            'kode_barang_buku' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => false,
                'default'    => '03.11.01.01.001',
                'after'      => 'kode_lembaga',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('app_settings', 'kode_barang_buku');
    }
}
