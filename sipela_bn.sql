-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 27, 2025 at 10:10 AM
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
-- Database: `sipela_bn`
--

-- --------------------------------------------------------

--
-- Table structure for table `bimbingan_konseling`
--

CREATE TABLE `bimbingan_konseling` (
  `bk_id` bigint(20) UNSIGNED NOT NULL,
  `siswa_id` bigint(20) UNSIGNED NOT NULL,
  `guru_konselor` bigint(20) UNSIGNED NOT NULL,
  `tahun_ajaran_id` bigint(20) UNSIGNED NOT NULL,
  `jenis_layanan` enum('Individu','Kelompok','Klasikal') NOT NULL,
  `topik` varchar(255) NOT NULL,
  `keluhan_masalah` text DEFAULT NULL,
  `tindakan_solusi` text DEFAULT NULL,
  `status` enum('terdaftar','diproses','selesai','tindak_lanjut') NOT NULL DEFAULT 'terdaftar',
  `tanggal_konseling` date NOT NULL,
  `tanggal_tindak_lanjut` date DEFAULT NULL,
  `hasil_evaluasi` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `sanksi_id` bigint(20) UNSIGNED DEFAULT NULL,
  `is_followup` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bimbingan_konseling`
--

INSERT INTO `bimbingan_konseling` (`bk_id`, `siswa_id`, `guru_konselor`, `tahun_ajaran_id`, `jenis_layanan`, `topik`, `keluhan_masalah`, `tindakan_solusi`, `status`, `tanggal_konseling`, `tanggal_tindak_lanjut`, `hasil_evaluasi`, `created_at`, `updated_at`, `sanksi_id`, `is_followup`) VALUES
(1, 1, 1, 1, 'Individu', 'pribasi', NULL, NULL, 'terdaftar', '2025-11-19', NULL, NULL, '2025-11-18 23:52:30', '2025-11-18 23:52:30', NULL, 0),
(2, 1, 1, 1, 'Individu', 'pribasi', NULL, NULL, 'terdaftar', '2025-11-19', NULL, NULL, '2025-11-18 23:53:46', '2025-11-18 23:53:46', NULL, 0),
(3, 1, 1, 1, 'Individu', 'pribadi', NULL, NULL, 'terdaftar', '2025-11-19', NULL, NULL, '2025-11-18 23:55:02', '2025-11-18 23:55:02', NULL, 0),
(4, 1, 3, 1, 'Individu', 'pribadi', NULL, NULL, 'terdaftar', '2025-11-19', NULL, NULL, '2025-11-19 00:00:36', '2025-11-19 00:00:36', NULL, 0),
(5, 1, 3, 1, 'Individu', 'pribadi', NULL, NULL, 'terdaftar', '2025-11-19', NULL, NULL, '2025-11-19 00:04:58', '2025-11-19 00:04:58', NULL, 0),
(6, 1, 3, 1, 'Individu', 'pribadi', NULL, NULL, 'terdaftar', '2025-11-19', NULL, NULL, '2025-11-19 00:06:19', '2025-11-19 00:06:19', NULL, 0),
(10, 1, 9, 1, 'Individu', 'Follow-up Sanksi: individu', 'Follow-up sanksi: N/A', NULL, 'terdaftar', '2025-11-19', NULL, NULL, '2025-11-19 05:49:22', '2025-11-19 05:49:22', NULL, 0),
(11, 1, 9, 1, 'Individu', 'pribadi', NULL, NULL, 'terdaftar', '2025-11-20', NULL, NULL, '2025-11-19 23:07:34', '2025-11-19 23:07:34', NULL, 0),
(12, 1, 9, 1, 'Individu', 'pribadi', NULL, NULL, 'terdaftar', '2025-11-20', NULL, NULL, '2025-11-20 01:53:50', '2025-11-20 01:53:50', NULL, 0),
(13, 1, 1, 1, 'Individu', 'Masalah belajar', NULL, NULL, 'terdaftar', '2025-11-21', NULL, NULL, '2025-11-20 22:39:26', '2025-11-20 22:39:26', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `guru`
--

CREATE TABLE `guru` (
  `guru_id` bigint(20) UNSIGNED NOT NULL,
  `nip` varchar(20) NOT NULL,
  `nama_guru` varchar(100) NOT NULL,
  `jenis_kelamin` enum('Laki-laki','Perempuan') NOT NULL,
  `bidang_studi` varchar(50) DEFAULT NULL,
  `no_telp` varchar(15) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `status` enum('Aktif','Cuti','Pensiun') NOT NULL DEFAULT 'Aktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `guru`
--

INSERT INTO `guru` (`guru_id`, `nip`, `nama_guru`, `jenis_kelamin`, `bidang_studi`, `no_telp`, `email`, `status`, `created_at`, `updated_at`) VALUES
(1, '9876543222', 'Suhendar', 'Laki-laki', 'Pai', '083176266583', 'suhendar4@gmail.com', 'Aktif', '2025-11-18 20:28:13', '2025-11-19 02:18:54'),
(3, '12032813', 'Ridwan', 'Laki-laki', NULL, NULL, NULL, 'Aktif', '2025-11-18 20:46:53', '2025-11-18 20:46:53'),
(6, '102830123', 'aji sukma', 'Laki-laki', 'Database', '08219818', 'mutiara@gmail.com', 'Aktif', '2025-11-19 01:46:15', '2025-11-19 01:46:15'),
(9, '1293128', 'Ridwan kamil', 'Laki-laki', 'BK/konselig', '08923292', 'ridwan@gmail.com', 'Aktif', '2025-11-19 02:54:33', '2025-11-19 02:54:33'),
(10, 'admin', 'admin', 'Laki-laki', 'Admin', NULL, NULL, 'Aktif', '2025-11-19 06:00:17', '2025-11-19 06:00:17'),
(14, '32129371', 'Cika', 'Perempuan', 'Bahasa indonesia', '08219818', 'Cika@gmail.com', 'Aktif', '2025-11-19 07:17:29', '2025-11-19 07:17:29'),
(15, '237913', 'razka', 'Laki-laki', NULL, NULL, NULL, 'Aktif', '2025-11-19 07:32:43', '2025-11-19 07:32:43'),
(16, '289319312', 'Gatot', 'Laki-laki', 'Kesiswaan', '0832823129', 'gatot@gmail.com', 'Aktif', '2025-11-19 19:37:57', '2025-11-19 19:37:57'),
(17, '9127171', 'Deni danis', 'Laki-laki', NULL, NULL, NULL, 'Aktif', '2025-11-19 21:38:51', '2025-11-19 21:38:51');

-- --------------------------------------------------------

--
-- Table structure for table `jenis_pelanggaran`
--

CREATE TABLE `jenis_pelanggaran` (
  `jenis_pelanggaran_id` bigint(20) UNSIGNED NOT NULL,
  `nama_pelanggaran` varchar(100) NOT NULL,
  `kategori` enum('ringan','sedang','berat','sangat_berat') NOT NULL,
  `poin` int(11) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `sanksi_rekomendasi` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jenis_pelanggaran`
--

INSERT INTO `jenis_pelanggaran` (`jenis_pelanggaran_id`, `nama_pelanggaran`, `kategori`, `poin`, `deskripsi`, `sanksi_rekomendasi`, `created_at`, `updated_at`) VALUES
(1, 'Membuat keributan / kegaduhan dalam kelas pada saat berlangsungnya pelajaran', 'sedang', 5, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `jenis_prestasi`
--

CREATE TABLE `jenis_prestasi` (
  `jenis_prestasi_id` bigint(20) UNSIGNED NOT NULL,
  `nama_prestasi` varchar(100) NOT NULL,
  `poin` int(11) NOT NULL,
  `kategori` enum('Akademik','Non-Akademik') NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `reward` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jenis_prestasi`
--

INSERT INTO `jenis_prestasi` (`jenis_prestasi_id`, `nama_prestasi`, `poin`, `kategori`, `deskripsi`, `reward`, `created_at`, `updated_at`) VALUES
(1, 'Juara 1 olimpiade Bahasa indonesia', 100, 'Akademik', 'meraih medali emas dalam kompetisi matematika', 'piagam penghargaan, uang tunai dan Beasiswa Studi lanjutan', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `jenis_sanksi`
--

CREATE TABLE `jenis_sanksi` (
  `jenis_sanksi_id` bigint(20) UNSIGNED NOT NULL,
  `nama_sanksi` varchar(255) NOT NULL,
  `kategori` varchar(100) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jenis_sanksi`
--

INSERT INTO `jenis_sanksi` (`jenis_sanksi_id`, `nama_sanksi`, `kategori`, `deskripsi`, `created_at`, `updated_at`) VALUES
(1, 'Teguran Lisan', 'ringan', 'Teguran lisan dari guru', NULL, NULL),
(2, 'Teguran Tertulis', 'ringan', 'Teguran tertulis dan surat pernyataan', NULL, NULL),
(3, 'Kerja Sosial', 'sedang', 'Membersihkan lingkungan sekolah', NULL, NULL),
(4, 'Skorsing', 'berat', 'Tidak boleh masuk sekolah sementara', NULL, NULL),
(5, 'Teguran Lisan', 'ringan', 'Teguran lisan dari guru', NULL, NULL),
(6, 'Kerja Sosial', 'sedang', 'Membersihkan lingkungan sekolah', NULL, NULL),
(7, 'Skorsing', 'berat', 'Tidak boleh masuk sekolah sementara', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kelas`
--

CREATE TABLE `kelas` (
  `kelas_id` bigint(20) UNSIGNED NOT NULL,
  `nama_kelas` varchar(50) NOT NULL,
  `jurusan` varchar(50) DEFAULT NULL,
  `kapasitas` int(11) DEFAULT NULL,
  `wali_kelas_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kelas`
--

INSERT INTO `kelas` (`kelas_id`, `nama_kelas`, `jurusan`, `kapasitas`, `wali_kelas_id`, `created_at`, `updated_at`) VALUES
(1, 'XII PPLG 1', 'Pengembangan Perangkat Lunak dan Gim', 31, 1, '2025-11-18 20:28:29', '2025-11-19 02:23:44'),
(2, 'XII PPLG 2', 'Pengembangan Perangkat Lunak dan Gim', 31, 15, '2025-11-19 07:33:12', '2025-11-19 07:33:12');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(24, '0001_01_01_000001_create_cache_table', 1),
(25, '0001_01_01_000002_create_jobs_table', 1),
(26, '2025_11_11_000000_create_users_table', 2),
(27, '2025_01_15_000000_add_profile_photo_to_users_table', 3),
(28, '2025_11_11_000001_create_tahun_ajaran_table', 4),
(29, '2025_11_11_000002_create_guru_table', 5),
(30, '2025_11_11_000003_create_kelas_table', 6),
(31, '2025_11_11_000004_create_siswa_table', 7),
(32, '2025_11_11_000005_create_orang_tua_table', 8),
(33, '2025_11_11_000006_create_jenis_pelanggaran_table', 9),
(34, '2025_11_11_000008_create_jenis_sanksi_table', 10),
(35, '2025_11_11_000009_create_pelanggaran_table', 11),
(36, '2025_11_11_000010_create_sanksi_table', 12),
(37, '2025_11_11_000011_create_pelaksanaan_sanksi_table', 13),
(38, '2025_11_11_000007_create_jenis_prestasi_table', 14),
(39, '2025_11_11_000012_create_prestasi_table', 15),
(40, '2025_11_11_000013_create_bimbingan_konseling_table', 16),
(41, '2025_11_11_000014_create_monitoring_pelanggaran_table', 17),
(42, '2025_11_11_000015_create_verifikasi_data_table', 18),
(43, '2025_11_11_110845_create_sessions_table', 19),
(44, '2025_11_14_012610_add_remember_token_to_users_table', 20),
(46, '2025_01_16_000000_update_sanksi_structure', 16),
(47, '2025_01_16_000001_fix_sanksi_data', 16),
(48, '2025_11_17_005821_add_foreign_keys_to_users_table', 21),
(49, '2025_11_19_055206_add_followup_fields_to_sanksi_table', 22),
(50, '2025_11_19_055231_add_sanksi_id_to_input_bk_table', 23),
(51, '2024_11_19_000001_add_pencatat_nama_to_pelanggaran_table', 24),
(52, '2025_01_17_000000_clean_sample_bukti_foto', 25),
(53, '2025_11_19_140000_fix_user_level_enum', 26);

-- --------------------------------------------------------

--
-- Table structure for table `monitoring_pelanggaran`
--

CREATE TABLE `monitoring_pelanggaran` (
  `monitoring_id` bigint(20) UNSIGNED NOT NULL,
  `pelanggaran_id` bigint(20) UNSIGNED NOT NULL,
  `guru_kepsek` bigint(20) UNSIGNED NOT NULL,
  `status_monitoring` enum('Menunggu','Diproses','Selesai') NOT NULL DEFAULT 'Menunggu',
  `catatan_monitoring` text DEFAULT NULL,
  `tanggal_monitoring` date NOT NULL,
  `tindak_lanjut` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `sanksi_id` bigint(20) UNSIGNED DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `type`, `user_id`, `sanksi_id`, `title`, `message`, `is_read`, `created_at`, `updated_at`) VALUES
(1, 'bk_panggilan', 18, NULL, 'Panggilan Konseling BK', 'Anda dipanggil untuk konseling BK dengan topik: pribadi pada tanggal 19/11/2025', 0, '2025-11-19 03:02:08', '2025-11-19 03:02:08'),
(2, 'bk_panggilan', 18, NULL, 'Panggilan Konseling BK', 'Anda dipanggil untuk konseling BK dengan topik: Sosial pada tanggal 19/11/2025', 0, '2025-11-19 04:10:40', '2025-11-19 04:10:40'),
(6, 'sanksi_followup', 25, 15, 'Follow-up Sanksi Diperlukan', 'Sanksi untuk siswa Riska Dwi Ramadani memerlukan follow-up konseling. Guru penanggung jawab: Ridwan kamil. Jenis sanksi: Teguran Tertulis', 1, '2025-11-19 23:06:10', '2025-11-19 23:07:33'),
(7, 'bk_panggilan', 27, NULL, 'Panggilan Konseling BK', 'Anda dipanggil untuk konseling BK dengan topik: pribadi pada tanggal 20/11/2025', 1, '2025-11-19 23:07:34', '2025-11-19 23:19:07'),
(8, 'bk_reminder', 25, NULL, 'Reminder Konseling BK', 'Anda telah memanggil siswa Riska Dwi Ramadani untuk konseling BK dengan topik: pribadi pada tanggal 20/11/2025', 0, '2025-11-19 23:07:34', '2025-11-19 23:07:34'),
(9, 'sanksi_followup', 25, 16, 'Follow-up Sanksi Diperlukan', 'Sanksi untuk siswa Riska Dwi Ramadani memerlukan follow-up konseling. Guru penanggung jawab: Ridwan kamil. Jenis sanksi: Teguran Lisan', 1, '2025-11-20 01:52:07', '2025-11-20 01:53:50'),
(10, 'bk_panggilan', 27, NULL, 'Panggilan Konseling BK', 'Anda dipanggil untuk konseling BK dengan topik: pribadi pada tanggal 20/11/2025', 1, '2025-11-20 01:53:51', '2025-11-20 01:54:18'),
(11, 'bk_reminder', 25, NULL, 'Reminder Konseling BK', 'Anda telah memanggil siswa Riska Dwi Ramadani untuk konseling BK dengan topik: pribadi pada tanggal 20/11/2025', 0, '2025-11-20 01:53:51', '2025-11-20 01:53:51'),
(12, 'bk_panggilan', 27, NULL, 'Panggilan Konseling BK', 'Anda dipanggil untuk konseling BK dengan topik: Masalah belajar pada tanggal 21/11/2025', 0, '2025-11-20 22:39:27', '2025-11-20 22:39:27'),
(13, 'bk_reminder', 25, NULL, 'Reminder Konseling BK', 'Anda telah memanggil siswa Riska Dwi Ramadani untuk konseling BK dengan topik: Masalah belajar pada tanggal 21/11/2025', 1, '2025-11-20 22:39:27', '2025-11-24 20:53:53');

-- --------------------------------------------------------

--
-- Table structure for table `orang_tua`
--

CREATE TABLE `orang_tua` (
  `ortu_id` bigint(20) UNSIGNED NOT NULL,
  `siswa_id` bigint(20) UNSIGNED NOT NULL,
  `hubungan` enum('Ayah','Ibu','Wali') NOT NULL,
  `nama_orangtua` varchar(100) NOT NULL,
  `pekerjaan` varchar(50) DEFAULT NULL,
  `pendidikan` varchar(50) DEFAULT NULL,
  `no_telp` varchar(15) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orang_tua`
--

INSERT INTO `orang_tua` (`ortu_id`, `siswa_id`, `hubungan`, `nama_orangtua`, `pekerjaan`, `pendidikan`, `no_telp`, `alamat`, `created_at`, `updated_at`) VALUES
(4, 1, 'Ibu', 'Lala Laelasari', 'Ibu rumah tangga', 'Smp', '083176266583', 'kp.sukaahayu', '2025-11-19 07:49:25', '2025-11-19 07:49:25');

-- --------------------------------------------------------

--
-- Table structure for table `pelaksanaan_sanksi`
--

CREATE TABLE `pelaksanaan_sanksi` (
  `pelaksanaan_sanksi_id` bigint(20) UNSIGNED NOT NULL,
  `sanksi_id` bigint(20) UNSIGNED NOT NULL,
  `tanggal_pelaksanaan` date NOT NULL,
  `deskripsi_pelaksanaan` text DEFAULT NULL,
  `bukti_pelaksanaan` varchar(255) DEFAULT NULL,
  `status` enum('terjadwal','dikerjakan','tuntas','terlambat','perpanjangan') NOT NULL DEFAULT 'terjadwal',
  `catatan` text DEFAULT NULL,
  `guru_pengawas` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pelaksanaan_sanksi`
--

INSERT INTO `pelaksanaan_sanksi` (`pelaksanaan_sanksi_id`, `sanksi_id`, `tanggal_pelaksanaan`, `deskripsi_pelaksanaan`, `bukti_pelaksanaan`, `status`, `catatan`, `guru_pengawas`, `created_at`, `updated_at`) VALUES
(7, 15, '2025-11-21', 'Sudah tuntas', 'bukti_pelaksanaan/nSmnhFCAy40v12PgEbNMwTV6IdSBvyDVuNbC92HP.jpg', 'tuntas', NULL, 9, '2025-11-20 01:53:01', '2025-11-20 01:53:01');

-- --------------------------------------------------------

--
-- Table structure for table `pelanggaran`
--

CREATE TABLE `pelanggaran` (
  `pelanggaran_id` bigint(20) UNSIGNED NOT NULL,
  `siswa_id` bigint(20) UNSIGNED NOT NULL,
  `guru_pencatat` bigint(20) UNSIGNED NOT NULL,
  `pencatat_nama` varchar(255) DEFAULT NULL,
  `jenis_pelanggaran_id` bigint(20) UNSIGNED NOT NULL,
  `tahun_ajaran_id` bigint(20) UNSIGNED NOT NULL,
  `poin` int(11) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `bukti_foto` varchar(255) DEFAULT NULL,
  `status_verifikasi` enum('menunggu','diverifikasi','ditolak','revisi') NOT NULL DEFAULT 'menunggu',
  `guru_verifikator` bigint(20) UNSIGNED DEFAULT NULL,
  `catatan_verifikasi` text DEFAULT NULL,
  `tanggal` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pelanggaran`
--

INSERT INTO `pelanggaran` (`pelanggaran_id`, `siswa_id`, `guru_pencatat`, `pencatat_nama`, `jenis_pelanggaran_id`, `tahun_ajaran_id`, `poin`, `keterangan`, `bukti_foto`, `status_verifikasi`, `guru_verifikator`, `catatan_verifikasi`, `tanggal`, `created_at`, `updated_at`) VALUES
(21, 3, 14, NULL, 1, 1, 5, 'Membuat keributan', 'pelanggaran/oyhFIKcSCuucVTSviAaXHr5I8PkCcrJmBO0FLuh1.jpg', 'diverifikasi', 1, NULL, '2025-11-20', '2025-11-19 17:47:57', '2025-11-19 17:53:25'),
(22, 3, 15, NULL, 1, 1, 5, 'Membuat gaduh di kelas', 'pelanggaran/2KuMsNKy9yutyEijFGuStPqP5kAMxz7CY6Hdzhuh.jpg', 'diverifikasi', 16, NULL, '2025-11-20', '2025-11-19 21:41:24', '2025-11-19 21:48:54'),
(23, 1, 15, NULL, 1, 1, 5, 'berisik', 'pelanggaran/foFubgkTQ3hX5cJuFAec5AvflLlLJ6hiBcQbm4Mo.jpg', 'diverifikasi', 16, NULL, '2025-11-20', '2025-11-19 22:53:54', '2025-11-19 22:55:35'),
(24, 1, 1, NULL, 1, 1, 5, 'Membuat keributan di kelas', 'pelanggaran/nXJt7NdXuiB2lt1mkfn7DMQ43jpEzGTQp3Leb5NX.jpg', 'diverifikasi', 1, 'Pencatat: Kesiswaan - Menunggu verifikasi kesiswaan', '2025-11-20', '2025-11-20 01:51:18', '2025-11-20 01:51:18');

-- --------------------------------------------------------

--
-- Table structure for table `prestasi`
--

CREATE TABLE `prestasi` (
  `prestasi_id` bigint(20) UNSIGNED NOT NULL,
  `siswa_id` bigint(20) UNSIGNED NOT NULL,
  `guru_pencatat` bigint(20) UNSIGNED NOT NULL,
  `jenis_prestasi_id` bigint(20) UNSIGNED NOT NULL,
  `tahun_ajaran_id` bigint(20) UNSIGNED NOT NULL,
  `poin` int(11) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `tingkat` enum('Sekolah','Kabupaten','Provinsi','Nasional','Internasional') DEFAULT NULL,
  `penghargaan` varchar(100) DEFAULT NULL,
  `bukti_dokumen` varchar(255) DEFAULT NULL,
  `status_verifikasi` enum('menunggu','diverifikasi','ditolak','revisi') NOT NULL DEFAULT 'menunggu',
  `guru_verifikator` bigint(20) UNSIGNED DEFAULT NULL,
  `tanggal` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sanksi`
--

CREATE TABLE `sanksi` (
  `sanksi_id` bigint(20) UNSIGNED NOT NULL,
  `pelanggaran_id` bigint(20) UNSIGNED NOT NULL,
  `jenis_sanksi_id` bigint(20) UNSIGNED NOT NULL,
  `deskripsi_sanksi` text DEFAULT NULL,
  `tanggal_mulai` date DEFAULT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `status` enum('terdaftar','dijadwalkan','berlangsung','selesai','tindak_lanjut') NOT NULL DEFAULT 'terdaftar',
  `catatan_pelaksanaan` text DEFAULT NULL,
  `guru_penanggungjawab` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `assigned_to_bk` tinyint(1) NOT NULL DEFAULT 0,
  `followup_status` enum('pending','in_progress','completed') NOT NULL DEFAULT 'pending',
  `bk_user_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sanksi`
--

INSERT INTO `sanksi` (`sanksi_id`, `pelanggaran_id`, `jenis_sanksi_id`, `deskripsi_sanksi`, `tanggal_mulai`, `tanggal_selesai`, `status`, `catatan_pelaksanaan`, `guru_penanggungjawab`, `created_at`, `updated_at`, `assigned_to_bk`, `followup_status`, `bk_user_id`) VALUES
(15, 23, 2, NULL, '2025-11-20', '2025-11-21', 'dijadwalkan', NULL, 9, '2025-11-19 23:06:09', '2025-11-19 23:06:09', 0, 'pending', NULL),
(16, 24, 1, NULL, '2025-11-20', '2025-11-21', 'dijadwalkan', NULL, 9, '2025-11-20 01:52:07', '2025-11-20 01:52:07', 0, 'pending', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `siswa`
--

CREATE TABLE `siswa` (
  `siswa_id` bigint(20) UNSIGNED NOT NULL,
  `nis` varchar(20) NOT NULL,
  `nisn` varchar(20) DEFAULT NULL,
  `nama_siswa` varchar(100) NOT NULL,
  `jenis_kelamin` enum('Laki-laki','Perempuan') NOT NULL,
  `status_kesiswaan` enum('aktif','lulus','pindah','drop_out','cuti') NOT NULL DEFAULT 'aktif',
  `tanggal_lahir` date DEFAULT NULL,
  `tempat_lahir` varchar(50) DEFAULT NULL,
  `alamat` varchar(255) DEFAULT NULL,
  `no_telp` varchar(15) DEFAULT NULL,
  `kelas_id` bigint(20) UNSIGNED DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `siswa`
--

INSERT INTO `siswa` (`siswa_id`, `nis`, `nisn`, `nama_siswa`, `jenis_kelamin`, `status_kesiswaan`, `tanggal_lahir`, `tempat_lahir`, `alamat`, `no_telp`, `kelas_id`, `foto`, `created_at`, `updated_at`) VALUES
(1, '232417070076', '23891238283', 'Riska Dwi Ramadani', 'Perempuan', 'aktif', '2007-11-09', 'Bandung', 'Kp.Sukahayu', '0831726309', 1, NULL, '2025-11-18 20:28:55', '2025-11-18 20:28:55'),
(3, '232417070063', '2109381', 'Mutiara Sukma', 'Perempuan', 'aktif', '2007-09-09', 'Bandung', 'Tanjakan Muncang', '08219818', 1, NULL, '2025-11-19 01:25:54', '2025-11-19 01:25:54');

-- --------------------------------------------------------

--
-- Table structure for table `tahun_ajaran`
--

CREATE TABLE `tahun_ajaran` (
  `tahun_ajaran_id` bigint(20) UNSIGNED NOT NULL,
  `kode_tahun` varchar(10) NOT NULL,
  `tahun_ajaran` varchar(20) NOT NULL,
  `semester` enum('Ganjil','Genap') NOT NULL,
  `status_aktif` tinyint(1) NOT NULL DEFAULT 0,
  `tanggal_mulai` date DEFAULT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tahun_ajaran`
--

INSERT INTO `tahun_ajaran` (`tahun_ajaran_id`, `kode_tahun`, `tahun_ajaran`, `semester`, `status_aktif`, `tanggal_mulai`, `tanggal_selesai`, `created_at`, `updated_at`) VALUES
(1, 'K001', '2025/026', 'Ganjil', 1, NULL, NULL, '2025-11-18 20:27:14', '2025-11-18 20:27:14');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `guru_id` bigint(20) UNSIGNED DEFAULT NULL,
  `siswa_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ortu_id` bigint(20) UNSIGNED DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL,
  `level` varchar(20) NOT NULL,
  `can_verify` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `guru_id`, `siswa_id`, `ortu_id`, `username`, `password`, `profile_photo`, `nama_lengkap`, `level`, `can_verify`, `is_active`, `last_login`, `created_at`, `updated_at`, `remember_token`) VALUES
(15, NULL, NULL, NULL, 'admin', '$2y$12$bQHfDc2m5ZpsoCJbHRhq1OtDT2iDLRV6rPPBOzsurmzPvfg6xTtHC', NULL, NULL, 'admin', 1, 1, '2025-11-25 01:02:00', '2025-11-19 02:01:21', '2025-11-24 18:02:00', NULL),
(21, 14, NULL, NULL, 'guru', '$2y$12$GPIGIg6C45HiciWmVSkCS.yE3cd35FyS9/yJ4eVTwaKOunl76mVPK', NULL, 'Cika', 'guru', 0, 1, '2025-11-25 03:25:21', '2025-11-19 07:17:30', '2025-11-24 20:25:21', NULL),
(22, 15, NULL, NULL, 'guru1', '$2y$12$pZkXP5iNd6//DQDQw3G/jOrjCBprAUhzeFVIE5CO60cbMt7nPd0bC', NULL, NULL, 'guru', 0, 1, '2025-11-25 03:36:20', '2025-11-19 07:32:44', '2025-11-24 20:36:20', NULL),
(23, NULL, NULL, 4, 'ortu', '$2y$12$K5XhEl6Nd.ppWYpjtBhrzeKmh3Z8TGCJv360vnh0/Dasahrd685E.', NULL, NULL, 'orang_tua', 0, 1, '2025-11-25 04:21:06', '2025-11-19 07:49:51', '2025-11-24 21:21:06', NULL),
(24, 16, NULL, NULL, 'kesiswaan', '$2y$12$28GVPS6Zr0GlzNidcbYZkuNAS52qLNb760I/GDV2m72w4prZVH6KO', NULL, 'Gatot', 'kesiswaan', 0, 1, '2025-11-25 02:32:07', '2025-11-19 19:37:57', '2025-11-24 19:32:07', NULL),
(25, 9, NULL, NULL, 'bk', '$2y$12$4TNZmQB5lkYVZuJe44dUeuL39PZXsURxPpQESQzhZKfxgaHL2t32m', NULL, NULL, 'konselor_bk', 0, 1, '2025-11-25 03:52:45', '2025-11-19 21:37:30', '2025-11-24 20:52:45', NULL),
(26, 17, NULL, NULL, 'kepsek', '$2y$12$eFJDJAs35LuZdYM3Rj4arOQz2VmEhOxJNTIs2FqU9Bins/EotJWdK', NULL, NULL, 'kepala_sekolah', 0, 1, '2025-11-24 06:08:47', '2025-11-19 21:38:51', '2025-11-23 23:08:47', NULL),
(27, NULL, 1, NULL, 'siswa', '$2y$12$kTh5QUtF6mouCcJwHzTql.SQSzQDWaWMbpKbPueKbonwoucPIr1je', NULL, NULL, 'siswa', 0, 1, '2025-11-25 04:08:53', '2025-11-19 21:39:24', '2025-11-24 21:08:53', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `verifikasi_data`
--

CREATE TABLE `verifikasi_data` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tabel_terkait` varchar(100) NOT NULL,
  `id_terkait` int(11) NOT NULL,
  `guru_verifikator` bigint(20) UNSIGNED NOT NULL,
  `status` enum('menunggu','diverifikasi','ditolak') NOT NULL DEFAULT 'menunggu',
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bimbingan_konseling`
--
ALTER TABLE `bimbingan_konseling`
  ADD PRIMARY KEY (`bk_id`),
  ADD KEY `bimbingan_konseling_siswa_id_foreign` (`siswa_id`),
  ADD KEY `bimbingan_konseling_guru_konselor_foreign` (`guru_konselor`),
  ADD KEY `bimbingan_konseling_tahun_ajaran_id_foreign` (`tahun_ajaran_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `guru`
--
ALTER TABLE `guru`
  ADD PRIMARY KEY (`guru_id`),
  ADD UNIQUE KEY `guru_nip_unique` (`nip`);

--
-- Indexes for table `jenis_pelanggaran`
--
ALTER TABLE `jenis_pelanggaran`
  ADD PRIMARY KEY (`jenis_pelanggaran_id`);

--
-- Indexes for table `jenis_prestasi`
--
ALTER TABLE `jenis_prestasi`
  ADD PRIMARY KEY (`jenis_prestasi_id`);

--
-- Indexes for table `jenis_sanksi`
--
ALTER TABLE `jenis_sanksi`
  ADD PRIMARY KEY (`jenis_sanksi_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kelas`
--
ALTER TABLE `kelas`
  ADD PRIMARY KEY (`kelas_id`),
  ADD KEY `kelas_wali_kelas_id_foreign` (`wali_kelas_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `monitoring_pelanggaran`
--
ALTER TABLE `monitoring_pelanggaran`
  ADD PRIMARY KEY (`monitoring_id`),
  ADD KEY `monitoring_pelanggaran_pelanggaran_id_foreign` (`pelanggaran_id`),
  ADD KEY `monitoring_pelanggaran_guru_kepsek_foreign` (`guru_kepsek`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orang_tua`
--
ALTER TABLE `orang_tua`
  ADD PRIMARY KEY (`ortu_id`),
  ADD KEY `orang_tua_siswa_id_foreign` (`siswa_id`);

--
-- Indexes for table `pelaksanaan_sanksi`
--
ALTER TABLE `pelaksanaan_sanksi`
  ADD PRIMARY KEY (`pelaksanaan_sanksi_id`),
  ADD KEY `pelaksanaan_sanksi_sanksi_id_foreign` (`sanksi_id`),
  ADD KEY `pelaksanaan_sanksi_guru_pengawas_foreign` (`guru_pengawas`);

--
-- Indexes for table `pelanggaran`
--
ALTER TABLE `pelanggaran`
  ADD PRIMARY KEY (`pelanggaran_id`),
  ADD KEY `pelanggaran_siswa_id_foreign` (`siswa_id`),
  ADD KEY `pelanggaran_guru_pencatat_foreign` (`guru_pencatat`),
  ADD KEY `pelanggaran_jenis_pelanggaran_id_foreign` (`jenis_pelanggaran_id`),
  ADD KEY `pelanggaran_tahun_ajaran_id_foreign` (`tahun_ajaran_id`),
  ADD KEY `pelanggaran_guru_verifikator_foreign` (`guru_verifikator`);

--
-- Indexes for table `prestasi`
--
ALTER TABLE `prestasi`
  ADD PRIMARY KEY (`prestasi_id`),
  ADD KEY `prestasi_siswa_id_foreign` (`siswa_id`),
  ADD KEY `prestasi_guru_pencatat_foreign` (`guru_pencatat`),
  ADD KEY `prestasi_jenis_prestasi_id_foreign` (`jenis_prestasi_id`),
  ADD KEY `prestasi_tahun_ajaran_id_foreign` (`tahun_ajaran_id`),
  ADD KEY `prestasi_guru_verifikator_foreign` (`guru_verifikator`);

--
-- Indexes for table `sanksi`
--
ALTER TABLE `sanksi`
  ADD PRIMARY KEY (`sanksi_id`),
  ADD KEY `sanksi_pelanggaran_id_foreign` (`pelanggaran_id`),
  ADD KEY `sanksi_jenis_sanksi_id_foreign` (`jenis_sanksi_id`),
  ADD KEY `sanksi_guru_penanggungjawab_foreign` (`guru_penanggungjawab`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `siswa`
--
ALTER TABLE `siswa`
  ADD PRIMARY KEY (`siswa_id`),
  ADD UNIQUE KEY `siswa_nis_unique` (`nis`),
  ADD UNIQUE KEY `siswa_nisn_unique` (`nisn`),
  ADD KEY `siswa_kelas_id_foreign` (`kelas_id`);

--
-- Indexes for table `tahun_ajaran`
--
ALTER TABLE `tahun_ajaran`
  ADD PRIMARY KEY (`tahun_ajaran_id`),
  ADD UNIQUE KEY `tahun_ajaran_kode_tahun_unique` (`kode_tahun`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `users_username_unique` (`username`);

--
-- Indexes for table `verifikasi_data`
--
ALTER TABLE `verifikasi_data`
  ADD PRIMARY KEY (`id`),
  ADD KEY `verifikasi_data_guru_verifikator_foreign` (`guru_verifikator`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bimbingan_konseling`
--
ALTER TABLE `bimbingan_konseling`
  MODIFY `bk_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `guru`
--
ALTER TABLE `guru`
  MODIFY `guru_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `jenis_pelanggaran`
--
ALTER TABLE `jenis_pelanggaran`
  MODIFY `jenis_pelanggaran_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `jenis_prestasi`
--
ALTER TABLE `jenis_prestasi`
  MODIFY `jenis_prestasi_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `jenis_sanksi`
--
ALTER TABLE `jenis_sanksi`
  MODIFY `jenis_sanksi_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kelas`
--
ALTER TABLE `kelas`
  MODIFY `kelas_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `monitoring_pelanggaran`
--
ALTER TABLE `monitoring_pelanggaran`
  MODIFY `monitoring_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `orang_tua`
--
ALTER TABLE `orang_tua`
  MODIFY `ortu_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `pelaksanaan_sanksi`
--
ALTER TABLE `pelaksanaan_sanksi`
  MODIFY `pelaksanaan_sanksi_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `pelanggaran`
--
ALTER TABLE `pelanggaran`
  MODIFY `pelanggaran_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `prestasi`
--
ALTER TABLE `prestasi`
  MODIFY `prestasi_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `sanksi`
--
ALTER TABLE `sanksi`
  MODIFY `sanksi_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `siswa`
--
ALTER TABLE `siswa`
  MODIFY `siswa_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tahun_ajaran`
--
ALTER TABLE `tahun_ajaran`
  MODIFY `tahun_ajaran_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `verifikasi_data`
--
ALTER TABLE `verifikasi_data`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bimbingan_konseling`
--
ALTER TABLE `bimbingan_konseling`
  ADD CONSTRAINT `bimbingan_konseling_guru_konselor_foreign` FOREIGN KEY (`guru_konselor`) REFERENCES `guru` (`guru_id`),
  ADD CONSTRAINT `bimbingan_konseling_siswa_id_foreign` FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`siswa_id`),
  ADD CONSTRAINT `bimbingan_konseling_tahun_ajaran_id_foreign` FOREIGN KEY (`tahun_ajaran_id`) REFERENCES `tahun_ajaran` (`tahun_ajaran_id`);

--
-- Constraints for table `kelas`
--
ALTER TABLE `kelas`
  ADD CONSTRAINT `kelas_wali_kelas_id_foreign` FOREIGN KEY (`wali_kelas_id`) REFERENCES `guru` (`guru_id`);

--
-- Constraints for table `monitoring_pelanggaran`
--
ALTER TABLE `monitoring_pelanggaran`
  ADD CONSTRAINT `monitoring_pelanggaran_guru_kepsek_foreign` FOREIGN KEY (`guru_kepsek`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `monitoring_pelanggaran_pelanggaran_id_foreign` FOREIGN KEY (`pelanggaran_id`) REFERENCES `pelanggaran` (`pelanggaran_id`);

--
-- Constraints for table `orang_tua`
--
ALTER TABLE `orang_tua`
  ADD CONSTRAINT `orang_tua_siswa_id_foreign` FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`siswa_id`);

--
-- Constraints for table `pelaksanaan_sanksi`
--
ALTER TABLE `pelaksanaan_sanksi`
  ADD CONSTRAINT `pelaksanaan_sanksi_guru_pengawas_foreign` FOREIGN KEY (`guru_pengawas`) REFERENCES `guru` (`guru_id`),
  ADD CONSTRAINT `pelaksanaan_sanksi_sanksi_id_foreign` FOREIGN KEY (`sanksi_id`) REFERENCES `sanksi` (`sanksi_id`);

--
-- Constraints for table `pelanggaran`
--
ALTER TABLE `pelanggaran`
  ADD CONSTRAINT `pelanggaran_guru_pencatat_foreign` FOREIGN KEY (`guru_pencatat`) REFERENCES `guru` (`guru_id`),
  ADD CONSTRAINT `pelanggaran_guru_verifikator_foreign` FOREIGN KEY (`guru_verifikator`) REFERENCES `guru` (`guru_id`),
  ADD CONSTRAINT `pelanggaran_jenis_pelanggaran_id_foreign` FOREIGN KEY (`jenis_pelanggaran_id`) REFERENCES `jenis_pelanggaran` (`jenis_pelanggaran_id`),
  ADD CONSTRAINT `pelanggaran_siswa_id_foreign` FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`siswa_id`),
  ADD CONSTRAINT `pelanggaran_tahun_ajaran_id_foreign` FOREIGN KEY (`tahun_ajaran_id`) REFERENCES `tahun_ajaran` (`tahun_ajaran_id`);

--
-- Constraints for table `prestasi`
--
ALTER TABLE `prestasi`
  ADD CONSTRAINT `prestasi_guru_pencatat_foreign` FOREIGN KEY (`guru_pencatat`) REFERENCES `guru` (`guru_id`),
  ADD CONSTRAINT `prestasi_guru_verifikator_foreign` FOREIGN KEY (`guru_verifikator`) REFERENCES `guru` (`guru_id`),
  ADD CONSTRAINT `prestasi_jenis_prestasi_id_foreign` FOREIGN KEY (`jenis_prestasi_id`) REFERENCES `jenis_prestasi` (`jenis_prestasi_id`),
  ADD CONSTRAINT `prestasi_siswa_id_foreign` FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`siswa_id`),
  ADD CONSTRAINT `prestasi_tahun_ajaran_id_foreign` FOREIGN KEY (`tahun_ajaran_id`) REFERENCES `tahun_ajaran` (`tahun_ajaran_id`);

--
-- Constraints for table `sanksi`
--
ALTER TABLE `sanksi`
  ADD CONSTRAINT `sanksi_guru_penanggungjawab_foreign` FOREIGN KEY (`guru_penanggungjawab`) REFERENCES `guru` (`guru_id`),
  ADD CONSTRAINT `sanksi_jenis_sanksi_id_foreign` FOREIGN KEY (`jenis_sanksi_id`) REFERENCES `jenis_sanksi` (`jenis_sanksi_id`),
  ADD CONSTRAINT `sanksi_pelanggaran_id_foreign` FOREIGN KEY (`pelanggaran_id`) REFERENCES `pelanggaran` (`pelanggaran_id`);

--
-- Constraints for table `siswa`
--
ALTER TABLE `siswa`
  ADD CONSTRAINT `siswa_kelas_id_foreign` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`kelas_id`);

--
-- Constraints for table `verifikasi_data`
--
ALTER TABLE `verifikasi_data`
  ADD CONSTRAINT `verifikasi_data_guru_verifikator_foreign` FOREIGN KEY (`guru_verifikator`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
