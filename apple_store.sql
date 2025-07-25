-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 20, 2025 at 04:38 PM
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

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `variant_id`, `quantity`, `added_at`) VALUES
(9, 7, 11, 1, '2025-07-19 17:28:47');

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
(6, 'Phụ kiện', NULL, 1, 'phu-kien'),
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
(110, NULL, 'visit_index', 'User visited customer index page', '2025-07-20 14:38:11');

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
(1, 'ORD20250718123456', 7, '2025-07-18 15:28:03', 27990000.00, 'Pending', 'Nhà giàu nhất Kim Bôi, Xã Vĩnh Đồng, Huyện Kim Bôi, Tỉnh Hòa Bình', 'Hoa Quý', 'kiogit30@gmail.com', '0127865412', NULL, 'bank');

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
(1, 1, 13, 1, 27990000.00);

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
(1, 'iPhone 16 Pro Max', 34990000.00, 'iPhone 16 Pro Max với chip A18 Bionic, camera cải tiến và pin vượt trội.', 'iphone16promax.png', 7, 1, '2025-07-15 14:52:45', 'IP16PROMAX', '2025-07-15 14:52:45', 1),
(2, 'iPhone 16 Pro', 30990000.00, 'iPhone 16 Pro với chip A18 Bionic và thiết kế cao cấp.', 'iphone16pro.png', 7, 1, '2025-07-15 14:52:45', 'IP16PRO', '2025-07-15 14:52:45', 1),
(3, 'iPhone 16', 22990000.00, 'iPhone 16 với hiệu năng mạnh mẽ và camera nâng cấp.', 'iphone16.png', 7, 1, '2025-07-15 14:52:45', 'IP16', '2025-07-15 14:52:45', 1),
(4, 'iPhone 15 Pro Max', 29990000.00, 'iPhone 15 Pro Max với thiết kế titan, chip A17 Pro và hệ thống camera chuyên nghiệp.', 'iphone15promax.png', 7, 1, '2025-07-15 14:52:45', 'IP15PROMAX', '2025-07-15 14:52:45', 1),
(5, 'iPhone 15 Pro', 26990000.00, 'iPhone 15 Pro với thiết kế titan, chip A17 Pro mạnh mẽ.', 'iphone15pro.png', 7, 1, '2025-07-15 14:52:45', 'IP15PRO', '2025-07-15 14:52:45', 1),
(6, 'iPhone 15 Plus', 22990000.00, 'iPhone 15 Plus với màn hình lớn và Dynamic Island.', 'iphone15plus.png', 7, 1, '2025-07-15 14:52:45', 'IP15PLUS', '2025-07-15 14:52:45', 1),
(7, 'iPhone 15', 19990000.00, 'iPhone 15 với cổng USB-C và Dynamic Island.', 'iphone15.png', 7, 1, '2025-07-15 14:52:45', 'IP15', '2025-07-15 14:52:45', 1),
(8, 'iPhone 14 Pro Max', 24990000.00, 'iPhone 14 Pro Max với Dynamic Island và camera 48MP.', 'iphone14promax.png', 8, 1, '2025-07-15 14:52:45', 'IP14PROMAX', '2025-07-15 14:52:45', 1),
(9, 'iPhone 14 Pro', 21990000.00, 'iPhone 14 Pro với Dynamic Island và hiệu năng ấn tượng.', 'iphone14pro.png', 8, 1, '2025-07-15 14:52:45', 'IP14PRO', '2025-07-15 14:52:45', 1),
(10, 'iPhone 14 Plus', 18990000.00, 'iPhone 14 Plus với màn hình lớn và pin tốt.', 'iphone14plus.png', 8, 1, '2025-07-15 14:52:45', 'IP14PLUS', '2025-07-15 14:52:45', 1),
(11, 'iPhone 14', 16990000.00, 'iPhone 14 với camera cải tiến và tính năng an toàn.', 'iphone14.png', 8, 1, '2025-07-15 14:52:45', 'IP14', '2025-07-15 14:52:45', 1),
(12, 'MacBook Pro M3 Pro 14-inch', 49990000.00, 'MacBook Pro 14 inch với chip M3 Pro mang lại hiệu năng chuyên nghiệp.', 'macbookprom3pro14.png', 2, 1, '2025-07-15 14:52:45', 'MBPROM3PRO14', '2025-07-15 14:52:45', 1),
(13, 'MacBook Air M3 13-inch', 27990000.00, 'MacBook Air 13 inch với chip M3 mang lại hiệu năng vượt trội và thời lượng pin cả ngày.', 'macbookairm3_13.png', 2, 1, '2025-07-15 14:52:45', 'MBAIRM3-13', '2025-07-15 14:52:45', 1);

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
(1, 1, 'IP16PM-256GB-NATURAL', 34990000.00, 50, 'iphone16promax_natural.png', '2025-07-15 14:52:45', '2025-07-15 14:52:45', 1),
(2, 1, 'IP16PM-512GB-NATURAL', 37990000.00, 40, 'iphone16promax_natural.png', '2025-07-15 14:52:45', '2025-07-15 14:52:45', 1),
(3, 1, 'IP16PM-256GB-BLACK', 34990000.00, 45, 'iphone16promax_black.png', '2025-07-15 14:52:45', '2025-07-15 14:52:45', 1),
(4, 1, 'IP16PM-512GB-BLACK', 37990000.00, 35, 'iphone16promax_black.png', '2025-07-15 14:52:45', '2025-07-15 14:52:45', 1),
(5, 4, 'IP15PM-256GB-NATURAL', 29990000.00, 60, 'iphone15promax_natural.png', '2025-07-15 14:52:45', '2025-07-15 14:52:45', 1),
(6, 4, 'IP15PM-512GB-BLUE', 32990000.00, 45, 'iphone15promax_blue.png', '2025-07-15 14:52:45', '2025-07-15 14:52:45', 1),
(7, 7, 'IP15-128GB-PINK', 19990000.00, 70, 'iphone15_pink.png', '2025-07-15 14:52:45', '2025-07-15 14:52:45', 1),
(8, 7, 'IP15-256GB-GREEN', 22990000.00, 55, 'iphone15_green.png', '2025-07-15 14:52:45', '2025-07-15 14:52:45', 1),
(9, 9, 'IP14PRO-128GB-SPACEBLACK', 21990000.00, 31, 'iphone14pro_spaceblack.png', '2025-07-15 14:52:45', '2025-07-17 17:47:30', 1),
(10, 9, 'IP14PRO-256GB-SILVER', 24990000.00, 25, 'iphone14pro_silver.png', '2025-07-15 14:52:45', '2025-07-15 14:52:45', 1),
(11, 12, 'MBPROM3PRO14-16GB-512SSD-SILVER', 49990000.00, 15, 'macbookprom3pro14_silver.png', '2025-07-15 14:52:45', '2025-07-15 14:52:45', 1),
(12, 12, 'MBPROM3PRO14-32GB-1TBSSD-SPACEBLACK', 59990000.00, 10, 'macbookprom3pro14_spaceblack.png', '2025-07-15 14:52:45', '2025-07-15 14:52:45', 1),
(13, 13, 'MBAIRM3-13-8GB-256SSD-SILVER', 27990000.00, 1031, 'macbookairm3_silver.png', '2025-07-15 14:52:45', '2025-07-19 18:30:24', 1),
(14, 13, 'MBAIRM3-13-8GB-512SSD-SPACEGRAY', 30990000.00, 25, 'macbookairm3_spacegray.png', '2025-07-15 14:52:45', '2025-07-15 14:52:45', 1);

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
(1, 4),
(1, 18),
(2, 4),
(2, 19),
(3, 1),
(3, 18),
(4, 1),
(4, 19),
(5, 4),
(5, 18),
(6, 3),
(6, 19),
(7, 8),
(7, 17),
(8, 9),
(8, 18),
(9, 1),
(9, 17),
(10, 7),
(10, 18),
(11, 7),
(11, 24),
(12, 1),
(12, 26),
(13, 7),
(13, 22),
(14, 6),
(14, 23);

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
(4, 7, 'Hoa', 'Quý', 'assets/avatars/user_7_1752941051.png', 'Nhà giàu nhất Kim Bôi', '0127865412', 'female', '2005-03-26');

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
(1, 'Color', 'Màu sắc', 'color_picker', '2025-07-15 14:52:45', '2025-07-15 14:52:45'),
(2, 'Storage', 'Dung lượng', 'text', '2025-07-15 14:52:45', '2025-07-15 14:52:45');

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
(1, 1, 'Space Black', '2025-07-15 14:52:45', '2025-07-15 14:52:45', NULL),
(2, 1, 'White Titanium', '2025-07-15 14:52:45', '2025-07-15 14:52:45', NULL),
(3, 1, 'Blue Titanium', '2025-07-15 14:52:45', '2025-07-15 14:52:45', NULL),
(4, 1, 'Natural Titanium', '2025-07-15 14:52:45', '2025-07-15 14:52:45', NULL),
(5, 1, 'Starlight', '2025-07-15 14:52:45', '2025-07-15 14:52:45', NULL),
(6, 1, 'Midnight', '2025-07-15 14:52:45', '2025-07-15 14:52:45', NULL),
(7, 1, 'Silver', '2025-07-15 14:52:45', '2025-07-15 14:52:45', NULL),
(8, 1, 'Pink', '2025-07-15 14:52:45', '2025-07-15 14:52:45', NULL),
(9, 1, 'Green', '2025-07-15 14:52:45', '2025-07-15 14:52:45', NULL),
(10, 1, 'Red', '2025-07-15 14:52:45', '2025-07-15 14:52:45', NULL),
(11, 1, 'Yellow', '2025-07-15 14:52:45', '2025-07-15 14:52:45', NULL),
(12, 1, 'Blue', '2025-07-15 14:52:45', '2025-07-15 14:52:45', NULL),
(13, 1, 'Purple', '2025-07-15 14:52:45', '2025-07-15 14:52:45', NULL),
(14, 1, 'Graphite', '2025-07-15 14:52:45', '2025-07-15 14:52:45', NULL),
(15, 1, 'Sierra Blue', '2025-07-15 14:52:45', '2025-07-15 14:52:45', NULL),
(16, 2, '64GB', '2025-07-15 14:52:45', '2025-07-15 14:52:45', NULL),
(17, 2, '128GB', '2025-07-15 14:52:45', '2025-07-15 14:52:45', NULL),
(18, 2, '256GB', '2025-07-15 14:52:45', '2025-07-15 14:52:45', NULL),
(19, 2, '512GB', '2025-07-15 14:52:45', '2025-07-15 14:52:45', NULL),
(20, 2, '1TB', '2025-07-15 14:52:45', '2025-07-15 14:52:45', NULL),
(21, 2, '2TB', '2025-07-15 14:52:45', '2025-07-15 14:52:45', NULL),
(22, 2, '8GB RAM / 256GB SSD', '2025-07-15 14:52:45', '2025-07-15 14:52:45', NULL),
(23, 2, '8GB RAM / 512GB SSD', '2025-07-15 14:52:45', '2025-07-15 14:52:45', NULL),
(24, 2, '16GB RAM / 512GB SSD', '2025-07-15 14:52:45', '2025-07-15 14:52:45', NULL),
(25, 2, '16GB RAM / 1TB SSD', '2025-07-15 14:52:45', '2025-07-15 14:52:45', NULL),
(26, 2, '32GB RAM / 1TB SSD', '2025-07-15 14:52:45', '2025-07-15 14:52:45', NULL),
(27, 2, '32GB RAM / 2TB SSD', '2025-07-15 14:52:45', '2025-07-15 14:52:45', NULL);

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
(1, 7, 13, '2025-07-18 15:28:33');

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
-- Indexes for table `product_variant_attribute_links`
--
ALTER TABLE `product_variant_attribute_links`
  ADD PRIMARY KEY (`variant_id`,`attribute_value_id`),
  ADD KEY `attribute_value_id` (`attribute_value_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `product_variants`
--
ALTER TABLE `product_variants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `wishlists`
--
ALTER TABLE `wishlists`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
-- Constraints for table `product_variant_attribute_links`
--
ALTER TABLE `product_variant_attribute_links`
  ADD CONSTRAINT `product_variant_attribute_links_ibfk_1` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_variant_attribute_links_ibfk_2` FOREIGN KEY (`attribute_value_id`) REFERENCES `variant_attribute_values` (`id`) ON DELETE CASCADE;

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
