-- MySQL dump 10.13  Distrib 8.0.36, for Win64 (x86_64)
--
-- Host: localhost    Database: pbazaar
-- ------------------------------------------------------
-- Server version	8.0.30

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
-- Table structure for table `boutique_stock`
--

DROP TABLE IF EXISTS `boutique_stock`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `boutique_stock` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `buyerid` int unsigned DEFAULT NULL,
  `price` int DEFAULT NULL,
  `description` text NOT NULL,
  `purchased` tinyint(1) DEFAULT NULL,
  `itemname` text NOT NULL,
  `sellerid` int unsigned DEFAULT NULL,
  `originalfilename` varchar(255) DEFAULT NULL,
  `uniquefilename` varchar(255) DEFAULT NULL,
  `uniqueimage` varchar(255) DEFAULT NULL,
  `purchasedate` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `buyerid` (`buyerid`),
  CONSTRAINT `boutique_stock_ibfk_1` FOREIGN KEY (`buyerid`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `clawmachine`
--

DROP TABLE IF EXISTS `clawmachine`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `clawmachine` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `claimed` tinyint(1) DEFAULT NULL,
  `uniquefilename` varchar(255) DEFAULT NULL,
  `originalfilename` varchar(255) DEFAULT NULL,
  `uniqueimage` varchar(255) DEFAULT NULL,
  `claimdate` datetime DEFAULT NULL,
  `userID` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `userID` (`userID`),
  CONSTRAINT `userID` FOREIGN KEY (`userID`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ledger`
--

DROP TABLE IF EXISTS `ledger`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ledger` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `buyerid` int unsigned DEFAULT NULL,
  `sellerid` int unsigned DEFAULT NULL,
  `price` int DEFAULT NULL,
  `purchasedate` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `buyerid` (`buyerid`),
  KEY `sellerid` (`sellerid`),
  CONSTRAINT `ledger_ibfk_1` FOREIGN KEY (`buyerid`) REFERENCES `users` (`id`),
  CONSTRAINT `ledger_ibfk_2` FOREIGN KEY (`sellerid`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `messages` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `sender_id` int unsigned NOT NULL,
  `receiver_id` int unsigned NOT NULL,
  `message` text NOT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `subject` text NOT NULL,
  `opened` tinyint(1) DEFAULT '0',
  `read` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `sender_id` (`sender_id`),
  KEY `receiver_id` (`receiver_id`),
  CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`),
  CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shop_stock`
--

DROP TABLE IF EXISTS `shop_stock`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `shop_stock` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `sellerid` int unsigned NOT NULL,
  `buyerid` int unsigned DEFAULT NULL,
  `price` int DEFAULT NULL,
  `auction` tinyint(1) DEFAULT NULL,
  `auctionend` datetime DEFAULT NULL,
  `description` text NOT NULL,
  `purchased` tinyint(1) DEFAULT NULL,
  `itemname` text NOT NULL,
  `purchasedate` datetime DEFAULT NULL,
  `uniqueimage` varchar(255) DEFAULT NULL,
  `uniquefilename` varchar(255) DEFAULT NULL,
  `originalfilename` varchar(255) DEFAULT NULL,
  `creationdate` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sellerid` (`sellerid`),
  KEY `buyerid` (`buyerid`),
  CONSTRAINT `shop_stock_ibfk_1` FOREIGN KEY (`sellerid`) REFERENCES `users` (`id`),
  CONSTRAINT `shop_stock_ibfk_2` FOREIGN KEY (`buyerid`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shop_views`
--

DROP TABLE IF EXISTS `shop_views`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `shop_views` (
  `userId` int unsigned NOT NULL,
  `sellerId` int unsigned NOT NULL,
  `viewed` datetime NOT NULL,
  PRIMARY KEY (`userId`,`sellerId`),
  KEY `sellerId` (`sellerId`),
  CONSTRAINT `shop_views_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`id`),
  CONSTRAINT `shop_views_ibfk_2` FOREIGN KEY (`sellerId`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL,
  `password` varchar(64) NOT NULL,
  `email` varchar(50) DEFAULT NULL,
  `coinz` int NOT NULL DEFAULT 25,
  `quartz` int NOT NULL DEFAULT 0,
  `open` tinyint(1) NOT NULL DEFAULT 0,
  `transactionz` int NOT NULL DEFAULT 0,
  `Digs` int NOT NULL DEFAULT 0,
  `storefront` text,
  `sales` int NOT NULL DEFAULT 0,
  `shop_slots` int NOT NULL DEFAULT 10,
  `last_login_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fortunedate` date DEFAULT NULL,
  `fortunedraw` int DEFAULT NULL,
  `digdate` date DEFAULT NULL,
  `bonus` date DEFAULT NULL,
  `customcss` text,
  `approved` tinyint(1) NOT NULL DEFAULT '0',
  `userhash` varchar(8) NOT NULL,
  `passwordresettoken` varchar(36) DEFAULT NULL,
  `passwordresettokendate` datetime DEFAULT NULL,
  `refer` varchar(255) DEFAULT NULL,
  `clawdonations` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-06-16 20:35:39
