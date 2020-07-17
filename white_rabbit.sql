-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 17, 2020 at 10:21 AM
-- Server version: 10.1.38-MariaDB
-- PHP Version: 7.3.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `white_rabbit`
--

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

CREATE TABLE `files` (
  `file_id` int(11) NOT NULL,
  `file_name` varchar(50) NOT NULL,
  `file_uploaded_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `files`
--

INSERT INTO `files` (`file_id`, `file_name`, `file_uploaded_date`) VALUES
(1, '3025_P1.jpg', '2020-07-17 08:03:18'),
(2, 'Aviator-Classic.jpg', '2020-07-17 08:03:26'),
(3, 'Round-Metal.jpg', '2020-07-17 08:03:29'),
(4, 'Round-Metal1.jpg', '2020-07-17 08:03:32'),
(7, 'Anzal_Abdulla.pdf', '2020-07-17 08:18:08');

-- --------------------------------------------------------

--
-- Table structure for table `file_action_log`
--

CREATE TABLE `file_action_log` (
  `log_id` int(11) NOT NULL,
  `log_action` enum('insert','delete') NOT NULL,
  `file_name` varchar(200) NOT NULL,
  `log_action_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `file_action_log`
--

INSERT INTO `file_action_log` (`log_id`, `log_action`, `file_name`, `log_action_date`) VALUES
(1, 'insert', '3025_P1.jpg', '2020-07-17 08:03:18'),
(2, 'insert', 'Aviator-Classic.jpg', '2020-07-17 08:03:26'),
(3, 'insert', 'Round-Metal.jpg', '2020-07-17 08:03:29'),
(4, 'insert', 'Round-Metal1.jpg', '2020-07-17 08:03:32'),
(5, 'insert', '3025_P11.jpg', '2020-07-17 08:03:38'),
(6, 'insert', 'Anzal_Abdulla.pdf', '2020-07-17 08:03:47'),
(7, 'delete', 'Anzal_Abdulla.pdf', '2020-07-17 08:04:01'),
(8, 'delete', '3025_P11.jpg', '2020-07-17 08:04:45'),
(9, 'delete', '3025_P1.jpg', '2020-07-17 08:17:56'),
(10, 'insert', 'Anzal_Abdulla.pdf', '2020-07-17 08:18:08');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`file_id`);

--
-- Indexes for table `file_action_log`
--
ALTER TABLE `file_action_log`
  ADD PRIMARY KEY (`log_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `files`
--
ALTER TABLE `files`
  MODIFY `file_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `file_action_log`
--
ALTER TABLE `file_action_log`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
