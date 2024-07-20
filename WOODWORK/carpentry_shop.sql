-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 13, 2024 at 05:45 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `carpentry_shop`
--

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `customer_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `order_count` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`customer_id`, `first_name`, `last_name`, `email`, `phone_number`, `address`, `order_count`) VALUES
(1, 'Kelvin', 'Thuku', 'paetmandry@gmail.com', '0758831128', 'Frest House', 5),
(2, 'ROSEMARY', 'WARIARA', 'joegatimu20@gmail.com', '07998864337', '2768', 1),
(3, 'Simon', 'Karianja', 'kamnjoro@gmail.com', '0789388993', 'Kihunguro', 1),
(4, 'ROSEMARY', 'WARIARA', 'denniswahometnk@gmail.com', '07998864337', '2768', 1),
(5, 'Wahome', 'Ndiritu', 'sawcrafts@gmail.com', '0799987174', 'Gwa Kairo', 1);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `order_date` date NOT NULL,
  `delivery_date` date DEFAULT NULL,
  `order_status` varchar(20) NOT NULL,
  `order_details` text DEFAULT NULL,
  `pick_up_date` date DEFAULT NULL,
  `picked_status` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `customer_id`, `order_date`, `delivery_date`, `order_status`, `order_details`, `pick_up_date`, `picked_status`) VALUES
(1, 1, '2024-03-12', NULL, 'Pending', 'a:4:{s:12:\"product_type\";s:5:\"Table\";s:9:\"wood_type\";s:8:\"mahogany\";s:10:\"dimensions\";s:24:\"6*3 Insert a side mirror\";s:8:\"quantity\";i:2;}', NULL, 1),
(2, 1, '2024-03-13', NULL, 'Pending', '0', '2024-03-16', 1),
(3, 2, '2024-03-13', NULL, 'Pending', '0', '2024-03-16', 1),
(4, 3, '2024-03-13', NULL, 'Pending', '0', '2024-03-16', 1),
(5, 4, '2024-03-13', NULL, 'Pending', '0', '2024-03-16', 1),
(6, 5, '2024-03-13', NULL, 'Pending', '0', '2024-03-16', 0);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `product_type` varchar(50) NOT NULL,
  `description` varchar(2000) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `image` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_name`, `product_type`, `description`, `price`, `image`) VALUES
(2, 'DESK', 'wooden', '0', 25.00, 'image/CAT11.png'),
(3, 'DESK', 'wooden', '0', 25.00, 'image/CAT11.png'),
(4, 'DESK', 'wooden', '0', 25.00, 'image/CAT11.png'),
(5, 'door', 'wooden', '0', 2.00, 'image/CAT11.png'),
(6, 'chair', 'metal', '6 legs', 20.00, 'image/football-watermark.png');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customer_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
