-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 11, 2024 at 01:37 PM
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
-- Database: `crm`
--

-- --------------------------------------------------------

--
-- Table structure for table `conflicts`
--

CREATE TABLE `conflicts` (
  `conflict_id` int(11) NOT NULL,
  `schedule_id` int(11) DEFAULT NULL,
  `conflict_reason` text DEFAULT NULL,
  `resolved` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `report_id` int(11) NOT NULL,
  `report_type` enum('room_usage','user_activity','maintenance_schedule') NOT NULL,
  `report_details` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `room_id` int(11) NOT NULL,
  `room_code` varchar(50) NOT NULL,
  `room_detail` varchar(50) NOT NULL,
  `room_status` enum('available','occupied','maintenance') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`room_id`, `room_code`, `room_detail`, `room_status`) VALUES
(1, 'A101', 'Television', 'available'),
(2, 'B101', 'Projector', 'available'),
(3, 'A102', 'Whiteboard', 'available'),
(4, 'C101', 'Whiteboard', 'available'),
(5, 'C102', 'Television', 'available'),
(6, 'C103', 'Television', 'available'),
(7, 'A103', 'Television', 'available'),
(8, 'C104', 'Computer', 'available');

-- --------------------------------------------------------

--
-- Table structure for table `room_maintenance`
--

CREATE TABLE `room_maintenance` (
  `maintenance_id` int(11) NOT NULL,
  `room_id` int(11) DEFAULT NULL,
  `maintenance_date` date NOT NULL,
  `maintenance_status` enum('pending','completed') NOT NULL,
  `label` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `room_usage_log`
--

CREATE TABLE `room_usage_log` (
  `usage_id` int(11) NOT NULL,
  `room_id` int(11) DEFAULT NULL,
  `schedule_id` int(11) DEFAULT NULL,
  `usage_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

CREATE TABLE `schedules` (
  `schedule_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `room_id` int(11) DEFAULT NULL,
  `subject` varchar(100) NOT NULL,
  `course_year` varchar(50) NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schedules`
--

INSERT INTO `schedules` (`schedule_id`, `user_id`, `room_id`, `subject`, `course_year`, `start_time`, `end_time`) VALUES
(2, 4, 8, '0', '', '2024-10-02 10:00:00', '2024-10-02 12:00:00'),
(4, 4, 1, 'aszxc', '', '2024-10-03 09:00:00', '2024-10-03 11:00:00'),
(6, 4, 4, 'science', '', '2024-10-11 09:00:00', '2024-10-11 13:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('student','teacher','admin') NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) NOT NULL,
  `gender` enum('male','female','other') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `role`, `first_name`, `middle_name`, `last_name`, `gender`) VALUES
(2, 'johndoe', '$2y$10$MBGE.XiA0LnOPL6yjGUaVeODEIddy1kI0KWqTYtCuFjFbxHCuv/ti', 'admin', 'John', 'D', 'Doe', 'male'),
(4, 'jakedoe', '$2y$10$ratAPPAxUiNqwwz6wKStouPF5f4W./qogbeYA3I2mRKx7EosREytu', 'teacher', 'Jake', 'D', 'Doe', 'male'),
(5, 'jeandoe', '$2y$10$l4J1CspnV9oqob4O9LKtbOQMBt2d6kHHEXW0G.7oRAlPhjY/G6SQK', 'student', 'Jean', 'D', 'Doe', 'female'),
(8, 'janedoe', '$2y$10$wm9i8K6F/UxodoI5LmbCvuOg/EyPufdYr3ONQa5LMBh7EUdBc3mRa', 'student', 'Jane', 'D', 'Doe', 'female'),
(12, 'admin', '$2y$10$obF315eLSBEL8/oDTfo2IO.PbT/Slh/mhDCSwdtULhDwIc2xTwhae', 'admin', 'admin', 'admin', 'admin', 'other');

-- --------------------------------------------------------

--
-- Table structure for table `user_activity_log`
--

CREATE TABLE `user_activity_log` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `login_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_activity_log`
--

INSERT INTO `user_activity_log` (`log_id`, `user_id`, `login_time`) VALUES
(1, 12, '2024-10-02 11:06:53'),
(2, 4, '2024-10-02 11:10:02'),
(3, 5, '2024-10-02 11:15:11'),
(4, 12, '2024-10-02 11:15:24'),
(5, 2, '2024-10-02 14:16:07'),
(6, 4, '2024-10-02 14:16:33'),
(7, 12, '2024-10-03 13:41:00'),
(8, 4, '2024-10-03 13:45:12'),
(9, 4, '2024-10-03 13:47:14'),
(10, 12, '2024-10-03 13:47:25'),
(11, 2, '2024-10-03 13:48:00'),
(12, 2, '2024-10-03 13:48:11'),
(13, 4, '2024-10-03 13:49:16'),
(14, 5, '2024-10-03 13:50:36'),
(15, 12, '2024-10-03 13:51:36'),
(16, 4, '2024-10-03 13:52:35'),
(17, 2, '2024-10-03 13:52:55'),
(18, 2, '2024-10-03 13:53:26'),
(19, 4, '2024-10-03 14:04:46'),
(20, 8, '2024-10-10 12:20:44'),
(21, 4, '2024-10-10 12:28:24'),
(22, 5, '2024-10-10 12:28:41'),
(23, 4, '2024-10-10 12:35:48'),
(24, 2, '2024-10-10 12:36:12'),
(25, 4, '2024-10-10 12:36:44'),
(26, 8, '2024-10-11 09:08:21'),
(27, 4, '2024-10-11 09:25:44'),
(28, 12, '2024-10-11 09:45:44'),
(29, 4, '2024-10-11 09:46:22'),
(30, 5, '2024-10-11 09:48:16'),
(31, 4, '2024-10-11 09:49:19');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `conflicts`
--
ALTER TABLE `conflicts`
  ADD PRIMARY KEY (`conflict_id`),
  ADD KEY `schedule_id` (`schedule_id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`report_id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`room_id`),
  ADD UNIQUE KEY `room_code` (`room_code`);

--
-- Indexes for table `room_maintenance`
--
ALTER TABLE `room_maintenance`
  ADD PRIMARY KEY (`maintenance_id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `room_usage_log`
--
ALTER TABLE `room_usage_log`
  ADD PRIMARY KEY (`usage_id`),
  ADD KEY `room_id` (`room_id`),
  ADD KEY `schedule_id` (`schedule_id`);

--
-- Indexes for table `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`schedule_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `user_activity_log`
--
ALTER TABLE `user_activity_log`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `conflicts`
--
ALTER TABLE `conflicts`
  MODIFY `conflict_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `room_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `room_maintenance`
--
ALTER TABLE `room_maintenance`
  MODIFY `maintenance_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `room_usage_log`
--
ALTER TABLE `room_usage_log`
  MODIFY `usage_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `schedule_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `user_activity_log`
--
ALTER TABLE `user_activity_log`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `conflicts`
--
ALTER TABLE `conflicts`
  ADD CONSTRAINT `conflicts_ibfk_1` FOREIGN KEY (`schedule_id`) REFERENCES `schedules` (`schedule_id`);

--
-- Constraints for table `room_maintenance`
--
ALTER TABLE `room_maintenance`
  ADD CONSTRAINT `room_maintenance_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`);

--
-- Constraints for table `room_usage_log`
--
ALTER TABLE `room_usage_log`
  ADD CONSTRAINT `room_usage_log_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`),
  ADD CONSTRAINT `room_usage_log_ibfk_2` FOREIGN KEY (`schedule_id`) REFERENCES `schedules` (`schedule_id`);

--
-- Constraints for table `schedules`
--
ALTER TABLE `schedules`
  ADD CONSTRAINT `schedules_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `schedules_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`);

--
-- Constraints for table `user_activity_log`
--
ALTER TABLE `user_activity_log`
  ADD CONSTRAINT `user_activity_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
