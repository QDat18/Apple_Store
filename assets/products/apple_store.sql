-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 15, 2025 at 04:40 PM
-- Server version: 9.2.0
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `apple_store`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `variant_id` int NOT NULL,
  `quantity` int DEFAULT '1',
  `added_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `parent_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `parent_id`) VALUES
(1, 'iPhone', NULL),
(2, 'iPad', NULL),
(3, 'Macbook', NULL),
(4, 'Apple Watch', NULL),
(5, 'AirPods', NULL),
(6, 'Sạc & Phụ kiện', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `email_queue`
--

CREATE TABLE `email_queue` (
  `id` int NOT NULL,
  `recipient_id` int DEFAULT NULL,
  `recipient_email` varchar(255) NOT NULL,
  `subject` text NOT NULL,
  `body` longtext NOT NULL,
  `status` enum('pending','sent','failed') DEFAULT 'pending',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `sent_at` datetime DEFAULT NULL,
  `error_message` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `email_queue`
--

INSERT INTO `email_queue` (`id`, `recipient_id`, `recipient_email`, `subject`, `body`, `status`, `created_at`, `sent_at`, `error_message`) VALUES
(1, 3, 'kiogit30@gmail.com', 'Thư chửi', '<p>hiconcec</p>', 'sent', '2025-07-15 15:26:22', '2025-07-15 15:28:39', NULL),
(2, 2, 'hoangquangdat182005@gmail.com', 'Thư chửi', '<p>hiconcec</p>', 'sent', '2025-07-15 15:26:22', '2025-07-15 15:28:46', NULL),
(3, 3, 'kiogit30@gmail.com', 'Thư chửi', '<p>hiconcec</p>', 'sent', '2025-07-15 15:27:16', '2025-07-15 15:28:53', NULL),
(4, 2, 'hoangquangdat182005@gmail.com', 'Thư chửi', '<p>hiconcec</p>', 'sent', '2025-07-15 15:27:16', '2025-07-15 15:28:59', NULL),
(5, 3, 'kiogit30@gmail.com', 'Thư chửi', '<p>hiconcec</p>', 'sent', '2025-07-15 15:28:06', '2025-07-15 15:29:06', NULL),
(6, 2, 'hoangquangdat182005@gmail.com', 'Thư chửi', '<p>hiconcec</p>', 'sent', '2025-07-15 15:28:06', '2025-07-15 15:29:13', NULL),
(7, 3, 'kiogit30@gmail.com', 'Thư chửi', '<p>hiconcec</p>', 'pending', '2025-07-15 15:28:51', NULL, NULL),
(8, 2, 'hoangquangdat182005@gmail.com', 'Thư chửi', '<p>hiconcec</p>', 'pending', '2025-07-15 15:28:51', NULL, NULL),
(9, 3, 'kiogit30@gmail.com', 'Huệ y&ecirc;u Qu&yacute;', '<p>Qu&yacute; y&ecirc;u Huệ</p>', 'pending', '2025-07-15 15:29:41', NULL, NULL),
(10, 2, 'hoangquangdat182005@gmail.com', 'Huệ y&ecirc;u Qu&yacute;', '<p>Qu&yacute; y&ecirc;u Huệ</p>', 'pending', '2025-07-15 15:29:41', NULL, NULL),
(11, 3, 'kiogit30@gmail.com', 'Huệ y&ecirc;u Qu&yacute;', '<p>Qu&yacute; y&ecirc;u Huệ</p>', 'pending', '2025-07-15 15:33:34', NULL, NULL),
(12, 2, 'hoangquangdat182005@gmail.com', 'Huệ y&ecirc;u Qu&yacute;', '<p>Qu&yacute; y&ecirc;u Huệ</p>', 'pending', '2025-07-15 15:33:34', NULL, NULL),
(13, 3, 'kiogit30@gmail.com', 'Huệ y&ecirc;u Qu&yacute;', '<p>Qu&yacute; y&ecirc;u Huệ</p>', 'pending', '2025-07-15 15:33:46', NULL, NULL),
(14, 2, 'hoangquangdat182005@gmail.com', 'Huệ y&ecirc;u Qu&yacute;', '<p>Qu&yacute; y&ecirc;u Huệ</p>', 'pending', '2025-07-15 15:33:46', NULL, NULL),
(15, 3, 'kiogit30@gmail.com', 'Huệ y&ecirc;u Qu&yacute;', '<p>Qu&yacute; y&ecirc;u Huệ</p>', 'pending', '2025-07-15 15:37:12', NULL, NULL),
(16, 2, 'hoangquangdat182005@gmail.com', 'Huệ y&ecirc;u Qu&yacute;', '<p>Qu&yacute; y&ecirc;u Huệ</p>', 'pending', '2025-07-15 15:37:12', NULL, NULL),
(17, 4, 'latanloc24012005@gmail.com', 'kiogit30@gmail.com', '<h1>nnnn</h1>', 'pending', '2025-07-15 16:07:10', NULL, NULL),
(18, 4, 'latanloc24012005@gmail.com', 'kiogit30@gmail.com', '<h1>nnnn</h1>', 'pending', '2025-07-15 16:09:33', NULL, NULL),
(19, 2, 'hoangquangdat182005@gmail.com', 'kiogit30@gmail.com', '<h1>nnnn</h1>', 'pending', '2025-07-15 16:09:45', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `email_templates`
--

CREATE TABLE `email_templates` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `subject` text NOT NULL,
  `body` longtext NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `email_templates`
--

INSERT INTO `email_templates` (`id`, `name`, `subject`, `body`, `created_at`) VALUES
(1, 'nnn', 'kiogit30@gmail.com', '<h1>nnnn</h1>', '2025-07-15 16:06:52');

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE `logs` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `details` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `logs`
--

INSERT INTO `logs` (`id`, `user_id`, `action`, `details`, `created_at`) VALUES
(1, 1, 'login', 'Admin logged in', '2025-07-12 15:51:31'),
(2, 2, 'view_product', 'Viewed iPhone 15 Pro Max', '2025-07-12 15:51:31'),
(3, NULL, 'visit_home', 'Guest visited homepage', '2025-07-12 15:51:31'),
(4, 1, 'logout', 'Admin logged out', '2025-07-12 15:51:55'),
(5, 1, 'visit_admin_dashboard', 'Admin visited dashboard', '2025-07-12 15:52:12'),
(6, 1, 'view_order', 'Viewed order ID: 2', '2025-07-12 15:52:20'),
(7, 1, 'view_order', 'Viewed order ID: 1', '2025-07-12 15:52:25'),
(8, 1, 'visit_admin_dashboard', 'Admin visited dashboard', '2025-07-12 15:52:41'),
(9, 1, 'logout', 'Admin logged out', '2025-07-12 15:52:47'),
(10, NULL, 'visit_index', 'User visited customer index page', '2025-07-12 16:00:34'),
(11, NULL, 'visit_index', 'User visited customer index page', '2025-07-12 16:34:27'),
(12, NULL, 'visit_index', 'User visited customer index page', '2025-07-12 16:34:30'),
(13, NULL, 'visit_index', 'User visited customer index page', '2025-07-12 17:50:34'),
(14, NULL, 'visit_index', 'User visited customer index page', '2025-07-12 18:07:03'),
(15, NULL, 'visit_index', 'User visited customer index page', '2025-07-13 07:21:21'),
(16, 2, 'visit_index', 'User visited customer index page', '2025-07-13 07:21:58'),
(17, 2, 'logout', 'Admin logged out', '2025-07-13 08:22:02'),
(18, 1, 'visit_admin_dashboard', 'Admin visited dashboard', '2025-07-13 08:22:38'),
(19, 1, 'view_order', 'Viewed order ID: 8', '2025-07-13 08:22:43'),
(20, 1, 'logout', 'Admin logged out', '2025-07-13 08:22:50'),
(21, 1, 'visit_admin_dashboard', 'Admin visited dashboard', '2025-07-13 08:22:58'),
(22, 1, 'logout', 'Admin logged out', '2025-07-13 08:47:02'),
(23, 2, 'visit_index', 'User visited customer index page', '2025-07-13 08:47:09'),
(24, 2, 'update_profile', 'User updated their profile information.', '2025-07-13 09:10:43'),
(26, 2, 'visit_index', 'User visited customer index page', '2025-07-13 09:26:03'),
(27, 2, 'update_profile', 'User updated their profile information.', '2025-07-13 09:26:20'),
(28, 2, 'update_profile', 'User updated their profile information.', '2025-07-13 09:26:23'),
(29, 2, 'logout', 'Admin logged out', '2025-07-13 09:42:53'),
(30, 2, 'visit_index', 'User visited customer index page', '2025-07-13 09:42:56'),
(31, 2, 'visit_index', 'User visited customer index page', '2025-07-13 09:43:43'),
(32, 2, 'visit_index', 'User visited customer index page', '2025-07-13 09:43:46'),
(33, 2, 'visit_index', 'User visited customer index page', '2025-07-13 09:43:49'),
(34, 2, 'visit_index', 'User visited customer index page', '2025-07-13 09:43:49'),
(35, 2, 'visit_index', 'User visited customer index page', '2025-07-13 09:43:49'),
(36, 2, 'visit_index', 'User visited customer index page', '2025-07-13 09:43:49'),
(37, 2, 'visit_index', 'User visited customer index page', '2025-07-13 09:43:50'),
(38, 2, 'visit_index', 'User visited customer index page', '2025-07-13 09:43:53'),
(39, 2, 'visit_index', 'User visited customer index page', '2025-07-13 09:43:55'),
(40, 2, 'visit_index', 'User visited customer index page', '2025-07-13 09:46:26'),
(41, 2, 'visit_index', 'User visited customer index page', '2025-07-13 09:46:54'),
(42, 2, 'visit_index', 'User visited customer index page', '2025-07-13 09:48:51'),
(43, 2, 'visit_index', 'User visited customer index page', '2025-07-13 09:51:08'),
(44, 2, 'visit_index', 'User visited customer index page', '2025-07-13 09:51:10'),
(45, 2, 'visit_index', 'User visited customer index page', '2025-07-13 09:51:10'),
(46, 2, 'visit_index', 'User visited customer index page', '2025-07-13 09:51:10'),
(47, 2, 'visit_index', 'User visited customer index page', '2025-07-13 09:51:11'),
(48, 2, 'visit_index', 'User visited customer index page', '2025-07-13 09:52:04'),
(49, 2, 'visit_index', 'User visited customer index page', '2025-07-13 09:52:13'),
(50, 2, 'visit_index', 'User visited customer index page', '2025-07-13 09:53:18'),
(51, 2, 'visit_index', 'User visited customer index page', '2025-07-13 09:53:29'),
(52, 2, 'visit_index', 'User visited customer index page', '2025-07-13 09:54:20'),
(53, 2, 'visit_index', 'User visited customer index page', '2025-07-13 09:54:21'),
(54, 2, 'visit_index', 'User visited customer index page', '2025-07-13 09:54:21'),
(55, 2, 'visit_index', 'User visited customer index page', '2025-07-13 09:54:21'),
(56, 2, 'visit_index', 'User visited customer index page', '2025-07-13 09:54:21'),
(57, 2, 'visit_index', 'User visited customer index page', '2025-07-13 09:54:22'),
(58, 2, 'visit_index', 'User visited customer index page', '2025-07-13 09:56:39'),
(59, 2, 'visit_index', 'User visited customer index page', '2025-07-13 10:00:55'),
(60, 2, 'visit_index', 'User visited customer index page', '2025-07-13 10:01:36'),
(61, 2, 'visit_index', 'User visited customer index page', '2025-07-13 10:02:08'),
(62, 2, 'visit_index', 'User visited customer index page', '2025-07-13 10:03:57'),
(63, 2, 'visit_index', 'User visited customer index page', '2025-07-13 10:04:48'),
(64, 2, 'visit_index', 'User visited customer index page', '2025-07-13 10:04:48'),
(65, 2, 'visit_index', 'User visited customer index page', '2025-07-13 10:04:48'),
(66, 2, 'visit_index', 'User visited customer index page', '2025-07-13 10:04:54'),
(67, 2, 'visit_index', 'User visited customer index page', '2025-07-13 10:06:38'),
(68, 2, 'visit_index', 'User visited customer index page', '2025-07-13 10:10:16'),
(69, 2, 'visit_index', 'User visited customer index page', '2025-07-13 10:11:15'),
(70, 2, 'visit_index', 'User visited customer index page', '2025-07-13 10:11:26'),
(71, 2, 'visit_index', 'User visited customer index page', '2025-07-13 10:11:27'),
(72, 2, 'visit_index', 'User visited customer index page', '2025-07-13 10:11:27'),
(73, 2, 'visit_index', 'User visited customer index page', '2025-07-13 10:11:32'),
(74, 2, 'visit_index', 'User visited customer index page', '2025-07-13 10:11:32'),
(75, 2, 'visit_index', 'User visited customer index page', '2025-07-13 10:11:32'),
(76, 2, 'visit_index', 'User visited customer index page', '2025-07-13 10:11:48'),
(77, 2, 'visit_index', 'User visited customer index page', '2025-07-13 10:21:48'),
(78, 2, 'visit_index', 'User visited customer index page', '2025-07-13 10:23:32'),
(79, 2, 'visit_index', 'User visited customer index page', '2025-07-13 10:26:23'),
(80, 2, 'visit_index', 'User visited customer index page', '2025-07-13 10:26:31'),
(81, 2, 'visit_index', 'User visited customer index page', '2025-07-13 10:26:34'),
(82, 2, 'visit_index', 'User visited customer index page', '2025-07-13 10:26:49'),
(83, 2, 'visit_index', 'User visited customer index page', '2025-07-13 10:27:51'),
(84, 2, 'visit_index', 'User visited customer index page', '2025-07-13 10:28:21'),
(85, 2, 'visit_index', 'User visited customer index page', '2025-07-13 10:28:21'),
(86, 2, 'visit_index', 'User visited customer index page', '2025-07-13 10:28:22'),
(87, 2, 'visit_index', 'User visited customer index page', '2025-07-13 10:28:22'),
(88, 2, 'visit_index', 'User visited customer index page', '2025-07-13 10:28:22'),
(89, 2, 'visit_index', 'User visited customer index page', '2025-07-13 10:28:44'),
(90, 2, 'visit_index', 'User visited customer index page', '2025-07-13 10:28:49'),
(91, 2, 'visit_index', 'User visited customer index page', '2025-07-13 10:33:21'),
(92, 2, 'visit_index', 'User visited customer index page', '2025-07-13 10:34:41'),
(93, 2, 'visit_index', 'User visited customer index page', '2025-07-13 10:34:43'),
(94, 2, 'visit_index', 'User visited customer index page', '2025-07-13 10:34:43'),
(95, 2, 'visit_index', 'User visited customer index page', '2025-07-13 10:34:43'),
(96, 2, 'visit_index', 'User visited customer index page', '2025-07-13 10:34:43'),
(97, 2, 'visit_index', 'User visited customer index page', '2025-07-13 10:34:43'),
(98, 2, 'visit_index', 'User visited customer index page', '2025-07-13 10:34:43'),
(99, 2, 'visit_index', 'User visited customer index page', '2025-07-13 10:34:48'),
(100, 2, 'visit_index', 'User visited customer index page', '2025-07-13 10:39:25'),
(101, 2, 'visit_index', 'User visited customer index page', '2025-07-13 10:39:55'),
(102, 2, 'visit_index', 'User visited customer index page', '2025-07-13 10:40:42'),
(103, 2, 'visit_index', 'User visited customer index page', '2025-07-13 10:44:01'),
(104, 2, 'visit_index', 'User visited customer index page', '2025-07-13 10:47:05'),
(105, 2, 'visit_index', 'User visited customer index page', '2025-07-13 10:49:27'),
(106, 2, 'visit_index', 'User visited customer index page', '2025-07-13 10:49:58'),
(107, 2, 'visit_index', 'User visited customer index page', '2025-07-13 10:50:45'),
(108, 2, 'visit_index', 'User visited customer index page', '2025-07-13 10:51:38'),
(109, 2, 'visit_index', 'User visited customer index page', '2025-07-13 10:52:19'),
(110, 2, 'visit_index', 'User visited customer index page', '2025-07-13 10:52:49'),
(111, 2, 'visit_index', 'User visited customer index page', '2025-07-13 10:53:39'),
(112, 2, 'visit_index', 'User visited customer index page', '2025-07-13 10:58:19'),
(113, 2, 'visit_index', 'User visited customer index page', '2025-07-13 10:58:29'),
(114, 2, 'visit_index', 'User visited customer index page', '2025-07-13 10:59:00'),
(115, 2, 'visit_index', 'User visited customer index page', '2025-07-13 11:00:09'),
(116, 2, 'visit_index', 'User visited customer index page', '2025-07-13 11:04:20'),
(117, 2, 'visit_index', 'User visited customer index page', '2025-07-13 11:13:07'),
(118, 2, 'update_profile', 'User updated their profile information.', '2025-07-13 11:29:24'),
(119, 2, 'update_profile', 'User updated their profile information.', '2025-07-13 11:29:28'),
(120, 2, 'visit_index', 'User visited customer index page', '2025-07-13 11:29:29'),
(121, 2, 'visit_index', 'User visited customer index page', '2025-07-13 11:29:36'),
(122, 2, 'visit_index', 'User visited customer index page', '2025-07-13 11:31:39'),
(123, 2, 'visit_index', 'User visited customer index page', '2025-07-13 11:31:50'),
(124, 2, 'visit_index', 'User visited customer index page', '2025-07-13 11:31:55'),
(125, 2, 'visit_index', 'User visited customer index page', '2025-07-13 11:36:49'),
(126, NULL, 'visit_index', 'User visited customer index page', '2025-07-13 14:50:42'),
(127, NULL, 'visit_index', 'User visited customer index page', '2025-07-13 14:55:49'),
(128, NULL, 'visit_index', 'User visited customer index page', '2025-07-13 14:56:47'),
(129, NULL, 'visit_index', 'User visited customer index page', '2025-07-13 14:59:30'),
(130, NULL, 'visit_index', 'User visited customer index page', '2025-07-13 15:01:33'),
(131, NULL, 'visit_index', 'User visited customer index page', '2025-07-13 15:17:22'),
(132, NULL, 'visit_index', 'User visited customer index page', '2025-07-13 15:19:25'),
(133, NULL, 'visit_index', 'User visited customer index page', '2025-07-13 15:38:58'),
(134, NULL, 'visit_index', 'User visited customer index page', '2025-07-13 16:45:43'),
(135, NULL, 'visit_index', 'User visited customer index page', '2025-07-13 17:01:25'),
(136, NULL, 'visit_index', 'User visited customer index page', '2025-07-13 17:01:39'),
(137, NULL, 'visit_index', 'User visited customer index page', '2025-07-13 17:06:37'),
(138, NULL, 'visit_index', 'User visited customer index page', '2025-07-13 17:06:40'),
(139, NULL, 'visit_index', 'User visited customer index page', '2025-07-13 17:06:54'),
(140, NULL, 'visit_index', 'User visited customer index page', '2025-07-13 17:08:01'),
(141, NULL, 'visit_index', 'User visited customer index page', '2025-07-13 17:08:39'),
(142, NULL, 'visit_index', 'User visited customer index page', '2025-07-13 17:08:46'),
(143, NULL, 'visit_index', 'User visited customer index page', '2025-07-13 17:24:01'),
(144, NULL, 'visit_index', 'User visited customer index page', '2025-07-13 17:49:18'),
(145, NULL, 'visit_index', 'User visited customer index page', '2025-07-13 17:51:04'),
(146, NULL, 'visit_index', 'User visited customer index page', '2025-07-13 17:53:00'),
(147, NULL, 'visit_index', 'User visited customer index page', '2025-07-13 18:03:34'),
(148, NULL, 'visit_index', 'User visited customer index page', '2025-07-13 18:03:36'),
(149, NULL, 'visit_index', 'User visited customer index page', '2025-07-13 18:03:36'),
(150, NULL, 'visit_index', 'User visited customer index page', '2025-07-13 18:03:36'),
(151, NULL, 'visit_index', 'User visited customer index page', '2025-07-13 18:03:36'),
(152, NULL, 'visit_index', 'User visited customer index page', '2025-07-13 18:03:54'),
(153, 2, 'visit_index', 'User visited customer index page', '2025-07-13 18:04:06'),
(154, 2, 'visit_index', 'User visited customer index page', '2025-07-13 18:08:00'),
(155, 2, 'visit_index', 'User visited customer index page', '2025-07-13 18:08:11'),
(156, 2, 'visit_index', 'User visited customer index page', '2025-07-13 18:08:18'),
(157, NULL, 'visit_index', 'User visited customer index page', '2025-07-14 03:01:16'),
(158, 2, 'visit_index', 'User visited customer index page', '2025-07-14 03:01:26'),
(159, 2, 'visit_index', 'User visited customer index page', '2025-07-14 03:06:23'),
(160, 2, 'visit_index', 'User visited customer index page', '2025-07-14 03:08:49'),
(161, 2, 'visit_index', 'User visited customer index page', '2025-07-14 03:09:12'),
(162, 2, 'visit_index', 'User visited customer index page', '2025-07-14 03:10:09'),
(163, 2, 'visit_index', 'User visited customer index page', '2025-07-14 03:10:20'),
(164, 2, 'visit_index', 'User visited customer index page', '2025-07-14 03:10:31'),
(165, 2, 'visit_index', 'User visited customer index page', '2025-07-14 03:11:01'),
(166, 2, 'visit_index', 'User visited customer index page', '2025-07-14 03:13:31'),
(167, 2, 'visit_index', 'User visited customer index page', '2025-07-14 03:14:18'),
(168, 2, 'visit_index', 'User visited customer index page', '2025-07-14 03:15:54'),
(169, 2, 'logout', 'Admin logged out', '2025-07-14 03:20:42'),
(170, 1, 'visit_admin_dashboard', 'Admin visited dashboard', '2025-07-14 03:21:00'),
(171, 1, 'view_order', 'Viewed order ID: 17', '2025-07-14 03:21:11'),
(172, 1, 'view_order', 'Viewed order ID: 16', '2025-07-14 03:21:28'),
(173, 1, 'view_order', 'Viewed order ID: 15', '2025-07-14 03:22:26'),
(174, 1, 'view_order', 'Viewed order ID: 17', '2025-07-14 03:24:52'),
(175, 1, 'logout', 'Admin logged out', '2025-07-14 03:28:10'),
(176, 1, 'visit_admin_dashboard', 'Admin visited dashboard', '2025-07-14 03:33:20'),
(177, 1, 'visit_admin_dashboard', 'Admin visited dashboard', '2025-07-14 03:33:27'),
(178, 1, 'view_order', 'Viewed order ID: 13', '2025-07-14 03:33:32'),
(179, 1, 'visit_admin_dashboard', 'Admin visited dashboard', '2025-07-14 03:34:11'),
(180, NULL, 'visit_index', 'User visited customer index page', '2025-07-14 03:35:12'),
(181, 1, 'visit_admin_dashboard', 'Admin visited dashboard', '2025-07-14 03:35:21'),
(182, 1, 'visit_admin_dashboard', 'Admin visited dashboard', '2025-07-14 03:37:00'),
(183, 1, 'visit_admin_dashboard', 'Admin visited dashboard', '2025-07-14 03:39:28'),
(184, 1, 'view_order', 'Viewed order ID: 17', '2025-07-14 03:39:30'),
(185, 1, 'visit_admin_dashboard', 'Admin visited dashboard', '2025-07-14 03:39:31'),
(186, 1, 'visit_admin_dashboard', 'Admin visited dashboard', '2025-07-14 03:39:37'),
(187, 1, 'visit_admin_dashboard', 'Admin visited dashboard', '2025-07-14 03:39:57'),
(188, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 05:42 AM, 14/07/2025', '2025-07-14 03:42:12'),
(189, 1, 'view_order', 'Viewed order ID: 17', '2025-07-14 03:43:32'),
(190, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 05:43 AM, 14/07/2025', '2025-07-14 03:43:33'),
(191, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 05:43 AM, 14/07/2025', '2025-07-14 03:43:34'),
(192, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 05:45 AM, 14/07/2025', '2025-07-14 03:45:24'),
(193, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 05:45 AM, 14/07/2025', '2025-07-14 03:45:28'),
(194, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 05:46 AM, 14/07/2025', '2025-07-14 03:46:40'),
(195, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 05:46 AM, 14/07/2025', '2025-07-14 03:46:45'),
(196, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 05:49 AM, 14/07/2025', '2025-07-14 03:49:06'),
(197, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 05:49 AM, 14/07/2025', '2025-07-14 03:49:09'),
(198, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 05:49 AM, 14/07/2025', '2025-07-14 03:49:09'),
(199, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 05:49 AM, 14/07/2025', '2025-07-14 03:49:09'),
(200, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 05:49 AM, 14/07/2025', '2025-07-14 03:49:09'),
(201, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 05:49 AM, 14/07/2025', '2025-07-14 03:49:10'),
(202, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 05:49 AM, 14/07/2025', '2025-07-14 03:49:10'),
(203, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 05:49 AM, 14/07/2025', '2025-07-14 03:49:10'),
(204, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 05:50 AM, 14/07/2025', '2025-07-14 03:50:42'),
(205, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 05:51 AM, 14/07/2025', '2025-07-14 03:51:21'),
(206, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 05:52 AM, 14/07/2025', '2025-07-14 03:52:33'),
(207, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 05:52 AM, 14/07/2025', '2025-07-14 03:52:58'),
(208, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 06:10 AM, 14/07/2025', '2025-07-14 04:10:19'),
(209, 1, 'update_order_status', 'Updated order ID: 17 to status: shipped', '2025-07-14 04:11:22'),
(210, 1, 'update_order_status', 'Updated order ID: 3 to status: delivered', '2025-07-14 04:11:29'),
(211, 1, 'view_order', 'Viewed order ID: 3', '2025-07-14 04:11:31'),
(212, 1, 'view_order', 'Viewed order ID: 3', '2025-07-14 04:11:39'),
(213, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 06:15 AM, 14/07/2025', '2025-07-14 04:15:42'),
(214, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 06:15 AM, 14/07/2025', '2025-07-14 04:15:43'),
(215, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 06:16 AM, 14/07/2025', '2025-07-14 04:16:05'),
(216, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 06:16 AM, 14/07/2025', '2025-07-14 04:16:21'),
(217, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 06:16 AM, 14/07/2025', '2025-07-14 04:16:22'),
(218, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 06:17 AM, 14/07/2025', '2025-07-14 04:17:04'),
(219, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 06:17 AM, 14/07/2025', '2025-07-14 04:17:31'),
(220, 1, 'update_order_status', 'Updated order ID: 15 to status: cancelled', '2025-07-14 04:18:34'),
(221, 1, 'update_order_status', 'Updated order ID: 15 to status: cancelled', '2025-07-14 04:19:57'),
(222, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 06:19 AM, 14/07/2025', '2025-07-14 04:19:59'),
(223, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 06:22 AM, 14/07/2025', '2025-07-14 04:22:39'),
(224, 1, 'logout', 'Admin logged out', '2025-07-14 04:22:58'),
(225, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 06:23 AM, 14/07/2025', '2025-07-14 04:23:02'),
(226, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 06:23 AM, 14/07/2025', '2025-07-14 04:23:05'),
(227, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 06:30 AM, 14/07/2025', '2025-07-14 04:30:30'),
(228, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 06:30 AM, 14/07/2025', '2025-07-14 04:30:33'),
(229, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 06:30 AM, 14/07/2025', '2025-07-14 04:30:35'),
(230, 1, 'edit_product', 'Edited product ID: 1 - Name: iPhone 15 Pro Max 256GB', '2025-07-14 04:35:25'),
(231, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 06:35 AM, 14/07/2025', '2025-07-14 04:35:36'),
(232, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 06:35 AM, 14/07/2025', '2025-07-14 04:35:43'),
(233, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 06:35 AM, 14/07/2025', '2025-07-14 04:35:52'),
(234, NULL, 'edit_product', 'Edited product ID: 1 - Name: iPhone 15 Pro Max 256GB', '2025-07-14 04:36:07'),
(235, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 06:36 AM, 14/07/2025', '2025-07-14 04:36:10'),
(236, NULL, 'edit_product', 'Edited product ID: 1 - Name: iPhone 15 Pro Max 256GB', '2025-07-14 04:36:46'),
(237, NULL, 'edit_product', 'Edited product ID: 2 - Name: iPhone 15 Pro 128GB', '2025-07-14 04:36:55'),
(238, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 06:37 AM, 14/07/2025', '2025-07-14 04:37:22'),
(239, NULL, 'edit_product', 'Edited product ID: 1 - Name: iPhone 15 Pro Max 256GB', '2025-07-14 04:38:24'),
(240, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 06:40 AM, 14/07/2025', '2025-07-14 04:40:17'),
(241, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 06:40 AM, 14/07/2025', '2025-07-14 04:40:21'),
(242, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 06:43 AM, 14/07/2025', '2025-07-14 04:43:36'),
(243, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 06:43 AM, 14/07/2025', '2025-07-14 04:43:42'),
(244, NULL, 'edit_product', 'Edited product ID: 1 - Name: iPhone 15 Pro Max 256GB', '2025-07-14 04:44:38'),
(245, NULL, 'add_product', 'Added product: iPhone SE 2027', '2025-07-14 04:45:17'),
(246, 1, 'view_order', 'Viewed order ID: 17', '2025-07-14 04:48:33'),
(247, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 06:48 AM, 14/07/2025', '2025-07-14 04:48:43'),
(248, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 06:50 AM, 14/07/2025', '2025-07-14 04:50:45'),
(249, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 06:54 AM, 14/07/2025', '2025-07-14 04:54:03'),
(250, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 06:54 AM, 14/07/2025', '2025-07-14 04:54:06'),
(251, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 06:54 AM, 14/07/2025', '2025-07-14 04:54:18'),
(252, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 06:54 AM, 14/07/2025', '2025-07-14 04:54:29'),
(253, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 06:54 AM, 14/07/2025', '2025-07-14 04:54:35'),
(254, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 06:55 AM, 14/07/2025', '2025-07-14 04:55:09'),
(255, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 06:55 AM, 14/07/2025', '2025-07-14 04:55:17'),
(256, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 06:55 AM, 14/07/2025', '2025-07-14 04:55:22'),
(257, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 06:56 AM, 14/07/2025', '2025-07-14 04:56:38'),
(258, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 06:56 AM, 14/07/2025', '2025-07-14 04:56:45'),
(259, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 06:57 AM, 14/07/2025', '2025-07-14 04:57:13'),
(260, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 06:57 AM, 14/07/2025', '2025-07-14 04:57:58'),
(261, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 06:58 AM, 14/07/2025', '2025-07-14 04:58:03'),
(262, NULL, 'add_product_stock', 'Added 10 units to product ID: 9 stock.', '2025-07-14 04:58:45'),
(263, NULL, 'add_product_stock', 'Added 1 units to product ID: 17 stock.', '2025-07-14 04:59:00'),
(264, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 07:02 AM, 14/07/2025', '2025-07-14 05:02:12'),
(265, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 07:21 AM, 14/07/2025', '2025-07-14 05:21:25'),
(266, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 07:21 AM, 14/07/2025', '2025-07-14 05:21:29'),
(267, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 07:21 AM, 14/07/2025', '2025-07-14 05:21:33'),
(268, 1, 'logout', 'Admin logged out', '2025-07-14 05:22:52'),
(269, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 07:22 AM, 14/07/2025', '2025-07-14 05:22:53'),
(270, 1, 'add_supplier', 'Added supplier: Hoa Sơn Quý', '2025-07-14 05:24:32'),
(271, NULL, 'add_supplier', 'Added supplier: Hoa Sơn Quý', '2025-07-14 05:24:47'),
(272, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 07:26 AM, 14/07/2025', '2025-07-14 05:26:05'),
(273, 1, 'logout', 'Admin logged out', '2025-07-14 05:26:06'),
(274, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 07:26 AM, 14/07/2025', '2025-07-14 05:26:08'),
(275, NULL, 'add_supplier', 'Added supplier: Hoa Sơn Quý', '2025-07-14 05:26:22'),
(276, NULL, 'add_supplier', 'Added supplier: Hoa Sơn Quý', '2025-07-14 05:26:58'),
(277, NULL, 'add_supplier', 'Added supplier: Hoa Sơn Quý', '2025-07-14 05:27:58'),
(278, NULL, 'add_supplier', 'Added supplier: Hoa Sơn Quý', '2025-07-14 05:28:17'),
(279, NULL, 'visit_index', 'User visited customer index page', '2025-07-14 05:29:07'),
(280, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 07:29 AM, 14/07/2025', '2025-07-14 05:29:23'),
(281, NULL, 'edit_supplier', 'Updated supplier ID: 8', '2025-07-14 05:29:41'),
(282, NULL, 'edit_supplier', 'Updated supplier ID: 8', '2025-07-14 05:29:44'),
(283, NULL, 'add_supplier', 'Added supplier: Hoa Sơn Quý', '2025-07-14 05:30:27'),
(284, 1, 'add_supplier', 'Added supplier: Hoa Sơn Quý', '2025-07-14 05:31:47'),
(285, 1, 'send_mass_email', 'Sent email to 1 customer(s).', '2025-07-14 05:49:28'),
(286, 1, 'send_mass_email', 'Sent email to 1 customer(s).', '2025-07-14 05:51:17'),
(287, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 07:51 AM, 14/07/2025', '2025-07-14 05:51:17'),
(288, 1, 'send_mass_email', 'Sent email to 1 customer(s).', '2025-07-14 05:57:20'),
(289, 1, 'send_mass_email', 'Sent email to 1 customer(s).', '2025-07-14 06:02:29'),
(290, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 08:02 AM, 14/07/2025', '2025-07-14 06:02:34'),
(291, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 08:02 AM, 14/07/2025', '2025-07-14 06:02:48'),
(292, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 08:02 AM, 14/07/2025', '2025-07-14 06:02:49'),
(293, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 08:03 AM, 14/07/2025', '2025-07-14 06:03:15'),
(294, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 08:05 AM, 14/07/2025', '2025-07-14 06:05:21'),
(295, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 08:05 AM, 14/07/2025', '2025-07-14 06:05:54'),
(296, 1, 'view_order', 'Viewed order ID: 17', '2025-07-14 06:06:48'),
(297, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 08:06 AM, 14/07/2025', '2025-07-14 06:06:50'),
(298, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 08:10 AM, 14/07/2025', '2025-07-14 06:10:17'),
(299, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 08:10 AM, 14/07/2025', '2025-07-14 06:10:52'),
(300, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 08:11 AM, 14/07/2025', '2025-07-14 06:11:05'),
(301, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 08:11 AM, 14/07/2025', '2025-07-14 06:11:09'),
(302, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 08:11 AM, 14/07/2025', '2025-07-14 06:11:42'),
(303, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 08:11 AM, 14/07/2025', '2025-07-14 06:11:52'),
(304, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 08:12 AM, 14/07/2025', '2025-07-14 06:12:27'),
(305, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 08:12 AM, 14/07/2025', '2025-07-14 06:12:30'),
(306, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 08:12 AM, 14/07/2025', '2025-07-14 06:12:54'),
(307, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 08:13 AM, 14/07/2025', '2025-07-14 06:13:03'),
(308, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 08:16 AM, 14/07/2025', '2025-07-14 06:16:06'),
(309, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 08:16 AM, 14/07/2025', '2025-07-14 06:16:44'),
(310, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 08:16 AM, 14/07/2025', '2025-07-14 06:16:53'),
(311, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 08:18 AM, 14/07/2025', '2025-07-14 06:18:11'),
(312, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 08:18 AM, 14/07/2025', '2025-07-14 06:18:18'),
(313, 1, 'logout', 'Admin logged out', '2025-07-14 06:18:21'),
(314, 2, 'visit_index', 'User visited customer index page', '2025-07-14 06:19:25'),
(315, 2, 'logout', 'Admin logged out', '2025-07-14 06:19:37'),
(316, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 08:19 AM, 14/07/2025', '2025-07-14 06:19:39'),
(317, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 08:20 AM, 14/07/2025', '2025-07-14 06:20:27'),
(318, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 08:20 AM, 14/07/2025', '2025-07-14 06:20:53'),
(319, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 08:21 AM, 14/07/2025', '2025-07-14 06:21:10'),
(320, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 08:21 AM, 14/07/2025', '2025-07-14 06:21:59'),
(321, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 08:22 AM, 14/07/2025', '2025-07-14 06:22:08'),
(322, 1, 'logout', 'Admin logged out', '2025-07-14 06:22:18'),
(323, 3, 'register', 'User registered with email: kiogit30@gmail.com', '2025-07-14 06:22:41'),
(324, 3, 'visit_index', 'User visited customer index page', '2025-07-14 06:24:00'),
(325, 3, 'update_profile', 'User updated their profile information.', '2025-07-14 06:24:28'),
(326, 3, 'visit_index', 'User visited customer index page', '2025-07-14 06:24:35'),
(327, 3, 'visit_index', 'User visited customer index page', '2025-07-14 06:25:17'),
(328, 3, 'visit_index', 'User visited customer index page', '2025-07-14 06:25:51'),
(329, 3, 'logout', 'Admin logged out', '2025-07-14 06:25:53'),
(330, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 08:25 AM, 14/07/2025', '2025-07-14 06:25:55'),
(331, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 05:34 AM, 15/07/2025', '2025-07-15 03:34:52'),
(332, 1, 'view_order', 'Viewed order ID: 17', '2025-07-15 03:35:03'),
(333, 1, 'view_order', 'Viewed order ID: 16', '2025-07-15 03:35:06'),
(334, 1, 'send_mass_email', 'Sent email to 1 customer(s).', '2025-07-15 03:35:58'),
(335, NULL, 'visit_index', 'User visited customer index page', '2025-07-15 08:11:47'),
(336, NULL, 'visit_index', 'User visited customer index page', '2025-07-15 08:12:01'),
(337, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 10:12 AM, 15/07/2025', '2025-07-15 08:12:06'),
(338, 1, 'send_mass_email', 'Sent email to 1 customer(s).', '2025-07-15 08:18:36'),
(339, 1, 'send_mass_email', 'Sent email to 1 customer(s).', '2025-07-15 08:19:31'),
(340, 1, 'send_mass_email', 'Sent email to 2 customer(s).', '2025-07-15 08:20:59'),
(341, 1, 'add_mass_email_to_queue', 'Added 2 email(s) to queue.', '2025-07-15 08:26:22'),
(342, 1, 'add_mass_email_to_queue', 'Added 2 email(s) to queue.', '2025-07-15 08:27:16'),
(343, 1, 'add_mass_email_to_queue', 'Added 2 email(s) to queue.', '2025-07-15 08:28:06'),
(344, 1, 'add_mass_email_to_queue', 'Added 2 email(s) to queue.', '2025-07-15 08:28:51'),
(345, 1, 'add_mass_email_to_queue', 'Added 2 email(s) to queue.', '2025-07-15 08:29:41'),
(346, 1, 'add_mass_email_to_queue', 'Added 2 email(s) to queue.', '2025-07-15 08:33:34'),
(347, 1, 'add_mass_email_to_queue', 'Added 2 email(s) to queue.', '2025-07-15 08:33:46'),
(348, 1, 'add_mass_email_to_queue', 'Added 2 email(s) to queue.', '2025-07-15 08:37:12'),
(349, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 10:40 AM, 15/07/2025', '2025-07-15 08:40:45'),
(350, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 10:40 AM, 15/07/2025', '2025-07-15 08:40:50'),
(351, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 10:41 AM, 15/07/2025', '2025-07-15 08:41:01'),
(352, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 10:55 AM, 15/07/2025', '2025-07-15 08:55:21'),
(353, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 10:55 AM, 15/07/2025', '2025-07-15 08:55:22'),
(354, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 10:55 AM, 15/07/2025', '2025-07-15 08:55:26'),
(355, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 10:55 AM, 15/07/2025', '2025-07-15 08:55:32'),
(356, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 10:56 AM, 15/07/2025', '2025-07-15 08:56:12'),
(357, 1, 'logout', 'Admin logged out', '2025-07-15 08:56:50'),
(358, 4, 'register', 'User registered with email: kiogit301@gmail.com', '2025-07-15 08:57:07'),
(359, 4, 'visit_index', 'User visited customer index page', '2025-07-15 08:58:02'),
(360, 4, 'update_profile', 'User updated their profile information.', '2025-07-15 08:59:01'),
(361, 4, 'logout', 'Admin logged out', '2025-07-15 08:59:06'),
(362, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 10:59 AM, 15/07/2025', '2025-07-15 08:59:08'),
(363, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 11:01 AM, 15/07/2025', '2025-07-15 09:01:03'),
(364, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 11:02 AM, 15/07/2025', '2025-07-15 09:02:18'),
(365, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 11:02 AM, 15/07/2025', '2025-07-15 09:02:50'),
(366, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 11:04 AM, 15/07/2025', '2025-07-15 09:04:08'),
(367, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 11:05 AM, 15/07/2025', '2025-07-15 09:05:04'),
(368, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 11:08 AM, 15/07/2025', '2025-07-15 09:08:51'),
(369, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 11:08 AM, 15/07/2025', '2025-07-15 09:08:54'),
(370, 1, 'logout', 'Admin logged out', '2025-07-15 09:21:45'),
(371, 2, 'visit_index', 'User visited customer index page', '2025-07-15 09:21:52'),
(372, 2, 'visit_index', 'User visited customer index page', '2025-07-15 09:22:58'),
(373, 2, 'logout', 'Admin logged out', '2025-07-15 09:23:01'),
(374, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 11:23 AM, 15/07/2025', '2025-07-15 09:23:03'),
(375, 1, 'logout', 'Admin logged out', '2025-07-15 09:25:09'),
(376, 3, 'visit_index', 'User visited customer index page', '2025-07-15 09:25:12'),
(377, 3, 'logout', 'Admin logged out', '2025-07-15 09:27:11'),
(378, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 11:27 AM, 15/07/2025', '2025-07-15 09:27:22'),
(379, 1, 'update_order_status', 'Updated order ID: 18 to status: delivered', '2025-07-15 09:27:30'),
(380, 1, 'logout', 'Admin logged out', '2025-07-15 09:31:51'),
(381, 3, 'visit_index', 'User visited customer index page', '2025-07-15 09:31:54'),
(382, 3, 'visit_index', 'User visited customer index page', '2025-07-15 09:33:00'),
(383, 3, 'visit_index', 'User visited customer index page', '2025-07-15 09:33:01'),
(384, 3, 'visit_index', 'User visited customer index page', '2025-07-15 13:34:27'),
(385, 3, 'visit_index', 'User visited customer index page', '2025-07-15 13:42:52'),
(386, 3, 'logout', 'Admin logged out', '2025-07-15 13:42:55'),
(387, 1, 'visit_admin_dashboard', 'Admin visited dashboard at 03:42 PM, 15/07/2025', '2025-07-15 13:42:57');

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `category` varchar(50) DEFAULT NULL,
  `created_date` date DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `news`
--

INSERT INTO `news` (`id`, `title`, `content`, `category`, `created_date`, `image_url`) VALUES
(1, 'Ra mắt iPhone 16 tại Anh Em Rọt Store', 'Ngày 15/07/2025, Anh Em Rọt Store sẽ chính thức phân phối iPhone 16 với nhiều ưu đãi hấp dẫn. Sản phẩm được trang bị chip A18 Bionic mạnh mẽ, camera cải tiến 48MP, và thiết kế hoàn toàn mới. Khách hàng có thể đến cửa hàng hoặc đặt hàng online để nhận ngay ưu đãi giảm giá 10% cùng quà tặng đi kèm. Đừng bỏ lỡ cơ hội sở hữu chiếc điện thoại hàng đầu trong năm 2025!', 'sản phẩm mới', '2025-07-13', 'assets/news/iphone16.jpg'),
(2, 'Chính sách đổi trả mới', 'Từ 13/07/2025, Anh Em Rọt Store áp dụng chính sách đổi trả mới cho khách hàng. Bạn sẽ được đổi sản phẩm trong vòng 7 ngày nếu có lỗi kỹ thuật, kèm theo hỗ trợ kiểm tra miễn phí tại tất cả chi nhánh. Vui lòng mang theo hóa đơn và sản phẩm nguyên vẹn để được xử lý nhanh chóng. Xem chi tiết tại đây hoặc liên hệ hotline 0909 123 456 để được tư vấn thêm.', 'chính sách', '2025-07-13', 'assets/news/policy.jpg'),
(3, 'Sự kiện triển lãm công nghệ 2025', 'Tham gia triển lãm công nghệ lớn nhất năm vào ngày 20/07/2025 tại Trung tâm Hội nghị TP.HCM. Sự kiện sẽ giới thiệu các sản phẩm mới nhất từ Apple, Samsung, và nhiều thương hiệu khác, cùng với các gian hàng trải nghiệm thực tế. Nhiều ưu đãi đặc biệt như giảm giá trực tiếp và quà tặng độc quyền sẽ được áp dụng. Đừng bỏ lỡ cơ hội khám phá công nghệ đỉnh cao!', 'sự kiện', '2025-07-12', 'assets/news/tech_event.jpg'),
(4, 'Ra mắt tai nghe AirPods Pro 3', 'Ngày 17/07/2025, Anh Em Rọt Store sẽ giới thiệu AirPods Pro 3 với công nghệ chống ồn chủ động tiên tiến, thời lượng pin lên đến 30 giờ, và thiết kế chống nước IPX7. Khách hàng đặt trước sẽ nhận ưu đãi giảm 15% cùng tai nghe dự phòng miễn phí. Sự kiện diễn ra tại tất cả chi nhánh, đừng bỏ lỡ cơ hội trải nghiệm!', 'sản phẩm mới', '2025-07-16', 'assets/news/airpods_pro3.jpg'),
(5, 'Chính sách hỗ trợ khách VIP', 'Từ 15/07/2025, Anh Em Rọt Store triển khai chương trình hỗ trợ đặc biệt cho khách hàng VIP. Bao gồm dịch vụ sửa chữa ưu tiên, giảm giá 10% cho mọi sản phẩm, và quà tặng nhân dịp sinh nhật. Để tham gia, đăng ký thành viên tại cửa hàng hoặc qua ứng dụng chính thức. Chi tiết liên hệ hotline 0909 123 456!', 'chính sách', '2025-07-15', 'assets/news/vip_support.jpg'),
(6, 'Sự kiện Black Friday mùa hè', 'Tham gia sự kiện Black Friday mùa hè vào ngày 19/07/2025 tại TP.HCM và Hà Nội. Chương trình mang đến giảm giá lên đến 50% cho điện thoại, phụ kiện, và đồng hồ thông minh. Khách hàng tham gia sẽ nhận thêm voucher 500k cho lần mua sắm tiếp theo. Đăng ký sớm để giữ chỗ!', 'sự kiện', '2025-07-14', 'assets/news/black_friday.jpg'),
(7, 'Cập nhật Android 16', 'Android 16 chính thức ra mắt với giao diện Material You mới và hiệu suất tăng 25%. Từ 18/07/2025, Anh Em Rọt Store sẽ hỗ trợ cập nhật miễn phí cho tất cả thiết bị Samsung và Google Pixel. Đội ngũ kỹ thuật sẽ hướng dẫn chi tiết và kiểm tra thiết bị trước khi nâng cấp!', 'sản phẩm mới', '2025-07-17', 'assets/news/android16.jpg'),
(8, 'Chính sách bảo mật thông tin', 'Từ 16/07/2025, Anh Em Rọt Store áp dụng chính sách bảo mật thông tin mới theo tiêu chuẩn quốc tế. Dữ liệu khách hàng sẽ được mã hóa và không chia sẻ với bên thứ ba. Khách hàng có thể kiểm tra chi tiết trên website hoặc liên hệ để được tư vấn thêm về quyền lợi của mình!', 'chính sách', '2025-07-16', 'assets/news/privacy.jpg'),
(9, 'Ra mắt tai nghe AirPods Pro 3', 'Ngày 17/07/2025, Anh Em Rọt Store sẽ giới thiệu AirPods Pro 3 với công nghệ chống ồn chủ động tiên tiến, thời lượng pin lên đến 30 giờ, và thiết kế chống nước IPX7. Khách hàng đặt trước sẽ nhận ưu đãi giảm 15% cùng tai nghe dự phòng miễn phí. Sự kiện diễn ra tại tất cả chi nhánh, đừng bỏ lỡ cơ hội trải nghiệm!', 'sản phẩm mới', '2025-07-16', 'assets/news/airpods_pro3.jpg'),
(10, 'Chính sách hỗ trợ khách VIP', 'Từ 15/07/2025, Anh Em Rọt Store triển khai chương trình hỗ trợ đặc biệt cho khách hàng VIP. Bao gồm dịch vụ sửa chữa ưu tiên, giảm giá 10% cho mọi sản phẩm, và quà tặng nhân dịp sinh nhật. Để tham gia, đăng ký thành viên tại cửa hàng hoặc qua ứng dụng chính thức. Chi tiết liên hệ hotline 0909 123 456!', 'chính sách', '2025-07-15', 'assets/news/vip_support.jpg'),
(11, 'Sự kiện Black Friday mùa hè', 'Tham gia sự kiện Black Friday mùa hè vào ngày 19/07/2025 tại TP.HCM và Hà Nội. Chương trình mang đến giảm giá lên đến 50% cho điện thoại, phụ kiện, và đồng hồ thông minh. Khách hàng tham gia sẽ nhận thêm voucher 500k cho lần mua sắm tiếp theo. Đăng ký sớm để giữ chỗ!', 'sự kiện', '2025-07-14', 'assets/news/black_friday.jpg'),
(12, 'Cập nhật Android 16', 'Android 16 chính thức ra mắt với giao diện Material You mới và hiệu suất tăng 25%. Từ 18/07/2025, Anh Em Rọt Store sẽ hỗ trợ cập nhật miễn phí cho tất cả thiết bị Samsung và Google Pixel. Đội ngũ kỹ thuật sẽ hướng dẫn chi tiết và kiểm tra thiết bị trước khi nâng cấp!', 'sản phẩm mới', '2025-07-17', 'assets/news/android16.jpg'),
(13, 'Chính sách bảo mật thông tin', 'Từ 16/07/2025, Anh Em Rọt Store áp dụng chính sách bảo mật thông tin mới theo tiêu chuẩn quốc tế. Dữ liệu khách hàng sẽ được mã hóa và không chia sẻ với bên thứ ba. Khách hàng có thể kiểm tra chi tiết trên website hoặc liên hệ để được tư vấn thêm về quyền lợi của mình!', 'chính sách', '2025-07-16', 'assets/news/privacy.jpg'),
(14, 'Hội thảo công nghệ tương lai', 'Tham gia hội thảo công nghệ tương lai vào ngày 22/07/2025 tại Trung tâm Hội nghị Quốc gia. Sự kiện sẽ tập trung vào xu hướng AI, 5G, và IoT với sự tham gia của các chuyên gia hàng đầu. Anh Em Rọt Store tài trợ và mang đến các sản phẩm trưng bày, kèm voucher giảm giá 10% cho khách tham dự!', 'sự kiện', '2025-07-18', 'assets/news/tech_seminar.jpg'),
(15, 'Chính sách hoàn tiền 100%', 'Từ 19/07/2025, Anh Em Rọt Store triển khai chính sách hoàn tiền 100% nếu khách hàng không hài lòng trong vòng 14 ngày. Điều kiện áp dụng cho sản phẩm còn nguyên vẹn, kèm hóa đơn mua hàng. Liên hệ chi nhánh gần nhất hoặc qua hotline 0909 123 456 để được hỗ trợ chi tiết!', 'chính sách', '2025-07-19', 'assets/news/refund_policy.jpg'),
(16, 'Cập nhật phần mềm cho đồng hồ thông minh', 'Từ 21/07/2025, Anh Em Rọt Store sẽ hỗ trợ cập nhật phần mềm mới cho tất cả đồng hồ thông minh, bao gồm Apple Watch và Galaxy Watch. Bản cập nhật cải thiện hiệu suất pin, thêm tính năng theo dõi sức khỏe, và giao diện mới. Đội ngũ kỹ thuật sẽ hỗ trợ miễn phí tại cửa hàng!', 'sản phẩm mới', '2025-07-20', 'assets/news/watch_update.jpg'),
(17, 'Chính sách bảo hành mở rộng', 'Từ 18/07/2025, Anh Em Rọt Store cung cấp dịch vụ bảo hành mở rộng lên đến 2 năm cho các sản phẩm điện tử cao cấp như iPhone, MacBook, và Samsung Galaxy. Chương trình áp dụng cho khách hàng đăng ký trong tháng 7, kèm kiểm tra định kỳ miễn phí 6 tháng/lần!', 'chính sách', '2025-07-18', 'assets/news/extended_warranty.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int NOT NULL,
  `order_code` varchar(50) DEFAULT NULL,
  `user_id` int NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text,
  `city` varchar(50) DEFAULT NULL,
  `payment_method` enum('cod','bank','momo') DEFAULT 'cod',
  `notes` text,
  `total` decimal(10,2) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `district` varchar(100) NOT NULL,
  `ward` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_code`, `user_id`, `full_name`, `email`, `phone`, `address`, `city`, `payment_method`, `notes`, `total`, `status`, `created_at`, `district`, `ward`) VALUES
(1, NULL, 2, NULL, NULL, NULL, NULL, NULL, 'cod', NULL, 33980000.00, 'completed', '2025-07-12 15:51:31', '', ''),
(2, NULL, 2, NULL, NULL, NULL, NULL, NULL, 'cod', NULL, 7990000.00, 'pending', '2025-07-12 15:51:31', '', ''),
(3, 'ORD20250712190045986', 1, 'Hoàng Quang Đạt', 'hoangquangdat182005@gmail.com', '0827925712', 'Hà Nội', 'Hà Nội', 'bank', '', 5669.92, 'delivered', '2025-07-12 17:00:45', '', ''),
(4, 'ORD20250713092827475', 2, 'Hoàng Quang Đạt', 'hoangquangdat182005@gmail.com', '0827925712', 'Hà Nội', 'Tỉnh Hưng Yên', 'cod', '', 50000.00, 'pending', '2025-07-13 07:28:27', 'Huyện Khoái Châu', 'Xã Chí Minh'),
(5, 'ORD20250713092943880', 2, 'Hoàng Quang Đạt', 'hoangquangdat182005@gmail.com', '0827925712', 'Hà Nội', 'Tỉnh Bình Định', 'cod', '', 9999.00, 'pending', '2025-07-13 07:29:43', 'Huyện An Lão', 'Xã An Trung'),
(6, 'ORD20250713093346821', 2, 'Hoàng Quang Đạt', 'hoangquangdat182005@gmail.com', '0827925712', 'Hà Nội', 'Tỉnh Bến Tre', 'bank', '', 0.00, 'pending', '2025-07-13 07:33:46', 'Huyện Mỏ Cày Bắc', 'Xã Tân Thanh Tây'),
(7, 'ORD20250713100130357', 2, 'Hoàng Quang Đạt', 'hoangquangdat182005@gmail.com', '0827925712', 'Hà Nội', 'Tỉnh Bến Tre', 'momo', '', 0.00, 'pending', '2025-07-13 08:01:30', 'Huyện Thạnh Phú', 'Xã Thạnh Phong'),
(8, 'ORD20250713101451754', 2, 'Hoàng Quang Đạt', 'hoangquangdat182005@gmail.com', '0827925712', 'Hà Nội', 'Tỉnh Bình Định', 'cod', '', 0.00, 'pending', '2025-07-13 08:14:51', 'Huyện Tuy Phước', 'Xã Phước Thuận'),
(9, 'ORD20250713102321127', 1, 'Hoàng Quang Đạt', 'hoangquangdat182005@gmail.com', '0827925712', 'Hà Nội', 'Tỉnh Bình Định', 'cod', '', 0.00, 'pending', '2025-07-13 08:23:21', 'Huyện Tây Sơn', 'Xã Tây Xuân'),
(10, 'ORD20250713102434169', 1, 'Hoàng Quang Đạt', 'hoangquangdat182005@gmail.com', '0827925712', 'Hà Nội', 'Thành phố Huế', 'cod', '', 0.00, 'pending', '2025-07-13 08:24:34', 'Quận Thuận Hóa', 'Phường Vĩnh Ninh'),
(11, 'ORD20250713104050228', 1, 'Hoàng Quang Đạt', 'hoangquangdat182005@gmail.com', '0827925712', 'Hà Nội', 'Tỉnh Bắc Kạn', 'cod', '', 0.00, 'pending', '2025-07-13 08:40:50', 'Huyện Pác Nặm', 'Xã Bộc Bố'),
(12, 'ORD20250713104646210', 1, 'Hoàng Quang Đạt', 'hoangquangdat182005@gmail.com', '0827925712', 'Hà Nội', 'Tỉnh Cao Bằng', 'cod', '', 0.00, 'pending', '2025-07-13 08:46:46', 'Huyện Nguyên Bình', 'Xã Hưng Đạo'),
(13, 'ORD20250713104751471', 2, 'Hoàng Quang Đạt', 'hoangquangdat182005@gmail.com', '0827925712', 'Hà Nội', 'Tỉnh Bình Định', 'cod', '', 0.00, 'pending', '2025-07-13 08:47:51', 'Huyện Vân Canh', 'Xã Canh Hòa'),
(14, 'ORD20250713104926863', 2, 'Hoàng Quang Đạt', 'hoangquangdat182005@gmail.com', '0827925712', 'Hà Nội', 'Tỉnh Cao Bằng', 'bank', '', 0.00, 'pending', '2025-07-13 08:49:26', 'Huyện Bảo Lâm', 'Xã Thái Học'),
(15, 'ORD20250713112920390', 2, 'Hoa Sơn Quý', 'kiogit30@gmail.com', '0127865412', 'Nhà giàu nhất Kim Bôi', 'Tỉnh Hoà Bình', 'bank', '', 0.00, 'cancelled', '2025-07-13 09:29:20', 'Huyện Kim Bôi', 'Xã Vĩnh Đồng'),
(16, 'ORD20250713113643514', 2, 'Hoa Sơn Quý', 'hoangquangdat182005@gmail.com', '127865412', 'Nhà giàu nhất Kim Bôi', 'Tỉnh Hoà Bình', 'cod', '', 0.00, 'pending', '2025-07-13 09:36:43', 'Thành phố Hòa Bình', 'Phường Thái Bình'),
(17, 'ORD20250713125209862', 2, 'Hoa Sơn Quý', 'kiogit30@gmail.com', '127865412', 'Nhà giàu nhất Kim Bôi', 'Tỉnh Hoà Bình', 'cod', '', 0.00, 'shipped', '2025-07-13 10:52:09', 'Huyện Đà Bắc', 'Xã Nánh Nghê'),
(18, 'ORD20250715112230357', 2, 'Hoa Sơn Quý', 'kiogit30@gmail.com', '127865412', 'Nhà giàu nhất Kim Bôi', 'Tỉnh Hoà Bình', 'cod', '', 0.00, 'delivered', '2025-07-15 09:22:30', 'Huyện Kim Bôi', 'Xã Vĩnh Đồng');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int NOT NULL,
  `order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int DEFAULT '1',
  `price` decimal(10,2) NOT NULL,
  `storage` varchar(50) DEFAULT NULL,
  `color` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`, `storage`, `color`) VALUES
(1, 1, 1, 1, 33990000.00, NULL, NULL),
(2, 2, 14, 1, 7990000.00, NULL, NULL),
(3, 3, 2, 9, 629.99, '64GB', 'Black'),
(4, 4, 1, 1, 719.99, '64GB', 'Blue'),
(5, 4, 2, 1, 629.99, '64GB', 'Black'),
(6, 5, 2, 3, 629.99, '64GB', 'Black'),
(7, 5, 3, 1, 599.99, '64GB', 'Black'),
(8, 6, 1, 1, 719.99, '64GB', 'Black'),
(9, 7, 2, 1, 629.99, '64GB', 'Black'),
(10, 7, 3, 1, 599.99, '64GB', 'Black'),
(11, 8, 1, 1, 719.99, '64GB', 'Black'),
(12, 8, 2, 1, 629.99, '64GB', 'Black'),
(13, 9, 1, 1, 719.99, '64GB', 'Black'),
(14, 9, 2, 1, 629.99, '64GB', 'Black'),
(15, 10, 2, 1, 629.99, '64GB', 'Black'),
(16, 11, 2, 1, 629.99, '64GB', 'Black'),
(17, 12, 1, 1, 719.99, '64GB', 'Black'),
(18, 12, 2, 1, 629.99, '64GB', 'Black'),
(19, 12, 3, 1, 599.99, '64GB', 'Black'),
(20, 13, 2, 1, 629.99, '64GB', 'Black'),
(21, 13, 3, 2, 599.99, '64GB', 'Black'),
(22, 14, 2, 1, 629.99, '64GB', 'Black'),
(23, 15, 2, 1, 629.99, '64GB', 'Black'),
(24, 16, 2, 1, 629.99, '64GB', 'Black'),
(25, 17, 2, 1, 629.99, '64GB', 'Black'),
(26, 17, 3, 1, 599.99, '64GB', 'Black'),
(27, 18, 2, 3, 629.99, '64GB', 'Black'),
(28, 18, 3, 2, 599.99, '64GB', 'Black');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int NOT NULL,
  `order_id` int NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `method` enum('COD','MOMO','BANK') DEFAULT 'COD',
  `status` enum('pending','paid','failed') DEFAULT 'pending',
  `paid_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `order_id`, `amount`, `method`, `status`, `paid_at`) VALUES
(1, 1, 33990000.00, 'MOMO', 'paid', '2025-07-12 22:51:31'),
(2, 4, 1349.98, 'COD', 'pending', NULL),
(3, 5, 2489.96, 'COD', 'pending', NULL),
(4, 6, 719.99, 'BANK', 'pending', NULL),
(5, 7, 1229.98, 'MOMO', 'pending', NULL),
(6, 8, 1349.98, 'COD', 'pending', NULL),
(7, 9, 1349.98, 'COD', 'pending', NULL),
(8, 10, 629.99, 'COD', 'pending', NULL),
(9, 11, 629.99, 'COD', 'pending', NULL),
(10, 12, 1949.97, 'COD', 'pending', NULL),
(11, 13, 1829.97, 'COD', 'pending', NULL),
(12, 14, 629.99, 'BANK', 'pending', NULL),
(13, 15, 629.99, 'BANK', 'pending', NULL),
(14, 16, 629.99, 'COD', 'pending', NULL),
(15, 17, 1229.98, 'COD', 'pending', NULL),
(16, 18, 3089.95, 'COD', 'pending', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int NOT NULL,
  `product_code` varchar(100) DEFAULT NULL,
  `product_name` varchar(150) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text,
  `product_image` varchar(255) DEFAULT NULL,
  `category_id` int DEFAULT NULL,
  `supplier_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `product_code`, `product_name`, `price`, `description`, `product_image`, `category_id`, `supplier_id`, `created_at`, `updated_at`, `status`) VALUES
(1, NULL, 'iPhone 15 Pro Max 256GB', 33990000.00, 'Màn hình 6.7\", Chip A17 Pro, Camera 48MP, sang', 'img_68748b365c3351.48799595.png', 1, 1, '2025-07-12 15:51:31', '2025-07-15 14:36:59', 1),
(2, NULL, 'iPhone 15 Pro 128GB', 26990000.00, 'Màn hình 6.1\", Chip A17 Pro, Titanium', 'iphone11prm_vangdong.png', 1, 1, '2025-07-12 15:51:31', '2025-07-15 14:36:59', 1),
(3, NULL, 'iPhone 15 128GB', 22990000.00, 'Màn hình 6.1\", Chip A16 Bionic', 'iphone/iphone15_xanhduongnhat.png', 1, 1, '2025-07-12 15:51:31', '2025-07-15 14:36:59', 1),
(4, NULL, 'iPhone 14 128GB', 18990000.00, 'Màn hình 6.1\", Chip A15 Bionic', 'iphone/iphone14_xanhduong_256v128.png', 1, 2, '2025-07-12 15:51:31', '2025-07-15 14:36:59', 1),
(5, NULL, 'iPad Pro 11-inch M2', 22990000.00, 'Màn hình Liquid Retina, hỗ trợ Apple Pencil', 'ipad/ipad_proM4_bac.png', 2, 1, '2025-07-12 15:51:31', '2025-07-15 14:36:59', 1),
(6, NULL, 'iPad Air 10.9-inch M1', 16990000.00, 'Thiết kế mỏng nhẹ, hiệu năng mạnh mẽ', 'ipad/ipad_Air6M2_Tim.png', 2, 1, '2025-07-12 15:51:31', '2025-07-15 14:36:59', 1),
(7, NULL, 'iPad 10th Gen', 12990000.00, 'Thiết kế hiện đại, giá cả phải chăng', 'ipad/ipad_10_Xanhduong.png', 2, 2, '2025-07-12 15:51:31', '2025-07-15 14:36:59', 1),
(8, NULL, 'MacBook Pro 14-inch M3', 49990000.00, 'Hiệu năng đỉnh cao cho công việc chuyên nghiệp', 'macbook/MacbookPro_Bac.png', 3, 1, '2025-07-12 15:51:31', '2025-07-15 14:36:59', 1),
(9, NULL, 'MacBook Air 13-inch M2', 28990000.00, 'Mỏng nhẹ, pin lâu, hiệu năng ấn tượng', 'macbook/MacbookAir_Xanhdatroinhat.png', 3, 1, '2025-07-12 15:51:31', '2025-07-15 14:36:59', 1),
(10, NULL, 'iMac 24-inch M1', 34990000.00, 'Màn hình Retina 4.5K, 7 màu sắc', 'macbook/iMac24_M4_Hong.png', 3, 2, '2025-07-12 15:51:31', '2025-07-15 14:36:59', 1),
(11, NULL, 'Apple Watch Series 9', 14990000.00, 'Theo dõi sức khỏe chính xác', 'watch/applewatch_series10_bac.png', 4, 1, '2025-07-12 15:51:31', '2025-07-15 14:36:59', 1),
(12, NULL, 'Apple Watch Ultra 2', 21990000.00, 'Dành cho vận động viên, pin lâu', 'watch/applewatch_ultra2_dayalpine.png', 4, 1, '2025-07-12 15:51:31', '2025-07-15 14:36:59', 1),
(13, NULL, 'Apple Watch SE', 8990000.00, 'Trải nghiệm Apple Watch giá tốt', 'watch/applewatch_se2_trangstarlight.png', 4, 2, '2025-07-12 15:51:31', '2025-07-15 14:36:59', 1),
(14, NULL, 'AirPods Pro 2', 7990000.00, 'Chống ồn chủ động, chất âm tuyệt vời', 'tainghe/Airpods_pro_usb_c.png', 5, 1, '2025-07-12 15:51:31', '2025-07-15 14:36:59', 1),
(15, NULL, 'AirPods 3', 4990000.00, 'Thiết kế in-ear, chất âm cao cấp', 'tainghe/Airpod3_lightning.png', 5, 1, '2025-07-12 15:51:31', '2025-07-15 14:36:59', 1),
(16, NULL, 'AirPods Max', 12990000.00, 'Tai nghe over-ear cao cấp', 'tainghe/AirpodsMax_Hong.png', 5, 2, '2025-07-12 15:51:31', '2025-07-15 14:36:59', 1),
(17, NULL, 'Adapter sạc 20W USB-C', 590000.00, 'Sạc nhanh cho iPhone và iPad', 'phukien/Adapter_sacusbC.png', 6, 1, '2025-07-12 15:51:31', '2025-07-15 14:36:59', 1),
(18, NULL, 'Cáp USB-C 1m', 490000.00, 'Cáp chính hãng Apple', 'phukien/cap_typeC.png', 6, 1, '2025-07-12 15:51:31', '2025-07-15 14:36:59', 1),
(19, NULL, 'Apple Pencil Pro', 3490000.00, 'Bút cảm ứng cho iPad Pro', 'phukien/apple_pencilpro.png', 6, 2, '2025-07-12 15:51:31', '2025-07-15 14:36:59', 1),
(20, NULL, 'Ốp lưng iPhone 15 Pro', 1290000.00, 'Ốp lưng chính hãng', 'phukien/cuonglucIphone.png', 6, 2, '2025-07-12 15:51:31', '2025-07-15 14:36:59', 1),
(21, NULL, 'iPhone SE 2027', 9999.00, 'abc', 'img_68748b5d3e55f6.62592949.png', NULL, NULL, '2025-07-14 04:45:17', '2025-07-15 14:36:59', 1);

-- --------------------------------------------------------

--
-- Table structure for table `product_variants`
--

CREATE TABLE `product_variants` (
  `id` int NOT NULL,
  `product_id` int NOT NULL,
  `variant_code` varchar(100) DEFAULT NULL,
  `variant_price` decimal(10,2) NOT NULL,
  `stock_quantity` int DEFAULT '0',
  `variant_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_variant_attribute_links`
--

CREATE TABLE `product_variant_attribute_links` (
  `variant_id` int NOT NULL,
  `attribute_value_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `promotions`
--

CREATE TABLE `promotions` (
  `id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `promotions`
--

INSERT INTO `promotions` (`id`, `title`, `description`, `type`, `expiry_date`, `image_url`) VALUES
(1, 'Giảm 10% cho iPhone 15', 'Giảm 10% cho tất cả mẫu iPhone 15 từ 13/07/2025 đến 20/07/2025. Chương trình áp dụng cho đơn hàng từ 5 triệu đồng, bao gồm cả phiên bản Pro và Pro Max. Khách hàng sẽ nhận thêm quà tặng là sạc không dây khi mua trực tiếp tại cửa hàng. Số lượng ưu đãi có hạn, hãy nhanh tay đặt hàng!', 'giảm giá', '2025-07-20', 'assets/promotion/iphone15.jpg'),
(2, 'Mua 2 tặng 1 phụ kiện', 'Mua 2 sản phẩm bất kỳ tại Anh Em Rọt Store, tặng ngay 1 ốp lưng hoặc sạc dự phòng trị giá lên đến 1 triệu đồng. Chương trình áp dụng đến 15/07/2025, dành cho tất cả khách hàng. Hãy kết hợp mua điện thoại và phụ kiện để nhận ưu đãi này. Liên hệ ngay để biết thêm chi tiết!', 'tặng phẩm', '2025-07-15', 'assets/promotion/accessory.jpg'),
(3, 'Giảm giá sạc nhanh 20%', 'Giảm 20% cho sạc nhanh từ 15/07/2025 đến 18/07/2025. Sản phẩm hỗ trợ sạc nhanh 65W, tương thích với hầu hết các dòng điện thoại hiện nay. Chương trình áp dụng cho 50 khách hàng đầu tiên mỗi ngày tại cửa hàng. Đặt hàng online để nhận mã giảm giá đặc biệt!', 'giảm giá', '2025-07-18', 'assets/promotion/charger.jpg'),
(4, 'Giảm 20% cho MacBook Air M3', 'Giảm 20% cho MacBook Air M3 từ 16/07/2025 đến 23/07/2025. Sản phẩm sở hữu chip M3 8 nhân, màn hình Retina 13.6 inch, và pin 18 giờ. Ưu đãi áp dụng cho 50 khách hàng đầu tiên, kèm bảo hành mở rộng 1 năm. Đặt hàng ngay để nhận ưu đãi!', 'giảm giá', '2025-07-23', 'assets/promotion/macbook_air.jpg'),
(5, 'Tặng sạc dự phòng khi mua iPad', 'Mua iPad bất kỳ, tặng sạc dự phòng 10.000mAh từ 17/07/2025 đến 20/07/2025. Chương trình áp dụng cho tất cả mẫu iPad, bao gồm Air và Pro. Số lượng quà tặng có hạn, chỉ 100 suất mỗi ngày. Đặt hàng online để nhận ưu đãi nhanh chóng!', 'tặng phẩm', '2025-07-20', 'assets/promotion/ipad.jpg'),
(6, 'Giảm 30% tai nghe không dây', 'Khuyến mãi 30% cho tai nghe không dây từ 18/07/2025 đến 22/07/2025. Sản phẩm bao gồm các thương hiệu JBL, Sony, và Anker với chất lượng âm thanh cao cấp. Ưu đãi dành cho 75 khách hàng đầu tiên mỗi ngày. Đến cửa hàng ngay!', 'giảm giá', '2025-07-22', 'assets/promotion/wireless_earbuds.jpg'),
(7, 'Mua 4 tặng 1 dây sạc', 'Mua 4 sản phẩm bất kỳ, tặng 1 dây sạc nhanh Type-C trị giá 200k. Chương trình áp dụng đến 19/07/2025 tại tất cả chi nhánh. Ưu đãi dành cho khách hàng thanh toán trực tiếp, kèm hỗ trợ kiểm tra sản phẩm miễn phí!', 'tặng phẩm', '2025-07-19', 'assets/promotion/charging_cable.jpg'),
(8, 'Giảm 15% cho loa Bluetooth', 'Giảm 15% cho tất cả loa Bluetooth từ 15/07/2025 đến 21/07/2025. Sản phẩm bao gồm các dòng JBL Flip, Bose SoundLink, và hơn thế nữa. Ưu đãi áp dụng cho đơn từ 2 triệu đồng, kèm bảo hành 6 tháng. Đặt hàng ngay!', 'giảm giá', '2025-07-21', 'assets/promotion/bluetooth_speaker.jpg'),
(9, 'Giảm 18% cho iMac 2025', 'Giảm 18% cho iMac 2025 từ 20/07/2025 đến 27/07/2025. Sản phẩm sở hữu chip M4, màn hình Retina 24 inch, và thiết kế mỏng nhẹ. Ưu đãi áp dụng cho 60 khách hàng đầu tiên, kèm bảo hành mở rộng 18 tháng. Đặt hàng ngay để nhận ưu đãi!', 'giảm giá', '2025-07-27', 'assets/promotion/imac2025.jpg'),
(10, 'Tặng bàn phím khi mua laptop', 'Mua bất kỳ laptop nào, tặng bàn phím cơ trị giá 1 triệu đồng từ 21/07/2025 đến 24/07/2025. Chương trình áp dụng cho MacBook, Dell, và HP. Số lượng quà tặng có hạn, chỉ 80 suất mỗi ngày. Đặt hàng online để nhận ưu đãi nhanh!', 'tặng phẩm', '2025-07-24', 'assets/promotion/laptop_keyboard.jpg'),
(11, 'Giảm 25% cho máy chiếu mini', 'Khuyến mãi 25% cho máy chiếu mini từ 19/07/2025 đến 26/07/2025. Sản phẩm hỗ trợ độ phân giải Full HD, kết nối không dây, và pin 5 giờ. Ưu đãi dành cho 50 khách hàng đầu tiên mỗi ngày. Đến cửa hàng ngay!', 'giảm giá', '2025-07-26', 'assets/promotion/mini_projector.jpg'),
(12, 'Mua 5 tặng 1 túi chống sốc', 'Mua 5 sản phẩm bất kỳ, tặng 1 túi chống sốc cao cấp trị giá 400k. Chương trình áp dụng đến 22/07/2025 tại tất cả chi nhánh. Ưu đãi dành cho khách hàng thanh toán trực tiếp, kèm hỗ trợ giao hàng miễn phí nội thành!', 'tặng phẩm', '2025-07-22', 'assets/promotion/shockproof_bag.jpg'),
(13, 'Giảm 12% cho máy chơi game', 'Giảm 12% cho máy chơi game từ 18/07/2025 đến 25/07/2025. Sản phẩm bao gồm Nintendo Switch, PlayStation 5, và Xbox Series X. Ưu đãi áp dụng cho đơn từ 10 triệu đồng, kèm bảo hành 1 năm. Đặt hàng ngay!', 'giảm giá', '2025-07-25', 'assets/promotion/gaming_console.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_items`
--

CREATE TABLE `purchase_items` (
  `id` int NOT NULL,
  `purchase_order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `purchase_items`
--

INSERT INTO `purchase_items` (`id`, `purchase_order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 1, 1, 10, 450.50),
(2, 1, 2, 5, 451.50),
(3, 2, 1, 15, 460.00);

-- --------------------------------------------------------

--
-- Table structure for table `purchase_orders`
--

CREATE TABLE `purchase_orders` (
  `id` int NOT NULL,
  `supplier_id` int NOT NULL,
  `user_id` int NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `note` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `purchase_orders`
--

INSERT INTO `purchase_orders` (`id`, `supplier_id`, `user_id`, `total`, `note`, `created_at`) VALUES
(1, 1, 1, 6762.50, 'Nhập hàng định kỳ tháng 7', '2025-07-13 08:37:00'),
(2, 1, 1, 6900.00, 'Nhập bổ sung cho cửa hàng', '2025-07-13 08:37:00');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `product_id` int NOT NULL,
  `rating` int NOT NULL,
  `comment` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `user_id`, `product_id`, `rating`, `comment`, `created_at`) VALUES
(1, 2, 1, 5, 'Sản phẩm tuyệt vời, chụp ảnh đẹp', '2025-07-12 15:51:31'),
(2, 2, 14, 4, 'Chất âm tốt nhưng giá hơi cao', '2025-07-12 15:51:31');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `name`, `email`, `phone`, `address`, `created_at`) VALUES
(1, 'Apple Inc.', 'supplier@apple.com', '+1 800-692-7753', '1 Apple Park Way, Cupertino, CA, USA', '2025-07-12 15:51:31'),
(2, 'Nhà phân phối Apple Việt Nam', 'contact@apple-vn.com', '+84 28 3821 0511', 'Tòa nhà Viettel, TP.HCM, Việt Nam', '2025-07-12 15:51:31');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','customer') DEFAULT 'customer',
  `verify_token` varchar(64) DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT '0',
  `phone` varchar(20) DEFAULT NULL,
  `address` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `verify_token_expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `password`, `role`, `verify_token`, `is_verified`, `phone`, `address`, `created_at`, `updated_at`, `verify_token_expires_at`) VALUES
(1, 'Admin User', 'admin@example.com', '$2y$10$VIeCxulcxI5ONTWy4deQ2u7E6e3W4KTgqYmQvxTvVQrwxF5RjS8ZK', 'admin', NULL, 1, '0987654321', '123 Admin Street', '2025-07-12 15:51:31', NULL, NULL),
(2, 'Hoàng Quang Đạt', 'hoangquangdat182005@gmail.com', '$2y$10$VIeCxulcxI5ONTWy4deQ2u7E6e3W4KTgqYmQvxTvVQrwxF5RjS8ZK', 'customer', NULL, 1, '0912345678', 'Hà Nội', '2025-07-12 15:51:31', '2025-07-13 11:29:28', NULL),
(3, 'Hoa Sơn Quý', 'kiogit30@gmail.com', '$2y$10$e4g.K/SEpnu/y52SJzWXKOqe.nH.cjH6zwSrLPTjTbC.dN8Y88y.2', 'customer', 'b3deb28406d7fd4bca64e3d493174e8dcaf1a8b7c098dfa5a4f4558b02755162', 1, '0127865412', 'Nhà giàu nhất Kim Bôi', '2025-07-14 06:22:41', '2025-07-14 06:24:28', '2025-07-15 08:22:41'),
(4, 'Lã Tấn Lộc', 'latanloc24012005@gmail.com', '$2y$10$nDWJ8nV0BoX0y607hnv5BuZ.eRVgT4CwSXBGSwrURVpTxuSIrnFuu', 'customer', '6aeedcd235ad975c2e4a6301513573a0521a582abc34e4317d4b625e60fd1212', 1, '08275823190', 'Nhà giàu', '2025-07-15 08:57:07', '2025-07-15 08:59:01', '2025-07-16 10:57:07');

-- --------------------------------------------------------

--
-- Table structure for table `user_detail`
--

CREATE TABLE `user_detail` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `gender` enum('Nam','Nữ','Khác') DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user_detail`
--

INSERT INTO `user_detail` (`id`, `user_id`, `gender`, `avatar`, `updated_at`) VALUES
(1, 2, 'Nam', 'uploads/avatars/avatar_687398984f3a0_logo.png', '2025-07-13 11:29:28'),
(5, 3, 'Nam', 'uploads/avatars/avatar_6874a29cd350b_467471866_1104405337319429_8026237455612096783_n.png', '2025-07-14 06:24:28'),
(6, 4, 'Nam', 'uploads/avatars/avatar_68761855528cd_467471866_1104405337319429_8026237455612096783_n.png', '2025-07-15 08:59:01');

-- --------------------------------------------------------

--
-- Table structure for table `variant_attributes`
--

CREATE TABLE `variant_attributes` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `display_name` varchar(100) NOT NULL,
  `type` varchar(50) DEFAULT 'text',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `variant_attribute_values`
--

CREATE TABLE `variant_attribute_values` (
  `id` int NOT NULL,
  `attribute_id` int NOT NULL,
  `value` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wishlists`
--

CREATE TABLE `wishlists` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `product_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `wishlists`
--

INSERT INTO `wishlists` (`id`, `user_id`, `product_id`, `created_at`) VALUES
(1, 2, 8, '2025-07-12 15:51:31'),
(2, 2, 11, '2025-07-12 15:51:31');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `fk_cart_variant` (`variant_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_category_parent` (`parent_id`);

--
-- Indexes for table `email_queue`
--
ALTER TABLE `email_queue`
  ADD PRIMARY KEY (`id`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `email_templates`
--
ALTER TABLE `email_templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_code` (`order_code`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `product_code` (`product_code`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indexes for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `variant_code` (`variant_code`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `product_variant_attribute_links`
--
ALTER TABLE `product_variant_attribute_links`
  ADD PRIMARY KEY (`variant_id`,`attribute_value_id`),
  ADD KEY `attribute_value_id` (`attribute_value_id`);

--
-- Indexes for table `promotions`
--
ALTER TABLE `promotions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `purchase_items`
--
ALTER TABLE `purchase_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_order_id` (`purchase_order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_detail`
--
ALTER TABLE `user_detail`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `variant_attributes`
--
ALTER TABLE `variant_attributes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `variant_attribute_values`
--
ALTER TABLE `variant_attribute_values`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `attribute_id` (`attribute_id`,`value`);

--
-- Indexes for table `wishlists`
--
ALTER TABLE `wishlists`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `email_queue`
--
ALTER TABLE `email_queue`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `email_templates`
--
ALTER TABLE `email_templates`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=388;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `product_variants`
--
ALTER TABLE `product_variants`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `promotions`
--
ALTER TABLE `promotions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `purchase_items`
--
ALTER TABLE `purchase_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `user_detail`
--
ALTER TABLE `user_detail`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `variant_attributes`
--
ALTER TABLE `variant_attributes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `variant_attribute_values`
--
ALTER TABLE `variant_attribute_values`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wishlists`
--
ALTER TABLE `wishlists`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cart_variant` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `fk_category_parent` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `logs`
--
ALTER TABLE `logs`
  ADD CONSTRAINT `logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_products_supplier` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`);

--
-- Constraints for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD CONSTRAINT `product_variants_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_variant_attribute_links`
--
ALTER TABLE `product_variant_attribute_links`
  ADD CONSTRAINT `product_variant_attribute_links_ibfk_1` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_variant_attribute_links_ibfk_2` FOREIGN KEY (`attribute_value_id`) REFERENCES `variant_attribute_values` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `purchase_items`
--
ALTER TABLE `purchase_items`
  ADD CONSTRAINT `purchase_items_ibfk_1` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchase_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD CONSTRAINT `purchase_orders_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`),
  ADD CONSTRAINT `purchase_orders_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_detail`
--
ALTER TABLE `user_detail`
  ADD CONSTRAINT `user_detail_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `variant_attribute_values`
--
ALTER TABLE `variant_attribute_values`
  ADD CONSTRAINT `variant_attribute_values_ibfk_1` FOREIGN KEY (`attribute_id`) REFERENCES `variant_attributes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `wishlists`
--
ALTER TABLE `wishlists`
  ADD CONSTRAINT `wishlists_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wishlists_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
