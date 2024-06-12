-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 28, 2024 at 02:54 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bandbarcode`
--

-- --------------------------------------------------------

--
-- Table structure for table `band`
--

CREATE TABLE `band` (
  `serial_number` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(255) NOT NULL,
  `color_code` varchar(255) NOT NULL,
  `batch_code` varchar(255) NOT NULL,
  `letter` varchar(255) NOT NULL,
  `bar_code` varchar(255) NOT NULL,
  `issue_time` datetime DEFAULT NULL current_timestamp(),
  `issued` tinyint(1) NOT NULL DEFAULT 0,
  `fo_issue_time` datetime DEFAULT NULL,
  `fo_issued` tinyint(1) NOT NULL DEFAULT 0,
  `fo_user` varchar(255) NOT NULL,
  `used_time` datetime DEFAULT NULL,
  `used` tinyint(1) NOT NULL DEFAULT 0,
  `count` int(11) DEFAULT 0,
  `voiditem` tinyint(1) NOT NULL DEFAULT 0,
  `voidtime` datetime DEFAULT NULL,
  `upgradedfrombarcode` varchar(255) NOT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  PRIMARY KEY("serial_number")
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `users` (
  `empid` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`empid`, `name`, `username`, `password`, `department`, `status`) VALUES
('123', 'Yogesh', 'Yogesh', '$2y$10$/DVaKQikRYOsV4ONYhdG5.UoUIELtM7IpLXbFJZBHOVtStCByHlfq', 'accounts', 1),
('4530', 'Kalpesh', 'Kalpesh', '$2y$10$hi1xARx7YLjOf/beO5Xw4eNcHssdlKpGWaF7LZWvQfz3mLg/C02lO', 'technical', 1),
('123', 'yelu', 'yelu', '$2y$10$BjmqtzYBqXjmEGGITsuJf.WiDTvc9ucjkxJ3Di4EW0t7/Ew33RD1u', 'security', 1),
('123', 'natasha', 'natasha', '$2y$10$QTfPERAcWwLKYRRylXC3N.TacQEs1fLGWb9EozlXUCZMaTOZCmMBO', 'fo', 1),
('0012', 'surveillance', 'surveillance', '$2y$10$x0UZCdPBU2mlyXVFvVTjBuB4ntCNTDENMIq0g0otixyrHO5W5KHtm', 'surveillance', 1),
('456', 'foonboard', 'foonboard', '$2y$10$c84VtmDR9VJQ64Vnmwo.JOX8zC.gV1hIx7xLU77AKH9pRWPT5a0w6', 'foonboard', 1),
('452', 'FOsuperviser', 'FOsuperviser', '$2y$10$jQEoor1nf5RynWnwXtAMCuSw1waDCe8bXTlLZaawmeiVPSiy9XY0.', 'fosuperviser', 1),
('789', 'Sbd', 'Sbd', '$2y$10$./2GdgBvsHj/9Pawz0On9uwTXGP5KeeoZrF6s3AtjTDjQBytlH83i', 'Administrator', 1),
('784', 'rajesh', 'raj', '$2y$10$bu6UIIrpNPbjhhGCIhIrNuGLHsdPtw8iwPiEUOUxWbWPqNo2gdVoS', 'accounts', 1),
('1234', 'Accounts', 'Accounts', '$2y$10$yImqIv119r8V0/TEv85H0ucE7qZpmKFt15oUq88h/d6pxF2RLVQ1W', 'accounts', 1),
('457', 'asd', 'asd', '$2y$10$azeJp/UwS4aAvQM9iWxuteE9tyswwBAVGcIZ4FwSp/WfbtWS/qHoy', 'foonboard', 1),
('456', 'FOonboard', 'FOonboard', '$2y$10$HEr7zKtCCZZu1wOvUz2nF.glp3/pNCRiWsG5i4A9vo9AisroQlpfS', 'foonboard', 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_log`
--

CREATE TABLE `user_log` (
  `id` int(11) NOT NULL,
  `page` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `log_time` datetime DEFAULT current_timestamp(),
  `log_action` longtext NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_log`
--

--
-- Indexes for dumped tables
--

--
-- Indexes for table `band`
--
ALTER TABLE `band`
  ADD PRIMARY KEY (`serial_number`);

--
-- Indexes for table `user_log`
--
ALTER TABLE `user_log`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `band`
--
ALTER TABLE `band`
  MODIFY `serial_number` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `user_log`
--
ALTER TABLE `user_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1129;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
