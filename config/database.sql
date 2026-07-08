-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 07, 2026 at 05:57 PM
-- Server version: 5.7.44-cll-lve
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `heriansyah_gudang`
--

-- --------------------------------------------------------

--
-- Table structure for table `barang_keluar`
--

CREATE TABLE `barang_keluar` (
  `id` int(11) NOT NULL,
  `id_barang` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `alasan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal` date NOT NULL,
  `diinput_oleh` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `barang_keluar`
--

INSERT INTO `barang_keluar` (`id`, `id_barang`, `jumlah`, `alasan`, `tanggal`, `diinput_oleh`, `created_at`) VALUES
(22, 21, 1, 'Dipinjam oleh Bon Gunna Roxanne (123)', '2026-07-05', 'Admin Gudang', '2026-07-05 08:20:40'),
(23, 21, 1, 'Dipinjam oleh Elham Rest Shanks (222)', '2026-07-05', 'Staff Gudang', '2026-07-05 08:35:19'),
(24, 21, 1, 'Dipinjam oleh Guti Adik Bon (477849393051992065)', '2026-07-05', 'Staff Gudang', '2026-07-05 08:44:01'),
(25, 7, 3036, 'ngosongin', '2026-07-05', 'Staff Gudang', '2026-07-05 08:54:26'),
(26, 5, 400, 'salah tulis gw tadi', '2026-07-05', 'Staff Gudang', '2026-07-05 08:58:07'),
(27, 21, 11, 'ngosongin', '2026-07-05', 'Staff Gudang', '2026-07-05 08:58:52'),
(28, 20, 1, 'Dipinjam oleh Vodra Bocil Gak Ngangkat (1384654444624609331)', '2026-07-05', 'Staff Gudang', '2026-07-05 09:02:22'),
(29, 19, 1, 'Dipinjam oleh Taluz Pengen Ganteng (535787845403672596)', '2026-07-05', 'Staff Gudang', '2026-07-05 09:03:00'),
(30, 28, 1, 'Dibeli', '2026-07-05', 'Admin Gudang', '2026-07-05 09:12:01'),
(31, 27, 2, 'bon pegangan', '2026-07-05', 'Staff Gudang', '2026-07-05 09:19:15'),
(32, 15, 1, 'Dipinjam oleh Ko Jos (1301800426965962833)', '2026-07-05', 'Staff Gudang', '2026-07-05 10:32:45'),
(33, 10, 1, 'Dipinjam oleh Ko Jos (1301800426965962833)', '2026-07-05', 'Staff Gudang', '2026-07-05 10:33:10'),
(34, 20, 1, 'Dipinjam oleh Mama Van (346216369437802496)', '2026-07-05', 'Staff Gudang', '2026-07-05 10:59:39'),
(35, 19, 1, 'Dipinjam oleh Mama Van (346216369437802496)', '2026-07-05', 'Staff Gudang', '2026-07-05 10:59:47'),
(36, 20, 1, 'Dipinjam oleh Six (1279933788591362138)', '2026-07-05', 'Admin Gudang', '2026-07-05 13:49:02'),
(37, 19, 1, 'Dipinjam oleh Sunduy Ga Ngevlog (616598017910374410)', '2026-07-05', 'Staff Gudang', '2026-07-05 14:22:45'),
(38, 14, 1, 'Dipinjam oleh Patrick Jawa Imut (955856494409162772)', '2026-07-05', 'Staff Gudang', '2026-07-05 14:29:16'),
(39, 14, 1, 'prepare', '2026-07-05', 'Staff Gudang', '2026-07-05 15:44:55'),
(40, 14, 1, 'Dipinjam oleh Lexy Mau Jadi Patem (1458072280373395581)', '2026-07-05', 'Staff Gudang', '2026-07-05 15:46:36'),
(41, 15, 1, 'Dipinjam oleh Taluz Pengen Ganteng (535787845403672596)', '2026-07-05', 'Staff Gudang', '2026-07-05 15:47:04'),
(42, 15, 1, 'Dipinjam oleh Petter Punya Spa (737344630743498752)', '2026-07-05', 'Staff Gudang', '2026-07-05 15:48:53'),
(43, 14, 1, 'Dipinjam oleh Galang Nocturnal (1479889568940425328)', '2026-07-06', 'Staff Gudang', '2026-07-05 17:40:35'),
(44, 19, 1, 'Dibeli badol', '2026-07-06', 'Staff Gudang', '2026-07-06 16:21:00'),
(45, 26, 1, 'Dijual oleh Baba Collin (967090176310538270)', '2026-07-07', 'Admin Gudang', '2026-07-07 10:53:29'),
(46, 26, 1, 'Dijual oleh Baba Collin (967090176310538270)', '2026-07-07', 'Admin Gudang', '2026-07-07 10:55:27');

-- --------------------------------------------------------

--
-- Table structure for table `barang_masuk`
--

CREATE TABLE `barang_masuk` (
  `id` int(11) NOT NULL,
  `id_barang` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `alasan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal` date NOT NULL,
  `diinput_oleh` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `barang_masuk`
--

INSERT INTO `barang_masuk` (`id`, `id_barang`, `jumlah`, `alasan`, `tanggal`, `diinput_oleh`, `created_at`) VALUES
(19, 21, 1, 'Dikembalikan oleh Elham Rest Shanks (222)', '2026-07-05', 'Staff Gudang', '2026-07-05 08:35:30'),
(20, 7, 2982, NULL, '2026-07-05', 'Staff Gudang', '2026-07-05 08:53:54'),
(21, 26, 810, NULL, '2026-07-05', 'Staff Gudang', '2026-07-05 08:55:08'),
(22, 6, 3560, NULL, '2026-07-05', 'Staff Gudang', '2026-07-05 08:55:26'),
(23, 5, 3698, NULL, '2026-07-05', 'Staff Gudang', '2026-07-05 08:57:44'),
(24, 7, 2982, NULL, '2026-07-05', 'Staff Gudang', '2026-07-05 08:58:24'),
(25, 21, 5, NULL, '2026-07-05', 'Staff Gudang', '2026-07-05 08:58:43'),
(26, 21, 5, NULL, '2026-07-05', 'Staff Gudang', '2026-07-05 08:58:56'),
(27, 20, 19, NULL, '2026-07-05', 'Staff Gudang', '2026-07-05 08:59:15'),
(28, 13, 9, NULL, '2026-07-05', 'Staff Gudang', '2026-07-05 08:59:27'),
(29, 18, 16, NULL, '2026-07-05', 'Staff Gudang', '2026-07-05 08:59:35'),
(30, 11, 2, NULL, '2026-07-05', 'Staff Gudang', '2026-07-05 08:59:48'),
(31, 15, 11, NULL, '2026-07-05', 'Staff Gudang', '2026-07-05 08:59:59'),
(32, 15, 5, NULL, '2026-07-05', 'Staff Gudang', '2026-07-05 09:00:27'),
(33, 14, 11, NULL, '2026-07-05', 'Staff Gudang', '2026-07-05 09:00:37'),
(34, 17, 6, NULL, '2026-07-05', 'Staff Gudang', '2026-07-05 09:00:45'),
(35, 12, 18, NULL, '2026-07-05', 'Staff Gudang', '2026-07-05 09:00:57'),
(36, 19, 2, NULL, '2026-07-05', 'Staff Gudang', '2026-07-05 09:01:39'),
(37, 9, 2, NULL, '2026-07-05', 'Staff Gudang', '2026-07-05 09:01:51'),
(38, 10, 2, NULL, '2026-07-05', 'Staff Gudang', '2026-07-05 09:02:08'),
(39, 21, 1, 'Dikembalikan oleh Bon Gunna Roxanne (404315517956915202)', '2026-07-05', 'Staff Gudang', '2026-07-05 09:03:07'),
(40, 21, 1, 'Dikembalikan oleh Guti Adik Bon (477849393051992065)', '2026-07-05', 'Staff Gudang', '2026-07-05 09:05:00'),
(41, 27, 10, NULL, '2026-07-05', 'Staff Gudang', '2026-07-05 09:11:38'),
(42, 28, 1, NULL, '2026-07-05', 'Admin Gudang', '2026-07-05 09:11:41'),
(43, 28, 272, NULL, '2026-07-05', 'Staff Gudang', '2026-07-05 09:12:41'),
(44, 41, 18, NULL, '2026-07-05', 'Staff Gudang', '2026-07-05 09:17:20'),
(45, 38, 3, NULL, '2026-07-05', 'Staff Gudang', '2026-07-05 09:17:32'),
(46, 42, 1, NULL, '2026-07-05', 'Staff Gudang', '2026-07-05 09:17:44'),
(47, 36, 10, NULL, '2026-07-05', 'Staff Gudang', '2026-07-05 09:17:50'),
(48, 37, 12, NULL, '2026-07-05', 'Staff Gudang', '2026-07-05 09:18:01'),
(49, 39, 17, NULL, '2026-07-05', 'Staff Gudang', '2026-07-05 09:18:11'),
(50, 43, 1, NULL, '2026-07-05', 'Staff Gudang', '2026-07-05 09:18:20'),
(51, 48, 1, NULL, '2026-07-05', 'Staff Gudang', '2026-07-05 09:18:27'),
(52, 22, 1, NULL, '2026-07-05', 'Staff Gudang', '2026-07-05 13:45:06'),
(53, 10, 1, NULL, '2026-07-05', 'Staff Gudang', '2026-07-05 13:46:07'),
(54, 9, 1, NULL, '2026-07-05', 'Staff Gudang', '2026-07-05 13:47:10'),
(55, 18, 1, NULL, '2026-07-05', 'Staff Gudang', '2026-07-05 13:47:58'),
(56, 20, 1, 'Dikembalikan oleh Six (1279933788591362138)', '2026-07-05', 'Admin Gudang', '2026-07-05 13:49:14'),
(57, 15, 1, NULL, '2026-07-05', 'Staff Gudang', '2026-07-05 13:53:12'),
(58, 17, 1, NULL, '2026-07-05', 'Staff Gudang', '2026-07-05 13:53:31'),
(59, 11, 1, NULL, '2026-07-05', 'Staff Gudang', '2026-07-05 13:53:43'),
(60, 19, 1, NULL, '2026-07-05', 'Staff Gudang', '2026-07-05 13:54:06'),
(61, 21, 1, NULL, '2026-07-05', 'Staff Gudang', '2026-07-05 13:54:09'),
(62, 27, 40, NULL, '2026-07-05', 'Staff Gudang', '2026-07-05 14:00:07'),
(63, 14, 1, NULL, '2026-07-05', 'Staff Gudang', '2026-07-05 15:48:09'),
(64, 19, 1, 'Dikembalikan oleh Sunduy Ga Ngevlog (616598017910374410)', '2026-07-06', 'Staff Gudang', '2026-07-05 18:44:40'),
(65, 15, 1, 'Dikembalikan oleh Petter Punya Spa (737344630743498752)', '2026-07-06', 'Staff Gudang', '2026-07-05 18:55:59'),
(66, 14, 1, 'Dikembalikan oleh Lexy Mau Jadi Patem (1458072280373395581)', '2026-07-06', 'Staff Gudang', '2026-07-06 07:46:41'),
(67, 15, 1, 'Dikembalikan oleh Taluz Pengen Ganteng (535787845403672596)', '2026-07-06', 'Staff Gudang', '2026-07-06 16:21:50');

-- --------------------------------------------------------

--
-- Table structure for table `homies`
--

CREATE TABLE `homies` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nomor_hp` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cid` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `homies`
--

INSERT INTO `homies` (`id`, `nama`, `nomor_hp`, `cid`, `created_at`) VALUES
(5, 'Baba Collin', NULL, '967090176310538270', '2026-07-04 05:47:35'),
(6, 'Six', '+44 7507 709577', '1279933788591362138', '2026-07-04 07:33:01'),
(7, 'Bon Gunna Roxanne', NULL, '404315517956915202', '2026-07-05 08:20:33'),
(8, 'Elham Tua Banget', NULL, '539845830568443904', '2026-07-05 08:20:50'),
(9, 'Mama Van', NULL, '346216369437802496', '2026-07-05 08:20:57'),
(10, 'Rafi Mau Nikah 2026', NULL, '724091230300012575', '2026-07-05 08:21:16'),
(11, 'Joo Dewa Perang', NULL, '708976863686557706', '2026-07-05 08:21:28'),
(12, 'Ko Jos', NULL, '1301800426965962833', '2026-07-05 08:21:42'),
(13, 'Rexter Sahabat Mungkung', NULL, '369925395593560074', '2026-07-05 08:21:51'),
(14, 'Chelsea', NULL, '592694360466915348', '2026-07-05 08:22:09'),
(15, 'Sunduy Ga Ngevlog', NULL, '616598017910374410', '2026-07-05 08:22:26'),
(16, 'Dupan Raja Brangkas', NULL, '201576375277191168', '2026-07-05 08:22:34'),
(17, 'Juki Laptop Matot', NULL, '1032681136473767998', '2026-07-05 08:22:44'),
(18, 'Kenz Moderator Drax', NULL, '1468168542649323683', '2026-07-05 08:22:57'),
(19, 'Ardawg Hopeless Romantic', NULL, '847401229860864060', '2026-07-05 08:23:31'),
(20, 'Tokyo Masuk Kalo Perang Doang', NULL, '864400408772083732', '2026-07-05 08:24:11'),
(21, 'Badol', NULL, '491855615828819979', '2026-07-05 08:24:21'),
(22, 'Ica Pacar Ko Jos', NULL, '848147077900402718', '2026-07-05 08:24:32'),
(23, 'Vodra Bocil Gak Ngangkat', NULL, '1384654444624609331', '2026-07-05 08:24:50'),
(24, 'Jali', NULL, '1164157791645225043', '2026-07-05 08:25:02'),
(25, 'Jayvon Manusia Soundboard', NULL, '1409769623167172722', '2026-07-05 08:25:17'),
(26, 'Rukun Anak ROS', NULL, '795335843665281025', '2026-07-05 08:25:30'),
(27, 'Soba Vtuber Baik', NULL, '508058398034624540', '2026-07-05 08:25:41'),
(28, 'Razor anak TSG', NULL, '1476256538799898787', '2026-07-05 08:27:20'),
(29, 'Acol Gak Soleh', NULL, '1061099442020831393', '2026-07-05 08:27:29'),
(30, 'Ardo', NULL, '748927972551032911', '2026-07-05 08:27:51'),
(31, 'Falto Bocil Imoet', NULL, '1270703813833003109', '2026-07-05 08:28:12'),
(32, 'Galang Nocturnal', NULL, '1479889568940425328', '2026-07-05 08:28:23'),
(33, 'Lexy Mau Jadi Patem', NULL, '1458072280373395581', '2026-07-05 08:29:09'),
(34, 'Guti Adik Bon', NULL, '477849393051992065', '2026-07-05 08:29:17'),
(35, 'Mamun Dialog Fivem', NULL, '1231223401339682876', '2026-07-05 08:29:28'),
(36, 'Patrick Jawa Imut', NULL, '955856494409162772', '2026-07-05 08:29:42'),
(37, 'Pongky Raja ROS', NULL, '746045150567858278', '2026-07-05 08:29:52'),
(38, 'Yanti Sakit Perut', NULL, '756519202419900536', '2026-07-05 08:30:13'),
(39, 'Ren Scammer Jawa', NULL, '183605505866858496', '2026-07-05 08:30:31'),
(40, 'Yuji Yahudi', NULL, '512534030697365524', '2026-07-05 08:31:58'),
(41, 'Apis Cacat Lahir', NULL, '1241083845969969283', '2026-07-05 08:32:08'),
(42, 'Yudhis Pugzy', NULL, '691635872180731947', '2026-07-05 08:43:29'),
(43, 'Pakde Rerey Rockstar Editor', NULL, '415427546222428160', '2026-07-05 08:46:09'),
(44, 'Kimi Arabian Girl', NULL, '558329836175753248', '2026-07-05 08:46:20'),
(45, 'Ucey Anime Rata Kanan', NULL, '333052511885852672', '2026-07-05 08:46:30'),
(46, 'Petter Punya Spa', NULL, '737344630743498752', '2026-07-05 08:46:51'),
(47, 'Cecep Kyodai Bon', NULL, '1156940393817055335', '2026-07-05 08:46:58'),
(48, 'Soso Yatim', NULL, '948895961755828284', '2026-07-05 08:47:08'),
(49, 'Taluz Pengen Ganteng', NULL, '535787845403672596', '2026-07-05 09:02:53');

-- --------------------------------------------------------

--
-- Table structure for table `kategori_barang`
--

CREATE TABLE `kategori_barang` (
  `id` int(11) NOT NULL,
  `jenis` enum('Senjata','Narko','Lainnya') COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_barang` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `class_senjata` enum('Class 1','Class 2','Class 3','Class 4') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tag_narko` enum('Bungkusan','Mentahan') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tag_lainnya` enum('Vest','Ammo','Attachment') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `stok` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kategori_barang`
--

INSERT INTO `kategori_barang` (`id`, `jenis`, `nama_barang`, `class_senjata`, `tag_narko`, `tag_lainnya`, `stok`, `created_at`, `updated_at`) VALUES
(5, 'Narko', 'Weed', NULL, 'Bungkusan', NULL, 3378, '2026-07-04 06:24:04', '2026-07-05 08:58:07'),
(6, 'Narko', 'Meth', NULL, 'Bungkusan', NULL, 3589, '2026-07-04 06:24:04', '2026-07-05 08:55:38'),
(7, 'Narko', 'Opium', NULL, 'Bungkusan', NULL, 2982, '2026-07-04 06:24:04', '2026-07-05 08:58:24'),
(9, 'Senjata', 'Virtus', 'Class 3', NULL, NULL, 3, '2026-07-04 14:50:08', '2026-07-05 13:47:10'),
(10, 'Senjata', 'X17 Modular', 'Class 1', NULL, NULL, 2, '2026-07-04 14:50:38', '2026-07-05 13:46:07'),
(11, 'Senjata', 'Machine pistol', 'Class 1', NULL, NULL, 3, '2026-07-04 14:50:47', '2026-07-05 13:53:43'),
(12, 'Senjata', 'Pistol.50', 'Class 1', NULL, NULL, 18, '2026-07-04 14:50:51', '2026-07-05 09:00:57'),
(13, 'Senjata', 'Ceramic Pistol', 'Class 1', NULL, NULL, 9, '2026-07-04 14:50:54', '2026-07-05 08:59:27'),
(14, 'Senjata', 'Mini SMG', 'Class 2', NULL, NULL, 9, '2026-07-04 14:51:02', '2026-07-06 07:46:41'),
(15, 'Senjata', 'Micro SMG', 'Class 2', NULL, NULL, 16, '2026-07-04 14:51:06', '2026-07-06 16:21:50'),
(16, 'Senjata', 'SMG', 'Class 2', NULL, NULL, 0, '2026-07-04 14:51:10', '2026-07-04 14:51:10'),
(17, 'Senjata', 'Navy revolver', 'Class 2', NULL, NULL, 7, '2026-07-04 14:51:17', '2026-07-05 13:53:31'),
(18, 'Senjata', 'KVR', 'Class 2', NULL, NULL, 17, '2026-07-04 14:51:21', '2026-07-05 13:47:58'),
(19, 'Senjata', 'Pump Shotgun', 'Class 2', NULL, NULL, 0, '2026-07-04 14:51:25', '2026-07-06 16:21:00'),
(20, 'Senjata', 'Black Revolver', 'Class 2', NULL, NULL, 17, '2026-07-04 14:51:42', '2026-07-05 13:49:14'),
(21, 'Senjata', 'Assault Rifle', 'Class 3', NULL, NULL, 8, '2026-07-04 14:51:50', '2026-07-05 13:54:09'),
(22, 'Senjata', 'Carbine Rifle', 'Class 3', NULL, NULL, 1, '2026-07-04 14:51:54', '2026-07-05 13:45:06'),
(23, 'Narko', 'Meth Pax', NULL, 'Mentahan', NULL, 0, '2026-07-04 15:00:32', '2026-07-04 15:00:32'),
(24, 'Narko', 'Liquid Meth', NULL, 'Mentahan', NULL, 0, '2026-07-04 15:00:43', '2026-07-04 15:00:43'),
(25, 'Narko', 'Weed Seed', NULL, 'Mentahan', NULL, 0, '2026-07-04 15:00:54', '2026-07-04 15:00:54'),
(26, 'Narko', 'Cocain', NULL, 'Bungkusan', NULL, 808, '2026-07-05 08:53:30', '2026-07-07 10:55:27'),
(27, 'Lainnya', 'Vest Biru', NULL, NULL, 'Vest', 48, '2026-07-05 09:10:34', '2026-07-05 14:00:07'),
(28, 'Lainnya', '9MM', NULL, NULL, 'Ammo', 272, '2026-07-05 09:10:45', '2026-07-05 09:12:41'),
(29, 'Lainnya', 'Shotgun Ammo', NULL, NULL, 'Ammo', 0, '2026-07-05 09:12:46', '2026-07-05 09:12:46'),
(30, 'Lainnya', '44 Magnum', NULL, NULL, 'Ammo', 0, '2026-07-05 09:12:56', '2026-07-05 09:12:56'),
(31, 'Lainnya', '45ACP', NULL, NULL, 'Ammo', 0, '2026-07-05 09:13:04', '2026-07-05 09:13:04'),
(32, 'Lainnya', 'Rifle 762', NULL, NULL, 'Ammo', 0, '2026-07-05 09:13:28', '2026-07-05 09:16:59'),
(33, 'Lainnya', 'Rifle 556', NULL, NULL, 'Ammo', 0, '2026-07-05 09:13:35', '2026-07-05 09:17:06'),
(34, 'Lainnya', 'Vest Merah', NULL, NULL, 'Vest', 0, '2026-07-05 09:13:43', '2026-07-05 09:13:43'),
(35, 'Lainnya', 'Tactical Flashlight', NULL, NULL, 'Attachment', 0, '2026-07-05 09:13:53', '2026-07-05 09:13:53'),
(36, 'Lainnya', 'Grip', NULL, NULL, 'Attachment', 10, '2026-07-05 09:14:00', '2026-07-05 09:17:50'),
(37, 'Lainnya', 'Suppressor', NULL, NULL, 'Attachment', 12, '2026-07-05 09:14:09', '2026-07-05 09:18:01'),
(38, 'Lainnya', 'Tactical Suppressor', NULL, NULL, 'Attachment', 3, '2026-07-05 09:14:16', '2026-07-05 09:17:32'),
(39, 'Lainnya', 'Extended Pistol Clip', NULL, NULL, 'Attachment', 17, '2026-07-05 09:14:22', '2026-07-05 09:18:11'),
(40, 'Lainnya', 'Extended SMG Clip', NULL, NULL, 'Attachment', 0, '2026-07-05 09:14:28', '2026-07-05 09:14:28'),
(41, 'Lainnya', 'Extended Rifle Clip', NULL, NULL, 'Attachment', 18, '2026-07-05 09:14:34', '2026-07-05 09:17:20'),
(42, 'Lainnya', 'SMG Drum', NULL, NULL, 'Attachment', 1, '2026-07-05 09:14:41', '2026-07-05 09:17:44'),
(43, 'Lainnya', 'Rifle Drum', NULL, NULL, 'Attachment', 1, '2026-07-05 09:14:47', '2026-07-05 09:18:20'),
(44, 'Lainnya', 'Macro Scope', NULL, NULL, 'Attachment', 0, '2026-07-05 09:14:52', '2026-07-05 09:14:52'),
(45, 'Lainnya', 'Medium Scope', NULL, NULL, 'Attachment', 0, '2026-07-05 09:14:58', '2026-07-05 09:14:58'),
(46, 'Lainnya', 'Modern Extended Drum', NULL, NULL, 'Attachment', 0, '2026-07-05 09:15:03', '2026-07-05 09:15:03'),
(47, 'Lainnya', 'Modern Supre Short', NULL, NULL, 'Attachment', 0, '2026-07-05 09:15:12', '2026-07-05 09:15:12'),
(48, 'Lainnya', 'Holo Scope', NULL, NULL, 'Attachment', 1, '2026-07-05 09:15:24', '2026-07-05 09:18:27'),
(49, 'Lainnya', '.50', NULL, NULL, 'Ammo', 0, '2026-07-05 09:17:47', '2026-07-05 09:17:47');

-- --------------------------------------------------------

--
-- Table structure for table `peminjaman`
--

CREATE TABLE `peminjaman` (
  `id` int(11) NOT NULL,
  `id_homies` int(11) NOT NULL,
  `id_barang` int(11) NOT NULL,
  `tanggal_pinjam` date NOT NULL,
  `tanggal_kembali` date DEFAULT NULL,
  `jumlah` int(11) NOT NULL,
  `status` enum('Dipinjam','Dikembalikan') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Dipinjam',
  `diinput_oleh` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dikembalikan_oleh` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `peminjaman`
--

INSERT INTO `peminjaman` (`id`, `id_homies`, `id_barang`, `tanggal_pinjam`, `tanggal_kembali`, `jumlah`, `status`, `diinput_oleh`, `dikembalikan_oleh`, `created_at`) VALUES
(15, 7, 21, '2026-07-05', '2026-07-05', 1, 'Dikembalikan', 'Admin Gudang', 'Staff Gudang', '2026-07-05 08:20:40'),
(16, 8, 21, '2026-07-05', '2026-07-05', 1, 'Dikembalikan', 'Staff Gudang', 'Staff Gudang', '2026-07-05 08:35:19'),
(17, 34, 21, '2026-07-05', '2026-07-05', 1, 'Dikembalikan', 'Staff Gudang', 'Staff Gudang', '2026-07-05 08:44:01'),
(18, 23, 20, '2026-07-05', NULL, 1, 'Dipinjam', 'Staff Gudang', NULL, '2026-07-05 09:02:22'),
(19, 49, 19, '2026-07-05', NULL, 1, 'Dipinjam', 'Staff Gudang', NULL, '2026-07-05 09:03:00'),
(20, 12, 15, '2026-07-05', NULL, 1, 'Dipinjam', 'Staff Gudang', NULL, '2026-07-05 10:32:45'),
(21, 12, 10, '2026-07-05', NULL, 1, 'Dipinjam', 'Staff Gudang', NULL, '2026-07-05 10:33:10'),
(22, 9, 20, '2026-07-05', NULL, 1, 'Dipinjam', 'Staff Gudang', NULL, '2026-07-05 10:59:39'),
(23, 9, 19, '2026-07-05', NULL, 1, 'Dipinjam', 'Staff Gudang', NULL, '2026-07-05 10:59:47'),
(24, 6, 20, '2026-07-05', '2026-07-05', 1, 'Dikembalikan', 'Admin Gudang', 'Admin Gudang', '2026-07-05 13:49:02'),
(25, 15, 19, '2026-07-05', '2026-07-06', 1, 'Dikembalikan', 'Staff Gudang', 'Staff Gudang', '2026-07-05 14:22:45'),
(26, 36, 14, '2026-07-05', NULL, 1, 'Dipinjam', 'Staff Gudang', NULL, '2026-07-05 14:29:16'),
(27, 33, 14, '2026-07-05', '2026-07-06', 1, 'Dikembalikan', 'Staff Gudang', 'Staff Gudang', '2026-07-05 15:46:36'),
(28, 49, 15, '2026-07-05', '2026-07-06', 1, 'Dikembalikan', 'Staff Gudang', 'Staff Gudang', '2026-07-05 15:47:04'),
(29, 46, 15, '2026-07-05', '2026-07-06', 1, 'Dikembalikan', 'Staff Gudang', 'Staff Gudang', '2026-07-05 15:48:53'),
(30, 32, 14, '2026-07-06', NULL, 1, 'Dipinjam', 'Staff Gudang', NULL, '2026-07-05 17:40:35');

-- --------------------------------------------------------

--
-- Table structure for table `penjualan`
--

CREATE TABLE `penjualan` (
  `id` int(11) NOT NULL,
  `id_homies` int(11) NOT NULL,
  `id_barang` int(11) NOT NULL,
  `tanggal_penjualan` date NOT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `jumlah` int(11) NOT NULL,
  `status` enum('Proses','Selesai') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Proses',
  `diinput_oleh` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `diselesaikan_oleh` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `penjualan`
--

INSERT INTO `penjualan` (`id`, `id_homies`, `id_barang`, `tanggal_penjualan`, `tanggal_selesai`, `jumlah`, `status`, `diinput_oleh`, `diselesaikan_oleh`, `created_at`) VALUES
(1, 5, 26, '2026-07-07', '2026-07-07', 1, 'Selesai', 'Admin Gudang', 'Admin Gudang', '2026-07-07 10:53:29'),
(2, 5, 26, '2026-07-07', '2026-07-07', 1, 'Selesai', 'Admin Gudang', 'Admin Gudang', '2026-07-07 10:55:27');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','staff') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'staff',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `role`, `created_at`) VALUES
(1, 'admin', 'pepekmemek002', 'Admin Gudang', 'admin', '2026-07-04 05:37:46'),
(4, 'staff', 'staff', 'Staff Gudang', 'staff', '2026-07-04 14:53:32');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `barang_keluar`
--
ALTER TABLE `barang_keluar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_barang` (`id_barang`);

--
-- Indexes for table `barang_masuk`
--
ALTER TABLE `barang_masuk`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_barang` (`id_barang`);

--
-- Indexes for table `homies`
--
ALTER TABLE `homies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cid` (`cid`);

--
-- Indexes for table `kategori_barang`
--
ALTER TABLE `kategori_barang`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_homies` (`id_homies`),
  ADD KEY `id_barang` (`id_barang`);

--
-- Indexes for table `penjualan`
--
ALTER TABLE `penjualan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_homies` (`id_homies`),
  ADD KEY `id_barang` (`id_barang`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `barang_keluar`
--
ALTER TABLE `barang_keluar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `barang_masuk`
--
ALTER TABLE `barang_masuk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `homies`
--
ALTER TABLE `homies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `kategori_barang`
--
ALTER TABLE `kategori_barang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `peminjaman`
--
ALTER TABLE `peminjaman`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `penjualan`
--
ALTER TABLE `penjualan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `barang_keluar`
--
ALTER TABLE `barang_keluar`
  ADD CONSTRAINT `barang_keluar_ibfk_1` FOREIGN KEY (`id_barang`) REFERENCES `kategori_barang` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `barang_masuk`
--
ALTER TABLE `barang_masuk`
  ADD CONSTRAINT `barang_masuk_ibfk_1` FOREIGN KEY (`id_barang`) REFERENCES `kategori_barang` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD CONSTRAINT `peminjaman_ibfk_1` FOREIGN KEY (`id_homies`) REFERENCES `homies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `peminjaman_ibfk_2` FOREIGN KEY (`id_barang`) REFERENCES `kategori_barang` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `penjualan`
--
ALTER TABLE `penjualan`
  ADD CONSTRAINT `penjualan_ibfk_1` FOREIGN KEY (`id_homies`) REFERENCES `homies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `penjualan_ibfk_2` FOREIGN KEY (`id_barang`) REFERENCES `kategori_barang` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
