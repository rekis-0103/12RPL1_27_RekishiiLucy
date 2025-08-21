-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 21, 2025 at 07:11 AM
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
  `reason` text,
  `interview_date` datetime DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `applied_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`application_id`, `job_id`, `user_id`, `cv`, `status`, `reason`, `interview_date`, `start_date`, `applied_at`, `updated_at`) VALUES
(1, 1, 2, 'uploads/cv/cv_2_1_1755673380.pdf', 'diterima bekerja', 'wjijdwjidjidwjiijwdji', '2025-08-20 14:49:00', '2025-08-22', '2025-08-20 07:03:00', '2025-08-21 04:54:48');

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
-- Dumping data for table `log_aktivitas`
--

INSERT INTO `log_aktivitas` (`log_id`, `user_id`, `action`, `log_time`, `ip_address`) VALUES
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
(25, 4, 'Logout', '2025-08-20 02:18:27', '::1'),
(26, 1, 'Login', '2025-08-20 04:19:24', '::1'),
(27, 1, 'Logout', '2025-08-20 06:05:00', '::1'),
(28, 3, 'Login', '2025-08-20 06:06:09', '::1'),
(29, 3, 'Logout', '2025-08-20 06:46:39', '::1'),
(30, 2, 'Login', '2025-08-20 06:46:48', '::1'),
(31, 2, 'Buka halaman Bergabung', '2025-08-20 06:57:54', '::1'),
(32, 2, 'Buka halaman Bergabung', '2025-08-20 06:57:55', '::1'),
(33, 2, 'Buka halaman Bergabung', '2025-08-20 06:57:56', '::1'),
(34, 2, 'Buka halaman Bergabung', '2025-08-20 06:57:56', '::1'),
(35, 2, 'Buka halaman Bergabung', '2025-08-20 06:58:09', '::1'),
(36, 2, 'Logout', '2025-08-20 06:58:51', '::1'),
(37, 3, 'Login', '2025-08-20 06:59:05', '::1'),
(38, 3, 'HRD: tambah lowongan #1 - Programmer Frontend', '2025-08-20 07:01:47', '::1'),
(39, 3, 'Buka halaman Bergabung', '2025-08-20 07:02:05', '::1'),
(40, 3, 'Logout', '2025-08-20 07:02:23', '::1'),
(41, 2, 'Login', '2025-08-20 07:02:36', '::1'),
(42, 2, 'Kirim lamaran (job #1)', '2025-08-20 07:03:00', '::1'),
(43, 2, 'Buka halaman Bergabung', '2025-08-20 07:06:06', '::1'),
(44, 2, 'Buka halaman Bergabung', '2025-08-20 07:09:30', '::1'),
(45, 2, 'Update profil', '2025-08-20 07:12:11', '::1'),
(46, 2, 'Update profil', '2025-08-20 07:12:15', '::1'),
(47, 2, 'Update profil', '2025-08-20 07:12:19', '::1'),
(48, 2, 'Logout', '2025-08-20 07:12:25', '::1'),
(49, 3, 'Login', '2025-08-20 07:12:35', '::1'),
(50, 3, 'HRD: buka detail application #1 => seleksi administrasi', '2025-08-20 07:12:44', '::1'),
(51, 3, 'HRD: terima administrasi application #1 (Programmer Frontend)', '2025-08-20 07:13:34', '::1'),
(52, 3, 'HRD: buka detail application #1 => seleksi administrasi', '2025-08-20 07:15:02', '::1'),
(53, 3, 'HRD: terima administrasi application #1 (Programmer Frontend)', '2025-08-20 07:15:13', '::1'),
(54, 3, 'HRD: buka detail application #1 => seleksi administrasi', '2025-08-20 07:28:43', '::1'),
(55, 3, 'HRD: terima administrasi application #1 (Programmer Frontend)', '2025-08-20 07:28:59', '::1'),
(56, 3, 'HRD: terima bekerja application #1 (Programmer Frontend)', '2025-08-20 07:34:32', '::1'),
(57, 3, 'HRD: terima bekerja application #1 (Programmer Frontend)', '2025-08-20 07:37:40', '::1'),
(58, 3, 'HRD: buka detail application #1 => seleksi administrasi', '2025-08-20 07:38:14', '::1'),
(59, 3, 'HRD: terima administrasi application #1 (Programmer Frontend)', '2025-08-20 07:38:22', '::1'),
(60, 3, 'HRD: terima administrasi application #1 (Programmer Frontend)', '2025-08-20 07:40:32', '::1'),
(61, 3, 'HRD: terima administrasi application #1 (Programmer Frontend)', '2025-08-20 07:41:15', '::1'),
(62, 3, 'HRD: terima administrasi application #1 (Programmer Frontend)', '2025-08-20 07:45:06', '::1'),
(63, 3, 'Logout', '2025-08-20 07:45:35', '::1'),
(64, 3, 'Login', '2025-08-20 07:45:45', '::1'),
(65, 3, 'HRD: terima bekerja application #1 (Programmer Frontend)', '2025-08-20 07:45:56', '::1'),
(66, 3, 'HRD: terima bekerja application #1 (Programmer Frontend)', '2025-08-20 07:47:18', '::1'),
(67, 3, 'Logout', '2025-08-20 07:48:46', '127.0.0.1'),
(68, 3, 'Login', '2025-08-20 07:49:06', '::1'),
(69, 3, 'HRD: buka detail application #1 => seleksi administrasi', '2025-08-20 07:49:37', '::1'),
(70, 3, 'HRD: terima administrasi application #1 (Programmer Frontend)', '2025-08-20 07:49:48', '::1'),
(71, 3, 'HRD: terima administrasi application #1 (Programmer Frontend)', '2025-08-20 07:53:25', '::1'),
(72, 3, 'HRD: terima administrasi application #1 (Programmer Frontend)', '2025-08-20 07:58:06', '::1'),
(73, 1, 'Login', '2025-08-21 04:31:21', '::1'),
(74, 1, 'Logout', '2025-08-21 04:32:55', '::1'),
(75, 3, 'Login', '2025-08-21 04:33:11', '::1'),
(76, 3, 'HRD: terima bekerja application #1 (Programmer Frontend)', '2025-08-21 04:54:52', '::1'),
(77, 3, 'Logout', '2025-08-21 06:35:52', '::1'),
(78, 2, 'Login', '2025-08-21 06:36:07', '::1'),
(79, 2, 'Buka halaman Bergabung', '2025-08-21 06:52:16', '::1'),
(80, 2, 'Buka halaman Bergabung', '2025-08-21 06:56:12', '::1');

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

--
-- Dumping data for table `lowongan`
--

INSERT INTO `lowongan` (`job_id`, `title`, `description`, `requirements`, `location`, `salary_range`, `status`, `posted_by`, `posted_at`, `updated_at`, `hapus`) VALUES
(1, 'Programmer Frontend', 'lorem ipsum dolor sit amet', 'Lulusan S1 Informatika', 'PT. Waindo Specterra', '10.000.000 - 15.000.000', 'open', 3, '2025-08-20 07:01:47', NULL, 0);

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
(2, 'pelamar01', '9c5fa085ce256c7c598f6710584ab25d', 'rekishiilucy123@gmail.com', 'Budi Santoso', 'pelamar', 'active', '2025-08-19 03:54:54', 0),
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
-- Indexes for table `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
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
  MODIFY `application_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
-- AUTO_INCREMENT for table `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  MODIFY `log_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT for table `lowongan`
--
ALTER TABLE `lowongan`
  MODIFY `job_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
-- Constraints for table `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  ADD CONSTRAINT `log_aktivitas_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `lowongan`
--
ALTER TABLE `lowongan`
  ADD CONSTRAINT `lowongan_ibfk_1` FOREIGN KEY (`posted_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
