<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SourceFundSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['kode' => 'BOS', 'source_name' => 'BOS'],
            ['kode' => 'APBD', 'source_name' => 'APBD'],
            ['kode' => 'APBN', 'source_name' => 'APBN'],
            ['kode' => 'HIBAH', 'source_name' => 'Hibah / Donasi'],
            ['kode' => 'PRIBADI', 'source_name' => 'Pembelian Pribadi'],
        ];

        // Lewati yang sudah ada agar aman dijalankan ulang (tidak membuat data ganda)
        foreach ($data as $row) {
            if (!$this->db->table('source_fund')->where('kode', $row['kode'])->countAllResults()) {
                $this->db->table('source_fund')->insert($row);
            }
        }
    }
}