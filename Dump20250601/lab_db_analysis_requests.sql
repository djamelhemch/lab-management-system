-- MySQL dump 10.13  Distrib 8.0.42, for Win64 (x86_64)
--
-- Host: localhost    Database: lab_db
-- ------------------------------------------------------
-- Server version	9.3.0

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `analysis_requests`
--

DROP TABLE IF EXISTS `analysis_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `analysis_requests` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `sample_id` bigint DEFAULT NULL,
  `analysis_id` bigint DEFAULT NULL,
  `machine_id` bigint DEFAULT NULL,
  `status` enum('pending','in_progress','completed','validated','rejected') DEFAULT NULL,
  `urgent` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `sample_id` (`sample_id`),
  KEY `analysis_id` (`analysis_id`),
  KEY `machine_id` (`machine_id`),
  CONSTRAINT `analysis_requests_ibfk_1` FOREIGN KEY (`sample_id`) REFERENCES `samples` (`id`),
  CONSTRAINT `analysis_requests_ibfk_2` FOREIGN KEY (`analysis_id`) REFERENCES `analysis_catalog` (`id`),
  CONSTRAINT `analysis_requests_ibfk_3` FOREIGN KEY (`machine_id`) REFERENCES `machines` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `analysis_requests`
--

LOCK TABLES `analysis_requests` WRITE;
/*!40000 ALTER TABLE `analysis_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `analysis_requests` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-06-01  0:22:57
