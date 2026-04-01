-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: mysql
-- Generation Time: Jan 17, 2026 at 04:55 PM
-- Server version: 12.0.2-MariaDB-ubu2404
-- PHP Version: 8.3.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `developmentdb`
--
CREATE DATABASE IF NOT EXISTS `developmentdb` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_ai_ci;
USE `developmentdb`;

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `tutor_id` int(11) NOT NULL,
  `profile_id` int(11) DEFAULT NULL,
  `scheduled_at` datetime NOT NULL,
  `status` enum('pending','confirmed','completed','cancelled') DEFAULT 'pending',
  `student_comment` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `student_id`, `tutor_id`, `profile_id`, `scheduled_at`, `status`, `student_comment`, `created_at`) VALUES
(10, 4, 8, 2, '2026-02-04 21:00:00', 'confirmed', '', '2026-01-01 13:16:36'),
(15, 4, 8, 8, '2026-01-20 21:30:00', 'confirmed', 'daas', '2026-01-17 02:54:59'),
(16, 4, 12, 12, '2026-01-20 11:00:00', 'cancelled', 'asdsadadad', '2026-01-17 02:56:02'),
(17, 4, 12, 12, '2026-01-20 12:00:00', 'confirmed', 'help me!', '2026-01-17 02:57:47'),
(18, 4, 12, 12, '2026-01-28 08:30:00', 'confirmed', 'og', '2026-01-17 02:58:12'),
(19, 6, 12, 12, '2026-01-21 10:30:00', 'confirmed', 'fsdafsaf', '2026-01-17 02:58:59'),
(20, 6, 12, 12, '2026-01-20 10:00:00', 'cancelled', 'i can choose any time\r\n\r\n', '2026-01-17 02:59:32'),
(21, 6, 12, 12, '2026-01-19 11:30:00', 'pending', 'fdsf', '2026-01-17 03:05:03'),
(22, 4, 12, 12, '2026-01-23 12:00:00', 'cancelled', 'dsadsa', '2026-01-17 03:17:11'),
(23, 4, 12, 12, '2026-01-22 12:00:00', 'confirmed', 'dasdsaadsadsadasdadadsadas', '2026-01-17 03:17:51'),
(24, 4, 12, 12, '2026-01-24 09:00:00', 'confirmed', 'nice now the days are working\r\n', '2026-01-17 10:04:01'),
(25, 13, 12, 8, '2026-01-18 20:00:00', 'pending', 'am begging am begging youuuu', '2026-01-17 16:51:43');

-- --------------------------------------------------------

--
-- Table structure for table `student_profiles`
--

CREATE TABLE `student_profiles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date_of_birth` date NOT NULL,
  `bio` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `student_profiles`
--

INSERT INTO `student_profiles` (`id`, `user_id`, `date_of_birth`, `bio`) VALUES
(1, 6, '2010-11-20', 'I like basketball and i am EMINEM\r\n'),
(2, 4, '2012-05-21', ''),
(3, 13, '2010-05-03', 'i am the perfect student\r\n');

-- --------------------------------------------------------

--
-- Table structure for table `tutor_profiles`
--

CREATE TABLE `tutor_profiles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `bio` text DEFAULT NULL,
  `hourly_rate` decimal(10,2) NOT NULL,
  `experience_years` int(11) DEFAULT 0,
  `subject` varchar(100) NOT NULL,
  `availability_start` varchar(5) DEFAULT '09:00',
  `availability_end` varchar(5) DEFAULT '17:00',
  `available_days` varchar(255) DEFAULT 'Mon,Tue,Wed,Thu,Fri'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `tutor_profiles`
--

INSERT INTO `tutor_profiles` (`id`, `user_id`, `bio`, `hourly_rate`, `experience_years`, `subject`, `availability_start`, `availability_end`, `available_days`) VALUES
(7, 12, 'Experienced english tutor', 25.00, 10, 'English', '06:00', '16:00', 'Mon,Wed,Sat'),
(8, 12, 'trust me', 50.00, 40, 'Computer Science', '18:00', '22:00', 'Tue,Wed,Thu,Fri,Sun'),
(11, 8, 'Pianist', 50.00, 5, 'Music', '09:00', '17:00', 'Mon,Tue'),
(12, 8, 'meth expert', 30.00, 4, 'Math', '09:00', '13:00', 'Wed,Thu'),
(13, 8, 'amazing math teacher', 15.00, 4, 'Math', '13:00', '20:00', 'Mon,Wed,Sat'),
(14, 14, 'cheapest music teacher out there', 5.00, 5, 'Music', '06:00', '10:00', 'Mon,Tue,Wed,Thu,Fri,Sat,Sun');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `role` enum('student','tutor','admin') NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `first_name`, `last_name`, `role`, `created_at`) VALUES
(4, 'bruh@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'edin', 'bruh', 'student', '2025-12-29 10:53:48'),
(6, 'EminKarabulut@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Emin', 'Karabulut', 'student', '2025-12-29 10:59:17'),
(7, 'Admin@admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'admin', 'admin', '2025-12-29 11:05:59'),
(8, 'asd123@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'asd', 'qwedsa', 'tutor', '2026-01-01 10:48:30'),
(12, 'Enes@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Enes', 'Yigit', 'tutor', '2026-01-17 01:45:15'),
(13, 'exampleStudent@gmail.com', '$2y$12$Ykn5Ma/S8UxDoe/eO4vB/OCALxdM0u6d4QcYNBMPE4/kzpLz3jJZ2', 'ExampleStudent', 'perfect', 'student', '2026-01-17 16:50:36'),
(14, 'ExampleTutor@gmail.com', '$2y$12$zQ6Q1z6AgVW/KirA92MiMO07fn1XJ54ydM4L4bHGTscRdcvTwPbRe', 'ExampleTutor', 'perfecttutor', 'tutor', '2026-01-17 16:52:29');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `tutor_id` (`tutor_id`);

--
-- Indexes for table `student_profiles`
--
ALTER TABLE `student_profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `tutor_profiles`
--
ALTER TABLE `tutor_profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

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
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `student_profiles`
--
ALTER TABLE `student_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tutor_profiles`
--
ALTER TABLE `tutor_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`tutor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_profiles`
--
ALTER TABLE `student_profiles`
  ADD CONSTRAINT `student_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tutor_profiles`
--
ALTER TABLE `tutor_profiles`
  ADD CONSTRAINT `tutor_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
