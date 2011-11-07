-- MySQL dump 10.13  Distrib 5.1.58, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: nitroxy_retail
-- ------------------------------------------------------
-- Server version	5.1.58-1ubuntu1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `account`
--

DROP TABLE IF EXISTS `account`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `account` (
  `account_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) COLLATE utf8_swedish_ci NOT NULL,
  `default_sign` enum('debit','kredit') COLLATE utf8_swedish_ci NOT NULL,
  `warn_on_non_default` tinyint(1) NOT NULL DEFAULT '0',
  `description` text COLLATE utf8_swedish_ci,
  `account_type` enum('balance','result') COLLATE utf8_swedish_ci DEFAULT NULL,
  `code_name` varchar(16) COLLATE utf8_swedish_ci DEFAULT NULL,
  PRIMARY KEY (`account_id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `code_name` (`code_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `account_transaction`
--

DROP TABLE IF EXISTS `account_transaction`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `account_transaction` (
  `account_transaction_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `description` text COLLATE utf8_swedish_ci,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`account_transaction_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `account_transaction_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `account_transaction_contents`
--

DROP TABLE IF EXISTS `account_transaction_contents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `account_transaction_contents` (
  `account_transaction_id` int(10) unsigned NOT NULL,
  `account_id` int(10) unsigned NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  PRIMARY KEY (`account_transaction_id`,`account_id`),
  KEY `account_id` (`account_id`),
  CONSTRAINT `account_transaction_contents_ibfk_1` FOREIGN KEY (`account_transaction_id`) REFERENCES `account_transaction` (`account_transaction_id`),
  CONSTRAINT `account_transaction_contents_ibfk_2` FOREIGN KEY (`account_id`) REFERENCES `account` (`account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `category_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) COLLATE utf8_swedish_ci NOT NULL,
  PRIMARY KEY (`category_id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `daily_count`
--

DROP TABLE IF EXISTS `daily_count`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `daily_count` (
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `amount` decimal(10,2) NOT NULL,
  `account_transaction_id` int(10) unsigned DEFAULT NULL,
  `user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`time`),
  KEY `account_transaction_id` (`account_transaction_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `deliveries`
--

DROP TABLE IF EXISTS `deliveries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `deliveries` (
  `delivery_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `description` text COLLATE utf8_swedish_ci NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`delivery_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `deliveries_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `delivery_contents`
--

DROP TABLE IF EXISTS `delivery_contents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `delivery_contents` (
  `delivery_id` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `count` int(11) NOT NULL,
  `cost` decimal(8,4) NOT NULL,
  PRIMARY KEY (`delivery_id`,`product_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `delivery_contents_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`),
  CONSTRAINT `delivery_contents_ibfk_2` FOREIGN KEY (`delivery_id`) REFERENCES `deliveries` (`delivery_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_log`
--

DROP TABLE IF EXISTS `product_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_log` (
  `product_log_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(11) unsigned NOT NULL,
  `old_price` decimal(10,2) NOT NULL,
  `new_price` decimal(10,2) NOT NULL,
  `changed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`product_log_id`),
  KEY `product_id` (`product_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `product_log_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  CONSTRAINT `product_log_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_package`
--

DROP TABLE IF EXISTS `product_package`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_package` (
  `product_id` int(10) unsigned NOT NULL,
  `package` int(10) unsigned NOT NULL,
  `count` smallint(6) NOT NULL DEFAULT '1',
  PRIMARY KEY (`product_id`,`package`),
  KEY `package` (`package`),
  CONSTRAINT `product_package_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`),
  CONSTRAINT `product_package_ibfk_2` FOREIGN KEY (`package`) REFERENCES `products` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `products` (
  `product_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_swedish_ci NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `ean` varchar(30) COLLATE utf8_swedish_ci NOT NULL,
  `category_id` int(10) unsigned NOT NULL,
  `value` decimal(8,4) NOT NULL,
  `count` int(11) NOT NULL DEFAULT '0',
  `inventory_threshold` int(10) unsigned DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`product_id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `ean` (`ean`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `transaction_contents`
--

DROP TABLE IF EXISTS `transaction_contents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transaction_contents` (
  `transaction_id` int(11) unsigned NOT NULL,
  `product_id` int(11) unsigned NOT NULL,
  `count` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `stock_usage` decimal(10,4) NOT NULL DEFAULT '0.0000',
  PRIMARY KEY (`transaction_id`,`product_id`),
  KEY `product_id` (`product_id`),
  KEY `transaction_id` (`transaction_id`),
  CONSTRAINT `transaction_contents_ibfk_2` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`transaction_id`),
  CONSTRAINT `transaction_contents_ibfk_3` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `transactions`
--

DROP TABLE IF EXISTS `transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transactions` (
  `transaction_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `amount` decimal(10,2) NOT NULL,
  PRIMARY KEY (`transaction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `user_id` int(10) unsigned NOT NULL,
  `first_name` varchar(100) COLLATE utf8_swedish_ci NOT NULL,
  `surname` varchar(100) COLLATE utf8_swedish_ci NOT NULL,
  `username` varchar(100) COLLATE utf8_swedish_ci NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping routines for database 'nitroxy_retail'
--
/*!50003 DROP PROCEDURE IF EXISTS `merge_products` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `merge_products`( IN _main int, IN _merge int )
    MODIFIES SQL DATA
BEGIN
	DECLARE _value decimal(10,4);
	DECLARE _count int;
	DECLARE _inventory_threshold int;
	
	
	INSERT INTO transaction_contents (transaction_id, product_id, count, amount, stock_usage)
		(SELECT transaction_id, _main, count, amount, stock_usage FROM transaction_contents AS tc WHERE tc.product_id = _merge)
		ON DUPLICATE KEY UPDATE
			count = transaction_contents.count + tc.count,
			stock_usage = transaction_contents.stock_usage + tc.stock_usage,
			amount = transaction_contents.amount + tc.amount;
	DELETE FROM transaction_contents WHERE product_id = _merge;

	
	DELETE FROM product_log WHERE product_id = _merge;
	
	
	INSERT INTO delivery_contents (delivery_id, product_id, count, cost)
		(SELECT delivery_id, _main, count, cost FROM delivery_contents AS dc WHERE dc.product_id = _merge)
		ON DUPLICATE KEY UPDATE
			cost =  COALESCE((delivery_contents.cost * delivery_contents.count + dc.cost * dc.count) / (delivery_contents.count + dc.count), delivery_contents.cost),
			count = delivery_contents.count + dc.count;
	DELETE FROM delivery_contents WHERE product_id = _merge;

	
	UPDATE product_package SET product_id = _main WHERE product_id = _merge;
	UPDATE product_package SET package = _main WHERE package = _merge;

	
	SELECT count, value, inventory_threshold INTO _count, _value, _inventory_threshold FROM products WHERE product_id = _merge;
	UPDATE products SET
		value = COALESCE((value * count + _count * _value) / (count + _count), value),
		count = count + _count,
		inventory_threshold = COALESCE(inventory_threshold, _inventory_threshold)
		WHERE products.product_id = _main;
	
	
	DELETE FROM products WHERE product_id = _merge;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2011-11-07  8:40:14
