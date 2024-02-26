-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 26, 2024 at 11:32 PM
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
-- Database: `ascendionCG`
--
CREATE DATABASE IF NOT EXISTS `ascendionCG` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `ascendionCG`;

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` int(11) NOT NULL,
  `item` varchar(14) DEFAULT NULL,
  `item_type` int(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `item`, `item_type`) VALUES
(1, 'Pen', 1),
(2, 'Printer', 2),
(3, 'Marker', 1),
(4, 'Scanner', 2),
(5, 'Clear Tape', 1),
(6, 'Standing Table', 2),
(7, 'Shredder', 2),
(8, 'Thumbtack', 1),
(10, 'Paper Clip', 1),
(11, ' A4 Sheet', 1),
(12, ' Notebook', 1),
(13, 'Chair', 3),
(14, 'Stool', 3);

-- --------------------------------------------------------

--
-- Table structure for table `item_type`
--

CREATE TABLE `item_type` (
  `id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `item_type`
--

INSERT INTO `item_type` (`id`, `type`) VALUES
(1, 'Office Supply'),
(2, 'Equipment'),
(3, 'Furniture');

-- --------------------------------------------------------

--
-- Table structure for table `requests`
--

CREATE TABLE `requests` (
  `id` int(11) NOT NULL,
  `user_fk` int(11) NOT NULL,
  `dt_requested` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `requests`
--

INSERT INTO `requests` (`id`, `user_fk`, `dt_requested`) VALUES
(1, 1, '2024-02-17 21:25:16'),
(2, 2, '2024-02-17 21:44:32'),
(3, 3, '2024-02-17 21:44:59'),
(12, 11, '2024-02-18 13:33:39'),
(13, 13, '2024-02-19 11:08:12'),
(14, 14, '2024-02-26 16:50:53'),
(15, 15, '2024-02-26 16:57:26'),
(16, 16, '2024-02-26 17:00:31'),
(17, 17, '2024-02-26 17:11:16');

-- --------------------------------------------------------

--
-- Table structure for table `summary`
--

CREATE TABLE `summary` (
  `id` int(11) NOT NULL,
  `req_id` int(11) NOT NULL,
  `requested_by` varchar(50) NOT NULL,
  `items` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `summary`
--

INSERT INTO `summary` (`id`, `req_id`, `requested_by`, `items`) VALUES
(5, 12, 'Bill', '[{2,[7,4,6]},{1,[12,3,8]}]'),
(7, 3, 'Dick', '[{2,[7,6,2]}]'),
(10, 2, 'Tom', '[{1,[10,5]}]'),
(16, 13, 'Linda', '[{2,[2]},{1,[3,1,8]}]'),
(17, 14, 'Barry', '[{2,[7,2]}]'),
(19, 15, 'Penny', '[{3,[13,14]}]'),
(23, 16, 'Zachary', '[{2,[6]},{1,[8]}]'),
(28, 1, 'Chris', '[{2,[7,4]},{3,[14,13]},{1,[5,11,1,8]}]'),
(30, 17, 'Newman', '[{1,[1]}]');

-- --------------------------------------------------------

--
-- Table structure for table `type_requests`
--

CREATE TABLE `type_requests` (
  `id` int(11) NOT NULL,
  `request_fk` int(11) NOT NULL,
  `type_fk` int(11) NOT NULL,
  `item_fk` int(11) NOT NULL,
  `dt_requested` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `type_requests`
--

INSERT INTO `type_requests` (`id`, `request_fk`, `type_fk`, `item_fk`, `dt_requested`) VALUES
(180, 12, 1, 12, '2024-02-18 15:05:31'),
(181, 12, 1, 3, '2024-02-18 15:05:31'),
(182, 12, 1, 8, '2024-02-18 15:05:31'),
(183, 12, 2, 4, '2024-02-18 15:05:31'),
(184, 12, 2, 6, '2024-02-18 15:05:31'),
(185, 12, 2, 7, '2024-02-18 15:05:31'),
(189, 3, 2, 7, '2024-02-18 15:05:48'),
(190, 3, 2, 6, '2024-02-18 15:05:48'),
(191, 3, 2, 2, '2024-02-18 15:05:48'),
(197, 2, 1, 10, '2024-02-19 11:06:38'),
(198, 2, 1, 5, '2024-02-19 11:06:38'),
(213, 13, 1, 3, '2024-02-26 16:50:11'),
(214, 13, 1, 1, '2024-02-26 16:50:11'),
(215, 13, 1, 8, '2024-02-26 16:50:11'),
(216, 13, 2, 2, '2024-02-26 16:50:11'),
(217, 14, 2, 7, '2024-02-26 16:50:53'),
(218, 14, 2, 2, '2024-02-26 16:50:53'),
(222, 15, 3, 13, '2024-02-26 16:57:26'),
(223, 15, 3, 14, '2024-02-26 16:57:26'),
(233, 16, 1, 8, '2024-02-26 17:01:06'),
(234, 16, 2, 6, '2024-02-26 17:01:06'),
(258, 1, 1, 5, '2024-02-26 17:11:05'),
(259, 1, 1, 1, '2024-02-26 17:11:05'),
(260, 1, 1, 8, '2024-02-26 17:11:05'),
(261, 1, 1, 11, '2024-02-26 17:11:05'),
(262, 1, 2, 4, '2024-02-26 17:11:05'),
(263, 1, 2, 7, '2024-02-26 17:11:05'),
(264, 1, 3, 13, '2024-02-26 17:11:05'),
(265, 1, 3, 14, '2024-02-26 17:11:05'),
(267, 17, 1, 1, '2024-02-26 17:11:33');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fName` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fName`) VALUES
(1, 'Chris'),
(2, 'Tom'),
(3, 'Dick'),
(11, 'Bill'),
(12, 'Tim'),
(13, 'Linda'),
(14, 'Barry'),
(15, 'Penny'),
(16, 'Zachary'),
(17, 'Newman');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `item_type`
--
ALTER TABLE `item_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `requests`
--
ALTER TABLE `requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `summary`
--
ALTER TABLE `summary`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `type_requests`
--
ALTER TABLE `type_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `item_type`
--
ALTER TABLE `item_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `requests`
--
ALTER TABLE `requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `summary`
--
ALTER TABLE `summary`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `type_requests`
--
ALTER TABLE `type_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=268;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
