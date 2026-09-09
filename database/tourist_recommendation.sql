-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 09, 2026 at 05:33 PM
-- Server version: 10.4.21-MariaDB
-- PHP Version: 7.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tourist_recommendation`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `name`, `email`, `password`, `created_at`) VALUES
(1, 'Sayali', 'sayali@touristrecommendation.com', '$2y$10$URoYNYdbccH3a.WEnk.hee6B9YMInFUOSrKOzYN3yxMfqwQZhpQxm', '2026-08-30 08:32:43'),
(2, 'Bhumika', 'bhumika@touristrecommendation.com', '$2y$10$8/VybNUsv7YH4pnmUDq6tux39Jku2a.602.RDOciiRZkG5JMgjm5m', '2026-08-30 08:32:43'),
(3, 'Neha', 'neha@touristrecommendation.com', '$2y$10$G5bD8Bz6k8u3zByJY6UpCu2y8y5fL2zBYyMuglp3PS0ajnKQVpTRC', '2026-08-30 08:32:43');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `place_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `review_text` text NOT NULL,
  `experience_text` text DEFAULT NULL,
  `is_public` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `user_id`, `place_id`, `rating`, `review_text`, `experience_text`, `is_public`, `created_at`) VALUES
(1, 1, 1, 5, 'Goa is a beautiful place.', 'I really enjoyed my trip to Goa.', 1, '2026-08-30 14:12:35');

-- --------------------------------------------------------

--
-- Table structure for table `tourist_places`
--

CREATE TABLE `tourist_places` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `location` varchar(255) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `budget` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `best_time` varchar(150) DEFAULT NULL,
  `activities` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `map_link` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tourist_places`
--

INSERT INTO `tourist_places` (`id`, `name`, `location`, `category`, `budget`, `description`, `best_time`, `activities`, `image`, `map_link`, `created_at`) VALUES
(1, 'Goa', 'Goa, India', 'Beach', 'Medium', 'Goa is famous for its beautiful beaches, nightlife, Portuguese architecture and relaxing coastal atmosphere.', 'November to February', 'Beach activities, Water sports, Sightseeing, Shopping', 'goa.jpg', NULL, '2026-08-30 13:51:34'),
(2, 'Manali', 'Manali, Himachal Pradesh, India', 'Adventure', 'Medium', 'Manali is a beautiful mountain destination surrounded by snow-capped peaks, valleys and rivers.', 'October to June', 'Trekking, Snow activities, River rafting, Sightseeing', 'manali.jpg', NULL, '2026-08-30 13:51:34'),
(3, 'Kerala', 'Kerala, India', 'Nature', 'Medium', 'Kerala is known for peaceful backwaters, lush greenery, beaches and traditional houseboats.', 'October to March', 'Houseboat, Backwater tour, Sightseeing, Nature walk', 'kerala.jpg', NULL, '2026-08-30 13:51:34');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `created_at`) VALUES
(1, 'samarth rane', 'sam3040@gmail.com', '$2y$10$7anQpo2zO.mdy0wwaCEayeHD//LYg/0CRQ7rP9zeYFJKttfJPL3pK', '2026-08-30 08:39:35');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `place_id` (`place_id`);

--
-- Indexes for table `tourist_places`
--
ALTER TABLE `tourist_places`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tourist_places`
--
ALTER TABLE `tourist_places`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`place_id`) REFERENCES `tourist_places` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
