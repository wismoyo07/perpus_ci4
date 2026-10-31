<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Hapus data kelas ganda (ClassSeeder dulu memakai insertBatch tanpa cek, sehingga
 * dijalankan berulang membuat tiap kelas tercatat lebih dari sekali) dan kunci keunikannya.
 * users.class menyimpan nama kelas (bukan id), jadi tidak ada referensi yang terputus.
 */
class DedupeClassTable extends Migration
{
    public function up()
    {
        // Simpan id terkecil per nama kelas
        $this->db->query('DELETE c FROM class c JOIN class k ON c.class_name = k.class_name AND c.id > k.id');
        $this->db->query('ALTER TABLE class ADD UNIQUE KEY uq_class_class_name (class_name)');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE class DROP INDEX uq_class_class_name');
        // Catatan: data ganda yang dihapus di up() tidak dikembalikan.
    }
}
