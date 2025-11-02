-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 31, 2025 at 01:52 PM
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
-- Database: `school_it_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `building_floor_plans`
--

CREATE TABLE `building_floor_plans` (
  `id` int(11) NOT NULL COMMENT 'รหัสแผนผัง (Primary Key)',
  `school_name` varchar(255) NOT NULL COMMENT 'ชื่อโรงเรียน',
  `building_name` varchar(255) NOT NULL COMMENT 'ชื่ออาคาร',
  `academic_year` int(11) NOT NULL COMMENT 'ปีการศึกษา',
  `floor_plan_image` varchar(255) NOT NULL COMMENT 'เส้นทางไฟล์ภาพแผนผัง',
  `plan_description` text DEFAULT NULL COMMENT 'คำอธิบายแผนผัง',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่สร้างบันทึก',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'วันที่แก้ไขล่าสุด'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `school_name` varchar(100) NOT NULL COMMENT 'ชื่อโรงเรียน',
  `department_name` varchar(100) NOT NULL COMMENT 'ชื่อแผนก',
  `department_description` text DEFAULT NULL COMMENT 'รายละเอียดแผนก',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่สร้างบันทึก',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'วันที่แก้ไขล่าสุด'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `school_name`, `department_name`, `department_description`, `created_at`, `updated_at`) VALUES
(12, 'โรงเรียนวารีเชียงใหม่', 'ฝ่ายบริหาร', '', '2025-10-31 09:25:27', '2025-10-31 09:26:02'),
(13, 'โรงเรียนวารีเชียงใหม่', 'แผนกอำนวยการ', '', '2025-10-31 09:26:08', '2025-10-31 09:26:08'),
(14, 'โรงเรียนวารีเชียงใหม่', 'ฝ่ายวิชาการ', '', '2025-10-31 09:26:12', '2025-10-31 09:26:12'),
(15, 'โรงเรียนวารีเชียงใหม่', 'แผนกอนุบาล', '', '2025-10-31 09:26:20', '2025-10-31 09:26:20'),
(16, 'โรงเรียนวารีเชียงใหม่', 'ประถมศึกษา', '', '2025-10-31 09:26:26', '2025-10-31 09:26:26'),
(17, 'โรงเรียนวารีเชียงใหม่', 'มัธยมศึกษา', '', '2025-10-31 09:26:31', '2025-10-31 09:26:31'),
(18, 'โรงเรียนวารีเชียงใหม่', 'สนับสนุน', '', '2025-10-31 09:26:39', '2025-10-31 09:26:39'),
(19, 'โรงเรียนวารีเชียงใหม่', 'IT', '', '2025-10-31 09:26:44', '2025-10-31 09:26:44'),
(20, 'โรงเรียนอนุบาลวารีเชียงใหม่', 'แผนกอำนวยการ', '', '2025-10-31 09:26:50', '2025-10-31 09:26:50'),
(21, 'โรงเรียนอนุบาลวารีเชียงใหม่', 'แผนกอนุบาล', '', '2025-10-31 09:26:59', '2025-10-31 09:26:59'),
(22, 'โรงเรียนนานาชาติวารีเชียงใหม่', 'Administration', '', '2025-10-31 09:30:43', '2025-10-31 09:30:43'),
(23, 'โรงเรียนนานาชาติวารีเชียงใหม่', 'Kindergarten', '', '2025-10-31 09:30:48', '2025-10-31 09:30:48'),
(24, 'โรงเรียนนานาชาติวารีเชียงใหม่', 'Primary', '', '2025-10-31 09:30:57', '2025-10-31 09:30:57'),
(25, 'โรงเรียนนานาชาติวารีเชียงใหม่', 'Secondary', '', '2025-10-31 09:31:06', '2025-10-31 09:31:06'),
(26, 'โรงเรียนนานาชาติวารีเชียงใหม่', 'Support', '', '2025-10-31 09:31:19', '2025-10-31 09:31:19');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL COMMENT 'รหัสพนักงาน (Primary Key)',
  `employee_code` varchar(20) DEFAULT NULL COMMENT 'รหัสพนักงานสำหรับอ้างอิง',
  `first_name` varchar(50) NOT NULL COMMENT 'ชื่อพนักงาน',
  `last_name` varchar(50) NOT NULL COMMENT 'นามสกุลพนักงาน',
  `department_id` int(11) DEFAULT NULL COMMENT 'รหัสแผนก (Foreign Key)',
  `position_name` varchar(100) DEFAULT NULL COMMENT 'ตำแหน่งงาน',
  `email_address` varchar(100) DEFAULT NULL COMMENT 'ที่อยู่อีเมลพนักงาน',
  `phone_number` varchar(20) DEFAULT NULL COMMENT 'เบอร์โทรศัพท์พนักงาน',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่สร้างบันทึก',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'วันที่แก้ไขล่าสุด'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `employee_code`, `first_name`, `last_name`, `department_id`, `position_name`, `email_address`, `phone_number`, `created_at`, `updated_at`) VALUES
(1, 'EMP001', 'สมชาย', 'ใจดี', 20, 'ช่างคอมพิวเตอร์', 'somchai@varee.ac.th', '081-111-1111', '2025-10-31 03:28:28', '2025-10-31 09:43:38'),
(2, 'EMP002', 'สมหญิง', 'รักงาน', 24, 'เจ้าหน้าที่ IT', 'somying@varee.ac.th', '081-222-2222', '2025-10-31 03:28:28', '2025-10-31 09:43:46'),
(3, 'EMP003', 'วิชัย', 'มั่นคง', 23, 'เจ้าหน้าที่บริหาร', 'wichai@varee.ac.th', '081-333-3333', '2025-10-31 03:28:28', '2025-10-31 09:41:33');

-- --------------------------------------------------------

--
-- Table structure for table `equipment`
--

CREATE TABLE `equipment` (
  `id` int(11) NOT NULL COMMENT 'รหัสครุภัณฑ์ (Primary Key)',
  `equipment_code` varchar(50) NOT NULL COMMENT 'รหัสครุภัณฑ์สำหรับอ้างอิง',
  `equipment_name` varchar(200) NOT NULL COMMENT 'ชื่อครุภัณฑ์',
  `category_id` int(11) DEFAULT NULL COMMENT 'รหัสหมวดหมู่หลัก (Foreign Key)',
  `subcategory_id` int(11) DEFAULT NULL COMMENT 'รหัสหมวดหมู่ย่อย (Foreign Key)',
  `brand_name` varchar(100) DEFAULT NULL COMMENT 'ยี่ห้อครุภัณฑ์',
  `model_name` varchar(100) DEFAULT NULL COMMENT 'รุ่นครุภัณฑ์',
  `serial_number` varchar(100) DEFAULT NULL COMMENT 'หมายเลขซีเรียล',
  `purchase_date` date DEFAULT NULL COMMENT 'วันที่จัดซื้อ',
  `warranty_expiry_date` date DEFAULT NULL COMMENT 'วันที่หมดประกัน',
  `purchase_price` decimal(10,2) DEFAULT 0.00 COMMENT 'ราคาจัดซื้อ',
  `supplier_name` varchar(200) DEFAULT NULL COMMENT 'ชื่อผู้จัดจำหน่าย',
  `equipment_status` varchar(50) DEFAULT 'ใหม่' COMMENT 'สถานะครุภัณฑ์',
  `location_school` varchar(200) DEFAULT NULL COMMENT 'โรงเรียนที่ตั้ง',
  `location_building` varchar(100) DEFAULT NULL COMMENT 'ตึก/อาคารที่ตั้ง',
  `location_floor` varchar(50) DEFAULT NULL COMMENT 'ชั้นที่ตั้ง',
  `location_room` varchar(100) DEFAULT NULL COMMENT 'ห้องที่ตั้ง',
  `responsible_person` varchar(100) DEFAULT NULL COMMENT 'ผู้รับผิดชอบครุภัณฑ์',
  `notes` text DEFAULT NULL COMMENT 'หมายเหตุเพิ่มเติม',
  `image_path` varchar(255) DEFAULT NULL COMMENT 'เส้นทางเก็บรูปภาพ',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่สร้างบันทึก',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'วันที่แก้ไขล่าสุด'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `equipment_classroom`
--

CREATE TABLE `equipment_classroom` (
  `id` int(11) NOT NULL COMMENT 'รหัสการจัดวาง (Primary Key)',
  `equipment_code` varchar(50) NOT NULL COMMENT 'รหัสครุภัณฑ์สำหรับอ้างอิง',
  `school_name` varchar(255) NOT NULL COMMENT 'ชื่อโรงเรียนที่ตั้ง',
  `building_name` varchar(255) NOT NULL COMMENT 'ชื่ออาคารที่ตั้ง',
  `floor_level` varchar(100) NOT NULL COMMENT 'ชั้นที่ตั้ง',
  `room_number` varchar(100) NOT NULL COMMENT 'หมายเลขห้อง',
  `room_name` varchar(255) DEFAULT NULL COMMENT 'ชื่อห้อง',
  `equipment_quantity` int(11) NOT NULL DEFAULT 1 COMMENT 'จำนวนครุภัณฑ์ในห้อง',
  `installation_date` date DEFAULT NULL COMMENT 'วันที่ติดตั้ง',
  `placement_notes` text DEFAULT NULL COMMENT 'หมายเหตุการจัดวาง',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่สร้างบันทึก',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'วันที่แก้ไขล่าสุด'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `equipment_disposals`
--

CREATE TABLE `equipment_disposals` (
  `id` int(11) NOT NULL COMMENT 'รหัสการจำหน่าย (Primary Key)',
  `equipment_id` int(11) NOT NULL COMMENT 'รหัสครุภัณฑ์ (Foreign Key)',
  `disposal_date` date NOT NULL COMMENT 'วันที่จำหน่าย',
  `disposal_method` varchar(100) DEFAULT NULL COMMENT 'วิธีการจำหน่าย',
  `disposal_value` decimal(10,2) DEFAULT 0.00 COMMENT 'มูลค่าที่จำหน่าย',
  `disposal_reason` text DEFAULT NULL COMMENT 'เหตุผลในการจำหน่าย',
  `approved_by` varchar(100) DEFAULT NULL COMMENT 'ผู้อนุมัติการจำหน่าย',
  `disposal_notes` text DEFAULT NULL COMMENT 'หมายเหตุการจำหน่าย',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่สร้างบันทึก'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `equipment_subcategories`
--

CREATE TABLE `equipment_subcategories` (
  `id` int(11) NOT NULL COMMENT 'รหัสหมวดหมู่ย่อย (Primary Key)',
  `category_id` int(11) NOT NULL COMMENT 'รหัสหมวดหมู่หลัก (Foreign Key)',
  `subcategory_name` varchar(100) NOT NULL COMMENT 'ชื่อหมวดหมู่ย่อย',
  `subcategory_description` text DEFAULT NULL COMMENT 'รายละเอียดหมวดหมู่ย่อย',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่สร้างบันทึก',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'วันที่แก้ไขล่าสุด'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `equipment_subcategories`
--

INSERT INTO `equipment_subcategories` (`subcategory_id`, `category_id`, `subcategory_name`, `subcategory_description`, `created_at`, `updated_at`) VALUES
(1, 1, 'เครื่องคอมพิวเตอร์ตั้งโต๊ะ', 'Desktop Computer', '2025-10-31 03:28:15', '2025-10-31 03:28:15'),
(2, 1, 'โน้ตบุ๊ค', 'Notebook/Laptop', '2025-10-31 03:28:15', '2025-10-31 03:28:15'),
(3, 1, 'จอคอมพิวเตอร์', 'Monitor', '2025-10-31 03:28:15', '2025-10-31 03:28:15'),
(4, 1, 'เครื่องพิมพ์', 'Printer', '2025-10-31 03:28:15', '2025-10-31 03:28:15'),
(5, 1, 'เมาส์', 'Mouse', '2025-10-31 03:28:15', '2025-10-31 03:28:15'),
(6, 1, 'คีย์บอร์ด', 'Keyboard', '2025-10-31 03:28:15', '2025-10-31 03:28:15'),
(7, 2, 'โทรศัพท์', 'Telephone', '2025-10-31 03:28:15', '2025-10-31 03:28:15'),
(8, 2, 'เครื่องแฟกซ์', 'Fax Machine', '2025-10-31 03:28:15', '2025-10-31 03:28:15'),
(9, 2, 'เครื่องถ่ายเอกสาร', 'Photocopier', '2025-10-31 03:28:15', '2025-10-31 03:28:15'),
(10, 2, 'เครื่องสแกน', 'Scanner', '2025-10-31 03:28:15', '2025-10-31 03:28:15'),
(11, 3, 'โปรเจคเตอร์', 'Projector', '2025-10-31 03:28:15', '2025-10-31 03:28:15'),
(12, 3, 'จอรับภาพ', 'Projection Screen', '2025-10-31 03:28:15', '2025-10-31 03:28:15'),
(13, 3, 'กล้องวงจรปิด', 'CCTV Camera', '2025-10-31 03:28:15', '2025-10-31 03:28:15'),
(14, 3, 'ลำโพง', 'Speaker', '2025-10-31 03:28:15', '2025-10-31 03:28:15'),
(15, 5, 'โต๊ะทำงาน', 'Desk', '2025-10-31 03:28:15', '2025-10-31 03:28:15'),
(16, 5, 'เก้าอี้', 'Chair', '2025-10-31 03:28:15', '2025-10-31 03:28:15'),
(17, 5, 'ตู้เก็บเอกสาร', 'Filing Cabinet', '2025-10-31 03:28:15', '2025-10-31 03:28:15'),
(18, 5, 'ชั้นวางหนังสือ', 'Bookshelf', '2025-10-31 03:28:15', '2025-10-31 03:28:15');

-- --------------------------------------------------------

--
-- Table structure for table `equipment_transfers`
--

CREATE TABLE `equipment_transfers` (
  `transfer_id` int(11) NOT NULL COMMENT 'รหัสการโอนย้าย (Primary Key)',
  `equipment_id` int(11) NOT NULL COMMENT 'รหัสครุภัณฑ์ (Foreign Key)',
  `from_school` varchar(200) DEFAULT NULL COMMENT 'โรงเรียนเดิม',
  `from_building` varchar(100) DEFAULT NULL COMMENT 'ตึกเดิม',
  `from_floor` varchar(50) DEFAULT NULL COMMENT 'ชั้นเดิม',
  `from_room` varchar(100) DEFAULT NULL COMMENT 'ห้องเดิม',
  `to_school` varchar(200) DEFAULT NULL COMMENT 'โรงเรียนใหม่',
  `to_building` varchar(100) DEFAULT NULL COMMENT 'ตึกใหม่',
  `to_floor` varchar(50) DEFAULT NULL COMMENT 'ชั้นใหม่',
  `to_room` varchar(100) DEFAULT NULL COMMENT 'ห้องใหม่',
  `transfer_date` date NOT NULL COMMENT 'วันที่โอนย้าย',
  `transferred_by` varchar(100) DEFAULT NULL COMMENT 'ผู้ดำเนินการโอนย้าย',
  `transfer_reason` text DEFAULT NULL COMMENT 'เหตุผลในการโอนย้าย',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่สร้างบันทึก'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `maintenance_logs`
--

CREATE TABLE `maintenance_logs` (
  `log_id` int(11) NOT NULL COMMENT 'รหัสบันทึก (Primary Key)',
  `maintenance_id` int(11) NOT NULL COMMENT 'รหัสการซ่อม (Foreign Key)',
  `previous_status` varchar(50) DEFAULT NULL COMMENT 'สถานะก่อนหน้า',
  `new_status` varchar(50) DEFAULT NULL COMMENT 'สถานะใหม่',
  `changed_by_user` varchar(100) DEFAULT NULL COMMENT 'ผู้ใช้งานที่เปลี่ยนแปลงสถานะ',
  `action_notes` text DEFAULT NULL COMMENT 'หมายเหตุการดำเนินการ',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่สร้างบันทึก'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `maintenance_requests`
--

CREATE TABLE `maintenance_requests` (
  `id` int(11) NOT NULL COMMENT 'รหัสการซ่อม (Primary Key)',
  `repair_code` varchar(20) NOT NULL COMMENT 'รหัสใบแจ้งซ่อม',
  `equipment_id` int(11) NOT NULL COMMENT 'รหัสครุภัณฑ์ (Foreign Key)',
  `report_date` date NOT NULL COMMENT 'วันที่แจ้งซ่อม',
  `problem_description` text NOT NULL COMMENT 'รายละเอียดปัญหา',
  `reported_by` varchar(100) NOT NULL COMMENT 'ผู้แจ้งซ่อม',
  `assigned_technician` varchar(100) DEFAULT NULL COMMENT 'ช่างผู้รับผิดชอบ',
  `repair_status` varchar(50) DEFAULT 'รอซ่อม' COMMENT 'สถานะการซ่อม',
  `solution_description` text DEFAULT NULL COMMENT 'รายละเอียดการแก้ไข',
  `repair_cost` decimal(10,2) DEFAULT 0.00 COMMENT 'ค่าใช้จ่ายในการซ่อม',
  `completed_date` date DEFAULT NULL COMMENT 'วันที่ซ่อมเสร็จสิ้น',
  `location_school` varchar(200) DEFAULT NULL COMMENT 'โรงเรียนที่ตั้งครุภัณฑ์',
  `location_building` varchar(100) DEFAULT NULL COMMENT 'ตึกที่ตั้งครุภัณฑ์',
  `location_floor` varchar(50) DEFAULT NULL COMMENT 'ชั้นที่ตั้งครุภัณฑ์',
  `location_room` varchar(100) DEFAULT NULL COMMENT 'ห้องที่ตั้งครุภัณฑ์',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่สร้างบันทึก',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'วันที่แก้ไขล่าสุด'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `maintenance_requests`
--

INSERT INTO `maintenance_requests` (`id`, `repair_code`, `equipment_id`, `report_date`, `problem_description`, `reported_by`, `assigned_technician`, `repair_status`, `solution_description`, `repair_cost`, `completed_date`, `location_school`, `location_building`, `location_floor`, `location_room`, `created_at`, `updated_at`) VALUES
(1, 'R202410-0001', 1, '2024-10-01', 'คอมพิวเตอร์เปิดไม่ติด', 'คุณสมศรี', 'สมชาย ใจดี', 'ซ่อมเสร็จ', 'เปลี่ยนแหล่งจ่ายไฟใหม่', 2500.00, '2024-10-03', 'โรงเรียนวารีเชียงใหม่', 'ตึก1-อำนวยการ', 'ชั้น 1', 'ห้องธุรการ', '2025-10-31 03:29:04', '2025-10-31 03:29:04'),
(2, 'R202410-0002', 3, '2024-10-15', 'ภาพไม่ชัด มีจุดดำ', 'คุณวิชัย', 'สมชาย ใจดี', 'รอซ่อม', '', 0.00, '0000-00-00', 'โรงเรียนวารีเชียงใหม่', 'ตึก3-ประถม', 'ชั้น 2', 'ห้องประชุม', '2025-10-31 03:29:04', '2025-10-31 09:53:43'),
(3, 'R202410-0003', 2, '2024-10-20', 'แบตเตอรี่เสื่อม ใช้งานได้ไม่เกิน 1 ชั่วโมง', 'คุณสมหญิง', '', 'รอซ่อม', '', 0.00, '0000-00-00', 'โรงเรียนวารีเชียงใหม่', 'ตึก7-มัธยม', 'ชั้น 2', '201', '2025-10-31 03:29:04', '2025-10-31 09:55:09');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL COMMENT 'รหัสผู้ใช้ระบบ (Primary Key)',
  `username` varchar(50) NOT NULL COMMENT 'ชื่อผู้ใช้สำหรับเข้าสู่ระบบ',
  `password` varchar(255) NOT NULL COMMENT 'รหัสผ่านที่เข้ารหัสแล้ว',
  `employee_id` varchar(20) NOT NULL COMMENT 'รหัสพนักงาน',
  `full_name` varchar(100) NOT NULL COMMENT 'ชื่อ-นามสกุลผู้ใช้',
  `role` enum('admin','user','technician') DEFAULT 'admin' COMMENT 'บทบาทผู้ใช้ (admin, user, technician)',
  `is_active` tinyint(1) DEFAULT 1 COMMENT 'สถานะ',
  `last_login` timestamp NULL DEFAULT NULL COMMENT 'เข้าใช้ล่าสุด',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่สร้างบันทึก',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'วันที่แก้ไขล่าสุด'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `employee_id`, `full_name`, `role`, `is_active`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '6501005', 'jukkrit', 'admin', 1, '2025-10-31 09:52:16', '2025-10-26 20:02:41', '2025-10-31 09:52:16'),
(2, 'moss', '$2y$10$HzzC.TvfQ4bNKUkqaSH8fe.GTa40FEOvHvOjdWwvJfgk26Rc5HK3.', '6501008', 'mosss', 'admin', 1, '2025-10-31 07:00:18', '2025-10-31 05:56:11', '2025-10-31 07:00:18'),
(5, 'test3', '$2y$10$GVrzPjNNuHlqLKeh.xJYDeN6No6Q9rF8TTdlz5zGkv6pQZ.ecQNCW', '6541321', 'moss', 'technician', 1, '2025-10-31 06:46:31', '2025-10-31 06:13:01', '2025-10-31 06:46:31'),
(6, 'ะำหะ4', '$2y$10$gakEebHjbQMiQvpV6zdgG.6IXbNMXKcX/KKG5EzgkVD0nfOg/uXay', '74123', 'suriya', 'user', 1, NULL, '2025-10-31 06:39:12', '2025-10-31 06:39:12');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `building_floor_plans`
--
ALTER TABLE `building_floor_plans`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD UNIQUE KEY `unique_plan` (`school_name`,`building_name`,`academic_year`) COMMENT 'ป้องกันแผนผังซ้ำสำหรับโรงเรียน-อาคาร-ปีการศึกษาเดียวกัน',
  ADD KEY `idx_school_building` (`school_name`,`building_name`) COMMENT 'ดัชนีสำหรับค้นหาตามโรงเรียนและอาคาร',
  ADD KEY `idx_academic_year` (`academic_year`) COMMENT 'ดัชนีสำหรับค้นหาตามปีการศึกษา';

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_department_name` (`department_name`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`employee_id`),
  ADD UNIQUE KEY `employee_code` (`employee_code`),
  ADD KEY `idx_employee_code` (`employee_code`),
  ADD KEY `idx_department_id` (`department_id`),
  ADD KEY `idx_employee_name` (`first_name`,`last_name`);

--
-- Indexes for table `equipment`
--
ALTER TABLE `equipment`
  ADD PRIMARY KEY (`equipment_id`),
  ADD UNIQUE KEY `equipment_code` (`equipment_code`),
  ADD KEY `subcategory_id` (`subcategory_id`),
  ADD KEY `idx_equipment_code` (`equipment_code`),
  ADD KEY `idx_category_id` (`category_id`),
  ADD KEY `idx_equipment_status` (`equipment_status`),
  ADD KEY `idx_location` (`location_school`,`location_building`,`location_floor`,`location_room`),
  ADD KEY `idx_purchase_date` (`purchase_date`);

--
-- Indexes for table `equipment_categories`
--
ALTER TABLE `equipment_categories`
  ADD PRIMARY KEY (`category_id`),
  ADD KEY `idx_category_name` (`category_name`);

--
-- Indexes for table `equipment_classroom`
--
ALTER TABLE `equipment_classroom`
  ADD PRIMARY KEY (`placement_id`),
  ADD KEY `equipment_code` (`equipment_code`),
  ADD KEY `idx_location` (`school_name`,`building_name`,`floor_level`) COMMENT 'ดัชนีสำหรับค้นหาตามสถานที่ตั้ง';

--
-- Indexes for table `equipment_disposals`
--
ALTER TABLE `equipment_disposals`
  ADD PRIMARY KEY (`disposal_id`),
  ADD KEY `idx_equipment_id` (`equipment_id`),
  ADD KEY `idx_disposal_date` (`disposal_date`);

--
-- Indexes for table `equipment_subcategories`
--
ALTER TABLE `equipment_subcategories`
  ADD PRIMARY KEY (`subcategory_id`),
  ADD KEY `idx_category_id` (`category_id`),
  ADD KEY `idx_subcategory_name` (`subcategory_name`);

--
-- Indexes for table `equipment_transfers`
--
ALTER TABLE `equipment_transfers`
  ADD PRIMARY KEY (`transfer_id`),
  ADD KEY `idx_equipment_id` (`equipment_id`),
  ADD KEY `idx_transfer_date` (`transfer_date`);

--
-- Indexes for table `maintenance_logs`
--
ALTER TABLE `maintenance_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `idx_maintenance_id` (`maintenance_id`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `maintenance_requests`
--
ALTER TABLE `maintenance_requests`
  ADD PRIMARY KEY (`maintenance_id`),
  ADD UNIQUE KEY `repair_code` (`repair_code`),
  ADD KEY `idx_repair_code` (`repair_code`),
  ADD KEY `idx_equipment_id` (`equipment_id`),
  ADD KEY `idx_repair_status` (`repair_status`),
  ADD KEY `idx_report_date` (`report_date`),
  ADD KEY `idx_location` (`location_school`,`location_building`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `building_floor_plans`
--
ALTER TABLE `building_floor_plans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'รหัสแผนผัง (Primary Key)';

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `employee_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'รหัสพนักงาน (Primary Key)', AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `equipment`
--
ALTER TABLE `equipment`
  MODIFY `equipment_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'รหัสครุภัณฑ์ (Primary Key)', AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `equipment_categories`
--
ALTER TABLE `equipment_categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'รหัสหมวดหมู่ (Primary Key)', AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `equipment_classroom`
--
ALTER TABLE `equipment_classroom`
  MODIFY `placement_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'รหัสการจัดวาง (Primary Key)';

--
-- AUTO_INCREMENT for table `equipment_disposals`
--
ALTER TABLE `equipment_disposals`
  MODIFY `disposal_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'รหัสการจำหน่าย (Primary Key)';

--
-- AUTO_INCREMENT for table `equipment_subcategories`
--
ALTER TABLE `equipment_subcategories`
  MODIFY `subcategory_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'รหัสหมวดหมู่ย่อย (Primary Key)', AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `equipment_transfers`
--
ALTER TABLE `equipment_transfers`
  MODIFY `transfer_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'รหัสการโอนย้าย (Primary Key)';

--
-- AUTO_INCREMENT for table `maintenance_logs`
--
ALTER TABLE `maintenance_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'รหัสบันทึก (Primary Key)';

--
-- AUTO_INCREMENT for table `maintenance_requests`
--
ALTER TABLE `maintenance_requests`
  MODIFY `maintenance_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'รหัสการซ่อม (Primary Key)', AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'รหัสผู้ใช้ระบบ (Primary Key)', AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `employees_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `equipment`
--
ALTER TABLE `equipment`
  ADD CONSTRAINT `equipment_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `equipment_categories` (`category_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `equipment_ibfk_2` FOREIGN KEY (`subcategory_id`) REFERENCES `equipment_subcategories` (`subcategory_id`) ON DELETE SET NULL;

--
-- Constraints for table `equipment_classroom`
--
ALTER TABLE `equipment_classroom`
  ADD CONSTRAINT `equipment_classroom_ibfk_1` FOREIGN KEY (`equipment_code`) REFERENCES `equipment` (`equipment_code`) ON DELETE CASCADE;

--
-- Constraints for table `equipment_disposals`
--
ALTER TABLE `equipment_disposals`
  ADD CONSTRAINT `equipment_disposals_ibfk_1` FOREIGN KEY (`equipment_id`) REFERENCES `equipment` (`equipment_id`) ON DELETE CASCADE;

--
-- Constraints for table `equipment_subcategories`
--
ALTER TABLE `equipment_subcategories`
  ADD CONSTRAINT `equipment_subcategories_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `equipment_categories` (`category_id`) ON DELETE CASCADE;

--
-- Constraints for table `equipment_transfers`
--
ALTER TABLE `equipment_transfers`
  ADD CONSTRAINT `equipment_transfers_ibfk_1` FOREIGN KEY (`equipment_id`) REFERENCES `equipment` (`equipment_id`) ON DELETE CASCADE;

--
-- Constraints for table `maintenance_logs`
--
ALTER TABLE `maintenance_logs`
  ADD CONSTRAINT `maintenance_logs_ibfk_1` FOREIGN KEY (`maintenance_id`) REFERENCES `maintenance_requests` (`maintenance_id`) ON DELETE CASCADE;

--
-- Constraints for table `maintenance_requests`
--
ALTER TABLE `maintenance_requests`
  ADD CONSTRAINT `maintenance_requests_ibfk_1` FOREIGN KEY (`equipment_id`) REFERENCES `equipment` (`equipment_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
