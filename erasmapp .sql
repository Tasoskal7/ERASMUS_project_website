-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 04, 2025 at 09:02 PM
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
-- Database: `erasmapp`
--

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE `applications` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `lname` varchar(50) NOT NULL,
  `am` varchar(20) NOT NULL,
  `passed_percent` decimal(5,2) NOT NULL,
  `average` decimal(4,2) NOT NULL,
  `english_level` varchar(5) NOT NULL,
  `other_langs` varchar(10) DEFAULT NULL,
  `uni1` int(11) NOT NULL,
  `uni2` int(11) DEFAULT NULL,
  `uni3` int(11) DEFAULT NULL,
  `grades_file` varchar(255) NOT NULL,
  `eng_cert` varchar(255) NOT NULL,
  `other_certs` text DEFAULT NULL,
  `terms_accepted` tinyint(1) NOT NULL,
  `submitted_at` datetime NOT NULL,
  `accepted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `application_period`
--

CREATE TABLE `application_period` (
  `id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `active` tinyint(1) DEFAULT 1,
  `results_announced` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `application_period`
--

INSERT INTO `application_period` (`id`, `start_date`, `end_date`, `active`, `results_announced`) VALUES
(50, '2025-05-31', '2025-06-13', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `universities`
--

CREATE TABLE `universities` (
  `id` int(11) NOT NULL,
  `uni_name` varchar(255) NOT NULL,
  `country` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `universities`
--

INSERT INTO `universities` (`id`, `uni_name`, `country`, `city`, `active`) VALUES
(1, 'Πανεπιστήμιο Παρισιού', 'Γαλλία', 'Παρίσι', 1),
(2, 'Πανεπιστήμιο Βαρκελώνης', 'Ισπανία', 'Βαρκελώνη', 1),
(3, 'Πανεπιστήμιο Βερολίνου', 'Γερμανία', 'Βερολίνο', 1),
(5, 'Peloponesse', 'greece', 'tripoli', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `LName` varchar(100) NOT NULL,
  `AM` varchar(13) NOT NULL,
  `pNumber` varchar(10) NOT NULL,
  `email` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `pw` varchar(255) NOT NULL,
  `role` enum('registered','administrator') NOT NULL DEFAULT 'registered'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uni1` (`uni1`),
  ADD KEY `uni2` (`uni2`),
  ADD KEY `uni3` (`uni3`);

--
-- Indexes for table `application_period`
--
ALTER TABLE `application_period`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `universities`
--
ALTER TABLE `universities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `AM` (`AM`),
  ADD UNIQUE KEY `pNumber` (`pNumber`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `application_period`
--
ALTER TABLE `application_period`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `universities`
--
ALTER TABLE `universities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `applications`
--
ALTER TABLE `applications`
  ADD CONSTRAINT `applications_ibfk_1` FOREIGN KEY (`uni1`) REFERENCES `universities` (`id`),
  ADD CONSTRAINT `applications_ibfk_2` FOREIGN KEY (`uni2`) REFERENCES `universities` (`id`),
  ADD CONSTRAINT `applications_ibfk_3` FOREIGN KEY (`uni3`) REFERENCES `universities` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
