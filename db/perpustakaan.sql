-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.15.0.7171
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for perpus_ci4
DROP DATABASE IF EXISTS `perpus_ci4`;
CREATE DATABASE IF NOT EXISTS `perpus_ci4` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `perpus_ci4`;

-- Dumping structure for table perpus_ci4.activity_logs
CREATE TABLE IF NOT EXISTS `activity_logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `aktivitas` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `target_user_id` int DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`id`),
  KEY `activity_logs_user_id_foreign` (`user_id`),
  CONSTRAINT `activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table perpus_ci4_bck.activity_logs: ~6 rows (approximately)
DELETE FROM `activity_logs`;
INSERT INTO `activity_logs` (`id`, `user_id`, `aktivitas`, `created_at`, `target_user_id`, `ip_address`, `user_agent`) VALUES
	(1, NULL, 'Login gagal - password salah', '2026-03-07 15:21:14', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36'),
	(2, 1, 'Login berhasil', '2026-03-07 15:21:23', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36'),
	(3, 1, 'Logout', '2026-03-07 22:24:11', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36'),
	(4, 1, 'Login berhasil', '2026-03-07 22:24:23', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36'),
	(5, 1, 'Mengupdate buku: Pemrograman Laravel Dasar', '2026-03-07 22:25:17', NULL, NULL, NULL),
	(6, 1, 'Mengupdate buku: Pemrograman Laravel Dasar', '2026-03-07 22:25:25', NULL, NULL, NULL);

-- Dumping structure for table perpus_ci4_bck.books
CREATE TABLE IF NOT EXISTS `books` (
  `id` int NOT NULL AUTO_INCREMENT,
  `kode_buku` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `judul` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `pengarang` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `penerbit` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `about` text COLLATE utf8mb4_general_ci,
  `stok_total` int NOT NULL DEFAULT '0',
  `stok_tersedia` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode_buku` (`kode_buku`),
  KEY `is_active` (`is_active`),
  CONSTRAINT `chk_stok` CHECK (((`stok_total` >= 0) and (`stok_tersedia` >= 0) and (`stok_tersedia` <= `stok_total`)))
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table perpus_ci4_bck.books: ~50 rows (approximately)
DELETE FROM `books`;
INSERT INTO `books` (`id`, `kode_buku`, `judul`, `pengarang`, `penerbit`, `about`, `stok_total`, `stok_tersedia`, `is_active`, `created_at`, `updated_at`) VALUES
	(1, 'BK001', 'Pemrograman Laravel Dasar', 'Andi Wijaya', 'Informatika Press', 'Panduan dasar membangun aplikasi dengan Laravel.', 22, 22, 1, '2026-02-22 02:27:58', '2026-03-07 22:25:25'),
	(2, 'BK002', 'Mastering PHP 8', 'Budi Santoso', 'Tekno Media', 'Pembahasan lengkap fitur terbaru PHP 8.', 15, 15, 1, '2026-02-22 02:27:58', '2026-02-22 02:27:58'),
	(3, 'BK003', 'Jaringan Komputer Fundamental', 'Rudi Hartono', 'Net Press', 'Konsep dasar jaringan komputer dan implementasi.', 25, 25, 1, '2026-02-22 02:27:58', '2026-02-22 02:27:58'),
	(4, 'BK004', 'Cyber Security Blue Team', 'Agus Saputra', 'Secure Media', 'Strategi pertahanan keamanan siber modern.', 18, 18, 1, '2026-02-22 02:27:58', '2026-02-22 14:07:15'),
	(5, 'BK005', 'Administrasi Server Linux', 'Deni Pratama', 'SysAdmin Publisher', 'Panduan konfigurasi dan manajemen server Linux.', 24, 23, 1, '2026-02-22 02:27:58', '2026-03-01 00:27:41'),
	(6, 'BK006', 'Belajar MySQL Cepat', 'Siti Rahma', 'Database Press', 'Tutorial praktis mengelola database MySQL.', 30, 30, 1, '2026-02-22 02:27:58', '2026-02-22 02:27:58'),
	(7, 'BK007', 'Membangun REST API', 'Rizky Maulana', 'API Media', 'Panduan membuat REST API scalable.', 20, 20, 1, '2026-02-22 02:27:58', '2026-02-22 14:07:15'),
	(8, 'BK008', 'Cloud Computing Dasar', 'Fajar Nugroho', 'Cloud Press', 'Pengenalan konsep dan arsitektur cloud.', 17, 17, 1, '2026-02-22 02:27:58', '2026-02-22 14:31:51'),
	(9, 'BK009', 'DevOps untuk Pemula', 'Yoga Pratama', 'Tech Media', 'Praktik CI/CD dan automasi deployment.', 19, 19, 1, '2026-02-22 02:27:58', '2026-02-22 14:31:51'),
	(10, 'BK010', 'Docker & Container', 'Arif Setiawan', 'Container Press', 'Implementasi containerisasi aplikasi.', 21, 21, 1, '2026-02-22 02:27:58', '2026-02-22 02:27:58'),
	(11, 'BK011', 'Kubernetes Practical Guide', 'Hendra Gunawan', 'Cloud Native Media', 'Manajemen container orchestration.', 16, 16, 1, '2026-02-22 02:27:58', '2026-02-22 02:27:58'),
	(12, 'BK012', 'Algoritma & Struktur Data', 'Dewi Lestari', 'Informatika Press', 'Dasar algoritma dan struktur data.', 28, 28, 1, '2026-02-22 02:27:58', '2026-02-28 22:58:24'),
	(13, 'BK013', 'Pemrograman Python', 'Tono Wibowo', 'Code Publisher', 'Belajar Python untuk berbagai kebutuhan.', 35, 35, 1, '2026-02-22 02:27:58', '2026-02-22 02:27:58'),
	(14, 'BK014', 'Data Science Praktis', 'Lukman Hakim', 'Data Media', 'Pengolahan data dan machine learning dasar.', 14, 14, 1, '2026-02-22 02:27:58', '2026-02-22 02:27:58'),
	(15, 'BK015', 'Machine Learning Dasar', 'Rina Kartika', 'AI Press', 'Konsep supervised dan unsupervised learning.', 12, 12, 1, '2026-02-22 02:27:58', '2026-02-22 02:27:58'),
	(16, 'BK016', 'Artificial Intelligence Modern', 'Yusuf Maulana', 'AI Media', 'Implementasi AI dalam industri.', 10, 10, 1, '2026-02-22 02:27:58', '2026-02-22 02:27:58'),
	(17, 'BK017', 'WordPress Developer Guide', 'Indra Saputra', 'CMS Press', 'Membangun theme dan plugin WordPress.', 27, 27, 1, '2026-02-22 02:27:58', '2026-02-22 02:27:58'),
	(18, 'BK018', 'SEO untuk Website', 'Mega Sari', 'Digital Media', 'Optimasi mesin pencari untuk website.', 20, 20, 1, '2026-02-22 02:27:58', '2026-02-22 02:27:58'),
	(19, 'BK019', 'UI/UX Design Principle', 'Ayu Permata', 'Creative Press', 'Prinsip desain antarmuka modern.', 15, 15, 1, '2026-02-22 02:27:58', '2026-02-22 02:27:58'),
	(20, 'BK020', 'ReactJS Modern', 'Bagus Firmansyah', 'Frontend Media', 'Membangun SPA dengan React.', 18, 18, 1, '2026-02-22 02:27:58', '2026-02-22 02:27:58'),
	(21, 'BK021', 'VueJS Framework', 'Randy Kurniawan', 'Frontend Media', 'Panduan lengkap VueJS.', 16, 16, 1, '2026-02-22 02:27:58', '2026-02-22 02:27:58'),
	(22, 'BK022', 'Angular Enterprise', 'Farhan Akbar', 'Enterprise Press', 'Pengembangan aplikasi enterprise Angular.', 14, 14, 1, '2026-02-22 02:27:58', '2026-02-22 02:27:58'),
	(23, 'BK023', 'Tailwind CSS', 'Dita Puspita', 'Design Press', 'Utility-first CSS framework.', 20, 20, 1, '2026-02-22 02:27:58', '2026-02-22 02:27:58'),
	(24, 'BK024', 'Bootstrap 5 Guide', 'Rahmat Hidayat', 'Frontend Press', 'Panduan cepat Bootstrap 5.', 22, 22, 1, '2026-02-22 02:27:58', '2026-02-22 02:27:58'),
	(25, 'BK025', 'Git & Version Control', 'Ilham Maulana', 'Dev Media', 'Manajemen kode dengan Git.', 30, 30, 1, '2026-02-22 02:27:58', '2026-02-22 02:27:58'),
	(26, 'BK026', 'CI/CD Implementation', 'Reza Fahlevi', 'DevOps Press', 'Pipeline otomatis deployment.', 18, 18, 1, '2026-02-22 02:27:58', '2026-02-22 02:27:58'),
	(27, 'BK027', 'Nginx Web Server', 'Galih Pratama', 'Server Press', 'Konfigurasi dan optimasi Nginx.', 13, 13, 1, '2026-02-22 02:27:58', '2026-02-22 02:27:58'),
	(28, 'BK028', 'Apache Server Advanced', 'Eko Susanto', 'Server Media', 'Manajemen Apache production.', 12, 12, 1, '2026-02-22 02:27:58', '2026-02-22 02:27:58'),
	(29, 'BK029', 'Firewall & Security', 'Bayu Saputra', 'Secure Press', 'Manajemen firewall jaringan.', 17, 17, 1, '2026-02-22 02:27:58', '2026-02-22 02:27:58'),
	(30, 'BK030', 'Penetration Testing', 'Hafiz Rahman', 'Security Media', 'Teknik dasar penetration testing.', 11, 11, 1, '2026-02-22 02:27:58', '2026-02-22 02:27:58'),
	(31, 'BK031', 'Kriptografi Modern', 'Satria Wijaya', 'Crypto Press', 'Konsep enkripsi dan keamanan data.', 14, 14, 1, '2026-02-22 02:27:58', '2026-02-22 02:27:58'),
	(32, 'BK032', 'Blockchain Teknologi', 'Naufal Rizki', 'Fintech Media', 'Implementasi blockchain.', 16, 16, 1, '2026-02-22 02:27:58', '2026-02-22 02:27:58'),
	(33, 'BK033', 'Fintech Revolution', 'Kevin Saputra', 'Finance Press', 'Transformasi digital finansial.', 20, 20, 1, '2026-02-22 02:27:58', '2026-02-22 02:27:58'),
	(34, 'BK034', 'Analisis Saham Fundamental', 'Rama Pratama', 'Market Press', 'Analisis laporan keuangan.', 25, 25, 1, '2026-02-22 02:27:58', '2026-02-22 02:27:58'),
	(35, 'BK035', 'Analisis Teknikal Trading', 'Yogi Firmansyah', 'Market Media', 'Strategi chart dan indikator.', 22, 22, 1, '2026-02-22 02:27:58', '2026-02-22 02:27:58'),
	(36, 'BK036', 'Manajemen Risiko Investasi', 'Andre Gunawan', 'Finance Media', 'Pengelolaan risiko investasi.', 18, 18, 1, '2026-02-22 02:27:58', '2026-02-22 02:27:58'),
	(37, 'BK037', 'Public Speaking IT', 'Citra Lestari', 'Skill Press', 'Komunikasi teknis efektif.', 15, 15, 1, '2026-02-22 02:27:58', '2026-02-22 02:27:58'),
	(38, 'BK038', 'Leadership Teknologi', 'Putri Andini', 'Leadership Media', 'Kepemimpinan dalam tim IT.', 12, 12, 1, '2026-02-22 02:27:58', '2026-02-22 02:27:58'),
	(39, 'BK039', 'Project Management IT', 'Arman Syah', 'PM Press', 'Manajemen proyek teknologi.', 19, 19, 1, '2026-02-22 02:27:58', '2026-02-22 02:27:58'),
	(40, 'BK040', 'Scrum & Agile', 'Nadia Salsabila', 'Agile Media', 'Metodologi agile development.', 21, 21, 1, '2026-02-22 02:27:58', '2026-02-22 02:27:58'),
	(41, 'BK041', 'Database Optimization', 'Haris Maulana', 'DB Press', 'Optimasi performa database.', 13, 13, 1, '2026-02-22 02:27:58', '2026-02-22 02:27:58'),
	(42, 'BK042', 'High Availability System', 'Iqbal Ramadhan', 'Infra Media', 'Arsitektur sistem high availability.', 10, 10, 1, '2026-02-22 02:27:58', '2026-02-22 02:27:58'),
	(43, 'BK043', 'Monitoring & Logging', 'Sandi Prakoso', 'Ops Press', 'Implementasi monitoring sistem.', 14, 14, 1, '2026-02-22 02:27:58', '2026-02-22 02:27:58'),
	(44, 'BK044', 'Microservices Architecture', 'Rico Saputra', 'Arch Media', 'Desain arsitektur microservices.', 14, 13, 1, '2026-02-22 02:27:58', '2026-03-01 00:35:17'),
	(45, 'BK045', 'API Security', 'Fikri Ananda', 'Security Press', 'Keamanan API production.', 12, 12, 1, '2026-02-22 02:27:58', '2026-02-22 02:27:58'),
	(46, 'BK046', 'Cloudflare Practical', 'Rafi Maulana', 'Cloud Media', 'Optimasi dan keamanan Cloudflare.', 16, 16, 1, '2026-02-22 02:27:58', '2026-02-26 23:58:42'),
	(47, 'BK047', 'Linux Hardening', 'Bagas Prasetyo', 'Security Media', 'Pengamanan server Linux.', 17, 17, 1, '2026-02-22 02:27:58', '2026-02-22 02:27:58'),
	(48, 'BK048', 'Backup & Disaster Recovery', 'Seno Aji', 'Infra Press', 'Strategi backup dan recovery.', 20, 20, 1, '2026-02-22 02:27:58', '2026-02-22 02:27:58'),
	(49, 'BK049', 'Big Data Processing', 'Wahyu Nugraha', 'Data Press', 'Pemrosesan data skala besar.', 14, 14, 1, '2026-02-22 02:27:58', '2026-02-22 02:27:58'),
	(50, 'BK050', 'Enterprise Architecture', 'Rizal Hidayat', 'Enterprise Media', 'Perancangan arsitektur enterprise.', 18, 18, 1, '2026-02-22 02:27:58', '2026-02-26 23:58:42');

-- Dumping structure for table perpus_ci4_bck.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `version` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `class` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `group` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `namespace` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `time` int NOT NULL,
  `batch` int unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table perpus_ci4_bck.migrations: ~0 rows (approximately)
DELETE FROM `migrations`;

-- Dumping structure for table perpus_ci4_bck.transaction_details
CREATE TABLE IF NOT EXISTS `transaction_details` (
  `id` int NOT NULL AUTO_INCREMENT,
  `transaction_id` int NOT NULL,
  `book_id` int NOT NULL,
  `is_lost` tinyint(1) DEFAULT '0',
  `lost_settlement_status` enum('pending','replaced') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `lost_note` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `transaction_id_book_id` (`transaction_id`,`book_id`),
  UNIQUE KEY `uniq_transaction_book` (`transaction_id`,`book_id`),
  KEY `book_id` (`book_id`),
  KEY `idx_details_transaction` (`transaction_id`),
  KEY `idx_details_book` (`book_id`),
  KEY `idx_details_lost` (`is_lost`),
  CONSTRAINT `transaction_details_book_id_foreign` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `transaction_details_transaction_id_foreign` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table perpus_ci4_bck.transaction_details: ~0 rows (approximately)
DELETE FROM `transaction_details`;

-- Dumping structure for table perpus_ci4_bck.transactions
CREATE TABLE IF NOT EXISTS `transactions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `kode_transaksi` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `user_id` int NOT NULL,
  `borrowed_by` int NOT NULL,
  `returned_by` int DEFAULT NULL,
  `tanggal_pinjam` date NOT NULL,
  `jatuh_tempo` date NOT NULL,
  `tanggal_kembali` date DEFAULT NULL,
  `status` enum('dipinjam','terlambat','selesai') COLLATE utf8mb4_general_ci DEFAULT 'dipinjam',
  `total_denda` decimal(12,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode_transaksi` (`kode_transaksi`),
  KEY `user_id` (`user_id`),
  KEY `status` (`status`),
  KEY `jatuh_tempo` (`jatuh_tempo`),
  KEY `idx_transactions_user_status` (`user_id`,`status`),
  KEY `idx_transactions_status` (`status`),
  KEY `idx_transactions_jatuh_tempo` (`jatuh_tempo`),
  KEY `fk_transactions_borrowed_by` (`borrowed_by`),
  KEY `fk_transactions_returned_by` (`returned_by`),
  CONSTRAINT `fk_transactions_borrowed_by` FOREIGN KEY (`borrowed_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_transactions_returned_by` FOREIGN KEY (`returned_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table perpus_ci4_bck.transactions: ~0 rows (approximately)
DELETE FROM `transactions`;

-- Dumping structure for table perpus_ci4_bck.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('admin','staff','member') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'member',
  `jenis_pengguna` enum('siswa','guru','pegawai') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'siswa',
  `nomor_induk` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `is_validated` tinyint(1) DEFAULT '0',
  `is_archived` tinyint(1) DEFAULT '0',
  `must_change_password` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `last_login` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `uniq_nomor_induk` (`nomor_induk`),
  KEY `role` (`role`),
  KEY `is_active` (`is_validated`),
  KEY `idx_users_role` (`role`),
  KEY `idx_users_active` (`is_validated`),
  KEY `idx_users_jenis` (`jenis_pengguna`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table perpus_ci4_bck.users: ~0 rows (approximately)
DELETE FROM `users`;
INSERT INTO `users` (`id`, `name`, `username`, `password`, `role`, `jenis_pengguna`, `nomor_induk`, `is_validated`, `is_archived`, `must_change_password`, `created_at`, `updated_at`, `last_login`) VALUES
	(1, 'Super Administrator', 'admin', '$2y$12$wX6z2w/CrqZaGWiDYtVgZ.6Cm39nrhF/p0CD5kLNiHzoGP849yoKm', 'admin', 'guru', 'ADM-001', 1, 0, 0, '2026-03-03 01:30:09', '2026-03-03 01:30:09', NULL);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
