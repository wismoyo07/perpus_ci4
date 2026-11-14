<?php

namespace App\Commands;

use App\Libraries\Installer;
use App\Libraries\PasswordPolicy;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;
use Throwable;

/**
 * Wizard instalasi: pepper password unik, migrasi, data master, akun admin pertama,
 * dan (opsional) data demo. Aman dijalankan ulang: langkah yang sudah selesai dilewati.
 */
class Install extends BaseCommand
{
    protected $group       = 'Library';
    protected $name        = 'perpus:install';
    protected $description = 'Wizard instalasi: pepper password, migrasi, data master, akun admin pertama, dan data demo (opsional).';
    protected $usage       = 'perpus:install [options]';
    protected $options     = [
        '--demo'       => 'Tambahkan akun demo (staf & anggota) dan katalog buku contoh.',
        '--admin-name' => 'Nama admin pertama (tanpa pertanyaan).',
        '--admin-user' => 'Username admin pertama (tanpa pertanyaan).',
        '--admin-pass' => 'Password admin pertama (tanpa pertanyaan; hati-hati, tersimpan di riwayat terminal).',
        '--yes'        => 'Jangan minta konfirmasi database tujuan.',
    ];

    public function run(array $params)
    {
        CLI::write('=== Instalasi Perpustakaan CI4 ===', 'green');
        CLI::newLine();

        // 1. File .env
        $envPath = ROOTPATH . '.env';
        if (!is_file($envPath)) {
            copy(ROOTPATH . 'env.example', $envPath);
            CLI::write('File .env belum ada, sudah dibuat dari env.example.', 'yellow');
            CLI::write('Isi dulu app.baseURL dan koneksi database (database.default.*) di .env, lalu jalankan ulang perintah ini.');

            return EXIT_ERROR;
        }

        // 2. Koneksi & konfirmasi database tujuan
        try {
            $db = Database::connect();
            $db->initialize();
        } catch (Throwable $e) {
            CLI::error('Gagal terhubung ke database: ' . $e->getMessage());
            CLI::write('Periksa database.default.* di .env dan pastikan database sudah dibuat.');

            return EXIT_ERROR;
        }

        CLI::write('Database tujuan: ' . CLI::color($db->database, 'yellow') . ' di ' . $db->hostname);
        if (!array_key_exists('yes', $params) && !CLI::getOption('yes')
            && CLI::prompt('Lanjutkan instalasi ke database ini?', ['y', 'n']) !== 'y') {
            CLI::write('Dibatalkan.');

            return EXIT_ERROR;
        }

        // 3. Pepper password
        $userCount = $db->tableExists('users') ? $db->table('users')->countAllResults() : 0;
        $env       = file_get_contents($envPath);
        $pepper    = Installer::getEnvValue($env, 'APP_PASSWORD_PEPPER');

        if (Installer::shouldGeneratePepper($pepper, $userCount)) {
            $pepper = Installer::generatePepper();
            file_put_contents($envPath, Installer::setEnvValue($env, 'APP_PASSWORD_PEPPER', $pepper));
            // Pakai juga di proses ini, agar akun yang dibuat di bawah memakai pepper baru
            putenv('APP_PASSWORD_PEPPER=' . $pepper);
            $_ENV['APP_PASSWORD_PEPPER'] = $_SERVER['APP_PASSWORD_PEPPER'] = $pepper;
            CLI::write('[OK] Pepper password unik dibuat dan disimpan di .env.', 'green');
        } elseif (Installer::isPublicPepper($pepper)) {
            CLI::write('[!] Pepper password masih nilai contoh/publik, tetapi sudah ada ' . $userCount . ' akun.', 'yellow');
            CLI::write('    Pepper tidak diganti, karena password akun yang ada akan tidak bisa dipakai login.');
        } else {
            CLI::write('[OK] Pepper password sudah unik.', 'green');
        }

        // 4. Migrasi
        CLI::write('Menjalankan migrasi...');
        try {
            service('migrations')->setNamespace(null)->latest();
        } catch (Throwable $e) {
            CLI::error('Migrasi gagal: ' . $e->getMessage());

            return EXIT_ERROR;
        }
        CLI::write('[OK] Struktur database terbaru.', 'green');

        // 5. Data master (aman dijalankan ulang)
        $seeder = Database::seeder();
        $seeder->setSilent(true);
        foreach (['DdcSeeder', 'ClassSeeder', 'SourceFundSeeder'] as $name) {
            $seeder->call($name);
        }
        CLI::write('[OK] Data master: klasifikasi DDC, kelas, dan sumber dana.', 'green');

        // 6. Admin pertama
        if ($db->table('users')->where('role', 'admin')->countAllResults() > 0) {
            CLI::write('[OK] Akun admin sudah ada, dilewati.', 'green');
        } else {
            $this->createAdmin($db);
        }

        // 7. Data demo (opsional)
        if (array_key_exists('demo', $params) || CLI::getOption('demo')) {
            $this->seedDemo($db, $seeder);
        }

        CLI::newLine();
        CLI::write('Instalasi selesai.', 'green');
        CLI::write('Jalankan `php spark serve` (lokal) lalu buka aplikasi dan login sebagai admin.');
        CLI::write('Atur identitas sekolah di menu Pengaturan.');

        return EXIT_SUCCESS;
    }

    private function createAdmin($db): void
    {
        CLI::write('Membuat akun admin pertama.', 'yellow');

        $name = (string) (CLI::getOption('admin-name') ?? '');
        while (mb_strlen(trim($name)) < 3) {
            $name = CLI::prompt('Nama lengkap admin');
        }

        $username = (string) (CLI::getOption('admin-user') ?? '');
        while (!$this->validUsername($db, $username)) {
            if ($username !== '') {
                CLI::error('Username minimal 4 karakter (huruf, angka, titik, garis bawah, minus) dan belum dipakai.');
            }
            $username = CLI::prompt('Username admin', 'admin');
        }

        $password = (string) (CLI::getOption('admin-pass') ?? '');
        while (($errors = PasswordPolicy::errors($password, $username)) !== []) {
            if ($password !== '') {
                CLI::error(implode(' ', $errors));
            } else {
                CLI::write('Syarat password: ' . implode('; ', PasswordPolicy::hints()) . '.');
            }
            $password = CLI::prompt('Password admin (terlihat saat diketik)');
        }

        helper('password');
        $db->table('users')->insert([
            'name'                 => trim($name),
            'username'             => trim($username),
            'password'             => hashPassword($password),
            'role'                 => 'admin',
            'jenis_pengguna'       => 'pegawai',
            'member_code'          => 'ADM-001',
            'is_validated'         => 1,
            'is_archived'          => 0,
            'must_change_password' => 0,
            'created_at'           => date('Y-m-d H:i:s'),
            'updated_at'           => date('Y-m-d H:i:s'),
        ]);

        CLI::write('[OK] Admin "' . trim($username) . '" dibuat.', 'green');
    }

    private function validUsername($db, string $username): bool
    {
        $username = trim($username);

        return strlen($username) >= 4
            && preg_match('/^[A-Za-z0-9_.\-]+$/', $username)
            && $db->table('users')->where('username', $username)->countAllResults() === 0;
    }

    private function seedDemo($db, $seeder): void
    {
        foreach (['StaffSeeder', 'UserDemoSeeder'] as $name) {
            $seeder->call($name);
        }
        CLI::write('[OK] Akun demo staf & anggota (wajib ganti password saat login pertama, lihat README).', 'green');

        // BookSeeder mengosongkan tabel buku, jadi hanya dijalankan pada katalog kosong
        if ($db->table('books')->countAllResults() === 0) {
            $seeder->call('BookSeeder');
            CLI::write('[OK] Katalog buku contoh.', 'green');
        } else {
            CLI::write('[!] Katalog buku sudah berisi data, buku contoh tidak ditambahkan.', 'yellow');
        }
    }
}
