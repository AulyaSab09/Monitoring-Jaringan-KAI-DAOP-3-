-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 11 Feb 2026 pada 05.28
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `it_monitoring_jaringan`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `monitors`
--
SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `monitors`;
CREATE TABLE `monitors` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'Device',
  `location` varchar(255) DEFAULT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `kode_lokasi` varchar(255) DEFAULT NULL,
  `ip_address` varchar(255) NOT NULL,
  `zone` enum('center','lintas utara','lintas selatan') NOT NULL DEFAULT 'center',
  `status` varchar(255) NOT NULL,
  `latency` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `history` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `monitors`
--

INSERT INTO `monitors` (`id`, `name`, `type`, `location`, `parent_id`, `kode_lokasi`, `ip_address`, `zone`, `status`, `latency`, `created_at`, `updated_at`, `history`) VALUES
(5, 'SDWAN PGB', 'Router', 'STASIUN PEGADENBARU', NULL, 'PGB', '10.3.31.1', 'center', 'Disconnected', 0, '2026-02-11 03:12:31', '2026-02-11 04:28:38', '[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]'),
(6, 'SDWAN HGL', 'Router', 'STASIUN HAURGEULIS', NULL, 'HGL', '10.3.46.1', 'center', 'Disconnected', 0, '2026-02-11 03:13:12', '2026-02-11 04:28:38', '[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]'),
(7, 'SDWAN TIS', 'Router', 'STASIUN TERISI', NULL, 'TIS', '10.3.44.1', 'center', 'Disconnected', 0, '2026-02-11 03:15:24', '2026-02-11 04:28:38', '[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]'),
(8, 'SDWAN JTB', 'Router', 'STASIUN JATIBARANG', NULL, 'JTB', '10.3.22.1', 'center', 'Disconnected', 0, '2026-02-11 03:16:26', '2026-02-11 04:28:38', '[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]'),
(9, 'SDWAN AWN', 'Router', 'STASIUN ARJAWINANGUN', NULL, 'AWN', '10.3.47.1', 'center', 'Disconnected', 0, '2026-02-11 03:17:17', '2026-02-11 04:28:38', '[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]'),
(10, 'SDWAN CN', 'Router', 'STASIUN CIREBON', NULL, 'CN', '10.3.1.1', 'center', 'Disconnected', 0, '2026-02-11 03:17:56', '2026-02-11 04:28:38', '[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]'),
(11, 'SDWAN CNP', 'Router', 'STASIUN CIREBON PRUJAKAN', NULL, 'CNP', '10.3.36.1', 'center', 'Disconnected', 0, '2026-02-11 03:19:29', '2026-02-11 04:28:38', '[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]'),
(12, 'SDWAN CLD', 'Router', 'STASIUN CILEDUG', NULL, 'CLD', '10.3.37.1', 'lintas selatan', 'Disconnected', 0, '2026-02-11 03:20:24', '2026-02-11 04:28:38', '[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]'),
(13, 'SDWAN BBK', 'Router', 'STASIUN BABAKAN', NULL, 'BBK', '10.3.23.1', 'lintas utara', 'Disconnected', 0, '2026-02-11 03:21:51', '2026-02-11 04:28:38', '[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]'),
(14, 'SDWAN KGG', 'Router', 'STASIUN KETANGGUNGAN', NULL, 'KGG', '10.3.30.1', 'lintas selatan', 'Disconnected', 0, '2026-02-11 03:23:25', '2026-02-11 04:28:38', '[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]'),
(15, 'SDWAN LOS', 'Router', 'STASIUN LOSARI', NULL, 'LOS', '10.3.24.1', 'lintas utara', 'Disconnected', 0, '2026-02-11 03:25:06', '2026-02-11 04:28:38', '[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]'),
(16, 'SDWAN TGN', 'Router', 'STASIUN TANJUNG', NULL, 'TGN', '10.3.21.1', 'lintas utara', 'Disconnected', 0, '2026-02-11 03:25:48', '2026-02-11 04:28:38', '[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]'),
(17, 'SDWAN BB', 'Router', 'STASIUN BREBES', NULL, 'BB', '10.3.35.1', 'lintas utara', 'Disconnected', 0, '2026-02-11 03:26:25', '2026-02-11 04:28:38', '[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]'),
(18, 'ARUBA STPGB', 'Switch', 'STASIUN PEGADENBARU', 5, 'PGB', '10.3.31.210', 'center', 'Disconnected', 0, '2026-02-11 03:29:28', '2026-02-11 04:28:38', '[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]'),
(19, 'ARUBA JRPGB', 'Switch', 'STASIUN PEGADENBARU', 18, 'PGB', '10.3.31.211', 'center', 'Disconnected', 0, '2026-02-11 03:38:50', '2026-02-11 04:28:38', '[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]'),
(20, 'ARUBAST', 'Switch', 'STASIUN HAURGEULIS', 6, 'HGL', '10.3.46.210', 'center', 'Disconnected', 0, '2026-02-11 03:40:02', '2026-02-11 04:28:38', '[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]'),
(21, 'ARUBAJRHGL', 'Switch', 'STASIUN HAURGEULIS', 20, 'HGL', '10.3.46.211', 'center', 'Disconnected', 0, '2026-02-11 03:40:48', '2026-02-11 04:28:38', '[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]'),
(22, 'ARUBASTTIS', 'Switch', 'STASIUN TERISI', 7, 'TIS', '10.3.44.210', 'center', 'Disconnected', 0, '2026-02-11 03:41:21', '2026-02-11 04:28:38', '[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]'),
(23, 'ARUBATISJR', 'Switch', 'STASIUN TERISI', 22, 'TIS', '10.3.44.211', 'center', 'Disconnected', 0, '2026-02-11 03:41:56', '2026-02-11 04:28:38', '[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]'),
(24, 'ARUBASTJTB', 'Switch', 'STASIUN JATIBARANG', 8, 'JTB', '10.3.22.210', 'center', 'Disconnected', 0, '2026-02-11 03:42:20', '2026-02-11 04:28:38', '[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]'),
(25, 'ARUBASTAWN', 'Switch', 'STASIUN ARJAWINANGUN', 9, 'AWN', '10.3.47.210', 'center', 'Disconnected', 0, '2026-02-11 03:43:09', '2026-02-11 04:28:38', '[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]'),
(26, 'ARUBAAWN1', 'Switch', 'STASIUN ARJAWINANGUN', 25, 'AWN', '10.3.47.211', 'center', 'Disconnected', 0, '2026-02-11 03:43:41', '2026-02-11 04:28:38', '[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]'),
(27, 'ARUBAAWN2', 'Switch', 'STASIUN ARJAWINANGUN', 26, 'AWN', '10.3.47.212', 'center', 'Disconnected', 0, '2026-02-11 03:44:06', '2026-02-11 04:28:38', '[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]'),
(28, 'SWRISECN1', 'Switch', 'STASIUN CIREBON', 10, 'CN', '10.3.1.10', 'center', 'Disconnected', 0, '2026-02-11 03:44:33', '2026-02-11 04:28:38', '[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]'),
(29, 'SWRISECN2', 'Switch', 'STASIUN CIREBON', 28, 'CN', '10.3.1.11', 'center', 'Disconnected', 0, '2026-02-11 03:44:58', '2026-02-11 04:28:38', '[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]'),
(30, 'SWRISECNP1', 'Switch', 'STASIUN CIREBON PRUJAKAN', 11, 'CNP', '10.3.36.5', 'center', 'Disconnected', 0, '2026-02-11 03:45:28', '2026-02-11 04:28:39', '[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]'),
(31, 'ARUBAJRCNP', 'Switch', 'STASIUN CIREBON PRUJAKAN', 30, 'CNP', '10.3.36.211', 'center', 'Disconnected', 0, '2026-02-11 03:46:54', '2026-02-11 04:28:39', '[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]'),
(32, 'SWRISECNP2', 'Switch', 'STASIUN CIREBON PRUJAKAN', 30, 'CNP', '10.3.36.6', 'center', 'Disconnected', 0, '2026-02-11 03:47:30', '2026-02-11 04:28:39', '[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]'),
(33, 'ARUBASTCLD', 'Switch', 'STASIUN CILEDUG', 12, 'CLD', '10.3.37.210', 'lintas selatan', 'Disconnected', 0, '2026-02-11 03:48:18', '2026-02-11 04:28:39', '[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]'),
(34, 'ARUBACLDJR', 'Switch', 'STASIUN CILEDUG', 33, 'CLD', '10.3.37.211', 'lintas selatan', 'Disconnected', 0, '2026-02-11 03:48:43', '2026-02-11 04:28:39', '[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]'),
(35, 'ARUBASTKGG', 'Switch', 'STASIUN KETANGGUNGAN', 14, 'KGG', '10.3.30.210', 'lintas selatan', 'Disconnected', 0, '2026-02-11 03:49:17', '2026-02-11 04:28:39', '[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]'),
(36, 'ARUBAKGGJJ', 'Switch', 'STASIUN KETANGGUNGAN', 35, 'KGG', '10.3.30.211', 'lintas selatan', 'Disconnected', 0, '2026-02-11 03:50:16', '2026-02-11 04:28:39', '[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]'),
(37, 'ARUBAKGGSTL', 'Switch', 'STASIUN KETANGGUNGAN', 36, 'KGG', '10.3.30.212', 'lintas selatan', 'Disconnected', 0, '2026-02-11 03:50:46', '2026-02-11 04:28:39', '[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]'),
(38, 'ARUBASTBBK', 'Switch', 'STASIUN BABAKAN', 13, 'BBK', '10.3.23.210', 'lintas utara', 'Disconnected', 0, '2026-02-11 03:51:08', '2026-02-11 04:28:39', '[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]'),
(39, 'ARUBABBKJJ', 'Switch', 'STASIUN BABAKAN', 38, 'BBK', '10.3.23.211', 'lintas utara', 'Disconnected', 0, '2026-02-11 03:51:31', '2026-02-11 04:28:39', '[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]'),
(40, 'ARUBASTLOS', 'Switch', 'STASIUN LOSARI', 15, 'LOS', '10.3.24.210', 'lintas utara', 'Disconnected', 0, '2026-02-11 03:51:54', '2026-02-11 04:28:39', '[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]'),
(41, 'ARUBALOSJJ', 'Switch', 'STASIUN LOSARI', 40, 'LOS', '10.3.24.211', 'lintas utara', 'Disconnected', 0, '2026-02-11 03:52:20', '2026-02-11 04:28:39', '[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]'),
(42, 'ARUBASTTGN', 'Switch', 'STASIUN TANJUNG', 16, 'TGN', '10.3.21.210', 'lintas utara', 'Disconnected', 0, '2026-02-11 03:52:51', '2026-02-11 04:28:39', '[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]'),
(43, 'ARUBATGNJJ', 'Switch', 'STASIUN TANJUNG', 42, 'TGN', '10.3.21.211', 'lintas utara', 'Disconnected', 0, '2026-02-11 03:53:27', '2026-02-11 04:28:39', '[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]'),
(44, 'ARUBABBUK', 'Switch', 'STASIUN BREBES', 17, 'BB', '10.3.35.211', 'lintas utara', 'Disconnected', 0, '2026-02-11 03:54:44', '2026-02-11 04:28:39', '[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]'),
(45, 'ARUBASTBB', 'Switch', 'STASIUN BREBES', 44, 'BB', '10.3.35.212', 'lintas utara', 'Disconnected', 0, '2026-02-11 03:55:33', '2026-02-11 04:28:39', '[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]'),
(46, 'ARUBABBJR', 'Switch', 'STASIUN BREBES', 45, 'BB', '10.3.35.210', 'lintas utara', 'Disconnected', 0, '2026-02-11 03:56:02', '2026-02-11 04:28:39', '[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]'),
(47, 'PC 5', 'PC', 'KANTOR DAOP', NULL, 'DAOP', '10.3.0.5', 'center', 'Disconnected', 0, '2026-02-11 04:13:39', '2026-02-11 04:28:39', '[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `monitors`
--
ALTER TABLE `monitors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `monitors_ip_address_index` (`ip_address`),
  ADD KEY `monitors_status_index` (`status`),
  ADD KEY `monitors_parent_id_foreign` (`parent_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `monitors`
--
ALTER TABLE `monitors`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `monitors`
--
ALTER TABLE `monitors`
  ADD CONSTRAINT `monitors_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `monitors` (`id`) ON DELETE CASCADE;
SET FOREIGN_KEY_CHECKS=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
