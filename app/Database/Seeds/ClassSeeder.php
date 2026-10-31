<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ClassSeeder extends Seeder
{
    public function run()
    {
        $classes = [
            'VII-A','VII-B','VII-C','VII-D','VII-E',
            'VIII-A','VIII-B','VIII-C','VIII-D','VIII-E',
            'IX-A','IX-B','IX-C','IX-D','IX-E'
        ];

        // Lewati yang sudah ada agar aman dijalankan ulang (tidak membuat data ganda)
        foreach ($classes as $class) {
            if (!$this->db->table('class')->where('class_name', $class)->countAllResults()) {
                $this->db->table('class')->insert(['class_name' => $class]);
            }
        }
    }
}