<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SourceFundSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['source_name' => 'BOS'],
            ['source_name' => 'APBD'],
            ['source_name' => 'APBN'],
            ['source_name' => 'Hibah / Donasi'],
            ['source_name' => 'Pembelian Pribadi'],
        ];

        $this->db->table('source_fund')->insertBatch($data);
    }
}