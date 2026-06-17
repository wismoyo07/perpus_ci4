-- MySQL dump 10.13  Distrib 8.0.30, for Win64 (x86_64)
--
-- Host: localhost    Database: perpus_ci4
-- ------------------------------------------------------
-- Server version	8.0.30

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `activity_logs`
--

DROP TABLE IF EXISTS `activity_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activity_logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `aktivitas` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `target_user_id` int DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_logs`
--

LOCK TABLES `activity_logs` WRITE;
/*!40000 ALTER TABLE `activity_logs` DISABLE KEYS */;
INSERT INTO `activity_logs` VALUES (1,8,'Registrasi akun baru',8,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36','2026-06-16 15:30:56'),(2,1,'Login gagal - akun belum divalidasi',8,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36','2026-06-16 15:31:19'),(3,1,'Login berhasil',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36','2026-06-16 15:31:49'),(4,1,'Validasi user',8,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36','2026-06-16 15:32:16'),(5,1,'Logout',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36','2026-06-16 15:32:40'),(6,8,'Login berhasil',8,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36','2026-06-16 15:33:03'),(7,8,'Logout',8,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36','2026-06-16 15:33:16'),(8,NULL,'Login gagal - password salah',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36','2026-06-16 16:59:26'),(9,1,'Login berhasil',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36','2026-06-16 17:00:19'),(10,1,'Logout',1,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36','2026-06-16 17:04:13'),(11,3,'Login berhasil',3,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36','2026-06-16 23:44:17'),(12,3,'Menambahkan salinan fisik book ID 1 dengan kode 03.11.01.01.001 / BOS / 2026 / 001 / 006',NULL,NULL,NULL,'2026-06-16 23:44:54'),(13,3,'Mencatat transaksi peminjaman TRX-2026061706463588 (ID 1)',7,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36','2026-06-16 23:46:35'),(14,3,'Buku \'Pemrograman Laravel Dasar\' (Copy ID 1) dinyatakan hilang (Transaksi 1)',7,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36','2026-06-16 23:47:07'),(15,3,'Transaksi 1 diselesaikan. Denda: Rp0',7,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36','2026-06-16 23:47:07'),(16,3,'Penyelesaian buku hilang: menggantikan [03.11.01.01.001 / BOS / 2026 / 001 / 001] dengan [03.11.01.01.001 / BOS / 2026 / 001 / 007] (Transaksi ID 1)',7,'::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36','2026-06-16 23:47:54');
/*!40000 ALTER TABLE `activity_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `app_settings`
--

DROP TABLE IF EXISTS `app_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `app_settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `logo` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `kode_lembaga` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nama_lembaga` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `status_akreditasi` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `alamat` text COLLATE utf8mb4_general_ci,
  `kepala_sekolah` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `wakil_kepala_sekolah` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `website_url` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `app_settings`
--

LOCK TABLES `app_settings` WRITE;
/*!40000 ALTER TABLE `app_settings` DISABLE KEYS */;
INSERT INTO `app_settings` VALUES (1,NULL,'','Perpustakaan Sekolah','','','','','','2026-06-16 16:58:47','2026-06-16 16:58:47');
/*!40000 ALTER TABLE `app_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `book_items`
--

DROP TABLE IF EXISTS `book_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `book_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `book_id` int NOT NULL,
  `kode_inventaris` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `sumber_dana` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tahun_pengadaan` year DEFAULT NULL,
  `nomor_urut` int DEFAULT NULL,
  `status` enum('available','borrowed','lost','damaged') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'available',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode_inventaris` (`kode_inventaris`),
  KEY `book_id` (`book_id`),
  CONSTRAINT `book_items_book_id_foreign` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=186 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `book_items`
--

LOCK TABLES `book_items` WRITE;
/*!40000 ALTER TABLE `book_items` DISABLE KEYS */;
INSERT INTO `book_items` VALUES (1,1,'03.11.01.01.001 / BOS / 2026 / 001 / 001','BOS',2026,1,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(2,1,'03.11.01.01.001 / BOS / 2026 / 001 / 002','BOS',2026,2,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(3,1,'03.11.01.01.001 / BOS / 2026 / 001 / 003','BOS',2026,3,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(4,1,'03.11.01.01.001 / BOS / 2026 / 001 / 004','BOS',2026,4,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(5,1,'03.11.01.01.001 / BOS / 2026 / 001 / 005','BOS',2026,5,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(6,2,'03.11.01.01.001 / BOS / 2026 / 002 / 001','BOS',2026,1,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(7,2,'03.11.01.01.001 / BOS / 2026 / 002 / 002','BOS',2026,2,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(8,2,'03.11.01.01.001 / BOS / 2026 / 002 / 003','BOS',2026,3,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(9,2,'03.11.01.01.001 / BOS / 2026 / 002 / 004','BOS',2026,4,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(10,3,'03.11.01.01.001 / BOS / 2026 / 003 / 001','BOS',2026,1,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(11,3,'03.11.01.01.001 / BOS / 2026 / 003 / 002','BOS',2026,2,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(12,3,'03.11.01.01.001 / BOS / 2026 / 003 / 003','BOS',2026,3,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(13,3,'03.11.01.01.001 / BOS / 2026 / 003 / 004','BOS',2026,4,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(14,3,'03.11.01.01.001 / BOS / 2026 / 003 / 005','BOS',2026,5,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(15,4,'03.11.01.01.001 / BOS / 2026 / 004 / 001','BOS',2026,1,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(16,4,'03.11.01.01.001 / BOS / 2026 / 004 / 002','BOS',2026,2,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(17,4,'03.11.01.01.001 / BOS / 2026 / 004 / 003','BOS',2026,3,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(18,5,'03.11.01.01.001 / BOS / 2026 / 005 / 001','BOS',2026,1,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(19,5,'03.11.01.01.001 / BOS / 2026 / 005 / 002','BOS',2026,2,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(20,5,'03.11.01.01.001 / BOS / 2026 / 005 / 003','BOS',2026,3,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(21,5,'03.11.01.01.001 / BOS / 2026 / 005 / 004','BOS',2026,4,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(22,6,'03.11.01.01.001 / BOS / 2026 / 006 / 001','BOS',2026,1,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(23,6,'03.11.01.01.001 / BOS / 2026 / 006 / 002','BOS',2026,2,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(24,6,'03.11.01.01.001 / BOS / 2026 / 006 / 003','BOS',2026,3,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(25,6,'03.11.01.01.001 / BOS / 2026 / 006 / 004','BOS',2026,4,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(26,6,'03.11.01.01.001 / BOS / 2026 / 006 / 005','BOS',2026,5,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(27,6,'03.11.01.01.001 / BOS / 2026 / 006 / 006','BOS',2026,6,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(28,7,'03.11.01.01.001 / BOS / 2026 / 007 / 001','BOS',2026,1,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(29,7,'03.11.01.01.001 / BOS / 2026 / 007 / 002','BOS',2026,2,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(30,7,'03.11.01.01.001 / BOS / 2026 / 007 / 003','BOS',2026,3,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(31,7,'03.11.01.01.001 / BOS / 2026 / 007 / 004','BOS',2026,4,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(32,7,'03.11.01.01.001 / BOS / 2026 / 007 / 005','BOS',2026,5,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(33,8,'03.11.01.01.001 / BOS / 2026 / 008 / 001','BOS',2026,1,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(34,8,'03.11.01.01.001 / BOS / 2026 / 008 / 002','BOS',2026,2,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(35,8,'03.11.01.01.001 / BOS / 2026 / 008 / 003','BOS',2026,3,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(36,9,'03.11.01.01.001 / BOS / 2026 / 009 / 001','BOS',2026,1,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(37,9,'03.11.01.01.001 / BOS / 2026 / 009 / 002','BOS',2026,2,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(38,9,'03.11.01.01.001 / BOS / 2026 / 009 / 003','BOS',2026,3,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(39,9,'03.11.01.01.001 / BOS / 2026 / 009 / 004','BOS',2026,4,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(40,10,'03.11.01.01.001 / BOS / 2026 / 010 / 001','BOS',2026,1,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(41,10,'03.11.01.01.001 / BOS / 2026 / 010 / 002','BOS',2026,2,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(42,10,'03.11.01.01.001 / BOS / 2026 / 010 / 003','BOS',2026,3,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(43,10,'03.11.01.01.001 / BOS / 2026 / 010 / 004','BOS',2026,4,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(44,10,'03.11.01.01.001 / BOS / 2026 / 010 / 005','BOS',2026,5,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(45,11,'03.11.01.01.001 / BOS / 2026 / 011 / 001','BOS',2026,1,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(46,11,'03.11.01.01.001 / BOS / 2026 / 011 / 002','BOS',2026,2,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(47,11,'03.11.01.01.001 / BOS / 2026 / 011 / 003','BOS',2026,3,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(48,12,'03.11.01.01.001 / BOS / 2026 / 012 / 001','BOS',2026,1,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(49,12,'03.11.01.01.001 / BOS / 2026 / 012 / 002','BOS',2026,2,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(50,12,'03.11.01.01.001 / BOS / 2026 / 012 / 003','BOS',2026,3,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(51,12,'03.11.01.01.001 / BOS / 2026 / 012 / 004','BOS',2026,4,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(52,12,'03.11.01.01.001 / BOS / 2026 / 012 / 005','BOS',2026,5,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(53,13,'03.11.01.01.001 / BOS / 2026 / 013 / 001','BOS',2026,1,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(54,13,'03.11.01.01.001 / BOS / 2026 / 013 / 002','BOS',2026,2,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(55,13,'03.11.01.01.001 / BOS / 2026 / 013 / 003','BOS',2026,3,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(56,13,'03.11.01.01.001 / BOS / 2026 / 013 / 004','BOS',2026,4,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(57,13,'03.11.01.01.001 / BOS / 2026 / 013 / 005','BOS',2026,5,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(58,13,'03.11.01.01.001 / BOS / 2026 / 013 / 006','BOS',2026,6,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(59,14,'03.11.01.01.001 / BOS / 2026 / 014 / 001','BOS',2026,1,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(60,14,'03.11.01.01.001 / BOS / 2026 / 014 / 002','BOS',2026,2,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(61,14,'03.11.01.01.001 / BOS / 2026 / 014 / 003','BOS',2026,3,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(62,15,'03.11.01.01.001 / BOS / 2026 / 015 / 001','BOS',2026,1,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(63,15,'03.11.01.01.001 / BOS / 2026 / 015 / 002','BOS',2026,2,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(64,15,'03.11.01.01.001 / BOS / 2026 / 015 / 003','BOS',2026,3,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(65,16,'03.11.01.01.001 / BOS / 2026 / 016 / 001','BOS',2026,1,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(66,16,'03.11.01.01.001 / BOS / 2026 / 016 / 002','BOS',2026,2,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(67,17,'03.11.01.01.001 / BOS / 2026 / 017 / 001','BOS',2026,1,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(68,17,'03.11.01.01.001 / BOS / 2026 / 017 / 002','BOS',2026,2,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(69,17,'03.11.01.01.001 / BOS / 2026 / 017 / 003','BOS',2026,3,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(70,17,'03.11.01.01.001 / BOS / 2026 / 017 / 004','BOS',2026,4,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(71,17,'03.11.01.01.001 / BOS / 2026 / 017 / 005','BOS',2026,5,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(72,18,'03.11.01.01.001 / BOS / 2026 / 018 / 001','BOS',2026,1,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(73,18,'03.11.01.01.001 / BOS / 2026 / 018 / 002','BOS',2026,2,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(74,18,'03.11.01.01.001 / BOS / 2026 / 018 / 003','BOS',2026,3,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(75,18,'03.11.01.01.001 / BOS / 2026 / 018 / 004','BOS',2026,4,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(76,19,'03.11.01.01.001 / BOS / 2026 / 019 / 001','BOS',2026,1,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(77,19,'03.11.01.01.001 / BOS / 2026 / 019 / 002','BOS',2026,2,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(78,19,'03.11.01.01.001 / BOS / 2026 / 019 / 003','BOS',2026,3,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(79,20,'03.11.01.01.001 / BOS / 2026 / 020 / 001','BOS',2026,1,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(80,20,'03.11.01.01.001 / BOS / 2026 / 020 / 002','BOS',2026,2,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(81,20,'03.11.01.01.001 / BOS / 2026 / 020 / 003','BOS',2026,3,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(82,20,'03.11.01.01.001 / BOS / 2026 / 020 / 004','BOS',2026,4,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(83,21,'03.11.01.01.001 / BOS / 2026 / 021 / 001','BOS',2026,1,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(84,21,'03.11.01.01.001 / BOS / 2026 / 021 / 002','BOS',2026,2,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(85,21,'03.11.01.01.001 / BOS / 2026 / 021 / 003','BOS',2026,3,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(86,22,'03.11.01.01.001 / BOS / 2026 / 022 / 001','BOS',2026,1,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(87,22,'03.11.01.01.001 / BOS / 2026 / 022 / 002','BOS',2026,2,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(88,22,'03.11.01.01.001 / BOS / 2026 / 022 / 003','BOS',2026,3,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(89,23,'03.11.01.01.001 / BOS / 2026 / 023 / 001','BOS',2026,1,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(90,23,'03.11.01.01.001 / BOS / 2026 / 023 / 002','BOS',2026,2,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(91,23,'03.11.01.01.001 / BOS / 2026 / 023 / 003','BOS',2026,3,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(92,23,'03.11.01.01.001 / BOS / 2026 / 023 / 004','BOS',2026,4,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(93,24,'03.11.01.01.001 / BOS / 2026 / 024 / 001','BOS',2026,1,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(94,24,'03.11.01.01.001 / BOS / 2026 / 024 / 002','BOS',2026,2,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(95,24,'03.11.01.01.001 / BOS / 2026 / 024 / 003','BOS',2026,3,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(96,24,'03.11.01.01.001 / BOS / 2026 / 024 / 004','BOS',2026,4,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(97,24,'03.11.01.01.001 / BOS / 2026 / 024 / 005','BOS',2026,5,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(98,25,'03.11.01.01.001 / BOS / 2026 / 025 / 001','BOS',2026,1,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(99,25,'03.11.01.01.001 / BOS / 2026 / 025 / 002','BOS',2026,2,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(100,25,'03.11.01.01.001 / BOS / 2026 / 025 / 003','BOS',2026,3,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(101,25,'03.11.01.01.001 / BOS / 2026 / 025 / 004','BOS',2026,4,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(102,25,'03.11.01.01.001 / BOS / 2026 / 025 / 005','BOS',2026,5,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(103,25,'03.11.01.01.001 / BOS / 2026 / 025 / 006','BOS',2026,6,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(104,26,'03.11.01.01.001 / BOS / 2026 / 026 / 001','BOS',2026,1,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(105,26,'03.11.01.01.001 / BOS / 2026 / 026 / 002','BOS',2026,2,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(106,26,'03.11.01.01.001 / BOS / 2026 / 026 / 003','BOS',2026,3,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(107,26,'03.11.01.01.001 / BOS / 2026 / 026 / 004','BOS',2026,4,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(108,27,'03.11.01.01.001 / BOS / 2026 / 027 / 001','BOS',2026,1,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(109,27,'03.11.01.01.001 / BOS / 2026 / 027 / 002','BOS',2026,2,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(110,27,'03.11.01.01.001 / BOS / 2026 / 027 / 003','BOS',2026,3,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(111,28,'03.11.01.01.001 / BOS / 2026 / 028 / 001','BOS',2026,1,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(112,28,'03.11.01.01.001 / BOS / 2026 / 028 / 002','BOS',2026,2,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(113,28,'03.11.01.01.001 / BOS / 2026 / 028 / 003','BOS',2026,3,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(114,29,'03.11.01.01.001 / BOS / 2026 / 029 / 001','BOS',2026,1,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(115,29,'03.11.01.01.001 / BOS / 2026 / 029 / 002','BOS',2026,2,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(116,29,'03.11.01.01.001 / BOS / 2026 / 029 / 003','BOS',2026,3,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(117,29,'03.11.01.01.001 / BOS / 2026 / 029 / 004','BOS',2026,4,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(118,30,'03.11.01.01.001 / BOS / 2026 / 030 / 001','BOS',2026,1,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(119,30,'03.11.01.01.001 / BOS / 2026 / 030 / 002','BOS',2026,2,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(120,31,'03.11.01.01.001 / BOS / 2026 / 031 / 001','BOS',2026,1,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(121,31,'03.11.01.01.001 / BOS / 2026 / 031 / 002','BOS',2026,2,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(122,31,'03.11.01.01.001 / BOS / 2026 / 031 / 003','BOS',2026,3,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(123,32,'03.11.01.01.001 / BOS / 2026 / 032 / 001','BOS',2026,1,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(124,32,'03.11.01.01.001 / BOS / 2026 / 032 / 002','BOS',2026,2,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(125,32,'03.11.01.01.001 / BOS / 2026 / 032 / 003','BOS',2026,3,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(126,33,'03.11.01.01.001 / BOS / 2026 / 033 / 001','BOS',2026,1,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(127,33,'03.11.01.01.001 / BOS / 2026 / 033 / 002','BOS',2026,2,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(128,33,'03.11.01.01.001 / BOS / 2026 / 033 / 003','BOS',2026,3,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(129,33,'03.11.01.01.001 / BOS / 2026 / 033 / 004','BOS',2026,4,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(130,34,'03.11.01.01.001 / BOS / 2026 / 034 / 001','BOS',2026,1,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(131,34,'03.11.01.01.001 / BOS / 2026 / 034 / 002','BOS',2026,2,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(132,34,'03.11.01.01.001 / BOS / 2026 / 034 / 003','BOS',2026,3,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(133,34,'03.11.01.01.001 / BOS / 2026 / 034 / 004','BOS',2026,4,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(134,34,'03.11.01.01.001 / BOS / 2026 / 034 / 005','BOS',2026,5,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(135,35,'03.11.01.01.001 / BOS / 2026 / 035 / 001','BOS',2026,1,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(136,35,'03.11.01.01.001 / BOS / 2026 / 035 / 002','BOS',2026,2,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(137,35,'03.11.01.01.001 / BOS / 2026 / 035 / 003','BOS',2026,3,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(138,35,'03.11.01.01.001 / BOS / 2026 / 035 / 004','BOS',2026,4,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(139,36,'03.11.01.01.001 / BOS / 2026 / 036 / 001','BOS',2026,1,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(140,36,'03.11.01.01.001 / BOS / 2026 / 036 / 002','BOS',2026,2,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(141,36,'03.11.01.01.001 / BOS / 2026 / 036 / 003','BOS',2026,3,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(142,37,'03.11.01.01.001 / BOS / 2026 / 037 / 001','BOS',2026,1,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(143,37,'03.11.01.01.001 / BOS / 2026 / 037 / 002','BOS',2026,2,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(144,37,'03.11.01.01.001 / BOS / 2026 / 037 / 003','BOS',2026,3,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(145,38,'03.11.01.01.001 / BOS / 2026 / 038 / 001','BOS',2026,1,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(146,38,'03.11.01.01.001 / BOS / 2026 / 038 / 002','BOS',2026,2,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(147,39,'03.11.01.01.001 / BOS / 2026 / 039 / 001','BOS',2026,1,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(148,39,'03.11.01.01.001 / BOS / 2026 / 039 / 002','BOS',2026,2,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(149,39,'03.11.01.01.001 / BOS / 2026 / 039 / 003','BOS',2026,3,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(150,39,'03.11.01.01.001 / BOS / 2026 / 039 / 004','BOS',2026,4,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(151,40,'03.11.01.01.001 / BOS / 2026 / 040 / 001','BOS',2026,1,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(152,40,'03.11.01.01.001 / BOS / 2026 / 040 / 002','BOS',2026,2,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(153,40,'03.11.01.01.001 / BOS / 2026 / 040 / 003','BOS',2026,3,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(154,40,'03.11.01.01.001 / BOS / 2026 / 040 / 004','BOS',2026,4,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(155,41,'03.11.01.01.001 / BOS / 2026 / 041 / 001','BOS',2026,1,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(156,41,'03.11.01.01.001 / BOS / 2026 / 041 / 002','BOS',2026,2,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(157,41,'03.11.01.01.001 / BOS / 2026 / 041 / 003','BOS',2026,3,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(158,42,'03.11.01.01.001 / BOS / 2026 / 042 / 001','BOS',2026,1,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(159,42,'03.11.01.01.001 / BOS / 2026 / 042 / 002','BOS',2026,2,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(160,43,'03.11.01.01.001 / BOS / 2026 / 043 / 001','BOS',2026,1,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(161,43,'03.11.01.01.001 / BOS / 2026 / 043 / 002','BOS',2026,2,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(162,43,'03.11.01.01.001 / BOS / 2026 / 043 / 003','BOS',2026,3,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(163,44,'03.11.01.01.001 / BOS / 2026 / 044 / 001','BOS',2026,1,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(164,44,'03.11.01.01.001 / BOS / 2026 / 044 / 002','BOS',2026,2,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(165,45,'03.11.01.01.001 / BOS / 2026 / 045 / 001','BOS',2026,1,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(166,45,'03.11.01.01.001 / BOS / 2026 / 045 / 002','BOS',2026,2,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(167,45,'03.11.01.01.001 / BOS / 2026 / 045 / 003','BOS',2026,3,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(168,46,'03.11.01.01.001 / BOS / 2026 / 046 / 001','BOS',2026,1,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(169,46,'03.11.01.01.001 / BOS / 2026 / 046 / 002','BOS',2026,2,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(170,46,'03.11.01.01.001 / BOS / 2026 / 046 / 003','BOS',2026,3,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(171,47,'03.11.01.01.001 / BOS / 2026 / 047 / 001','BOS',2026,1,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(172,47,'03.11.01.01.001 / BOS / 2026 / 047 / 002','BOS',2026,2,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(173,47,'03.11.01.01.001 / BOS / 2026 / 047 / 003','BOS',2026,3,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(174,47,'03.11.01.01.001 / BOS / 2026 / 047 / 004','BOS',2026,4,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(175,48,'03.11.01.01.001 / BOS / 2026 / 048 / 001','BOS',2026,1,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(176,48,'03.11.01.01.001 / BOS / 2026 / 048 / 002','BOS',2026,2,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(177,48,'03.11.01.01.001 / BOS / 2026 / 048 / 003','BOS',2026,3,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(178,48,'03.11.01.01.001 / BOS / 2026 / 048 / 004','BOS',2026,4,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(179,49,'03.11.01.01.001 / BOS / 2026 / 049 / 001','BOS',2026,1,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(180,49,'03.11.01.01.001 / BOS / 2026 / 049 / 002','BOS',2026,2,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(181,49,'03.11.01.01.001 / BOS / 2026 / 049 / 003','BOS',2026,3,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(182,50,'03.11.01.01.001 / BOS / 2026 / 050 / 001','BOS',2026,1,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(183,50,'03.11.01.01.001 / BOS / 2026 / 050 / 002','BOS',2026,2,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(184,50,'03.11.01.01.001 / BOS / 2026 / 050 / 003','BOS',2026,3,'available','2026-06-17 02:13:00','2026-06-17 02:13:00'),(185,50,'03.11.01.01.001 / BOS / 2026 / 050 / 004','BOS',2026,4,'available','2026-06-17 02:13:00','2026-06-17 02:13:00');
/*!40000 ALTER TABLE `book_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `books`
--

DROP TABLE IF EXISTS `books`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `books` (
  `id` int NOT NULL AUTO_INCREMENT,
  `kode_buku` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
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
  KEY `is_active` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `books`
--

LOCK TABLES `books` WRITE;
/*!40000 ALTER TABLE `books` DISABLE KEYS */;
INSERT INTO `books` VALUES (1,'03.11.01.01.001 / BOS / 2026 / 001','Pemrograman Laravel Dasar','Andi Wijaya','Informatika Press','Panduan dasar membangun aplikasi dengan Laravel.',5,5,1,'2026-06-17 02:13:00','2026-06-17 02:13:00'),(2,'03.11.01.01.001 / BOS / 2026 / 002','Mastering PHP 8','Budi Santoso','Tekno Media','Pembahasan lengkap fitur terbaru PHP 8.',4,4,1,'2026-06-17 02:13:00','2026-06-17 02:13:00'),(3,'03.11.01.01.001 / BOS / 2026 / 003','Jaringan Komputer Fundamental','Rudi Hartono','Net Press','Konsep dasar jaringan komputer dan implementasi.',5,5,1,'2026-06-17 02:13:00','2026-06-17 02:13:00'),(4,'03.11.01.01.001 / BOS / 2026 / 004','Cyber Security Blue Team','Agus Saputra','Secure Media','Strategi pertahanan keamanan siber modern.',3,3,1,'2026-06-17 02:13:00','2026-06-17 02:13:00'),(5,'03.11.01.01.001 / BOS / 2026 / 005','Administrasi Server Linux','Deni Pratama','SysAdmin Publisher','Panduan konfigurasi dan manajemen server Linux.',4,4,1,'2026-06-17 02:13:00','2026-06-17 02:13:00'),(6,'03.11.01.01.001 / BOS / 2026 / 006','Belajar MySQL Cepat','Siti Rahma','Database Press','Tutorial praktis mengelola database MySQL.',6,6,1,'2026-06-17 02:13:00','2026-06-17 02:13:00'),(7,'03.11.01.01.001 / BOS / 2026 / 007','Membangun REST API','Rizky Maulana','API Media','Panduan membuat REST API scalable.',5,5,1,'2026-06-17 02:13:00','2026-06-17 02:13:00'),(8,'03.11.01.01.001 / BOS / 2026 / 008','Cloud Computing Dasar','Fajar Nugroho','Cloud Press','Pengenalan konsep dan arsitektur cloud.',3,3,1,'2026-06-17 02:13:00','2026-06-17 02:13:00'),(9,'03.11.01.01.001 / BOS / 2026 / 009','DevOps untuk Pemula','Yoga Pratama','Tech Media','Praktik CI/CD dan automasi deployment.',4,4,1,'2026-06-17 02:13:00','2026-06-17 02:13:00'),(10,'03.11.01.01.001 / BOS / 2026 / 010','Docker & Container','Arif Setiawan','Container Press','Implementasi containerisasi aplikasi.',5,5,1,'2026-06-17 02:13:00','2026-06-17 02:13:00'),(11,'03.11.01.01.001 / BOS / 2026 / 011','Kubernetes Practical Guide','Hendra Gunawan','Cloud Native Media','Manajemen container orchestration.',3,3,1,'2026-06-17 02:13:00','2026-06-17 02:13:00'),(12,'03.11.01.01.001 / BOS / 2026 / 012','Algoritma & Struktur Data','Dewi Lestari','Informatika Press','Dasar algoritma dan struktur data.',5,5,1,'2026-06-17 02:13:00','2026-06-17 02:13:00'),(13,'03.11.01.01.001 / BOS / 2026 / 013','Pemrograman Python','Tono Wibowo','Code Publisher','Belajar Python untuk berbagai kebutuhan.',6,6,1,'2026-06-17 02:13:00','2026-06-17 02:13:00'),(14,'03.11.01.01.001 / BOS / 2026 / 014','Data Science Praktis','Lukman Hakim','Data Media','Pengolahan data dan machine learning dasar.',3,3,1,'2026-06-17 02:13:00','2026-06-17 02:13:00'),(15,'03.11.01.01.001 / BOS / 2026 / 015','Machine Learning Dasar','Rina Kartika','AI Press','Konsep supervised dan unsupervised learning.',3,3,1,'2026-06-17 02:13:00','2026-06-17 02:13:00'),(16,'03.11.01.01.001 / BOS / 2026 / 016','Artificial Intelligence Modern','Yusuf Maulana','AI Media','Implementasi AI dalam industri.',2,2,1,'2026-06-17 02:13:00','2026-06-17 02:13:00'),(17,'03.11.01.01.001 / BOS / 2026 / 017','WordPress Developer Guide','Indra Saputra','CMS Press','Membangun theme dan plugin WordPress.',5,5,1,'2026-06-17 02:13:00','2026-06-17 02:13:00'),(18,'03.11.01.01.001 / BOS / 2026 / 018','SEO untuk Website','Mega Sari','Digital Media','Optimasi mesin pencari untuk website.',4,4,1,'2026-06-17 02:13:00','2026-06-17 02:13:00'),(19,'03.11.01.01.001 / BOS / 2026 / 019','UI/UX Design Principle','Ayu Permata','Creative Press','Prinsip desain antarmuka modern.',3,3,1,'2026-06-17 02:13:00','2026-06-17 02:13:00'),(20,'03.11.01.01.001 / BOS / 2026 / 020','ReactJS Modern','Bagus Firmansyah','Frontend Media','Membangun SPA dengan React.',4,4,1,'2026-06-17 02:13:00','2026-06-17 02:13:00'),(21,'03.11.01.01.001 / BOS / 2026 / 021','VueJS Framework','Randy Kurniawan','Frontend Media','Panduan lengkap VueJS.',3,3,1,'2026-06-17 02:13:00','2026-06-17 02:13:00'),(22,'03.11.01.01.001 / BOS / 2026 / 022','Angular Enterprise','Farhan Akbar','Enterprise Press','Pengembangan aplikasi enterprise Angular.',3,3,1,'2026-06-17 02:13:00','2026-06-17 02:13:00'),(23,'03.11.01.01.001 / BOS / 2026 / 023','Tailwind CSS','Dita Puspita','Design Press','Utility-first CSS framework.',4,4,1,'2026-06-17 02:13:00','2026-06-17 02:13:00'),(24,'03.11.01.01.001 / BOS / 2026 / 024','Bootstrap 5 Guide','Rahmat Hidayat','Frontend Press','Panduan cepat Bootstrap 5.',5,5,1,'2026-06-17 02:13:00','2026-06-17 02:13:00'),(25,'03.11.01.01.001 / BOS / 2026 / 025','Git & Version Control','Ilham Maulana','Dev Media','Manajemen kode dengan Git.',6,6,1,'2026-06-17 02:13:00','2026-06-17 02:13:00'),(26,'03.11.01.01.001 / BOS / 2026 / 026','CI/CD Implementation','Reza Fahlevi','DevOps Press','Pipeline otomatis deployment.',4,4,1,'2026-06-17 02:13:00','2026-06-17 02:13:00'),(27,'03.11.01.01.001 / BOS / 2026 / 027','Nginx Web Server','Galih Pratama','Server Press','Konfigurasi dan optimasi Nginx.',3,3,1,'2026-06-17 02:13:00','2026-06-17 02:13:00'),(28,'03.11.01.01.001 / BOS / 2026 / 028','Apache Server Advanced','Eko Susanto','Server Media','Manajemen Apache production.',3,3,1,'2026-06-17 02:13:00','2026-06-17 02:13:00'),(29,'03.11.01.01.001 / BOS / 2026 / 029','Firewall & Security','Bayu Saputra','Secure Press','Manajemen firewall jaringan.',4,4,1,'2026-06-17 02:13:00','2026-06-17 02:13:00'),(30,'03.11.01.01.001 / BOS / 2026 / 030','Penetration Testing','Hafiz Rahman','Security Media','Teknik dasar penetration testing.',2,2,1,'2026-06-17 02:13:00','2026-06-17 02:13:00'),(31,'03.11.01.01.001 / BOS / 2026 / 031','Kriptografi Modern','Satria Wijaya','Crypto Press','Konsep enkripsi dan keamanan data.',3,3,1,'2026-06-17 02:13:00','2026-06-17 02:13:00'),(32,'03.11.01.01.001 / BOS / 2026 / 032','Blockchain Teknologi','Naufal Rizki','Fintech Media','Implementasi blockchain.',3,3,1,'2026-06-17 02:13:00','2026-06-17 02:13:00'),(33,'03.11.01.01.001 / BOS / 2026 / 033','Fintech Revolution','Kevin Saputra','Finance Press','Transformasi digital finansial.',4,4,1,'2026-06-17 02:13:00','2026-06-17 02:13:00'),(34,'03.11.01.01.001 / BOS / 2026 / 034','Analisis Saham Fundamental','Rama Pratama','Market Press','Analisis laporan keuangan.',5,5,1,'2026-06-17 02:13:00','2026-06-17 02:13:00'),(35,'03.11.01.01.001 / BOS / 2026 / 035','Analisis Teknikal Trading','Yogi Firmansyah','Market Media','Strategi chart dan indikator.',4,4,1,'2026-06-17 02:13:00','2026-06-17 02:13:00'),(36,'03.11.01.01.001 / BOS / 2026 / 036','Manajemen Risiko Investasi','Andre Gunawan','Finance Media','Pengelolaan risiko investasi.',3,3,1,'2026-06-17 02:13:00','2026-06-17 02:13:00'),(37,'03.11.01.01.001 / BOS / 2026 / 037','Public Speaking IT','Citra Lestari','Skill Press','Komunikasi teknis efektif.',3,3,1,'2026-06-17 02:13:00','2026-06-17 02:13:00'),(38,'03.11.01.01.001 / BOS / 2026 / 038','Leadership Teknologi','Putri Andini','Leadership Media','Kepemimpinan dalam tim IT.',2,2,1,'2026-06-17 02:13:00','2026-06-17 02:13:00'),(39,'03.11.01.01.001 / BOS / 2026 / 039','Project Management IT','Arman Syah','PM Press','Manajemen proyek teknologi.',4,4,1,'2026-06-17 02:13:00','2026-06-17 02:13:00'),(40,'03.11.01.01.001 / BOS / 2026 / 040','Scrum & Agile','Nadia Salsabila','Agile Media','Metodologi agile development.',4,4,1,'2026-06-17 02:13:00','2026-06-17 02:13:00'),(41,'03.11.01.01.001 / BOS / 2026 / 041','Database Optimization','Haris Maulana','DB Press','Optimasi performa database.',3,3,1,'2026-06-17 02:13:00','2026-06-17 02:13:00'),(42,'03.11.01.01.001 / BOS / 2026 / 042','High Availability System','Iqbal Ramadhan','Infra Media','Arsitektur sistem high availability.',2,2,1,'2026-06-17 02:13:00','2026-06-17 02:13:00'),(43,'03.11.01.01.001 / BOS / 2026 / 043','Monitoring & Logging','Sandi Prakoso','Ops Press','Implementasi monitoring sistem.',3,3,1,'2026-06-17 02:13:00','2026-06-17 02:13:00'),(44,'03.11.01.01.001 / BOS / 2026 / 044','Microservices Architecture','Rico Saputra','Arch Media','Desain arsitektur microservices.',2,2,1,'2026-06-17 02:13:00','2026-06-17 02:13:00'),(45,'03.11.01.01.001 / BOS / 2026 / 045','API Security','Fikri Ananda','Security Press','Keamanan API production.',3,3,1,'2026-06-17 02:13:00','2026-06-17 02:13:00'),(46,'03.11.01.01.001 / BOS / 2026 / 046','Cloudflare Practical','Rafi Maulana','Cloud Media','Optimasi dan keamanan Cloudflare.',3,3,1,'2026-06-17 02:13:00','2026-06-17 02:13:00'),(47,'03.11.01.01.001 / BOS / 2026 / 047','Linux Hardening','Bagas Prasetyo','Security Media','Pengamanan server Linux.',4,4,1,'2026-06-17 02:13:00','2026-06-17 02:13:00'),(48,'03.11.01.01.001 / BOS / 2026 / 048','Backup & Disaster Recovery','Seno Aji','Infra Press','Strategi backup dan recovery.',4,4,1,'2026-06-17 02:13:00','2026-06-17 02:13:00'),(49,'03.11.01.01.001 / BOS / 2026 / 049','Big Data Processing','Wahyu Nugraha','Data Press','Pemrosesan data skala besar.',3,3,1,'2026-06-17 02:13:00','2026-06-17 02:13:00'),(50,'03.11.01.01.001 / BOS / 2026 / 050','Enterprise Architecture','Rizal Hidayat','Enterprise Media','Perancangan arsitektur enterprise.',4,4,1,'2026-06-17 02:13:00','2026-06-17 02:13:00');
/*!40000 ALTER TABLE `books` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `category`
--

DROP TABLE IF EXISTS `category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `category` (
  `id` int NOT NULL AUTO_INCREMENT,
  `code_ddc` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `desc_ddc` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `category`
--

LOCK TABLES `category` WRITE;
/*!40000 ALTER TABLE `category` DISABLE KEYS */;
INSERT INTO `category` VALUES (1,'000','Karya Umum, Ilmu Komputer, Informasi'),(2,'100','Filsafat & Psikologi'),(3,'200','Agama'),(4,'300','Ilmu Sosial'),(5,'400','Bahasa'),(6,'500','Ilmu Pengetahuan Alam / Sains'),(7,'600','Teknologi / Ilmu Terapan'),(8,'700','Seni & Rekreasi'),(9,'800','Kesusastraan'),(10,'900','Sejarah & Geografi'),(11,'000','Karya Umum, Ilmu Komputer, Informasi'),(12,'100','Filsafat & Psikologi'),(13,'200','Agama'),(14,'300','Ilmu Sosial'),(15,'400','Bahasa'),(16,'500','Ilmu Pengetahuan Alam / Sains'),(17,'600','Teknologi / Ilmu Terapan'),(18,'700','Seni & Rekreasi'),(19,'800','Kesusastraan'),(20,'900','Sejarah & Geografi'),(21,'000','Karya Umum, Ilmu Komputer, Informasi'),(22,'100','Filsafat & Psikologi'),(23,'200','Agama'),(24,'300','Ilmu Sosial'),(25,'400','Bahasa'),(26,'500','Ilmu Pengetahuan Alam / Sains'),(27,'600','Teknologi / Ilmu Terapan'),(28,'700','Seni & Rekreasi'),(29,'800','Kesusastraan'),(30,'900','Sejarah & Geografi');
/*!40000 ALTER TABLE `category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `class`
--

DROP TABLE IF EXISTS `class`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `class` (
  `id` int NOT NULL AUTO_INCREMENT,
  `class_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `class`
--

LOCK TABLES `class` WRITE;
/*!40000 ALTER TABLE `class` DISABLE KEYS */;
INSERT INTO `class` VALUES (1,'VII-A'),(2,'VII-B'),(3,'VII-C'),(4,'VII-D'),(5,'VII-E'),(6,'VIII-A'),(7,'VIII-B'),(8,'VIII-C'),(9,'VIII-D'),(10,'VIII-E'),(11,'IX-A'),(12,'IX-B'),(13,'IX-C'),(14,'IX-D'),(15,'IX-E'),(16,'VII-A'),(17,'VII-B'),(18,'VII-C'),(19,'VII-D'),(20,'VII-E'),(21,'VIII-A'),(22,'VIII-B'),(23,'VIII-C'),(24,'VIII-D'),(25,'VIII-E'),(26,'IX-A'),(27,'IX-B'),(28,'IX-C'),(29,'IX-D'),(30,'IX-E'),(31,'VII-A'),(32,'VII-B'),(33,'VII-C'),(34,'VII-D'),(35,'VII-E'),(36,'VIII-A'),(37,'VIII-B'),(38,'VIII-C'),(39,'VIII-D'),(40,'VIII-E'),(41,'IX-A'),(42,'IX-B'),(43,'IX-C'),(44,'IX-D'),(45,'IX-E');
/*!40000 ALTER TABLE `class` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `version` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `class` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `group` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `namespace` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `time` int NOT NULL,
  `batch` int unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (17,'2026-02-17-113210','App\\Database\\Migrations\\CreateUsers','default','App',1781623736,1),(18,'2026-02-17-113310','App\\Database\\Migrations\\CreateBooks','default','App',1781623736,1),(19,'2026-02-17-113410','App\\Database\\Migrations\\CreateTransactions','default','App',1781623736,1),(20,'2026-02-17-113510','App\\Database\\Migrations\\CreateActivityLogs','default','App',1781623736,1),(21,'2026-02-21-000004','App\\Database\\Migrations\\CreateTransactionDetails','default','App',1781623736,1),(22,'2026-03-09-000003','App\\Database\\Migrations\\CreateBookItems','default','App',1781623736,1),(23,'2026-03-09-000007','App\\Database\\Migrations\\CreateAppSettings','default','App',1781623736,1),(24,'2026-03-09-161011','App\\Database\\Migrations\\MasterClass','default','App',1781623736,1),(25,'2026-06-17-000001','App\\Database\\Migrations\\AddPhase2Columns','default','App',1781653212,2);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `source_fund`
--

DROP TABLE IF EXISTS `source_fund`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `source_fund` (
  `id` int NOT NULL AUTO_INCREMENT,
  `source_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `source_fund`
--

LOCK TABLES `source_fund` WRITE;
/*!40000 ALTER TABLE `source_fund` DISABLE KEYS */;
INSERT INTO `source_fund` VALUES (1,'BOS'),(2,'APBD'),(3,'APBN'),(4,'Hibah / Donasi'),(5,'Pembelian Pribadi'),(6,'BOS'),(7,'APBD'),(8,'APBN'),(9,'Hibah / Donasi'),(10,'Pembelian Pribadi'),(11,'BOS'),(12,'APBD'),(13,'APBN'),(14,'Hibah / Donasi'),(15,'Pembelian Pribadi');
/*!40000 ALTER TABLE `source_fund` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transaction_details`
--

DROP TABLE IF EXISTS `transaction_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `transaction_details` (
  `id` int NOT NULL AUTO_INCREMENT,
  `transaction_id` int NOT NULL,
  `book_id` int NOT NULL,
  `book_item_id` int DEFAULT NULL,
  `is_lost` tinyint(1) NOT NULL DEFAULT '0',
  `lost_settlement_status` enum('pending','replaced') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `lost_note` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `transaction_id_book_id` (`transaction_id`,`book_id`),
  KEY `transaction_id` (`transaction_id`),
  KEY `book_id` (`book_id`),
  KEY `is_lost` (`is_lost`),
  KEY `fk_transaction_details_book_item` (`book_item_id`),
  CONSTRAINT `fk_transaction_details_book_item` FOREIGN KEY (`book_item_id`) REFERENCES `book_items` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `transaction_details_book_id_foreign` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `transaction_details_transaction_id_foreign` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transaction_details`
--

LOCK TABLES `transaction_details` WRITE;
/*!40000 ALTER TABLE `transaction_details` DISABLE KEYS */;
INSERT INTO `transaction_details` VALUES (1,1,1,1,1,'replaced','Menggantikan salinan fisik [03.11.01.01.001 / BOS / 2026 / 001 / 001] yang hilang. Kode inventaris baru: [03.11.01.01.001 / BOS / 2026 / 001 / 007]. Catatan: Buku pengganti diserahkan siswa','2026-06-16 23:46:35');
/*!40000 ALTER TABLE `transaction_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transactions`
--

DROP TABLE IF EXISTS `transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `transactions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `kode_transaksi` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `jenis_peminjaman` enum('pendek','panjang') COLLATE utf8mb4_general_ci DEFAULT 'pendek',
  `user_id` int NOT NULL,
  `borrowed_by` int NOT NULL,
  `returned_by` int DEFAULT NULL,
  `tanggal_pinjam` date NOT NULL,
  `jatuh_tempo` date NOT NULL,
  `tanggal_kembali` date DEFAULT NULL,
  `status` enum('dipinjam','terlambat','selesai') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'dipinjam',
  `total_denda` decimal(12,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode_transaksi` (`kode_transaksi`),
  KEY `transactions_borrowed_by_foreign` (`borrowed_by`),
  KEY `transactions_returned_by_foreign` (`returned_by`),
  KEY `user_id` (`user_id`),
  KEY `status` (`status`),
  KEY `jatuh_tempo` (`jatuh_tempo`),
  CONSTRAINT `transactions_borrowed_by_foreign` FOREIGN KEY (`borrowed_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `transactions_returned_by_foreign` FOREIGN KEY (`returned_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE SET NULL,
  CONSTRAINT `transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transactions`
--

LOCK TABLES `transactions` WRITE;
/*!40000 ALTER TABLE `transactions` DISABLE KEYS */;
INSERT INTO `transactions` VALUES (1,'TRX-2026061706463588','panjang',7,3,3,'2026-06-17','2026-12-14','2026-06-17','selesai',0.00,'2026-06-16 23:46:35','2026-06-16 23:47:07');
/*!40000 ALTER TABLE `transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `member_code` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `member_code_nation` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `class` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('admin','staff','member') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'member',
  `jenis_pengguna` enum('siswa','guru','pegawai') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'siswa',
  `is_validated` tinyint(1) NOT NULL DEFAULT '0',
  `is_archived` tinyint(1) NOT NULL DEFAULT '0',
  `must_change_password` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `last_login` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `member_code` (`member_code`),
  UNIQUE KEY `member_code_nation` (`member_code_nation`),
  KEY `role` (`role`),
  KEY `is_validated` (`is_validated`),
  KEY `jenis_pengguna` (`jenis_pengguna`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Super Administrator','ADM-001',NULL,NULL,'admin','$2y$12$JAlbrsFhjsp1GC6we9iJTucWKqIOlBBhvWPDTcw5VAdW79V3vm7ty','admin','guru',1,0,0,'2026-06-16 15:29:06','2026-06-16 15:29:06',NULL),(2,'Staff Perpustakaan','STF-001',NULL,NULL,'staff','$2y$12$/yWdlv8nvGDSuck1k4FpUexV.yujo98YiWsSRxBsqBz8lHpGNfdS2','staff','pegawai',1,0,0,'2026-06-16 15:29:07','2026-06-16 15:29:07',NULL),(3,'Lina Petugas','STF-002',NULL,NULL,'lina_petugas','$2y$12$UrA3BJrEmLFd1o5JF63Fq.Os5y2aSv2AAkSiYTnoWzLLSOmFCvRaa','staff','pegawai',1,0,0,'2026-06-16 15:29:09','2026-06-16 15:29:09',NULL),(4,'Bambang Pegawai','PEG-001',NULL,NULL,'bambang_pegawai','$2y$12$l2CGRKsLC/uzAHJ9fQH0HeBNJeENUVOGwXm4qFrLxBY6PG4Z9Det6','member','pegawai',1,0,0,'2026-06-16 15:29:09','2026-06-16 15:29:09',NULL),(5,'Dra. Endang Guru','GRU-001',NULL,NULL,'endang_guru','$2y$12$kRe4V4F.cPhrghnFE8v3r.SjwcE/9uY/aGkqmUk/z6pE9I8onsmCi','member','guru',1,0,0,'2026-06-16 15:29:09','2026-06-16 15:29:09',NULL),(6,'Siti Rahmawati (Siswa)','SSW-001',NULL,NULL,'siti_siswa','$2y$12$bTH8kL2uUXXKpEfmoume8utn3cuycd1XnJT/libJBxJIzDlp46yDy','member','siswa',1,0,0,'2026-06-16 15:29:09','2026-06-16 15:29:09',NULL),(7,'Rian Hidayat (Siswa)','SSW-002',NULL,NULL,'rian_siswa','$2y$12$LR1XHYgi0s7d9LLNlk.fRuL3DIMSlLH.tYgm4ipMFGrbCaSoT.rmO','member','siswa',1,0,0,'2026-06-16 15:29:09','2026-06-16 15:29:09',NULL),(8,'Budi Siswa','12345','12345678','VII-A','budi_siswa','$2y$12$OIAitiwp0DPVsTgCOEiVuuj.o7xcdXTM7kIcKDSDDjmDkzG65c24.','member','siswa',1,0,0,'2026-06-16 15:30:56','2026-06-16 15:32:16',NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-17  9:13:07
