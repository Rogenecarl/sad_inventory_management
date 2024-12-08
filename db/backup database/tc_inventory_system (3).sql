-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 02, 2024 at 11:27 AM
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
(2, 'Raw Materials', '2024-11-25 17:22:00'),
(3, 'Finished Goods', '2024-11-25 17:22:00'),
(4, 'Packing Materials', '2024-11-25 17:22:00'),
(5, 'Machinery', '2024-11-25 17:22:00'),
(6, 'Work in Progress', '2024-11-25 17:22:00'),
(8, 'Stationery Items', '2024-11-25 17:22:00');

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
(2, NULL, 'Box Varieties', '11969', '55.00', 'g', 12000.00, 4, 0, '2021-04-04 10:44:52', '2024-11-28 02:21:26'),
(3, NULL, 'Wheat', '68', '2.00', NULL, 5.00, 2, 0, '2021-04-04 10:48:53', '2024-11-25 17:30:58'),
(4, NULL, 'Timber', '1194', '780.00', NULL, 1069.00, 2, 0, '2021-04-04 11:03:23', '2024-11-25 17:30:58'),
(5, NULL, 'W1848 Oscillating Floor Drill Press', '25', '299.00', NULL, 494.00, 5, 0, '2021-04-04 11:11:30', '2024-11-25 17:30:58'),
(6, NULL, 'Portable Band Saw XBP02Z', '42', '280.00', NULL, 415.00, 5, 0, '2021-04-04 11:13:35', '2024-11-25 17:30:58'),
(7, NULL, 'Life Breakfast Cereal-3 Pk', '107', '3.00', NULL, 7.00, 3, 0, '2021-04-04 11:15:38', '2024-11-25 17:30:58'),
(8, NULL, 'Chicken of the Sea Sardines W', '110', '13.00', NULL, 20.00, 3, 0, '2021-04-04 11:17:11', '2024-11-25 17:30:58'),
(9, NULL, 'Disney Woody - Action Figure', '67', '29.00', NULL, 55.00, 3, 0, '2021-04-04 11:19:20', '2024-11-25 17:30:58'),
(10, NULL, 'Hasbro Marvel Legends Series Toys', '103', '219.00', NULL, 322.00, 3, 0, '2021-04-04 11:20:28', '2024-11-25 17:30:58'),
(11, NULL, 'Packing Chips', '78', '21.00', NULL, 31.00, 4, 0, '2021-04-04 11:25:22', '2024-11-25 17:30:58'),
(12, NULL, 'Classic Desktop Tape Dispenser 38', '160', '5.00', NULL, 10.00, 8, 0, '2021-04-04 11:48:01', '2024-11-25 17:30:58'),
(13, NULL, 'Small Bubble Cushioning Wrap', '199', '8.00', NULL, 19.00, 4, 0, '2021-04-04 11:49:00', '2024-11-25 17:30:58'),
(15, NULL, 'hehesdfssdf', '20', 'hehesfsdf', 'hehesdfsdf', 1238.00, 4, 0, '2024-11-26 08:05:50', '2024-11-26 08:06:28'),
(25, NULL, 'testsdf', '12', 'sdf', 'dsf', 32.00, 5, 0, '2024-11-27 08:56:32', '2024-11-27 08:56:32'),
(32, NULL, 's', '213', 'lp', 'kani', 21.00, 3, 0, '2024-11-27 19:28:16', '2024-11-27 19:28:16'),
(36, NULL, 'okay', '0', 'okay', 'okay', 1.00, 3, 0, '2024-11-28 07:31:12', '2024-11-28 07:31:12'),
(37, NULL, 'brom', '45', 'fef', 'sdfs', 1000.00, 4, 0, '2024-11-28 08:01:42', '2024-11-28 08:01:42'),
(38, NULL, 'kani', '9', 'sdf', 'sdf', 619.00, 4, 0, '2024-11-28 08:05:50', '2024-11-28 08:05:50');

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `sales_id` int(11) UNSIGNED NOT NULL,
  `product_id` int(11) UNSIGNED NOT NULL,
  `qty` int(11) NOT NULL,
  `total_price` decimal(25,2) NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`sales_id`, `product_id`, `qty`, `total_price`, `date`) VALUES
(2, 3, 4, 20.00, '2024-11-22'),
(3, 10, 6, 1932.00, '2021-04-04'),
(4, 6, 2, 830.00, '2021-04-04'),
(5, 12, 5, 50.00, '2021-04-04'),
(6, 13, 21, 399.00, '2021-04-04'),
(7, 7, 5, 35.00, '2021-04-04'),
(8, 9, 2, 110.00, '2021-04-04'),
(24, 36, 10, 10.00, '2024-11-28'),
(25, 37, 5, 5000.00, '2024-11-28'),
(26, 38, 1, 619.00, '2024-11-28'),
(27, 2, 6, 72000.00, '2024-08-12'),
(28, 4, 6, 6414.00, '2024-12-25'),
(29, 5, 1, 494.00, '2025-01-15');

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
(1, 'Harry Denn', 'admin', 'd033e22ae348aeb5660fc2140aec35850c4da997', 1, 'no_image.png', 1, '2024-12-02 18:17:16'),
(2, 'John Walker', 'special', 'ba36b97a41e7faf742ab09bf88405ac04f99599a', 2, 'no_image.png', 1, '2024-11-23 10:52:23'),
(3, 'Christopher', 'user', '12dea96fec20593566ab75692c9949596833adc9', 3, 'no_image.png', 1, '2021-04-04 19:54:46'),
(4, 'Natie Williams', 'natie', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 3, 'no_image.png', 1, NULL),
(51, 'dev', 'dev', '34c6fceca75e456f25e7e99531e2425c6c1de443', 1, 'no_image.jpg', 1, NULL);

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
  MODIFY `category_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `media`
--
ALTER TABLE `media`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `prod_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `sales_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `User_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

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
