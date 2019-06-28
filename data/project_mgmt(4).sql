-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 15, 2017 at 01:20 PM
-- Server version: 5.7.14
-- PHP Version: 7.0.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `project_mgmt`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_types`
--

CREATE TABLE `activity_types` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alias` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `alias`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'UI Development', 'ui_development', 1, 1, '2017-10-16 04:04:15', '2017-10-16 04:04:15'),
(2, 'API Development', 'api_development', 1, 1, '2017-10-16 04:05:00', '2017-10-16 04:05:00'),
(3, 'Mobile Development', 'mobile_development', 1, 1, '2017-10-16 04:05:41', '2017-10-16 04:05:41'),
(14, 'IOS Development', 'IOS Development', 1, 1, '2017-11-15 05:58:37', '2017-11-15 05:58:37'),
(15, 'Web Developement', 'Web Developement', 1, 1, '2017-11-15 05:59:18', '2017-11-15 05:59:42');

-- --------------------------------------------------------

--
-- Table structure for table `categories_technology_mapping`
--

CREATE TABLE `categories_technology_mapping` (
  `id` int(10) UNSIGNED NOT NULL,
  `technology_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories_technology_mapping`
--

INSERT INTO `categories_technology_mapping` (`id`, `technology_id`, `category_id`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(45, 3, 1, 1, 1, '2017-11-15 07:46:15', '2017-11-15 07:46:15'),
(57, 2, 2, 1, 1, '2017-11-15 07:47:06', '2017-11-15 07:47:06'),
(51, 1, 3, 1, 1, '2017-11-15 07:46:48', '2017-11-15 07:46:48'),
(5, 1, 4, 1, 1, '2017-10-18 00:46:47', '2017-10-18 00:46:47'),
(6, 1, 5, 1, 1, '2017-10-18 00:46:48', '2017-10-18 00:46:48'),
(7, 1, 6, 1, 1, '2017-10-18 00:46:49', '2017-10-18 00:46:49'),
(8, 1, 7, 1, 1, '2017-10-18 00:46:50', '2017-10-18 00:46:50'),
(9, 1, 8, 1, 1, '2017-10-18 00:46:51', '2017-10-18 00:46:51'),
(10, 1, 9, 1, 1, '2017-10-18 00:46:52', '2017-10-18 00:46:52'),
(11, 1, 10, 1, 1, '2017-10-18 00:46:52', '2017-10-18 00:46:52'),
(12, 1, 11, 1, 1, '2017-10-18 00:46:53', '2017-10-18 00:46:53'),
(13, 1, 12, 1, 1, '2017-10-18 01:03:22', '2017-10-18 01:03:22'),
(14, 1, 16, 1, 1, '2017-10-23 00:33:19', '2017-10-23 00:33:19'),
(15, 1, 17, 1, 1, '2017-10-23 00:34:37', '2017-10-23 00:34:37'),
(16, 1, 18, 1, 1, '2017-10-23 00:35:49', '2017-10-23 00:35:49'),
(17, 1, 19, 1, 1, '2017-10-23 00:35:50', '2017-10-23 00:35:50'),
(18, 1, 20, 1, 1, '2017-10-23 00:35:51', '2017-10-23 00:35:51'),
(19, 1, 21, 1, 1, '2017-10-24 04:26:36', '2017-10-24 04:26:36'),
(56, 8, 2, 1, 1, '2017-11-15 07:47:06', '2017-11-15 07:47:06'),
(55, 5, 2, 1, 1, '2017-11-15 07:47:06', '2017-11-15 07:47:06'),
(54, 4, 2, 1, 1, '2017-11-15 07:47:06', '2017-11-15 07:47:06'),
(49, 14, 14, 1, 1, '2017-11-15 07:46:34', '2017-11-15 07:46:34'),
(48, 15, 14, 1, 1, '2017-11-15 07:46:34', '2017-11-15 07:46:34'),
(44, 3, 15, 1, 1, '2017-11-15 05:59:42', '2017-11-15 05:59:42'),
(43, 10, 15, 1, 1, '2017-11-15 05:59:42', '2017-11-15 05:59:42'),
(42, 13, 15, 1, 1, '2017-11-15 05:59:42', '2017-11-15 05:59:42'),
(41, 9, 15, 1, 1, '2017-11-15 05:59:42', '2017-11-15 05:59:42'),
(46, 12, 1, 1, 1, '2017-11-15 07:46:15', '2017-11-15 07:46:15'),
(47, 11, 1, 1, 1, '2017-11-15 07:46:15', '2017-11-15 07:46:15'),
(50, 7, 14, 1, 1, '2017-11-15 07:46:34', '2017-11-15 07:46:34'),
(52, 5, 3, 1, 1, '2017-11-15 07:46:48', '2017-11-15 07:46:48'),
(53, 6, 3, 1, 1, '2017-11-15 07:46:48', '2017-11-15 07:46:48');

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `id` int(10) UNSIGNED NOT NULL,
  `project_id` int(11) DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `city` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pincode` int(11) DEFAULT NULL,
  `state` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile` bigint(20) DEFAULT NULL,
  `tel_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `business` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`id`, `project_id`, `name`, `address`, `city`, `pincode`, `state`, `country`, `mobile`, `tel_number`, `email`, `business`, `type`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, NULL, 'aaaaaaaaaaaaa', 'ssssssssssssss', 'Nagpur', 789789, 'Maharashtra', 'India', 7854561235, '7777777777', 'aaaaaaa@vyako.com', 'ssssssss', 'ssssssss', 1, 1, '2017-10-27 05:39:37', '2017-10-27 05:39:37'),
(2, NULL, 'StarClip', 'Butibori', 'Nagpur', 789789, 'Maharashtra', 'India', 8765432566, '7777777777', 'user@starclip.com', 'Hardware manifacturing', 'Clips Hardware', 1, 1, '2017-11-15 05:56:03', '2017-11-15 05:56:03'),
(3, NULL, 'NVDIA', 'Sadar', 'Nagpur', 789789, 'Maharashtra', 'India', 7653890383, '7777777777', 'ssss@nvdia.in', 'IT', 'Computer Hardware', 1, 1, '2017-11-15 05:56:53', '2017-11-15 05:56:53'),
(4, NULL, 'TCS', 'Mihan', 'Nagpur', 789789, 'Maharashtra', 'India', 7854561235, '7777777777', 'ssss@tcs.in', 'IT', 'software', 1, 1, '2017-11-15 05:57:29', '2017-11-15 05:57:29'),
(5, NULL, 'Syslogyx', 'Sonegaon', 'Nagpur', 789789, 'Maharashtra', 'India', 7854561235, '7777777777', 'a1111@syslogyx.com', 'Computer+Embedded', 'Web+IIOT', 1, 1, '2017-11-15 06:08:05', '2017-11-15 06:08:05');

-- --------------------------------------------------------

--
-- Table structure for table `domains`
--

CREATE TABLE `domains` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alias` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_100000_create_password_resets_table', 1),
(2, '2017_05_03_100406_create_test_table', 1),
(3, '2017_05_10_130017_create_technologies_table', 1),
(4, '2017_05_11_063738_create_domains_table', 1),
(5, '2017_05_11_112523_create_project_documents_table', 1),
(6, '2017_05_16_071713_create_clients_table', 1),
(7, '2017_05_17_085111_create_activity_types_table', 1),
(8, '2017_05_17_091141_create_users_table', 1),
(9, '2017_05_18_110612_entrust_setup_tables', 1),
(10, '2017_05_29_093123_create_projects_table', 1),
(11, '2017_06_06_090838_create_tasks_table', 1),
(12, '2017_06_06_093204_create_milestones_table', 1),
(13, '2017_06_06_093807_create_project_resources_table', 1),
(14, '2017_06_06_124133_create_project_technologies_table', 1),
(15, '2017_06_09_064522_create_status_table', 1),
(16, '2017_06_09_065129_create_task_comment_logs_table', 1),
(17, '2017_06_09_065507_create_technical_supports_table', 1),
(18, '2017_06_09_072136_create_project_activity_status_logs_table', 1),
(19, '2017_08_29_104311_create_categories_table', 1),
(20, '2017_08_29_121408_create_categories_technology_mapping_table', 1),
(21, '2017_08_31_055544_create_user_technology_mapping_table', 1),
(22, '2017_09_11_081021_create_resource_matrix_log_table', 1),
(23, '2017_09_20_060914_create_project_poc_table', 1),
(24, '2017_09_21_134357_alter_project_table', 1),
(25, '2017_09_21_135207_alter_task_table', 1),
(26, '2017_09_21_135340_alter_milestone_table', 1),
(27, '2017_09_21_135524_alter_project_resource_table', 1),
(28, '2017_09_21_135720_alter_project_activity_status_log_table', 1),
(29, '2017_09_25_063749_alter_user_table', 1),
(30, '2017_09_26_093903_alter_activity_status_log_table', 1),
(31, '2017_10_03_103911_alter_project_technologies', 1),
(32, '2017_10_18_054431_create_project_category_mapping_table', 1),
(33, '2017_10_26_055911_create_project_category_technology_mapping_table', 1),
(34, '2017_10_30_090114_create_project_category_technology_user_mapping_table', 1),
(35, '2017_11_02_085250_create_project_resource_technology_mapping_table', 1),
(36, '2017_11_07_093646_alter_project_table_add', 1);

-- --------------------------------------------------------

--
-- Table structure for table `milestones`
--

CREATE TABLE `milestones` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `project_id` int(11) DEFAULT NULL,
  `status_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `milestone_index` int(11) DEFAULT NULL,
  `due_date` datetime DEFAULT NULL,
  `start_date` datetime DEFAULT NULL,
  `revised_date` datetime DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permission_role`
--

CREATE TABLE `permission_role` (
  `permission_id` int(10) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` int(10) UNSIGNED NOT NULL,
  `domain_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `status_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `client_id` int(11) DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` datetime DEFAULT NULL,
  `due_date` datetime DEFAULT NULL,
  `revised_date` datetime DEFAULT NULL,
  `duration_in_days` double(5,2) DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `current_milestone_index` int(11) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `company_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` tinyint(4) DEFAULT NULL,
  `duration_in_years` double(5,2) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `domain_id`, `user_id`, `status_id`, `client_id`, `name`, `start_date`, `due_date`, `revised_date`, `duration_in_days`, `description`, `current_milestone_index`, `created_by`, `updated_by`, `created_at`, `updated_at`, `company_name`, `role`, `type`, `duration_in_years`) VALUES
(1, NULL, 9, NULL, NULL, 'DRONE', '2017-02-01 00:00:00', '2017-12-01 00:00:00', NULL, NULL, NULL, NULL, 1, 1, '2017-11-15 06:03:38', '2017-11-15 06:03:38', 'Wipro', 'Team Lead', 1, 0.00),
(2, NULL, 9, 'Ongoing', 5, 'Project Management', '2017-11-14 00:00:00', '2018-01-19 00:00:00', '2018-01-19 00:00:00', 0.00, '<p>Project Management</p>', 0, 1, 1, '2017-11-15 06:09:42', '2017-11-15 06:09:42', 'Syslogic Technologies Pvt. Ltd.', NULL, 2, 0.00),
(3, NULL, 8, 'Onhold', 2, 'Smart Factory', '2017-11-13 00:00:00', '2018-01-11 00:00:00', '2018-01-11 00:00:00', 0.00, '<p>Smart Factory</p>', 0, 1, 1, '2017-11-15 06:11:37', '2017-11-15 06:51:08', 'Syslogic Technologies Pvt. Ltd.', NULL, 2, 0.00),
(4, NULL, 14, 'Onhold', 5, 'HRMS', '2017-11-13 00:00:00', '2018-01-10 00:00:00', '2018-01-10 00:00:00', 0.00, '<p>Human Resource Management System</p>', 0, 1, 1, '2017-11-15 06:14:10', '2017-11-15 06:14:10', 'Syslogic Technologies Pvt. Ltd.', NULL, 2, 0.00),
(5, NULL, 34, 'Closed', 4, 'BPO system', '2017-11-14 00:00:00', '2017-11-23 00:00:00', '2017-11-23 00:00:00', 0.00, '<p>BPO system</p>', 0, 1, 1, '2017-11-15 06:15:57', '2017-11-15 06:15:57', 'Syslogic Technologies Pvt. Ltd.', NULL, 2, 0.00),
(6, NULL, 26, NULL, NULL, 'Time Table Generation System', '2017-01-01 00:00:00', '2017-11-01 00:00:00', NULL, NULL, NULL, NULL, 1, 1, '2017-11-15 07:14:45', '2017-11-15 07:14:45', 'YCCE', 'Project Member', 1, 0.00),
(7, NULL, 11, NULL, NULL, 'Traffic Monitoring system', '2017-01-01 00:00:00', '2018-07-01 00:00:00', NULL, NULL, NULL, NULL, 1, 1, '2017-11-15 07:17:26', '2017-11-15 07:17:26', 'TCS', 'Tester', 1, 0.00),
(8, NULL, 11, NULL, NULL, 'Placement Storage Software', '2017-06-01 00:00:00', '2017-10-01 00:00:00', NULL, NULL, NULL, NULL, 1, 1, '2017-11-15 07:21:26', '2017-11-15 07:21:26', 'YCCE', 'Team Memeber', 1, 0.00),
(9, NULL, 11, NULL, NULL, 'Metro Monitoring System', '2013-06-01 00:00:00', '2016-02-01 00:00:00', NULL, NULL, NULL, NULL, 1, 1, '2017-11-15 07:24:45', '2017-11-15 07:24:45', 'L & T', 'Quality Assurance', 1, 0.00),
(10, NULL, 26, NULL, NULL, 'MAT OS', '2017-02-01 00:00:00', '2017-08-01 00:00:00', NULL, NULL, NULL, NULL, 1, 1, '2017-11-15 07:28:58', '2017-11-15 07:28:58', 'NVADIA', 'Team Member', 1, 0.00),
(11, NULL, 15, 'Ongoing', 4, 'PassPort', '2017-11-13 00:00:00', '2017-12-21 00:00:00', '2017-12-21 00:00:00', 0.00, '<p>PassPort Application store the whole valid data of people.&nbsp;</p>', 0, 1, 1, '2017-11-15 07:33:13', '2017-11-15 07:33:13', 'Syslogic Technologies Pvt. Ltd.', NULL, 2, 0.00),
(12, NULL, 37, 'Onhold', 2, 'Marathi Text Editor', '2017-10-29 00:00:00', '2017-11-24 00:00:00', '2017-11-24 00:00:00', 0.00, '<p>Marathi Text Editor is use to as atext editor for writting in marathi. In this text editor user can easily write in marathi according to the pronounsaction of a word in english</p>', 0, 1, 1, '2017-11-15 07:38:32', '2017-11-15 07:38:32', 'Syslogic Technologies Pvt. Ltd.', NULL, 2, 0.00),
(13, NULL, 16, 'Ongoing', 1, 'Medicine Help System', '2017-11-07 00:00:00', '2017-12-21 00:00:00', '2017-12-21 00:00:00', 0.00, '<p>Medicine Help System</p>', 0, 1, 1, '2017-11-15 07:44:34', '2017-11-15 07:44:34', 'Syslogic Technologies Pvt. Ltd.', NULL, 2, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `project_activity_status_logs`
--

CREATE TABLE `project_activity_status_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `activity_id` int(11) DEFAULT NULL,
  `activity_type_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `spent_hour` double(5,2) DEFAULT NULL,
  `start_date` datetime DEFAULT NULL,
  `due_date` datetime DEFAULT NULL,
  `revised_date` datetime DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `project_activity_status_logs`
--

INSERT INTO `project_activity_status_logs` (`id`, `activity_id`, `activity_type_id`, `status_id`, `spent_hour`, `start_date`, `due_date`, `revised_date`, `created_by`, `updated_by`, `created_at`, `updated_at`, `comment`) VALUES
(1, 1, 'PROJECT', NULL, NULL, NULL, NULL, NULL, 1, 1, '2017-11-15 06:03:38', '2017-11-15 06:03:38', ''),
(2, 2, 'PROJECT', 'Ongoing', NULL, NULL, NULL, NULL, 1, 1, '2017-11-15 06:09:42', '2017-11-15 06:09:42', ''),
(3, 3, 'PROJECT', 'Onhold', NULL, NULL, NULL, NULL, 1, 1, '2017-11-15 06:11:37', '2017-11-15 06:11:37', ''),
(4, 4, 'PROJECT', 'Onhold', NULL, NULL, NULL, NULL, 1, 1, '2017-11-15 06:14:10', '2017-11-15 06:14:10', ''),
(5, 5, 'PROJECT', 'Closed', NULL, NULL, NULL, NULL, 1, 1, '2017-11-15 06:15:57', '2017-11-15 06:15:57', ''),
(6, 6, 'PROJECT', NULL, NULL, NULL, NULL, NULL, 1, 1, '2017-11-15 07:14:45', '2017-11-15 07:14:45', ''),
(7, 7, 'PROJECT', NULL, NULL, NULL, NULL, NULL, 1, 1, '2017-11-15 07:17:26', '2017-11-15 07:17:26', ''),
(8, 8, 'PROJECT', NULL, NULL, NULL, NULL, NULL, 1, 1, '2017-11-15 07:21:26', '2017-11-15 07:21:26', ''),
(9, 9, 'PROJECT', NULL, NULL, NULL, NULL, NULL, 1, 1, '2017-11-15 07:24:45', '2017-11-15 07:24:45', ''),
(10, 10, 'PROJECT', NULL, NULL, NULL, NULL, NULL, 1, 1, '2017-11-15 07:28:58', '2017-11-15 07:28:58', ''),
(11, 11, 'PROJECT', 'Ongoing', NULL, NULL, NULL, NULL, 1, 1, '2017-11-15 07:33:13', '2017-11-15 07:33:13', ''),
(12, 12, 'PROJECT', 'Onhold', NULL, NULL, NULL, NULL, 1, 1, '2017-11-15 07:38:32', '2017-11-15 07:38:32', ''),
(13, 13, 'PROJECT', 'Ongoing', NULL, NULL, NULL, NULL, 1, 1, '2017-11-15 07:44:34', '2017-11-15 07:44:34', '');

-- --------------------------------------------------------

--
-- Table structure for table `project_category_mapping`
--

CREATE TABLE `project_category_mapping` (
  `id` int(10) UNSIGNED NOT NULL,
  `project_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `project_category_mapping`
--

INSERT INTO `project_category_mapping` (`id`, `project_id`, `category_id`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 1, 14, 1, 1, '2017-11-15 06:03:38', '2017-11-15 06:03:38'),
(15, 6, 2, 1, 1, '2017-11-15 07:14:45', '2017-11-15 07:14:45'),
(3, 2, 2, 1, 1, '2017-11-15 06:09:42', '2017-11-15 06:09:42'),
(4, 2, 15, 1, 1, '2017-11-15 06:09:42', '2017-11-15 06:09:42'),
(14, 3, 2, 1, 1, '2017-11-15 07:07:05', '2017-11-15 07:07:05'),
(6, 3, 3, 1, 1, '2017-11-15 06:11:37', '2017-11-15 06:11:37'),
(7, 3, 15, 1, 1, '2017-11-15 06:11:37', '2017-11-15 06:11:37'),
(8, 4, 2, 1, 1, '2017-11-15 06:14:10', '2017-11-15 06:14:10'),
(9, 4, 15, 1, 1, '2017-11-15 06:14:10', '2017-11-15 06:14:10'),
(16, 6, 15, 1, 1, '2017-11-15 07:14:45', '2017-11-15 07:14:45'),
(17, 7, 1, 1, 1, '2017-11-15 07:17:26', '2017-11-15 07:17:26'),
(12, 5, 3, 1, 1, '2017-11-15 06:15:57', '2017-11-15 06:15:57'),
(13, 5, 1, 1, 1, '2017-11-15 06:15:57', '2017-11-15 06:15:57'),
(18, 7, 3, 1, 1, '2017-11-15 07:17:26', '2017-11-15 07:17:26'),
(19, 8, 2, 1, 1, '2017-11-15 07:21:26', '2017-11-15 07:21:26'),
(20, 8, 15, 1, 1, '2017-11-15 07:21:26', '2017-11-15 07:21:26'),
(21, 9, 15, 1, 1, '2017-11-15 07:24:45', '2017-11-15 07:24:45'),
(22, 10, 2, 1, 1, '2017-11-15 07:28:59', '2017-11-15 07:28:59'),
(23, 10, 14, 1, 1, '2017-11-15 07:28:59', '2017-11-15 07:28:59'),
(24, 11, 2, 1, 1, '2017-11-15 07:33:13', '2017-11-15 07:33:13'),
(25, 11, 3, 1, 1, '2017-11-15 07:33:13', '2017-11-15 07:33:13'),
(26, 12, 1, 1, 1, '2017-11-15 07:38:32', '2017-11-15 07:38:32'),
(27, 12, 2, 1, 1, '2017-11-15 07:38:32', '2017-11-15 07:38:32'),
(28, 12, 15, 1, 1, '2017-11-15 07:38:32', '2017-11-15 07:38:32'),
(29, 13, 3, 1, 1, '2017-11-15 07:44:34', '2017-11-15 07:44:34'),
(30, 13, 15, 1, 1, '2017-11-15 07:44:34', '2017-11-15 07:44:34'),
(31, 13, 1, 1, 1, '2017-11-15 07:44:34', '2017-11-15 07:44:34');

-- --------------------------------------------------------

--
-- Table structure for table `project_category_technology_mapping`
--

CREATE TABLE `project_category_technology_mapping` (
  `id` int(10) UNSIGNED NOT NULL,
  `project_category_id` int(11) DEFAULT NULL,
  `technology_id` int(11) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `project_category_technology_mapping`
--

INSERT INTO `project_category_technology_mapping` (`id`, `project_category_id`, `technology_id`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 1, 15, 1, 1, '2017-11-15 06:03:38', '2017-11-15 06:03:38'),
(2, 1, 14, 1, 1, '2017-11-15 06:03:38', '2017-11-15 06:03:38'),
(30, 15, 2, 1, 1, '2017-11-15 07:14:45', '2017-11-15 07:14:45'),
(4, 3, 2, 1, 1, '2017-11-15 06:09:42', '2017-11-15 06:09:42'),
(5, 3, 8, 1, 1, '2017-11-15 06:09:42', '2017-11-15 06:09:42'),
(6, 3, 4, 1, 1, '2017-11-15 06:09:42', '2017-11-15 06:09:42'),
(7, 4, 3, 1, 1, '2017-11-15 06:09:42', '2017-11-15 06:09:42'),
(29, 14, 6, 1, 1, '2017-11-15 07:07:05', '2017-11-15 07:07:05'),
(9, 4, 13, 1, 1, '2017-11-15 06:09:42', '2017-11-15 06:09:42'),
(28, 14, 4, 1, 1, '2017-11-15 07:07:05', '2017-11-15 07:07:05'),
(11, 6, 1, 1, 1, '2017-11-15 06:11:37', '2017-11-15 06:11:37'),
(12, 7, 3, 1, 1, '2017-11-15 06:11:37', '2017-11-15 06:11:37'),
(13, 7, 10, 1, 1, '2017-11-15 06:11:37', '2017-11-15 06:11:37'),
(14, 7, 13, 1, 1, '2017-11-15 06:11:37', '2017-11-15 06:11:37'),
(15, 8, 2, 1, 1, '2017-11-15 06:14:10', '2017-11-15 06:14:10'),
(16, 8, 5, 1, 1, '2017-11-15 06:14:10', '2017-11-15 06:14:10'),
(17, 8, 4, 1, 1, '2017-11-15 06:14:10', '2017-11-15 06:14:10'),
(18, 9, 3, 1, 1, '2017-11-15 06:14:10', '2017-11-15 06:14:10'),
(19, 9, 10, 1, 1, '2017-11-15 06:14:10', '2017-11-15 06:14:10'),
(20, 9, 13, 1, 1, '2017-11-15 06:14:10', '2017-11-15 06:14:10'),
(31, 15, 4, 1, 1, '2017-11-15 07:14:45', '2017-11-15 07:14:45'),
(33, 16, 10, 1, 1, '2017-11-15 07:14:45', '2017-11-15 07:14:45'),
(32, 16, 3, 1, 1, '2017-11-15 07:14:45', '2017-11-15 07:14:45'),
(24, 12, 1, 1, 1, '2017-11-15 06:15:57', '2017-11-15 06:15:57'),
(25, 13, 3, 1, 1, '2017-11-15 06:15:57', '2017-11-15 06:15:57'),
(27, 14, 2, 1, 1, '2017-11-15 07:07:05', '2017-11-15 07:07:05'),
(34, 17, 3, 1, 1, '2017-11-15 07:17:26', '2017-11-15 07:17:26'),
(35, 18, 1, 1, 1, '2017-11-15 07:17:26', '2017-11-15 07:17:26'),
(36, 19, 5, 1, 1, '2017-11-15 07:21:26', '2017-11-15 07:21:26'),
(37, 19, 4, 1, 1, '2017-11-15 07:21:26', '2017-11-15 07:21:26'),
(38, 20, 10, 1, 1, '2017-11-15 07:21:26', '2017-11-15 07:21:26'),
(39, 21, 10, 1, 1, '2017-11-15 07:24:45', '2017-11-15 07:24:45'),
(40, 22, 5, 1, 1, '2017-11-15 07:28:59', '2017-11-15 07:28:59'),
(41, 23, 15, 1, 1, '2017-11-15 07:28:59', '2017-11-15 07:28:59'),
(42, 23, 14, 1, 1, '2017-11-15 07:28:59', '2017-11-15 07:28:59'),
(43, 24, 8, 1, 1, '2017-11-15 07:33:13', '2017-11-15 07:33:13'),
(44, 24, 4, 1, 1, '2017-11-15 07:33:13', '2017-11-15 07:33:13'),
(45, 24, 6, 1, 1, '2017-11-15 07:33:13', '2017-11-15 07:33:13'),
(46, 25, 1, 1, 1, '2017-11-15 07:33:13', '2017-11-15 07:33:13'),
(47, 26, 3, 1, 1, '2017-11-15 07:38:32', '2017-11-15 07:38:32'),
(48, 27, 8, 1, 1, '2017-11-15 07:38:32', '2017-11-15 07:38:32'),
(49, 27, 5, 1, 1, '2017-11-15 07:38:32', '2017-11-15 07:38:32'),
(50, 28, 10, 1, 1, '2017-11-15 07:38:32', '2017-11-15 07:38:32'),
(51, 28, 13, 1, 1, '2017-11-15 07:38:32', '2017-11-15 07:38:32'),
(52, 29, 1, 1, 1, '2017-11-15 07:44:34', '2017-11-15 07:44:34'),
(53, 30, 3, 1, 1, '2017-11-15 07:44:34', '2017-11-15 07:44:34'),
(54, 30, 9, 1, 1, '2017-11-15 07:44:34', '2017-11-15 07:44:34'),
(55, 31, 3, 1, 1, '2017-11-15 07:44:34', '2017-11-15 07:44:34');

-- --------------------------------------------------------

--
-- Table structure for table `project_documents`
--

CREATE TABLE `project_documents` (
  `id` int(10) UNSIGNED NOT NULL,
  `project_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` int(11) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `project_poc`
--

CREATE TABLE `project_poc` (
  `id` int(10) UNSIGNED NOT NULL,
  `project_id` int(11) DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mobile_primary` bigint(20) DEFAULT NULL,
  `mobile_secondary` bigint(20) DEFAULT NULL,
  `email_personal` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_official` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `project_resources`
--

CREATE TABLE `project_resources` (
  `id` int(10) UNSIGNED NOT NULL,
  `project_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `domain_id` int(11) DEFAULT NULL,
  `status_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_date` datetime DEFAULT NULL,
  `due_date` datetime DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `project_resources`
--

INSERT INTO `project_resources` (`id`, `project_id`, `user_id`, `domain_id`, `status_id`, `role`, `start_date`, `due_date`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 1, 9, 14, NULL, 'Team Lead', '2017-02-01 00:00:00', '2017-12-01 00:00:00', 1, 1, '2017-11-15 06:03:38', '2017-11-15 06:03:38'),
(2, 2, 9, NULL, 'Ongoing', 'Manager', '2017-11-14 00:00:00', '2018-01-19 00:00:00', 1, 1, '2017-11-15 06:09:42', '2017-11-15 06:09:42'),
(19, 2, 34, 15, 'Ongoing', 'Team Member', '2017-11-14 00:00:00', '2018-01-19 00:00:00', 1, 1, '2017-11-15 07:06:08', '2017-11-15 07:06:08'),
(4, 4, 14, NULL, 'Onhold', 'Manager', '2017-11-13 00:00:00', '2018-01-10 00:00:00', 1, 1, '2017-11-15 06:14:10', '2017-11-15 06:14:10'),
(5, 5, 34, NULL, 'Closed', 'Manager', '2017-11-14 00:00:00', '2017-11-23 00:00:00', 1, 1, '2017-11-15 06:15:57', '2017-11-15 06:15:57'),
(6, 5, 16, 3, 'Closed', 'Team Member', '2017-11-14 00:00:00', '2017-11-23 00:00:00', 1, 1, '2017-11-15 06:47:20', '2017-11-15 06:47:20'),
(15, 4, 26, 2, 'Onhold', 'Team Member', '2017-11-13 00:00:00', '2018-01-10 00:00:00', 1, 1, '2017-11-15 07:04:43', '2017-11-15 07:04:43'),
(8, 5, 26, 1, 'Closed', 'Team Member', '2017-11-14 00:00:00', '2017-11-23 00:00:00', 1, 1, '2017-11-15 06:47:41', '2017-11-15 06:47:41'),
(18, 2, 34, 2, 'Ongoing', 'Team Member', '2017-11-14 00:00:00', '2018-01-19 00:00:00', 1, 1, '2017-11-15 07:05:57', '2017-11-15 07:05:57'),
(17, 4, 34, 15, 'Onhold', 'Team Member', '2017-11-13 00:00:00', '2018-01-10 00:00:00', 1, 1, '2017-11-15 07:05:01', '2017-11-15 07:05:01'),
(16, 4, 34, 2, 'Onhold', 'Team Member', '2017-11-13 00:00:00', '2018-01-10 00:00:00', 1, 1, '2017-11-15 07:04:44', '2017-11-15 07:04:44'),
(12, 3, 34, 15, 'Onhold', 'Team Member', '2017-11-13 00:00:00', '2018-01-11 00:00:00', 1, 1, '2017-11-15 06:49:47', '2017-11-15 06:49:47'),
(13, 3, 11, 3, 'Onhold', 'Team Member', '2017-11-13 00:00:00', '2018-01-11 00:00:00', 1, 1, '2017-11-15 06:50:02', '2017-11-15 06:50:02'),
(21, 3, 26, 2, 'Onhold', 'Team Member', '2017-11-13 00:00:00', '2018-01-11 00:00:00', 1, 1, '2017-11-15 07:07:43', '2017-11-15 07:07:43'),
(20, 2, 14, 2, 'Ongoing', 'Team Member', '2017-11-14 00:00:00', '2018-01-19 00:00:00', 1, 1, '2017-11-15 07:06:20', '2017-11-15 07:06:20'),
(22, 6, 26, 2, NULL, 'Project Member', '2017-01-01 00:00:00', '2017-11-01 00:00:00', 1, 1, '2017-11-15 07:14:45', '2017-11-15 07:14:45'),
(23, 6, 26, 15, NULL, 'Project Member', '2017-01-01 00:00:00', '2017-11-01 00:00:00', 1, 1, '2017-11-15 07:14:45', '2017-11-15 07:14:45'),
(24, 7, 11, 1, NULL, 'Tester', '2017-01-01 00:00:00', '2018-07-01 00:00:00', 1, 1, '2017-11-15 07:17:26', '2017-11-15 07:17:26'),
(25, 7, 11, 3, NULL, 'Tester', '2017-01-01 00:00:00', '2018-07-01 00:00:00', 1, 1, '2017-11-15 07:17:26', '2017-11-15 07:17:26'),
(26, 8, 11, 2, NULL, 'Team Memeber', '2017-06-01 00:00:00', '2017-10-01 00:00:00', 1, 1, '2017-11-15 07:21:26', '2017-11-15 07:21:26'),
(27, 8, 11, 15, NULL, 'Team Memeber', '2017-06-01 00:00:00', '2017-10-01 00:00:00', 1, 1, '2017-11-15 07:21:26', '2017-11-15 07:21:26'),
(28, 9, 11, 15, NULL, 'Quality Assurance', '2013-06-01 00:00:00', '2016-02-01 00:00:00', 1, 1, '2017-11-15 07:24:45', '2017-11-15 07:24:45'),
(29, 10, 26, 2, NULL, 'Team Member', '2017-02-01 00:00:00', '2017-08-01 00:00:00', 1, 1, '2017-11-15 07:28:59', '2017-11-15 07:28:59'),
(30, 10, 26, 14, NULL, 'Team Member', '2017-02-01 00:00:00', '2017-08-01 00:00:00', 1, 1, '2017-11-15 07:28:59', '2017-11-15 07:28:59'),
(31, 11, 15, NULL, 'Ongoing', 'Manager', '2017-11-13 00:00:00', '2017-12-21 00:00:00', 1, 1, '2017-11-15 07:33:13', '2017-11-15 07:33:13'),
(32, 11, 15, 3, 'Ongoing', 'Team Member', '2017-11-13 00:00:00', '2017-12-21 00:00:00', 1, 1, '2017-11-15 07:33:38', '2017-11-15 07:33:38'),
(33, 11, 16, 3, 'Ongoing', 'Team Member', '2017-11-13 00:00:00', '2017-12-21 00:00:00', 1, 1, '2017-11-15 07:33:38', '2017-11-15 07:33:38'),
(34, 11, 26, 2, 'Ongoing', 'Team Member', '2017-11-13 00:00:00', '2017-12-21 00:00:00', 1, 1, '2017-11-15 07:33:51', '2017-11-15 07:33:51'),
(35, 11, 34, 2, 'Ongoing', 'Team Member', '2017-11-13 00:00:00', '2017-12-21 00:00:00', 1, 1, '2017-11-15 07:33:51', '2017-11-15 07:33:51'),
(36, 12, 37, NULL, 'Onhold', 'Manager', '2017-10-29 00:00:00', '2017-11-24 00:00:00', 1, 1, '2017-11-15 07:38:32', '2017-11-15 07:38:32'),
(37, 12, 15, 2, 'Onhold', 'Team Member', '2017-10-29 00:00:00', '2017-11-24 00:00:00', 1, 1, '2017-11-15 07:39:02', '2017-11-15 07:39:02'),
(38, 12, 14, 1, 'Onhold', 'Team Member', '2017-10-29 00:00:00', '2017-11-24 00:00:00', 1, 1, '2017-11-15 07:39:14', '2017-11-15 07:39:14'),
(39, 12, 26, 15, 'Onhold', 'Team Member', '2017-10-29 00:00:00', '2017-11-24 00:00:00', 1, 1, '2017-11-15 07:39:37', '2017-11-15 07:39:37'),
(40, 12, 34, 15, 'Onhold', 'Team Member', '2017-10-29 00:00:00', '2017-11-24 00:00:00', 1, 1, '2017-11-15 07:39:37', '2017-11-15 07:39:37'),
(41, 12, 26, 1, 'Onhold', 'Team Member', '2017-10-29 00:00:00', '2017-11-24 00:00:00', 1, 1, '2017-11-15 07:39:51', '2017-11-15 07:39:51'),
(42, 12, 34, 1, 'Onhold', 'Team Member', '2017-10-29 00:00:00', '2017-11-24 00:00:00', 1, 1, '2017-11-15 07:39:51', '2017-11-15 07:39:51'),
(43, 12, 34, 2, 'Onhold', 'Team Member', '2017-10-29 00:00:00', '2017-11-24 00:00:00', 1, 1, '2017-11-15 07:40:55', '2017-11-15 07:40:55'),
(44, 12, 37, 2, 'Onhold', 'Team Member', '2017-10-29 00:00:00', '2017-11-24 00:00:00', 1, 1, '2017-11-15 07:41:25', '2017-11-15 07:41:25'),
(45, 13, 16, NULL, 'Ongoing', 'Manager', '2017-11-07 00:00:00', '2017-12-21 00:00:00', 1, 1, '2017-11-15 07:44:34', '2017-11-15 07:44:34'),
(46, 13, 26, 1, 'Ongoing', 'Team Member', '2017-11-07 00:00:00', '2017-12-21 00:00:00', 1, 1, '2017-11-15 07:44:57', '2017-11-15 07:44:57'),
(47, 13, 39, 1, 'Ongoing', 'Team Member', '2017-11-07 00:00:00', '2017-12-21 00:00:00', 1, 1, '2017-11-15 07:44:57', '2017-11-15 07:44:57'),
(48, 13, 34, 15, 'Ongoing', 'Team Member', '2017-11-07 00:00:00', '2017-12-21 00:00:00', 1, 1, '2017-11-15 07:45:07', '2017-11-15 07:45:07'),
(49, 13, 15, 3, 'Ongoing', 'Team Member', '2017-11-07 00:00:00', '2017-12-21 00:00:00', 1, 1, '2017-11-15 07:45:26', '2017-11-15 07:45:26'),
(50, 13, 16, 3, 'Ongoing', 'Team Member', '2017-11-07 00:00:00', '2017-12-21 00:00:00', 1, 1, '2017-11-15 07:45:26', '2017-11-15 07:45:26'),
(51, 13, 26, 15, 'Ongoing', 'Team Member', '2017-11-07 00:00:00', '2017-12-21 00:00:00', 1, 1, '2017-11-15 07:45:39', '2017-11-15 07:45:39');

-- --------------------------------------------------------

--
-- Table structure for table `project_resource_technology_mapping`
--

CREATE TABLE `project_resource_technology_mapping` (
  `id` int(10) UNSIGNED NOT NULL,
  `project_resource_id` int(11) DEFAULT NULL,
  `technology_id` int(11) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `project_resource_technology_mapping`
--

INSERT INTO `project_resource_technology_mapping` (`id`, `project_resource_id`, `technology_id`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 1, 15, 1, 1, '2017-11-15 06:03:38', '2017-11-15 06:03:38'),
(2, 1, 14, 1, 1, '2017-11-15 06:03:38', '2017-11-15 06:03:38'),
(3, 6, 1, 1, 1, '2017-11-15 06:47:20', '2017-11-15 06:47:20'),
(14, 15, 2, 1, 1, '2017-11-15 07:04:43', '2017-11-15 07:04:43'),
(5, 8, 3, 1, 1, '2017-11-15 06:47:41', '2017-11-15 06:47:41'),
(17, 16, 4, 1, 1, '2017-11-15 07:04:44', '2017-11-15 07:04:44'),
(16, 16, 2, 1, 1, '2017-11-15 07:04:44', '2017-11-15 07:04:44'),
(15, 15, 4, 1, 1, '2017-11-15 07:04:43', '2017-11-15 07:04:43'),
(9, 12, 10, 1, 1, '2017-11-15 06:49:47', '2017-11-15 06:49:47'),
(10, 12, 13, 1, 1, '2017-11-15 06:49:47', '2017-11-15 06:49:47'),
(11, 13, 1, 1, 1, '2017-11-15 06:50:02', '2017-11-15 06:50:02'),
(23, 21, 4, 1, 1, '2017-11-15 07:07:43', '2017-11-15 07:07:43'),
(13, 12, 3, 1, 1, '2017-11-15 06:50:43', '2017-11-15 06:50:43'),
(18, 17, 3, 1, 1, '2017-11-15 07:05:01', '2017-11-15 07:05:01'),
(19, 17, 10, 1, 1, '2017-11-15 07:05:01', '2017-11-15 07:05:01'),
(20, 18, 8, 1, 1, '2017-11-15 07:05:57', '2017-11-15 07:05:57'),
(21, 19, 13, 1, 1, '2017-11-15 07:06:08', '2017-11-15 07:06:08'),
(22, 20, 2, 1, 1, '2017-11-15 07:06:20', '2017-11-15 07:06:20'),
(24, 22, 2, 1, 1, '2017-11-15 07:14:45', '2017-11-15 07:14:45'),
(25, 22, 4, 1, 1, '2017-11-15 07:14:45', '2017-11-15 07:14:45'),
(26, 23, 3, 1, 1, '2017-11-15 07:14:45', '2017-11-15 07:14:45'),
(27, 23, 10, 1, 1, '2017-11-15 07:14:45', '2017-11-15 07:14:45'),
(28, 24, 3, 1, 1, '2017-11-15 07:17:26', '2017-11-15 07:17:26'),
(29, 25, 1, 1, 1, '2017-11-15 07:17:26', '2017-11-15 07:17:26'),
(30, 26, 5, 1, 1, '2017-11-15 07:21:26', '2017-11-15 07:21:26'),
(31, 26, 4, 1, 1, '2017-11-15 07:21:26', '2017-11-15 07:21:26'),
(32, 27, 10, 1, 1, '2017-11-15 07:21:26', '2017-11-15 07:21:26'),
(33, 28, 10, 1, 1, '2017-11-15 07:24:45', '2017-11-15 07:24:45'),
(34, 29, 5, 1, 1, '2017-11-15 07:28:59', '2017-11-15 07:28:59'),
(35, 30, 15, 1, 1, '2017-11-15 07:28:59', '2017-11-15 07:28:59'),
(36, 30, 14, 1, 1, '2017-11-15 07:28:59', '2017-11-15 07:28:59'),
(37, 32, 1, 1, 1, '2017-11-15 07:33:38', '2017-11-15 07:33:38'),
(38, 33, 1, 1, 1, '2017-11-15 07:33:38', '2017-11-15 07:33:38'),
(39, 34, 4, 1, 1, '2017-11-15 07:33:51', '2017-11-15 07:33:51'),
(40, 35, 4, 1, 1, '2017-11-15 07:33:51', '2017-11-15 07:33:51'),
(41, 35, 6, 1, 1, '2017-11-15 07:34:00', '2017-11-15 07:34:00'),
(42, 37, 5, 1, 1, '2017-11-15 07:39:02', '2017-11-15 07:39:02'),
(43, 38, 3, 1, 1, '2017-11-15 07:39:14', '2017-11-15 07:39:14'),
(44, 39, 10, 1, 1, '2017-11-15 07:39:37', '2017-11-15 07:39:37'),
(45, 40, 10, 1, 1, '2017-11-15 07:39:37', '2017-11-15 07:39:37'),
(46, 41, 3, 1, 1, '2017-11-15 07:39:51', '2017-11-15 07:39:51'),
(47, 42, 3, 1, 1, '2017-11-15 07:39:51', '2017-11-15 07:39:51'),
(48, 43, 8, 1, 1, '2017-11-15 07:40:55', '2017-11-15 07:40:55'),
(49, 43, 5, 1, 1, '2017-11-15 07:41:25', '2017-11-15 07:41:25'),
(50, 44, 5, 1, 1, '2017-11-15 07:41:25', '2017-11-15 07:41:25'),
(51, 46, 3, 1, 1, '2017-11-15 07:44:57', '2017-11-15 07:44:57'),
(52, 47, 3, 1, 1, '2017-11-15 07:44:57', '2017-11-15 07:44:57'),
(53, 48, 9, 1, 1, '2017-11-15 07:45:07', '2017-11-15 07:45:07'),
(54, 49, 1, 1, 1, '2017-11-15 07:45:26', '2017-11-15 07:45:26'),
(55, 50, 1, 1, 1, '2017-11-15 07:45:26', '2017-11-15 07:45:26'),
(56, 51, 3, 1, 1, '2017-11-15 07:45:39', '2017-11-15 07:45:39'),
(57, 48, 3, 1, 1, '2017-11-15 07:45:39', '2017-11-15 07:45:39');

-- --------------------------------------------------------

--
-- Table structure for table `project_technologies`
--

CREATE TABLE `project_technologies` (
  `id` int(10) UNSIGNED NOT NULL,
  `project_id` int(11) DEFAULT NULL,
  `technology_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `resource_matrix_log`
--

CREATE TABLE `resource_matrix_log` (
  `id` int(10) UNSIGNED NOT NULL,
  `project_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `remark` text COLLATE utf8mb4_unicode_ci,
  `start_date` datetime DEFAULT NULL,
  `due_date` datetime DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `role_user`
--

CREATE TABLE `role_user` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `status`
--

CREATE TABLE `status` (
  `id` int(10) UNSIGNED NOT NULL,
  `activity_type_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `status`
--

INSERT INTO `status` (`id`, `activity_type_id`, `name`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'PROJECT', 'Ongoing', 1, 1, '2017-09-22 03:43:39', '2017-09-22 03:43:39'),
(2, 'PROJECT', 'Onhold', 1, 1, '2017-09-22 03:43:48', '2017-09-22 03:43:48'),
(3, 'PROJECT', 'Closed', 1, 1, '2017-09-22 03:43:55', '2017-09-22 03:43:55'),
(4, 'PROJECT', 'Pending', 1, 1, '2017-09-22 03:44:04', '2017-09-26 05:46:27');

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` int(10) UNSIGNED NOT NULL,
  `project_resource_id` int(11) DEFAULT NULL,
  `milestone_id` int(11) DEFAULT NULL,
  `status_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `technical_support_id` int(11) DEFAULT NULL,
  `start_date` datetime DEFAULT NULL,
  `completion_date` datetime DEFAULT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `parent_id` int(11) DEFAULT NULL,
  `estimated_hours` double(5,2) DEFAULT NULL,
  `comment` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `original_task_id` int(11) NOT NULL,
  `priority_id` int(11) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `task_comment_logs`
--

CREATE TABLE `task_comment_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `task_id` int(11) DEFAULT NULL,
  `comment` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `technical_supports`
--

CREATE TABLE `technical_supports` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `task_id` int(11) DEFAULT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `technologies`
--

CREATE TABLE `technologies` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `alias` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `technologies`
--

INSERT INTO `technologies` (`id`, `name`, `parent_id`, `alias`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'Android', NULL, 'android', 1, 1, '2017-10-16 03:59:29', '2017-10-16 03:59:29'),
(2, 'PHP', NULL, 'php', 1, 1, '2017-10-16 03:59:42', '2017-10-16 03:59:42'),
(3, 'Angular', NULL, 'angular', 1, 1, '2017-10-16 04:00:13', '2017-10-16 04:00:13'),
(4, 'Cakephp', 2, 'cakephp', 1, 1, '2017-10-16 04:00:45', '2017-10-16 04:00:45'),
(5, 'Java', NULL, 'Java', 1, 1, '2017-10-31 04:41:26', '2017-10-31 04:41:26'),
(6, 'Spring', 5, 'Spring', 1, 1, '2017-10-31 04:41:37', '2017-10-31 04:41:37'),
(7, 'ios', NULL, 'ios', 1, 1, '2017-10-31 04:42:05', '2017-10-31 04:42:05'),
(8, 'Laravel', 2, 'Laravel', 1, 1, '2017-11-04 00:55:04', '2017-11-04 00:55:04'),
(9, 'React Js', NULL, 'React Js', 1, 1, '2017-11-04 00:55:21', '2017-11-04 00:55:21'),
(10, 'Javascript', NULL, 'Javascript', 1, 1, '2017-11-04 00:55:36', '2017-11-04 00:55:36'),
(11, 'HTML', NULL, 'HTML', 1, 1, '2017-11-04 00:55:51', '2017-11-04 00:55:51'),
(12, 'CSS', NULL, 'CSS', 1, 1, '2017-11-04 00:55:58', '2017-11-04 00:55:58'),
(13, 'Jquery', NULL, 'Jquery', 1, 1, '2017-11-04 00:57:08', '2017-11-04 00:57:08'),
(14, 'C++', NULL, 'C++', 1, 1, '2017-11-15 05:57:52', '2017-11-15 05:57:52'),
(15, 'C Language', 7, 'C Language', 1, 1, '2017-11-15 05:58:15', '2017-11-15 05:58:15');

-- --------------------------------------------------------

--
-- Table structure for table `test`
--

CREATE TABLE `test` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `surname` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mobile` mediumint(9) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `email_internal` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_external` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `department` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `designation` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `total_experience` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `gender`, `status`, `email_internal`, `email_external`, `department`, `designation`, `avatar`, `remember_token`, `created_at`, `updated_at`, `user_id`, `total_experience`) VALUES
(2, 'Pooja Pande', 'poojashree.pande@syslogic.in', '$2y$10$n1kcFnNddp1RXf4qR8bQU.KFPoRMH3lvA47M6Bm/WDfLBJIZNfdHK', 'Female', NULL, 'poojashree.pande@syslogic.in', 'pooja@syslogyx.com', 'Administration', 'Software Engineer', 'default_profile.png', NULL, NULL, NULL, 1, '4.10'),
(1, 'Super Admin', 'admin@syslogyx.com', '$2y$10$YDgsdBySTuOdeBikEtuxIejVAnq5tA2gSCuGN4zj1r7si2VD3A6bW', 'male', 1, 'admin@syslogyx.in', 'admin@syslogyx.com', 'Admin', 'Administrator', 'image.png', NULL, '2017-09-27 04:07:32', '2017-09-27 04:07:32', 0, '1'),
(3, 'Richa SIngh', 'richa@vyako.com', '$2y$10$XGIVHVb4Rcwo3xHz9xH68uzQSp.vZxzcBWV4gd6WHq3BKxtHZJOoO', 'Female', NULL, 'richa.singh@syslogic.in', 'richa@vyako.com', 'Administration', 'Asst. Manager - HR', 'default_profile.png', NULL, NULL, NULL, 157, '8.4'),
(4, 'Dhirendra Khatua', 'dhirendra.khatua@syslogic.in', '$2y$10$i7og/UiQ1FqiG0KFtO/8JOLyHuaOLMtx/Dcz2p.BiuJm/e4mhKlv.', 'Male', NULL, 'dhirendra.khatua@syslogic.in', 'dhirendra.khatua@syslogyx.com', 'Embedded', 'Software Engineer', '1494483241_319061.png', NULL, NULL, NULL, 191, '11.0'),
(5, 'Amar', 'amar.talokar@syslogic.in', '$2y$10$Um9IaB8QZ1Ij.Bl78OmjxePFUQDk90861SHkxVRp8IdaRH0L6H5qC', 'Male', NULL, 'amar.talokar@syslogic.in', 'amar.talokar@vyako.com', 'System Administrator', 'Software Engineer', '1494419440_199462.png', NULL, NULL, NULL, 192, '10.0'),
(6, 'Monika SIngh', 'monika@vyako.com', '$2y$10$0rhRGFAYOa7IwTWKVzy8ZetXyU0k/TO2NwG1fmZFFPT7t2/nItn9G', 'Female', NULL, 'monika.singh@syslogic.in', 'monika@vyako.com', 'Administration', 'Asst. Manager - Accounts', '1494483884_985870.png', NULL, NULL, NULL, 193, '8'),
(7, 'Prashant Ashok  Padol', 'prashant.padol@syslogic.in', '$2y$10$TpW29tM/sh/5cQqDYxlZ4O3fAHS1VMq8hrarbkwHrIzjJzpWgyPFG', 'Male', NULL, 'prashant.padol@syslogic.in', 'prashant.padol@vyako.com', 'UI/UX', 'Sr. Graphic Designer', '1494484648_68298.png', NULL, NULL, NULL, 194, '10.8'),
(8, 'Ranjit Singh', 'ranjit@vyako.com', '$2y$10$coObvB77o8wvJS1tL8C9e.z6EEz8HhIw6qVqD66IiKorR1AAHncLO', 'Male', NULL, 'ranjit.singh@syslogic.in', 'ranjit@vyako.com', 'Management', 'COO', '1494494047_342132.png', NULL, NULL, NULL, 195, '16.0'),
(9, 'Ajit Singh', 'Ajit@vyako.com', '$2y$10$odPU95OxO/mMdmIesiIwXOxB651DGX6trxVMoS.UEkETIgPGQvD/6', 'Male', NULL, 'ajit.singh@syslogic.in', 'ajit@vyako.com', 'Management', 'CEO', '1494496701_97198.png', NULL, NULL, NULL, 196, '14'),
(10, 'Suchintra Singh', 'suchintra.singh@vsyslogic.in', '$2y$10$jBVs5uKX4HLiRg1LUXj7NOFSJ6xU8jPO4b.r8pk7qzbl0jU2BbdM.', 'Male', NULL, 'suchintra.singh@vsyslogic.in', 'suchintra.singh@vyako.com', 'Quality Assurance', 'Sr Quality Engineer', '1494497677_172180.png', NULL, NULL, NULL, 197, '2.11'),
(11, 'Bhushan Vijayrao Chumbhale', 'bhushan.chumbhale@syslogic.in', '$2y$10$neOAhgV2PAW3gKzAMrwk0.dx6wLmpbWsIltC2U7EcABt1qW3/x5GS', 'Male', NULL, 'bhushan.chumbhale@syslogic.in', 'bhushan.chumbhale@vyako.com', 'Quality Assurance', 'Software Engineer', '1494499383_152465.png', NULL, NULL, NULL, 198, '2.7'),
(12, 'Siddharth Yadav', 'siddharth.yadav@vyako.com', '$2y$10$FLv9cy9T3qhIrZAWnwJrX.i6Ac3CwtTVJOFYBmGWXuJ3GYTbt1k8C', 'Male', NULL, 'siddharth.yadav@syslogic.in', 'siddharth.yadav@vyako.com', 'Development', 'Sr Software Engineer', '1494503521_834991.png', NULL, NULL, NULL, 199, '1.9'),
(13, 'Yogesh Shamlal Jaiswal', 'yogesh.jaiswal@syslogic.in', '$2y$10$6JRdxTi91avqfEVXdSYwd.u.6vFIOIcGfPOH2IotH0Rl17cDaiAPS', 'Male', NULL, 'yogesh.jaiswal@syslogic.in', 'yogesh.j@vyako.com', 'Frontend Development', 'Software Engineer', 'default_profile.png', NULL, NULL, NULL, 200, '2.1'),
(14, 'Chandrashekhar Shrawan Sontakke', 'chandrashekhar.sontakke@syslogic.in', '$2y$10$YjF.j2/Hf3Fl8VPhtSyTZem.68VbtqIg7pwI7QGophCpTvECzLJyu', 'Male', NULL, 'chandrashekhar.sontakke@syslogic.in', 'chandrashekhar.sontakke@vyako.com', 'Development', 'Software Engineer', '1494582640_286376.png', NULL, NULL, NULL, 201, '4.0'),
(15, 'Kauhsik Suraj Patil', 'kaushik.patil@syslogic.in', '$2y$10$2qufiLwMEjo3/vc3CFR9bOQKfBXP9DyFCv/Cosm8Cb4GAR4y76amu', 'Male', NULL, 'kaushik.patil@syslogic.in', 'kaushik.patil@vyako.com', 'Android Development', 'Software Engineer', '1494583563_265594.png', NULL, NULL, NULL, 202, '1.5'),
(16, 'Nikhil Moharkar', 'nikhil.moharkar@syslogic.com', '$2y$10$spFA90DdyfcN0pkLCFUKp.k4qc5bRlpSCqOw5yHSVZwONIdK9skM.', 'Male', NULL, 'nikhil.moharkar@syslogic.com', 'nikhil.moharkar@vyako.com', 'Development', 'Software Engineer', 'default_profile.png', NULL, NULL, NULL, 203, '4.5'),
(17, 'Vickram Sharma', 'vickram.sharma@vyako.com', '$2y$10$2wDhWtTXwtyDqxs4q3T9QOFHIDCyU.xvYlL4p87tzB5PGbdi0aXzK', 'Male', NULL, 'vickram.sharma@vyako.com', 'vickram.sharma@vyako.com', 'Development', 'Software Engineer', '1494931294_471923.png', NULL, NULL, NULL, 204, '2.0'),
(18, 'Neha Photani', 'neha.p@vyako.com', '$2y$10$2ZbZ9Y5J3KFY/Q7KAfoHLu4SbSYe6nm4eJ5a2jtNjqYTCUUX2yGji', 'Female', NULL, 'neha.p@vyako.com', 'neha.p@syslogyx.com', 'Business Development', 'Software Engineer', '1494934413_892517.png', NULL, NULL, NULL, 205, '1.2'),
(19, 'Namrata Khobragade', 'namrata.khobragade@syslogic.in', '$2y$10$FUKB/zeRO4UQEH00/wYawuTPsEjA9g1HIt78Fkgz413JzwUJd84ky', 'Female', NULL, 'namrata.khobragade@syslogic.in', 'namrata.khobragade@vyako.com', 'Development', 'Software Engineer', '1495092527_740264.png', NULL, NULL, NULL, 206, '1.8'),
(20, 'Nikhil Vijay Muley', 'nikhil.muley@syslogic.in', '$2y$10$JdZUYW5xNp9FlWuhxbXkI.DahFQPoJRwlqeaU5J5g7JuWflXWLNDG', 'Male', NULL, 'nikhil.muley@syslogic.in', 'nikhil@syslogyx.com', 'Embedded', 'Software Engineer', '1495101005_690887.png', NULL, NULL, NULL, 207, '5.6'),
(21, 'Vaibhav Krishnarao Deshpande', 'vaibhav.deshpande@syslogic.in', '$2y$10$sCtmGh4i1iLjtVV.TpnW3ew6/vrb8IdqVcX9/Ca00R3AvlVDY2CeS', 'Male', NULL, 'vaibhav.deshpande@syslogic.in', 'vaibhav@syslogyx.com', 'Embedded', 'Software Engineer', '1495105084_521545.png', NULL, NULL, NULL, 208, '12.0'),
(22, 'Umesh Singh', 'umesh.singh@syslogic.in', '$2y$10$j3O1CkYp/dX0juMs2DQvku8cBqHiI0NSBZpyCsYDUNV2JwAjAXS7u', 'Male', NULL, 'umesh.singh@syslogic.in', 'umesh.singh@syslogyx.com', 'Embedded', 'Sr Software Engineer', '1495108554_920471.png', NULL, NULL, NULL, 209, '6.1'),
(23, 'Jiteshkumar Onkarlal Patle', 'jitesh.patle@syslogic.in', '$2y$10$KaRQwcErj7/oCvyPAzRIweroocVNc8YN0MpvtjhyaeGMb3YFxHdSm', 'Male', NULL, 'jitesh.patle@syslogic.in', 'jitesh.patle@syslogic.in', 'Embedded', 'Software Engineer', '1495109965_445922.png', NULL, NULL, NULL, 210, '1.2'),
(24, 'Sanket Gore', 'sanket.gore@syslogic.in', '$2y$10$Knt0CQLuVOMDk6YliBavROzy6prh1cY4jM6mM8qcpAiVUBxZSmKbu', 'Male', NULL, 'sanket.gore@syslogic.in', 'sanket@syslogyx.com', 'Embedded', 'Software Engineer', 'default_profile.png', NULL, NULL, NULL, 211, '0.3'),
(25, 'Neha Ishwar Kadbe', 'neha.kadbe@syslogic.in', '$2y$10$X6AYxhtlh7VxBxwXuE40dOkUqw.vPwjJAtQMNBm.m1VZ3/tK8u.NK', 'Female', NULL, 'neha.kadbe@syslogic.in', 'neha.kadbe@vyako.com', 'Frontend & iOS', 'Software Engineer', 'default_profile.png', NULL, NULL, NULL, 212, '1.0'),
(26, 'Alok Das', 'alok.das@syslogic.com', '$2y$10$bTZQOAV4veaw.9oLnlhvuuqw3DxXaUAl9bky34P9ce0rybovBeLf2', 'Male', NULL, 'alok.das@syslogic.com', 'alok.das@syslogic.com', 'Frontend & Backend Development', 'Software Engineer', 'default_profile.png', NULL, NULL, NULL, 213, '0.6'),
(27, 'Pooja Rajesh Uapdhyay', 'pooja.upadhyay@syslogic.in', '$2y$10$d/0fTaE4TF1cX5ypchafHemNvO.jdpQI.qaKlUyeGqci6SdedWveK', 'Female', NULL, 'pooja.upadhyay@syslogic.in', 'pooja.upadhyay@syslogic.in', 'UAV', 'Software Engineer', 'default_profile.png', NULL, NULL, NULL, 214, '0.1'),
(28, 'Pallavi Wadhai', 'pallavi.wadhai@syslogic.in', '$2y$10$023BiIfMQuNvwX5/uyy0wO/PbHOtmwO2wnDx1QkpaSwzz9GpKRTYa', 'Female', NULL, 'pallavi.wadhai@syslogic.in', 'pallavi.wadhai@syslogic.in', 'Embedded', 'Software Engineer', 'default_profile.png', NULL, NULL, NULL, 215, '0.3'),
(29, 'PRANATI PRADYOT KEDAR', 'pranati.kedar@syslogics.in', '$2y$10$xkvfy7K4jMIBFhvTKM2iweuQvYqul7.Tys7tuGa/drJkDKq5Sdnym', 'Female', NULL, 'pranati.kedar@syslogics.in', 'pranati.kedar@syslogics.in', 'UAV', 'Software Engineer', 'default_profile.png', NULL, NULL, NULL, 216, '0.1'),
(30, 'Anuradha Singh', 'anuradha.singh@syslogic.in', '$2y$10$PQs8NN26166CtBnW0FsZ/.Q8kZa1UjvFl.f7lWk6ju0yDYn9d3oIO', 'Female', NULL, 'anuradha.singh@syslogic.in', 'anuradha.singh@vyako.com', 'Business Development', 'Software Engineer', '1503495550_614685.jpg', NULL, NULL, NULL, 217, '1.7'),
(31, 'NAMRATA I KHOBRAGADE', 'namrata.k@syslogic.in', '$2y$10$.xt14mQIWDRz.vocZugAqONT7W41IpBopu7Yd9j0VUQNyHFhruh8a', 'Female', NULL, 'namrata.k@syslogic.in', 'namrata.k@syslogic.in', 'Backend Development', 'Software Engineer', 'default_profile.png', NULL, NULL, NULL, 218, '0.7'),
(32, 'Aniruddha C Agarkathe', 'aniruddha.agarkathe@syslogic.in', '$2y$10$Ne6ZdNcilURp4ThJ/4ayNupZXLs75hkg9hOTiD6WANQxewMSoU/pi', 'Male', NULL, 'aniruddha.agarkathe@syslogic.in', 'ani1nom@gmail.com', 'Quality Assurance', 'Software Engineer', 'default_profile.png', NULL, NULL, NULL, 219, '0'),
(33, 'Shubham Langde', 'shubham.langde@syslogic.in', '$2y$10$sBxuz7ndoC4icxvv8Sld8eTtwxn4ux/8unvtCRTnDFtomfetpOANe', 'Male', NULL, 'shubham.langde@syslogic.in', 'shubham.langde@syslogic.in', 'Embedded', 'Software Engineer', 'default_profile.png', NULL, NULL, NULL, 220, '0.7'),
(34, 'Monica Jha', 'monica.jha@syslogic.in', '$2y$10$L5LPDBBk2zxWs1/DMlzXrOGSFqMna3YQcAE0i34isDOArnMetRJZ2', 'Female', NULL, 'monica.jha@syslogic.in', 'monica.jha@vyako.com', 'Frontend & Backend Development', 'Software Engineer', 'default_profile.png', NULL, NULL, NULL, 221, '1.7'),
(35, 'Simpi', 'simpi.chaudhary@syslogic.in', '$2y$10$/Mweo.E4O5D94USBMWtllu0zMtml8pnth.VFVBYlFogqn.78cUS2.', 'Female', NULL, 'simpi.chaudhary@syslogic.in', 'simpi.chaudhary@syslogic.in', 'Development', 'Software Engineer', 'default_profile.png', NULL, NULL, NULL, 222, '0.3'),
(36, 'KISHOR UDDHAO MASURKAR', 'kishor.masurkar@syslogic.in', '$2y$10$yzgimpBQkLB4UzkCkttY2uO.mpt24Y2peu0PIS3ubWCQ1mxZ9GJga', 'Male', NULL, 'kishor.masurkar@syslogic.in', 'kishor.masurkar@syslogic.in', 'UAV', 'Intern Software Engineer', 'default_profile.png', NULL, NULL, NULL, 223, '0.6'),
(37, 'BHAVNA MULANI', 'bhavana.mulani@syslogic.in', '$2y$10$FJA1wkpc.L.Gp7oNPMHMD.KfRtIh/PMP7rIRunHruY.RU4bPhvxDe', 'Female', NULL, 'bhavana.mulani@syslogic.in', 'bhavana.mulani@vyako.com', 'iOS Development', 'Software Engineer', 'default_profile.png', NULL, NULL, NULL, 224, '1.0'),
(38, 'palak sanjeev agrawal', 'palak.agrawal@syslogic.in', '$2y$10$aAStgkqS3v.eUWjiTgFasO6z.j//3mK7M8/71lIwJ/94vaYVilvz2', 'Female', NULL, 'palak.agrawal@syslogic.in', 'agrawal.palak1108@gmail.com', 'Development', 'Software Engineer', 'default_profile.png', NULL, NULL, NULL, 225, '0'),
(39, 'Abhishek Govindrao Pimpalshende', 'abhishek.pimpleshende@syslogic.in', '$2y$10$9PCZQ9LTh3O0FKtWwhf1Nu88wuV6JP/Er5Z63b5wdjGFNR0pjAtvu', 'Female', NULL, 'abhishek.pimpleshende@syslogic.in', 'abhishek.pimpleshende@syslogic.in', 'Embedded', 'Software Engineer', 'default_profile.png', NULL, NULL, NULL, 226, '0.6'),
(40, 'Mohin lakhe', 'mohini.lakhe@syslogic.in', '$2y$10$Ij/DryisVALEL7Xy9bbc2eW.zUBXGBMZJ7..jscTRpTS8D6umnhYm', 'Female', NULL, 'mohini.lakhe@syslogic.in', 'mohinilakhe506@gmail.com', 'Embedded', 'Software Engineer', 'default_profile.png', NULL, NULL, NULL, 227, '0'),
(41, 'HIMANSHU HEMANT NELWADKAR', 'himanshu@syslogic.in', '$2y$10$999XwFi6.8AAPrsSBDpQVOT5g0u7o0azWXvD5URth2y/wO4yM2g/.', 'Male', NULL, 'himanshu@syslogic.in', 'nelwadkar.himanshu.h@gmail.com', 'Embedded', 'Software Engineer', 'default_profile.png', NULL, NULL, NULL, 228, '0.2'),
(42, 'Surabhi Shrivastava', 'surbhi.shrivastava@syslogic.in', '$2y$10$3QYy4zQwUZjTvZ7dsh6BXudj4uijv98XJxGEPkJ7iO2gkndb92882', 'Female', NULL, 'surbhi.shrivastava@syslogic.in', 'surbhi.vyako@gmail.com', 'Frontend Development', 'Software Engineer', 'default_profile.png', NULL, NULL, NULL, 229, '1.0'),
(43, 'Sayali Ashok Suryawanshi', 'sayli.suryawanshi@syslogic.in', '$2y$10$aWdLc1leSg6B.5vHL/kI5OVFj.HsvyrMXdxaohCEvWwoDBzetO1ey', 'Female', NULL, 'sayli.suryawanshi@syslogic.in', 'sayali.suryawanshi@vyako.com', 'Development', 'Software Engineer', 'default_profile.png', NULL, NULL, NULL, 230, '1.0');

-- --------------------------------------------------------

--
-- Table structure for table `user_technology_mapping`
--

CREATE TABLE `user_technology_mapping` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `technology_id` int(11) DEFAULT NULL,
  `duration_in_month` double(8,2) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_technology_mapping`
--

INSERT INTO `user_technology_mapping` (`id`, `user_id`, `technology_id`, `duration_in_month`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(3, 39, 1, 0.00, 1, 1, '2017-10-27 01:05:30', '2017-10-30 05:04:41'),
(4, 11, 1, 1.00, 1, 1, '2017-10-27 01:05:30', '2017-10-27 04:44:14'),
(5, 39, 3, 13.00, 1, 1, '2017-10-30 05:05:01', '2017-10-30 05:05:01'),
(6, 34, 3, 25.00, 1, 1, '2017-10-31 04:40:26', '2017-10-31 04:40:26'),
(7, 34, 2, 39.00, 1, 1, '2017-10-31 04:40:35', '2017-10-31 04:40:35'),
(8, 34, 4, 13.00, 1, 1, '2017-10-31 04:40:44', '2017-10-31 04:40:44'),
(9, 16, 7, 49.00, 1, 1, '2017-10-31 04:42:22', '2017-10-31 04:42:22'),
(10, 16, 1, 52.00, 1, 1, '2017-10-31 04:42:28', '2017-10-31 04:42:28'),
(11, 16, 5, 38.00, 1, 1, '2017-10-31 04:42:41', '2017-10-31 04:42:41'),
(12, 37, 7, 26.00, 1, 1, '2017-10-31 04:43:10', '2017-10-31 04:43:10'),
(13, 26, 3, 4.00, 1, 1, '2017-10-31 04:43:50', '2017-10-31 04:43:50'),
(14, 26, 2, 9.00, 1, 1, '2017-10-31 04:43:58', '2017-10-31 04:43:58'),
(15, 26, 5, 25.00, 1, 1, '2017-10-31 04:44:06', '2017-10-31 04:44:06'),
(16, 26, 4, 6.00, 1, 1, '2017-10-31 04:44:25', '2017-10-31 04:44:25'),
(17, 34, 7, 52.00, 1, 1, '2017-11-04 00:53:53', '2017-11-04 00:53:53'),
(18, 34, 6, 49.00, 1, 1, '2017-11-04 00:54:02', '2017-11-04 00:54:02'),
(19, 34, 5, 65.00, 1, 1, '2017-11-04 00:54:18', '2017-11-04 00:54:18'),
(20, 34, 1, 39.00, 1, 1, '2017-11-04 00:54:38', '2017-11-04 00:54:38'),
(21, 34, 12, 26.00, 1, 1, '2017-11-04 00:56:10', '2017-11-04 00:56:10'),
(22, 34, 11, 38.00, 1, 1, '2017-11-04 00:56:15', '2017-11-04 00:56:15'),
(23, 34, 10, 51.00, 1, 1, '2017-11-04 00:56:22', '2017-11-04 00:56:22'),
(24, 34, 8, 37.00, 1, 1, '2017-11-04 00:56:30', '2017-11-04 00:56:30'),
(25, 34, 9, 52.00, 1, 1, '2017-11-04 00:56:50', '2017-11-04 00:56:50'),
(26, 34, 13, 39.00, 1, 1, '2017-11-04 00:57:31', '2017-11-04 00:57:31'),
(27, 14, 2, 38.00, 1, 1, '2017-11-04 02:48:10', '2017-11-04 02:48:10'),
(28, 9, 7, 26.00, 1, 1, '2017-11-15 06:01:01', '2017-11-15 06:01:01'),
(29, 14, 14, 50.00, 1, 1, '2017-11-15 06:18:26', '2017-11-15 06:18:26'),
(30, 14, 3, 26.00, 1, 1, '2017-11-15 07:08:38', '2017-11-15 07:08:38'),
(31, 14, 13, 31.00, 1, 1, '2017-11-15 07:08:53', '2017-11-15 07:08:53'),
(32, 26, 10, 14.00, 1, 1, '2017-11-15 07:09:21', '2017-11-15 07:09:21'),
(33, 32, 14, 12.00, 1, 1, '2017-11-15 07:09:47', '2017-11-15 07:09:47'),
(34, 32, 15, 17.00, 1, 1, '2017-11-15 07:10:00', '2017-11-15 07:10:00'),
(35, 37, 5, 20.00, 1, 1, '2017-11-15 07:10:25', '2017-11-15 07:10:25'),
(36, 37, 14, 18.00, 1, 1, '2017-11-15 07:10:46', '2017-11-15 07:10:46'),
(37, 37, 1, 15.00, 1, 1, '2017-11-15 07:29:47', '2017-11-15 07:29:47'),
(38, 15, 1, 16.00, 1, 1, '2017-11-15 07:30:14', '2017-11-15 07:30:14'),
(39, 15, 5, 25.00, 1, 1, '2017-11-15 07:30:28', '2017-11-15 07:30:28');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_types`
--
ALTER TABLE `activity_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories_technology_mapping`
--
ALTER TABLE `categories_technology_mapping`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categories_technology_mapping_technology_id_foreign` (`technology_id`),
  ADD KEY `categories_technology_mapping_category_id_foreign` (`category_id`);

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `clients_project_id_foreign` (`project_id`);

--
-- Indexes for table `domains`
--
ALTER TABLE `domains`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `milestones`
--
ALTER TABLE `milestones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `milestones_project_id_foreign` (`project_id`),
  ADD KEY `milestones_status_id_foreign` (`status_id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_unique` (`name`);

--
-- Indexes for table `permission_role`
--
ALTER TABLE `permission_role`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `permission_role_role_id_foreign` (`role_id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `projects_domain_id_foreign` (`domain_id`),
  ADD KEY `projects_user_id_foreign` (`user_id`),
  ADD KEY `projects_client_id_foreign` (`client_id`),
  ADD KEY `projects_status_id_foreign` (`status_id`);

--
-- Indexes for table `project_activity_status_logs`
--
ALTER TABLE `project_activity_status_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_activity_status_logs_status_id_foreign` (`status_id`);

--
-- Indexes for table `project_category_mapping`
--
ALTER TABLE `project_category_mapping`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_category_mapping_project_id_foreign` (`project_id`),
  ADD KEY `project_category_mapping_category_id_foreign` (`category_id`);

--
-- Indexes for table `project_category_technology_mapping`
--
ALTER TABLE `project_category_technology_mapping`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_category_technology_mapping_project_category_id_foreign` (`project_category_id`),
  ADD KEY `project_category_technology_mapping_technology_id_foreign` (`technology_id`);

--
-- Indexes for table `project_documents`
--
ALTER TABLE `project_documents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `project_poc`
--
ALTER TABLE `project_poc`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_poc_project_id_foreign` (`project_id`);

--
-- Indexes for table `project_resources`
--
ALTER TABLE `project_resources`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `project_resources_project_id_user_id_domain_id_unique` (`project_id`,`user_id`,`domain_id`),
  ADD KEY `project_resources_user_id_foreign` (`user_id`),
  ADD KEY `project_resources_domain_id_foreign` (`domain_id`),
  ADD KEY `project_resources_status_id_foreign` (`status_id`);

--
-- Indexes for table `project_resource_technology_mapping`
--
ALTER TABLE `project_resource_technology_mapping`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_resource_technology_mapping_project_resource_id_foreign` (`project_resource_id`),
  ADD KEY `project_resource_technology_mapping_technology_id_foreign` (`technology_id`);

--
-- Indexes for table `project_technologies`
--
ALTER TABLE `project_technologies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_technologies_project_id_foreign` (`project_id`),
  ADD KEY `project_technologies_technology_id_foreign` (`technology_id`);

--
-- Indexes for table `resource_matrix_log`
--
ALTER TABLE `resource_matrix_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `resource_matrix_log_project_id_foreign` (`project_id`),
  ADD KEY `resource_matrix_log_user_id_foreign` (`user_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `role_user`
--
ALTER TABLE `role_user`
  ADD PRIMARY KEY (`user_id`,`role_id`),
  ADD KEY `role_user_role_id_foreign` (`role_id`);

--
-- Indexes for table `status`
--
ALTER TABLE `status`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tasks_project_resource_id_foreign` (`project_resource_id`),
  ADD KEY `tasks_milestone_id_foreign` (`milestone_id`),
  ADD KEY `tasks_technical_support_id_foreign` (`technical_support_id`),
  ADD KEY `tasks_status_id_foreign` (`status_id`);

--
-- Indexes for table `task_comment_logs`
--
ALTER TABLE `task_comment_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_comment_logs_task_id_foreign` (`task_id`);

--
-- Indexes for table `technical_supports`
--
ALTER TABLE `technical_supports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `technical_supports_user_id_foreign` (`user_id`),
  ADD KEY `technical_supports_task_id_foreign` (`task_id`);

--
-- Indexes for table `technologies`
--
ALTER TABLE `technologies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `test`
--
ALTER TABLE `test`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_technology_mapping`
--
ALTER TABLE `user_technology_mapping`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_technology_mapping_user_id_foreign` (`user_id`),
  ADD KEY `user_technology_mapping_technology_id_foreign` (`technology_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_types`
--
ALTER TABLE `activity_types`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
--
-- AUTO_INCREMENT for table `categories_technology_mapping`
--
ALTER TABLE `categories_technology_mapping`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;
--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `domains`
--
ALTER TABLE `domains`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;
--
-- AUTO_INCREMENT for table `milestones`
--
ALTER TABLE `milestones`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
--
-- AUTO_INCREMENT for table `project_activity_status_logs`
--
ALTER TABLE `project_activity_status_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
--
-- AUTO_INCREMENT for table `project_category_mapping`
--
ALTER TABLE `project_category_mapping`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;
--
-- AUTO_INCREMENT for table `project_category_technology_mapping`
--
ALTER TABLE `project_category_technology_mapping`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;
--
-- AUTO_INCREMENT for table `project_documents`
--
ALTER TABLE `project_documents`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `project_poc`
--
ALTER TABLE `project_poc`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `project_resources`
--
ALTER TABLE `project_resources`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;
--
-- AUTO_INCREMENT for table `project_resource_technology_mapping`
--
ALTER TABLE `project_resource_technology_mapping`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;
--
-- AUTO_INCREMENT for table `project_technologies`
--
ALTER TABLE `project_technologies`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `resource_matrix_log`
--
ALTER TABLE `resource_matrix_log`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `status`
--
ALTER TABLE `status`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `task_comment_logs`
--
ALTER TABLE `task_comment_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `technical_supports`
--
ALTER TABLE `technical_supports`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `technologies`
--
ALTER TABLE `technologies`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
--
-- AUTO_INCREMENT for table `test`
--
ALTER TABLE `test`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;
--
-- AUTO_INCREMENT for table `user_technology_mapping`
--
ALTER TABLE `user_technology_mapping`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
