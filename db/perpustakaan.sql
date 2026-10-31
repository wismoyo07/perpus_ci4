-- MySQL dump 10.13  Distrib 8.0.30, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: perpus_seed_build
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_logs`
--

LOCK TABLES `activity_logs` WRITE;
/*!40000 ALTER TABLE `activity_logs` DISABLE KEYS */;
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
  `kode_barang_buku` varchar(30) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '03.11.01.01.001',
  `maks_hari_denda` int NOT NULL DEFAULT '10',
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
INSERT INTO `app_settings` VALUES (1,NULL,'','03.11.01.01.001',10,'Perpustakaan Sekolah','','','','','','2026-10-31 10:50:12','2026-10-31 10:50:12');
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
INSERT INTO `book_items` VALUES (1,1,'03.11.01.01.001 / BOS / 2026 / 001 / 001','BOS',2026,1,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(2,1,'03.11.01.01.001 / BOS / 2026 / 001 / 002','BOS',2026,2,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(3,1,'03.11.01.01.001 / BOS / 2026 / 001 / 003','BOS',2026,3,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(4,1,'03.11.01.01.001 / BOS / 2026 / 001 / 004','BOS',2026,4,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(5,1,'03.11.01.01.001 / BOS / 2026 / 001 / 005','BOS',2026,5,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(6,2,'03.11.01.01.001 / BOS / 2026 / 002 / 001','BOS',2026,1,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(7,2,'03.11.01.01.001 / BOS / 2026 / 002 / 002','BOS',2026,2,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(8,2,'03.11.01.01.001 / BOS / 2026 / 002 / 003','BOS',2026,3,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(9,2,'03.11.01.01.001 / BOS / 2026 / 002 / 004','BOS',2026,4,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(10,3,'03.11.01.01.001 / BOS / 2026 / 003 / 001','BOS',2026,1,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(11,3,'03.11.01.01.001 / BOS / 2026 / 003 / 002','BOS',2026,2,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(12,3,'03.11.01.01.001 / BOS / 2026 / 003 / 003','BOS',2026,3,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(13,3,'03.11.01.01.001 / BOS / 2026 / 003 / 004','BOS',2026,4,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(14,3,'03.11.01.01.001 / BOS / 2026 / 003 / 005','BOS',2026,5,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(15,4,'03.11.01.01.001 / BOS / 2026 / 004 / 001','BOS',2026,1,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(16,4,'03.11.01.01.001 / BOS / 2026 / 004 / 002','BOS',2026,2,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(17,4,'03.11.01.01.001 / BOS / 2026 / 004 / 003','BOS',2026,3,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(18,5,'03.11.01.01.001 / BOS / 2026 / 005 / 001','BOS',2026,1,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(19,5,'03.11.01.01.001 / BOS / 2026 / 005 / 002','BOS',2026,2,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(20,5,'03.11.01.01.001 / BOS / 2026 / 005 / 003','BOS',2026,3,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(21,5,'03.11.01.01.001 / BOS / 2026 / 005 / 004','BOS',2026,4,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(22,6,'03.11.01.01.001 / BOS / 2026 / 006 / 001','BOS',2026,1,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(23,6,'03.11.01.01.001 / BOS / 2026 / 006 / 002','BOS',2026,2,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(24,6,'03.11.01.01.001 / BOS / 2026 / 006 / 003','BOS',2026,3,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(25,6,'03.11.01.01.001 / BOS / 2026 / 006 / 004','BOS',2026,4,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(26,6,'03.11.01.01.001 / BOS / 2026 / 006 / 005','BOS',2026,5,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(27,6,'03.11.01.01.001 / BOS / 2026 / 006 / 006','BOS',2026,6,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(28,7,'03.11.01.01.001 / BOS / 2026 / 007 / 001','BOS',2026,1,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(29,7,'03.11.01.01.001 / BOS / 2026 / 007 / 002','BOS',2026,2,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(30,7,'03.11.01.01.001 / BOS / 2026 / 007 / 003','BOS',2026,3,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(31,7,'03.11.01.01.001 / BOS / 2026 / 007 / 004','BOS',2026,4,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(32,7,'03.11.01.01.001 / BOS / 2026 / 007 / 005','BOS',2026,5,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(33,8,'03.11.01.01.001 / BOS / 2026 / 008 / 001','BOS',2026,1,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(34,8,'03.11.01.01.001 / BOS / 2026 / 008 / 002','BOS',2026,2,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(35,8,'03.11.01.01.001 / BOS / 2026 / 008 / 003','BOS',2026,3,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(36,9,'03.11.01.01.001 / BOS / 2026 / 009 / 001','BOS',2026,1,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(37,9,'03.11.01.01.001 / BOS / 2026 / 009 / 002','BOS',2026,2,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(38,9,'03.11.01.01.001 / BOS / 2026 / 009 / 003','BOS',2026,3,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(39,9,'03.11.01.01.001 / BOS / 2026 / 009 / 004','BOS',2026,4,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(40,10,'03.11.01.01.001 / BOS / 2026 / 010 / 001','BOS',2026,1,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(41,10,'03.11.01.01.001 / BOS / 2026 / 010 / 002','BOS',2026,2,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(42,10,'03.11.01.01.001 / BOS / 2026 / 010 / 003','BOS',2026,3,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(43,10,'03.11.01.01.001 / BOS / 2026 / 010 / 004','BOS',2026,4,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(44,10,'03.11.01.01.001 / BOS / 2026 / 010 / 005','BOS',2026,5,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(45,11,'03.11.01.01.001 / BOS / 2026 / 011 / 001','BOS',2026,1,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(46,11,'03.11.01.01.001 / BOS / 2026 / 011 / 002','BOS',2026,2,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(47,11,'03.11.01.01.001 / BOS / 2026 / 011 / 003','BOS',2026,3,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(48,12,'03.11.01.01.001 / BOS / 2026 / 012 / 001','BOS',2026,1,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(49,12,'03.11.01.01.001 / BOS / 2026 / 012 / 002','BOS',2026,2,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(50,12,'03.11.01.01.001 / BOS / 2026 / 012 / 003','BOS',2026,3,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(51,12,'03.11.01.01.001 / BOS / 2026 / 012 / 004','BOS',2026,4,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(52,12,'03.11.01.01.001 / BOS / 2026 / 012 / 005','BOS',2026,5,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(53,13,'03.11.01.01.001 / BOS / 2026 / 013 / 001','BOS',2026,1,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(54,13,'03.11.01.01.001 / BOS / 2026 / 013 / 002','BOS',2026,2,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(55,13,'03.11.01.01.001 / BOS / 2026 / 013 / 003','BOS',2026,3,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(56,13,'03.11.01.01.001 / BOS / 2026 / 013 / 004','BOS',2026,4,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(57,13,'03.11.01.01.001 / BOS / 2026 / 013 / 005','BOS',2026,5,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(58,13,'03.11.01.01.001 / BOS / 2026 / 013 / 006','BOS',2026,6,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(59,14,'03.11.01.01.001 / BOS / 2026 / 014 / 001','BOS',2026,1,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(60,14,'03.11.01.01.001 / BOS / 2026 / 014 / 002','BOS',2026,2,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(61,14,'03.11.01.01.001 / BOS / 2026 / 014 / 003','BOS',2026,3,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(62,15,'03.11.01.01.001 / BOS / 2026 / 015 / 001','BOS',2026,1,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(63,15,'03.11.01.01.001 / BOS / 2026 / 015 / 002','BOS',2026,2,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(64,15,'03.11.01.01.001 / BOS / 2026 / 015 / 003','BOS',2026,3,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(65,16,'03.11.01.01.001 / BOS / 2026 / 016 / 001','BOS',2026,1,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(66,16,'03.11.01.01.001 / BOS / 2026 / 016 / 002','BOS',2026,2,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(67,17,'03.11.01.01.001 / BOS / 2026 / 017 / 001','BOS',2026,1,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(68,17,'03.11.01.01.001 / BOS / 2026 / 017 / 002','BOS',2026,2,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(69,17,'03.11.01.01.001 / BOS / 2026 / 017 / 003','BOS',2026,3,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(70,17,'03.11.01.01.001 / BOS / 2026 / 017 / 004','BOS',2026,4,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(71,17,'03.11.01.01.001 / BOS / 2026 / 017 / 005','BOS',2026,5,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(72,18,'03.11.01.01.001 / BOS / 2026 / 018 / 001','BOS',2026,1,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(73,18,'03.11.01.01.001 / BOS / 2026 / 018 / 002','BOS',2026,2,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(74,18,'03.11.01.01.001 / BOS / 2026 / 018 / 003','BOS',2026,3,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(75,18,'03.11.01.01.001 / BOS / 2026 / 018 / 004','BOS',2026,4,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(76,19,'03.11.01.01.001 / BOS / 2026 / 019 / 001','BOS',2026,1,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(77,19,'03.11.01.01.001 / BOS / 2026 / 019 / 002','BOS',2026,2,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(78,19,'03.11.01.01.001 / BOS / 2026 / 019 / 003','BOS',2026,3,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(79,20,'03.11.01.01.001 / BOS / 2026 / 020 / 001','BOS',2026,1,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(80,20,'03.11.01.01.001 / BOS / 2026 / 020 / 002','BOS',2026,2,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(81,20,'03.11.01.01.001 / BOS / 2026 / 020 / 003','BOS',2026,3,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(82,20,'03.11.01.01.001 / BOS / 2026 / 020 / 004','BOS',2026,4,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(83,21,'03.11.01.01.001 / BOS / 2026 / 021 / 001','BOS',2026,1,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(84,21,'03.11.01.01.001 / BOS / 2026 / 021 / 002','BOS',2026,2,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(85,21,'03.11.01.01.001 / BOS / 2026 / 021 / 003','BOS',2026,3,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(86,22,'03.11.01.01.001 / BOS / 2026 / 022 / 001','BOS',2026,1,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(87,22,'03.11.01.01.001 / BOS / 2026 / 022 / 002','BOS',2026,2,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(88,22,'03.11.01.01.001 / BOS / 2026 / 022 / 003','BOS',2026,3,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(89,23,'03.11.01.01.001 / BOS / 2026 / 023 / 001','BOS',2026,1,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(90,23,'03.11.01.01.001 / BOS / 2026 / 023 / 002','BOS',2026,2,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(91,23,'03.11.01.01.001 / BOS / 2026 / 023 / 003','BOS',2026,3,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(92,23,'03.11.01.01.001 / BOS / 2026 / 023 / 004','BOS',2026,4,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(93,24,'03.11.01.01.001 / BOS / 2026 / 024 / 001','BOS',2026,1,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(94,24,'03.11.01.01.001 / BOS / 2026 / 024 / 002','BOS',2026,2,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(95,24,'03.11.01.01.001 / BOS / 2026 / 024 / 003','BOS',2026,3,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(96,24,'03.11.01.01.001 / BOS / 2026 / 024 / 004','BOS',2026,4,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(97,24,'03.11.01.01.001 / BOS / 2026 / 024 / 005','BOS',2026,5,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(98,25,'03.11.01.01.001 / BOS / 2026 / 025 / 001','BOS',2026,1,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(99,25,'03.11.01.01.001 / BOS / 2026 / 025 / 002','BOS',2026,2,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(100,25,'03.11.01.01.001 / BOS / 2026 / 025 / 003','BOS',2026,3,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(101,25,'03.11.01.01.001 / BOS / 2026 / 025 / 004','BOS',2026,4,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(102,25,'03.11.01.01.001 / BOS / 2026 / 025 / 005','BOS',2026,5,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(103,25,'03.11.01.01.001 / BOS / 2026 / 025 / 006','BOS',2026,6,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(104,26,'03.11.01.01.001 / BOS / 2026 / 026 / 001','BOS',2026,1,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(105,26,'03.11.01.01.001 / BOS / 2026 / 026 / 002','BOS',2026,2,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(106,26,'03.11.01.01.001 / BOS / 2026 / 026 / 003','BOS',2026,3,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(107,26,'03.11.01.01.001 / BOS / 2026 / 026 / 004','BOS',2026,4,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(108,27,'03.11.01.01.001 / BOS / 2026 / 027 / 001','BOS',2026,1,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(109,27,'03.11.01.01.001 / BOS / 2026 / 027 / 002','BOS',2026,2,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(110,27,'03.11.01.01.001 / BOS / 2026 / 027 / 003','BOS',2026,3,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(111,28,'03.11.01.01.001 / BOS / 2026 / 028 / 001','BOS',2026,1,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(112,28,'03.11.01.01.001 / BOS / 2026 / 028 / 002','BOS',2026,2,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(113,28,'03.11.01.01.001 / BOS / 2026 / 028 / 003','BOS',2026,3,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(114,29,'03.11.01.01.001 / BOS / 2026 / 029 / 001','BOS',2026,1,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(115,29,'03.11.01.01.001 / BOS / 2026 / 029 / 002','BOS',2026,2,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(116,29,'03.11.01.01.001 / BOS / 2026 / 029 / 003','BOS',2026,3,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(117,29,'03.11.01.01.001 / BOS / 2026 / 029 / 004','BOS',2026,4,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(118,30,'03.11.01.01.001 / BOS / 2026 / 030 / 001','BOS',2026,1,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(119,30,'03.11.01.01.001 / BOS / 2026 / 030 / 002','BOS',2026,2,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(120,31,'03.11.01.01.001 / BOS / 2026 / 031 / 001','BOS',2026,1,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(121,31,'03.11.01.01.001 / BOS / 2026 / 031 / 002','BOS',2026,2,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(122,31,'03.11.01.01.001 / BOS / 2026 / 031 / 003','BOS',2026,3,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(123,32,'03.11.01.01.001 / BOS / 2026 / 032 / 001','BOS',2026,1,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(124,32,'03.11.01.01.001 / BOS / 2026 / 032 / 002','BOS',2026,2,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(125,32,'03.11.01.01.001 / BOS / 2026 / 032 / 003','BOS',2026,3,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(126,33,'03.11.01.01.001 / BOS / 2026 / 033 / 001','BOS',2026,1,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(127,33,'03.11.01.01.001 / BOS / 2026 / 033 / 002','BOS',2026,2,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(128,33,'03.11.01.01.001 / BOS / 2026 / 033 / 003','BOS',2026,3,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(129,33,'03.11.01.01.001 / BOS / 2026 / 033 / 004','BOS',2026,4,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(130,34,'03.11.01.01.001 / BOS / 2026 / 034 / 001','BOS',2026,1,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(131,34,'03.11.01.01.001 / BOS / 2026 / 034 / 002','BOS',2026,2,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(132,34,'03.11.01.01.001 / BOS / 2026 / 034 / 003','BOS',2026,3,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(133,34,'03.11.01.01.001 / BOS / 2026 / 034 / 004','BOS',2026,4,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(134,34,'03.11.01.01.001 / BOS / 2026 / 034 / 005','BOS',2026,5,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(135,35,'03.11.01.01.001 / BOS / 2026 / 035 / 001','BOS',2026,1,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(136,35,'03.11.01.01.001 / BOS / 2026 / 035 / 002','BOS',2026,2,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(137,35,'03.11.01.01.001 / BOS / 2026 / 035 / 003','BOS',2026,3,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(138,35,'03.11.01.01.001 / BOS / 2026 / 035 / 004','BOS',2026,4,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(139,36,'03.11.01.01.001 / BOS / 2026 / 036 / 001','BOS',2026,1,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(140,36,'03.11.01.01.001 / BOS / 2026 / 036 / 002','BOS',2026,2,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(141,36,'03.11.01.01.001 / BOS / 2026 / 036 / 003','BOS',2026,3,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(142,37,'03.11.01.01.001 / BOS / 2026 / 037 / 001','BOS',2026,1,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(143,37,'03.11.01.01.001 / BOS / 2026 / 037 / 002','BOS',2026,2,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(144,37,'03.11.01.01.001 / BOS / 2026 / 037 / 003','BOS',2026,3,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(145,38,'03.11.01.01.001 / BOS / 2026 / 038 / 001','BOS',2026,1,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(146,38,'03.11.01.01.001 / BOS / 2026 / 038 / 002','BOS',2026,2,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(147,39,'03.11.01.01.001 / BOS / 2026 / 039 / 001','BOS',2026,1,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(148,39,'03.11.01.01.001 / BOS / 2026 / 039 / 002','BOS',2026,2,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(149,39,'03.11.01.01.001 / BOS / 2026 / 039 / 003','BOS',2026,3,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(150,39,'03.11.01.01.001 / BOS / 2026 / 039 / 004','BOS',2026,4,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(151,40,'03.11.01.01.001 / BOS / 2026 / 040 / 001','BOS',2026,1,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(152,40,'03.11.01.01.001 / BOS / 2026 / 040 / 002','BOS',2026,2,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(153,40,'03.11.01.01.001 / BOS / 2026 / 040 / 003','BOS',2026,3,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(154,40,'03.11.01.01.001 / BOS / 2026 / 040 / 004','BOS',2026,4,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(155,41,'03.11.01.01.001 / BOS / 2026 / 041 / 001','BOS',2026,1,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(156,41,'03.11.01.01.001 / BOS / 2026 / 041 / 002','BOS',2026,2,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(157,41,'03.11.01.01.001 / BOS / 2026 / 041 / 003','BOS',2026,3,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(158,42,'03.11.01.01.001 / BOS / 2026 / 042 / 001','BOS',2026,1,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(159,42,'03.11.01.01.001 / BOS / 2026 / 042 / 002','BOS',2026,2,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(160,43,'03.11.01.01.001 / BOS / 2026 / 043 / 001','BOS',2026,1,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(161,43,'03.11.01.01.001 / BOS / 2026 / 043 / 002','BOS',2026,2,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(162,43,'03.11.01.01.001 / BOS / 2026 / 043 / 003','BOS',2026,3,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(163,44,'03.11.01.01.001 / BOS / 2026 / 044 / 001','BOS',2026,1,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(164,44,'03.11.01.01.001 / BOS / 2026 / 044 / 002','BOS',2026,2,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(165,45,'03.11.01.01.001 / BOS / 2026 / 045 / 001','BOS',2026,1,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(166,45,'03.11.01.01.001 / BOS / 2026 / 045 / 002','BOS',2026,2,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(167,45,'03.11.01.01.001 / BOS / 2026 / 045 / 003','BOS',2026,3,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(168,46,'03.11.01.01.001 / BOS / 2026 / 046 / 001','BOS',2026,1,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(169,46,'03.11.01.01.001 / BOS / 2026 / 046 / 002','BOS',2026,2,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(170,46,'03.11.01.01.001 / BOS / 2026 / 046 / 003','BOS',2026,3,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(171,47,'03.11.01.01.001 / BOS / 2026 / 047 / 001','BOS',2026,1,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(172,47,'03.11.01.01.001 / BOS / 2026 / 047 / 002','BOS',2026,2,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(173,47,'03.11.01.01.001 / BOS / 2026 / 047 / 003','BOS',2026,3,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(174,47,'03.11.01.01.001 / BOS / 2026 / 047 / 004','BOS',2026,4,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(175,48,'03.11.01.01.001 / BOS / 2026 / 048 / 001','BOS',2026,1,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(176,48,'03.11.01.01.001 / BOS / 2026 / 048 / 002','BOS',2026,2,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(177,48,'03.11.01.01.001 / BOS / 2026 / 048 / 003','BOS',2026,3,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(178,48,'03.11.01.01.001 / BOS / 2026 / 048 / 004','BOS',2026,4,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(179,49,'03.11.01.01.001 / BOS / 2026 / 049 / 001','BOS',2026,1,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(180,49,'03.11.01.01.001 / BOS / 2026 / 049 / 002','BOS',2026,2,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(181,49,'03.11.01.01.001 / BOS / 2026 / 049 / 003','BOS',2026,3,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(182,50,'03.11.01.01.001 / BOS / 2026 / 050 / 001','BOS',2026,1,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(183,50,'03.11.01.01.001 / BOS / 2026 / 050 / 002','BOS',2026,2,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(184,50,'03.11.01.01.001 / BOS / 2026 / 050 / 003','BOS',2026,3,'available','2026-10-31 10:50:12','2026-10-31 10:50:12'),(185,50,'03.11.01.01.001 / BOS / 2026 / 050 / 004','BOS',2026,4,'available','2026-10-31 10:50:12','2026-10-31 10:50:12');
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
  `category_id` int DEFAULT NULL,
  `ddc_number` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `stok_total` int NOT NULL DEFAULT '0',
  `stok_tersedia` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode_buku` (`kode_buku`),
  KEY `is_active` (`is_active`),
  KEY `idx_books_category_id` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `books`
--

LOCK TABLES `books` WRITE;
/*!40000 ALTER TABLE `books` DISABLE KEYS */;
INSERT INTO `books` VALUES (1,'03.11.01.01.001 / BOS / 2026 / 001','Pemrograman Laravel Dasar','Andi Wijaya','Informatika Press','Panduan dasar membangun aplikasi dengan Laravel.',91,'005.133',5,5,1,'2026-10-31 10:50:12','2026-10-31 10:50:12'),(2,'03.11.01.01.001 / BOS / 2026 / 002','Mastering PHP 8','Budi Santoso','Tekno Media','Pembahasan lengkap fitur terbaru PHP 8.',91,'005.133',4,4,1,'2026-10-31 10:50:12','2026-10-31 10:50:12'),(3,'03.11.01.01.001 / BOS / 2026 / 003','Jaringan Komputer Fundamental','Rudi Hartono','Net Press','Konsep dasar jaringan komputer dan implementasi.',91,'004.6',5,5,1,'2026-10-31 10:50:12','2026-10-31 10:50:12'),(4,'03.11.01.01.001 / BOS / 2026 / 004','Cyber Security Blue Team','Agus Saputra','Secure Media','Strategi pertahanan keamanan siber modern.',91,'005.8',3,3,1,'2026-10-31 10:50:12','2026-10-31 10:50:12'),(5,'03.11.01.01.001 / BOS / 2026 / 005','Administrasi Server Linux','Deni Pratama','SysAdmin Publisher','Panduan konfigurasi dan manajemen server Linux.',91,'005.432',4,4,1,'2026-10-31 10:50:12','2026-10-31 10:50:12'),(6,'03.11.01.01.001 / BOS / 2026 / 006','Belajar MySQL Cepat','Siti Rahma','Database Press','Tutorial praktis mengelola database MySQL.',91,'005.7565',6,6,1,'2026-10-31 10:50:12','2026-10-31 10:50:12'),(7,'03.11.01.01.001 / BOS / 2026 / 007','Membangun REST API','Rizky Maulana','API Media','Panduan membuat REST API scalable.',91,'005.3',5,5,1,'2026-10-31 10:50:12','2026-10-31 10:50:12'),(8,'03.11.01.01.001 / BOS / 2026 / 008','Cloud Computing Dasar','Fajar Nugroho','Cloud Press','Pengenalan konsep dan arsitektur cloud.',91,'004.6782',3,3,1,'2026-10-31 10:50:12','2026-10-31 10:50:12'),(9,'03.11.01.01.001 / BOS / 2026 / 009','DevOps untuk Pemula','Yoga Pratama','Tech Media','Praktik CI/CD dan automasi deployment.',91,'005.1',4,4,1,'2026-10-31 10:50:12','2026-10-31 10:50:12'),(10,'03.11.01.01.001 / BOS / 2026 / 010','Docker & Container','Arif Setiawan','Container Press','Implementasi containerisasi aplikasi.',91,'005.43',5,5,1,'2026-10-31 10:50:12','2026-10-31 10:50:12'),(11,'03.11.01.01.001 / BOS / 2026 / 011','Kubernetes Practical Guide','Hendra Gunawan','Cloud Native Media','Manajemen container orchestration.',91,'005.43',3,3,1,'2026-10-31 10:50:12','2026-10-31 10:50:12'),(12,'03.11.01.01.001 / BOS / 2026 / 012','Algoritma & Struktur Data','Dewi Lestari','Informatika Press','Dasar algoritma dan struktur data.',91,'005.1',5,5,1,'2026-10-31 10:50:12','2026-10-31 10:50:12'),(13,'03.11.01.01.001 / BOS / 2026 / 013','Pemrograman Python','Tono Wibowo','Code Publisher','Belajar Python untuk berbagai kebutuhan.',91,'005.133',6,6,1,'2026-10-31 10:50:12','2026-10-31 10:50:12'),(14,'03.11.01.01.001 / BOS / 2026 / 014','Data Science Praktis','Lukman Hakim','Data Media','Pengolahan data dan machine learning dasar.',91,'006.312',3,3,1,'2026-10-31 10:50:12','2026-10-31 10:50:12'),(15,'03.11.01.01.001 / BOS / 2026 / 015','Machine Learning Dasar','Rina Kartika','AI Press','Konsep supervised dan unsupervised learning.',91,'006.31',3,3,1,'2026-10-31 10:50:12','2026-10-31 10:50:12'),(16,'03.11.01.01.001 / BOS / 2026 / 016','Artificial Intelligence Modern','Yusuf Maulana','AI Media','Implementasi AI dalam industri.',91,'006.3',2,2,1,'2026-10-31 10:50:12','2026-10-31 10:50:12'),(17,'03.11.01.01.001 / BOS / 2026 / 017','WordPress Developer Guide','Indra Saputra','CMS Press','Membangun theme dan plugin WordPress.',91,'006.78',5,5,1,'2026-10-31 10:50:12','2026-10-31 10:50:12'),(18,'03.11.01.01.001 / BOS / 2026 / 018','SEO untuk Website','Mega Sari','Digital Media','Optimasi mesin pencari untuk website.',2,'025.04',4,4,1,'2026-10-31 10:50:12','2026-10-31 10:50:12'),(19,'03.11.01.01.001 / BOS / 2026 / 019','UI/UX Design Principle','Ayu Permata','Creative Press','Prinsip desain antarmuka modern.',91,'004.019',3,3,1,'2026-10-31 10:50:12','2026-10-31 10:50:12'),(20,'03.11.01.01.001 / BOS / 2026 / 020','ReactJS Modern','Bagus Firmansyah','Frontend Media','Membangun SPA dengan React.',91,'005.2762',4,4,1,'2026-10-31 10:50:12','2026-10-31 10:50:12'),(21,'03.11.01.01.001 / BOS / 2026 / 021','VueJS Framework','Randy Kurniawan','Frontend Media','Panduan lengkap VueJS.',91,'005.2762',3,3,1,'2026-10-31 10:50:12','2026-10-31 10:50:12'),(22,'03.11.01.01.001 / BOS / 2026 / 022','Angular Enterprise','Farhan Akbar','Enterprise Press','Pengembangan aplikasi enterprise Angular.',91,'005.2762',3,3,1,'2026-10-31 10:50:12','2026-10-31 10:50:12'),(23,'03.11.01.01.001 / BOS / 2026 / 023','Tailwind CSS','Dita Puspita','Design Press','Utility-first CSS framework.',91,'006.74',4,4,1,'2026-10-31 10:50:12','2026-10-31 10:50:12'),(24,'03.11.01.01.001 / BOS / 2026 / 024','Bootstrap 5 Guide','Rahmat Hidayat','Frontend Press','Panduan cepat Bootstrap 5.',91,'006.74',5,5,1,'2026-10-31 10:50:12','2026-10-31 10:50:12'),(25,'03.11.01.01.001 / BOS / 2026 / 025','Git & Version Control','Ilham Maulana','Dev Media','Manajemen kode dengan Git.',91,'005.1',6,6,1,'2026-10-31 10:50:12','2026-10-31 10:50:12'),(26,'03.11.01.01.001 / BOS / 2026 / 026','CI/CD Implementation','Reza Fahlevi','DevOps Press','Pipeline otomatis deployment.',91,'005.1',4,4,1,'2026-10-31 10:50:12','2026-10-31 10:50:12'),(27,'03.11.01.01.001 / BOS / 2026 / 027','Nginx Web Server','Galih Pratama','Server Press','Konfigurasi dan optimasi Nginx.',91,'005.71',3,3,1,'2026-10-31 10:50:12','2026-10-31 10:50:12'),(28,'03.11.01.01.001 / BOS / 2026 / 028','Apache Server Advanced','Eko Susanto','Server Media','Manajemen Apache production.',91,'005.71',3,3,1,'2026-10-31 10:50:12','2026-10-31 10:50:12'),(29,'03.11.01.01.001 / BOS / 2026 / 029','Firewall & Security','Bayu Saputra','Secure Press','Manajemen firewall jaringan.',91,'005.8',4,4,1,'2026-10-31 10:50:12','2026-10-31 10:50:12'),(30,'03.11.01.01.001 / BOS / 2026 / 030','Penetration Testing','Hafiz Rahman','Security Media','Teknik dasar penetration testing.',91,'005.8',2,2,1,'2026-10-31 10:50:12','2026-10-31 10:50:12'),(31,'03.11.01.01.001 / BOS / 2026 / 031','Kriptografi Modern','Satria Wijaya','Crypto Press','Konsep enkripsi dan keamanan data.',91,'005.824',3,3,1,'2026-10-31 10:50:12','2026-10-31 10:50:12'),(32,'03.11.01.01.001 / BOS / 2026 / 032','Blockchain Teknologi','Naufal Rizki','Fintech Media','Implementasi blockchain.',91,'005.74',3,3,1,'2026-10-31 10:50:12','2026-10-31 10:50:12'),(33,'03.11.01.01.001 / BOS / 2026 / 033','Fintech Revolution','Kevin Saputra','Finance Press','Transformasi digital finansial.',30,'332.1',4,4,1,'2026-10-31 10:50:12','2026-10-31 10:50:12'),(34,'03.11.01.01.001 / BOS / 2026 / 034','Analisis Saham Fundamental','Rama Pratama','Market Press','Analisis laporan keuangan.',30,'332.632',5,5,1,'2026-10-31 10:50:12','2026-10-31 10:50:12'),(35,'03.11.01.01.001 / BOS / 2026 / 035','Analisis Teknikal Trading','Yogi Firmansyah','Market Media','Strategi chart dan indikator.',30,'332.6',4,4,1,'2026-10-31 10:50:12','2026-10-31 10:50:12'),(36,'03.11.01.01.001 / BOS / 2026 / 036','Manajemen Risiko Investasi','Andre Gunawan','Finance Media','Pengelolaan risiko investasi.',30,'332.6',3,3,1,'2026-10-31 10:50:12','2026-10-31 10:50:12'),(37,'03.11.01.01.001 / BOS / 2026 / 037','Public Speaking IT','Citra Lestari','Skill Press','Komunikasi teknis efektif.',99,'808.51',3,3,1,'2026-10-31 10:50:12','2026-10-31 10:50:12'),(38,'03.11.01.01.001 / BOS / 2026 / 038','Leadership Teknologi','Putri Andini','Leadership Media','Kepemimpinan dalam tim IT.',59,'658.4092',2,2,1,'2026-10-31 10:50:12','2026-10-31 10:50:12'),(39,'03.11.01.01.001 / BOS / 2026 / 039','Project Management IT','Arman Syah','PM Press','Manajemen proyek teknologi.',59,'658.404',4,4,1,'2026-10-31 10:50:12','2026-10-31 10:50:12'),(40,'03.11.01.01.001 / BOS / 2026 / 040','Scrum & Agile','Nadia Salsabila','Agile Media','Metodologi agile development.',91,'005.1',4,4,1,'2026-10-31 10:50:12','2026-10-31 10:50:12'),(41,'03.11.01.01.001 / BOS / 2026 / 041','Database Optimization','Haris Maulana','DB Press','Optimasi performa database.',91,'005.74',3,3,1,'2026-10-31 10:50:12','2026-10-31 10:50:12'),(42,'03.11.01.01.001 / BOS / 2026 / 042','High Availability System','Iqbal Ramadhan','Infra Media','Arsitektur sistem high availability.',91,'004.6',2,2,1,'2026-10-31 10:50:12','2026-10-31 10:50:12'),(43,'03.11.01.01.001 / BOS / 2026 / 043','Monitoring & Logging','Sandi Prakoso','Ops Press','Implementasi monitoring sistem.',91,'005.7',3,3,1,'2026-10-31 10:50:12','2026-10-31 10:50:12'),(44,'03.11.01.01.001 / BOS / 2026 / 044','Microservices Architecture','Rico Saputra','Arch Media','Desain arsitektur microservices.',91,'005.12',2,2,1,'2026-10-31 10:50:12','2026-10-31 10:50:12'),(45,'03.11.01.01.001 / BOS / 2026 / 045','API Security','Fikri Ananda','Security Press','Keamanan API production.',91,'005.8',3,3,1,'2026-10-31 10:50:12','2026-10-31 10:50:12'),(46,'03.11.01.01.001 / BOS / 2026 / 046','Cloudflare Practical','Rafi Maulana','Cloud Media','Optimasi dan keamanan Cloudflare.',91,'004.678',3,3,1,'2026-10-31 10:50:12','2026-10-31 10:50:12'),(47,'03.11.01.01.001 / BOS / 2026 / 047','Linux Hardening','Bagas Prasetyo','Security Media','Pengamanan server Linux.',91,'005.8',4,4,1,'2026-10-31 10:50:12','2026-10-31 10:50:12'),(48,'03.11.01.01.001 / BOS / 2026 / 048','Backup & Disaster Recovery','Seno Aji','Infra Press','Strategi backup dan recovery.',91,'005.86',4,4,1,'2026-10-31 10:50:12','2026-10-31 10:50:12'),(49,'03.11.01.01.001 / BOS / 2026 / 049','Big Data Processing','Wahyu Nugraha','Data Press','Pemrosesan data skala besar.',91,'005.7',3,3,1,'2026-10-31 10:50:12','2026-10-31 10:50:12'),(50,'03.11.01.01.001 / BOS / 2026 / 050','Enterprise Architecture','Rizal Hidayat','Enterprise Media','Perancangan arsitektur enterprise.',59,'658.4038',4,4,1,'2026-10-31 10:50:12','2026-10-31 10:50:12');
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
  `is_active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_category_code_ddc` (`code_ddc`)
) ENGINE=InnoDB AUTO_INCREMENT=101 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `category`
--

LOCK TABLES `category` WRITE;
/*!40000 ALTER TABLE `category` DISABLE KEYS */;
INSERT INTO `category` VALUES (1,'010','Bibliografi',1),(2,'020','Ilmu Perpustakaan dan Informasi',1),(3,'030','Ensiklopedi Umum',1),(4,'050','Terbitan Berseri Umum (Majalah, Jurnal)',1),(5,'060','Organisasi Umum dan Museum',1),(6,'070','Jurnalisme, Penerbitan, Surat Kabar',1),(7,'080','Kumpulan Karya Umum',1),(8,'090','Manuskrip dan Buku Langka',1),(9,'110','Metafisika',1),(10,'120','Epistemologi',1),(11,'130','Fenomena Paranormal & Psikologi Populer',1),(12,'140','Aliran Filsafat Tertentu',1),(13,'150','Psikologi',1),(14,'160','Logika',1),(15,'170','Etika (Filsafat Moral)',1),(16,'180','Filsafat Kuno, Abad Pertengahan, & Oriental',1),(17,'190','Filsafat Barat Modern',1),(18,'210','Agama Alamiah (Filsafat Agama)',1),(19,'220','Alkitab & Injil',1),(20,'230','Teologi Kristen',1),(21,'240','Teologi Moral & Devosional Kristen',1),(22,'250','Gereja Setempat & Tarekat Kristen',1),(23,'260','Teologi Sosial & Agama Kristen',1),(24,'270','Sejarah Gereja Kristen',1),(25,'280','Denominasi & Sekte Kristen',1),(26,'290','Agama Lainnya & Agama Perbandingan',1),(27,'297','Islam',1),(28,'310','Statistik Umum',1),(29,'320','Ilmu Politik',1),(30,'330','Ekonomi',1),(31,'340','Hukum',1),(32,'350','Administrasi Publik & Ilmu Militer',1),(33,'360','Masalah & Layanan Sosial; Asosiasi',1),(34,'370','Pendidikan',1),(35,'380','Perdagangan, Komunikasi, & Transportasi',1),(36,'390','Adat Istiadat, Etiket, & Folklore',1),(37,'410','Linguistik',1),(38,'420','Bahasa Inggris & Anglo-Saxon',1),(39,'430','Bahasa Jerman',1),(40,'440','Bahasa Prancis',1),(41,'450','Bahasa Italia',1),(42,'460','Bahasa Spanyol & Portugis',1),(43,'470','Bahasa Latin',1),(44,'480','Bahasa Yunani Klasik',1),(45,'490','Bahasa-bahasa Lainnya (termasuk Bahasa Indonesia)',1),(46,'510','Matematika',1),(47,'520','Astronomi',1),(48,'530','Fisika',1),(49,'540','Kimia & Ilmu Pengetahuan Terkait',1),(50,'550','Ilmu Bumi (Geologi, Meteorologi)',1),(51,'560','Paleontologi (Fosil)',1),(52,'570','Biologi & Ilmu Hayati',1),(53,'580','Botani (Tumbuhan)',1),(54,'590','Zoologi (Hewan)',1),(55,'610','Ilmu Kedokteran & Kesehatan',1),(56,'620','Teknik (Engineering) & Sekitarnya',1),(57,'630','Pertanian & Industri Terkait',1),(58,'640','Ekonomi Rumah Tangga & Keluarga',1),(59,'650','Manajemen & Bisnis',1),(60,'660','Teknik Kimia',1),(61,'670','Manufaktur',1),(62,'680','Pabrikasi Produk Khusus',1),(63,'690','Teknik Bangunan & Konstruksi',1),(64,'710','Perencanaan Kota & Seni Pertamanan',1),(65,'720','Arsitektur',1),(66,'730','Seni Plastik; Patung',1),(67,'740','Menggambar & Seni Dekorasi',1),(68,'750','Seni Lukis & Lukisan',1),(69,'760','Seni Grafis',1),(70,'770','Fotografi & Foto',1),(71,'780','Musik',1),(72,'790','Seni Rekreasi, Permainan, & Olahraga',1),(73,'810','Sastra Amerika dalam Bahasa Inggris',1),(74,'820','Sastra Inggris & Anglo-Saxon',1),(75,'830','Sastra Jerman',1),(76,'840','Sastra Prancis',1),(77,'850','Sastra Italia',1),(78,'860','Sastra Spanyol & Portugis',1),(79,'870','Sastra Latin',1),(80,'880','Sastra Yunani',1),(81,'890','Sastra Bahasa Lainnya (termasuk Sastra Indonesia)',1),(82,'910','Geografi & Perjalanan',1),(83,'920','Biografi, Genealogi, Insignia',1),(84,'930','Sejarah Dunia Kuno',1),(85,'940','Sejarah Umum Eropa',1),(86,'950','Sejarah Umum Asia (termasuk Sejarah Indonesia)',1),(87,'960','Sejarah Umum Afrika',1),(88,'970','Sejarah Umum Amerika Utara',1),(89,'980','Sejarah Umum Amerika Selatan',1),(90,'990','Sejarah Umum Wilayah Lainnya',1),(91,'000','Karya Umum, Ilmu Komputer, Informasi',1),(92,'100','Filsafat & Psikologi',1),(93,'200','Agama',1),(94,'300','Ilmu Sosial',1),(95,'400','Bahasa',1),(96,'500','Ilmu Pengetahuan Alam / Sains',1),(97,'600','Teknologi / Ilmu Terapan',1),(98,'700','Seni & Rekreasi',1),(99,'800','Kesusastraan',1),(100,'900','Sejarah & Geografi',1);
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
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `class`
--

LOCK TABLES `class` WRITE;
/*!40000 ALTER TABLE `class` DISABLE KEYS */;
INSERT INTO `class` VALUES (1,'VII-A'),(2,'VII-B'),(3,'VII-C'),(4,'VII-D'),(5,'VII-E'),(6,'VIII-A'),(7,'VIII-B'),(8,'VIII-C'),(9,'VIII-D'),(10,'VIII-E'),(11,'IX-A'),(12,'IX-B'),(13,'IX-C'),(14,'IX-D'),(15,'IX-E');
/*!40000 ALTER TABLE `class` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fines`
--

DROP TABLE IF EXISTS `fines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fines` (
  `id` int NOT NULL AUTO_INCREMENT,
  `transaction_id` int NOT NULL,
  `transaction_detail_id` int DEFAULT NULL,
  `user_id` int NOT NULL,
  `hari_terlambat` int NOT NULL DEFAULT '0',
  `hari_dikenakan` int NOT NULL DEFAULT '0',
  `tarif` int NOT NULL DEFAULT '0',
  `jumlah_awal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `jumlah` decimal(12,2) NOT NULL DEFAULT '0.00',
  `status` enum('ditahan','belum_lunas','lunas','dibebaskan') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'belum_lunas',
  `catatan` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `diproses_by` int DEFAULT NULL,
  `diproses_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `transaction_detail_id` (`transaction_detail_id`),
  KEY `fines_transaction_id_foreign` (`transaction_id`),
  KEY `fines_diproses_by_foreign` (`diproses_by`),
  KEY `user_id_status` (`user_id`,`status`),
  CONSTRAINT `fines_diproses_by_foreign` FOREIGN KEY (`diproses_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fines_transaction_detail_id_foreign` FOREIGN KEY (`transaction_detail_id`) REFERENCES `transaction_details` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fines_transaction_id_foreign` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fines_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fines`
--

LOCK TABLES `fines` WRITE;
/*!40000 ALTER TABLE `fines` DISABLE KEYS */;
/*!40000 ALTER TABLE `fines` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `version` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `class` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `group` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `namespace` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `time` int NOT NULL,
  `batch` int unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2026-02-17-113210','App\\Database\\Migrations\\CreateUsers','default','App',1793443806,1),(2,'2026-02-17-113310','App\\Database\\Migrations\\CreateBooks','default','App',1793443806,1),(3,'2026-02-17-113410','App\\Database\\Migrations\\CreateTransactions','default','App',1793443806,1),(4,'2026-02-17-113510','App\\Database\\Migrations\\CreateActivityLogs','default','App',1793443806,1),(5,'2026-02-21-000004','App\\Database\\Migrations\\CreateTransactionDetails','default','App',1793443806,1),(6,'2026-03-09-000003','App\\Database\\Migrations\\CreateBookItems','default','App',1793443806,1),(7,'2026-03-09-000007','App\\Database\\Migrations\\CreateAppSettings','default','App',1793443806,1),(8,'2026-03-09-161011','App\\Database\\Migrations\\MasterClass','default','App',1793443806,1),(9,'2026-06-17-000001','App\\Database\\Migrations\\AddPhase2Columns','default','App',1793443806,1),(10,'2026-07-01-052000','App\\Database\\Migrations\\AddContactColumnsToUsers','default','App',1793443806,1),(11,'2026-07-03-084500','App\\Database\\Migrations\\UpdateLostSettlementColumns','default','App',1793443807,1),(12,'2026-10-31-100000','App\\Database\\Migrations\\AddKodeBarangToAppSettings','default','App',1793443807,1),(13,'2026-10-31-100100','App\\Database\\Migrations\\UpdateMasterSourceFundAndCategory','default','App',1793443807,1),(14,'2026-10-31-100200','App\\Database\\Migrations\\AddDdcToBooks','default','App',1793443807,1),(15,'2026-10-31-100300','App\\Database\\Migrations\\AddDdcDivisions','default','App',1793443807,1),(16,'2026-11-01-090000','App\\Database\\Migrations\\LateFinesPerBook','default','App',1793443807,1),(17,'2026-11-01-100000','App\\Database\\Migrations\\AddPartialReturnToTransactionDetails','default','App',1793443807,1);
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
  `kode` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `source_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_source_fund_kode` (`kode`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `source_fund`
--

LOCK TABLES `source_fund` WRITE;
/*!40000 ALTER TABLE `source_fund` DISABLE KEYS */;
INSERT INTO `source_fund` VALUES (1,'BOS','BOS',1),(2,'APBD','APBD',1),(3,'APBN','APBN',1),(4,'HIBAH','Hibah / Donasi',1),(5,'PRIBADI','Pembelian Pribadi',1);
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
  `returned_at` date DEFAULT NULL,
  `returned_by` int DEFAULT NULL,
  `is_lost` tinyint(1) NOT NULL DEFAULT '0',
  `lost_reported_at` date DEFAULT NULL,
  `lost_settlement_status` enum('pending','replaced','paid','written_off','found') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `lost_note` text COLLATE utf8mb4_general_ci,
  `lost_settlement_amount` int DEFAULT NULL,
  `settled_by` int DEFAULT NULL,
  `settled_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `transaction_id_book_id` (`transaction_id`,`book_id`),
  KEY `transaction_id` (`transaction_id`),
  KEY `book_id` (`book_id`),
  KEY `is_lost` (`is_lost`),
  KEY `fk_transaction_details_book_item` (`book_item_id`),
  KEY `fk_transaction_details_settled_by` (`settled_by`),
  KEY `fk_transaction_details_returned_by` (`returned_by`),
  CONSTRAINT `fk_transaction_details_book_item` FOREIGN KEY (`book_item_id`) REFERENCES `book_items` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_transaction_details_returned_by` FOREIGN KEY (`returned_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_transaction_details_settled_by` FOREIGN KEY (`settled_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `transaction_details_book_id_foreign` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `transaction_details_transaction_id_foreign` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transaction_details`
--

LOCK TABLES `transaction_details` WRITE;
/*!40000 ALTER TABLE `transaction_details` DISABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transactions`
--

LOCK TABLES `transactions` WRITE;
/*!40000 ALTER TABLE `transactions` DISABLE KEYS */;
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
  `phone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `whatsapp` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Super Administrator','ADM-001',NULL,NULL,NULL,NULL,'admin','$2y$12$PPfTbGhFxMBX/lMtKJdy1OkMFXKnKB9apgwn36mp7v90vOGqvfYYy','admin','guru',1,0,0,'2026-10-31 10:50:09','2026-10-31 10:50:09',NULL),(2,'Staff Perpustakaan','STF-001',NULL,NULL,NULL,NULL,'staff','$2y$12$3iXqFF1e34b1e7qK.Vkhlu4kvjV1/dW2D/2VjjJRTqtTFCk.Qvc3e','staff','pegawai',1,0,0,'2026-10-31 10:50:10','2026-10-31 10:50:10',NULL),(3,'Lina Petugas','STF-002',NULL,NULL,NULL,NULL,'lina_petugas','$2y$12$/iFJNsPjzpRLHZ.RmsRypOw8XFy7aHj6MlyIt1cX8hA9E1/2X7UKy','staff','pegawai',1,0,0,'2026-10-31 10:50:11','2026-10-31 10:50:11',NULL),(4,'Bambang Pegawai','PEG-001',NULL,NULL,NULL,NULL,'bambang_pegawai','$2y$12$2CuZbcz5T.TLaX6WgBxtKOZXUnmRF0V2388dFlb27.hOWMpiBeAT2','member','pegawai',1,0,0,'2026-10-31 10:50:11','2026-10-31 10:50:11',NULL),(5,'Dra. Endang Guru','GRU-001',NULL,NULL,NULL,NULL,'endang_guru','$2y$12$hgxNHBTH0exxu2ARWlTXn.6Jum03O3jrY5tIsAc1I2ThzrWr8TTfm','member','guru',1,0,0,'2026-10-31 10:50:11','2026-10-31 10:50:11',NULL),(6,'Siti Rahmawati (Siswa)','SSW-001',NULL,NULL,NULL,NULL,'siti_siswa','$2y$12$aOX6GfrBdpE21NtLUk7/AO94wBxp0GEvpiwhmzcbXneJtGTR1GWbC','member','siswa',1,0,0,'2026-10-31 10:50:11','2026-10-31 10:50:11',NULL),(7,'Rian Hidayat (Siswa)','SSW-002',NULL,NULL,NULL,NULL,'rian_siswa','$2y$12$dvQatQ.tRA5WHT4qtrfcNu3LlYpSoOZ.GJhsIL4CDeAUkFEQwNE3O','member','siswa',1,0,0,'2026-10-31 10:50:11','2026-10-31 10:50:11',NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'perpus_seed_build'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-10-31 17:50:12
