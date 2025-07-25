-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 22, 2025 at 10:39 AM
-- Server version: 10.4.32-MariaDB
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
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `variant_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `added_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `slug` varchar(120) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `parent_id`, `is_active`, `slug`) VALUES
(1, 'iPhone', NULL, 1, 'iphone'),
(2, 'Mac', NULL, 1, 'mac'),
(3, 'iPad', NULL, 1, 'ipad'),
(4, 'Apple Watch', NULL, 1, 'apple-watch'),
(5, 'AirPods', NULL, 1, 'airpods'),
(6, 'Accessory', NULL, 1, 'phu-kien'),
(7, 'iPhone 15 Series', 1, 1, 'iphone-15-series'),
(8, 'iPhone 14 Series', 1, 1, 'iphone-14-series'),
(9, 'iPhone 13 Series', 1, 1, 'iphone-13-series'),
(10, 'iPhone Cũ', 1, 1, 'iphone-cu'),
(11, 'MacBook Air', 2, 1, 'macbook-air'),
(12, 'MacBook Pro', 2, 1, 'macbook-pro'),
(13, 'iMac', 2, 1, 'imac'),
(14, 'Mac Mini', 2, 1, 'mac-mini'),
(15, 'iPad Pro', 3, 1, 'ipad-pro'),
(16, 'iPad Air', 3, 1, 'ipad-air'),
(17, 'iPad Gen', 3, 1, 'ipad-gen'),
(18, 'iPad Mini', 3, 1, 'ipad-mini'),
(19, 'Apple Watch Ultra', 4, 1, 'apple-watch-ultra'),
(20, 'Apple Watch Series', 4, 1, 'apple-watch-series'),
(21, 'Apple Watch SE', 4, 1, 'apple-watch-se'),
(22, 'AirPods Pro', 5, 1, 'airpods-pro'),
(23, 'AirPods 3', 5, 1, 'airpods-3'),
(24, 'AirPods 2', 5, 1, 'airpods-2'),
(25, 'AirPods Max', 5, 1, 'airpods-max'),
(26, 'Ốp lưng iPhone', 6, 1, 'op-lung-iphone'),
(27, 'Bao da iPhone', 6, 1, 'bao-da-iphone'),
(28, 'Sạc cáp iPhone', 6, 1, 'sac-cap-iphone'),
(29, 'Miếng dán màn hình', 6, 1, 'mieng-dan-man-hinh');

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contacts`
--

INSERT INTO `contacts` (`id`, `name`, `email`, `message`, `created_at`) VALUES
(1, 'Lã Tấn Lộc', 'hoangquangdat182005@gmail.com', 'Lồ ton', '2025-07-18 15:32:07');

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `details` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `logs`
--

INSERT INTO `logs` (`id`, `user_id`, `action`, `details`, `created_at`) VALUES
(1, 1, 'logout', 'Admin logged out', '2025-07-15 14:55:06'),
(2, NULL, 'visit_index', 'User visited customer index page', '2025-07-17 06:45:52'),
(3, 3, 'visit_index', 'User visited customer index page', '2025-07-17 07:00:39'),
(4, 3, 'visit_index', 'User visited customer index page', '2025-07-17 07:01:05'),
(5, 3, 'logout', 'Admin logged out', '2025-07-17 07:01:07'),
(6, 3, 'visit_admin_dashboard', 'Admin visited dashboard at 09:06 AM, 17/07/2025', '2025-07-17 07:06:22'),
(7, 3, 'lock_user', 'Unlocked user ID: 2', '2025-07-17 07:21:15'),
(8, 3, 'lock_user', 'Unlocked user ID: 2', '2025-07-17 07:22:55'),
(9, 3, 'lock_user', 'Unlocked user ID: 2', '2025-07-17 07:23:15'),
(10, 3, 'visit_admin_dashboard', 'Admin visited dashboard at 09:26 AM, 17/07/2025', '2025-07-17 07:26:19'),
(11, 3, 'logout', 'Admin logged out', '2025-07-17 07:29:48'),
(12, 3, 'visit_admin_dashboard', 'Admin visited dashboard at 09:31 AM, 17/07/2025', '2025-07-17 07:31:44'),
(13, 3, 'lock_user', 'Locked user ID: 4', '2025-07-17 07:31:53'),
(14, 3, 'lock_user', 'Unlocked user ID: 4', '2025-07-17 07:31:58'),
(15, NULL, 'visit_index', 'User visited customer index page', '2025-07-17 15:50:40'),
(16, NULL, 'visit_index', 'User visited customer index page', '2025-07-17 16:11:21'),
(17, 3, 'visit_admin_dashboard', 'Admin visited dashboard at 06:12 PM, 17/07/2025', '2025-07-17 16:12:23'),
(18, 3, 'logout', 'Admin logged out', '2025-07-17 16:12:49'),
(19, 3, 'visit_admin_dashboard', 'Admin visited dashboard at 06:12 PM, 17/07/2025', '2025-07-17 16:12:50'),
(20, 3, 'logout', 'Admin logged out', '2025-07-17 16:12:59'),
(21, NULL, 'visit_index', 'User visited customer index page', '2025-07-17 16:17:25'),
(22, 5, 'visit_index', 'User visited customer index page', '2025-07-17 16:20:45'),
(23, 5, 'visit_index', 'User visited customer index page', '2025-07-17 16:20:52'),
(24, 5, 'logout', 'Admin logged out', '2025-07-17 16:31:29'),
(25, 5, 'visit_admin_dashboard', 'Admin visited dashboard at 06:31 PM, 17/07/2025', '2025-07-17 16:31:38'),
(26, 5, 'visit_admin_dashboard', 'Admin visited dashboard at 06:31 PM, 17/07/2025', '2025-07-17 16:31:43'),
(27, 5, 'add_category', 'Added category: Iphone 12 Series', '2025-07-17 16:35:28'),
(28, 5, 'delete_category', 'Deleted category ID: 30', '2025-07-17 16:35:42'),
(29, 5, 'visit_admin_dashboard', 'Admin visited dashboard at 07:43 PM, 17/07/2025', '2025-07-17 17:43:53'),
(30, 5, 'visit_admin_dashboard', 'Admin visited dashboard at 07:44 PM, 17/07/2025', '2025-07-17 17:44:42'),
(31, NULL, 'add_variant_stock', 'Added 1 units to product variant ID: 9 stock.', '2025-07-17 17:47:30'),
(32, 5, 'visit_admin_dashboard', 'Admin visited dashboard at 09:07 PM, 17/07/2025', '2025-07-17 19:07:04'),
(33, 5, 'visit_admin_dashboard', 'Admin visited dashboard at 09:24 PM, 17/07/2025', '2025-07-17 19:24:09'),
(34, NULL, 'add_variant_stock', 'Added 1000 units to product variant ID: 13 stock.', '2025-07-17 19:24:42'),
(35, NULL, 'visit_index', 'User visited customer index page', '2025-07-18 05:32:49'),
(36, 5, 'visit_admin_dashboard', 'Admin visited dashboard at 07:33 AM, 18/07/2025', '2025-07-18 05:33:09'),
(37, 5, 'visit_admin_dashboard', 'Admin visited dashboard at 08:05 AM, 18/07/2025', '2025-07-18 06:05:49'),
(38, 5, 'visit_admin_dashboard', 'Admin visited dashboard at 08:11 AM, 18/07/2025', '2025-07-18 06:11:53'),
(39, 5, 'visit_admin_dashboard', 'Admin visited dashboard at 08:44 AM, 18/07/2025', '2025-07-18 06:44:38'),
(40, 5, 'visit_admin_dashboard', 'Admin visited dashboard at 08:47 AM, 18/07/2025', '2025-07-18 06:47:33'),
(41, 5, 'visit_admin_dashboard', 'Admin visited dashboard at 09:43 AM, 18/07/2025', '2025-07-18 07:43:38'),
(42, 5, 'visit_admin_dashboard', 'Admin visited dashboard at 09:44 AM, 18/07/2025', '2025-07-18 07:44:03'),
(43, NULL, 'visit_index', 'User visited customer index page', '2025-07-18 07:45:20'),
(44, 3, 'visit_admin_dashboard', 'Admin visited dashboard at 09:45 AM, 18/07/2025', '2025-07-18 07:45:23'),
(45, 3, 'visit_admin_dashboard', 'Admin visited dashboard at 09:47 AM, 18/07/2025', '2025-07-18 07:47:51'),
(46, 3, 'visit_admin_dashboard', 'Admin visited dashboard at 09:48 AM, 18/07/2025', '2025-07-18 07:48:13'),
(47, 3, 'visit_admin_dashboard', 'Admin visited dashboard at 09:59 AM, 18/07/2025', '2025-07-18 07:59:03'),
(48, 3, 'visit_admin_dashboard', 'Admin visited dashboard at 09:59 AM, 18/07/2025', '2025-07-18 07:59:32'),
(49, 3, 'visit_admin_dashboard', 'Admin visited dashboard at 10:05 AM, 18/07/2025', '2025-07-18 08:05:37'),
(50, 3, 'visit_admin_dashboard', 'Admin visited dashboard at 10:05 AM, 18/07/2025', '2025-07-18 08:05:39'),
(51, 3, 'visit_admin_dashboard', 'Admin visited dashboard at 10:05 AM, 18/07/2025', '2025-07-18 08:05:40'),
(52, 3, 'visit_admin_dashboard', 'Admin visited dashboard at 12:36 PM, 18/07/2025', '2025-07-18 10:36:06'),
(53, 3, 'visit_admin_dashboard', 'Admin visited dashboard at 12:37 PM, 18/07/2025', '2025-07-18 10:37:20'),
(54, 3, 'visit_admin_dashboard', 'Admin visited dashboard at 12:37 PM, 18/07/2025', '2025-07-18 10:37:21'),
(55, 3, 'visit_admin_dashboard', 'Admin visited dashboard at 12:42 PM, 18/07/2025', '2025-07-18 10:42:48'),
(56, 3, 'visit_admin_dashboard', 'Admin visited dashboard at 12:45 PM, 18/07/2025', '2025-07-18 10:45:21'),
(57, 3, 'visit_admin_dashboard', 'Admin visited dashboard at 12:45 PM, 18/07/2025', '2025-07-18 10:45:53'),
(58, 3, 'logout', 'Admin logged out', '2025-07-18 10:45:53'),
(59, 7, 'visit_index', 'User visited customer index page', '2025-07-18 10:49:04'),
(60, 7, 'visit_index', 'User visited customer index page', '2025-07-18 10:51:23'),
(61, 7, 'visit_index', 'User visited customer index page', '2025-07-18 10:51:38'),
(62, 7, 'visit_index', 'User visited customer index page', '2025-07-18 10:53:29'),
(63, 7, 'visit_index', 'User visited customer index page', '2025-07-18 10:55:57'),
(64, NULL, 'visit_index', 'User visited customer index page', '2025-07-18 11:05:15'),
(65, 7, 'visit_index', 'User visited customer index page', '2025-07-18 11:05:30'),
(66, 7, 'visit_index', 'User visited customer index page', '2025-07-18 11:18:02'),
(67, 7, 'visit_index', 'User visited customer index page', '2025-07-18 11:51:16'),
(68, 7, 'logout', 'Admin logged out', '2025-07-18 11:51:18'),
(69, 7, 'visit_index', 'User visited customer index page', '2025-07-18 11:51:19'),
(70, 7, 'visit_index', 'User visited customer index page', '2025-07-18 11:56:40'),
(71, 7, 'logout', 'Admin logged out', '2025-07-18 11:56:42'),
(72, 7, 'visit_index', 'User visited customer index page', '2025-07-18 11:56:43'),
(73, 7, 'visit_index', 'User visited customer index page', '2025-07-18 12:37:31'),
(74, 7, 'visit_index', 'User visited customer index page', '2025-07-18 12:37:41'),
(75, 7, 'visit_index', 'User visited customer index page', '2025-07-18 12:40:55'),
(76, 7, 'logout', 'Admin logged out', '2025-07-18 14:27:23'),
(77, 7, 'visit_index', 'User visited customer index page', '2025-07-18 14:27:29'),
(78, 7, 'visit_index', 'User visited customer index page', '2025-07-18 15:08:37'),
(79, 7, 'logout', 'Admin logged out', '2025-07-18 15:08:39'),
(80, 7, 'visit_index', 'User visited customer index page', '2025-07-18 15:08:42'),
(81, 7, 'visit_index', 'User visited customer index page', '2025-07-18 15:23:33'),
(82, 7, 'visit_index', 'User visited customer index page', '2025-07-18 15:26:41'),
(83, 7, 'visit_index', 'User visited customer index page', '2025-07-18 15:28:40'),
(84, 7, 'visit_index', 'User visited customer index page', '2025-07-18 15:28:54'),
(85, 7, 'submit_contact', 'User submitted contact form. Name: Lã Tấn Lộc, Email: hoangquangdat182005@gmail.com, Contact ID: 1', '2025-07-18 15:32:07'),
(86, NULL, 'visit_index', 'User visited customer index page', '2025-07-19 15:36:16'),
(87, 3, 'visit_admin_dashboard', 'Admin visited dashboard at 05:36 PM, 19/07/2025', '2025-07-19 15:36:19'),
(88, 3, 'logout', 'Admin logged out', '2025-07-19 15:36:24'),
(89, NULL, 'visit_index', 'User visited customer index page', '2025-07-19 15:36:31'),
(90, 7, 'visit_index', 'User visited customer index page', '2025-07-19 15:36:34'),
(91, 7, 'visit_index', 'User visited customer index page', '2025-07-19 15:51:57'),
(92, 7, 'visit_index', 'User visited customer index page', '2025-07-19 15:54:40'),
(93, 7, 'visit_index', 'User visited customer index page', '2025-07-19 15:56:58'),
(94, 7, 'logout', 'Admin logged out', '2025-07-19 15:57:00'),
(95, 7, 'visit_index', 'User visited customer index page', '2025-07-19 15:57:02'),
(96, NULL, 'visit_index', 'User visited customer index page', '2025-07-19 16:03:50'),
(97, 7, 'visit_index', 'User visited customer index page', '2025-07-19 16:04:01'),
(98, 7, 'visit_index', 'User visited customer index page', '2025-07-19 16:04:17'),
(99, 7, 'visit_index', 'User visited customer index page', '2025-07-19 16:04:23'),
(100, 7, 'visit_index', 'User visited customer index page', '2025-07-19 16:10:08'),
(101, 7, 'visit_index', 'User visited customer index page', '2025-07-19 16:10:32'),
(102, 7, 'visit_index', 'User visited customer index page', '2025-07-19 17:14:55'),
(103, 7, 'visit_index', 'User visited customer index page', '2025-07-19 17:15:11'),
(104, 3, 'visit_admin_dashboard', 'Admin visited dashboard at 08:22 PM, 19/07/2025', '2025-07-19 18:22:24'),
(105, NULL, 'add_variant_stock', 'Added 1 units to product variant ID: 13 stock.', '2025-07-19 18:30:24'),
(106, 3, 'lock_user', 'Locked user ID: 7', '2025-07-19 18:36:11'),
(107, 3, 'logout', 'Admin logged out', '2025-07-19 18:36:15'),
(108, 3, 'visit_admin_dashboard', 'Admin visited dashboard at 08:37 PM, 19/07/2025', '2025-07-19 18:37:08'),
(109, 3, 'lock_user', 'Unlocked user ID: 7', '2025-07-19 18:37:12'),
(110, NULL, 'visit_index', 'User visited customer index page', '2025-07-20 14:38:11'),
(111, 7, 'logout', 'Admin logged out', '2025-07-21 10:41:54'),
(112, 7, 'logout', 'Admin logged out', '2025-07-21 14:32:30'),
(113, 7, 'logout', 'Admin logged out', '2025-07-21 14:32:49'),
(114, 7, 'filter_products', 'Filtered products for category: iPhone, model: all, storage: all, color: all', '2025-07-21 15:42:59'),
(115, 7, 'filter_products', 'Filtered products for category: iPhone, model: all, storage: all, color: all', '2025-07-21 15:47:25'),
(116, 7, 'filter_products', 'Filtered products for category: iPhone, model: all, storage: all, color: all', '2025-07-21 15:48:13'),
(117, 7, 'filter_products', 'Filtered products for category: iPhone, model: all, storage: all, color: all', '2025-07-21 16:04:17'),
(118, 7, 'filter_products', 'Filtered products for category: iPhone, model: all, storage: all, color: all', '2025-07-21 16:06:06'),
(119, 7, 'filter_products', 'Filtered products for category: iPhone, model: all, storage: all, color: all', '2025-07-21 16:07:46'),
(120, 7, 'filter_products', 'Filtered products for category: iPhone, model: all, storage: all, color: all', '2025-07-21 16:08:11'),
(121, 7, 'filter_products', 'Filtered products for category: iPhone, model: all, storage: all, color: all', '2025-07-21 16:12:36'),
(122, 7, 'filter_products', 'Filtered products for category: iPhone, model: all, storage: all, color: all', '2025-07-21 16:12:41'),
(123, 7, 'filter_products', 'Filtered products for category: iPhone, model: all, storage: all, color: all', '2025-07-21 16:12:41'),
(124, 7, 'filter_products', 'Filtered products for category: iPhone, model: all, storage: all, color: all', '2025-07-21 16:12:42'),
(125, 7, 'filter_products', 'Filtered products for category: iPhone, model: all, storage: all, color: all', '2025-07-21 16:12:42'),
(126, 7, 'filter_products', 'Filtered products for category: iPhone, model: all, storage: all, color: all', '2025-07-21 16:12:42'),
(127, 7, 'logout', 'Admin logged out', '2025-07-21 16:12:51'),
(130, 3, 'visit_admin_dashboard', 'Admin visited dashboard at 06:14 PM, 21/07/2025', '2025-07-21 16:14:47'),
(131, 3, 'update_order_status', 'Updated order ID: 6 to status: delivered', '2025-07-21 16:15:08'),
(132, 3, 'logout', 'Admin logged out', '2025-07-21 16:15:30'),
(133, 7, 'filter_products', 'Filtered products for category: iPhone, model: all, storage: all, color: all', '2025-07-21 16:15:46'),
(134, 7, 'filter_products', 'Filtered products for category: iPhone, model: all, storage: all, color: all', '2025-07-21 16:19:44'),
(135, 7, 'filter_products', 'Filtered products for category: iPhone, model: all, storage: all, color: all', '2025-07-21 16:30:29'),
(136, 7, 'filter_products', 'Filtered products for category: iPhone, model: all, storage: all, color: all', '2025-07-21 16:32:03'),
(137, 7, 'filter_products', 'Filtered products for category: iPhone, model: all, storage: all, color: all', '2025-07-21 16:32:12'),
(138, 7, 'filter_products', 'Filtered products for category: iPhone, model: all, storage: all, color: all', '2025-07-21 16:32:14'),
(139, 7, 'filter_products', 'Filtered products for category: iPhone, model: all, storage: all, color: all', '2025-07-21 16:37:31'),
(140, 7, 'filter_products', 'Filtered products for category: iPhone, model: all, storage: all, color: all', '2025-07-21 16:52:55'),
(141, 7, 'filter_products', 'Filtered products for category: iPhone, model: all, storage: all, color: all', '2025-07-21 16:52:56'),
(142, 7, 'filter_products', 'Filtered products for category: iPhone, model: all, storage: all, color: all', '2025-07-21 16:52:58'),
(143, 7, 'filter_products', 'Filtered products for category: iPhone, model: all, storage: all, color: all', '2025-07-21 16:52:59'),
(144, 7, 'filter_products', 'Filtered products for category: iPhone, model: all, storage: all, color: all', '2025-07-21 16:53:01'),
(145, 7, 'filter_products', 'Filtered products for category: iPhone, model: all, storage: all, color: all', '2025-07-21 16:53:17'),
(146, 7, 'filter_products', 'Filtered products for category: iPhone, model: all, storage: all, color: all', '2025-07-21 16:56:39'),
(147, 7, 'filter_products', 'Filtered products for category: iPhone, model: all, storage: all, color: all', '2025-07-21 16:56:40'),
(148, 7, 'filter_products', 'Filtered products for category: iPhone, model: all, storage: all, color: all', '2025-07-21 16:57:05'),
(149, 3, 'visit_admin_dashboard', 'Admin visited dashboard at 08:41 PM, 21/07/2025', '2025-07-21 18:41:05');

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `author_id` int(11) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `slug` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `news`
--

INSERT INTO `news` (`id`, `title`, `content`, `image`, `created_at`, `updated_at`, `author_id`, `status`, `slug`) VALUES
(1, 'iPhone 15 Pro Max: Đỉnh Cao Công Nghệ', 'iPhone 15 Pro Max là mẫu điện thoại cao cấp nhất của Apple, được trang bị chip A17 Bionic, camera tiên tiến và thiết kế titan sang trọng.', 'iphone15_pro_max.jpg', '2023-10-26 10:00:00', '2024-07-15 16:34:40', 1, 1, 'iphone-15-pro-max-dinh-cao-cong-nghe'),
(2, 'MacBook Air M3: Hiệu Năng Vượt Trội', 'MacBook Air M3 mang đến hiệu năng mạnh mẽ với chip M3, thiết kế siêu mỏng nhẹ và thời lượng pin ấn tượng, lý tưởng cho công việc và giải trí.', 'macbook_air_m3.jpg', '2023-11-15 11:30:00', '2024-07-15 16:34:40', 1, 1, 'macbook-air-m3-hieu-nang-vuot-troi'),
(3, 'Apple Watch Series 9: Thông Minh Hơn, Mạnh Mẽ Hơn', 'Apple Watch Series 9 sở hữu chip S9 SiP mới, màn hình sáng hơn và các tính năng sức khỏe tiên tiến, giúp bạn sống khỏe mạnh hơn mỗi ngày.', 'apple_watch_s9.jpg', '2023-12-01 09:45:00', '2024-07-15 16:34:40', 1, 1, 'apple-watch-series-9-thong-minh-hon'),
(4, 'iPad Pro M4: Sức Mạnh Đột Phá', 'iPad Pro M4 với chip M4 siêu mạnh mẽ, màn hình Liquid Retina XDR và thiết kế siêu mỏng, mở ra kỷ nguyên mới cho máy tính bảng.', 'ipad_pro_m4.jpg', '2024-01-10 14:00:00', '2024-07-15 16:34:40', 1, 1, 'ipad-pro-m4-suc-manh-dot-pha'),
(5, 'AirPods Pro 2: Âm Thanh Tuyệt Hảo', 'AirPods Pro 2 mang đến chất lượng âm thanh vượt trội với khả năng khử tiếng ồn chủ động, chế độ xuyên âm thích ứng và thời lượng pin dài hơn.', 'airpods_pro2.jpg', '2024-02-20 10:30:00', '2024-07-15 16:34:40', 1, 1, 'airpods-pro-2-am-thanh-tuyet-hao');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `order_code` varchar(50) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_date` timestamp NULL DEFAULT current_timestamp(),
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('Pending','Processing','Shipped','Delivered','Cancelled') DEFAULT 'Pending',
  `shipping_address` varchar(255) DEFAULT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `payment_method` enum('cod','bank','momo') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_code`, `user_id`, `order_date`, `total_amount`, `status`, `shipping_address`, `full_name`, `email`, `phone_number`, `notes`, `payment_method`) VALUES
(5, 'ORD20250720224054135', 7, '2025-07-20 15:40:54', 99999999.99, 'Shipped', 'Nhà giàu nhất Kim Bôi, Xã Tử Nê, Huyện Tân Lạc, Tỉnh Hoà Bình', 'Lã Tấn Lộc', 'hoangquangdat182005@gmail.com', '0127865412', '', 'cod'),
(6, 'ORD20250721182114721', 7, '2025-07-21 11:21:14', 27891000.00, 'Delivered', 'Nhà giàu nhất Kim Bôi, Xã Gia Phú, Huyện Gia Viễn, Tỉnh Ninh Bình', 'Lã Tấn Lộc', 'kiogit30@gmail.com', '0127865412', '', 'cod'),
(7, 'ORD20250721205734666', 7, '2025-07-21 13:57:34', 42282000.00, 'Delivered', 'Nhà giàu nhất Kim Bôi, Xã Thành Sơn, Huyện Mai Châu, Tỉnh Hoà Bình', 'Hoa Sơn Quý', 'latanloc24012005@gmail.com', '0127865412', '', 'cod'),
(8, 'ORD20250722004228333', 7, '2025-07-21 17:42:28', 77863000.00, 'Pending', 'Nhà giàu nhất Kim Bôi, Xã Tòng Đậu, Huyện Mai Châu, Tỉnh Hoà Bình', 'Lã Tấn Lộc', 'hoangquangdat182005@gmail.com', '0127865412', '', 'momo');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `variant_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `variant_id`, `quantity`, `price`) VALUES
(14, 6, 10228, 1, 27891000.00),
(15, 7, 10160, 1, 25191000.00),
(16, 7, 10207, 1, 17091000.00),
(17, 8, 10307, 1, 490000.00),
(18, 8, 10160, 1, 25191000.00),
(19, 8, 10156, 1, 25191000.00),
(20, 8, 10164, 1, 26991000.00);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `product_name` varchar(150) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `product_image` varchar(255) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `product_code` varchar(100) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `product_name`, `price`, `description`, `product_image`, `category_id`, `supplier_id`, `created_at`, `product_code`, `updated_at`, `status`) VALUES
(1001, 'iPhone 16 Pro Max', 34990000.00, 'iPhone 16 Pro Max là chiếc điện thoại mạnh mẽ nhất của Apple, sở hữu màn hình lớn nhất, hiệu năng đỉnh cao với chip A18 Pro và hệ thống camera tiên tiến nhất. Đây là lựa chọn hoàn hảo cho những người dùng chuyên nghiệp và những ai muốn trải nghiệm công nghệ di động tốt nhất.\r\nMàn hình LTPO Super Retina XDR 6.9 inch với công nghệ ProMotion và Always-On Display mang lại không gian hiển thị rộng lớn, mượt mà và sắc nét tuyệt đối. Độ sáng tối đa 2000 nits cùng công nghệ HDR10 và Dolby Vision cho phép bạn thưởng thức nội dung với chất lượng hình ảnh không thể tin được.\r\nHệ thống camera Pro Max được nâng cấp với camera chính 48MP, camera Ultra Wide 48MP và camera Telephoto 12MP (zoom quang học 5x). Khả năng chụp ảnh thiếu sáng vượt trội, quay video 4K Pro Res lên đến 120fps và tính năng Spatial Video mang đến khả năng sáng tạo không giới hạn.\r\niPhone 16 Pro Max hỗ trợ Wi-Fi 7 tiên tiến nhất và Bluetooth 5.3, đảm bảo tốc độ kết nối siêu nhanh và ổn định. Viên pin dung lượng lớn nhất trong dòng iPhone cùng công nghệ sạc nhanh và sạc MagSafe cung cấp thời lượng sử dụng bền bỉ cho cả ngày dài làm việc và giải trí.\r\nThiết bị còn được trang bị nút Tác vụ (Action Button) tùy chỉnh, Face ID, và các tính năng kết nối vệ tinh nâng cao, biến iPhone 16 Pro Max thành một công cụ không thể thiếu cho mọi nhu cầu.', 'assets/products/iphone/iphone16prm_titanden.png', 1, 1, '2025-07-21 11:17:35', 'IPHONE16PROMAX', '2025-07-21 11:17:55', 1),
(1002, 'iPhone 16 Pro', 28990000.00, 'iPhone 16 Pro là đỉnh cao của công nghệ di động, mang đến hiệu năng vượt trội với chip A18 Pro mạnh mẽ và hệ thống camera chuyên nghiệp. Thiết kế sang trọng với khung viền Titanium và màn hình ProMotion là những điểm nhấn ấn tượng của chiếc điện thoại này.\r\nMàn hình LTPO Super Retina XDR 6.3 inch với công nghệ ProMotion và Always-On Display mang lại trải nghiệm hình ảnh mượt mà, sống động và chi tiết. Độ sáng tối đa lên đến 2000 nits đảm bảo hiển thị rõ ràng ngay cả trong môi trường sáng nhất.\r\nHệ thống camera Pro được nâng cấp đáng kể với camera chính 48MP, camera Ultra Wide 48MP và camera Telephoto 12MP (zoom quang học 5x). Khả năng quay video 4K Pro Res và tính năng Spatial Video cho phép bạn ghi lại những thước phim chất lượng điện ảnh.\r\niPhone 16 Pro hỗ trợ Wi-Fi 7 và Bluetooth 5.3, mang lại tốc độ kết nối không dây nhanh chóng và ổn định. Dung lượng pin tối ưu cùng công nghệ sạc nhanh giúp bạn luôn sẵn sàng cho mọi tác vụ.\r\nNgoài ra, thiết bị còn có nút Tác vụ (Action Button) tùy chỉnh, Face ID, và khả năng kết nối vệ tinh để liên lạc trong trường hợp khẩn cấp. iPhone 16 Pro là lựa chọn hoàn hảo cho những người dùng đòi hỏi hiệu năng cao và khả năng chụp ảnh chuyên nghiệp.', 'assets/products/iphone/iphone16pro_titantunhien.png', 1, 1, '2025-07-21 11:17:35', 'IPHONE16PRO', '2025-07-21 11:18:51', 1),
(1003, 'iPhone 16 Plus', 25990000.00, 'iPhone 16 Plus mang đến trải nghiệm di động lớn hơn và mạnh mẽ hơn, được trang bị chip A18 tiên tiến của Apple. Với màn hình rộng rãi và thời lượng pin ấn tượng, đây là lựa chọn lý tưởng cho những ai yêu thích giải trí và làm việc trên một thiết bị lớn hơn.\r\nMàn hình Super Retina XDR 6.7 inch với công nghệ Dynamic Island mang lại không gian hiển thị rộng lớn, màu sắc chân thực và độ sáng cao, lý tưởng cho việc xem phim, chơi game và đa nhiệm. Tận hưởng hình ảnh sắc nét và rõ ràng trong mọi điều kiện ánh sáng.\r\nHệ thống camera kép được nâng cấp với camera chính 48MP và camera Ultra Wide 12MP cho phép bạn chụp ảnh và quay video 4K tuyệt đẹp. Chế độ chụp đêm và các tính năng chụp ảnh chuyên nghiệp giúp bạn ghi lại mọi khoảnh khắc với chất lượng cao nhất.\r\nKhả năng kết nối Wi-Fi 7 và Bluetooth 5.3 đảm bảo tốc độ truyền tải dữ liệu cực nhanh và kết nối ổn định. Viên pin dung lượng lớn cung cấp thời lượng sử dụng cả ngày dài, cùng với công nghệ sạc nhanh tiện lợi.\r\niPhone 16 Plus cũng tích hợp nút Tác vụ (Action Button) và tính năng Face ID, mang lại sự tiện lợi và bảo mật tối ưu. Đây là một thiết bị đa năng, hoàn hảo cho cả công việc và giải trí.', 'assets/products/iphone/iphone16plus_hong.png', 1, 1, '2025-07-21 11:17:35', 'IPHONE16PLUS', '2025-07-21 11:18:51', 1),
(1004, 'iPhone 16', 22990000.00, 'iPhone 16 là phiên bản mới nhất của Apple, tích hợp chip A18 mạnh mẽ, mang đến hiệu năng vượt trội cho mọi tác vụ từ làm việc đến giải trí. Với thiết kế sang trọng và bền bỉ, chiếc điện thoại này hứa hẹn sẽ nâng tầm trải nghiệm di động của bạn.\r\nMàn hình Super Retina XDR 6.1 inch sắc nét với độ phân giải cao và công nghệ Dynamic Island mang lại hình ảnh sống động, màu sắc chân thực. Độ sáng tối đa lên đến 2000 nits giúp hiển thị rõ ràng ngay cả dưới ánh nắng mặt trời gay gắt.\r\nHệ thống camera kép tiên tiến của iPhone 16 cho phép bạn chụp ảnh và quay video 4K chất lượng cao. Camera chính 48MP và camera Ultra Wide 12MP được cải tiến giúp ghi lại mọi khoảnh khắc với độ chi tiết và màu sắc ấn tượng. Camera trước 12MP hỗ trợ FaceTime và các cuộc gọi video chất lượng cao.\r\niPhone 16 tương thích với Wi-Fi 7 siêu tốc và Bluetooth 5.3, đảm bảo kết nối ổn định và nhanh chóng. Dung lượng pin lớn cùng công nghệ sạc nhanh giúp bạn thoải mái sử dụng suốt cả ngày dài mà không lo gián đoạn.\r\nNgoài ra, iPhone 16 còn tích hợp nút Tác vụ (Action Button) tùy chỉnh, cho phép bạn truy cập nhanh các tính năng yêu thích chỉ với một lần nhấn. Tính năng nhận diện khuôn mặt Face ID an toàn và tiện lợi, bảo vệ thông tin cá nhân của bạn.', 'assets/products/iphone/iphone16_trang.png', 1, 1, '2025-07-21 11:17:35', 'IPHONE16', '2025-07-21 11:18:51', 1),
(1005, 'iPhone 16e', 17990000.00, 'iPhone 16e là phiên bản \'essential\' của dòng iPhone 16, mang đến trải nghiệm Apple cốt lõi với mức giá phải chăng hơn. Được trang bị chip A17 Bionic (tiếp bước chip của thế hệ trước), thiết bị này vẫn đảm bảo hiệu năng mượt mà cho các tác vụ hàng ngày, từ lướt web, mạng xã hội đến chơi game nhẹ và xem phim.\r\nMàn hình Liquid Retina HD 6.1 inch của iPhone 16e cung cấp màu sắc sống động và độ rõ nét tuyệt vời cho mọi nội dung. Viền màn hình có thể sẽ dày hơn một chút so với các mẫu cao cấp, nhưng vẫn mang lại trải nghiệm xem thoải mái và trực quan.\r\nHệ thống camera đơn hoặc kép được tối ưu hóa với camera chính 12MP sẽ giúp bạn chụp ảnh và quay video chất lượng tốt trong nhiều điều kiện. Camera trước cũng được cải tiến để có những cuộc gọi video sắc nét và ảnh selfie đẹp.\r\niPhone 16e hỗ trợ kết nối 5G và Wi-Fi 6, đảm bảo bạn luôn có tốc độ truy cập internet nhanh chóng và đáng tin cậy. Dung lượng pin được cải thiện đáng kể so với các mẫu iPhone SE trước đây, cho phép bạn sử dụng thoải mái trong suốt cả ngày.\r\nThiết kế của iPhone 16e sẽ vẫn giữ nguyên sự chắc chắn và sang trọng đặc trưng của Apple, với khung nhôm và mặt lưng kính. Máy cũng sẽ tích hợp các tính năng bảo mật quen thuộc như Face ID hoặc Touch ID (nếu sử dụng nút nguồn tích hợp).', 'assets/products/iphone/iphone16e_trang.png', 1, 1, '2025-07-21 11:17:35', 'IPHONE16E', '2025-07-21 11:18:51', 1),
(1006, 'iPhone 15 Pro Max', 31990000.00, 'iPhone 15 Pro Max là chiếc điện thoại mạnh mẽ và tiên tiến nhất của Apple, sở hữu màn hình lớn nhất, hiệu năng đỉnh cao với chip A17 Pro và hệ thống camera chuyên nghiệp hàng đầu. Đây là lựa chọn tối ưu cho những người dùng yêu cầu khắt khe nhất về hiệu suất và khả năng chụp ảnh.\r\nMàn hình Super Retina XDR 6.7 inch với công nghệ ProMotion và Always-On Display mang lại trải nghiệm hiển thị tuyệt vời, mượt mà và cực kỳ sắc nét. Khung viền Titanium bền bỉ và nhẹ hơn đáng kể, tạo nên một thiết kế cao cấp và thoải mái khi cầm nắm.\r\nHệ thống camera Pro Max với camera chính 48MP, camera Ultra Wide 12MP và đặc biệt là camera Telephoto 12MP với khả năng zoom quang học 5x, cho phép bạn chụp những bức ảnh và quay video chất lượng đỉnh cao, kể cả từ xa. Chế độ Cinematic Mode, ProRes video và khả năng quay video không gian (Spatial Video) mở ra những khả năng sáng tạo mới.\r\niPhone 15 Pro Max được trang bị cổng USB-C với tốc độ USB 3, giúp truyền dữ liệu cực nhanh. Khả năng kết nối Wi-Fi 6E và 5G tiên tiến nhất đảm bảo tốc độ internet và độ trễ thấp tối đa cho mọi hoạt động trực tuyến.\r\nNút Tác vụ (Action Button) mới có thể tùy chỉnh, Face ID bảo mật, và thời lượng pin vượt trội giúp bạn thoải mái sử dụng cả ngày dài mà không cần lo lắng. iPhone 15 Pro Max là biểu tượng của sự kết hợp hoàn hảo giữa công nghệ, thiết kế và trải nghiệm người dùng.', 'assets/products/iphone/iphone15prm_titanden.png', 1, 1, '2025-07-21 11:17:35', 'IPHONE15PROMAX', '2025-07-21 11:18:51', 1),
(1007, 'iPhone 15 Pro', 25990000.00, 'iPhone 15 Pro là dòng sản phẩm cao cấp của Apple, được thiết kế cho những người dùng đòi hỏi hiệu năng tối đa và khả năng chụp ảnh chuyên nghiệp. Máy được trang bị chip A17 Pro siêu mạnh, mang đến hiệu năng đồ họa và xử lý chưa từng có trên điện thoại thông minh.\r\nMàn hình Super Retina XDR 6.1 inch với công nghệ ProMotion và Always-On Display mang lại trải nghiệm hiển thị mượt mà, sống động và cực kỳ chi tiết. Khung viền mỏng hơn và chất liệu Titanium cao cấp tạo nên sự sang trọng và bền bỉ.\r\nHệ thống camera Pro được nâng cấp vượt bậc với camera chính 48MP, camera Ultra Wide 12MP và camera Telephoto 12MP (zoom quang học 3x). Khả năng chụp ảnh thiếu sáng, quay video ProRes và chế độ Cinematic Mode cho phép bạn ghi lại những thước phim chất lượng điện ảnh.\r\niPhone 15 Pro tích hợp cổng USB-C tốc độ cao (USB 3), cho phép truyền dữ liệu cực nhanh. Khả năng kết nối Wi-Fi 6E và 5G tiên tiến đảm bảo tốc độ internet và độ trễ thấp tối đa.\r\nNút Tác vụ (Action Button) mới có thể tùy chỉnh giúp truy cập nhanh vào các tính năng yêu thích. Pin được tối ưu hóa cho thời lượng sử dụng cả ngày dài, cùng với công nghệ sạc nhanh và MagSafe tiện lợi. Đây là chiếc iPhone dành cho những người dùng chuyên nghiệp và sáng tạo.', 'assets/products/iphone/iphone15pro_titanden.png', 1, 1, '2025-07-21 11:17:35', 'IPHONE15PRO', '2025-07-21 11:18:51', 1),
(1008, 'iPhone 15 Plus', 22990000.00, 'iPhone 15 Plus mang đến trải nghiệm màn hình lớn hơn và thời lượng pin ấn tượng, lý tưởng cho những ai yêu thích không gian giải trí và làm việc rộng rãi. Máy được trang bị chip A16 Bionic mạnh mẽ, đảm bảo hiệu năng mượt mà cho mọi tác vụ.\r\nMàn hình Super Retina XDR 6.7 inch với công nghệ Dynamic Island mở rộng không gian hiển thị, mang lại hình ảnh sắc nét, sống động. Đây là lựa chọn hoàn hảo để xem phim, chơi game hay làm việc đa nhiệm.\r\nHệ thống camera kép với camera chính 48MP và camera Ultra Wide 12MP giúp bạn chụp ảnh và quay video chất lượng cao với độ chi tiết ấn tượng. Khả năng chụp ảnh chân dung được cải thiện và các tính năng thông minh giúp bạn dễ dàng có được những bức ảnh đẹp.\r\nVới cổng USB-C tiện lợi, iPhone 15 Plus giúp việc sạc và truyền dữ liệu trở nên dễ dàng hơn bao giờ hết. Khả năng kết nối 5G và Wi-Fi 6 siêu tốc đảm bảo bạn luôn được kết nối ổn định và nhanh chóng.\r\nThiết kế bền bỉ với mặt lưng kính màu sắc được xử lý đặc biệt và khung nhôm chắc chắn. Viên pin dung lượng lớn mang lại thời lượng sử dụng dài, giúp bạn thoải mái trải nghiệm suốt cả ngày dài mà không cần lo lắng về việc sạc pin.', 'assets/products/iphone/iphone15plus_hongnhat.png', 1, 1, '2025-07-21 11:17:35', 'IPHONE15PLUS', '2025-07-21 11:18:51', 1),
(1009, 'iPhone 15', 19990000.00, 'iPhone 15 là mẫu điện thoại mới nhất của Apple, kế thừa những tinh hoa thiết kế và công nghệ. Máy được trang bị chip A16 Bionic mạnh mẽ, mang đến hiệu năng vượt trội và khả năng xử lý đồ họa ấn tượng, đáp ứng mọi nhu cầu từ công việc đến giải trí.\r\nMàn hình Super Retina XDR 6.1 inch sắc nét với công nghệ Dynamic Island độc đáo, mang lại trải nghiệm tương tác trực quan và thú vị. Độ sáng tối đa cao giúp hiển thị rõ ràng ngay cả dưới ánh nắng mặt trời gay gắt.\r\nHệ thống camera kép được nâng cấp đáng kể với camera chính 48MP và camera Ultra Wide 12MP, cho phép bạn chụp ảnh chi tiết hơn và quay video 4K chất lượng cao. Chế độ chân dung thế hệ mới và các tính năng chụp ảnh thông minh khác giúp bạn ghi lại mọi khoảnh khắc một cách chuyên nghiệp.\r\niPhone 15 tích hợp cổng USB-C, mang lại sự tiện lợi và tương thích cao với nhiều thiết bị. Khả năng kết nối 5G siêu tốc và Wi-Fi 6 giúp bạn luôn duy trì kết nối ổn định và truyền tải dữ liệu nhanh chóng.\r\nVới thiết kế bền bỉ, mặt lưng kính màu sắc được xử lý bằng phương pháp mới và khung nhôm chuẩn hàng không vũ trụ, iPhone 15 không chỉ đẹp mà còn chắc chắn. Thời lượng pin được cải thiện đáng kể, cho phép bạn sử dụng thiết bị suốt cả ngày dài mà không lo hết pin.', 'assets/products/iphone/iphone15_xanhduongnhat.png', 1, 1, '2025-07-21 11:17:36', 'IPHONE15', '2025-07-21 11:18:51', 1),
(1010, 'iPhone 14 Pro Max', 27990000.00, 'iPhone 14 Pro Max là siêu phẩm cao cấp nhất của Apple với màn hình lớn 6.7 inch và công nghệ Dynamic Island hiện đại, mang đến trải nghiệm tương tác hoàn toàn mới.\r\nTrang bị màn hình Super Retina XDR với ProMotion 120Hz, thiết bị hiển thị hình ảnh mượt mà, sắc nét, ngay cả khi bạn cuộn hoặc chơi game tốc độ cao.\r\nCụm camera chuyên nghiệp với cảm biến chính 48MP, hỗ trợ chụp ảnh RAW, chế độ điện ảnh và quay video 4K giúp bạn sáng tạo như một nhiếp ảnh gia thực thụ.\r\nChip A16 Bionic siêu mạnh mẽ đảm bảo mọi thao tác đều mượt mà, đồng thời tiết kiệm điện năng, tối ưu hiệu suất sử dụng lâu dài.\r\nMáy có thiết kế thép không gỉ bền bỉ, mặt lưng kính nhám sang trọng và hỗ trợ sạc nhanh cùng các kết nối hiện đại như 5G, Wi-Fi 6, và Lightning.', 'assets/products/iphone/iphone14prm_vang.png', 1, 1, '2025-07-21 11:17:36', 'IPHONE14PROMAX', '2025-07-21 11:18:51', 1),
(1011, 'iPhone 14 Pro', 25990000.00, 'iPhone 14 Pro mang đến thiết kế đột phá với Dynamic Island – khu vực tương tác mới thông minh và đầy sáng tạo.\r\nMàn hình Super Retina XDR 6.1 inch kết hợp công nghệ ProMotion 120Hz cho trải nghiệm mượt mà, màu sắc rực rỡ và chi tiết cao.\r\nCamera chính 48MP hỗ trợ chụp ảnh ProRAW cùng chế độ ban đêm, điện ảnh, giúp bạn lưu giữ mọi khoảnh khắc sắc nét và sống động.\r\nBên trong là chip A16 Bionic mạnh mẽ, tối ưu hiệu năng và tiết kiệm pin, phù hợp cả chơi game, làm việc lẫn chỉnh sửa ảnh/video chuyên nghiệp.\r\nThiết kế với khung thép không gỉ, mặt kính nhám chống bám vân tay, đi kèm khả năng chống nước IP68 cao cấp.', 'assets/products/iphone/iphone14pro_vang.png', 1, 1, '2025-07-21 11:17:36', 'IPHONE14PRO', '2025-07-21 11:18:51', 1),
(1012, 'iPhone 14 Plus', 21990000.00, 'iPhone 14 Plus sở hữu màn hình lớn 6.7 inch cùng thời lượng pin vượt trội, lý tưởng cho người dùng yêu thích xem phim, chơi game hay làm việc cả ngày dài.\r\nTrang bị chip A15 Bionic mạnh mẽ với GPU 5 lõi, iPhone 14 Plus xử lý mượt mà mọi tác vụ từ đa nhiệm đến giải trí nặng như game 3D hay chỉnh sửa video.\r\nCamera kép 12MP với công nghệ Photonic Engine cải thiện khả năng chụp thiếu sáng, mang lại ảnh và video sắc nét, sống động trong mọi điều kiện ánh sáng.\r\nThiết kế nguyên khối với khung nhôm bền bỉ và mặt kính bóng bẩy. Mặt trước là kính Ceramic Shield tăng cường độ bền và chống va đập.\r\nHỗ trợ 5G, Wi-Fi 6 cùng cổng Lightning quen thuộc, mang đến trải nghiệm kết nối nhanh và ổn định trong mọi tình huống.', 'assets/products/iphone/iphone14plus_vang_256v128.png', 1, 1, '2025-07-21 11:17:36', 'IPHONE14PLUS', '2025-07-21 11:18:51', 1),
(1013, 'iPhone 14', 18990000.00, 'iPhone 14 mang đến thiết kế quen thuộc nhưng được cải tiến về hiệu năng và trải nghiệm camera, phù hợp với người dùng yêu thích sự ổn định và mượt mà trong sử dụng hằng ngày.\r\nMàn hình Super Retina XDR 6.1 inch hiển thị sắc nét với độ sáng cao, hỗ trợ hiển thị nội dung sống động, thích hợp cho giải trí và làm việc.\r\nCamera kép với cảm biến chính 12MP và camera Ultra Wide 12MP hỗ trợ chụp ảnh thiếu sáng tốt hơn, cùng chế độ Photonic Engine nâng cao chất lượng ảnh toàn diện.\r\nChip A15 Bionic với GPU 5 lõi mang lại hiệu năng mạnh mẽ, tiết kiệm pin, đồng thời hỗ trợ các tác vụ nặng và chơi game một cách mượt mà.\r\nThiết kế nguyên khối chắc chắn với mặt lưng kính cường lực và khung nhôm, tích hợp eSIM, 5G và Wi-Fi 6 cho kết nối tốc độ cao, cùng thời lượng pin đủ dùng cả ngày.', 'assets/products/iphone/iphone14_xanhduong_256v128.png', 1, 1, '2025-07-21 11:17:36', 'IPHONE14', '2025-07-21 11:18:51', 1),
(1014, 'iPhone 13 Pro Max', 27990000.00, 'iPhone 13 Pro Max là chiếc iPhone mạnh mẽ nhất năm 2021 với màn hình lớn 6.7 inch và pin cực trâu.\r\nTích hợp ProMotion 120Hz cho trải nghiệm cảm ứng mượt mà vượt trội, đặc biệt khi chơi game hay xem video tốc độ cao.\r\nCamera 3 ống kính hỗ trợ Zoom quang học 3x, quay video chuẩn điện ảnh với Cinematic Mode, chụp đêm, Deep Fusion.\r\nHiệu năng đầu bảng nhờ chip A15 Bionic, RAM 6GB và GPU 5 nhân – mọi tác vụ đều siêu mượt.\r\nPin lớn nhất trên iPhone, dùng cả ngày thoải mái, hỗ trợ sạc nhanh, sạc không dây và MagSafe hiện đại.', 'assets/products/iphone/iphone13prm_xam.png', 1, 1, '2025-07-21 11:17:36', 'IPHONE13PROMAX', '2025-07-21 11:18:51', 1),
(1015, 'iPhone 13 Pro', 24990000.00, 'iPhone 13 Pro mang đến trải nghiệm cao cấp với thiết kế sang trọng, hiệu năng vượt trội và cụm 3 camera chuyên nghiệp.\r\nMàn hình Super Retina XDR 6.1 inch với ProMotion 120Hz cho độ mượt mà tối đa khi cuộn và chơi game.\r\nTrang bị chip A15 Bionic với GPU 5 nhân, iPhone 13 Pro xử lý mượt mọi tác vụ nặng, chơi game đồ họa cao hay quay dựng video chuyên nghiệp.\r\nCamera 3 ống kính (Chính, Tele, Ultra Wide) hỗ trợ chụp đêm, xóa phông, quay video Dolby Vision, Cinematic Mode đầy ấn tượng.\r\niPhone 13 Pro sở hữu pin tốt hơn nhiều thế hệ trước, kết hợp cùng sạc nhanh, MagSafe và các công nghệ mới nhất của Apple.', 'assets/products/iphone/iphone13pro_xanhla.jpg', 1, 1, '2025-07-21 11:17:36', 'IPHONE13PRO', '2025-07-21 11:18:51', 1),
(1016, 'iPhone 13', 16990000.00, 'iPhone 13 mang đến hiệu năng mạnh mẽ nhờ chip A15 Bionic cùng thiết kế hiện đại và nhiều màu sắc trẻ trung. Đây là lựa chọn lý tưởng cho những ai muốn một chiếc iPhone mạnh mẽ nhưng vừa tay.\r\nMàn hình Super Retina XDR 6.1 inch cho hình ảnh sắc nét và sống động, lý tưởng để xem video, chơi game và sử dụng hàng ngày.\r\nCamera kép 12MP hỗ trợ chế độ chụp ảnh chân dung, Deep Fusion và Smart HDR 4 giúp bạn ghi lại mọi khoảnh khắc một cách rõ nét và chuyên nghiệp.\r\niPhone 13 sử dụng kết nối 5G, Wi-Fi 6 và hỗ trợ sạc không dây MagSafe, mang lại trải nghiệm hiện đại và tiện lợi.\r\nVới thời lượng pin cả ngày, thiết kế bền bỉ cùng iOS mượt mà, iPhone 13 là người bạn đồng hành đáng tin cậy trong công việc lẫn giải trí.', 'assets/products/iphone/iphone13_do.png', 1, 1, '2025-07-21 11:17:36', 'IPHONE13', '2025-07-21 11:18:51', 1),
(1017, 'iPhone 12 Pro Max', 24990000.00, 'iPhone 12 Pro Max mang đến trải nghiệm cao cấp với màn hình lớn 6.7 inch và thiết kế thép không gỉ sang trọng. Máy sở hữu hiệu năng mạnh mẽ cùng khả năng chụp ảnh vượt trội.\r\nTrang bị chip A14 Bionic tiên tiến, iPhone 12 Pro Max dễ dàng xử lý mọi tác vụ từ chơi game đến chỉnh sửa video 4K mượt mà.\r\nCụm 3 camera sau 12MP cùng cảm biến LiDAR hỗ trợ chụp ảnh chân dung trong điều kiện thiếu sáng và quay video Dolby Vision đỉnh cao.\r\nMàn hình Super Retina XDR cho hình ảnh sắc nét, màu sắc chân thực và độ sáng cao lên đến 1200 nits.\r\nKết nối 5G tốc độ cao, sạc không dây MagSafe tiện lợi cùng thời lượng pin được cải thiện giúp bạn yên tâm sử dụng cả ngày dài.', 'assets/products/iphone/iphone12prm_xanhduong.png', 1, 1, '2025-07-21 11:17:36', 'IPHONE12PROMAX', '2025-07-21 11:18:51', 1),
(1018, 'iPhone 12 Pro', 21990000.00, 'iPhone 12 Pro là lựa chọn hoàn hảo cho người dùng muốn trải nghiệm thiết kế cao cấp, hiệu năng mạnh mẽ với chip A14 Bionic và cụm camera chuyên nghiệp.\r\nMàn hình Super Retina XDR 6.1 inch với độ sáng cao, màu sắc chân thực, phù hợp cho việc giải trí và làm việc hiệu quả.\r\nHệ thống 3 camera sau: chính 12MP, tele 12MP, ultra wide 12MP, hỗ trợ chụp ảnh chân dung, zoom quang học và quay video chất lượng cao.\r\nThiết kế sang trọng với khung thép không gỉ và mặt kính Ceramic Shield chống trầy xước, độ bền vượt trội.\r\nHỗ trợ 5G, Wi-Fi 6, Face ID, sạc MagSafe tiện lợi và các tính năng bảo mật nâng cao.', 'assets/products/iphone/iphone12pro_xam.png', 1, 1, '2025-07-21 11:17:36', 'IPHONE12PRO', '2025-07-21 11:18:51', 1),
(1019, 'iPhone 12', 16990000.00, 'iPhone 12 là mẫu iPhone thế hệ mới với thiết kế mỏng nhẹ, hiệu năng mạnh mẽ và hệ thống camera chất lượng.\r\nMàn hình Super Retina XDR 6.1 inch mang đến hình ảnh sắc nét, màu sắc chân thực, phù hợp cho nhu cầu sử dụng hàng ngày.\r\nCamera kép 12MP hỗ trợ chụp ảnh xóa phông, chụp đêm và quay video 4K HDR.\r\nKhung nhôm nguyên khối, mặt kính Ceramic Shield giúp chống trầy xước và tăng độ bền cho máy.\r\nHỗ trợ 5G, Face ID, sạc MagSafe và nhiều tính năng thông minh khác.', 'assets/products/iphone/iphone12_tim.png', 1, 1, '2025-07-21 11:17:36', 'IPHONE12', '2025-07-21 11:18:51', 1),
(1020, 'iPad Air M3 (2024)', 16990000.00, 'iPad Air M3 trang bị chip Apple M3 mạnh mẽ, mang lại hiệu năng vượt trội cho mọi tác vụ từ học tập đến làm việc chuyên nghiệp.\r\nMàn hình Liquid Retina 10.9 inch với độ phân giải cao, hỗ trợ công nghệ True Tone và Wide Color (P3) cho màu sắc sống động.\r\nThiết kế mỏng nhẹ với viền màn hình mỏng, camera sau 12MP và camera trước Ultra Wide 12MP hỗ trợ Center Stage.\r\nHỗ trợ bút Apple Pencil 2 và bàn phím Magic Keyboard, biến iPad Air M3 thành công cụ sáng tạo và năng suất hiệu quả.\r\nKết nối 5G, Wi-Fi 6E, bảo mật Face ID và thời lượng pin dài lên đến 10 giờ.', 'assets/products/ipad/ipad_AirM3_Tim.png', 3, 1, '2025-07-21 11:17:36', 'IPADAIRM32024', '2025-07-21 15:55:57', 1),
(1021, 'iPad 10 (A16 Bionic)', 12990000.00, 'iPad 10 sử dụng chip A16 Bionic, cho hiệu năng mạnh mẽ, đáp ứng mọi nhu cầu học tập và giải trí.\r\nMàn hình Retina 10.9 inch sắc nét, màu sắc trung thực với True Tone.\r\nCamera sau 12MP và camera trước 12MP Ultra Wide hỗ trợ Center Stage giúp video call sống động.\r\nThiết kế mỏng nhẹ, hỗ trợ Apple Pencil 1 và bàn phím ngoài Smart Keyboard Folio.\r\nHỗ trợ kết nối 5G và Wi-Fi 6, thời lượng pin lên đến 10 giờ sử dụng.', 'assets/products/ipad/ipad_10_Hong.png', 3, 1, '2025-07-21 11:17:36', 'IPAD10A16BIONIC', '2025-07-21 15:54:44', 1),
(1022, 'iPad Pro M4 (2024)', 30990000.00, 'iPad Pro M4 là dòng iPad cao cấp nhất với chip Apple M4 mới, hiệu suất vượt trội và tiết kiệm năng lượng.\r\nMàn hình Ultra Retina XDR 13 inch (hoặc 11 inch) sử dụng công nghệ OLED kép mang đến trải nghiệm hình ảnh tuyệt đẹp.\r\nThiết kế mỏng nhất từ trước đến nay của Apple, thân nhôm bền nhẹ.\r\nHỗ trợ Apple Pencil Pro và Magic Keyboard mới, mở ra trải nghiệm như laptop thực thụ.\r\nKết nối Wi-Fi 6E và 5G, thời lượng pin cả ngày với công nghệ sạc nhanh.', 'assets/products/ipad/ipad_proM4_bac.png', 3, 1, '2025-07-21 11:17:36', 'IPADPROM42024', '2025-07-21 15:54:44', 1),
(1023, 'iPad Air M2 (2024)', 16990000.00, 'iPad Air M2 mang đến hiệu suất vượt trội nhờ chip Apple M2, phục vụ tốt nhu cầu học tập, làm việc và sáng tạo.\r\nMàn hình Liquid Retina 11 inch rực rỡ với công nghệ True Tone và P3 Color.\r\nCamera sau 12MP và camera trước 12MP hỗ trợ Center Stage.\r\nHỗ trợ Apple Pencil 2 và Magic Keyboard giúp nâng cao trải nghiệm làm việc.\r\nKết nối Wi-Fi 6E và 5G giúp truy cập internet siêu nhanh.', 'assets/products/ipad/ipad_Air6M2_TrangStarlight.png', 3, 1, '2025-07-21 11:17:36', 'IPADAIRM22024', '2025-07-21 15:55:57', 1),
(1024, 'iPad Mini (Gen 6)', 12990000.00, 'iPad Mini 6 nhỏ gọn nhưng mạnh mẽ, với chip A15 Bionic, lý tưởng cho công việc di động và giải trí.\r\nMàn hình Liquid Retina 8.3 inch sắc nét với True Tone và P3 Wide Color.\r\nCamera sau và trước đều 12MP, hỗ trợ Center Stage khi gọi video.\r\nHỗ trợ Apple Pencil 2 giúp ghi chú và vẽ dễ dàng.\r\nCổng USB-C và hỗ trợ Wi-Fi 6, 5G giúp kết nối nhanh chóng.', 'assets/products/ipad/ipad_mini_trangstarlight.png', 3, 1, '2025-07-21 11:17:36', 'IPADMINIGEN6', '2025-07-21 15:54:44', 1),
(1025, 'iPad 9 (2021)', 8990000.00, 'iPad 9 là lựa chọn phổ thông phù hợp cho học sinh và người dùng cơ bản.\r\nSử dụng chip A13 Bionic, hiệu năng ổn định trong học tập, xem phim và lướt web.\r\nMàn hình Retina 10.2 inch sắc nét, hỗ trợ True Tone.\r\nCamera trước 12MP Ultra Wide với Center Stage, camera sau 8MP.\r\nHỗ trợ Apple Pencil 1 và Smart Keyboard thông qua cổng Lightning.', 'assets/products/ipad/ipad_9_Bac.png', 3, 1, '2025-07-21 11:17:36', 'IPAD92021', '2025-07-21 15:54:44', 1),
(1026, 'iPad 10 (A14 Bionic)', 10990000.00, 'iPad 10 phiên bản dùng chip A14 Bionic mang đến hiệu năng ổn định cho học tập và giải trí.\r\nThiết kế mới với màn hình Liquid Retina 10.9 inch viền mỏng, hỗ trợ True Tone và độ sáng cao.\r\nCamera trước 12MP Ultra Wide đặt ngang giúp gọi video thuận tiện, hỗ trợ Center Stage.\r\nTương thích với Apple Pencil (Gen 1) qua adapter USB-C và bàn phím Smart Keyboard Folio.\r\nHỗ trợ Wi-Fi 6 và tùy chọn 5G giúp kết nối tốc độ cao mọi lúc mọi nơi.', 'assets/products/ipad/ipad_10_Bac.png', 3, 1, '2025-07-21 11:17:36', 'IPAD10A14BIONIC', '2025-07-21 15:54:44', 1),
(1027, 'MacBook Air', 27990000.00, 'MacBook Air với chip Apple mang lại hiệu năng vượt trội cho cả công việc và giải trí.\r\nThiết kế siêu mỏng nhẹ, màn hình Liquid Retina 13.6 inch sắc nét, pin sử dụng đến 18 giờ.\r\nTích hợp webcam 1080p, Touch ID, và hệ thống loa 4 loa sống động.\r\nHỗ trợ Wi-Fi 6E, Thunderbolt 3, và sạc nhanh qua MagSafe 3.', 'assets/products/macbook/MacbookAir_Vang.png', 2, 1, '2025-07-21 11:17:36', 'MACBOOKAIR', '2025-07-21 15:55:30', 1),
(1028, 'MacBook Pro', 46990000.00, 'MacBook Pro là cỗ máy mạnh mẽ với chip Apple , tối ưu cho đồ họa, lập trình và xử lý video.\r\nMàn hình Liquid Retina XDR 14 inch, độ sáng cao và hỗ trợ ProMotion 120Hz.\r\nHệ thống tản nhiệt hiệu quả, thời lượng pin lên đến 17 giờ.\r\nNhiều cổng kết nối chuyên nghiệp: HDMI, SDXC, MagSafe, 3 cổng Thunderbolt 4.', 'assets/products/macbook/MacbookPro_Bac.png', 2, 1, '2025-07-21 11:17:36', 'MACBOOKPRO', '2025-07-21 15:54:04', 1),
(1029, 'iMac 24 inch', 35990000.00, 'iMac thiết kế siêu mỏng, chip Apple mang lại hiệu suất đáng kinh ngạc.\r\nMàn hình Retina 4.5K 24 inch sống động, hỗ trợ 1 tỷ màu.\r\nCamera FaceTime HD 1080p và hệ thống 6 loa cao cấp.\r\nTích hợp Touch ID trên bàn phím và nhiều lựa chọn màu sắc bắt mắt.', 'assets/products/macbook/iMac24_M3_Hong.png', 2, 1, '2025-07-21 11:17:36', 'IMAC24INCH', '2025-07-21 15:54:04', 1),
(1030, 'Mac mini', 14990000.00, 'Mac mini nhỏ gọn nhưng mạnh mẽ với chip Apple, phù hợp làm việc đa nhiệm và giải trí.\r\nThiết kế tối giản, dễ dàng đặt ở bất kỳ không gian nào.\r\nTrang bị nhiều cổng kết nối hiện đại: Thunderbolt 4, HDMI, USB-A, Ethernet.\r\nHỗ trợ RAM lên đến 16GB và lưu trữ SSD nhanh chóng.', 'assets/products/macbook/Macmini_M4_Bac.png', 2, 1, '2025-07-21 11:17:36', 'MACMINI', '2025-07-21 15:54:04', 1),
(1031, 'Mac Studio M2 Ultra', 87990000.00, 'Mac Studio M2 Ultra mạnh mẽ, tối ưu cho công việc sáng tạo chuyên sâu như biên tập video, thiết kế đồ họa.\r\nTrang bị chip Apple M2 Ultra với hiệu năng vượt trội.\r\nHỗ trợ RAM lên tới 128GB, lưu trữ SSD tốc độ cao.\r\nNhiều cổng kết nối đa dạng, thích hợp cho môi trường làm việc chuyên nghiệp.', 'assets/products/macbook/MacStudio_M2Ultra.png', 2, 1, '2025-07-21 11:17:36', 'MACSTUDIOM2ULTRA', '2025-07-21 15:54:04', 1),
(1032, 'Apple Watch Series 10', 10990000.00, 'Apple Watch Series 10 với thiết kế mỏng nhẹ, viền màn hình siêu mỏng.\r\nMàn hình Always-On Retina LTPO OLED sắc nét, hỗ trợ cảm biến sức khỏe nâng cao.\r\nChip S10 siêu mạnh, tối ưu hiệu năng và tiết kiệm pin.\r\nHỗ trợ đo nhịp tim, ECG, theo dõi oxy trong máu và nhiều chế độ luyện tập.', 'assets/products/watch/applewatch_series10_vanghong.png', 4, 1, '2025-07-21 11:17:36', 'APPLEWATCHSERIES10', '2025-07-21 15:55:05', 1),
(1033, 'Apple Watch Ultra 2', 30990000.00, 'Apple Watch Ultra 2 dành cho người dùng yêu thích thể thao mạo hiểm, với thiết kế bền bỉ, chịu va đập tốt.\r\nMàn hình lớn 49mm Retina LTPO OLED, độ sáng cực cao, dễ dàng nhìn ngoài trời nắng.\r\nChip S10 Ultra cải tiến, tối ưu cho các hoạt động ngoài trời và thể thao.\r\nTích hợp GPS đa băng tần, đo độ sâu, la bàn nâng cao và các cảm biến sinh học tiên tiến.', 'assets/products/watch/applewatch_ultra2_dayocean.png', 4, 1, '2025-07-21 11:17:36', 'APPLEWATCHULTRA2', '2025-07-21 15:55:05', 1),
(1034, 'Apple Watch Series 9', 9990000.00, 'Apple Watch Series 9 cải tiến với chip S9 mạnh mẽ, thời lượng pin lâu hơn.\r\nMàn hình Always-On Retina với độ sáng được nâng cấp.\r\nNhiều chế độ luyện tập và cảm biến sức khỏe tích hợp.\r\nHỗ trợ đo điện tâm đồ (ECG), nhịp tim và oxy trong máu.', 'assets/products/watch/applewatch_series9_do.png', 4, 1, '2025-07-21 11:17:36', 'APPLEWATCHSERIES9', '2025-07-21 15:55:05', 1),
(1035, 'Apple Watch SE 2', 6990000.00, 'Apple Watch SE 2 phù hợp cho người mới dùng, với nhiều tính năng cơ bản và giá cả phải chăng.\r\nChip S8 cho hiệu năng mượt mà, hỗ trợ đa dạng các chế độ luyện tập.\r\nMàn hình Retina lớn, cảm biến nhịp tim và theo dõi sức khỏe cơ bản.\r\nHỗ trợ chống nước 50 mét và nhiều màu sắc trẻ trung.', 'assets/products/watch/applewatch_se2_xanhden.png', 4, 1, '2025-07-21 11:17:36', 'APPLEWATCHSE2', '2025-07-21 15:55:05', 1),
(1036, 'EarPods Lightning', 390000.00, 'Tai nghe EarPods với đầu kết nối Lightning tương thích với iPhone và iPad.\r\nThiết kế gọn nhẹ, vừa vặn tai, mang lại âm thanh rõ ràng và chân thực.\r\nTích hợp micro và điều khiển từ xa để điều chỉnh âm lượng, phát/tạm dừng nhạc.', 'assets/products/tainghe/Earpods_lightning.png', 5, 1, '2025-07-21 11:17:36', 'EARPODSLIGHTNING', '2025-07-21 16:03:48', 1),
(1037, 'EarPods USB-C', 450000.00, 'Tai nghe EarPods với đầu kết nối USB-C dành cho các thiết bị Apple và Android hỗ trợ USB-C.\r\nThiết kế tương tự EarPods Lightning, âm thanh trong trẻo, độ bền cao.\r\nĐiều khiển âm lượng và micro tích hợp.', 'assets/products/tainghe/earpods_usb_c.png', 5, 1, '2025-07-21 11:17:36', 'EARPODSUSBC', '2025-07-21 16:03:52', 1),
(1038, 'AirPods 3', 3990000.00, 'AirPods 3 với thiết kế tai nghe mở, vừa vặn thoải mái cho nhiều kích cỡ tai.\r\nHỗ trợ âm thanh không gian Spatial Audio, Adaptive EQ và cảm biến lực.\r\nThời lượng pin lên đến 6 giờ nghe liên tục, hộp sạc MagSafe hỗ trợ sạc không dây.', 'assets/products/tainghe/Airpod3_lightning.png', 5, 1, '2025-07-21 11:17:36', 'AIRPODS3', '2025-07-21 15:55:14', 1),
(1039, 'AirPods Pro', 6990000.00, 'AirPods Pro với tính năng chống ồn chủ động (ANC) vượt trội.\r\nThiết kế in-ear với 3 kích cỡ đệm tai silicon giúp cách âm tốt hơn.\r\nHỗ trợ Spatial Audio, Adaptive EQ và chế độ Transparency.\r\nThời lượng pin đến 4.5 giờ nghe, hộp sạc hỗ trợ sạc không dây MagSafe.', 'assets/products/tainghe/Airpods_pro_usb_c.png', 5, 1, '2025-07-21 11:17:36', 'AIRPODSPRO', '2025-07-21 15:55:14', 1),
(1040, 'AirPods Max', 15990000.00, 'AirPods Max là tai nghe chụp đầu cao cấp với âm thanh Hi-Fi chất lượng cao.\r\nThiết kế khung nhôm và đệm tai memory foam sang trọng, thoải mái khi đeo lâu.\r\nTích hợp công nghệ chống ồn chủ động ANC và Transparency mode.\r\nThời lượng pin lên đến 20 giờ nghe liên tục, hỗ trợ sạc nhanh.', 'assets/products/tainghe/AirpodsMax_Hong.png', 5, 1, '2025-07-21 11:17:36', 'AIRPODSMAX', '2025-07-21 15:55:14', 1),
(1041, 'AirPods 4', 4990000.00, 'AirPods 4 kế thừa thiết kế nhỏ gọn, cải tiến khả năng chống ồn và âm thanh Spatial Audio sống động.\r\nTích hợp chip Apple H2 mới nhất giúp kết nối nhanh và ổn định hơn.\r\nThời lượng pin cải thiện lên đến 8 giờ nghe liên tục, hỗ trợ sạc không dây MagSafe.\r\nTính năng nâng cao như Adaptive EQ, Transparency Mode và cảm biến lực nhạy bén.', 'assets/products/tainghe/Airpods4_chongon.png', 5, 1, '2025-07-21 11:17:36', 'AIRPODS4', '2025-07-21 15:55:14', 1),
(1042, 'Adapter sạc sbC', 590000.00, 'Adapter sạc USB-C với công suất cao, tương thích với nhiều thiết bị Apple như iPhone, iPad và MacBook.\r\nThiết kế nhỏ gọn, an toàn với công nghệ sạc nhanh và bảo vệ quá tải.', 'assets/products/phukien/Adapter_sacusbC.png', 6, 1, '2025-07-21 11:17:36', '', '2025-07-21 15:57:47', 1),
(1043, 'Airtag', 790000.00, 'Thiết bị theo dõi thông minh từ Apple, sử dụng với ứng dụng Find My để định vị đồ vật.\r\nTích hợp chip U1 Ultra Wideband cho độ chính xác cao.', 'assets/products/phukien/Airtag.png', 6, 1, '2025-07-21 11:17:36', 'AIRTAG', '2025-07-21 15:55:37', 1),
(1044, 'Apple Pencil Pro', 3290000.00, 'Bút stylus cao cấp cho iPad, hỗ trợ các tính năng chuyên nghiệp như nghiêng và cảm ứng lực.\r\nTích hợp sạc không dây và cảm biến áp suất mới.', 'assets/products/phukien/apple_pencilpro.png', 6, 1, '2025-07-21 11:17:36', 'APPLEPENCILPRO', '2025-07-21 16:02:48', 1),
(1045, 'Bàn di chuột', 490000.00, 'Dây đeo chuột không dây, thiết kế tiện lợi cho người dùng máy tính.\r\nTăng cường độ chính xác và thoải mái khi sử dụng.', 'assets/products/phukien/bandichuot.png', 6, 1, '2025-07-21 11:17:36', 'BANDICHUOT', '2025-07-21 16:03:18', 1),
(1046, 'Bao da iPad', 990000.00, 'Ốp lưng bảo vệ dành riêng cho iPad, làm từ chất liệu cao cấp.\r\nThiết kế mỏng nhẹ, hỗ trợ sạc không dây.', 'assets/products/phukien/baodaIpad.png', 6, 1, '2025-07-21 11:17:36', 'BAODAIPAD', '2025-07-21 16:02:10', 1),
(1047, 'Cáp Type-C', 290000.00, 'Cáp sạc và truyền dữ liệu Type-C, tương thích với nhiều thiết bị.\r\nHỗ trợ sạc nhanh và truyền dữ liệu tốc độ cao.', 'assets/products/phukien/cap_typeC.png', 6, 1, '2025-07-21 11:17:36', 'CAPTYPEC', '2025-07-21 15:57:47', 1),
(1048, 'Cáp sạc Type-C', 690000.00, 'Bao da sạc không dây Type-C, tiện lợi cho việc bảo vệ và sạc.\r\nThiết kế chắc chắn, hỗ trợ sạc nhanh.', 'assets/products/phukien/capsac_typeC.png', 6, 1, '2025-07-21 11:17:36', 'CAPSACTYPEC', '2025-07-21 15:58:05', 1),
(1049, 'Chuot Laptop', 390000.00, 'Chuột không dây dành cho laptop, thiết kế nhỏ gọn và tiện dụng.\r\nPhù hợp cho công việc và giải trí.', 'assets/products/phukien/chuotlaptop.png', 6, 1, '2025-07-21 11:17:36', 'CHUOTLAPTOP', '2025-07-21 16:01:17', 1),
(1050, 'Cường lực iPad Air', 290000.00, 'Miếng dán cường lực dành cho iPad Air, bảo vệ màn hình khỏi trầy xước.\r\nĐộ trong suốt cao, chống vân tay hiệu quả.', 'assets/products/phukien/cuongluc_iPadAir.png', 6, 1, '2025-07-21 11:17:36', 'CUONGLUCIPADAIR', '2025-07-21 15:59:28', 1),
(1051, 'Cường lực iPad Pro', 350000.00, 'Miếng dán cường lực dành cho iPad Pro, bảo vệ màn hình khỏi trầy xước và va đập.\r\nĐộ trong suốt cao, chống vân tay và bám bẩn hiệu quả.', 'assets/products/phukien/cuongluc_iPadPro.png', 6, 1, '2025-07-21 11:17:36', 'CUONGLUCIPADPRO', '2025-07-21 15:59:37', 1),
(1052, 'Cường lực Apple Watch', 200000.00, 'Miếng dán cường lực dành cho Apple Watch, bảo vệ màn hình khỏi trầy xước.\r\nThiết kế mỏng nhẹ, không ảnh hưởng đến cảm ứng.', 'assets/products/phukien/cuonglucAppleWatch.png', 6, 1, '2025-07-21 11:17:36', 'CUONGLUCAPPLEWATCH', '2025-07-21 15:59:45', 1),
(1053, 'Cường lực iPhone', 300000.00, 'Miếng dán cường lực dành cho iPhone, bảo vệ màn hình khỏi trầy xước và va đập mạnh.\r\nCông nghệ chống bám vân tay và độ trong suốt cao.', 'assets/products/phukien/cuonglucIphone.png', 6, 1, '2025-07-21 11:17:36', 'CUONGLUCIPHONE', '2025-07-21 15:59:51', 1),
(1054, 'Cường lực Macbook', 500000.00, 'Miếng dán cường lực dành cho MacBook, bảo vệ màn hình khỏi trầy xước.\r\nĐộ trong suốt cao, dễ dàng lắp đặt.', 'assets/products/phukien/cuonglucMac.png', 6, 1, '2025-07-21 11:17:36', 'CUONGLUCMAC', '2025-07-21 16:00:00', 1),
(1055, 'Dây cao su Apple Watch', 790000.00, 'Dây đeo cao su chính hãng cho Apple Watch, mềm mại và bền bỉ.\r\nThiết kế thể thao, phù hợp cho mọi hoạt động.', 'assets/products/phukien/daycaosuAppleWatch.png', 6, 1, '2025-07-21 11:17:36', 'DAYCAOSUAPPLEWATCH', '2025-07-21 16:01:17', 1),
(1056, 'Dây nilon Apple Watch', 890000.00, 'Dây đeo nylon cho Apple Watch, nhẹ và thoáng khí.\r\nThiết kế thời trang, dễ dàng thay đổi.', 'assets/products/phukien/dayNylonAppleWatch.png', 6, 1, '2025-07-21 11:17:36', 'DAYNYLONAPPLEWATCH', '2025-07-21 16:01:17', 1),
(1057, 'Dây silicone Aggi Apple Watch', 690000.00, 'Dây đeo silicone cao cấp cho Apple Watch, chống thấm nước.\r\nThiết kế trẻ trung, phù hợp với mọi phong cách.', 'assets/products/phukien/daysiliconeAppleWatch.png', 6, 1, '2025-07-21 11:17:37', 'DAYSILICONEAGGIAPPLEWATCH', '2025-07-21 16:01:17', 1),
(1058, 'Dây thép không gỉ Apple Watch', 1490000.00, 'Dây đeo thép không gỉ cho Apple Watch, sang trọng và bền bỉ.\r\nPhù hợp với phong cách chuyên nghiệp.', 'assets/products/phukien/daythepkhonggiAppleWatch.png', 6, 1, '2025-07-21 11:17:37', 'DAYTHEPHKHONGGGIAPPLEWATCH', '2025-07-21 16:01:17', 1),
(1059, 'Đế sạc không dây Type-C', 990000.00, 'Đế sạc không dây Type-C, hỗ trợ sạc nhanh cho nhiều thiết bị.\r\nThiết kế tối giản, dễ dàng sử dụng.', 'assets/products/phukien/desackhongday_TypeC.png', 6, 1, '2025-07-21 11:17:37', 'DESACHKHONGDAYTYPEC', '2025-07-21 15:58:20', 1),
(1060, 'Giado Laptop', 490000.00, 'Giá đỡ laptop, giúp điều chỉnh góc nhìn và tản nhiệt hiệu quả.\r\nThiết kế gấp gọn, dễ dàng mang theo.', 'assets/products/phukien/GiadoLaptop.png', 6, 1, '2025-07-21 11:17:37', 'GIADOLAPTOP', '2025-07-21 16:01:17', 1),
(1061, 'Magic Keyboard', 2990000.00, 'Bàn phím Magic Keyboard từ Apple, thiết kế mỏng nhẹ và sang trọng.\r\nHỗ trợ kết nối không dây, tích hợp cảm ứng đa điểm.', 'assets/products/phukien/MagicKeyboard.png', 6, 1, '2025-07-21 11:17:37', 'MAGICKEYBOARD', '2025-07-21 16:02:55', 1),
(1062, 'Ốp Apple Watch', 200000.00, 'Ốp bảo vệ dành cho Apple Watch, chống va đập và trầy xước.\r\nThiết kế trong suốt, không che khuất vẻ đẹp của đồng hồ.', 'assets/products/phukien/OpAppleWatch.png', 6, 1, '2025-07-21 11:17:37', 'OPAPPLEWATCH', '2025-07-21 16:01:17', 1),
(1063, 'Thiết bị cầm tay chống rung', 2490000.00, 'Thiết bị cầm tay chống rung, hỗ trợ quay video mượt mà.\r\nTương thích với iPhone và các thiết bị khác.', 'assets/products/phukien/thietbicamtaychongrung.png', 6, 1, '2025-07-21 11:17:37', 'THIETBICAMTAYCHONGRUNG', '2025-07-21 16:03:12', 1),
(1064, 'Thu âm', 1990000.00, 'Thiết bị thu âm chuyên nghiệp, hỗ trợ ghi âm chất lượng cao.\r\nTương thích với iPhone, MacBook và các thiết bị khác.', 'assets/products/phukien/thuam.png', 6, 1, '2025-07-21 11:17:37', 'THUAM', '2025-07-21 16:03:05', 1);

-- --------------------------------------------------------

--
-- Table structure for table `product_variants`
--

CREATE TABLE `product_variants` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `variant_code` varchar(100) DEFAULT NULL,
  `variant_price` decimal(10,2) NOT NULL,
  `stock_quantity` int(11) DEFAULT 0,
  `variant_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_variants`
--

INSERT INTO `product_variants` (`id`, `product_id`, `variant_code`, `variant_price`, `stock_quantity`, `variant_image`, `created_at`, `updated_at`, `status`) VALUES
(10001, 1001, '1001-256GB-TITANTNHIN', 34990000.00, 20, 'assets/products/iphone/iphone16prm_titantunhien.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10002, 1001, '1001-256GB-TITANEN', 34990000.00, 20, 'assets/products/iphone/iphone16prm_titanden.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10003, 1001, '1001-256GB-TITANTRNG', 34990000.00, 20, 'assets/products/iphone/iphone16prm_titantrang.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10004, 1001, '1001-256GB-TITANSAMC', 34990000.00, 20, 'assets/products/iphone/iphone16prm_titansamac.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10005, 1001, '1001-512GB-TITANTNHIN', 38990000.00, 20, 'assets/products/iphone/iphone16prm_titantunhien.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10006, 1001, '1001-512GB-TITANEN', 38990000.00, 20, 'assets/products/iphone/iphone16prm_titanden.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10007, 1001, '1001-512GB-TITANTRNG', 38990000.00, 20, 'assets/products/iphone/iphone16prm_titantrang.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10008, 1001, '1001-512GB-TITANSAMC', 38990000.00, 20, 'assets/products/iphone/iphone16prm_titansamac.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10009, 1001, '1001-1TB-TITANTNHIN', 42990000.00, 20, 'assets/products/iphone/iphone16prm_titantunhien.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10010, 1001, '1001-1TB-TITANEN', 42990000.00, 20, 'assets/products/iphone/iphone16prm_titanden.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10011, 1001, '1001-1TB-TITANTRNG', 42990000.00, 20, 'assets/products/iphone/iphone16prm_titantrang.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10012, 1001, '1001-1TB-TITANSAMC', 42990000.00, 20, 'assets/products/iphone/iphone16prm_titansamac.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10013, 1002, '1002-128GB-TITANTNHIN', 28990000.00, 20, 'assets/products/iphone/iphone16pro_titantunhien.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10014, 1002, '1002-128GB-TITANEN', 28990000.00, 20, 'assets/products/iphone/iphone16pro_titanden.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10015, 1002, '1002-128GB-TITANTRNG', 28990000.00, 20, 'assets/products/iphone/iphone16pro_titantrang.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10016, 1002, '1002-128GB-TITANSAMC', 28990000.00, 20, 'assets/products/iphone/iphone16pro_titansamac.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10017, 1002, '1002-256GB-TITANTNHIN', 31990000.00, 20, 'assets/products/iphone/iphone16pro_titantunhien.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10018, 1002, '1002-256GB-TITANEN', 31990000.00, 20, 'assets/products/iphone/iphone16pro_titanden.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10019, 1002, '1002-256GB-TITANTRNG', 31990000.00, 20, 'assets/products/iphone/iphone16pro_titantrang.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10020, 1002, '1002-256GB-TITANSAMC', 31990000.00, 20, 'assets/products/iphone/iphone16pro_titansamac.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10021, 1002, '1002-512GB-TITANTNHIN', 35990000.00, 20, 'assets/products/iphone/iphone16pro_titantunhien.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10022, 1002, '1002-512GB-TITANEN', 35990000.00, 20, 'assets/products/iphone/iphone16pro_titanden.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10023, 1002, '1002-512GB-TITANTRNG', 35990000.00, 20, 'assets/products/iphone/iphone16pro_titantrang.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10024, 1002, '1002-512GB-TITANSAMC', 35990000.00, 20, 'assets/products/iphone/iphone16pro_titansamac.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10025, 1002, '1002-1TB-TITANTNHIN', 39990000.00, 20, 'assets/products/iphone/iphone16pro_titantunhien.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10026, 1002, '1002-1TB-TITANEN', 39990000.00, 20, 'assets/products/iphone/iphone16pro_titanden.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10027, 1002, '1002-1TB-TITANTRNG', 39990000.00, 20, 'assets/products/iphone/iphone16pro_titantrang.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10028, 1002, '1002-1TB-TITANSAMC', 39990000.00, 20, 'assets/products/iphone/iphone16pro_titansamac.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10029, 1003, '1003-128GB-EN', 25990000.00, 20, 'assets/products/iphone/iphone16plus_den.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10030, 1003, '1003-128GB-TRNG', 25990000.00, 20, 'assets/products/iphone/iphone16plus_trang.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10031, 1003, '1003-128GB-HNG', 25990000.00, 20, 'assets/products/iphone/iphone16plus_hong.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10032, 1003, '1003-128GB-XANHLC', 25990000.00, 20, 'assets/products/iphone/iphone16plus_xanhmongket.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10033, 1003, '1003-128GB-XANHLAM', 25990000.00, 20, 'assets/products/iphone/iphone16plus_xanhluuly.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10034, 1003, '1003-256GB-EN', 28990000.00, 20, 'assets/products/iphone/iphone16plus_den.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10035, 1003, '1003-256GB-TRNG', 28990000.00, 20, 'assets/products/iphone/iphone16plus_trang.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10036, 1003, '1003-256GB-HNG', 28990000.00, 20, 'assets/products/iphone/iphone16plus_hong.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10037, 1003, '1003-256GB-XANHLC', 28990000.00, 20, 'assets/products/iphone/iphone16plus_xanhmongket.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10038, 1003, '1003-256GB-XANHLAM', 28990000.00, 20, 'assets/products/iphone/iphone16plus_xanhluuly.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10039, 1003, '1003-512GB-EN', 32990000.00, 20, 'assets/products/iphone/iphone16plus_den.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10040, 1003, '1003-512GB-TRNG', 32990000.00, 20, 'assets/products/iphone/iphone16plus_trang.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10041, 1003, '1003-512GB-HNG', 32990000.00, 20, 'assets/products/iphone/iphone16plus_hong.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10042, 1003, '1003-512GB-XANHLC', 32990000.00, 20, 'assets/products/iphone/iphone16plus_xanhmongket.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10043, 1003, '1003-512GB-XANHLAM', 32990000.00, 20, 'assets/products/iphone/iphone16plus_xanhluuly.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10044, 1004, '1004-128GB-EN', 22990000.00, 20, 'assets/products/iphone/iphone16_den.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10045, 1004, '1004-128GB-TRNG', 22990000.00, 20, 'assets/products/iphone/iphone16_trang.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10046, 1004, '1004-128GB-HNG', 22990000.00, 20, 'assets/products/iphone/iphone16_hong.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10047, 1004, '1004-128GB-XANHLC', 22990000.00, 20, 'assets/products/iphone/iphone16_xanhmongket.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10048, 1004, '1004-128GB-XANHLAM', 22990000.00, 20, 'assets/products/iphone/iphone16_xanhluuly.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10049, 1004, '1004-256GB-EN', 25990000.00, 20, 'assets/products/iphone/iphone16_den.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10050, 1004, '1004-256GB-TRNG', 25990000.00, 20, 'assets/products/iphone/iphone16_trang.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10051, 1004, '1004-256GB-HNG', 25990000.00, 20, 'assets/products/iphone/iphone16_hong.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10052, 1004, '1004-256GB-XANHLC', 25990000.00, 20, 'assets/products/iphone/iphone16_xanhmongket.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10053, 1004, '1004-256GB-XANHLAM', 25990000.00, 20, 'assets/products/iphone/iphone16_xanhluuly.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10054, 1004, '1004-512GB-EN', 29990000.00, 20, 'assets/products/iphone/iphone16_den.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10055, 1004, '1004-512GB-TRNG', 29990000.00, 20, 'assets/products/iphone/iphone16_trang.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10056, 1004, '1004-512GB-HNG', 29990000.00, 20, 'assets/products/iphone/iphone16_hong.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10057, 1004, '1004-512GB-XANHLC', 29990000.00, 20, 'assets/products/iphone/iphone16_xanhmongket.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10058, 1004, '1004-512GB-XANHLAM', 29990000.00, 20, 'assets/products/iphone/iphone16_xanhluuly.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10059, 1005, '1005-128GB-TRNG', 17990000.00, 20, 'assets/products/iphone/iphone16e_trang.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10060, 1005, '1005-128GB-EN', 17990000.00, 20, 'assets/products/iphone/iphone16e_den.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10061, 1005, '1005-256GB-TRNG', 19990000.00, 20, 'assets/products/iphone/iphone16e_trang.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10062, 1005, '1005-256GB-EN', 19990000.00, 20, 'assets/products/iphone/iphone16e_den.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10063, 1006, '1006-256GB-TITANTNHIN', 31990000.00, 20, 'assets/products/iphone/iphone15prm_titantunhien.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10064, 1006, '1006-256GB-TITANEN', 31990000.00, 20, 'assets/products/iphone/iphone15prm_titanden.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10065, 1006, '1006-256GB-TITANTRNG', 31990000.00, 20, 'assets/products/iphone/iphone15prm_titantrang.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10066, 1006, '1006-256GB-TITANXANH', 31990000.00, 20, 'assets/products/iphone/iphone15prm_titanxanh.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10067, 1006, '1006-512GB-TITANTNHIN', 36990000.00, 20, 'assets/products/iphone/iphone15prm_titantunhien.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10068, 1006, '1006-512GB-TITANEN', 36990000.00, 20, 'assets/products/iphone/iphone15prm_titanden.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10069, 1006, '1006-512GB-TITANTRNG', 36990000.00, 20, 'assets/products/iphone/iphone15prm_titantrang.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10070, 1006, '1006-512GB-TITANXANH', 36990000.00, 20, 'assets/products/iphone/iphone15prm_titanxanh.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10071, 1006, '1006-1TB-TITANTNHIN', 41990000.00, 20, 'assets/products/iphone/iphone15prm_titantunhien.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10072, 1006, '1006-1TB-TITANEN', 41990000.00, 20, 'assets/products/iphone/iphone15prm_titanden.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10073, 1006, '1006-1TB-TITANTRNG', 41990000.00, 20, 'assets/products/iphone/iphone15prm_titantrang.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10074, 1006, '1006-1TB-TITANXANH', 41990000.00, 20, 'assets/products/iphone/iphone15prm_titanxanh.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10075, 1007, '1007-128GB-TITANTNHIN', 25990000.00, 20, 'assets/products/iphone/iphone15pro_titantunhien.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10076, 1007, '1007-128GB-TITANEN', 25990000.00, 20, 'assets/products/iphone/iphone15pro_titanden.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10077, 1007, '1007-128GB-TITANTRNG', 25990000.00, 20, 'assets/products/iphone/iphone15pro_titantrang.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10078, 1007, '1007-128GB-TITANXANH', 25990000.00, 20, 'assets/products/iphone/iphone15pro_titanxanh.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10079, 1007, '1007-256GB-TITANTNHIN', 28990000.00, 20, 'assets/products/iphone/iphone15pro_titantunhien.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10080, 1007, '1007-256GB-TITANEN', 28990000.00, 20, 'assets/products/iphone/iphone15pro_titanden.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10081, 1007, '1007-256GB-TITANTRNG', 28990000.00, 20, 'assets/products/iphone/iphone15pro_titantrang.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10082, 1007, '1007-256GB-TITANXANH', 28990000.00, 20, 'assets/products/iphone/iphone15pro_titanxanh.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10083, 1007, '1007-512GB-TITANTNHIN', 33990000.00, 20, 'assets/products/iphone/iphone15pro_titantunhien.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10084, 1007, '1007-512GB-TITANEN', 33990000.00, 20, 'assets/products/iphone/iphone15pro_titanden.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10085, 1007, '1007-512GB-TITANTRNG', 33990000.00, 20, 'assets/products/iphone/iphone15pro_titantrang.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10086, 1007, '1007-512GB-TITANXANH', 33990000.00, 20, 'assets/products/iphone/iphone15pro_titanxanh.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10087, 1007, '1007-1TB-TITANTNHIN', 38990000.00, 20, 'assets/products/iphone/iphone15pro_titantunhien.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10088, 1007, '1007-1TB-TITANEN', 38990000.00, 20, 'assets/products/iphone/iphone15pro_titanden.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10089, 1007, '1007-1TB-TITANTRNG', 38990000.00, 20, 'assets/products/iphone/iphone15pro_titanden.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10090, 1007, '1007-1TB-TITANXANH', 38990000.00, 20, 'assets/products/iphone/iphone15pro_titanxanh.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10091, 1008, '1008-128GB-EN', 22990000.00, 20, 'assets/products/iphone/iphone15plus_den.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10092, 1008, '1008-128GB-XANHL', 22990000.00, 20, 'assets/products/iphone/iphone15plus_xanhlanhat.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10093, 1008, '1008-128GB-HNG', 22990000.00, 20, 'assets/products/iphone/iphone15plus_hongnhat.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10094, 1008, '1008-128GB-VNG', 22990000.00, 20, 'assets/products/iphone/iphone15_vangnhat.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10095, 1008, '1008-128GB-XANHDNG', 22990000.00, 20, 'assets/products/iphone/iphone15plus_xanhduongnhat.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10096, 1008, '1008-256GB-EN', 25990000.00, 20, 'assets/products/iphone/iphone15plus_den.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10097, 1008, '1008-256GB-XANHL', 25990000.00, 20, 'assets/products/iphone/iphone15plus_xanhlanhat.png', '2025-07-21 11:17:35', '2025-07-21 11:17:35', 1),
(10098, 1008, '1008-256GB-HNG', 25990000.00, 20, 'assets/products/iphone/iphone15plus_hongnhat.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10099, 1008, '1008-256GB-VNG', 25990000.00, 20, 'assets/products/iphone/iphone15_vangnhat.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10100, 1008, '1008-256GB-XANHDNG', 25990000.00, 20, 'assets/products/iphone/iphone15plus_xanhduongnhat.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10101, 1008, '1008-512GB-EN', 30990000.00, 20, 'assets/products/iphone/iphone15plus_den.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10102, 1008, '1008-512GB-XANHL', 30990000.00, 20, 'assets/products/iphone/iphone15plus_xanhlanhat.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10103, 1008, '1008-512GB-HNG', 30990000.00, 20, 'assets/products/iphone/iphone15plus_hongnhat.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10104, 1008, '1008-512GB-VNG', 30990000.00, 20, 'assets/products/iphone/iphone15_vangnhat.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10105, 1008, '1008-512GB-XANHDNG', 30990000.00, 20, 'assets/products/iphone/iphone15plus_xanhduongnhat.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10106, 1009, '1009-128GB-EN', 19990000.00, 20, 'assets/products/iphone/iphone15_den.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10107, 1009, '1009-128GB-XANHL', 19990000.00, 20, 'assets/products/iphone/iphone15_xanhlanhat.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10108, 1009, '1009-128GB-HNG', 19990000.00, 20, 'assets/products/iphone/iphone15_hongnhat.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10109, 1009, '1009-128GB-VNG', 19990000.00, 20, 'assets/products/iphone/iphone15_vangnhat.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10110, 1009, '1009-128GB-XANHDNG', 19990000.00, 20, 'assets/products/iphone/iphone15_xanhduongnhat.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10111, 1009, '1009-256GB-EN', 22990000.00, 20, 'assets/products/iphone/iphone15_den.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10112, 1009, '1009-256GB-XANHL', 22990000.00, 20, 'assets/products/iphone/iphone15_xanhlanhat.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10113, 1009, '1009-256GB-HNG', 22990000.00, 20, 'assets/products/iphone/iphone15_hongnhat.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10114, 1009, '1009-256GB-VNG', 22990000.00, 20, 'assets/products/iphone/iphone15_vangnhat.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10115, 1009, '1009-256GB-XANHDNG', 22990000.00, 20, 'assets/products/iphone/iphone15_xanhduongnhat.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10116, 1009, '1009-512GB-EN', 27990000.00, 20, 'assets/products/iphone/iphone15_den.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10117, 1009, '1009-512GB-XANHL', 27990000.00, 20, 'assets/products/iphone/iphone15_xanhlanhat.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10118, 1009, '1009-512GB-HNG', 27990000.00, 20, 'assets/products/iphone/iphone15_hongnhat.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10119, 1009, '1009-512GB-VNG', 27990000.00, 20, 'assets/products/iphone/iphone15_vangnhat.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10120, 1009, '1009-512GB-XANHDNG', 27990000.00, 20, 'assets/products/iphone/iphone15_xanhduongnhat.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10121, 1010, '1010-128GB-EN', 27990000.00, 20, 'assets/products/iphone/iphone14prm_den.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10122, 1010, '1010-128GB-BC', 27990000.00, 20, 'assets/products/iphone/iphone14prm_bac.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10123, 1010, '1010-128GB-VNG', 27990000.00, 20, 'assets/products/iphone/iphone14prm_vang.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10124, 1010, '1010-128GB-TM', 27990000.00, 20, 'assets/products/iphone/iphone14prm_tim.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10125, 1010, '1010-256GB-EN', 30990000.00, 20, 'assets/products/iphone/iphone14prm_den.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10126, 1010, '1010-256GB-BC', 30990000.00, 20, 'assets/products/iphone/iphone14prm_bac.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10127, 1010, '1010-256GB-VNG', 30990000.00, 20, 'assets/products/iphone/iphone14prm_vang.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10128, 1010, '1010-256GB-TM', 30990000.00, 20, 'assets/products/iphone/iphone14prm_tim.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10129, 1011, '1011-128GB-EN', 25990000.00, 20, 'assets/products/iphone/iphone14pro_den.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10130, 1011, '1011-128GB-BC', 25990000.00, 20, 'assets/products/iphone/iphone14pro_bac.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10131, 1011, '1011-128GB-VNG', 25990000.00, 20, 'assets/products/iphone/iphone14pro_vang.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10132, 1011, '1011-128GB-TM', 25990000.00, 20, 'assets/products/iphone/iphone14pro_tim.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10133, 1011, '1011-256GB-EN', 28990000.00, 20, 'assets/products/iphone/iphone14pro_den.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10134, 1011, '1011-256GB-BC', 28990000.00, 20, 'assets/products/iphone/iphone14pro_bac.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10135, 1011, '1011-256GB-VNG', 28990000.00, 20, 'assets/products/iphone/iphone14pro_vang.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10136, 1011, '1011-256GB-TM', 28990000.00, 20, 'assets/products/iphone/iphone14pro_tim.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10137, 1012, '1012-128GB-EN', 21990000.00, 20, 'assets/products/iphone/iphone14plus_den_512v256v128.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10138, 1012, '1012-128GB-TRNG', 21990000.00, 20, 'assets/products/iphone/iphone14plus_trang_512v256.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10139, 1012, '1012-128GB-', 21990000.00, 20, 'assets/products/iphone/iphone14plus_do_512GB.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10140, 1012, '1012-128GB-TM', 21990000.00, 20, 'assets/products/iphone/iphone14plus_timnhat_512v256.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10141, 1012, '1012-128GB-XANHDNG', 21990000.00, 20, 'assets/products/iphone/iphone14plus_xanhduong_512v256.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10142, 1012, '1012-256GB-EN', 24990000.00, 20, 'assets/products/iphone/iphone14plus_den_512v256v128.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10143, 1012, '1012-256GB-TRNG', 24990000.00, 20, 'assets/products/iphone/iphone14plus_trang_512v256.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10144, 1012, '1012-256GB-', 24990000.00, 20, 'assets/products/iphone/iphone14plus_do_512GB.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10145, 1012, '1012-256GB-TM', 24990000.00, 20, 'assets/products/iphone/iphone14plus_timnhat_512v256.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10146, 1012, '1012-256GB-XANHDNG', 24990000.00, 20, 'assets/products/iphone/iphone14plus_xanhduong_512v256.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10147, 1013, '1013-128GB-EN', 18990000.00, 20, 'assets/products/iphone/iphone14_den_256v128.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10148, 1013, '1013-128GB-TRNG', 18990000.00, 20, 'assets/products/iphone/iphone14_trang_256v128.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10149, 1013, '1013-128GB-XANHDNG', 18990000.00, 20, 'assets/products/iphone/iphone14_xanhduong_256v128.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10150, 1013, '1013-256GB-EN', 21990000.00, 20, 'assets/products/iphone/iphone14_den_256v128.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10151, 1013, '1013-256GB-TRNG', 21990000.00, 20, 'assets/products/iphone/iphone14_trang_256v128.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10152, 1013, '1013-256GB-XANHDNG', 21990000.00, 20, 'assets/products/iphone/iphone14_xanhduong_256v128.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10153, 1013, '1013-512GB-EN', 26990000.00, 20, 'assets/products/iphone/iphone14_den_256v128.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10154, 1013, '1013-512GB-TRNG', 26990000.00, 20, 'assets/products/iphone/iphone14_trang_256v128.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10155, 1013, '1013-512GB-XANHDNG', 26990000.00, 20, 'assets/products/iphone/iphone14_xanhduong_256v128.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10156, 1014, '1014-128GB-XANHDNG', 27990000.00, 20, 'assets/products/iphone/iphone13prm_xanhduong.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10157, 1014, '1014-128GB-XM', 27990000.00, 20, 'assets/products/iphone/iphone13prm_xam.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10158, 1014, '1014-128GB-XANHL', 27990000.00, 20, 'assets/products/iphone/iphone13prm_xanhla.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10159, 1014, '1014-128GB-BC', 27990000.00, 20, 'assets/products/iphone/iphone13prm_bac.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10160, 1014, '1014-128GB-VNG', 27990000.00, 20, 'assets/products/iphone/iphone13prm_vangdong.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10161, 1014, '1014-256GB-XANHDNG', 29990000.00, 20, 'assets/products/iphone/iphone13prm_xanhduong.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10162, 1014, '1014-256GB-XM', 29990000.00, 20, 'assets/products/iphone/iphone13prm_xam.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10163, 1014, '1014-256GB-XANHL', 29990000.00, 20, 'assets/products/iphone/iphone13prm_xanhla.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10164, 1014, '1014-256GB-BC', 29990000.00, 20, 'assets/products/iphone/iphone13prm_bac.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10165, 1014, '1014-256GB-VNG', 29990000.00, 20, 'assets/products/iphone/iphone13prm_vangdong.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10166, 1015, '1015-128GB-XANHL', 24990000.00, 20, 'assets/products/iphone/iphone13pro_xanhla.jpg', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10167, 1015, '1015-128GB-VNG', 24990000.00, 20, 'assets/products/iphone/iphone13pro_vangdong.webp', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10168, 1016, '1016-128GB-XANHL', 16990000.00, 20, 'assets/products/iphone/iphone13_xanhla_512v256.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10169, 1016, '1016-128GB-TRNG', 16990000.00, 20, 'assets/products/iphone/iphone13_trang_512v128.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10170, 1016, '1016-128GB-HNG', 16990000.00, 20, 'assets/products/iphone/iphone13_hong.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10171, 1016, '1016-128GB-XANHDNG', 16990000.00, 20, 'assets/products/iphone/iphone13_xanhduong_512v256v128.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10172, 1016, '1016-128GB-', 16990000.00, 20, 'assets/products/iphone/iphone13_do.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10173, 1016, '1016-128GB-EN', 16990000.00, 20, 'assets/products/iphone/iphone13_den_512v128.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10174, 1016, '1016-256GB-XANHL', 19990000.00, 20, 'assets/products/iphone/iphone13_xanhla_512v256.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10175, 1016, '1016-256GB-TRNG', 19990000.00, 20, 'assets/products/iphone/iphone13_trang_512v128.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10176, 1016, '1016-256GB-HNG', 19990000.00, 20, 'assets/products/iphone/iphone13_hong.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10177, 1016, '1016-512GB-', 23990000.00, 20, 'assets/products/iphone/iphone13_do.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10178, 1016, '1016-512GB-TRNG', 23990000.00, 20, 'assets/products/iphone/iphone13_trang_512v128.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10179, 1016, '1016-512GB-EN', 23990000.00, 20, 'assets/products/iphone/iphone13_den_512v128.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10180, 1017, '1017-128GB-XANHDNG', 24990000.00, 20, 'assets/products/iphone/iphone12prm_xanhduong.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10181, 1017, '1017-128GB-VNG', 24990000.00, 20, 'assets/products/iphone/iphone12prm_vangdong.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10182, 1017, '1017-128GB-BC', 24990000.00, 20, 'assets/products/iphone/iphone12prm_bac.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10183, 1017, '1017-128GB-XM', 24990000.00, 20, 'assets/products/iphone/iphone12prm_xam.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10184, 1017, '1017-256GB-XANHDNG', 27990000.00, 20, 'assets/products/iphone/iphone12prm_xanhduong.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10185, 1017, '1017-256GB-VNG', 27990000.00, 20, 'assets/products/iphone/iphone12prm_vangdong.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10186, 1017, '1017-256GB-BC', 27990000.00, 20, 'assets/products/iphone/iphone12prm_bac.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10187, 1017, '1017-256GB-XM', 27990000.00, 20, 'assets/products/iphone/iphone12prm_xam.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10188, 1018, '1018-128GB-XM', 21990000.00, 20, 'assets/products/iphone/iphone12pro_xam.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10189, 1018, '1018-128GB-BC', 21990000.00, 20, 'assets/products/iphone/iphone12pro_bac.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10190, 1018, '1018-128GB-VNG', 21990000.00, 20, 'assets/products/iphone/iphone12pro_vangdong.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10191, 1018, '1018-128GB-XANHDNG', 21990000.00, 20, 'assets/products/iphone/iphone12pro_xanhduong.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10192, 1018, '1018-256GB-XM', 24990000.00, 20, 'assets/products/iphone/iphone12pro_xam.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10193, 1018, '1018-256GB-BC', 24990000.00, 20, 'assets/products/iphone/iphone12pro_bac.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10194, 1018, '1018-256GB-VNG', 24990000.00, 20, 'assets/products/iphone/iphone12pro_vangdong.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10195, 1018, '1018-256GB-XANHDNG', 24990000.00, 20, 'assets/products/iphone/iphone12pro_xanhduong.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10196, 1018, '1018-512GB-XM', 29990000.00, 20, 'assets/products/iphone/iphone12pro_xam.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10197, 1018, '1018-512GB-BC', 29990000.00, 20, 'assets/products/iphone/iphone12pro_bac.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10198, 1018, '1018-512GB-VNG', 29990000.00, 20, 'assets/products/iphone/iphone12pro_vangdong.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10199, 1018, '1018-512GB-XANHDNG', 29990000.00, 20, 'assets/products/iphone/iphone12pro_xanhduong.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10200, 1019, '1019-64GB-EN', 16990000.00, 20, 'assets/products/iphone/iphone12_den.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10201, 1019, '1019-64GB-TRNG', 16990000.00, 20, 'assets/products/iphone/iphone12_trang.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10202, 1019, '1019-64GB-XANHL', 16990000.00, 20, 'assets/products/iphone/iphone12_xanhla.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10203, 1019, '1019-64GB-TM', 16990000.00, 20, 'assets/products/iphone/iphone12_tim.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10204, 1019, '1019-128GB-EN', 18990000.00, 20, 'assets/products/iphone/iphone12_den.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10205, 1019, '1019-128GB-TRNG', 18990000.00, 20, 'assets/products/iphone/iphone12_trang.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10206, 1019, '1019-128GB-XANHL', 18990000.00, 20, 'assets/products/iphone/iphone12_xanhla.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10207, 1019, '1019-128GB-TM', 18990000.00, 20, 'assets/products/iphone/iphone12_tim.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10208, 1019, '1019-256GB-EN', 21990000.00, 20, 'assets/products/iphone/iphone12_den.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10209, 1019, '1019-256GB-TRNG', 21990000.00, 20, 'assets/products/iphone/iphone12_trang.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10210, 1019, '1019-256GB-XANHL', 21990000.00, 20, 'assets/products/iphone/iphone12_xanhla.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10211, 1019, '1019-256GB-TM', 21990000.00, 20, 'assets/products/iphone/iphone12_tim.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10212, 1020, '1020-64GB-ENXM', 16990000.00, 20, 'assets/products/ipad/ipad_AirM3_denxam.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10213, 1020, '1020-64GB-XANHDNG', 16990000.00, 20, 'assets/products/iapd/ipad_AirM3_Xanhduong.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10214, 1020, '1020-64GB-TM', 16990000.00, 20, 'assets/products/ipad/ipad_AirM3_Tim.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10215, 1020, '1020-64GB-TRNG', 16990000.00, 20, 'assets/products/ipad/ipad_AirM3_TrangStarlight.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10216, 1020, '1020-256GB-ENXM', 19990000.00, 20, 'assets/products/ipad/ipad_AirM3_denxam.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10217, 1020, '1020-256GB-XANHDNG', 19990000.00, 20, 'assets/products/iapd/ipad_AirM3_Xanhduong.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10218, 1020, '1020-256GB-TM', 19990000.00, 20, 'assets/products/ipad/ipad_AirM3_Tim.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10219, 1020, '1020-256GB-TRNG', 19990000.00, 20, 'assets/products/ipad/ipad_AirM3_TrangStarlight.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10220, 1021, '1021-64GB-BC', 12990000.00, 20, 'assets/products/ipad/ipad_10_Bac.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10221, 1021, '1021-64GB-XANHDNG', 12990000.00, 20, 'assets/products/ipad/ipad_10_Xanhduong.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10222, 1021, '1021-64GB-HNG', 12990000.00, 20, 'assets/products/ipad/ipad_10_Hong.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10223, 1021, '1021-64GB-VNG', 12990000.00, 20, 'assets/products/ipad/ipad_10_Vang.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10224, 1021, '1021-256GB-BC', 15990000.00, 20, 'assets/products/ipad/ipad_10_Bac.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10225, 1021, '1021-256GB-XANHDNG', 15990000.00, 20, 'assets/products/ipad/ipad_10_Xanhduong.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10226, 1021, '1021-256GB-HNG', 15990000.00, 20, 'assets/products/ipad/ipad_10_Hong.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10227, 1021, '1021-256GB-VNG', 15990000.00, 20, 'assets/products/ipad/ipad_10_Vang.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10228, 1022, '1022-256GB-EN', 30990000.00, 20, 'assets/products/ipad/ipad_proM4_den.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10229, 1022, '1022-256GB-BC', 30990000.00, 20, 'assets/products/ipad/ipad_proM4_bac.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10230, 1023, '1023-128GB-XANHDNG', 16990000.00, 20, 'assets/products/iapd/ipad_Air6M2_Xanhduong.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10231, 1023, '1023-128GB-TM', 16990000.00, 20, 'assets/products/ipad/ipad_Air6M2_Tim.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10232, 1023, '1023-128GB-XM', 16990000.00, 20, 'assets/products/ipad/ipad_Air6M2_Xam.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10233, 1023, '1023-128GB-TRNG', 16990000.00, 20, 'assets/products/ipad/ipad_Air6M2_TrangStarlight.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10234, 1024, '1024-64GB-XM', 12990000.00, 20, 'assets/products/ipad/ipad_mini_denxam.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10235, 1024, '1024-64GB-XANHDNG', 12990000.00, 20, 'assets/products/iapd/ipad_mini_xanhduong.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10236, 1024, '1024-64GB-TRNG', 12990000.00, 20, 'assets/products/ipad/ipad_mini_trangstarlight.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10237, 1024, '1024-64GB-TM', 12990000.00, 20, 'assets/products/ipad/ipad_mini_tim.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10238, 1025, '1025-64GB-BC', 8990000.00, 20, 'assets/products/ipad/ipad_9_Bac.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10239, 1026, '1026-64GB-BC', 10990000.00, 20, 'assets/products/ipad/ipad_10_Bac.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10240, 1026, '1026-64GB-VNG', 10990000.00, 20, 'assets/products/ipad/ipad_10_Vang.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10241, 1026, '1026-256GB-BC', 14490000.00, 20, 'assets/products/ipad/ipad_10_Bac.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10242, 1026, '1026-256GB-VNG', 14490000.00, 20, 'assets/products/ipad/ipad_10_Vang.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10243, 1027, '1027-256GB-XANHDATRI', 27990000.00, 20, 'assets/products/macbook/MacbookAir_Xanhdatroinhat', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10244, 1027, '1027-256GB-BC', 27990000.00, 20, 'assets/products/macbook/MacbookAir_Bac.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10245, 1027, '1027-256GB-VNG', 27990000.00, 20, 'assets/products/macbook/MacbookAir_Vang.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10246, 1027, '1027-256GB-XANHEN', 27990000.00, 20, 'assets/products/macbook/MacbookAir_Xanhden.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10247, 1027, '1027-512GB-XANHDATRI', 32990000.00, 20, 'assets/products/macbook/MacbookAir_Xanhdatroinhat', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10248, 1027, '1027-512GB-BC', 32990000.00, 20, 'assets/products/macbook/MacbookAir_Bac.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10249, 1027, '1027-512GB-VNG', 32990000.00, 20, 'assets/products/ipad/MacbookAir_Vang.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10250, 1027, '1027-512GB-XANHEN', 32990000.00, 20, 'assets/products/macbook/MacbookAir_Xanhden.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10251, 1028, '1028-512GB-XM', 46990000.00, 20, 'assets/products/macbook/MacbookPro_Xam.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10252, 1028, '1028-512GB-BC', 46990000.00, 20, 'assets/products/macbook/MacbookPro_Bac.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10253, 1028, '1028-512GB-EN', 46990000.00, 20, 'assets/products/macbook/MacbookPro_Den.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10254, 1028, '1028-1TB-XM', 51990000.00, 20, 'assets/products/macbook/MacbookPro_Xam.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10255, 1028, '1028-1TB-BC', 51990000.00, 20, 'assets/products/macbook/MacbookPro_Bac.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10256, 1028, '1028-1TB-EN', 51990000.00, 20, 'assets/products/macbook/MacbookPro_Den.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10257, 1029, '1029-256GB-XANHDNG', 35990000.00, 20, 'assets/products/macbook/iMac24_M3_Xanhduong.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10258, 1029, '1029-256GB-HNG', 35990000.00, 20, 'assets/products/macbook/iMac24_M3_Hong.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10259, 1030, '1030-256GB-BC', 14990000.00, 20, 'assets/products/macbook/Macmini_M4_Bac.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10260, 1030, '1030-512GB-BC', 17990000.00, 20, 'assets/products/macbook/Macmini_M4_Bac.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10261, 1031, '1031-1TB-XM', 87990000.00, 20, 'assets/products/macbook/MacStudio_M2Ultra.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10262, 1031, '1031-2TB-XM', 99990000.00, 20, 'assets/products/macbook/MacStudio_M2Ultra.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10263, 1032, '1032-41MM-BC', 10990000.00, 20, 'assets/products/watch/applewatch_series10_bac.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10264, 1032, '1032-41MM-ENBNG', 17990000.00, 20, 'assets/products/watch/applewatch_series10_denbong.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10265, 1032, '1032-41MM-VNGHNG', 11990000.00, 20, 'assets/products/watch/applewatch_series10_vanghong.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10266, 1032, '1032-45MM-BC', 11990000.00, 20, 'assets/products/watch/applewatch_series10_bac.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10267, 1032, '1032-45MM-ENBNG', 18990000.00, 20, 'assets/products/watch/applewatch_series10_denbong.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10268, 1032, '1032-45MM-VNGHNG', 12990000.00, 20, 'assets/products/watch/applewatch_series10_vanghong.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10269, 1033, '1033-49MM-ALPINE', 30990000.00, 20, 'assets/products/watch/applewatch_ultra2_dayalpine.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10270, 1033, '1033-49MM-TRAIL', 32990000.00, 20, 'assets/products/watch/applewatch_ultra2_daytrail.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10271, 1033, '1033-49MM-OCEAN', 34990000.00, 20, 'assets/products/watch/applewatch_ultra2_dayocean.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10272, 1034, '1034-41MM-', 9990000.00, 20, 'assets/products/watch/applewatch_series9_do.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10273, 1034, '1034-41MM-XANHEN', 16990000.00, 20, 'assets/products/watch/applewatch_series9_xanhdendam.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10274, 1034, '1034-45MM-', 10990000.00, 20, 'assets/products/watch/applewatch_series9_do.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10275, 1034, '1034-45MM-XANHEN', 17990000.00, 20, 'assets/products/watch/applewatch_series9_xanhdendam.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10276, 1035, '1035-40MM-BC', 6990000.00, 20, 'assets/products/watch/applewatch_se2_bac.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10277, 1035, '1035-40MM-XANHEN', 10990000.00, 20, 'assets/products/watch/applewatch_se2_xanhden.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10278, 1035, '1035-40MM-TRNG', 7490000.00, 20, 'assets/products/watch/applewatch_se2_trangstarlight.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10279, 1035, '1035-44MM-BC', 7990000.00, 20, 'assets/products/watch/applewatch_se2_bac.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10280, 1035, '1035-44MM-XANHEN', 11990000.00, 20, 'assets/products/watch/applewatch_se2_xanhden.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10281, 1035, '1035-44MM-TRNG', 8490000.00, 20, 'assets/products/watch/applewatch_se2_trangstarlight.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10282, 1036, '1036-STANDARD-TRNG', 390000.00, 20, 'assets/products/tainghe/Earpods_lightning.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10283, 1037, '1037-STANDARD-TRNG', 450000.00, 20, 'assets/products/tainghe/earpods_usb_c.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10284, 1038, '1038-STANDARD-TRNG', 3990000.00, 20, 'assets/products/tainghe/Airpod3_lightning.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10285, 1039, '1039-STANDARD-TRNG', 6990000.00, 20, 'assets/products/tainghe/Airpods_pro_usb_c.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10286, 1040, '1040-STANDARD-BC', 15990000.00, 20, 'assets/products/tainghe/AirpodsMax_Bac.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10287, 1040, '1040-STANDARD-HNG', 15990000.00, 20, 'assets/products/tainghe/AirpodsMax_Hong.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10288, 1041, '1041-STANDARD-TRNG', 4990000.00, 20, 'assets/products/tainghe/Airpods4_chongon.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10289, 1042, '1042-STANDARD-WHITE', 590000.00, 20, 'assets/products/phukien/Adapter_sacusbC.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10290, 1043, '1043-STANDARD-SILVER', 790000.00, 20, 'assets/products/phukien/Airtag.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10291, 1044, '1044-STANDARD-WHITE', 3290000.00, 20, 'assets/products/phukien/apple_pencilpro.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10292, 1045, '1045-STANDARD-PINK', 490000.00, 20, 'assets/products/phukien/bandichuot.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10293, 1046, '1046-STANDARD-GRAY', 990000.00, 20, 'assets/products/phukien/baodaIpad.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10294, 1047, '1047-STANDARD-BLACK', 290000.00, 20, 'assets/products/phukien/cap_typeC.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10295, 1048, '1048-STANDARD-BLACK', 690000.00, 20, 'assets/products/phukien/capsac_typeC.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10296, 1049, '1049-STANDARD-PINK', 390000.00, 20, 'assets/products/phukien/chuotlaptop.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10297, 1050, '1050-STANDARD-CLEAR', 290000.00, 20, 'assets/products/phukien/cuongluc_iPadAir.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10298, 1051, '1051-STANDARD-CLEAR', 350000.00, 20, 'assets/products/phukien/cuongluc_iPadPro.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10299, 1052, '1052-STANDARD-CLEAR', 200000.00, 20, 'assets/products/phukien/cuonglucAppleWatch.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10300, 1053, '1053-STANDARD-CLEAR', 300000.00, 20, 'assets/products/phukien/cuonglucIphone.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10301, 1054, '1054-STANDARD-CLEAR', 500000.00, 20, 'assets/products/phukien/cuonglucMac.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10302, 1055, '1055-STANDARD-PINK', 790000.00, 20, 'assets/products/phukien/daycaosuAppleWatch.png', '2025-07-21 11:17:36', '2025-07-21 11:17:36', 1),
(10303, 1056, '1056-STANDARD-BROWN', 890000.00, 20, 'assets/products/phukien/dayNylonAppleWatch.png', '2025-07-21 11:17:37', '2025-07-21 11:17:37', 1),
(10304, 1057, '1057-STANDARD-BLACK', 690000.00, 20, 'assets/products/phukien/daysiliconeAppleWatch.png', '2025-07-21 11:17:37', '2025-07-21 11:17:37', 1),
(10305, 1058, '1058-STANDARD-SILVER', 1490000.00, 20, 'assets/products/phukien/daythepkhonggiAppleWatch.png', '2025-07-21 11:17:37', '2025-07-21 11:17:37', 1),
(10306, 1059, '1059-STANDARD-WHITE', 990000.00, 20, 'assets/products/phukien/desackhongday_TypeC.png', '2025-07-21 11:17:37', '2025-07-21 11:17:37', 1),
(10307, 1060, '1060-STANDARD-SILVER', 490000.00, 20, 'assets/products/phukien/GiadoLaptop.png', '2025-07-21 11:17:37', '2025-07-21 11:17:37', 1),
(10308, 1061, '1061-STANDARD-WHITE', 2990000.00, 20, 'assets/products/phukien/MagicKeyboard.png', '2025-07-21 11:17:37', '2025-07-21 11:17:37', 1),
(10309, 1062, '1062-STANDARD-CLEAR', 200000.00, 20, 'assets/products/phukien/OpAppleWatch.png', '2025-07-21 11:17:37', '2025-07-21 11:17:37', 1),
(10310, 1063, '1063-STANDARD-BLACK', 2490000.00, 20, 'assets/products/phukien/thietbicamtaychongrung.png', '2025-07-21 11:17:37', '2025-07-21 11:17:37', 1),
(10311, 1064, '1064-STANDARD-BLACK', 1990000.00, 20, 'assets/products/phukien/thuam.png', '2025-07-21 11:17:37', '2025-07-21 11:17:37', 1);

-- --------------------------------------------------------

--
-- Table structure for table `product_variant_attribute_links`
--

CREATE TABLE `product_variant_attribute_links` (
  `variant_id` int(11) NOT NULL,
  `attribute_value_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_variant_attribute_links`
--

INSERT INTO `product_variant_attribute_links` (`variant_id`, `attribute_value_id`) VALUES
(10001, 1001),
(10001, 2001),
(10002, 1001),
(10002, 2002),
(10003, 1001),
(10003, 2003),
(10004, 1001),
(10004, 2004),
(10005, 1002),
(10005, 2001),
(10006, 1002),
(10006, 2002),
(10007, 1002),
(10007, 2003),
(10008, 1002),
(10008, 2004),
(10009, 1003),
(10009, 2001),
(10010, 1003),
(10010, 2002),
(10011, 1003),
(10011, 2003),
(10012, 1003),
(10012, 2004),
(10013, 1004),
(10013, 2001),
(10014, 1004),
(10014, 2002),
(10015, 1004),
(10015, 2003),
(10016, 1004),
(10016, 2004),
(10017, 1001),
(10017, 2001),
(10018, 1001),
(10018, 2002),
(10019, 1001),
(10019, 2003),
(10020, 1001),
(10020, 2004),
(10021, 1002),
(10021, 2001),
(10022, 1002),
(10022, 2002),
(10023, 1002),
(10023, 2003),
(10024, 1002),
(10024, 2004),
(10025, 1003),
(10025, 2001),
(10026, 1003),
(10026, 2002),
(10027, 1003),
(10027, 2003),
(10028, 1003),
(10028, 2004),
(10029, 1004),
(10029, 2005),
(10030, 1004),
(10030, 2006),
(10031, 1004),
(10031, 2007),
(10032, 1004),
(10032, 2008),
(10033, 1004),
(10033, 2009),
(10034, 1001),
(10034, 2005),
(10035, 1001),
(10035, 2006),
(10036, 1001),
(10036, 2007),
(10037, 1001),
(10037, 2008),
(10038, 1001),
(10038, 2009),
(10039, 1002),
(10039, 2005),
(10040, 1002),
(10040, 2006),
(10041, 1002),
(10041, 2007),
(10042, 1002),
(10042, 2008),
(10043, 1002),
(10043, 2009),
(10044, 1004),
(10044, 2005),
(10045, 1004),
(10045, 2006),
(10046, 1004),
(10046, 2007),
(10047, 1004),
(10047, 2008),
(10048, 1004),
(10048, 2009),
(10049, 1001),
(10049, 2005),
(10050, 1001),
(10050, 2006),
(10051, 1001),
(10051, 2007),
(10052, 1001),
(10052, 2008),
(10053, 1001),
(10053, 2009),
(10054, 1002),
(10054, 2005),
(10055, 1002),
(10055, 2006),
(10056, 1002),
(10056, 2007),
(10057, 1002),
(10057, 2008),
(10058, 1002),
(10058, 2009),
(10059, 1004),
(10059, 2006),
(10060, 1004),
(10060, 2005),
(10061, 1001),
(10061, 2006),
(10062, 1001),
(10062, 2005),
(10063, 1001),
(10063, 2001),
(10064, 1001),
(10064, 2002),
(10065, 1001),
(10065, 2003),
(10066, 1001),
(10066, 2010),
(10067, 1002),
(10067, 2001),
(10068, 1002),
(10068, 2002),
(10069, 1002),
(10069, 2003),
(10070, 1002),
(10070, 2010),
(10071, 1003),
(10071, 2001),
(10072, 1003),
(10072, 2002),
(10073, 1003),
(10073, 2003),
(10074, 1003),
(10074, 2010),
(10075, 1004),
(10075, 2001),
(10076, 1004),
(10076, 2002),
(10077, 1004),
(10077, 2003),
(10078, 1004),
(10078, 2010),
(10079, 1001),
(10079, 2001),
(10080, 1001),
(10080, 2002),
(10081, 1001),
(10081, 2003),
(10082, 1001),
(10082, 2010),
(10083, 1002),
(10083, 2001),
(10084, 1002),
(10084, 2002),
(10085, 1002),
(10085, 2003),
(10086, 1002),
(10086, 2010),
(10087, 1003),
(10087, 2001),
(10088, 1003),
(10088, 2002),
(10089, 1003),
(10089, 2003),
(10090, 1003),
(10090, 2010),
(10091, 1004),
(10091, 2005),
(10092, 1004),
(10092, 2011),
(10093, 1004),
(10093, 2007),
(10094, 1004),
(10094, 2012),
(10095, 1004),
(10095, 2013),
(10096, 1001),
(10096, 2005),
(10097, 1001),
(10097, 2011),
(10098, 1001),
(10098, 2007),
(10099, 1001),
(10099, 2012),
(10100, 1001),
(10100, 2013),
(10101, 1002),
(10101, 2005),
(10102, 1002),
(10102, 2011),
(10103, 1002),
(10103, 2007),
(10104, 1002),
(10104, 2012),
(10105, 1002),
(10105, 2013),
(10106, 1004),
(10106, 2005),
(10107, 1004),
(10107, 2011),
(10108, 1004),
(10108, 2007),
(10109, 1004),
(10109, 2012),
(10110, 1004),
(10110, 2013),
(10111, 1001),
(10111, 2005),
(10112, 1001),
(10112, 2011),
(10113, 1001),
(10113, 2007),
(10114, 1001),
(10114, 2012),
(10115, 1001),
(10115, 2013),
(10116, 1002),
(10116, 2005),
(10117, 1002),
(10117, 2011),
(10118, 1002),
(10118, 2007),
(10119, 1002),
(10119, 2012),
(10120, 1002),
(10120, 2013),
(10121, 1004),
(10121, 2005),
(10122, 1004),
(10122, 2014),
(10123, 1004),
(10123, 2012),
(10124, 1004),
(10124, 2015),
(10125, 1001),
(10125, 2005),
(10126, 1001),
(10126, 2014),
(10127, 1001),
(10127, 2012),
(10128, 1001),
(10128, 2015),
(10129, 1004),
(10129, 2005),
(10130, 1004),
(10130, 2014),
(10131, 1004),
(10131, 2012),
(10132, 1004),
(10132, 2015),
(10133, 1001),
(10133, 2005),
(10134, 1001),
(10134, 2014),
(10135, 1001),
(10135, 2012),
(10136, 1001),
(10136, 2015),
(10137, 1004),
(10137, 2005),
(10138, 1004),
(10138, 2006),
(10139, 1004),
(10139, 2016),
(10140, 1004),
(10140, 2015),
(10141, 1004),
(10141, 2013),
(10142, 1001),
(10142, 2005),
(10143, 1001),
(10143, 2006),
(10144, 1001),
(10144, 2016),
(10145, 1001),
(10145, 2015),
(10146, 1001),
(10146, 2013),
(10147, 1004),
(10147, 2005),
(10148, 1004),
(10148, 2006),
(10149, 1004),
(10149, 2013),
(10150, 1001),
(10150, 2005),
(10151, 1001),
(10151, 2006),
(10152, 1001),
(10152, 2013),
(10153, 1002),
(10153, 2005),
(10154, 1002),
(10154, 2006),
(10155, 1002),
(10155, 2013),
(10156, 1004),
(10156, 2013),
(10157, 1004),
(10157, 2017),
(10158, 1004),
(10158, 2018),
(10159, 1004),
(10159, 2014),
(10160, 1004),
(10160, 2012),
(10161, 1001),
(10161, 2013),
(10162, 1001),
(10162, 2017),
(10163, 1001),
(10163, 2018),
(10164, 1001),
(10164, 2014),
(10165, 1001),
(10165, 2012),
(10166, 1004),
(10166, 2011),
(10167, 1004),
(10167, 2012),
(10168, 1004),
(10168, 2011),
(10169, 1004),
(10169, 2006),
(10170, 1004),
(10170, 2007),
(10171, 1004),
(10171, 2013),
(10172, 1004),
(10172, 2016),
(10173, 1004),
(10173, 2005),
(10174, 1001),
(10174, 2011),
(10175, 1001),
(10175, 2006),
(10176, 1001),
(10176, 2007),
(10177, 1002),
(10177, 2016),
(10178, 1002),
(10178, 2006),
(10179, 1002),
(10179, 2005),
(10180, 1004),
(10180, 2013),
(10181, 1004),
(10181, 2012),
(10182, 1004),
(10182, 2014),
(10183, 1004),
(10183, 2017),
(10184, 1001),
(10184, 2013),
(10185, 1001),
(10185, 2012),
(10186, 1001),
(10186, 2014),
(10187, 1001),
(10187, 2017),
(10188, 1004),
(10188, 2017),
(10189, 1004),
(10189, 2014),
(10190, 1004),
(10190, 2012),
(10191, 1004),
(10191, 2013),
(10192, 1001),
(10192, 2017),
(10193, 1001),
(10193, 2014),
(10194, 1001),
(10194, 2012),
(10195, 1001),
(10195, 2013),
(10196, 1002),
(10196, 2017),
(10197, 1002),
(10197, 2014),
(10198, 1002),
(10198, 2012),
(10199, 1002),
(10199, 2013),
(10200, 1005),
(10200, 2005),
(10201, 1005),
(10201, 2006),
(10202, 1005),
(10202, 2011),
(10203, 1005),
(10203, 2015),
(10204, 1004),
(10204, 2005),
(10205, 1004),
(10205, 2006),
(10206, 1004),
(10206, 2011),
(10207, 1004),
(10207, 2015),
(10208, 1001),
(10208, 2005),
(10209, 1001),
(10209, 2006),
(10210, 1001),
(10210, 2011),
(10211, 1001),
(10211, 2015),
(10212, 1005),
(10212, 2019),
(10213, 1005),
(10213, 2013),
(10214, 1005),
(10214, 2015),
(10215, 1005),
(10215, 2006),
(10216, 1001),
(10216, 2019),
(10217, 1001),
(10217, 2013),
(10218, 1001),
(10218, 2015),
(10219, 1001),
(10219, 2006),
(10220, 1005),
(10220, 2014),
(10221, 1005),
(10221, 2013),
(10222, 1005),
(10222, 2007),
(10223, 1005),
(10223, 2012),
(10224, 1001),
(10224, 2014),
(10225, 1001),
(10225, 2013),
(10226, 1001),
(10226, 2007),
(10227, 1001),
(10227, 2012),
(10228, 1001),
(10228, 2005),
(10229, 1001),
(10229, 2014),
(10230, 1004),
(10230, 2013),
(10231, 1004),
(10231, 2015),
(10232, 1004),
(10232, 2017),
(10233, 1004),
(10233, 2006),
(10234, 1005),
(10234, 2017),
(10235, 1005),
(10235, 2013),
(10236, 1005),
(10236, 2006),
(10237, 1005),
(10237, 2015),
(10238, 1005),
(10238, 2014),
(10239, 1005),
(10239, 2014),
(10240, 1005),
(10240, 2012),
(10241, 1001),
(10241, 2014),
(10242, 1001),
(10242, 2012),
(10243, 1001),
(10243, 2020),
(10244, 1001),
(10244, 2014),
(10245, 1001),
(10245, 2012),
(10246, 1001),
(10246, 2021),
(10247, 1002),
(10247, 2020),
(10248, 1002),
(10248, 2014),
(10249, 1002),
(10249, 2012),
(10250, 1002),
(10250, 2021),
(10251, 1002),
(10251, 2017),
(10252, 1002),
(10252, 2014),
(10253, 1002),
(10253, 2005),
(10254, 1003),
(10254, 2017),
(10255, 1003),
(10255, 2014),
(10256, 1003),
(10256, 2005),
(10257, 1001),
(10257, 2013),
(10258, 1001),
(10258, 2007),
(10259, 1001),
(10259, 2014),
(10260, 1002),
(10260, 2014),
(10261, 1003),
(10261, 2017),
(10262, 1006),
(10262, 2017),
(10263, 1007),
(10263, 2014),
(10264, 1007),
(10264, 2022),
(10265, 1007),
(10265, 2023),
(10266, 1008),
(10266, 2014),
(10267, 1008),
(10267, 2022),
(10268, 1008),
(10268, 2023),
(10269, 1009),
(10269, 2024),
(10270, 1009),
(10270, 2025),
(10271, 1009),
(10271, 2026),
(10272, 1007),
(10272, 2016),
(10273, 1007),
(10273, 2027),
(10274, 1008),
(10274, 2016),
(10275, 1008),
(10275, 2027),
(10276, 1010),
(10276, 2014),
(10277, 1010),
(10277, 2027),
(10278, 1010),
(10278, 2006),
(10279, 1011),
(10279, 2014),
(10280, 1011),
(10280, 2027),
(10281, 1011),
(10281, 2006),
(10282, 1012),
(10282, 2006),
(10283, 1012),
(10283, 2006),
(10284, 1012),
(10284, 2006),
(10285, 1012),
(10285, 2006),
(10286, 1012),
(10286, 2014),
(10287, 1012),
(10287, 2007),
(10288, 1012),
(10288, 2006),
(10289, 1013),
(10289, 2028),
(10290, 1013),
(10290, 2029),
(10291, 1013),
(10291, 2028),
(10292, 1013),
(10292, 2030),
(10293, 1013),
(10293, 2031),
(10294, 1013),
(10294, 2032),
(10295, 1013),
(10295, 2032),
(10296, 1013),
(10296, 2030),
(10297, 1013),
(10297, 2033),
(10298, 1013),
(10298, 2033),
(10299, 1013),
(10299, 2033),
(10300, 1013),
(10300, 2033),
(10301, 1013),
(10301, 2033),
(10302, 1013),
(10302, 2030),
(10303, 1013),
(10303, 2034),
(10304, 1013),
(10304, 2032),
(10305, 1013),
(10305, 2029),
(10306, 1013),
(10306, 2028),
(10307, 1013),
(10307, 2029),
(10308, 1013),
(10308, 2028),
(10309, 1013),
(10309, 2033),
(10310, 1013),
(10310, 2032),
(10311, 1013),
(10311, 2032);

-- --------------------------------------------------------

--
-- Table structure for table `promotions`
--

CREATE TABLE `promotions` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `promotions`
--

INSERT INTO `promotions` (`id`, `title`, `slug`, `description`, `type`, `image_url`, `expiry_date`, `created_at`, `updated_at`) VALUES
(1, 'Giảm giá 10% cho iPhone 16 Pro', 'giam-gia-10-iphone-16-pro', 'Ưu đãi đặc biệt giảm 10% cho tất cả các mẫu iPhone 16 Pro, áp dụng đến cuối tháng.', 'discount', 'https://example.com/images/promo_iphone16.jpg', '2025-08-31', '2025-07-18 10:59:49', '2025-07-18 10:59:49'),
(2, 'Ưu đãi đặc biệt MacBook Air M3', 'uu-dai-macbook-air-m3', 'Tặng kèm phụ kiện trị giá 1.000.000 VNĐ khi mua MacBook Air M3 bất kỳ.', 'event', 'https://example.com/images/promo_macbook.jpg', '2025-09-15', '2025-07-18 10:59:49', '2025-07-18 10:59:49'),
(3, 'Miễn phí vận chuyển toàn quốc', 'mien-phi-van-chuyen-toan-quoc', 'Miễn phí giao hàng cho tất cả các đơn hàng có giá trị trên 5.000.000 VNĐ.', 'shipping', 'https://example.com/images/promo_freeship.jpg', '2025-12-31', '2025-07-18 10:59:49', '2025-07-18 10:59:49'),
(4, 'Giảm 50% phụ kiện khi mua iPad Pro', 'giam-50-phu-kien-ipad-pro', 'Mua iPad Pro và nhận ngay ưu đãi 50% cho tất cả các phụ kiện đi kèm.', 'discount', 'https://example.com/images/promo_ipadacc.jpg', '2025-10-20', '2025-07-18 10:59:49', '2025-07-18 10:59:49'),
(5, 'Khuyến mãi mừng sinh nhật cửa hàng', 'khuyen-mai-sinh-nhat', 'Chào mừng sinh nhật Anh Em Rọt Store với hàng ngàn ưu đãi hấp dẫn trên toàn bộ sản phẩm.', 'event', 'https://example.com/images/promo_birthday.jpg', '2025-08-01', '2025-07-18 10:59:49', '2025-07-18 10:59:49'),
(6, 'Ưu đãi độc quyền cho thành viên VIP', 'uu-dai-thanh-vien-vip', 'Giảm thêm 5% cho tất cả các đơn hàng dành cho thành viên VIP của chúng tôi.', 'discount', 'https://example.com/images/promo_vip.jpg', '2026-01-31', '2025-07-18 10:59:49', '2025-07-18 10:59:49'),
(7, 'Hoàn tiền 5% khi thanh toán MoMo/ZaloPay', 'hoan-tien-thanh-toan-vi', 'Nhận ngay 5% hoàn tiền vào ví điện tử khi thanh toán bằng MoMo hoặc ZaloPay.', 'discount', 'https://example.com/images/promo_ewallet.jpg', '2025-09-30', '2025-07-18 10:59:49', '2025-07-18 10:59:49'),
(8, 'Flash Sale cuối tuần', 'flash-sale-cuoi-tuan', 'Cơ hội vàng săn deal hot với Flash Sale cuối tuần, giảm giá lên đến 70%!', 'event', 'https://example.com/images/promo_flashsale.jpg', '2025-07-21', '2025-07-18 10:59:49', '2025-07-18 10:59:49'),
(9, 'Chương trình Thu cũ đổi mới', 'thu-cu-doi-moi', 'Nâng cấp thiết bị Apple của bạn với chương trình Thu cũ đổi mới, trợ giá cực kỳ hấp dẫn.', 'event', 'https://example.com/images/promo_tradein.jpg', '2025-11-30', '2025-07-18 10:59:49', '2025-07-18 10:59:49'),
(10, 'Ưu đãi đặc biệt mùa tựu trường', 'uu-dai-tu-truong', 'Giảm giá đặc biệt và tặng quà hấp dẫn cho học sinh, sinh viên trong mùa tựu trường.', 'discount', 'https://example.com/images/promo_backtoschool.jpg', '2025-09-10', '2025-07-18 10:59:49', '2025-07-18 10:59:49');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_items`
--

CREATE TABLE `purchase_items` (
  `id` int(11) NOT NULL,
  `purchase_order_id` int(11) NOT NULL,
  `variant_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_orders`
--

CREATE TABLE `purchase_orders` (
  `id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_date` timestamp NULL DEFAULT current_timestamp(),
  `total_amount` decimal(10,2) NOT NULL,
  `status` varchar(50) DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `variant_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `site_settings`
--

CREATE TABLE `site_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `site_settings`
--

INSERT INTO `site_settings` (`id`, `setting_key`, `setting_value`, `description`) VALUES
(1, 'site_name', 'Apple Store VN', 'Tên trang web'),
(2, 'contact_email', 'contact@applestore.vn', 'Email liên hệ'),
(3, 'phone_number', '+84 123 456 789', 'Số điện thoại liên hệ'),
(4, 'address', '123 Đường ABC, Quận 1, TP.HCM', 'Địa chỉ cửa hàng'),
(5, 'facebook_url', 'https://www.facebook.com/applestorevn', 'Đường dẫn Facebook'),
(6, 'twitter_url', 'https://www.twitter.com/applestorevn', 'Đường dẫn Twitter'),
(7, 'instagram_url', 'https://www.instagram.com/applestorevn', 'Đường dẫn Instagram'),
(8, 'flash_sale_duration', '3600', 'Thời lượng Flash Sale tính bằng giây'),
(9, 'products_per_page', '12', 'Số lượng sản phẩm hiển thị mỗi trang'),
(10, 'news_per_page', '5', 'Số lượng bài viết tin tức hiển thị mỗi trang'),
(11, 'default_currency', 'VND', 'Đơn vị tiền tệ mặc định');

-- --------------------------------------------------------

--
-- Table structure for table `sliders`
--

CREATE TABLE `sliders` (
  `id` int(11) NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `link_url` varchar(255) DEFAULT NULL,
  `order_by` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sliders`
--

INSERT INTO `sliders` (`id`, `image_url`, `caption`, `link_url`, `order_by`, `is_active`) VALUES
(1, 'uploads/slider1.jpg', 'iPhone 15 Pro Max - Đỉnh Cao Sức Mạnh', 'product_detail.php?id=1', 1, 1),
(2, 'uploads/slider2.jpg', 'MacBook Air M3 - Siêu Mỏng, Siêu Mạnh', 'product_detail.php?id=2', 2, 1),
(3, 'uploads/slider3.jpg', 'Apple Watch Series 9 - Kết Nối Mọi Lúc Mọi Nơi', 'product_detail.php?id=3', 3, 1);

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `contact_person` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `name`, `contact_person`, `phone`, `email`, `address`) VALUES
(1, 'Apple Inc.', 'Tim Cook', '+1-800-275-2273', 'support@apple.com', 'One Apple Park Way, Cupertino, CA 95014, USA');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `password` varchar(255) NOT NULL COMMENT 'Hashed password',
  `email` varchar(255) NOT NULL,
  `role` varchar(50) DEFAULT 'customer',
  `verify_token` varchar(64) DEFAULT NULL,
  `verify_token_expires_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `is_updated` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `password`, `email`, `role`, `verify_token`, `verify_token_expires_at`, `created_at`, `last_login`, `is_verified`, `is_updated`) VALUES
(1, '$2y$10$w82d/P2P7JcE3S2H2p0L5u/N5X6A0L2F5M0N0Q5R2S3T0U4V5W6X7Y8Z9a0b1c2d3e4f5g6h7i8j9k0l1m2n3o4p5q6r7s8t9u0v1w2x3y4z0', 'admin@example.com', 'customer', NULL, NULL, '2023-10-26 10:00:00', '2024-07-15 16:34:40', 1, 0),
(2, '$2y$10$w82d/P2P7JcE3S2H2p0L5u/N5X6A0L2F5M0N0Q5R2S3T0U4V5W6X7Y8Z9a0b1c2d3e4f5g6h7i8j9k0l1m2n3o4p5q6r7s8t9u0v1w2x3y4z0', 'customer1@example.com', 'customer', NULL, NULL, '2023-10-26 10:05:00', '2024-07-15 16:34:40', 1, 0),
(3, '$2y$10$cGlSJlSkn.zGetksDct3YuYiCafOtPDlxspu2wkb6/Qm/4kmOqhd6', 'hoangquangdat182005@gmail.com', 'admin', NULL, NULL, '2025-07-17 06:54:17', NULL, 1, 1),
(4, '123', 'khachhang1@example.com', 'customer', NULL, NULL, '2025-07-17 07:30:49', NULL, 1, 0),
(5, '$2y$10$qcVQYH4IX/6XuEyouLtmKehslOa9T/UzgO0xI04LJtHOHVcKUMzGa', '26a4040725@hvnh.edu.vn', 'admin', '959b4b30ec9febe70c06cffda72dd53822c7abadbafaa272b4c0ffe8cb1a07c0', '2025-07-18 18:17:42', '2025-07-17 16:17:42', NULL, 1, 1),
(7, '$2y$10$DX8t3m.D0XEnt8o86pGkbu0MCQfHUSwOYmC7GHrkF5lxT/vjVMErq', 'kiogit30@gmail.com', 'customer', 'd0b40fa82cb757c3bbf2348d4b15a4b3bda90ce31b704460808059e6b65e55bc', '2025-07-19 12:48:13', '2025-07-18 10:48:13', NULL, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_detail`
--

CREATE TABLE `user_detail` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_detail`
--

INSERT INTO `user_detail` (`id`, `user_id`, `first_name`, `last_name`, `avatar`, `address`, `phone_number`, `gender`, `date_of_birth`) VALUES
(1, 3, 'Hoàng Quang', 'Đạt', 'assets/avatars/user_3_1752768743.png', 'xã HNN', '0127865412', 'male', '2005-08-01'),
(2, 4, 'Nguyen', 'Van A', NULL, 'Ha Noi', '0123456789', NULL, NULL),
(3, 5, 'Hoàng Quang', 'Đạt', 'assets/avatars/user_5_1752769245.png', 'xã Chí Minh, tỉnh Hưng Yên', '0827925712', 'male', '2005-08-01'),
(4, 7, 'Hoa', 'Quý', NULL, 'Nhà giàu nhất Kim Bôi rồi', '0127865412', 'female', '2005-03-26');

-- --------------------------------------------------------

--
-- Table structure for table `variant_attributes`
--

CREATE TABLE `variant_attributes` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `display_name` varchar(100) NOT NULL,
  `type` varchar(50) DEFAULT 'text',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `variant_attributes`
--

INSERT INTO `variant_attributes` (`id`, `name`, `display_name`, `type`, `created_at`, `updated_at`) VALUES
(1, 'Color', 'Màu sắc', 'color_picker', '2025-07-21 11:12:32', '2025-07-21 11:12:32'),
(2, 'Storage', 'Dung lượng', 'text', '2025-07-21 11:12:32', '2025-07-21 11:12:32');

-- --------------------------------------------------------

--
-- Table structure for table `variant_attribute_values`
--

CREATE TABLE `variant_attribute_values` (
  `id` int(11) NOT NULL,
  `attribute_id` int(11) NOT NULL,
  `value` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `hex_code` varchar(7) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `variant_attribute_values`
--

INSERT INTO `variant_attribute_values` (`id`, `attribute_id`, `value`, `created_at`, `updated_at`, `hex_code`) VALUES
(1001, 2, '256GB', '2025-07-21 11:17:35', '2025-07-21 11:17:35', NULL),
(1002, 2, '512GB', '2025-07-21 11:17:35', '2025-07-21 11:17:35', NULL),
(1003, 2, '1TB', '2025-07-21 11:17:35', '2025-07-21 11:17:35', NULL),
(1004, 2, '128GB', '2025-07-21 11:17:35', '2025-07-21 11:17:35', NULL),
(1005, 2, '64GB', '2025-07-21 11:17:36', '2025-07-21 11:17:36', NULL),
(1006, 2, '2TB', '2025-07-21 11:17:36', '2025-07-21 11:17:36', NULL),
(1007, 2, '41mm', '2025-07-21 11:17:36', '2025-07-21 11:17:36', NULL),
(1008, 2, '45mm', '2025-07-21 11:17:36', '2025-07-21 11:17:36', NULL),
(1009, 2, '49mm', '2025-07-21 11:17:36', '2025-07-21 11:17:36', NULL),
(1010, 2, '40mm', '2025-07-21 11:17:36', '2025-07-21 11:17:36', NULL),
(1011, 2, '44mm', '2025-07-21 11:17:36', '2025-07-21 11:17:36', NULL),
(1012, 2, 'standard', '2025-07-21 11:17:36', '2025-07-21 11:17:36', NULL),
(2001, 1, 'Titan Tự nhiên', '2025-07-21 11:17:35', '2025-07-21 13:27:54', '#D6D6D6'),
(2002, 1, 'Titan Đen', '2025-07-21 11:17:35', '2025-07-21 13:27:54', '#353535'),
(2003, 1, 'Titan Trắng', '2025-07-21 11:17:35', '2025-07-21 13:27:54', '#F5F5F7'),
(2004, 1, 'Titan Sa mạc', '2025-07-21 11:17:35', '2025-07-21 13:27:54', '#E5C29F'),
(2005, 1, 'Đen', '2025-07-21 11:17:35', '2025-07-21 13:27:04', '#222222'),
(2006, 1, 'Trắng', '2025-07-21 11:17:35', '2025-07-21 13:27:04', '#ffffff'),
(2007, 1, 'Hồng', '2025-07-21 11:17:35', '2025-07-21 13:27:54', '#FFB6C1'),
(2008, 1, 'Xanh Lục', '2025-07-21 11:17:35', '2025-07-21 13:27:54', '#4CAF50'),
(2009, 1, 'Xanh Lam', '2025-07-21 11:17:35', '2025-07-21 13:27:54', '#1976D2'),
(2010, 1, 'Titan Xanh', '2025-07-21 11:17:35', '2025-07-21 13:27:54', '#3B5B7A'),
(2011, 1, 'Xanh Lá', '2025-07-21 11:17:35', '2025-07-21 13:27:04', '#4CAF50'),
(2012, 1, 'Vàng', '2025-07-21 11:17:35', '2025-07-21 13:27:05', '#FFD700'),
(2013, 1, 'Xanh Dương', '2025-07-21 11:17:35', '2025-07-21 13:27:05', '#0000FF'),
(2014, 1, 'Bạc', '2025-07-21 11:17:36', '2025-07-21 11:17:36', NULL),
(2015, 1, 'Tím', '2025-07-21 11:17:36', '2025-07-21 13:27:05', '#800080'),
(2016, 1, 'Đỏ', '2025-07-21 11:17:36', '2025-07-21 11:17:36', NULL),
(2017, 1, 'Xám', '2025-07-21 11:17:36', '2025-07-21 11:17:36', NULL),
(2019, 1, 'Đen Xám', '2025-07-21 11:17:36', '2025-07-21 11:17:36', NULL),
(2020, 1, 'Xanh Da Trời', '2025-07-21 11:17:36', '2025-07-21 11:17:36', NULL),
(2021, 1, 'Xanh Đen', '2025-07-21 11:17:36', '2025-07-21 11:17:36', NULL),
(2022, 1, 'Đen bóng', '2025-07-21 11:17:36', '2025-07-21 11:17:36', NULL),
(2023, 1, 'Vàng hồng', '2025-07-21 11:17:36', '2025-07-21 11:17:36', NULL),
(2024, 1, 'Alpine', '2025-07-21 11:17:36', '2025-07-21 11:17:36', NULL),
(2025, 1, 'Trail', '2025-07-21 11:17:36', '2025-07-21 11:17:36', NULL),
(2026, 1, 'Ocean', '2025-07-21 11:17:36', '2025-07-21 11:17:36', NULL),
(2028, 1, 'White', '2025-07-21 11:17:36', '2025-07-21 11:17:36', NULL),
(2029, 1, 'Silver', '2025-07-21 11:17:36', '2025-07-21 11:17:36', NULL),
(2030, 1, 'Pink', '2025-07-21 11:17:36', '2025-07-21 11:17:36', NULL),
(2031, 1, 'Gray', '2025-07-21 11:17:36', '2025-07-21 11:17:36', NULL),
(2032, 1, 'Black', '2025-07-21 11:17:36', '2025-07-21 11:17:36', NULL),
(2033, 1, 'Clear', '2025-07-21 11:17:36', '2025-07-21 11:17:36', NULL),
(2034, 1, 'Brown', '2025-07-21 11:17:36', '2025-07-21 11:17:36', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `wishlists`
--

CREATE TABLE `wishlists` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `variant_id` int(11) NOT NULL,
  `added_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wishlists`
--

INSERT INTO `wishlists` (`id`, `user_id`, `variant_id`, `added_at`) VALUES
(4, 7, 10228, '2025-07-21 11:20:32'),
(5, 7, 10160, '2025-07-21 12:19:40'),
(6, 7, 10203, '2025-07-21 13:57:04'),
(7, 7, 10013, '2025-07-21 15:22:30');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `fk_cart_variant` (`variant_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

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
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `author_id` (`author_id`);

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
  ADD KEY `fk_order_items_variant` (`variant_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email` (`email`);

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
  ADD KEY `product_id` (`product_id`),
  ADD KEY `idx_product_variants_product_id` (`product_id`),
  ADD KEY `idx_product_variants_status` (`status`);

--
-- Indexes for table `promotions`
--
ALTER TABLE `promotions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `purchase_items`
--
ALTER TABLE `purchase_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_order_id` (`purchase_order_id`),
  ADD KEY `fk_purchase_items_variant` (`variant_id`);

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
  ADD KEY `fk_reviews_variant` (`variant_id`);

--
-- Indexes for table `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `sliders`
--
ALTER TABLE `sliders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

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
  ADD KEY `user_id` (`user_id`),
  ADD KEY `fk_wishlists_variant` (`variant_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=150;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1065;

--
-- AUTO_INCREMENT for table `product_variants`
--
ALTER TABLE `product_variants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10312;

--
-- AUTO_INCREMENT for table `promotions`
--
ALTER TABLE `promotions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `purchase_items`
--
ALTER TABLE `purchase_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `site_settings`
--
ALTER TABLE `site_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `sliders`
--
ALTER TABLE `sliders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `user_detail`
--
ALTER TABLE `user_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `variant_attributes`
--
ALTER TABLE `variant_attributes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `variant_attribute_values`
--
ALTER TABLE `variant_attribute_values`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2035;

--
-- AUTO_INCREMENT for table `wishlists`
--
ALTER TABLE `wishlists`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

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
  ADD CONSTRAINT `logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `news`
--
ALTER TABLE `news`
  ADD CONSTRAINT `news_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_order_items_variant` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_products_supplier` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD CONSTRAINT `product_variants_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `purchase_items`
--
ALTER TABLE `purchase_items`
  ADD CONSTRAINT `fk_purchase_items_variant` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchase_items_ibfk_1` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE;

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
  ADD CONSTRAINT `fk_reviews_variant` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

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
  ADD CONSTRAINT `fk_wishlists_variant` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wishlists_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
