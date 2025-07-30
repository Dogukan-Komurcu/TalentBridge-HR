-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1
-- Üretim Zamanı: 30 Tem 2025, 14:06:58
-- Sunucu sürümü: 10.4.32-MariaDB
-- PHP Sürümü: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `talentbridge`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `applications`
--

CREATE TABLE `applications` (
  `id` int(11) NOT NULL,
  `job_posting_id` int(11) NOT NULL,
  `applicant_name` varchar(100) NOT NULL,
  `applicant_email` varchar(100) NOT NULL,
  `applicant_phone` varchar(15) DEFAULT NULL,
  `resume_path` varchar(255) DEFAULT NULL,
  `cover_letter` text DEFAULT NULL,
  `status` enum('pending','approved','rejected','interview') DEFAULT 'pending',
  `applied_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `reviewed_by` int(11) DEFAULT NULL,
  `reviewed_date` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `applications`
--

INSERT INTO `applications` (`id`, `job_posting_id`, `applicant_name`, `applicant_email`, `applicant_phone`, `resume_path`, `cover_letter`, `status`, `applied_date`, `reviewed_by`, `reviewed_date`, `notes`) VALUES
(1, 1, 'Ahmet Yılmaz', 'ahmet@email.com', '0532-123-4567', NULL, NULL, 'pending', '2025-07-28 07:30:00', NULL, NULL, NULL),
(2, 2, 'Ayşe Kaya', 'ayse@email.com', '0533-234-5678', NULL, NULL, 'approved', '2025-07-27 11:15:00', NULL, NULL, NULL),
(3, 3, 'Mehmet Özkan', 'mehmet@email.com', '0534-345-6789', NULL, NULL, 'pending', '2025-07-26 06:45:00', NULL, NULL, NULL),
(4, 4, 'Fatma Demir', 'fatma@email.com', '0535-456-7890', NULL, NULL, 'rejected', '2025-07-25 13:20:00', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `manager_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `departments`
--

INSERT INTO `departments` (`id`, `name`, `description`, `manager_id`, `created_at`) VALUES
(1, 'Bilgi Teknolojileri', 'Yazılım geliştirme ve IT altyapı', NULL, '2025-07-29 10:21:21'),
(2, 'Satış', 'Satış ve müşteri ilişkileri', NULL, '2025-07-29 10:21:21'),
(3, 'Pazarlama', 'Dijital pazarlama ve marka yönetimi', NULL, '2025-07-29 10:21:21'),
(4, 'İnsan Kaynakları', 'Personel yönetimi ve işe alım', NULL, '2025-07-29 10:21:21'),
(5, 'Finans', 'Muhasebe ve finansal planlama', NULL, '2025-07-29 10:21:21');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `interviews`
--

CREATE TABLE `interviews` (
  `id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `interviewer_id` int(11) NOT NULL,
  `interview_date` datetime NOT NULL,
  `location` varchar(200) DEFAULT NULL,
  `type` enum('phone','video','in_person') DEFAULT 'in_person',
  `status` enum('scheduled','completed','cancelled') DEFAULT 'scheduled',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `job_postings`
--

CREATE TABLE `job_postings` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `department_id` int(11) NOT NULL,
  `requirements` text DEFAULT NULL,
  `salary_min` decimal(10,2) DEFAULT NULL,
  `salary_max` decimal(10,2) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `employment_type` enum('full_time','part_time','contract','internship') DEFAULT 'full_time',
  `status` enum('active','draft','expired','closed') DEFAULT 'draft',
  `posted_by` int(11) NOT NULL,
  `posted_date` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `job_postings`
--

INSERT INTO `job_postings` (`id`, `title`, `description`, `department_id`, `requirements`, `salary_min`, `salary_max`, `location`, `employment_type`, `status`, `posted_by`, `posted_date`, `expiry_date`, `created_at`) VALUES
(1, 'Frontend Developer', 'React ve Vue.js ile frontend geliştirme', 1, 'React, Vue.js, JavaScript, HTML, CSS', 8000.00, 12000.00, 'İstanbul', 'full_time', 'active', 1, '2025-07-25', '2025-08-25', '2025-07-29 10:21:21'),
(2, 'UI/UX Designer', 'Kullanıcı deneyimi ve arayüz tasarımı', 1, 'Figma, Adobe XD, Sketch', 7000.00, 10000.00, 'İstanbul', 'full_time', 'active', 1, '2025-07-26', '2025-08-26', '2025-07-29 10:21:21'),
(3, 'Backend Developer', 'PHP ve Node.js ile backend geliştirme', 1, 'PHP, Node.js, MySQL, MongoDB', 9000.00, 14000.00, 'İstanbul', 'full_time', 'active', 1, '2025-07-24', '2025-08-24', '2025-07-29 10:21:21'),
(4, 'Project Manager', 'Proje yönetimi ve takım liderliği', 1, 'PMP, Agile, Scrum', 12000.00, 18000.00, 'İstanbul', 'full_time', 'active', 1, '2025-07-23', '2025-08-23', '2025-07-29 10:21:21');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `type` enum('application','interview','approval','system') DEFAULT 'system',
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `title`, `message`, `type`, `is_read`, `created_at`) VALUES
(1, 1, 'Yeni başvuru alındı', 'Frontend Developer pozisyonu için Ahmet Yılmaz başvurdu', 'application', 0, '2025-07-29 05:30:00'),
(2, 1, 'Başvuru onaylandı', 'UI/UX Designer pozisyonu için Ayşe Kaya\'nın başvurusu onaylandı', 'approval', 0, '2025-07-29 00:30:00'),
(3, 1, 'Görüşme planlandı', 'Yarın saat 14:00\'da Mehmet Özkan ile görüşme', 'interview', 0, '2025-07-28 15:30:00');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `security_logs`
--

CREATE TABLE `security_logs` (
  `id` int(11) NOT NULL,
  `event_type` varchar(50) NOT NULL,
  `user_id` int(11) DEFAULT 0,
  `user_name` varchar(100) DEFAULT NULL,
  `user_role` varchar(50) DEFAULT NULL,
  `ip_address` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `additional_data` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `security_logs`
--

INSERT INTO `security_logs` (`id`, `event_type`, `user_id`, `user_name`, `user_role`, `ip_address`, `description`, `additional_data`, `created_at`) VALUES
(1, 'DATABASE_UPDATE', 8, 'TalentBridge Admin', 'admin', '::1', 'Admin tarafından veritabanı güncellendi', '{\"queries\":5}', '2025-07-30 10:43:13'),
(2, 'DATABASE_UPDATE', 8, 'TalentBridge Admin', 'admin', '::1', 'Admin tarafından veritabanı güncellendi', '{\"queries\":5}', '2025-07-30 10:43:16'),
(3, 'DATABASE_UPDATE', 8, 'TalentBridge Admin', 'admin', '::1', 'Admin tarafından veritabanı güncellendi', '{\"queries\":5}', '2025-07-30 10:43:17'),
(4, 'DATABASE_UPDATE', 8, 'TalentBridge Admin', 'admin', '::1', 'Admin tarafından veritabanı güncellendi', '{\"queries\":5}', '2025-07-30 10:43:18'),
(5, 'DATABASE_UPDATE', 8, 'TalentBridge Admin', 'admin', '::1', 'Admin tarafından veritabanı güncellendi', '{\"queries\":5}', '2025-07-30 10:43:21');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `system_settings`
--

CREATE TABLE `system_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `hire_date` date DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `phone` varchar(15) DEFAULT NULL,
  `salary` decimal(10,2) DEFAULT NULL,
  `department` varchar(100) DEFAULT 'Belirtilmemiþ',
  `location` varchar(100) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `role` varchar(50) DEFAULT 'employee'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `profile_image`, `created_at`, `updated_at`, `last_login`, `department_id`, `position`, `hire_date`, `status`, `phone`, `salary`, `department`, `location`, `bio`, `start_date`, `role`) VALUES
(1, 'doğukan', 'dogukankomurcu@outlook.com.tr', '$2y$10$afmT/tJyKJTZkuwzOxlTjuF8nr82NGTFKlBMF13ZN0oQWPfhaEyp.', NULL, '2025-07-29 10:04:07', '2025-07-30 10:43:29', '2025-07-30 10:43:29', NULL, NULL, NULL, 'active', NULL, NULL, 'Belirtilmemiþ', NULL, NULL, NULL, 'employee'),
(2, 'Test Employee 14:06:03', 'test1753790763@example.com', '$2y$10$pZq5WezJ7/NB2OVojzWOMe0pqcpDWM.mjD0NMJbgIzh2m06HVaXpy', NULL, '2025-07-29 12:06:03', '2025-07-30 10:28:34', NULL, NULL, 'Test Position', NULL, 'active', '05321234567', 15000.00, 'IT', NULL, NULL, '2025-07-29', 'employee'),
(3, 'Test User 14:07:19', 'test1753790839@test.com', '$2y$10$tk/lYJZdPRC7zffuCnuk/OapbpO8Q55NDJ4v9kfZ9pFv0s/aZHwre', NULL, '2025-07-29 12:07:19', '2025-07-30 10:28:34', NULL, NULL, 'Test Position', NULL, 'active', '05321234567', NULL, 'IT', NULL, NULL, NULL, 'employee'),
(4, 'Final Test User', 'final_test@test.com', '$2y$10$yEOcHsF9a1Qcx9U.Ev2MjuLfoHTYOgRboRIb8.iob374ZgAxvp5ye', NULL, '2025-07-29 12:09:54', '2025-07-30 10:28:34', NULL, NULL, 'Test Developer', NULL, 'active', '05321234567', 25000.00, 'IT', NULL, NULL, '2025-07-29', 'employee'),
(5, 'Mehmet', '20yobi1005@isik.edu.tr', '$2y$10$VTwbITKjBpmz0D.G6.h1R.FjtpG6A8OEOnytY6NZDOpJDnwSzHRu.', NULL, '2025-07-29 12:10:26', '2025-07-30 10:28:34', NULL, NULL, 'çalışan', NULL, 'active', '0345 345 2344', 345534.00, 'İK', NULL, NULL, '2025-07-20', 'employee'),
(6, 'Test Kullanıcı', 'test@example.com', '$2y$10$erj/JpoXjJwVxrf.GnarduMKyPvK1tk5Vn.HS..AZF36hWjV4bQK6', NULL, '2025-07-30 08:08:07', '2025-07-30 10:28:34', NULL, NULL, NULL, NULL, 'active', NULL, NULL, 'Belirtilmemiþ', NULL, NULL, NULL, 'employee'),
(7, 'veli', 'veli@gmail.com', '$2y$10$EHgQy9aG52oGQECw7ftIkueD3.9l29qMVtasnsJXZIStrxujzyFxK', NULL, '2025-07-30 08:21:35', '2025-07-30 10:28:34', NULL, NULL, NULL, NULL, 'active', NULL, NULL, 'Belirtilmemiþ', NULL, NULL, NULL, 'employee'),
(8, 'TalentBridge Admin', 'admin@talentbridge.com', '$2y$10$CQUhJsYD09IkkyeFGVfQvefCKpzpsVt0C9b.HGoZEOzIESq4rFK1m', NULL, '2025-07-30 08:46:23', '2025-07-30 10:28:34', NULL, NULL, 'Sistem Yöneticisi', NULL, 'active', NULL, NULL, 'Bilgi İşlem', NULL, NULL, NULL, 'admin'),
(9, 'Normal Kullanıcı', 'user@talentbridge.com', '$2y$10$2MYkKeL2R9YuI6qI74.14.DUwJTcfNKpV4CZPbGC9MLJY1Jpp913q', NULL, '2025-07-30 08:46:23', '2025-07-30 10:28:34', NULL, NULL, 'Yazılım Geliştirici', NULL, 'active', NULL, NULL, 'Bilgi İşlem', NULL, NULL, NULL, 'employee'),
(10, 'asd', 'te34st@example.com', '$2y$10$fGObZ250WU26Sp1WRo79.OWpNhWC6xktOTe2RNGkrm.rhakjyNIpS', NULL, '2025-07-30 10:08:22', '2025-07-30 10:28:34', NULL, NULL, 'çalışan', NULL, 'active', '5648456454', 14311.00, 'Satış', NULL, NULL, '2025-07-26', 'manager');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `user_settings`
--

CREATE TABLE `user_settings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `email_notifications` tinyint(1) DEFAULT 1,
  `system_notifications` tinyint(1) DEFAULT 1,
  `theme` varchar(50) DEFAULT 'light',
  `language` varchar(10) DEFAULT 'tr',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `user_settings`
--

INSERT INTO `user_settings` (`id`, `user_id`, `email_notifications`, `system_notifications`, `theme`, `language`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 'dark', 'tr', '2025-07-30 10:28:34', '2025-07-30 10:28:34'),
(2, 2, 1, 0, 'light', 'tr', '2025-07-30 10:28:34', '2025-07-30 10:28:34'),
(3, 3, 0, 1, 'light', 'tr', '2025-07-30 10:28:34', '2025-07-30 10:28:34'),
(4, 1, 1, 1, 'dark', 'tr', '2025-07-30 10:28:38', '2025-07-30 10:28:38'),
(5, 2, 1, 0, 'light', 'tr', '2025-07-30 10:28:38', '2025-07-30 10:28:38'),
(6, 3, 0, 1, 'light', 'tr', '2025-07-30 10:28:38', '2025-07-30 10:28:38'),
(7, 1, 1, 1, 'dark', 'tr', '2025-07-30 10:28:49', '2025-07-30 10:28:49'),
(8, 2, 1, 0, 'light', 'tr', '2025-07-30 10:28:49', '2025-07-30 10:28:49'),
(9, 3, 0, 1, 'light', 'tr', '2025-07-30 10:28:49', '2025-07-30 10:28:49'),
(10, 1, 1, 1, 'dark', 'tr', '2025-07-30 10:35:57', '2025-07-30 10:35:57'),
(11, 2, 1, 0, 'light', 'tr', '2025-07-30 10:35:57', '2025-07-30 10:35:57'),
(12, 3, 0, 1, 'light', 'tr', '2025-07-30 10:35:57', '2025-07-30 10:35:57'),
(13, 1, 1, 1, 'dark', 'tr', '2025-07-30 10:37:30', '2025-07-30 10:37:30'),
(14, 2, 1, 0, 'light', 'tr', '2025-07-30 10:37:30', '2025-07-30 10:37:30'),
(15, 3, 0, 1, 'light', 'tr', '2025-07-30 10:37:30', '2025-07-30 10:37:30'),
(16, 1, 1, 1, 'dark', 'tr', '2025-07-30 10:37:34', '2025-07-30 10:37:34'),
(17, 2, 1, 0, 'light', 'tr', '2025-07-30 10:37:34', '2025-07-30 10:37:34'),
(18, 3, 0, 1, 'light', 'tr', '2025-07-30 10:37:34', '2025-07-30 10:37:34'),
(19, 1, 1, 1, 'dark', 'tr', '2025-07-30 10:43:13', '2025-07-30 10:43:13'),
(20, 2, 1, 0, 'light', 'tr', '2025-07-30 10:43:13', '2025-07-30 10:43:13'),
(21, 3, 0, 1, 'light', 'tr', '2025-07-30 10:43:13', '2025-07-30 10:43:13'),
(22, 1, 1, 1, 'dark', 'tr', '2025-07-30 10:43:16', '2025-07-30 10:43:16'),
(23, 2, 1, 0, 'light', 'tr', '2025-07-30 10:43:16', '2025-07-30 10:43:16'),
(24, 3, 0, 1, 'light', 'tr', '2025-07-30 10:43:16', '2025-07-30 10:43:16'),
(25, 1, 1, 1, 'dark', 'tr', '2025-07-30 10:43:17', '2025-07-30 10:43:17'),
(26, 2, 1, 0, 'light', 'tr', '2025-07-30 10:43:17', '2025-07-30 10:43:17'),
(27, 3, 0, 1, 'light', 'tr', '2025-07-30 10:43:17', '2025-07-30 10:43:17'),
(28, 1, 1, 1, 'dark', 'tr', '2025-07-30 10:43:18', '2025-07-30 10:43:18'),
(29, 2, 1, 0, 'light', 'tr', '2025-07-30 10:43:18', '2025-07-30 10:43:18'),
(30, 3, 0, 1, 'light', 'tr', '2025-07-30 10:43:18', '2025-07-30 10:43:18'),
(31, 1, 1, 1, 'dark', 'tr', '2025-07-30 10:43:21', '2025-07-30 10:43:21'),
(32, 2, 1, 0, 'light', 'tr', '2025-07-30 10:43:21', '2025-07-30 10:43:21'),
(33, 3, 0, 1, 'light', 'tr', '2025-07-30 10:43:21', '2025-07-30 10:43:21'),
(34, 8, 1, 1, 'light', 'tr', '2025-07-30 10:43:24', '2025-07-30 10:43:24');

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `job_posting_id` (`job_posting_id`),
  ADD KEY `reviewed_by` (`reviewed_by`);

--
-- Tablo için indeksler `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `manager_id` (`manager_id`);

--
-- Tablo için indeksler `interviews`
--
ALTER TABLE `interviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `application_id` (`application_id`),
  ADD KEY `interviewer_id` (`interviewer_id`);

--
-- Tablo için indeksler `job_postings`
--
ALTER TABLE `job_postings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `department_id` (`department_id`),
  ADD KEY `posted_by` (`posted_by`);

--
-- Tablo için indeksler `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Tablo için indeksler `security_logs`
--
ALTER TABLE `security_logs`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Tablo için indeksler `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Tablo için indeksler `user_settings`
--
ALTER TABLE `user_settings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `applications`
--
ALTER TABLE `applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Tablo için AUTO_INCREMENT değeri `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Tablo için AUTO_INCREMENT değeri `interviews`
--
ALTER TABLE `interviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `job_postings`
--
ALTER TABLE `job_postings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Tablo için AUTO_INCREMENT değeri `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Tablo için AUTO_INCREMENT değeri `security_logs`
--
ALTER TABLE `security_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Tablo için AUTO_INCREMENT değeri `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Tablo için AUTO_INCREMENT değeri `user_settings`
--
ALTER TABLE `user_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- Dökümü yapılmış tablolar için kısıtlamalar
--

--
-- Tablo kısıtlamaları `applications`
--
ALTER TABLE `applications`
  ADD CONSTRAINT `applications_ibfk_1` FOREIGN KEY (`job_posting_id`) REFERENCES `job_postings` (`id`),
  ADD CONSTRAINT `applications_ibfk_2` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`);

--
-- Tablo kısıtlamaları `departments`
--
ALTER TABLE `departments`
  ADD CONSTRAINT `departments_ibfk_1` FOREIGN KEY (`manager_id`) REFERENCES `users` (`id`);

--
-- Tablo kısıtlamaları `interviews`
--
ALTER TABLE `interviews`
  ADD CONSTRAINT `interviews_ibfk_1` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`),
  ADD CONSTRAINT `interviews_ibfk_2` FOREIGN KEY (`interviewer_id`) REFERENCES `users` (`id`);

--
-- Tablo kısıtlamaları `job_postings`
--
ALTER TABLE `job_postings`
  ADD CONSTRAINT `job_postings_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`),
  ADD CONSTRAINT `job_postings_ibfk_2` FOREIGN KEY (`posted_by`) REFERENCES `users` (`id`);

--
-- Tablo kısıtlamaları `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Tablo kısıtlamaları `user_settings`
--
ALTER TABLE `user_settings`
  ADD CONSTRAINT `user_settings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
