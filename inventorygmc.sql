-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 02, 2025 at 02:21 AM
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
-- Database: `inventorygmc`
--

-- --------------------------------------------------------

--
-- Table structure for table `fixed_assets`
--

CREATE TABLE `fixed_assets` (
  `asset_id` int(11) NOT NULL,
  `stockin_item` varchar(255) NOT NULL,
  `owner` varchar(50) NOT NULL,
  `qty` int(11) NOT NULL,
  `department` varchar(50) NOT NULL,
  `location` varchar(255) NOT NULL,
  `serial_number` varchar(255) NOT NULL,
  `remarks` text DEFAULT NULL,
  `date_assigned` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fixed_assets`
--

INSERT INTO `fixed_assets` (`asset_id`, `stockin_item`, `owner`, `qty`, `department`, `location`, `serial_number`, `remarks`, `date_assigned`) VALUES
(37, 'Monitor (ASUS ROG Swift PG32UQX)', 'Rosille Mae Lumangtad', 1, '', 'HIMS Office', '', NULL, '2025-04-01 14:13:13'),
(38, 'UPS Secure', 'Desiree Darish', 1, '', 'Laboratory', '', NULL, '2025-04-01 15:34:37'),
(39, 'Welding Machine', 'Prince Sayre', 4, '', 'HIMS Office', '', NULL, '2025-04-01 15:44:08');

-- --------------------------------------------------------

--
-- Table structure for table `request`
--

CREATE TABLE `request` (
  `req_id` int(11) NOT NULL,
  `req_number` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `item_request` varchar(100) NOT NULL,
  `qty` int(11) NOT NULL,
  `department` varchar(255) NOT NULL,
  `date` date DEFAULT current_timestamp(),
  `status` tinyint(1) DEFAULT 0,
  `issued_by` varchar(250) DEFAULT NULL,
  `date_issued` date DEFAULT NULL,
  `date_approved` date DEFAULT NULL,
  `date_declined` date DEFAULT NULL,
  `is_posted` tinyint(1) NOT NULL DEFAULT 0,
  `declined_by` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `request`
--

INSERT INTO `request` (`req_id`, `req_number`, `user_id`, `item_request`, `qty`, `department`, `date`, `status`, `issued_by`, `date_issued`, `date_approved`, `date_declined`, `is_posted`, `declined_by`) VALUES
(13, 'REQ-00001', 28, 'Welding Machine', 4, 'HIMS', '2025-04-02', 1, 'Rosille Watapampa', '2025-04-01', NULL, NULL, 1, '');

-- --------------------------------------------------------

--
-- Table structure for table `stock_in`
--

CREATE TABLE `stock_in` (
  `stockin_id` int(11) NOT NULL,
  `controlNO` varchar(255) NOT NULL,
  `serialNO` varchar(100) NOT NULL,
  `item` varchar(255) NOT NULL,
  `qty` int(11) NOT NULL,
  `orig_qty` int(11) NOT NULL,
  `category` enum('IT Equipment','Engineering Equipment','Fixed Asset','') NOT NULL,
  `dop` date NOT NULL,
  `dr` date NOT NULL,
  `warranty` tinyint(1) NOT NULL,
  `is_posted` tinyint(1) NOT NULL DEFAULT 0,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stock_in`
--

INSERT INTO `stock_in` (`stockin_id`, `controlNO`, `serialNO`, `item`, `qty`, `orig_qty`, `category`, `dop`, `dr`, `warranty`, `is_posted`, `user_id`) VALUES
(82, 'CN-1', 'DHUHSIOQS', 'Monitor (ASUS ROG Swift PG32UQX)', 0, 1, 'IT Equipment', '2025-03-30', '2025-04-01', 0, 1, NULL),
(83, 'CN-1', 'IOFIOFFOIFO', 'Monitor (ASUS ROG Swift PG32UQX)', 1, 1, 'IT Equipment', '2025-03-30', '2025-04-01', 0, 1, NULL),
(84, 'CN-1', 'DWNJDNWIP', 'Monitor (ASUS ROG Swift PG32UQX)', 1, 1, 'IT Equipment', '2025-03-30', '2025-04-01', 0, 1, NULL),
(85, 'CN-1', 'DJWIPJDWI', 'Monitor (ASUS ROG Swift PG32UQX)', 1, 1, 'IT Equipment', '2025-03-30', '2025-04-01', 0, 1, NULL),
(86, 'CN-1', 'DJWIPJDPIWD', 'Monitor (ASUS ROG Swift PG32UQX)', 1, 1, 'IT Equipment', '2025-03-30', '2025-04-01', 0, 1, NULL),
(87, 'CN-2', 'DJWIDJW', 'Keyboard (A4TECH)', 1, 1, 'IT Equipment', '2025-03-30', '2025-04-01', 0, 1, NULL),
(88, 'CN-2', 'DKWOPDWOP', 'Keyboard (A4TECH)', 1, 1, 'IT Equipment', '2025-03-30', '2025-04-01', 0, 1, NULL),
(89, 'CN-2', 'DOWPDOPW', 'Keyboard (A4TECH)', 1, 1, 'IT Equipment', '2025-03-30', '2025-04-01', 0, 1, NULL),
(90, 'CN-2', 'JDOWPJDOPDW', 'Keyboard (A4TECH)', 1, 1, 'IT Equipment', '2025-03-30', '2025-04-01', 0, 1, NULL),
(91, 'CN-2', 'DIODJWOI', 'UPS Secure', 0, 1, 'IT Equipment', '2025-03-30', '2025-04-01', 0, 1, NULL),
(92, 'CN-2', 'DJWPJDPW', 'UPS Secure', 1, 1, 'IT Equipment', '2025-03-30', '2025-04-01', 0, 1, NULL),
(93, 'CN-2', 'KDOWKDPOW', 'UPS Secure', 1, 1, 'IT Equipment', '2025-03-30', '2025-04-01', 0, 1, NULL),
(94, 'CN-2', 'DKOWPKDPODW', 'UPS Secure', 1, 1, 'IT Equipment', '2025-03-30', '2025-04-01', 0, 1, NULL),
(95, 'CN-3', 'DHWUDIWO', 'Welding Machine', 0, 1, 'Engineering Equipment', '2025-04-14', '2025-04-01', 0, 1, NULL),
(96, 'CN-3', 'DJIWJDWP', 'Welding Machine', 0, 1, 'Engineering Equipment', '2025-04-14', '2025-04-01', 0, 1, NULL),
(97, 'CN-3', 'DJWOIJDI', 'Welding Machine', 0, 1, 'Engineering Equipment', '2025-04-14', '2025-04-01', 0, 1, NULL),
(98, 'CN-3', 'DJIWJDWOI', 'Welding Machine', 0, 1, 'Engineering Equipment', '2025-04-14', '2025-04-01', 0, 1, NULL),
(99, 'CN-3', 'DJIWPJDWO', 'Welding Machine', 1, 1, 'Engineering Equipment', '2025-04-14', '2025-04-01', 0, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `pword` varchar(255) NOT NULL,
  `number` varchar(20) NOT NULL,
  `department` varchar(100) NOT NULL,
  `role` enum('admin','mmo','it','engineering') NOT NULL,
  `is_hide` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `fullname`, `username`, `pword`, `number`, `department`, `role`, `is_hide`) VALUES
(28, 'Rosille Mae C. Lumangtad', 'ros', '$2y$10$X5/2t2HSnD.gLOZRp3khqubjJM3ElvAqemgF0ItiJInq/i/vdpDHC', '', 'HIMS', 'admin', 0),
(29, 'Desiree Darish', 'des', '$2y$10$0OtodRU0911iTKYc5zXNRu8W5ORnNHkSqGytvV16HVjg.p0bMwqkO', '', 'MMO', 'mmo', 0),
(30, 'Prince Jay Sayre', 'haruki', '$2y$10$zu3jwwkZtXxjN7asjGmBzOo0K35pF0YM0mNWdXsH8nVsiKIm4JhZ6', '', 'Engineering and Maintenance Office', 'engineering', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `fixed_assets`
--
ALTER TABLE `fixed_assets`
  ADD PRIMARY KEY (`asset_id`);

--
-- Indexes for table `request`
--
ALTER TABLE `request`
  ADD PRIMARY KEY (`req_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `stock_in`
--
ALTER TABLE `stock_in`
  ADD PRIMARY KEY (`stockin_id`),
  ADD KEY `fk_user` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `fixed_assets`
--
ALTER TABLE `fixed_assets`
  MODIFY `asset_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `request`
--
ALTER TABLE `request`
  MODIFY `req_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `stock_in`
--
ALTER TABLE `stock_in`
  MODIFY `stockin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `request`
--
ALTER TABLE `request`
  ADD CONSTRAINT `request_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `stock_in`
--
ALTER TABLE `stock_in`
  ADD CONSTRAINT `fk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
