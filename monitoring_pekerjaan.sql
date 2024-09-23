-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 07, 2024 at 05:48 AM
-- Server version: 10.4.13-MariaDB
-- PHP Version: 7.4.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `monitoring_pekerjaan`
--

-- --------------------------------------------------------

--
-- Table structure for table `r_customer`
--

CREATE TABLE `r_customer` (
  `id` int(11) NOT NULL,
  `customer` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `r_customer`
--

INSERT INTO `r_customer` (`id`, `customer`) VALUES
(1, 'dika'),
(2, 'hafis');

-- --------------------------------------------------------

--
-- Table structure for table `r_user`
--

CREATE TABLE `r_user` (
  `id` int(11) NOT NULL,
  `programmer` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `r_user`
--

INSERT INTO `r_user` (`id`, `programmer`) VALUES
(1, 'dika'),
(2, 'calvin'),
(3, 'hafis');

-- --------------------------------------------------------

--
-- Table structure for table `t_log`
--

CREATE TABLE `t_log` (
  `id` int(11) NOT NULL,
  `tgl_complain` date NOT NULL,
  `target_selesai` date NOT NULL,
  `uraian` text DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `tgl_input` date DEFAULT NULL,
  `status` enum('TEPAT WAKTU','TERLAMBAT','','') DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `t_log`
--

INSERT INTO `t_log` (`id`, `tgl_complain`, `target_selesai`, `uraian`, `user_id`, `tgl_input`, `status`, `name`) VALUES
(43, '2024-09-06', '2024-09-06', 'yayayyaa', 3, '2024-09-06', 'TEPAT WAKTU', 'hafis'),
(44, '2024-09-06', '2024-09-06', 'ja', 1, '2024-09-06', 'TEPAT WAKTU', 'dika'),
(46, '2024-09-07', '2024-09-07', 'terlambat', 2, '2024-09-07', 'TERLAMBAT', NULL),
(47, '2024-09-07', '2024-09-07', 'terlambat', 2, '2024-09-07', 'TERLAMBAT', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `r_customer`
--
ALTER TABLE `r_customer`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `r_user`
--
ALTER TABLE `r_user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `t_log`
--
ALTER TABLE `t_log`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `r_customer`
--
ALTER TABLE `r_customer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `r_user`
--
ALTER TABLE `r_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `t_log`
--
ALTER TABLE `t_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
