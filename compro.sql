-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 22, 2025 at 07:54 AM
-- Server version: 8.0.30
-- PHP Version: 8.2.29

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
(1, 1, 2, 'uploads/cv/cv_2_1_1755673380.pdf', 'tes & wawancara', 'ya ya saya setuju dengan anda', '2025-09-18 11:20:00', '2025-08-22', '2025-08-20 07:03:00', '2025-09-12 04:21:50');

-- --------------------------------------------------------

--
-- Table structure for table `content_categories`
--

CREATE TABLE `content_categories` (
  `category_id` int NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `category_description` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `galeri`
--

CREATE TABLE `galeri` (
  `galeri_id` int NOT NULL,
  `judul` varchar(200) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `category_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `galeri`
--

INSERT INTO `galeri` (`galeri_id`, `judul`, `created_at`, `category_id`) VALUES
(1, 'Acara Kemerdekaan Indonesia', '2025-08-26 02:21:27', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `galeri_foto`
--

CREATE TABLE `galeri_foto` (
  `foto_id` int NOT NULL,
  `galeri_id` int DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `galeri_foto`
--

INSERT INTO `galeri_foto` (`foto_id`, `galeri_id`, `foto`) VALUES
(1, 1, 'uploads/galeri/1756174887_3.jpeg'),
(2, 1, 'uploads/galeri/1756174887_2.jpeg'),
(3, 1, 'uploads/galeri/1756174887_1.jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `kegiatan`
--

CREATE TABLE `kegiatan` (
  `kegiatan_id` int NOT NULL,
  `judul` varchar(200) NOT NULL,
  `deskripsi` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `category_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `kegiatan`
--

INSERT INTO `kegiatan` (`kegiatan_id`, `judul`, `deskripsi`, `created_at`, `category_id`) VALUES
(1, 'Kegiatan Outing dan Family Gathering', 'PT. Waindo SpecTerra membuat kegiatan yang dapat mengajak seluruh karyawan dan keluarga, yang tentunya melibatkan suami atau istri serta anak-anak mereka dalam suasana yang penuh keakraban. Rekreasi seluruh karyawan dan keluarganya ini dikemas dalam acara family day. Family day diselenggarakan perusahaan sebagai salah satu bentuk penghargaan perusahaan terhadap karyawan dan keluarganya atas kerja keras dan dukungan yang telah diberikan. Family day ini bertujuan untuk mempererat hubungan antara karyawan dan keluarganya, serta untuk meningkatkan kinerja para karyawan.\r\n\r\nTujuan :\r\n1.Untuk mempererat hubungan dan menghilangkan kepenatan selama bekerja\r\n2.Untuk mengembalikan optimalisasi kinerja karyawan\r\n3.Agar karyawan dan keluarga bisa dapat saling mengenal satu sama lain dan memperkokoh tali silaturahmi\r\n4.Untuk mewujudkan rasa kebersamaan dan kerukunan antar keluarga', '2025-08-21 09:19:16', NULL),
(2, 'Halal Bihalal Virtual saat Pandemi COVID-19', 'Pada Hari Raya Idul Fitri walaupun Tangan Tak Bisa Berjabat dan Tidak Bisa Betatap muka langsung tidak melunturkan semangat untuk saling bermaafan dan kembali fitri,Waindo tetap melaksanakan Halal Bi Halal secara virtual dengan mendengarkan tauziyah yang sangat bermanfaat saat pandemi oleh Ustadzah Bunda Yati', '2025-08-27 03:35:06', NULL),
(3, 'Kegiatan Pembagian Sembako Saat Pandemi Covid-19', 'Program ini dilakukan PT Waindo SpecTerra untuk support dan mengembalikan semangat karyawan dan keluarganya menghadapi Pandemi Covid 19 untuk pembagian sembako bagi yang berkeluarga dan ada voucher belanja bagi yang masih belum menikah. pembagian sembako, diantarkan langsung ke rumah masing - masing dengan menggunakan fasilitas mobil operasional kantor.', '2025-08-29 01:21:53', NULL),
(4, 'Kegiatan Berbagi Saat Ramadhan dan Santunan Anak Yatim', 'Program ini memang biasa dilakukan PT Waindo SpecTerra setiap tahunnya untuk membagikan makanan berbuka untuk anak-anak yatim piatu dan untuk masjid di lokasi tinggal para karyawan atau karyawan membagikan makanan untuk berbuka ke anak jalanan dengan cara membagikan dari dalam mobil karena situasi Covid19.\r\n', '2025-08-29 01:24:26', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `kegiatan_foto`
--

CREATE TABLE `kegiatan_foto` (
  `foto_id` int NOT NULL,
  `kegiatan_id` int DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `kegiatan_foto`
--

INSERT INTO `kegiatan_foto` (`foto_id`, `kegiatan_id`, `foto`) VALUES
(4, 1, 'uploads/kegiatan/1756260233_3__1_.jpeg'),
(5, 1, 'uploads/kegiatan/1756260233_2__1_.jpeg'),
(6, 1, 'uploads/kegiatan/1756260233_1__1_.jpeg'),
(7, 2, 'uploads/kegiatan/1756265706_6.jpeg'),
(9, 2, 'uploads/kegiatan/1756265895_1__2_.jpeg'),
(10, 3, 'uploads/kegiatan/1756430513_3__2_.jpeg'),
(11, 3, 'uploads/kegiatan/1756430513_2__2_.jpeg'),
(12, 3, 'uploads/kegiatan/1756430513_1__3_.jpeg'),
(13, 4, 'uploads/kegiatan/1756430666_3.jpg'),
(14, 4, 'uploads/kegiatan/1756430666_2.jpg'),
(15, 4, 'uploads/kegiatan/1756430666_1__4_.jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `live_streaming`
--

CREATE TABLE `live_streaming` (
  `streaming_id` int NOT NULL,
  `judul` varchar(200) NOT NULL,
  `tipe` enum('youtube','mp4') NOT NULL,
  `url` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `category_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `live_streaming`
--

INSERT INTO `live_streaming` (`streaming_id`, `judul`, `tipe`, `url`, `created_at`, `category_id`) VALUES
(1, 'Live Streaming #1', 'mp4', 'uploads/live/1756174758_1.mp4', '2025-08-26 02:19:18', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `log_aktivitas`
--

CREATE TABLE `log_aktivitas` (
  `log_id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `log_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `log_aktivitas`
--

INSERT INTO `log_aktivitas` (`log_id`, `user_id`, `action`, `log_time`) VALUES
(1, 1, 'Login', '2025-08-19 04:10:41'),
(2, 1, 'Logout', '2025-08-19 04:12:20'),
(3, 1, 'Login', '2025-08-19 04:13:24'),
(4, 1, 'Logout', '2025-08-19 04:22:58'),
(5, 2, 'Login', '2025-08-19 04:24:16'),
(6, 2, 'Logout', '2025-08-19 06:14:57'),
(7, 1, 'Login', '2025-08-19 06:20:22'),
(8, 1, 'Logout', '2025-08-19 07:09:14'),
(9, 1, 'Login', '2025-08-19 07:15:49'),
(10, 1, 'Logout', '2025-08-19 07:26:14'),
(11, 1, 'Login', '2025-08-19 07:26:29'),
(12, 1, 'Logout', '2025-08-19 07:33:27'),
(13, 1, 'Login', '2025-08-19 07:33:42'),
(14, 1, 'Logout', '2025-08-19 08:23:16'),
(15, 2, 'Login', '2025-08-19 08:23:49'),
(16, 2, 'Logout', '2025-08-19 08:25:53'),
(17, 2, 'Login', '2025-08-19 08:56:07'),
(18, 1, 'Login', '2025-08-20 01:15:41'),
(19, 1, 'Logout', '2025-08-20 01:16:43'),
(20, 2, 'Login', '2025-08-20 01:17:02'),
(21, 2, 'Logout', '2025-08-20 01:17:08'),
(22, 3, 'Login', '2025-08-20 01:18:09'),
(23, 3, 'Logout', '2025-08-20 01:52:17'),
(24, 4, 'Login', '2025-08-20 01:52:52'),
(25, 4, 'Logout', '2025-08-20 02:18:27'),
(26, 1, 'Login', '2025-08-20 04:19:24'),
(27, 1, 'Logout', '2025-08-20 06:05:00'),
(28, 3, 'Login', '2025-08-20 06:06:09'),
(29, 3, 'Logout', '2025-08-20 06:46:39'),
(30, 2, 'Login', '2025-08-20 06:46:48'),
(31, 2, 'Buka halaman Bergabung', '2025-08-20 06:57:54'),
(32, 2, 'Buka halaman Bergabung', '2025-08-20 06:57:55'),
(33, 2, 'Buka halaman Bergabung', '2025-08-20 06:57:56'),
(34, 2, 'Buka halaman Bergabung', '2025-08-20 06:57:56'),
(35, 2, 'Buka halaman Bergabung', '2025-08-20 06:58:09'),
(36, 2, 'Logout', '2025-08-20 06:58:51'),
(37, 3, 'Login', '2025-08-20 06:59:05'),
(38, 3, 'HRD: tambah lowongan #1 - Programmer Frontend', '2025-08-20 07:01:47'),
(39, 3, 'Buka halaman Bergabung', '2025-08-20 07:02:05'),
(40, 3, 'Logout', '2025-08-20 07:02:23'),
(41, 2, 'Login', '2025-08-20 07:02:36'),
(42, 2, 'Kirim lamaran (job #1)', '2025-08-20 07:03:00'),
(43, 2, 'Buka halaman Bergabung', '2025-08-20 07:06:06'),
(44, 2, 'Buka halaman Bergabung', '2025-08-20 07:09:30'),
(45, 2, 'Update profil', '2025-08-20 07:12:11'),
(46, 2, 'Update profil', '2025-08-20 07:12:15'),
(47, 2, 'Update profil', '2025-08-20 07:12:19'),
(48, 2, 'Logout', '2025-08-20 07:12:25'),
(49, 3, 'Login', '2025-08-20 07:12:35'),
(50, 3, 'HRD: buka detail application #1 => seleksi administrasi', '2025-08-20 07:12:44'),
(51, 3, 'HRD: terima administrasi application #1 (Programmer Frontend)', '2025-08-20 07:13:34'),
(52, 3, 'HRD: buka detail application #1 => seleksi administrasi', '2025-08-20 07:15:02'),
(53, 3, 'HRD: terima administrasi application #1 (Programmer Frontend)', '2025-08-20 07:15:13'),
(54, 3, 'HRD: buka detail application #1 => seleksi administrasi', '2025-08-20 07:28:43'),
(55, 3, 'HRD: terima administrasi application #1 (Programmer Frontend)', '2025-08-20 07:28:59'),
(56, 3, 'HRD: terima bekerja application #1 (Programmer Frontend)', '2025-08-20 07:34:32'),
(57, 3, 'HRD: terima bekerja application #1 (Programmer Frontend)', '2025-08-20 07:37:40'),
(58, 3, 'HRD: buka detail application #1 => seleksi administrasi', '2025-08-20 07:38:14'),
(59, 3, 'HRD: terima administrasi application #1 (Programmer Frontend)', '2025-08-20 07:38:22'),
(60, 3, 'HRD: terima administrasi application #1 (Programmer Frontend)', '2025-08-20 07:40:32'),
(61, 3, 'HRD: terima administrasi application #1 (Programmer Frontend)', '2025-08-20 07:41:15'),
(62, 3, 'HRD: terima administrasi application #1 (Programmer Frontend)', '2025-08-20 07:45:06'),
(63, 3, 'Logout', '2025-08-20 07:45:35'),
(64, 3, 'Login', '2025-08-20 07:45:45'),
(65, 3, 'HRD: terima bekerja application #1 (Programmer Frontend)', '2025-08-20 07:45:56'),
(66, 3, 'HRD: terima bekerja application #1 (Programmer Frontend)', '2025-08-20 07:47:18'),
(67, 3, 'Logout', '2025-08-20 07:48:46'),
(68, 3, 'Login', '2025-08-20 07:49:06'),
(69, 3, 'HRD: buka detail application #1 => seleksi administrasi', '2025-08-20 07:49:37'),
(70, 3, 'HRD: terima administrasi application #1 (Programmer Frontend)', '2025-08-20 07:49:48'),
(71, 3, 'HRD: terima administrasi application #1 (Programmer Frontend)', '2025-08-20 07:53:25'),
(72, 3, 'HRD: terima administrasi application #1 (Programmer Frontend)', '2025-08-20 07:58:06'),
(73, 1, 'Login', '2025-08-21 04:31:21'),
(74, 1, 'Logout', '2025-08-21 04:32:55'),
(75, 3, 'Login', '2025-08-21 04:33:11'),
(76, 3, 'HRD: terima bekerja application #1 (Programmer Frontend)', '2025-08-21 04:54:52'),
(77, 3, 'Logout', '2025-08-21 06:35:52'),
(78, 2, 'Login', '2025-08-21 06:36:07'),
(79, 2, 'Buka halaman Bergabung', '2025-08-21 06:52:16'),
(80, 2, 'Buka halaman Bergabung', '2025-08-21 06:56:12'),
(81, 2, 'Logout', '2025-08-21 07:31:47'),
(82, 4, 'Login', '2025-08-21 07:59:53'),
(83, 4, 'Konten: tambah kegiatan #1 (Kegiatan test 123)', '2025-08-21 09:19:16'),
(84, 4, 'Buka halaman Bergabung', '2025-08-21 09:20:22'),
(85, 1, 'Login', '2025-08-22 00:50:54'),
(86, 1, 'Logout', '2025-08-22 03:58:08'),
(87, 1, 'Logout', '2025-08-22 04:35:02'),
(88, 1, 'Login', '2025-08-25 01:30:58'),
(89, 1, 'Menambah user baru', '2025-08-25 01:34:19'),
(90, 1, 'Mengubah role user', '2025-08-25 01:35:00'),
(91, 1, 'Mengubah role user', '2025-08-25 01:35:31'),
(92, 1, 'Logout', '2025-08-25 01:37:12'),
(93, 6, 'Login', '2025-08-25 01:37:29'),
(94, 6, 'Logout', '2025-08-25 01:38:06'),
(95, 2, 'Login', '2025-08-25 01:38:14'),
(96, 2, 'Logout', '2025-08-25 01:38:21'),
(97, 7, 'Login', '2025-08-25 01:49:06'),
(98, 7, 'Logout', '2025-08-25 03:46:45'),
(99, 1, 'Login', '2025-08-25 03:46:54'),
(100, 1, 'Logout', '2025-08-25 03:49:16'),
(101, 3, 'Login', '2025-08-25 03:49:29'),
(102, 3, 'Logout', '2025-08-25 03:55:06'),
(103, 2, 'Login', '2025-08-25 03:55:21'),
(104, 2, 'Logout', '2025-08-25 03:56:00'),
(105, 7, 'Login', '2025-08-25 03:56:07'),
(106, 7, 'Logout', '2025-08-25 03:58:05'),
(107, 4, 'Login', '2025-08-25 03:58:20'),
(108, 4, 'Logout', '2025-08-25 04:04:18'),
(109, 1, 'Login', '2025-08-25 04:04:25'),
(110, 1, 'Logout', '2025-08-25 04:10:30'),
(111, 2, 'Login', '2025-08-25 04:10:38'),
(112, 2, 'Logout', '2025-08-25 04:15:59'),
(113, 4, 'Login', '2025-08-25 04:16:07'),
(114, 4, 'Logout', '2025-08-25 04:17:09'),
(115, 3, 'Login', '2025-08-25 04:17:18'),
(116, 3, 'Logout', '2025-08-25 09:27:16'),
(117, 4, 'Login', '2025-08-25 09:27:32'),
(118, 4, 'Login', '2025-08-26 01:46:20'),
(119, 4, 'Konten: tambah webinar #1 (Webinar Waindo Series #1 GIS Enterprise & Dashboard Operation, CSRT, Airbone LiDAR dan Aplikasi Pemanfaatannya)', '2025-08-26 02:09:03'),
(120, 4, 'Logout', '2025-08-26 02:13:34'),
(121, 4, 'Login', '2025-08-26 02:13:44'),
(122, 4, 'Konten: tambah live_streaming #1 (mp4)', '2025-08-26 02:19:18'),
(123, 4, 'Konten: tambah galeri #1 (Acara Kemerdekaan Indonesia)', '2025-08-26 02:21:27'),
(124, 4, 'Logout', '2025-08-26 02:44:29'),
(125, 1, 'Login', '2025-08-26 02:44:35'),
(126, 1, 'Logout', '2025-08-26 02:46:09'),
(127, 4, 'Login', '2025-08-26 02:46:17'),
(128, 4, 'Logout', '2025-08-26 03:51:50'),
(129, 1, 'Login', '2025-08-26 04:04:47'),
(130, 1, 'Logout', '2025-08-26 04:07:21'),
(131, 4, 'Login', '2025-08-26 04:07:32'),
(132, 4, 'Logout', '2025-08-26 04:23:43'),
(133, 3, 'Login', '2025-08-26 04:23:54'),
(134, 3, 'HRD: buka detail application #1 => seleksi administrasi', '2025-08-26 04:45:06'),
(135, 3, 'Logout', '2025-08-26 07:29:57'),
(136, 3, 'Login', '2025-08-26 07:30:05'),
(137, 3, 'Logout', '2025-08-26 07:30:41'),
(138, 3, 'Login', '2025-08-26 07:30:51'),
(139, 3, 'Logout', '2025-08-26 07:31:19'),
(140, 3, 'Login', '2025-08-26 07:31:29'),
(141, 4, 'Login', '2025-08-27 01:54:15'),
(142, 4, 'Konten: edit kegiatan #1 (Kegiatan Outing dan Family Gathering)', '2025-08-27 02:03:53'),
(143, 4, 'Konten: edit webinar #1 (Webinar Waindo Series #1 GIS Enterprise & Dashboard Operation, CSRT, Airbone LiDAR dan Aplikasi Pemanfaatannyaa)', '2025-08-27 02:18:21'),
(144, 4, 'Konten: edit webinar #1 (Webinar Waindo Series #1 GIS Enterprise & Dashboard Operation, CSRT, Airbone LiDAR dan Aplikasi Pemanfaatannya)', '2025-08-27 02:18:29'),
(145, 4, 'Konten: edit webinar #1 (Webinar Waindo Series #1 GIS Enterprise & Dashboard Operation, CSRT, Airbone LiDAR dan Aplikasi Pemanfaatannya)', '2025-08-27 02:19:17'),
(146, 4, 'Konten: edit webinar #1 (Webinar Waindo Series #1 GIS Enterprise & Dashboard Operation, CSRT, Airbone LiDAR dan Aplikasi Pemanfaatannya)', '2025-08-27 02:19:45'),
(147, 4, 'Konten: edit webinar #1 (Webinar Waindo Series #1 GIS Enterprise & Dashboard Operation, CSRT, Airbone LiDAR dan Aplikasi Pemanfaatannya)', '2025-08-27 02:19:47'),
(148, 4, 'Konten: edit webinar #1 (Webinar Waindo Series #1 GIS Enterprise & Dashboard Operation, CSRT, Airbone LiDAR dan Aplikasi Pemanfaatannya)', '2025-08-27 02:19:50'),
(149, 4, 'Konten: tambah kegiatan #2 (Halal Bihalal Virtual saat Pandemi COVID-19)', '2025-08-27 03:35:06'),
(150, 4, 'Konten: tambah webinar #2 (Webinar 2 *Webinar Waindo Series #2 Pembuatan Peta 3D Menggunakan ArcGIS PRO)', '2025-08-27 03:36:38'),
(151, 4, 'Konten: edit kegiatan #2 (Halal Bihalal Virtual saat Pandemi COVID-19)', '2025-08-27 03:38:15'),
(152, 4, 'Konten: tambah galeri #2 (Acara test)', '2025-08-27 03:39:56'),
(153, 4, 'Konten: hapus galeri #2', '2025-08-27 03:40:33'),
(154, 4, 'Logout', '2025-08-27 03:44:14'),
(155, 1, 'Login', '2025-08-27 03:44:21'),
(156, 1, 'Logout', '2025-08-27 07:03:30'),
(157, 2, 'Login', '2025-08-27 07:03:40'),
(158, 4, 'Login', '2025-08-29 01:18:52'),
(159, 4, 'Konten: edit webinar #2 (Webinar Waindo Series #2 Pembuatan Peta 3D Menggunakan ArcGIS PRO)', '2025-08-29 01:19:20'),
(160, 4, 'Konten: tambah webinar #3 (Webinar Waindo Series #3 Technology Updates Low Cost GNSS for Surveying dan Monitoring)', '2025-08-29 01:20:18'),
(161, 4, 'Konten: tambah kegiatan #3 (Kegiatan Pembagian Sembako Saat Pandemi Covid-19)', '2025-08-29 01:21:53'),
(162, 4, 'Konten: tambah kegiatan #4 (Kegiatan Berbagi Saat Ramadhan dan Santunan Anak Yatim)', '2025-08-29 01:24:26'),
(163, 1, 'Login', '2025-09-04 03:20:43'),
(164, 1, 'Logout', '2025-09-04 03:21:43'),
(165, 2, 'Login', '2025-09-04 03:22:12'),
(166, 2, 'Logout', '2025-09-04 03:28:00'),
(167, 3, 'Login', '2025-09-04 03:29:45'),
(168, 3, 'Logout', '2025-09-04 03:36:48'),
(169, 4, 'Login', '2025-09-04 03:36:58'),
(170, 4, 'Logout', '2025-09-04 07:56:36'),
(171, 1, 'Login', '2025-09-10 01:19:39'),
(172, 1, 'Mengedit data user', '2025-09-10 01:20:31'),
(173, 1, 'Mengedit data user', '2025-09-10 01:21:09'),
(174, 1, 'Logout', '2025-09-10 01:39:18'),
(175, 3, 'Login', '2025-09-10 01:39:30'),
(176, 3, 'HRD: ubah status lowongan #1 -> closed', '2025-09-10 01:40:47'),
(177, 3, 'HRD: ubah status lowongan #1 -> open', '2025-09-10 01:40:54'),
(178, 3, 'HRD: ubah status lowongan #1 -> closed', '2025-09-10 01:40:59'),
(179, 3, 'HRD: ubah status lowongan #1 -> open', '2025-09-10 01:41:15'),
(180, 3, 'Logout', '2025-09-10 02:10:48'),
(181, 2, 'Login', '2025-09-10 02:10:56'),
(182, 2, 'Logout', '2025-09-10 02:19:13'),
(183, 2, 'Login', '2025-09-10 02:19:26'),
(184, 2, 'Logout', '2025-09-10 02:47:38'),
(185, 2, 'Login', '2025-09-10 08:22:57'),
(186, 2, 'Login', '2025-09-11 07:04:51'),
(187, 2, 'Logout', '2025-09-11 07:05:04'),
(188, 2, 'Login', '2025-09-11 07:05:13'),
(189, 2, 'Logout', '2025-09-11 07:08:15'),
(190, 1, 'Login', '2025-09-11 07:08:25'),
(191, 1, 'Logout', '2025-09-11 07:08:51'),
(192, 2, 'Login', '2025-09-11 07:09:05'),
(193, 2, 'Logout', '2025-09-11 07:09:10'),
(194, 4, 'Login', '2025-09-11 07:09:18'),
(195, 4, 'Logout', '2025-09-11 07:11:41'),
(196, 3, 'Login', '2025-09-11 07:11:55'),
(197, 3, 'Logout', '2025-09-11 07:25:07'),
(198, 1, 'Login', '2025-09-12 01:25:58'),
(199, 1, 'Logout', '2025-09-12 01:33:41'),
(200, 1, 'Login', '2025-09-12 03:07:07'),
(201, 1, 'Logout', '2025-09-12 03:10:33'),
(202, 2, 'Login', '2025-09-12 03:10:47'),
(203, 2, 'Logout', '2025-09-12 03:17:48'),
(204, 1, 'Login', '2025-09-12 03:18:05'),
(205, 1, 'Logout', '2025-09-12 03:53:57'),
(206, 3, 'Login', '2025-09-12 03:55:56'),
(207, 3, 'HRD: ubah status lowongan #1 -> closed', '2025-09-12 03:57:33'),
(208, 3, 'Logout', '2025-09-12 03:59:11'),
(209, 7, 'Login', '2025-09-12 03:59:18'),
(210, 7, 'Logout', '2025-09-12 03:59:51'),
(211, 7, 'Login', '2025-09-12 03:59:59'),
(212, 7, 'Logout', '2025-09-12 04:02:05'),
(213, 3, 'Login', '2025-09-12 04:02:22'),
(214, 3, 'HRD: terima administrasi application #1 (Programmer Frontend)', '2025-09-12 04:20:54'),
(215, 3, 'HRD: terima administrasi application #1 (Programmer Frontend)', '2025-09-12 04:21:13'),
(216, 3, 'HRD: set interview application #1', '2025-09-12 04:21:50'),
(217, 3, 'Logout', '2025-09-12 04:26:55'),
(218, 1, 'Login', '2025-09-12 04:27:14'),
(219, 1, 'Logout', '2025-09-12 04:29:45'),
(220, 4, 'Login', '2025-09-12 04:29:52'),
(221, 4, 'Logout', '2025-09-12 06:12:08'),
(222, 2, 'Login', '2025-09-12 06:12:24'),
(223, 3, 'Login', '2025-09-13 03:23:43'),
(224, 3, 'HRD: ubah status lowongan #1 -> open', '2025-09-13 03:25:05'),
(225, 3, 'Logout', '2025-09-13 03:25:30'),
(226, 2, 'Login', '2025-09-13 03:26:05'),
(227, 4, 'Login', '2025-09-15 01:47:28'),
(228, 4, 'Logout', '2025-09-15 01:50:19'),
(229, 3, 'Login', '2025-09-15 02:38:55'),
(230, 3, 'HRD: tambah lowongan #2 - Programmer Backend', '2025-09-15 02:52:56'),
(231, 3, 'HRD: edit lowongan #1 - Programmer Frontend', '2025-09-15 02:53:16'),
(232, 3, 'HRD: ubah status lowongan #2 -> closed', '2025-09-15 02:53:18'),
(233, 3, 'Logout', '2025-09-15 03:01:21'),
(234, 2, 'Login', '2025-09-15 03:01:30'),
(235, 2, 'Logout', '2025-09-15 03:56:56'),
(236, 3, 'Login', '2025-09-15 04:05:33'),
(237, 3, 'Logout', '2025-09-15 04:05:52'),
(238, 3, 'Login', '2025-09-15 04:06:25'),
(239, 3, 'HRD: edit lowongan #1 - Programmer Frontend', '2025-09-15 04:07:52'),
(240, 3, 'Logout', '2025-09-15 04:15:05'),
(241, 4, 'Login', '2025-09-15 04:15:24'),
(242, 4, 'Logout', '2025-09-15 07:26:28'),
(243, 4, 'Login', '2025-09-15 07:26:37'),
(244, 4, 'Logout', '2025-09-15 08:44:24'),
(245, 3, 'Login', '2025-09-16 02:00:22'),
(246, 3, 'Logout', '2025-09-16 02:07:18'),
(247, 7, 'Login', '2025-09-16 02:08:30'),
(248, 7, 'Logout', '2025-09-16 05:54:12'),
(249, 1, 'Login', '2025-09-16 05:57:35'),
(250, 1, 'Logout', '2025-09-16 06:55:12'),
(251, 3, 'Login', '2025-09-16 06:55:22'),
(252, 3, 'Login', '2025-09-17 02:26:34'),
(253, 3, 'HRD: tambah popup gambar #1 - Lowongan Terbaru Di PT Waindo', '2025-09-17 02:31:27'),
(254, 3, 'HRD: toggle popup gambar #1 -> aktif', '2025-09-17 02:31:34'),
(255, 3, 'HRD: toggle popup gambar #1 -> nonaktif', '2025-09-17 02:33:35'),
(256, 3, 'HRD: toggle popup gambar #1 -> aktif', '2025-09-17 02:33:38'),
(257, 3, 'HRD: toggle popup gambar #1 -> nonaktif', '2025-09-17 02:36:54'),
(258, 3, 'Logout', '2025-09-17 02:37:47'),
(259, 1, 'Login', '2025-09-17 02:37:54'),
(260, 1, 'Logout', '2025-09-17 02:38:45'),
(261, 3, 'Login', '2025-09-17 02:38:51'),
(262, 3, 'HRD: toggle popup gambar #1 -> aktif', '2025-09-17 02:38:59'),
(263, 3, 'Logout', '2025-09-17 02:39:13'),
(264, 3, 'Login', '2025-09-17 02:47:30'),
(265, 3, 'HRD: tambah popup gambar #2 - Lowongan geospasial', '2025-09-17 06:21:11'),
(266, 3, 'HRD: toggle popup gambar #2 -> aktif', '2025-09-17 06:21:15'),
(267, 3, 'HRD: toggle popup gambar #1 -> aktif', '2025-09-17 06:21:22'),
(268, 3, 'HRD: toggle popup gambar #2 -> aktif', '2025-09-17 06:21:37'),
(269, 3, 'HRD: toggle popup gambar #1 -> aktif', '2025-09-17 08:35:13'),
(270, 3, 'HRD: toggle popup gambar #2 -> aktif', '2025-09-17 08:35:16'),
(271, 3, 'Logout', '2025-09-17 08:38:52'),
(272, 3, 'Login', '2025-09-17 08:39:08'),
(273, 3, 'HRD: toggle popup gambar #1 -> aktif', '2025-09-17 08:40:17'),
(274, 3, 'Login', '2025-09-18 07:32:03'),
(275, 1, 'Login', '2025-09-22 02:45:44'),
(276, 1, 'Logout', '2025-09-22 04:07:41'),
(277, 3, 'Login', '2025-09-22 04:07:52'),
(278, 3, 'Logout', '2025-09-22 04:20:49'),
(279, 4, 'Login', '2025-09-22 04:21:58'),
(280, 4, 'Logout', '2025-09-22 04:24:36'),
(281, 4, 'Login', '2025-09-22 04:26:22');

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
(1, 'Programmer Frontend', 'Jadi Programmer Frontend', '- S1 informasika\r\n- Pengalaman 20 tahun\r\n- mampu bekerja dibawah tekanan\r\n- Fresh Graduate', 'PT. Waindo Specterra', '10.000.000 - 15.000.000', 'open', 3, '2025-08-20 07:01:47', '2025-09-15 04:07:52', 0),
(2, 'Programmer Backend', 'Jadi Programmer Backend', '- Fresh Graduate\r\n- S1 Informatika\r\n- Pengalaman Kerja 10 tahun', 'PT. Waindo Specterra', '20.000.000 - 30.000.000', 'open', 3, '2025-09-15 02:52:56', '2025-09-15 02:53:18', 0);

-- --------------------------------------------------------

--
-- Table structure for table `popup_images`
--

CREATE TABLE `popup_images` (
  `popup_id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `image_filename` varchar(255) NOT NULL,
  `orientation` enum('vertical','horizontal') DEFAULT 'vertical',
  `is_active` tinyint(1) DEFAULT '0',
  `created_by` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `popup_images`
--

INSERT INTO `popup_images` (`popup_id`, `title`, `image_filename`, `orientation`, `is_active`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'Lowongan Terbaru Di PT Waindo', 'popup_vertical_68ca1d7f08849.png', 'vertical', 1, 3, '2025-09-17 02:31:27', '2025-09-17 08:40:17'),
(2, 'Lowongan geospasial', 'popup_horizontal_68ca5357b1b93.jpg', 'horizontal', 1, 3, '2025-09-17 06:21:11', '2025-09-17 08:35:16');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` text,
  `image` varchar(255) DEFAULT NULL,
  `category_id` int NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_by` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `name`, `description`, `image`, `category_id`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'Enterprise Rack Server', 'Server rack enterprise dengan performa tinggi yang dirancang khusus untuk mendukung aplikasi geomatika skala besar. Menyediakan kapasitas penyimpanan besar, kecepatan pemrosesan data spasial yang optimal, serta keandalan 24/7 untuk mendukung kebutuhan survei, pemetaan, hingga analisis geospasial yang kompleks.', 'assets/remotesesing.jpg', 1, 'active', 4, '2025-09-22 07:15:25', '2025-09-22 07:15:25'),
(2, 'Geographic Information System', 'Sistem informasi geografis yang komprehensif untuk pengolahan, analisis, serta visualisasi data spasial. GIS ini membantu dalam pengambilan keputusan berbasis lokasi, pemetaan interaktif, hingga integrasi data multi-sumber sehingga dapat digunakan oleh berbagai sektor, mulai dari tata ruang, lingkungan, hingga infrastruktur.', 'assets/Geograpic-Information-System.jpg', 1, 'active', 4, '2025-09-22 07:15:25', '2025-09-22 07:15:25'),
(3, 'ArcGIS For Desktop', 'Perangkat lunak GIS desktop yang menjadi standar industri dalam analisis geospasial. Menyediakan berbagai tools untuk pemetaan, manajemen data spasial, hingga analisis tingkat lanjut, sehingga sangat ideal digunakan oleh pemerintah, perusahaan, maupun akademisi.', 'assets/arcgis_destop.jpg', 2, 'active', 4, '2025-09-22 07:15:25', '2025-09-22 07:15:25'),
(4, 'ArcGIS Enterprise', 'Platform GIS berbasis enterprise yang dirancang untuk organisasi besar. Mendukung integrasi data spasial lintas divisi, memungkinkan kolaborasi antar pengguna, serta menyediakan kontrol penuh terhadap keamanan dan distribusi informasi geospasial.', 'assets/arcgisportal.jpg', 2, 'active', 4, '2025-09-22 07:15:25', '2025-09-22 07:15:25');

-- --------------------------------------------------------

--
-- Table structure for table `product_categories`
--

CREATE TABLE `product_categories` (
  `category_id` int NOT NULL,
  `category_key` varchar(50) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `category_description` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `product_categories`
--

INSERT INTO `product_categories` (`category_id`, `category_key`, `category_name`, `category_description`, `created_at`, `updated_at`) VALUES
(1, 'geomatic-applications', 'Geomatic Applications', 'Solusi aplikasi geomatika untuk survei, pemetaan, dan analisis geospasial', '2025-09-22 07:15:25', '2025-09-22 07:15:25'),
(2, 'software-provider', 'Software Provider', 'Software dan platform GIS terdepan untuk analisis geospasial dan pemetaan', '2025-09-22 07:15:25', '2025-09-22 07:15:25'),
(3, 'enrm', 'Environment & Natural Resources Management', 'Solusi manajemen lingkungan dan sumber daya alam untuk pembangunan berkelanjutan', '2025-09-22 07:15:25', '2025-09-22 07:15:25'),
(4, 'gis-data-provider', 'GIS Data Provider', 'Penyedia data geospasial dan citra satelit untuk berbagai kebutuhan pemetaan', '2025-09-22 07:15:25', '2025-09-22 07:15:25'),
(5, 'gis-information-technology', 'GIS & Information Technology', 'Aplikasi dan sistem teknologi informasi geografis untuk berbagai platform', '2025-09-22 07:15:25', '2025-09-22 07:15:25');

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
(4, 'konten01', '26ed30f28908645239254ff4f88c1b75', 'rian@gmail.com', 'Rian', 'konten', 'active', '2025-08-19 03:54:54', 0),
(6, 'agus01', '01c3c766ce47082b1b130daedd347ffd', 'agus123@gmail.com', 'Agus Agus', 'hrd', 'active', '2025-08-25 01:34:19', 0),
(7, 'rekis', 'ef14d8aeff3c7255004a18508133b8ad', 'weioewhifewhuifwhui@gmail.com', 'rekishii lucy', 'hrd', 'active', '2025-08-25 01:48:43', 0);

-- --------------------------------------------------------

--
-- Table structure for table `webinar`
--

CREATE TABLE `webinar` (
  `webinar_id` int NOT NULL,
  `judul` varchar(200) NOT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `category_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `webinar`
--

INSERT INTO `webinar` (`webinar_id`, `judul`, `gambar`, `created_at`, `category_id`) VALUES
(1, 'Webinar Waindo Series #1 GIS Enterprise & Dashboard Operation, CSRT, Airbone LiDAR dan Aplikasi Pemanfaatannya', 'uploads/webinar/1756174143_webinar1.jpeg', '2025-08-26 02:09:03', NULL),
(2, 'Webinar Waindo Series #2 Pembuatan Peta 3D Menggunakan ArcGIS PRO', 'uploads/webinar/1756265798_webinar2.jpeg', '2025-08-27 03:36:38', NULL),
(3, 'Webinar Waindo Series #3 Technology Updates Low Cost GNSS for Surveying dan Monitoring', 'uploads/webinar/1756430418_webinar3.jpeg', '2025-08-29 01:20:18', NULL);

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
-- Indexes for table `content_categories`
--
ALTER TABLE `content_categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `category_name` (`category_name`);

--
-- Indexes for table `galeri`
--
ALTER TABLE `galeri`
  ADD PRIMARY KEY (`galeri_id`),
  ADD KEY `fk_galeri_category` (`category_id`);

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
  ADD PRIMARY KEY (`kegiatan_id`),
  ADD KEY `fk_kegiatan_category` (`category_id`);

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
  ADD PRIMARY KEY (`streaming_id`),
  ADD KEY `fk_live_streaming_category` (`category_id`);

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
-- Indexes for table `popup_images`
--
ALTER TABLE `popup_images`
  ADD PRIMARY KEY (`popup_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `product_categories`
--
ALTER TABLE `product_categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `category_key` (`category_key`);

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
  ADD PRIMARY KEY (`webinar_id`),
  ADD KEY `fk_webinar_category` (`category_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `application_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `content_categories`
--
ALTER TABLE `content_categories`
  MODIFY `category_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `galeri`
--
ALTER TABLE `galeri`
  MODIFY `galeri_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `galeri_foto`
--
ALTER TABLE `galeri_foto`
  MODIFY `foto_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `kegiatan`
--
ALTER TABLE `kegiatan`
  MODIFY `kegiatan_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `kegiatan_foto`
--
ALTER TABLE `kegiatan_foto`
  MODIFY `foto_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `live_streaming`
--
ALTER TABLE `live_streaming`
  MODIFY `streaming_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  MODIFY `log_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=282;

--
-- AUTO_INCREMENT for table `lowongan`
--
ALTER TABLE `lowongan`
  MODIFY `job_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `popup_images`
--
ALTER TABLE `popup_images`
  MODIFY `popup_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `product_categories`
--
ALTER TABLE `product_categories`
  MODIFY `category_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `webinar`
--
ALTER TABLE `webinar`
  MODIFY `webinar_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
-- Constraints for table `galeri`
--
ALTER TABLE `galeri`
  ADD CONSTRAINT `fk_galeri_category` FOREIGN KEY (`category_id`) REFERENCES `content_categories` (`category_id`) ON DELETE SET NULL;

--
-- Constraints for table `galeri_foto`
--
ALTER TABLE `galeri_foto`
  ADD CONSTRAINT `galeri_foto_ibfk_1` FOREIGN KEY (`galeri_id`) REFERENCES `galeri` (`galeri_id`) ON DELETE CASCADE;

--
-- Constraints for table `kegiatan`
--
ALTER TABLE `kegiatan`
  ADD CONSTRAINT `fk_kegiatan_category` FOREIGN KEY (`category_id`) REFERENCES `content_categories` (`category_id`) ON DELETE SET NULL;

--
-- Constraints for table `kegiatan_foto`
--
ALTER TABLE `kegiatan_foto`
  ADD CONSTRAINT `kegiatan_foto_ibfk_1` FOREIGN KEY (`kegiatan_id`) REFERENCES `kegiatan` (`kegiatan_id`) ON DELETE CASCADE;

--
-- Constraints for table `live_streaming`
--
ALTER TABLE `live_streaming`
  ADD CONSTRAINT `fk_live_streaming_category` FOREIGN KEY (`category_id`) REFERENCES `content_categories` (`category_id`) ON DELETE SET NULL;

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

--
-- Constraints for table `popup_images`
--
ALTER TABLE `popup_images`
  ADD CONSTRAINT `popup_images_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `product_categories` (`category_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `webinar`
--
ALTER TABLE `webinar`
  ADD CONSTRAINT `fk_webinar_category` FOREIGN KEY (`category_id`) REFERENCES `content_categories` (`category_id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
