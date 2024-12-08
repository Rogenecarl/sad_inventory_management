-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 03, 2024 at 12:04 PM
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `name`, `created_at`) VALUES
(23, 'Keyboard', '2024-12-03 09:41:19'),
(24, 'Guitar Capo', '2024-12-03 09:51:18'),
(25, 'Reeds', '2024-12-03 09:54:00'),
(26, 'Digital Drum Pad', '2024-12-03 10:14:12'),
(27, 'Lyre', '2024-12-03 10:14:21'),
(28, 'Snake Cable', '2024-12-03 10:14:29'),
(29, 'Stavol Regulator', '2024-12-03 10:14:46');

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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`prod_id`, `photo`, `name`, `quantity`, `prod_brand`, `prod_model`, `sale_price`, `categorie_id`, `media_id`, `created_at`, `updated_at`) VALUES
(50, NULL, 'Yamaha - PSR-E273', '5', 'Yamaha', 'PSR-E273', 12750.00, 23, 0, '2024-12-03 09:42:30', '2024-12-03 09:42:30'),
(51, NULL, 'Yamaha - EZ-300', '10', 'Yamaha', 'EZ-300', 23800.00, 23, 0, '2024-12-03 09:43:09', '2024-12-03 09:43:09'),
(52, NULL, 'Yamaha - PSR-E373', '5', 'Yamaha', 'E373', 17500.00, 23, 0, '2024-12-03 09:43:56', '2024-12-03 09:43:56'),
(53, NULL, 'Piaggero - UP-15', '5', 'Yamaha', 'UP-15', 10300.00, 23, 0, '2024-12-03 09:49:25', '2024-12-03 09:49:25'),
(54, NULL, 'Yamaha - PSR-E473', '3', 'Yamaha', 'E373', 30800.00, 23, 0, '2024-12-03 09:50:54', '2024-12-03 09:50:54'),
(55, NULL, 'Blueridge', '20', 'Capo', 'Blueridge', 120.00, 24, 0, '2024-12-03 09:52:23', '2024-12-03 09:52:23'),
(56, NULL, 'Chard', '45', 'Capo', 'Chart', 120.00, 24, 0, '2024-12-03 09:52:48', '2024-12-03 09:52:48'),
(57, NULL, 'Fender', '280', 'Capo', 'Fender', 260.00, 24, 0, '2024-12-03 09:53:23', '2024-12-03 09:53:23'),
(58, NULL, 'Alice', '7', 'Capo', 'Alice', 380.00, 24, 0, '2024-12-03 09:53:47', '2024-12-03 09:53:47'),
(59, NULL, 'Lazer - CR', '1', 'Lazer', 'CR', 75.00, 24, 0, '2024-12-03 09:54:51', '2024-12-03 09:54:51'),
(60, NULL, 'Lazer - ASR', '1', 'Lazer', 'ASR', 75.00, 24, 0, '2024-12-03 09:55:25', '2024-12-03 09:55:25'),
(61, NULL, 'Lazer - SSR', '1', 'Lazer', 'SSR', 75.00, 24, 0, '2024-12-03 09:56:00', '2024-12-03 09:56:00'),
(62, NULL, 'Lazer - TSR', '1', 'Lazer', 'TSR', 85.00, 24, 0, '2024-12-03 09:56:33', '2024-12-03 09:56:33'),
(63, NULL, 'Nux - DP-2000', '4', 'Nux', 'DP-2000', 18500.00, 26, 0, '2024-12-03 10:15:37', '2024-12-03 10:15:37'),
(64, NULL, 'Yamaha - DD - 75', '1', 'Yamaha', 'DD-75', 19500.00, 26, 0, '2024-12-03 10:16:12', '2024-12-03 10:16:12'),
(65, NULL, 'Sikat - Small', '1', 'sikat', 'small', 2100.00, 27, 0, '2024-12-03 10:17:04', '2024-12-03 10:17:04'),
(68, NULL, 'Sikat - Medium', '10', 'Sikat', 'Medium', 2500.00, 27, 0, '2024-12-03 10:20:34', '2024-12-03 10:20:34'),
(70, NULL, 'Sikat - Large', '7', 'Sikat', 'Large', 3420.00, 27, 0, '2024-12-03 10:31:35', '2024-12-03 10:31:35'),
(71, NULL, 'Sikat - Deluxe', '2', 'Sikat', 'Deluxe', 3570.00, 27, 0, '2024-12-03 10:32:15', '2024-12-03 10:32:15'),
(72, NULL, 'IMIX - 12x4 30m', '1', 'IMIX', '12x4 30m', 15850.00, 28, 0, '2024-12-03 10:33:34', '2024-12-03 10:33:34'),
(73, NULL, 'IMIX - 16x4 30m', '1', 'IMIX', '16x4 30m', 18800.00, 28, 0, '2024-12-03 10:45:10', '2024-12-03 10:45:10');

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `sales_id` int(11) UNSIGNED NOT NULL,
  `product_id` int(11) UNSIGNED NOT NULL,
  `qty` int(11) NOT NULL,
  `total_price` decimal(25,2) NOT NULL,
  `date` date NOT NULL,
  `sales_month` int(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`sales_id`, `product_id`, `qty`, `total_price`, `date`, `sales_month`) VALUES
(43, 50, 5, 63750.00, '2024-01-17', 1),
(44, 71, 5, 17850.00, '2024-01-17', 1),
(45, 71, 3, 10710.00, '2024-02-12', 2);

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
(51, 'dev', 'dev', '34c6fceca75e456f25e7e99531e2425c6c1de443', 1, 'no_image.jpg', 1, '2024-12-03 17:40:54'),
(52, 'admin', 'admin', 'd033e22ae348aeb5660fc2140aec35850c4da997', 2, 'no_image.jpg', 1, NULL),
(53, 'dev2', 'dev2', '36f6e4b16c040980088c91e4d0f84ed4813af952', 1, 'no_image.jpg', 1, NULL),
(54, 'admin2', 'admin2', '315f166c5aca63a157f7d41007675cb44a948b33', 1, 'no_image.jpg', 1, NULL);

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
  MODIFY `category_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `media`
--
ALTER TABLE `media`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `prod_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `sales_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `User_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

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
  ADD CONSTRAINT `SK` FOREIGN KEY (`product_id`) REFERENCES `products` (`prod_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `FK_user` FOREIGN KEY (`user_level`) REFERENCES `user_groups` (`group_level`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
