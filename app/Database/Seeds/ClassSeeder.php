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

        $data = [];

        foreach ($classes as $class) {
            $data[] = [
                'class_name' => $class
            ];
        }

        $this->db->table('class')->insertBatch($data);
    }
}