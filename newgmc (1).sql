-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 13, 2025 at 07:05 PM
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
-- Database: `newgmc`
--

-- --------------------------------------------------------

--
-- Table structure for table `asset`
--

CREATE TABLE `asset` (
  `asset_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `serial_number` varchar(50) NOT NULL,
  `deployment_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `equipment`
--

CREATE TABLE `equipment` (
  `equipment_id` int(11) NOT NULL,
  `equip_name` varchar(255) NOT NULL,
  `category` enum('IT Equipment','Engineering Equipment','','') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `equipment`
--

INSERT INTO `equipment` (`equipment_id`, `equip_name`, `category`) VALUES
(1, 'Monitor', 'IT Equipment'),
(2, 'UPS', 'IT Equipment'),
(3, 'System Unit', 'IT Equipment'),
(4, 'AVR', 'IT Equipment'),
(5, 'Keyboard', 'IT Equipment'),
(6, 'Mouse', 'IT Equipment'),
(7, 'Printer', 'IT Equipment'),
(8, 'Multimeter', 'Engineering Equipment'),
(9, 'Water Quality Analyzer', 'Engineering Equipment'),
(10, 'Welding Machine', 'Engineering Equipment'),
(11, 'Hydraulic Press', 'Engineering Equipment');

-- --------------------------------------------------------

--
-- Table structure for table `fixed_assets`
--

CREATE TABLE `fixed_assets` (
  `asset_id` int(11) NOT NULL,
  `stockin_item` varchar(255) NOT NULL,
  `req_id` int(11) NOT NULL,
  `assigned_to` int(11) NOT NULL,
  `location` varchar(255) NOT NULL,
  `serial_number` varchar(255) NOT NULL,
  `remarks` text DEFAULT NULL,
  `date_assigned` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `request`
--

CREATE TABLE `request` (
  `req_id` int(11) NOT NULL,
  `req_number` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `stockin_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `department` varchar(255) NOT NULL,
  `date` date DEFAULT current_timestamp(),
  `status` tinyint(1) DEFAULT 0,
  `issued_by` int(11) DEFAULT NULL,
  `date_issued` datetime DEFAULT NULL,
  `date_approved` datetime DEFAULT NULL,
  `date_declined` datetime DEFAULT NULL,
  `is_posted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `request`
--

INSERT INTO `request` (`req_id`, `req_number`, `user_id`, `stockin_id`, `qty`, `department`, `date`, `status`, `issued_by`, `date_issued`, `date_approved`, `date_declined`, `is_posted`) VALUES
(122, 'REQ-78428', 21, 43, 4, 'Engineering and Maintenance Office', '2025-03-12', 1, NULL, '2025-03-12 00:00:00', NULL, NULL, 1),
(123, 'REQ-78428', 21, 48, 4, 'Engineering and Maintenance Office', '2025-03-12', 1, NULL, '2025-03-12 00:00:00', NULL, NULL, 1),
(124, 'REQ-26469', 21, 48, 3, 'Engineering and Maintenance Office', '2025-03-12', 1, NULL, '2025-03-12 00:00:00', NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `stock_in`
--

CREATE TABLE `stock_in` (
  `stockin_id` int(11) NOT NULL,
  `controlNO` varchar(255) NOT NULL,
  `item` varchar(255) NOT NULL,
  `qty` int(11) NOT NULL,
  `orig_qty` int(11) NOT NULL,
  `category` enum('IT Equipment','Engineering Equipment','','') NOT NULL,
  `dop` date NOT NULL,
  `dr` date NOT NULL,
  `warranty` tinyint(1) NOT NULL,
  `is_posted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stock_in`
--

INSERT INTO `stock_in` (`stockin_id`, `controlNO`, `item`, `qty`, `orig_qty`, `category`, `dop`, `dr`, `warranty`, `is_posted`) VALUES
(40, 'CN-1', 'Monitor (ASUS ROG Swift PG32UQX)', 0, 5, 'IT Equipment', '2025-03-11', '2025-03-10', 1, 1),
(41, 'CN-2', 'CNC Milling Machine (Tormach PCNC 440)', 3, 5, 'Engineering Equipment', '2025-03-10', '2025-03-10', 1, 1),
(42, 'CN-3', 'CNC Milling Machine (Tormach PCNC 440)', 5, 5, 'Engineering Equipment', '2025-03-28', '2025-03-18', 0, 1),
(43, 'CN-4', 'Dell Latitude 7430', 2, 5, 'Engineering Equipment', '2025-03-12', '2025-03-10', 0, 1),
(44, 'CN-5', 'Printer HP LaserJet MFP M236sdw', 2, 3, 'IT Equipment', '2025-03-22', '2025-03-10', 1, 1),
(45, 'CN-5', 'Router TP-Link Archer AX10', 3, 3, 'IT Equipment', '2025-03-22', '2025-03-10', 0, 1),
(46, 'CN-5', ' Switch Cisco	CBS110-16T', 2, 3, 'IT Equipment', '2025-03-22', '2025-03-10', 0, 1),
(47, 'CN-6', 'Digital Multimeter Fluke 117', 10, 10, 'Engineering Equipment', '2025-03-09', '2025-03-10', 1, 1),
(48, 'CN-6', 'Welding Machine Panasonic YD-200RD', 10, 10, 'Engineering Equipment', '2025-03-09', '2025-03-10', 0, 1),
(49, 'CN-7', 'Monitor (ACER)', 1, 1, 'IT Equipment', '2025-03-20', '2025-03-11', 0, 0),
(50, 'CN-7', 'Keyboard (A4TECH)', 1, 1, 'IT Equipment', '2025-03-20', '2025-03-11', 0, 0);

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
(11, 'Rosille Mae C. Lumangtad', 'rosalindadddw', '$2y$10$u9TE7uu2RGZOR4WbqBIwA.MsM5M.7OHffVLRSZNZOfv2MzaXf323q', '09889878767', 'HIMS', 'admin', 0),
(20, 'Joanna Marie Ducut', 'wana', '$2y$10$vQtofYK4Bdxcy2beOUjVoOF5CzC/LJGSPl0pIg2qFNN3S7pW/8LwG', '09889887877', 'HIMS', 'it', 0),
(21, 'Prince Jay Sayre', 'haruki', '$2y$10$4FHxv5xv2Uw3Iimfg3/ky.h4zjhDh3GHcrpV.6oSASf7bHlwxrPfe', '09889887877', 'Engineering and Maintenance Office', 'engineering', 0),
(23, 'Desiree Darish', 'des', '$2y$10$eh15n1MUKAaaQ2cv86f48eK2nJnj.nzftRnJN/voOxOCWeRfLmiVm', '09889887877', 'MMO', 'mmo', 0),
(24, 'Reachell Mandawe', 'chell', '$2y$10$E/DOWpbXaEyFl.J9W/YF4OgcfzPO//./9rAg853snbrFsXxa1iKMm', '', 'HIMS', 'it', 0),
(25, 'Rajsheed Limpas', 'raj', '$2y$10$pm22cDTe3W825IC0oUJ1/u6ebEFU4McQmpawcBbbUwhsmjBd6etUa', '', 'Engineering and Maintenance Office', 'engineering', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `asset`
--
ALTER TABLE `asset`
  ADD PRIMARY KEY (`asset_id`),
  ADD UNIQUE KEY `serial_number` (`serial_number`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `equipment`
--
ALTER TABLE `equipment`
  ADD PRIMARY KEY (`equipment_id`);

--
-- Indexes for table `fixed_assets`
--
ALTER TABLE `fixed_assets`
  ADD PRIMARY KEY (`asset_id`),
  ADD KEY `assigned_to` (`assigned_to`);

--
-- Indexes for table `request`
--
ALTER TABLE `request`
  ADD PRIMARY KEY (`req_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `stockin_id` (`stockin_id`);

--
-- Indexes for table `stock_in`
--
ALTER TABLE `stock_in`
  ADD PRIMARY KEY (`stockin_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `asset`
--
ALTER TABLE `asset`
  MODIFY `asset_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `equipment`
--
ALTER TABLE `equipment`
  MODIFY `equipment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `fixed_assets`
--
ALTER TABLE `fixed_assets`
  MODIFY `asset_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `request`
--
ALTER TABLE `request`
  MODIFY `req_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=125;

--
-- AUTO_INCREMENT for table `stock_in`
--
ALTER TABLE `stock_in`
  MODIFY `stockin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `asset`
--
ALTER TABLE `asset`
  ADD CONSTRAINT `asset_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `fixed_assets`
--
ALTER TABLE `fixed_assets`
  ADD CONSTRAINT `fixed_assets_ibfk_4` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `request`
--
ALTER TABLE `request`
  ADD CONSTRAINT `request_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `request_ibfk_2` FOREIGN KEY (`stockin_id`) REFERENCES `stock_in` (`stockin_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
