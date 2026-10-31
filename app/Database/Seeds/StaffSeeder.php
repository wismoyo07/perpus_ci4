<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class StaffSeeder extends Seeder
{
    public function run()
    {
        helper('password');
        
        $db = \Config\Database::connect();

        $existing = $db->table('users')
            ->where('username', 'staff')
            ->get()
            ->getRow();

        if ($existing) {
            echo "Staff sudah ada.\n";
            return;
        }

        $db->table('users')->insert([
            'name'      => 'Staff Perpustakaan',
            'username'  => 'staff',
            // 'password'  => password_hash('admin123', PASSWORD_BCRYPT), hash password default
            'password'   => hashPassword('Zona##123'),
            'role'      => 'staff',
            'jenis_pengguna' => 'pegawai',
            'member_code' => 'STF-001',
            'is_validated' => 1,
            'is_archived' => 0,
            'must_change_password' => 1
        ]);

        echo "User Staff berhasil dibuat.\n";
    }
}
