<?php

namespace App\Database\Seeds;

use App\Services\BookService;
use CodeIgniter\Database\Seeder;

class BookSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // Truncate existing data to start clean
        $db->query("SET FOREIGN_KEY_CHECKS=0");
        $db->table('book_items')->truncate();
        $db->table('books')->truncate();
        $db->query("SET FOREIGN_KEY_CHECKS=1");

        $books = [
            ['judul' => 'Pemrograman Laravel Dasar', 'pengarang' => 'Andi Wijaya', 'penerbit' => 'Informatika Press', 'about' => 'Panduan dasar membangun aplikasi dengan Laravel.', 'stok' => 5],
            ['judul' => 'Mastering PHP 8', 'pengarang' => 'Budi Santoso', 'penerbit' => 'Tekno Media', 'about' => 'Pembahasan lengkap fitur terbaru PHP 8.', 'stok' => 4],
            ['judul' => 'Jaringan Komputer Fundamental', 'pengarang' => 'Rudi Hartono', 'penerbit' => 'Net Press', 'about' => 'Konsep dasar jaringan komputer dan implementasi.', 'stok' => 5],
            ['judul' => 'Cyber Security Blue Team', 'pengarang' => 'Agus Saputra', 'penerbit' => 'Secure Media', 'about' => 'Strategi pertahanan keamanan siber modern.', 'stok' => 3],
            ['judul' => 'Administrasi Server Linux', 'pengarang' => 'Deni Pratama', 'penerbit' => 'SysAdmin Publisher', 'about' => 'Panduan konfigurasi dan manajemen server Linux.', 'stok' => 4],
            ['judul' => 'Belajar MySQL Cepat', 'pengarang' => 'Siti Rahma', 'penerbit' => 'Database Press', 'about' => 'Tutorial praktis mengelola database MySQL.', 'stok' => 6],
            ['judul' => 'Membangun REST API', 'pengarang' => 'Rizky Maulana', 'penerbit' => 'API Media', 'about' => 'Panduan membuat REST API scalable.', 'stok' => 5],
            ['judul' => 'Cloud Computing Dasar', 'pengarang' => 'Fajar Nugroho', 'penerbit' => 'Cloud Press', 'about' => 'Pengenalan konsep dan arsitektur cloud.', 'stok' => 3],
            ['judul' => 'DevOps untuk Pemula', 'pengarang' => 'Yoga Pratama', 'penerbit' => 'Tech Media', 'about' => 'Praktik CI/CD dan automasi deployment.', 'stok' => 4],
            ['judul' => 'Docker & Container', 'pengarang' => 'Arif Setiawan', 'penerbit' => 'Container Press', 'about' => 'Implementasi containerisasi aplikasi.', 'stok' => 5],
            ['judul' => 'Kubernetes Practical Guide', 'pengarang' => 'Hendra Gunawan', 'penerbit' => 'Cloud Native Media', 'about' => 'Manajemen container orchestration.', 'stok' => 3],
            ['judul' => 'Algoritma & Struktur Data', 'pengarang' => 'Dewi Lestari', 'penerbit' => 'Informatika Press', 'about' => 'Dasar algoritma dan struktur data.', 'stok' => 5],
            ['judul' => 'Pemrograman Python', 'pengarang' => 'Tono Wibowo', 'penerbit' => 'Code Publisher', 'about' => 'Belajar Python untuk berbagai kebutuhan.', 'stok' => 6],
            ['judul' => 'Data Science Praktis', 'pengarang' => 'Lukman Hakim', 'penerbit' => 'Data Media', 'about' => 'Pengolahan data dan machine learning dasar.', 'stok' => 3],
            ['judul' => 'Machine Learning Dasar', 'pengarang' => 'Rina Kartika', 'penerbit' => 'AI Press', 'about' => 'Konsep supervised dan unsupervised learning.', 'stok' => 3],
            ['judul' => 'Artificial Intelligence Modern', 'pengarang' => 'Yusuf Maulana', 'penerbit' => 'AI Media', 'about' => 'Implementasi AI dalam industri.', 'stok' => 2],
            ['judul' => 'WordPress Developer Guide', 'pengarang' => 'Indra Saputra', 'penerbit' => 'CMS Press', 'about' => 'Membangun theme dan plugin WordPress.', 'stok' => 5],
            ['judul' => 'SEO untuk Website', 'pengarang' => 'Mega Sari', 'penerbit' => 'Digital Media', 'about' => 'Optimasi mesin pencari untuk website.', 'stok' => 4],
            ['judul' => 'UI/UX Design Principle', 'pengarang' => 'Ayu Permata', 'penerbit' => 'Creative Press', 'about' => 'Prinsip desain antarmuka modern.', 'stok' => 3],
            ['judul' => 'ReactJS Modern', 'pengarang' => 'Bagus Firmansyah', 'penerbit' => 'Frontend Media', 'about' => 'Membangun SPA dengan React.', 'stok' => 4],
            ['judul' => 'VueJS Framework', 'pengarang' => 'Randy Kurniawan', 'penerbit' => 'Frontend Media', 'about' => 'Panduan lengkap VueJS.', 'stok' => 3],
            ['judul' => 'Angular Enterprise', 'pengarang' => 'Farhan Akbar', 'penerbit' => 'Enterprise Press', 'about' => 'Pengembangan aplikasi enterprise Angular.', 'stok' => 3],
            ['judul' => 'Tailwind CSS', 'pengarang' => 'Dita Puspita', 'penerbit' => 'Design Press', 'about' => 'Utility-first CSS framework.', 'stok' => 4],
            ['judul' => 'Bootstrap 5 Guide', 'pengarang' => 'Rahmat Hidayat', 'penerbit' => 'Frontend Press', 'about' => 'Panduan cepat Bootstrap 5.', 'stok' => 5],
            ['judul' => 'Git & Version Control', 'pengarang' => 'Ilham Maulana', 'penerbit' => 'Dev Media', 'about' => 'Manajemen kode dengan Git.', 'stok' => 6],
            ['judul' => 'CI/CD Implementation', 'pengarang' => 'Reza Fahlevi', 'penerbit' => 'DevOps Press', 'about' => 'Pipeline otomatis deployment.', 'stok' => 4],
            ['judul' => 'Nginx Web Server', 'pengarang' => 'Galih Pratama', 'penerbit' => 'Server Press', 'about' => 'Konfigurasi dan optimasi Nginx.', 'stok' => 3],
            ['judul' => 'Apache Server Advanced', 'pengarang' => 'Eko Susanto', 'penerbit' => 'Server Media', 'about' => 'Manajemen Apache production.', 'stok' => 3],
            ['judul' => 'Firewall & Security', 'pengarang' => 'Bayu Saputra', 'penerbit' => 'Secure Press', 'about' => 'Manajemen firewall jaringan.', 'stok' => 4],
            ['judul' => 'Penetration Testing', 'pengarang' => 'Hafiz Rahman', 'penerbit' => 'Security Media', 'about' => 'Teknik dasar penetration testing.', 'stok' => 2],
            ['judul' => 'Kriptografi Modern', 'pengarang' => 'Satria Wijaya', 'penerbit' => 'Crypto Press', 'about' => 'Konsep enkripsi dan keamanan data.', 'stok' => 3],
            ['judul' => 'Blockchain Teknologi', 'pengarang' => 'Naufal Rizki', 'penerbit' => 'Fintech Media', 'about' => 'Implementasi blockchain.', 'stok' => 3],
            ['judul' => 'Fintech Revolution', 'pengarang' => 'Kevin Saputra', 'penerbit' => 'Finance Press', 'about' => 'Transformasi digital finansial.', 'stok' => 4],
            ['judul' => 'Analisis Saham Fundamental', 'pengarang' => 'Rama Pratama', 'penerbit' => 'Market Press', 'about' => 'Analisis laporan keuangan.', 'stok' => 5],
            ['judul' => 'Analisis Teknikal Trading', 'pengarang' => 'Yogi Firmansyah', 'penerbit' => 'Market Media', 'about' => 'Strategi chart dan indikator.', 'stok' => 4],
            ['judul' => 'Manajemen Risiko Investasi', 'pengarang' => 'Andre Gunawan', 'penerbit' => 'Finance Media', 'about' => 'Pengelolaan risiko investasi.', 'stok' => 3],
            ['judul' => 'Public Speaking IT', 'pengarang' => 'Citra Lestari', 'penerbit' => 'Skill Press', 'about' => 'Komunikasi teknis efektif.', 'stok' => 3],
            ['judul' => 'Leadership Teknologi', 'pengarang' => 'Putri Andini', 'penerbit' => 'Leadership Media', 'about' => 'Kepemimpinan dalam tim IT.', 'stok' => 2],
            ['judul' => 'Project Management IT', 'pengarang' => 'Arman Syah', 'penerbit' => 'PM Press', 'about' => 'Manajemen proyek teknologi.', 'stok' => 4],
            ['judul' => 'Scrum & Agile', 'pengarang' => 'Nadia Salsabila', 'penerbit' => 'Agile Media', 'about' => 'Metodologi agile development.', 'stok' => 4],
            ['judul' => 'Database Optimization', 'pengarang' => 'Haris Maulana', 'penerbit' => 'DB Press', 'about' => 'Optimasi performa database.', 'stok' => 3],
            ['judul' => 'High Availability System', 'pengarang' => 'Iqbal Ramadhan', 'penerbit' => 'Infra Media', 'about' => 'Arsitektur sistem high availability.', 'stok' => 2],
            ['judul' => 'Monitoring & Logging', 'pengarang' => 'Sandi Prakoso', 'penerbit' => 'Ops Press', 'about' => 'Implementasi monitoring sistem.', 'stok' => 3],
            ['judul' => 'Microservices Architecture', 'pengarang' => 'Rico Saputra', 'penerbit' => 'Arch Media', 'about' => 'Desain arsitektur microservices.', 'stok' => 2],
            ['judul' => 'API Security', 'pengarang' => 'Fikri Ananda', 'penerbit' => 'Security Press', 'about' => 'Keamanan API production.', 'stok' => 3],
            ['judul' => 'Cloudflare Practical', 'pengarang' => 'Rafi Maulana', 'penerbit' => 'Cloud Media', 'about' => 'Optimasi dan keamanan Cloudflare.', 'stok' => 3],
            ['judul' => 'Linux Hardening', 'pengarang' => 'Bagas Prasetyo', 'penerbit' => 'Security Media', 'about' => 'Pengamanan server Linux.', 'stok' => 4],
            ['judul' => 'Backup & Disaster Recovery', 'pengarang' => 'Seno Aji', 'penerbit' => 'Infra Press', 'about' => 'Strategi backup dan recovery.', 'stok' => 4],
            ['judul' => 'Big Data Processing', 'pengarang' => 'Wahyu Nugraha', 'penerbit' => 'Data Press', 'about' => 'Pemrosesan data skala besar.', 'stok' => 3],
            ['judul' => 'Enterprise Architecture', 'pengarang' => 'Rizal Hidayat', 'penerbit' => 'Enterprise Media', 'about' => 'Perancangan arsitektur enterprise.', 'stok' => 4],
        ];

        // Nomor DDC contoh, urut sesuai daftar buku di atas
        $ddc = [
            '005.133', '005.133', '004.6', '005.8', '005.432', '005.7565', '005.3', '004.6782', '005.1', '005.43',
            '005.43', '005.1', '005.133', '006.312', '006.31', '006.3', '006.78', '025.04', '004.019', '005.2762',
            '005.2762', '005.2762', '006.74', '006.74', '005.1', '005.1', '005.71', '005.71', '005.8', '005.8',
            '005.824', '005.74', '332.1', '332.632', '332.6', '332.6', '808.51', '658.4092', '658.404', '005.1',
            '005.74', '004.6', '005.7', '005.12', '005.8', '004.678', '005.8', '005.86', '005.7', '658.4038',
        ];

        // id klasifikasi DDC per kode (kelas, divisi, seksi); kosong jika DdcSeeder belum dijalankan
        $kelas = array_column($db->table('category')->get()->getResultArray(), 'id', 'code_ddc');

        $kodeAkun = '03.11.01.01.001';
        $sumberDana = 'BOS';
        $tahun = 2026;

        foreach ($books as $index => $b) {
            $numUrutBuku = str_pad($index + 1, 3, '0', STR_PAD_LEFT);
            $kodeBuku = "{$kodeAkun} / {$sumberDana} / {$tahun} / {$numUrutBuku}";

            // Insert book catalog row
            $db->table('books')->insert([
                'kode_buku' => $kodeBuku,
                'judul' => $b['judul'],
                'pengarang' => $b['pengarang'],
                'penerbit' => $b['penerbit'],
                'about' => $b['about'],
                'category_id' => $this->klasifikasi($kelas, $ddc[$index]),
                'ddc_number' => $ddc[$index],
                'stok_total' => $b['stok'],
                'stok_tersedia' => $b['stok'],
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $bookId = $db->insertID();

            // Insert book items copies
            for ($copy = 1; $copy <= $b['stok']; $copy++) {
                $numUrutCopy = str_pad($copy, 3, '0', STR_PAD_LEFT);
                $kodeInventaris = "{$kodeBuku} / {$numUrutCopy}";

                $db->table('book_items')->insert([
                    'book_id' => $bookId,
                    'kode_inventaris' => $kodeInventaris,
                    'sumber_dana' => $sumberDana,
                    'tahun_pengadaan' => $tahun,
                    'nomor_urut' => $copy,
                    'status' => 'available',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }
    }

    /**
     * Klasifikasi paling spesifik untuk Nomor DDC (seksi -> divisi -> kelas).
     */
    private function klasifikasi(array $kelas, string $ddc): ?int
    {
        foreach (BookService::ddcCandidates($ddc) as $code) {
            if (isset($kelas[$code])) {
                return (int) $kelas[$code];
            }
        }

        return null;
    }
}
