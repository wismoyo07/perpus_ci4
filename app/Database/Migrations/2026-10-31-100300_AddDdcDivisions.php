<?php

namespace App\Database\Migrations;

use App\Libraries\DdcData;
use CodeIgniter\Database\Migration;

/**
 * Tambah rincian divisi DDC (010-990) dan seksi 297 Islam ke tabel category.
 * Hanya menambah kode yang belum ada; nama kelas utama yang sudah ada tidak diubah.
 */
class AddDdcDivisions extends Migration
{
    public function up()
    {
        foreach (DdcData::DIVISIONS as $code => $desc) {
            $code   = (string) $code; // key seperti '110' menjadi int di PHP
            $exists = $this->db->table('category')->where('code_ddc', $code)->countAllResults();
            if (!$exists) {
                $this->db->table('category')->insert(['code_ddc' => $code, 'desc_ddc' => $desc, 'is_active' => 1]);
            }
        }
    }

    public function down()
    {
        // Hapus hanya baris dari migrasi ini (nama sama) yang belum dipakai buku
        foreach (DdcData::DIVISIONS as $code => $desc) {
            $row = $this->db->table('category')->where('code_ddc', (string) $code)->where('desc_ddc', $desc)->get()->getRowArray();
            if (!$row) {
                continue;
            }

            $used = $this->db->table('books')->where('category_id', $row['id'])->countAllResults();
            if (!$used) {
                $this->db->table('category')->where('id', $row['id'])->delete();
            }
        }
    }
}
