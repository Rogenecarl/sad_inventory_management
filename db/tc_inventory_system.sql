-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 08, 2024 at 09:52 AM
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
-- Database: `tc_inventory_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) UNSIGNED NOT NULL,
  `name` varchar(60) NOT NULL,
  `description` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `name`, `description`, `created_at`) VALUES
(23, 'Keyboard', '', '2024-12-03 09:41:19'),
(24, 'Guitar Capo', '', '2024-12-03 09:51:18'),
(25, 'Reeds', '', '2024-12-03 09:54:00'),
(26, 'Digital Drum Pad', 'drums', '2024-12-03 10:14:12'),
(27, 'Lyre', '', '2024-12-03 10:14:21'),
(28, 'Snake Cable', '', '2024-12-03 10:14:29'),
(29, 'Stavol Regulator', '', '2024-12-03 10:14:46'),
(37, 'Amplifier', '', '2024-12-06 10:02:24'),
(38, 'kani', 'ok', '2024-12-06 16:17:17'),
(39, 'andaw bi', 'he', '2024-12-06 16:28:52'),
(41, 'test', 'test rani', '2024-12-06 23:56:21'),
(45, 'test33', 'd', '2024-12-08 08:46:50');

-- --------------------------------------------------------

--
-- Table structure for table `media`
--

CREATE TABLE `media` (
  `id` int(11) UNSIGNED NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_type` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `prod_id` int(11) UNSIGNED NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `quantity` varchar(50) DEFAULT NULL,
  `prod_brand` varchar(100) DEFAULT NULL,
  `prod_model` varchar(100) DEFAULT NULL,
  `sale_price` decimal(25,2) NOT NULL,
  `categorie_id` int(11) UNSIGNED NOT NULL,
  `media_id` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`prod_id`, `photo`, `name`, `quantity`, `prod_brand`, `prod_model`, `sale_price`, `categorie_id`, `media_id`, `created_at`, `updated_at`) VALUES
(50, NULL, 'Yamaha - PSR-E273', '0', 'Yamahas', 'PSR-E273', 12750.00, 23, 0, '2024-12-03 09:42:30', '2024-12-07 04:04:51'),
(51, NULL, 'Yamaha - EZ-300', '2', 'Yamaha', 'EZ-300', 24800.00, 23, 0, '2024-12-03 09:43:09', '2024-12-08 07:54:23'),
(52, NULL, 'Yamaha - PSR-E373', '3', 'Yamaha', 'E373', 17500.00, 23, 0, '2024-12-03 09:43:56', '2024-12-03 09:43:56'),
(53, NULL, 'Piaggero - UP-15', '0', 'Yamahad', 'UP-15', 10300.00, 23, 0, '2024-12-03 09:49:25', '2024-12-07 04:51:43'),
(54, NULL, 'Yamaha - PSR-E473', '3', 'Yamaha', 'E373', 30800.00, 23, 0, '2024-12-03 09:50:54', '2024-12-03 09:50:54'),
(55, NULL, 'Blueridge', '7', 'Capo', 'Blueridge', 120.00, 24, 0, '2024-12-03 09:52:23', '2024-12-03 09:52:23'),
(56, NULL, 'Chard', '30', 'Capo', 'Chart', 120.00, 24, 0, '2024-12-03 09:52:48', '2024-12-03 09:52:48'),
(57, NULL, 'Fender', '28', 'Capo', 'Fender', 260.00, 24, 0, '2024-12-03 09:53:23', '2024-12-03 09:53:23'),
(58, NULL, 'Alice', '6', 'Capo', 'Alice', 380.00, 24, 0, '2024-12-03 09:53:47', '2024-12-03 09:53:47'),
(59, NULL, 'Lazer - CR', '1', 'Lazer', 'CR', 75.00, 24, 0, '2024-12-03 09:54:51', '2024-12-03 09:54:51'),
(60, NULL, 'Lazer - ASR', '1', 'Lazer', 'ASR', 75.00, 24, 0, '2024-12-03 09:55:25', '2024-12-03 09:55:25'),
(61, NULL, 'Lazer - SSR', '1', 'Lazer', 'SSR', 75.00, 24, 0, '2024-12-03 09:56:00', '2024-12-03 09:56:00'),
(62, NULL, 'Lazer - TSR', '1', 'Lazer', 'TSR', 85.00, 24, 0, '2024-12-03 09:56:33', '2024-12-03 09:56:33'),
(63, NULL, 'Nux - DP-2000', '3', 'Nux', 'DP-2000', 18500.00, 26, 0, '2024-12-03 10:15:37', '2024-12-03 10:15:37'),
(64, NULL, 'Yamaha - DD - 75', '0', 'Yamaha', 'DD-75', 19500.00, 26, 0, '2024-12-03 10:16:12', '2024-12-03 10:16:12'),
(68, NULL, 'Sikat - Medium', '10', 'Sikat', 'Medium', 2500.00, 27, 0, '2024-12-03 10:20:34', '2024-12-03 10:20:34'),
(70, NULL, 'Sikat - Large', '7', 'Sikat', 'Largel', 3420.00, 27, 0, '2024-12-03 10:31:35', '2024-12-07 04:36:57'),
(71, NULL, 'Sikat - Deluxe', '6', 'Sikat', 'Deluxe', 3570.00, 27, 0, '2024-12-03 10:32:15', '2024-12-06 15:41:32'),
(84, '1733533021_zamboanga.jpg', 'testss', '12', 'testss', 'testssw', 210.00, 41, 0, '2024-12-07 00:57:01', '2024-12-08 06:41:06'),
(86, NULL, 'test11', '50', 'test1', 'test1', 100.00, 41, 0, '2024-12-08 07:40:26', '2024-12-08 07:41:22');

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `sales_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `qty` int(11) NOT NULL,
  `total_price` decimal(25,2) NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`sales_id`, `product_id`, `qty`, `total_price`, `date`) VALUES
(18, 51, 1, 23800.00, '2024-12-07'),
(19, 50, 1, 12750.00, '2024-12-07'),
(20, 51, 1, 23800.00, '2024-12-07'),
(21, 56, 3, 360.00, '2024-12-07'),
(22, 51, 1, 23800.00, '2024-12-07'),
(23, 84, 1, 210.00, '2024-12-07'),
(24, 63, 1, 18500.00, '2024-12-07'),
(25, 64, 1, 19500.00, '2024-12-07'),
(26, 53, 1, 10300.00, '2024-12-07'),
(27, 55, 1, 120.00, '2024-12-07'),
(28, 56, 1, 120.00, '2024-12-07'),
(29, 56, 1, 120.00, '2024-12-07'),
(30, 56, 1, 120.00, '2024-12-07'),
(31, 56, 1, 120.00, '2024-12-07'),
(32, 56, 2, 240.00, '2024-12-07'),
(33, 57, 1, 260.00, '2024-12-07'),
(34, 56, 1, 120.00, '2024-12-07'),
(35, 56, 1, 120.00, '2024-12-07'),
(36, 57, 1, 260.00, '2024-12-07'),
(37, 55, 1, 120.00, '2024-12-07'),
(38, 51, 1, 23800.00, '2024-12-08'),
(39, 84, 1, 210.00, '2024-12-08'),
(40, 56, 1, 120.00, '2024-12-08'),
(41, 55, 1, 120.00, '2024-12-08'),
(42, 56, 1, 120.00, '2024-12-08'),
(43, 86, 1, 100.00, '2024-12-08'),
(44, 51, 1, 24800.00, '2024-12-08'),
(45, 52, 1, 17500.00, '2024-12-08');

-- --------------------------------------------------------

--
-- Table structure for table `stockhistory`
--

CREATE TABLE `stockhistory` (
  `id` int(11) NOT NULL,
  `prod_id` int(10) UNSIGNED DEFAULT NULL,
  `quantity_added` int(11) DEFAULT NULL,
  `previous_stock` int(11) DEFAULT NULL,
  `new_stock` int(11) DEFAULT NULL,
  `price` decimal(25,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `stockhistory`
--

INSERT INTO `stockhistory` (`id`, `prod_id`, `quantity_added`, `previous_stock`, `new_stock`, `price`, `created_at`, `remarks`) VALUES
(14, 71, 4, 2, 6, 3570.00, '2024-12-06 15:41:32', NULL),
(31, 84, 1, 10, 11, 209.00, '2024-12-07 00:57:25', NULL),
(32, 84, 0, 11, 11, 210.00, '2024-12-07 00:57:55', NULL),
(33, 50, 10, -2, 8, 12750.00, '2024-12-07 03:43:11', NULL),
(34, 50, 1, -1, 0, 12750.00, '2024-12-07 04:04:37', NULL),
(35, 50, 1, 0, 1, 12750.00, '2024-12-07 04:04:47', NULL),
(36, 53, 2, -1, 1, 10300.00, '2024-12-07 04:51:32', NULL),
(38, 84, 8, 5, 13, 210.00, '2024-12-08 06:41:06', NULL),
(39, 86, 1, 50, 51, 100.00, '2024-12-08 07:41:22', NULL),
(40, 51, 0, 3, 3, 24800.00, '2024-12-08 07:54:23', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `User_id` int(11) UNSIGNED NOT NULL,
  `name` varchar(60) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_level` int(11) NOT NULL,
  `image` varchar(255) DEFAULT 'no_image.jpg',
  `status` int(1) NOT NULL,
  `last_login` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`User_id`, `name`, `username`, `password`, `user_level`, `image`, `status`, `last_login`) VALUES
(52, 'admin', 'admin', 'd033e22ae348aeb5660fc2140aec35850c4da997', 2, 'no_image.jpg', 1, '2024-12-06 11:39:37'),
(54, 'admin2', 'admin2', '315f166c5aca63a157f7d41007675cb44a948b33', 1, 'no_image.jpg', 1, '2024-12-08 14:40:31'),
(61, 'bro', 'bro', '71424f1db376666e5625da3dc57d0ccef35244a2', 1, 'no_image.jpg', 1, NULL),
(63, 'dev', 'dev', '34c6fceca75e456f25e7e99531e2425c6c1de443', 1, 'no_image.jpg', 1, '2024-12-08 16:52:09'),
(64, 'test', 'test', 'a94a8fe5ccb19ba61c4c0873d391e987982fbbd3', 1, 'no_image.jpg', 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_groups`
--

CREATE TABLE `user_groups` (
  `id` int(11) NOT NULL,
  `group_name` varchar(150) NOT NULL,
  `group_level` int(11) NOT NULL,
  `group_status` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `user_groups`
--

INSERT INTO `user_groups` (`id`, `group_name`, `group_level`, `group_status`) VALUES
(1, 'Admin', 1, 1),
(2, 'special', 2, 1),
(3, 'User', 3, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `media`
--
ALTER TABLE `media`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`prod_id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `categorie_id` (`categorie_id`),
  ADD KEY `media_id` (`media_id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`sales_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `stockhistory`
--
ALTER TABLE `stockhistory`
  ADD PRIMARY KEY (`id`),
  ADD KEY `prod_id` (`prod_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`User_id`),
  ADD KEY `user_level` (`user_level`);

--
-- Indexes for table `user_groups`
--
ALTER TABLE `user_groups`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `group_level` (`group_level`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `media`
--
ALTER TABLE `media`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `prod_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `sales_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `stockhistory`
--
ALTER TABLE `stockhistory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `User_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `user_groups`
--
ALTER TABLE `user_groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `FK_products` FOREIGN KEY (`categorie_id`) REFERENCES `categories` (`category_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `sales_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`prod_id`) ON DELETE CASCADE;

--
-- Constraints for table `stockhistory`
--
ALTER TABLE `stockhistory`
  ADD CONSTRAINT `stockhistory_ibfk_1` FOREIGN KEY (`prod_id`) REFERENCES `products` (`prod_id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `FK_user` FOREIGN KEY (`user_level`) REFERENCES `user_groups` (`group_level`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
