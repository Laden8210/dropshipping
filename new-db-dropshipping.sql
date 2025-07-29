-- MySQL dump 10.13  Distrib 8.0.38, for Win64 (x86_64)
--
-- Host: localhost    Database: dropshipping_db
-- ------------------------------------------------------
-- Server version	8.0.39

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
-- Table structure for table `cart`
--

DROP TABLE IF EXISTS `cart`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cart` (
  `cart_id` bigint NOT NULL AUTO_INCREMENT,
  `user_id` char(14) NOT NULL,
  `product_id` bigint NOT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `store_id` bigint NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`cart_id`),
  KEY `fk_cart_user` (`user_id`),
  KEY `fk_cart_product` (`product_id`),
  KEY `fk_cart_store` (`store_id`),
  CONSTRAINT `fk_cart_product` FOREIGN KEY (`product_id`) REFERENCES `imported_product` (`product_id`),
  CONSTRAINT `fk_cart_store` FOREIGN KEY (`store_id`) REFERENCES `store_profile` (`store_id`),
  CONSTRAINT `fk_cart_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cart`
--

LOCK TABLES `cart` WRITE;
/*!40000 ALTER TABLE `cart` DISABLE KEYS */;
INSERT INTO `cart` VALUES (1,'650A-0543-J926',23,8,1,'2025-07-26 16:25:21','2025-07-26 16:32:25'),(2,'650A-0543-J926',14,18,2,'2025-07-26 16:25:21','2025-07-29 18:42:50'),(3,'650A-0543-J926',10,1,1,'2025-07-29 19:37:53','2025-07-29 19:37:53');
/*!40000 ALTER TABLE `cart` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `imported_product`
--

DROP TABLE IF EXISTS `imported_product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `imported_product` (
  `imported_product_id` bigint NOT NULL AUTO_INCREMENT,
  `user_id` char(14) NOT NULL,
  `product_id` bigint NOT NULL,
  `store_id` bigint NOT NULL,
  `profit_margin` decimal(10,0) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`imported_product_id`),
  KEY `fk_imported_product_user` (`user_id`),
  KEY `fk_imported_product_product` (`product_id`),
  KEY `fk_imported_product_store` (`store_id`),
  CONSTRAINT `fk_imported_product_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`),
  CONSTRAINT `fk_imported_product_store` FOREIGN KEY (`store_id`) REFERENCES `store_profile` (`store_id`),
  CONSTRAINT `fk_imported_product_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `imported_product`
--

LOCK TABLES `imported_product` WRITE;
/*!40000 ALTER TABLE `imported_product` DISABLE KEYS */;
INSERT INTO `imported_product` VALUES (1,'0206-5510-970L',16,1,NULL,'2025-06-22 14:50:15','2025-06-22 14:50:15'),(2,'0206-5510-970L',15,1,NULL,'2025-06-22 14:54:28','2025-06-22 14:54:28'),(3,'0206-5510-970L',10,1,10,'2025-06-22 15:08:31','2025-06-22 15:51:52'),(4,'0206-5510-970L',23,1,NULL,'2025-06-24 05:05:07','2025-06-24 05:05:07'),(5,'0206-5510-970L',21,1,NULL,'2025-06-24 05:05:53','2025-06-24 05:05:53'),(6,'0206-5510-970L',20,1,NULL,'2025-06-24 05:05:57','2025-06-24 05:05:57'),(7,'0206-5510-970L',24,1,NULL,'2025-07-29 18:39:37','2025-07-29 18:39:37'),(8,'0206-5510-970L',14,2,10,'2025-07-29 18:39:40','2025-07-29 19:23:13');
/*!40000 ALTER TABLE `imported_product` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory`
--

DROP TABLE IF EXISTS `inventory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory` (
  `inventory_id` bigint NOT NULL AUTO_INCREMENT,
  `product_id` bigint NOT NULL,
  `quantity` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`inventory_id`),
  KEY `fk_inventory_product` (`product_id`),
  CONSTRAINT `fk_inventory_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory`
--

LOCK TABLES `inventory` WRITE;
/*!40000 ALTER TABLE `inventory` DISABLE KEYS */;
INSERT INTO `inventory` VALUES (1,10,4,'2025-06-22 07:37:54','2025-06-24 04:32:42'),(2,23,96,'2025-06-24 05:05:36','2025-07-29 16:55:50'),(3,20,0,'2025-07-29 16:53:28','2025-07-29 16:53:28');
/*!40000 ALTER TABLE `inventory` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_items` (
  `order_item_id` bigint NOT NULL AUTO_INCREMENT,
  `order_id` bigint NOT NULL,
  `product_id` bigint NOT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`order_item_id`),
  KEY `fk_order_items_order` (`order_id`),
  KEY `fk_order_items_product` (`product_id`),
  CONSTRAINT `fk_order_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  CONSTRAINT `fk_order_items_product` FOREIGN KEY (`product_id`) REFERENCES `imported_product` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
INSERT INTO `order_items` VALUES (1,4,23,1,664.60,'2025-07-29 16:02:47','2025-07-29 16:02:47'),(2,5,20,1,664.60,'2025-07-29 16:02:47','2025-07-29 16:02:47'),(3,6,23,1,664.60,'2025-07-29 16:02:57','2025-07-29 16:02:57'),(4,7,20,1,664.60,'2025-07-29 16:02:57','2025-07-29 16:02:57'),(5,8,23,1,664.60,'2025-07-29 16:03:11','2025-07-29 16:03:11'),(6,9,20,1,664.60,'2025-07-29 16:03:11','2025-07-29 16:03:11'),(16,19,23,1,664.60,'2025-07-29 16:23:05','2025-07-29 16:23:05'),(17,20,20,1,664.60,'2025-07-29 16:23:05','2025-07-29 16:23:05'),(18,21,23,1,664.60,'2025-07-29 16:27:09','2025-07-29 16:27:09'),(25,28,23,8,664.60,'2025-07-29 20:10:36','2025-07-29 20:10:36'),(26,29,23,8,664.60,'2025-07-29 20:10:49','2025-07-29 20:10:49'),(27,29,10,1,9658.01,'2025-07-29 20:10:49','2025-07-29 20:10:49');
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_payments`
--

DROP TABLE IF EXISTS `order_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_payments` (
  `payment_id` bigint NOT NULL AUTO_INCREMENT,
  `order_id` bigint NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `account_number` varchar(100) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('pending','completed','failed','refunded') DEFAULT 'pending',
  `transaction_id` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`payment_id`),
  KEY `fk_order_payments_order` (`order_id`),
  CONSTRAINT `fk_order_payments_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_payments`
--

LOCK TABLES `order_payments` WRITE;
/*!40000 ALTER TABLE `order_payments` DISABLE KEYS */;
INSERT INTO `order_payments` VALUES (1,19,'cod',NULL,844.35,'pending','TXN-20250729-182305-K64H','2025-07-29 16:23:05','2025-07-29 16:23:05'),(2,20,'cod',NULL,844.35,'pending','TXN-20250729-182305-A03H','2025-07-29 16:23:05','2025-07-29 16:23:05'),(3,21,'cod',NULL,844.35,'pending','TXN-20250729-182709-5CBS','2025-07-29 16:27:09','2025-07-29 16:27:09'),(4,28,'credit_card',NULL,6054.82,'pending','TXN-20250729-221036-WRN7','2025-07-29 20:10:36','2025-07-29 20:10:36'),(5,29,'cod',NULL,16871.79,'pending','TXN-20250729-221049-GKLT','2025-07-29 20:10:49','2025-07-29 20:10:49');
/*!40000 ALTER TABLE `order_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_shipping_status`
--

DROP TABLE IF EXISTS `order_shipping_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_shipping_status` (
  `shipping_status_id` bigint NOT NULL AUTO_INCREMENT,
  `order_id` bigint NOT NULL,
  `remarks` text,
  `tracking_number` varchar(100) DEFAULT NULL,
  `current_location` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`shipping_status_id`),
  KEY `fk_order_shipping_status_order` (`order_id`),
  CONSTRAINT `fk_order_shipping_status_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_shipping_status`
--

LOCK TABLES `order_shipping_status` WRITE;
/*!40000 ALTER TABLE `order_shipping_status` DISABLE KEYS */;
/*!40000 ALTER TABLE `order_shipping_status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_status_history`
--

DROP TABLE IF EXISTS `order_status_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_status_history` (
  `status_history_id` bigint NOT NULL AUTO_INCREMENT,
  `order_id` bigint NOT NULL,
  `status` enum('pending','processing','shipped','delivered','completed','cancelled','refunded','failed') NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`status_history_id`),
  KEY `fk_order_status_history_order` (`order_id`),
  CONSTRAINT `fk_order_status_history_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_status_history`
--

LOCK TABLES `order_status_history` WRITE;
/*!40000 ALTER TABLE `order_status_history` DISABLE KEYS */;
INSERT INTO `order_status_history` VALUES (1,4,'pending','2025-07-29 16:02:47','2025-07-29 16:02:47'),(2,5,'pending','2025-07-29 16:02:47','2025-07-29 16:02:47'),(3,6,'pending','2025-07-29 16:02:57','2025-07-29 16:02:57'),(4,7,'pending','2025-07-29 16:02:57','2025-07-29 16:02:57'),(5,8,'pending','2025-07-29 16:03:11','2025-07-29 16:03:11'),(6,9,'pending','2025-07-29 16:03:11','2025-07-29 16:03:11'),(7,19,'pending','2025-07-29 16:23:05','2025-07-29 16:23:05'),(8,20,'pending','2025-07-29 16:23:05','2025-07-29 16:23:05'),(9,21,'pending','2025-07-29 16:27:09','2025-07-29 16:27:09'),(10,21,'processing','2025-07-29 16:42:33','2025-07-29 16:42:33'),(11,21,'processing','2025-07-29 16:45:51','2025-07-29 16:45:51'),(12,21,'processing','2025-07-29 16:46:26','2025-07-29 16:46:26'),(13,21,'shipped','2025-07-29 16:52:27','2025-07-29 16:52:27'),(14,19,'shipped','2025-07-29 16:53:11','2025-07-29 16:53:11'),(15,20,'processing','2025-07-29 16:53:28','2025-07-29 16:53:28'),(16,8,'processing','2025-07-29 16:54:15','2025-07-29 16:54:15'),(17,8,'shipped','2025-07-29 16:54:47','2025-07-29 16:54:47'),(18,20,'shipped','2025-07-29 16:55:04','2025-07-29 16:55:04'),(19,9,'shipped','2025-07-29 16:55:29','2025-07-29 16:55:29'),(20,6,'processing','2025-07-29 16:55:50','2025-07-29 16:55:50'),(21,6,'shipped','2025-07-29 16:55:58','2025-07-29 16:55:58'),(22,28,'pending','2025-07-29 20:10:36','2025-07-29 20:10:36'),(23,29,'pending','2025-07-29 20:10:49','2025-07-29 20:10:49');
/*!40000 ALTER TABLE `order_status_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `order_id` bigint NOT NULL AUTO_INCREMENT,
  `user_id` char(14) NOT NULL,
  `store_id` bigint NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `shipping_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `tax` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(10,2) NOT NULL,
  `order_number` varchar(50) NOT NULL,
  `tracking_number` varchar(100) DEFAULT NULL,
  `shipping_address_id` bigint DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`order_id`),
  UNIQUE KEY `order_number` (`order_number`),
  KEY `fk_orders_user` (`user_id`),
  KEY `fk_orders_shipping_address` (`shipping_address_id`),
  KEY `fk_orders_store` (`store_id`),
  CONSTRAINT `fk_orders_shipping_address` FOREIGN KEY (`shipping_address_id`) REFERENCES `user_shipping_address` (`address_id`),
  CONSTRAINT `fk_orders_store` FOREIGN KEY (`store_id`) REFERENCES `store_profile` (`store_id`),
  CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (4,'650A-0543-J926',1,664.60,100.00,79.75,844.35,'ORD-20250729-180247-1J5P',NULL,6,'2025-07-29 16:02:47','2025-07-29 16:02:47'),(5,'650A-0543-J926',2,664.60,100.00,79.75,844.35,'ORD-20250729-180247-VDUA',NULL,6,'2025-07-29 16:02:47','2025-07-29 16:02:47'),(6,'650A-0543-J926',1,664.60,100.00,79.75,844.35,'ORD-20250729-180257-MOU9','TRK-20250729-185558-WA65',6,'2025-07-29 16:02:57','2025-07-29 16:55:58'),(7,'650A-0543-J926',2,664.60,100.00,79.75,844.35,'ORD-20250729-180257-TAVW',NULL,6,'2025-07-29 16:02:57','2025-07-29 16:02:57'),(8,'650A-0543-J926',1,664.60,100.00,79.75,844.35,'ORD-20250729-180311-EZDX',NULL,6,'2025-07-29 16:03:11','2025-07-29 16:03:11'),(9,'650A-0543-J926',2,664.60,100.00,79.75,844.35,'ORD-20250729-180311-95ZF','TRK-20250729-185529-5TSI',6,'2025-07-29 16:03:11','2025-07-29 16:55:29'),(19,'650A-0543-J926',1,664.60,100.00,79.75,844.35,'ORD-20250729-182305-G56F',NULL,6,'2025-07-29 16:23:05','2025-07-29 16:23:05'),(20,'650A-0543-J926',2,664.60,100.00,79.75,844.35,'ORD-20250729-182305-U451',NULL,6,'2025-07-29 16:23:05','2025-07-29 16:23:05'),(21,'650A-0543-J926',1,664.60,100.00,79.75,844.35,'ORD-20250729-182709-GQK5',NULL,6,'2025-07-29 16:27:09','2025-07-29 16:27:09'),(28,'650A-0543-J926',1,5316.80,100.00,638.02,6054.82,'ORD-20250729-221036-E694',NULL,6,'2025-07-29 20:10:36','2025-07-29 20:10:36'),(29,'650A-0543-J926',1,14974.81,100.00,1796.98,16871.79,'ORD-20250729-221049-LFHZ',NULL,6,'2025-07-29 20:10:49','2025-07-29 20:10:49');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_categories`
--

DROP TABLE IF EXISTS `product_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_categories` (
  `category_id` bigint NOT NULL AUTO_INCREMENT,
  `category_name` varchar(255) NOT NULL,
  `user_id` char(14) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_deleted` int DEFAULT '0',
  PRIMARY KEY (`category_id`),
  UNIQUE KEY `category_name` (`category_name`),
  KEY `fk_product_categories_user` (`user_id`),
  CONSTRAINT `fk_product_categories_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_categories`
--

LOCK TABLES `product_categories` WRITE;
/*!40000 ALTER TABLE `product_categories` DISABLE KEYS */;
INSERT INTO `product_categories` VALUES (1,'dsd','650A-0543-J926','2025-06-21 05:38:49','2025-06-21 05:53:30',1),(2,'dasd','650A-0543-J926','2025-06-21 05:39:26','2025-06-21 05:54:39',1),(3,'dasddsd','650A-0543-J926','2025-06-21 05:39:53','2025-06-21 05:56:07',1),(4,'dasddsddsd','650A-0543-J926','2025-06-21 05:39:56','2025-06-21 05:56:37',1),(5,'dsddsds','650A-0543-J926','2025-06-21 05:40:23','2025-06-21 05:57:24',1),(6,'dasddasdas','650A-0543-J926','2025-06-21 05:57:54','2025-06-21 05:57:57',1),(7,'dsa','650A-0543-J926','2025-06-21 06:00:11','2025-06-21 06:00:15',1),(8,'dsddsdsdsd','650A-0543-J926','2025-06-21 06:01:25','2025-06-21 06:01:30',1),(9,'dsdsd','650A-0543-J926','2025-06-21 06:02:14','2025-06-21 06:02:16',1),(10,'dasddasdsa','650A-0543-J926','2025-06-21 06:05:16','2025-06-21 06:05:19',1),(11,'dasdsa','650A-0543-J926','2025-06-21 06:07:22','2025-06-21 06:07:26',1),(12,'das','650A-0543-J926','2025-06-21 06:08:05','2025-06-21 06:08:08',1),(13,'dsdassa2','650A-0543-J926','2025-06-21 06:08:37','2025-06-21 06:08:40',1),(14,'dasddasdasdadasd','650A-0543-J926','2025-06-21 06:10:27','2025-06-21 06:10:29',1),(15,'dadasdasd','650A-0543-J926','2025-06-21 06:11:34','2025-06-21 06:11:37',1),(16,'dsds','650A-0543-J926','2025-06-21 06:12:45','2025-06-21 06:12:48',1),(17,'dasds','650A-0543-J926','2025-06-21 06:13:55','2025-06-21 06:13:58',1),(18,'dasda','650A-0543-J926','2025-06-21 06:20:34','2025-06-21 06:20:34',0),(21,'dasddasda','241T-S405-0P32','2025-06-24 05:04:37','2025-06-24 05:04:37',0);
/*!40000 ALTER TABLE `product_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_images`
--

DROP TABLE IF EXISTS `product_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_images` (
  `image_id` bigint NOT NULL AUTO_INCREMENT,
  `product_id` bigint NOT NULL,
  `image_url` text NOT NULL,
  `is_primary` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`image_id`),
  KEY `fk_product_images_product` (`product_id`),
  CONSTRAINT `fk_product_images_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_images`
--

LOCK TABLES `product_images` WRITE;
/*!40000 ALTER TABLE `product_images` DISABLE KEYS */;
INSERT INTO `product_images` VALUES (10,10,'product_685a31f31a67c0.57035415.jpg',1,'2025-06-22 05:55:14','2025-07-20 03:26:59'),(11,10,'product_68579ac22aeac7.21249560.png',0,'2025-06-22 05:55:14','2025-06-22 05:55:14'),(12,10,'product_68579ac22c1955.34528338.jpg',0,'2025-06-22 05:55:14','2025-06-22 05:55:14'),(13,11,'product_6857df16ac0c83.42522988.jpg',1,'2025-06-22 10:46:46','2025-06-22 10:46:46'),(14,11,'product_6857df16ad46f0.98688366.png',0,'2025-06-22 10:46:46','2025-06-22 10:46:46'),(15,11,'product_6857df16ae83b7.68619769.png',0,'2025-06-22 10:46:46','2025-06-22 10:46:46'),(16,11,'product_6857df16afad39.44037002.jpg',0,'2025-06-22 10:46:46','2025-06-22 10:46:46'),(17,12,'product_6857df2b6365f5.28941550.jpg',1,'2025-06-22 10:47:07','2025-06-22 10:47:07'),(18,12,'product_6857df2b64f3b1.51458423.png',0,'2025-06-22 10:47:07','2025-06-22 10:47:07'),(19,12,'product_6857df2b662498.52387822.png',0,'2025-06-22 10:47:07','2025-06-22 10:47:07'),(20,12,'product_6857df2b676263.42481574.jpg',0,'2025-06-22 10:47:07','2025-06-22 10:47:07'),(21,13,'product_6857df43c15ab8.68965198.jpg',1,'2025-06-22 10:47:31','2025-06-22 10:47:31'),(22,13,'product_6857df43c2ab40.88193471.png',0,'2025-06-22 10:47:31','2025-06-22 10:47:31'),(23,13,'product_6857df43c38e50.42741542.png',0,'2025-06-22 10:47:31','2025-06-22 10:47:31'),(24,13,'product_6857df43c4a842.60733837.jpg',0,'2025-06-22 10:47:31','2025-06-22 10:47:31'),(25,14,'product_6857df479db176.76713010.jpg',1,'2025-06-22 10:47:35','2025-06-22 10:47:35'),(26,14,'product_6857df479ecea1.54956179.png',0,'2025-06-22 10:47:35','2025-06-22 10:47:35'),(27,14,'product_6857df479ff9d2.24651488.png',0,'2025-06-22 10:47:35','2025-06-22 10:47:35'),(28,14,'product_6857df47a0fe46.21855027.jpg',0,'2025-06-22 10:47:35','2025-06-22 10:47:35'),(29,15,'product_6857df5308bae1.95307487.jpg',1,'2025-06-22 10:47:47','2025-06-22 10:47:47'),(30,15,'product_6857df5309c878.77876580.png',0,'2025-06-22 10:47:47','2025-06-22 10:47:47'),(31,15,'product_6857df530adea3.96422676.png',0,'2025-06-22 10:47:47','2025-06-22 10:47:47'),(32,15,'product_6857df530bdea5.85031208.jpg',0,'2025-06-22 10:47:47','2025-06-22 10:47:47'),(33,16,'product_6857df5540a215.03118221.jpg',1,'2025-06-22 10:47:49','2025-06-22 10:47:49'),(34,16,'product_6857df5541d1a5.65260302.png',0,'2025-06-22 10:47:49','2025-06-22 10:47:49'),(35,16,'product_6857df5542e205.88889800.png',0,'2025-06-22 10:47:49','2025-06-22 10:47:49'),(36,16,'product_6857df55442cc5.94502500.jpg',0,'2025-06-22 10:47:49','2025-06-22 10:47:49'),(37,17,'product_6857dfae6fa815.60311741.jpg',1,'2025-06-22 10:49:18','2025-06-22 10:49:18'),(38,17,'product_6857dfae70f232.68689635.png',0,'2025-06-22 10:49:18','2025-06-22 10:49:18'),(39,18,'product_6857dff722d889.98843692.png',1,'2025-06-22 10:50:31','2025-06-22 10:50:31'),(40,18,'product_6857dff723b030.70586439.png',0,'2025-06-22 10:50:31','2025-06-22 10:50:31'),(41,18,'product_6857dff724bc28.38741470.png',0,'2025-06-22 10:50:31','2025-06-22 10:50:31'),(42,19,'product_6857e0289fd088.71326360.png',1,'2025-06-22 10:51:20','2025-06-22 10:51:20'),(43,19,'product_6857e028a11b57.18209553.png',0,'2025-06-22 10:51:20','2025-06-22 10:51:20'),(44,19,'product_6857e028a26fa1.32275344.png',0,'2025-06-22 10:51:20','2025-06-22 10:51:20'),(45,19,'product_6857e028a3e3e0.05955815.png',0,'2025-06-22 10:51:20','2025-06-22 10:51:20'),(46,20,'product_685a1044e76ab0.99920326.jpg',1,'2025-06-24 02:41:08','2025-06-24 02:41:08'),(47,20,'product_685a1044e943a1.84828293.png',0,'2025-06-24 02:41:08','2025-06-24 02:41:08'),(48,21,'product_685a16acf0d1e8.22178879.png',1,'2025-06-24 03:08:28','2025-06-24 03:08:28'),(49,21,'product_685a16acf2e235.78655757.jpg',0,'2025-06-24 03:08:28','2025-06-24 03:08:28'),(50,21,'product_685a16ad006a08.86094894.png',0,'2025-06-24 03:08:29','2025-06-24 03:08:29'),(51,22,'product_685a19d1215d37.74628345.png',1,'2025-06-24 03:21:53','2025-06-24 03:21:53'),(52,22,'product_685a19d1226745.40432205.png',0,'2025-06-24 03:21:53','2025-06-24 03:21:53'),(53,23,'product_685a31f31a67c0.57035415.jpg',1,'2025-06-24 05:04:51','2025-06-24 05:04:51'),(54,23,'product_685a31f31bcb66.93342402.jpg',0,'2025-06-24 05:04:51','2025-06-24 05:04:51'),(55,23,'product_685a31f31cfb70.68097994.jpg',0,'2025-06-24 05:04:51','2025-06-24 05:04:51'),(56,24,'product_687fed713d66a5.96483918.png',1,'2025-07-22 19:58:41','2025-07-22 19:58:41'),(57,24,'product_687fed713e9749.58908902.png',0,'2025-07-22 19:58:41','2025-07-22 19:58:41');
/*!40000 ALTER TABLE `product_images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_price_history`
--

DROP TABLE IF EXISTS `product_price_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_price_history` (
  `history_id` bigint NOT NULL AUTO_INCREMENT,
  `product_id` bigint NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `currency` varchar(10) NOT NULL DEFAULT 'USD',
  `change_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`history_id`),
  KEY `fk_product_price_history_product` (`product_id`),
  CONSTRAINT `fk_product_price_history_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_price_history`
--

LOCK TABLES `product_price_history` WRITE;
/*!40000 ALTER TABLE `product_price_history` DISABLE KEYS */;
INSERT INTO `product_price_history` VALUES (1,10,563.00,'PLN','2025-06-22 05:55:14','2025-06-22 05:55:14','2025-06-22 05:55:14'),(2,11,113.00,'CAD','2025-06-22 10:46:46','2025-06-22 10:46:46','2025-06-22 10:46:46'),(3,12,113.00,'CAD','2025-06-22 10:47:07','2025-06-22 10:47:07','2025-06-22 10:47:07'),(4,13,113.00,'CAD','2025-06-22 10:47:31','2025-06-22 10:47:31','2025-06-22 10:47:31'),(5,14,113.00,'CAD','2025-06-22 10:47:35','2025-06-22 10:47:35','2025-06-22 10:47:35'),(6,15,113.00,'CAD','2025-06-22 10:47:47','2025-06-22 10:47:47','2025-06-22 10:47:47'),(7,16,113.00,'CAD','2025-06-22 10:47:49','2025-06-22 10:47:49','2025-06-22 10:47:49'),(8,17,319.00,'BRL','2025-06-22 10:49:18','2025-06-22 10:49:18','2025-06-22 10:49:18'),(9,18,608.00,'KRW','2025-06-22 10:50:31','2025-06-22 10:50:31','2025-06-22 10:50:31'),(10,19,738.00,'VND','2025-06-22 10:51:20','2025-06-22 10:51:20','2025-06-22 10:51:20'),(11,20,557.00,'RUB','2025-06-24 02:41:08','2025-06-24 02:41:08','2025-06-24 02:41:08'),(12,21,34.00,'PHP','2025-06-24 03:08:29','2025-06-24 03:08:29','2025-06-24 03:08:29'),(13,22,577.00,'PHP','2025-06-24 03:21:53','2025-06-24 03:21:53','2025-06-24 03:21:53'),(14,23,196.00,'EUR','2025-06-24 05:04:51','2025-06-24 05:04:51','2025-06-24 05:04:51'),(15,24,196.00,'EUR','2025-07-22 19:58:41','2025-07-22 19:58:41','2025-07-22 19:58:41'),(16,23,196.00,'EUR','2025-07-22 20:04:26','2025-07-22 20:04:26','2025-07-22 20:04:26'),(17,23,196.00,'EUR','2025-07-22 20:05:03','2025-07-22 20:05:03','2025-07-22 20:05:03'),(18,23,10.00,'EUR','2025-07-28 18:37:04','2025-07-28 18:37:04','2025-07-28 18:37:04');
/*!40000 ALTER TABLE `product_price_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `product_id` bigint NOT NULL AUTO_INCREMENT,
  `user_id` char(14) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_sku` varchar(100) NOT NULL,
  `product_category` bigint NOT NULL,
  `description` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('active','inactive','archived') DEFAULT 'active',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `product_weight` int DEFAULT NULL,
  PRIMARY KEY (`product_id`),
  KEY `fk_products_user` (`user_id`),
  KEY `fk_products_category` (`product_category`),
  CONSTRAINT `fk_products_category` FOREIGN KEY (`product_category`) REFERENCES `product_categories` (`category_id`),
  CONSTRAINT `fk_products_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (10,'650A-0543-J926','s','SKU-4152-27A5-0256',18,'Delectus enim et re','2025-06-22 05:55:14','active','2025-06-22 10:46:00',100),(11,'650A-0543-J926','Germane Byrd','SKU-4646-W522-F021',18,'Aute nisi sed tempor','2025-06-22 10:46:46','active','2025-06-22 10:46:46',38),(12,'650A-0543-J926','Germane Byrd','SKU-2525-0271-7042',18,'Aute nisi sed tempor','2025-06-22 10:47:07','active','2025-06-22 10:47:07',38),(13,'650A-0543-J926','Germane Byrd','SKU-1135-0Q26-7221',18,'Aute nisi sed tempor','2025-06-22 10:47:31','active','2025-06-22 10:47:31',38),(14,'650A-0543-J926','Germane Byrd','SKU-316B-7252-0452',18,'Aute nisi sed tempor','2025-06-22 10:47:35','active','2025-06-22 10:47:35',38),(15,'650A-0543-J926','Germane Byrd','SKU-5422-6N24-I7W0',18,'Aute nisi sed tempor','2025-06-22 10:47:47','active','2025-06-22 10:47:47',38),(16,'650A-0543-J926','Germane Byrd','SKU-07K2-2569-24I4',18,'Aute nisi sed tempor','2025-06-22 10:47:49','active','2025-06-22 10:47:49',38),(17,'650A-0543-J926','Mannix Richardson','SKU-412Y-6920-12H5',18,'Iusto nostrum consec','2025-06-22 10:49:18','archived','2025-06-22 10:49:18',9),(18,'650A-0543-J926','Iris Deleon','SKU-2619-052Q-4250',18,'Earum ea pariatur E','2025-06-22 10:50:31','archived','2025-06-22 10:50:31',75),(19,'650A-0543-J926','Athena Jacobs','SKU-A625-0922-M12E',18,'Cum laudantium dist','2025-06-22 10:51:20','inactive','2025-06-22 10:51:20',12),(20,'650A-0543-J926','Leigh Ochoa','SKU-8R21-024Y-05HF',18,'Laboriosam sed qui','2025-06-24 02:41:08','active','2025-06-24 02:41:08',7),(21,'650A-0543-J926','Teegan Oliver','SKU-5620-2C58-09ZV',3,'Aut saepe duis animi','2025-06-24 03:08:28','active','2025-06-24 03:08:28',83),(22,'650A-0543-J926','Tyrone Jenkins','SKU-0142-06Q5-A352',18,'Ea delectus tempore','2025-06-24 03:21:53','inactive','2025-06-24 03:21:53',77),(23,'241T-S405-0P32','Jenna Howe','SKU-4512-5072-D30Z',21,'Magna pariatur Aut','2025-06-24 05:04:51','active','2025-07-22 20:04:26',86223),(24,'241T-S405-0P32','Jenna Howe','SKU-1250-2D41-58A7',21,'Magna pariatur Aut','2025-07-22 19:58:41','active','2025-07-22 19:58:41',86);
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stock_movements`
--

DROP TABLE IF EXISTS `stock_movements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock_movements` (
  `movement_id` bigint NOT NULL AUTO_INCREMENT,
  `movement_number` varchar(50) NOT NULL,
  `product_id` bigint NOT NULL,
  `inventory_id` bigint NOT NULL,
  `quantity` int NOT NULL,
  `movement_type` enum('in','out') NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`movement_id`),
  UNIQUE KEY `movement_number` (`movement_number`),
  KEY `fk_stock_movements_product` (`product_id`),
  KEY `fk_stock_movements_inventory` (`inventory_id`),
  CONSTRAINT `fk_stock_movements_inventory` FOREIGN KEY (`inventory_id`) REFERENCES `inventory` (`inventory_id`),
  CONSTRAINT `fk_stock_movements_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_movements`
--

LOCK TABLES `stock_movements` WRITE;
/*!40000 ALTER TABLE `stock_movements` DISABLE KEYS */;
INSERT INTO `stock_movements` VALUES (1,'SM-06222025-093754',10,1,10,'in','ds','2025-06-22 07:37:54','2025-06-22 07:37:54'),(2,'SM-06222025-095205',10,1,5,'out','das','2025-06-22 07:52:05','2025-06-22 07:52:05'),(3,'SM-06232025-025300',10,1,-1,'out','Order ID: ORD-20250623-022217-YRV7','2025-06-23 00:53:00','2025-06-23 00:53:00'),(4,'SM-06232025-025330',10,1,-1,'out','Order ID: ORD-20250623-022017-PUJZ','2025-06-23 00:53:30','2025-06-23 00:53:30'),(5,'SM-06242025-063242',10,1,1,'out','Order ID: ORD-20250623-022424-FWZA','2025-06-24 04:32:42','2025-06-24 04:32:42'),(6,'SM-06242025-070536',23,2,100,'in','na','2025-06-24 05:05:36','2025-06-24 05:05:36'),(7,'SM-07222025-222916',23,2,1,'out','Order ID: ORD-20250720-064639-B3RH','2025-07-22 20:29:16','2025-07-22 20:29:16'),(8,'SM-07282025-204230',23,2,8,'out','Order ID: ORD-20250726-141647-08LZ','2025-07-28 18:42:30','2025-07-28 18:42:30'),(9,'SM-07282025-204459',23,2,10,'in','dasd','2025-07-28 18:44:59','2025-07-28 18:44:59'),(10,'SM-07292025-184233',23,2,1,'out','Order ID: ORD-20250729-182709-GQK5','2025-07-29 16:42:33','2025-07-29 16:42:33'),(11,'SM-07292025-184551',23,2,1,'out','Order ID: ORD-20250729-182709-GQK5','2025-07-29 16:45:51','2025-07-29 16:45:51'),(12,'SM-07292025-184626',23,2,1,'out','Order ID: ORD-20250729-182709-GQK5','2025-07-29 16:46:26','2025-07-29 16:46:26'),(13,'SM-07292025-185328',20,3,1,'out','Order ID: ORD-20250729-182305-U451','2025-07-29 16:53:28','2025-07-29 16:53:28'),(14,'SM-07292025-185415',23,2,1,'out','Order ID: ORD-20250729-180311-EZDX','2025-07-29 16:54:15','2025-07-29 16:54:15'),(15,'SM-07292025-185550',23,2,1,'out','Order ID: ORD-20250729-180257-MOU9','2025-07-29 16:55:50','2025-07-29 16:55:50');
/*!40000 ALTER TABLE `stock_movements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `store_profile`
--

DROP TABLE IF EXISTS `store_profile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `store_profile` (
  `store_id` bigint NOT NULL AUTO_INCREMENT,
  `user_id` char(14) NOT NULL,
  `store_name` varchar(255) NOT NULL,
  `store_description` text,
  `store_logo_url` text,
  `store_address` varchar(255) DEFAULT NULL,
  `store_phone` varchar(20) DEFAULT NULL,
  `store_email` varchar(150) DEFAULT NULL,
  `status` enum('active','inactive','archived') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`store_id`),
  KEY `fk_store_profile_user` (`user_id`),
  CONSTRAINT `fk_store_profile_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `store_profile`
--

LOCK TABLES `store_profile` WRITE;
/*!40000 ALTER TABLE `store_profile` DISABLE KEYS */;
INSERT INTO `store_profile` VALUES (1,'0206-5510-970L','dasdad','0913122224','store_logo_6857f2209889f4.18679119.png','0913122224','0913122224','dad@gmail.com','active','2025-06-22 12:08:00','2025-06-22 12:08:00'),(2,'0206-5510-970L','test','dasd','store_logo_6857f63cc6bcb2.83708896.png','dasd','3123321','adsda@gmail.com','active','2025-06-22 12:25:32','2025-06-22 12:25:32');
/*!40000 ALTER TABLE `store_profile` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_shipping_address`
--

DROP TABLE IF EXISTS `user_shipping_address`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_shipping_address` (
  `address_id` bigint NOT NULL AUTO_INCREMENT,
  `user_id` char(14) NOT NULL,
  `address_line` varchar(255) NOT NULL,
  `region` varchar(100) NOT NULL,
  `city` varchar(100) NOT NULL,
  `brgy` varchar(100) NOT NULL,
  `postal_code` varchar(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`address_id`),
  KEY `fk_order_shipping_address_user` (`user_id`),
  CONSTRAINT `fk_order_shipping_address_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_shipping_address`
--

LOCK TABLES `user_shipping_address` WRITE;
/*!40000 ALTER TABLE `user_shipping_address` DISABLE KEYS */;
INSERT INTO `user_shipping_address` VALUES (4,'650A-0543-J926','asdsadsadas','saddad','sdasd','sdasd','123212','2025-07-20 04:12:12','2025-07-20 04:12:12'),(5,'650A-0543-J926','dasdadsdd','asdsaad','asdad','dadds','21331','2025-07-20 04:21:19','2025-07-20 04:21:19'),(6,'650A-0543-J926','asddsada','dasdada','dadssdd','adas','1213','2025-07-20 04:24:11','2025-07-20 04:24:11');
/*!40000 ALTER TABLE `user_shipping_address` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `user_id` char(14) NOT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'user',
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `gender` enum('male','female') DEFAULT 'male',
  `avatar_url` text,
  `password` varchar(255) DEFAULT NULL,
  `is_google_auth` tinyint(1) DEFAULT '0',
  `google_id` varchar(50) DEFAULT NULL,
  `is_email_verified` tinyint(1) DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `google_id` (`google_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES ('0206-5510-970L','user','Wynne','Morrow','joqydep@mailinator.com','+1 (497) 627-7826',NULL,'male','','Pa$$w0rd!',0,NULL,0,1,'2025-06-05 05:39:56','2025-06-05 05:39:56',NULL),('0566-J531-5049','user','John Michael','Domingo','domingo.laden@gmail.com','',NULL,'male','https://lh3.googleusercontent.com/a/ACg8ocJovXyuatiGGvSSCfXpq6IU-koW8QIssl6Q4G5yniovaXwRF88p=s96-c',NULL,1,'106422103518120268035',1,1,'2025-06-08 14:34:59','2025-06-08 14:34:59',NULL),('241T-S405-0P32','supplier','ddsa','dasddasd','dadsd@gmail.com','09559786016',NULL,'male','','Laden8210',0,NULL,0,1,'2025-06-24 02:13:00','2025-06-24 02:13:00',NULL),('2602-M7XY-0322','courier','Wanda','Wilkerson','lawinofuj@mailinator.com','+1 (193) 308-5852',NULL,'male','','Pa$$w0rd!',0,NULL,0,1,'2025-07-06 11:25:12','2025-07-06 11:25:12',NULL),('2Z01-52Y6-2074','user','dasd','dasd','dasd@gmail.com','dadas',NULL,'male','','Laden8210',0,NULL,0,1,'2025-06-24 02:12:17','2025-06-24 02:12:17',NULL),('4263-004D-OX25','user','dasdadsa','dsdsd','ddsdsd@gmail.com','09123456768',NULL,'male','','Laden8210',0,NULL,0,1,'2025-06-24 02:09:23','2025-06-24 02:09:23',NULL),('650A-0543-J926','client','John Michael','Domingo','kimatong874@gmail.com','09559786019',NULL,'male','','Laden8210',0,NULL,0,1,'2025-06-09 04:34:23','2025-06-24 05:02:02',NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `warehouse`
--

DROP TABLE IF EXISTS `warehouse`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `warehouse` (
  `warehouse_id` bigint NOT NULL AUTO_INCREMENT,
  `user_id` char(14) NOT NULL,
  `warehouse_name` varchar(255) NOT NULL,
  `warehouse_address` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`warehouse_id`),
  KEY `fk_warehouse_user` (`user_id`),
  CONSTRAINT `fk_warehouse_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `warehouse`
--

LOCK TABLES `warehouse` WRITE;
/*!40000 ALTER TABLE `warehouse` DISABLE KEYS */;
INSERT INTO `warehouse` VALUES (2,'650A-0543-J926','dsdd','dsd','2025-06-24 02:52:58','2025-06-24 02:52:58');
/*!40000 ALTER TABLE `warehouse` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-07-30  4:12:01
