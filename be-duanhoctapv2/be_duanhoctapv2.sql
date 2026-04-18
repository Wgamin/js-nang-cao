-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 18, 2026 at 09:50 AM
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
-- Database: `be_duanhoctapv2`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendances`
--

CREATE TABLE `attendances` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `schedule_id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `is_present` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('present','absent','late') NOT NULL DEFAULT 'absent',
  `note` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attendances`
--

INSERT INTO `attendances` (`id`, `schedule_id`, `student_id`, `is_present`, `status`, `note`, `created_at`, `updated_at`) VALUES
(1, 6, 4, 1, 'present', NULL, '2026-04-17 11:46:04', '2026-04-17 11:46:04'),
(2, 6, 5, 1, 'present', NULL, '2026-04-17 11:46:04', '2026-04-17 11:46:04'),
(3, 6, 6, 0, 'late', 'Đến muộn 10 phút', '2026-04-17 11:46:04', '2026-04-17 11:46:04'),
(4, 2, 4, 0, 'absent', NULL, '2026-04-17 12:58:03', '2026-04-17 12:58:03'),
(5, 2, 5, 1, 'present', NULL, '2026-04-17 12:58:03', '2026-04-17 12:58:03'),
(6, 2, 6, 0, 'absent', NULL, '2026-04-17 12:58:03', '2026-04-17 12:58:03'),
(7, 1, 4, 0, 'absent', NULL, '2026-04-17 13:17:16', '2026-04-17 13:17:16'),
(8, 1, 5, 1, 'present', NULL, '2026-04-17 13:17:16', '2026-04-18 00:12:45'),
(9, 1, 6, 1, 'present', NULL, '2026-04-17 13:17:16', '2026-04-18 00:12:45');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel-cache-spatie.permission.cache', 'a:3:{s:5:\"alias\";a:0:{}s:11:\"permissions\";a:0:{}s:5:\"roles\";a:0:{}}', 1776542112);

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
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `teacher_id` bigint(20) UNSIGNED NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `subject_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`id`, `name`, `teacher_id`, `description`, `status`, `created_at`, `updated_at`, `subject_id`) VALUES
(1, 'Lớp Toán 12', 2, NULL, 'active', '2026-04-17 11:20:42', '2026-04-17 11:20:42', NULL),
(2, 'Lớp Lý 11', 2, NULL, 'active', '2026-04-17 11:20:42', '2026-04-17 11:20:42', NULL),
(3, 'Lớp Anh Văn 10', 3, NULL, 'active', '2026-04-17 11:20:42', '2026-04-17 11:20:42', NULL),
(4, 'lớp toán a', 3, 'gẻgte', 'active', '2026-04-17 12:43:10', '2026-04-17 12:43:10', 1);

-- --------------------------------------------------------

--
-- Table structure for table `class_user`
--

CREATE TABLE `class_user` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `class_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `class_user`
--

INSERT INTO `class_user` (`id`, `class_id`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 1, 4, NULL, NULL),
(2, 1, 5, NULL, NULL),
(3, 1, 6, NULL, NULL),
(4, 2, 4, NULL, NULL),
(5, 2, 5, NULL, NULL),
(6, 3, 5, NULL, NULL),
(7, 3, 6, NULL, NULL),
(8, 4, 4, NULL, NULL),
(9, 4, 6, NULL, NULL);

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
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_04_17_164951_create_permission_tables', 1),
(5, '2026_04_17_165015_create_personal_access_tokens_table', 1),
(6, '2026_04_17_170000_create_tutoring_tables', 1),
(7, '2026_04_18_000001_add_new_columns_to_tutoring_tables', 1),
(8, '2026_04_18_010000_add_parent_and_tuition_tables', 2),
(9, '2026_04_17_191349_create_subjects_table', 3),
(10, '2026_04_17_191350_create_rooms_table', 3),
(11, '2026_04_17_191351_alter_classes_and_schedules_add_subject_and_room_fk', 4),
(12, '2026_04_17_192429_remove_rooms_and_fix_subject_seeder_2', 5);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),
(1, 'App\\Models\\User', 8),
(1, 'App\\Models\\User', 22),
(1, 'App\\Models\\User', 26),
(2, 'App\\Models\\User', 2),
(2, 'App\\Models\\User', 3),
(2, 'App\\Models\\User', 27),
(2, 'App\\Models\\User', 31),
(3, 'App\\Models\\User', 4),
(3, 'App\\Models\\User', 5),
(3, 'App\\Models\\User', 6),
(3, 'App\\Models\\User', 18),
(3, 'App\\Models\\User', 19),
(3, 'App\\Models\\User', 21),
(3, 'App\\Models\\User', 23),
(3, 'App\\Models\\User', 24),
(3, 'App\\Models\\User', 28),
(3, 'App\\Models\\User', 29),
(3, 'App\\Models\\User', 32),
(4, 'App\\Models\\User', 7),
(4, 'App\\Models\\User', 20),
(4, 'App\\Models\\User', 25),
(4, 'App\\Models\\User', 30);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(10, 'App\\Models\\User', 1, 'API Token', '80d71a08d22a3c5eebe4a35cd9e3f8706c8e875fddbf3f374bc4280417c7b305', '[\"*\"]', '2026-04-17 13:05:57', NULL, '2026-04-17 12:09:03', '2026-04-17 13:05:57'),
(28, 'App\\Models\\User', 1, 'API Token', 'ec5d7712b0ee266cba1fd82719d44a5295242e0b8979d121baf410c1f0383638', '[\"*\"]', '2026-04-17 23:19:44', NULL, '2026-04-17 13:17:28', '2026-04-17 23:19:44'),
(39, 'App\\Models\\User', 1, 'API Token', 'ee42ff53190cb34794a1bcdc37bd3275149da8044bfa7f44f19442d7818dd0a0', '[\"*\"]', '2026-04-18 00:23:42', NULL, '2026-04-18 00:16:11', '2026-04-18 00:23:42');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'web', '2026-04-17 11:20:41', '2026-04-17 11:20:41'),
(2, 'Teacher', 'web', '2026-04-17 11:20:41', '2026-04-17 11:20:41'),
(3, 'Student', 'web', '2026-04-17 11:20:41', '2026-04-17 11:20:41'),
(4, 'Parent', 'web', '2026-04-17 11:46:02', '2026-04-17 11:46:02');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

CREATE TABLE `schedules` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `class_id` bigint(20) UNSIGNED NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime DEFAULT NULL,
  `room` varchar(255) NOT NULL,
  `note` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `schedules`
--

INSERT INTO `schedules` (`id`, `class_id`, `start_time`, `end_time`, `room`, `note`, `created_at`, `updated_at`) VALUES
(1, 1, '2026-04-18 18:00:00', NULL, 'Phòng 101', NULL, '2026-04-17 11:20:42', '2026-04-17 11:20:42'),
(2, 1, '2026-04-20 18:00:00', NULL, 'Phòng 101', NULL, '2026-04-17 11:20:42', '2026-04-17 11:20:42'),
(3, 2, '2026-04-19 19:30:00', NULL, 'Phòng 203', NULL, '2026-04-17 11:20:42', '2026-04-17 11:20:42'),
(4, 3, '2026-04-18 08:00:00', NULL, 'Phòng 305', NULL, '2026-04-17 11:20:42', '2026-04-17 11:20:42'),
(5, 3, '2026-04-18 15:00:00', '2026-04-18 03:00:00', 'Phòng 305', NULL, '2026-04-17 11:20:42', '2026-04-17 13:00:55'),
(6, 1, '2026-04-15 18:00:00', '2026-04-15 20:00:00', 'Phòng 101', 'Buổi học ôn tập', '2026-04-17 11:46:04', '2026-04-17 11:46:04'),
(7, 4, '2026-04-17 02:44:00', '2026-04-18 02:44:00', 'phòng a', '32egr', '2026-04-17 12:44:32', '2026-04-17 12:44:32');

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

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('eDxZI23ALLyrUftg42E5iUL0XzNq1epw6aTHCx2b', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibERxamkzNk9aNDRpalRnYmp6UmlRU3BTcGduMTB5c1JPbUJGVDd0dCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1776450346);

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Toán Học', 'Toán Trung học & Đại học', '2026-04-17 12:26:02', '2026-04-17 12:26:02'),
(2, 'Vật Lý', 'Vật Lý nền tảng & Nâng cao', '2026-04-17 12:26:02', '2026-04-17 12:26:02');

-- --------------------------------------------------------

--
-- Table structure for table `tuitions`
--

CREATE TABLE `tuitions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `study_class_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(12,0) NOT NULL DEFAULT 0 COMMENT 'Số tiền phải đóng',
  `amount_paid` decimal(12,0) NOT NULL DEFAULT 0 COMMENT 'Số tiền đã đóng',
  `status` enum('unpaid','partially_paid','paid') NOT NULL DEFAULT 'unpaid',
  `payment_date` date DEFAULT NULL COMMENT 'Ngày đóng tiền',
  `note` text DEFAULT NULL COMMENT 'Ghi chú: tiền mặt, chuyển khoản...',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tuitions`
--

INSERT INTO `tuitions` (`id`, `user_id`, `study_class_id`, `amount`, `amount_paid`, `status`, `payment_date`, `note`, `created_at`, `updated_at`) VALUES
(2, 4, 2, 700000, 350000, 'partially_paid', '2026-04-16', 'Chuyển khoản MB Bank', '2026-04-17 11:46:04', '2026-04-17 11:46:04'),
(3, 5, 1, 800000, 0, 'unpaid', NULL, NULL, '2026-04-17 11:46:04', '2026-04-17 11:46:04'),
(4, 5, 3, 600000, 600000, 'paid', '2026-04-14', 'Đóng đủ tiền mặt', '2026-04-17 11:46:04', '2026-04-17 11:46:04'),
(5, 6, 1, 800000, 0, 'unpaid', NULL, NULL, '2026-04-17 11:46:04', '2026-04-17 11:46:04'),
(6, 6, 3, 600000, 200000, 'partially_paid', '2026-04-10', 'Đóng một phần', '2026-04-17 11:46:04', '2026-04-17 11:46:04');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `phone`, `address`, `parent_id`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin User', 'admin@test.com', NULL, '$2y$12$J/yc60YPoecxF8LFwd5PiuOybjyg/AD8sudky6I14UHNopedTgY2m', NULL, NULL, NULL, NULL, '2026-04-17 11:20:41', '2026-04-17 11:20:41'),
(2, 'Nguyễn Văn Thầy', 'teacher@test.com', NULL, '$2y$12$A3rE9BTLlDWdCVAq.J9zxOT4eQSvUdlkHioNdQdfxBF.l5U69SE06', NULL, NULL, NULL, NULL, '2026-04-17 11:20:41', '2026-04-17 11:20:41'),
(3, 'Trần Thị Cô', 'teacher2@test.com', NULL, '$2y$12$dn6RkZRizX.PE9V93zuoD.uFwEcdyelSbxVXs5K.wMr1JDLcv1W.e', '7586070', NULL, NULL, NULL, '2026-04-17 11:20:42', '2026-04-17 11:31:47'),
(4, 'Lê Văn Học Sinh', 'student@test.com', NULL, '$2y$12$gscRCUC.YNoS5VE295xwcO./Ua1IxTR4cbP0Upmz7yalDtAwtHydG', NULL, NULL, 7, NULL, '2026-04-17 11:20:42', '2026-04-17 12:05:12'),
(5, 'Phạm Thị Sinh Viên', 'student2@test.com', NULL, '$2y$12$8Uaaj3zClPC4a3VGzjicPerothBfTTCIeZ4B1QVADGgqjqgHg93Uu', NULL, NULL, 7, NULL, '2026-04-17 11:20:42', '2026-04-17 12:05:12'),
(6, 'Hoàng Minh Trí', 'student3@test.com', NULL, '$2y$12$9PtDatrRgkm714Nip7n6vOg7bjrL0MvDN/F6aw3047d2QLE875Lem', NULL, NULL, NULL, NULL, '2026-04-17 11:20:42', '2026-04-17 11:20:42'),
(7, 'Phụ Huynh Nguyễn', 'parent@test.com', NULL, '$2y$12$KEo/XoKCcl9NYEuEHefvfuo6lKF.Oe3K2LwGzcQZYoZOBvad4iXFW', '0912345678', '123 Nguyễn Trãi, Quận 1', NULL, NULL, '2026-04-17 11:46:03', '2026-04-17 11:46:03'),
(8, 'admin1', 'admin1@gmail.com', NULL, '$2y$12$NmVYGx9WNVp6tMYZVV0.9eFmiVD7rp8NA87cx3OdzmfYPPkgBMf2K', '0343196386', 'abcxyz', NULL, NULL, '2026-04-17 12:51:41', '2026-04-17 12:51:55'),
(18, 'Nguyễn Văn A', 'anguyen@test.com', NULL, '$2y$12$sGz36DDbuU8gOalP4xiWL.H5UKa5TkM5k0JCO8T2B2xcVqmLjX1hG', 'pass123', 'Hà Nội', NULL, NULL, '2026-04-17 23:51:52', '2026-04-17 23:51:52'),
(19, 'Trần Thị B', 'btran@test.com', NULL, '$2y$12$JW5hr/cY1tpv4LLVxh5qJ.7hEjxf1fcCtcwt88Zf4C3xABRzs92JG', 'pass123', 'Đà Nẵng', NULL, NULL, '2026-04-17 23:51:52', '2026-04-17 23:51:52'),
(20, 'Lê Văn C', 'cle@test.com', NULL, '$2y$12$dxB5hVUipdj6anKCXtGGN.mqdHDefpyB6x9EmFRG08i9rRnu2E50a', 'pass123', 'HCM', NULL, NULL, '2026-04-17 23:51:53', '2026-04-17 23:51:53'),
(21, 'Phạm Minh D', 'dpham@test.com', NULL, '$2y$12$aDhbt9pM9fnnrRLuCQ.hj.7tifnBdP9mJATmXNd1FLqv63ABPdDTG', 'pass123', 'Cần Thơ', NULL, NULL, '2026-04-17 23:51:53', '2026-04-17 23:51:53'),
(22, 'Admin Tuyển', 'admin_test@test.com', NULL, '$2y$12$8apKxzrK3BWguNKHitXbfOTMQ6JhhA0zTXeNcoKl5VWlk3aEpai6m', 'admin123', 'Hải Phòng', NULL, NULL, '2026-04-17 23:51:53', '2026-04-17 23:51:53'),
(23, 'Bùi Minh Tuấn', 'tuantest1@test.com', NULL, '$2y$12$lPQ1UEo7710I9KxuH8/eiO36fkT/DouDYS4erSK.bRLSRBgvqMK6m', '901234455', 'Hà Nội', NULL, NULL, '2026-04-18 00:18:04', '2026-04-18 00:18:04'),
(24, 'Lê Hồng Hạnh', 'hanhtest2@test.com', NULL, '$2y$12$YiOJ3Y5VZRkG4r/IhCENd.JDy1/3sAdp.8rQLDHjp4k.TE4qQn14y', '988223344', 'Đà Nẵng', NULL, NULL, '2026-04-18 00:18:04', '2026-04-18 00:18:04'),
(25, 'Nguyễn Quang Hải', 'hai_parent@test.com', NULL, '$2y$12$QetngzXzI2qOqaVFfrDUXe8VGmR8TiWNgiBWFM2Etbis53oagL8AC', '911002233', 'Hải Phòng', NULL, NULL, '2026-04-18 00:18:05', '2026-04-18 00:18:05'),
(26, 'Vũ Đức Đam', 'dam_admin@test.com', NULL, '$2y$12$lGCHCExElOcEEXMRl7YBvum5baP9GRPY3R1rqZz7Giz0Orwiklai2', '888777666', 'TP. HCM', NULL, NULL, '2026-04-18 00:18:05', '2026-04-18 00:18:05'),
(27, 'Trần Thanh Tâm', 'tamtt@test.com', NULL, '$2y$12$9r6/IMbzg6ZRTUHbhz9DKeE4Oh8H.p/c7V.VPoROriJBj9SEiZZWy', '345667788', 'Cần Thơ', NULL, NULL, '2026-04-18 00:18:05', '2026-04-18 00:18:05'),
(28, 'Đặng Thu Thảo', 'thaodt@test.com', NULL, '$2y$12$ZIqKH8oxZN7W9.HCPM.DS.OM6xGj/RZl8dHXq..ksL8cnuFVho7Pm', '977112233', 'Huế', NULL, NULL, '2026-04-18 00:18:05', '2026-04-18 00:18:05'),
(29, 'Phan Anh Tuấn', 'tuanpa@test.com', NULL, '$2y$12$Nz8Uxvla0iBEQmcLe3b0e.kJlgB8.h.I04R/4IUmvN6CiM8vmvhZ2', '966445522', 'Nam Định', NULL, NULL, '2026-04-18 00:18:06', '2026-04-18 00:18:06'),
(30, 'Võ Văn Thưởng', 'thuong_parent@test.com', NULL, '$2y$12$ctfbFXUMD06Ocu4Ahe6t1eJPmK98Q03.LsvDfV96wI3Hp9/lz8b5C', '933221100', 'Bình Dương', NULL, NULL, '2026-04-18 00:18:06', '2026-04-18 00:18:06'),
(31, 'Lý Hải', 'lyhai_teacher@test.com', NULL, '$2y$12$rDj7xDxiRiPJRD22tl9eCuZBLNeIYTVOkK8JjIapSHguz3F0UVeXi', '322114455', 'Lâm Đồng', NULL, NULL, '2026-04-18 00:18:06', '2026-04-18 00:18:06'),
(32, 'Mai Phương Thúy', 'thuy_student@test.com', NULL, '$2y$12$JB4kKkTD.WCH1YRQITvINuqDcwcNgK/wXERMdHIH/wtrvMbbGuH0G', '944009988', 'Quảng Ninh', NULL, NULL, '2026-04-18 00:18:06', '2026-04-18 00:18:06');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendances`
--
ALTER TABLE `attendances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `attendances_schedule_id_foreign` (`schedule_id`),
  ADD KEY `attendances_student_id_foreign` (`student_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `classes_teacher_id_foreign` (`teacher_id`),
  ADD KEY `classes_subject_id_foreign` (`subject_id`);

--
-- Indexes for table `class_user`
--
ALTER TABLE `class_user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class_user_class_id_foreign` (`class_id`),
  ADD KEY `class_user_user_id_foreign` (`user_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

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
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `schedules_class_id_foreign` (`class_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `subjects_name_unique` (`name`);

--
-- Indexes for table `tuitions`
--
ALTER TABLE `tuitions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tuitions_user_id_foreign` (`user_id`),
  ADD KEY `tuitions_study_class_id_foreign` (`study_class_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_parent_id_foreign` (`parent_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendances`
--
ALTER TABLE `attendances`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `class_user`
--
ALTER TABLE `class_user`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tuitions`
--
ALTER TABLE `tuitions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendances`
--
ALTER TABLE `attendances`
  ADD CONSTRAINT `attendances_schedule_id_foreign` FOREIGN KEY (`schedule_id`) REFERENCES `schedules` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attendances_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `classes`
--
ALTER TABLE `classes`
  ADD CONSTRAINT `classes_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `classes_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `class_user`
--
ALTER TABLE `class_user`
  ADD CONSTRAINT `class_user_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `class_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `schedules`
--
ALTER TABLE `schedules`
  ADD CONSTRAINT `schedules_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tuitions`
--
ALTER TABLE `tuitions`
  ADD CONSTRAINT `tuitions_study_class_id_foreign` FOREIGN KEY (`study_class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tuitions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
