-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 14, 2025 at 04:41 AM
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
(12, 1, 2, 1, 12.00, 'confirmed', NULL, '2025-10-03 10:51:24', 9, '2025-10-03 01:51:22'),
(13, 1, 1, 2, 24.00, 'confirmed', NULL, '2025-10-16 17:06:22', NULL, '2025-10-16 09:06:22'),
(14, 1, 4, 1, 12.00, 'confirmed', NULL, '2025-10-16 17:24:59', NULL, '2025-10-16 09:24:59'),
(15, 1, 2, 1, 12.00, 'confirmed', NULL, '2025-10-16 17:25:05', NULL, '2025-10-16 09:25:05'),
(19, 1, 3, 2, 18.00, 'expired', '2025-10-17 10:12:39', NULL, NULL, '2025-10-17 01:57:39'),
(20, 1, 2, 2, 24.00, 'confirmed', NULL, NULL, NULL, '2025-10-17 01:57:55'),
(21, 1, 2, 2, 18.00, 'expired', '2025-10-17 10:13:04', NULL, NULL, '2025-10-17 01:58:04'),
(22, 1, 4, 2, 21.00, 'confirmed', NULL, NULL, NULL, '2025-10-17 04:15:25'),
(23, 1, 1, 2, 24.00, 'confirmed', NULL, NULL, NULL, '2025-10-17 04:16:08'),
(25, 1, 2, 1, 12.00, 'confirmed', NULL, NULL, NULL, '2025-10-17 06:14:53'),
(26, 1, 2, 1, 12.00, 'confirmed', NULL, NULL, NULL, '2025-10-17 06:16:13'),
(27, 1, 2, 1, 12.00, 'confirmed', NULL, NULL, NULL, '2025-10-17 06:26:01'),
(28, 1, 4, 1, 12.00, 'confirmed', NULL, NULL, NULL, '2025-10-17 06:26:46'),
(29, 1, 4, 1, 12.00, 'confirmed', NULL, NULL, NULL, '2025-10-17 06:47:58'),
(30, 1, 4, 1, 12.00, 'confirmed', NULL, NULL, NULL, '2025-10-17 06:49:08'),
(31, 1, 2, 1, 12.00, 'confirmed', NULL, NULL, NULL, '2025-10-17 06:57:01'),
(32, 1, 2, 1, 12.00, 'confirmed', NULL, NULL, NULL, '2025-10-17 07:12:31'),
(33, 1, 2, 1, 12.00, 'confirmed', NULL, NULL, NULL, '2025-10-17 07:13:41'),
(34, 1, 4, 1, 12.00, 'confirmed', NULL, NULL, NULL, '2025-10-17 08:57:51'),
(35, 1, 4, 1, 12.00, 'confirmed', NULL, NULL, NULL, '2025-10-17 09:04:03'),
(36, 1, 3, 1, 12.00, 'confirmed', NULL, NULL, NULL, '2025-10-17 09:06:10'),
(37, 1, 4, 1, 12.00, 'confirmed', NULL, NULL, NULL, '2025-10-17 09:06:17'),
(38, 1, 1, 1, 12.00, 'confirmed', NULL, NULL, NULL, '2025-10-17 09:06:34'),
(39, 1, 2, 1, 12.00, 'confirmed', NULL, NULL, NULL, '2025-10-17 09:06:44'),
(40, 1, 2, 1, 12.00, 'expired', '2025-10-17 17:23:29', NULL, NULL, '2025-10-17 09:08:29'),
(41, 1, 4, 1, 12.00, 'confirmed', '2025-10-17 18:45:23', '2025-10-17 18:30:31', NULL, '2025-10-17 10:30:23'),
(42, 1, 2, 1, 12.00, 'expired', '2025-10-17 18:45:57', NULL, NULL, '2025-10-17 10:30:57'),
(43, 1, 2, 1, 12.00, 'expired', '2025-10-17 18:46:06', NULL, NULL, '2025-10-17 10:31:06'),
(44, 1, 4, 1, 12.00, 'expired', '2025-10-18 16:59:18', NULL, NULL, '2025-10-18 08:44:18'),
(46, 1, 4, 1, 12.00, 'confirmed', '2025-10-19 16:46:23', '2025-10-19 16:31:33', NULL, '2025-10-19 08:31:23'),
(49, 1, 4, 1, 12.00, 'expired', '2025-10-19 16:50:55', NULL, NULL, '2025-10-19 08:35:55'),
(50, 1, 4, 1, 12.00, 'expired', '2025-10-19 18:33:58', NULL, NULL, '2025-10-19 10:18:58'),
(55, 1, 4, 1, 12.00, 'expired', '2025-10-19 18:53:46', NULL, NULL, '2025-10-19 10:38:46'),
(56, 1, 4, 1, 12.00, 'expired', '2025-10-19 19:40:49', NULL, NULL, '2025-10-19 11:25:49'),
(57, 1, 4, 1, 12.00, 'expired', '2025-10-19 19:51:33', NULL, NULL, '2025-10-19 11:36:33'),
(58, 1, 4, 1, 12.00, 'confirmed', NULL, '2025-10-19 20:16:51', NULL, '2025-10-19 12:16:49'),
(65, 1, 4, 1, 12.00, 'confirmed', NULL, '2025-10-19 20:51:50', NULL, '2025-10-19 12:51:49'),
(66, 1, 3, 1, 12.00, 'expired', '2025-10-29 20:02:57', NULL, NULL, '2025-10-29 11:47:57'),
(67, 1, 1, 1, 12.00, 'confirmed', NULL, '2025-11-04 14:58:52', NULL, '2025-11-04 06:58:49'),
(70, 1, 4, 1, 12.00, 'expired', '2025-11-04 16:39:48', NULL, NULL, '2025-11-04 08:24:48'),
(73, 1, 2, 1, 12.00, 'confirmed', NULL, '2025-11-05 16:46:19', NULL, '2025-11-05 08:46:18'),
(75, 1, 2, 1, 12.00, 'confirmed', NULL, '2025-11-05 19:32:34', NULL, '2025-11-05 11:30:17'),
(78, 1, 2, 1, 12.00, 'expired', '2025-11-05 19:50:45', NULL, NULL, '2025-11-05 11:35:45'),
(79, 1, 2, 1, 12.00, 'expired', '2025-11-05 19:51:02', NULL, NULL, '2025-11-05 11:36:02'),
(84, 1, 2, 4, 41.00, 'confirmed', NULL, '2025-11-11 09:42:03', NULL, '2025-11-11 01:40:56'),
(86, 1, 2, 4, 41.00, 'confirmed', NULL, '2025-11-11 09:58:49', NULL, '2025-11-11 01:58:17');

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
(26, 12, 2, 14, 1, 12.00),
(27, 13, 1, 25, 1, 12.00),
(28, 13, 1, 26, 1, 12.00),
(29, 14, 4, 5, 1, 12.00),
(30, 15, 2, 16, 1, 12.00),
(33, 20, 2, 27, 1, 12.00),
(34, 20, 2, 28, 1, 12.00),
(37, 22, 4, 48, 1, 12.00),
(38, 22, 4, 49, 2, 9.00),
(39, 23, 1, 49, 1, 12.00),
(40, 23, 1, 50, 1, 12.00),
(43, 25, 2, 50, 1, 12.00),
(44, 26, 2, 49, 1, 12.00),
(45, 27, 2, 26, 1, 12.00),
(46, 28, 4, 50, 1, 12.00),
(47, 29, 4, 47, 1, 12.00),
(48, 30, 4, 46, 1, 12.00),
(49, 31, 2, 48, 1, 12.00),
(50, 32, 2, 47, 1, 12.00),
(51, 33, 2, 41, 1, 12.00),
(52, 34, 4, 45, 1, 12.00),
(53, 35, 4, 44, 1, 12.00),
(54, 36, 3, 1, 1, 12.00),
(55, 37, 4, 1, 1, 12.00),
(56, 38, 1, 1, 1, 12.00),
(57, 39, 2, 1, 1, 12.00),
(59, 41, 4, 43, 1, 12.00),
(64, 46, 4, 41, 1, 12.00),
(77, 58, 4, 42, 1, 12.00),
(86, 65, 4, 34, 1, 12.00),
(88, 67, 1, 36, 1, 12.00),
(96, 73, 2, 42, 1, 12.00),
(98, 75, 2, 3, 1, 12.00),
(114, 84, 2, 37, 2, 9.00),
(115, 84, 2, 38, 1, 12.00),
(116, 84, 2, 39, 3, 8.00),
(117, 84, 2, 40, 1, 12.00),
(122, 86, 2, 17, 3, 8.00),
(123, 86, 2, 18, 2, 9.00),
(124, 86, 2, 19, 1, 12.00),
(125, 86, 2, 20, 1, 12.00);

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
(2, 'The Conjuring: Last Rites', 'Paranormal investigators Ed and Lorraine Warren take on one last terrifying case involving mysterious entities they must confront.', 135, 'NC-16', 'assets/images/theconjuring.jpg', '2025-09-26 04:12:59'),
(5, 'IE4727: Web app design', 'demo', 15, 'PG', NULL, '2025-11-11 02:00:15');

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
(9, 12, 12.00, 'apple_pay', 'succeeded', 'TESTTX-0777e747a50e', '2025-10-03 01:51:24'),
(10, 20, 24.00, 'card_visa', 'succeeded', 'TESTTX-a0d28c2d8b53', '2025-10-17 01:57:55'),
(11, 22, 21.00, 'card_visa', 'succeeded', 'TESTTX-a2f9bf915c95', '2025-10-17 04:16:11'),
(12, 23, 24.00, 'card_visa', 'succeeded', 'TESTTX-055ef7da8cd8', '2025-10-17 04:16:11'),
(13, 25, 12.00, 'card_visa', 'succeeded', 'TESTTX-2a22f6ec8e5c', '2025-10-17 06:14:56'),
(14, 26, 12.00, 'card_visa', 'succeeded', 'TESTTX-2dd41810aef2', '2025-10-17 06:16:13'),
(15, 27, 12.00, 'card_visa', 'succeeded', 'TESTTX-f42977e6f9f4', '2025-10-17 06:26:01'),
(16, 28, 12.00, 'card_visa', 'succeeded', 'TESTTX-784f6115599a', '2025-10-17 06:26:46'),
(17, 29, 12.00, 'card_visa', 'succeeded', 'TESTTX-90ca9aae9b55', '2025-10-17 06:47:58'),
(18, 30, 12.00, 'card_visa', 'succeeded', 'TESTTX-3bc06e344028', '2025-10-17 06:49:08'),
(19, 31, 12.00, 'card_visa', 'succeeded', 'TESTTX-ec8ee32742b5', '2025-10-17 06:57:01'),
(20, 32, 12.00, 'card_visa', 'succeeded', 'TESTTX-103315e1a2fe', '2025-10-17 07:12:31'),
(21, 33, 12.00, 'card_visa', 'succeeded', 'TESTTX-39a28f65b07d', '2025-10-17 07:13:41'),
(22, 34, 12.00, 'card_visa', 'succeeded', 'TESTTX-612142608aaa', '2025-10-17 08:57:51'),
(23, 35, 12.00, 'card_visa', 'succeeded', 'TESTTX-bd5812862b8a', '2025-10-17 09:04:03'),
(24, 36, 12.00, 'card_visa', 'succeeded', 'TESTTX-1e7e6dee8c2a', '2025-10-17 09:06:49'),
(25, 37, 12.00, 'card_visa', 'succeeded', 'TESTTX-30eb2382fb1b', '2025-10-17 09:06:49'),
(26, 38, 12.00, 'card_visa', 'succeeded', 'TESTTX-2b94a2323534', '2025-10-17 09:06:49'),
(27, 39, 12.00, 'card_visa', 'succeeded', 'TESTTX-54900607a956', '2025-10-17 09:06:49'),
(28, 41, 12.00, 'card_visa', 'succeeded', 'CARD_VISA-348c285cc5', '2025-10-17 10:30:31'),
(29, 46, 12.00, 'gpay', 'succeeded', 'GPAY-6943455d5c', '2025-10-19 08:31:33'),
(30, 58, 12.00, 'card_visa', 'succeeded', 'CARD_VISA-9fcb73b2fa', '2025-10-19 12:16:51'),
(31, 65, 12.00, 'card_visa', 'succeeded', 'CARD_VISA-8f5118df67', '2025-10-19 12:51:50'),
(32, 67, 12.00, 'gpay', 'succeeded', 'GPAY-2d9ca73b35', '2025-11-04 06:58:52'),
(33, 73, 12.00, 'card_visa', 'succeeded', 'CARD_VISA-bf87dd6fe3', '2025-11-05 08:46:19'),
(34, 75, 12.00, 'applepay', 'succeeded', 'APPLEPAY-e483bc0bbb', '2025-11-05 11:32:34'),
(35, 84, 41.00, 'gpay', 'succeeded', 'GPAY-bcd19eacfc', '2025-11-11 01:42:03'),
(36, 86, 41.00, 'applepay', 'succeeded', 'APPLEPAY-ce87c7ac9d', '2025-11-11 01:58:49');

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
(1, 2, 1, '2025-11-11 19:30:00'),
(2, 2, 1, '2025-11-11 22:00:00'),
(3, 1, 1, '2025-11-12 11:00:00'),
(4, 1, 1, '2025-11-12 13:00:00'),
(8, 5, 1, '2025-11-11 10:00:00');

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
(1, 'demo', 'f32ee@cinema.local', '$2y$10$DMzFt2AYY/iyGcsKneY9.u48wJwlCY3tpoMz/LAhPp1VIDtHSRIca', 'user', '2025-09-29 15:42:04'),
(2, 'admin', 'admin@cinema.local', '$2y$10$W3ECZM7xeOiILbDReb6g8OIi4dKD.lHy8wWXFi7BXQJ.L7Hv9u8XG', 'admin', '2025-11-03 06:04:32'),
(3, 'gaven', 'gaven@cinema-local.com', '$2y$10$XzT8S4HMdWvKPDivugirs.1kiJI/FywsqEtWsTmOM7IhzwWbtD0Iq', 'user', '2025-11-04 02:56:18'),
(4, 'gavenzz', 'gavenzz@cinema-local.com', '$2y$10$mgZynZg5jGmimOCKdBBYPOrmGkVO4.Y.2chTkpfglIamr2JjgIxY2', 'user', '2025-11-05 11:19:23'),
(5, 'brandon', 'brandon@cinema-local.com', '$2y$10$./mhujih64Lxr4zUVlUDJ.gxgX9CaAbpp1F4MOXKWBbJOZg.j.JGi', 'user', '2025-11-11 02:01:52');

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
  ADD KEY `fk_bi_seats` (`seat_id`),
  ADD KEY `fk_bi_price` (`price_id`),
  ADD KEY `idx_bi_showtime` (`showtime_id`),
  ADD KEY `idx_bi_seat` (`seat_id`);

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- AUTO_INCREMENT for table `booking_items`
--
ALTER TABLE `booking_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=130;

--
-- AUTO_INCREMENT for table `halls`
--
ALTER TABLE `halls`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `movies`
--
ALTER TABLE `movies`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
