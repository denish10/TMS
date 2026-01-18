-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 18, 2026 at 11:24 AM
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
-- Database: `db_task_management_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

CREATE TABLE `department` (
  `department_id` int(11) NOT NULL,
  `department_name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `department`
--

INSERT INTO `department` (`department_id`, `department_name`) VALUES
(1, 'Cooperation'),
(2, 'Human Resource'),
(8, 'Accounting'),
(10, 'ITs'),
(11, 'general');

-- --------------------------------------------------------

--
-- Table structure for table `employee_department`
--

CREATE TABLE `employee_department` (
  `users_id` int(11) NOT NULL,
  `department_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employee_department`
--

INSERT INTO `employee_department` (`users_id`, `department_id`) VALUES
(3, 1),
(12, 1),
(16, 1),
(15, 2),
(18, 2),
(13, 8),
(14, 8),
(1, 10),
(11, 10),
(19, 11);

-- --------------------------------------------------------

--
-- Table structure for table `leave_apply`
--

CREATE TABLE `leave_apply` (
  `leave_id` int(11) NOT NULL,
  `users_id` int(11) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `leave_apply`
--

INSERT INTO `leave_apply` (`leave_id`, `users_id`, `subject`, `message`, `start_date`, `end_date`, `created_date`, `status`) VALUES
(2, 3, 'Family Emergency', 'I need to travel to my hometown due to a family emergency.', '2025-08-18', '2025-08-20', '2025-08-13 18:36:06', 'Approved'),
(8, 3, 'sick leave ', 'g', '2025-09-19', '2025-09-25', '2025-09-16 11:11:58', 'Rejected');

-- --------------------------------------------------------

--
-- Table structure for table `task_assign`
--

CREATE TABLE `task_assign` (
  `record_id` int(11) NOT NULL,
  `task_id` int(11) DEFAULT NULL,
  `users_id` int(11) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `task_assign`
--

INSERT INTO `task_assign` (`record_id`, `task_id`, `users_id`, `status`) VALUES
(20, 18, 3, 'Completed'),
(21, 18, 12, 'Completed'),
(22, 18, 16, 'Completed');

-- --------------------------------------------------------

--
-- Table structure for table `task_manage`
--

CREATE TABLE `task_manage` (
  `task_id` int(11) NOT NULL,
  `task_title` varchar(255) DEFAULT NULL,
  `task_description` text DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `users_id` int(11) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `priority` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `task_manage`
--

INSERT INTO `task_manage` (`task_id`, `task_title`, `task_description`, `created_date`, `users_id`, `start_date`, `end_date`, `priority`) VALUES
(18, 'Inventory Audit and Stock Update', 'Conduct a full audit of the warehouse inventory and update discrepancies in the management system', '2025-11-08 22:33:13', NULL, '2025-11-08', '2025-11-09', 'High');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `users_id` int(11) NOT NULL,
  `profile_photo` varchar(255) DEFAULT 'default.png',
  `fullname` varchar(100) DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `mobile` varchar(15) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','staff') NOT NULL DEFAULT 'staff',
  `created_at` datetime DEFAULT NULL,
  `last_login` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`users_id`, `profile_photo`, `fullname`, `username`, `email`, `mobile`, `password`, `role`, `created_at`, `last_login`) VALUES
(1, '1762619625_cropped.png', 'Denish Dhakal', 'denish', 'sudipdhakal345@gmail.com', '9866208236', '$2y$10$IOhhGaG0wn5odPYOpSDzbOsb9d8.DZm/WUFGWpPTZEzXTWscRO/xe', 'admin', '2025-08-13 12:44:19', '2026-01-18 15:51:04'),
(3, 'default.png', 'Priya Karki', 'priyak', 'priya.karki@example.com', '9800000002', '$2y$10$4MJyMAuhZ3oS0jUDVXXKfujj.f4QK1n7NU1mrDH87UhymqmVuDZwK', 'staff', '2025-08-13 12:44:19', '2026-01-17 12:57:07'),
(11, '1762619441_cropped.png', 'Ashap kshetri', 'ashap', 'ashap@gmail.com', '9812345678', '$2y$10$iDbRggoq5mnFmWpbm3u2AuMrz6On//7v7ls3EkblkXxnS1I3jR8tC', 'admin', '2025-11-08 22:07:43', '2025-11-08 22:49:47'),
(12, 'default.png', 'Aashika KC', 'aashika', 'aashika.kc@company.com', '9841123456', '$2y$10$lu44xI4Hodtw1Wu13549pex77CDe3m.zPlt7v6sy9Cbi/YXPF4eUq', 'staff', '2025-11-08 22:07:43', '2025-11-08 22:51:34'),
(13, 'default.png', 'Ramesh Adhikari', 'ramesh_a', 'ramesh.adhikari@gmail.com', '9801122334', '309b9ef639c1ac17e94d364ee5ed24f2f9194868d57866fa088c884ab8789798', 'staff', '2025-11-08 22:07:43', '2025-11-08 22:07:43'),
(14, 'default.png', 'Pratik Gurung', 'pratik_g', 'pratik.gurung@company.com', '9865432109', '5c7aaf50ac01e64fe1ebb11d28924e111324fc561c12fb8784734ae4ad9102bd', 'staff', '2025-11-08 22:07:43', '2025-11-08 22:07:43'),
(15, 'default.png', 'Kritika Pandey', 'kritika_p', 'kritika.pandey@gmail.com', '9818765432', 'b9a128d1aaa3c0fce17f4d7e87cc653a4c4bea8de5cfe345dd247c80a1d1d871', 'staff', '2025-11-08 22:07:43', '2025-11-08 22:07:43'),
(16, 'default.png', 'Deepak Thapa', 'deepak', 'deepak.thapa@company.com', '9845678910', '$2y$10$jPHceUoXJjODk1a34/e7S.Cy1Onk2oGkoMKVN.3qWJ5H9zYpBSLxe', 'staff', '2025-11-08 22:07:43', '2025-11-08 23:11:26'),
(18, 'default.png', 'Niraj Karki', 'niraj_k', 'niraj.karki@company.com', '9821456789', 'd49eeac9028c6ed044bdcdf004eb0f2f3fde19644784649be0885e8b4723e276', 'staff', '2025-11-08 22:07:43', '2025-11-08 22:07:43'),
(19, 'default.png', 'Sneha Maharjan', 'sneha_m', 'sneha.maharjan@gmail.com', '9867123456', '14b3e2c863322aafefd988a0a62e868b00f22473162edced5769a95dea57fdbe', 'staff', '2025-11-08 22:07:43', '2025-11-08 22:07:43');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`department_id`);

--
-- Indexes for table `employee_department`
--
ALTER TABLE `employee_department`
  ADD PRIMARY KEY (`users_id`),
  ADD KEY `fk_empdept_dept` (`department_id`);

--
-- Indexes for table `leave_apply`
--
ALTER TABLE `leave_apply`
  ADD PRIMARY KEY (`leave_id`),
  ADD KEY `users_id` (`users_id`);

--
-- Indexes for table `task_assign`
--
ALTER TABLE `task_assign`
  ADD PRIMARY KEY (`record_id`),
  ADD KEY `task_id` (`task_id`),
  ADD KEY `users_id` (`users_id`);

--
-- Indexes for table `task_manage`
--
ALTER TABLE `task_manage`
  ADD PRIMARY KEY (`task_id`),
  ADD KEY `users_id` (`users_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`users_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `department`
--
ALTER TABLE `department`
  MODIFY `department_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `leave_apply`
--
ALTER TABLE `leave_apply`
  MODIFY `leave_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `task_assign`
--
ALTER TABLE `task_assign`
  MODIFY `record_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `task_manage`
--
ALTER TABLE `task_manage`
  MODIFY `task_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `users_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `employee_department`
--
ALTER TABLE `employee_department`
  ADD CONSTRAINT `fk_empdept_dept` FOREIGN KEY (`department_id`) REFERENCES `department` (`department_id`),
  ADD CONSTRAINT `fk_empdept_users` FOREIGN KEY (`users_id`) REFERENCES `users` (`users_id`) ON DELETE CASCADE;

--
-- Constraints for table `leave_apply`
--
ALTER TABLE `leave_apply`
  ADD CONSTRAINT `fk_leaveapply_users` FOREIGN KEY (`users_id`) REFERENCES `users` (`users_id`);

--
-- Constraints for table `task_assign`
--
ALTER TABLE `task_assign`
  ADD CONSTRAINT `fk_taskassign_task` FOREIGN KEY (`task_id`) REFERENCES `task_manage` (`task_id`),
  ADD CONSTRAINT `fk_taskassign_users` FOREIGN KEY (`users_id`) REFERENCES `users` (`users_id`);

--
-- Constraints for table `task_manage`
--
ALTER TABLE `task_manage`
  ADD CONSTRAINT `fk_taskmanage_users` FOREIGN KEY (`users_id`) REFERENCES `users` (`users_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
