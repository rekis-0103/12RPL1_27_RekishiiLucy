-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 20, 2025 at 03:49 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `compro`
--

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE `applications` (
  `application_id` int NOT NULL,
  `job_id` int NOT NULL,
  `user_id` int NOT NULL,
  `cv` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `applied_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `galeri`
--

CREATE TABLE `galeri` (
  `galeri_id` int NOT NULL,
  `judul` varchar(200) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `galeri_foto`
--

CREATE TABLE `galeri_foto` (
  `foto_id` int NOT NULL,
  `galeri_id` int DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kegiatan`
--

CREATE TABLE `kegiatan` (
  `kegiatan_id` int NOT NULL,
  `judul` varchar(200) NOT NULL,
  `deskripsi` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kegiatan_foto`
--

CREATE TABLE `kegiatan_foto` (
  `foto_id` int NOT NULL,
  `kegiatan_id` int DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `live_streaming`
--

CREATE TABLE `live_streaming` (
  `streaming_id` int NOT NULL,
  `judul` varchar(200) NOT NULL,
  `tipe` enum('youtube','mp4') NOT NULL,
  `url` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `log_aktivitas`
--

CREATE TABLE `log_aktivitas` (
  `log_id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `log_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `log_aktifitas`
--

INSERT INTO `log_aktifitas` (`log_id`, `user_id`, `action`, `log_time`, `ip_address`) VALUES
(1, 1, 'Login', '2025-08-19 04:10:41', '::1'),
(2, 1, 'Logout', '2025-08-19 04:12:20', '::1'),
(3, 1, 'Login', '2025-08-19 04:13:24', '::1'),
(4, 1, 'Logout', '2025-08-19 04:22:58', '::1'),
(5, 2, 'Login', '2025-08-19 04:24:16', '::1'),
(6, 2, 'Logout', '2025-08-19 06:14:57', '::1'),
(7, 1, 'Login', '2025-08-19 06:20:22', '::1'),
(8, 1, 'Logout', '2025-08-19 07:09:14', '::1'),
(9, 1, 'Login', '2025-08-19 07:15:49', '::1'),
(10, 1, 'Logout', '2025-08-19 07:26:14', '::1'),
(11, 1, 'Login', '2025-08-19 07:26:29', '::1'),
(12, 1, 'Logout', '2025-08-19 07:33:27', '::1'),
(13, 1, 'Login', '2025-08-19 07:33:42', '::1'),
(14, 1, 'Logout', '2025-08-19 08:23:16', '::1'),
(15, 2, 'Login', '2025-08-19 08:23:49', '::1'),
(16, 2, 'Logout', '2025-08-19 08:25:53', '::1'),
(17, 2, 'Login', '2025-08-19 08:56:07', '::1'),
(18, 1, 'Login', '2025-08-20 01:15:41', '::1'),
(19, 1, 'Logout', '2025-08-20 01:16:43', '::1'),
(20, 2, 'Login', '2025-08-20 01:17:02', '::1'),
(21, 2, 'Logout', '2025-08-20 01:17:08', '::1'),
(22, 3, 'Login', '2025-08-20 01:18:09', '::1'),
(23, 3, 'Logout', '2025-08-20 01:52:17', '::1'),
(24, 4, 'Login', '2025-08-20 01:52:52', '::1'),
(25, 4, 'Logout', '2025-08-20 02:18:27', '::1');

-- --------------------------------------------------------

--
-- Table structure for table `lowongan`
--

CREATE TABLE `lowongan` (
  `job_id` int NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `requirements` text,
  `location` varchar(100) DEFAULT NULL,
  `salary_range` varchar(50) DEFAULT NULL,
  `status` enum('open','closed') DEFAULT 'open',
  `posted_by` int DEFAULT NULL,
  `posted_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `hapus` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `role` enum('admin','pelamar','hrd','konten') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'pelamar',
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `hapus` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `email`, `full_name`, `role`, `status`, `created_at`, `hapus`) VALUES
(1, 'admin01', '0192023a7bbd73250516f069df18b500', 'admin@gmail.com', 'Admin', 'admin', 'active', '2025-08-19 03:54:54', 0),
(2, 'pelamar01', '9c5fa085ce256c7c598f6710584ab25d', 'budi@gmail.com', 'Budi Santoso', 'pelamar', 'active', '2025-08-19 03:54:54', 0),
(3, 'hrd01', '5c2e4a2563f9f4427955422fe1402762', 'siti@gmail.com', 'Siti', 'hrd', 'active', '2025-08-19 03:54:54', 0),
(4, 'konten01', '26ed30f28908645239254ff4f88c1b75', 'rian@gmail.com', 'Rian', 'konten', 'active', '2025-08-19 03:54:54', 0);

-- --------------------------------------------------------

--
-- Table structure for table `webinar`
--

CREATE TABLE `webinar` (
  `webinar_id` int NOT NULL,
  `judul` varchar(200) NOT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`application_id`),
  ADD KEY `relasi_user` (`user_id`),
  ADD KEY `relasi_job` (`job_id`);

--
-- Indexes for table `galeri`
--
ALTER TABLE `galeri`
  ADD PRIMARY KEY (`galeri_id`);

--
-- Indexes for table `galeri_foto`
--
ALTER TABLE `galeri_foto`
  ADD PRIMARY KEY (`foto_id`),
  ADD KEY `galeri_id` (`galeri_id`);

--
-- Indexes for table `kegiatan`
--
ALTER TABLE `kegiatan`
  ADD PRIMARY KEY (`kegiatan_id`);

--
-- Indexes for table `kegiatan_foto`
--
ALTER TABLE `kegiatan_foto`
  ADD PRIMARY KEY (`foto_id`),
  ADD KEY `kegiatan_id` (`kegiatan_id`);

--
-- Indexes for table `live_streaming`
--
ALTER TABLE `live_streaming`
  ADD PRIMARY KEY (`streaming_id`);

--
-- Indexes for table `log_aktifitas`
--
ALTER TABLE `log_aktifitas`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `lowongan`
--
ALTER TABLE `lowongan`
  ADD PRIMARY KEY (`job_id`),
  ADD KEY `posted_by` (`posted_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username_2` (`username`);

--
-- Indexes for table `webinar`
--
ALTER TABLE `webinar`
  ADD PRIMARY KEY (`webinar_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `application_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `galeri`
--
ALTER TABLE `galeri`
  MODIFY `galeri_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `galeri_foto`
--
ALTER TABLE `galeri_foto`
  MODIFY `foto_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kegiatan`
--
ALTER TABLE `kegiatan`
  MODIFY `kegiatan_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kegiatan_foto`
--
ALTER TABLE `kegiatan_foto`
  MODIFY `foto_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `live_streaming`
--
ALTER TABLE `live_streaming`
  MODIFY `streaming_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `log_aktifitas`
--
ALTER TABLE `log_aktifitas`
  MODIFY `log_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `lowongan`
--
ALTER TABLE `lowongan`
  MODIFY `job_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `webinar`
--
ALTER TABLE `webinar`
  MODIFY `webinar_id` int NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `applications`
--
ALTER TABLE `applications`
  ADD CONSTRAINT `relasi_job` FOREIGN KEY (`job_id`) REFERENCES `lowongan` (`job_id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  ADD CONSTRAINT `relasi_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `galeri_foto`
--
ALTER TABLE `galeri_foto`
  ADD CONSTRAINT `galeri_foto_ibfk_1` FOREIGN KEY (`galeri_id`) REFERENCES `galeri` (`galeri_id`) ON DELETE CASCADE;

--
-- Constraints for table `kegiatan_foto`
--
ALTER TABLE `kegiatan_foto`
  ADD CONSTRAINT `kegiatan_foto_ibfk_1` FOREIGN KEY (`kegiatan_id`) REFERENCES `kegiatan` (`kegiatan_id`) ON DELETE CASCADE;

--
-- Constraints for table `log_aktifitas`
--
ALTER TABLE `log_aktifitas`
  ADD CONSTRAINT `log_aktifitas_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `lowongan`
--
ALTER TABLE `lowongan`
  ADD CONSTRAINT `lowongan_ibfk_1` FOREIGN KEY (`posted_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
