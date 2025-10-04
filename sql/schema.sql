-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Oct 04, 2025 at 02:10 AM
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
-- Database: `cinema`
--

-- --------------------------------------------------------

--
-- Table structure for table `booking`
--

CREATE TABLE `booking` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `showtime_id` int(10) UNSIGNED NOT NULL,
  `qty` int(10) UNSIGNED NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `booking_status` enum('pending','confirmed','expired','cancelled') NOT NULL DEFAULT 'pending',
  `expires_at` datetime DEFAULT NULL,
  `paid_at` datetime DEFAULT NULL,
  `payment_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking`
--

INSERT INTO `booking` (`id`, `user_id`, `showtime_id`, `qty`, `total_amount`, `booking_status`, `expires_at`, `paid_at`, `payment_id`, `created_at`) VALUES
(4, 1, 1, 2, 16.00, 'confirmed', NULL, '2025-10-01 22:49:21', 1, '2025-10-01 13:49:15'),
(5, 1, 1, 2, 16.00, 'confirmed', NULL, '2025-10-01 23:04:46', 2, '2025-10-01 14:04:43'),
(6, 1, 1, 2, 16.00, 'confirmed', NULL, '2025-10-01 23:12:25', 3, '2025-10-01 14:12:20'),
(7, 1, 1, 2, 16.00, 'confirmed', NULL, '2025-10-01 23:13:44', 4, '2025-10-01 14:13:40'),
(8, 1, 2, 3, 24.00, 'confirmed', NULL, '2025-10-03 00:19:10', 5, '2025-10-02 15:17:28'),
(9, 1, 3, 2, 16.00, 'confirmed', NULL, '2025-10-03 00:27:06', 6, '2025-10-02 15:27:04'),
(10, 1, 2, 2, 24.00, 'confirmed', NULL, '2025-10-03 00:27:48', 7, '2025-10-02 15:27:45'),
(11, 1, 2, 2, 16.00, 'confirmed', NULL, '2025-10-03 00:31:06', 8, '2025-10-02 15:31:05'),
(12, 1, 2, 1, 12.00, 'confirmed', NULL, '2025-10-03 10:51:24', 9, '2025-10-03 01:51:22');

-- --------------------------------------------------------

--
-- Table structure for table `booking_items`
--

CREATE TABLE `booking_items` (
  `id` int(10) UNSIGNED NOT NULL,
  `booking_id` int(10) UNSIGNED NOT NULL,
  `showtime_id` int(10) UNSIGNED NOT NULL,
  `seat_id` int(10) UNSIGNED NOT NULL,
  `price_id` int(10) UNSIGNED NOT NULL,
  `line_amount` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking_items`
--

INSERT INTO `booking_items` (`id`, `booking_id`, `showtime_id`, `seat_id`, `price_id`, `line_amount`) VALUES
(9, 4, 1, 43, 3, 8.00),
(10, 4, 1, 44, 3, 8.00),
(11, 5, 1, 3, 3, 8.00),
(12, 5, 1, 4, 3, 8.00),
(13, 6, 1, 13, 3, 8.00),
(14, 6, 1, 14, 3, 8.00),
(15, 7, 1, 33, 3, 8.00),
(16, 7, 1, 34, 3, 8.00),
(17, 8, 2, 4, 3, 8.00),
(18, 8, 2, 5, 3, 8.00),
(19, 8, 2, 6, 3, 8.00),
(20, 9, 3, 3, 3, 8.00),
(21, 9, 3, 13, 3, 8.00),
(22, 10, 2, 44, 1, 12.00),
(23, 10, 2, 45, 1, 12.00),
(24, 11, 2, 24, 3, 8.00),
(25, 11, 2, 34, 3, 8.00),
(26, 12, 2, 14, 1, 12.00);

-- --------------------------------------------------------

--
-- Table structure for table `halls`
--

CREATE TABLE `halls` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `rows_count` int(10) UNSIGNED NOT NULL,
  `cols_count` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `halls`
--

INSERT INTO `halls` (`id`, `name`, `rows_count`, `cols_count`) VALUES
(1, 'hall 1', 5, 10);

-- --------------------------------------------------------

--
-- Table structure for table `movies`
--

CREATE TABLE `movies` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `runtime_min` int(10) UNSIGNED DEFAULT NULL,
  `rating` enum('G','PG','PG-13','NC-16','M18','R21') DEFAULT NULL,
  `poster_url` varchar(500) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `movies`
--

INSERT INTO `movies` (`id`, `title`, `description`, `runtime_min`, `rating`, `poster_url`, `created_at`) VALUES
(1, 'F1: The Movie', 'A Formula One driver comes out of retirement to mentor and team up with a younger driver.', 155, 'PG-13', 'assets/images/f1movie.jpg', '2025-09-26 04:09:33'),
(2, 'The Conjuring: Last Rites', 'Paranormal investigators Ed and Lorraine Warren take on one last terrifying case involving mysterious entities they must confront.', 135, 'NC-16', 'assets/images/theconjuring.jpg', '2025-09-26 04:12:59');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(10) UNSIGNED NOT NULL,
  `booking_id` int(10) UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `method` varchar(50) NOT NULL,
  `status` enum('initiated','succeeded','failed') NOT NULL DEFAULT 'initiated',
  `transaction_ref` varchar(128) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `booking_id`, `amount`, `method`, `status`, `transaction_ref`, `created_at`) VALUES
(1, 4, 16.00, 'card_visa', 'succeeded', 'TESTTX-d4890e3ccf70', '2025-10-01 13:49:21'),
(2, 5, 16.00, 'google_pay', 'succeeded', 'TESTTX-f3f95a9f183c', '2025-10-01 14:04:46'),
(3, 6, 16.00, 'card_master', 'succeeded', 'TESTTX-5266dfabb345', '2025-10-01 14:12:25'),
(4, 7, 16.00, 'google_pay', 'succeeded', 'TESTTX-e240bac48eca', '2025-10-01 14:13:44'),
(5, 8, 24.00, 'apple_pay', 'succeeded', 'TESTTX-3c41d71a30eb', '2025-10-02 15:19:10'),
(6, 9, 16.00, 'apple_pay', 'succeeded', 'TESTTX-bc978c713efa', '2025-10-02 15:27:06'),
(7, 10, 24.00, 'apple_pay', 'succeeded', 'TESTTX-23c7da48a1b4', '2025-10-02 15:27:48'),
(8, 11, 16.00, 'apple_pay', 'succeeded', 'TESTTX-cada20c27bd4', '2025-10-02 15:31:06'),
(9, 12, 12.00, 'apple_pay', 'succeeded', 'TESTTX-0777e747a50e', '2025-10-03 01:51:24');

-- --------------------------------------------------------

--
-- Table structure for table `pricing`
--

CREATE TABLE `pricing` (
  `id` int(10) UNSIGNED NOT NULL,
  `ticket_type` enum('Adult','Children','Senior') NOT NULL,
  `amount` decimal(8,2) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pricing`
--

INSERT INTO `pricing` (`id`, `ticket_type`, `amount`, `is_active`) VALUES
(1, 'Adult', 12.00, 1),
(2, 'Children', 9.00, 1),
(3, 'Senior', 8.00, 1);

-- --------------------------------------------------------

--
-- Table structure for table `seats`
--

CREATE TABLE `seats` (
  `id` int(10) UNSIGNED NOT NULL,
  `halls_id` int(10) UNSIGNED NOT NULL,
  `row_label` varchar(5) NOT NULL,
  `col_num` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `seats`
--

INSERT INTO `seats` (`id`, `halls_id`, `row_label`, `col_num`) VALUES
(1, 1, 'A', 1),
(2, 1, 'A', 2),
(3, 1, 'A', 3),
(4, 1, 'A', 4),
(5, 1, 'A', 5),
(6, 1, 'A', 6),
(7, 1, 'A', 7),
(8, 1, 'A', 8),
(9, 1, 'A', 9),
(10, 1, 'A', 10),
(11, 1, 'B', 1),
(12, 1, 'B', 2),
(13, 1, 'B', 3),
(14, 1, 'B', 4),
(15, 1, 'B', 5),
(16, 1, 'B', 6),
(17, 1, 'B', 7),
(18, 1, 'B', 8),
(19, 1, 'B', 9),
(20, 1, 'B', 10),
(21, 1, 'C', 1),
(22, 1, 'C', 2),
(23, 1, 'C', 3),
(24, 1, 'C', 4),
(25, 1, 'C', 5),
(26, 1, 'C', 6),
(27, 1, 'C', 7),
(28, 1, 'C', 8),
(29, 1, 'C', 9),
(30, 1, 'C', 10),
(31, 1, 'D', 1),
(32, 1, 'D', 2),
(33, 1, 'D', 3),
(34, 1, 'D', 4),
(35, 1, 'D', 5),
(36, 1, 'D', 6),
(37, 1, 'D', 7),
(38, 1, 'D', 8),
(39, 1, 'D', 9),
(40, 1, 'D', 10),
(41, 1, 'E', 1),
(42, 1, 'E', 2),
(43, 1, 'E', 3),
(44, 1, 'E', 4),
(45, 1, 'E', 5),
(46, 1, 'E', 6),
(47, 1, 'E', 7),
(48, 1, 'E', 8),
(49, 1, 'E', 9),
(50, 1, 'E', 10);

-- --------------------------------------------------------

--
-- Table structure for table `showtimes`
--

CREATE TABLE `showtimes` (
  `id` int(10) UNSIGNED NOT NULL,
  `movies_id` int(10) UNSIGNED NOT NULL,
  `hall_id` int(10) UNSIGNED NOT NULL,
  `starts_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `showtimes`
--

INSERT INTO `showtimes` (`id`, `movies_id`, `hall_id`, `starts_at`) VALUES
(1, 2, 1, '2025-10-06 19:30:00'),
(2, 2, 1, '2025-10-06 22:00:00'),
(3, 1, 1, '2025-10-07 11:00:00'),
(4, 1, 1, '2025-10-07 13:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('user','admin') NOT NULL DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `role`, `created_at`) VALUES
(1, 'demo', 'demo@example.com', '$2y$10$DMzFt2AYY/iyGcsKneY9.u48wJwlCY3tpoMz/LAhPp1VIDtHSRIca', 'user', '2025-09-29 15:42:04');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`id`),
  ADD KEY `showtime_id` (`showtime_id`),
  ADD KEY `idx_bookings_users_time` (`user_id`,`created_at`) USING BTREE,
  ADD KEY `idx_booking_status` (`booking_status`) USING BTREE,
  ADD KEY `idx_booking_expires` (`expires_at`) USING BTREE;

--
-- Indexes for table `booking_items`
--
ALTER TABLE `booking_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_booking_seat` (`booking_id`,`seat_id`) USING BTREE,
  ADD UNIQUE KEY `uq_showtime_seat` (`showtime_id`,`seat_id`),
  ADD KEY `fk_bi_seats` (`seat_id`),
  ADD KEY `fk_bi_price` (`price_id`);

--
-- Indexes for table `halls`
--
ALTER TABLE `halls`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_halls_name` (`name`);

--
-- Indexes for table `movies`
--
ALTER TABLE `movies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_movies_title` (`title`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_pay_status` (`status`),
  ADD KEY `fk_pay_booking` (`booking_id`);

--
-- Indexes for table `pricing`
--
ALTER TABLE `pricing`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_pricing_type` (`ticket_type`) USING BTREE;

--
-- Indexes for table `seats`
--
ALTER TABLE `seats`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_seat_coord` (`halls_id`,`row_label`,`col_num`) USING BTREE;

--
-- Indexes for table `showtimes`
--
ALTER TABLE `showtimes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `movie_id` (`movies_id`),
  ADD KEY `idx_showtimes_start` (`starts_at`),
  ADD KEY `idx_showtimes_movie_start` (`movies_id`,`starts_at`),
  ADD KEY `idx_showtimes_hall` (`hall_id`);

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
-- AUTO_INCREMENT for table `booking`
--
ALTER TABLE `booking`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `booking_items`
--
ALTER TABLE `booking_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `halls`
--
ALTER TABLE `halls`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `movies`
--
ALTER TABLE `movies`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `pricing`
--
ALTER TABLE `pricing`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `seats`
--
ALTER TABLE `seats`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `showtimes`
--
ALTER TABLE `showtimes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `booking`
--
ALTER TABLE `booking`
  ADD CONSTRAINT `fk_booking_showtimes` FOREIGN KEY (`showtime_id`) REFERENCES `showtimes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_booking_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `booking_items`
--
ALTER TABLE `booking_items`
  ADD CONSTRAINT `fk_bi_booking` FOREIGN KEY (`booking_id`) REFERENCES `booking` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_bi_price` FOREIGN KEY (`price_id`) REFERENCES `pricing` (`id`),
  ADD CONSTRAINT `fk_bi_seats` FOREIGN KEY (`seat_id`) REFERENCES `seats` (`id`),
  ADD CONSTRAINT `fk_bi_showtime` FOREIGN KEY (`showtime_id`) REFERENCES `showtimes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `fk_pay_booking` FOREIGN KEY (`booking_id`) REFERENCES `booking` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `seats`
--
ALTER TABLE `seats`
  ADD CONSTRAINT `fk_seats_halls` FOREIGN KEY (`halls_id`) REFERENCES `halls` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `showtimes`
--
ALTER TABLE `showtimes`
  ADD CONSTRAINT `fk_showtimes_halls` FOREIGN KEY (`hall_id`) REFERENCES `halls` (`id`),
  ADD CONSTRAINT `fk_showtimes_movies` FOREIGN KEY (`movies_id`) REFERENCES `movies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
