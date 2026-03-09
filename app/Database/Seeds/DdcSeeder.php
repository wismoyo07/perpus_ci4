<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DdcSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['code_ddc' => '000', 'desc_ddc' => 'Karya Umum, Ilmu Komputer, Informasi'],
            ['code_ddc' => '100', 'desc_ddc' => 'Filsafat & Psikologi'],
            ['code_ddc' => '200', 'desc_ddc' => 'Agama'],
            ['code_ddc' => '300', 'desc_ddc' => 'Ilmu Sosial'],
            ['code_ddc' => '400', 'desc_ddc' => 'Bahasa'],
            ['code_ddc' => '500', 'desc_ddc' => 'Ilmu Pengetahuan Alam / Sains'],
            ['code_ddc' => '600', 'desc_ddc' => 'Teknologi / Ilmu Terapan'],
            ['code_ddc' => '700', 'desc_ddc' => 'Seni & Rekreasi'],
            ['code_ddc' => '800', 'desc_ddc' => 'Kesusastraan'],
            ['code_ddc' => '900', 'desc_ddc' => 'Sejarah & Geografi'],
        ];

        $this->db->table('category')->insertBatch($data);
    }
}