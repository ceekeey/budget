-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 15, 2025 at 03:04 PM
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
-- Database: `budget_tracker`
--

-- --------------------------------------------------------

--
-- Table structure for table `budget_categories`
--

CREATE TABLE `budget_categories` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `category_name` varchar(50) NOT NULL,
  `category_type` enum('travel','financial','spending','other') NOT NULL,
  `budget_limit` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `budget_categories`
--

INSERT INTO `budget_categories` (`id`, `user_id`, `category_name`, `category_type`, `budget_limit`, `created_at`) VALUES
(1, 1, 'education', 'travel', 2000.00, '2025-11-15 12:36:29'),
(2, 1, 'medical', 'spending', 20000.00, '2025-11-15 12:37:34'),
(3, 1, 'abuja trip new', 'financial', 600000.00, '2025-11-15 12:38:16'),
(4, 1, 'security', 'spending', 50000.00, '2025-11-15 13:00:01'),
(5, 1, 'data', 'other', 6000.00, '2025-11-15 13:00:47'),
(6, 1, 'salary', 'financial', 500000.00, '2025-11-15 13:28:02'),
(7, 3, 'education', 'financial', 150000.00, '2025-11-15 13:56:38'),
(8, 3, 'Data', 'spending', 10000.00, '2025-11-15 13:58:41'),
(9, 3, 'travel', 'travel', 50000.00, '2025-11-15 14:00:35');

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `expense_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`id`, `user_id`, `category_id`, `amount`, `description`, `expense_date`, `created_at`) VALUES
(1, 1, 1, 2000.00, '4nd', '2025-11-15', '2025-11-15 12:36:50'),
(2, 1, 2, 2000.00, 'medial', '2025-11-30', '2025-11-15 12:37:51'),
(4, 1, 5, 400.00, '2gb data', '2025-11-15', '2025-11-15 13:02:03'),
(5, 1, 6, 4000.00, 'loan', '2025-11-15', '2025-11-15 13:28:18'),
(6, 1, 1, 1000.00, 'sch fees', '2025-11-15', '2025-11-15 13:29:07'),
(7, 3, 8, 500.00, '2gb data', '2025-11-15', '2025-11-15 13:59:26'),
(8, 3, 7, 82000.00, 'school fees', '2025-11-15', '2025-11-15 13:59:57'),
(9, 3, 9, 35000.00, 'gombe to abuja', '2025-11-15', '2025-11-15 14:00:56');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `full_name`, `created_at`) VALUES
(1, 'ceekeey', 'isahceekeey@gmail.com', '$2y$10$tFuRkQRykcUsN5vbNtkJv.N72GIbwQmuQGBBNd2OEJ2GhWeNLir0i', 'Isah Abdulhameed Haruna new', '2025-11-15 12:21:23'),
(2, 'ubaida', 'ubaidalawan@gmail.com', '$2y$10$AhDeq70wRlzT4pGq5RTgcuULQNmyAadQZq2tBqbt1zcLzXPBygn/y', 'ubaida lawan', '2025-11-15 13:43:45'),
(3, 'kefas', 'kefas404@gmail.com', '$2y$10$4uN4JTyYF5/De6iwq4epx.F17lwlJAEcOM.UKVjqkMJg3zLlmE1sO', 'kwaskebe musa kefas', '2025-11-15 13:54:26');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `budget_categories`
--
ALTER TABLE `budget_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `budget_categories`
--
ALTER TABLE `budget_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `budget_categories`
--
ALTER TABLE `budget_categories`
  ADD CONSTRAINT `budget_categories_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `expenses`
--
ALTER TABLE `expenses`
  ADD CONSTRAINT `expenses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `expenses_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `budget_categories` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
