-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 29, 2024 at 05:32 AM
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
-- Database: `map_mark_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_mark`
--

CREATE TABLE `tbl_mark` (
  `tbl_mark_id` int(11) NOT NULL,
  `mark_name` varchar(255) NOT NULL,
  `mark_long` double NOT NULL,
  `mark_lat` double NOT NULL,
  `mark_info` longtext NOT NULL,
  `mark_status` varchar(20) NOT NULL,
  `mark_image` varchar(255) NOT NULL,
  `mark_petFriendly` varchar(3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tbl_mark`
--

INSERT INTO `tbl_mark` (`tbl_mark_id`, `mark_name`, `mark_long`, `mark_lat`, `mark_info`, `mark_status`, `mark_image`, `mark_petFriendly`) VALUES
(51, 'jhdsafkhka', 120.913109, 13.706984, 'fjhgf', 'not full', '', NULL),
(52, 'abe', 120.908056, 13.708174, '', 'not full', '', NULL),
(53, 'fdd', 120.895423, 13.703313, 'dsa', 'slightly full', 'IMG_1903.JPG', NULL),
(54, 'Test 1', 120.931114, 13.74819, 'Test 1 Information', 'full', 'beach.jpg', NULL),
(55, 'test0', 120.904867, 13.737847, 'ssasasas', 'not full', 'beach.jpg', 'yes'),
(58, 'Aguila', 120.927414, 13.762296, 'It is also an ideal place to hold your business meetings and seminars. A romantic haven, too, for the newlyweds and an attractive place for social functions and receptions.', 'slightly full', 'beach.jpg', 'yes');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_mark`
--
ALTER TABLE `tbl_mark`
  ADD PRIMARY KEY (`tbl_mark_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_mark`
--
ALTER TABLE `tbl_mark`
  MODIFY `tbl_mark_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
