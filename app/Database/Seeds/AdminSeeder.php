<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run()
    {
        helper('password');
        
        $db = \Config\Database::connect();

        $existing = $db->table('users')
            ->where('username', 'admin')
            ->get()
            ->getRow();

        if ($existing) {
            echo "Admin sudah ada.\n";
            return;
        }

        $db->table('users')->insert([
            'name'      => 'Super Administrator',
            'username'  => 'admin',
            // 'password'  => password_hash('admin123', PASSWORD_BCRYPT), hash password default
            'password'   => hashPassword('Zona##123'),
            'role'      => 'admin',
            'jenis_pengguna' => 'guru',
            'member_code' => 'ADM-001',
            'is_validated' => 1,
            'is_archived' => 0,
            'must_change_password' => 1
        ]);

        echo "Admin default berhasil dibuat.\n";
    }
}
