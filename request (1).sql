-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 13, 2025 at 11:23 PM
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
(127, 'REQ-00001', 21, 47, 3, 'Engineering and Maintenance Office', '2025-03-13', 1, NULL, '2025-03-13 00:00:00', NULL, NULL, 1),
(128, 'REQ-00002', 24, 44, 4, 'HIMS', '2025-03-13', 0, NULL, NULL, NULL, NULL, 0),
(129, 'REQ-00002', 24, 45, 4, 'HIMS', '2025-03-13', 0, NULL, NULL, NULL, NULL, 0),
(130, 'REQ-00003', 26, 46, 4, 'HIMS', '2025-03-13', 1, NULL, '2025-03-13 00:00:00', NULL, NULL, 1),
(131, 'REQ-00004', 26, 45, 1, 'HIMS', '2025-03-13', 1, NULL, '2025-03-13 00:00:00', NULL, NULL, 1),
(132, 'REQ-00005', 26, 47, 5, 'HIMS', '2025-03-13', 1, NULL, '2025-03-13 00:00:00', NULL, NULL, 1),
(133, 'REQ-00006', 11, 43, 12, 'HIMS', '2025-03-13', 0, NULL, NULL, NULL, NULL, 1),
(134, 'REQ-00007', 25, 43, 5, 'Engineering and Maintenance Office', '2025-03-13', 1, NULL, '2025-03-13 00:00:00', NULL, NULL, 1),
(135, 'REQ-00008', 25, 48, 3, 'Engineering and Maintenance Office', '2025-03-13', 1, NULL, '2025-03-13 00:00:00', NULL, NULL, 1),
(136, 'REQ-00008', 25, 41, 3, 'Engineering and Maintenance Office', '2025-03-13', 1, NULL, '2025-03-13 00:00:00', NULL, NULL, 1),
(137, 'REQ-00008', 25, 47, 3, 'Engineering and Maintenance Office', '2025-03-13', 1, NULL, '2025-03-13 00:00:00', NULL, NULL, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `request`
--
ALTER TABLE `request`
  ADD PRIMARY KEY (`req_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `stockin_id` (`stockin_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `request`
--
ALTER TABLE `request`
  MODIFY `req_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=138;

--
-- Constraints for dumped tables
--

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
