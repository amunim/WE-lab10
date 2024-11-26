-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:6453
-- Generation Time: Nov 26, 2024 at 06:04 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `nust_lms`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `classid` int(50) NOT NULL,
  `studentid` int(50) NOT NULL,
  `isPresent` tinyint(1) NOT NULL,
  `comments` varchar(200) NOT NULL,
  `marked_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`classid`, `studentid`, `isPresent`, `comments`, `marked_at`) VALUES
(1, 1, 0, '', '2024-11-26 17:35:02'),
(3, 1, 1, '', '2024-11-25 20:06:26'),
(2, 1, 1, '', '2024-11-25 20:06:26'),
(2, 1, 0, '', '2024-11-25 20:06:26'),
(3, 1, 1, '', '2024-11-25 20:14:38'),
(3, 1, 0, '', '2024-11-25 20:15:01'),
(3, 1, 1, '', '2024-11-26 20:19:30'),
(5, 1, 1, '', '2024-11-26 17:59:45'),
(7, 1, 0, '', '2024-11-26 17:56:02');

-- --------------------------------------------------------

--
-- Table structure for table `class`
--

CREATE TABLE `class` (
  `id` int(11) NOT NULL,
  `teacherid` int(50) NOT NULL,
  `starttime` datetime NOT NULL,
  `endtime` datetime NOT NULL,
  `credit_hours` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `class`
--

INSERT INTO `class` (`id`, `teacherid`, `starttime`, `endtime`, `credit_hours`) VALUES
(1, 2, '2024-11-26 18:09:00', '2024-11-26 18:09:00', 11),
(2, 2, '2024-11-26 18:27:00', '2024-11-26 23:32:00', 3),
(3, 2, '2024-11-26 18:27:00', '2024-11-26 23:32:00', 3),
(4, 2, '2024-11-26 18:27:00', '2024-11-26 23:32:00', 3),
(5, 2, '2024-11-26 20:27:00', '2024-11-26 21:27:00', 12),
(6, 2, '2024-11-26 20:19:00', '2024-11-26 21:20:00', 12),
(7, 2, '2024-11-30 22:53:00', '2024-11-30 13:53:00', 1);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(50) NOT NULL,
  `fullname` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `class` varchar(10) NOT NULL,
  `role` enum('teacher','student','admin') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `fullname`, `email`, `class`, `role`) VALUES
(1, 'Abdul Munim', 'amunim@amunim.me', '13', 'student'),
(2, 'Ammar Shahzad', 'ammar@gmail.com', '13', 'teacher');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `class`
--
ALTER TABLE `class`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `class`
--
ALTER TABLE `class`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;
