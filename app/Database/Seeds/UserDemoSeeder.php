<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserDemoSeeder extends Seeder
{
    public function run()
    {
        helper('password');
        $db = \Config\Database::connect();

        $demoUsers = [
            // 1. Petugas Perpustakaan (Staff)
            [
                'name'      => 'Lina Petugas',
                'username'  => 'lina_petugas',
                'password'  => hashPassword('Lina##123'),
                'role'      => 'staff',
                'jenis_pengguna' => 'pegawai',
                'member_code' => 'STF-002',
                'is_validated' => 1,
                'is_archived' => 0,
                'must_change_password' => 0
            ],
            // 2. Pegawai Sekolah (Member)
            [
                'name'      => 'Bambang Pegawai',
                'username'  => 'bambang_pegawai',
                'password'  => hashPassword('Bambang##123'),
                'role'      => 'member',
                'jenis_pengguna' => 'pegawai',
                'member_code' => 'PEG-001',
                'is_validated' => 1,
                'is_archived' => 0,
                'must_change_password' => 0
            ],
            // 3. Guru (Member)
            [
                'name'      => 'Dra. Endang Guru',
                'username'  => 'endang_guru',
                'password'  => hashPassword('Endang##123'),
                'role'      => 'member',
                'jenis_pengguna' => 'guru',
                'member_code' => 'GRU-001',
                'is_validated' => 1,
                'is_archived' => 0,
                'must_change_password' => 0
            ],
            // 4. Siswa 1 (Member)
            [
                'name'      => 'Siti Rahmawati (Siswa)',
                'username'  => 'siti_siswa',
                'password'  => hashPassword('Siti##123'),
                'role'      => 'member',
                'jenis_pengguna' => 'siswa',
                'member_code' => 'SSW-001',
                'is_validated' => 1,
                'is_archived' => 0,
                'must_change_password' => 0
            ],
            // 5. Siswa 2 (Member)
            [
                'name'      => 'Rian Hidayat (Siswa)',
                'username'  => 'rian_siswa',
                'password'  => hashPassword('Rian##123'),
                'role'      => 'member',
                'jenis_pengguna' => 'siswa',
                'member_code' => 'SSW-002',
                'is_validated' => 1,
                'is_archived' => 0,
                'must_change_password' => 0
            ]
        ];

        foreach ($demoUsers as $user) {
            $existing = $db->table('users')
                ->where('username', $user['username'])
                ->get()
                ->getRow();

            if (!$existing) {
                $db->table('users')->insert($user);
                echo "User {$user['username']} created successfully.\n";
            } else {
                echo "User {$user['username']} already exists.\n";
            }
        }
    }
}
