<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Status akreditasi sebelumnya dibatasi 10 karakter, sehingga nilai seperti
 * "Baik Sekali" atau "Terakreditasi A" ditolak. Diperlebar menjadi 50 karakter.
 */
class WidenStatusAkreditasi extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn('app_settings', [
            'status_akreditasi' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
        ]);
    }

    public function down()
    {
        // Nilai lebih dari 10 karakter dipotong agar kolom bisa dikembalikan
        $this->db->query('UPDATE app_settings SET status_akreditasi = LEFT(status_akreditasi, 10)');
        $this->forge->modifyColumn('app_settings', [
            'status_akreditasi' => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => true],
        ]);
    }
}
