-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 30, 2025 at 01:06 AM
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
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `code` varchar(2) NOT NULL,
  `name` varchar(255) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `code`, `name`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, '01', 'อุปกรณ์คอมพิวเตอร์', 1, 1, '2025-10-27 03:02:40', '2025-10-27 03:02:40'),
(2, '02', 'อุปกรณ์เครือข่าย', 1, 2, '2025-10-27 03:02:40', '2025-10-27 03:02:40'),
(3, '03', 'อุปกรณ์สำนักงาน / ต่อพ่วง', 1, 3, '2025-10-27 03:02:40', '2025-10-27 03:02:40'),
(4, '04', 'อุปกรณ์สื่อการเรียนการสอน', 1, 4, '2025-10-27 03:02:40', '2025-10-27 03:02:40'),
(5, '05', 'อะไหล่ / ชิ้นส่วนคอมพิวเตอร์', 1, 5, '2025-10-27 03:02:40', '2025-10-27 03:02:40'),
(6, '06', 'ระบบรักษาความปลอดภัย', 1, 6, '2025-10-27 03:02:40', '2025-10-27 03:02:40'),
(7, '07', 'อุปกรณ์อิเล็กทรอนิกส์ทั่วไป', 1, 7, '2025-10-27 03:02:40', '2025-10-27 03:02:40'),
(8, '08', 'อุปกรณ์ซ่อมบำรุง / เครื่องมือ', 1, 8, '2025-10-27 03:02:40', '2025-10-27 03:02:40'),
(9, '09', 'ซอฟต์แวร์และใบอนุญาต', 1, 9, '2025-10-27 03:02:40', '2025-10-27 03:02:40'),
(10, '10', 'อื่น ๆ', 1, 10, '2025-10-27 03:02:40', '2025-10-27 03:02:40');

-- --------------------------------------------------------

--
-- Table structure for table `categories_items`
--

CREATE TABLE `categories_items` (
  `id` int(11) NOT NULL,
  `category_code` varchar(2) NOT NULL,
  `name` varchar(255) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories_items`
--

INSERT INTO `categories_items` (`id`, `category_code`, `name`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, '01', 'เครื่องคอมพิวเตอร์ตั้งโต๊ะ (PC)', 1, 1, '2025-10-27 03:02:40', '2025-10-27 03:02:40'),
(2, '01', 'เครื่องคอมพิวเตอร์โน้ตบุ๊ก (Notebook)', 1, 2, '2025-10-27 03:02:40', '2025-10-27 03:02:40'),
(3, '01', 'เครื่องคอมพิวเตอร์ All-in-One', 1, 3, '2025-10-27 03:02:40', '2025-10-27 03:02:40'),
(4, '01', 'จอภาพ (Monitor)', 1, 4, '2025-10-27 03:02:40', '2025-10-27 03:02:40'),
(5, '01', 'เครื่องสำรองไฟ (UPS)', 1, 5, '2025-10-27 03:02:40', '2025-10-27 03:02:40'),
(6, '01', 'คีย์บอร์ด (Keyboard)', 1, 6, '2025-10-27 03:02:40', '2025-10-27 03:02:40'),
(7, '01', 'เมาส์ (Mouse)', 1, 7, '2025-10-27 03:02:40', '2025-10-27 03:02:40'),
(8, '02', 'เราเตอร์ (Router)', 1, 1, '2025-10-27 03:02:40', '2025-10-27 03:02:40'),
(9, '02', 'สวิตช์ (Switch)', 1, 2, '2025-10-27 03:02:40', '2025-10-27 03:02:40'),
(10, '02', 'แอคเซสพอยต์ (Access Point)', 1, 3, '2025-10-27 03:02:40', '2025-10-27 03:02:40'),
(11, '02', 'โมเด็ม (Modem)', 1, 4, '2025-10-27 03:02:40', '2025-10-27 03:02:40'),
(12, '02', 'สายแลน (LAN Cable)', 1, 5, '2025-10-27 03:02:40', '2025-10-27 03:02:40'),
(13, '02', 'ตู้แร็ก (Rack Cabinet)', 1, 6, '2025-10-27 03:02:40', '2025-10-27 03:02:40'),
(14, '03', 'เครื่องพิมพ์ (Printer)', 1, 1, '2025-10-27 03:02:40', '2025-10-27 03:02:40'),
(15, '03', 'เครื่องสแกน (Scanner)', 1, 2, '2025-10-27 03:02:40', '2025-10-27 03:02:40'),
(16, '03', 'เครื่องฉายโปรเจกเตอร์ (Projector)', 1, 3, '2025-10-27 03:02:40', '2025-10-27 03:02:40'),
(17, '03', 'เครื่องถ่ายเอกสาร (Photocopier)', 1, 4, '2025-10-27 03:02:40', '2025-10-27 03:02:40'),
(18, '03', 'เครื่องฉายภาพ (Visual Presenter)', 1, 5, '2025-10-27 03:02:40', '2025-10-27 03:02:40'),
(19, '03', 'เครื่องเสียง (Sound System)', 1, 6, '2025-10-27 03:02:40', '2025-10-27 03:02:40'),
(20, '04', 'กระดานอัจฉริยะ (Smart Board)', 1, 1, '2025-10-27 03:02:40', '2025-10-27 03:02:40'),
(21, '04', 'ชุดคอมพิวเตอร์ห้องเรียน', 1, 2, '2025-10-27 03:02:40', '2025-10-27 03:02:40'),
(22, '04', 'แท็บเล็ต (Tablet)', 1, 3, '2025-10-27 03:02:40', '2025-10-27 03:02:40'),
(23, '04', 'เครื่องเสียงห้องเรียน', 1, 4, '2025-10-27 03:02:40', '2025-10-27 03:02:40'),
(24, '04', 'ชุดสื่อมัลติมีเดีย', 1, 5, '2025-10-27 03:02:40', '2025-10-27 03:02:40'),
(25, '05', 'ฮาร์ดดิสก์ (HDD)', 1, 1, '2025-10-27 03:02:40', '2025-10-27 03:02:40'),
(26, '05', 'สถานะ固态 (SSD)', 1, 2, '2025-10-27 03:02:40', '2025-10-27 03:02:40'),
(27, '05', 'แรม (RAM)', 1, 3, '2025-10-27 03:02:40', '2025-10-27 03:02:40'),
(28, '05', 'การ์ดจอ (Graphics Card)', 1, 4, '2025-10-27 03:02:40', '2025-10-27 03:02:40'),
(29, '05', 'เพาเวอร์ซัพพลาย (Power Supply)', 1, 5, '2025-10-27 03:02:40', '2025-10-27 03:02:40'),
(30, '05', 'อแดปเตอร์ (Adapter)', 1, 6, '2025-10-27 03:02:40', '2025-10-27 03:02:40'),
(31, '06', 'กล้องวงจรปิด (CCTV)', 1, 1, '2025-10-27 03:02:40', '2025-10-27 03:02:40'),
(32, '06', 'เครื่องบันทึกภาพ (NVR / DVR)', 1, 2, '2025-10-27 03:02:40', '2025-10-27 03:02:40'),
(33, '06', 'จอมอนิเตอร์สำหรับดูภาพ', 1, 3, '2025-10-27 03:02:40', '2025-10-27 03:02:40'),
(34, '06', 'เครื่องสแกนลายนิ้วมือ', 1, 4, '2025-10-27 03:02:40', '2025-10-27 03:02:40'),
(35, '06', 'เครื่องอ่านบัตร (Card Reader)', 1, 5, '2025-10-27 03:02:40', '2025-10-27 03:02:40'),
(36, '07', 'โทรทัศน์ (TV)', 1, 1, '2025-10-27 03:02:40', '2025-10-27 03:02:40'),
(37, '07', 'เครื่องเล่นดีวีดี (DVD Player)', 1, 2, '2025-10-27 03:02:40', '2025-10-27 03:02:40'),
(38, '07', 'ลำโพง (Speaker)', 1, 3, '2025-10-27 03:02:40', '2025-10-27 03:02:40'),
(39, '07', 'เครื่องขยายเสียง (Amplifier)', 1, 4, '2025-10-27 03:02:40', '2025-10-27 03:02:40'),
(40, '07', 'จานดาวเทียม (Satellite Dish)', 1, 5, '2025-10-27 03:02:40', '2025-10-27 03:02:40'),
(41, '08', 'เครื่องมือวัดไฟ (Multimeter)', 1, 1, '2025-10-27 03:02:40', '2025-10-27 03:02:40'),
(42, '08', 'เครื่องมือบัดกรี (Soldering Iron)', 1, 2, '2025-10-27 03:02:40', '2025-10-27 03:02:40'),
(43, '08', 'อุปกรณ์ตรวจสอบระบบ (Diagnostic Tools)', 1, 3, '2025-10-27 03:02:40', '2025-10-27 03:02:40'),
(44, '09', 'ระบบปฏิบัติการ Windows', 1, 1, '2025-10-27 03:02:40', '2025-10-27 03:02:40'),
(45, '09', 'Microsoft Office', 1, 2, '2025-10-27 03:02:40', '2025-10-27 03:02:40'),
(46, '09', 'โปรแกรมแอนตี้ไวรัส (Antivirus)', 1, 3, '2025-10-27 03:02:40', '2025-10-27 03:02:40'),
(47, '09', 'โปรแกรมสื่อการสอน', 1, 4, '2025-10-27 03:02:40', '2025-10-27 03:02:40'),
(48, '09', 'ลิขสิทธิ์ (License Key)', 1, 5, '2025-10-27 03:02:40', '2025-10-27 03:02:40'),
(49, '10', 'อุปกรณ์ที่ไม่เข้าพวกข้างต้น', 1, 1, '2025-10-27 03:02:40', '2025-10-27 03:02:40'),
(50, '10', 'ของทดลอง / ชุดทดลอง', 1, 2, '2025-10-27 03:02:40', '2025-10-27 03:02:40'),
(51, '10', 'ชุดทดลองหุ่นยนต์ (Robot Kit)', 1, 3, '2025-10-27 03:02:40', '2025-10-27 03:02:40'),
(52, '10', 'ชุดอุปกรณ์ IoT (IoT Kit)', 1, 4, '2025-10-27 03:02:40', '2025-10-27 03:02:40');

-- --------------------------------------------------------

--
-- Table structure for table `classroom_equipment`
--

CREATE TABLE `classroom_equipment` (
  `id` int(11) NOT NULL,
  `room_number` varchar(20) NOT NULL,
  `equipment_name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `equipment_condition` varchar(50) DEFAULT 'ดี',
  `description` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `school` varchar(255) NOT NULL,
  `building` varchar(255) NOT NULL,
  `floor` varchar(255) NOT NULL,
  `room` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` enum('อำนวยการ','อนุบาล','ประถม','มัธยม','สนับสนุน') NOT NULL,
  `responsible_person` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `school`, `building`, `floor`, `room`, `name`, `type`, `responsible_person`, `created_at`, `updated_at`) VALUES
(1, 'โรงเรียนวารีเชียงใหม่', 'ตึก1-อำนวยการ', 'ชั้น 1', '101', 'ฝ่ายบริหาร', 'อำนวยการ', 'ผู้บริหาร', '2025-10-28 04:47:15', '2025-10-28 04:47:15'),
(2, 'โรงเรียนวารีเชียงใหม่', 'ตึก3-ประถม', 'ชั้น 2', '201', 'ห้องเรียนประถมปีที่ 1', 'ประถม', 'ครูสมศรี', '2025-10-28 04:47:15', '2025-10-28 04:47:15'),
(3, 'โรงเรียนอนุบาลวารีเชียงใหม่', 'ตึก1-อำนวยการ', 'ชั้น 1', '102', 'ฝ่ายธุรการ', 'อำนวยการ', 'เจ้าหน้าที่ธุรการ', '2025-10-28 04:47:15', '2025-10-28 04:47:15'),
(4, 'โรงเรียนนานาชาติวารีเชียงใหม่', 'ตึก8', 'ชั้น 3', '301', 'ห้องวิทยาศาสตร์', 'มัธยม', 'ครูต่างชาติ', '2025-10-28 04:47:15', '2025-10-28 04:47:15'),
(5, 'โรงเรียนวารีเชียงใหม่', 'ตึก1-อำนวยการ', 'ชั้น 1', 'office', 'dawd', 'อำนวยการ', '', '2025-10-28 07:09:34', '2025-10-28 07:09:34'),
(6, 'โรงเรียนวารีเชียงใหม่', 'ตึก1-อำนวยการ', 'ชั้น 1', '101', 'ห้องผู้อำนวยการ', 'อำนวยการ', 'ผู้บริหาร', '2025-10-28 09:21:14', '2025-10-28 09:21:14'),
(7, 'โรงเรียนวารีเชียงใหม่', 'ตึก1-อำนวยการ', 'ชั้น 1', '102', 'ห้องธุรการ', 'อำนวยการ', 'เจ้าหน้าที่ธุรการ', '2025-10-28 09:21:14', '2025-10-28 09:21:14'),
(8, 'โรงเรียนวารีเชียงใหม่', 'ตึก3-ประถม', 'ชั้น 1', '201', 'ป.1/1', 'ประถม', 'ครูสมศรี', '2025-10-28 09:21:14', '2025-10-28 09:21:14'),
(9, 'โรงเรียนวารีเชียงใหม่', 'ตึก3-ประถม', 'ชั้น 1', '202', 'ป.1/2', 'ประถม', 'ครูวรรณา', '2025-10-28 09:21:14', '2025-10-28 09:21:14'),
(10, 'โรงเรียนวารีเชียงใหม่', 'ตึก5-อนุบาล', 'ชั้น 1', '501', 'อนุบาล 1/1', 'อนุบาล', 'ครูน้อย', '2025-10-28 09:21:14', '2025-10-28 09:21:14'),
(11, 'โรงเรียนวารีเชียงใหม่', 'ตึก5-อนุบาล', 'ชั้น 1', '502', 'อนุบาล 1/2', 'อนุบาล', 'ครูแดง', '2025-10-28 09:21:14', '2025-10-28 09:21:14'),
(12, 'โรงเรียนวารีเชียงใหม่', 'ตึก7-มัธยม', 'ชั้น 1', '701', 'ม.1/1', 'มัธยม', 'ครูใหญ่', '2025-10-28 09:21:14', '2025-10-28 09:21:14'),
(13, 'โรงเรียนวารีเชียงใหม่', 'ตึก7-มัธยม', 'ชั้น 1', '702', 'ม.1/2', 'มัธยม', 'ครูเล็ก', '2025-10-28 09:21:14', '2025-10-28 09:21:14'),
(14, 'โรงเรียนวารีเชียงใหม่', 'ตึก1-อำนวยการ', 'ชั้น 1', '103', 'ห้องพยาบาล', 'สนับสนุน', 'พยาบาลโรงเรียน', '2025-10-28 09:21:14', '2025-10-28 09:21:14'),
(15, 'โรงเรียนอนุบาลวารีเชียงใหม่', 'ตึก1-อำนวยการ', 'ชั้น 1', '101', 'ฝ่ายบริหาร', 'อำนวยการ', 'ผู้บริหาร', '2025-10-28 09:21:14', '2025-10-28 09:21:14'),
(16, 'โรงเรียนอนุบาลวารีเชียงใหม่', 'ตึก6', 'ชั้น 1', '601', 'อนุบาล 1', 'อนุบาล', 'ครูสมใจ', '2025-10-28 09:21:14', '2025-10-28 09:21:14'),
(17, 'โรงเรียนนานาชาติวารีเชียงใหม่', 'ตึก8', 'ชั้น 1', '801', 'Grade 1', 'ประถม', 'Teacher John', '2025-10-28 09:21:14', '2025-10-28 09:21:14'),
(18, 'โรงเรียนนานาชาติวารีเชียงใหม่', 'ตึก8', 'ชั้น 2', '811', 'Grade 3', 'ประถม', 'Teacher Mary', '2025-10-28 09:21:14', '2025-10-28 09:21:14'),
(19, 'โรงเรียนนานาชาติวารีเชียงใหม่', 'ตึก9', 'ชั้น 1', '901', 'Science Lab', 'มัธยม', 'Teacher David', '2025-10-28 09:21:14', '2025-10-28 09:21:14');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `employee_code` varchar(50) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `department_id` int(11) DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `employee_code`, `first_name`, `last_name`, `department_id`, `position`, `phone`, `email`, `created_at`) VALUES
(1, 'EMP001', 'สมชาย', 'ใจดี', 1, 'ผู้ดูแลระบบ', '081-234-5678', 'somchai@school.ac.th', '2025-10-27 03:02:41'),
(2, 'EMP002', 'สมหญิง', 'กล้าหาญ', 2, 'ครูใหญ่', '082-345-6789', 'somying@school.ac.th', '2025-10-27 03:02:41'),
(3, 'EMP003', 'ธีรภัทร', 'สุขใจ', 3, 'หัวหน้าแผนก', '083-456-7890', 'theeraphat@school.ac.th', '2025-10-27 03:02:41'),
(6, '6501005', 'จักรกฤษณ์', 'สุริยา', 1, 'ไอที', '0882510722', '', '2025-10-29 10:00:58');

-- --------------------------------------------------------

--
-- Table structure for table `equipment`
--

CREATE TABLE `equipment` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `category_item_id` int(11) DEFAULT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `model` varchar(100) DEFAULT NULL,
  `serial_number` varchar(100) DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `purchase_price` decimal(10,2) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `responsible_person` varchar(255) DEFAULT NULL,
  `equipment_status` varchar(50) NOT NULL DEFAULT 'อุปกรณ์ใหม่',
  `specifications` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `equipment`
--

INSERT INTO `equipment` (`id`, `code`, `name`, `category_id`, `category_item_id`, `brand`, `model`, `serial_number`, `purchase_date`, `purchase_price`, `department_id`, `responsible_person`, `equipment_status`, `specifications`, `image`, `created_at`, `updated_at`) VALUES
(1, 'IT-2023-0001', 'คอมพิวเตอร์ตั้งโต๊ะ Dell', 1, 1, 'Dell', 'OptiPlex 3090', 'SN001', '2023-01-15', 25000.00, 1, 'สมชาย ใจดี', 'อุปกรณ์เดิม', '', 'IT-2023-0001_1761550727.jpg', '2025-10-27 03:02:41', '2025-10-29 08:32:24'),
(2, 'IT-2023-0002', 'โน๊ตบุ๊ค Lenovo', 1, 2, 'Lenovo', 'ThinkPad X1', 'SN002', '2023-02-20', 35000.00, 2, 'สมหญิง กล้าหาญ', 'อุปกรณ์ใหม่', '', '', '2025-10-27 03:02:41', '2025-10-29 06:10:04'),
(3, 'IT-2023-0003', 'เครื่องพิมพ์ HP', 4, 20, 'HP', 'LaserJet Pro', 'SN003', '2023-03-10', 8000.00, 3, 'ธีรภัทร สุขใจ', 'อุปกรณ์ใหม่', '', '', '2025-10-27 03:02:41', '2025-10-29 05:52:16'),
(4, '0005', 'แรม', 5, 27, 'hyperX', '', '', '2025-10-27', 1000.00, 4, 'มอส', 'อุปกรณ์เดิม', '', '0005_1761556977.webp', '2025-10-27 09:22:57', '2025-10-29 05:51:55'),
(5, '0596', 'SSD', 5, 26, 'King', '', '', '2025-10-29', 900.00, 3, 'moss', 'อุปกรณ์ใหม่', '', '0596_SSD.webp', '2025-10-29 02:21:59', '2025-10-29 05:51:48'),
(6, '9560', 'ipad', 4, 22, 'Apple', '', '', '2025-10-28', 20000.00, 13, '', 'อุปกรณ์ใหม่', '', '9560_ipad.jpg', '2025-10-29 08:09:06', '2025-10-29 08:25:05');

-- --------------------------------------------------------

--
-- Table structure for table `maintenance`
--

CREATE TABLE `maintenance` (
  `id` int(11) NOT NULL,
  `equipment_id` int(11) NOT NULL,
  `report_date` date NOT NULL,
  `problem_description` text NOT NULL,
  `reported_by` varchar(255) NOT NULL,
  `assigned_technician` varchar(255) DEFAULT NULL,
  `cost` decimal(10,2) DEFAULT 0.00,
  `repair_status` varchar(50) NOT NULL DEFAULT 'รอซ่อม',
  `solution_description` text DEFAULT NULL,
  `completed_date` date DEFAULT NULL,
  `school_name` varchar(255) DEFAULT NULL,
  `building_name` varchar(255) DEFAULT NULL,
  `floor_name` varchar(255) DEFAULT NULL,
  `room_name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `maintenance`
--

INSERT INTO `maintenance` (`id`, `equipment_id`, `report_date`, `problem_description`, `reported_by`, `assigned_technician`, `cost`, `repair_status`, `solution_description`, `completed_date`, `school_name`, `building_name`, `floor_name`, `room_name`, `created_at`, `updated_at`) VALUES
(1, 4, '2025-10-29', 'เสีย', 'มอส', 'มอส', 0.00, 'ซ่อมเสร็จ', '', '2025-10-29', 'โรงเรียนวารีเชียงใหม่', 'ตึก1-อำนวยการ', 'ชั้น 1', 'ห้อง 101', '2025-10-29 01:34:23', '2025-10-29 01:38:07'),
(2, 1, '2025-10-29', 'เสีย', 'มอส', 'มอสส', 0.00, 'ซ่อมเสร็จ', '', '0000-00-00', 'โรงเรียนอนุบาลวารีเชียงใหม่', 'ตึก1-อำนวยการ', 'ชั้น 1', 'ห้อง 102', '2025-10-29 02:00:48', '2025-10-29 08:07:41'),
(3, 5, '2025-10-29', 'เปิดไม่ติด', 'มอส', 'มอส', 0.00, 'ซ่อมเสร็จ', '', '0000-00-00', 'โรงเรียนวารีเชียงใหม่', 'ตึก1-อำนวยการ', 'ชั้น 2', 'ห้องคอมพิวเตอร์', '2025-10-29 02:23:15', '2025-10-29 08:05:24'),
(4, 2, '2025-10-29', 'error', 'moss', 'moss', 0.00, 'ซ่อมเสร็จ', '', '0000-00-00', 'โรงเรียนนานาชาติวารีเชียงใหม่', 'ตึก9', 'ชั้น 2', 'ห้องคอมพิวเตอร์', '2025-10-29 03:06:21', '2025-10-29 03:06:44'),
(5, 3, '2025-10-29', 'ไกฟไก', 'moss', 'ทนหห', 0.00, 'ซ่อมเสร็จ', '', '0000-00-00', 'โรงเรียนอนุบาลวารีเชียงใหม่', 'ตึก1-อำนวยการ', 'ชั้น 1', 'ห้อง 102', '2025-10-29 03:12:32', '2025-10-29 03:30:51'),
(6, 2, '2025-10-29', 'จอเสีย', 'มอส', 'มอส', 0.00, 'ซ่อมเสร็จ', '', '0000-00-00', 'โรงเรียนอนุบาลวารีเชียงใหม่', 'ตึก1-อำนวยการ', 'ชั้น 2', 'ห้องปฏิบัติการวิทยาศาสตร์', '2025-10-29 06:09:32', '2025-10-29 07:51:02'),
(7, 6, '2025-10-29', 'ค้างหน้า logo', 'moss', '', 0.00, 'กำลังดำเนินการ', '', '0000-00-00', 'โรงเรียนนานาชาติวารีเชียงใหม่', 'ตึก9', 'ชั้น 2', 'ห้องสำนักงาน', '2025-10-29 08:10:11', '2025-10-29 14:03:55');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `role` enum('admin','user','viewer') DEFAULT 'user',
  `is_active` tinyint(1) DEFAULT 1,
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `employee_id`, `role`, `is_active`, `last_login`, `created_at`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 'admin', 1, '2025-10-28 05:37:00', '2025-10-27 03:02:41'),
(2, 'user1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, 'user', 1, NULL, '2025-10-27 03:02:41'),
(3, 'user2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'user', 1, NULL, '2025-10-27 03:02:41');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `categories_items`
--
ALTER TABLE `categories_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_code` (`category_code`);

--
-- Indexes for table `classroom_equipment`
--
ALTER TABLE `classroom_equipment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_code` (`employee_code`),
  ADD KEY `department_id` (`department_id`);

--
-- Indexes for table `equipment`
--
ALTER TABLE `equipment`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `department_id` (`department_id`),
  ADD KEY `category_item_id` (`category_item_id`);

--
-- Indexes for table `maintenance`
--
ALTER TABLE `maintenance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `equipment_id` (`equipment_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `employee_id` (`employee_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `categories_items`
--
ALTER TABLE `categories_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `classroom_equipment`
--
ALTER TABLE `classroom_equipment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `equipment`
--
ALTER TABLE `equipment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `maintenance`
--
ALTER TABLE `maintenance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `categories_items`
--
ALTER TABLE `categories_items`
  ADD CONSTRAINT `categories_items_ibfk_1` FOREIGN KEY (`category_code`) REFERENCES `categories` (`code`) ON DELETE CASCADE;

--
-- Constraints for table `classroom_equipment`
--
ALTER TABLE `classroom_equipment`
  ADD CONSTRAINT `classroom_equipment_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `employees_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`);

--
-- Constraints for table `equipment`
--
ALTER TABLE `equipment`
  ADD CONSTRAINT `equipment_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `equipment_ibfk_2` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`),
  ADD CONSTRAINT `equipment_ibfk_3` FOREIGN KEY (`category_item_id`) REFERENCES `categories_items` (`id`);

--
-- Constraints for table `maintenance`
--
ALTER TABLE `maintenance`
  ADD CONSTRAINT `maintenance_ibfk_1` FOREIGN KEY (`equipment_id`) REFERENCES `equipment` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
