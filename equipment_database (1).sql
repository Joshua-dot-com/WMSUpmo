-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 27, 2025 at 05:50 PM
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
-- Database: `equipment database`
--

-- --------------------------------------------------------

--
-- Table structure for table `overview_users`
--

CREATE TABLE `overview_users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `college` varchar(100) NOT NULL,
  `role` varchar(100) NOT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `admin_role` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `overview_users`
--

INSERT INTO `overview_users` (`id`, `first_name`, `last_name`, `email`, `college`, `role`, `status`, `last_login`, `created_at`, `admin_role`) VALUES
(12, 'Ahron', 'Pasadilla', 'scarfred15@gmail.com', '', 'Administrative Officials', 'Active', NULL, '2025-03-24 05:50:52', 'Chairperson'),
(13, 'Ahron', 'Pasadilla', 'pasadill211@gmail.com', 'College of Teacher Education', 'User', 'Active', NULL, '2025-03-24 07:51:17', '');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('User','Administrative Officials') NOT NULL,
  `college` varchar(100) DEFAULT NULL,
  `admin_role` varchar(100) DEFAULT NULL,
  `other_services` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(20) NOT NULL DEFAULT 'Pending',
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  `is_owner` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `password`, `role`, `college`, `admin_role`, `other_services`, `created_at`, `status`, `is_admin`, `is_owner`) VALUES
(15, 'Ahron', 'Pasadilla', 'scarfred15@gmail.com', '$2y$10$LQmligNz1.yhZvPe3SCejucz1EHzBt8uQmc0T4nWj2IwI2DLd/OtW', 'Administrative Officials', '', 'Chairperson', '', '2025-03-24 04:51:46', 'Granted', 1, 0),
(17, 'Ahron', 'Pasadilla', 'pasadill211@gmail.com', '$2y$10$2cJvF6ZtQFaa0ReYmKxYfOlgOCxLAIDsHDrBlfEto3ejjsvbQLU9W', 'User', 'College of Teacher Education', '', '', '2025-03-24 07:45:57', 'Granted', 0, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `overview_users`
--
ALTER TABLE `overview_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `overview_users`
--
ALTER TABLE `overview_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
