-- -------------------------------------------------------------
-- TablePlus 6.1.8(574)
--
-- https://tableplus.com/
--
-- Database: sportswh
-- Generation Time: 2024-11-26 21:03:48.8450
-- -------------------------------------------------------------


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


CREATE TABLE `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `slug` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'inactive',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `category_slug_unique` (`slug`) USING BTREE,
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

CREATE TABLE `contacts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) NOT NULL DEFAULT '',
  `last_name` varchar(255) NOT NULL DEFAULT '',
  `contact` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `message` varchar(255) DEFAULT '',
  `mailing_list` tinyint(1) DEFAULT '0',
  `has_been_contacted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=156 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `order_product` (
  `product_id` int NOT NULL,
  `order_id` int NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`product_id`,`order_id`),
  KEY `order_id` (`order_id`),
  CONSTRAINT `order_product_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `order_product_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `status` varchar(50) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `address` varchar(200) NOT NULL,
  `contact_number` varchar(20) NOT NULL,
  `card_number` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `expiry_date` varchar(10) NOT NULL,
  `card_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `ccv` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `purchase_date` datetime NOT NULL,
  `total` float NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `password_resets` (
  `id` int NOT NULL AUTO_INCREMENT,
  `token` varchar(255) NOT NULL,
  `email` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `slug` varchar(150) DEFAULT NULL,
  `image` varchar(250) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `sale_price` decimal(10,2) DEFAULT NULL,
  `description` varchar(2000) DEFAULT NULL,
  `featured` tinyint(1) NOT NULL DEFAULT '0',
  `category_id` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`) USING BTREE,
  UNIQUE KEY `item_slug_unique` (`slug`) USING BTREE,
  KEY `fk_item_category` (`category_id`),
  FULLTEXT KEY `name` (`name`),
  FULLTEXT KEY `ft_name` (`name`) /*!50100 WITH PARSER `ngram` */ ,
  FULLTEXT KEY `idx_name_description` (`name`,`description`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;

CREATE TABLE `role_user` (
  `user_id` int NOT NULL,
  `role_id` int NOT NULL,
  `assigned_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`,`role_id`),
  KEY `role_id` (`role_id`),
  CONSTRAINT `role_user_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_user_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `roles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQUE_EMAIL` (`email`) USING BTREE,
  UNIQUE KEY `UNIQUE_USERNAME` (`username`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `categories` (`id`, `slug`, `name`, `status`, `updated_at`, `created_at`) VALUES
(1, 'shoes', 'Shoes', 'active', '2024-05-16 22:27:20', '2024-06-13 13:08:15'),
(2, 'helmets', 'Helmets', 'active', '2024-05-16 22:27:20', '2024-06-13 13:04:50'),
(3, 'pants', 'Pants', 'active', '2024-05-16 22:27:20', '2024-06-12 19:37:44'),
(4, 'tops', 'Tops', 'active', '2024-05-16 22:27:20', '2024-06-12 19:37:44'),
(5, 'balls', 'Balls', 'active', '2024-05-16 22:27:20', '2024-06-12 19:37:44'),
(6, 'equipment', 'Equipment', 'active', '2024-05-16 22:27:20', '2024-06-12 19:37:44'),
(7, 'training-gear', 'Training Gear', 'active', '2024-05-16 22:27:20', '2024-06-12 19:37:44');

INSERT INTO `contacts` (`id`, `first_name`, `last_name`, `contact`, `email`, `message`, `mailing_list`, `has_been_contacted`, `created_at`, `updated_at`) VALUES
(1, 'Cameron', 'Kemshal-Bell', '0400155755', 'cam@panr.app', 'Here is the mailing list', 1, 0, '2024-11-19 20:32:53', '2024-11-19 20:32:53'),
(2, 'Cameron', 'Kemshal-Bell', '0400155755', 'user@admin.com', 'Lets see if this works', 1, 0, '2024-11-19 20:43:37', '2024-11-19 20:43:37'),
(3, 'Cameron', 'Kemshal-Bell', '0400155755', 'admin@user.com', 'dssdvcscsd', 1, 0, '2024-11-19 20:43:49', '2024-11-19 20:43:49'),
(4, 'Cameron', 'Kemshal-Bell', '0400155755', 'camkb85@gmail.com', 'fddcdcs dcvdccsdcscdc\r\ndcsdcscdcsd', 1, 0, '2024-11-19 20:44:41', '2024-11-19 20:44:41'),
(5, 'Cameron', 'Kemshal-Bell', '0400155755', 'camkb85@gmail.com', 'fddcdcs dcvdccsdcscdc\r\ndcsdcscdcsd', 1, 0, '2024-11-19 20:44:55', '2024-11-19 20:44:55'),
(6, 'Cameron', 'Kemshal-Bell', '0400155755', 'camkb85@gmail.com', 'fddcdcs dcvdccsdcscdc\r\ndcsdcscdcsd', 1, 0, '2024-11-19 20:45:08', '2024-11-19 20:45:08'),
(7, 'Cameron', 'Kemshal-Bell', '0400155755', 'camkb85@gmail.com', 'fddcdcs dcvdccsdcscdc\r\ndcsdcscdcsd', 1, 0, '2024-11-19 20:46:17', '2024-11-19 20:46:17'),
(8, 'Cameron', 'Kemshal-Bell', '0400155755', 'camkb85@gmail.com', 'fddcdcs dcvdccsdcscdc\r\ndcsdcscdcsd', 1, 0, '2024-11-19 20:48:33', '2024-11-19 20:48:33'),
(9, 'Cameron', 'Kemshal-Bell', '0400155755', 'camkb85@gmail.com', 'fddcdcs dcvdccsdcscdc\r\ndcsdcscdcsd', 1, 0, '2024-11-19 20:51:34', '2024-11-19 20:51:34'),
(10, 'Cameron', 'Kemshal-Bell', '0400155755', 'camkb85@gmail.com', 'fddcdcs dcvdccsdcscdc\r\ndcsdcscdcsd', 1, 0, '2024-11-19 20:51:50', '2024-11-19 20:51:50'),
(11, 'Cameron', 'Kemshal-Bell', '0400155755', 'camkb@icloud.com', 'dcsdcdsc dscds csccdccsd', 0, 0, '2024-11-19 21:01:49', '2024-11-19 21:01:49'),
(12, 'Cameron', 'Kemshal-Bell', '0400155755', 'cam@panr.app', 'dscdc sdcsdc sdcsd dcdscsdc cdc', 1, 0, '2024-11-19 21:02:02', '2024-11-19 21:02:02'),
(13, 'Cameron', 'Kemshal-Bell', '0400155755', 'cam@panr.app', 'dscdc sdcsdc sdcsd dcdscsdc cdc', 0, 0, '2024-11-19 21:02:44', '2024-11-19 21:02:44'),
(14, 'Cameron', 'Kemshal-Bell', '0400155755', 'cam@panr.app', 'dscdc sdcsdc sdcsd dcdscsdc cdc', 0, 0, '2024-11-19 21:03:24', '2024-11-19 21:03:24'),
(15, 'Cameron', 'Kemshal-Bell', '0400155755', 'cam@panr.app', 'dscdc sdcsdc sdcsd dcdscsdc cdc', 0, 0, '2024-11-19 21:04:22', '2024-11-19 21:04:22'),
(16, 'Cameron', 'Kemshal-Bell', '0400155755', 'cam@panr.app', 'dscdc sdcsdc sdcsd dcdscsdc cdc', 0, 0, '2024-11-19 21:04:50', '2024-11-19 21:04:50'),
(17, 'Cameron', 'Kemshal-Bell', '0400155755', 'cam@panr.app', 'dscdc sdcsdc sdcsd dcdscsdc cdc', 0, 0, '2024-11-19 21:05:18', '2024-11-19 21:05:18'),
(18, 'Cameron', 'Kemshal-Bell', '0400155755', 'cam@panr.app', 'dscdc sdcsdc sdcsd dcdscsdc cdc', 0, 0, '2024-11-19 21:05:36', '2024-11-19 21:05:36'),
(19, 'Cameron', 'Kemshal-Bell', '0400155755', 'cam@panr.app', 'dscdc sdcsdc sdcsd dcdscsdc cdc', 0, 0, '2024-11-19 21:06:03', '2024-11-19 21:06:03'),
(20, 'Cameron', 'Kemshal-Bell', '0400155755', 'camkb@icloud.com', 'MessGEDFF DFDSF', 0, 0, '2024-11-21 19:00:08', '2024-11-21 19:00:08'),
(21, 'Cameron', 'Kemshal-Bell', '0400155755', 'camkb@icloud.com', 'MessGEDFF DFDSF', 0, 0, '2024-11-21 19:02:22', '2024-11-21 19:02:22'),
(22, 'Cameron', 'Kemshal-Bell', '0400155755', 'camkb@icloud.com', 'MessGEDFF DFDSF', 0, 0, '2024-11-21 19:03:05', '2024-11-21 19:03:05'),
(23, 'Cameron', 'Kemshal-Bell', '0400155755', 'camkb@icloud.com', 'MessGEDFF DFDSF', 0, 0, '2024-11-21 19:03:36', '2024-11-21 19:03:36'),
(24, 'Cameron', 'Kemshal-Bell', '0400155755', 'camkb@icloud.com', 'MessGEDFF DFDSF', 0, 0, '2024-11-21 19:04:44', '2024-11-21 19:04:44'),
(25, 'Cameron', 'Kemshal-Bell', '0400155755', 'camkb@icloud.com', 'MessGEDFF DFDSF', 0, 0, '2024-11-21 19:04:56', '2024-11-21 19:04:56'),
(26, 'Cameron', 'Kemshal-Bell', '0400155755', 'camkb@icloud.com', 'MessGEDFF DFDSF', 0, 0, '2024-11-21 19:05:22', '2024-11-21 19:05:22'),
(27, 'Cameron', 'Kemshal-Bell', '0400155755', 'camkb@icloud.com', 'sadcdscddsdc dscsdcd dscddsds dcsdc sdcsdcs', 0, 0, '2024-11-21 19:06:43', '2024-11-21 19:06:43'),
(28, 'Cameron', 'Kemshal-Bell', '0400155755', 'camkb@icloud.com', 'sadcdscddsdc dscsdcd dscddsds dcsdc sdcsdcs', 0, 0, '2024-11-21 19:09:19', '2024-11-21 19:09:19'),
(29, 'Cameron', 'Kemshal-Bell', '0400155755', 'camkb@icloud.com', 'sadcdscddsdc dscsdcd dscddsds dcsdc sdcsdcs', 0, 0, '2024-11-21 19:43:41', '2024-11-21 19:43:41'),
(30, 'Cameron', 'Kemshal-Bell', '0400155755', 'cam@panr.app', 'sdcdscs dcdsdscdsc dscdscdsc sd cdscdscds', 0, 0, '2024-11-21 20:25:37', '2024-11-21 20:25:37'),
(31, 'Cameron', 'Kemshal-Bell', '0400155755', 'cam@panr.app', 'sdcdscs dcdsdscdsc dscdscdsc sd cdscdscds', 0, 0, '2024-11-21 20:28:34', '2024-11-21 20:28:34'),
(32, 'Cameron', 'Kemshal-Bell', '0400155755', 'cam@panr.app', 'sdcdscs dcdsdscdsc dscdscdsc sd cdscdscds', 0, 0, '2024-11-21 20:32:43', '2024-11-21 20:32:43'),
(33, 'Cameron', 'Kemshal-Bell', '0400155755', 'cam@panr.app', 'sdcdscs dcdsdscdsc dscdscdsc sd cdscdscds', 0, 0, '2024-11-21 20:32:56', '2024-11-21 20:32:56'),
(34, 'Cameron', 'Kemshal-Bell', '0400155755', 'cam@panr.app', 'sdcdscs dcdsdscdsc dscdscdsc sd cdscdscds', 0, 0, '2024-11-21 20:34:24', '2024-11-21 20:34:24'),
(35, 'Cameron', 'Kemshal-Bell', '0400155755', 'cam@panr.app', 'sdcdscs dcdsdscdsc dscdscdsc sd cdscdscds', 0, 0, '2024-11-21 20:35:00', '2024-11-21 20:35:00'),
(36, 'Cameron', 'Kemshal-Bell', '0400155755', 'cam@panr.app', 'sdcdscs dcdsdscdsc dscdscdsc sd cdscdscds', 0, 0, '2024-11-21 20:35:35', '2024-11-21 20:35:35'),
(37, 'Cameron', 'Kemshal-Bell', '0400155755', 'cam@panr.app', 'sdcdscs dcdsdscdsc dscdscdsc sd cdscdscds', 0, 0, '2024-11-21 20:35:52', '2024-11-21 20:35:52'),
(38, 'Cameron', 'Kemshal-Bell', '0400155755', 'cam@panr.app', 'sdcdscs dcdsdscdsc dscdscdsc sd cdscdscds', 0, 0, '2024-11-21 20:37:02', '2024-11-21 20:37:02'),
(39, 'Cameron', 'Kemshal-Bell', '0400155755', 'cam@panr.app', 'sdcdscs dcdsdscdsc dscdscdsc sd cdscdscds', 0, 0, '2024-11-21 20:38:06', '2024-11-21 20:38:06'),
(40, 'Cameron', 'Kemshal-Bell', '0400155755', 'cam@panr.app', 'sdcdscs dcdsdscdsc dscdscdsc sd cdscdscds', 0, 0, '2024-11-21 20:39:34', '2024-11-21 20:39:34'),
(41, 'Cameron', 'Kemshal-Bell', '0400155755', 'cam@panr.app', 'sdcdscs dcdsdscdsc dscdscdsc sd cdscdscds', 0, 0, '2024-11-21 20:39:43', '2024-11-21 20:39:43'),
(42, 'Cameron', 'Kemshal-Bell', '0400155755', 'user@email.com', 'dsdssd dfs df dsf sdfd sf fsd f dsf sdf s dds sf dsf sd', 0, 0, '2024-11-21 20:42:25', '2024-11-21 20:42:25'),
(43, 'Cameron', 'Kemshal-Bell', '0400155755', 'user@email.com', 'dsdssd dfs df dsf sdfd sf fsd f dsf sdf s dds sf dsf sd', 0, 0, '2024-11-21 20:43:12', '2024-11-21 20:43:12'),
(44, 'Cameron', 'Kemshal-Bell', '0400155755', 'user@email.com', 'dsdssd dfs df dsf sdfd sf fsd f dsf sdf s dds sf dsf sd', 0, 0, '2024-11-21 20:43:22', '2024-11-21 20:43:22'),
(45, 'Cameron', 'Kemshal-Bell', '0400155755', 'user@email.com', 'dsdssd dfs df dsf sdfd sf fsd f dsf sdf s dds sf dsf sd', 0, 0, '2024-11-21 20:43:25', '2024-11-21 20:43:25'),
(46, 'Cameron', 'Kemshal-Bell', '0400155755', 'user@email.com', 'dsdssd dfs df dsf sdfd sf fsd f dsf sdf s dds sf dsf sd', 0, 0, '2024-11-21 20:43:27', '2024-11-21 20:43:27'),
(47, 'Cameron', 'Kemshal-Bell', '0400155755', 'user@email.com', 'dsdssd dfs df dsf sdfd sf fsd f dsf sdf s dds sf dsf sd', 0, 0, '2024-11-21 20:44:10', '2024-11-21 20:44:10'),
(48, 'Cameron', 'Kemshal-Bell', '0400155755', 'user@email.com', 'dsdssd dfs df dsf sdfd sf fsd f dsf sdf s dds sf dsf sd', 0, 0, '2024-11-21 20:44:14', '2024-11-21 20:44:14'),
(49, 'Cameron', 'Kemshal-Bell', '0400155755', 'user@email.com', 'dsdssd dfs df dsf sdfd sf fsd f dsf sdf s dds sf dsf sd', 0, 0, '2024-11-21 20:45:07', '2024-11-21 20:45:07'),
(50, 'Cameron', 'Kemshal-Bell', '0400155755', 'user@email.com', 'dsdssd dfs df dsf sdfd sf fsd f dsf sdf s dds sf dsf sd', 0, 0, '2024-11-21 20:46:43', '2024-11-21 20:46:43'),
(51, 'Cameron', 'Kemshal-Bell', '0400155755', 'user@email.com', 'dsdssd dfs df dsf sdfd sf fsd f dsf sdf s dds sf dsf sd', 0, 0, '2024-11-21 20:47:23', '2024-11-21 20:47:23'),
(52, 'Cameron', 'Kemshal-Bell', '0400155755', 'user@email.com', 'dsdssd dfs df dsf sdfd sf fsd f dsf sdf s dds sf dsf sd', 0, 0, '2024-11-21 20:47:56', '2024-11-21 20:47:56'),
(53, 'Cameron', 'Kemshal-Bell', '0400155755', 'user@email.com', 'dsdssd dfs df dsf sdfd sf fsd f dsf sdf s dds sf dsf sd', 0, 0, '2024-11-21 20:48:14', '2024-11-21 20:48:14'),
(54, 'Cameron', 'Kemshal-Bell', '0400155755', 'user@email.com', 'dsdssd dfs df dsf sdfd sf fsd f dsf sdf s dds sf dsf sd', 0, 0, '2024-11-21 20:48:59', '2024-11-21 20:48:59'),
(55, 'Cameron', 'Kemshal-Bell', '0400155755', 'user@email.com', 'dsdssd dfs df dsf sdfd sf fsd f dsf sdf s dds sf dsf sd', 0, 0, '2024-11-21 20:50:17', '2024-11-21 20:50:17'),
(56, 'Cameron', 'Kemshal-Bell', '0400155755', 'user@email.com', 'dsdssd dfs df dsf sdfd sf fsd f dsf sdf s dds sf dsf sd', 0, 0, '2024-11-21 20:50:21', '2024-11-21 20:50:21'),
(57, 'Cameron', 'Kemshal-Bell', '0400155755', 'user@email.com', 'dsdssd dfs df dsf sdfd sf fsd f dsf sdf s dds sf dsf sd', 0, 0, '2024-11-21 20:51:08', '2024-11-21 20:51:08'),
(58, 'Cameron', 'Kemshal-Bell', '0400155755', 'user@email.com', 'dsdssd dfs df dsf sdfd sf fsd f dsf sdf s dds sf dsf sd', 0, 0, '2024-11-21 20:51:27', '2024-11-21 20:51:27'),
(59, 'Cameron', 'Kemshal-Bell', '0400155755', 'user@email.com', 'dsdssd dfs df dsf sdfd sf fsd f dsf sdf s dds sf dsf sd', 0, 0, '2024-11-21 20:51:55', '2024-11-21 20:51:55'),
(60, 'Cameron', 'Kemshal-Bell', '0400155755', 'user@email.com', 'dsdssd dfs df dsf sdfd sf fsd f dsf sdf s dds sf dsf sd', 0, 0, '2024-11-21 20:52:46', '2024-11-21 20:52:46'),
(61, 'Cameron', 'Kemshal-Bell', '0400155755', 'user@email.com', 'dsdssd dfs df dsf sdfd sf fsd f dsf sdf s dds sf dsf sd', 0, 0, '2024-11-21 20:53:14', '2024-11-21 20:53:14'),
(62, 'Cameron', 'Kemshal-Bell', '0400155755', 'user@email.com', 'dsdssd dfs df dsf sdfd sf fsd f dsf sdf s dds sf dsf sd', 0, 0, '2024-11-21 20:53:45', '2024-11-21 20:53:45'),
(63, 'Cameron', 'Kemshal-Bell', '0400155755', 'user@email.com', 'dsdssd dfs df dsf sdfd sf fsd f dsf sdf s dds sf dsf sd', 0, 0, '2024-11-21 20:54:05', '2024-11-21 20:54:05'),
(64, 'Cameron', 'Kemshal-Bell', '0400155755', 'user@email.com', 'dsdssd dfs df dsf sdfd sf fsd f dsf sdf s dds sf dsf sd', 0, 0, '2024-11-21 20:57:12', '2024-11-21 20:57:12'),
(65, 'Cameron', 'Kemshal-Bell', '0400155755', 'user@email.com', 'dsdssd dfs df dsf sdfd sf fsd f dsf sdf s dds sf dsf sd', 0, 0, '2024-11-21 20:57:24', '2024-11-21 20:57:24'),
(66, 'Cameron', 'Kemshal-Bell', '0400155755', 'sdcsdcdsc@example.co.uk', 'dfds sdf sdfds fds fsd fds fds fdsfdsdsf df df', 0, 0, '2024-11-21 21:01:39', '2024-11-21 21:01:39'),
(67, 'Cameron', 'Kemshal-Bell', '0400155755', 'sdcsdcdsc@example.co.uk', 'dfds sdf sdfds fds fsd fds fds fdsfdsdsf df df', 0, 0, '2024-11-21 21:02:53', '2024-11-21 21:02:53'),
(68, 'Cameron', 'Kemshal-Bell', '0400155755', 'sdcsdcdsc@example.co.uk', 'dfds sdf sdfds fds fsd fds fds fdsfdsdsf df df', 0, 0, '2024-11-21 21:04:17', '2024-11-21 21:04:17'),
(69, 'Cameron', 'Kemshal-Bell', '0400155755', 'sdcsdcdsc@example.co.uk', 'dfds sdf sdfds fds fsd fds fds fdsfdsdsf df df', 0, 0, '2024-11-21 21:04:48', '2024-11-21 21:04:48'),
(70, 'Cameron', 'Kemshal-Bell', '0400155755', 'camkb@icloud.com', 'sadcdscddsdc dscsdcd dscddsds dcsdc sdcsdcs', 0, 0, '2024-11-21 21:05:42', '2024-11-21 21:05:42'),
(71, 'Cameron', 'Kemshal-Bell', '0400155755', 'camkb@icloud.com', 'sadcdscddsdc dscsdcd dscddsds dcsdc sdcsdcs', 0, 0, '2024-11-21 21:06:22', '2024-11-21 21:06:22'),
(72, 'Cameron', 'Kemshal-Bell', '0400155755', 'camkb@icloud.com', 'sadcdscddsdc dscsdcd dscddsds dcsdc sdcsdcs', 0, 0, '2024-11-21 21:06:48', '2024-11-21 21:06:48'),
(73, 'Cameron', 'Kemshal-Bell', '0400155755', 'camkb@icloud.com', 'sadcdscddsdc dscsdcd dscddsds dcsdc sdcsdcs', 0, 0, '2024-11-21 21:16:36', '2024-11-21 21:16:36'),
(74, 'Cameron', 'Kemshal-Bell', '0400155755', 'camkb@icloud.com', 'sadcdscddsdc dscsdcd dscddsds dcsdc sdcsdcs', 0, 0, '2024-11-21 21:17:14', '2024-11-21 21:17:14'),
(75, 'Cameron', 'Kemshal-Bell', '0400155755', 'camkb@icloud.com', 'sadcdscddsdc dscsdcd dscddsds dcsdc sdcsdcs', 0, 0, '2024-11-21 21:21:53', '2024-11-21 21:21:53'),
(76, 'Cameron', 'Kemshal-Bell', '0400155755', 'camkb@icloud.com', 'sadcdscddsdc dscsdcd dscddsds dcsdc sdcsdcs', 0, 0, '2024-11-21 21:24:22', '2024-11-21 21:24:22'),
(77, 'Cameron', 'Kemshal-Bell', '0400155755', 'camkb@icloud.com', 'sadcdscddsdc dscsdcd dscddsds dcsdc sdcsdcs', 0, 0, '2024-11-21 21:25:38', '2024-11-21 21:25:38'),
(78, 'Cameron', 'Kemshal-Bell', '0400155755', 'camkb@icloud.com', 'sadcdscddsdc dscsdcd dscddsds dcsdc sdcsdcs', 0, 0, '2024-11-21 21:26:12', '2024-11-21 21:26:12'),
(79, 'Cameron', 'Kemshal-Bell', '0400155755', 'camkb@icloud.com', 'sadcdscddsdc dscsdcd dscddsds dcsdc sdcsdcs', 0, 0, '2024-11-21 21:26:53', '2024-11-21 21:26:53'),
(80, 'Cameron', 'Kemshal-Bell', '0400155755', 'camkb@icloud.com', 'sadcdscddsdc dscsdcd dscddsds dcsdc sdcsdcs', 0, 0, '2024-11-21 21:39:03', '2024-11-21 21:39:03'),
(81, 'Cameron', 'Kemshal-Bell', '0400155755', 'camkb@icloud.com', 'sadcdscddsdc dscsdcd dscddsds dcsdc sdcsdcs', 0, 0, '2024-11-21 21:42:06', '2024-11-21 21:42:06'),
(82, 'Cameron', 'Kemshal-Bell', '0400155755', 'camkb@icloud.com', 'sadcdscddsdc dscsdcd dscddsds dcsdc sdcsdcs', 0, 0, '2024-11-21 21:47:31', '2024-11-21 21:47:31'),
(83, 'Cameron', 'Kemshal-Bell', '0400155755', 'camkb@icloud.com', 'sadcdscddsdc dscsdcd dscddsds dcsdc sdcsdcs', 0, 0, '2024-11-21 21:47:58', '2024-11-21 21:47:58'),
(84, 'Cameron', 'Kemshal-Bell', '0400155755', 'camkb@icloud.com', 'sadcdscddsdc dscsdcd dscddsds dcsdc sdcsdcs', 0, 0, '2024-11-21 21:48:31', '2024-11-21 21:48:31'),
(85, 'Cameron', 'Kemshal-Bell', '0400155755', 'camkb@icloud.com', 'sadcdscddsdc dscsdcd dscddsds dcsdc sdcsdcs', 0, 0, '2024-11-21 21:48:36', '2024-11-21 21:48:36'),
(86, 'Cameron', 'Kemshal-Bell', '0400155755', 'camkb@icloud.com', 'sadcdscddsdc dscsdcd dscddsds dcsdc sdcsdcs', 0, 0, '2024-11-21 21:51:01', '2024-11-21 21:51:01'),
(87, 'Cameron', 'Kemshal-Bell', '0400155755', 'camkb@icloud.com', 'sadcdscddsdc dscsdcd dscddsds dcsdc sdcsdcs', 0, 0, '2024-11-21 21:51:21', '2024-11-21 21:51:21'),
(88, 'Cameron', 'Kemshal-Bell', '0400155755', 'camkb@icloud.com', 'sadcdscddsdc dscsdcd dscddsds dcsdc sdcsdcs', 0, 0, '2024-11-21 21:51:43', '2024-11-21 21:51:43'),
(89, 'Cameron', 'Kemshal-Bell', '0400155755', 'camkb@icloud.com', 'sadcdscddsdc dscsdcd dscddsds dcsdc sdcsdcs', 0, 0, '2024-11-21 21:51:56', '2024-11-21 21:51:56'),
(90, 'Cameron', 'Kemshal-Bell', '0400155755', 'camkb@icloud.com', 'sadcdscddsdc dscsdcd dscddsds dcsdc sdcsdcs', 0, 0, '2024-11-21 21:52:10', '2024-11-21 21:52:10'),
(91, 'Cameron', 'Kemshal-Bell', '0400155755', 'camkb@icloud.com', 'sadcdscddsdc dscsdcd dscddsds dcsdc sdcsdcs', 0, 0, '2024-11-21 21:54:07', '2024-11-21 21:54:07'),
(92, 'Cameron', 'Kemshal-Bell', '0400155755', 'camkb@icloud.com', 'sadcdscddsdc dscsdcd dscddsds dcsdc sdcsdcs', 0, 0, '2024-11-21 21:54:23', '2024-11-21 21:54:23'),
(93, 'Cameron', 'Kemshal-Bell', '0400155755', 'camkb@icloud.com', 'sadcdscddsdc dscsdcd dscddsds dcsdc sdcsdcs', 0, 0, '2024-11-21 21:55:56', '2024-11-21 21:55:56'),
(94, 'Cameron', 'Kemshal-Bell', '0400155755', 'camkb@icloud.com', 'sadcdscddsdc dscsdcd dscddsds dcsdc sdcsdcs', 0, 0, '2024-11-21 21:57:44', '2024-11-21 21:57:44'),
(95, 'Cameron', 'Kemshal-Bell', '0400155755', 'camkb@icloud.com', 'sadcdscddsdc dscsdcd dscddsds dcsdc sdcsdcs', 0, 0, '2024-11-21 21:58:15', '2024-11-21 21:58:15'),
(96, 'Cameron', 'Kemshal-Bell', '0400155755', 'camkb@icloud.com', 'sadcdscddsdc dscsdcd dscddsds dcsdc sdcsdcs', 0, 0, '2024-11-21 21:58:48', '2024-11-21 21:58:48'),
(97, 'Cameron', 'Kemshal-Bell', '0400155755', 'camkb@icloud.com', 'sadcdscddsdc dscsdcd dscddsds dcsdc sdcsdcs', 0, 0, '2024-11-21 22:02:13', '2024-11-21 22:02:13'),
(98, 'Cameron', 'Kemshal-Bell', '0400155755', 'camkb@icloud.com', 'sadcdscddsdc dscsdcd dscddsds dcsdc sdcsdcs', 0, 0, '2024-11-21 22:03:26', '2024-11-21 22:03:26'),
(99, 'Cameron', 'Kemshal-Bell', '0400155755', 'camkb@icloud.com', 'sadcdscddsdc dscsdcd dscddsds dcsdc sdcsdcs', 0, 0, '2024-11-21 22:03:42', '2024-11-21 22:03:42'),
(100, 'Cameron', 'Kemshal-Bell', '0400155755', 'camkb@icloud.com', 'sadcdscddsdc dscsdcd dscddsds dcsdc sdcsdcs', 0, 0, '2024-11-21 22:06:32', '2024-11-21 22:06:32'),
(101, 'Cameron', 'Kemshal-Bell', '0400155755', 'camkb@icloud.com', 'sadcdscddsdc dscsdcd dscddsds dcsdc sdcsdcs', 0, 0, '2024-11-21 22:06:50', '2024-11-21 22:06:50'),
(102, 'Cameron', 'Kemshal-Bell', '0400155755', 'camkb@icloud.com', 'sadcdscddsdc dscsdcd dscddsds dcsdc sdcsdcs', 0, 0, '2024-11-21 22:07:36', '2024-11-21 22:07:36'),
(103, 'Cameron', 'Kemshal-Bell', '0400155755', 'camkb@icloud.com', 'sadcdscddsdc dscsdcd dscddsds dcsdc sdcsdcs', 0, 0, '2024-11-21 22:08:09', '2024-11-21 22:08:09'),
(104, 'Cameron', 'Kemshal-Bell', '0400155755', 'camkb@icloud.com', 'sadcdscddsdc dscsdcd dscddsds dcsdc sdcsdcs', 0, 0, '2024-11-21 22:08:31', '2024-11-21 22:08:31'),
(105, 'Cameron', 'Kemshal-Bell', '0400155755', 'camkb@icloud.com', 'sadcdscddsdc dscsdcd dscddsds dcsdc sdcsdcs', 0, 0, '2024-11-21 22:10:07', '2024-11-21 22:10:07'),
(106, 'Cameron', 'Kemshal-Bell', '0400155755', 'camkb@icloud.com', 'sadcdscddsdc dscsdcd dscddsds dcsdc sdcsdcs', 0, 0, '2024-11-21 22:10:55', '2024-11-21 22:10:55'),
(107, 'Cameron', 'Kemshal-Bell', '0400155755', 'camkb@icloud.com', 'sadcdscddsdc dscsdcd dscddsds dcsdc sdcsdcs', 0, 0, '2024-11-21 22:11:59', '2024-11-21 22:11:59'),
(108, 'Cameron', 'Kemshal-Bell', '0400155755', 'camkb@icloud.com', 'sadcdscddsdc dscsdcd dscddsds dcsdc sdcsdcs', 0, 0, '2024-11-21 22:13:48', '2024-11-21 22:13:48'),
(109, 'Cameron', 'Kemshal-Bell', '0400155755', 'camkb@icloud.com', 'sadcdscddsdc dscsdcd dscddsds dcsdc sdcsdcs', 0, 0, '2024-11-21 22:14:39', '2024-11-21 22:14:39'),
(110, 'Cameron', 'Kemshal-Bell', '0400155755', 'camkb@icloud.com', 'sadcdscddsdc dscsdcd dscddsds dcsdc sdcsdcs', 0, 0, '2024-11-21 22:25:17', '2024-11-21 22:25:17'),
(111, 'Cameron', 'Kemshal-Bell', '0400155755', 'camkb@icloud.com', 'sadcdscddsdc dscsdcd dscddsds dcsdc sdcsdcs', 0, 0, '2024-11-21 22:26:14', '2024-11-21 22:26:14'),
(112, 'Cameron', 'Kemshal-Bell', '0400155755', 'cam@iterated.tech', 'sadcdscddsdc dscsdcd dscddsds dcsdc sdcsdcs', 0, 0, '2024-11-21 22:34:29', '2024-11-21 22:34:29'),
(113, 'Cameron', 'Kemshal-Bell', '0400155755', 'cam@iterated.tech', 'sadcdscddsdc dscsdcd dscddsds dcsdc sdcsdcs', 0, 0, '2024-11-21 22:38:20', '2024-11-21 22:38:20'),
(114, 'Cameron', 'Kemshal-Bell', '0400155755', 'cam@iterated.tech', 'sadcdscddsdc dscsdcd dscddsds dcsdc sdcsdcs', 0, 0, '2024-11-21 22:40:03', '2024-11-21 22:40:03'),
(115, 'Cameron', 'Kemshal-Bell', '0400155755', 'cam@iterated.tech', 'sadcdscddsdc dscsdcd dscddsds dcsdc sdcsdcs', 0, 0, '2024-11-21 22:40:55', '2024-11-21 22:40:55'),
(116, 'Cameron', 'Kemshal-Bell', '0400155755', 'cam@iterated.tech', 'sadcdscddsdc dscsdcd dscddsds dcsdc sdcsdcs', 0, 0, '2024-11-21 22:44:12', '2024-11-21 22:44:12'),
(117, 'Cameron', 'Kemshal-Bell', '0400155755', 'cam@iterated.tech', 'sadcdscddsdc dscsdcd dscddsds dcsdc sdcsdcs', 0, 0, '2024-11-21 22:44:32', '2024-11-21 22:44:32'),
(118, 'Cameron', 'Kemshal-Bell', '0400155755', 'cam@iterated.tech', 'sadcdscddsdc dscsdcd dscddsds dcsdc sdcsdcs', 0, 0, '2024-11-21 22:44:49', '2024-11-21 22:44:49'),
(119, 'new', 'person', '0400155755', 'new@peoplearewe.com', 'sddf sfdsfdsfdf sfdsfdsfs dffdsf dsfdsfdsfdsfdsfsfd', 0, 0, '2024-11-21 22:45:20', '2024-11-21 22:45:20'),
(120, 'new', 'person', '0400155755', 'new@peoplearewe.com', 'sddf sfdsfdsfdf sfdsfdsfs dffdsf dsfdsfdsfdsfdsfsfd', 0, 0, '2024-11-21 22:45:47', '2024-11-21 22:45:47'),
(121, 'new', 'person', '0400155755', 'new@peoplearewe.com', 'sddf sfdsfdsfdf sfdsfdsfs dffdsf dsfdsfdsfdsfdsfsfd', 0, 0, '2024-11-21 22:46:20', '2024-11-21 22:46:20'),
(122, 'Bettina', 'Baird', '0400827827', 'betti_b@me.com', 'Hmm, what\'s this then?', 0, 0, '2024-11-21 22:46:45', '2024-11-21 22:46:45'),
(123, 'Bettina', 'Baird', '0400333222', 'bet.1@me.com', 'dfdsfds ffd fds ff sdf dsf dsf sd fsd fds fsd fdsf dsf ', 0, 0, '2024-11-21 22:47:24', '2024-11-21 22:47:24'),
(124, 'Bettina', 'Baird', '0400333222', 'bet.1@me.com', 'dfdsfds ffd fds ff sdf dsf dsf sd fsd fds fsd fdsf dsf ', 0, 0, '2024-11-21 22:50:59', '2024-11-21 22:50:59'),
(125, 'Bettina', 'Baird', '0400333222', 'bet.1@me.com', 'dfdsfds ffd fds ff sdf dsf dsf sd fsd fds fsd fdsf dsf ', 0, 0, '2024-11-21 22:51:17', '2024-11-21 22:51:17'),
(126, 'Cameron', 'Kemshal-Bell', '0400155755', 'admin@user.com', 'sdcds dcsd ccs dc dsc dsc dcd scs dcdc dsc dsc dsc dsc', 0, 0, '2024-11-21 22:51:53', '2024-11-21 22:51:53'),
(127, 'Cameron', 'Kemshal-Bell', '0400155755', 'user@admin.com', 'dsvcsd dscdsc dcds csd cdscds cdsc sd dscsdcdsc', 0, 0, '2024-11-21 22:52:46', '2024-11-21 22:52:46'),
(128, 'Cameron', 'Kemshal-Bell', '0400155755', 'cam@panr.app', 'sdcdsvjksndncdkcjsdncsdjk dscndscksn', 0, 0, '2024-11-21 22:53:16', '2024-11-21 22:53:16'),
(129, 'Cameron', 'Kemshal-Bell', '0400155755', 'cam@panr.app', 'sdcdsvjksndncdkcjsdncsdjk dscndscksn', 0, 0, '2024-11-21 22:53:47', '2024-11-21 22:53:47'),
(130, 'Cameron', 'Kemshal-Bell', '0400155755', 'cam@panr.app', 'sdcdsvjksndncdkcjsdncsdjk dscndscksn', 0, 0, '2024-11-21 22:53:51', '2024-11-21 22:53:51'),
(131, 'Cameron', 'Kemshal-Bell', '0400155755', 'cam@panr.com', 'sdcdsvjksndncdkcjsdncsdjk dscndscksn', 0, 0, '2024-11-21 22:54:15', '2024-11-21 22:54:15'),
(132, 'Cameron', 'Kemshal-Bell', '0400155755', 'ddvdvs@bb.com', 'ddsvsdvd dfdfvfvdffvdffd fdvdfvdfvfdfdvfddfv/fd vf vdvf', 0, 0, '2024-11-21 22:56:50', '2024-11-21 22:56:50'),
(133, 'Cameron', 'Kemshal-Bell', '0400155755', 'ddsdcd@dd.com', 'dsflkdsnjfdsfldksfsdflksdjfsdklfjdsdklsfjsdlkfjsdlkfjsdklfjsdkfjsdkfjdsk', 0, 0, '2024-11-21 22:57:25', '2024-11-21 22:57:25'),
(134, 'Cameron', 'Kemshal-Bell', '0400155755', 'camkb85@gmail.com', 'dscsddcdscs cdscds dscsd cdsc sdcdcdscds dcsdc sdcdscds', 0, 0, '2024-11-21 22:58:53', '2024-11-21 22:58:53'),
(135, 'Cameron', 'Kemshal-Bell', '0400155755', 'camkb85@gmail.com', 'dscsdcc sdcdscdscdsc sdc dscds csdcds sdc cds csdcdscdscsdc  dscsc', 0, 0, '2024-11-21 22:59:17', '2024-11-21 22:59:17'),
(136, 'Cameron', 'Kemshal-Bell', '0400155755', 'camkb85@gmail.com', 'sacsc sc scs saca scasc ascsc asc asc as sc asc acs cs acsc ', 0, 0, '2024-11-21 23:01:26', '2024-11-21 23:01:26'),
(137, 'Cameron', 'Kemshal-Bell', '0400155755', 'camkb85@gmail.com', 'sdcsd dcsdcsd csdcd dcdsc dsc dcs cds c cd cddcs ', 0, 0, '2024-11-21 23:04:08', '2024-11-21 23:04:08'),
(138, 'Cameron', 'Kemshal-Bell', '0400155755', 'scsscsdcsdcsd@me.com', 'dscdscdcsd dscdscds cdscds sdcsdcsdc sdcssd cddsc', 0, 0, '2024-11-21 23:08:02', '2024-11-21 23:08:02'),
(139, 'Cameron', 'Kemshal-Bell', '0400155755', 'admin@user.com', 'dcddscdscsdcd sdcsdc sdcsdc sdcsd ds cds cds cdcd cds cds c dscsd cds cds dsc', 0, 0, '2024-11-21 23:09:39', '2024-11-21 23:09:39'),
(140, 'Cameron', 'Kemshal-Bell', '0400155755', 'user@admin.com', 'dfdf sd f dss dfs df sdf ds fsd fds fsdf sd fdsf sd fdsf ', 0, 0, '2024-11-21 23:10:40', '2024-11-21 23:10:40'),
(141, 'Cameron', 'Kemshal-Bell', '0400155755', 'camkb@icloud.com.au', 'dfdsf dsfs dfsdf sd fds fs dfs df sdf ds fsd fds f sdf sdf sdf ', 0, 0, '2024-11-21 23:11:38', '2024-11-21 23:11:38'),
(142, 'Cameron', 'Kemshal-Bell', '0400155755', 'admin@user.com', 'dscds dcsd cdscd sdcdsc dsc dsd\r\n', 0, 0, '2024-11-21 23:16:13', '2024-11-21 23:16:13'),
(143, 'Cameron', 'Kemshal-Bell', '0400155755', 'user@admin.com', 'dscdsc scsd cdsc sdc sdcsdcsdc dscd', 0, 0, '2024-11-21 23:19:35', '2024-11-21 23:19:35'),
(144, 'Cameron', 'Kemshal-Bell', '0400155755', 'cam@newdomain.com', 'lets try this one out then so I can go to bed.', 0, 0, '2024-11-21 23:23:24', '2024-11-21 23:23:24'),
(145, 'Cameron', 'Kemshal-Bell', '0400155755', 'anoeht@newemail.com', 'dfsdnkdsjf skdfjsdfjs dfk sdjfsdlkf sdfjldsf jsdkf jsdklfj ldsfj dslkf jsdlf', 0, 0, '2024-11-21 23:26:38', '2024-11-21 23:26:38'),
(146, 'Cameron', 'Kemshal-Bell', '0400155755', 'admin@user.com', 'scsacs cscsacsc sa csa cs csac asc sc c as casc sc as cas cas casc sc ac sac aca csa c', 0, 0, '2024-11-21 23:27:01', '2024-11-21 23:27:01'),
(147, 'Cameron', 'Kemshal-Bell', '0400155755', 'new@personemaildom.com.au', 'dfsdfjdsfjsdfkjdks dsfdsklfj skldjf sdkfjsdklf sdjlkfj sdlf dskj f', 0, 0, '2024-11-21 23:30:46', '2024-11-21 23:30:46'),
(148, 'Cameron', 'Kemshal-Bell', '0400155755', 'new@personemaildom.com.au', 'dfsdfjdsfjsdfkjdks dsfdsklfj skldjf sdkfjsdklf sdjlkfj sdlf dskj f', 0, 0, '2024-11-21 23:34:41', '2024-11-21 23:34:41'),
(149, 'Cameron', 'Kemshal-Bell', '0400155755', 'user@admin.com', 'dscsdcdcdcdc ds sdcc sdcsdc cdsc csd csdc sdcs d', 0, 0, '2024-11-21 23:36:48', '2024-11-21 23:36:48'),
(150, 'Cameron', 'Kemshal-Bell', '0400155755', 'cam@panr.app', 'sdcs dcsd ccsdc ds dsc scdsdc cds cds dsc cs cd cds sd csds cds ccd ds  ds c sd sdcdcdc dc d', 0, 0, '2024-11-21 23:37:03', '2024-11-21 23:37:03'),
(151, 'Cameron', 'Kemshal-Bell', '0400155755', 'cam@panr.app', 'sdcs dcsd ccsdc ds dsc scdsdc cds cds dsc cs cd cds sd csds cds ccd ds  ds c sd sdcdcdc dc d', 0, 0, '2024-11-21 23:38:02', '2024-11-21 23:38:02'),
(152, 'Cameron', 'Kemshal-Bell', '0400155755', 'cam@panr.app', 'sdcs dcsd ccsdc ds dsc scdsdc cds cds dsc cs cd cds sd csds cds ccd ds  ds c sd sdcdcdc dc d', 0, 0, '2024-11-21 23:41:19', '2024-11-21 23:41:19'),
(153, 'Cameron', 'Kemshal-Bell', '0400155755', 'cam@panr.app', 'sdcs dcsd ccsdc ds dsc scdsdc cds cds dsc cs cd cds sd csds cds ccd ds  ds c sd sdcdcdc dc d', 0, 0, '2024-11-21 23:41:52', '2024-11-21 23:41:52'),
(154, 'Cameron', 'Kemshal-Bell', '0400155755', 'cam@panr.app', 'sdcs dcsd ccsdc ds dsc scdsdc cds cds dsc cs cd cds sd csds cds ccd ds  ds c sd sdcdcdc dc d', 0, 0, '2024-11-21 23:43:30', '2024-11-21 23:43:30'),
(155, 'Cameron', 'Kemshal-Bell', '0400155755', 'camkb@icloud.com', 'saccdscsd cdsc dc sdc dsc dsc cds cdc dsc s', 0, 0, '2024-11-24 14:47:29', '2024-11-24 14:47:29');

INSERT INTO `order_product` (`product_id`, `order_id`, `quantity`, `price`) VALUES
(4, 1, 5, 79.95);

INSERT INTO `orders` (`id`, `user_id`, `status`, `first_name`, `last_name`, `address`, `contact_number`, `card_number`, `expiry_date`, `card_name`, `ccv`, `purchase_date`, `total`, `created_at`, `updated_at`) VALUES
(1, 1, 'delivered', 'Cameron', 'Kemshal-Bell', '67 Lawrence Street, Wodonga, Vic, 3690', '0400155755', '1234344333454332', '12/25', 'Cameron Kemshal-Bell', '232', '2024-11-24 15:13:19', 399.75, '2024-11-24 15:13:19', '2024-11-24 15:13:44');

INSERT INTO `products` (`id`, `name`, `slug`, `image`, `price`, `sale_price`, `description`, `featured`, `category_id`, `created_at`, `updated_at`) VALUES
(1, 'Adidas Euro16 Top Soccer Ball', 'adidas-euro16-top-soccer-ball', 'soccerBall.jpg', 46.01, 35.95, 'adidas Performance Euro 16 Official Match Soccer Ball, Size 5, White/Bright Blue/Solar', 1, 5, '2024-05-16 22:31:32', '2024-11-24 15:14:32'),
(2, 'Pro-tec Classic Skate Helmet', 'pro-tec-classic-skate-helmet', 'skateHelmet.jpg', 70.00, 39.95, 'Get the classic Pro-Tec look with proven protection. Shop a wide range of skate, bmx & water helmets online at Pro-Tec Australia.', 1, 2, '2024-05-16 22:31:32', '2024-05-16 22:31:32'),
(3, 'Nike sport 600ml Water Bottle', 'nike-sport-600ml-water-bottle', 'waterBottle.jpg', 17.50, 15.00, 'Rehydrate your body and revive your day with the Nike Sport 600ml Water Bottle. The asymmetrical, one-hand design provides easy grasping while the leakproof valve to prevent leakage. ', 1, 6, '2024-05-16 22:31:32', '2024-05-16 22:31:32'),
(4, 'String ArmaPlus Boxing Gloves', 'string-armaplus-boxing-gloves', 'boxingGloves.jpg', 79.95, NULL, 'Get the perfect hand feel with the anatomically designed square shouldered mould to help you feel every shot land.', 1, 7, '2024-05-16 22:31:32', '2024-05-16 22:31:32'),
(5, 'Asics Gel Lethal Tigreor 8 IT Men\'s', 'asics-gel-lethal-tigreor-8-it-mens', 'footyBoots.jpg', 160.00, NULL, 'The GEL-Lethal Tigreor 8 IT is an advanced lightweight football boot designed for high performance and speed. This boot features HG10mm technology.', 1, 1, '2024-05-16 22:31:32', '2024-05-16 22:31:32'),
(6, 'Asics GEL Kayano 27 Kids Running Shoes', 'asics-gel-kayano-27-kids-running-shoes', 'runningShoes.jpg', 179.99, NULL, 'Asics refine running for the next generation of young athletes with the Asics GEL Kayano 27. The exceptional support and comfort of the Kayano return in a lighter even more comfortable runner thanks to the two-piece, Flightfoam Propel midsole. ', 0, 1, '2024-05-16 22:31:32', '2024-05-16 22:31:32'),
(7, 'Adidas must have stripes tee', 'adidas-must-have-stripes-tee', 'blackTop.jpg', 34.99, NULL, 'Built for busy training schedules, the adidas Boys Aeroready 3-Stripes Tee is a must have for budding young athletes.', 0, 4, '2024-05-16 22:31:32', '2024-05-16 22:31:32'),
(8, 'Nike girls Futura Air tee', 'nike-girls-futura-air-tee', 'whitePinkTop.jpg', 29.99, 24.99, 'Your child will be motivated to perform her best at training in the Nike Girls Futura Air Tee. The comfortable, non-restrictive crew neckline offers durability, while the iconic Nike Air logo is featured across the front and on the sleeve to highlight her sporty vibe.', 0, 4, '2024-05-16 22:31:32', '2024-05-16 22:31:32'),
(9, 'Adidas 3 stripes flare pants', 'adidas-3-stripes-flare-pants', 'tracksuit.jpg', 69.99, 55.99, 'Kick it old school this winter when you step out in the adidas Women\'s Tricot 3-Stripes Flare Pants. Ideal for post-gym wear, the stretchy tricot fabric allows you to move with ease as you recover from your big session. ', 0, 3, '2024-05-16 22:31:32', '2024-05-16 22:31:32'),
(10, 'New product', 'new-product', 'Fuzzy-search-algo---SQL.png', 124.00, 10.00, 'This is a new ball made out of code', 0, 5, '2024-11-24 15:15:04', '2024-11-24 15:15:26');

INSERT INTO `role_user` (`user_id`, `role_id`, `assigned_at`) VALUES
(1, 1, '2024-11-25 21:40:31'),
(1, 2, '2024-11-25 21:40:31');

INSERT INTO `roles` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'user', 'Default user role', '2024-05-16 22:25:17', '2024-05-16 22:25:17'),
(2, 'admin', 'Admin role with full privlidges', '2024-05-16 22:25:17', '2024-05-16 22:25:17');

INSERT INTO `users` (`id`, `username`, `email`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@user.com', '$2y$10$I5jHVQuy/g7kaUTyUaJYv.55s7AZ.fwRy3k/xce/LYV0JE1iIIk1u', '$2y$10$H2zbHVqnGIzAa8/Ems6cEO5SikoRjEBv2rZvXFZ2vYrg1UwJixoBm', '2024-06-15 14:35:28', '2024-11-25 21:40:31');



/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;