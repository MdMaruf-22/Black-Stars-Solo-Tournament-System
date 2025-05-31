-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 31, 2025 at 08:22 PM
-- Server version: 11.6.2-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `efootball_club_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `created_at`) VALUES
(1, 'harun', '$2y$10$npoz99kayTV.KQzR4jHQgOrT/c6IwJkaS0MOlh0kkMgLW5YriYyii', '2025-05-27 14:59:15'),
(2, 'maruf', '$2y$10$2TcXt6d5zsD0WXNUcVOJwed8Wg2p8KmT2vvNEM5n1XaO56A3ZOClG', '2025-05-28 13:48:30');

-- --------------------------------------------------------

--
-- Table structure for table `leagues`
--

CREATE TABLE `leagues` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('upcoming','ongoing','completed') DEFAULT 'upcoming',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leagues`
--

INSERT INTO `leagues` (`id`, `name`, `description`, `status`, `created_at`) VALUES
(2, 'One Night Solo League Season-1', '6 minute time ✅\r\nNo extra❌ No Pk❌\r\n1️⃣ Leg', 'completed', '2025-05-29 18:48:19'),
(4, '𝗕𝗦𝗦 𝗣𝗿𝗼 𝗟𝗲𝗮𝗴𝘂𝗲 - for Academians', 'একজনের সাথে ২ ল্যাগ খেলে নিজের রেজাল্ট নিজে সাবমিট করে দিবেন ভালভাবে চেক করে \r\n\r\nMatch Time: 8 Minutes\r\nExtra: Off\r\nPenalty: Off\r\nCondition: Excellent', 'upcoming', '2025-05-31 12:38:50'),
(5, '𝐁𝐒𝐒 𝐋𝐞𝐠𝐞𝐧𝐝𝐬 𝐋𝐞𝐚𝐠𝐮𝐞 - for Main Team\'s', 'একজনের সাথে ২ ল্যাগ খেলে নিজের রেজাল্ট নিজে সাবমিট করে দিবেন ভালভাবে চেক করে \r\n\r\nMatch Time: 8 Minutes✅\r\nExtra:  ❌\r\nPenalty: ❌\r\nCondition: Excellent ✅', 'upcoming', '2025-05-31 12:42:51'),
(7, 'Test', 'as', 'ongoing', '2025-05-31 15:58:00');

-- --------------------------------------------------------

--
-- Table structure for table `league_matches`
--

CREATE TABLE `league_matches` (
  `id` int(11) NOT NULL,
  `league_id` int(11) NOT NULL,
  `player1_id` int(11) NOT NULL,
  `player2_id` int(11) NOT NULL,
  `match_date` date DEFAULT NULL,
  `result_submitted_by` int(11) DEFAULT NULL,
  `player1_score` int(11) DEFAULT NULL,
  `player2_score` int(11) DEFAULT NULL,
  `winner_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `league_player`
--

CREATE TABLE `league_player` (
  `id` int(11) NOT NULL,
  `league_id` int(11) DEFAULT NULL,
  `player_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `league_player`
--

INSERT INTO `league_player` (`id`, `league_id`, `player_id`, `created_at`) VALUES
(5, 2, 9, '2025-05-29 18:48:48'),
(6, 2, 19, '2025-05-29 18:53:34'),
(7, 2, 14, '2025-05-29 18:56:06'),
(8, 2, 13, '2025-05-29 18:59:44'),
(9, 2, 10, '2025-05-29 19:11:28'),
(10, 2, 11, '2025-05-29 19:13:34'),
(11, 2, 22, '2025-05-30 16:29:40'),
(16, 7, 9, '2025-05-31 16:00:20'),
(17, 7, 8, '2025-05-31 16:02:42');

-- --------------------------------------------------------

--
-- Table structure for table `league_registrations`
--

CREATE TABLE `league_registrations` (
  `id` int(11) NOT NULL,
  `league_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `matches`
--

CREATE TABLE `matches` (
  `id` int(11) NOT NULL,
  `league_id` int(11) DEFAULT NULL,
  `player1_id` int(11) DEFAULT NULL,
  `player2_id` int(11) DEFAULT NULL,
  `match_date` date DEFAULT NULL,
  `player1_score` int(11) DEFAULT NULL,
  `player2_score` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `matches`
--

INSERT INTO `matches` (`id`, `league_id`, `player1_id`, `player2_id`, `match_date`, `player1_score`, `player2_score`, `created_at`) VALUES
(13, 2, 9, 10, NULL, 0, 0, '2025-05-29 19:13:51'),
(14, 2, 10, 9, NULL, 0, 1, '2025-05-29 19:13:51'),
(15, 2, 9, 11, NULL, 0, 0, '2025-05-29 19:13:51'),
(16, 2, 11, 9, NULL, 0, 1, '2025-05-29 19:13:51'),
(17, 2, 9, 13, NULL, 0, 0, '2025-05-29 19:13:51'),
(18, 2, 13, 9, NULL, 0, 1, '2025-05-29 19:13:51'),
(19, 2, 9, 14, NULL, 0, 0, '2025-05-29 19:13:51'),
(20, 2, 14, 9, NULL, 1, 1, '2025-05-29 19:13:51'),
(21, 2, 9, 19, NULL, 0, 0, '2025-05-29 19:13:51'),
(22, 2, 19, 9, NULL, 5, 1, '2025-05-29 19:13:51'),
(23, 2, 10, 11, NULL, 0, 0, '2025-05-29 19:13:51'),
(24, 2, 11, 10, NULL, 1, 0, '2025-05-29 19:13:51'),
(25, 2, 10, 13, NULL, 0, 0, '2025-05-29 19:13:51'),
(26, 2, 13, 10, NULL, 1, 0, '2025-05-29 19:13:51'),
(27, 2, 10, 14, NULL, 0, 0, '2025-05-29 19:13:51'),
(28, 2, 14, 10, NULL, 1, 0, '2025-05-29 19:13:51'),
(29, 2, 10, 19, NULL, 0, 0, '2025-05-29 19:13:51'),
(30, 2, 19, 10, NULL, 1, 0, '2025-05-29 19:13:51'),
(31, 2, 11, 13, NULL, 0, 0, '2025-05-29 19:13:51'),
(32, 2, 13, 11, NULL, 1, 0, '2025-05-29 19:13:51'),
(33, 2, 11, 14, NULL, 0, 0, '2025-05-29 19:13:51'),
(34, 2, 14, 11, NULL, 0, 1, '2025-05-29 19:13:51'),
(35, 2, 11, 19, NULL, 0, 0, '2025-05-29 19:13:51'),
(36, 2, 19, 11, NULL, 0, 0, '2025-05-29 19:13:51'),
(37, 2, 13, 14, NULL, 0, 0, '2025-05-29 19:13:51'),
(38, 2, 14, 13, NULL, 1, 2, '2025-05-29 19:13:51'),
(39, 2, 13, 19, NULL, 1, 1, '2025-05-29 19:13:51'),
(40, 2, 19, 13, NULL, 0, 0, '2025-05-29 19:13:51'),
(41, 2, 14, 19, NULL, 0, 0, '2025-05-29 19:13:51'),
(42, 2, 19, 14, NULL, 0, 1, '2025-05-29 19:13:51'),
(47, 7, 8, 9, NULL, 5, 1, '2025-05-31 16:03:25'),
(48, 7, 9, 8, NULL, 1, 5, '2025-05-31 16:03:25');

-- --------------------------------------------------------

--
-- Table structure for table `tournaments`
--

CREATE TABLE `tournaments` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('registration','ongoing','completed') DEFAULT 'registration',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tournaments`
--

INSERT INTO `tournaments` (`id`, `name`, `start_date`, `end_date`, `status`, `created_at`, `description`) VALUES
(4, 'One Night Solo S-6', '2025-05-30', '2025-05-31', 'completed', '2025-05-30 16:22:41', 'এন্ট্রি ফী: ২০ টাকা\r\n\r\n01828982898 - BKash\r\nSend Money(must)\r\n\r\nটাকা পাঠানোর পর আপনার প্রেরনকৃত নাম্বারের শেষের ৩ ডিজিট লিখে সাবমিট করবেন'),
(5, 'SD', '2025-05-31', '2025-05-31', 'registration', '2025-05-31 16:08:57', 'asd');

-- --------------------------------------------------------

--
-- Table structure for table `tournament_join_requests`
--

CREATE TABLE `tournament_join_requests` (
  `id` int(11) NOT NULL,
  `tournament_id` int(11) NOT NULL,
  `player_id` int(11) NOT NULL,
  `fee_code` varchar(3) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `requested_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tournament_join_requests`
--

INSERT INTO `tournament_join_requests` (`id`, `tournament_id`, `player_id`, `fee_code`, `status`, `requested_at`) VALUES
(18, 4, 14, '297', 'approved', '2025-05-30 16:29:14'),
(19, 4, 21, '390', 'approved', '2025-05-30 16:31:47'),
(20, 4, 24, '977', 'approved', '2025-05-30 16:32:49'),
(21, 4, 8, '696', 'approved', '2025-05-30 16:37:18'),
(22, 4, 11, '111', 'approved', '2025-05-30 16:41:28'),
(23, 4, 13, '138', 'approved', '2025-05-30 16:45:02'),
(24, 4, 25, '697', 'approved', '2025-05-30 17:08:16'),
(25, 4, 9, '898', 'approved', '2025-05-30 17:09:53'),
(26, 5, 8, '111', 'approved', '2025-05-31 16:09:14'),
(27, 5, 9, '123', 'approved', '2025-05-31 16:09:32');

-- --------------------------------------------------------

--
-- Table structure for table `tournament_matches`
--

CREATE TABLE `tournament_matches` (
  `id` int(11) NOT NULL,
  `tournament_id` int(11) DEFAULT NULL,
  `round` varchar(50) DEFAULT NULL,
  `player1_id` int(11) DEFAULT NULL,
  `player2_id` int(11) DEFAULT NULL,
  `player1_score` int(11) DEFAULT NULL,
  `player2_score` int(11) DEFAULT NULL,
  `match_date` date DEFAULT NULL,
  `winner_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tournament_matches`
--

INSERT INTO `tournament_matches` (`id`, `tournament_id`, `round`, `player1_id`, `player2_id`, `player1_score`, `player2_score`, `match_date`, `winner_id`) VALUES
(36, 4, 'Quarterfinal', 25, 14, 1, 4, NULL, NULL),
(37, 4, 'Quarterfinal', 8, 11, 5, 2, NULL, NULL),
(38, 4, 'Quarterfinal', 13, 9, 2, 3, NULL, NULL),
(39, 4, 'Quarterfinal', 21, 24, 1, 2, NULL, NULL),
(40, 4, 'Semifinal', 14, 8, 5, 0, NULL, NULL),
(41, 4, 'Semifinal', 9, 24, 4, 6, NULL, NULL),
(42, 4, 'Final', 14, 24, 2, 1, NULL, NULL),
(46, 5, 'Final', 9, 8, 1, 10, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tournament_players`
--

CREATE TABLE `tournament_players` (
  `tournament_id` int(11) NOT NULL,
  `player_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tournament_players`
--

INSERT INTO `tournament_players` (`tournament_id`, `player_id`) VALUES
(4, 8),
(5, 8),
(4, 9),
(5, 9),
(4, 11),
(4, 13),
(4, 14),
(4, 21),
(4, 24),
(4, 25);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `matches_played` int(11) DEFAULT 0,
  `wins` int(11) DEFAULT 0,
  `losses` int(11) DEFAULT 0,
  `draws` int(11) DEFAULT 0,
  `goals_scored` int(11) DEFAULT 0,
  `goals_conceded` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`, `matches_played`, `wins`, `losses`, `draws`, `goals_scored`, `goals_conceded`) VALUES
(8, 'Maruf', 'marufislam74208@gmail.com', '$2y$10$dDlQpdJJZekPLBtVneZ2BexIwWYCuVgY6DERmFh3IOhAs1MGIzbxq', '2025-05-28 14:19:20', 5, 6, 1, 0, 34, 8),
(9, 'Harunor Rashed', 'mdharunanwer@gmail.com', '$2y$10$lhwvZ1cfLA/C3iQPGh986eljNANuE07L/scthLT3EvbgmfZvo0V1e', '2025-05-28 14:26:33', 5, 1, 6, 0, 8, 34),
(10, 'Emon', 'emon236280@gmail.com', '$2y$10$hnZULkZO6nE7qOOi1QAyJujxlXa6gUhYf8C5eZFb51Euo5ZkDcbxu', '2025-05-28 15:23:01', 0, 0, 0, 0, 0, 0),
(11, 'Fahim89', 'fahim.jui6189@gmail.com', '$2y$10$ekB0M8Ljsr8VPlIl36X3dOjJGMdlaF2xn1plqWr6pYYlO.Bh/YCGy', '2025-05-28 15:25:59', 0, 0, 0, 0, 0, 0),
(12, 'Limon', 'saifourrahmanlimon@gmail.com', '$2y$10$oXhT7ulT5VqRE05iT0BAauIiXSu8we5zyiTTvLo1JHJC9fQksN0Lu', '2025-05-28 15:44:40', 0, 0, 0, 0, 0, 0),
(13, 'Saifour', 'mdlimon@gmail.com', '$2y$10$wZjdsv4qplEx0DiCirKjxOU.WgALQhbSMjYzm0iYBkoZzvI2loGtC', '2025-05-28 15:48:16', 0, 0, 0, 0, 0, 0),
(14, 'SOFI GOAT', 'sofiullahsofi10@gmail.com', '$2y$10$cKB4yylhLYsdAfeCjgot7e8kXmKjbBD8TJv5Uf4ARhqY.rt9W4Z2W', '2025-05-28 16:11:07', 0, 0, 0, 0, 0, 0),
(15, 'Imon', 'imonndas@gmail.com', '$2y$10$I5/pfWqY0bBOrWDmdhTfRe6BTAud05J41tXGRF/Ca/DaZki2rz8SW', '2025-05-28 17:36:56', 0, 0, 0, 0, 0, 0),
(16, 'Shihab', 'tasmirefootball89@gmail.com', '$2y$10$A7IymA8C6.pNUOxHT9WzPeoIXlo43xDYBy7LRuFnknZazWfVHrbBC', '2025-05-28 17:44:02', 0, 0, 0, 0, 0, 0),
(17, 'Arif', 'ariftawsif1@gmail.com', '$2y$10$rqcFbeuDdY/8vQOUH2X7m.cEVogh9QKSZXhHadokJ4mXu7ZeDob/W', '2025-05-28 18:29:27', 0, 0, 0, 0, 0, 0),
(18, 'Auto', 'auto@gmail.com', '$2y$10$ChTRwQfJsGR6515.AKhcRe1eSFPd2tE4osAyQ0.g0aUXh0WRtNPj2', '2025-05-28 19:07:35', 0, 0, 0, 0, 0, 0),
(19, 'Yasin29', 'nafizbk212@gmail.com', '$2y$10$JFA91CuNiNv6fMgzs/lNo.JNnVm/21LFzbNv.8jK57np4Uj1T9YcC', '2025-05-29 18:53:14', 0, 0, 0, 0, 0, 0),
(20, 'Abid', 'rahmanabidxm@gmail.com', '$2y$10$Ql4PRrwCzNPUj1GjNEn.O.1lMF7Chn7P0/kBGGk6wVqY5aLmaOchi', '2025-05-29 19:02:52', 0, 0, 0, 0, 0, 0),
(21, 'Tawsif69', 'tawsifsiddique@gmail.com', '$2y$10$z1FQmO4aUos6julbYp9eFu/mPCRSFPr0nAYEY7OmOFV7GxNIPOiNi', '2025-05-30 16:28:41', 0, 0, 0, 0, 0, 0),
(22, 'Mamun', 'm.mamun.rashid.ctg@gmail.com', '$2y$10$2KMME0Rm0ab3L82YgQwRWO9it6bvu5hdlPlxlRDF3bW8QMX1.wfNW', '2025-05-30 16:28:52', 0, 0, 0, 0, 0, 0),
(23, 'rifazrahman', 'tarif.rifaz2005@gmail.com', '$2y$10$3TOZ4TtI7gVfmW1HO3c2M.j6iBvFO75zgUJeQ3V4g72R.w9EPhjea', '2025-05-30 16:31:12', 0, 0, 0, 0, 0, 0),
(24, 'Foyzul Islam', 'foyzulislam568@gmail.com', '$2y$10$OpV.9H0Xys3FDDoLB5Jw4e9KwbEbk7mZXNbLsRmNLDSc5nkzf6pm6', '2025-05-30 16:31:36', 0, 0, 0, 0, 0, 0),
(25, 'Ashfaqur', 'ashfaqur1098@gmail.com', '$2y$10$nv.tHWJnsq8oiqzITs4uxeFpIM.Laf1vNty4c6QWCQls7ZmK7B8D6', '2025-05-30 17:05:32', 0, 0, 0, 0, 0, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `leagues`
--
ALTER TABLE `leagues`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `league_matches`
--
ALTER TABLE `league_matches`
  ADD PRIMARY KEY (`id`),
  ADD KEY `league_id` (`league_id`),
  ADD KEY `player1_id` (`player1_id`),
  ADD KEY `player2_id` (`player2_id`),
  ADD KEY `winner_id` (`winner_id`),
  ADD KEY `result_submitted_by` (`result_submitted_by`);

--
-- Indexes for table `league_player`
--
ALTER TABLE `league_player`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `league_id` (`league_id`,`player_id`),
  ADD KEY `player_id` (`player_id`);

--
-- Indexes for table `league_registrations`
--
ALTER TABLE `league_registrations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `league_id` (`league_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `matches`
--
ALTER TABLE `matches`
  ADD PRIMARY KEY (`id`),
  ADD KEY `league_id` (`league_id`),
  ADD KEY `player1_id` (`player1_id`),
  ADD KEY `player2_id` (`player2_id`);

--
-- Indexes for table `tournaments`
--
ALTER TABLE `tournaments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tournament_join_requests`
--
ALTER TABLE `tournament_join_requests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tournament_id` (`tournament_id`,`player_id`),
  ADD KEY `player_id` (`player_id`);

--
-- Indexes for table `tournament_matches`
--
ALTER TABLE `tournament_matches`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tournament_id` (`tournament_id`),
  ADD KEY `player1_id` (`player1_id`),
  ADD KEY `player2_id` (`player2_id`),
  ADD KEY `winner_id` (`winner_id`);

--
-- Indexes for table `tournament_players`
--
ALTER TABLE `tournament_players`
  ADD PRIMARY KEY (`tournament_id`,`player_id`),
  ADD KEY `player_id` (`player_id`);

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
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `leagues`
--
ALTER TABLE `leagues`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `league_matches`
--
ALTER TABLE `league_matches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `league_player`
--
ALTER TABLE `league_player`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `league_registrations`
--
ALTER TABLE `league_registrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `matches`
--
ALTER TABLE `matches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `tournaments`
--
ALTER TABLE `tournaments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tournament_join_requests`
--
ALTER TABLE `tournament_join_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `tournament_matches`
--
ALTER TABLE `tournament_matches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `league_matches`
--
ALTER TABLE `league_matches`
  ADD CONSTRAINT `league_matches_ibfk_1` FOREIGN KEY (`league_id`) REFERENCES `leagues` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `league_matches_ibfk_2` FOREIGN KEY (`player1_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `league_matches_ibfk_3` FOREIGN KEY (`player2_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `league_matches_ibfk_4` FOREIGN KEY (`winner_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `league_matches_ibfk_5` FOREIGN KEY (`result_submitted_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `league_player`
--
ALTER TABLE `league_player`
  ADD CONSTRAINT `league_player_ibfk_1` FOREIGN KEY (`league_id`) REFERENCES `leagues` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `league_player_ibfk_2` FOREIGN KEY (`player_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `league_registrations`
--
ALTER TABLE `league_registrations`
  ADD CONSTRAINT `league_registrations_ibfk_1` FOREIGN KEY (`league_id`) REFERENCES `leagues` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `league_registrations_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `matches`
--
ALTER TABLE `matches`
  ADD CONSTRAINT `matches_ibfk_1` FOREIGN KEY (`league_id`) REFERENCES `leagues` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `matches_ibfk_2` FOREIGN KEY (`player1_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `matches_ibfk_3` FOREIGN KEY (`player2_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tournament_join_requests`
--
ALTER TABLE `tournament_join_requests`
  ADD CONSTRAINT `tournament_join_requests_ibfk_1` FOREIGN KEY (`tournament_id`) REFERENCES `tournaments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tournament_join_requests_ibfk_2` FOREIGN KEY (`player_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tournament_matches`
--
ALTER TABLE `tournament_matches`
  ADD CONSTRAINT `tournament_matches_ibfk_1` FOREIGN KEY (`tournament_id`) REFERENCES `tournaments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tournament_matches_ibfk_2` FOREIGN KEY (`player1_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `tournament_matches_ibfk_3` FOREIGN KEY (`player2_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `tournament_matches_ibfk_4` FOREIGN KEY (`winner_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `tournament_players`
--
ALTER TABLE `tournament_players`
  ADD CONSTRAINT `tournament_players_ibfk_1` FOREIGN KEY (`tournament_id`) REFERENCES `tournaments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tournament_players_ibfk_2` FOREIGN KEY (`player_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
