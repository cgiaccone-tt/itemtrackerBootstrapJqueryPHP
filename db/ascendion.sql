-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 16, 2024 at 03:18 PM
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
-- Database: `ascendion`
--

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
  `dt_requested` datetime NOT NULL DEFAULT current_timestamp(),
  `item_fk` int(11) NOT NULL,
  `json1` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `requests`
--

INSERT INTO `requests` (`id`, `user_fk`, `dt_requested`, `item_fk`, `json1`) VALUES
(78, 8, '2024-02-15 17:09:38', 6, ''),
(79, 8, '2024-02-15 17:09:38', 11, ''),
(80, 8, '2024-02-15 17:09:38', 10, ''),
(81, 50, '2024-02-15 17:38:15', 2, ''),
(82, 50, '2024-02-15 17:38:15', 5, ''),
(83, 50, '2024-02-15 17:38:15', 13, ''),
(84, 50, '2024-02-15 17:38:37', 3, ''),
(85, 52, '2024-02-15 20:14:29', 5, '[[{\"id\":2,\"item\":\"Printer\",\"item_type\":2},{\"id\":7,\"item\":\"Shredder\",\"item_type\":2},{\"id\":11,\"item\":\" A4 Sheet\",\"item_type\":1},{\"id\":12,\"item\":\" Notebook\",\"item_type\":1}]]');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fName` varchar(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fName`) VALUES
(1, 'maya'),
(2, 'kie'),
(3, 'ron'),
(4, 'john'),
(5, 'smith'),
(6, 'lily'),
(8, 'Chris'),
(45, 'aaaa'),
(46, 'bbbb'),
(47, 'ccccc'),
(48, 'ddddd'),
(49, 'jjjjj'),
(50, 'ggggg'),
(51, 'fffff'),
(52, 'ttttt');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
