<?php

namespace App\Database\Seeds;

use App\Libraries\DdcData;
use CodeIgniter\Database\Seeder;

class DdcSeeder extends Seeder
{
    public function run()
    {
        // Kelas utama + divisi/seksi (lihat App\Libraries\DdcData)
        $data = DdcData::CLASSES + DdcData::DIVISIONS;
        ksort($data, SORT_STRING);

        // Lewati yang sudah ada agar aman dijalankan ulang (tidak membuat data ganda)
        foreach ($data as $code => $desc) {
            $code = (string) $code; // key seperti '110' menjadi int di PHP
            if (!$this->db->table('category')->where('code_ddc', $code)->countAllResults()) {
                $this->db->table('category')->insert(['code_ddc' => $code, 'desc_ddc' => $desc]);
            }
        }
    }
}
