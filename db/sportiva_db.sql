-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 22, 2025 at 07:29 PM
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
-- Database: `sportiva_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `mabar_events`
--

CREATE TABLE `mabar_events` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `sport` varchar(100) NOT NULL,
  `skill_level` varchar(100) NOT NULL,
  `event_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `venue_name` varchar(255) NOT NULL,
  `slots_filled` int(11) NOT NULL,
  `total_slots` int(11) NOT NULL,
  `host_name` varchar(255) NOT NULL,
  `host_logo` varchar(255) NOT NULL,
  `tags` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mabar_events`
--

INSERT INTO `mabar_events` (`id`, `title`, `sport`, `skill_level`, `event_date`, `start_time`, `end_time`, `venue_name`, `slots_filled`, `total_slots`, `host_name`, `host_logo`, `tags`) VALUES
(1, 'Grand Launching Bmx (badminton Mode Xantai)', 'Badminton', 'Newbie - Advanced', '2025-05-20', '20:00:00', '22:00:00', 'Gala Sport Asem Reges', 8, 11, 'Bmx', 'logo_bmx_1749836038110_464.png', 'Booked via AYS'),
(2, 'Bwf Mabar Selasa After Office', 'Badminton', 'Beginner - Intermediate', '2025-05-20', '20:00:00', '22:00:00', 'Gadjah Mada Sport Center', 8, 13, 'Bwf - Badminton With Fun', 'logo_bwf___badminton_with_fun_1749836038104_847.png', 'Booked via AYS,⭐ Superhost');

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

CREATE TABLE `schedules` (
  `id` int(11) NOT NULL,
  `venue_id` int(11) NOT NULL,
  `sport_id` int(11) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `is_booked` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schedules`
--

INSERT INTO `schedules` (`id`, `venue_id`, `sport_id`, `start_time`, `end_time`, `price`, `is_booked`) VALUES
(1, 1, 1, '10:00:00', '11:00:00', 40000.00, 0),
(2, 1, 2, '11:00:00', '12:00:00', 55000.00, 0),
(3, 1, 1, '17:00:00', '18:00:00', 50000.00, 0),
(4, 3, 3, '18:00:00', '19:00:00', 30000.00, 0),
(5, 4, 2, '20:00:00', '21:00:00', 60000.00, 1),
(6, 5, 1, '21:00:00', '22:00:00', 45000.00, 1),
(7, 2, 2, '09:00:00', '10:00:00', 80000.00, 0),
(8, 2, 2, '19:00:00', '20:00:00', 80000.00, 0),
(9, 6, 1, '16:00:00', '17:00:00', 95000.00, 0),
(10, 6, 3, '17:00:00', '18:00:00', 75000.00, 0),
(11, 7, 1, '19:00:00', '20:00:00', 50000.00, 0),
(12, 7, 1, '20:00:00', '21:00:00', 65000.00, 0),
(13, 8, 2, '18:00:00', '19:00:00', 75000.00, 0),
(14, 8, 2, '19:00:00', '20:00:00', 50000.00, 0),
(15, 9, 1, '10:00:00', '11:00:00', 84000.00, 0),
(16, 10, 3, '20:00:00', '21:00:00', 45000.00, 0),
(17, 11, 4, '08:00:00', '09:00:00', 60000.00, 0),
(18, 12, 1, '19:00:00', '20:00:00', 63000.00, 0),
(19, 12, 2, '21:00:00', '22:00:00', 70000.00, 0);

-- --------------------------------------------------------

--
-- Table structure for table `sports`
--

CREATE TABLE `sports` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `icon_svg` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sports`
--

INSERT INTO `sports` (`id`, `name`, `icon_svg`) VALUES
(1, 'Futsal', '<svg ...></svg>'),
(2, 'Badminton', '<svg ...></svg>'),
(3, 'Basket', '<svg ...></svg>'),
(4, 'Tennis', '<svg ...></svg>'),
(5, 'Voli', '<svg ...></svg>');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `created_at`) VALUES
(1, 'Budi Santoso', 'budi.s@gmail.com', '$2y$10$JYo3VvBgEEIYPhBmgoTlsulNhcGKUWkHWPv1hHlKLVpYU1KfRPV1K', '2025-06-14 03:19:27'),
(2, 'Citra Lestari', 'citra.l@gmail.com', '$2y$10$Ss7dNU5jZ7OEA8gs4BJrcOY/vWxbiz/jZ7FhK4mZzzakm3QViM6ii', '2025-06-15 13:11:25'),
(3, 'Nutrisatir', 'asede@gmail.com', '$2y$10$GjJv6T2f62iyB6Bc9Hif3.feKO0tGXliQlkGJFJA7Ei8HSvY30ttO', '2025-06-18 21:30:58'),
(4, 'user', 'user@gmail.com', '$2y$10$waiLCUIQkIghM6eeh0ko6OQwk6rImCWdqbhCzKaNhjR5MbCmZyqZ2', '2025-06-23 22:56:45'),
(5, 'puput', 'puput@gmail.com', '$2y$10$nSqoNCzh9mY/GIZpE73.9.TxjhqgTYEjTbtHdxFjKK3bQQJ3fE7HW', '2025-06-25 01:27:02');

-- --------------------------------------------------------

--
-- Table structure for table `venues`
--

CREATE TABLE `venues` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `city` varchar(100) DEFAULT NULL,
  `description` text NOT NULL,
  `main_image` varchar(255) NOT NULL,
  `rating` decimal(3,2) NOT NULL DEFAULT 4.50,
  `min_price` int(11) NOT NULL DEFAULT 50000
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `venues`
--

INSERT INTO `venues` (`id`, `name`, `address`, `city`, `description`, `main_image`, `rating`, `min_price`) VALUES
(1, 'Metro Atom Futsal & Badminton', 'Pd Pasar Jaya Pasar Baru Metro Atom Plaza Lt. 8', 'Jakarta Pusat', 'Venue futsal dan badminton dengan fasilitas lengkap di pusat Jakarta.', 'metro_atom_futsal_badminton_1749748084309_127.png', 4.76, 30000),
(2, 'Gala Sport Asem Reges', 'Jl. Asem Reges No. 12, Jakarta Pusat', 'Jakarta Pusat', 'Lapangan badminton standar internasional.', 'gala_sport_asem_reges_1749748084308_38.png', 4.81, 80000),
(3, 'The Hawk Basketball', 'Jl. Gatot Subroto Kav. 5, Kuningan', 'Jakarta Selatan', 'Arena basket indoor dengan standar FIBA dan parkir luas.', 'metro_atom_futsal_badminton_1749748084309_127.png', 4.90, 40000),
(4, 'Gadjah Mada Sport Center', 'Jl. Kaliurang KM. 5, Caturtunggal, Sleman', 'Yogyakarta', 'Pusat olahraga terlengkap di Yogyakarta dengan fasilitas Badminton dan Tennis.', 'gala_sport_asem_reges_1749748084308_38.png', 4.85, 60000),
(5, 'Maguwoharjo Futsal', 'Jl. Raya Tajem, Maguwoharjo, Sleman', 'Yogyakarta', 'Lapangan futsal vinyl berkualitas tinggi dekat stadion.', 'gala_sport_asem_reges_1749748084308_38.png', 4.70, 45000),
(6, 'Surabaya Sport Complex', 'Jl. Ahmad Yani No. 117, Gayungan', 'Surabaya', 'Kompleks olahraga modern dengan lapangan Futsal dan Basket.', 'gala_sport_asem_reges_1749748084308_38.png', 4.92, 85000),
(7, 'Champion Futsal', 'Jl. Panjang No. 55, Kebon Jeruk', 'Jakarta Barat', 'Fasilitas futsal modern dengan 2 lapangan interlock standar internasional.', 'metro_atom_futsal_badminton_1749748084309_127.png', 4.75, 65000),
(8, 'Garuda Badminton Hall', 'Jl. MH. Thamrin No. 1, Cikokol', 'Tangerang', 'Hall badminton dengan 8 lapangan karpet vinyl dan pencahayaan yang sangat baik.', 'gala_sport_asem_reges_1749748084308_38.png', 4.88, 75000),
(9, 'Planet Futsal Surabaya', 'Jl. Raya Darmo Permai III, Pradahkalikendal', 'Surabaya', 'Pusat futsal favorit di Surabaya Barat dengan kantin dan area parkir yang luas.', 'metro_atom_futsal_badminton_1749748084309_127.png', 4.60, 95000),
(10, 'Slam Dunk Arena', 'Jl. Cihampelas No. 160, Cipaganti', 'Bandung', 'Lapangan basket indoor premium dengan ring standar NBA dan lantai kayu.', 'gala_sport_asem_reges_1749748084308_38.png', 4.95, 75000),
(11, 'Jogja Tennis Center', 'Jl. Ring Road Utara, Condongcatur, Sleman', 'Yogyakarta', 'Menyediakan lapangan tennis indoor dan outdoor dengan permukaan clay dan hard court.', 'metro_atom_futsal_badminton_1749748084309_127.png', 4.80, 55000),
(12, 'Pro Arena Futsal & Badminton', 'Jl. Pondok Indah No. 1, Pondok Pinang', 'Jakarta Selatan', 'Venue 2-in-1 untuk futsal dan badminton dengan fasilitas bintang lima.', 'gala_sport_asem_reges_1749748084308_38.png', 4.79, 80000);

-- --------------------------------------------------------

--
-- Table structure for table `venue_sports`
--

CREATE TABLE `venue_sports` (
  `venue_id` int(11) NOT NULL,
  `sport_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `venue_sports`
--

INSERT INTO `venue_sports` (`venue_id`, `sport_id`) VALUES
(1, 1),
(1, 2),
(2, 2),
(3, 3),
(4, 2),
(4, 4),
(5, 1),
(6, 1),
(6, 3),
(7, 1),
(8, 2),
(9, 1),
(10, 3),
(11, 4),
(12, 1),
(12, 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `mabar_events`
--
ALTER TABLE `mabar_events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `venue_id` (`venue_id`),
  ADD KEY `fk_schedules_sports` (`sport_id`);

--
-- Indexes for table `sports`
--
ALTER TABLE `sports`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `venues`
--
ALTER TABLE `venues`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `venue_sports`
--
ALTER TABLE `venue_sports`
  ADD PRIMARY KEY (`venue_id`,`sport_id`),
  ADD KEY `sport_id` (`sport_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `mabar_events`
--
ALTER TABLE `mabar_events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `sports`
--
ALTER TABLE `sports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `venues`
--
ALTER TABLE `venues`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `schedules`
--
ALTER TABLE `schedules`
  ADD CONSTRAINT `fk_schedules_sports` FOREIGN KEY (`sport_id`) REFERENCES `sports` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `schedules_ibfk_1` FOREIGN KEY (`venue_id`) REFERENCES `venues` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `venue_sports`
--
ALTER TABLE `venue_sports`
  ADD CONSTRAINT `venue_sports_ibfk_1` FOREIGN KEY (`venue_id`) REFERENCES `venues` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `venue_sports_ibfk_2` FOREIGN KEY (`sport_id`) REFERENCES `sports` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
