-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 29, 2025 at 02:02 PM
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
-- Database: `asset_management`
--
CREATE DATABASE IF NOT EXISTS `asset_management` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `asset_management`;

-- --------------------------------------------------------

--
-- Table structure for table `assets`
--

CREATE TABLE `assets` (
  `id` int(11) NOT NULL,
  `asset_code` varchar(50) NOT NULL,
  `asset_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `machine_number` varchar(100) DEFAULT NULL,
  `category_id` int(11) NOT NULL,
  `purchase_date` date NOT NULL,
  `vendor` varchar(255) DEFAULT NULL,
  `price` decimal(15,2) NOT NULL,
  `department_id` int(11) NOT NULL,
  `responsible_person` varchar(100) NOT NULL,
  `status` enum('ใหม่','ใช้งานได้','ชำรุด','รอจำหน่าย','จำหน่ายแล้ว') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assets`
--

INSERT INTO `assets` (`id`, `asset_code`, `asset_name`, `description`, `machine_number`, `category_id`, `purchase_date`, `vendor`, `price`, `department_id`, `responsible_person`, `status`, `created_at`) VALUES
(1, '0010', 'pc', '', '578667', 1, '2025-10-24', 'jib', 15000.00, 4, 'มอส', 'ใช้งานได้', '2025-10-24 06:06:22');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `category_code` varchar(20) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `device_type` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `category_code`, `category_name`, `device_type`, `created_at`) VALUES
(1, 'COM001', 'คอมพิวเตอร์', 'อุปกรณ์ไอที', '2025-10-24 05:53:27'),
(2, 'NET001', 'เครือข่าย', 'อุปกรณ์เครือข่าย', '2025-10-24 05:53:27'),
(3, 'PRN001', 'เครื่องพิมพ์', 'อุปกรณ์สำนักงาน', '2025-10-24 05:53:27'),
(4, 'PC001', 'คอมพิวเตอร์', 'AIO', '2025-10-24 06:03:24');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `school` varchar(100) NOT NULL,
  `building` varchar(50) NOT NULL,
  `floor` int(11) NOT NULL,
  `room` varchar(50) NOT NULL,
  `division` varchar(100) NOT NULL,
  `responsible_person` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `school`, `building`, `floor`, `room`, `division`, `responsible_person`, `created_at`) VALUES
(1, 'วารี', 'office 3', 1, 'วิชาการ', 'อำนวยการ', 'สมชาย ใจดี', '2025-10-24 05:53:27'),
(2, 'วารี', 'office 4', 2, '311', 'มัธยม', 'สุนิตา เก่งเรียน', '2025-10-24 05:53:27'),
(3, 'นานาชาติ', 'office 5', 1, 'ฝ่ายบุคคล', 'อำนวยการ', 'John Smith', '2025-10-24 05:53:27'),
(4, 'นานาชาติ', 'office 8', 2, '8201', 'ประถม', 'มอส', '2025-10-24 06:04:25');

-- --------------------------------------------------------

--
-- Table structure for table `disposals`
--

CREATE TABLE `disposals` (
  `id` int(11) NOT NULL,
  `asset_id` int(11) NOT NULL,
  `disposal_date` date NOT NULL,
  `method` varchar(255) NOT NULL,
  `reason` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `maintenances`
--

CREATE TABLE `maintenances` (
  `id` int(11) NOT NULL,
  `asset_id` int(11) NOT NULL,
  `report_date` date NOT NULL,
  `issue` text NOT NULL,
  `technician` varchar(100) NOT NULL,
  `cost` decimal(15,2) DEFAULT NULL,
  `status` enum('รอซ่อม','กำลังดำเนินการ','ซ่อมเสร็จ') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `maintenances`
--

INSERT INTO `maintenances` (`id`, `asset_id`, `report_date`, `issue`, `technician`, `cost`, `status`, `created_at`) VALUES
(1, 1, '2025-10-24', 'ชำรุด', 'มอส', 0.00, 'ซ่อมเสร็จ', '2025-10-24 06:08:15');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `employee_id` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `department` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('Admin','Staff','Viewer') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `employee_id`, `name`, `department`, `username`, `password`, `role`, `created_at`) VALUES
(1, 'ADM001', 'ผู้ดูแลระบบ', 'อำนวยการ', 'admin', '$2y$10$NAQz1BnymRDusgHOS9DoI.LMUnZtymubxRgWinKMyfYk9i2KM6cjS', 'Admin', '2025-10-24 05:53:27'),
(2, '6501005', 'จักรกฤษณ์ สุริยา', 'ไอที', 'jukkrit', '$2y$10$5CFEN5gd55JUeVW7Tk392uAXFWVahEMwaabsXASUERDAejfnhujJS', 'Admin', '2025-10-24 06:05:21');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `assets`
--
ALTER TABLE `assets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `asset_code` (`asset_code`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `department_id` (`department_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `category_code` (`category_code`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `disposals`
--
ALTER TABLE `disposals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `asset_id` (`asset_id`);

--
-- Indexes for table `maintenances`
--
ALTER TABLE `maintenances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `asset_id` (`asset_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_id` (`employee_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `assets`
--
ALTER TABLE `assets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `disposals`
--
ALTER TABLE `disposals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `maintenances`
--
ALTER TABLE `maintenances`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `assets`
--
ALTER TABLE `assets`
  ADD CONSTRAINT `assets_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `assets_ibfk_2` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`);

--
-- Constraints for table `disposals`
--
ALTER TABLE `disposals`
  ADD CONSTRAINT `disposals_ibfk_1` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`);

--
-- Constraints for table `maintenances`
--
ALTER TABLE `maintenances`
  ADD CONSTRAINT `maintenances_ibfk_1` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`);
--
-- Database: `db_borrow`
--
CREATE DATABASE IF NOT EXISTS `db_borrow` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `db_borrow`;

-- --------------------------------------------------------

--
-- Table structure for table `apps_borrow`
--

CREATE TABLE `apps_borrow` (
  `id` int(11) NOT NULL,
  `borrow_no` varchar(20) NOT NULL COMMENT 'เลขที่ใบเบิก',
  `transaction_date` date NOT NULL COMMENT 'วันเวลาที่ทำรายการ',
  `borrower_id` int(11) NOT NULL COMMENT 'ผู้เบิก',
  `borrow_date` date NOT NULL COMMENT 'วันที่ต้องการเบิก',
  `return_date` date DEFAULT NULL COMMENT 'กำหนดคืน'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `apps_borrow`
--

INSERT INTO `apps_borrow` (`id`, `borrow_no`, `transaction_date`, `borrower_id`, `borrow_date`, `return_date`) VALUES
(2, 'B6810-0002', '2025-10-16', 1, '2025-10-16', NULL),
(3, 'B6810-0003', '2025-10-16', 1, '2025-10-16', NULL),
(4, 'B6810-0004', '2025-10-16', 3, '2025-10-16', '2025-10-16'),
(5, 'B6810-0005', '2025-10-16', 1, '2025-10-16', '2025-10-16');

-- --------------------------------------------------------

--
-- Table structure for table `apps_borrow_items`
--

CREATE TABLE `apps_borrow_items` (
  `id` int(11) NOT NULL,
  `borrow_id` int(11) NOT NULL,
  `topic` varchar(90) NOT NULL,
  `num_requests` int(11) NOT NULL,
  `amount` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(4) NOT NULL,
  `unit` varchar(50) DEFAULT NULL,
  `product_no` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `apps_borrow_items`
--

INSERT INTO `apps_borrow_items` (`id`, `borrow_id`, `topic`, `num_requests`, `amount`, `status`, `unit`, `product_no`) VALUES
(0, 2, 'ram ddr4', 2, 0, 2, 'อัน', 'ี6545123'),
(0, 3, 'ASUS A550JX', 1, 0, 2, 'กล่อง', '0000-0001'),
(0, 4, 'ram ddr4', 2, 0, 2, 'อัน', 'ี6545123'),
(0, 5, 'จอมอนิเตอร์ ACER S220HQLEBD', 2, 2, 2, 'กล่อง', '1108-365D');

-- --------------------------------------------------------

--
-- Table structure for table `apps_category`
--

CREATE TABLE `apps_category` (
  `type` varchar(20) NOT NULL,
  `category_id` varchar(10) DEFAULT '0',
  `language` varchar(2) DEFAULT '',
  `topic` varchar(150) NOT NULL,
  `color` varchar(16) DEFAULT NULL,
  `published` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `apps_category`
--

INSERT INTO `apps_category` (`type`, `category_id`, `language`, `topic`, `color`, `published`) VALUES
('model_id', '2', '', 'Asus', '', 1),
('type_id', '3', '', 'โปรเจ็คเตอร์', '', 1),
('type_id', '2', '', 'เครื่องพิมพ์', '', 1),
('model_id', '3', '', 'Cannon', '', 1),
('category_id', '2', '', 'วัสดุสำนักงาน', NULL, 1),
('model_id', '1', '', 'Apple', '', 1),
('type_id', '1', '', 'เครื่องคอมพิวเตอร์', '', 1),
('model_id', '4', '', 'ACER', '', 1),
('type_id', '4', '', 'จอมอนิเตอร์', '', 1),
('category_id', '1', '', 'เครื่องใช้ไฟฟ้า', NULL, 1),
('category_id', '3', '', 'Ram', NULL, 1),
('category_id', '4', '', 'คอมพิวเตอร์', NULL, 1),
('unit', '1', '', 'อัน', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `apps_inventory`
--

CREATE TABLE `apps_inventory` (
  `id` int(11) NOT NULL,
  `category_id` varchar(10) DEFAULT NULL,
  `model_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `topic` varchar(64) NOT NULL,
  `inuse` tinyint(1) DEFAULT 1,
  `count_stock` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `apps_inventory`
--

INSERT INTO `apps_inventory` (`id`, `category_id`, `model_id`, `type_id`, `topic`, `inuse`, `count_stock`) VALUES
(1, '1', 4, 4, 'จอมอนิเตอร์ ACER S220HQLEBD', 1, 1),
(2, '1', 2, 1, 'ASUS A550JX', 1, 1),
(3, '3', 4, 1, 'Crucial 4GB DDR3L&amp;1600 SODIMM', 1, 1),
(4, '4', 4, 1, 'ram ddr4', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `apps_inventory_items`
--

CREATE TABLE `apps_inventory_items` (
  `product_no` varchar(150) NOT NULL,
  `inventory_id` int(11) NOT NULL,
  `unit` varchar(50) DEFAULT NULL,
  `stock` float NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `apps_inventory_items`
--

INSERT INTO `apps_inventory_items` (`product_no`, `inventory_id`, `unit`, `stock`) VALUES
('0000-0001', 2, 'กล่อง', 5),
('1108-365D', 1, 'กล่อง', 3),
('IF11-0001', 3, 'อัน', 5),
('ี6545123', 4, 'อัน', 10);

-- --------------------------------------------------------

--
-- Table structure for table `apps_inventory_meta`
--

CREATE TABLE `apps_inventory_meta` (
  `inventory_id` int(11) NOT NULL,
  `name` varchar(20) NOT NULL,
  `value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `apps_inventory_meta`
--

INSERT INTO `apps_inventory_meta` (`inventory_id`, `name`, `value`) VALUES
(1, 'detail', 'โทรศัพท์ใช้ภายในสำนักงาน ห้ามติตั้งโปรแกรมเพิ่มเติม'),
(3, 'detail', 'สว่านเจาะกระแทก โรตารี่ 10 หุน');

-- --------------------------------------------------------

--
-- Table structure for table `apps_language`
--

CREATE TABLE `apps_language` (
  `id` int(11) NOT NULL,
  `key` text NOT NULL,
  `type` varchar(5) NOT NULL,
  `owner` varchar(20) NOT NULL,
  `js` tinyint(1) NOT NULL,
  `th` text DEFAULT NULL,
  `en` text DEFAULT NULL,
  `la` text CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `apps_language`
--

INSERT INTO `apps_language` (`id`, `key`, `type`, `owner`, `js`, `th`, `en`, `la`) VALUES
(1, 'ACCEPT_ALL', 'text', 'index', 1, 'ยอมรับทั้งหมด', 'Accept all', 'ຍອມຮັບທັງຫມົດ'),
(2, 'ADD', 'text', 'index', 1, 'เพิ่ม', 'Add', 'ເພີ່ມ​ຂຶ້ນ'),
(3, 'CANCEL', 'text', 'index', 1, 'ยกเลิก', 'Cancel', 'ຍົກເລີກ'),
(4, 'CHANGE_COLOR', 'text', 'index', 1, 'เปลี่ยนสี', 'change color', 'ປ່ຽນສີ'),
(5, 'CHECK', 'text', 'index', 1, 'เลือก', 'check', 'ເລືອກ'),
(6, 'CHECKBOX', 'text', 'index', 1, 'ตัวเลือก', 'Checkbox', 'ກ່ອງກາເຄື່ອງໝາຍ'),
(7, 'COOKIES_SETTINGS', 'text', 'index', 1, 'ตั้งค่าคุกกี้', 'Cookies settings', 'ຕັ້ງຄ່າຄຸກກີ'),
(8, 'DELETE', 'text', 'index', 1, 'ลบ', 'delete', 'ລຶບ'),
(9, 'DISABLE', 'text', 'index', 1, 'ปิดใช้งาน', 'Disable', 'ປິດໃຊ້ການ'),
(10, 'DRAG_AND_DROP_TO_REORDER', 'text', 'index', 1, 'ลากและวางเพื่อจัดลำดับใหม่', 'Drag and drop to reorder', 'ລາກແລ້ວວາງລົງເພື່ອຈັດຮຽງໃໝ່'),
(11, 'ENABLE', 'text', 'index', 1, 'เปิดใช้งาน', 'Enable', 'ເປີດໃຊ້ການ'),
(12, 'GO_TO_PAGE', 'text', 'index', 1, 'ไปหน้าที่', 'go to page', 'ໄປ​ຫນ້າ​ທີ່​'),
(13, 'INVALID_DATA', 'text', 'index', 1, 'ข้อมูล XXX ไม่ถูกต้อง', 'XXX Invalid data', 'ຂໍ້ມູນ XXX ບໍ່ຖືກຕ້ອງ'),
(14, 'ITEM', 'text', 'index', 1, 'รายการ', 'item', 'ລາຍການ'),
(15, 'ITEMS', 'text', 'index', 1, 'รายการ', 'items', 'ລາຍການ'),
(16, 'NEXT_MONTH', 'text', 'index', 1, 'เดือนถัดไป', 'Next Month', 'ເດືອນຕໍ່ໄປ'),
(17, 'PLEASE_BROWSE_FILE', 'text', 'index', 1, 'กรุณาเลือกไฟล์', 'Please browse file', 'ກະລຸນາເລືອກແຟ້ມຂໍ້ມູນ'),
(18, 'PLEASE_FILL_IN', 'text', 'index', 1, 'กรุณากรอก', 'Please fill in', 'ກະລຸນາພີ່ມ'),
(19, 'PLEASE_SAVE_BEFORE_CONTINUING', 'text', 'index', 1, 'กรุณาบันทึก ก่อนดำเนินการต่อ', 'Please save before continuing', 'ກະລຸນາບັນທຶກກ່ອນດຳເນີນການຕໍ່'),
(20, 'PLEASE_SELECT', 'text', 'index', 1, 'กรุณาเลือก', 'Please select', 'ກະລຸນາເລືອກ'),
(21, 'PLEASE_SELECT_AT_LEAST_ONE_ITEM', 'text', 'index', 1, 'กรุณาเลือก XXX อย่างน้อย 1 รายการ', 'Please select XXX at least one item', 'ກະລຸນາເລືອກ XXX ຢ່າງໜ້ອຍໜຶ່ງລາຍການ'),
(22, 'PREV_MONTH', 'text', 'index', 1, 'เดือนก่อนหน้า', 'Prev Month', 'ເດືອນທີ່ຜ່ານມາ'),
(23, 'SELECT_ALL', 'text', 'index', 1, 'เลือกทั้งหมด', 'select all', 'ເລືອກທັງໝົດ'),
(24, 'SELECT_NONE', 'text', 'index', 1, 'ไม่เลือกรายการใดเลย', 'select none', 'ບໍ່ເລືອກລາຍການໃດເລີຍ'),
(25, 'SHOWING_PAGE', 'text', 'index', 1, 'กำลังแสดงหน้าที่', 'showing page', 'ສະແດງໜ້າທີ່'),
(26, 'SORRY_XXX_NOT_FOUND', 'text', 'index', 1, 'ขออภัย ไม่พบ XXX ที่ต้องการ', 'Sorry XXX not found', 'ຂໍອະໄພບໍ່ພົບ XXX ທີ່ຕ້ອງການ'),
(27, 'SUCCESSFULLY_COPIED_TO_CLIPBOARD', 'text', 'index', 1, 'สำเนาไปยังคลิปบอร์ดเรียบร้อย', 'Successfully copied to clipboard', 'ສຳເນົາໄປຍັງຄິບບອດຮຽບຮ້ອຍ'),
(28, 'SUCCESSFULLY_UPLOADED_XXX_FILES', 'text', 'index', 1, 'อัปโหลดเรียบร้อย XXX ไฟล์', 'Successfully uploaded XXX files', 'ອັບໂຫຼດຮຽບຮ້ອຍ XXX ແຟ້ມ'),
(29, 'THE_TYPE_OF_FILE_IS_INVALID', 'text', 'index', 1, 'ชนิดของไฟล์ไม่ถูกต้อง', 'The type of file is invalid', 'ຊະນິດຂອງແຟ້ມບໍ່ຖືກຕ້ອງ'),
(30, 'UNCHECK', 'text', 'index', 1, 'ไม่เลือก', 'uncheck', 'ບໍ່ເລືອກ'),
(31, 'YOU_WANT_TO_XXX', 'text', 'index', 1, 'คุณต้องการ XXX ?', 'You want to XXX ?', 'ທ່ານບໍ່ຕ້ອງການ XXX ?'),
(32, 'YOU_WANT_TO_XXX_THE_SELECTED_ITEMS', 'text', 'index', 1, 'คุณต้องการ XXX รายการที่เลือก ?', 'You want to XXX the selected items ?', 'ທ່ານຕ້ອງການ XXX ລາຍການທີ່ເລືອກ?'),
(33, 'BOOLEANS', 'array', 'index', 0, 'a:2:{i:0;s:27:\"ปิดใช้งาน\";i:1;s:30:\"เปิดใช้งาน\";}', 'a:2:{i:0;s:7:\"Disable\";i:1;s:7:\"Enabled\";}', 'a:2:{i:0;s:27:\"ປິດໃຊ້ວຽກ\";i:1;s:30:\"ເປີດໃຊ້ວຽກ\";}'),
(34, 'BORROW_STATUS', 'array', 'index', 0, 'a:4:{i:0;s:27:\"รอตรวจสอบ\";i:1;s:30:\"ไม่อนุมัติ\";i:2;s:21:\"อนุมัติ\";i:3;s:21:\"คืนแล้ว\";}', 'a:4:{i:0;s:7:\"Pending\";i:1;s:11:\"Not allowed\";i:2;s:7:\"Approve\";i:3;s:8:\"Returned\";}', 'a:4:{i:0;s:15:\"ລໍຖ້າ\";i:1;s:30:\"ບໍ່ອະນຸຍາດ\";i:2;s:21:\"ອະນຸມັດ\";i:3;s:18:\"ກັບຄືນ\";}'),
(35, 'DATE_FORMAT', 'text', 'index', 0, 'd M Y เวลา H:i น.', 'd M Y, H:i', 'd M Y ເວລາ H:i'),
(36, 'DATE_LONG', 'array', 'index', 0, 'a:7:{i:0;s:21:\"อาทิตย์\";i:1;s:18:\"จันทร์\";i:2;s:18:\"อังคาร\";i:3;s:9:\"พุธ\";i:4;s:24:\"พฤหัสบดี\";i:5;s:15:\"ศุกร์\";i:6;s:15:\"เสาร์\";}', 'a:7:{i:0;s:6:\"Sunday\";i:1;s:6:\"Monday\";i:2;s:7:\"Tuesday\";i:3;s:9:\"Wednesday\";i:4;s:8:\"Thursday\";i:5;s:6:\"Friday\";i:6;s:8:\"Saturday\";}', 'a:7:{i:0;s:15:\"ອາທິດ\";i:1;s:9:\"ຈັນ\";i:2;s:18:\"ອັງຄານ\";i:3;s:9:\"ພຸດ\";i:4;s:15:\"ພະຫັດ\";i:5;s:9:\"ສຸກ\";i:6;s:12:\"ເສົາ\";}'),
(37, 'DATE_SHORT', 'array', 'index', 0, 'a:7:{i:0;s:7:\"อา.\";i:1;s:4:\"จ.\";i:2;s:4:\"อ.\";i:3;s:4:\"พ.\";i:4;s:7:\"พฤ.\";i:5;s:4:\"ศ.\";i:6;s:4:\"ส.\";}', 'a:7:{i:0;s:2:\"Su\";i:1;s:2:\"Mo\";i:2;s:2:\"Tu\";i:3;s:2:\"We\";i:4;s:2:\"Th\";i:5;s:2:\"Fr\";i:6;s:2:\"Sa\";}', 'a:7:{i:0;s:4:\"ທ.\";i:1;s:4:\"ຈ.\";i:2;s:4:\"ຄ.\";i:3;s:4:\"ພ.\";i:4;s:4:\"ພ.\";i:5;s:4:\"ສ.\";i:6;s:4:\"ສ.\";}'),
(38, 'Inventory', 'text', 'index', 0, 'คลังสินค้า', 'Inventory', 'ຄັງສິນຄ້າ'),
(39, 'INVENTORY_CATEGORIES', 'array', 'index', 0, 'a:3:{s:11:\"category_id\";s:24:\"หมวดหมู่\";s:7:\"type_id\";s:18:\"ประเภท\";s:8:\"model_id\";s:18:\"ยี่ห้อ\";}', 'a:3:{s:11:\"category_id\";s:8:\"Category\";s:7:\"type_id\";s:4:\"Type\";s:8:\"model_id\";s:5:\"Model\";}', 'a:3:{s:11:\"category_id\";s:24:\"ຫມວດຫມູ່\";s:7:\"type_id\";s:15:\"ປະເພດ\";s:8:\"model_id\";s:18:\"ຍີ່ຫໍ້\";}'),
(40, 'INVENTORY_METAS', 'array', 'index', 0, 'a:1:{s:6:\"detail\";s:30:\"รายละเอียด\";}', 'a:1:{s:6:\"detail\";s:6:\"Detail\";}', 'a:1:{s:6:\"detail\";s:27:\"ລາຍະລະອຽດ\";}'),
(41, 'INVENTORY_STATUS', 'array', 'index', 0, 'a:2:{i:0;s:42:\"เลิกใช้งานแล้ว\";i:1;s:30:\"ใช้งานอยู่\";}', 'a:2:{i:0;s:8:\"Inactive\";i:1;s:6:\"Active\";}', 'a:2:{i:0;s:36:\"ປິດການໃຊ້ວຽກ\";i:1;s:30:\"ຍັງເຮັດວຽກ\";}'),
(42, 'LINE_FOLLOW_MESSAGE', 'text', 'index', 0, 'สวัสดี คุณ :name นี่คือบัญชีทางการของ :title เราจะส่งข่าวสารถึงคุณผ่านช่องทางนี้', 'Hello, :name This is :title official account. We will send you news via this channel.', 'ສະບາຍດີ, :name ນີ້ແມ່ນບັນຊີທາງການຂອງ :title ພວກເຮົາຈະສົ່ງຂ່າວໃຫ້ທ່ານຜ່ານຊ່ອງທາງນີ້.'),
(43, 'LINE_REPLY_MESSAGE', 'text', 'index', 0, 'ขออภัยไม่สามารถตอบกลับข้อความนี้ได้', 'Sorry, can&#039;t reply to this message.', 'ຂໍອະໄພ, ບໍ່ສາມາດຕອບກັບຂໍ້ຄວາມນີ້ໄດ້.'),
(44, 'LOGIN_FIELDS', 'array', 'index', 0, 'a:4:{s:8:\"username\";s:30:\"ชื่อผู้ใช้\";s:5:\"email\";s:15:\"อีเมล\";s:5:\"phone\";s:39:\"เบอร์โทรศัพท์\";s:7:\"id_card\";s:30:\"เลขประชาชน\";}', 'a:4:{s:8:\"username\";s:8:\"Username\";s:5:\"email\";s:5:\"Email\";s:5:\"phone\";s:5:\"Phone\";s:7:\"id_card\";s:18:\"Identification No.\";}', 'a:4:{s:8:\"username\";s:27:\"ຊື່ຜູ້ໃຊ້\";s:5:\"email\";s:15:\"ອີເມວ\";s:5:\"phone\";s:30:\"ເບີໂທລະສັບ\";s:7:\"id_card\";s:39:\"ເລກບັດປະຈຳຕົວ\";}'),
(45, 'MAIL_PROGRAMS', 'array', 'index', 0, 'a:3:{i:0;s:43:\"ส่งจดหมายด้วย PHP\";i:1;s:72:\"ส่งจดหมายด้วย PHPMailer+SMTP (แนะนำ)\";i:2;s:58:\"ส่งจดหมายด้วย PHPMailer+PHP Mail\";}', 'a:3:{i:0;s:13:\"Send with PHP\";i:1;s:38:\"Send with PHPMailer+SMTP (recommended)\";i:2;s:28:\"Send with PHPMailer+PHP Mail\";}', 'a:3:{i:0;s:46:\"ສົ່ງຈົດໝາຍດ້ວຍ PHP\";i:1;s:75:\"ສົ່ງຈົດໝາຍດ້ວຍ PHPMailer+SMTP (ແນະນຳ)\";i:2;s:61:\"ສົ່ງຈົດໝາຍດ້ວຍ PHPMailer+PHP Mail\";}'),
(46, 'MONTH_LONG', 'array', 'index', 0, 'a:12:{i:1;s:18:\"มกราคม\";i:2;s:30:\"กุมภาพันธ์\";i:3;s:18:\"มีนาคม\";i:4;s:18:\"เมษายน\";i:5;s:21:\"พฤษภาคม\";i:6;s:24:\"มิถุนายน\";i:7;s:21:\"กรกฎาคม\";i:8;s:21:\"สิงหาคม\";i:9;s:21:\"กันยายน\";i:10;s:18:\"ตุลาคม\";i:11;s:27:\"พฤศจิกายน\";i:12;s:21:\"ธันวาคม\";}', 'a:12:{i:1;s:7:\"January\";i:2;s:8:\"February\";i:3;s:5:\"March\";i:4;s:5:\"April\";i:5;s:3:\"May\";i:6;s:4:\"June\";i:7;s:4:\"July\";i:8;s:6:\"August\";i:9;s:9:\"September\";i:10;s:7:\"October\";i:11;s:8:\"November\";i:12;s:8:\"December\";}', 'a:12:{i:1;s:18:\"ມັງກອນ\";i:2;s:15:\"ກຸມພາ\";i:3;s:12:\"ມີນາ\";i:4;s:12:\"ເມສາ\";i:5;s:21:\"ພຶດສະພາ\";i:6;s:18:\"ມິຖຸນາ\";i:7;s:21:\"ກໍລະກົດ\";i:8;s:15:\"ສິງຫາ\";i:9;s:15:\"ກັນຍາ\";i:10;s:12:\"ຕຸລາ\";i:11;s:15:\"ພະຈິກ\";i:12;s:15:\"ທັນວາ\";}'),
(47, 'MONTH_SHORT', 'array', 'index', 0, 'a:12:{i:1;s:8:\"ม.ค.\";i:2;s:8:\"ก.พ.\";i:3;s:11:\"มี.ค.\";i:4;s:11:\"เม.ย.\";i:5;s:8:\"พ.ค.\";i:6;s:11:\"มิ.ย.\";i:7;s:8:\"ก.ค.\";i:8;s:8:\"ส.ค.\";i:9;s:8:\"ก.ย.\";i:10;s:8:\"ต.ค.\";i:11;s:8:\"พ.ย.\";i:12;s:8:\"ธ.ค.\";}', 'a:12:{i:1;s:3:\"Jan\";i:2;s:3:\"Feb\";i:3;s:3:\"Mar\";i:4;s:3:\"Apr\";i:5;s:3:\"May\";i:6;s:3:\"Jun\";i:7;s:3:\"Jul\";i:8;s:3:\"Aug\";i:9;s:3:\"Sep\";i:10;s:3:\"Oct\";i:11;s:3:\"Nov\";i:12;s:3:\"Dec\";}', 'a:12:{i:1;s:8:\"ມ.ກ.\";i:2;s:8:\"ກ.ພ.\";i:3;s:11:\"ມີ.ນ.\";i:4;s:11:\"ເມ.ສ.\";i:5;s:8:\"ພ.ພ.\";i:6;s:11:\"ມິ.ນ.\";i:7;s:8:\"ກ.ກ.\";i:8;s:8:\"ສ.ຫ.\";i:9;s:8:\"ກ.ຍ.\";i:10;s:8:\"ຕ.ລ.\";i:11;s:8:\"ພ.ຈ.\";i:12;s:8:\"ທ.ວ.\";}'),
(48, 'Name', 'text', 'index', 0, 'ชื่อ นามสกุล', 'Name Surname', 'ຊື່ ນາມສະກຸນ'),
(49, 'PERMISSIONS', 'array', 'index', 0, 'a:2:{s:10:\"can_config\";s:60:\"สามารถตั้งค่าระบบได้\";s:22:\"can_view_usage_history\";s:93:\"สามารถดูประวัติการใช้งานระบบได้\";}', 'a:2:{s:10:\"can_config\";s:24:\"Can configure the system\";s:22:\"can_view_usage_history\";s:33:\"Able to view system usage history\";}', 'a:2:{s:10:\"can_config\";s:60:\"ສາມາດຕັ້ງຄ່າລະບົບໄດ້\";s:22:\"can_view_usage_history\";s:90:\"ສາມາດເບິ່ງປະຫວັດການນໍາໃຊ້ລະບົບ\";}'),
(50, 'PUBLISHEDS', 'array', 'index', 0, 'a:2:{i:0;s:45:\"ระงับการเผยแพร่\";i:1;s:21:\"เผยแพร่\";}', 'a:2:{i:0;s:11:\"Unpublished\";i:1;s:9:\"Published\";}', 'a:2:{i:0;s:45:\"ລະງັບການເຜີຍແຜ່\";i:1;s:21:\"ເຜີຍແຜ່\";}'),
(51, 'SEXES', 'array', 'index', 0, 'a:3:{s:1:\"u\";s:21:\"ไม่ระบุ\";s:1:\"f\";s:12:\"หญิง\";s:1:\"m\";s:9:\"ชาย\";}', 'a:3:{s:1:\"u\";s:13:\"Not specified\";s:1:\"f\";s:6:\"Female\";s:1:\"m\";s:4:\"Male\";}', 'a:3:{s:1:\"u\";s:30:\"ບໍ່ໄດ້ລະບຸ\";s:1:\"f\";s:9:\"ຍິງ\";s:1:\"m\";s:9:\"ຊາຍ\";}'),
(52, 'SMS_SENDER_COMMENT', 'text', 'index', 0, 'บาง Package อาจไม่สามารถกำหนดชื่อผู้ส่งได้ กรุณาตรวจสอบกับผู้ให้บริการ', 'Some packages may not be able to assign the sender name. Please check with the service provider.', 'ບາງແພັກເກັດອາດບໍ່ສາມາດມອບຊື່ຜູ້ສົ່ງໄດ້. ກະລຸນາກວດສອບກັບຜູ້ໃຫ້ບໍລິການ.'),
(53, 'SMTPSECURIES', 'array', 'index', 0, 'a:2:{s:0:\"\";s:57:\"การเชื่อมต่อแบบปกติ\";s:3:\"ssl\";s:72:\"การเชื่อมต่อที่ปลอดภัย (SSL)\";}', 'a:2:{s:0:\"\";s:10:\"Clear Text\";s:3:\"ssl\";s:38:\"Server using a secure connection (SSL)\";}', 'a:2:{s:0:\"\";s:66:\"ການເຊື່ອມຕໍ່ແບບປົກກະຕິ\";s:3:\"ssl\";s:66:\"ການເຊື່ອມຕໍ່ທີ່ປອດໄຟ (SSL)\";}'),
(54, 'THEME_WIDTH', 'array', 'index', 0, 'a:3:{s:7:\"default\";s:33:\"ค่าเริ่มต้น\";s:4:\"wide\";s:15:\"กว้าง\";s:9:\"fullwidth\";s:30:\"กว้างพิเศษ\";}', 'a:3:{s:7:\"default\";s:7:\"Default\";s:4:\"wide\";s:4:\"Wide\";s:9:\"fullwidth\";s:10:\"Extra wide\";}', 'a:3:{s:7:\"default\";s:36:\"ຄ່າເລີ່ມຕົ້ນ\";s:4:\"wide\";s:15:\"ກວ້າງ\";s:9:\"fullwidth\";s:30:\"ກວ້າງພິເສດ\";}'),
(55, 'TIME_FORMAT', 'text', 'index', 0, 'H:i น.', 'H:i', 'H:i'),
(56, 'YEAR_OFFSET', 'int', 'index', 0, '543', '0', '0'),
(57, ':name for new members Used when members need to specify', 'text', 'index', 0, ':name เริ่มต้นสำหรับสมาชิกใหม่ ใช้ในกรณีที่สมาชิกจำเป็นต้องระบุ', NULL, ':name ສໍາລັບສະມາຊິກໃຫມ່ ໃຊ້ໃນເວລາທີ່ສະມາຊິກຕ້ອງການກໍານົດ'),
(58, '0.0.0.0 mean all IP addresses', 'text', 'index', 0, '0.0.0.0 หมายถึงอนุญาตทั้งหมด', NULL, '0.0.0.0 ຫມາຍຄວາມວ່າອະນຸຍາດໃຫ້ທັງຫມົດ'),
(59, 'Accept all', 'text', 'index', 0, 'ยอมรับทั้งหมด', NULL, 'ຍອມຮັບທັງຫມົດ'),
(60, 'Accept member verification request', 'text', 'index', 0, 'ยอมรับคำขอยืนยันสมาชิก', NULL, 'ຍອມຮັບການຮ້ອງຂໍການຢັ້ງຢືນສະມາຊິກ'),
(61, 'Accept this agreement', 'text', 'index', 0, 'ยอมรับข้อตกลง', NULL, 'ຍອມຮັບຂໍ້ຕົກລົງ'),
(62, 'Add', 'text', 'index', 0, 'เพิ่ม', NULL, 'ເພີ່ມ'),
(63, 'Add Borrow', 'text', 'index', 0, 'ทำรายการยืม', NULL, 'ເຮັດລາຍະການຍືມ'),
(64, 'Add friend', 'text', 'index', 0, 'เพิ่มเพื่อน', NULL, 'ເພີ່ມເພື່ອນ'),
(65, 'Address', 'text', 'index', 0, 'ที่อยู่', NULL, 'ທີ່ຢູ່'),
(66, 'Address details', 'text', 'index', 0, 'รายละเอียดที่อยู่', NULL, 'ລາຍລະອຽດທີ່ຢູ່'),
(67, 'Administrator status It is of utmost importance to do everything', 'text', 'index', 0, 'สถานะผู้ดูแลระบบ มีความสำคัญสูงสุดสามารถทำได้ทุกอย่าง', NULL, 'ສະຖານະຜູ້ເບິ່ງແຍງລະບົບມີຄວາມສຳຄັນສຸງທີ່ສຸດສາມາດເຮັດໄດ້ທຸກຢ່າງ'),
(68, 'age', 'text', 'index', 0, 'อายุ', NULL, 'ອາຍຸ'),
(69, 'All :count entries, displayed :start to :end, page :page of :total pages', 'text', 'index', 0, 'ทั้งหมด :count รายการ, แสดง :start ถึง :end, หน้าที่ :page จากทั้งหมด :total หน้า', NULL, 'ທັງໝົດ :count ລາຍການ, ສະແດງ :start ເຖິງ :end, ໜ້າທີ່ :page ຈາກທັງໝົດ:total ໜ້າ'),
(70, 'all items', 'text', 'index', 0, 'ทั้งหมด', NULL, 'ທັງໝົດ'),
(71, 'Already registered The system has sent an OTP code to the number you registered. Please check the SMS and enter the code to confirm your phone number.', 'text', 'index', 0, 'ลงทะเบียนเรียบร้อยแล้ว ระบบได้ส่งรหัส OTP ไปยังเบอร์ที่ท่านได้ลงทะเบียนไว้ กรุณาตรวจสอบ SMS แล้วให้นำรหัสมากรอกเพื่อเป็นการยืนยันเบอร์โทรศัพท์', NULL, 'ລົງ​ທະ​ບຽນ​ແລ້ວ ລະບົບໄດ້ສົ່ງລະຫັດ OTP ໄປຫາເບີໂທລະສັບທີ່ທ່ານລົງທະບຽນ, ກະລຸນາກວດເບິ່ງ SMS ແລະໃສ່ລະຫັດເພື່ອຢືນຢັນເບີໂທລະສັບຂອງທ່ານ.'),
(72, 'Always enabled', 'text', 'index', 0, 'เปิดใช้งานตลอดเวลา', NULL, 'ເປີດສະເໝີ'),
(73, 'anyone', 'text', 'index', 0, 'ใครก็ได้', NULL, 'ໃຜ'),
(74, 'API settings', 'text', 'index', 0, 'ตั้งค่า API', NULL, 'ຕັ້ງຄ່າ API'),
(75, 'Appointment date', 'text', 'index', 0, 'วันนัดรับ', NULL, 'ວັນນັດຫມາຍ'),
(76, 'Appraiser', 'text', 'index', 0, 'ประเมินราคา', NULL, 'ຕີລາຄາ'),
(77, 'Authentication require', 'text', 'index', 0, 'การตรวจสอบความถูกต้อง', NULL, 'ການກວດກາຄວາມຖືກຕ້ອງ'),
(78, 'Avatar', 'text', 'index', 0, 'รูปสมาชิก', NULL, 'ຮູບແທນຕົວ'),
(79, 'Background color', 'text', 'index', 0, 'สีพื้นหลัง', NULL, 'ສີພື້ນຫລັງ'),
(80, 'Background image', 'text', 'index', 0, 'รูปภาพพื้นหลัง', NULL, 'ພາບພື້ນຫລັງ'),
(81, 'Begin date', 'text', 'index', 0, 'วันที่เริ่มต้น', NULL, 'ວັນເລີ່ມຕົ້ນ'),
(82, 'Begin time', 'text', 'index', 0, 'เวลาเริ่มต้น', NULL, 'ເລີ່ມເວລາ'),
(83, 'Borrow', 'text', 'index', 0, 'ยืม', NULL, 'ຍືມ'),
(84, 'Borrowed date', 'text', 'index', 0, 'วันที่ยืม', NULL, 'ວັນທີ່ຍືມ'),
(85, 'Borrower', 'text', 'index', 0, 'ผู้ยืม', NULL, 'ຜູ້ຢືມ'),
(86, 'Browse file', 'text', 'index', 0, 'เลือกไฟล์', NULL, 'ເລືອກໄຟລ໌'),
(87, 'Browse image uploaded, type :type', 'text', 'index', 0, 'เลือกรูปภาพอัปโหลด ชนิด :type', NULL, 'ເລືອກຮູບພາບອັບໂຫຼດຊະນິດ :type'),
(88, 'Can be approve', 'text', 'index', 0, 'สามารถอนุมัติได้', NULL, 'ສາມາດອະນຸມັດ'),
(89, 'Can login', 'text', 'index', 0, 'สามารถเข้าระบบได้', NULL, 'ສາມາດເຂົ້າສູ່ລະບົບ'),
(90, 'Can manage borrow', 'text', 'index', 0, 'สามารถจัดการการยืม', NULL, 'ສາມາດຈັດການການຍືມ'),
(91, 'Can manage the', 'text', 'index', 0, 'สามารถจัดการ', NULL, 'ສາມາດຈັດການ'),
(92, 'Can manage the inventory', 'text', 'index', 0, 'สามารถจัดการคลังสินค้าได้', NULL, 'ສາມາດຈັດການຄັງສິນຄ້າໄດ້'),
(93, 'Can not be performed this request. Because they do not find the information you need or you are not allowed', 'text', 'index', 0, 'ไม่สามารถดำเนินการตามที่ร้องขอได้ เนื่องจากไม่พบข้อมูลที่ต้องการ หรือ คุณไม่มีสิทธิ์', NULL, 'ບໍ່ສາມາດດຳເນີນການຕາມທີ່ຮ້ອງຂໍໄດ້ເນື່ອງຈາກບໍ່ພົບຂໍ້ມູນທີ່ຕ້ອງການ ຫຼື ທ່ານບໍ່ມີສິດ'),
(94, 'Can&#039;t login', 'text', 'index', 0, 'ไม่สามารถเข้าระบบได้', NULL, 'ບໍ່ສາມາດເຂົ້າສູ່ລະບົບໄດ້'),
(95, 'Cancel', 'text', 'index', 0, 'ยกเลิก', NULL, 'ຍົກເລີກ'),
(96, 'Canceled successfully', 'text', 'index', 0, 'ยกเลิกเรียบร้อย', NULL, 'ຍົກເລີກສົບຜົນສໍາເລັດ'),
(97, 'Cannot use :name', 'text', 'index', 0, 'ไม่สามารถใช้ :name ได้', NULL, 'ບໍ່ສາມາດໃຊ້ :name'),
(98, 'Category', 'text', 'index', 0, 'หมวดหมู่', NULL, 'ຫມວດຫມູ່'),
(99, 'Change language', 'text', 'index', 0, 'สลับภาษา', NULL, 'ປ່ຽນພາສາ'),
(100, 'Click to edit', 'text', 'index', 0, 'คลิกเพื่อแก้ไข', NULL, 'ກົດເພື່ອແກ້ໄຂ'),
(101, 'Comment', 'text', 'index', 0, 'หมายเหตุ', NULL, 'ຫມາຍ​ເຫດ​'),
(102, 'Confirm password', 'text', 'index', 0, 'ยืนยันรหัสผ่าน', NULL, 'ຢືນຢັນລະຫັດຜ່ານ'),
(103, 'Congratulations, your email address has been verified. please login', 'text', 'index', 0, 'ยินดีด้วย ที่อยู่อีเมลของคุณได้รับการยืนยันเรียบร้อยแล้ว กรุณาเข้าสู่ระบบ', NULL, 'ຂໍສະແດງຄວາມຍິນດີ, ທີ່ຢູ່ອີເມວຂອງທ່ານໄດ້ຮັບການຢັ້ງຢືນແລ້ວ. ກະລຸນາເຂົ້າສູ່ລະບົບ'),
(104, 'Contact name', 'text', 'index', 0, 'ชื่อผู้จอง', NULL, 'ຕົວແທນການຈອງ'),
(105, 'Cookie Policy', 'text', 'index', 0, 'นโยบายคุกกี้', NULL, 'ນະໂຍບາຍຄຸກກີ'),
(106, 'COOKIE_NECESSARY_DETAILS', 'text', 'index', 0, 'คุกกี้พื้นฐาน จำเป็นต่อการใช้งานเว็บไซต์ ใช้เพื่อการรักษาความปลอดภัยและให้เว็บไซต์สามารถทำงานได้อย่างถูกต้อง', NULL, 'ຄຸກກີພື້ນຖານ ມີຄວາມຈໍາເປັນໃນການນໍາໃຊ້ເວັບໄຊທ໌ ມັນຖືກນໍາໃຊ້ເພື່ອຈຸດປະສົງຄວາມປອດໄພແລະເພື່ອໃຫ້ເວັບໄຊທ໌ເຮັດວຽກຢ່າງຖືກຕ້ອງ.'),
(107, 'COOKIE_POLICY_DETAILS', 'text', 'index', 0, 'เราใช้คุกกี้ (Cookies) หรือเทคโนโลยีที่คล้ายคลึงกันเท่าที่จำเป็น เพื่อใช้ในการเข้าถึงสินค้าหรือบริการ และติดตามการใช้งานของคุณเท่านั้น หากคุณไม่ต้องการให้มีคุกกี้ไว้ในคอมพิวเตอร์ของคุณ คุณสามารถตั้งค่าบราวเซอร์เพื่อปฏิเสธการจัดเก็บคุกกี้ก่อนที่จะใช้งานเว็บไซต์ หรือใช้โหมดไม่ระบุตัวตนเพื่อเข้าใช้งานเว็บไซต์ก็ได้', NULL, 'ພວກເຮົາໃຊ້ຄຸກກີ (Cookies) ຫຼືເຕັກໂນໂລຢີທີ່ຄ້າຍຄືກັນໃນຂອບເຂດທີ່ຈໍາເປັນ. ສໍາລັບການນໍາໃຊ້ໃນການເຂົ້າເຖິງສິນຄ້າຫຼືການບໍລິການ ແລະພຽງແຕ່ຕິດຕາມການນໍາໃຊ້ຂອງທ່ານ ຖ້າ​ຫາກ​ວ່າ​ທ່ານ​ບໍ່​ຕ້ອງ​ການ cookies ໃນ​ຄອມ​ພິວ​ເຕີ​ຂອງ​ທ່ານ​ ທ່ານສາມາດຕັ້ງຕົວທ່ອງເວັບຂອງທ່ານເພື່ອປະຕິເສດການເກັບຮັກສາ cookies. ກ່ອນທີ່ຈະນໍາໃຊ້ເວັບໄຊທ໌ ທ່ານຍັງສາມາດໃຊ້ໂໝດບໍ່ເປີດເຜີຍຕົວຕົນເພື່ອເຂົ້າຫາເວັບໄຊທ໌ໄດ້.'),
(108, 'Cost', 'text', 'index', 0, 'ค่าใช้จ่าย', NULL, 'ຄ່າໃຊ້ຈ່າຍ'),
(109, 'Country', 'text', 'index', 0, 'ประเทศ', NULL, 'ປະເທດ'),
(110, 'Create', 'text', 'index', 0, 'สร้าง', NULL, 'ສ້າງ'),
(111, 'Create new account', 'text', 'index', 0, 'สร้างบัญชีใหม่', NULL, 'ສ້າງບັນຊີໃໝ່'),
(112, 'Created', 'text', 'index', 0, 'สร้างเมื่อ', NULL, 'ສ້າງເມື່ອ'),
(113, 'Customer', 'text', 'index', 0, 'ลูกค้า', NULL, 'ລູກຄ້າ'),
(114, 'Customer Name', 'text', 'index', 0, 'ชื่อลูกค้า', NULL, 'ຊື່ລູກຄ້າ'),
(115, 'Dark mode', 'text', 'index', 0, 'โหมดมืด', NULL, 'ໂໝດມືດ'),
(116, 'Data controller', 'text', 'index', 0, 'ผู้ควบคุม/ใช้ ข้อมูล', NULL, 'ຜູ້​ຄວບ​ຄຸມຂໍ້ມູນ'),
(117, 'Date', 'text', 'index', 0, 'วันที่', NULL, 'ວັນທີ'),
(118, 'Date of return', 'text', 'index', 0, 'กำหนดคืน', NULL, 'ຕັ້ງຄືນ'),
(119, 'days', 'text', 'index', 0, 'วัน', NULL, 'ມື້'),
(120, 'Delete', 'text', 'index', 0, 'ลบ', NULL, 'ລືບ'),
(121, 'Delivery', 'text', 'index', 0, 'ส่งมอบ', NULL, 'ສົ່ງມອບ'),
(122, 'Demo Mode', 'text', 'index', 0, 'โหมดตัวอย่าง', NULL, 'ຕົວຢ່າງ'),
(123, 'Department', 'text', 'index', 0, 'แผนก', NULL, 'ຜະແນກ'),
(124, 'Description', 'text', 'index', 0, 'คำอธิบาย', NULL, 'ຄຳອະທິບາຍ'),
(125, 'Detail', 'text', 'index', 0, 'รายละเอียด', NULL, 'ລາຍະລະອຽດ'),
(126, 'Details of', 'text', 'index', 0, 'รายละเอียดของ', NULL, 'ລາຍລະອຽດຂອງ'),
(127, 'Didn&#039;t receive code?', 'text', 'index', 0, 'ไม่ได้รับโค้ด?', NULL, 'ບໍ່ໄດ້ຮັບລະຫັດບໍ?'),
(128, 'Do not want', 'text', 'index', 0, 'ไม่ต้องการ', NULL, 'ບໍ່ຕ້ອງການ'),
(129, 'Drag and drop to reorder', 'text', 'index', 0, 'ลากและวางเพื่อจัดลำดับใหม่', NULL, 'ລາກແລ້ວວາງລົງເພື່ອຈັດຮຽງໃໝ່'),
(130, 'Edit', 'text', 'index', 0, 'แก้ไข', NULL, 'ແກ້ໄຂ'),
(131, 'Editing your account', 'text', 'index', 0, 'แก้ไขข้อมูลส่วนตัว', NULL, 'ແກ້ໄຂຂໍ້ມູນສ່ວນຕົວສະມາຊິກ'),
(132, 'Email', 'text', 'index', 0, 'อีเมล', NULL, 'ອີເມວ'),
(133, 'Email address used for login or request a new password', 'text', 'index', 0, 'ที่อยู่อีเมล ใช้สำหรับการเข้าระบบหรือการขอรหัสผ่านใหม่', NULL, 'ທີ່ຢູ່ອີເມວໃຊ້ສຳລັບການເຂົ້າລະບົບຫຼືການຂໍລະຫັດໃໝ່'),
(134, 'Email address verification', 'text', 'index', 0, 'ยืนยันที่อยู่อีเมล', NULL, 'ຢືນຢັນທີ່ຢູ່ອີເມວ'),
(135, 'Email addresses for sender and do not reply such as no-reply@domain.tld', 'text', 'index', 0, 'ทีอยู่อีเมลใช้เป็นผู้ส่งจดหมาย สำหรับจดหมายที่ไม่ต้องการตอบกลับ เช่น no-reply@domain.tld', NULL, 'ທີ່ຢູ່ອີເມວໃຊ້ເປັນຜູ້ສົ່ງຈົດໝາຍ ສຳລັບຈົດໝາຍທີ່ບໍ່ຕ້ອງການຕອບກັບເຊັ່ນ no-reply@domain.tld'),
(136, 'Email encoding', 'text', 'index', 0, 'รหัสภาษาของจดหมาย', NULL, 'ລະຫັດພາສາຂອງຈົດໝາຍ'),
(137, 'Email settings', 'text', 'index', 0, 'ตั้งค่าอีเมล', NULL, 'ຕັ້ງຄ່າອີເມວ'),
(138, 'Email the relevant person', 'text', 'index', 0, 'ส่งอีเมลแจ้งผู้ที่เกี่ยวข้องด้วย', NULL, 'ສົ່ງອີເມວຫາບຸກຄົນທີ່ກ່ຽວຂ້ອງ.'),
(139, 'Email was not verified', 'text', 'index', 0, 'ยังไม่ได้ยืนยันอีเมล', NULL, 'ອີເມວບໍ່ໄດ້ຖືກຢືນຢັນ'),
(140, 'Enable SSL encryption for sending email', 'text', 'index', 0, 'เปิดใช้งานการเข้ารหัส SSL สำหรับการส่งอีเมล', NULL, 'ເປີດໃຊ້ການເຂົ້າລະຫັດ SSL ສຳລັບການສົ່ງອີເມວ'),
(141, 'End date', 'text', 'index', 0, 'วันที่สิ้นสุด', NULL, 'ວັນສິ້ນສຸດ'),
(142, 'End date must be greater than begin date', 'text', 'index', 0, 'วันที่สิ้นสุดต้องมากกว่าวันที่เริ่มต้น', NULL, 'ວັນສິ້ນສຸດຕ້ອງຫຼາຍກວ່າວັນທີເລີ່ມຕົ້ນ.'),
(143, 'End time', 'text', 'index', 0, 'เวลาสิ้นสุด', NULL, 'ເວລາສິ້ນສຸດ'),
(144, 'English lowercase letters and numbers, not less than 6 digits', 'text', 'index', 0, 'ภาษาอังกฤษตัวพิมพ์เล็กและตัวเลข ไม่น้อยกว่า 6 หลัก', NULL, 'ໂຕພິມນ້ອຍພາສາອັງກິດ ແລະຕົວເລກ, ບໍ່ຕໍ່າກວ່າ 6 ຕົວເລກ'),
(145, 'Enter the 4-digit verification code that was sent to your phone number.', 'text', 'index', 0, 'ป้อนรหัสยืนยัน 4 หลักที่ส่งไปยังหมายเลขโทรศัพท์ของคุณ', NULL, 'ໃສ່ລະຫັດຢືນຢັນ 4 ຕົວເລກທີ່ສົ່ງໄປຫາເບີໂທລະສັບຂອງທ່ານ.'),
(146, 'Enter the domain name you want to allow or enter * for all domains. or leave it blank if you want to use it on this domain only', 'text', 'index', 0, 'กรอกชื่อโดเมนที่ต้องการอนุญาต หรือกรอก * สำหรับทุกโดเมน หรือเว้นว่างไว้ถ้าต้องการให้ใช้งานได้บนโดเมนนี้เท่านั้น', NULL, 'ໃສ່ຊື່ໂດເມນທີ່ທ່ານຕ້ອງການທີ່ຈະອະນຸຍາດໃຫ້ຫຼືໃສ່ * ສໍາລັບໂດເມນທັງຫມົດ. ຫຼືປ່ອຍໃຫ້ມັນຫວ່າງຖ້າທ່ານຕ້ອງການໃຊ້ມັນຢູ່ໃນໂດເມນນີ້ເທົ່ານັ້ນ'),
(147, 'Enter the email address registered. A new password will be sent to this email address.', 'text', 'index', 0, 'กรอกที่อยู่อีเมลที่ลงทะเบียนไว้ ระบบจะส่งรหัสผ่านใหม่ไปยังที่อยู่อีเมลนี้', NULL, 'ເພີ່ມທີ່ຢູ່ອີເມວທີ່ລົງທະບຽນໄວ້ ລະບົບຈະສົ່ງລະຫັດຜ່ານໃໝ່ໄປຍັງທີ່ຢູ່ອີເມວນີ້'),
(148, 'Enter the LINE user ID you received when adding friends. Or type userId sent to the official account to request a new user ID. This information is used for receiving private messages from the system via LINE.', 'text', 'index', 0, 'กรอก user ID ของไลน์ที่ได้ตอนเพิ่มเพื่อน หรือพิมพ์คำว่า userId ส่งไปยังบัญชีทางการเพื่อขอ user ID ใหม่ ข้อมูลนี้ใช้สำหรับการรับข้อความส่วนตัวที่มาจากระบบผ่านไลน์', NULL, 'ໃສ່ user ID ຂອງ LINE ທີ່ທ່ານໄດ້ຮັບໃນເວລາເພີ່ມເພື່ອນ. ຫຼືພິມ userId ທີ່ຖືກສົ່ງໄປຫາບັນຊີທາງການເພື່ອຮ້ອງຂໍ user ID ໃຫມ່. ຂໍ້ມູນນີ້ແມ່ນໃຊ້ສໍາລັບການຮັບຂໍ້ຄວາມສ່ວນຕົວຈາກລະບົບຜ່ານ LINE.'),
(149, 'Enter your password again', 'text', 'index', 0, 'กรอกรหัสผ่านของคุณอีกครั้ง', NULL, 'ໃສ່ລະຫັດຜ່ານຂອງທ່ານອີກຄັ້ງ'),
(150, 'Enter your registered username. A new password will be sent to this username.', 'text', 'index', 0, 'กรอกชื่อผู้ใช้ที่ลงทะเบียนไว้ ระบบจะส่งรหัสผ่านใหม่ไปยังชื่อผู้ใช้นี้', NULL, 'ໃສ່ຊື່ຜູ້ໃຊ້ທີ່ລົງທະບຽນຂອງທ່ານ. ລະຫັດຜ່ານໃໝ່ຈະຖືກສົ່ງໄປຫາຊື່ຜູ້ໃຊ້ນີ້'),
(151, 'entries', 'text', 'index', 0, 'รายการ', NULL, 'ລາຍການ'),
(152, 'Equipment', 'text', 'index', 0, 'พัสดุ', NULL, 'ພັສດຸ'),
(153, 'Expiration date', 'text', 'index', 0, 'วันหมดอายุ', NULL, 'ວັນໝົດອາຍຸ'),
(154, 'Fax', 'text', 'index', 0, 'โทรสาร', NULL, 'ແຟັກ'),
(155, 'File', 'text', 'index', 0, 'ไฟล์', NULL, 'ແຟ້ມ'),
(156, 'Fill some of the :name to find', 'text', 'index', 0, 'กรอกบางส่วนของ :name เพื่อค้นหา', NULL, 'ຕື່ມຂໍ້ມູນໃສ່ບາງສ່ວນຂອງ :name ເພື່ອຄົ້ນຫາ'),
(157, 'Find equipment by', 'text', 'index', 0, 'ค้นหาพัสดุโดย', NULL, 'ຄົ້ນຫາພັສດຸຈາກ'),
(158, 'Footer', 'text', 'index', 0, 'ส่วนท้าย', NULL, 'ສ່ວນທ້າຍ'),
(159, 'for login by LINE account', 'text', 'index', 0, 'สำหรับการเข้าระบบโดยบัญชีไลน์', NULL, 'ສໍາລັບການເຂົ້າສູ່ລະບົບດ້ວຍບັນຊີ LINE'),
(160, 'Forgot', 'text', 'index', 0, 'ลืมรหัสผ่าน', NULL, 'ລືມລະຫັດຜ່ານ'),
(161, 'from', 'text', 'index', 0, 'จาก', NULL, 'ຈາກ'),
(162, 'General', 'text', 'index', 0, 'ทั่วไป', NULL, 'ທົ່ວໄປ'),
(163, 'General site settings', 'text', 'index', 0, 'ตั้งค่าพื้นฐานของเว็บไซต์', NULL, 'ຕັ້ງຄ່າພື້ນຖານຂອງເວັບໄຊ'),
(164, 'Get new password', 'text', 'index', 0, 'ขอรหัสผ่าน', NULL, 'ຂໍລະຫັດຜ່ານ'),
(165, 'go to page', 'text', 'index', 0, 'ไปหน้าที่', NULL, 'ໄປທີ່ໜ້າ'),
(166, 'Header', 'text', 'index', 0, 'ส่วนหัว', NULL, 'ສ່ວນຫົວ'),
(167, 'Home', 'text', 'index', 0, 'หน้าหลัก', NULL, 'ໜ້າຫຼັກ'),
(168, 'How to define user authentication for mail servers. If you enable it, you must configure below correctly.', 'text', 'index', 0, 'กำหนดวิธีการตรวจสอบผู้ใช้สำหรับเมล์เซิร์ฟเวอร์ หากคุณเปิดใช้งานคุณต้องกำหนดค่าต่างๆด้านล่างถูกต้อง', NULL, 'ກຳນົດວິທີການກວດສອບຜູ້ໃຊ້ສຳລັບເມນເຊິບເວີຫາກທ່ານເປີດໃຊ້ການທ່ານຕ້ອງກຳນົດຄ່າຕ່າງໆດ້ານລຸ່ມຖືກຕ້ອງ'),
(169, 'Identification No.', 'text', 'index', 0, 'เลขประชาชน', NULL, 'ເລກບັດປະຈຳຕົວ'),
(170, 'Image', 'text', 'index', 0, 'รูปภาพ', NULL, 'ຮູບພາບ'),
(171, 'Image size is in pixels', 'text', 'index', 0, 'ขนาดของรูปภาพเป็นพิกเซล', NULL, 'ຂະໜາດຂອງຮູບພາບເປັນພິກເຊວ'),
(172, 'Import', 'text', 'index', 0, 'นำเข้า', NULL, 'ນຳເຂົ້າ'),
(173, 'Installed modules', 'text', 'index', 0, 'โมดูลที่ติดตั้งแล้ว', NULL, 'ໂມດູນທີ່ຕິດຕັ້ງ'),
(174, 'Invalid :name', 'text', 'index', 0, ':name ไม่ถูกต้อง', NULL, ':name ບໍ່ຖືກຕ້ອງ'),
(175, 'Job today', 'text', 'index', 0, 'งานซ่อมวันนี้', NULL, 'ວຽກຊ່ອມວັນນີ້'),
(176, 'Key', 'text', 'index', 0, 'คีย์', NULL, 'ແປ້ນພີມ'),
(177, 'Language', 'text', 'index', 0, 'ภาษา', NULL, 'ພາສາ'),
(178, 'Leave empty for generate auto', 'text', 'index', 0, 'เว้นว่างไว้เพื่อสร้างโดยอัตโนมัติ', NULL, 'ປ່ອຍຫວ່າງໄວ້ເພື່ອສ້າງອັດໂນມັດ'),
(179, 'LINE official account (with @ prefix, e.g. @xxxx)', 'text', 'index', 0, 'บัญชีทางการของไลน์ (มี @ นำหน้า เช่น @xxxx)', NULL, 'ບັນຊີທາງການຂອງ LINE (ມີ @ ຄໍານໍາຫນ້າ, ເຊັ່ນ: @xxxx)'),
(180, 'LINE settings', 'text', 'index', 0, 'ตั้งค่าไลน์', NULL, 'ຕັ້ງ​ຄ່າ LINE'),
(181, 'List of', 'text', 'index', 0, 'รายการ', NULL, 'ລາຍການ'),
(182, 'List of IPs that allow connection 1 line per 1 IP', 'text', 'index', 0, 'รายการไอพีแอดเดรสทั้งหมดที่อนุญาต 1 บรรทัดต่อ 1 ไอพี', NULL, 'ລາຍຊື່ IP ທີ່ອະນຸຍາດໃຫ້ເຊື່ອມຕໍ່ 1 ເສັ້ນຕໍ່ 1 IP'),
(183, 'Local time', 'text', 'index', 0, 'เวลาท้องถิ่น', NULL, 'ເວລາທ້ອງຖິ່ນ'),
(184, 'Log in to Telegram to request an ID', 'text', 'index', 0, 'เข้าระบบ Telegram เพื่อขอ ID', NULL, 'ເຂົ້າສູ່ລະບົບ Telegram ເພື່ອຮ້ອງຂໍ ID.'),
(185, 'Login', 'text', 'index', 0, 'เข้าสู่ระบบ', NULL, 'ເຂົ້າສູ່ລະບົບ'),
(186, 'Login as', 'text', 'index', 0, 'เข้าระบบเป็น', NULL, 'ເຂົ້າ​ສູ່​ລະ​ບົບ​ເປັນ'),
(187, 'Login by', 'text', 'index', 0, 'เข้าระบบโดย', NULL, 'ເຂົ້າສູ່ລະບົບໂດຍ'),
(188, 'Login information', 'text', 'index', 0, 'ข้อมูลการเข้าระบบ', NULL, 'ຂໍ້ມູນການເຂົ້າລະບົບ'),
(189, 'Login page', 'text', 'index', 0, 'หน้าเข้าสู่ระบบ', NULL, 'ໜ້າເຂົ້າສູ່ລະບົບ'),
(190, 'Login with an existing account', 'text', 'index', 0, 'เข้าระบบด้วยบัญชีสมาชิกที่มีอยู่แล้ว', NULL, 'ເຂົ້າລະບົບດ້ວຍບັນຊີສະມາຊິກທີ່ມີຢູ່ແລ້ວ'),
(191, 'Logo', 'text', 'index', 0, 'โลโก', NULL, 'ໂລໂກ'),
(192, 'Logout', 'text', 'index', 0, 'ออกจากระบบ', NULL, 'ອອກຈາກລະບົບ'),
(193, 'Logout successful', 'text', 'index', 0, 'ออกจากระบบเรียบร้อย', NULL, 'ອອກຈາກລະບົບຮຽບຮ້ອຍ'),
(194, 'Mail program', 'text', 'index', 0, 'โปรแกรมส่งอีเมล', NULL, 'ໂປຮແກຮມສົ່ງອີເມວ'),
(195, 'Mail server', 'text', 'index', 0, 'เซิร์ฟเวอร์อีเมล', NULL, 'ເຊີບເວີອີເມວ'),
(196, 'Mail server port number (default is 25, for GMail used 465, 587 for DirectAdmin).', 'text', 'index', 0, 'หมายเลขพอร์ตของเมล์เซิร์ฟเวอร์ (ค่าปกติคือ 25, สำหรับ gmail ใช้ 465, 587 สำหรับ DirectAdmin)', NULL, 'ໝາຍເລກພອດຂອງເມວເຊີບເວີ(ຄ່າປົກກະຕິຄື 25, ສຳລັບ gmail ໃຊ້ 465, 587 ສຳລັບ DirectAdmin)'),
(197, 'Mail server settings', 'text', 'index', 0, 'ค่ากำหนดของเมล์เซิร์ฟเวอร์', NULL, 'ຄ່າກຳນົດຂອງເມວເຊີບເວີ'),
(198, 'Manage languages', 'text', 'index', 0, 'จัดการภาษา', NULL, 'ຈັດການພາສາ'),
(199, 'Member list', 'text', 'index', 0, 'รายชื่อสมาชิก', NULL, 'ລາຍຊື່ສະມາຊິກ'),
(200, 'Member status', 'text', 'index', 0, 'สถานะสมาชิก', NULL, 'ສະຖານະສະມາຊິກ'),
(201, 'Membership has not been confirmed yet.', 'text', 'index', 0, 'ยังไม่ได้ยืนยันสมาชิก', NULL, 'ສະມາຊິກຍັງບໍ່ທັນໄດ້ຮັບການຢືນຢັນ'),
(202, 'Message', 'text', 'index', 0, 'ข้อความ', NULL, 'ຂໍ້ຄວາມ'),
(203, 'Message displayed on login page', 'text', 'index', 0, 'ข้อความแสดงในหน้าเข้าสู่ระบบ', NULL, 'ຂໍ້ຄວາມສະແດງຢູ່ໃນຫນ້າເຂົ້າສູ່ລະບົບ'),
(204, 'Mobile Phone Verification', 'text', 'index', 0, 'ยืนยันหมายเลขโทรศัพท์', NULL, 'ຢືນຢັນເບີໂທລະສັບ'),
(205, 'Model', 'text', 'index', 0, 'ยี่ห้อ', NULL, 'ຍີ່ຫໍ້'),
(206, 'Module', 'text', 'index', 0, 'โมดูล', NULL, 'ໂມດູນ'),
(207, 'Module settings', 'text', 'index', 0, 'ตั้งค่าโมดูล', NULL, 'ຕັ້ງຄ່າໂມດູນ'),
(208, 'month', 'text', 'index', 0, 'เดือน', NULL, 'ເດືອນ'),
(209, 'My Borrow', 'text', 'index', 0, 'รายการยืมของฉัน', NULL, 'ລາຍະການຍືມຂອງຂ້ອຍ'),
(210, 'Necessary cookies', 'text', 'index', 0, 'คุกกี้พื้นฐานที่จำเป็น', NULL, 'ຄຸກກີພື້ນຖານທີ່ຈໍາເປັນ'),
(211, 'New members', 'text', 'index', 0, 'สมาชิกใหม่', NULL, 'ສະມາຊິກໃໝ່'),
(212, 'No data available in this table.', 'text', 'index', 0, 'ไม่มีข้อมูลในตารางนี้', NULL, 'ບໍ່ມີຂໍ້ມູນຢູ່ໃນຕາຕະລາງນີ້.'),
(213, 'no larger than :size', 'text', 'index', 0, 'ขนาดไม่เกิน :size', NULL, 'ຂະໜາດບໍ່ເກີນ :size'),
(214, 'No need to fill in English text. If the English text matches the Key', 'text', 'index', 0, 'ไม่จำเป็นต้องกรอกข้อความในภาษาอังกฤษ หากข้อความในภาษาอังกฤษตรงกับคีย์', NULL, 'ບໍ່ຈຳເປັນເພີ່ມຂໍ້ຄວາມໃນພາສາອັງກິດຫາກຂໍ້ຄວາມໃນພາສານອັງກົງກັບແປ້ນພີມ'),
(215, 'not a registered user', 'text', 'index', 0, 'ไม่พบสมาชิกนี้ลงทะเบียนไว้', NULL, 'ບໍ່ພົບສະມາຊິກນີ້ລົງທະບຽນໄວ້'),
(216, 'Not specified', 'text', 'index', 0, 'ไม่ระบุ', NULL, 'ບໍ່ໄດ້ກໍານົດ'),
(217, 'Note or additional notes', 'text', 'index', 0, 'คำอธิบายหรือหมายเหตุเพิ่มเติม', NULL, 'ຄໍາອະທິບາຍຫລືຫມາຍເຫດເພີ່ມເຕີມ'),
(218, 'Number such as %04d (%04d means 4 digits, maximum 11 digits)', 'text', 'index', 0, 'เลขที่ เช่น %04d (%04d หมายถึง ตัวเลข 4 หลัก สูงสุด 11 หลัก)', NULL, 'ຕົວເລກເຊັ່ນ %04d (%04d ຫມາຍຄວາມວ່າ 4 ຕົວເລກ, ສູງສຸດ 11 ຕົວເລກ)'),
(219, 'Other', 'text', 'index', 0, 'อื่นๆ', NULL, 'ອື່ນໆ'),
(220, 'OTP is invalid or expired. Please request a new OTP.', 'text', 'index', 0, 'OTP ไม่ถูกต้องหรือหมดอายุ กรุณาขอ OTP ใหม่', NULL, 'OTP ບໍ່ຖືກຕ້ອງ ຫຼືໝົດອາຍຸ ກະລຸນາຮ້ອງຂໍ OTP ໃໝ່.'),
(221, 'Page details', 'text', 'index', 0, 'รายละเอียดของหน้า', NULL, 'ລາຍລະອຽດຂອງໜ້າ'),
(222, 'Password', 'text', 'index', 0, 'รหัสผ่าน', NULL, 'ລະຫັດຜ່ານ'),
(223, 'Password of the mail server. (To change the fill.)', 'text', 'index', 0, 'รหัสผ่านของเมล์เซิร์ฟเวอร์ (ต้องการเปลี่ยน ให้กรอก)', NULL, 'ລະຫັດຜ່ານຂອງເມວເຊີບເວີ (ຕ້ອງການປ່ຽນ ໃຫ້ເພີ່ມ)'),
(224, 'Passwords must be at least four characters', 'text', 'index', 0, 'รหัสผ่านต้องไม่น้อยกว่า 4 ตัวอักษร', NULL, 'ລະຫັດຜ່ານຕ້ອງບໍ່ຕ່ຳກວ່າ 4 ຕົວອັກສອນ'),
(225, 'Permission', 'text', 'index', 0, 'สิทธิ์การใช้งาน', NULL, 'ສິດໃນການໃຊ້ວຽກ'),
(226, 'persons', 'text', 'index', 0, 'ท่าน', NULL, 'ຄົນ'),
(227, 'Phone', 'text', 'index', 0, 'โทรศัพท์', NULL, 'ເບີໂທລະສັບ'),
(228, 'Please check the new member registration.', 'text', 'index', 0, 'กรุณาตรวจสอบการลงทะเบียนสมาชิกใหม่', NULL, 'ກະລຸນາກວດສອບການລົງທະບຽນສະມາຊິກໃໝ່.'),
(229, 'Please click the link to verify your email address.', 'text', 'index', 0, 'กรุณาคลิกลิงค์เพื่อยืนยันที่อยู่อีเมล', NULL, 'ກະລຸນາຄລິກທີ່ລິ້ງເພື່ອຢືນຢັນທີ່ຢູ່ອີເມວຂອງທ່ານ'),
(230, 'Please fill in', 'text', 'index', 0, 'กรุณากรอก', NULL, 'ກະລຸນາຕື່ມຂໍ້ມູນໃສ່'),
(231, 'Please fill up this form', 'text', 'index', 0, 'กรุณากรอกแบบฟอร์มนี้', NULL, 'ກະລຸນາຕື່ມແບບຟອມນີ້'),
(232, 'Please login', 'text', 'index', 0, 'กรุณาเข้าระบบ', NULL, 'ກະລຸນາເຂົ້າສູ່ລະບົບ'),
(233, 'Please select', 'text', 'index', 0, 'กรุณาเลือก', NULL, 'ກະລຸນາເລືອກ'),
(234, 'Please select :name at least one item', 'text', 'index', 0, 'กรุณาเลือก :name อย่างน้อย 1 รายการ', NULL, 'ກະລຸນາເລືອກ :name ຢ່າງໜ້ອຍ 1 ລາຍການ'),
(235, 'Please select from the search results', 'text', 'index', 0, 'กรุณาเลือกจากผลการค้นหา', NULL, 'ກະລຸນາເລືອກຈາກຜົນການຄົ້ນຫາ'),
(236, 'Port', 'text', 'index', 0, 'พอร์ต', NULL, 'ພອດ'),
(237, 'Prefix', 'text', 'index', 0, 'คำนำหน้า', NULL, 'ຄຳນຳໜ້າ'),
(238, 'Prefix, if changed The number will be counted again. You can enter %Y%M (year, month).', 'text', 'index', 0, 'คำนำหน้า ถ้ามีการเปลี่ยนแปลง เลขที่จะนับหนึ่งใหม่ สามารถใส่ %Y%M (ปี, เดือน) ได้', NULL, 'ຄໍານໍາຫນ້າ, ຖ້າມີການປ່ຽນແປງ ຕົວເລກທີ່ຈະນັບອີກຄັ້ງສາມາດໃສ່ເປັນ %Y%M (ປີ, ເດືອນ).'),
(239, 'Privacy Policy', 'text', 'index', 0, 'นโยบายความเป็นส่วนตัว', NULL, 'ນະໂຍບາຍຄວາມເປັນສ່ວນຕົວ'),
(240, 'Product code', 'text', 'index', 0, 'รหัสสินค้า', NULL, 'ລະຫັດສິນຄ້າ'),
(241, 'Profile', 'text', 'index', 0, 'ข้อมูลส่วนตัว', NULL, 'ຂໍ້ມູນສ່ວນຕົວ'),
(242, 'Province', 'text', 'index', 0, 'จังหวัด', NULL, 'ແຂວງ'),
(243, 'Quantity', 'text', 'index', 0, 'จำนวน', NULL, 'ຈໍານວນ'),
(244, 'Reason', 'text', 'index', 0, 'เหตุผล', NULL, 'ເຫດ​ຜົນ'),
(245, 'Register', 'text', 'index', 0, 'สมัครสมาชิก', NULL, 'ສະໝັກສະມາຊິກ'),
(246, 'Register successfully Please log in', 'text', 'index', 0, 'ลงทะเบียนเรียบร้อยแล้วกรุณาเข้าสู่ระบบ', NULL, 'ລົງທະບຽນຢ່າງສຳເລັດຜົນກະລຸນາເຂົ້າສູ່ລະບົບ'),
(247, 'Register successfully, We have sent complete registration information to :email', 'text', 'index', 0, 'ลงทะเบียนสมาชิกใหม่เรียบร้อย เราได้ส่งข้อมูลการลงทะเบียนไปยัง :email', NULL, 'ລົງທະບຽນສຳເລັດແລ້ວ ເຮົາໄດ້ສົ່ງຂໍ້ມູນການລົງທະບຽນໄປທີ່ :email'),
(248, 'Registered successfully Please check your email :email and click the link to verify your email.', 'text', 'index', 0, 'ลงทะเบียนเรียบร้อย กรุณาตรวจสอบอีเมล์ :email และคลิงลิงค์ยืนยันอีเมล', NULL, 'ລົງທະບຽນສົບຜົນສໍາເລັດ ກະ​ລຸ​ນາ​ກວດ​ສອບ​ອີ​ເມວ​ຂອງ​ທ່ານ :email ແລະ​ຄລິກ​ໃສ່​ການ​ເຊື່ອມ​ຕໍ່​ເພື່ອ​ກວດ​ສອບ​ອີ​ເມວ​ຂອງ​ທ່ານ​.'),
(249, 'Remain', 'text', 'index', 0, 'คงเหลือ', NULL, 'ຄົງເຫລືອ'),
(250, 'Remember me', 'text', 'index', 0, 'จำการเข้าระบบ', NULL, 'ຈົດຈຳການເຂົ້າລະບົບ'),
(251, 'Remove', 'text', 'index', 0, 'ลบ', NULL, 'ລຶບ'),
(252, 'Report', 'text', 'index', 0, 'รายงาน', NULL, 'ບົດລາຍງານ'),
(253, 'Resend', 'text', 'index', 0, 'ส่งใหม่', NULL, 'ສົ່ງຄືນ'),
(254, 'resized automatically', 'text', 'index', 0, 'ปรับขนาดอัตโนมัติ', NULL, 'ປັບຂະໜາດອັດຕະໂນມັດ'),
(255, 'Return', 'text', 'index', 0, 'คืน', NULL, 'ຄືນ'),
(256, 'Returned', 'text', 'index', 0, 'ส่งคืน', NULL, 'ສົ່ງຄືນ'),
(257, 'Save', 'text', 'index', 0, 'บันทึก', NULL, 'ບັນທຶກ'),
(258, 'Save and email completed', 'text', 'index', 0, 'บันทึกและส่งอีเมลเรียบร้อย', NULL, 'ບັນທຶກແລະສົ່ງອີເມວຮຽນຮ້ອຍ'),
(259, 'Saved successfully', 'text', 'index', 0, 'บันทึกเรียบร้อย', NULL, 'ບັນທຶກຮຽບຮ້ອຍ'),
(260, 'scroll to top', 'text', 'index', 0, 'เลื่อนขึ้นด้านบน', NULL, 'ເລື່ອນຂື້ນດ້ານເທິງ'),
(261, 'Search', 'text', 'index', 0, 'ค้นหา', NULL, 'ຄົ້ນຫາ'),
(262, 'Search <strong>:search</strong> found :count entries, displayed :start to :end, page :page of :total pages', 'text', 'index', 0, 'ค้นหา <strong>:search</strong> พบ :count รายการ แสดงรายการที่ :start - :end หน้าที่ :page จากทั้งหมด :total หน้า', NULL, 'ຄົ້ນຫາ <strong>:search</strong> ພົບ :count ລາຍການ ສະແດງລາຍການທີ່:start - :end ໜ້າທີ່:page ຈາກທັງໝົດ :total ໜ້າ'),
(263, 'Search for equipment and then choose from the list', 'text', 'index', 0, 'ค้นหาพัสดุ แล้วเลือกจากรายการ', NULL, 'ຄົ້ນຫາພັສດຸແລ້ວເລືອກຈາກລາຍະການ'),
(264, 'Send a new password request', 'text', 'index', 0, 'ส่งคำขอ ขอรหัสผ่านใหม่', NULL, 'ສົ່ງຄຳຮ້ອງຂໍລະຫັດຜ່ານໃໝ່'),
(265, 'Send a welcome email to new members', 'text', 'index', 0, 'ส่งข้อความต้อนรับสมาชิกใหม่', NULL, 'ສົ່ງອີເມວຕ້ອນຮັບກັບສະມາຊິກໃຫມ່'),
(266, 'Send again in', 'text', 'index', 0, 'ส่งใหม่ในอีก', NULL, 'ສົ່ງຄືນໃນເວລາອື່ນ'),
(267, 'Send login approval notification', 'text', 'index', 0, 'ส่งแจ้งเตือนอนุมัติการเข้าระบบ', NULL, 'ສົ່ງການແຈ້ງເຕືອນການອະນຸມັດການເຂົ້າສູ່ລະບົບ'),
(268, 'Send login authorization email', 'text', 'index', 0, 'ส่งอีเมลอนุมัติการเข้าระบบ', NULL, 'ສົ່ງອີເມວການອະນຸຍາດເຂົ້າສູ່ລະບົບ'),
(269, 'Send member confirmation email', 'text', 'index', 0, 'ส่งอีเมลยืนยันสมาชิก', NULL, 'ສົ່ງອີເມລ໌ຢືນຢັນສະມາຊິກ'),
(270, 'Send member confirmation message', 'text', 'index', 0, 'ส่งข้อความยืนยันสมาชิก', NULL, 'ສົ່ງຂໍ້ຄວາມຢືນຢັນສະມາຊິກ'),
(271, 'send message to user When a user adds LINE&#039;s official account as a friend', 'text', 'index', 0, 'ส่งข้อความไปยังผู้ใช้ เมื่อผู้ใช้เพิ่มบัญชีทางการของไลน์เป็นเพื่อน', NULL, 'ສົ່ງຂໍ້ຄວາມຫາຜູ້ໃຊ້ ເມື່ອຜູ້ໃຊ້ເພີ່ມບັນຊີທາງການຂອງ LINE ເປັນໝູ່'),
(272, 'Send notification messages When making a transaction', 'text', 'index', 0, 'ส่งข้อความแจ้งเตือนเมื่อมีการทำรายการ', NULL, 'ສົ່ງຂໍ້ຄວາມແຈ້ງເຕືອນເມື່ອມີການເຮັດທຸລະກຳ'),
(273, 'Sender Name', 'text', 'index', 0, 'ชื่อผู้ส่ง', NULL, 'ຊື່ຜູ້ສົ່ງ'),
(274, 'Serial/Registration No.', 'text', 'index', 0, 'หมายเลขเครื่อง/เลขทะเบียน', NULL, 'ຫມາຍເລກເຄື່ອງ/ເລກທະບຽນ'),
(275, 'Server time', 'text', 'index', 0, 'เวลาเซิร์ฟเวอร์', NULL, 'ເວລາຂອງເຊີບເວີ'),
(276, 'Set the application for send an email', 'text', 'index', 0, 'เลือกโปรแกรมที่ใช้ในการส่งอีเมล', NULL, 'ເລືອກໂປຮແກຮມທີ່ໃຊ້ໃນການສົ່ງອີເມວ'),
(277, 'Setting up the email system', 'text', 'index', 0, 'การตั้งค่าเกี่ยวกับระบบอีเมล', NULL, 'ການຕັ້ງຄ່າກ່ຽວກັບລະບົບອີເມວ'),
(278, 'Settings', 'text', 'index', 0, 'ตั้งค่า', NULL, 'ຕັ້ງຄ່າ'),
(279, 'Settings the conditions for member login', 'text', 'index', 0, 'ตั้งค่าเงื่อนไขในการตรวจสอบการเข้าสู่ระบบ', NULL, 'ຕັ້ງເງື່ອນໄຂການກວດສອບການເຂົ້າລະບົບ'),
(280, 'Settings the timing of the server to match the local time', 'text', 'index', 0, 'ตั้งค่าเขตเวลาของเซิร์ฟเวอร์ ให้ตรงกันกับเวลาท้องถิ่น', NULL, 'ຕັ້ງຄ່າເຂດເວລາຂອງເຊີບເວີ ໃຫ້ກົງກັບເວລາທ້ອງຖີ່ນ'),
(281, 'Sex', 'text', 'index', 0, 'เพศ', NULL, 'ເພດ'),
(282, 'Short description about your website', 'text', 'index', 0, 'ข้อความสั้นๆอธิบายว่าเป็นเว็บไซต์เกี่ยวกับอะไร', NULL, 'ຂໍ້ຄວາມສັ້ນໆ ອະທິບາຍວ່າເປັນເວັບໄຊກ່ຽວກັບຫຍັງ'),
(283, 'Show', 'text', 'index', 0, 'แสดง', NULL, 'ສະແດງ'),
(284, 'Show web title with logo', 'text', 'index', 0, 'แสดงชื่อเว็บและโลโก', NULL, 'ສະແດງຊື່ເວັບແລະໂລໂກ້'),
(285, 'showing page', 'text', 'index', 0, 'กำลังแสดงหน้าที่', NULL, 'ສະແດງໜ້າທີ່'),
(286, 'Site Name', 'text', 'index', 0, 'ชื่อของเว็บไซต์', NULL, 'ຊື່ຂອງເວັບໄຊ'),
(287, 'Site settings', 'text', 'index', 0, 'ตั้งค่าเว็บไซต์', NULL, 'ຕັ້ງຄ່າເວັບໄຊ'),
(288, 'size :width*:height pixel', 'text', 'index', 0, 'ขนาด :width*:height พิกเซล', NULL, 'ຂະໜາດ :width*:height ຟິດເຊວล'),
(289, 'Size of', 'text', 'index', 0, 'ขนาดของ', NULL, 'ຂະໜາດຂອງ'),
(290, 'skip to content', 'text', 'index', 0, 'ข้ามไปยังเนื้อหา', NULL, 'ຂ້າມໄປຍັງເນື້ອຫາ'),
(291, 'SMS Settings', 'text', 'index', 0, 'ตั้งค่า SMS', NULL, 'ຕັ້ງຄ່າ SMS'),
(292, 'Sorry', 'text', 'index', 0, 'ขออภัย', NULL, 'ຂໍໂທດ'),
(293, 'Sorry, cannot find a page called Please check the URL or try the call again.', 'text', 'index', 0, 'ขออภัย ไม่พบหน้าที่เรียก โปรดตรวจสอบ URL หรือลองเรียกอีกครั้ง', NULL, 'ຂໍ​ອະ​ໄພ, ບໍ່​ສາ​ມາດ​ຊອກ​ຫາ​ຫນ້າ​ທີ່​ຮ້ອງ​ຂໍ. ກະ​ລຸ​ນາ​ກວດ​ສອບ URL ຫຼື​ພະ​ຍາ​ຍາມ​ດຶງ​ຂໍ້​ມູນ​ອີກ​ເທື່ອ​ຫນຶ່ງ.'),
(294, 'Sorry, Item not found It&#39;s may be deleted', 'text', 'index', 0, 'ขออภัย ไม่พบรายการที่เลือก รายการนี้อาจถูกลบไปแล้ว', NULL, 'ຂໍໂທດ ບໍ່ພົບລາຍການທີ່ເລືອກ ລາຍການນີ້ອາດຖືກລຶບໄປແລ້ວ'),
(295, 'Specify the language code of the email, as utf-8', 'text', 'index', 0, 'ระบุรหัสภาษาของอีเมลที่ส่ง เช่น utf-8', NULL, 'ລະບຸລະຫັດພາສາຂອງອີເມວທີ່ສົ່ງເຊັ່ນ utf-8'),
(296, 'Start a new line with the web name', 'text', 'index', 0, 'ขึ้นบรรทัดใหม่ชื่อเว็บ', NULL, 'ເລີ່ມແຖວໃໝ່ຊື່ເວັບ'),
(297, 'Status', 'text', 'index', 0, 'สถานะ', NULL, 'ສະຖານະ'),
(298, 'Status for general members', 'text', 'index', 0, 'สถานะสำหรับสมาชิกทั่วไป', NULL, 'ສະຖານະສຳລັບສະມາຊິກທົ່ວໄປ'),
(299, 'Status update', 'text', 'index', 0, 'ปรับปรุงสถานะ', NULL, 'ປັບປຸງສະຖານະ'),
(300, 'Stock', 'text', 'index', 0, 'คงเหลือ', NULL, 'ທີ່ຍັງເຫຼືອ'),
(301, 'Style', 'text', 'index', 0, 'รูปแบบ', NULL, 'ຮູບແບບ'),
(302, 'Telegram settings', 'text', 'index', 0, 'ตั้งค่า Telegram', NULL, 'ຕັ້ງຄ່າ Telegram'),
(303, 'Text color', 'text', 'index', 0, 'สีตัวอักษร', NULL, 'ສີຕົວອັກສອນ'),
(304, 'The amount delivered is greater than the amount borrowed', 'text', 'index', 0, 'จำนวนที่ส่งมอบมากกว่าจำนวนที่ยืม', NULL, 'ຈຳນວນທີ່ຖືກສົ່ງແມ່ນໃຫຍ່ກວ່າຈຳນວນທີ່ຖືກຢືມ.'),
(305, 'The amount returned is greater than the amount delivered', 'text', 'index', 0, 'จำนวนที่คืนมากกว่าจำนวนที่ส่งมอบ', NULL, 'ຈໍານວນທີ່ສົ່ງຄືນແມ່ນສູງກວ່າຈໍານວນທີ່ສົ່ງ'),
(306, 'The amount returned is not equal to the amount delivered', 'text', 'index', 0, 'จำนวนที่คืนไม่เท่ากับจำนวนที่ส่งมอบ', NULL, 'ຈໍານວນທີ່ສົ່ງຄືນແມ່ນບໍ່ເທົ່າກັບຈໍານວນທີ່ສົ່ງ'),
(307, 'The e-mail address of the person or entity that has the authority to make decisions about the collection, use or dissemination of personal data.', 'text', 'index', 0, 'ที่อยู่อีเมลของบุคคลหรือนิติบุคคลที่มีอำนาจตัดสินใจเกี่ยวกับการเก็บรวบรวม ใช้ หรือเผยแพร่ข้อมูลส่วนบุคคล', NULL, 'ທີ່ຢູ່ອີເມລ໌ຂອງບຸກຄົນຫຼືຫນ່ວຍງານທີ່ມີອໍານາດໃນການຕັດສິນໃຈກ່ຽວກັບການລວບລວມ, ການນໍາໃຊ້ຫຼືການເຜີຍແຜ່ຂໍ້ມູນສ່ວນບຸກຄົນ.'),
(308, 'The members status of the site', 'text', 'index', 0, 'สถานะของสมาชิกของเว็บไซต์', NULL, 'ສະຖານະຂອງສະມາຂິກຂອງເວັບໄຊ'),
(309, 'The message has been sent to the admin successfully. Please wait a moment for the admin to approve the registration. You can log back in later if approved.', 'text', 'index', 0, 'ส่งข้อความไปยังผู้ดูแลระบบเรียบร้อยแล้ว กรุณารอสักครู่เพื่อให้ผู้ดูแลระบบอนุมัติการลงทะเบียน คุณสามารถกลับมาเข้าระบบได้ในภายหลังหากได้รับการอนุมัติแล้ว', NULL, 'ຂໍ້ຄວາມດັ່ງກ່າວໄດ້ຖືກສົ່ງໄປຫາຜູ້ເບິ່ງແຍງຢ່າງສໍາເລັດຜົນ. ກະລຸນາລໍຖ້າໃຫ້ຜູ້ເບິ່ງແຍງລະບົບອະນຸມັດການລົງທະບຽນ. ທ່ານສາມາດເຂົ້າສູ່ລະບົບຄືນໄດ້ໃນພາຍຫຼັງຖ້າໄດ້ຮັບການອະນຸມັດ.'),
(310, 'The name of the mail server as localhost or smtp.gmail.com (To change the settings of your email is the default. To remove this box entirely.)', 'text', 'index', 0, 'ชื่อของเมล์เซิร์ฟเวอร์ เช่น localhost หรือ smtp.gmail.com (ต้องการเปลี่ยนค่ากำหนดของอีเมลทั้งหมดเป็นค่าเริ่มต้น ให้ลบข้อความในช่องนี้ออกทั้งหมด)', NULL, 'ຊື່ຂອງເມວເຊີບເວີເຊັ່ນ localhost หรือ chitdpt@gmail.com (ຕ້ອງປ່ຽນຄ່າກຳນົດຂອງອີເມວທັງໝົດເປັນຄ່າເລີ່ມຕົ້ນ ໃຫ້ລຶບຂໍ້ຄວາມໃນຊ່ອງນີ້ອອກທັງໝົດ)'),
(311, 'The system has sent a new OTP code to the phone number you have registered. Please check the SMS and enter the code to confirm the phone number.', 'text', 'index', 0, 'ระบบได้ส่งรหัส OTP ใหม่ไปยังหมายเลขโทรศัพท์ที่ท่านได้ลงทะเบียนไว้แล้ว กรุณาตรวจสอบ SMS แล้วให้นำรหัสมากรอกเพื่อเป็นการยืนยันเบอร์โทรศัพท์', NULL, 'ລະບົບໄດ້ສົ່ງລະຫັດ OTP ໃໝ່ໄປຫາເບີໂທລະສັບທີ່ທ່ານລົງທະບຽນແລ້ວ ກະລຸນາກວດເບິ່ງ SMS ແລະໃສ່ລະຫັດເພື່ອຢືນຢັນເບີໂທລະສັບ.'),
(312, 'The type of file is invalid', 'text', 'index', 0, 'ชนิดของไฟล์ไม่รองรับ', NULL, 'ຊະນິດຂອງແຟ້ມບໍ່ຮອງຮັບ'),
(313, 'Theme', 'text', 'index', 0, 'ธีม', NULL, 'ຮູບແບບສີສັນ'),
(314, 'There is not enough :name (remaining :stock :unit)', 'text', 'index', 0, 'มีสินค้า :name ไม่เพียงพอ (คงเหลือ :stock :unit)', NULL, 'ຜະລິດຕະພັນ :name ບໍ່ພຽງພໍ (ຍັງເຫຼືອ :stock :unit)'),
(315, 'This :name already exist', 'text', 'index', 0, 'มี :name นี้อยู่ก่อนแล้ว', NULL, 'ມີ :name ນີ້ຢູ່ກ່ອນແລ້ວ'),
(316, 'This website uses cookies to provide our services. To find out more about our use of cookies, please see our :privacy.', 'text', 'index', 0, 'เว็บไซต์นี้มีการใช้คุกกี้เพื่อปรับปรุงการให้บริการ หากต้องการข้อมูลเพิ่มเติมเกี่ยวกับการใช้คุกกี้ของเรา โปรดดู :privacy', NULL, 'ເວັບໄຊທ໌ນີ້ໃຊ້ຄຸກກີເພື່ອປັບປຸງການບໍລິການ. ສໍາລັບຂໍ້ມູນເພີ່ມເຕີມກ່ຽວກັບການນໍາໃຊ້ຄຸກກີຂອງພວກເຮົາ, ເບິ່ງ :privacy'),
(317, 'time', 'text', 'index', 0, 'เวลา', NULL, 'ເວລາ'),
(318, 'Time zone', 'text', 'index', 0, 'เขตเวลา', NULL, 'ເຂດເວລາ'),
(319, 'times', 'text', 'index', 0, 'ครั้ง', NULL, 'ຄັ້ງ'),
(320, 'to', 'text', 'index', 0, 'ถึง', NULL, 'ເຖິງ'),
(321, 'To change your password, enter your password to match the two inputs', 'text', 'index', 0, 'ถ้าต้องการเปลี่ยนรหัสผ่าน กรุณากรอกรหัสผ่านสองช่องให้ตรงกัน', NULL, 'ຖ້າຕ້ອງການປ່ຽນລະຫັດຜ່ານກະລຸນາເພີ່ມລະຫັດຜ່ານສອງຊ່ອງໃຫ້ກົງກັນ'),
(322, 'Topic', 'text', 'index', 0, 'หัวข้อ', NULL, 'ຫົວຂໍ້'),
(323, 'Transaction date', 'text', 'index', 0, 'วันที่ทำรายการ', NULL, 'ວັນທີ່ເຮັດລາຍະການ'),
(324, 'Transaction details', 'text', 'index', 0, 'รายละเอียดการทำรายการ', NULL, 'ລາຍະລະອຽດການເຮັດລາຍະການ'),
(325, 'Transaction history', 'text', 'index', 0, 'ประวัติการทำรายการ', NULL, 'ປະວັດການເຮັດລາຍະການ'),
(326, 'Transaction No.', 'text', 'index', 0, 'เลขที่การทำรายการ', NULL, 'ເລກທີ່ການເຮັດລາຍະການ'),
(327, 'Type', 'text', 'index', 0, 'ชนิด', NULL, 'ປະເພດ'),
(328, 'Un-Returned items', 'text', 'index', 0, 'รายการครบกำหนดคืน', NULL, 'ລາຍະການຄົບກໍາຫນົດຄືນ'),
(329, 'Unable to complete the transaction', 'text', 'index', 0, 'ไม่สามารถทำรายการนี้ได้', NULL, 'ບໍ່ສາມາດເຮັດລາຍການນີ້ໄດ້'),
(330, 'Unit', 'text', 'index', 0, 'หน่วย', NULL, 'ໜ່ວຍ'),
(331, 'Upload', 'text', 'index', 0, 'อัปโหลด', NULL, 'ອັບໂຫຼດ'),
(332, 'Upload :type files', 'text', 'index', 0, 'อัปโหลดไฟล์ :type', NULL, 'ອັບໂຫຼດແຟ້ມຂໍ້ມູນ :type'),
(333, 'URL must begin with http:// or https://', 'text', 'index', 0, 'URL ต้องขึ้นต้นด้วย http:// หรือ https://', NULL, 'URL ຕ້ອງເລີ່ມຕົ້ນດ້ວຍ http:// ຫຼື https://'),
(334, 'Usage history', 'text', 'index', 0, 'ประวัติการใช้งาน', NULL, 'ປະ​ຫວັດ​ການ​ນໍາ​ໃຊ້​'),
(335, 'Use the theme&#039;s default settings.', 'text', 'index', 0, 'ใช้การตั้งค่าเริ่มต้นของธีม', NULL, 'ໃຊ້ການຕັ້ງຄ່າເລີ່ມຕົ້ນຂອງຮູບແບບສີສັນ.'),
(336, 'User', 'text', 'index', 0, 'สมาชิก', NULL, 'ສະມາຊິກ'),
(337, 'Username', 'text', 'index', 0, 'ชื่อผู้ใช้', NULL, 'ຊື່ຜູ້ໃຊ້'),
(338, 'Username for the mail server. (To change, enter a new value.)', 'text', 'index', 0, 'ชื่อผู้ใช้ของเมล์เซิร์ฟเวอร์ (ต้องการเปลี่ยน ให้กรอก)', NULL, 'ຊື່ຜູ້ໃຊ້ຂອງເມວເຊີບເວີ (ຕ້ອງການປ່ຽນ ໃຫ້ເພີ່ມ)'),
(339, 'Username used for login or request a new password', 'text', 'index', 0, 'ชื่อผู้ใช้ ใช้สำหรับการเข้าระบบหรือการขอรหัสผ่านใหม่', NULL, 'ຊື່ຜູ້ໃຊ້ທີ່ໃຊ້ສໍາລັບການເຂົ້າສູ່ລະບົບຫຼືຮ້ອງຂໍລະຫັດຜ່ານໃຫມ່'),
(340, 'Users', 'text', 'index', 0, 'สมาชิก', NULL, 'ສະມາຊິກ'),
(341, 'Verify Account', 'text', 'index', 0, 'ยืนยันบัญชี', NULL, 'ຢືນຢັນບັນຊີ'),
(342, 'Version', 'text', 'index', 0, 'รุ่น', NULL, 'ຮຸ່ນ'),
(343, 'Waiting list', 'text', 'index', 0, 'รายการรอตรวจสอบ', NULL, 'ລາຍຊື່ລໍຖ້າ'),
(344, 'Waiting to check from the staff', 'text', 'index', 0, 'รอตรวจสอบจากเจ้าหน้าที่', NULL, 'ລໍຖ້າການກວດສອບຈາກພະນັກງານ'),
(345, 'Website template', 'text', 'index', 0, 'แม่แบบเว็บไซต์', NULL, 'ແມ່ແບບເວັບໄຊທ໌'),
(346, 'Website title', 'text', 'index', 0, 'ชื่อเว็บ', NULL, 'ຊື່ເວັບ'),
(347, 'Welcome', 'text', 'index', 0, 'สวัสดี', NULL, 'ສະບາຍດີ'),
(348, 'Welcome %s, login complete', 'text', 'index', 0, 'สวัสดี คุณ %s ยินดีต้อนรับเข้าสู่ระบบ', NULL, 'ສະບາຍດີທ່ານ %s ຍິນດີຕ້ອນຮັບເຂົ້າສູ່ລະບົບ'),
(349, 'Welcome new members', 'text', 'index', 0, 'ยินดีต้อนรับสมาชิกใหม่', NULL, 'ຍິນດີຕ້ອນຮັບສະມາຊິກໃໝ່'),
(350, 'Welcome. Phone number has been verified. Please log in again.', 'text', 'index', 0, 'ยินดีต้อนรับ หมายเลขโทรศัพท์ได้รับการยืนยันแล้ว กรุณาเข้าระบบอีกครั้ง', NULL, 'ຍິນດີຕ້ອນຮັບເບີໂທລະສັບ. ກະລຸນາເຂົ້າສູ່ລະບົບອີກຄັ້ງ.'),
(351, 'When enabled Social accounts can be logged in as an administrator. (Some abilities will not be available)', 'text', 'index', 0, 'เมื่อเปิดใช้งาน บัญชีโซเชียลจะสามารถเข้าระบบเป็นผู้ดูแลได้ (ความสามารถบางอย่างจะไม่สามารถใช้งานได้)', NULL, 'ເມື່ອເປີດໃຊ້ງານ ບັນຊີສັງຄົມສາມາດເຂົ້າສູ່ລະບົບເປັນຜູ້ບໍລິຫານ. (ຄວາມສາມາດບາງຢ່າງຈະບໍ່ມີ)'),
(352, 'When enabled, a cookies consent banner will be displayed.', 'text', 'index', 0, 'เมื่อเปิดใช้งานระบบจะแสดงแบนเนอร์ขออนุญาตใช้งานคุ้กกี้', NULL, 'ເມື່ອເປີດໃຊ້ງານແລ້ວ, ປ້າຍໂຄສະນາການຍິນຍອມຂອງ cookies ຈະສະແດງຂຶ້ນ.'),
(353, 'When enabled, Members registered with email must also verify their email address. It is not recommended to use in conjunction with other login methods.', 'text', 'index', 0, 'เมื่อเปิดใช้งาน สมาชิกที่ลงทะเบียนด้วยอีเมลจะต้องยืนยันที่อยู่อีเมลด้วย ไม่แนะนำให้ใช้ร่วมกับการเข้าระบบด้วยช่องทางอื่นๆ', NULL, 'ເມື່ອເປີດໃຊ້ ສະມາຊິກທີ່ລົງທະບຽນກັບອີເມລ໌ຈະຕ້ອງຢືນຢັນທີ່ຢູ່ອີເມວຂອງເຂົາເຈົ້າ. ມັນບໍ່ໄດ້ຖືກແນະນໍາໃຫ້ໃຊ້ຮ່ວມກັບວິທີການເຂົ້າສູ່ລະບົບອື່ນໆ.'),
(354, 'Width', 'text', 'index', 0, 'กว้าง', NULL, 'ກວ້າງ'),
(355, 'With selected', 'text', 'index', 0, 'ทำกับที่เลือก', NULL, 'ເຮັດກັບທີ່ເລືອກ'),
(356, 'year', 'text', 'index', 0, 'ปี', NULL, 'ປີ'),
(357, 'You can enter your LINE user ID below on your personal information page. to link your account to this official account', 'text', 'index', 0, 'คุณสามารถกรอก LINE user ID ด้านล่างในหน้าข้อมูลส่วนตัวของคุณ เพื่อผูกบัญชีของคุณเข้ากับบัญชีทางการนี้ได้', NULL, 'ທ່ານສາມາດໃສ່ LINE user ID ຂອງທ່ານຂ້າງລຸ່ມນີ້ຢູ່ໃນຫນ້າຂໍ້ມູນສ່ວນຕົວຂອງທ່ານ. ເພື່ອເຊື່ອມຕໍ່ບັນຊີຂອງທ່ານກັບບັນຊີທາງການນີ້'),
(358, 'You can login at', 'text', 'index', 0, 'คุณสามารถเข้าระบบได้ที่', NULL, 'ທ່ານສາມາດເຂົ້າສູ່ລະບົບໄດ້ທີ່'),
(359, 'You have not returned the equipment. Please return it first.', 'text', 'index', 0, 'คุณยังไม่ได้คืนพัสดุ กรุณาคืนพัสดุก่อน', NULL, 'ຄຸນຍັງບໍ່ໄດ້ຄືນພັສດຸກະລຸນາຄືນພັສດຸກ່ອນ'),
(360, 'You haven&#039;t verified your email address. Please check your email. and click the link in the email', 'text', 'index', 0, 'คุณยังไม่ได้ยืนยันที่อยู่อีเมล กรุณาตรวจสอบอีเมลของคุณ และคลิกลิงค์ในอีเมล', NULL, 'ທ່ານຍັງບໍ່ໄດ້ຢືນຢັນທີ່ຢູ່ອີເມວຂອງທ່ານ. ກະລຸນາກວດເບິ່ງອີເມວຂອງທ່ານ. ແລະຄລິກໃສ່ການເຊື່ອມຕໍ່ໃນອີເມລ໌'),
(361, 'You want to', 'text', 'index', 0, 'คุณต้องการ', NULL, 'ທ່ານຕ້ອງການ'),
(362, 'Your account has been approved.', 'text', 'index', 0, 'บัญชีของท่านได้รับการอนุมัติเรียบร้อยแล้ว', NULL, 'ບັນຊີຂອງທ່ານໄດ້ຮັບການອະນຸມັດແລ້ວ.'),
(363, 'Your account has not been approved, please wait or contact the administrator.', 'text', 'index', 0, 'บัญชีของท่านยังไม่ได้รับการอนุมัติ กรุณารอ หรือติดต่อสอบถามไปยังผู้ดูแล', NULL, 'ບັນຊີຂອງທ່ານບໍ່ໄດ້ຮັບການອະນຸມັດ, ກະລຸນາລໍຖ້າ ຫຼືຕິດຕໍ່ຜູ້ເບິ່ງແຍງລະບົບ.'),
(364, 'Your message was sent successfully', 'text', 'index', 0, 'ส่งข้อความไปยังผู้ที่เกี่ยวข้องเรียบร้อยแล้ว', NULL, 'ສົ່ງຂໍ້ຄວາມໄປຍັງຜູ້ຮັບຮຽບຮ້ອຍແລ້ວ'),
(365, 'Your new password is', 'text', 'index', 0, 'รหัสผ่านใหม่ของคุณคือ', NULL, 'ລະຫັດຜ່ານໃໝ່ຂອງທ່ານຄື'),
(366, 'Your OTP code is :otp. Please enter this code on the website to confirm your phone number.', 'text', 'index', 0, 'รหัส OTP ของคุณคือ :otp กรุณาป้อนรหัสนี้บนเว็บไซต์เพื่อยืนยันหมายเลขโทรศัพท์ของคุณ', NULL, 'ລະຫັດ OTP ຂອງທ່ານແມ່ນ :otp ກະລຸນາໃສ່ລະຫັດນີ້ຢູ່ໃນເວັບໄຊທ໌ເພື່ອຢືນຢັນເບີໂທລະສັບຂອງທ່ານ.'),
(367, 'Your registration information', 'text', 'index', 0, 'ข้อมูลการลงทะเบียนของคุณ', NULL, 'ຂໍ້ມູນການລົງທະບຽນຂອງທ່ານ'),
(368, 'Zipcode', 'text', 'index', 0, 'รหัสไปรษณีย์', NULL, 'ລະຫັດໄປສະນີ');

-- --------------------------------------------------------

--
-- Table structure for table `apps_logs`
--

CREATE TABLE `apps_logs` (
  `id` int(11) NOT NULL,
  `src_id` int(11) NOT NULL,
  `module` varchar(20) NOT NULL,
  `action` varchar(20) NOT NULL,
  `create_date` datetime NOT NULL,
  `reason` text DEFAULT NULL,
  `member_id` int(11) DEFAULT NULL,
  `topic` text NOT NULL,
  `datas` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `apps_logs`
--

INSERT INTO `apps_logs` (`id`, `src_id`, `module`, `action`, `create_date`, `reason`, `member_id`, `topic`, `datas`) VALUES
(1, 4, 'inventory', 'Save', '2025-10-16 11:46:07', NULL, 1, '{LNG_Equipment} ID : 4', NULL),
(2, 1, 'borrow', 'Save', '2025-10-16 11:47:16', 'Array', 1, '{LNG_Borrow} &amp; {LNG_Return} {LNG_Add Borrow}', NULL),
(3, 1, 'borrow', 'Save', '2025-10-16 11:48:08', 'Array', 1, '{LNG_Borrow} &amp; {LNG_Return} {LNG_Edit}', NULL),
(4, 1, 'index', 'User', '2025-10-16 11:48:54', NULL, 1, '{LNG_Login} IP ::1', NULL),
(5, 0, 'index', 'Save', '2025-10-16 11:49:24', NULL, 1, '{LNG_General site settings}', NULL),
(6, 0, 'index', 'Save', '2025-10-16 11:49:33', NULL, 1, '{LNG_General site settings}', NULL),
(7, 0, 'index', 'Save', '2025-10-16 11:49:48', NULL, 1, '{LNG_Settings} {LNG_Login page}', NULL),
(8, 1, 'borrow', 'Save', '2025-10-16 11:51:08', 'Array', 1, '{LNG_Borrow} &amp; {LNG_Return} {LNG_Edit}', NULL),
(9, 1, 'index', 'User', '2025-10-16 11:52:04', NULL, 1, '{LNG_Editing your account} ID : 1', NULL),
(10, 1, 'index', 'User', '2025-10-16 11:52:21', NULL, 1, '{LNG_Editing your account} ID : 1', NULL),
(11, 1, 'index', 'User', '2025-10-16 11:52:55', NULL, 1, '{LNG_Login} IP ::1', NULL),
(12, 1, 'borrow', 'Save', '2025-10-16 11:54:02', 'Array', 1, '{LNG_Borrow} &amp; {LNG_Return} {LNG_Edit}', NULL),
(13, 0, 'borrow', 'Delete', '2025-10-16 11:54:04', NULL, 1, '{LNG_Delete} {LNG_Borrow} &amp; {LNG_Return} ID : 1', NULL),
(14, 2, 'borrow', 'Save', '2025-10-16 11:54:26', 'Array', 1, '{LNG_Borrow} &amp; {LNG_Return} {LNG_Add Borrow}', NULL),
(15, 2, 'borrow', 'Save', '2025-10-16 11:55:42', 'Array', 1, '{LNG_Borrow} &amp; {LNG_Return} {LNG_Edit}', NULL),
(16, 4, 'inventory', 'Status', '2025-10-16 11:56:25', NULL, 1, 'เลิกใช้งานแล้ว ID : 4', NULL),
(17, 3, 'borrow', 'Save', '2025-10-16 11:56:43', 'Array', 1, '{LNG_Borrow} &amp; {LNG_Return} {LNG_Add Borrow}', NULL),
(18, 2, 'borrow', 'Status', '2025-10-16 11:57:25', NULL, 1, 'ram ddr4 อนุมัติ', NULL),
(19, 2, 'borrow', 'Save', '2025-10-16 11:57:27', 'Array', 1, '{LNG_Transaction details} {LNG_Borrow} &amp; {LNG_Return}', NULL),
(20, 1, 'index', 'User', '2025-10-16 13:36:35', NULL, 1, '{LNG_Login} IP ::1', NULL),
(21, 4, 'inventory', 'Save', '2025-10-16 13:37:04', NULL, 1, '{LNG_Equipment} ID : 4', NULL),
(22, 1, 'index', 'User', '2025-10-16 13:39:13', NULL, 1, '{LNG_Create new account} ID : 3', NULL),
(23, 3, 'index', 'User', '2025-10-16 13:39:32', NULL, 3, '{LNG_Login} IP ::1', NULL),
(24, 3, 'borrow', 'Status', '2025-10-16 13:40:06', NULL, 3, 'ASUS A550JX อนุมัติ', NULL),
(25, 3, 'borrow', 'Save', '2025-10-16 13:40:08', 'Array', 3, '{LNG_Transaction details} {LNG_Borrow} &amp; {LNG_Return}', NULL),
(26, 4, 'borrow', 'Save', '2025-10-16 13:40:48', 'Array', 3, '{LNG_Borrow} &amp; {LNG_Return} {LNG_Add Borrow}', NULL),
(27, 1, 'index', 'User', '2025-10-16 13:41:37', NULL, 1, '{LNG_Login} IP ::1', NULL),
(28, 4, 'borrow', 'Status', '2025-10-16 13:42:11', NULL, 1, 'ram ddr4 อนุมัติ', NULL),
(29, 4, 'borrow', 'Save', '2025-10-16 13:42:13', 'Array', 1, '{LNG_Transaction details} {LNG_Borrow} &amp; {LNG_Return}', NULL),
(30, 5, 'borrow', 'Save', '2025-10-16 13:43:43', 'Array', 1, '{LNG_Borrow} &amp; {LNG_Return} {LNG_Add Borrow}', NULL),
(31, 3, 'index', 'User', '2025-10-16 13:44:12', NULL, 3, '{LNG_Login} IP ::1', NULL),
(32, 5, 'borrow', 'Status', '2025-10-16 13:44:57', NULL, 3, 'จอมอนิเตอร์ ACER S220HQLEBD อนุมัติ', NULL),
(33, 5, 'borrow', 'Save', '2025-10-16 13:45:00', 'Array', 3, '{LNG_Transaction details} {LNG_Borrow} &amp; {LNG_Return}', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `apps_number`
--

CREATE TABLE `apps_number` (
  `type` varchar(20) NOT NULL,
  `prefix` varchar(20) NOT NULL,
  `auto_increment` int(11) NOT NULL,
  `last_update` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `apps_number`
--

INSERT INTO `apps_number` (`type`, `prefix`, `auto_increment`, `last_update`) VALUES
('borrow_no', 'B6810-', 5, '2025-10-16');

-- --------------------------------------------------------

--
-- Table structure for table `apps_user`
--

CREATE TABLE `apps_user` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `salt` varchar(32) DEFAULT '',
  `password` varchar(50) NOT NULL,
  `token` varchar(50) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 0,
  `permission` text DEFAULT '',
  `name` varchar(150) NOT NULL,
  `sex` varchar(1) DEFAULT NULL,
  `id_card` varchar(13) DEFAULT NULL,
  `address` varchar(150) DEFAULT NULL,
  `phone` varchar(32) DEFAULT NULL,
  `provinceID` varchar(3) DEFAULT NULL,
  `province` varchar(50) DEFAULT NULL,
  `zipcode` varchar(10) DEFAULT NULL,
  `country` varchar(2) DEFAULT 'TH',
  `create_date` datetime DEFAULT NULL,
  `active` tinyint(1) DEFAULT 1,
  `social` tinyint(1) DEFAULT 0,
  `line_uid` varchar(33) DEFAULT NULL,
  `telegram_id` varchar(13) DEFAULT NULL,
  `activatecode` varchar(32) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `apps_user`
--

INSERT INTO `apps_user` (`id`, `username`, `salt`, `password`, `token`, `status`, `permission`, `name`, `sex`, `id_card`, `address`, `phone`, `provinceID`, `province`, `zipcode`, `country`, `create_date`, `active`, `social`, `line_uid`, `telegram_id`, `activatecode`) VALUES
(1, 'admin@localhost', '68f07799cfd14', '1ef13e2ccc1cc9202c5fc22e2ce9651b2066a801', 'b7cbe97294a50651bd7ca84a03149258405517f4', 1, ',can_config,can_view_usage_history,can_approve_borrow,can_manage_inventory,', 'แอดมิน', 'u', NULL, '', NULL, '', '', '', 'TH', '2025-10-16 06:42:01', 1, 0, NULL, NULL, ''),
(2, 'demo', '68f07799cfd14', 'ac1eb39b4e11a653c2a925d95111e926a518b22c', NULL, 0, '', 'ตัวอย่าง', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'TH', '2025-10-16 06:42:01', 1, 0, NULL, NULL, ''),
(3, 'jukkrit', 'f593b5956159e', '90db371fd2ce672993d4b7d128149a72211ccd07', '80046e2e9c3afe03b42ff58eab15fd9ace851e2b', 1, ',can_config,can_view_usage_history,can_approve_borrow,can_manage_inventory,', 'jukkrit suriya', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'TH', '2025-10-16 13:39:13', 1, 0, NULL, NULL, '');

-- --------------------------------------------------------

--
-- Table structure for table `apps_user_meta`
--

CREATE TABLE `apps_user_meta` (
  `value` varchar(10) NOT NULL,
  `name` varchar(20) NOT NULL,
  `member_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `apps_borrow`
--
ALTER TABLE `apps_borrow`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `apps_borrow_items`
--
ALTER TABLE `apps_borrow_items`
  ADD PRIMARY KEY (`borrow_id`,`id`);

--
-- Indexes for table `apps_category`
--
ALTER TABLE `apps_category`
  ADD KEY `type` (`type`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `language` (`language`);

--
-- Indexes for table `apps_inventory`
--
ALTER TABLE `apps_inventory`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `model_id` (`model_id`),
  ADD KEY `type_id` (`type_id`);

--
-- Indexes for table `apps_inventory_items`
--
ALTER TABLE `apps_inventory_items`
  ADD PRIMARY KEY (`product_no`),
  ADD KEY `inventory_id` (`inventory_id`);

--
-- Indexes for table `apps_inventory_meta`
--
ALTER TABLE `apps_inventory_meta`
  ADD KEY `inventory_id` (`inventory_id`),
  ADD KEY `name` (`name`);

--
-- Indexes for table `apps_language`
--
ALTER TABLE `apps_language`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `apps_logs`
--
ALTER TABLE `apps_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `src_id` (`src_id`),
  ADD KEY `module` (`module`),
  ADD KEY `action` (`action`);

--
-- Indexes for table `apps_number`
--
ALTER TABLE `apps_number`
  ADD PRIMARY KEY (`type`,`prefix`);

--
-- Indexes for table `apps_user`
--
ALTER TABLE `apps_user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `line_uid` (`line_uid`),
  ADD KEY `username` (`username`),
  ADD KEY `token` (`token`),
  ADD KEY `phone` (`phone`),
  ADD KEY `id_card` (`id_card`),
  ADD KEY `activatecode` (`activatecode`);

--
-- Indexes for table `apps_user_meta`
--
ALTER TABLE `apps_user_meta`
  ADD KEY `member_id` (`member_id`,`name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `apps_borrow`
--
ALTER TABLE `apps_borrow`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `apps_inventory`
--
ALTER TABLE `apps_inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `apps_language`
--
ALTER TABLE `apps_language`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=369;

--
-- AUTO_INCREMENT for table `apps_logs`
--
ALTER TABLE `apps_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `apps_user`
--
ALTER TABLE `apps_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- Database: `db_oas`
--
CREATE DATABASE IF NOT EXISTS `db_oas` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `db_oas`;

-- --------------------------------------------------------

--
-- Table structure for table `app_category`
--

CREATE TABLE `app_category` (
  `type` varchar(20) NOT NULL,
  `category_id` varchar(10) DEFAULT '0',
  `language` varchar(2) DEFAULT '',
  `topic` varchar(150) NOT NULL,
  `color` varchar(16) DEFAULT NULL,
  `published` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `app_category`
--

INSERT INTO `app_category` (`type`, `category_id`, `language`, `topic`, `color`, `published`) VALUES
('category_id', '1', '', 'จอมอนิเตอร์', NULL, 1),
('category_id', '2', '', 'Notebook', NULL, 1),
('category_id', '3', '', 'อุปกรณ์คอมพิวเตอร์', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `app_customer`
--

CREATE TABLE `app_customer` (
  `id` int(11) NOT NULL,
  `customer_no` varchar(20) DEFAULT NULL,
  `company` varchar(150) DEFAULT NULL,
  `branch` varchar(50) DEFAULT NULL,
  `name` varchar(150) NOT NULL,
  `idcard` varchar(13) DEFAULT NULL,
  `tax_id` varchar(13) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `fax` varchar(20) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `address` varchar(64) DEFAULT NULL,
  `provinceID` smallint(3) UNSIGNED NOT NULL,
  `province` varchar(64) DEFAULT NULL,
  `zipcode` varchar(5) DEFAULT NULL,
  `country` varchar(2) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `bank` varchar(100) DEFAULT NULL,
  `bank_name` varchar(100) DEFAULT NULL,
  `bank_no` varchar(20) DEFAULT NULL,
  `discount` double NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `app_customer`
--

INSERT INTO `app_customer` (`id`, `customer_no`, `company`, `branch`, `name`, `idcard`, `tax_id`, `phone`, `fax`, `email`, `address`, `provinceID`, `province`, `zipcode`, `country`, `website`, `bank`, `bank_name`, `bank_no`, `discount`) VALUES
(1, NULL, 'ทดสอบ คู่ค้า', '', '', '', '', '03412345678', '', '', '123/45 อ.เมือง', 103, 'กาญจนบุรี', '71000', 'TH', '', '', '', '', 10),
(2, NULL, 'ทดสอบ ลูกค้า', '', '', '', '', '03412456', '', '', '', 102, 'กรุงเทพมหานคร', '10000', 'TH', '', NULL, NULL, NULL, 0),
(3, 'CU0003', 'PCS IT', 'Varee', '', NULL, '0000000000012', '', '', '', '', 10, 'กรุงเทพมหานคร', '10000', 'TH', '', NULL, NULL, NULL, 0),
(4, '0001', 'มอส', 'อำนวยการ', '', NULL, '', '', '', '', '', 10, 'กรุงเทพมหานคร', '10000', 'TH', '', NULL, NULL, NULL, 0),
(5, '0003', 'นาย A', 'แผนกประถม', '', NULL, '', '', '', '', '', 10, 'กรุงเทพมหานคร', '10000', 'TH', '', NULL, NULL, NULL, 0),
(6, '0004', 'นาย B', 'แผนกมัธยม', '', NULL, '', '', '', '', '', 10, 'กรุงเทพมหานคร', '10000', 'TH', '', NULL, NULL, NULL, 0),
(7, 'CU0007', 'Advice', 'ช้างแผือก', '', NULL, '', '', '', '', '', 10, 'กรุงเทพมหานคร', '10000', 'TH', '', NULL, NULL, NULL, 0),
(8, 'CU0008', 'JIB', 'ไอค่อน', '', NULL, '', '', '', '', '', 10, 'กรุงเทพมหานคร', '10000', 'TH', '', NULL, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `app_inventory`
--

CREATE TABLE `app_inventory` (
  `id` int(11) NOT NULL,
  `category_id` varchar(10) DEFAULT '0',
  `topic` varchar(150) DEFAULT NULL,
  `cost` double DEFAULT 0,
  `vat` double DEFAULT 0,
  `stock` double DEFAULT 0,
  `count_stock` tinyint(1) DEFAULT 1,
  `inuse` tinyint(1) NOT NULL DEFAULT 1,
  `create_date` date DEFAULT NULL,
  `unit` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `app_inventory`
--

INSERT INTO `app_inventory` (`id`, `category_id`, `topic`, `cost`, `vat`, `stock`, `count_stock`, `inuse`, `create_date`, `unit`) VALUES
(1, '1', 'จอมอนิเตอร์ ACER S220HQLEBD', 3500, 2, 5, 1, 1, '2018-08-28', NULL),
(2, '2', 'ASUS A550JX', 25000, 0, 1, 1, 1, '2018-08-28', NULL),
(3, '3', 'Crucial 4GB DDR3L&amp;1600 SODIMM', 500, 0, 10, 1, 1, '2018-08-28', '0'),
(4, '3', 'เมาส์', 250, 0, 20, 1, 1, '2025-10-16', '0'),
(6, '3', 'หมึก Canon G2000 003', 500, 0, 9, 1, 1, '2025-10-16', '0');

-- --------------------------------------------------------

--
-- Table structure for table `app_inventory_items`
--

CREATE TABLE `app_inventory_items` (
  `product_no` varchar(150) NOT NULL,
  `inventory_id` int(11) NOT NULL,
  `topic` varchar(100) NOT NULL,
  `price` double DEFAULT 0,
  `cut_stock` double DEFAULT 1,
  `unit` varchar(50) DEFAULT NULL,
  `instock` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `app_inventory_items`
--

INSERT INTO `app_inventory_items` (`product_no`, `inventory_id`, `topic`, `price`, `cut_stock`, `unit`, `instock`) VALUES
('A550JX', 2, '', 29500, 1, 'เครื่อง', 1),
('IF111/036/1', 3, '', 790, 1, 'ชิ้น', 1),
('PP00004', 6, '', 500, 1, '1', 1),
('S220HQLEBD', 1, '', 4150, 1, 'เครื่อง', 1);

-- --------------------------------------------------------

--
-- Table structure for table `app_inventory_meta`
--

CREATE TABLE `app_inventory_meta` (
  `inventory_id` int(11) NOT NULL,
  `name` varchar(20) NOT NULL,
  `value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `app_language`
--

CREATE TABLE `app_language` (
  `id` int(11) NOT NULL,
  `key` text NOT NULL,
  `type` varchar(5) NOT NULL,
  `owner` varchar(20) NOT NULL,
  `js` tinyint(1) NOT NULL,
  `th` text DEFAULT NULL,
  `en` text DEFAULT NULL,
  `ma` text CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `la` text CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `app_language`
--

INSERT INTO `app_language` (`id`, `key`, `type`, `owner`, `js`, `th`, `en`, `ma`, `la`) VALUES
(1, 'ACCEPT_ALL', 'text', 'index', 1, 'ยอมรับทั้งหมด', 'Accept all', 'အားလုံးလက်ခံပါ။', 'ຍອມຮັບທັງຫມົດ'),
(2, 'CANCEL', 'text', 'index', 1, 'ยกเลิก', 'Cancel', 'ပယ်ဖျက်ရန်', 'ຍົກເລີກ'),
(3, 'CHANGE_COLOR', 'text', 'index', 1, 'เปลี่ยนสี', 'change color', 'အရောင်ပြောင်းပါ', 'ປ່ຽນສີ'),
(4, 'CHECK', 'text', 'index', 1, 'เลือก', 'check', 'ရွေးချယ်ပါ', 'ເລືອກ'),
(5, 'CHECKBOX', 'text', 'index', 1, 'ตัวเลือก', 'Checkbox', 'ရွေးချယ်စရာများ', 'ກ່ອງກາເຄື່ອງໝາຍ'),
(6, 'COOKIES_SETTINGS', 'text', 'index', 1, 'ตั้งค่าคุกกี้', 'Cookies settings', 'ကွတ်ကီးဆက်တင်များ', 'ຕັ້ງຄ່າຄຸກກີ'),
(7, 'DELETE', 'text', 'index', 1, 'ลบ', 'delete', 'ဖျက်ပစ်ရန်', 'ລຶບ'),
(8, 'DISABLE', 'text', 'index', 1, 'ปิดใช้งาน', 'Disable', 'ပိတ်', 'ປິດໃຊ້ການ'),
(9, 'ENABLE', 'text', 'index', 1, 'เปิดใช้งาน', 'Enable', 'ဖွင့်', 'ເປີດໃຊ້ການ'),
(10, 'INVALID_DATA', 'text', 'index', 1, 'ข้อมูล XXX ไม่ถูกต้อง', 'XXX Invalid data', 'XXX ဒေတာသည်မမှန်ကန်ပါ။', 'ຂໍ້ມູນ XXX ບໍ່ຖືກຕ້ອງ'),
(11, 'ITEM', 'text', 'index', 1, 'รายการ', 'item', NULL, 'ລາຍການ'),
(12, 'ITEMS', 'text', 'index', 1, 'รายการ', 'items', NULL, 'ລາຍການ'),
(13, 'PLEASE_BROWSE_FILE', 'text', 'index', 1, 'กรุณาเลือกไฟล์', 'Please browse file', 'ကျေးဇူးပြု၍ ရွေးချယ်ပါ', 'ກະລຸນາເລືອກແຟ້ມຂໍ້ມູນ'),
(14, 'PLEASE_FILL_IN', 'text', 'index', 1, 'กรุณากรอก', 'Please fill in', 'ကျေးဇူးပြု၍ ဖြည့်ပါ', 'ກະລຸນາພີ່ມ'),
(15, 'PLEASE_SAVE_BEFORE_CONTINUING', 'text', 'index', 1, 'กรุณาบันทึก ก่อนดำเนินการต่อ', 'Please save before continuing', 'ဆက်မလုပ်မီသိမ်းဆည်းပါ', 'ກະລຸນາບັນທຶກກ່ອນດຳເນີນການຕໍ່'),
(16, 'PLEASE_SELECT', 'text', 'index', 1, 'กรุณาเลือก', 'Please select', 'ကျေးဇူးပြု၍ ရွေးချယ်ပါ', 'ກະລຸນາເລືອກ'),
(17, 'PLEASE_SELECT_AT_LEAST_ONE_ITEM', 'text', 'index', 1, 'กรุณาเลือก XXX อย่างน้อย 1 รายการ', 'Please select XXX at least one item', 'ကျေးဇူးပြုပြီးအနည်းဆုံး ၁ XXX ကိုရွေးပါ။', 'ກະລຸນາເລືອກ XXX ຢ່າງໜ້ອຍໜຶ່ງລາຍການ'),
(18, 'SELECT_ALL', 'text', 'index', 1, 'เลือกทั้งหมด', 'select all', 'အားလုံးကိုရွေးပါ', 'ເລືອກທັງໝົດ'),
(19, 'SELECT_NONE', 'text', 'index', 1, 'ไม่เลือกรายการใดเลย', 'select none', 'ရွေးချယ်ထားခြင်းမရှိပါ', 'ບໍ່ເລືອກລາຍການໃດເລີຍ'),
(20, 'SORRY_XXX_NOT_FOUND', 'text', 'index', 1, 'ขออภัย ไม่พบ XXX ที่ต้องการ', 'Sorry XXX not found', 'စိတ်မကောင်းပါ၊ XXX ကိုရှာမတွေ့ပါ။', 'ຂໍອະໄພບໍ່ພົບ XXX ທີ່ຕ້ອງການ'),
(21, 'SUCCESSFULLY_COPIED_TO_CLIPBOARD', 'text', 'index', 1, 'สำเนาไปยังคลิปบอร์ดเรียบร้อย', 'Successfully copied to clipboard', 'clipboard သို့ကူးယူပြီးပြီ', 'ສຳເນົາໄປຍັງຄິບບອດຮຽບຮ້ອຍ'),
(22, 'SUCCESSFULLY_UPLOADED_XXX_FILES', 'text', 'index', 1, 'อัปโหลดเรียบร้อย XXX ไฟล์', 'Successfully uploaded XXX files', 'တင်ထားသော XXX ဖိုင်များ', 'ອັບໂຫຼດຮຽບຮ້ອຍ XXX ແຟ້ມ'),
(23, 'THE_TYPE_OF_FILE_IS_INVALID', 'text', 'index', 1, 'ชนิดของไฟล์ไม่ถูกต้อง', 'The type of file is invalid', 'ဖိုင်အမျိုးအစားမမှန်ကန်ပါ', 'ຊະນິດຂອງແຟ້ມບໍ່ຖືກຕ້ອງ'),
(24, 'UNCHECK', 'text', 'index', 1, 'ไม่เลือก', 'uncheck', 'မရွေးရ', 'ບໍ່ເລືອກ'),
(25, 'YOU_WANT_TO_XXX', 'text', 'index', 1, 'คุณต้องการ XXX ?', 'You want to XXX ?', 'XXX လိုချင်လား', 'ທ່ານບໍ່ຕ້ອງການ XXX ?'),
(26, 'YOU_WANT_TO_XXX_THE_SELECTED_ITEMS', 'text', 'index', 1, 'คุณต้องการ XXX รายการที่เลือก ?', 'You want to XXX the selected items ?', 'XXX ပစ္စည်းများကိုသင်ရွေးချင်ပါသလား။', 'ທ່ານຕ້ອງການ XXX ລາຍການທີ່ເລືອກ?'),
(27, 'BANKS', 'array', 'index', 0, 'a:8:{s:3:\"scb\";s:48:\"ธนาคารไทยพาณิชย์\";s:5:\"kbank\";s:42:\"ธนาคารกสิกรไทย\";s:3:\"ktb\";s:39:\"ธนาคารกรุงไทย\";s:3:\"bbl\";s:39:\"ธนาคารกรุงเทพ\";s:3:\"bay\";s:39:\"ธนาคารกรุงศรี\";s:5:\"nbank\";s:33:\"ธนาคารธนชาต\";s:3:\"tmb\";s:39:\"ธนาคารทหารไทย\";s:3:\"gsb\";s:39:\"ธนาคารทหารไทย\";}', 'a:8:{s:3:\"scb\";s:48:\"ธนาคารไทยพาณิชย์\";s:5:\"kbank\";s:42:\"ธนาคารกสิกรไทย\";s:3:\"ktb\";s:39:\"ธนาคารกรุงไทย\";s:3:\"bbl\";s:39:\"ธนาคารกรุงเทพ\";s:3:\"bay\";s:39:\"ธนาคารกรุงศรี\";s:5:\"nbank\";s:33:\"ธนาคารธนชาต\";s:3:\"tmb\";s:39:\"ธนาคารทหารไทย\";s:3:\"gsb\";s:39:\"ธนาคารทหารไทย\";}', 'a:8:{s:3:\"scb\";s:48:\"ธนาคารไทยพาณิชย์\";s:5:\"kbank\";s:42:\"ธนาคารกสิกรไทย\";s:3:\"ktb\";s:39:\"ธนาคารกรุงไทย\";s:3:\"bbl\";s:39:\"ธนาคารกรุงเทพ\";s:3:\"bay\";s:39:\"ธนาคารกรุงศรี\";s:5:\"nbank\";s:33:\"ธนาคารธนชาต\";s:3:\"tmb\";s:39:\"ธนาคารทหารไทย\";s:3:\"gsb\";s:39:\"ธนาคารทหารไทย\";}', 'a:8:{s:3:\"scb\";s:48:\"ธนาคารไทยพาณิชย์\";s:5:\"kbank\";s:42:\"ธนาคารกสิกรไทย\";s:3:\"ktb\";s:39:\"ธนาคารกรุงไทย\";s:3:\"bbl\";s:39:\"ธนาคารกรุงเทพ\";s:3:\"bay\";s:39:\"ธนาคารกรุงศรี\";s:5:\"nbank\";s:33:\"ธนาคารธนชาต\";s:3:\"tmb\";s:39:\"ธนาคารทหารไทย\";s:3:\"gsb\";s:39:\"ธนาคารทหารไทย\";}'),
(28, 'BOOLEANS', 'array', 'index', 0, 'a:2:{i:0;s:27:\"ปิดใช้งาน\";i:1;s:30:\"เปิดใช้งาน\";}', 'a:2:{i:0;s:7:\"Disable\";i:1;s:7:\"Enabled\";}', 'a:2:{i:0;s:12:\"ပိတ်\";i:1;s:18:\"လုပ်ပါ\";}', 'a:2:{i:0;s:27:\"ປິດໃຊ້ວຽກ\";i:1;s:30:\"ເປີດໃຊ້ວຽກ\";}'),
(29, 'COOKIE_NECESSARY_DETAILS', 'text', 'index', 0, 'คุกกี้พื้นฐาน จำเป็นต่อการใช้งานเว็บไซต์ ใช้เพื่อการรักษาความปลอดภัยและให้เว็บไซต์สามารถทำงานได้อย่างถูกต้อง', 'Basic Cookies necessary to use the website It is used for security purposes and to enable the website to function properly.', 'အခြေခံ Cookies website ကိုအသုံးပြုရန်လိုအပ်သည်။ ၎င်းကို လုံခြုံရေးရည်ရွယ်ချက်များအတွက် အသုံးပြုပြီး ဝဘ်ဆိုဒ်ကို ကောင်းမွန်စွာလုပ်ဆောင်နိုင်စေရန်အတွက် အသုံးပြုပါသည်။', 'ຄຸກກີພື້ນຖານ ມີຄວາມຈໍາເປັນໃນການນໍາໃຊ້ເວັບໄຊທ໌ ມັນຖືກນໍາໃຊ້ເພື່ອຈຸດປະສົງຄວາມປອດໄພແລະເພື່ອໃຫ້ເວັບໄຊທ໌ເຮັດວຽກຢ່າງຖືກຕ້ອງ.'),
(30, 'COOKIE_POLICY_DETAILS', 'text', 'index', 0, 'เราใช้คุกกี้ (Cookies) หรือเทคโนโลยีที่คล้ายคลึงกันเท่าที่จำเป็น เพื่อใช้ในการเข้าถึงสินค้าหรือบริการ และติดตามการใช้งานของคุณเท่านั้น หากคุณไม่ต้องการให้มีคุกกี้ไว้ในคอมพิวเตอร์ของคุณ คุณสามารถตั้งค่าบราวเซอร์เพื่อปฏิเสธการจัดเก็บคุกกี้ก่อนที่จะใช้งานเว็บไซต์ หรือใช้โหมดไม่ระบุตัวตนเพื่อเข้าใช้งานเว็บไซต์ก็ได้', 'We use cookies (Cookies) or similar technologies to the extent necessary. for use in accessing goods or services and only track your usage If you do not want cookies on your computer You can set your browser to refuse the storage of cookies. Before using the website or use incognito mode to access the website', 'ကျွန်ုပ်တို့သည် လိုအပ်သည့်အတိုင်းအတာအထိ ကွတ်ကီးများ (Cookies) သို့မဟုတ် အလားတူနည်းပညာများကို အသုံးပြုပါသည်။ ကုန်စည် သို့မဟုတ် ဝန်ဆောင်မှုများကို ရယူအသုံးပြုရန် သင်၏အသုံးပြုမှုကိုသာ ခြေရာခံပါ။ သင့်ကွန်ပြူတာတွင် ကွတ်ကီးများကို မလိုချင်ပါက၊ ဝဘ်ဆိုက်ကို အသုံးမပြုမီ ကွတ်ကီးသိုလှောင်မှုကို ငြင်းဆိုရန် သင့်ဘရောက်ဆာကို သင်သတ်မှတ်နိုင်သည်။ ဝဘ်ဆိုက်ကို ဝင်ရောက်ကြည့်ရှုရန် ရုပ်ဖျက်မုဒ်ကိုလည်း သင်အသုံးပြုနိုင်ပါသည်။', 'ພວກເຮົາໃຊ້ຄຸກກີ (Cookies) ຫຼືເຕັກໂນໂລຢີທີ່ຄ້າຍຄືກັນໃນຂອບເຂດທີ່ຈໍາເປັນ. ສໍາລັບການນໍາໃຊ້ໃນການເຂົ້າເຖິງສິນຄ້າຫຼືການບໍລິການ ແລະພຽງແຕ່ຕິດຕາມການນໍາໃຊ້ຂອງທ່ານ ຖ້າ​ຫາກ​ວ່າ​ທ່ານ​ບໍ່​ຕ້ອງ​ການ cookies ໃນ​ຄອມ​ພິວ​ເຕີ​ຂອງ​ທ່ານ​ ທ່ານສາມາດຕັ້ງຕົວທ່ອງເວັບຂອງທ່ານເພື່ອປະຕິເສດການເກັບຮັກສາ cookies. ກ່ອນທີ່ຈະນໍາໃຊ້ເວັບໄຊທ໌ ທ່ານຍັງສາມາດໃຊ້ໂໝດບໍ່ເປີດເຜີຍຕົວຕົນເພື່ອເຂົ້າຫາເວັບໄຊທ໌ໄດ້.'),
(31, 'COUNT_STOCK', 'array', 'index', 0, 'a:3:{i:0;s:33:\"ไม่นับสต๊อก\";i:1;s:33:\"นับสต๊อกรวม\";i:2;s:126:\"นับสต๊อกแยก (เช่นสินค้าที่มีซีเรียลนัมเบอร์)\";}', 'a:3:{i:0;s:18:\"Not counting stock\";i:1;s:17:\"Total stock count\";i:2;s:59:\"Separate inventory count (e.g. products with serial number)\";}', 'a:3:{i:0;s:54:\"စတော့ရှယ်ယာရေတွက်မ\";i:1;s:87:\"စုစုပေါင်းစတော့ရှယ်ယာအရေအတွက်\";i:2;s:156:\"သီးခြားစာရင်းအရေအတွက် (ဥပမာနံပါတ်နှင့်အတူထုတ်ကုန်များ)\";}', 'a:3:{i:0;s:39:\"ບໍ່ນັບຫຼັກຊັບ\";i:1;s:30:\"ນັບຫຸ້ນລວມ\";i:2;s:151:\"ນັບຫຸ້ນແຍກຕ່າງຫາກ (ເຊັ່ນ: ຜະລິດຕະພັນທີ່ມີ ຈຳ ນວນຕົວເລກ)\";}'),
(32, 'CURRENCY_UNITS', 'array', 'index', 0, 'a:4:{s:3:\"THB\";s:9:\"บาท\";s:3:\"USD\";s:24:\"ดอลล่าร์\";s:3:\"LAK\";s:9:\"กีบ\";s:3:\"MMK\";s:12:\"จ๊าด\";}', 'a:4:{s:3:\"THB\";s:4:\"Baht\";s:3:\"USD\";s:6:\"Dollar\";s:3:\"LAK\";s:3:\"Kip\";s:3:\"MMK\";s:5:\"Kyart\";}', 'a:4:{s:3:\"THB\";s:9:\"ဘတ်\";s:3:\"USD\";s:18:\"ဒေါ်လာ\";s:3:\"LAK\";s:3:\"Kip\";s:3:\"MMK\";s:3:\"Kip\";}', 'a:4:{s:3:\"THB\";s:9:\"ບາດ\";s:3:\"USD\";s:12:\"ໂດລາ\";s:3:\"LAK\";s:9:\"ກີບ\";s:3:\"MMK\";s:5:\"Kyart\";}'),
(33, 'DATE_FORMAT', 'text', 'index', 0, 'd M Y เวลา H:i น.', 'd M Y, H:i', 'd M Y အချိန် H:i', 'd M Y ເວລາ H:i'),
(34, 'DATE_LONG', 'array', 'index', 0, 'a:7:{i:0;s:21:\"อาทิตย์\";i:1;s:18:\"จันทร์\";i:2;s:18:\"อังคาร\";i:3;s:9:\"พุธ\";i:4;s:24:\"พฤหัสบดี\";i:5;s:15:\"ศุกร์\";i:6;s:15:\"เสาร์\";}', 'a:7:{i:0;s:6:\"Sunday\";i:1;s:6:\"Monday\";i:2;s:7:\"Tuesday\";i:3;s:9:\"Wednesday\";i:4;s:8:\"Thursday\";i:5;s:6:\"Friday\";i:6;s:8:\"Saturday\";}', 'a:7:{i:0;s:36:\"တနင်္ဂနွေနေ့\";i:1;s:30:\"တနင်္လာနေ့\";i:2;s:27:\"အင်္ဂါနေ့\";i:3;s:33:\"ဗုဒ္ဓဟူးနေ့\";i:4;s:33:\"ကြာသပတေးနေ့\";i:5;s:27:\"သောကြာနေ့\";i:6;s:18:\"စနေနေ့\";}', 'a:7:{i:0;s:15:\"ອາທິດ\";i:1;s:9:\"ຈັນ\";i:2;s:18:\"ອັງຄານ\";i:3;s:9:\"ພຸດ\";i:4;s:15:\"ພະຫັດ\";i:5;s:9:\"ສຸກ\";i:6;s:12:\"ເສົາ\";}'),
(35, 'DATE_SHORT', 'array', 'index', 0, 'a:7:{i:0;s:7:\"อา.\";i:1;s:4:\"จ.\";i:2;s:4:\"อ.\";i:3;s:4:\"พ.\";i:4;s:7:\"พฤ.\";i:5;s:4:\"ศ.\";i:6;s:4:\"ส.\";}', 'a:7:{i:0;s:2:\"Su\";i:1;s:2:\"Mo\";i:2;s:2:\"Tu\";i:3;s:2:\"We\";i:4;s:2:\"Th\";i:5;s:2:\"Fr\";i:6;s:2:\"Sa\";}', 'a:7:{i:0;s:4:\"SUN.\";i:1;s:4:\"MON.\";i:2;s:4:\"TUE.\";i:3;s:4:\"WED.\";i:4;s:4:\"THU.\";i:5;s:4:\"FRI.\";i:6;s:4:\"SAT.\";}', 'a:7:{i:0;s:4:\"ທ.\";i:1;s:4:\"ຈ.\";i:2;s:4:\"ຄ.\";i:3;s:4:\"ພ.\";i:4;s:4:\"ພ.\";i:5;s:4:\"ສ.\";i:6;s:4:\"ສ.\";}'),
(36, 'INVENTORY_CATEGORIES', 'array', 'index', 0, 'a:1:{s:11:\"category_id\";s:24:\"หมวดหมู่\";}', 'a:1:{s:11:\"category_id\";s:8:\"Category\";}', 'a:1:{s:11:\"category_id\";s:30:\"အမျိုးအစား\";}', 'a:1:{s:11:\"category_id\";s:9:\"ໝວດ\";}'),
(37, 'INVENTORY_METAS', 'array', 'index', 0, 'a:2:{s:11:\"description\";s:24:\"คำอธิบาย\";s:6:\"detail\";s:30:\"รายละเอียด\";}', 'a:2:{s:11:\"description\";s:11:\"Description\";s:6:\"detail\";s:6:\"Detail\";}', 'a:2:{s:11:\"description\";s:30:\"ဖော်ပြချက်\";s:6:\"detail\";s:24:\"အသေးစိတ်\";}', 'a:2:{s:11:\"description\";s:27:\"ຄຳອະທິບາຍ\";s:6:\"detail\";s:27:\"ລາຍະລະອຽດ\";}'),
(38, 'INVENTORY_STATUS', 'array', 'index', 0, 'a:3:{s:2:\"IN\";s:21:\"รับเข้า\";s:3:\"OUT\";s:18:\"ขายออก\";s:3:\"RET\";s:9:\"คืน\";}', 'a:3:{s:2:\"IN\";s:7:\"Receive\";s:3:\"OUT\";s:4:\"Sold\";s:3:\"RET\";s:8:\"Returned\";}', 'a:3:{s:2:\"IN\";s:36:\"လက်ခံရရှိသည်\";s:3:\"OUT\";s:42:\"ရောင်းချခဲ့သည်\";s:3:\"RET\";s:18:\"ပြန်လာ\";}', 'a:3:{s:2:\"IN\";s:9:\"ຮັບ\";s:3:\"OUT\";s:9:\"ຂາຍ\";s:3:\"RET\";s:9:\"ຄົນ\";}'),
(39, 'INVENTORY_TYPIES', 'array', 'index', 0, 'a:2:{s:4:\"sell\";s:18:\"งานขาย\";s:3:\"buy\";s:21:\"จัดซื้อ\";}', 'a:2:{s:4:\"sell\";s:5:\"Sales\";s:3:\"buy\";s:8:\"Purchase\";}', 'a:2:{s:4:\"sell\";s:18:\"ရောင်း\";s:3:\"buy\";s:24:\"ဝယ်ယူပါ။\";}', 'a:2:{s:4:\"sell\";s:9:\"ຂາຍ\";s:3:\"buy\";s:9:\"ຊື້\";}'),
(40, 'LINE_FOLLOW_MESSAGE', 'text', 'index', 0, 'สวัสดี คุณ :name นี่คือบัญชีทางการของ :title เราจะส่งข่าวสารถึงคุณผ่านช่องทางนี้', 'Hello, :name This is :title official account. We will send you news via this channel.', 'မင်္ဂလာပါ :name ဤသည်မှာ :title ၏တရားဝင်အကောင့်ဖြစ်သည်။ ဤချန်နယ်မှသတင်းများပေးပို့ပါမည်။', 'ສະບາຍດີ, :name ນີ້ແມ່ນບັນຊີທາງການຂອງ :title ພວກເຮົາຈະສົ່ງຂ່າວໃຫ້ທ່ານຜ່ານຊ່ອງທາງນີ້.'),
(41, 'LINE_REPLY_MESSAGE', 'text', 'index', 0, 'ขออภัยไม่สามารถตอบกลับข้อความนี้ได้', 'Sorry, can&#039;t reply to this message.', 'ဝမ်းနည်းပါသည်၊ ဤစာကို စာပြန်၍မရပါ။', 'ຂໍອະໄພ, ບໍ່ສາມາດຕອບກັບຂໍ້ຄວາມນີ້ໄດ້.'),
(42, 'LOGIN_FIELDS', 'array', 'index', 0, 'a:4:{s:8:\"username\";s:30:\"ชื่อผู้ใช้\";s:5:\"email\";s:15:\"อีเมล\";s:5:\"phone\";s:39:\"เบอร์โทรศัพท์\";s:7:\"id_card\";s:30:\"เลขประชาชน\";}', 'a:4:{s:8:\"username\";s:8:\"Username\";s:5:\"email\";s:5:\"Email\";s:5:\"phone\";s:5:\"Phone\";s:7:\"id_card\";s:18:\"Identification No.\";}', 'a:4:{s:8:\"username\";s:24:\"အီးမေးလ်\";s:5:\"email\";s:24:\"အီးမေးလ်\";s:5:\"phone\";s:33:\"ဖုန်းနံပါတ်\";s:7:\"id_card\";s:36:\"နံပါတ်ပြည်သူ\";}', 'a:4:{s:8:\"username\";s:27:\"ຊື່ຜູ້ໃຊ້\";s:5:\"email\";s:15:\"ອີເມວ\";s:5:\"phone\";s:30:\"ເບີໂທລະສັບ\";s:7:\"id_card\";s:39:\"ເລກບັດປະຈຳຕົວ\";}'),
(43, 'MAIL_PROGRAMS', 'array', 'index', 0, 'a:3:{i:0;s:43:\"ส่งจดหมายด้วย PHP\";i:1;s:72:\"ส่งจดหมายด้วย PHPMailer+SMTP (แนะนำ)\";i:2;s:58:\"ส่งจดหมายด้วย PHPMailer+PHP Mail\";}', 'a:3:{i:0;s:13:\"Send with PHP\";i:1;s:38:\"Send with PHPMailer+SMTP (recommended)\";i:2;s:28:\"Send with PHPMailer+PHP Mail\";}', 'a:2:{i:0;s:46:\"အီးမေးလ်ပို့ပါ PHP\";i:1;s:91:\"အီးမေးလ်ပို့ပါ PHPMailer (အကြံပြုပါသည်)\";}', 'a:3:{i:0;s:46:\"ສົ່ງຈົດໝາຍດ້ວຍ PHP\";i:1;s:75:\"ສົ່ງຈົດໝາຍດ້ວຍ PHPMailer+SMTP (ແນະນຳ)\";i:2;s:61:\"ສົ່ງຈົດໝາຍດ້ວຍ PHPMailer+PHP Mail\";}'),
(44, 'MONTH_LONG', 'array', 'index', 0, 'a:12:{i:1;s:18:\"มกราคม\";i:2;s:30:\"กุมภาพันธ์\";i:3;s:18:\"มีนาคม\";i:4;s:18:\"เมษายน\";i:5;s:21:\"พฤษภาคม\";i:6;s:24:\"มิถุนายน\";i:7;s:21:\"กรกฎาคม\";i:8;s:21:\"สิงหาคม\";i:9;s:21:\"กันยายน\";i:10;s:18:\"ตุลาคม\";i:11;s:27:\"พฤศจิกายน\";i:12;s:21:\"ธันวาคม\";}', 'a:12:{i:1;s:7:\"January\";i:2;s:8:\"February\";i:3;s:5:\"March\";i:4;s:5:\"April\";i:5;s:3:\"May\";i:6;s:4:\"June\";i:7;s:4:\"July\";i:8;s:6:\"August\";i:9;s:9:\"September\";i:10;s:7:\"October\";i:11;s:8:\"November\";i:12;s:8:\"December\";}', 'a:12:{i:1;s:27:\"ဇန်နဝါရီလ\";i:2;s:33:\"ဖေဖော်ဝါရီလ\";i:3;s:12:\"မတ်လ\";i:4;s:12:\"ပြီလ\";i:5;s:9:\"မေလ\";i:6;s:12:\"ဇွန်\";i:7;s:24:\"ဇူလိုင်လ\";i:8;s:21:\"သြဂုတ်လ\";i:9;s:24:\"စက်တင်ဘာ\";i:10;s:30:\"အောက်တိုဘာ\";i:11;s:24:\"နိုဝင်ဘာ\";i:12;s:21:\"ဒီဇင်ဘာ\";}', 'a:12:{i:1;s:18:\"ມັງກອນ\";i:2;s:15:\"ກຸມພາ\";i:3;s:12:\"ມີນາ\";i:4;s:12:\"ເມສາ\";i:5;s:21:\"ພຶດສະພາ\";i:6;s:18:\"ມິຖຸນາ\";i:7;s:21:\"ກໍລະກົດ\";i:8;s:15:\"ສິງຫາ\";i:9;s:15:\"ກັນຍາ\";i:10;s:12:\"ຕຸລາ\";i:11;s:15:\"ພະຈິກ\";i:12;s:15:\"ທັນວາ\";}'),
(45, 'MONTH_SHORT', 'array', 'index', 0, 'a:12:{i:1;s:8:\"ม.ค.\";i:2;s:8:\"ก.พ.\";i:3;s:11:\"มี.ค.\";i:4;s:11:\"เม.ย.\";i:5;s:8:\"พ.ค.\";i:6;s:11:\"มิ.ย.\";i:7;s:8:\"ก.ค.\";i:8;s:8:\"ส.ค.\";i:9;s:8:\"ก.ย.\";i:10;s:8:\"ต.ค.\";i:11;s:8:\"พ.ย.\";i:12;s:8:\"ธ.ค.\";}', 'a:12:{i:1;s:3:\"Jan\";i:2;s:3:\"Feb\";i:3;s:3:\"Mar\";i:4;s:3:\"Apr\";i:5;s:3:\"May\";i:6;s:3:\"Jun\";i:7;s:3:\"Jul\";i:8;s:3:\"Aug\";i:9;s:3:\"Sep\";i:10;s:3:\"Oct\";i:11;s:3:\"Nov\";i:12;s:3:\"Dec\";}', 'a:12:{i:1;s:4:\"JAN.\";i:2;s:4:\"FEB.\";i:3;s:4:\"MAR.\";i:4;s:4:\"APR.\";i:5;s:4:\"MAY.\";i:6;s:4:\"JUN.\";i:7;s:4:\"JUL.\";i:8;s:4:\"AUG.\";i:9;s:5:\"SEPT.\";i:10;s:4:\"OCT.\";i:11;s:4:\"NOV.\";i:12;s:4:\"DEC.\";}', 'a:12:{i:1;s:8:\"ມ.ກ.\";i:2;s:8:\"ກ.ພ.\";i:3;s:11:\"ມີ.ນ.\";i:4;s:11:\"ເມ.ສ.\";i:5;s:8:\"ພ.ພ.\";i:6;s:11:\"ມິ.ນ.\";i:7;s:8:\"ກ.ກ.\";i:8;s:8:\"ສ.ຫ.\";i:9;s:8:\"ກ.ຍ.\";i:10;s:8:\"ຕ.ລ.\";i:11;s:8:\"ພ.ຈ.\";i:12;s:8:\"ທ.ວ.\";}'),
(46, 'Name', 'text', 'index', 0, 'ชื่อ นามสกุล', 'Name Surname', 'ပထမအမည်', 'ຊື່ ນາມສະກຸນ'),
(47, 'ORDER_STATUS', 'array', 'index', 0, 'a:5:{s:3:\"QUO\";s:30:\"ใบเสนอราคา\";s:3:\"OUT\";s:42:\"ใบเสร็จรับเงิน\";s:2:\"PO\";s:30:\"ใบสั่งซื้อ\";s:3:\"RET\";s:33:\"ใบคืนสินค้า\";s:2:\"IN\";s:33:\"ใบรับสินค้า\";}', 'a:5:{s:3:\"QUO\";s:9:\"Quotation\";s:3:\"OUT\";s:7:\"Receipt\";s:2:\"PO\";s:14:\"Purchase Order\";s:3:\"RET\";s:7:\"Returns\";s:2:\"IN\";s:19:\"Receiving inventory\";}', 'a:5:{s:3:\"QUO\";s:27:\"ဈေးနှုန်း\";s:3:\"OUT\";s:45:\"လက်ခံဖြတ်ပိုင်း\";s:2:\"PO\";s:57:\"ဝယ်ယူရန်မှာကြားလွှာ\";s:3:\"RET\";s:18:\"ပြန်လာ\";s:2:\"IN\";s:15:\"ပြေစာ\";}', 'a:5:{s:3:\"QUO\";s:18:\"ວົງຢືມ\";s:3:\"OUT\";s:27:\"ໃບຮັບເງິນ\";s:2:\"PO\";s:21:\"ສັ່ງຊື້\";s:3:\"RET\";s:15:\"ກັບມາ\";s:2:\"IN\";s:15:\"ໃບຮັບ\";}'),
(48, 'PERMISSIONS', 'array', 'index', 0, 'a:3:{s:10:\"can_config\";s:60:\"สามารถตั้งค่าระบบได้\";s:22:\"can_view_usage_history\";s:93:\"สามารถดูประวัติการใช้งานระบบได้\";s:12:\"can_customer\";s:102:\"สามารถบริหารจัดการรายชื่อลูกค้าได้\";}', 'a:3:{s:10:\"can_config\";s:24:\"Can configure the system\";s:22:\"can_view_usage_history\";s:33:\"Able to view system usage history\";s:12:\"can_customer\";s:28:\"Can manage the customer list\";}', 'a:3:{s:10:\"can_config\";s:66:\"စနစ်ကိုသတ်မှတ်နိုင်သည်\";s:22:\"can_view_usage_history\";s:130:\"စနစ်အသုံးပြုမှုမှတ်တမ်းကို ကြည့်ရှုနိုင်သည်။\";s:12:\"can_customer\";s:115:\"ဖောက်သည်စာရင်းကို စီမံခန့်ခွဲနိုင်ရမည်။\";}', 'a:3:{s:10:\"can_config\";s:60:\"ສາມາດຕັ້ງຄ່າລະບົບໄດ້\";s:22:\"can_view_usage_history\";s:90:\"ສາມາດເບິ່ງປະຫວັດການນໍາໃຊ້ລະບົບ\";s:12:\"can_customer\";s:69:\"ສາມາດຈັດການລາຍຊື່ລູກຄ້າ\";}'),
(49, 'PERSON_TYPIES', 'array', 'index', 0, 'a:2:{i:0;s:33:\"บุคคลธรรมดา\";i:1;s:27:\"นิติบุคคล\";}', 'a:2:{i:0;s:14:\"Natural Person\";i:1;s:15:\"Juristic Person\";}', 'a:2:{i:0;s:30:\"တစ်ဦးချင်း\";i:1;s:51:\"တရားဝင်အဖွဲ့အစည်း\";}', 'a:2:{i:0;s:18:\"ບຸກຄົນ\";i:1;s:30:\"ນິຕິບຸກຄົນ\";}'),
(50, 'PUBLISHEDS', 'array', 'index', 0, 'a:2:{i:0;s:45:\"ระงับการเผยแพร่\";i:1;s:21:\"เผยแพร่\";}', 'a:2:{i:0;s:11:\"Unpublished\";i:1;s:9:\"Published\";}', 'a:2:{i:0;s:45:\"ရပ်ဆိုင်းထားသည်\";i:1;s:27:\"ထုတ်ဝေမှု\";}', 'a:2:{i:0;s:45:\"ລະງັບການເຜີຍແຜ່\";i:1;s:21:\"ເຜີຍແຜ່\";}'),
(51, 'SEXES', 'array', 'index', 0, 'a:3:{s:1:\"u\";s:21:\"ไม่ระบุ\";s:1:\"f\";s:12:\"หญิง\";s:1:\"m\";s:9:\"ชาย\";}', 'a:3:{s:1:\"u\";s:13:\"Not specified\";s:1:\"f\";s:6:\"Female\";s:1:\"m\";s:4:\"Male\";}', 'a:2:{s:1:\"f\";s:30:\"အမျိုးသမီး\";s:1:\"m\";s:30:\"လူတစ်ယောက်\";}', 'a:3:{s:1:\"u\";s:30:\"ບໍ່ໄດ້ລະບຸ\";s:1:\"f\";s:9:\"ຍິງ\";s:1:\"m\";s:9:\"ຊາຍ\";}'),
(52, 'SMS_SENDER_COMMENT', 'text', 'index', 0, 'บาง Package อาจไม่สามารถกำหนดชื่อผู้ส่งได้ กรุณาตรวจสอบกับผู้ให้บริการ', 'Some packages may not be able to assign the sender name. Please check with the service provider.', 'အချို့သော ပက်ကေ့ခ်ျများသည် ပေးပို့သူ၏အမည်ကို သတ်မှတ်၍မရပါ။ ကျေးဇူးပြု၍ ဝန်ဆောင်မှုပေးသူနှင့် စစ်ဆေးပါ။', 'ບາງແພັກເກັດອາດບໍ່ສາມາດມອບຊື່ຜູ້ສົ່ງໄດ້. ກະລຸນາກວດສອບກັບຜູ້ໃຫ້ບໍລິການ.'),
(53, 'SMTPSECURIES', 'array', 'index', 0, 'a:2:{s:0:\"\";s:57:\"การเชื่อมต่อแบบปกติ\";s:3:\"ssl\";s:72:\"การเชื่อมต่อที่ปลอดภัย (SSL)\";}', 'a:2:{s:0:\"\";s:10:\"Clear Text\";s:3:\"ssl\";s:38:\"Server using a secure connection (SSL)\";}', 'a:2:{s:0:\"\";s:51:\"ပုံမှန်ဆက်သွယ်မှု\";s:3:\"ssl\";s:66:\"လုံခြုံသောဆက်သွယ်မှု (SSL)\";}', 'a:2:{s:0:\"\";s:66:\"ການເຊື່ອມຕໍ່ແບບປົກກະຕິ\";s:3:\"ssl\";s:66:\"ການເຊື່ອມຕໍ່ທີ່ປອດໄຟ (SSL)\";}'),
(54, 'TAX_STATUS', 'array', 'index', 0, 'a:3:{i:0;s:27:\"ไม่มีภาษี\";i:1;s:21:\"แยกภาษี\";i:2;s:21:\"รวมภาษี\";}', 'a:3:{i:0;s:6:\"No Tax\";i:1;s:11:\"Exclude Tax\";i:2;s:11:\"Include Tax\";}', 'a:3:{i:0;s:33:\"အခွန်မရှိပါ\";i:1;s:51:\"အခွန်နှုတ်ယူခြင်း\";i:2;s:39:\"အခွန်ပါ၀င်သည်\";}', 'a:3:{i:0;s:27:\"ບໍ່ມີພາສີ\";i:1;s:33:\"ການຫັກອາກອນ\";i:2;s:21:\"ພາສີລວມ\";}'),
(55, 'THEME_WIDTH', 'array', 'index', 0, 'a:3:{s:7:\"default\";s:33:\"ค่าเริ่มต้น\";s:4:\"wide\";s:15:\"กว้าง\";s:9:\"fullwidth\";s:30:\"กว้างพิเศษ\";}', 'a:3:{s:7:\"default\";s:7:\"Default\";s:4:\"wide\";s:4:\"Wide\";s:9:\"fullwidth\";s:10:\"Extra wide\";}', 'a:3:{s:7:\"default\";s:15:\"ပုံသေ\";s:4:\"wide\";s:39:\"ကျယ်ပြန့်သည်။\";s:9:\"fullwidth\";s:33:\"ပိုကျယ်တယ်။\";}', 'a:3:{s:7:\"default\";s:36:\"ຄ່າເລີ່ມຕົ້ນ\";s:4:\"wide\";s:15:\"ກວ້າງ\";s:9:\"fullwidth\";s:30:\"ກວ້າງພິເສດ\";}'),
(56, 'TIME_FORMAT', 'text', 'index', 0, 'H:i น.', 'H:i', 'H:i', 'H:i'),
(57, 'WH_TAX', 'array', 'index', 0, 'a:7:{i:0;s:27:\"ไม่มีภาษี\";s:4:\"0.75\";s:5:\"0.75%\";i:1;s:4:\"1.0%\";i:2;s:4:\"2.0%\";i:3;s:4:\"3.0%\";i:5;s:4:\"5.0%\";i:10;s:5:\"10.0%\";}', 'a:7:{i:0;s:6:\"No Tax\";s:4:\"0.75\";s:5:\"0.75%\";i:1;s:4:\"1.0%\";i:2;s:4:\"2.0%\";i:3;s:4:\"3.0%\";i:5;s:4:\"5.0%\";i:10;s:5:\"10.0%\";}', 'a:7:{i:0;s:33:\"အခွန်မရှိပါ\";s:4:\"0.75\";s:5:\"0.75%\";i:1;s:4:\"1.0%\";i:2;s:4:\"2.0%\";i:3;s:4:\"3.0%\";i:5;s:4:\"5.0%\";i:10;s:5:\"10.0%\";}', 'a:7:{i:0;s:27:\"ບໍ່ມີພາສີ\";s:4:\"0.75\";s:5:\"0.75%\";i:1;s:4:\"1.0%\";i:2;s:4:\"2.0%\";i:3;s:4:\"3.0%\";i:5;s:4:\"5.0%\";i:10;s:5:\"10.0%\";}'),
(58, 'YEAR_OFFSET', 'int', 'index', 0, '543', '0', '0', '0'),
(59, ':name for new members Used when members need to specify', 'text', 'index', 0, ':name เริ่มต้นสำหรับสมาชิกใหม่ ใช้ในกรณีที่สมาชิกจำเป็นต้องระบุ', NULL, ':name အဖွဲ့ဝင်အသစ်များအတွက်စတင်သည်။ အဖွဲ့ဝင်များ သတ်မှတ်ရန် လိုအပ်ပါက အသုံးပြုသည်။', ':name ສໍາລັບສະມາຊິກໃຫມ່ ໃຊ້ໃນເວລາທີ່ສະມາຊິກຕ້ອງການກໍານົດ'),
(60, '0.0.0.0 mean all IP addresses', 'text', 'index', 0, '0.0.0.0 หมายถึงอนุญาตทั้งหมด', NULL, '0.0.0.0 အားလုံးခွင့်ပြုဆိုလိုသည်။', '0.0.0.0 ຫມາຍຄວາມວ່າອະນຸຍາດໃຫ້ທັງຫມົດ'),
(61, '13-digit company registration number', 'text', 'index', 0, 'เลขทะเบียนนิติบุคคล 13 หลัก', NULL, 'ကုမ္ပဏီမှတ်ပုံတင်နံပါတ် 13 ဂဏန်း', 'ເລກທະບຽນວິສາຫະກິດ 13 ຕົວເລກ'),
(62, '13-digit identification number', 'text', 'index', 0, 'หมายเลขประจำตัวประชาชน 13 หลัก', NULL, '13 ဂဏန်းမှတ်ပုံတင်နံပါတ်', 'ເລກປະຈຳຕົວ 13 ຕົວເລກ'),
(63, 'Accept all', 'text', 'index', 0, 'ยอมรับทั้งหมด', NULL, 'အားလုံးလက်ခံပါ။', 'ຍອມຮັບທັງຫມົດ'),
(64, 'Accept member verification request', 'text', 'index', 0, 'ยอมรับคำขอยืนยันสมาชิก', NULL, 'အဖွဲ့ဝင်အတည်ပြုခြင်းတောင်းဆိုချက်ကိုလက်ခံပါ။', 'ຍອມຮັບການຮ້ອງຂໍການຢັ້ງຢືນສະມາຊິກ'),
(65, 'Accept this agreement', 'text', 'index', 0, 'ยอมรับข้อตกลง', NULL, 'သဘောတူညီချက်ကိုလက်ခံပါ။', 'ຍອມຮັບຂໍ້ຕົກລົງ'),
(66, 'Account name', 'text', 'index', 0, 'ชื่อบัญชี', NULL, 'အကောင့်နာမည်', 'ຊື່ບັນຊີ'),
(67, 'Account number', 'text', 'index', 0, 'เลขที่บัญชี', NULL, 'အကောင့်နံပါတ်', 'ເລກບັນຊີ'),
(68, 'Accounting', 'text', 'index', 0, 'ระบบบัญชี', NULL, 'စာရင်းကိုင်စနစ်', 'ລະບົບບັນຊີ'),
(69, 'Accounting settings', 'text', 'index', 0, 'ตั้งค่าระบบบัญชี', NULL, 'စာရင်းအင်းစနစ်ကို set up', 'ການຕັ້ງຄ່າບັນຊີ'),
(70, 'Add', 'text', 'index', 0, 'เพิ่ม', NULL, 'ထည့်ပါ', 'ເພີ່ມ'),
(71, 'Add friend', 'text', 'index', 0, 'เพิ่มเพื่อน', NULL, 'သူငယ်ချင်းကိုထည့်ပါ။', 'ເພີ່ມເພື່ອນ'),
(72, 'Address', 'text', 'index', 0, 'ที่อยู่', NULL, 'လိပ်စာ', 'ທີ່ຢູ່'),
(73, 'Address details', 'text', 'index', 0, 'รายละเอียดที่อยู่', NULL, 'လိပ်စာအသေးစိတ်', 'ລາຍລະອຽດທີ່ຢູ່'),
(74, 'Adjusting entries', 'text', 'index', 0, 'ปรับรายการ', NULL, 'စာရင်းကိုညှိပါ', 'ປັບລາຍການ'),
(75, 'Administrator status It is of utmost importance to do everything', 'text', 'index', 0, 'สถานะผู้ดูแลระบบ มีความสำคัญสูงสุดสามารถทำได้ทุกอย่าง', NULL, 'အုပ်ချုပ်ရေးမှူးအဆင့် အရာအားလုံးကိုလုပ်နိုင်စွမ်းသည်အလွန်အရေးကြီးသည်။', 'ສະຖານະຜູ້ເບິ່ງແຍງລະບົບມີຄວາມສຳຄັນສຸງທີ່ສຸດສາມາດເຮັດໄດ້ທຸກຢ່າງ'),
(76, 'All :count entries, displayed :start to :end, page :page of :total pages', 'text', 'index', 0, 'ทั้งหมด :count รายการ, แสดง :start ถึง :end, หน้าที่ :page จากทั้งหมด :total หน้า', NULL, 'အားလုံး :count စာရင်းပြပါ :start ရန် :end, စာမျက်နှာ :page အားလုံးထံမှ :Total: မျက်နှာ', 'ທັງໝົດ :count ລາຍການ, ສະແດງ :start ເຖິງ :end, ໜ້າທີ່ :page ຈາກທັງໝົດ:total ໜ້າ'),
(77, 'all items', 'text', 'index', 0, 'ทั้งหมด', NULL, 'ပစ္စည်းအားလုံး', 'ທັງໝົດ'),
(78, 'Already registered The system has sent an OTP code to the number you registered. Please check the SMS and enter the code to confirm your phone number.', 'text', 'index', 0, 'ลงทะเบียนเรียบร้อยแล้ว ระบบได้ส่งรหัส OTP ไปยังเบอร์ที่ท่านได้ลงทะเบียนไว้ กรุณาตรวจสอบ SMS แล้วให้นำรหัสมากรอกเพื่อเป็นการยืนยันเบอร์โทรศัพท์', NULL, 'စာရင်းသွင်းပြီးပြီ။ စနစ်သည် သင်စာရင်းသွင်းထားသော နံပါတ်ထံသို့ OTP ကုဒ်တစ်ခု ပေးပို့ထားပြီး ကျေးဇူးပြု၍ SMS ကို စစ်ဆေးပြီး သင့်ဖုန်းနံပါတ်ကို အတည်ပြုရန် ကုဒ်ကို ထည့်သွင်းပါ။', 'ລົງ​ທະ​ບຽນ​ແລ້ວ ລະບົບໄດ້ສົ່ງລະຫັດ OTP ໄປຫາເບີໂທລະສັບທີ່ທ່ານລົງທະບຽນ, ກະລຸນາກວດເບິ່ງ SMS ແລະໃສ່ລະຫັດເພື່ອຢືນຢັນເບີໂທລະສັບຂອງທ່ານ.'),
(79, 'Always enabled', 'text', 'index', 0, 'เปิดใช้งานตลอดเวลา', NULL, 'အမြဲတမ်းပေါ်တယ်။', 'ເປີດສະເໝີ'),
(80, 'Amount', 'text', 'index', 0, 'จำนวนเงิน', NULL, 'ငွေပမာဏ', 'ຈຳນວນເງິນ'),
(81, 'Annotation', 'text', 'index', 0, 'หมายเหตุ', NULL, 'မှတ်ချက်', 'ໝາຍເຫດ'),
(82, 'API settings', 'text', 'index', 0, 'ตั้งค่า API', NULL, 'API ဆက်တင်များ', 'ຕັ້ງຄ່າ API'),
(83, 'Authentication require', 'text', 'index', 0, 'การตรวจสอบความถูกต้อง', NULL, 'အတည်ပြုခြင်း', 'ການກວດກາຄວາມຖືກຕ້ອງ'),
(84, 'Authorized', 'text', 'index', 0, 'ผู้มีอำนาจ', NULL, 'အာဏာပိုင်', 'ສິດອຳນາດ'),
(85, 'Authorized signatory receipt', 'text', 'index', 0, 'ชื่อ นามสกุล ผู้มีอำนาจลงนามในใบเสร็จรับเงิน', NULL, 'ငွေလက်ခံဖြတ်ပိုင်းကိုလက်မှတ်ရေးထိုးရန်ခွင့်ပြုထားသောသူ၏အမည်နှင့်နာမည်', 'ຊື່ແລະນາມສະກຸນຂອງຜູ້ທີ່ໄດ້ຮັບອະນຸຍາດລົງລາຍເຊັນ'),
(86, 'Avatar', 'text', 'index', 0, 'รูปสมาชิก', NULL, 'အဖွဲ့ဝင်ဓာတ်ပုံ', 'ຮູບແທນຕົວ'),
(87, 'Background color', 'text', 'index', 0, 'สีพื้นหลัง', NULL, 'နောက်ခံအရောင်', 'ສີພື້ນຫລັງ'),
(88, 'Background image', 'text', 'index', 0, 'รูปภาพพื้นหลัง', NULL, 'နောက်ခံပုံ', 'ພາບພື້ນຫລັງ'),
(89, 'Bank', 'text', 'index', 0, 'ธนาคาร', NULL, 'ဘဏ်', 'ທະນາຄານ'),
(90, 'Barcode', 'text', 'index', 0, 'บาร์โค้ด', NULL, 'ဘားကုဒ်', 'ບາໂຄດ'),
(91, 'Beginning Inventory', 'text', 'index', 0, 'ยอดยกมา', NULL, 'လက်ကျန်ငွေ', 'ການເລີ່ມຕົ້ນດຸ່ນດ່ຽງ'),
(92, 'Billing No.', 'text', 'index', 0, 'เลขที่ใบเสร็จ', NULL, 'ငွေလက်ခံဖြတ်ပိုင်းနံပါတ်', 'ເລກທີໃບຮັບເງິນ'),
(93, 'Branch name', 'text', 'index', 0, 'ชื่อสาขา', NULL, 'ဘဏ်ခွဲအမည်', 'ຊື່ສາຂາ'),
(94, 'Browse file', 'text', 'index', 0, 'เลือกไฟล์', NULL, 'ဖိုင်ရွေးပါ', 'ເລືອກໄຟລ໌'),
(95, 'Browse image uploaded, type :type', 'text', 'index', 0, 'เลือกรูปภาพอัปโหลด ชนิด :type', NULL, 'upload ပုံကိုရွေး။ :type ဟုရိုက်ပါ။', 'ເລືອກຮູບພາບອັບໂຫຼດຊະນິດ :type'),
(96, 'Buy', 'text', 'index', 0, 'ซื้อ', NULL, 'ဝယ်ပါ', 'ຊື້'),
(97, 'Can login', 'text', 'index', 0, 'สามารถเข้าระบบได้', NULL, 'အကောင့်ဝင်နိုင်သည်။', 'ສາມາດເຂົ້າສູ່ລະບົບ'),
(98, 'Can make an order', 'text', 'index', 0, 'สามารถทำรายการสั่งซื้อสินค้าได้', NULL, 'မှာယူနိုင်ပါတယ်', 'ສາມາດສັ່ງຊື້ໄດ້'),
(99, 'Can manage the inventory', 'text', 'index', 0, 'สามารถบริหารจัดการคลังสินค้าได้', NULL, 'စာရင်းစီမံခန့်ခွဲနိုင်ပါ', 'ສາມາດຄຸ້ມຄອງສາງ'),
(100, 'Can not be performed this request. Because they do not find the information you need or you are not allowed', 'text', 'index', 0, 'ไม่สามารถดำเนินการตามที่ร้องขอได้ เนื่องจากไม่พบข้อมูลที่ต้องการ หรือ คุณไม่มีสิทธิ์', NULL, 'ဒီတောင်းဆိုမှုကိုလုပ်ဆောင်လို့မရပါ ဘာဖြစ်လို့လဲဆိုတော့သူတို့ကသင်လိုအပ်တဲ့သတင်းအချက်အလက်ကိုမတွေ့ဘူး၊', 'ບໍ່ສາມາດດຳເນີນການຕາມທີ່ຮ້ອງຂໍໄດ້ເນື່ອງຈາກບໍ່ພົບຂໍ້ມູນທີ່ຕ້ອງການ ຫຼື ທ່ານບໍ່ມີສິດ'),
(101, 'Can select multiple files', 'text', 'index', 0, 'สามารถเลือกได้หลายไฟล์', NULL, 'ဖိုင်များစွာကို ရွေးချယ်နိုင်သည်။', 'ສາມາດເລືອກຫຼາຍໄຟລ໌'),
(102, 'Can sell items', 'text', 'index', 0, 'สามารถทำรายการขายสินค้าได้', NULL, 'အရောင်းစာရင်းလုပ်နိုင်စွမ်း', 'ສາມາດສ້າງລາຍການຂາຍໄດ້'),
(103, 'Can&#039;t login', 'text', 'index', 0, 'ไม่สามารถเข้าระบบได้', NULL, 'အကောင့်ဝင်၍မရပါ။', 'ບໍ່ສາມາດເຂົ້າສູ່ລະບົບໄດ້'),
(104, 'Cash', 'text', 'index', 0, 'เงินสด', NULL, 'ငွေသား', 'ເງິນສົດ'),
(105, 'Category', 'text', 'index', 0, 'หมวดหมู่', NULL, 'အမျိုးအစား', 'ໝວດ'),
(106, 'Change language', 'text', 'index', 0, 'สลับภาษา', NULL, 'ဘာသာစကားများပြောင်းပါ', 'ປ່ຽນພາສາ'),
(107, 'Click to edit', 'text', 'index', 0, 'คลิกเพื่อแก้ไข', NULL, 'တည်းဖြတ်ရန်နှိပ်ပါ', 'ກົດເພື່ອແກ້ໄຂ'),
(108, 'Comment', 'text', 'index', 0, 'ความคิดเห็น', NULL, 'မှတ်ချက်', 'ຫມາຍ​ເຫດ​'),
(109, 'Company', 'text', 'index', 0, 'บริษัท', NULL, 'ကုမ္ပဏီ', 'ບໍລິສັດ'),
(110, 'Company logo', 'text', 'index', 0, 'โลโกของบริษัท', NULL, 'ကုမ္ပဏီတံဆိပ်', 'ໂລໂກ້ຂອງບໍລິສັດ'),
(111, 'Company name', 'text', 'index', 0, 'ชื่อบริษัท', NULL, 'ကုမ္ပဏီအမည်', 'ຊື່ບໍລິສັດ'),
(112, 'Company profile', 'text', 'index', 0, 'ข้อมูลบริษัท', NULL, 'ကုမ္ပဏီ၏ကိုယ်ရေးအကျဉ်း', 'ຂໍ້ມູນບໍລິສັດ'),
(113, 'Confirm password', 'text', 'index', 0, 'ยืนยันรหัสผ่าน', NULL, 'စကားဝှက်အတည်ပြုခြင်း', 'ຢືນຢັນລະຫັດຜ່ານ'),
(114, 'Congratulations, your email address has been verified. please login', 'text', 'index', 0, 'ยินดีด้วย ที่อยู่อีเมลของคุณได้รับการยืนยันเรียบร้อยแล้ว กรุณาเข้าสู่ระบบ', NULL, 'ဂုဏ်ယူပါတယ်၊ သင့်အီးမေးလ်လိပ်စာကို အတည်ပြုပြီးပါပြီ။ ကျေးဇူးပြု၍ ဝင်ရောက်ပါ။', 'ຂໍສະແດງຄວາມຍິນດີ, ທີ່ຢູ່ອີເມວຂອງທ່ານໄດ້ຮັບການຢັ້ງຢືນແລ້ວ. ກະລຸນາເຂົ້າສູ່ລະບົບ'),
(115, 'Contact name If the customer is an agency or company', 'text', 'index', 0, 'ชื่อผู้ติดต่อหากลูกค้าเป็นหน่วยงานหรือบริษัท', NULL, 'ဆက်သွယ်ရန်အမည်ဖောက်သည်ကိုယ်စားလှယ်သို့မဟုတ်ကုမ္ပဏီဖြစ်ပါက', 'ຊື່ຕິດຕໍ່ຖ້າລູກຄ້າແມ່ນອົງການຫລືບໍລິສັດ'),
(116, 'Contactor', 'text', 'index', 0, 'ผู้ติดต่อ', NULL, 'ဆက်သွယ်ရန်ပုဂ္ဂိုလ်', 'ຜູ້ຕິດຕໍ່'),
(117, 'Cookie Policy', 'text', 'index', 0, 'นโยบายคุกกี้', NULL, 'ကွတ်ကီးမူဝါဒ', 'ນະໂຍບາຍຄຸກກີ'),
(118, 'Corporate information and contacts', 'text', 'index', 0, 'ข้อมูลทั่วไปของบริษัทและการติดต่อ', NULL, 'ကော်ပိုရိတ်သတင်းအချက်အလက်နှင့်အဆက်အသွယ်', 'ຂໍ້ມູນແລະການຕິດຕໍ່ຂອງບໍລິສັດ'),
(119, 'Cost', 'text', 'index', 0, 'ต้นทุน', NULL, 'ကုန်ကျစရိတ်', 'ຕົ້ນທຶນ'),
(120, 'Country', 'text', 'index', 0, 'ประเทศ', NULL, 'တိုင်းပြည်', 'ປະເທດ'),
(121, 'Create', 'text', 'index', 0, 'สร้าง', NULL, 'တည်ဆောက်ပါ', 'ສ້າງ'),
(122, 'Create new account', 'text', 'index', 0, 'สร้างบัญชีใหม่', NULL, 'အကောင့်အသစ်ဖန်တီးပါ', 'ສ້າງບັນຊີໃໝ່'),
(123, 'Created', 'text', 'index', 0, 'สร้างเมื่อ', NULL, 'တည်ဆောက်ခဲ့သည်', 'ສ້າງເມື່ອ'),
(124, 'Currency for goods and services', 'text', 'index', 0, 'สกุลเงินสำหรับสินค้าและบริการ', NULL, 'ကုန်ပစ္စည်းများနှင့်ဝန်ဆောင်မှုများအတွက်ငွေကြေး', 'ເງິນຕາສຳລັບສິນຄ້າແລະການບໍລິການ'),
(125, 'Currency unit', 'text', 'index', 0, 'สกุลเงิน', NULL, 'ငွေကြေး', 'ເງິນຕາ'),
(126, 'Current staff', 'text', 'index', 0, 'พนักงานปัจจุบัน', NULL, 'လက်ရှိဝန်ထမ်း', 'ພະນັກງານໃນປະຈຸບັນ'),
(127, 'Customer', 'text', 'index', 0, 'ลูกค้า', NULL, 'ဖောက်သည်', 'ລູກຄ້າ'),
(128, 'Customer list', 'text', 'index', 0, 'รายชื่อลูกค้า', NULL, 'ဖောက်သည်စာရင်း', 'ລາຍຊື່ລູກຄ້າ'),
(129, 'Customer No.', 'text', 'index', 0, 'รหัสลูกค้า', NULL, 'ကုဒ်ဖောက်သည်', 'ລະຫັດລູກຄ້າ'),
(130, 'Cut stock', 'text', 'index', 0, 'การตัดสต๊อก', NULL, 'စတော့ရှယ်ယာဖြတ်ယူ', 'ຕັດຫຸ້ນ'),
(131, 'Dark mode', 'text', 'index', 0, 'โหมดมืด', NULL, 'အမှောင်မုဒ်', 'ໂໝດມືດ'),
(132, 'Dashboard', 'text', 'index', 0, 'แผงควบคุมหลัก', NULL, 'ဒိုင်ခွက်', 'ແຜງຄວບຄຸມ'),
(133, 'Data controller', 'text', 'index', 0, 'ผู้ควบคุม/ใช้ ข้อมูล', NULL, 'ဒေတာထိန်းချုပ်ကိရိယာ', 'ຜູ້​ຄວບ​ຄຸມຂໍ້ມູນ'),
(134, 'date', 'text', 'index', 0, 'วันที่', NULL, 'ရက်စွဲ', 'ວັນທີ'),
(135, 'days', 'text', 'index', 0, 'วัน', NULL, 'နေ့', 'ມື້'),
(136, 'Delete', 'text', 'index', 0, 'ลบ', NULL, 'ဖျက်ပစ်ရန်', 'ລືບ'),
(137, 'Delivery note/Tax Invoice', 'text', 'index', 0, 'ใบส่งสินค้า/ใบกำกับภาษี', NULL, 'ပို့ဆောင်မှုမှတ်စု/ အခွန်ပြေစာ', 'ບັນທຶກການຈັດສົ່ງ/ໃບເກັບພາສີ'),
(138, 'Demo Mode', 'text', 'index', 0, 'โหมดตัวอย่าง', NULL, 'နမူနာမုဒ်', 'ຕົວຢ່າງ'),
(139, 'Description', 'text', 'index', 0, 'คำอธิบาย', NULL, 'ဖော်ပြချက်', 'ຄຳອະທິບາຍ'),
(140, 'Detail', 'text', 'index', 0, 'รายละเอียด', NULL, 'အသေးစိတ်', 'ລາຍະລະອຽດ'),
(141, 'Details of', 'text', 'index', 0, 'รายละเอียดของ', NULL, 'အသေးစိတ်', 'ລາຍລະອຽດຂອງ'),
(142, 'Didn&#039;t receive code?', 'text', 'index', 0, 'ไม่ได้รับโค้ด?', NULL, 'ကုဒ်ကို မရဘူးလား?', 'ບໍ່ໄດ້ຮັບລະຫັດບໍ?'),
(143, 'Discount', 'text', 'index', 0, 'ส่วนลด', NULL, 'အထူးလျှော့စျေး', 'ສ່ວນຫຼຸດ'),
(144, 'Download', 'text', 'index', 0, 'ดาวน์โหลด', NULL, 'ဒေါင်းလုပ်', 'ດາວໂຫຼດ'),
(145, 'Drag and drop to reorder', 'text', 'index', 0, 'ลากและวางเพื่อจัดลำดับใหม่', NULL, 'ပြန်လည်စီစဥ်ရန် ဆွဲယူ၍ချပါ။', 'ລາກແລ້ວວາງລົງເພື່ອຈັດຮຽງໃໝ່'),
(146, 'Due date', 'text', 'index', 0, 'วันที่ครบกำหนด', NULL, 'နေ့စွဲ', 'ວັນທີກຳນົດ'),
(147, 'Edit', 'text', 'index', 0, 'แก้ไข', NULL, 'တည်းဖြတ်ပါ', 'ແກ້ໄຂ'),
(148, 'Editing your account', 'text', 'index', 0, 'แก้ไขข้อมูลส่วนตัว', NULL, 'သင့်အကောင့်ကိုတည်းဖြတ်ခြင်း', 'ແກ້ໄຂຂໍ້ມູນສ່ວນຕົວສະມາຊິກ'),
(149, 'Email', 'text', 'index', 0, 'อีเมล', NULL, 'အီးမေးလ်', 'ອີເມວ'),
(150, 'Email addresses for sender and do not reply such as no-reply@domain.tld', 'text', 'index', 0, 'ทีอยู่อีเมลใช้เป็นผู้ส่งจดหมาย สำหรับจดหมายที่ไม่ต้องการตอบกลับ เช่น no-reply@domain.tld', NULL, 'ပေးပို့သူအတွက်အီးမေးလ်လိပ်စာများ၊ no-reply@domain.tld', 'ທີ່ຢູ່ອີເມວໃຊ້ເປັນຜູ້ສົ່ງຈົດໝາຍ ສຳລັບຈົດໝາຍທີ່ບໍ່ຕ້ອງການຕອບກັບເຊັ່ນ no-reply@domain.tld'),
(151, 'Email encoding', 'text', 'index', 0, 'รหัสภาษาของจดหมาย', NULL, 'အီးမေးလ်ကုဒ်', 'ລະຫັດພາສາຂອງຈົດໝາຍ'),
(152, 'Email settings', 'text', 'index', 0, 'ตั้งค่าอีเมล', NULL, 'အီးမေးလ်ချိန်ညှိချက်များ', 'ຕັ້ງຄ່າອີເມວ'),
(153, 'Emailing', 'text', 'index', 0, 'การส่งอีเมล์', NULL, 'အီးမေးလ်ပို့ခြင်း', 'ສົ່ງອີເມວ'),
(154, 'Enable SSL encryption for sending email', 'text', 'index', 0, 'เปิดใช้งานการเข้ารหัส SSL สำหรับการส่งอีเมล', NULL, 'အီးမေးလ်ပို့ခြင်းအတွက် SSL စာဝှက်စနစ်ကိုဖွင့်ပါ', 'ເປີດໃຊ້ການເຂົ້າລະຫັດ SSL ສຳລັບການສົ່ງອີເມວ'),
(155, 'English lowercase letters and numbers, not less than 6 digits', 'text', 'index', 0, 'ภาษาอังกฤษตัวพิมพ์เล็กและตัวเลข ไม่น้อยกว่า 6 หลัก', NULL, 'ဂဏန်း ၆ လုံးထက်မနည်းသော အင်္ဂလိပ်အက္ခရာနှင့် ဂဏန်းများ', 'ໂຕພິມນ້ອຍພາສາອັງກິດ ແລະຕົວເລກ, ບໍ່ຕໍ່າກວ່າ 6 ຕົວເລກ'),
(156, 'Enter the 4-digit verification code that was sent to your phone number.', 'text', 'index', 0, 'ป้อนรหัสยืนยัน 4 หลักที่ส่งไปยังหมายเลขโทรศัพท์ของคุณ', NULL, 'သင့်ဖုန်းနံပါတ်သို့ ပေးပို့ထားသော ဂဏန်း 4 လုံး အတည်ပြုကုဒ်ကို ထည့်သွင်းပါ။', 'ໃສ່ລະຫັດຢືນຢັນ 4 ຕົວເລກທີ່ສົ່ງໄປຫາເບີໂທລະສັບຂອງທ່ານ.'),
(157, 'Enter the domain name you want to allow or enter * for all domains. or leave it blank if you want to use it on this domain only', 'text', 'index', 0, 'กรอกชื่อโดเมนที่ต้องการอนุญาต หรือกรอก * สำหรับทุกโดเมน หรือเว้นว่างไว้ถ้าต้องการให้ใช้งานได้บนโดเมนนี้เท่านั้น', NULL, 'သင်ခွင့်ပြုလိုသော ဒိုမိန်းအမည်ကို ထည့်ပါ သို့မဟုတ် ဒိုမိန်းအားလုံးအတွက် * ထည့်ပါ။ သို့မဟုတ် ဤဒိုမိန်းတွင်သာ အသုံးပြုလိုပါက ၎င်းကို ကွက်လပ်ထားခဲ့ပါ။', 'ໃສ່ຊື່ໂດເມນທີ່ທ່ານຕ້ອງການທີ່ຈະອະນຸຍາດໃຫ້ຫຼືໃສ່ * ສໍາລັບໂດເມນທັງຫມົດ. ຫຼືປ່ອຍໃຫ້ມັນຫວ່າງຖ້າທ່ານຕ້ອງການໃຊ້ມັນຢູ່ໃນໂດເມນນີ້ເທົ່ານັ້ນ'),
(158, 'Enter the LINE user ID you received when adding friends. Or type userId sent to the official account to request a new user ID. This information is used for receiving private messages from the system via LINE.', 'text', 'index', 0, 'กรอก user ID ของไลน์ที่ได้ตอนเพิ่มเพื่อน หรือพิมพ์คำว่า userId ส่งไปยังบัญชีทางการเพื่อขอ user ID ใหม่ ข้อมูลนี้ใช้สำหรับการรับข้อความส่วนตัวที่มาจากระบบผ่านไลน์', NULL, 'သူငယ်ချင်းများထည့်ရာတွင် သင်လက်ခံရရှိသော LINE ၏ အသုံးပြုသူ ID ကို ထည့်သွင်းပါ။ သို့မဟုတ် အသုံးပြုသူ ID အသစ်တစ်ခုတောင်းဆိုရန် တရားဝင်အကောင့်သို့ ပေးပို့ထားသော userId ကို ရိုက်ထည့်ပါ။ ဤအချက်အလက်ကို LINE မှတစ်ဆင့် စနစ်မှ သီးသန့်စာတိုများ လက်ခံရရှိရန်အတွက် အသုံးပြုသည်။', 'ໃສ່ user ID ຂອງ LINE ທີ່ທ່ານໄດ້ຮັບໃນເວລາເພີ່ມເພື່ອນ. ຫຼືພິມ userId ທີ່ຖືກສົ່ງໄປຫາບັນຊີທາງການເພື່ອຮ້ອງຂໍ user ID ໃຫມ່. ຂໍ້ມູນນີ້ແມ່ນໃຊ້ສໍາລັບການຮັບຂໍ້ຄວາມສ່ວນຕົວຈາກລະບົບຜ່ານ LINE.'),
(159, 'Enter your password again', 'text', 'index', 0, 'กรอกรหัสผ่านของคุณอีกครั้ง', NULL, 'သင်၏စကားဝှက်ကိုထပ်ရိုက်ပါ', 'ໃສ່ລະຫັດຜ່ານຂອງທ່ານອີກຄັ້ງ'),
(160, 'Enter your registered username. A new password will be sent to this username.', 'text', 'index', 0, 'กรอกชื่อผู้ใช้ที่ลงทะเบียนไว้ ระบบจะส่งรหัสผ่านใหม่ไปยังชื่อผู้ใช้นี้', NULL, 'သင်၏မှတ်ပုံတင်ထားသော အသုံးပြုသူအမည်ကို ထည့်သွင်းပါ။ စကားဝှက်အသစ်ကို ဤအသုံးပြုသူအမည်သို့ ပေးပို့ပါမည်။', 'ໃສ່ຊື່ຜູ້ໃຊ້ທີ່ລົງທະບຽນຂອງທ່ານ. ລະຫັດຜ່ານໃໝ່ຈະຖືກສົ່ງໄປຫາຊື່ຜູ້ໃຊ້ນີ້'),
(161, 'entries', 'text', 'index', 0, 'รายการ', NULL, 'စာရင်း', 'ລາຍການ'),
(162, 'Expiration date', 'text', 'index', 0, 'วันหมดอายุ', NULL, 'သက်တမ်းကုန်ဆုံးရက်', 'ວັນໝົດອາຍຸ'),
(163, 'Fax', 'text', 'index', 0, 'โทรสาร', NULL, 'ဖက်စ်', 'ແຟັກ'),
(164, 'File', 'text', 'index', 0, 'ไฟล์', NULL, 'ဖိုင်', 'ແຟ້ມ'),
(165, 'File Name', 'text', 'index', 0, 'ชื่อไฟล์', NULL, 'ဖိုင်နာမည်', 'ຊື່ແຟ້ມຂໍ້ມູນ'),
(166, 'File not found', 'text', 'index', 0, 'ไม่พบไฟล์ที่ต้องการ', NULL, 'ဖိုင်ရှာမတွေ့ပါ', 'ບໍ່ພົບແຟ້ມທີ່ຕ້ອງການ'),
(167, 'File size is less than :size', 'text', 'index', 0, 'ขนาดของไฟล์ไม่เกิน :size', NULL, 'ဖိုင်အရွယ်အစားသည်အရွယ်အစားထက်သေးငယ်သည်', 'ຂະໜາດຂອງແຟ້ມບໍ່ເກີນ:size'),
(168, 'Fill some of the :name to find', 'text', 'index', 0, 'กรอกบางส่วนของ :name เพื่อค้นหา', NULL, 'ရှာဖွေရန် :name ၏တစ်စိတ်တစ်ပိုင်းကိုရိုက်ထည့်ပါ', 'ໃສ່ບາງສ່ວນຂອງ :name ເພື່ອຄົ້ນຫາ'),
(169, 'Finance', 'text', 'index', 0, 'การเงิน', NULL, 'ဘဏ္ာရေး', 'ການເງິນ'),
(170, 'Footer', 'text', 'index', 0, 'ส่วนท้าย', NULL, 'အောက်ခြေ', 'ສ່ວນທ້າຍ'),
(171, 'for login by LINE account', 'text', 'index', 0, 'สำหรับการเข้าระบบโดยบัญชีไลน์', NULL, 'LINE အကောင့်ဖြင့် ဝင်ရောက်ရန်', 'ສໍາລັບການເຂົ້າສູ່ລະບົບດ້ວຍບັນຊີ LINE'),
(172, 'Forgot', 'text', 'index', 0, 'ลืมรหัสผ่าน', NULL, 'မေ့သွားတယ်', 'ລືມລະຫັດຜ່ານ'),
(173, 'From', 'text', 'index', 0, 'จาก', NULL, 'မှ', 'ຈາກ'),
(174, 'General', 'text', 'index', 0, 'ทั่วไป', NULL, 'အထွေထွေ', 'ທົ່ວໄປ'),
(175, 'General site settings', 'text', 'index', 0, 'ตั้งค่าพื้นฐานของเว็บไซต์', NULL, 'ဝဘ်ဆိုက်အခြေခံဆက်တင်များ', 'ຕັ້ງຄ່າພື້ນຖານຂອງເວັບໄຊ'),
(176, 'Get into stock', 'text', 'index', 0, 'รับเข้าสต๊อก', NULL, 'စတော့ရှယ်ယာသို့ရယူပါ', 'ເຂົ້າໄປໃນຫຸ້ນ'),
(177, 'Get new password', 'text', 'index', 0, 'ขอรหัสผ่าน', NULL, 'စကားဝှက်အသစ်ရယူပါ', 'ຂໍລະຫັດຜ່ານ'),
(178, 'go to page', 'text', 'index', 0, 'ไปหน้าที่', NULL, 'စာမျက်နှာသို့သွားပါ', 'ໄປທີ່ໜ້າ'),
(179, 'Grand total', 'text', 'index', 0, 'รวมราคาทั้งสิ้น', NULL, 'စုစုပေါင်း', 'ລວມ​ທັງ​ຫມົດ'),
(180, 'Header', 'text', 'index', 0, 'ส่วนหัว', NULL, 'ခေါင်းစီး', 'ສ່ວນຫົວ'),
(181, 'Height', 'text', 'index', 0, 'สูง', NULL, 'အမြင့်', 'ສູງ'),
(182, 'Home', 'text', 'index', 0, 'หน้าหลัก', NULL, 'ပင်မစာမျက်နှာ', 'ໜ້າຫຼັກ'),
(183, 'How to define user authentication for mail servers. If you enable it, you must configure below correctly.', 'text', 'index', 0, 'กำหนดวิธีการตรวจสอบผู้ใช้สำหรับเมล์เซิร์ฟเวอร์ หากคุณเปิดใช้งานคุณต้องกำหนดค่าต่างๆด้านล่างถูกต้อง', NULL, 'မေးလ်ဆာဗာများအတွက်အသုံးပြုသူစစ်မှန်ကြောင်းအတည်ပြုရန်ကိုဘယ်လို။ အကယ်၍ သင်ဖွင့်လျှင်၊', 'ກຳນົດວິທີການກວດສອບຜູ້ໃຊ້ສຳລັບເມນເຊິບເວີຫາກທ່ານເປີດໃຊ້ການທ່ານຕ້ອງກຳນົດຄ່າຕ່າງໆດ້ານລຸ່ມຖືກຕ້ອງ'),
(184, 'Identification No.', 'text', 'index', 0, 'เลขประชาชน', NULL, 'မှတ်ပုံတင်နံပါတ်', 'ເລກບັດປະຈຳຕົວ'),
(185, 'If you are a corporate enter your 13-digit Tax identification number or if you are a person enter your 13-digit Personal identification number', 'text', 'index', 0, 'หากเป็นนิติบุคคลให้กรอกเลขประจำตัวผู้เสียภาษี 13 หลัก หรือหากเป็นบุคคลธรรมดาให้กรอกเลขประจำตัวประชาชน 13 หลัก', NULL, 'အကယ်၍ ဥပဒေပညာရှင်တစ် ဦး ဖြစ်ပါက ၁၃ ဂဏန်းအခွန်ထမ်းသူနံပါတ်ကိုထည့်ပါ၊', 'ຖ້າບຸກຄົນນິຕິບຸກຄົນ, ໃສ່ເລກປະ ຈຳຕົວຜູ້ເສຍພາສີ 13 ຕົວເລກຫຼືຖ້າບຸກຄົນທຳມະດາ, ໃສ່ເລກປະຈຳຕົວ 13 ຕົວເລກ'),
(186, 'Image', 'text', 'index', 0, 'รูปภาพ', NULL, 'ပုံ', 'ຮູບພາບ'),
(187, 'Image size is in pixels', 'text', 'index', 0, 'ขนาดของรูปภาพเป็นพิกเซล', NULL, 'ပုံအရွယ်အစားသည် pixel ဖြစ်သည်', 'ຂະໜາດຂອງຮູບພາບເປັນພິກເຊວ'),
(188, 'Import', 'text', 'index', 0, 'นำเข้า', NULL, 'သွင်းကုန်', 'ນຳເຂົ້າ'),
(189, 'Installed modules', 'text', 'index', 0, 'โมดูลที่ติดตั้งแล้ว', NULL, 'တပ်ဆင်ထားသော module တွေ', 'ໂມດູນທີ່ຕິດຕັ້ງ'),
(190, 'Invalid :name', 'text', 'index', 0, ':name ไม่ถูกต้อง', NULL, ':မမှန်ကန်ပါ', ':name ບໍ່ຖືກຕ້ອງ'),
(191, 'Inventory', 'text', 'index', 0, 'คลังสินค้า', NULL, 'ဂိုဒေါင်', 'ສາງ'),
(192, 'Items', 'text', 'index', 0, 'รายการ', NULL, 'ပစ္စည်းများ', 'ລາຍການ'),
(193, 'Key', 'text', 'index', 0, 'คีย์', NULL, 'သော့', 'ແປ້ນພີມ'),
(194, 'Language', 'text', 'index', 0, 'ภาษา', NULL, 'ဘာသာစကား', 'ພາສາ'),
(195, 'Leave empty for generate auto', 'text', 'index', 0, 'เว้นว่างไว้เพื่อสร้างโดยอัตโนมัติ', NULL, 'generate အော်တိုများအတွက်ဗလာချန်ထားပါ', 'ປ່ອຍຫວ່າງໄວ້ເພື່ອສ້າງອັດໂນມັດ'),
(196, 'LINE official account (with @ prefix, e.g. @xxxx)', 'text', 'index', 0, 'บัญชีทางการของไลน์ (มี @ นำหน้า เช่น @xxxx)', NULL, 'LINE တရားဝင်အကောင့် (ဥပမာ @ xxxx ၏ရှေ့ဆက်နှင့်အတူ)', 'ບັນຊີທາງການຂອງ LINE (ມີ @ ຄໍານໍາຫນ້າ, ເຊັ່ນ: @xxxx)'),
(197, 'LINE settings', 'text', 'index', 0, 'ตั้งค่าไลน์', NULL, 'လိုင်းဆက်တင်များ', 'ຕັ້ງ​ຄ່າ LINE'),
(198, 'List of', 'text', 'index', 0, 'รายการ', NULL, 'စာရင်း', 'ລາຍການ'),
(199, 'List of IPs that allow connection 1 line per 1 IP', 'text', 'index', 0, 'รายการไอพีแอดเดรสทั้งหมดที่อนุญาต 1 บรรทัดต่อ 1 ไอพี', NULL, 'ခွင့်ပြုထားသောအိုင်ပီလိပ်စာများစာရင်း၊ အိုင်ပီနှုန်း ၁ လိုင်းရှိသည်။', 'ລາຍຊື່ IP ທີ່ອະນຸຍາດໃຫ້ເຊື່ອມຕໍ່ 1 ເສັ້ນຕໍ່ 1 IP'),
(200, 'Local time', 'text', 'index', 0, 'เวลาท้องถิ่น', NULL, 'ဒေသဆိုင်ရာအချိန်', 'ເວລາທ້ອງຖິ່ນ'),
(201, 'Log in to Telegram to request an ID', 'text', 'index', 0, 'เข้าระบบ Telegram เพื่อขอ ID', NULL, NULL, 'ເຂົ້າສູ່ລະບົບ Telegram ເພື່ອຮ້ອງຂໍ ID.'),
(202, 'Login', 'text', 'index', 0, 'เข้าสู่ระบบ', NULL, 'လော့ဂ်အင်', 'ເຂົ້າສູ່ລະບົບ'),
(203, 'Login as', 'text', 'index', 0, 'เข้าระบบเป็น', NULL, 'အဖြစ်ဝင်ရောက်ပါ။', 'ເຂົ້າ​ສູ່​ລະ​ບົບ​ເປັນ'),
(204, 'Login by', 'text', 'index', 0, 'เข้าระบบโดย', NULL, 'ဖြင့်ဝင်မည်', 'ເຂົ້າສູ່ລະບົບໂດຍ'),
(205, 'Login information', 'text', 'index', 0, 'ข้อมูลการเข้าระบบ', NULL, 'ဝင်မည်သတင်းအချက်အလက်', 'ຂໍ້ມູນການເຂົ້າລະບົບ'),
(206, 'Login page', 'text', 'index', 0, 'หน้าเข้าสู่ระบบ', NULL, 'အကောင့်ဝင်စာမျက်နှာ', 'ໜ້າເຂົ້າສູ່ລະບົບ'),
(207, 'Login with an existing account', 'text', 'index', 0, 'เข้าระบบด้วยบัญชีสมาชิกที่มีอยู่แล้ว', NULL, 'ရှိပြီးသားအကောင့်တစ်ခုနှင့်ဝင်ပါ', 'ເຂົ້າລະບົບດ້ວຍບັນຊີສະມາຊິກທີ່ມີຢູ່ແລ້ວ'),
(208, 'Logo', 'text', 'index', 0, 'โลโก', NULL, 'လိုဂို', 'ໂລໂກ'),
(209, 'Logout', 'text', 'index', 0, 'ออกจากระบบ', NULL, 'ထွက်ပေါက်', 'ອອກຈາກລະບົບ'),
(210, 'Logout successful', 'text', 'index', 0, 'ออกจากระบบเรียบร้อย', NULL, 'အောင်မြင်စွာထွက်ပါ', 'ອອກຈາກລະບົບຮຽບຮ້ອຍ'),
(211, 'Mail program', 'text', 'index', 0, 'โปรแกรมส่งอีเมล', NULL, 'အီးမေးလ်ပရိုဂရမ်', 'ໂປຮແກຮມສົ່ງອີເມວ'),
(212, 'Mail server', 'text', 'index', 0, 'เซิร์ฟเวอร์อีเมล', NULL, 'မေးလ်ဆာဗာ', 'ເຊີບເວີອີເມວ'),
(213, 'Mail server port number (default is 25, for GMail used 465, 587 for DirectAdmin).', 'text', 'index', 0, 'หมายเลขพอร์ตของเมล์เซิร์ฟเวอร์ (ค่าปกติคือ 25, สำหรับ gmail ใช้ 465, 587 สำหรับ DirectAdmin)', NULL, 'မေးလ်ဆာဗာ၏ဆိပ်ကမ်းနံပါတ် (ပုံမှန်မှာ ၂၅ ဖြစ်သည် gmail များအတွက် 465, 587 ကိုသုံးပါ DirectAdmin)', 'ໝາຍເລກພອດຂອງເມວເຊີບເວີ(ຄ່າປົກກະຕິຄື 25, ສຳລັບ gmail ໃຊ້ 465, 587 ສຳລັບ DirectAdmin)'),
(214, 'Mail server settings', 'text', 'index', 0, 'ค่ากำหนดของเมล์เซิร์ฟเวอร์', NULL, 'မေးလ်ဆာဗာဆက်တင်များ', 'ຄ່າກຳນົດຂອງເມວເຊີບເວີ'),
(215, 'Manage languages', 'text', 'index', 0, 'จัดการภาษา', NULL, 'ဘာသာစကားများကိုစီမံပါ', 'ຈັດການພາສາ'),
(216, 'Member list', 'text', 'index', 0, 'รายชื่อสมาชิก', NULL, 'အသင်းဝင်စာရင်း', 'ລາຍຊື່ສະມາຊິກ'),
(217, 'Member status', 'text', 'index', 0, 'สถานะสมาชิก', NULL, 'အသင်းဝင်အခြေအနေ', 'ສະຖານະສະມາຊິກ'),
(218, 'Membership has not been confirmed yet.', 'text', 'index', 0, 'ยังไม่ได้ยืนยันสมาชิก', NULL, 'အဖွဲ့ဝင်အဖြစ် အတည်မပြုရသေးပါ။', 'ສະມາຊິກຍັງບໍ່ທັນໄດ້ຮັບການຢືນຢັນ'),
(219, 'Message', 'text', 'index', 0, 'ข้อความ', NULL, 'မက်ဆေ့ချ်', 'ຂໍ້ຄວາມ'),
(220, 'Message displayed on login page', 'text', 'index', 0, 'ข้อความแสดงในหน้าเข้าสู่ระบบ', NULL, 'အကောင့်ဝင်ခြင်းစာမျက်နှာတွင် ပြသထားသော မက်ဆေ့ချ်', 'ຂໍ້ຄວາມສະແດງຢູ່ໃນຫນ້າເຂົ້າສູ່ລະບົບ'),
(221, 'Mobile Phone Verification', 'text', 'index', 0, 'ยืนยันหมายเลขโทรศัพท์', NULL, 'ဖုန်းနံပါတ်ကို အတည်ပြုပါ။', 'ຢືນຢັນເບີໂທລະສັບ'),
(222, 'Module', 'text', 'index', 0, 'โมดูล', NULL, 'အပိုင်း', 'ໂມດູນ'),
(223, 'Module settings', 'text', 'index', 0, 'ตั้งค่าโมดูล', NULL, 'တပ်ဆင်ပါ အပိုင်း', 'ຕັ້ງຄ່າໂມດູນ'),
(224, 'month', 'text', 'index', 0, 'เดือน', NULL, 'လ', 'ເດືອນ'),
(225, 'monthly', 'text', 'index', 0, 'รายเดือน', NULL, 'လစဉ်', 'ລາຍເດືອນ'),
(226, 'Name of establishment', 'text', 'index', 0, 'ชื่อสถานประกอบการ', NULL, 'တည်ထောင်သည့်အမည်', 'ການສະຖາບັນການຄ້າ'),
(227, 'Name of establishment registered VAT', 'text', 'index', 0, 'ชื่อสถานประกอบการที่จดทะเบียนภาษีมูลค่าเพิ่ม', NULL, 'VAT မှတ်ပုံတင်တည်ထောင်သည့်အမည်', 'ຊື່ຂອງການສ້າງຕັ້ງຂຶ້ນທະບຽນ VAT'),
(228, 'Name of the person or company&#039;s name', 'text', 'index', 0, 'ชื่อของบุคคลหรือชื่อบริษัท', NULL, 'လူတစ် ဦး သို့မဟုတ်ကုမ္ပဏီအမည်', 'ຊື່ຂອງບຸກຄົນຫຼືຊື່ບໍລິສັດ'),
(229, 'Necessary cookies', 'text', 'index', 0, 'คุกกี้พื้นฐานที่จำเป็น', NULL, 'လိုအပ်သော ကွတ်ကီးများ', 'ຄຸກກີພື້ນຖານທີ່ຈໍາເປັນ'),
(230, 'New', 'text', 'index', 0, 'ใหม่', NULL, 'အသစ်', 'ໃໝ່'),
(231, 'New members', 'text', 'index', 0, 'สมาชิกใหม่', NULL, 'အဖွဲ့ဝင်အသစ်', 'ສະມາຊິກໃໝ່'),
(232, 'No data available in this table.', 'text', 'index', 0, 'ไม่มีข้อมูลในตารางนี้', NULL, NULL, 'ບໍ່ມີຂໍ້ມູນຢູ່ໃນຕາຕະລາງນີ້.'),
(233, 'no larger than :size', 'text', 'index', 0, 'ขนาดไม่เกิน :size', NULL, 'အရွယ်အစား :size ထက်မပိုပါ။', 'ຂະໜາດບໍ່ເກີນ :size'),
(234, 'No need to fill in English text. If the English text matches the Key', 'text', 'index', 0, 'ไม่จำเป็นต้องกรอกข้อความในภาษาอังกฤษ หากข้อความในภาษาอังกฤษตรงกับคีย์', NULL, 'အင်္ဂလိပ်စာသားဖြည့်ရန်မလိုအပ်ပါ။ အင်္ဂလိပ်စာသားသော့နှင့်ကိုက်ညီလျှင်', 'ບໍ່ຈຳເປັນເພີ່ມຂໍ້ຄວາມໃນພາສາອັງກິດຫາກຂໍ້ຄວາມໃນພາສານອັງກົງກັບແປ້ນພີມ'),
(235, 'No need to fill in the purchase price if the product is not counting stock', 'text', 'index', 0, 'ไม่จำเป็นต้องกรอกราคาซื้อหากสินค้าไม่นับสต๊อก', NULL, 'အကယ်၍ ကုန်ပစ္စည်းသည်ကုန်ပစ္စည်းမရှိလျှင်ဖြည့်ရန်မလိုပါ။', 'ບໍ່ຈຳເປັນຕ້ອງຕື່ມລາຄາຊື້ຖ້າສິນຄ້າບໍ່ຢູ່ໃນສະຕັອກ'),
(236, 'not a registered user', 'text', 'index', 0, 'ไม่พบสมาชิกนี้ลงทะเบียนไว้', NULL, 'အသင်း ၀ င်မရှိပါ', 'ບໍ່ພົບສະມາຊິກນີ້ລົງທະບຽນໄວ້'),
(237, 'Not enough products, Remaining :stock', 'text', 'index', 0, 'มีสินค้าไม่เพียงพอ คงเหลือ :stock', NULL, 'ကုန်ပစ္စည်းမလုံလောက်ပါ၊ ကျန် :stock', 'ຜະລິດຕະພັນບໍພຽງພໍ, ຍັງເຫຼືອ :stock'),
(238, 'not required', 'text', 'index', 0, 'ไม่มีไม่ต้องกรอก', NULL, 'မလိုအပ်ပါ', 'ບໍ່ຕ້ອງການ'),
(239, 'Not specified', 'text', 'index', 0, 'ไม่ระบุ', NULL, 'မသတ်မှတ်ထားပါ', 'ບໍ່ໄດ້ກໍານົດ'),
(240, 'Number of transactions', 'text', 'index', 0, 'เลขที่การทำรายการ', NULL, 'ငွေသွင်းငွေထုတ်နံပါတ်', 'ໝາຍເລກການເຮັດທຸລະກຳ'),
(241, 'Number such as %04d (%04d means 4 digits, maximum 11 digits)', 'text', 'index', 0, 'เลขที่ เช่น %04d (%04d หมายถึง ตัวเลข 4 หลัก สูงสุด 11 หลัก)', NULL, '%04d ကဲ့သို့သော နံပါတ် (%04d ဆိုသည်မှာ ဂဏန်း ၄ လုံး၊ အများဆုံး ဂဏန်း ၁၁ လုံး)', 'ຕົວເລກເຊັ່ນ %04d (%04d ຫມາຍຄວາມວ່າ 4 ຕົວເລກ, ສູງສຸດ 11 ຕົວເລກ)'),
(242, 'Office name or Branch name eg head office', 'text', 'index', 0, 'ชื่อสำนักงานหรือสาขา เช่น สำนักงานใหญ่', NULL, 'ရုံးချုပ် (သို့) ရုံးခွဲအမည်', 'ຊື່ຫ້ອງການຫຼືສາຂາ, ເຊັ່ນ ສຳ ນັກງານໃຫຍ່'),
(243, 'Order', 'text', 'index', 0, 'บัญชี', NULL, 'အဆက်မပြတ်', 'ລຳດັບ'),
(244, 'Order details', 'text', 'index', 0, 'รายละเอียดการสั่งซื้อ', NULL, 'အော်ဒါအသေးစိတ်များ', 'ລາຍລະອຽດການສັ່ງຊື້'),
(245, 'Order No.', 'text', 'index', 0, 'เลขที่ใบสั่งซื้อ', NULL, 'အော်ဒါနံပါတ်', 'ຈໍານວນຄໍາສັ່ງ'),
(246, 'Order report', 'text', 'index', 0, 'รายงานการสั่งซื้อ', NULL, 'အမိန့်အစီရင်ခံစာ', 'ລາຍງານການສັ່ງຊື້'),
(247, 'Other', 'text', 'index', 0, 'อื่นๆ', NULL, 'အခြား', 'ອື່ນໆ'),
(248, 'Other details', 'text', 'index', 0, 'รายละเอียดอื่นๆ', NULL, 'အခြားအသေးစိတ်အချက်အလက်များ', 'ລາຍລະອຽດອື່ນໆ'),
(249, 'OTP is invalid or expired. Please request a new OTP.', 'text', 'index', 0, 'OTP ไม่ถูกต้องหรือหมดอายุ กรุณาขอ OTP ใหม่', NULL, 'OTP မမှန်ကန်ပါ သို့မဟုတ် သက်တမ်းကုန်သွားပါက OTP အသစ်တစ်ခု တောင်းဆိုပါ။', 'OTP ບໍ່ຖືກຕ້ອງ ຫຼືໝົດອາຍຸ ກະລຸນາຮ້ອງຂໍ OTP ໃໝ່.'),
(250, 'Overview', 'text', 'index', 0, 'ภาพรวม', NULL, 'ခြုံငုံသုံးသပ်ချက်', 'ພາບລວມ'),
(251, 'Page details', 'text', 'index', 0, 'รายละเอียดของหน้า', NULL, 'စာမျက်နှာအသေးစိတ်', 'ລາຍລະອຽດຂອງໜ້າ'),
(252, 'Password', 'text', 'index', 0, 'รหัสผ่าน', NULL, 'စကားဝှက်', 'ລະຫັດຜ່ານ'),
(253, 'Password of the mail server. (To change the fill.)', 'text', 'index', 0, 'รหัสผ่านของเมล์เซิร์ฟเวอร์ (ต้องการเปลี่ยน ให้กรอก)', NULL, 'မေးလ်ဆာဗာ၏စကားဝှက် (ပြောင်းလဲရန်ဖြည့်စွက်ရန်)', 'ລະຫັດຜ່ານຂອງເມວເຊີບເວີ (ຕ້ອງການປ່ຽນ ໃຫ້ເພີ່ມ)'),
(254, 'Passwords must be at least four characters', 'text', 'index', 0, 'รหัสผ่านต้องไม่น้อยกว่า 4 ตัวอักษร', NULL, 'စကားဝှက်သည်စာလုံး ၄ လုံးထက်မကြီးရပါ။', 'ລະຫັດຜ່ານຕ້ອງບໍ່ຕ່ຳກວ່າ 4 ຕົວອັກສອນ'),
(255, 'Past employees', 'text', 'index', 0, 'พนักงานในอดีต', NULL, 'အတိတ်န်ထမ်း', 'ພະນັກງານທີ່ຜ່ານມາ'),
(256, 'Payment Amount', 'text', 'index', 0, 'ยอดชำระ', NULL, 'ငွေပေးချေမှုပမာဏ', 'ຈຳນວນການຈ່າຍເງິນ'),
(257, 'Permission', 'text', 'index', 0, 'สิทธิ์การใช้งาน', NULL, 'လိုင်စင်', 'ສິດໃນການໃຊ້ວຽກ'),
(258, 'Person type', 'text', 'index', 0, 'ประเภทของธุรกิจ', NULL, 'စီးပွားရေးအမျိုးအစား', 'ປະເພດທຸລະກິດ'),
(259, 'Phone', 'text', 'index', 0, 'โทรศัพท์', NULL, 'ဖုန်း', 'ເບີໂທລະສັບ'),
(260, 'Pictures for a receipt', 'text', 'index', 0, 'รูปภาพแสดงในใบเสร็จรับเงิน', NULL, 'ငွေလက်ခံဖြတ်ပိုင်းမှာပြထားတဲ့ပုံ', 'ຮູບທີ່ສະແດງຢູ່ໃນໃບຮັບເງິນ'),
(261, 'Please check the new member registration.', 'text', 'index', 0, 'กรุณาตรวจสอบการลงทะเบียนสมาชิกใหม่', NULL, 'အဖွဲ့ဝင်အသစ် မှတ်ပုံတင်ခြင်းကို စစ်ဆေးပါ။', 'ກະລຸນາກວດສອບການລົງທະບຽນສະມາຊິກໃໝ່.'),
(262, 'Please click the link to verify your email address.', 'text', 'index', 0, 'กรุณาคลิกลิงค์เพื่อยืนยันที่อยู่อีเมล', NULL, 'သင့်အီးမေးလ်လိပ်စာကို အတည်ပြုရန် လင့်ခ်ကို နှိပ်ပါ။', 'ກະລຸນາຄລິກທີ່ລິ້ງເພື່ອຢືນຢັນທີ່ຢູ່ອີເມວຂອງທ່ານ'),
(263, 'Please fill in', 'text', 'index', 0, 'กรุณากรอก', NULL, 'ကျေးဇူးပြု၍ ဖြည့်ပါ', 'ກະລຸນາຕື່ມຂໍ້ມູນໃສ່'),
(264, 'Please fill up this form', 'text', 'index', 0, 'กรุณากรอกแบบฟอร์มนี้', NULL, 'ဤဖောင်ကိုဖြည့်ပါ။', 'ກະລຸນາຕື່ມແບບຟອມນີ້'),
(265, 'Please login', 'text', 'index', 0, 'กรุณาเข้าระบบ', NULL, 'ကျေးဇူးပြု၍ ဝင်ရောက်ပါ။', 'ກະລຸນາເຂົ້າສູ່ລະບົບ'),
(266, 'Please select', 'text', 'index', 0, 'กรุณาเลือก', NULL, 'ကျေးဇူးပြု၍ ရွေးချယ်ပါ', 'ກະລຸນາເລືອກ'),
(267, 'Please select :name at least one item', 'text', 'index', 0, 'กรุณาเลือก :name อย่างน้อย 1 รายการ', NULL, 'ကျေးဇူးပြုပြီးအနည်းဆုံး ၁ :name ရွေးပါ', 'ກະລຸນາເລືອກ :name ຢ່າງໜ້ອຍ 1 ລາຍການ'),
(268, 'Port', 'text', 'index', 0, 'พอร์ต', NULL, 'ဆိပ်ကမ်း', 'ພອດ'),
(269, 'Prefix', 'text', 'index', 0, 'คำนำหน้า', NULL, 'ရှေ့ဆက်', 'ຄຳນຳໜ້າ'),
(270, 'Prefix, if changed The number will be counted again. You can enter %Y%M (year, month).', 'text', 'index', 0, 'คำนำหน้า ถ้ามีการเปลี่ยนแปลง เลขที่จะนับหนึ่งใหม่ สามารถใส่ %Y%M (ปี, เดือน) ได้', NULL, 'ပြောင်းရင် ရှေ့ဆက် ထပ်မံရေတွက်ရမည့် နံပါတ်ကို %Y%M (နှစ်၊ လ) အဖြစ် ထည့်သွင်းနိုင်ပါသည်။', 'ຄໍານໍາຫນ້າ, ຖ້າມີການປ່ຽນແປງ ຕົວເລກທີ່ຈະນັບອີກຄັ້ງສາມາດໃສ່ເປັນ %Y%M (ປີ, ເດືອນ).'),
(271, 'Prices', 'text', 'index', 0, 'ราคา', NULL, 'စျေးနှုန်း', 'ລາຄາ'),
(272, 'Print', 'text', 'index', 0, 'พิมพ์', NULL, 'ပုံနှိပ်', 'ພິມ'),
(273, 'Privacy Policy', 'text', 'index', 0, 'นโยบายความเป็นส่วนตัว', NULL, 'ကိုယ်ရေးအချက်အလက်မူဝါဒ', 'ນະໂຍບາຍຄວາມເປັນສ່ວນຕົວ'),
(274, 'Product', 'text', 'index', 0, 'สินค้า', NULL, 'ထုတ်ကုန်', 'ຜະລິດຕະພັນ'),
(275, 'Product activity report', 'text', 'index', 0, 'รายงานความเคลื่อนไหวสินค้า', NULL, 'ကုန်ပစ္စည်းလှုပ်ရှားမှုအစီရင်ခံစာ', 'ລາຍງານການເຄື່ອນໄຫວຜະລິດຕະພັນ'),
(276, 'Product code', 'text', 'index', 0, 'รหัสสินค้า', NULL, 'ကုန်ပစ္စည်းကုဒ်', 'ລະຫັດຜະລິດຕະພັນ'),
(277, 'Product details', 'text', 'index', 0, 'รายละเอียดของสินค้า', NULL, 'ကုန်ပစ္စည်းအသေးစိတ်', 'ລາຍລະອຽດຜະລິດຕະພັນ'),
(278, 'Product name', 'text', 'index', 0, 'ชื่อสินค้า', NULL, 'ကုန်ပစ္စည်းအမည်', 'ຊື່ຜະລິດຕະພັນ'),
(279, 'Profile', 'text', 'index', 0, 'ข้อมูลส่วนตัว', NULL, 'ကိုယ်ရေးအကျဉ်း', 'ຂໍ້ມູນສ່ວນຕົວ'),
(280, 'Province', 'text', 'index', 0, 'จังหวัด', NULL, 'ပြည်နယ်', 'ແຂວງ'),
(281, 'Purchase', 'text', 'index', 0, 'จัดซื้อ', NULL, 'ဝယ်ယူ', 'ການຊື້'),
(282, 'Purchase price', 'text', 'index', 0, 'ราคาซื้อ', NULL, '၀ ယ်ယူသည့်စျေးနှုန်း', 'ລາຄາຊື້'),
(283, 'Quantity', 'text', 'index', 0, 'จำนวน', NULL, 'အရေအတွက်', 'ຈຳນວນ'),
(284, 'Register', 'text', 'index', 0, 'สมัครสมาชิก', NULL, 'အသင်း ၀ င်ပါ', 'ສະໝັກສະມາຊິກ'),
(285, 'Register successfully Please log in', 'text', 'index', 0, 'ลงทะเบียนเรียบร้อยแล้วกรุณาเข้าสู่ระบบ', NULL, 'ကျေးဇူးပြုပြီးမှတ်ပုံတင်ပြီးပါ', 'ລົງທະບຽນຢ່າງສຳເລັດຜົນກະລຸນາເຂົ້າສູ່ລະບົບ');
INSERT INTO `app_language` (`id`, `key`, `type`, `owner`, `js`, `th`, `en`, `ma`, `la`) VALUES
(286, 'Register successfully, We have sent complete registration information to :email', 'text', 'index', 0, 'ลงทะเบียนสมาชิกใหม่เรียบร้อย เราได้ส่งข้อมูลการลงทะเบียนไปยัง :email', NULL, 'အောင်မြင်စွာမှတ်ပုံတင်ပါ၊ ကျွန်ုပ်တို့သည် :email သို့မှတ်ပုံတင်ခြင်းဆိုင်ရာအချက်အလက်အပြည့်အစုံကိုပေးပို့ပြီးပါပြီ', 'ລົງທະບຽນສຳເລັດແລ້ວ ເຮົາໄດ້ສົ່ງຂໍ້ມູນການລົງທະບຽນໄປທີ່ :email'),
(287, 'Registered successfully Please check your email :email and click the link to verify your email.', 'text', 'index', 0, 'ลงทะเบียนเรียบร้อย กรุณาตรวจสอบอีเมล์ :email และคลิงลิงค์ยืนยันอีเมล', NULL, 'မှတ်ပုံတင်ပြီးပါပြီ ကျေးဇူးပြု၍ သင့်အီးမေးလ် :email ကိုစစ်ဆေးပြီး သင့်အီးမေးလ်ကိုအတည်ပြုရန် လင့်ခ်ကိုနှိပ်ပါ။', 'ລົງທະບຽນສົບຜົນສໍາເລັດ ກະ​ລຸ​ນາ​ກວດ​ສອບ​ອີ​ເມວ​ຂອງ​ທ່ານ :email ແລະ​ຄລິກ​ໃສ່​ການ​ເຊື່ອມ​ຕໍ່​ເພື່ອ​ກວດ​ສອບ​ອີ​ເມວ​ຂອງ​ທ່ານ​.'),
(288, 'Remember me', 'text', 'index', 0, 'จำการเข้าระบบ', NULL, 'လော့ဂ်အင်မှတ်ပါ။', 'ຈົດຈຳການເຂົ້າລະບົບ'),
(289, 'Remove', 'text', 'index', 0, 'ลบ', NULL, 'ဖျက်ပစ်ရန်', 'ລຶບ'),
(290, 'remove this photo', 'text', 'index', 0, 'ลบรูปภาพนี้ออก', NULL, 'ဒီပုံကိုဖျက်ပါ', 'ເອົາຮູບນີ້ອອກ'),
(291, 'Report', 'text', 'index', 0, 'รายงาน', NULL, 'အစီရင်ခံစာ', 'ບົດລາຍງານ'),
(292, 'Resend', 'text', 'index', 0, 'ส่งใหม่', NULL, 'ပြန်ပို့ပါ။', 'ສົ່ງຄືນ'),
(293, 'resized automatically', 'text', 'index', 0, 'ปรับขนาดอัตโนมัติ', NULL, 'အလိုအလျောက်အရွယ်အစား', 'ປັບຂະໜາດອັດຕະໂນມັດ'),
(294, 'Sales', 'text', 'index', 0, 'งานขาย', NULL, 'အရောင်း', 'ການຂາຍ'),
(295, 'Sales items that can be cut stock', 'text', 'index', 0, 'รายการขายที่ตัดสต๊อก', NULL, 'စတော့ရှယ်ယာဖြတ်တောက်နိုင်သောအရောင်းပစ္စည်းများ', 'ລາຍການຂາຍທີ່ສາມາດຕັດຫຸ້ນໄດ້'),
(296, 'Sales report', 'text', 'index', 0, 'รายงานการขาย', NULL, 'အရောင်းအစီရင်ခံစာ', 'ລາຍງານການຂາຍ'),
(297, 'Sales today', 'text', 'index', 0, 'ยอดขายวันนี้', NULL, 'ဒီနေ့အရောင်း', 'ຂາຍມື້ນີ້'),
(298, 'Save', 'text', 'index', 0, 'บันทึก', NULL, 'သိမ်းဆည်းပါ', 'ບັນທຶກ'),
(299, 'Save and create new', 'text', 'index', 0, 'บันทึกแล้วสร้างใหม่', NULL, 'အသစ်သိမ်းဆည်းနှင့်ဖန်တီးပါ', 'ບັນທຶກແລະສ້າງໃໝ່'),
(300, 'Save and email completed', 'text', 'index', 0, 'บันทึกและส่งอีเมลเรียบร้อย', NULL, 'အီးမေးလ်များကိုသိမ်းဆည်းပြီးပေးပို့ပြီးပါပြီ', 'ບັນທຶກແລະສົ່ງອີເມວຮຽນຮ້ອຍ'),
(301, 'Saved successfully', 'text', 'index', 0, 'บันทึกเรียบร้อย', NULL, 'အောင်မြင်စွာသိမ်းဆည်းပြီး', 'ບັນທຶກຮຽບຮ້ອຍ'),
(302, 'scroll to top', 'text', 'index', 0, 'เลื่อนขึ้นด้านบน', NULL, 'အပေါ်သို့တက်ပါ', 'ເລື່ອນຂື້ນດ້ານເທິງ'),
(303, 'Search', 'text', 'index', 0, 'ค้นหา', NULL, 'ရှာပါ', 'ຄົ້ນຫາ'),
(304, 'Search <strong>:search</strong> found :count entries, displayed :start to :end, page :page of :total pages', 'text', 'index', 0, 'ค้นหา <strong>:search</strong> พบ :count รายการ แสดงรายการที่ :start - :end หน้าที่ :page จากทั้งหมด :total หน้า', NULL, 'ရှာရန် <strong>:search</strong> :count စာမျက်နှာအားလုံးမှ :start - :end :page စာမျက်နှာများကို :total စာမျက်နှာများမှရှာသည်။', 'ຄົ້ນຫາ <strong>:search</strong> ພົບ :count ລາຍການ ສະແດງລາຍການທີ່:start - :end ໜ້າທີ່:page ຈາກທັງໝົດ :total ໜ້າ'),
(305, 'Select image size 500 * 500 pixels, type jpg, PNG and webp.', 'text', 'index', 0, 'เลือกรูปภาพ ขนาด 500 * 500 พิกเซล ชนิด jpg, png และ webp', NULL, 'ဓာတ်ပုံအရွယ်အစား 500 * 500 pixel jpg, png, webp အမျိုးအစားများကိုရွေးချယ်ပါ', 'ເລືອກຂະ ໜາດ ຮູບພາບ 500 * 500 ພິກະເຊນ, jpg, png ແລະ webp ປະເພດ'),
(306, 'Sell', 'text', 'index', 0, 'ขาย', NULL, 'ရောင်းမည်', 'ຂາຍ'),
(307, 'Selling price', 'text', 'index', 0, 'ราคาขาย', NULL, 'စျေးနှုန်းရောင်း', 'ລາຄາຂາຍ'),
(308, 'Send a new password request', 'text', 'index', 0, 'ส่งคำขอรหัสผ่านใหม่', NULL, 'စကားဝှက်အသစ်တောင်းဆိုမှုကို ပေးပို့ပါ။', 'ສົ່ງຄຳຮ້ອງຂໍລະຫັດຜ່ານໃໝ່'),
(309, 'Send a welcome email to new members', 'text', 'index', 0, 'ส่งข้อความต้อนรับสมาชิกใหม่', NULL, 'အသင်းဝင်အသစ်များသို့ကြိုဆိုစာများပို့ပါ။', 'ສົ່ງອີເມວຕ້ອນຮັບກັບສະມາຊິກໃຫມ່'),
(310, 'Send again in', 'text', 'index', 0, 'ส่งใหม่ในอีก', NULL, 'နောက်တစ်ကြိမ်ပြန်ပို့ပါ။', 'ສົ່ງຄືນໃນເວລາອື່ນ'),
(311, 'Send login approval notification', 'text', 'index', 0, 'ส่งแจ้งเตือนอนุมัติการเข้าระบบ', NULL, 'အကောင့်ဝင်ခွင့်ပြုချက် အသိပေးချက်ကို ပေးပို့ပါ။', 'ສົ່ງການແຈ້ງເຕືອນການອະນຸມັດການເຂົ້າສູ່ລະບົບ'),
(312, 'Send member confirmation message', 'text', 'index', 0, 'ส่งข้อความยืนยันสมาชิก', NULL, 'အဖွဲ့ဝင်အတည်ပြုချက် မက်ဆေ့ခ်ျပို့ပါ။', 'ສົ່ງຂໍ້ຄວາມຢືນຢັນສະມາຊິກ'),
(313, 'send message to user When a user adds LINE&#039;s official account as a friend', 'text', 'index', 0, 'ส่งข้อความไปยังผู้ใช้ เมื่อผู้ใช้เพิ่มบัญชีทางการของไลน์เป็นเพื่อน', NULL, 'သုံးစွဲသူထံ မက်ဆေ့ချ်ပို့ပါ။ အသုံးပြုသူတစ်ဦးသည် LINE ၏တရားဝင်အကောင့်ကို သူငယ်ချင်းအဖြစ် ထည့်သောအခါ', 'ສົ່ງຂໍ້ຄວາມຫາຜູ້ໃຊ້ ເມື່ອຜູ້ໃຊ້ເພີ່ມບັນຊີທາງການຂອງ LINE ເປັນໝູ່'),
(314, 'Send notification messages When making a transaction', 'text', 'index', 0, 'ส่งข้อความแจ้งเตือนเมื่อมีการทำรายการ', NULL, 'ငွေပေးငွေယူပြုလုပ်သည့်အခါသတိပေးစာကိုပို့ပါ', 'ສົ່ງຂໍ້ຄວາມແຈ້ງເຕືອນເມື່ອມີການເຮັດທຸລະກຳ'),
(315, 'Sender Name', 'text', 'index', 0, 'ชื่อผู้ส่ง', NULL, 'ပေးပို့သူအမည်', 'ຊື່ຜູ້ສົ່ງ'),
(316, 'Server time', 'text', 'index', 0, 'เวลาเซิร์ฟเวอร์', NULL, 'ဆာဗာအချိန်', 'ເວລາຂອງເຊີບເວີ'),
(317, 'Service', 'text', 'index', 0, 'บริการ', NULL, 'ဝန်ဆောင်မှု', 'ການບໍລິການ'),
(318, 'Set the application for send an email', 'text', 'index', 0, 'เลือกโปรแกรมที่ใช้ในการส่งอีเมล', NULL, 'အီးမေးလ်ပို့ရန်အသုံးပြုသောပရိုဂရမ်ကိုရွေးချယ်ပါ။', 'ເລືອກໂປຮແກຮມທີ່ໃຊ້ໃນການສົ່ງອີເມວ'),
(319, 'Setting up the email system', 'text', 'index', 0, 'การตั้งค่าเกี่ยวกับระบบอีเมล', NULL, 'အီးမေးလ်စနစ်နှင့်ပတ်သက်သောဆက်တင်များ', 'ການຕັ້ງຄ່າກ່ຽວກັບລະບົບອີເມວ'),
(320, 'Settings', 'text', 'index', 0, 'ตั้งค่า', NULL, 'တပ်ဆင်ပါ', 'ຕັ້ງຄ່າ'),
(321, 'Settings the conditions for member login', 'text', 'index', 0, 'ตั้งค่าเงื่อนไขในการตรวจสอบการเข้าสู่ระบบ', NULL, 'အသင်းဝင် login အတွက်အခြေအနေများကိုချိန်ညှိပါ', 'ຕັ້ງເງື່ອນໄຂການກວດສອບການເຂົ້າລະບົບ'),
(322, 'Settings the timing of the server to match the local time', 'text', 'index', 0, 'ตั้งค่าเขตเวลาของเซิร์ฟเวอร์ ให้ตรงกันกับเวลาท้องถิ่น', NULL, 'ဆာဗာ၏အချိန်ဇုန်ကိုသတ်မှတ်ပါ။ ဒေသဆိုင်ရာအချိန်နှင့်ထပ်တူပြုပါ', 'ຕັ້ງຄ່າເຂດເວລາຂອງເຊີບເວີ ໃຫ້ກົງກັບເວລາທ້ອງຖີ່ນ'),
(323, 'Sex', 'text', 'index', 0, 'เพศ', NULL, 'လိင်', 'ເພດ'),
(324, 'Short description about your website', 'text', 'index', 0, 'ข้อความสั้นๆอธิบายว่าเป็นเว็บไซต์เกี่ยวกับอะไร', NULL, 'ဝက်ဘ်ဆိုက်အကြောင်းဘာကိုရှင်းပြတဲ့တိုတောင်းတဲ့စာသား', 'ຂໍ້ຄວາມສັ້ນໆ ອະທິບາຍວ່າເປັນເວັບໄຊກ່ຽວກັບຫຍັງ'),
(325, 'Show', 'text', 'index', 0, 'แสดง', NULL, 'ပြပါ', 'ສະແດງ'),
(326, 'show on receipt', 'text', 'index', 0, 'แสดงในใบเสร็จ', NULL, 'ငွေလက်ခံဖြတ်ပိုင်းတွင်ပြပါ', 'ສະແດງໃສ່ໃບຮັບເງິນ'),
(327, 'Show web title with logo', 'text', 'index', 0, 'แสดงชื่อเว็บและโลโก', NULL, 'ဝဘ်နာမည်နှင့်လိုဂိုကိုပြသည်။', 'ສະແດງຊື່ເວັບແລະໂລໂກ້'),
(328, 'showing page', 'text', 'index', 0, 'กำลังแสดงหน้าที่', NULL, NULL, 'ສະແດງໜ້າທີ່'),
(329, 'Site Name', 'text', 'index', 0, 'ชื่อของเว็บไซต์', NULL, 'ဝဘ်ဆိုက်နာမည်', 'ຊື່ຂອງເວັບໄຊ'),
(330, 'Site settings', 'text', 'index', 0, 'ตั้งค่าเว็บไซต์', NULL, 'ဝဘ်ဆိုက်ဆက်တင်များ', 'ຕັ້ງຄ່າເວັບໄຊ'),
(331, 'size :width*:height pixel', 'text', 'index', 0, 'ขนาด :width*:height พิกเซล', NULL, 'အရွယ်အစား :width*:height pixels', 'ຂະໜາດ :width*:height ຟິດເຊວล'),
(332, 'Size of', 'text', 'index', 0, 'ขนาดของ', NULL, 'အရွယ်အစား', 'ຂະໜາດຂອງ'),
(333, 'Size of the file upload', 'text', 'index', 0, 'ขนาดของไฟล์อัปโหลด', NULL, 'ဖိုင်အရွယ်အစားကိုတင်ပါ', 'ຂະໜາດຂອງແຟ້ມອັບໂຫຼດ'),
(334, 'skip to content', 'text', 'index', 0, 'ข้ามไปยังเนื้อหา', NULL, 'အကြောင်းအရာသွားပါ', 'ຂ້າມໄປຍັງເນື້ອຫາ'),
(335, 'SMS Settings', 'text', 'index', 0, 'ตั้งค่า SMS', NULL, 'SMS စနစ်ထည့်သွင်းပါ။', 'ຕັ້ງຄ່າ SMS'),
(336, 'Sorry', 'text', 'index', 0, 'ขออภัย', NULL, 'တောင်းပန်ပါတယ်', 'ຂໍໂທດ'),
(337, 'Sorry, cannot find a page called Please check the URL or try the call again.', 'text', 'index', 0, 'ขออภัย ไม่พบหน้าที่เรียก โปรดตรวจสอบ URL หรือลองเรียกอีกครั้ง', NULL, 'ဝမ်းနည်းပါသည်၊ ကျေးဇူးပြု၍ URL ကိုစစ်ဆေးပါ သို့မဟုတ် ဖုန်းခေါ်ဆိုမှု ထပ်လုပ်ကြည့်ပါ။', 'ຂໍ​ອະ​ໄພ, ບໍ່​ສາ​ມາດ​ຊອກ​ຫາ​ຫນ້າ​ທີ່​ຮ້ອງ​ຂໍ. ກະ​ລຸ​ນາ​ກວດ​ສອບ URL ຫຼື​ພະ​ຍາ​ຍາມ​ດຶງ​ຂໍ້​ມູນ​ອີກ​ເທື່ອ​ຫນຶ່ງ.'),
(338, 'Sorry, Item not found It&#39;s may be deleted', 'text', 'index', 0, 'ขออภัย ไม่พบรายการที่เลือก รายการนี้อาจถูกลบไปแล้ว', NULL, 'စိတ်မကောင်းပါ၊ ရွေးချယ်ထားသောပစ္စည်းကိုရှာမရပါ ဤပစ္စည်းအားဖျက်ပြီးပါက', 'ຂໍໂທດ ບໍ່ພົບລາຍການທີ່ເລືອກ ລາຍການນີ້ອາດຖືກລຶບໄປແລ້ວ'),
(339, 'Specify the file extension that allows uploading. English lowercase letters and numbers 2-4 characters to separate each type with a comma (,) and without spaces. eg zip,rar,doc,docx', 'text', 'index', 0, 'ระบุนามสกุลของไฟล์ที่สามารถอัพโหลดได้ ภาษาอังกฤษตัวพิมพ์เล็กและตัวเลขสองถึงสี่ตัวอักษร คั่นแต่ละรายการด้วยลูกน้ำ (,)', NULL, 'တင်ယူနိုင်သည့်ဖိုင် extension ကိုသတ်မှတ်ပါ။ အင်္ဂလိပ်စာလုံးအသေးများ၊ နံပါတ်များ၊ စာလုံးနှစ်လုံးမှလေးလုံးအထိ ပစ္စည်းတစ်ခုစီကိုကော်မာနှင့်သီးခြားခွဲပါ။ (,)', 'ລະບົບນາມສະກຸນຂອງແຟ້ມທີ່ສາມາດອັບໂຫຼດໄດ້ ພາສາອັງກິດຕົວພີມນ້ອຍແລະຕົວເລກສອງເຖິງສີ່ຕົວອັກສອນ ຄັ່ນແຕ່ລະລາຍການດ້ວຍເຄື່ອງໝາຍຈຸດ(,)'),
(340, 'Specify the language code of the email, as utf-8', 'text', 'index', 0, 'ระบุรหัสภาษาของอีเมลที่ส่ง เช่น utf-8', NULL, 'ထိုကဲ့သို့သောပို့အီးမေးလ်၏ဘာသာစကားကုဒ်သတ်မှတ်ပါ utf-8', 'ລະບຸລະຫັດພາສາຂອງອີເມວທີ່ສົ່ງເຊັ່ນ utf-8'),
(341, 'Start a new line with the web name', 'text', 'index', 0, 'ขึ้นบรรทัดใหม่ชื่อเว็บ', NULL, 'ဝဘ်အမည်ဖြင့် စာကြောင်းအသစ်ကို စတင်ပါ။', 'ເລີ່ມແຖວໃໝ່ຊື່ເວັບ'),
(342, 'Status', 'text', 'index', 0, 'สถานะ', NULL, 'အခြေအနေ', 'ສະຖານະ'),
(343, 'Status for general members', 'text', 'index', 0, 'สถานะสำหรับสมาชิกทั่วไป', NULL, 'အထွေထွေအဖွဲ့ဝင်များအတွက်အခြေအနေ', 'ສະຖານະສຳລັບສະມາຊິກທົ່ວໄປ'),
(344, 'Stock', 'text', 'index', 0, 'จำนวนสินค้า', NULL, 'ကုန်ပစ္စည်း', 'ຫຸ້ນ'),
(345, 'Style', 'text', 'index', 0, 'รูปแบบ', NULL, 'ပုံစံ', 'ຮູບແບບ'),
(346, 'successfully copied to clipboard', 'text', 'index', 0, 'สำเนาไปยังคลิปบอร์ดเรียบร้อยแล้ว', NULL, 'အောင်မြင်စွာ clipboard သို့ကူးယူ', 'ສຳເນົາໄປຍັງຄິບບອດຮຽບຮ້ອຍແລ້ວ'),
(347, 'Supplier', 'text', 'index', 0, 'คู่ค้า', NULL, 'ပေးသွင်းသူ', 'ຄູ່ຮ່ວມງານ'),
(348, 'Tax ID', 'text', 'index', 0, 'เลขประจำตัวผู้เสียภาษี', NULL, 'အခွန် ID', 'ບັດປະຈຳຕົວພາສີ'),
(349, 'Tax ID 13 digit', 'text', 'index', 0, 'เลขประจำตัวผู้เสียภาษีอากร 13 หลัก', NULL, '13 ဂဏန်းအခွန်ထမ်းမှတ်ပုံတင်နံပါတ်', 'ເລກປະຈຳຕົວຜູ້ເສຍອາກອນ 13 ຕົວເລກ'),
(350, 'Text color', 'text', 'index', 0, 'สีตัวอักษร', NULL, 'ဖောင့်အရောင်', 'ສີຕົວອັກສອນ'),
(351, 'The contact email Used to send documents by email', 'text', 'index', 0, 'ที่อยู่อีเมลที่ติดต่อได้ ใช้ในการส่งเอกสาร', NULL, 'အီးမေးလ်လိပ်စာကိုဆက်သွယ်ပါ စာရွက်စာတမ်းများပို့ရန်အသုံးပြုသည်', 'ຕິດຕໍ່ທີ່ຢູ່ອີເມວ ໃຊ້ໃນການສົ່ງເອກະສານ'),
(352, 'The e-mail address of the person or entity that has the authority to make decisions about the collection, use or dissemination of personal data.', 'text', 'index', 0, 'ที่อยู่อีเมลของบุคคลหรือนิติบุคคลที่มีอำนาจตัดสินใจเกี่ยวกับการเก็บรวบรวม ใช้ หรือเผยแพร่ข้อมูลส่วนบุคคล', NULL, 'ကိုယ်ရေးကိုယ်တာ အချက်အလက် စုဆောင်းမှု၊ အသုံးပြုမှု၊ သို့မဟုတ် ဖြန့်ဝေမှုနှင့်ပတ်သက်၍ ဆုံးဖြတ်ချက်များချရန် အခွင့်အာဏာရှိသော ပုဂ္ဂိုလ် သို့မဟုတ် အဖွဲ့အစည်း၏ အီးမေးလ်လိပ်စာ။', 'ທີ່ຢູ່ອີເມລ໌ຂອງບຸກຄົນຫຼືຫນ່ວຍງານທີ່ມີອໍານາດໃນການຕັດສິນໃຈກ່ຽວກັບການລວບລວມ, ການນໍາໃຊ້ຫຼືການເຜີຍແຜ່ຂໍ້ມູນສ່ວນບຸກຄົນ.'),
(353, 'The file size larger than the limit', 'text', 'index', 0, 'ขนาดของไฟล์ใหญ่กว่าที่กำหนด', NULL, 'ဖိုင်အရွယ်အစားသည်သတ်မှတ်ထားသောအရွယ်ထက်ကြီးသည်။', 'ຂະຫນາດຂອງໄຟໃຫຍ່ກວ່າທີ່ກໍາຫນົດ'),
(354, 'The members status of the site', 'text', 'index', 0, 'สถานะของสมาชิกของเว็บไซต์', NULL, 'ဝဘ်ဆိုက်အဖွဲ့ဝင်အခြေအနေ', 'ສະຖານະຂອງສະມາຂິກຂອງເວັບໄຊ'),
(355, 'The message has been sent to the admin successfully. Please wait a moment for the admin to approve the registration. You can log back in later if approved.', 'text', 'index', 0, 'ส่งข้อความไปยังผู้ดูแลระบบเรียบร้อยแล้ว กรุณารอสักครู่เพื่อให้ผู้ดูแลระบบอนุมัติการลงทะเบียน คุณสามารถกลับมาเข้าระบบได้ในภายหลังหากได้รับการอนุมัติแล้ว', NULL, 'မက်ဆေ့ချ်ကို စီမံခန့်ခွဲသူထံ အောင်မြင်စွာ ပေးပို့လိုက်ပါပြီ။ ကျေးဇူးပြု၍ မှတ်ပုံတင်ခြင်းကို စီမံခန့်ခွဲသူမှ အတည်ပြုရန် ခဏစောင့်ပါ။ အတည်ပြုပါက နောက်ပိုင်းတွင် သင်ပြန်လည်ဝင်ရောက်နိုင်ပါသည်။', 'ຂໍ້ຄວາມດັ່ງກ່າວໄດ້ຖືກສົ່ງໄປຫາຜູ້ເບິ່ງແຍງຢ່າງສໍາເລັດຜົນ. ກະລຸນາລໍຖ້າໃຫ້ຜູ້ເບິ່ງແຍງລະບົບອະນຸມັດການລົງທະບຽນ. ທ່ານສາມາດເຂົ້າສູ່ລະບົບຄືນໄດ້ໃນພາຍຫຼັງຖ້າໄດ້ຮັບການອະນຸມັດ.'),
(356, 'The name of the mail server as localhost or smtp.gmail.com (To change the settings of your email is the default. To remove this box entirely.)', 'text', 'index', 0, 'ชื่อของเมล์เซิร์ฟเวอร์ เช่น localhost หรือ smtp.gmail.com (ต้องการเปลี่ยนค่ากำหนดของอีเมลทั้งหมดเป็นค่าเริ่มต้น ให้ลบข้อความในช่องนี้ออกทั้งหมด)', NULL, 'မေးလ်ဆာဗာအမည် localhost หรือ smtp.gmail.com (အီးမေးလ်စိတ်ကြိုက်အားလုံးကိုပုံမှန်အဖြစ်ပြောင်းလိုသည်။ ဤအကွက်ရှိစာများအားလုံးကိုဖျက်ပါ။)', 'ຊື່ຂອງເມວເຊີບເວີເຊັ່ນ localhost หรือ chitdpt@gmail.com (ຕ້ອງປ່ຽນຄ່າກຳນົດຂອງອີເມວທັງໝົດເປັນຄ່າເລີ່ມຕົ້ນ ໃຫ້ລຶບຂໍ້ຄວາມໃນຊ່ອງນີ້ອອກທັງໝົດ)'),
(357, 'The size of the files can be uploaded. (Should not exceed the value of the Server :upload_max_filesize.)', 'text', 'index', 0, 'ขนาดของไฟล์ที่สามารถอัพโหลดได้ (ไม่ควรเกินค่ากำหนดของเซิร์ฟเวอร์ :upload_max_filesize)', NULL, 'အပ်လုဒ်လုပ်ထားသောဖိုင်၏အရွယ်အစား။ (ဆာဗာ၏ဖွဲ့စည်းမှုပုံစံကိုမကျော်လွန်သင့်ပါ:upload_max_filesize)', 'ຂະໜາດຂອງແຟ້ມທີ່ສາມາດອັບໂຫຼດໄດ້(ບໍ່ຄວນເກີນຄ່າກຳນົດຂອງເຊີບເວີ :upload_max_filesize)'),
(358, 'The type of file is invalid', 'text', 'index', 0, 'ชนิดของไฟล์ไม่รองรับ', NULL, 'ဖိုင်အမျိုးအစားကိုမထောက်ပံ့ပါ', 'ຊະນິດຂອງແຟ້ມບໍ່ຮອງຮັບ'),
(359, 'Telegram settings', 'text', 'index', 0, 'ตั้งค่า Telegram', NULL, NULL, 'ຕັ້ງຄ່າ Telegram'),
(360, 'Theme', 'text', 'index', 0, 'ธีม', NULL, 'အပြင်အဆင်', 'ຮູບແບບສີສັນ'),
(361, 'This :name already exist', 'text', 'index', 0, 'มี :name นี้อยู่ก่อนแล้ว', NULL, 'တစ်ခုရှိသည် :name ဒါပြီးပြီ', 'ມີ :name ນີ້ຢູ່ກ່ອນແລ້ວ'),
(362, 'This website uses cookies to provide our services. To find out more about our use of cookies, please see our :privacy.', 'text', 'index', 0, 'เว็บไซต์นี้มีการใช้คุกกี้เพื่อปรับปรุงการให้บริการ หากต้องการข้อมูลเพิ่มเติมเกี่ยวกับการใช้คุกกี้ของเรา โปรดดู :privacy', NULL, 'ဤဝဘ်ဆိုဒ်သည် ကျွန်ုပ်တို့၏ဝန်ဆောင်မှုများကိုပေးဆောင်ရန် ကွတ်ကီးများကိုအသုံးပြုသည်။ ကျွန်ုပ်တို့၏ ကွတ်ကီးအသုံးပြုမှုအကြောင်း ပိုမိုသိရှိရန်၊ ကျွန်ုပ်တို့၏ :privacy ကို ကြည့်ပါ။', 'ເວັບໄຊທ໌ນີ້ໃຊ້ຄຸກກີເພື່ອປັບປຸງການບໍລິການ. ສໍາລັບຂໍ້ມູນເພີ່ມເຕີມກ່ຽວກັບການນໍາໃຊ້ຄຸກກີຂອງພວກເຮົາ, ເບິ່ງ :privacy'),
(363, 'Time zone', 'text', 'index', 0, 'เขตเวลา', NULL, 'အချိန်ဇုန်', 'ເຂດເວລາ'),
(364, 'times', 'text', 'index', 0, 'ครั้ง', NULL, 'ကြိမ်', 'ຄັ້ງ'),
(365, 'To', 'text', 'index', 0, 'ถึง', NULL, 'ရန်', 'ເຖິງ'),
(366, 'To change your password, enter your password to match the two inputs', 'text', 'index', 0, 'ถ้าต้องการเปลี่ยนรหัสผ่าน กรุณากรอกรหัสผ่านสองช่องให้ตรงกัน', NULL, 'စကားဝှက်ကိုပြောင်းလဲလိုသောလျှင် ကျေးဇူးပြုပြီးကိုက်ညီမည့်လျှို့ဝှက်နံပါတ်နှစ်ခုကိုရိုက်ထည့်ပါ။', 'ຖ້າຕ້ອງການປ່ຽນລະຫັດຜ່ານກະລຸນາເພີ່ມລະຫັດຜ່ານສອງຊ່ອງໃຫ້ກົງກັນ'),
(367, 'Topic', 'text', 'index', 0, 'หัวข้อ', NULL, 'ခေါင်းစဉ်', 'ຫົວຂໍ້'),
(368, 'Total', 'text', 'index', 0, 'รวม', NULL, 'စုစုပေါင်း', 'ລວມ'),
(369, 'Total Before Tax', 'text', 'index', 0, 'ราคารวมก่อนภาษี', NULL, 'အခွန်မတိုင်မီစုစုပေါင်းစျေးနှုန်း', 'ລາຄາທັງໝົດກ່ອນເສຍອາກອນ'),
(370, 'Transaction date', 'text', 'index', 0, 'วันที่ทำรายการ', NULL, 'ငွေသွင်းငွေထုတ်သည့်ရက်', 'ວັນທີ່ເຮັດລາຍະການ'),
(371, 'Transaction details', 'text', 'index', 0, 'รายละเอียดการทำธุรกรรม', NULL, 'ငွေသွင်းငွေထုတ်အသေးစိတ်', 'ລາຍລະອຽດການເຮັດທຸລະ ກຳ'),
(372, 'Type', 'text', 'index', 0, 'ชนิด', NULL, 'အမျိုးအစား', 'ປະເພດ'),
(373, 'Type of file uploads', 'text', 'index', 0, 'ชนิดของไฟล์อัปโหลด', NULL, 'ဖိုင်တင်ပို့မှုအမျိုးအစား', 'ຊະນິດຂອງແຟ້ມອັບໂຫຼດ'),
(374, 'Unable to complete the transaction', 'text', 'index', 0, 'ไม่สามารถทำรายการนี้ได้', NULL, 'ငွေပေးငွေယူကိုအပြီးသတ်နိုင်ပါ', 'ບໍ່ສາມາດເຮັດລາຍການນີ້ໄດ້'),
(375, 'Unit', 'text', 'index', 0, 'หน่วย', NULL, 'ယူနစ်', 'ໜ່ວຍ'),
(376, 'Unit price', 'text', 'index', 0, 'หน่วยละ', NULL, 'တစ်ခုချင်းစျေးနှုန်း', 'ລາ​ຄາ​ຕໍ່​ຫນ່ວຍ'),
(377, 'Unlimited', 'text', 'index', 0, 'ไม่จำกัด', NULL, 'ကန့်သတ်မထားဘူး', 'ບໍ່ຈຳກັດ'),
(378, 'Upload', 'text', 'index', 0, 'อัปโหลด', NULL, 'တင်ပါ', 'ອັບໂຫຼດ'),
(379, 'Upload :type files no larger than :size', 'text', 'index', 0, 'อัปโหลดไฟล์ :type ขนาดไม่เกิน :size', NULL, 'ဖိုင်ဆိုဒ် :type အရွယ်အစား :size', 'ອັບໄຟລ໌ :type ຂະໜາດ :size'),
(380, 'URL must begin with http:// or https://', 'text', 'index', 0, 'URL ต้องขึ้นต้นด้วย http:// หรือ https://', NULL, 'URL သည် http:// သို့မဟုတ် https:// ဖြင့် စတင်ရပါမည်။', 'URL ຕ້ອງເລີ່ມຕົ້ນດ້ວຍ http:// ຫຼື https://'),
(381, 'Usage history', 'text', 'index', 0, 'ประวัติการใช้งาน', NULL, 'အသုံးပြုမှုသမိုင်း', 'ປະ​ຫວັດ​ການ​ນໍາ​ໃຊ້​'),
(382, 'Use the theme&#039;s default settings.', 'text', 'index', 0, 'ใช้การตั้งค่าเริ่มต้นของธีม', NULL, 'အပြင်အဆင်၏ မူရင်းဆက်တင်များကို အသုံးပြုပါ။', 'ໃຊ້ການຕັ້ງຄ່າເລີ່ມຕົ້ນຂອງຮູບແບບສີສັນ.'),
(383, 'User', 'text', 'index', 0, 'สมาชิก', NULL, 'အသုံးပြုသူ', 'ສະມາຊິກ'),
(384, 'Username', 'text', 'index', 0, 'ชื่อผู้ใช้', NULL, 'အသုံးပြုသူအမည်', 'ຊື່ຜູ້ໃຊ້'),
(385, 'Username for the mail server. (To change, enter a new value.)', 'text', 'index', 0, 'ชื่อผู้ใช้ของเมล์เซิร์ฟเวอร์ (ต้องการเปลี่ยน ให้กรอก)', NULL, 'မေးလ်ဆာဗာအတွက်အသုံးပြုသူအမည် (ပြောင်းလဲရန်တန်ဖိုးအသစ်တစ်ခုကိုထည့်ပါ။ )', 'ຊື່ຜູ້ໃຊ້ຂອງເມວເຊີບເວີ (ຕ້ອງການປ່ຽນ ໃຫ້ເພີ່ມ)'),
(386, 'Username used for login or request a new password', 'text', 'index', 0, 'ชื่อผู้ใช้ ใช้สำหรับการเข้าระบบหรือการขอรหัสผ่านใหม่', NULL, 'အသုံးပြုသူအမည်ကို လော့ဂ်အင်ဝင်ခြင်း သို့မဟုတ် စကားဝှက်အသစ်တောင်းဆိုခြင်းအတွက် အသုံးပြုသည်။', 'ຊື່ຜູ້ໃຊ້ທີ່ໃຊ້ສໍາລັບການເຂົ້າສູ່ລະບົບຫຼືຮ້ອງຂໍລະຫັດຜ່ານໃຫມ່'),
(387, 'Users', 'text', 'index', 0, 'สมาชิก', NULL, 'အသင်းဝင်', 'ສະມາຊິກ'),
(388, 'Verify Account', 'text', 'index', 0, 'ยืนยันบัญชี', NULL, 'အကောင့်ကို အတည်ပြုပါ။', 'ຢືນຢັນບັນຊີ'),
(389, 'Version', 'text', 'index', 0, 'รุ่น', NULL, 'ဗားရှင်း', 'ຮຸ່ນ'),
(390, 'Waiting for payment', 'text', 'index', 0, 'รอชำระเงิน', NULL, 'ငွေပေးချေမှုကိုစောင့်ဆိုင်း', 'ລໍຖ້າການຈ່າຍເງິນ'),
(391, 'Waiting list', 'text', 'index', 0, 'รายการรอตรวจสอบ', NULL, 'စစ်ဆေးရန်စာရင်း', 'ລາຍຊື່ລໍຖ້າ'),
(392, 'Waiting to check from the staff', 'text', 'index', 0, 'รอตรวจสอบจากเจ้าหน้าที่', NULL, 'ဝန်ထမ်းတွေဆီက စစ်ဆေးဖို့ စောင့်နေတယ်။', 'ລໍຖ້າການກວດສອບຈາກພະນັກງານ'),
(393, 'Website template', 'text', 'index', 0, 'แม่แบบเว็บไซต์', NULL, 'ဝဘ်ဆိုဒ်ပုံစံ', 'ແມ່ແບບເວັບໄຊທ໌'),
(394, 'Website title', 'text', 'index', 0, 'ชื่อเว็บ', NULL, 'ဝဘ်ဆိုက်ခေါင်းစဉ်', 'ຊື່ເວັບ'),
(395, 'Welcome', 'text', 'index', 0, 'สวัสดี', NULL, 'ကြိုဆိုပါတယ်', 'ສະບາຍດີ'),
(396, 'Welcome %s, login complete', 'text', 'index', 0, 'สวัสดี คุณ %s ยินดีต้อนรับเข้าสู่ระบบ', NULL, 'ကြိုဆိုပါတယ် %s ကြိုဆိုပါတယ်', 'ສະບາຍດີທ່ານ %s ຍິນດີຕ້ອນຮັບເຂົ້າສູ່ລະບົບ'),
(397, 'Welcome new members', 'text', 'index', 0, 'ยินดีต้อนรับสมาชิกใหม่', NULL, 'အဖွဲ့ဝင်သစ်ကြိုဆိုပါတယ်', 'ຍິນດີຕ້ອນຮັບສະມາຊິກໃໝ່'),
(398, 'Welcome. Phone number has been verified. Please log in again.', 'text', 'index', 0, 'ยินดีต้อนรับ หมายเลขโทรศัพท์ได้รับการยืนยันแล้ว กรุณาเข้าระบบอีกครั้ง', NULL, 'ဖုန်းနံပါတ်ကို စစ်ဆေးပြီးပါပြီ။ ကျေးဇူးပြု၍ ထပ်မံဝင်ရောက်ပါ။', 'ຍິນດີຕ້ອນຮັບເບີໂທລະສັບ. ກະລຸນາເຂົ້າສູ່ລະບົບອີກຄັ້ງ.'),
(399, 'When enabled Social accounts can be logged in as an administrator. (Some abilities will not be available)', 'text', 'index', 0, 'เมื่อเปิดใช้งาน บัญชีโซเชียลจะสามารถเข้าระบบเป็นผู้ดูแลได้ (ความสามารถบางอย่างจะไม่สามารถใช้งานได้)', NULL, 'ဖွင့်ထားသည့်အခါလူမှုအကောင့်များကိုစီမံခန့်ခွဲသူအဖြစ်ဝင်ရောက်နိုင်သည်။ (စွမ်းရည်အချို့ရရှိနိုင်လိမ့်မည်မဟုတ်ပေ)', 'ເມື່ອເປີດໃຊ້ງານ ບັນຊີສັງຄົມສາມາດເຂົ້າສູ່ລະບົບເປັນຜູ້ບໍລິຫານ. (ຄວາມສາມາດບາງຢ່າງຈະບໍ່ມີ)'),
(400, 'When enabled, a cookies consent banner will be displayed.', 'text', 'index', 0, 'เมื่อเปิดใช้งานระบบจะแสดงแบนเนอร์ขออนุญาตใช้งานคุ้กกี้', NULL, 'ဖွင့်ထားသောအခါ၊ ကွတ်ကီးသဘောတူညီချက်နဖူးစည်းကို ပြသပါမည်။', 'ເມື່ອເປີດໃຊ້ງານແລ້ວ, ປ້າຍໂຄສະນາການຍິນຍອມຂອງ cookies ຈະສະແດງຂຶ້ນ.'),
(401, 'When enabled, Members registered with email must also verify their email address. It is not recommended to use in conjunction with other login methods.', 'text', 'index', 0, 'เมื่อเปิดใช้งาน สมาชิกที่ลงทะเบียนด้วยอีเมลจะต้องยืนยันที่อยู่อีเมลด้วย ไม่แนะนำให้ใช้ร่วมกับการเข้าระบบด้วยช่องทางอื่นๆ', NULL, 'ဖွင့်ထားသောအခါတွင်၊ အီးမေးလ်ဖြင့် မှတ်ပုံတင်ထားသော အဖွဲ့ဝင်များသည် ၎င်းတို့၏ အီးမေးလ်လိပ်စာကိုလည်း အတည်ပြုရပါမည်။ အခြားသော လော့ဂ်အင်နည်းလမ်းများနှင့် တွဲဖက်အသုံးပြုရန် အကြံပြုထားခြင်းမရှိပါ။', 'ເມື່ອເປີດໃຊ້ ສະມາຊິກທີ່ລົງທະບຽນກັບອີເມລ໌ຈະຕ້ອງຢືນຢັນທີ່ຢູ່ອີເມວຂອງເຂົາເຈົ້າ. ມັນບໍ່ໄດ້ຖືກແນະນໍາໃຫ້ໃຊ້ຮ່ວມກັບວິທີການເຂົ້າສູ່ລະບົບອື່ນໆ.'),
(402, 'Width', 'text', 'index', 0, 'กว้าง', NULL, 'အကျယ်', 'ກວ້າງ'),
(403, 'With selected', 'text', 'index', 0, 'ทำกับที่เลือก', NULL, 'ရွေးချယ်ထားနှင့်အတူ', 'ເຮັດກັບທີ່ເລືອກ'),
(404, 'Withholding Tax', 'text', 'index', 0, 'ภาษีหัก ณ. ที่จ่าย', NULL, 'အခွန်ရှောင်', 'ການເກັບອາກອນ'),
(405, 'year', 'text', 'index', 0, 'ปี', NULL, 'နှစ်', 'ປີ'),
(406, 'You can enter your LINE user ID below on your personal information page. to link your account to this official account', 'text', 'index', 0, 'คุณสามารถกรอก LINE user ID ด้านล่างในหน้าข้อมูลส่วนตัวของคุณ เพื่อผูกบัญชีของคุณเข้ากับบัญชีทางการนี้ได้', NULL, 'သင့်ကိုယ်ရေးကိုယ်တာအချက်အလက်စာမျက်နှာတွင် အောက်ပါ သင်၏ LINE သုံးစွဲသူ ID ကို ထည့်သွင်းနိုင်သည်။ သင့်အကောင့်ကို ဤတရားဝင်အကောင့်နှင့် ချိတ်ဆက်ရန်', 'ທ່ານສາມາດໃສ່ LINE user ID ຂອງທ່ານຂ້າງລຸ່ມນີ້ຢູ່ໃນຫນ້າຂໍ້ມູນສ່ວນຕົວຂອງທ່ານ. ເພື່ອເຊື່ອມຕໍ່ບັນຊີຂອງທ່ານກັບບັນຊີທາງການນີ້'),
(407, 'You can login at', 'text', 'index', 0, 'คุณสามารถเข้าระบบได้ที่', NULL, 'တွင်သင်ဝင်ရောက်နိုင်သည်။', 'ທ່ານສາມາດເຂົ້າສູ່ລະບົບໄດ້ທີ່'),
(408, 'You haven&#039;t verified your email address. Please check your email. and click the link in the email', 'text', 'index', 0, 'คุณยังไม่ได้ยืนยันที่อยู่อีเมล กรุณาตรวจสอบอีเมลของคุณ และคลิกลิงค์ในอีเมล', NULL, 'သင့်အီးမေးလ်လိပ်စာကို သင်မစစ်ဆေးရသေးပါ။ သင့်အီးမေးလ်ကို စစ်ဆေးပါ။ အီးမေးလ်ထဲက လင့်ခ်ကို နှိပ်ပါ။', 'ທ່ານຍັງບໍ່ໄດ້ຢືນຢັນທີ່ຢູ່ອີເມວຂອງທ່ານ. ກະລຸນາກວດເບິ່ງອີເມວຂອງທ່ານ. ແລະຄລິກໃສ່ການເຊື່ອມຕໍ່ໃນອີເມລ໌'),
(409, 'You want to', 'text', 'index', 0, 'คุณต้องการ', NULL, 'မင်းလိုချင်တယ်', 'ທ່ານຕ້ອງການ'),
(410, 'Your account has been approved.', 'text', 'index', 0, 'บัญชีของท่านได้รับการอนุมัติเรียบร้อยแล้ว', NULL, 'သင့်အကောင့်ကို အတည်ပြုပြီးပါပြီ။', 'ບັນຊີຂອງທ່ານໄດ້ຮັບການອະນຸມັດແລ້ວ.'),
(411, 'Your account has not been approved, please wait or contact the administrator.', 'text', 'index', 0, 'บัญชีของท่านยังไม่ได้รับการอนุมัติ กรุณารอ หรือติดต่อสอบถามไปยังผู้ดูแล', NULL, 'သင့်အကောင့်ကို အတည်မပြုရသေးပါ၊ စောင့်ဆိုင်းပါ သို့မဟုတ် စီမံခန့်ခွဲသူကို ဆက်သွယ်ပါ။', 'ບັນຊີຂອງທ່ານບໍ່ໄດ້ຮັບການອະນຸມັດ, ກະລຸນາລໍຖ້າ ຫຼືຕິດຕໍ່ຜູ້ເບິ່ງແຍງລະບົບ.'),
(412, 'Your message was sent successfully', 'text', 'index', 0, 'ส่งข้อความไปยังผู้ที่เกี่ยวข้องเรียบร้อยแล้ว', NULL, 'အဆိုပါမက်ဆေ့ခ်ျကိုသက်ဆိုင်ရာပါတီများထံသို့ပေးပို့ခဲ့သည်', 'ສົ່ງຂໍ້ຄວາມໄປຍັງຜູ້ຮັບຮຽບຮ້ອຍແລ້ວ'),
(413, 'Your new password is', 'text', 'index', 0, 'รหัสผ่านใหม่ของคุณคือ', NULL, 'သင့်ရဲ့စကားဝှက်အသစ်က', 'ລະຫັດຜ່ານໃໝ່ຂອງທ່ານຄື'),
(414, 'Your OTP code is :otp. Please enter this code on the website to confirm your phone number.', 'text', 'index', 0, 'รหัส OTP ของคุณคือ :otp กรุณาป้อนรหัสนี้บนเว็บไซต์เพื่อยืนยันหมายเลขโทรศัพท์ของคุณ', NULL, 'သင်၏ OTP ကုဒ်မှာ :otp ဖြစ်ပြီး သင့်ဖုန်းနံပါတ်ကို အတည်ပြုရန် ဝဘ်ဆိုက်တွင် ဤကုဒ်ကို ထည့်ပါ။', 'ລະຫັດ OTP ຂອງທ່ານແມ່ນ :otp ກະລຸນາໃສ່ລະຫັດນີ້ຢູ່ໃນເວັບໄຊທ໌ເພື່ອຢືນຢັນເບີໂທລະສັບຂອງທ່ານ.'),
(415, 'Your registration information', 'text', 'index', 0, 'ข้อมูลการลงทะเบียนของคุณ', NULL, 'သင့်ရဲ့မှတ်ပုံတင်သတင်းအချက်အလက်', 'ຂໍ້ມູນການລົງທະບຽນຂອງທ່ານ'),
(416, 'Zipcode', 'text', 'index', 0, 'รหัสไปรษณีย์', NULL, 'စာတိုက်ကုဒ်', 'ລະຫັດໄປສະນີ'),
(417, 'CLOSE', 'text', 'index', 1, NULL, NULL, 'ပိတ်', NULL),
(418, 'NEXT_MONTH', 'text', 'index', 1, NULL, NULL, 'နောက်လ', NULL),
(419, 'PREV_MONTH', 'text', 'index', 1, NULL, NULL, 'ပြီးခဲ့သည့်လက', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `app_logs`
--

CREATE TABLE `app_logs` (
  `id` int(11) NOT NULL,
  `src_id` int(11) NOT NULL,
  `module` varchar(20) NOT NULL,
  `action` varchar(20) NOT NULL,
  `create_date` datetime NOT NULL,
  `reason` text DEFAULT NULL,
  `member_id` int(11) DEFAULT NULL,
  `topic` text NOT NULL,
  `datas` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `app_logs`
--

INSERT INTO `app_logs` (`id`, `src_id`, `module`, `action`, `create_date`, `reason`, `member_id`, `topic`, `datas`) VALUES
(1, 1, 'index', 'User', '2025-10-16 10:48:29', NULL, 1, '{LNG_Login} IP ::1', NULL),
(2, 4, 'inventory', 'Save', '2025-10-16 10:56:06', NULL, 1, '{LNG_Product} ID : 4', NULL),
(3, 3, 'inventory', 'Save', '2025-10-16 10:57:11', NULL, 1, '{LNG_Customer}-{LNG_Supplier} ID : 3', NULL),
(4, 5, 'inventory', 'Save', '2025-10-16 10:58:12', NULL, 1, '{LNG_Product} ID : 5', NULL),
(5, 1, 'inventory', 'Save', '2025-10-16 10:59:06', NULL, 1, '{LNG_Purchase} ID : 1', NULL),
(6, 4, 'inventory', 'Save', '2025-10-16 11:03:10', NULL, 1, '{LNG_Customer}-{LNG_Supplier} ID : 4', NULL),
(7, 5, 'inventory', 'Save', '2025-10-16 11:04:22', NULL, 1, '{LNG_Customer}-{LNG_Supplier} ID : 5', NULL),
(8, 6, 'inventory', 'Save', '2025-10-16 11:04:42', NULL, 1, '{LNG_Customer}-{LNG_Supplier} ID : 6', NULL),
(9, 7, 'inventory', 'Save', '2025-10-16 11:05:58', NULL, 1, '{LNG_Customer}-{LNG_Supplier} ID : 7', NULL),
(10, 8, 'inventory', 'Save', '2025-10-16 11:06:17', NULL, 1, '{LNG_Customer}-{LNG_Supplier} ID : 8', NULL),
(11, 6, 'inventory', 'Save', '2025-10-16 11:09:06', NULL, 1, '{LNG_Product} ID : 6', NULL),
(12, 2, 'inventory', 'Save', '2025-10-16 11:09:24', NULL, 1, '{LNG_Purchase} ID : 2', NULL),
(13, 2, 'inventory', 'Save', '2025-10-16 11:10:54', NULL, 1, '{LNG_Purchase} ID : 2', NULL),
(14, 3, 'inventory', 'Save', '2025-10-16 11:11:53', NULL, 1, '{LNG_Purchase} ID : 3', NULL),
(15, 4, 'inventory', 'Save', '2025-10-16 11:13:09', NULL, 1, '{LNG_Purchase} ID : 4', NULL),
(16, 5, 'inventory', 'Save', '2025-10-16 11:14:43', NULL, 1, '{LNG_Sales} ID : 5', NULL),
(17, 0, 'index', 'Save', '2025-10-16 11:18:11', NULL, 1, '{LNG_General site settings}', NULL),
(18, 0, 'index', 'Save', '2025-10-16 11:18:40', NULL, 1, '{LNG_General site settings}', NULL),
(19, 6, 'inventory', 'Save', '2025-10-16 11:21:08', NULL, 1, '{LNG_Product} ID : 6', NULL),
(20, 2, 'inventory', 'Save', '2025-10-16 11:22:26', NULL, 1, '{LNG_Purchase} ID : 2', NULL),
(21, 0, 'inventory', 'Delete', '2025-10-16 11:23:05', NULL, 1, '{LNG_Delete} {LNG_Order} ID : 3', NULL),
(22, 0, 'inventory', 'Delete', '2025-10-16 11:23:13', NULL, 1, '{LNG_Delete} {LNG_Order} ID : 4', NULL),
(23, 0, 'inventory', 'Delete', '2025-10-16 11:23:47', NULL, 1, '{LNG_Delete} {LNG_Order} ID : 5', NULL),
(24, 6, 'inventory', 'Save', '2025-10-16 11:24:16', NULL, 1, '{LNG_Product} ID : 6', NULL),
(25, 0, 'inventory', 'Delete', '2025-10-16 11:25:14', NULL, 1, '{LNG_Delete} {LNG_Inventory} ID : 5', NULL),
(26, 4, 'inventory', 'Save', '2025-10-16 11:25:26', NULL, 1, '{LNG_Product} ID : 4', NULL),
(27, 4, 'inventory', 'Save', '2025-10-16 11:26:04', NULL, 1, '{LNG_Barcode} ID : 4', NULL),
(28, 6, 'inventory', 'Save', '2025-10-16 11:27:49', NULL, 1, '{LNG_Sales} ID : 6', NULL),
(29, 7, 'inventory', 'Save', '2025-10-16 11:29:55', NULL, 1, '{LNG_Sales} ID : 7', NULL),
(30, 1, 'index', 'User', '2025-10-16 11:31:37', NULL, 1, '{LNG_Editing your account} ID : 1', NULL),
(31, 0, 'index', 'Save', '2025-10-16 11:34:54', NULL, 1, '{LNG_Settings} {LNG_Login page}', NULL),
(32, 8, 'inventory', 'Save', '2025-10-16 14:04:14', NULL, 3, '{LNG_Purchase} ID : 8', NULL),
(33, 8, 'inventory', 'Save', '2025-10-16 14:05:05', NULL, 3, '{LNG_Purchase} ID : 8', NULL),
(34, 1, 'index', 'User', '2025-10-21 13:59:54', NULL, 1, '{LNG_Login} IP ::1', NULL),
(35, 1, 'index', 'User', '2025-10-21 15:40:14', NULL, 1, '{LNG_Login} IP ::1', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `app_number`
--

CREATE TABLE `app_number` (
  `type` varchar(20) NOT NULL,
  `prefix` varchar(20) NOT NULL DEFAULT '',
  `auto_increment` int(11) NOT NULL,
  `last_update` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `app_number`
--

INSERT INTO `app_number` (`type`, `prefix`, `auto_increment`, `last_update`) VALUES
('IN_NO', 'RCV6810-', 1, '2025-10-16'),
('OUT_NO', 'INV6810-', 2, '2025-10-16'),
('PO_NO', 'PO6810-', 3, '2025-10-16'),
('product_no', 'P', 4, '2025-10-16'),
('QUO_NO', 'QUO6810-', 1, '2025-10-16'),
('RET_NO', 'RET6810-', 1, '2025-10-16');

-- --------------------------------------------------------

--
-- Table structure for table `app_orders`
--

CREATE TABLE `app_orders` (
  `id` int(11) NOT NULL,
  `order_no` varchar(20) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `order_date` date NOT NULL,
  `member_id` int(11) UNSIGNED NOT NULL,
  `discount` double NOT NULL,
  `vat` double NOT NULL,
  `tax` double NOT NULL,
  `total` double NOT NULL,
  `status` varchar(3) NOT NULL,
  `paid` double NOT NULL DEFAULT 0,
  `discount_percent` double NOT NULL,
  `tax_status` double NOT NULL,
  `vat_status` tinyint(1) NOT NULL,
  `order` varchar(32) DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `payment_date` datetime DEFAULT NULL,
  `payment_method` varchar(255) DEFAULT NULL,
  `comment` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `app_orders`
--

INSERT INTO `app_orders` (`id`, `order_no`, `customer_id`, `order_date`, `member_id`, `discount`, `vat`, `tax`, `total`, `status`, `paid`, `discount_percent`, `tax_status`, `vat_status`, `order`, `due_date`, `payment_date`, `payment_method`, `comment`) VALUES
(1, 'PO6810-0001', 3, '2025-10-16', 1, 0, 0, 0, 15000, 'PO', 0, 0, 0, 0, NULL, NULL, NULL, NULL, ''),
(2, 'PO6810-0002', 8, '2025-10-16', 1, 0, 0, 0, 2500, 'PO', 0, 0, 0, 0, NULL, NULL, NULL, NULL, ''),
(6, 'INV6810-0001', 5, '2025-10-16', 1, 0, 0, 0, 1000, 'OUT', 0, 0, 0, 0, NULL, NULL, NULL, NULL, ''),
(7, 'INV6810-0002', 6, '2025-10-16', 1, 0, 0, 0, 1000, 'OUT', 0, 0, 0, 0, NULL, NULL, NULL, NULL, ''),
(8, 'PO6810-0003', 8, '2025-10-16', 3, 0, 0, 0, 1500, 'IN', 0, 0, 0, 0, NULL, NULL, NULL, NULL, '');

-- --------------------------------------------------------

--
-- Table structure for table `app_stock`
--

CREATE TABLE `app_stock` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `inventory_id` int(11) DEFAULT NULL,
  `product_no` varchar(50) DEFAULT NULL,
  `status` varchar(3) DEFAULT NULL,
  `create_date` datetime NOT NULL,
  `topic` varchar(150) DEFAULT NULL,
  `quantity` float DEFAULT 0,
  `cut_stock` double DEFAULT NULL,
  `unit` varchar(50) DEFAULT NULL,
  `used` double NOT NULL,
  `price` double NOT NULL,
  `vat` double NOT NULL,
  `discount` double NOT NULL,
  `total` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `app_stock`
--

INSERT INTO `app_stock` (`id`, `order_id`, `member_id`, `inventory_id`, `product_no`, `status`, `create_date`, `topic`, `quantity`, `cut_stock`, `unit`, `used`, `price`, `vat`, `discount`, `total`) VALUES
(1, 0, 1, 1, 'S220HQLEBD', 'IN', '2021-02-13 00:00:00', '1108-365D จอมอนิเตอร์ ACER S220HQLEBD', 5, 1, 'เครื่อง', 0, 3500, 0, 0, 17500),
(2, 0, 1, 2, 'A550JX', 'IN', '2021-02-13 00:00:00', 'A550JX ASUS A550JX', 2, 1, 'เครื่อง', 0, 25000, 0, 0, 50000),
(3, 0, 1, 3, 'IF111/036/1', 'IN', '2021-02-13 00:00:00', 'IF111/036/1 Crucial 4GB DDR3L&amp;1600 SODIMM', 10, 1, 'ชิ้น', 0, 500, 0, 0, 5000),
(4, 0, 1, 5, 'PP00003', 'IN', '2025-10-16 10:58:12', 'คอมพิวเตอร์', 1, 1, '', 0, 15000, 0, 0, 15000),
(5, 1, 1, 5, 'PP00003', 'PO', '2025-10-16 00:00:00', 'PP00003 คอมพิวเตอร์', 1, 1, '', 0, 15000, 0, 0, 15000),
(6, 0, 1, 6, 'PP00004', 'IN', '2025-10-16 11:09:06', 'หมึก Canon G2000 003', 10, 1, '1', 4, 500, 0, 0, 5000),
(7, 2, 1, 6, 'PP00004', 'PO', '2025-10-16 00:00:00', 'PP00004 หมึก Canon G2000 003', 5, 1, '1', 0, 500, 0, 0, 2500),
(11, 0, 1, 6, NULL, 'OUT', '2025-10-16 11:21:08', 'หมึก Canon G2000 003', 2, 1, '1', 0, 500, 0, 0, 1000),
(12, 0, 1, 6, NULL, 'OUT', '2025-10-16 11:24:16', 'หมึก Canon G2000 003', 2, 1, '1', 0, 500, 0, 0, 1000),
(13, 6, 1, 6, 'PP00004', 'OUT', '2025-10-16 00:00:00', 'PP00004 หมึก Canon G2000 003', 2, 1, '1', 0, 500, 0, 0, 1000),
(14, 7, 1, 6, 'PP00004', 'OUT', '2025-10-16 00:00:00', 'PP00004 หมึก Canon G2000 003', 2, 1, '1', 0, 500, 0, 0, 1000),
(15, 8, 3, 6, 'PP00004', 'IN', '2025-10-16 00:00:00', 'PP00004 หมึก Canon G2000 003', 3, 1, '1', 0, 500, 0, 0, 1500);

-- --------------------------------------------------------

--
-- Table structure for table `app_user`
--

CREATE TABLE `app_user` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `salt` varchar(32) DEFAULT '',
  `password` varchar(50) NOT NULL,
  `token` varchar(50) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 0,
  `permission` text DEFAULT '',
  `name` varchar(150) NOT NULL,
  `sex` varchar(1) DEFAULT NULL,
  `id_card` varchar(13) DEFAULT NULL,
  `address` varchar(150) DEFAULT NULL,
  `phone` varchar(32) DEFAULT NULL,
  `fax` varchar(32) DEFAULT NULL,
  `provinceID` varchar(3) DEFAULT NULL,
  `province` varchar(50) DEFAULT NULL,
  `zipcode` varchar(10) DEFAULT NULL,
  `country` varchar(2) DEFAULT 'TH',
  `create_date` datetime DEFAULT NULL,
  `active` tinyint(1) DEFAULT 1,
  `social` tinyint(1) DEFAULT 0,
  `email` varchar(50) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `type` tinyint(1) NOT NULL DEFAULT 0,
  `line_uid` varchar(33) DEFAULT NULL,
  `telegram_id` varchar(13) DEFAULT NULL,
  `activatecode` varchar(32) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `app_user`
--

INSERT INTO `app_user` (`id`, `username`, `salt`, `password`, `token`, `status`, `permission`, `name`, `sex`, `id_card`, `address`, `phone`, `fax`, `provinceID`, `province`, `zipcode`, `country`, `create_date`, `active`, `social`, `email`, `website`, `type`, `line_uid`, `telegram_id`, `activatecode`) VALUES
(1, 'admin@localhost', '68f06a765ff7d', 'bf1903ef85e49fd2e2e7d88adbd18890541326d5', 'ce119fbb377f41b2d87d60e92b5fe0e40a0edae5', 1, ',can_config,can_view_usage_history,can_customer,can_inventory_order,can_inventory_receive,can_manage_inventory,', 'แอดมิน', 'u', NULL, '', NULL, NULL, '', '', '', 'TH', '2025-10-16 05:45:58', 1, 0, NULL, NULL, 0, NULL, NULL, ''),
(2, 'demo', '68f06a765ff7d', 'a56f6c3eb40d9ccb45bb5dc9124b3f06f7938a1d', NULL, 0, '', 'ตัวอย่าง', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'TH', '2025-10-16 05:45:58', 1, 0, NULL, NULL, 0, NULL, NULL, '');

-- --------------------------------------------------------

--
-- Table structure for table `app_user_meta`
--

CREATE TABLE `app_user_meta` (
  `value` varchar(10) NOT NULL,
  `name` varchar(20) NOT NULL,
  `member_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `app_category`
--
ALTER TABLE `app_category`
  ADD KEY `type` (`type`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `language` (`language`);

--
-- Indexes for table `app_customer`
--
ALTER TABLE `app_customer`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customer_no` (`customer_no`);

--
-- Indexes for table `app_inventory`
--
ALTER TABLE `app_inventory`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `count_stock` (`count_stock`);

--
-- Indexes for table `app_inventory_items`
--
ALTER TABLE `app_inventory_items`
  ADD PRIMARY KEY (`product_no`),
  ADD KEY `inventory_id` (`inventory_id`);

--
-- Indexes for table `app_inventory_meta`
--
ALTER TABLE `app_inventory_meta`
  ADD KEY `inventory_id` (`inventory_id`),
  ADD KEY `name` (`name`);

--
-- Indexes for table `app_language`
--
ALTER TABLE `app_language`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `app_logs`
--
ALTER TABLE `app_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `src_id` (`src_id`),
  ADD KEY `module` (`module`),
  ADD KEY `action` (`action`);

--
-- Indexes for table `app_number`
--
ALTER TABLE `app_number`
  ADD PRIMARY KEY (`type`,`prefix`);

--
-- Indexes for table `app_orders`
--
ALTER TABLE `app_orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `app_stock`
--
ALTER TABLE `app_stock`
  ADD PRIMARY KEY (`id`),
  ADD KEY `owner_id` (`order_id`),
  ADD KEY `status` (`status`),
  ADD KEY `product_no` (`product_no`),
  ADD KEY `inventory_id` (`inventory_id`);

--
-- Indexes for table `app_user`
--
ALTER TABLE `app_user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `line_uid` (`line_uid`),
  ADD KEY `username` (`username`),
  ADD KEY `token` (`token`),
  ADD KEY `phone` (`phone`),
  ADD KEY `id_card` (`id_card`),
  ADD KEY `activatecode` (`activatecode`);

--
-- Indexes for table `app_user_meta`
--
ALTER TABLE `app_user_meta`
  ADD KEY `member_id` (`member_id`,`name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `app_customer`
--
ALTER TABLE `app_customer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `app_inventory`
--
ALTER TABLE `app_inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `app_language`
--
ALTER TABLE `app_language`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=420;

--
-- AUTO_INCREMENT for table `app_logs`
--
ALTER TABLE `app_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `app_orders`
--
ALTER TABLE `app_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `app_stock`
--
ALTER TABLE `app_stock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `app_user`
--
ALTER TABLE `app_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- Database: `db_salary`
--
CREATE DATABASE IF NOT EXISTS `db_salary` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `db_salary`;

-- --------------------------------------------------------

--
-- Table structure for table `app_category`
--

CREATE TABLE `app_category` (
  `type` varchar(20) NOT NULL,
  `category_id` varchar(10) DEFAULT '0',
  `language` varchar(2) DEFAULT '',
  `topic` varchar(150) NOT NULL,
  `color` varchar(16) DEFAULT NULL,
  `published` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `app_category`
--

INSERT INTO `app_category` (`type`, `category_id`, `language`, `topic`, `color`, `published`) VALUES
('department', '1', '', 'บริหาร', NULL, 1),
('department', '2', '', 'จัดซื้อจัดจ้าง', NULL, 1),
('department', '3', '', 'บุคคล', NULL, 1),
('position', 'ฺMD', '', 'ผู้จัดการ', NULL, 1),
('position', 'HD', '', 'หัวหน้าแผนก', NULL, 1),
('position', 'EM', '', 'พนักงาน', NULL, 1),
('position', '1', '', 'ที่ปรึกษา', NULL, 1),
('department', '4', '', 'คุณภาพ', NULL, 1),
('position', '2', '', 'ผู้ช่วยผู้จัดการ', NULL, 1),
('department', '5', '', 'IT', NULL, 1),
('department', '6', '', 'ผลิต', NULL, 1),
('department', '7', '', 'ขาย', NULL, 1),
('position', '3', '', 'อาวุโส', NULL, 1),
('department', '8', '', 'การเงิน', NULL, 1),
('department', '9', '', 'การตลาด', NULL, 1),
('position', '4', '', 'ผู้อำนวยการ', NULL, 1),
('department', '10', '', 'บัญชี', NULL, 1),
('position', '5', '', 'ผู้เชี่ยวชาญ', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `app_language`
--

CREATE TABLE `app_language` (
  `id` int(11) NOT NULL,
  `key` text NOT NULL,
  `type` varchar(5) NOT NULL,
  `owner` varchar(20) NOT NULL,
  `js` tinyint(1) NOT NULL,
  `th` text DEFAULT NULL,
  `en` text DEFAULT NULL,
  `la` text CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `app_language`
--

INSERT INTO `app_language` (`id`, `key`, `type`, `owner`, `js`, `th`, `en`, `la`) VALUES
(1, 'ACCEPT_ALL', 'text', 'index', 1, 'ยอมรับทั้งหมด', 'Accept all', 'ຍອມຮັບທັງຫມົດ'),
(2, 'ADD', 'text', 'index', 1, 'เพิ่ม', 'Add', 'ເພີ່ມ​ຂຶ້ນ'),
(3, 'CANCEL', 'text', 'index', 1, 'ยกเลิก', 'Cancel', 'ຍົກເລີກ'),
(4, 'CHANGE_COLOR', 'text', 'index', 1, 'เปลี่ยนสี', 'change color', 'ປ່ຽນສີ'),
(5, 'CHECK', 'text', 'index', 1, 'เลือก', 'check', 'ເລືອກ'),
(6, 'CHECKBOX', 'text', 'index', 1, 'ตัวเลือก', 'Checkbox', 'ກ່ອງກາເຄື່ອງໝາຍ'),
(7, 'COOKIES_SETTINGS', 'text', 'index', 1, 'ตั้งค่าคุกกี้', 'Cookies settings', 'ຕັ້ງຄ່າຄຸກກີ'),
(8, 'DELETE', 'text', 'index', 1, 'ลบ', 'delete', 'ລຶບ'),
(9, 'DISABLE', 'text', 'index', 1, 'ปิดใช้งาน', 'Disable', 'ປິດໃຊ້ການ'),
(10, 'DRAG_AND_DROP_TO_REORDER', 'text', 'index', 1, 'ลากและวางเพื่อจัดลำดับใหม่', 'Drag and drop to reorder', 'ລາກແລ້ວວາງລົງເພື່ອຈັດຮຽງໃໝ່'),
(11, 'ENABLE', 'text', 'index', 1, 'เปิดใช้งาน', 'Enable', 'ເປີດໃຊ້ການ'),
(12, 'GO_TO_PAGE', 'text', 'index', 1, 'ไปหน้าที่', 'go to page', 'ໄປ​ຫນ້າ​ທີ່​'),
(13, 'INVALID_DATA', 'text', 'index', 1, 'ข้อมูล XXX ไม่ถูกต้อง', 'XXX Invalid data', 'ຂໍ້ມູນ XXX ບໍ່ຖືກຕ້ອງ'),
(14, 'ITEM', 'text', 'index', 1, 'รายการ', 'item', 'ລາຍການ'),
(15, 'ITEMS', 'text', 'index', 1, 'รายการ', 'items', 'ລາຍການ'),
(16, 'NEXT_MONTH', 'text', 'index', 1, 'เดือนถัดไป', 'Next Month', 'ເດືອນຕໍ່ໄປ'),
(17, 'PLEASE_BROWSE_FILE', 'text', 'index', 1, 'กรุณาเลือกไฟล์', 'Please browse file', 'ກະລຸນາເລືອກແຟ້ມຂໍ້ມູນ'),
(18, 'PLEASE_FILL_IN', 'text', 'index', 1, 'กรุณากรอก', 'Please fill in', 'ກະລຸນາພີ່ມ'),
(19, 'PLEASE_SAVE_BEFORE_CONTINUING', 'text', 'index', 1, 'กรุณาบันทึก ก่อนดำเนินการต่อ', 'Please save before continuing', 'ກະລຸນາບັນທຶກກ່ອນດຳເນີນການຕໍ່'),
(20, 'PLEASE_SELECT', 'text', 'index', 1, 'กรุณาเลือก', 'Please select', 'ກະລຸນາເລືອກ'),
(21, 'PLEASE_SELECT_AT_LEAST_ONE_ITEM', 'text', 'index', 1, 'กรุณาเลือก XXX อย่างน้อย 1 รายการ', 'Please select XXX at least one item', 'ກະລຸນາເລືອກ XXX ຢ່າງໜ້ອຍໜຶ່ງລາຍການ'),
(22, 'PREV_MONTH', 'text', 'index', 1, 'เดือนก่อนหน้า', 'Prev Month', 'ເດືອນທີ່ຜ່ານມາ'),
(23, 'SELECT_ALL', 'text', 'index', 1, 'เลือกทั้งหมด', 'select all', 'ເລືອກທັງໝົດ'),
(24, 'SELECT_NONE', 'text', 'index', 1, 'ไม่เลือกรายการใดเลย', 'select none', 'ບໍ່ເລືອກລາຍການໃດເລີຍ'),
(25, 'SHOWING_PAGE', 'text', 'index', 1, 'กำลังแสดงหน้าที่', 'showing page', 'ສະແດງໜ້າທີ່'),
(26, 'SORRY_XXX_NOT_FOUND', 'text', 'index', 1, 'ขออภัย ไม่พบ XXX ที่ต้องการ', 'Sorry XXX not found', 'ຂໍອະໄພບໍ່ພົບ XXX ທີ່ຕ້ອງການ'),
(27, 'SUCCESSFULLY_COPIED_TO_CLIPBOARD', 'text', 'index', 1, 'สำเนาไปยังคลิปบอร์ดเรียบร้อย', 'Successfully copied to clipboard', 'ສຳເນົາໄປຍັງຄິບບອດຮຽບຮ້ອຍ'),
(28, 'SUCCESSFULLY_UPLOADED_XXX_FILES', 'text', 'index', 1, 'อัปโหลดเรียบร้อย XXX ไฟล์', 'Successfully uploaded XXX files', 'ອັບໂຫຼດຮຽບຮ້ອຍ XXX ແຟ້ມ'),
(29, 'THE_TYPE_OF_FILE_IS_INVALID', 'text', 'index', 1, 'ชนิดของไฟล์ไม่ถูกต้อง', 'The type of file is invalid', 'ຊະນິດຂອງແຟ້ມບໍ່ຖືກຕ້ອງ'),
(30, 'UNCHECK', 'text', 'index', 1, 'ไม่เลือก', 'uncheck', 'ບໍ່ເລືອກ'),
(31, 'YOU_WANT_TO_XXX', 'text', 'index', 1, 'คุณต้องการ XXX ?', 'You want to XXX ?', 'ທ່ານບໍ່ຕ້ອງການ XXX ?'),
(32, 'YOU_WANT_TO_XXX_THE_SELECTED_ITEMS', 'text', 'index', 1, 'คุณต้องการ XXX รายการที่เลือก ?', 'You want to XXX the selected items ?', 'ທ່ານຕ້ອງການ XXX ລາຍການທີ່ເລືອກ?'),
(33, 'Approval Settings', 'text', 'index', 0, 'การตั้งค่าการอนุมัติ', 'Approval Settings', 'ການຕັ້ງຄ່າການອະນຸມັດ'),
(34, 'Auto Calculate', 'text', 'index', 0, 'คำนวณอัตโนมัติ', 'Auto Calculate', 'ຄິດໄລ່ອັດຕະໂນມັດ'),
(35, 'Automatically calculate salary components', 'text', 'index', 0, 'คำนวณส่วนประกอบเงินเดือนโดยอัตโนมัติ', 'Automatically calculate salary components', 'ຄິດໄລ່ສ່ວນປະກອບເງິນເດືອນໂດຍອັດຕະໂນມັດ'),
(36, 'BOOLEANS', 'array', 'index', 0, 'a:2:{i:0;s:27:\"ปิดใช้งาน\";i:1;s:30:\"เปิดใช้งาน\";}', 'a:2:{i:0;s:7:\"Disable\";i:1;s:7:\"Enabled\";}', 'a:2:{i:0;s:27:\"ປິດໃຊ້ວຽກ\";i:1;s:30:\"ເປີດໃຊ້ວຽກ\";}'),
(37, 'Calculated automatically', 'text', 'index', 0, 'คำนวณอัตโนมัติ', 'Calculated automatically', 'ຄິດໄລ່ອັດຕະໂນມັດ'),
(38, 'Calculation Settings', 'text', 'index', 0, 'การตั้งค่าการคำนวณ', 'Calculation Settings', 'ການຕັ້ງຄ່າການຄິດໄລ່'),
(39, 'CATEGORIES', 'array', 'index', 0, 'a:2:{s:10:\"department\";s:12:\"แผนก\";s:8:\"position\";s:21:\"ตำแหน่ง\";}', 'a:2:{s:10:\"department\";s:10:\"Department\";s:8:\"position\";s:8:\"Position\";}', 'a:2:{s:10:\"department\";s:15:\"ພະແນກ\";s:8:\"position\";s:30:\"ຕໍາ​ແຫນ່ງ​\";}'),
(40, 'CSV_ENCODING', 'array', 'index', 0, 'a:2:{s:5:\"UTF-8\";s:16:\"UTF-8 (with BOM)\";s:7:\"TIS-620\";s:7:\"TIS-620\";}', 'a:2:{s:7:\"TIS-620\";s:7:\"TIS-620\";s:5:\"UTF-8\";s:16:\"UTF-8 (with BOM)\";}', 'a:2:{s:5:\"UTF-8\";s:16:\"UTF-8 (with BOM)\";s:7:\"TIS-620\";s:7:\"TIS-620\";}'),
(41, 'DATE_FORMAT', 'text', 'index', 0, 'd M Y เวลา H:i น.', 'd M Y, H:i', 'd M Y ເວລາ H:i'),
(42, 'DATE_LONG', 'array', 'index', 0, 'a:7:{i:0;s:21:\"อาทิตย์\";i:1;s:18:\"จันทร์\";i:2;s:18:\"อังคาร\";i:3;s:9:\"พุธ\";i:4;s:24:\"พฤหัสบดี\";i:5;s:15:\"ศุกร์\";i:6;s:15:\"เสาร์\";}', 'a:7:{i:0;s:6:\"Sunday\";i:1;s:6:\"Monday\";i:2;s:7:\"Tuesday\";i:3;s:9:\"Wednesday\";i:4;s:8:\"Thursday\";i:5;s:6:\"Friday\";i:6;s:8:\"Saturday\";}', 'a:7:{i:0;s:15:\"ອາທິດ\";i:1;s:9:\"ຈັນ\";i:2;s:18:\"ອັງຄານ\";i:3;s:9:\"ພຸດ\";i:4;s:15:\"ພະຫັດ\";i:5;s:9:\"ສຸກ\";i:6;s:12:\"ເສົາ\";}'),
(43, 'DATE_SHORT', 'array', 'index', 0, 'a:7:{i:0;s:7:\"อา.\";i:1;s:4:\"จ.\";i:2;s:4:\"อ.\";i:3;s:4:\"พ.\";i:4;s:7:\"พฤ.\";i:5;s:4:\"ศ.\";i:6;s:4:\"ส.\";}', 'a:7:{i:0;s:2:\"Su\";i:1;s:2:\"Mo\";i:2;s:2:\"Tu\";i:3;s:2:\"We\";i:4;s:2:\"Th\";i:5;s:2:\"Fr\";i:6;s:2:\"Sa\";}', 'a:7:{i:0;s:4:\"ທ.\";i:1;s:4:\"ຈ.\";i:2;s:4:\"ຄ.\";i:3;s:4:\"ພ.\";i:4;s:4:\"ພ.\";i:5;s:4:\"ສ.\";i:6;s:4:\"ສ.\";}'),
(44, 'Decimal places', 'text', 'index', 0, 'จำนวนทศนิยม', 'Decimal places', 'ຈຳນວນທົດສະນິຍົມ'),
(45, 'Default Income Tax percentage', 'text', 'index', 0, 'เปอร์เซ็นต์ภาษีเงินได้เริ่มต้น', 'Default Income Tax percentage', 'ເປີເຊັນພາສີລາຍໄດ້ເລີ່ມຕົ້ນ'),
(46, 'DOWNLOAD_ACTIONS', 'array', 'index', 0, 'a:2:{i:0;s:39:\"ดาวน์โหลดไฟล์\";i:1;s:54:\"เปิดไฟล์ (ที่รู้จัก)\";}', 'a:2:{i:0;s:8:\"Download\";i:1;s:26:\"Open file (know file type)\";}', 'a:2:{i:0;s:33:\"ດາວໂຫຼດແຟ້ມ\";i:1;s:53:\"ເປີດແຟ້ມ(ທີ່ຮູ້ຈັກ)\";}'),
(47, 'Email Notification', 'text', 'index', 0, 'การแจ้งเตือนทางอีเมล', 'Email Notification', 'ການແຈ້ງເຕືອນທາງອີເມລ'),
(48, 'Identification No.', 'text', 'index', 0, 'รหัสพนักงาน', 'Employee ID', 'ID ພະນັກງານ'),
(49, 'Import/Export Settings', 'text', 'index', 0, 'การตั้งค่านำเข้า/ส่งออก', 'Import/Export Settings', 'ການຕັ້ງຄ່ານຳເຂົ້າ/ສົ່ງອອກ'),
(50, 'LEAVE_PERIOD', 'array', 'index', 0, 'a:3:{i:0;s:21:\"เต็มวัน\";i:1;s:36:\"ครึ่งวันเช้า\";i:2;s:36:\"ครึ่งวันบ่าย\";}', 'a:3:{i:0;s:7:\"All day\";i:1;s:16:\"Half day morning\";i:2;s:18:\"Half day afternoon\";}', 'a:3:{i:0;s:21:\"ມື້ເຕັມ\";i:1;s:39:\"ມື້ເຊົ້າເຄິ່ງ\";i:2;s:45:\"ຕອນບ່າຍມື້ເຄິ່ງ\";}'),
(51, 'LINE_FOLLOW_MESSAGE', 'text', 'index', 0, 'สวัสดี คุณ :name นี่คือบัญชีทางการของ :title เราจะส่งข่าวสารถึงคุณผ่านช่องทางนี้', 'Hello, :name This is :title official account. We will send you news via this channel.', 'ສະບາຍດີ, :name ນີ້ແມ່ນບັນຊີທາງການຂອງ :title ພວກເຮົາຈະສົ່ງຂ່າວໃຫ້ທ່ານຜ່ານຊ່ອງທາງນີ້.'),
(52, 'LINE_REPLY_MESSAGE', 'text', 'index', 0, 'ขออภัยไม่สามารถตอบกลับข้อความนี้ได้', 'Sorry, can&#039;t reply to this message.', 'ຂໍອະໄພ, ບໍ່ສາມາດຕອບກັບຂໍ້ຄວາມນີ້ໄດ້.'),
(53, 'LOGIN_FIELDS', 'array', 'index', 0, 'a:4:{s:8:\"username\";s:30:\"ชื่อผู้ใช้\";s:5:\"email\";s:15:\"อีเมล\";s:5:\"phone\";s:39:\"เบอร์โทรศัพท์\";s:7:\"id_card\";s:30:\"เลขประชาชน\";}', 'a:4:{s:8:\"username\";s:8:\"Username\";s:5:\"email\";s:5:\"Email\";s:5:\"phone\";s:5:\"Phone\";s:7:\"id_card\";s:18:\"Identification No.\";}', 'a:4:{s:8:\"username\";s:27:\"ຊື່ຜູ້ໃຊ້\";s:5:\"email\";s:15:\"ອີເມວ\";s:5:\"phone\";s:30:\"ເບີໂທລະສັບ\";s:7:\"id_card\";s:39:\"ເລກບັດປະຈຳຕົວ\";}'),
(54, 'MAIL_PROGRAMS', 'array', 'index', 0, 'a:3:{i:0;s:43:\"ส่งจดหมายด้วย PHP\";i:1;s:72:\"ส่งจดหมายด้วย PHPMailer+SMTP (แนะนำ)\";i:2;s:58:\"ส่งจดหมายด้วย PHPMailer+PHP Mail\";}', 'a:3:{i:0;s:13:\"Send with PHP\";i:1;s:38:\"Send with PHPMailer+SMTP (recommended)\";i:2;s:28:\"Send with PHPMailer+PHP Mail\";}', 'a:3:{i:0;s:46:\"ສົ່ງຈົດໝາຍດ້ວຍ PHP\";i:1;s:75:\"ສົ່ງຈົດໝາຍດ້ວຍ PHPMailer+SMTP (ແນະນຳ)\";i:2;s:61:\"ສົ່ງຈົດໝາຍດ້ວຍ PHPMailer+PHP Mail\";}'),
(55, 'Maximum amount for social security calculation', 'text', 'index', 0, 'จำนวนเงินสูงสุดสำหรับการคำนวณประกันสังคม', 'Maximum amount for social security calculation', 'ຈຳນວນເງິນສູງສຸດສຳລັບການຄິດໄລ່ປະກັນສັງຄົມ'),
(56, 'Money format', 'text', 'index', 0, 'รูปแบบตัวเลขเงิน', 'Money format', 'ຮູບແບບຕົວເລກເງິນ'),
(57, 'MONTH_LONG', 'array', 'index', 0, 'a:12:{i:1;s:18:\"มกราคม\";i:2;s:30:\"กุมภาพันธ์\";i:3;s:18:\"มีนาคม\";i:4;s:18:\"เมษายน\";i:5;s:21:\"พฤษภาคม\";i:6;s:24:\"มิถุนายน\";i:7;s:21:\"กรกฎาคม\";i:8;s:21:\"สิงหาคม\";i:9;s:21:\"กันยายน\";i:10;s:18:\"ตุลาคม\";i:11;s:27:\"พฤศจิกายน\";i:12;s:21:\"ธันวาคม\";}', 'a:12:{i:1;s:7:\"January\";i:2;s:8:\"February\";i:3;s:5:\"March\";i:4;s:5:\"April\";i:5;s:3:\"May\";i:6;s:4:\"June\";i:7;s:4:\"July\";i:8;s:6:\"August\";i:9;s:9:\"September\";i:10;s:7:\"October\";i:11;s:8:\"November\";i:12;s:8:\"December\";}', 'a:12:{i:1;s:18:\"ມັງກອນ\";i:2;s:15:\"ກຸມພາ\";i:3;s:12:\"ມີນາ\";i:4;s:12:\"ເມສາ\";i:5;s:21:\"ພຶດສະພາ\";i:6;s:18:\"ມິຖຸນາ\";i:7;s:21:\"ກໍລະກົດ\";i:8;s:15:\"ສິງຫາ\";i:9;s:15:\"ກັນຍາ\";i:10;s:12:\"ຕຸລາ\";i:11;s:15:\"ພະຈິກ\";i:12;s:15:\"ທັນວາ\";}'),
(58, 'MONTH_SHORT', 'array', 'index', 0, 'a:12:{i:1;s:8:\"ม.ค.\";i:2;s:8:\"ก.พ.\";i:3;s:11:\"มี.ค.\";i:4;s:11:\"เม.ย.\";i:5;s:8:\"พ.ค.\";i:6;s:11:\"มิ.ย.\";i:7;s:8:\"ก.ค.\";i:8;s:8:\"ส.ค.\";i:9;s:8:\"ก.ย.\";i:10;s:8:\"ต.ค.\";i:11;s:8:\"พ.ย.\";i:12;s:8:\"ธ.ค.\";}', 'a:12:{i:1;s:3:\"Jan\";i:2;s:3:\"Feb\";i:3;s:3:\"Mar\";i:4;s:3:\"Apr\";i:5;s:3:\"May\";i:6;s:3:\"Jun\";i:7;s:3:\"Jul\";i:8;s:3:\"Aug\";i:9;s:3:\"Sep\";i:10;s:3:\"Oct\";i:11;s:3:\"Nov\";i:12;s:3:\"Dec\";}', 'a:12:{i:1;s:8:\"ມ.ກ.\";i:2;s:8:\"ກ.ພ.\";i:3;s:11:\"ມີ.ນ.\";i:4;s:11:\"ເມ.ສ.\";i:5;s:8:\"ພ.ພ.\";i:6;s:11:\"ມິ.ນ.\";i:7;s:8:\"ກ.ກ.\";i:8;s:8:\"ສ.ຫ.\";i:9;s:8:\"ກ.ຍ.\";i:10;s:8:\"ຕ.ລ.\";i:11;s:8:\"ພ.ຈ.\";i:12;s:8:\"ທ.ວ.\";}'),
(59, 'Name', 'text', 'index', 0, 'ชื่อ นามสกุล', 'Name Surname', 'ຊື່ ນາມສະກຸນ'),
(60, 'Number format such as currency', 'text', 'index', 0, 'รูปแบบตัวเลข เช่น สกุลเงิน', 'Number format such as currency', 'ຮູບແບບຕົວເລກ ເຊັ່ນ ສະກຸນເງິນ'),
(61, 'Overtime hourly rate multiplier (normal rate x this value)', 'text', 'index', 0, 'ตัวคูณอัตราค่าล่วงเวลา (อัตราปกติ x ค่านี้)', 'Overtime hourly rate multiplier (normal rate x this value)', 'ຕົວຄູນອັດຕາຄ່າລ່ວງເວລາ (ອັດຕາປົກກະຕິ x ຄ່ານີ້)'),
(62, 'Overtime Rate', 'text', 'index', 0, 'อัตราค่าล่วงเวลา', 'Overtime Rate', 'ອັດຕາຄ່າລ່ວງເວລາ'),
(63, 'PERMISSIONS', 'array', 'index', 0, 'a:2:{s:10:\"can_config\";s:60:\"สามารถตั้งค่าระบบได้\";s:22:\"can_view_usage_history\";s:93:\"สามารถดูประวัติการใช้งานระบบได้\";}', 'a:2:{s:10:\"can_config\";s:24:\"Can configure the system\";s:22:\"can_view_usage_history\";s:33:\"Able to view system usage history\";}', 'a:2:{s:10:\"can_config\";s:60:\"ສາມາດຕັ້ງຄ່າລະບົບໄດ້\";s:22:\"can_view_usage_history\";s:90:\"ສາມາດເບິ່ງປະຫວັດການນໍາໃຊ້ລະບົບ\";}'),
(64, 'PUBLISHEDS', 'array', 'index', 0, 'a:2:{i:0;s:45:\"ระงับการเผยแพร่\";i:1;s:21:\"เผยแพร่\";}', 'a:2:{i:0;s:11:\"Unpublished\";i:1;s:9:\"Published\";}', 'a:2:{i:0;s:45:\"ລະງັບການເຜີຍແຜ່\";i:1;s:21:\"ເຜີຍແຜ່\";}'),
(65, 'Require Approval', 'text', 'index', 0, 'ต้องการอนุมัติ', 'Require Approval', 'ຕ້ອງການອະນຸມັດ'),
(66, 'Require approval before salary payment', 'text', 'index', 0, 'ต้องการอนุมัติก่อนจ่ายเงินเดือน', 'Require approval before salary payment', 'ຕ້ອງການອະນຸມັດກ່ອນຈ່າຍເງິນເດືອນ'),
(67, 'SALARY_STATUS', 'array', 'index', 0, 'a:2:{i:0;s:27:\"รออนุมัติ\";i:1;s:21:\"อนุมัติ\";}', 'a:2:{i:0;s:7:\"Pending\";i:1;s:7:\"Approve\";}', 'a:2:{i:0;s:69:\"ການອະນຸມັດທີ່ຍັງຄ້າງຢູ່\";i:1;s:21:\"ອະນຸມັດ\";}'),
(68, 'Send email notification when salary is approved', 'text', 'index', 0, 'ส่งการแจ้งเตือนทางอีเมลเมื่อเงินเดือนได้รับการอนุมัติ', 'Send email notification when salary is approved', 'ສົ່ງການແຈ້ງເຕືອນທາງອີເມລເມື່ອເງິນເດືອນໄດ້ຮັບການອະນຸມັດ'),
(69, 'SEXES', 'array', 'index', 0, 'a:3:{s:1:\"u\";s:21:\"ไม่ระบุ\";s:1:\"f\";s:12:\"หญิง\";s:1:\"m\";s:9:\"ชาย\";}', 'a:3:{s:1:\"u\";s:13:\"Not specified\";s:1:\"f\";s:6:\"Female\";s:1:\"m\";s:4:\"Male\";}', 'a:3:{s:1:\"u\";s:30:\"ບໍ່ໄດ້ລະບຸ\";s:1:\"f\";s:9:\"ຍິງ\";s:1:\"m\";s:9:\"ຊາຍ\";}'),
(70, 'SMS_SENDER_COMMENT', 'text', 'index', 0, 'บาง Package อาจไม่สามารถกำหนดชื่อผู้ส่งได้ กรุณาตรวจสอบกับผู้ให้บริการ', 'Some packages may not be able to assign the sender name. Please check with the service provider.', 'ບາງແພັກເກັດອາດບໍ່ສາມາດມອບຊື່ຜູ້ສົ່ງໄດ້. ກະລຸນາກວດສອບກັບຜູ້ໃຫ້ບໍລິການ.'),
(71, 'SMTPSECURIES', 'array', 'index', 0, 'a:2:{s:0:\"\";s:57:\"การเชื่อมต่อแบบปกติ\";s:3:\"ssl\";s:72:\"การเชื่อมต่อที่ปลอดภัย (SSL)\";}', 'a:2:{s:0:\"\";s:10:\"Clear Text\";s:3:\"ssl\";s:38:\"Server using a secure connection (SSL)\";}', 'a:2:{s:0:\"\";s:66:\"ການເຊື່ອມຕໍ່ແບບປົກກະຕິ\";s:3:\"ssl\";s:66:\"ການເຊື່ອມຕໍ່ທີ່ປອດໄຟ (SSL)\";}'),
(72, 'Social Security Employer', 'text', 'index', 0, 'ประกันสังคมนายจ้าง', 'Social Security Employer', 'ປະກັນສັງຄົມນາຍຈ້າງ'),
(73, 'Social Security Max Amount', 'text', 'index', 0, 'จำนวนเงินสูงสุดประกันสังคม', 'Social Security Max Amount', 'ຈຳນວນເງິນສູງສຸດປະກັນສັງຄົມ'),
(74, 'Social security percentage (employee contribution)', 'text', 'index', 0, 'เปอร์เซ็นต์ประกันสังคม (ส่วนของพนักงาน)', 'Social security percentage (employee contribution)', 'ເປີເຊັນປະກັນສັງຄົມ (ສ່ວນຂອງພະນັກງານ)'),
(75, 'Social security percentage (employer contribution)', 'text', 'index', 0, 'เปอร์เซ็นต์ประกันสังคม (ส่วนของนายจ้าง)', 'Social security percentage (employer contribution)', 'ເປີເຊັນປະກັນສັງຄົມ (ສ່ວນຂອງນາຍຈ້າງ)'),
(76, 'Standard working days per month for calculation', 'text', 'index', 0, 'วันทำงานมาตรฐานต่อเดือนสำหรับการคำนวณ', 'Standard working days per month for calculation', 'ມື້ເຮັດວຽກມາດຕະຖານຕໍ່ເດືອນສຳລັບການຄິດໄລ່'),
(77, 'Standard working hours per day', 'text', 'index', 0, 'ชั่วโมงทำงานมาตรฐานต่อวัน', 'Standard working hours per day', 'ຊົ່ວໂມງເຮັດວຽກມາດຕະຖານຕໍ່ວັນ'),
(78, 'Tax and Deductions', 'text', 'index', 0, 'ภาษีและรายหัก', 'Tax and Deductions', 'ພາສີ ແລະ ລາຍຫັກ'),
(79, 'The number of decimal places for salary amounts', 'text', 'index', 0, 'จำนวนตำแหน่งทศนิยมสำหรับจำนวนเงินเดือน', 'The number of decimal places for salary amounts', 'ຈຳນວນຕຳແໜ່ງທົດສະນິຍົມສຳລັບຈຳນວນເງິນເດືອນ'),
(80, 'THEME_WIDTH', 'array', 'index', 0, 'a:3:{s:7:\"default\";s:33:\"ค่าเริ่มต้น\";s:4:\"wide\";s:15:\"กว้าง\";s:9:\"fullwidth\";s:30:\"กว้างพิเศษ\";}', 'a:3:{s:7:\"default\";s:7:\"Default\";s:4:\"wide\";s:4:\"Wide\";s:9:\"fullwidth\";s:10:\"Extra wide\";}', 'a:3:{s:7:\"default\";s:36:\"ຄ່າເລີ່ມຕົ້ນ\";s:4:\"wide\";s:15:\"ກວ້າງ\";s:9:\"fullwidth\";s:30:\"ກວ້າງພິເສດ\";}'),
(81, 'TIME_FORMAT', 'text', 'index', 0, 'H:i น.', 'H:i', 'H:i'),
(82, 'Working Days per Month', 'text', 'index', 0, 'วันทำงานต่อเดือน', 'Working Days per Month', 'ມື້ເຮັດວຽກຕໍ່ເດືອນ'),
(83, 'Working Hours per Day', 'text', 'index', 0, 'ชั่วโมงทำงานต่อวัน', 'Working Hours per Day', 'ຊົ່ວໂມງເຮັດວຽກຕໍ່ວັນ'),
(84, 'YEAR_OFFSET', 'int', 'index', 0, '543', '0', '0'),
(85, 'Active employees', 'text', 'index', 0, 'พนักงานที่ใช้งาน', 'Active employees', 'ພະນັກງານທີ່ໃຊ້ງານ'),
(86, 'Add new salary record', 'text', 'index', 0, 'เพิ่มบันทึกเงินเดือนใหม่', 'Add new salary record', 'ເພີ່ມບັນທຶກເງິນເດືອນໃໝ່'),
(87, 'All departments', 'text', 'index', 0, 'ทุกแผนก', 'All departments', 'ທຸກພະແນກ'),
(88, 'Check salary records', 'text', 'index', 0, 'ตรวจสอบบันทึกเงินเดือน', 'Check salary records', 'ກວດເບິ່ງບັນທຶກເງິນເດືອນ'),
(89, 'Current month', 'text', 'index', 0, 'เดือนปัจจุบัน', 'Current month', 'ເດືອນປັດຈຸບັນ'),
(90, 'Employee Count Trend', 'text', 'index', 0, 'แนวโน้มจำนวนพนักงาน', 'Employee Count Trend', 'ແນວໂນ້ມຈໍານວນພະນັກງານ'),
(91, 'Last Salary', 'text', 'index', 0, 'เงินเดือนล่าสุด', 'Last Salary', 'ເງິນເດືອນຄັ້ງສຸດທ້າຍ'),
(92, 'My Salary This Month', 'text', 'index', 0, 'เงินเดือนของฉันเดือนนี้', 'My Salary This Month', 'ເງິນເດືອນຂອງຂ້ອຍເດືອນນີ້'),
(93, 'My Salary Trend', 'text', 'index', 0, 'แนวโน้มเงินเดือนของฉัน', 'My Salary Trend', 'ແນວໂນ້ມເງິນເດືອນຂອງຂ້ອຍ'),
(94, 'Need review', 'text', 'index', 0, 'ต้องการตรวจสอบ', 'Need review', 'ຕ້ອງການທົບທວນ'),
(95, 'No data available', 'text', 'index', 0, 'ไม่มีข้อมูล', 'No data available', 'ບໍ່ມີຂໍ້ມູນ'),
(96, 'Pending Records', 'text', 'index', 0, 'รายการรอดำเนินการ', 'Pending Records', 'ບັນທຶກທີ່ລໍຖ້າດໍາເນີນການ'),
(97, 'Salary History', 'text', 'index', 0, 'ประวัติเงินเดือน', 'Salary History', 'ປະຫວັດເງິນເດືອນ'),
(98, 'Salary Records', 'text', 'index', 0, 'บันทึกเงินเดือน', 'Salary Records', 'ບັນທຶກເງິນເດືອນ'),
(99, 'Salary Trend by Department', 'text', 'index', 0, 'แนวโน้มเงินเดือนตามแผนก', 'Salary Trend by Department', 'ແນວໂນ້ມເງິນເດືອນຕາມພະແນກ'),
(100, 'This month', 'text', 'index', 0, 'เดือนนี้', 'This month', 'ເດືອນນີ້'),
(101, 'Total Employees', 'text', 'index', 0, 'พนักงานทั้งหมด', 'Total Employees', 'ພະນັກງານທັງໝົດ'),
(102, 'Total Salary This Month', 'text', 'index', 0, 'เงินเดือนรวมเดือนนี้', 'Total Salary This Month', 'ເງິນເດືອນລວມເດືອນນີ້'),
(103, 'View Details', 'text', 'index', 0, 'ดูรายละเอียด', 'View Details', 'ເບິ່ງລາຍລະອຽດ'),
(104, 'View history', 'text', 'index', 0, 'ดูประวัติ', 'View history', 'ເບິ່ງປະຫວັດ'),
(105, '0.0.0.0 mean all IP addresses', 'text', 'index', 0, '0.0.0.0 หมายถึงอนุญาตทั้งหมด', NULL, '0.0.0.0 ຫມາຍຄວາມວ່າອະນຸຍາດໃຫ້ທັງຫມົດ'),
(106, ':name for new members Used when members need to specify', 'text', 'index', 0, ':name เริ่มต้นสำหรับสมาชิกใหม่ ใช้ในกรณีที่สมาชิกจำเป็นต้องระบุ', NULL, ':name ສໍາລັບສະມາຊິກໃຫມ່ ໃຊ້ໃນເວລາທີ່ສະມາຊິກຕ້ອງການກໍານົດ'),
(107, 'Accept all', 'text', 'index', 0, 'ยอมรับทั้งหมด', NULL, 'ຍອມຮັບທັງຫມົດ'),
(108, 'Accept member verification request', 'text', 'index', 0, 'ยอมรับคำขอยืนยันสมาชิก', NULL, 'ຍອມຮັບການຮ້ອງຂໍການຢັ້ງຢືນສະມາຊິກ'),
(109, 'Accept this agreement', 'text', 'index', 0, 'ยอมรับข้อตกลง', NULL, 'ຍອມຮັບຂໍ້ຕົກລົງ'),
(110, 'Add', 'text', 'index', 0, 'เพิ่ม', NULL, 'ເພີ່ມ'),
(111, 'Add friend', 'text', 'index', 0, 'เพิ่มเพื่อน', NULL, 'ເພີ່ມເພື່ອນ'),
(112, 'Address', 'text', 'index', 0, 'ที่อยู่', NULL, 'ທີ່ຢູ່'),
(113, 'Address details', 'text', 'index', 0, 'รายละเอียดที่อยู่', NULL, 'ລາຍລະອຽດທີ່ຢູ່'),
(114, 'Administrator status It is of utmost importance to do everything', 'text', 'index', 0, 'สถานะผู้ดูแลระบบ มีความสำคัญสูงสุดสามารถทำได้ทุกอย่าง', NULL, 'ສະຖານະຜູ້ເບິ່ງແຍງລະບົບມີຄວາມສຳຄັນສຸງທີ່ສຸດສາມາດເຮັດໄດ້ທຸກຢ່າງ'),
(115, 'All :count entries, displayed :start to :end, page :page of :total pages', 'text', 'index', 0, 'ทั้งหมด :count รายการ, แสดง :start ถึง :end, หน้าที่ :page จากทั้งหมด :total หน้า', NULL, 'ທັງໝົດ :count ລາຍການ, ສະແດງ :start ເຖິງ :end, ໜ້າທີ່ :page ຈາກທັງໝົດ:total ໜ້າ'),
(116, 'all items', 'text', 'index', 0, 'ทั้งหมด', NULL, 'ທັງໝົດ'),
(117, 'Allowance', 'text', 'index', 0, 'เบี้ยเลี้ยง', NULL, 'ເງິນອຸດໜູນ'),
(118, 'Already registered The system has sent an OTP code to the number you registered. Please check the SMS and enter the code to confirm your phone number.', 'text', 'index', 0, 'ลงทะเบียนเรียบร้อยแล้ว ระบบได้ส่งรหัส OTP ไปยังเบอร์ที่ท่านได้ลงทะเบียนไว้ กรุณาตรวจสอบ SMS แล้วให้นำรหัสมากรอกเพื่อเป็นการยืนยันเบอร์โทรศัพท์', NULL, 'ລົງ​ທະ​ບຽນ​ແລ້ວ ລະບົບໄດ້ສົ່ງລະຫັດ OTP ໄປຫາເບີໂທລະສັບທີ່ທ່ານລົງທະບຽນ, ກະລຸນາກວດເບິ່ງ SMS ແລະໃສ່ລະຫັດເພື່ອຢືນຢັນເບີໂທລະສັບຂອງທ່ານ.'),
(119, 'Always enabled', 'text', 'index', 0, 'เปิดใช้งานตลอดเวลา', NULL, 'ເປີດສະເໝີ'),
(120, 'API settings', 'text', 'index', 0, 'ตั้งค่า API', NULL, 'ຕັ້ງຄ່າ API'),
(121, 'Approval', 'text', 'index', 0, 'การอนุมัติ', NULL, 'ການອະນຸມັດ'),
(122, 'Approval No. :no', 'text', 'index', 0, 'อนุมัติลำดับที่ :no', NULL, 'ອັນດັບ :no ການອະນຸມັດ'),
(123, 'Approve', 'text', 'index', 0, 'อนุมัติ', NULL, 'ອະນຸມັດ'),
(124, 'Attached file', 'text', 'index', 0, 'ไฟล์แนบ', NULL, 'ເອກະສານຕິດຄັດ'),
(125, 'Authentication require', 'text', 'index', 0, 'การตรวจสอบความถูกต้อง', NULL, 'ການກວດກາຄວາມຖືກຕ້ອງ'),
(126, 'Auto calculated from settings', 'text', 'index', 0, 'คำนวณอัตโนมัติตามการตั้งค่า', NULL, 'ອັດຕະໂນມັດຄິດໄລ່ຈາກການຕັ້ງຄ່າ'),
(127, 'Avatar', 'text', 'index', 0, 'รูปสมาชิก', NULL, 'ຮູບແທນຕົວ'),
(128, 'Average Salary', 'text', 'index', 0, 'เงินเดือนเฉลี่ย', NULL, 'ເງິນເດືອນສະເລ່ຍ'),
(129, 'Background color', 'text', 'index', 0, 'สีพื้นหลัง', NULL, 'ສີພື້ນຫລັງ'),
(130, 'Background image', 'text', 'index', 0, 'รูปภาพพื้นหลัง', NULL, 'ພາບພື້ນຫລັງ'),
(131, 'Basic Salary', 'text', 'index', 0, 'ฐานเงินเดือน', NULL, 'ເງິນເດືອນພື້ນຖານ'),
(132, 'Bonus', 'text', 'index', 0, 'โบนัส', NULL, 'ໂບນັດ'),
(133, 'Browse file', 'text', 'index', 0, 'เลือกไฟล์', NULL, 'ເລືອກໄຟລ໌'),
(134, 'Browse image uploaded, type :type', 'text', 'index', 0, 'เลือกรูปภาพอัปโหลด ชนิด :type', NULL, 'ເລືອກຮູບພາບອັບໂຫຼດຊະນິດ :type'),
(135, 'Can be approve', 'text', 'index', 0, 'สามารถอนุมัติได้', NULL, 'ສາມາດອະນຸມັດ'),
(136, 'Can login', 'text', 'index', 0, 'สามารถเข้าระบบได้', NULL, 'ສາມາດເຂົ້າສູ່ລະບົບ'),
(137, 'Can not be performed this request. Because they do not find the information you need or you are not allowed', 'text', 'index', 0, 'ไม่สามารถดำเนินการตามที่ร้องขอได้ เนื่องจากไม่พบข้อมูลที่ต้องการ หรือ คุณไม่มีสิทธิ์', NULL, 'ບໍ່ສາມາດດຳເນີນການຕາມທີ່ຮ້ອງຂໍໄດ້ເນື່ອງຈາກບໍ່ພົບຂໍ້ມູນທີ່ຕ້ອງການ ຫຼື ທ່ານບໍ່ມີສິດ'),
(138, 'Can select multiple files', 'text', 'index', 0, 'สามารถเลือกได้หลายไฟล์', NULL, 'ສາມາດເລືອກເອກະສານໄດ້ຫຼາຍແບບ'),
(139, 'Can set the module', 'text', 'index', 0, 'สามารถตั้งค่าโมดูลได้', NULL, 'ສາມາດຕັ້ງໂມດູນໄດ້'),
(140, 'Can upload file', 'text', 'index', 0, 'สามารถอัปโหลดไฟล์ได้', NULL, 'ສາມາດອັບໂຫຼດແຟ້ມໄດ້'),
(141, 'Can view or download file', 'text', 'index', 0, 'สามารถดูหรือดาวน์โหลดเอกสารได้', NULL, 'ສາມາດເບິ່ງຫລືດາວໂຫລດເອກະສານໄດ້'),
(142, 'Can view the report', 'text', 'index', 0, 'สามารถดูรายงานได้', NULL, 'ທ່ານສາມາດເບິ່ງບົດລາຍງານ'),
(143, 'Can&#039;t login', 'text', 'index', 0, 'ไม่สามารถเข้าระบบได้', NULL, 'ບໍ່ສາມາດເຂົ້າສູ່ລະບົບໄດ້'),
(144, 'Cannot select this option', 'text', 'index', 0, 'ไม่สามารถเลือกตัวเลือกนี้ได้', NULL, 'ທາງເລືອກນີ້ບໍ່ສາມາດເລືອກໄດ້.'),
(145, 'Change language', 'text', 'index', 0, 'สลับภาษา', NULL, 'ປ່ຽນພາສາ'),
(146, 'Clear selected', 'text', 'index', 0, 'ไม่เลือก', NULL, 'ບໍ່ເລືອກ'),
(147, 'Click to edit', 'text', 'index', 0, 'คลิกเพื่อแก้ไข', NULL, 'ກົດເພື່ອແກ້ໄຂ'),
(148, 'Comment', 'text', 'index', 0, 'หมายเหตุ', NULL, 'ຫມາຍ​ເຫດ​'),
(149, 'Communication', 'text', 'index', 0, 'การติดต่อ', NULL, 'ຕິດຕໍ່'),
(150, 'Confirm password', 'text', 'index', 0, 'ยืนยันรหัสผ่าน', NULL, 'ຢືນຢັນລະຫັດຜ່ານ'),
(151, 'Congratulations, your email address has been verified. please login', 'text', 'index', 0, 'ยินดีด้วย ที่อยู่อีเมลของคุณได้รับการยืนยันเรียบร้อยแล้ว กรุณาเข้าสู่ระบบ', NULL, 'ຂໍສະແດງຄວາມຍິນດີ, ທີ່ຢູ່ອີເມວຂອງທ່ານໄດ້ຮັບການຢັ້ງຢືນແລ້ວ. ກະລຸນາເຂົ້າສູ່ລະບົບ'),
(152, 'Contact information during leave', 'text', 'index', 0, 'ข้อมูลการติดต่อระหว่างการลา', NULL, 'ຂໍ້ມູນຕິດຕໍ່ໃນເວລາພັກຜ່ອນ'),
(153, 'Cookie Policy', 'text', 'index', 0, 'นโยบายคุกกี้', NULL, 'ນະໂຍບາຍຄຸກກີ'),
(154, 'COOKIE_NECESSARY_DETAILS', 'text', 'index', 0, 'คุกกี้พื้นฐาน จำเป็นต่อการใช้งานเว็บไซต์ ใช้เพื่อการรักษาความปลอดภัยและให้เว็บไซต์สามารถทำงานได้อย่างถูกต้อง', NULL, 'ຄຸກກີພື້ນຖານ ມີຄວາມຈໍາເປັນໃນການນໍາໃຊ້ເວັບໄຊທ໌ ມັນຖືກນໍາໃຊ້ເພື່ອຈຸດປະສົງຄວາມປອດໄພແລະເພື່ອໃຫ້ເວັບໄຊທ໌ເຮັດວຽກຢ່າງຖືກຕ້ອງ.'),
(155, 'COOKIE_POLICY_DETAILS', 'text', 'index', 0, 'เราใช้คุกกี้ (Cookies) หรือเทคโนโลยีที่คล้ายคลึงกันเท่าที่จำเป็น เพื่อใช้ในการเข้าถึงสินค้าหรือบริการ และติดตามการใช้งานของคุณเท่านั้น หากคุณไม่ต้องการให้มีคุกกี้ไว้ในคอมพิวเตอร์ของคุณ คุณสามารถตั้งค่าบราวเซอร์เพื่อปฏิเสธการจัดเก็บคุกกี้ก่อนที่จะใช้งานเว็บไซต์ หรือใช้โหมดไม่ระบุตัวตนเพื่อเข้าใช้งานเว็บไซต์ก็ได้', NULL, 'ພວກເຮົາໃຊ້ຄຸກກີ (Cookies) ຫຼືເຕັກໂນໂລຢີທີ່ຄ້າຍຄືກັນໃນຂອບເຂດທີ່ຈໍາເປັນ. ສໍາລັບການນໍາໃຊ້ໃນການເຂົ້າເຖິງສິນຄ້າຫຼືການບໍລິການ ແລະພຽງແຕ່ຕິດຕາມການນໍາໃຊ້ຂອງທ່ານ ຖ້າ​ຫາກ​ວ່າ​ທ່ານ​ບໍ່​ຕ້ອງ​ການ cookies ໃນ​ຄອມ​ພິວ​ເຕີ​ຂອງ​ທ່ານ​ ທ່ານສາມາດຕັ້ງຕົວທ່ອງເວັບຂອງທ່ານເພື່ອປະຕິເສດການເກັບຮັກສາ cookies. ກ່ອນທີ່ຈະນໍາໃຊ້ເວັບໄຊທ໌ ທ່ານຍັງສາມາດໃຊ້ໂໝດບໍ່ເປີດເຜີຍຕົວຕົນເພື່ອເຂົ້າຫາເວັບໄຊທ໌ໄດ້.'),
(156, 'Country', 'text', 'index', 0, 'ประเทศ', NULL, 'ປະເທດ'),
(157, 'Create', 'text', 'index', 0, 'สร้าง', NULL, 'ສ້າງ'),
(158, 'Create new account', 'text', 'index', 0, 'สร้างบัญชีใหม่', NULL, 'ສ້າງບັນຊີໃໝ່'),
(159, 'Created', 'text', 'index', 0, 'สร้างเมื่อ', NULL, 'ສ້າງເມື່ອ'),
(160, 'Dark mode', 'text', 'index', 0, 'โหมดมืด', NULL, 'ໂໝດມືດ'),
(161, 'Data controller', 'text', 'index', 0, 'ผู้ควบคุม/ใช้ ข้อมูล', NULL, 'ຜູ້​ຄວບ​ຄຸມຂໍ້ມູນ'),
(162, 'Date', 'text', 'index', 0, 'วันที่', NULL, 'ວັນທີ'),
(163, 'Date of leave', 'text', 'index', 0, 'วันที่ลา', NULL, 'ວັນພັກ'),
(164, 'days', 'text', 'index', 0, 'วัน', NULL, 'ມື້'),
(165, 'Deduction', 'text', 'index', 0, 'หักอื่นๆ', NULL, 'ອົກຫັກ'),
(166, 'Delete', 'text', 'index', 0, 'ลบ', NULL, 'ລືບ'),
(167, 'Demo Mode', 'text', 'index', 0, 'โหมดตัวอย่าง', NULL, 'ຕົວຢ່າງ'),
(168, 'Department', 'text', 'index', 0, 'แผนก', NULL, 'ພະແນກ'),
(169, 'Description', 'text', 'index', 0, 'คำอธิบาย', NULL, 'ຄຳອະທິບາຍ'),
(170, 'Detail', 'text', 'index', 0, 'รายละเอียด', NULL, 'ລາຍະລະອຽດ'),
(171, 'Details of', 'text', 'index', 0, 'รายละเอียดของ', NULL, 'ລາຍລະອຽດຂອງ'),
(172, 'Didn&#039;t receive code?', 'text', 'index', 0, 'ไม่ได้รับโค้ด?', NULL, 'ບໍ່ໄດ້ຮັບລະຫັດບໍ?'),
(173, 'Document', 'text', 'index', 0, 'เอกสาร', NULL, 'ເອກະສານ'),
(174, 'Document No.', 'text', 'index', 0, 'เลขที่เอกสาร', NULL, 'ເລກທີເອກະສານ'),
(175, 'Document number', 'text', 'index', 0, 'เลขที่เอกสาร', NULL, 'ເລກທີເອກະສານ'),
(176, 'Download', 'text', 'index', 0, 'ดาวน์โหลด', NULL, 'ດາວໂຫຼດ'),
(177, 'Download history', 'text', 'index', 0, 'ประวัติการดาวน์โหลด', NULL, 'ປະຫວັດການດາວໂຫຼດ'),
(178, 'drag to order', 'text', 'index', 0, 'ลากเพื่อจัดลำดับ', NULL, 'ຫຼາກເພື່ອຈັດລຳດັບ'),
(179, 'E-Leave', 'text', 'index', 0, 'ระบบลางาน', NULL, 'ລະບົບລາພັກວຽກ'),
(180, 'Edit', 'text', 'index', 0, 'แก้ไข', NULL, 'ແກ້ໄຂ'),
(181, 'Editing your account', 'text', 'index', 0, 'แก้ไขข้อมูลส่วนตัว', NULL, 'ແກ້ໄຂຂໍ້ມູນສ່ວນຕົວສະມາຊິກ'),
(182, 'Email', 'text', 'index', 0, 'อีเมล', NULL, 'ອີເມວ'),
(183, 'Email address used for login or request a new password', 'text', 'index', 0, 'ที่อยู่อีเมล ใช้สำหรับการเข้าระบบหรือการขอรหัสผ่านใหม่', NULL, 'ທີ່ຢູ່ອີເມວໃຊ້ສຳລັບການເຂົ້າລະບົບຫຼືການຂໍລະຫັດໃໝ່'),
(184, 'Email address verification', 'text', 'index', 0, 'ยืนยันที่อยู่อีเมล', NULL, 'ຢືນຢັນທີ່ຢູ່ອີເມວ'),
(185, 'Email addresses for sender and do not reply such as no-reply@domain.tld', 'text', 'index', 0, 'ทีอยู่อีเมลใช้เป็นผู้ส่งจดหมาย สำหรับจดหมายที่ไม่ต้องการตอบกลับ เช่น no-reply@domain.tld', NULL, 'ທີ່ຢູ່ອີເມວໃຊ້ເປັນຜູ້ສົ່ງຈົດໝາຍ ສຳລັບຈົດໝາຍທີ່ບໍ່ຕ້ອງການຕອບກັບເຊັ່ນ no-reply@domain.tld'),
(186, 'Email encoding', 'text', 'index', 0, 'รหัสภาษาของจดหมาย', NULL, 'ລະຫັດພາສາຂອງຈົດໝາຍ'),
(187, 'Email settings', 'text', 'index', 0, 'ตั้งค่าอีเมล', NULL, 'ຕັ້ງຄ່າອີເມວ'),
(188, 'Email was not verified', 'text', 'index', 0, 'ยังไม่ได้ยืนยันอีเมล', NULL, 'ອີເມວບໍ່ໄດ້ຖືກຢືນຢັນ'),
(189, 'Emailing', 'text', 'index', 0, 'การส่งอีเมล', NULL, 'ສົ່ງອີເມວ'),
(190, 'Employee Count', 'text', 'index', 0, 'จำนวนพนักงาน', NULL, 'ຈຳນວນພະນັກງານ'),
(191, 'Enable SSL encryption for sending email', 'text', 'index', 0, 'เปิดใช้งานการเข้ารหัส SSL สำหรับการส่งอีเมล', NULL, 'ເປີດໃຊ້ການເຂົ້າລະຫັດ SSL ສຳລັບການສົ່ງອີເມວ'),
(192, 'End date', 'text', 'index', 0, 'วันที่สิ้นสุด', NULL, 'ວັນທີສິ້ນສຸດ'),
(193, 'End date must be greater than or equal to the start date', 'text', 'index', 0, 'วันที่สิ้นสุดต้องมากกว่าหรือเท่ากับวันที่เริ่มต้น', NULL, 'ວັນທີສິ້ນສຸດຕ້ອງຈະໃຫຍ່ກວ່າຫຼືເທົ່າກັບວັນທີເລີ່ມຕົ້ນ.'),
(194, 'English lowercase letters and numbers, not less than 6 digits', 'text', 'index', 0, 'ภาษาอังกฤษตัวพิมพ์เล็กและตัวเลข ไม่น้อยกว่า 6 หลัก', NULL, 'ໂຕພິມນ້ອຍພາສາອັງກິດ ແລະຕົວເລກ, ບໍ່ຕໍ່າກວ່າ 6 ຕົວເລກ'),
(195, 'Enter 0 if you want unlimited leave', 'text', 'index', 0, 'กรอก 0 ถ้าต้องการลาได้ไม่จำกัด', NULL, 'ໃສ່ເບີ 0 ຖ້າທ່ານຕ້ອງການການພັກຜ່ອນແບບບໍ່ຈຳກັດ'),
(196, 'Enter the 4-digit verification code that was sent to your phone number.', 'text', 'index', 0, 'ป้อนรหัสยืนยัน 4 หลักที่ส่งไปยังหมายเลขโทรศัพท์ของคุณ', NULL, 'ໃສ່ລະຫັດຢືນຢັນ 4 ຕົວເລກທີ່ສົ່ງໄປຫາເບີໂທລະສັບຂອງທ່ານ.'),
(197, 'Enter the domain name you want to allow or enter * for all domains. or leave it blank if you want to use it on this domain only', 'text', 'index', 0, 'กรอกชื่อโดเมนที่ต้องการอนุญาต หรือกรอก * สำหรับทุกโดเมน หรือเว้นว่างไว้ถ้าต้องการให้ใช้งานได้บนโดเมนนี้เท่านั้น', NULL, 'ໃສ່ຊື່ໂດເມນທີ່ທ່ານຕ້ອງການທີ່ຈະອະນຸຍາດໃຫ້ຫຼືໃສ່ * ສໍາລັບໂດເມນທັງຫມົດ. ຫຼືປ່ອຍໃຫ້ມັນຫວ່າງຖ້າທ່ານຕ້ອງການໃຊ້ມັນຢູ່ໃນໂດເມນນີ້ເທົ່ານັ້ນ'),
(198, 'Enter the email address registered. A new password will be sent to this email address.', 'text', 'index', 0, 'กรอกที่อยู่อีเมลที่ลงทะเบียนไว้ ระบบจะส่งรหัสผ่านใหม่ไปยังที่อยู่อีเมลนี้', NULL, 'ເພີ່ມທີ່ຢູ່ອີເມວທີ່ລົງທະບຽນໄວ້ ລະບົບຈະສົ່ງລະຫັດຜ່ານໃໝ່ໄປຍັງທີ່ຢູ່ອີເມວນີ້'),
(199, 'Enter the LINE user ID you received when adding friends. Or type userId sent to the official account to request a new user ID. This information is used for receiving private messages from the system via LINE.', 'text', 'index', 0, 'กรอก user ID ของไลน์ที่ได้ตอนเพิ่มเพื่อน หรือพิมพ์คำว่า userId ส่งไปยังบัญชีทางการเพื่อขอ user ID ใหม่ ข้อมูลนี้ใช้สำหรับการรับข้อความส่วนตัวที่มาจากระบบผ่านไลน์', NULL, 'ໃສ່ user ID ຂອງ LINE ທີ່ທ່ານໄດ້ຮັບໃນເວລາເພີ່ມເພື່ອນ. ຫຼືພິມ userId ທີ່ຖືກສົ່ງໄປຫາບັນຊີທາງການເພື່ອຮ້ອງຂໍ user ID ໃຫມ່. ຂໍ້ມູນນີ້ແມ່ນໃຊ້ສໍາລັບການຮັບຂໍ້ຄວາມສ່ວນຕົວຈາກລະບົບຜ່ານ LINE.'),
(200, 'Enter your password again', 'text', 'index', 0, 'กรอกรหัสผ่านของคุณอีกครั้ง', NULL, 'ໃສ່ລະຫັດຜ່ານຂອງທ່ານອີກຄັ້ງ'),
(201, 'Enter your registered username. A new password will be sent to this username.', 'text', 'index', 0, 'กรอกชื่อผู้ใช้ที่ลงทะเบียนไว้ ระบบจะส่งรหัสผ่านใหม่ไปยังชื่อผู้ใช้นี้', NULL, 'ໃສ່ຊື່ຜູ້ໃຊ້ທີ່ລົງທະບຽນຂອງທ່ານ. ລະຫັດຜ່ານໃໝ່ຈະຖືກສົ່ງໄປຫາຊື່ຜູ້ໃຊ້ນີ້'),
(202, 'entries', 'text', 'index', 0, 'รายการ', NULL, 'ລາຍການ'),
(203, 'Expiration date', 'text', 'index', 0, 'วันหมดอายุ', NULL, 'ວັນໝົດອາຍຸ'),
(204, 'Fax', 'text', 'index', 0, 'โทรสาร', NULL, 'ແຟັກ'),
(205, 'File', 'text', 'index', 0, 'ไฟล์', NULL, 'ແຟ້ມ'),
(206, 'File name', 'text', 'index', 0, 'ชื่อไฟล์', NULL, 'ຊື່ແຟ້ມຂໍ້ມູນ'),
(207, 'File not found', 'text', 'index', 0, 'ไม่พบไฟล์ที่ต้องการ', NULL, 'ບໍ່ພົບແຟ້ມທີ່ຕ້ອງການ'),
(208, 'File size is less than :size', 'text', 'index', 0, 'ขนาดของไฟล์ไม่เกิน :size', NULL, 'ຂະໜາດຂອງແຟ້ມບໍ່ເກີນ:size'),
(209, 'Fill in 0 to use the calculation of steps according to Thai law. Or the desired percentage (such as 3 for 3%)', 'text', 'index', 0, 'กรอก 0 เพื่อใช้การคำนวณแบบขั้นบันไดตามกฎหมายไทย หรือกรอกเปอร์เซ็นต์ที่ต้องการ (เช่น 3 สำหรับ 3%)', NULL, 'ຕື່ມ 0 ເພື່ອໃຊ້ການຄິດໄລ່ຂັ້ນຕອນຕາມກົດໝາຍໄທ. ຫຼືອັດຕາສ່ວນທີ່ຕ້ອງການ (ເຊັ່ນ: 3 ສໍາລັບ 3%)'),
(210, 'Fiscal year', 'text', 'index', 0, 'ปีงบประมาณ', NULL, 'ປີງົບປະມານ'),
(211, 'Footer', 'text', 'index', 0, 'ส่วนท้าย', NULL, 'ສ່ວນທ້າຍ'),
(212, 'for login by LINE account', 'text', 'index', 0, 'สำหรับการเข้าระบบโดยบัญชีไลน์', NULL, 'ສໍາລັບການເຂົ້າສູ່ລະບົບດ້ວຍບັນຊີ LINE'),
(213, 'Forgot', 'text', 'index', 0, 'ลืมรหัสผ่าน', NULL, 'ລືມລະຫັດຜ່ານ'),
(214, 'from', 'text', 'index', 0, 'จาก', NULL, 'ຈາກ'),
(215, 'General', 'text', 'index', 0, 'ทั่วไป', NULL, 'ທົ່ວໄປ'),
(216, 'General site settings', 'text', 'index', 0, 'ตั้งค่าพื้นฐานของเว็บไซต์', NULL, 'ຕັ້ງຄ່າພື້ນຖານຂອງເວັບໄຊ'),
(217, 'Get new password', 'text', 'index', 0, 'ขอรหัสผ่าน', NULL, 'ຂໍລະຫັດຜ່ານ'),
(218, 'go to page', 'text', 'index', 0, 'ไปหน้าที่', NULL, 'ໄປທີ່ໜ້າ'),
(219, 'Header', 'text', 'index', 0, 'ส่วนหัว', NULL, 'ສ່ວນຫົວ'),
(220, 'Height', 'text', 'index', 0, 'สูง', NULL, 'ສູງ'),
(221, 'Home', 'text', 'index', 0, 'หน้าหลัก', NULL, 'ໜ້າຫຼັກ'),
(222, 'How to define user authentication for mail servers. If you enable it, you must configure below correctly.', 'text', 'index', 0, 'กำหนดวิธีการตรวจสอบผู้ใช้สำหรับเมล์เซิร์ฟเวอร์ หากคุณเปิดใช้งานคุณต้องกำหนดค่าต่างๆด้านล่างถูกต้อง', NULL, 'ກຳນົດວິທີການກວດສອບຜູ້ໃຊ້ສຳລັບເມນເຊິບເວີຫາກທ່ານເປີດໃຊ້ການທ່ານຕ້ອງກຳນົດຄ່າຕ່າງໆດ້ານລຸ່ມຖືກຕ້ອງ'),
(223, 'Image', 'text', 'index', 0, 'รูปภาพ', NULL, 'ຮູບພາບ'),
(224, 'Image size is in pixels', 'text', 'index', 0, 'ขนาดของรูปภาพเป็นพิกเซล', NULL, 'ຂະໜາດຂອງຮູບພາບເປັນພິກເຊວ'),
(225, 'Import', 'text', 'index', 0, 'นำเข้า', NULL, 'ນຳເຂົ້າ'),
(226, 'Import Employee Data', 'text', 'index', 0, 'นำเข้าข้อมูลพนักงาน', NULL, 'ນຳເຂົ້າຂໍ້ມູນພະນັກງານ'),
(227, 'Import Salary Data', 'text', 'index', 0, 'นำเข้าข้อมูลเงินเดือน', NULL, 'ນໍາເຂົ້າຂໍ້ມູນເງິນເດືອນ'),
(228, 'Income Tax', 'text', 'index', 0, 'ภาษีเงินได้', NULL, 'ພາສີລາຍໄດ້'),
(229, 'Installed modules', 'text', 'index', 0, 'โมดูลที่ติดตั้งแล้ว', NULL, 'ໂມດູນທີ່ຕິດຕັ້ງ'),
(230, 'Invalid :name', 'text', 'index', 0, ':name ไม่ถูกต้อง', NULL, ':name ບໍ່ຖືກຕ້ອງ'),
(231, 'Key', 'text', 'index', 0, 'คีย์', NULL, 'ແປ້ນພີມ'),
(232, 'Language', 'text', 'index', 0, 'ภาษา', NULL, 'ພາສາ'),
(233, 'Leave', 'text', 'index', 0, 'ลา', NULL, 'ລາ'),
(234, 'Leave conditions', 'text', 'index', 0, 'เงื่อนไขการลา', NULL, 'ເງື່ອນໄຂການໃຫ້ລາ'),
(235, 'Leave empty for generate auto', 'text', 'index', 0, 'เว้นว่างไว้เพื่อสร้างโดยอัตโนมัติ', NULL, 'ປ່ອຍຫວ່າງໄວ້ເພື່ອສ້າງອັດໂນມັດ'),
(236, 'Leave history', 'text', 'index', 0, 'ประวัติการลา', NULL, 'ປະຫວັດສາດການພັກ'),
(237, 'Leave type', 'text', 'index', 0, 'ประเภทการลา', NULL, 'ປະເພດການພັກວຽກ'),
(238, 'LINE official account (with @ prefix, e.g. @xxxx)', 'text', 'index', 0, 'บัญชีทางการของไลน์ (มี @ นำหน้า เช่น @xxxx)', NULL, 'ບັນຊີທາງການຂອງ LINE (ມີ @ ຄໍານໍາຫນ້າ, ເຊັ່ນ: @xxxx)'),
(239, 'LINE settings', 'text', 'index', 0, 'ตั้งค่าไลน์', NULL, 'ຕັ້ງ​ຄ່າ LINE'),
(240, 'List of', 'text', 'index', 0, 'รายการ', NULL, 'ລາຍການ'),
(241, 'List of IPs that allow connection 1 line per 1 IP', 'text', 'index', 0, 'รายการไอพีแอดเดรสทั้งหมดที่อนุญาต 1 บรรทัดต่อ 1 ไอพี', NULL, 'ລາຍຊື່ IP ທີ່ອະນຸຍາດໃຫ້ເຊື່ອມຕໍ່ 1 ເສັ້ນຕໍ່ 1 IP'),
(242, 'Local time', 'text', 'index', 0, 'เวลาท้องถิ่น', NULL, 'ເວລາທ້ອງຖິ່ນ'),
(243, 'Log in to Telegram to request an ID', 'text', 'index', 0, 'เข้าระบบ Telegram เพื่อขอ ID', NULL, 'ເຂົ້າສູ່ລະບົບ Telegram ເພື່ອຮ້ອງຂໍ ID.'),
(244, 'Login', 'text', 'index', 0, 'เข้าสู่ระบบ', NULL, 'ເຂົ້າສູ່ລະບົບ'),
(245, 'Login as', 'text', 'index', 0, 'เข้าระบบเป็น', NULL, 'ເຂົ້າ​ສູ່​ລະ​ບົບ​ເປັນ'),
(246, 'Login by', 'text', 'index', 0, 'เข้าระบบโดย', NULL, 'ເຂົ້າສູ່ລະບົບໂດຍ'),
(247, 'Login information', 'text', 'index', 0, 'ข้อมูลการเข้าระบบ', NULL, 'ຂໍ້ມູນການເຂົ້າລະບົບ'),
(248, 'Login page', 'text', 'index', 0, 'หน้าเข้าสู่ระบบ', NULL, 'ໜ້າເຂົ້າສູ່ລະບົບ'),
(249, 'Login with an existing account', 'text', 'index', 0, 'เข้าระบบด้วยบัญชีสมาชิกที่มีอยู่แล้ว', NULL, 'ເຂົ້າລະບົບດ້ວຍບັນຊີສະມາຊິກທີ່ມີຢູ່ແລ້ວ'),
(250, 'Logo', 'text', 'index', 0, 'โลโก', NULL, 'ໂລໂກ'),
(251, 'Logout', 'text', 'index', 0, 'ออกจากระบบ', NULL, 'ອອກຈາກລະບົບ'),
(252, 'Logout successful', 'text', 'index', 0, 'ออกจากระบบเรียบร้อย', NULL, 'ອອກຈາກລະບົບຮຽບຮ້ອຍ'),
(253, 'Mail program', 'text', 'index', 0, 'โปรแกรมส่งอีเมล', NULL, 'ໂປຮແກຮມສົ່ງອີເມວ'),
(254, 'Mail server', 'text', 'index', 0, 'เซิร์ฟเวอร์อีเมล', NULL, 'ເຊີບເວີອີເມວ'),
(255, 'Mail server port number (default is 25, for GMail used 465, 587 for DirectAdmin).', 'text', 'index', 0, 'หมายเลขพอร์ตของเมล์เซิร์ฟเวอร์ (ค่าปกติคือ 25, สำหรับ gmail ใช้ 465, 587 สำหรับ DirectAdmin)', NULL, 'ໝາຍເລກພອດຂອງເມວເຊີບເວີ(ຄ່າປົກກະຕິຄື 25, ສຳລັບ gmail ໃຊ້ 465, 587 ສຳລັບ DirectAdmin)'),
(256, 'Mail server settings', 'text', 'index', 0, 'ค่ากำหนดของเมล์เซิร์ฟเวอร์', NULL, 'ຄ່າກຳນົດຂອງເມວເຊີບເວີ'),
(257, 'Manage languages', 'text', 'index', 0, 'จัดการภาษา', NULL, 'ຈັດການພາສາ'),
(258, 'Member list', 'text', 'index', 0, 'รายชื่อสมาชิก', NULL, 'ລາຍຊື່ສະມາຊິກ'),
(259, 'Member status', 'text', 'index', 0, 'สถานะสมาชิก', NULL, 'ສະຖານະສະມາຊິກ'),
(260, 'Membership has not been confirmed yet.', 'text', 'index', 0, 'ยังไม่ได้ยืนยันสมาชิก', NULL, 'ສະມາຊິກຍັງບໍ່ທັນໄດ້ຮັບການຢືນຢັນ'),
(261, 'Message', 'text', 'index', 0, 'ข้อความ', NULL, 'ຂໍ້ຄວາມ'),
(262, 'Message displayed on login page', 'text', 'index', 0, 'ข้อความแสดงในหน้าเข้าสู่ระบบ', NULL, 'ຂໍ້ຄວາມສະແດງຢູ່ໃນຫນ້າເຂົ້າສູ່ລະບົບ'),
(263, 'Mobile Phone Verification', 'text', 'index', 0, 'ยืนยันหมายเลขโทรศัพท์', NULL, 'ຢືນຢັນເບີໂທລະສັບ'),
(264, 'Module', 'text', 'index', 0, 'โมดูล', NULL, 'ໂມດູນ'),
(265, 'Module settings', 'text', 'index', 0, 'ตั้งค่าโมดูล', NULL, 'ຕັ້ງຄ່າໂມດູນ'),
(266, 'Month', 'text', 'index', 0, 'เดือน', NULL, 'ເດືອນ'),
(267, 'Monthly Comparison', 'text', 'index', 0, 'เปรียบเทียบรายเดือน', NULL, 'ປຽບທຽບເດືອນ'),
(268, 'Monthly salary :month :year', 'text', 'index', 0, 'เงินเดือนประจำเดือน :month พ.ศ. :year', NULL, 'ເງິນເດືອນ:month :year'),
(269, 'My leave', 'text', 'index', 0, 'ใบลาของฉัน', NULL, 'ການລາພັກຂອງຂ້ອຍ'),
(270, 'My Slip', 'text', 'index', 0, 'เงินเดือนของฉัน', NULL, 'ເງິນເດືອນຂອງຂ້ອຍ'),
(271, 'Necessary cookies', 'text', 'index', 0, 'คุกกี้พื้นฐานที่จำเป็น', NULL, 'ຄຸກກີພື້ນຖານທີ່ຈໍາເປັນ'),
(272, 'Net Salary', 'text', 'index', 0, 'เงินเดือนสุทธิ', NULL, 'ເງິນເດືອນສຸດທິ'),
(273, 'New', 'text', 'index', 0, 'ใหม่', NULL, 'ໃໝ່'),
(274, 'New members', 'text', 'index', 0, 'สมาชิกใหม่', NULL, 'ສະມາຊິກໃໝ່'),
(275, 'No data available in this table.', 'text', 'index', 0, 'ไม่มีข้อมูลในตารางนี้', NULL, 'ບໍ່ມີຂໍ້ມູນໃນຕາຕະລາງນີ້'),
(276, 'no larger than :size', 'text', 'index', 0, 'ขนาดไม่เกิน :size', NULL, 'ຂະໜາດບໍ່ເກີນ :size'),
(277, 'No need to fill in English text. If the English text matches the Key', 'text', 'index', 0, 'ไม่จำเป็นต้องกรอกข้อความในภาษาอังกฤษ หากข้อความในภาษาอังกฤษตรงกับคีย์', NULL, 'ບໍ່ຈຳເປັນເພີ່ມຂໍ້ຄວາມໃນພາສາອັງກິດຫາກຂໍ້ຄວາມໃນພາສານອັງກົງກັບແປ້ນພີມ'),
(278, 'not a registered user', 'text', 'index', 0, 'ไม่พบสมาชิกนี้ลงทะเบียนไว้', NULL, 'ບໍ່ພົບສະມາຊິກນີ້ລົງທະບຽນໄວ້'),
(279, 'Not specified', 'text', 'index', 0, 'ไม่ระบุ', NULL, 'ບໍ່ໄດ້ກໍານົດ'),
(280, 'Number of leave days', 'text', 'index', 0, 'จำนวนวันลา', NULL, 'ຈຳນວນວັນຢຸດ'),
(281, 'Order', 'text', 'index', 0, 'ลำดับ', NULL, 'ລຳດັບ'),
(282, 'Order of persons in positions', 'text', 'index', 0, 'ลำดับบุคคลในตำแหน่ง', NULL, 'ລຳດັບບຸກຄົນໃນຕຳແໜ່ງ'),
(283, 'Other', 'text', 'index', 0, 'อื่นๆ', NULL, 'ອື່ນໆ'),
(284, 'OTP is invalid or expired. Please request a new OTP.', 'text', 'index', 0, 'OTP ไม่ถูกต้องหรือหมดอายุ กรุณาขอ OTP ใหม่', NULL, 'OTP ບໍ່ຖືກຕ້ອງ ຫຼືໝົດອາຍຸ ກະລຸນາຮ້ອງຂໍ OTP ໃໝ່.'),
(285, 'Overtime', 'text', 'index', 0, 'ค่าล่วงเวลา', NULL, 'ເງິນລ່ວງເວລາ'),
(286, 'Page details', 'text', 'index', 0, 'รายละเอียดของหน้า', NULL, 'ລາຍລະອຽດຂອງໜ້າ'),
(287, 'Password', 'text', 'index', 0, 'รหัสผ่าน', NULL, 'ລະຫັດຜ່ານ'),
(288, 'Password of the mail server. (To change the fill.)', 'text', 'index', 0, 'รหัสผ่านของเมล์เซิร์ฟเวอร์ (ต้องการเปลี่ยน ให้กรอก)', NULL, 'ລະຫັດຜ່ານຂອງເມວເຊີບເວີ (ຕ້ອງການປ່ຽນ ໃຫ້ເພີ່ມ)'),
(289, 'Passwords must be at least four characters', 'text', 'index', 0, 'รหัสผ่านต้องไม่น้อยกว่า 4 ตัวอักษร', NULL, 'ລະຫັດຜ່ານຕ້ອງບໍ່ຕ່ຳກວ່າ 4 ຕົວອັກສອນ'),
(290, 'Permission', 'text', 'index', 0, 'สิทธิ์การใช้งาน', NULL, 'ສິດໃນການໃຊ້ວຽກ'),
(291, 'Phone', 'text', 'index', 0, 'โทรศัพท์', NULL, 'ເບີໂທລະສັບ'),
(292, 'Please check the new member registration.', 'text', 'index', 0, 'กรุณาตรวจสอบการลงทะเบียนสมาชิกใหม่', NULL, 'ກະລຸນາກວດສອບການລົງທະບຽນສະມາຊິກໃໝ່.'),
(293, 'Please click the link to verify your email address.', 'text', 'index', 0, 'กรุณาคลิกลิงค์เพื่อยืนยันที่อยู่อีเมล', NULL, 'ກະລຸນາຄລິກທີ່ລິ້ງເພື່ອຢືນຢັນທີ່ຢູ່ອີເມວຂອງທ່ານ'),
(294, 'Please fill in', 'text', 'index', 0, 'กรุณากรอก', NULL, 'ກະລຸນາຕື່ມຂໍ້ມູນໃສ່'),
(295, 'Please fill up this form', 'text', 'index', 0, 'กรุณากรอกแบบฟอร์มนี้', NULL, 'ກະລຸນາຕື່ມແບບຟອມນີ້'),
(296, 'Please login', 'text', 'index', 0, 'กรุณาเข้าระบบ', NULL, 'ກະລຸນາເຂົ້າສູ່ລະບົບ'),
(297, 'Please select', 'text', 'index', 0, 'กรุณาเลือก', NULL, 'ກະລຸນາເລືອກ'),
(298, 'Please select :name at least one item', 'text', 'index', 0, 'กรุณาเลือก :name อย่างน้อย 1 รายการ', NULL, 'ກະລຸນາເລືອກ :name ຢ່າງໜ້ອຍ 1 ລາຍການ'),
(299, 'Port', 'text', 'index', 0, 'พอร์ต', NULL, 'ພອດ'),
(300, 'Position', 'text', 'index', 0, 'ตำแหน่ง', NULL, 'ຕໍາ​ແຫນ່ງ​'),
(301, 'Privacy Policy', 'text', 'index', 0, 'นโยบายความเป็นส่วนตัว', NULL, 'ນະໂຍບາຍຄວາມເປັນສ່ວນຕົວ'),
(302, 'Profile', 'text', 'index', 0, 'ข้อมูลส่วนตัว', NULL, 'ຂໍ້ມູນສ່ວນຕົວ'),
(303, 'Province', 'text', 'index', 0, 'จังหวัด', NULL, 'ແຂວງ'),
(304, 'Re-Calculate', 'text', 'index', 0, 'คำนวณใหม่', NULL, 'ຄິດໄລ່ຄືນ'),
(305, 'Reason', 'text', 'index', 0, 'เหตุผล', NULL, 'ເຫດຜົນ'),
(306, 'Reasons for leave', 'text', 'index', 0, 'เหตุผลการลา', NULL, 'ເຫດຜົນຂອງການພັກ'),
(307, 'Register', 'text', 'index', 0, 'สมัครสมาชิก', NULL, 'ສະໝັກສະມາຊິກ'),
(308, 'Register successfully Please log in', 'text', 'index', 0, 'ลงทะเบียนเรียบร้อยแล้วกรุณาเข้าสู่ระบบ', NULL, 'ລົງທະບຽນຢ່າງສຳເລັດຜົນກະລຸນາເຂົ້າສູ່ລະບົບ'),
(309, 'Register successfully, We have sent complete registration information to :email', 'text', 'index', 0, 'ลงทะเบียนสมาชิกใหม่เรียบร้อย เราได้ส่งข้อมูลการลงทะเบียนไปยัง :email', NULL, 'ລົງທະບຽນສຳເລັດແລ້ວ ເຮົາໄດ້ສົ່ງຂໍ້ມູນການລົງທະບຽນໄປທີ່ :email'),
(310, 'Registered successfully Please check your email :email and click the link to verify your email.', 'text', 'index', 0, 'ลงทะเบียนเรียบร้อย กรุณาตรวจสอบอีเมล์ :email และคลิงลิงค์ยืนยันอีเมล', NULL, 'ລົງທະບຽນສົບຜົນສໍາເລັດ ກະ​ລຸ​ນາ​ກວດ​ສອບ​ອີ​ເມວ​ຂອງ​ທ່ານ :email ແລະ​ຄລິກ​ໃສ່​ການ​ເຊື່ອມ​ຕໍ່​ເພື່ອ​ກວດ​ສອບ​ອີ​ເມວ​ຂອງ​ທ່ານ​.'),
(311, 'Remain', 'text', 'index', 0, 'คงเหลือ', NULL, 'ດຸ່ນດ່ຽງ'),
(312, 'Remark', 'text', 'index', 0, 'หมายเหตุ', NULL, 'ຂໍ້ສັງເກດ'),
(313, 'Remember me', 'text', 'index', 0, 'จำการเข้าระบบ', NULL, 'ຈົດຈຳການເຂົ້າລະບົບ'),
(314, 'Remove', 'text', 'index', 0, 'ลบ', NULL, 'ລຶບ'),
(315, 'Report', 'text', 'index', 0, 'รายงาน', NULL, 'ລາຍງານ'),
(316, 'Request for leave', 'text', 'index', 0, 'คำขออนุมัติลา', NULL, 'ຮ້ອງຂໍລາພັກ'),
(317, 'Resend', 'text', 'index', 0, 'ส่งใหม่', NULL, 'ສົ່ງຄືນ'),
(318, 'resized automatically', 'text', 'index', 0, 'ปรับขนาดอัตโนมัติ', NULL, 'ປັບຂະໜາດອັດຕະໂນມັດ'),
(319, 'Salary', 'text', 'index', 0, 'เงินเดือน', NULL, 'ເງິນເດືອນ'),
(320, 'Salary by Department', 'text', 'index', 0, 'เงินเดือนเฉลี่ยตามแผนก', NULL, 'ເງິນເດືອນຕາມພະແນກ'),
(321, 'Salary Distribution', 'text', 'index', 0, 'การกระจายเงินเดือน', NULL, 'ການກະຈາຍເງິນເດືອນ'),
(322, 'Salary information', 'text', 'index', 0, 'ข้อมูลเงินเดือน', NULL, 'ຂໍ້ມູນເງິນເດືອນ'),
(323, 'Salary Report', 'text', 'index', 0, 'รายงานเงินเดือน', NULL, 'ບົດລາຍງານເງິນເດືອນ'),
(324, 'Salary slip', 'text', 'index', 0, 'สลิปเงินเดือน', NULL, 'ໃບປະກາດເງິນເດືອນ'),
(325, 'Salary Statistics', 'text', 'index', 0, 'สถิติเงินเดือน', NULL, 'ສະຖິຕິເງິນເດືອນ'),
(326, 'Salary Trend', 'text', 'index', 0, 'แนวโน้มเงินเดือน', NULL, 'ທ່າອ່ຽງເງິນເດືອນ'),
(327, 'Save', 'text', 'index', 0, 'บันทึก', NULL, 'ບັນທຶກ'),
(328, 'Save and email completed', 'text', 'index', 0, 'บันทึกและส่งอีเมลเรียบร้อย', NULL, 'ບັນທຶກແລະສົ່ງອີເມວຮຽນຮ້ອຍ'),
(329, 'Saved successfully', 'text', 'index', 0, 'บันทึกเรียบร้อย', NULL, 'ບັນທຶກຮຽບຮ້ອຍ'),
(330, 'scroll to top', 'text', 'index', 0, 'เลื่อนขึ้นด้านบน', NULL, 'ເລື່ອນຂື້ນດ້ານເທິງ'),
(331, 'Search', 'text', 'index', 0, 'ค้นหา', NULL, 'ຄົ້ນຫາ'),
(332, 'Search <strong>:search</strong> found :count entries, displayed :start to :end, page :page of :total pages', 'text', 'index', 0, 'ค้นหา <strong>:search</strong> พบ :count รายการ แสดงรายการที่ :start - :end หน้าที่ :page จากทั้งหมด :total หน้า', NULL, 'ຄົ້ນຫາ <strong>:search</strong> ພົບ :count ລາຍການ ສະແດງລາຍການທີ່:start - :end ໜ້າທີ່:page ຈາກທັງໝົດ :total ໜ້າ'),
(333, 'select all', 'text', 'index', 0, 'เลือกทั้งหมด', NULL, 'ເລືອກທັງໝົດ'),
(334, 'Send a new password request', 'text', 'index', 0, 'ส่งคำขอ ขอรหัสผ่านใหม่', NULL, 'ສົ່ງຄຳຮ້ອງຂໍລະຫັດຜ່ານໃໝ່'),
(335, 'Send a welcome email to new members', 'text', 'index', 0, 'ส่งข้อความต้อนรับสมาชิกใหม่', NULL, 'ສົ່ງອີເມວຕ້ອນຮັບກັບສະມາຊິກໃຫມ່'),
(336, 'Send again in', 'text', 'index', 0, 'ส่งใหม่ในอีก', NULL, 'ສົ່ງຄືນໃນເວລາອື່ນ'),
(337, 'Send Email', 'text', 'index', 0, 'ส่งอีเมล', NULL, 'ສົ່ງອີເມວ'),
(338, 'Send login approval notification', 'text', 'index', 0, 'ส่งแจ้งเตือนอนุมัติการเข้าระบบ', NULL, 'ສົ່ງການແຈ້ງເຕືອນການອະນຸມັດການເຂົ້າສູ່ລະບົບ'),
(339, 'Send login authorization email', 'text', 'index', 0, 'ส่งอีเมลอนุมัติการเข้าระบบ', NULL, 'ສົ່ງອີເມວການອະນຸຍາດເຂົ້າສູ່ລະບົບ'),
(340, 'Send member confirmation email', 'text', 'index', 0, 'ส่งอีเมลยืนยันสมาชิก', NULL, 'ສົ່ງອີເມລ໌ຢືນຢັນສະມາຊິກ'),
(341, 'send message to user When a user adds LINE&#039;s official account as a friend', 'text', 'index', 0, 'ส่งข้อความไปยังผู้ใช้ เมื่อผู้ใช้เพิ่มบัญชีทางการของไลน์เป็นเพื่อน', NULL, 'ສົ່ງຂໍ້ຄວາມຫາຜູ້ໃຊ້ ເມື່ອຜູ້ໃຊ້ເພີ່ມບັນຊີທາງການຂອງ LINE ເປັນໝູ່'),
(342, 'Send notification messages When making a transaction', 'text', 'index', 0, 'ส่งข้อความแจ้งเตือนเมื่อมีการทำรายการ', NULL, 'ສົ່ງຂໍ້ຄວາມແຈ້ງເຕືອນເມື່ອມີການເຮັດທຸລະກຳ'),
(343, 'Sender Name', 'text', 'index', 0, 'ผู้ส่ง', NULL, 'ຊື່ຜູ້ສົ່ງ'),
(344, 'Server time', 'text', 'index', 0, 'เวลาเซิร์ฟเวอร์', NULL, 'ເວລາຂອງເຊີບເວີ'),
(345, 'Set the application for send an email', 'text', 'index', 0, 'เลือกโปรแกรมที่ใช้ในการส่งอีเมล', NULL, 'ເລືອກໂປຮແກຮມທີ່ໃຊ້ໃນການສົ່ງອີເມວ'),
(346, 'Set the start date of the fiscal year, the 1st day of the selected month', 'text', 'index', 0, 'กำหนดวันที่เริ่มต้นของปีงบประมาณ วันที่ 1 ของเดือนที่เลือก', NULL, 'ກໍານົດວັນທີເລີ່ມຕົ້ນຂອງປີງົບປະມານ, ວັນທີ 1 ຂອງເດືອນທີ່ເລືອກ'),
(347, 'Setting up the email system', 'text', 'index', 0, 'การตั้งค่าเกี่ยวกับระบบอีเมล', NULL, 'ການຕັ້ງຄ່າກ່ຽວກັບລະບົບອີເມວ'),
(348, 'Settings', 'text', 'index', 0, 'ตั้งค่า', NULL, 'ຕັ້ງຄ່າ'),
(349, 'Settings the conditions for member login', 'text', 'index', 0, 'ตั้งค่าเงื่อนไขในการตรวจสอบการเข้าสู่ระบบ', NULL, 'ຕັ້ງເງື່ອນໄຂການກວດສອບການເຂົ້າລະບົບ'),
(350, 'Settings the timing of the server to match the local time', 'text', 'index', 0, 'ตั้งค่าเขตเวลาของเซิร์ฟเวอร์ ให้ตรงกันกับเวลาท้องถิ่น', NULL, 'ຕັ້ງຄ່າເຂດເວລາຂອງເຊີບເວີ ໃຫ້ກົງກັບເວລາທ້ອງຖີ່ນ'),
(351, 'Sex', 'text', 'index', 0, 'เพศ', NULL, 'ເພດ'),
(352, 'Short description about your website', 'text', 'index', 0, 'ข้อความสั้นๆอธิบายว่าเป็นเว็บไซต์เกี่ยวกับอะไร', NULL, 'ຂໍ້ຄວາມສັ້ນໆ ອະທິບາຍວ່າເປັນເວັບໄຊກ່ຽວກັບຫຍັງ'),
(353, 'Show', 'text', 'index', 0, 'แสดง', NULL, 'ສະແດງ'),
(354, 'Show web title with logo', 'text', 'index', 0, 'แสดงชื่อเว็บและโลโก', NULL, 'ສະແດງຊື່ເວັບແລະໂລໂກ້'),
(355, 'showing page', 'text', 'index', 0, 'กำลังแสดงหน้าที่', NULL, 'ສະແດງໜ້າທີ່'),
(356, 'Site Name', 'text', 'index', 0, 'ชื่อของเว็บไซต์', NULL, 'ຊື່ຂອງເວັບໄຊ'),
(357, 'Site settings', 'text', 'index', 0, 'ตั้งค่าเว็บไซต์', NULL, 'ຕັ້ງຄ່າເວັບໄຊ'),
(358, 'size :width*:height pixel', 'text', 'index', 0, 'ขนาด :width*:height พิกเซล', NULL, 'ຂະໜາດ :width*:height ຟິດເຊວล'),
(359, 'Size of the file upload', 'text', 'index', 0, 'ขนาดของไฟล์อัปโหลด', NULL, 'ຂະໜາດຂອງແຟ້ມອັບໂຫຼດ'),
(360, 'skip to content', 'text', 'index', 0, 'ข้ามไปยังเนื้อหา', NULL, 'ຂ້າມໄປຍັງເນື້ອຫາ'),
(361, 'SMS Settings', 'text', 'index', 0, 'ตั้งค่า SMS', NULL, 'ຕັ້ງຄ່າ SMS'),
(362, 'Social Security', 'text', 'index', 0, 'ประกันสังคม', NULL, 'ປະກັນສັງຄົມ'),
(363, 'Sorry', 'text', 'index', 0, 'ขออภัย', NULL, 'ຂໍໂທດ'),
(364, 'Sorry, cannot find a page called Please check the URL or try the call again.', 'text', 'index', 0, 'ขออภัย ไม่พบหน้าที่เรียก โปรดตรวจสอบ URL หรือลองเรียกอีกครั้ง', NULL, 'ຂໍ​ອະ​ໄພ, ບໍ່​ສາ​ມາດ​ຊອກ​ຫາ​ຫນ້າ​ທີ່​ຮ້ອງ​ຂໍ. ກະ​ລຸ​ນາ​ກວດ​ສອບ URL ຫຼື​ພະ​ຍາ​ຍາມ​ດຶງ​ຂໍ້​ມູນ​ອີກ​ເທື່ອ​ຫນຶ່ງ.'),
(365, 'Sorry, Item not found It&#39;s may be deleted', 'text', 'index', 0, 'ขออภัย ไม่พบรายการที่เลือก รายการนี้อาจถูกลบไปแล้ว', NULL, 'ຂໍໂທດ ບໍ່ພົບລາຍການທີ່ເລືອກ ລາຍການນີ້ອາດຖືກລຶບໄປແລ້ວ'),
(366, 'Specify the file extension that allows uploading. English lowercase letters and numbers 2-4 characters to separate each type with a comma (,) and without spaces. eg zip,rar,doc,docx', 'text', 'index', 0, 'ระบุนามสกุลของไฟล์ที่สามารถอัปโหลดได้ ภาษาอังกฤษตัวพิมพ์เล็กและตัวเลขสองถึงสี่ตัวอักษร คั่นแต่ละรายการด้วยลูกน้ำ (,)', NULL, 'ລະບົບນາມສະກຸນຂອງແຟ້ມທີ່ສາມາດອັບໂຫຼດໄດ້ ພາສາອັງກິດຕົວພີມນ້ອຍແລະຕົວເລກສອງເຖິງສີ່ຕົວອັກສອນ ຄັ່ນແຕ່ລະລາຍການດ້ວຍເຄື່ອງໝາຍຈຸດ(,)'),
(367, 'Specify the format of the document number as %04d means adding zeros until the four-digit number on the front, such as 0001.', 'text', 'index', 0, 'กำหนดรูปแบบเลขที่ของเอกสาร เช่น %04d หมายถึง เติมเลขศูนย์ด้านหน้าจนครบสี่หลัก เช่น 0001', NULL, 'ກຳນົດຮູບແບບເລກທີຂອງເອກະສານເຊັ່ນ %04d ໝາຍເຖິງຕື່ມເລກສູນດ້ານໜ້າຈົນຄົບສີ່ຫຼັກເຊັ່ນ 0001'),
(368, 'Specify the language code of the email, as utf-8', 'text', 'index', 0, 'ระบุรหัสภาษาของอีเมลที่ส่ง เช่น utf-8', NULL, 'ລະບຸລະຫັດພາສາຂອງອີເມວທີ່ສົ່ງເຊັ່ນ utf-8'),
(369, 'Start a new line with the web name', 'text', 'index', 0, 'ขึ้นบรรทัดใหม่ชื่อเว็บ', NULL, 'ເລີ່ມແຖວໃໝ່ຊື່ເວັບ'),
(370, 'Start date', 'text', 'index', 0, 'วันที่เริ่มต้น', NULL, 'ວັນທີເລີ່ມຕົ້ນ'),
(371, 'Statistics', 'text', 'index', 0, 'สถิติ', NULL, 'ສະຖິຕິ'),
(372, 'Statistics for leave', 'text', 'index', 0, 'สถิติการลา', NULL, 'ສະຖິຕິການລາພັກ'),
(373, 'Status', 'text', 'index', 0, 'สถานะ', NULL, 'ສະຖານະ'),
(374, 'Status for general members', 'text', 'index', 0, 'สถานะสำหรับสมาชิกทั่วไป', NULL, 'ສະຖານະສຳລັບສະມາຊິກທົ່ວໄປ'),
(375, 'Status update', 'text', 'index', 0, 'ปรับปรุงสถานะ', NULL, 'ການ​ປັບ​ປຸງ​ສະ​ຖາ​ນະ​'),
(376, 'Style', 'text', 'index', 0, 'รูปแบบ', NULL, 'ຮູບແບບ'),
(377, 'Telegram settings', 'text', 'index', 0, 'ตั้งค่า Telegram', NULL, 'ຕັ້ງຄ່າ Telegram'),
(378, 'Text color', 'text', 'index', 0, 'สีตัวอักษร', NULL, 'ສີຕົວອັກສອນ'),
(379, 'The e-mail address of the person or entity that has the authority to make decisions about the collection, use or dissemination of personal data.', 'text', 'index', 0, 'ที่อยู่อีเมลของบุคคลหรือนิติบุคคลที่มีอำนาจตัดสินใจเกี่ยวกับการเก็บรวบรวม ใช้ หรือเผยแพร่ข้อมูลส่วนบุคคล', NULL, 'ທີ່ຢູ່ອີເມລ໌ຂອງບຸກຄົນຫຼືຫນ່ວຍງານທີ່ມີອໍານາດໃນການຕັດສິນໃຈກ່ຽວກັບການລວບລວມ, ການນໍາໃຊ້ຫຼືການເຜີຍແຜ່ຂໍ້ມູນສ່ວນບຸກຄົນ.'),
(380, 'The file size larger than the limit', 'text', 'index', 0, 'ขนาดของไฟล์ใหญ่กว่าที่กำหนด', NULL, 'ຂະຫນາດຂອງໄຟໃຫຍ່ກວ່າທີ່ກໍາຫນົດ'),
(381, 'The members status of the site', 'text', 'index', 0, 'สถานะของสมาชิกของเว็บไซต์', NULL, 'ສະຖານະຂອງສະມາຂິກຂອງເວັບໄຊ'),
(382, 'The message has been sent to the admin successfully. Please wait a moment for the admin to approve the registration. You can log back in later if approved.', 'text', 'index', 0, 'ส่งข้อความไปยังผู้ดูแลระบบเรียบร้อยแล้ว กรุณารอสักครู่เพื่อให้ผู้ดูแลระบบอนุมัติการลงทะเบียน คุณสามารถกลับมาเข้าระบบได้ในภายหลังหากได้รับการอนุมัติแล้ว', NULL, 'ຂໍ້ຄວາມດັ່ງກ່າວໄດ້ຖືກສົ່ງໄປຫາຜູ້ເບິ່ງແຍງຢ່າງສໍາເລັດຜົນ. ກະລຸນາລໍຖ້າໃຫ້ຜູ້ເບິ່ງແຍງລະບົບອະນຸມັດການລົງທະບຽນ. ທ່ານສາມາດເຂົ້າສູ່ລະບົບຄືນໄດ້ໃນພາຍຫຼັງຖ້າໄດ້ຮັບການອະນຸມັດ.'),
(383, 'The name of the mail server as localhost or smtp.gmail.com (To change the settings of your email is the default. To remove this box entirely.)', 'text', 'index', 0, 'ชื่อของเมล์เซิร์ฟเวอร์ เช่น localhost หรือ smtp.gmail.com (ต้องการเปลี่ยนค่ากำหนดของอีเมลทั้งหมดเป็นค่าเริ่มต้น ให้ลบข้อความในช่องนี้ออกทั้งหมด)', NULL, 'ຊື່ຂອງເມວເຊີບເວີເຊັ່ນ localhost หรือ chitdpt@gmail.com (ຕ້ອງປ່ຽນຄ່າກຳນົດຂອງອີເມວທັງໝົດເປັນຄ່າເລີ່ມຕົ້ນ ໃຫ້ລຶບຂໍ້ຄວາມໃນຊ່ອງນີ້ອອກທັງໝົດ)');
INSERT INTO `app_language` (`id`, `key`, `type`, `owner`, `js`, `th`, `en`, `la`) VALUES
(384, 'The size of the files can be uploaded. (Should not exceed the value of the Server :upload_max_filesize.)', 'text', 'index', 0, 'ขนาดของไฟล์ที่สามารถอัปโหลดได้ (ไม่ควรเกินค่ากำหนดของเซิร์ฟเวอร์ :upload_max_filesize)', NULL, 'ຂະໜາດຂອງແຟ້ມທີ່ສາມາດອັບໂຫຼດໄດ້(ບໍ່ຄວນເກີນຄ່າກຳນົດຂອງເຊີບເວີ :upload_max_filesize)'),
(385, 'The system has sent a new OTP code to the phone number you have registered. Please check the SMS and enter the code to confirm the phone number.', 'text', 'index', 0, 'ระบบได้ส่งรหัส OTP ใหม่ไปยังหมายเลขโทรศัพท์ที่ท่านได้ลงทะเบียนไว้แล้ว กรุณาตรวจสอบ SMS แล้วให้นำรหัสมากรอกเพื่อเป็นการยืนยันเบอร์โทรศัพท์', NULL, 'ລະບົບໄດ້ສົ່ງລະຫັດ OTP ໃໝ່ໄປຫາເບີໂທລະສັບທີ່ທ່ານລົງທະບຽນແລ້ວ ກະລຸນາກວດເບິ່ງ SMS ແລະໃສ່ລະຫັດເພື່ອຢືນຢັນເບີໂທລະສັບ.'),
(386, 'The type of file is invalid', 'text', 'index', 0, 'ชนิดของไฟล์ไม่รองรับ', NULL, 'ຊະນິດຂອງແຟ້ມບໍ່ຮອງຮັບ'),
(387, 'Theme', 'text', 'index', 0, 'ธีม', NULL, 'ຮູບແບບສີສັນ'),
(388, 'This :name already exist', 'text', 'index', 0, 'มี :name นี้อยู่ก่อนแล้ว', NULL, 'ມີ :name ນີ້ຢູ່ກ່ອນແລ້ວ'),
(389, 'This website uses cookies to provide our services. To find out more about our use of cookies, please see our :privacy.', 'text', 'index', 0, 'เว็บไซต์นี้มีการใช้คุกกี้เพื่อปรับปรุงการให้บริการ หากต้องการข้อมูลเพิ่มเติมเกี่ยวกับการใช้คุกกี้ของเรา โปรดดู :privacy', NULL, 'ເວັບໄຊທ໌ນີ້ໃຊ້ຄຸກກີເພື່ອປັບປຸງການບໍລິການ. ສໍາລັບຂໍ້ມູນເພີ່ມເຕີມກ່ຽວກັບການນໍາໃຊ້ຄຸກກີຂອງພວກເຮົາ, ເບິ່ງ :privacy'),
(390, 'Time', 'text', 'index', 0, 'เวลา', NULL, 'ເວລາ'),
(391, 'Time zone', 'text', 'index', 0, 'เขตเวลา', NULL, 'ເຂດເວລາ'),
(392, 'times', 'text', 'index', 0, 'ครั้ง', NULL, 'ຄັ້ງ'),
(393, 'to', 'text', 'index', 0, 'ถึง', NULL, 'ເຖິງ'),
(394, 'To change your password, enter your password to match the two inputs', 'text', 'index', 0, 'ถ้าต้องการเปลี่ยนรหัสผ่าน กรุณากรอกรหัสผ่านสองช่องให้ตรงกัน', NULL, 'ຖ້າຕ້ອງການປ່ຽນລະຫັດຜ່ານກະລຸນາເພີ່ມລະຫັດຜ່ານສອງຊ່ອງໃຫ້ກົງກັນ'),
(395, 'Top 5 Employees by Salary', 'text', 'index', 0, 'พนักงาน 5 อันดับเงินเดือนสูงสุด', NULL, 'ພະນັກງານ 5 ອັນດັບເງິນເດືອນສູງສຸດ'),
(396, 'Topic', 'text', 'index', 0, 'หัวข้อ', NULL, 'ຫົວຂໍ້'),
(397, 'Total', 'text', 'index', 0, 'รวม', NULL, 'ລວມ'),
(398, 'Total deductions', 'text', 'index', 0, 'รวมรายหัก', NULL, 'ລວມລາຍຫັກ'),
(399, 'Total income', 'text', 'index', 0, 'รวมรายได้', NULL, 'ລາຍຮັບທັງໝົດ'),
(400, 'Total Salary', 'text', 'index', 0, 'เงินเดือนทั้งหมด', NULL, 'ເງິນເດືອນທັງໝົດ'),
(401, 'Transaction date', 'text', 'index', 0, 'วันที่ทำรายการ', NULL, 'ວັນທີ່ເຮັດລາຍະການ'),
(402, 'Type', 'text', 'index', 0, 'ประเภท', NULL, 'ປະເພດ'),
(403, 'Type of file uploads', 'text', 'index', 0, 'ชนิดของไฟล์อัปโหลด', NULL, 'ຊະນິດຂອງແຟ້ມອັບໂຫຼດ'),
(404, 'Unable to complete the transaction', 'text', 'index', 0, 'ไม่สามารถทำรายการนี้ได้', NULL, 'ບໍ່ສາມາດເຮັດລາຍການນີ້ໄດ້'),
(405, 'Unable to take leave across the fiscal year. If you want to take continuous leave, separate the leave form into two. within that fiscal year', 'text', 'index', 0, 'ไม่สามารถลาข้ามปีงบประมาณได้ ถ้าต้องการลาต่อเนื่องให้แยกใบลาเป็นสองใบ ภายในปีงบประมาณ นั้นๆ', NULL, 'ບໍ່ສາມາດຢຸດພັກໄດ້ຕະຫຼອດປີງົບປະມານ. ຖ້າທ່ານຕ້ອງການອອກພັກຢ່າງຕໍ່ເນື່ອງ, ໃຫ້ແຍກແບບຟອມອອກເປັນສອງ. ພາຍໃນປີງົບປະມານນັ້ນ'),
(406, 'Unlimited', 'text', 'index', 0, 'ไม่จำกัด', NULL, 'ບໍ່ຈຳກັດ'),
(407, 'Upload', 'text', 'index', 0, 'อัปโหลด', NULL, 'ອັບໂຫຼດ'),
(408, 'Upload :type files', 'text', 'index', 0, 'อัปโหลดไฟล์ :type', NULL, 'ອັບໂຫຼດແຟ້ມຂໍ້ມູນ :type'),
(409, 'URL must begin with http:// or https://', 'text', 'index', 0, 'URL ต้องขึ้นต้นด้วย http:// หรือ https://', NULL, 'URL ຕ້ອງເລີ່ມຕົ້ນດ້ວຍ http:// ຫຼື https://'),
(410, 'Usage history', 'text', 'index', 0, 'ประวัติการใช้งาน', NULL, 'ປະ​ຫວັດ​ການ​ນໍາ​ໃຊ້​'),
(411, 'Use the theme&#039;s default settings.', 'text', 'index', 0, 'ใช้การตั้งค่าเริ่มต้นของธีม', NULL, 'ໃຊ້ການຕັ້ງຄ່າເລີ່ມຕົ້ນຂອງຮູບແບບສີສັນ.'),
(412, 'User', 'text', 'index', 0, 'สมาชิก', NULL, 'ສະມາຊິກ'),
(413, 'Username', 'text', 'index', 0, 'ชื่อผู้ใช้', NULL, 'ຊື່ຜູ້ໃຊ້'),
(414, 'Username for the mail server. (To change, enter a new value.)', 'text', 'index', 0, 'ชื่อผู้ใช้ของเมล์เซิร์ฟเวอร์ (ต้องการเปลี่ยน ให้กรอก)', NULL, 'ຊື່ຜູ້ໃຊ້ຂອງເມວເຊີບເວີ (ຕ້ອງການປ່ຽນ ໃຫ້ເພີ່ມ)'),
(415, 'Username used for login or request a new password', 'text', 'index', 0, 'ชื่อผู้ใช้ ใช้สำหรับการเข้าระบบหรือการขอรหัสผ่านใหม่', NULL, 'ຊື່ຜູ້ໃຊ້ທີ່ໃຊ້ສໍາລັບການເຂົ້າສູ່ລະບົບຫຼືຮ້ອງຂໍລະຫັດຜ່ານໃຫມ່'),
(416, 'Users', 'text', 'index', 0, 'สมาชิก', NULL, 'ສະມາຊິກ'),
(417, 'Verify Account', 'text', 'index', 0, 'ยืนยันบัญชี', NULL, 'ຢືນຢັນບັນຊີ'),
(418, 'Version', 'text', 'index', 0, 'รุ่น', NULL, 'ຮຸ່ນ'),
(419, 'Waiting list', 'text', 'index', 0, 'รายการรอตรวจสอบ', NULL, 'ລາຍຊື່ລໍຖ້າ'),
(420, 'Waiting to check from the staff', 'text', 'index', 0, 'รอตรวจสอบจากเจ้าหน้าที่', NULL, 'ລໍຖ້າການກວດສອບຈາກພະນັກງານ'),
(421, 'Website template', 'text', 'index', 0, 'แม่แบบเว็บไซต์', NULL, 'ແມ່ແບບເວັບໄຊທ໌'),
(422, 'Website title', 'text', 'index', 0, 'ชื่อเว็บ', NULL, 'ຊື່ເວັບ'),
(423, 'Welcome', 'text', 'index', 0, 'สวัสดี', NULL, 'ສະບາຍດີ'),
(424, 'Welcome %s, login complete', 'text', 'index', 0, 'สวัสดี คุณ %s ยินดีต้อนรับเข้าสู่ระบบ', NULL, 'ສະບາຍດີທ່ານ %s ຍິນດີຕ້ອນຮັບເຂົ້າສູ່ລະບົບ'),
(425, 'Welcome new members', 'text', 'index', 0, 'ยินดีต้อนรับสมาชิกใหม่', NULL, 'ຍິນດີຕ້ອນຮັບສະມາຊິກໃໝ່'),
(426, 'Welcome. Phone number has been verified. Please log in again.', 'text', 'index', 0, 'ยินดีต้อนรับ หมายเลขโทรศัพท์ได้รับการยืนยันแล้ว กรุณาเข้าระบบอีกครั้ง', NULL, 'ຍິນດີຕ້ອນຮັບເບີໂທລະສັບ. ກະລຸນາເຂົ້າສູ່ລະບົບອີກຄັ້ງ.'),
(427, 'When download', 'text', 'index', 0, 'เมื่อคลิกดาวน์โหลด', NULL, 'ເມື່ອກົດດາວໂຫຼດ'),
(428, 'When enabled Social accounts can be logged in as an administrator. (Some abilities will not be available)', 'text', 'index', 0, 'เมื่อเปิดใช้งาน บัญชีโซเชียลจะสามารถเข้าระบบเป็นผู้ดูแลได้ (ความสามารถบางอย่างจะไม่สามารถใช้งานได้)', NULL, 'ເມື່ອເປີດໃຊ້ງານ ບັນຊີສັງຄົມສາມາດເຂົ້າສູ່ລະບົບເປັນຜູ້ບໍລິຫານ. (ຄວາມສາມາດບາງຢ່າງຈະບໍ່ມີ)'),
(429, 'When enabled, a cookies consent banner will be displayed.', 'text', 'index', 0, 'เมื่อเปิดใช้งานระบบจะแสดงแบนเนอร์ขออนุญาตใช้งานคุ้กกี้', NULL, 'ເມື່ອເປີດໃຊ້ງານແລ້ວ, ປ້າຍໂຄສະນາການຍິນຍອມຂອງ cookies ຈະສະແດງຂຶ້ນ.'),
(430, 'When enabled, Members registered with email must also verify their email address. It is not recommended to use in conjunction with other login methods.', 'text', 'index', 0, 'เมื่อเปิดใช้งาน สมาชิกที่ลงทะเบียนด้วยอีเมลจะต้องยืนยันที่อยู่อีเมลด้วย ไม่แนะนำให้ใช้ร่วมกับการเข้าระบบด้วยช่องทางอื่นๆ', NULL, 'ເມື່ອເປີດໃຊ້ ສະມາຊິກທີ່ລົງທະບຽນກັບອີເມລ໌ຈະຕ້ອງຢືນຢັນທີ່ຢູ່ອີເມວຂອງເຂົາເຈົ້າ. ມັນບໍ່ໄດ້ຖືກແນະນໍາໃຫ້ໃຊ້ຮ່ວມກັບວິທີການເຂົ້າສູ່ລະບົບອື່ນໆ.'),
(431, 'Width', 'text', 'index', 0, 'กว้าง', NULL, 'ກວ້າງ'),
(432, 'With selected', 'text', 'index', 0, 'ทำกับที่เลือก', NULL, 'ເຮັດກັບທີ່ເລືອກ'),
(433, 'Year', 'text', 'index', 0, 'ปี', NULL, 'ປີ'),
(434, 'You are not affiliated with a department. Please contact the administrator.', 'text', 'index', 0, 'คุณยังไม่ได้สังกัดแผนก กรุณาติดต่อผู้ดูแล', NULL, 'ເຈົ້າຍັງບໍ່ໄດ້ຂຶ້ນກັບພະແນກເທື່ອ. ກະລຸນາຕິດຕໍ່ຜູ້ເບິ່ງແຍງລະບົບ.'),
(435, 'You are not affiliated with a department. Please select a department first.', 'text', 'index', 0, 'คุณยังไม่ได้สังกัดแผนก กรุณาเลือกแผนกก่อน', NULL, 'ເຈົ້າຍັງບໍ່ໄດ້ຂຶ້ນກັບພະແນກເທື່ອ. ກະລຸນາເລືອກພະແນກກ່ອນ.'),
(436, 'You can enter your LINE user ID below on your personal information page. to link your account to this official account', 'text', 'index', 0, 'คุณสามารถกรอก LINE user ID ด้านล่างในหน้าข้อมูลส่วนตัวของคุณ เพื่อผูกบัญชีของคุณเข้ากับบัญชีทางการนี้ได้', NULL, 'ທ່ານສາມາດໃສ່ LINE user ID ຂອງທ່ານຂ້າງລຸ່ມນີ້ຢູ່ໃນຫນ້າຂໍ້ມູນສ່ວນຕົວຂອງທ່ານ. ເພື່ອເຊື່ອມຕໍ່ບັນຊີຂອງທ່ານກັບບັນຊີທາງການນີ້'),
(437, 'You can login at', 'text', 'index', 0, 'คุณสามารถเข้าระบบได้ที่', NULL, 'ທ່ານສາມາດເຂົ້າສູ່ລະບົບໄດ້ທີ່'),
(438, 'You haven&#039;t verified your email address. Please check your email. and click the link in the email', 'text', 'index', 0, 'คุณยังไม่ได้ยืนยันที่อยู่อีเมล กรุณาตรวจสอบอีเมลของคุณ และคลิกลิงค์ในอีเมล', NULL, 'ທ່ານຍັງບໍ່ໄດ້ຢືນຢັນທີ່ຢູ່ອີເມວຂອງທ່ານ. ກະລຸນາກວດເບິ່ງອີເມວຂອງທ່ານ. ແລະຄລິກໃສ່ການເຊື່ອມຕໍ່ໃນອີເມລ໌'),
(439, 'You want to', 'text', 'index', 0, 'คุณต้องการ', NULL, 'ທ່ານຕ້ອງການ'),
(440, 'Your account has been approved.', 'text', 'index', 0, 'บัญชีของท่านได้รับการอนุมัติเรียบร้อยแล้ว', NULL, 'ບັນຊີຂອງທ່ານໄດ້ຮັບການອະນຸມັດແລ້ວ.'),
(441, 'Your account has not been approved, please wait or contact the administrator.', 'text', 'index', 0, 'บัญชีของท่านยังไม่ได้รับการอนุมัติ กรุณารอ หรือติดต่อสอบถามไปยังผู้ดูแล', NULL, 'ບັນຊີຂອງທ່ານບໍ່ໄດ້ຮັບການອະນຸມັດ, ກະລຸນາລໍຖ້າ ຫຼືຕິດຕໍ່ຜູ້ເບິ່ງແຍງລະບົບ.'),
(442, 'Your message was sent successfully', 'text', 'index', 0, 'ส่งข้อความไปยังผู้ที่เกี่ยวข้องเรียบร้อยแล้ว', NULL, 'ສົ່ງຂໍ້ຄວາມໄປຍັງຜູ້ຮັບຮຽບຮ້ອຍແລ້ວ'),
(443, 'Your new password is', 'text', 'index', 0, 'รหัสผ่านใหม่ของคุณคือ', NULL, 'ລະຫັດຜ່ານໃໝ່ຂອງທ່ານຄື'),
(444, 'Your OTP code is :otp. Please enter this code on the website to confirm your phone number.', 'text', 'index', 0, 'รหัส OTP ของคุณคือ :otp กรุณาป้อนรหัสนี้บนเว็บไซต์เพื่อยืนยันหมายเลขโทรศัพท์ของคุณ', NULL, 'ລະຫັດ OTP ຂອງທ່ານແມ່ນ :otp ກະລຸນາໃສ່ລະຫັດນີ້ຢູ່ໃນເວັບໄຊທ໌ເພື່ອຢືນຢັນເບີໂທລະສັບຂອງທ່ານ.'),
(445, 'Your registration information', 'text', 'index', 0, 'ข้อมูลการลงทะเบียนของคุณ', NULL, 'ຂໍ້ມູນການລົງທະບຽນຂອງທ່ານ'),
(446, 'Zipcode', 'text', 'index', 0, 'รหัสไปรษณีย์', NULL, 'ລະຫັດໄປສະນີ'),
(447, 'THB', 'text', 'index', 0, 'บาท', NULL, 'ກີບ'),
(448, 'Print', 'text', 'index', 0, 'พิมพ์', NULL, 'ພິມ'),
(449, 'Close', 'text', 'index', 0, 'ปิด', NULL, 'ປິດ'),
(450, 'Income', 'text', 'index', 0, 'รายได้', NULL, 'ລາຍຮັບ');

-- --------------------------------------------------------

--
-- Table structure for table `app_logs`
--

CREATE TABLE `app_logs` (
  `id` int(11) NOT NULL,
  `src_id` int(11) NOT NULL,
  `module` varchar(20) NOT NULL,
  `action` varchar(20) NOT NULL,
  `create_date` datetime NOT NULL,
  `reason` text DEFAULT NULL,
  `member_id` int(11) DEFAULT NULL,
  `topic` text NOT NULL,
  `datas` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `app_salary`
--

CREATE TABLE `app_salary` (
  `id` varchar(15) NOT NULL,
  `member_id` int(11) NOT NULL,
  `year` varchar(4) NOT NULL,
  `month` varchar(2) NOT NULL,
  `basic_salary` double(10,2) NOT NULL DEFAULT 0.00,
  `allowance` double(10,2) NOT NULL DEFAULT 0.00,
  `overtime` double(10,2) NOT NULL DEFAULT 0.00,
  `bonus` double(10,2) NOT NULL DEFAULT 0.00,
  `deduction` double(10,2) NOT NULL DEFAULT 0.00,
  `social_security` double(10,2) NOT NULL DEFAULT 0.00,
  `tax` double(10,2) NOT NULL DEFAULT 0.00,
  `net_salary` double(10,2) NOT NULL DEFAULT 0.00,
  `remark` text DEFAULT NULL,
  `create_date` datetime NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `app_user`
--

CREATE TABLE `app_user` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `salt` varchar(32) DEFAULT '',
  `password` varchar(50) NOT NULL,
  `token` varchar(50) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 0,
  `permission` text DEFAULT '',
  `name` varchar(150) NOT NULL,
  `sex` varchar(1) DEFAULT NULL,
  `id_card` varchar(13) DEFAULT NULL,
  `address` varchar(150) DEFAULT NULL,
  `phone` varchar(32) DEFAULT NULL,
  `provinceID` varchar(3) DEFAULT NULL,
  `province` varchar(50) DEFAULT NULL,
  `zipcode` varchar(10) DEFAULT NULL,
  `country` varchar(2) DEFAULT 'TH',
  `create_date` datetime DEFAULT NULL,
  `active` tinyint(1) DEFAULT 1,
  `social` tinyint(1) DEFAULT 0,
  `line_uid` varchar(33) DEFAULT NULL,
  `telegram_id` varchar(13) DEFAULT NULL,
  `activatecode` varchar(32) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `app_user`
--

INSERT INTO `app_user` (`id`, `username`, `salt`, `password`, `token`, `status`, `permission`, `name`, `sex`, `id_card`, `address`, `phone`, `provinceID`, `province`, `zipcode`, `country`, `create_date`, `active`, `social`, `line_uid`, `telegram_id`, `activatecode`) VALUES
(1, 'admin@localhost', '68f06ab584878', '85f79c7ffe130bcfe83514931920f8c8f11df7ac', NULL, 1, '', 'แอดมิน', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'TH', '2025-10-16 05:47:01', 1, 0, NULL, NULL, ''),
(2, 'demo', '68f06ab584878', '016bdd5a89b75cb14fa8b81fe4e929bec0783719', NULL, 0, '', 'ตัวอย่าง', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'TH', '2025-10-16 05:47:01', 1, 0, NULL, NULL, '');

-- --------------------------------------------------------

--
-- Table structure for table `app_user_meta`
--

CREATE TABLE `app_user_meta` (
  `value` varchar(10) NOT NULL,
  `name` varchar(20) NOT NULL,
  `member_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `app_category`
--
ALTER TABLE `app_category`
  ADD KEY `type` (`type`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `app_language`
--
ALTER TABLE `app_language`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `app_logs`
--
ALTER TABLE `app_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `src_id` (`src_id`),
  ADD KEY `module` (`module`),
  ADD KEY `action` (`action`);

--
-- Indexes for table `app_salary`
--
ALTER TABLE `app_salary`
  ADD PRIMARY KEY (`id`),
  ADD KEY `member_id` (`member_id`),
  ADD KEY `year_month` (`year`,`month`);

--
-- Indexes for table `app_user`
--
ALTER TABLE `app_user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `line_uid` (`line_uid`),
  ADD KEY `username` (`username`),
  ADD KEY `token` (`token`),
  ADD KEY `phone` (`phone`),
  ADD KEY `id_card` (`id_card`),
  ADD KEY `activatecode` (`activatecode`);

--
-- Indexes for table `app_user_meta`
--
ALTER TABLE `app_user_meta`
  ADD KEY `member_id` (`member_id`,`name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `app_language`
--
ALTER TABLE `app_language`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=451;

--
-- AUTO_INCREMENT for table `app_logs`
--
ALTER TABLE `app_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `app_user`
--
ALTER TABLE `app_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- Database: `equipment_inventory`
--
CREATE DATABASE IF NOT EXISTS `equipment_inventory` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `equipment_inventory`;

-- --------------------------------------------------------

--
-- Table structure for table `equipments`
--

CREATE TABLE `equipments` (
  `id` int(11) NOT NULL,
  `asset_code` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `serial_number` varchar(100) NOT NULL,
  `purchase_date` date NOT NULL,
  `location` varchar(255) NOT NULL,
  `responsible` varchar(255) NOT NULL,
  `condition` enum('ดีมาก','ดี','ปานกลาง','ชำรุด') NOT NULL DEFAULT 'ดี',
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `vendor` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `image` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `equipments`
--

INSERT INTO `equipments` (`id`, `asset_code`, `name`, `serial_number`, `purchase_date`, `location`, `responsible`, `condition`, `price`, `vendor`, `notes`, `image`, `created_at`, `updated_at`) VALUES
(1, '05040', 'pc', '76786', '2025-10-22', 'varee', 'moss', 'ดีมาก', 12000.00, 'jib', '', 'uploads/68f85d1b9a4fc_1761107227.jpg', '2025-10-22 04:27:07', NULL),
(2, '003', 'dfef', 'efgwsfgr64', '2025-10-22', 'awd', 'wda', 'ดี', 185220.00, '', '', 'uploads/68f85d3dc0934_1761107261.jpg', '2025-10-22 04:27:41', NULL),
(3, '004', 'asus', '589556', '2025-10-22', 'varee', 'มอส', 'ดีมาก', 12000.00, 'advice', 'ปกติ', 'uploads/68f85d786177c_1761107320.jpg', '2025-10-22 04:28:40', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `equipments`
--
ALTER TABLE `equipments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `asset_code` (`asset_code`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `equipments`
--
ALTER TABLE `equipments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- Database: `inventory_db`
--
CREATE DATABASE IF NOT EXISTS `inventory_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `inventory_db`;

-- --------------------------------------------------------

--
-- Table structure for table `equipments`
--

CREATE TABLE `equipments` (
  `id` int(11) NOT NULL,
  `asset_code` varchar(100) NOT NULL,
  `name_model` varchar(255) NOT NULL,
  `serial` varchar(255) DEFAULT NULL,
  `date_acquired` date DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `owner_person` varchar(255) DEFAULT NULL,
  `condition_status` varchar(100) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `price` decimal(14,2) DEFAULT NULL,
  `vendor` varchar(255) DEFAULT NULL,
  `image_filename` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `equipments`
--

INSERT INTO `equipments` (`id`, `asset_code`, `name_model`, `serial`, `date_acquired`, `location`, `owner_person`, `condition_status`, `note`, `price`, `vendor`, `image_filename`, `created_at`, `updated_at`) VALUES
(1, '003', 'PC', '789654', '2025-10-22', 'varee', 'moss', 'ปกติ', 'ห้องสมุด', 15000.00, 'jib', 'img_68f8476102227.jpg', '2025-10-22 02:54:25', '2025-10-22 02:54:25');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `created_at`) VALUES
(1, 'admin', '$2y$10$6Qn/6UCjYyAgpYI5PYD4tOqYOD7quFJ2ZMTYK4Ej1oX7y6N0U/JcS', 'admin', '2025-10-22 03:09:14');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `equipments`
--
ALTER TABLE `equipments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `asset_code` (`asset_code`);

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
-- AUTO_INCREMENT for table `equipments`
--
ALTER TABLE `equipments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- Database: `it_asset_management`
--
CREATE DATABASE IF NOT EXISTS `it_asset_management` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `it_asset_management`;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` varchar(10) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `created_at`) VALUES
('01', '🖥️ อุปกรณ์คอมพิวเตอร์', 'เครื่องคอมพิวเตอร์ตั้งโต๊ะ, โน้ตบุ๊ก, All-in-One, จอภาพ, UPS, คีย์บอร์ด, เมาส์', '2025-10-22 07:09:53'),
('02', '🌐 อุปกรณ์เครือข่าย', 'Router, Switch, Access Point, Modem, สายแลน, ตู้แร็ก', '2025-10-22 07:09:53'),
('03', '🖨️ อุปกรณ์สำนักงาน / ต่อพ่วง', 'Printer, Scanner, Projector, เครื่องถ่ายเอกสาร, Visual Presenter, เครื่องเสียง', '2025-10-22 07:09:53'),
('04', '🏫 อุปกรณ์สื่อการเรียนการสอน', 'Smart Board, ชุดคอมพิวเตอร์ห้องเรียน, Tablet, เครื่องเสียงห้องเรียน, ชุดสื่อมัลติมีเดีย', '2025-10-22 07:09:53'),
('05', '⚙️ อะไหล่ / ชิ้นส่วนคอมพิวเตอร์', 'HDD, SSD, RAM, การ์ดจอ, เมนบอร์ด, Power Supply, Adapter', '2025-10-22 07:09:53'),
('06', '🔒 ระบบรักษาความปลอดภัย', 'กล้องวงจรปิด, NVR/DVR, จอมอนิเตอร์, เครื่องสแกนลายนิ้วมือ, เครื่องอ่านบัตร', '2025-10-22 07:09:53'),
('07', '💡 อุปกรณ์อิเล็กทรอนิกส์ทั่วไป', 'โทรทัศน์, เครื่องเล่นดีวีดี, ลำโพง, เครื่องขยายเสียง, จานดาวเทียม', '2025-10-22 07:09:53'),
('08', '🧰 อุปกรณ์ซ่อมบำรุง / เครื่องมือ', 'เครื่องมือวัดไฟ, เครื่องมือบัดกรี, อุปกรณ์ตรวจสอบระบบ', '2025-10-22 07:09:53'),
('09', '🗂️ ซอฟต์แวร์และใบอนุญาต', 'Windows, Microsoft Office, Antivirus, โปรแกรมสื่อการสอน, License Key', '2025-10-22 07:09:53'),
('10', '🧾 อื่น ๆ', 'อุปกรณ์ที่ไม่เข้าพวกข้างต้น เช่น ของทดลอง, ชุดทดลองหุ่นยนต์, IoT Kit ฯลฯ', '2025-10-22 07:09:53');

-- --------------------------------------------------------

--
-- Table structure for table `equipment`
--

CREATE TABLE `equipment` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(200) NOT NULL,
  `category_id` varchar(10) NOT NULL,
  `condition_status` enum('ใหม่','ดี','ชำรุด','รอซ่อม') NOT NULL DEFAULT 'ดี',
  `price` decimal(10,2) NOT NULL,
  `purchase_date` date NOT NULL,
  `description` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `equipment`
--

INSERT INTO `equipment` (`id`, `code`, `name`, `category_id`, `condition_status`, `price`, `purchase_date`, `description`, `image_path`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'IT001', 'เครื่องคอมพิวเตอร์ตั้งโต๊ะ Dell OptiPlex 7090', '01', 'ดี', 25000.00, '2024-01-15', 'CPU Intel Core i5-11500, RAM 8GB DDR4, SSD 256GB, จอ 24 นิ้ว', NULL, 1, '2025-10-22 07:09:53', '2025-10-22 07:09:53'),
(2, 'IT002', 'โน้ตบุ๊ก HP Pavilion 15', '01', 'ใหม่', 32000.00, '2024-08-20', 'CPU Intel Core i7-1255U, RAM 16GB, SSD 512GB, จอ 15.6 นิ้ว FHD', NULL, 1, '2025-10-22 07:09:53', '2025-10-22 07:09:53'),
(3, 'IT003', 'จอภาพ LG 27 นิ้ว', '01', 'ดี', 5500.00, '2023-11-10', 'IPS Panel, Full HD 1920x1080, HDMI + VGA', NULL, 1, '2025-10-22 07:09:53', '2025-10-22 07:09:53'),
(4, 'NET001', 'Router Cisco RV340', '02', 'ดี', 8500.00, '2023-05-10', 'Dual WAN, Gigabit, VPN Support', NULL, 1, '2025-10-22 07:09:53', '2025-10-22 07:09:53'),
(5, 'NET002', 'Switch TP-Link 24 Port Gigabit', '02', 'ดี', 4200.00, '2023-07-15', '24 Port Gigabit, Rack Mount, Managed', NULL, 1, '2025-10-22 07:09:53', '2025-10-22 07:09:53'),
(7, 'PR002', 'เครื่องพิมพ์ HP LaserJet Pro', '03', 'ดี', 12500.00, '2024-02-18', 'พิมพ์เลเซอร์สี, Duplex, WiFi', NULL, 1, '2025-10-22 07:09:53', '2025-10-22 07:09:53'),
(8, 'PRJ001', 'Projector Epson EB-X41', '03', 'ดี', 18000.00, '2024-02-28', 'โปรเจคเตอร์ 3600 Lumens, XGA 1024x768', NULL, 1, '2025-10-22 07:09:53', '2025-10-22 07:09:53'),
(9, 'EDU001', 'Smart Board 75 นิ้ว', '04', 'ใหม่', 85000.00, '2024-09-01', 'Smart Interactive Display, 4K Ultra HD, Android OS', NULL, 1, '2025-10-22 07:09:53', '2025-10-22 07:09:53'),
(10, 'EDU002', 'Tablet Samsung Galaxy Tab A8', '04', 'ดี', 8900.00, '2024-03-15', '10.5 นิ้ว, 32GB, WiFi, สำหรับห้องเรียน', NULL, 1, '2025-10-22 07:09:53', '2025-10-22 07:09:53'),
(11, 'PART001', 'SSD Samsung 970 EVO Plus 500GB', '05', 'ใหม่', 2800.00, '2024-10-01', 'NVMe M.2, Read 3500MB/s', NULL, 1, '2025-10-22 07:09:53', '2025-10-22 07:09:53'),
(12, 'PART002', 'RAM Kingston DDR4 16GB', '05', 'ใหม่', 1850.00, '2024-09-25', '16GB DDR4 3200MHz', NULL, 1, '2025-10-22 07:09:53', '2025-10-22 07:09:53'),
(13, 'SEC001', 'กล้องวงจรปิด Hikvision 4MP', '06', 'ดี', 3500.00, '2023-12-05', 'IP Camera, Night Vision, Outdoor', NULL, 1, '2025-10-22 07:09:53', '2025-10-22 07:09:53'),
(14, 'SEC002', 'NVR 8 CH Hikvision', '06', 'ดี', 12000.00, '2023-12-05', '8 Channel NVR, 2TB HDD, H.265+', NULL, 1, '2025-10-22 07:09:53', '2025-10-22 07:09:53'),
(15, 'ELEC001', 'โทรทัศน์ Samsung 55 นิ้ว', '07', 'ดี', 18500.00, '2024-01-20', '4K UHD Smart TV, HDR', NULL, 1, '2025-10-22 07:09:53', '2025-10-22 07:09:53');

--
-- Triggers `equipment`
--
DELIMITER $$
CREATE TRIGGER `trg_equipment_update` AFTER UPDATE ON `equipment` FOR EACH ROW BEGIN
    INSERT INTO equipment_history (equipment_id, action, old_data, new_data, changed_by)
    VALUES (NEW.id, 'update', 
        JSON_OBJECT(
            'code', OLD.code,
            'name', OLD.name,
            'condition_status', OLD.condition_status,
            'price', OLD.price
        ),
        JSON_OBJECT(
            'code', NEW.code,
            'name', NEW.name,
            'condition_status', NEW.condition_status,
            'price', NEW.price
        ),
        NEW.created_by
    );
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `equipment_history`
--

CREATE TABLE `equipment_history` (
  `id` int(11) NOT NULL,
  `equipment_id` int(11) NOT NULL,
  `action` enum('create','update','delete') NOT NULL,
  `old_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_data`)),
  `new_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_data`)),
  `changed_by` int(11) DEFAULT NULL,
  `changed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `maintenance`
--

CREATE TABLE `maintenance` (
  `id` int(11) NOT NULL,
  `equipment_id` int(11) NOT NULL,
  `maintenance_type` enum('ซ่อม','บำรุงรักษา','ตรวจสอบ') NOT NULL,
  `description` text NOT NULL,
  `cost` decimal(10,2) DEFAULT 0.00,
  `maintenance_date` date NOT NULL,
  `status` enum('รอดำเนินการ','กำลังดำเนินการ','เสร็จสิ้น') DEFAULT 'รอดำเนินการ',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `role` enum('admin','staff','viewer') NOT NULL DEFAULT 'viewer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `role`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ผู้ดูแลระบบ', 'admin', '2025-10-22 07:09:53', '2025-10-22 07:09:53'),
(2, 'staff', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'เจ้าหน้าที่', 'staff', '2025-10-22 07:09:53', '2025-10-22 07:09:53'),
(3, 'viewer', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ผู้ชม', 'viewer', '2025-10-22 07:09:53', '2025-10-22 07:09:53');

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_category_statistics`
-- (See below for the actual view)
--
CREATE TABLE `v_category_statistics` (
`id` varchar(10)
,`name` varchar(100)
,`total_equipment` bigint(21)
,`total_value` decimal(32,2)
,`count_new` bigint(21)
,`count_good` bigint(21)
,`count_damaged` bigint(21)
,`count_pending` bigint(21)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_equipment_summary`
-- (See below for the actual view)
--
CREATE TABLE `v_equipment_summary` (
`id` int(11)
,`code` varchar(50)
,`name` varchar(200)
,`category_name` varchar(100)
,`condition_status` enum('ใหม่','ดี','ชำรุด','รอซ่อม')
,`price` decimal(10,2)
,`purchase_date` date
,`created_by_name` varchar(100)
,`created_at` timestamp
);

-- --------------------------------------------------------

--
-- Structure for view `v_category_statistics`
--
DROP TABLE IF EXISTS `v_category_statistics`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_category_statistics`  AS SELECT `c`.`id` AS `id`, `c`.`name` AS `name`, count(`e`.`id`) AS `total_equipment`, sum(`e`.`price`) AS `total_value`, count(case when `e`.`condition_status` = 'ใหม่' then 1 end) AS `count_new`, count(case when `e`.`condition_status` = 'ดี' then 1 end) AS `count_good`, count(case when `e`.`condition_status` = 'ชำรุด' then 1 end) AS `count_damaged`, count(case when `e`.`condition_status` = 'รอซ่อม' then 1 end) AS `count_pending` FROM (`categories` `c` left join `equipment` `e` on(`c`.`id` = `e`.`category_id`)) GROUP BY `c`.`id`, `c`.`name` ORDER BY `c`.`id` ASC ;

-- --------------------------------------------------------

--
-- Structure for view `v_equipment_summary`
--
DROP TABLE IF EXISTS `v_equipment_summary`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_equipment_summary`  AS SELECT `e`.`id` AS `id`, `e`.`code` AS `code`, `e`.`name` AS `name`, `c`.`name` AS `category_name`, `e`.`condition_status` AS `condition_status`, `e`.`price` AS `price`, `e`.`purchase_date` AS `purchase_date`, `u`.`full_name` AS `created_by_name`, `e`.`created_at` AS `created_at` FROM ((`equipment` `e` left join `categories` `c` on(`e`.`category_id` = `c`.`id`)) left join `users` `u` on(`e`.`created_by` = `u`.`id`)) ORDER BY `e`.`created_at` DESC ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `equipment`
--
ALTER TABLE `equipment`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_code` (`code`),
  ADD KEY `idx_category` (`category_id`),
  ADD KEY `idx_condition` (`condition_status`),
  ADD KEY `idx_purchase_date` (`purchase_date`);

--
-- Indexes for table `equipment_history`
--
ALTER TABLE `equipment_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `changed_by` (`changed_by`),
  ADD KEY `idx_equipment` (`equipment_id`),
  ADD KEY `idx_changed_at` (`changed_at`);

--
-- Indexes for table `maintenance`
--
ALTER TABLE `maintenance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_equipment` (`equipment_id`),
  ADD KEY `idx_status` (`status`);

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
-- AUTO_INCREMENT for table `equipment`
--
ALTER TABLE `equipment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `equipment_history`
--
ALTER TABLE `equipment_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `maintenance`
--
ALTER TABLE `maintenance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `equipment`
--
ALTER TABLE `equipment`
  ADD CONSTRAINT `equipment_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `equipment_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `equipment_history`
--
ALTER TABLE `equipment_history`
  ADD CONSTRAINT `equipment_history_ibfk_1` FOREIGN KEY (`equipment_id`) REFERENCES `equipment` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `equipment_history_ibfk_2` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `maintenance`
--
ALTER TABLE `maintenance`
  ADD CONSTRAINT `maintenance_ibfk_1` FOREIGN KEY (`equipment_id`) REFERENCES `equipment` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `maintenance_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
--
-- Database: `it_inventory`
--
CREATE DATABASE IF NOT EXISTS `it_inventory` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `it_inventory`;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, '🖥️ คอมพิวเตอร์'),
(2, '🌐 เครือข่าย'),
(3, '🖨️ เครื่องพิมพ์'),
(4, '🏫 สื่อการเรียนการสอน'),
(5, '⚙️ อะไหล่'),
(6, '🔒 ระบบรักษาความปลอดภัย'),
(7, '💡 อุปกรณ์อิเล็กทรอนิกส์'),
(8, '🧰 เครื่องมือ/ซ่อมบำรุง'),
(9, '🗂️ ซอฟต์แวร์และใบอนุญาต'),
(10, '🧾 อื่น ๆ');

-- --------------------------------------------------------

--
-- Table structure for table `equipment`
--

CREATE TABLE `equipment` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `name_model` varchar(255) NOT NULL,
  `category_id` int(11) NOT NULL,
  `condition_status` enum('ใหม่','ดี','ชำรุด','รอซ่อม') DEFAULT 'ดี',
  `price` decimal(12,2) DEFAULT 0.00,
  `purchase_date` date NOT NULL,
  `description_d` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `equipment`
--

INSERT INTO `equipment` (`id`, `code`, `name_model`, `category_id`, `condition_status`, `price`, `purchase_date`, `description_d`, `image_path`, `created_by`, `created_at`) VALUES
(1, 'IT001', 'เครื่องคอมพิวเตอร์ตั้งโต๊ะ Dell', 1, 'ดี', 25000.00, '2024-01-15', 'CPU i5, RAM 8GB', NULL, 1, '2025-10-22 09:19:16'),
(2, 'IT002', 'โน้ตบุ๊ก HP Pavilion', 1, 'ใหม่', 32000.00, '2024-03-10', 'Core i7, SSD 512GB', NULL, 1, '2025-10-22 09:19:16');

-- --------------------------------------------------------

--
-- Table structure for table `equipment_history`
--

CREATE TABLE `equipment_history` (
  `id` int(11) NOT NULL,
  `equipment_id` int(11) NOT NULL,
  `action` enum('create','update','delete') NOT NULL,
  `old_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_data`)),
  `new_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_data`)),
  `changed_by` int(11) DEFAULT NULL,
  `changed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `role` enum('admin','staff','viewer') DEFAULT 'viewer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `role`, `created_at`) VALUES
(1, 'admin', '0192023a7bbd73250516f069df18b500', 'ผู้ดูแลระบบ', 'admin', '2025-10-22 09:19:16'),
(2, 'staff', 'de9bf5643eabf80f4a56fda3bbb84483', 'เจ้าหน้าที่', 'staff', '2025-10-22 09:19:16'),
(3, 'viewer', '49e5e739ea41d635246cd9cd21af17c4', 'ผู้ชม', 'viewer', '2025-10-22 09:19:16');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `equipment`
--
ALTER TABLE `equipment`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `equipment_history`
--
ALTER TABLE `equipment_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `equipment_id` (`equipment_id`),
  ADD KEY `changed_by` (`changed_by`);

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
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `equipment`
--
ALTER TABLE `equipment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `equipment_history`
--
ALTER TABLE `equipment_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `equipment`
--
ALTER TABLE `equipment`
  ADD CONSTRAINT `equipment_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `equipment_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `equipment_history`
--
ALTER TABLE `equipment_history`
  ADD CONSTRAINT `equipment_history_ibfk_1` FOREIGN KEY (`equipment_id`) REFERENCES `equipment` (`id`),
  ADD CONSTRAINT `equipment_history_ibfk_2` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`);
--
-- Database: `phpmyadmin`
--
CREATE DATABASE IF NOT EXISTS `phpmyadmin` DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
USE `phpmyadmin`;

-- --------------------------------------------------------

--
-- Table structure for table `pma__bookmark`
--

CREATE TABLE `pma__bookmark` (
  `id` int(10) UNSIGNED NOT NULL,
  `dbase` varchar(255) NOT NULL DEFAULT '',
  `user` varchar(255) NOT NULL DEFAULT '',
  `label` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `query` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Bookmarks';

-- --------------------------------------------------------

--
-- Table structure for table `pma__central_columns`
--

CREATE TABLE `pma__central_columns` (
  `db_name` varchar(64) NOT NULL,
  `col_name` varchar(64) NOT NULL,
  `col_type` varchar(64) NOT NULL,
  `col_length` text DEFAULT NULL,
  `col_collation` varchar(64) NOT NULL,
  `col_isNull` tinyint(1) NOT NULL,
  `col_extra` varchar(255) DEFAULT '',
  `col_default` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Central list of columns';

-- --------------------------------------------------------

--
-- Table structure for table `pma__column_info`
--

CREATE TABLE `pma__column_info` (
  `id` int(5) UNSIGNED NOT NULL,
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `table_name` varchar(64) NOT NULL DEFAULT '',
  `column_name` varchar(64) NOT NULL DEFAULT '',
  `comment` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `mimetype` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `transformation` varchar(255) NOT NULL DEFAULT '',
  `transformation_options` varchar(255) NOT NULL DEFAULT '',
  `input_transformation` varchar(255) NOT NULL DEFAULT '',
  `input_transformation_options` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Column information for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__designer_settings`
--

CREATE TABLE `pma__designer_settings` (
  `username` varchar(64) NOT NULL,
  `settings_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Settings related to Designer';

-- --------------------------------------------------------

--
-- Table structure for table `pma__export_templates`
--

CREATE TABLE `pma__export_templates` (
  `id` int(5) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL,
  `export_type` varchar(10) NOT NULL,
  `template_name` varchar(64) NOT NULL,
  `template_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Saved export templates';

--
-- Dumping data for table `pma__export_templates`
--

INSERT INTO `pma__export_templates` (`id`, `username`, `export_type`, `template_name`, `template_data`) VALUES
(1, 'root', 'database', 'school_it_management', '{\"quick_or_custom\":\"quick\",\"what\":\"sql\",\"structure_or_data_forced\":\"0\",\"table_select[]\":[\"categories\",\"categories_items\",\"departments\",\"employees\",\"equipment\",\"maintenance\",\"users\"],\"table_structure[]\":[\"categories\",\"categories_items\",\"departments\",\"employees\",\"equipment\",\"maintenance\",\"users\"],\"table_data[]\":[\"categories\",\"categories_items\",\"departments\",\"employees\",\"equipment\",\"maintenance\",\"users\"],\"aliases_new\":\"\",\"output_format\":\"sendit\",\"filename_template\":\"@DATABASE@\",\"remember_template\":\"on\",\"charset\":\"utf-8\",\"compression\":\"none\",\"maxsize\":\"\",\"codegen_structure_or_data\":\"data\",\"codegen_format\":\"0\",\"csv_separator\":\",\",\"csv_enclosed\":\"\\\"\",\"csv_escaped\":\"\\\"\",\"csv_terminated\":\"AUTO\",\"csv_null\":\"NULL\",\"csv_columns\":\"something\",\"csv_structure_or_data\":\"data\",\"excel_null\":\"NULL\",\"excel_columns\":\"something\",\"excel_edition\":\"win\",\"excel_structure_or_data\":\"data\",\"json_structure_or_data\":\"data\",\"json_unicode\":\"something\",\"latex_caption\":\"something\",\"latex_structure_or_data\":\"structure_and_data\",\"latex_structure_caption\":\"Structure of table @TABLE@\",\"latex_structure_continued_caption\":\"Structure of table @TABLE@ (continued)\",\"latex_structure_label\":\"tab:@TABLE@-structure\",\"latex_relation\":\"something\",\"latex_comments\":\"something\",\"latex_mime\":\"something\",\"latex_columns\":\"something\",\"latex_data_caption\":\"Content of table @TABLE@\",\"latex_data_continued_caption\":\"Content of table @TABLE@ (continued)\",\"latex_data_label\":\"tab:@TABLE@-data\",\"latex_null\":\"\\\\textit{NULL}\",\"mediawiki_structure_or_data\":\"structure_and_data\",\"mediawiki_caption\":\"something\",\"mediawiki_headers\":\"something\",\"htmlword_structure_or_data\":\"structure_and_data\",\"htmlword_null\":\"NULL\",\"ods_null\":\"NULL\",\"ods_structure_or_data\":\"data\",\"odt_structure_or_data\":\"structure_and_data\",\"odt_relation\":\"something\",\"odt_comments\":\"something\",\"odt_mime\":\"something\",\"odt_columns\":\"something\",\"odt_null\":\"NULL\",\"pdf_report_title\":\"\",\"pdf_structure_or_data\":\"structure_and_data\",\"phparray_structure_or_data\":\"data\",\"sql_include_comments\":\"something\",\"sql_header_comment\":\"\",\"sql_use_transaction\":\"something\",\"sql_compatibility\":\"NONE\",\"sql_structure_or_data\":\"structure_and_data\",\"sql_create_table\":\"something\",\"sql_auto_increment\":\"something\",\"sql_create_view\":\"something\",\"sql_procedure_function\":\"something\",\"sql_create_trigger\":\"something\",\"sql_backquotes\":\"something\",\"sql_type\":\"INSERT\",\"sql_insert_syntax\":\"both\",\"sql_max_query_size\":\"50000\",\"sql_hex_for_binary\":\"something\",\"sql_utc_time\":\"something\",\"texytext_structure_or_data\":\"structure_and_data\",\"texytext_null\":\"NULL\",\"xml_structure_or_data\":\"data\",\"xml_export_events\":\"something\",\"xml_export_functions\":\"something\",\"xml_export_procedures\":\"something\",\"xml_export_tables\":\"something\",\"xml_export_triggers\":\"something\",\"xml_export_views\":\"something\",\"xml_export_contents\":\"something\",\"yaml_structure_or_data\":\"data\",\"\":null,\"lock_tables\":null,\"as_separate_files\":null,\"csv_removeCRLF\":null,\"excel_removeCRLF\":null,\"json_pretty_print\":null,\"htmlword_columns\":null,\"ods_columns\":null,\"sql_dates\":null,\"sql_relation\":null,\"sql_mime\":null,\"sql_disable_fk\":null,\"sql_views_as_tables\":null,\"sql_metadata\":null,\"sql_create_database\":null,\"sql_drop_table\":null,\"sql_if_not_exists\":null,\"sql_simple_view_export\":null,\"sql_view_current_user\":null,\"sql_or_replace_view\":null,\"sql_truncate\":null,\"sql_delayed\":null,\"sql_ignore\":null,\"texytext_columns\":null}'),
(2, 'root', 'server', 'itequipmentsystem_DB', '{\"quick_or_custom\":\"quick\",\"what\":\"sql\",\"db_select[]\":[\"asset_management\",\"db_borrow\",\"db_oas\",\"db_salary\",\"equipment_inventory\",\"inventory_db\",\"it_asset_management\",\"it_inventory\",\"phpmyadmin\",\"school_assets\",\"school_inventory\",\"school_it_management\",\"test\"],\"aliases_new\":\"\",\"output_format\":\"sendit\",\"filename_template\":\"@SERVER@\",\"remember_template\":\"on\",\"charset\":\"utf-8\",\"compression\":\"none\",\"maxsize\":\"\",\"codegen_structure_or_data\":\"data\",\"codegen_format\":\"0\",\"csv_separator\":\",\",\"csv_enclosed\":\"\\\"\",\"csv_escaped\":\"\\\"\",\"csv_terminated\":\"AUTO\",\"csv_null\":\"NULL\",\"csv_columns\":\"something\",\"csv_structure_or_data\":\"data\",\"excel_null\":\"NULL\",\"excel_columns\":\"something\",\"excel_edition\":\"win\",\"excel_structure_or_data\":\"data\",\"json_structure_or_data\":\"data\",\"json_unicode\":\"something\",\"latex_caption\":\"something\",\"latex_structure_or_data\":\"structure_and_data\",\"latex_structure_caption\":\"Structure of table @TABLE@\",\"latex_structure_continued_caption\":\"Structure of table @TABLE@ (continued)\",\"latex_structure_label\":\"tab:@TABLE@-structure\",\"latex_relation\":\"something\",\"latex_comments\":\"something\",\"latex_mime\":\"something\",\"latex_columns\":\"something\",\"latex_data_caption\":\"Content of table @TABLE@\",\"latex_data_continued_caption\":\"Content of table @TABLE@ (continued)\",\"latex_data_label\":\"tab:@TABLE@-data\",\"latex_null\":\"\\\\textit{NULL}\",\"mediawiki_structure_or_data\":\"data\",\"mediawiki_caption\":\"something\",\"mediawiki_headers\":\"something\",\"htmlword_structure_or_data\":\"structure_and_data\",\"htmlword_null\":\"NULL\",\"ods_null\":\"NULL\",\"ods_structure_or_data\":\"data\",\"odt_structure_or_data\":\"structure_and_data\",\"odt_relation\":\"something\",\"odt_comments\":\"something\",\"odt_mime\":\"something\",\"odt_columns\":\"something\",\"odt_null\":\"NULL\",\"pdf_report_title\":\"\",\"pdf_structure_or_data\":\"data\",\"phparray_structure_or_data\":\"data\",\"sql_include_comments\":\"something\",\"sql_header_comment\":\"\",\"sql_use_transaction\":\"something\",\"sql_compatibility\":\"NONE\",\"sql_structure_or_data\":\"structure_and_data\",\"sql_create_table\":\"something\",\"sql_auto_increment\":\"something\",\"sql_create_view\":\"something\",\"sql_create_trigger\":\"something\",\"sql_backquotes\":\"something\",\"sql_type\":\"INSERT\",\"sql_insert_syntax\":\"both\",\"sql_max_query_size\":\"50000\",\"sql_hex_for_binary\":\"something\",\"sql_utc_time\":\"something\",\"texytext_structure_or_data\":\"structure_and_data\",\"texytext_null\":\"NULL\",\"yaml_structure_or_data\":\"data\",\"\":null,\"as_separate_files\":null,\"csv_removeCRLF\":null,\"excel_removeCRLF\":null,\"json_pretty_print\":null,\"htmlword_columns\":null,\"ods_columns\":null,\"sql_dates\":null,\"sql_relation\":null,\"sql_mime\":null,\"sql_disable_fk\":null,\"sql_views_as_tables\":null,\"sql_metadata\":null,\"sql_drop_database\":null,\"sql_drop_table\":null,\"sql_if_not_exists\":null,\"sql_simple_view_export\":null,\"sql_view_current_user\":null,\"sql_or_replace_view\":null,\"sql_procedure_function\":null,\"sql_truncate\":null,\"sql_delayed\":null,\"sql_ignore\":null,\"texytext_columns\":null}');

-- --------------------------------------------------------

--
-- Table structure for table `pma__favorite`
--

CREATE TABLE `pma__favorite` (
  `username` varchar(64) NOT NULL,
  `tables` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Favorite tables';

-- --------------------------------------------------------

--
-- Table structure for table `pma__history`
--

CREATE TABLE `pma__history` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL DEFAULT '',
  `db` varchar(64) NOT NULL DEFAULT '',
  `table` varchar(64) NOT NULL DEFAULT '',
  `timevalue` timestamp NOT NULL DEFAULT current_timestamp(),
  `sqlquery` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='SQL history for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__navigationhiding`
--

CREATE TABLE `pma__navigationhiding` (
  `username` varchar(64) NOT NULL,
  `item_name` varchar(64) NOT NULL,
  `item_type` varchar(64) NOT NULL,
  `db_name` varchar(64) NOT NULL,
  `table_name` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Hidden items of navigation tree';

-- --------------------------------------------------------

--
-- Table structure for table `pma__pdf_pages`
--

CREATE TABLE `pma__pdf_pages` (
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `page_nr` int(10) UNSIGNED NOT NULL,
  `page_descr` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='PDF relation pages for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__recent`
--

CREATE TABLE `pma__recent` (
  `username` varchar(64) NOT NULL,
  `tables` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Recently accessed tables';

--
-- Dumping data for table `pma__recent`
--

INSERT INTO `pma__recent` (`username`, `tables`) VALUES
('root', '[{\"db\":\"school_it_management\",\"table\":\"employees\"},{\"db\":\"school_it_management\",\"table\":\"departments\"},{\"db\":\"school_it_management\",\"table\":\"equipment\"},{\"db\":\"school_it_management\",\"table\":\"maintenance\"},{\"db\":\"school_it_management\",\"table\":\"categories\"},{\"db\":\"school_it_management\",\"table\":\"categories_items\"},{\"db\":\"asset_management\",\"table\":\"users\"},{\"db\":\"asset_management\",\"table\":\"maintenances\"},{\"db\":\"asset_management\",\"table\":\"assets\"},{\"db\":\"asset_management\",\"table\":\"categories\"}]');

-- --------------------------------------------------------

--
-- Table structure for table `pma__relation`
--

CREATE TABLE `pma__relation` (
  `master_db` varchar(64) NOT NULL DEFAULT '',
  `master_table` varchar(64) NOT NULL DEFAULT '',
  `master_field` varchar(64) NOT NULL DEFAULT '',
  `foreign_db` varchar(64) NOT NULL DEFAULT '',
  `foreign_table` varchar(64) NOT NULL DEFAULT '',
  `foreign_field` varchar(64) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Relation table';

-- --------------------------------------------------------

--
-- Table structure for table `pma__savedsearches`
--

CREATE TABLE `pma__savedsearches` (
  `id` int(5) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL DEFAULT '',
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `search_name` varchar(64) NOT NULL DEFAULT '',
  `search_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Saved searches';

-- --------------------------------------------------------

--
-- Table structure for table `pma__table_coords`
--

CREATE TABLE `pma__table_coords` (
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `table_name` varchar(64) NOT NULL DEFAULT '',
  `pdf_page_number` int(11) NOT NULL DEFAULT 0,
  `x` float UNSIGNED NOT NULL DEFAULT 0,
  `y` float UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Table coordinates for phpMyAdmin PDF output';

-- --------------------------------------------------------

--
-- Table structure for table `pma__table_info`
--

CREATE TABLE `pma__table_info` (
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `table_name` varchar(64) NOT NULL DEFAULT '',
  `display_field` varchar(64) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Table information for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__table_uiprefs`
--

CREATE TABLE `pma__table_uiprefs` (
  `username` varchar(64) NOT NULL,
  `db_name` varchar(64) NOT NULL,
  `table_name` varchar(64) NOT NULL,
  `prefs` text NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Tables'' UI preferences';

-- --------------------------------------------------------

--
-- Table structure for table `pma__tracking`
--

CREATE TABLE `pma__tracking` (
  `db_name` varchar(64) NOT NULL,
  `table_name` varchar(64) NOT NULL,
  `version` int(10) UNSIGNED NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime NOT NULL,
  `schema_snapshot` text NOT NULL,
  `schema_sql` text DEFAULT NULL,
  `data_sql` longtext DEFAULT NULL,
  `tracking` set('UPDATE','REPLACE','INSERT','DELETE','TRUNCATE','CREATE DATABASE','ALTER DATABASE','DROP DATABASE','CREATE TABLE','ALTER TABLE','RENAME TABLE','DROP TABLE','CREATE INDEX','DROP INDEX','CREATE VIEW','ALTER VIEW','DROP VIEW') DEFAULT NULL,
  `tracking_active` int(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Database changes tracking for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__userconfig`
--

CREATE TABLE `pma__userconfig` (
  `username` varchar(64) NOT NULL,
  `timevalue` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `config_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='User preferences storage for phpMyAdmin';

--
-- Dumping data for table `pma__userconfig`
--

INSERT INTO `pma__userconfig` (`username`, `timevalue`, `config_data`) VALUES
('root', '2025-10-29 13:02:44', '{\"Console\\/Mode\":\"collapse\"}');

-- --------------------------------------------------------

--
-- Table structure for table `pma__usergroups`
--

CREATE TABLE `pma__usergroups` (
  `usergroup` varchar(64) NOT NULL,
  `tab` varchar(64) NOT NULL,
  `allowed` enum('Y','N') NOT NULL DEFAULT 'N'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='User groups with configured menu items';

-- --------------------------------------------------------

--
-- Table structure for table `pma__users`
--

CREATE TABLE `pma__users` (
  `username` varchar(64) NOT NULL,
  `usergroup` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Users and their assignments to user groups';

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pma__bookmark`
--
ALTER TABLE `pma__bookmark`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pma__central_columns`
--
ALTER TABLE `pma__central_columns`
  ADD PRIMARY KEY (`db_name`,`col_name`);

--
-- Indexes for table `pma__column_info`
--
ALTER TABLE `pma__column_info`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `db_name` (`db_name`,`table_name`,`column_name`);

--
-- Indexes for table `pma__designer_settings`
--
ALTER TABLE `pma__designer_settings`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `pma__export_templates`
--
ALTER TABLE `pma__export_templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `u_user_type_template` (`username`,`export_type`,`template_name`);

--
-- Indexes for table `pma__favorite`
--
ALTER TABLE `pma__favorite`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `pma__history`
--
ALTER TABLE `pma__history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `username` (`username`,`db`,`table`,`timevalue`);

--
-- Indexes for table `pma__navigationhiding`
--
ALTER TABLE `pma__navigationhiding`
  ADD PRIMARY KEY (`username`,`item_name`,`item_type`,`db_name`,`table_name`);

--
-- Indexes for table `pma__pdf_pages`
--
ALTER TABLE `pma__pdf_pages`
  ADD PRIMARY KEY (`page_nr`),
  ADD KEY `db_name` (`db_name`);

--
-- Indexes for table `pma__recent`
--
ALTER TABLE `pma__recent`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `pma__relation`
--
ALTER TABLE `pma__relation`
  ADD PRIMARY KEY (`master_db`,`master_table`,`master_field`),
  ADD KEY `foreign_field` (`foreign_db`,`foreign_table`);

--
-- Indexes for table `pma__savedsearches`
--
ALTER TABLE `pma__savedsearches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `u_savedsearches_username_dbname` (`username`,`db_name`,`search_name`);

--
-- Indexes for table `pma__table_coords`
--
ALTER TABLE `pma__table_coords`
  ADD PRIMARY KEY (`db_name`,`table_name`,`pdf_page_number`);

--
-- Indexes for table `pma__table_info`
--
ALTER TABLE `pma__table_info`
  ADD PRIMARY KEY (`db_name`,`table_name`);

--
-- Indexes for table `pma__table_uiprefs`
--
ALTER TABLE `pma__table_uiprefs`
  ADD PRIMARY KEY (`username`,`db_name`,`table_name`);

--
-- Indexes for table `pma__tracking`
--
ALTER TABLE `pma__tracking`
  ADD PRIMARY KEY (`db_name`,`table_name`,`version`);

--
-- Indexes for table `pma__userconfig`
--
ALTER TABLE `pma__userconfig`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `pma__usergroups`
--
ALTER TABLE `pma__usergroups`
  ADD PRIMARY KEY (`usergroup`,`tab`,`allowed`);

--
-- Indexes for table `pma__users`
--
ALTER TABLE `pma__users`
  ADD PRIMARY KEY (`username`,`usergroup`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `pma__bookmark`
--
ALTER TABLE `pma__bookmark`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pma__column_info`
--
ALTER TABLE `pma__column_info`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pma__export_templates`
--
ALTER TABLE `pma__export_templates`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `pma__history`
--
ALTER TABLE `pma__history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pma__pdf_pages`
--
ALTER TABLE `pma__pdf_pages`
  MODIFY `page_nr` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pma__savedsearches`
--
ALTER TABLE `pma__savedsearches`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- Database: `school_assets`
--
CREATE DATABASE IF NOT EXISTS `school_assets` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `school_assets`;

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `id` int(11) NOT NULL,
  `user` varchar(100) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `timestamp` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_log`
--

INSERT INTO `activity_log` (`id`, `user`, `action`, `timestamp`) VALUES
(1, '', 'เพิ่มอุปกรณ์ใหม่: ', '2025-10-22 09:16:28'),
(2, 'ผู้ดูแลระบบ', 'เพิ่มอุปกรณ์ใหม่: ', '2025-10-22 09:17:43'),
(3, 'ผู้ดูแลระบบ', 'เพิ่มอุปกรณ์ใหม่: ', '2025-10-22 09:21:27'),
(4, 'ผู้ดูแลระบบ', 'เพิ่มอุปกรณ์ใหม่: ', '2025-10-22 09:21:46');

-- --------------------------------------------------------

--
-- Table structure for table `devices`
--

CREATE TABLE `devices` (
  `id` int(11) NOT NULL,
  `device_type` varchar(50) DEFAULT NULL,
  `asset_code` varchar(50) DEFAULT NULL,
  `model_name` varchar(100) DEFAULT NULL,
  `serial` varchar(100) DEFAULT NULL,
  `date_received` date DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `responsible_person` varchar(100) DEFAULT NULL,
  `device_condition` varchar(50) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `vendor` varchar(100) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `devices`
--

INSERT INTO `devices` (`id`, `device_type`, `asset_code`, `model_name`, `serial`, `date_received`, `location`, `responsible_person`, `device_condition`, `note`, `price`, `vendor`, `image`, `created_at`) VALUES
(1, '01', 'pc', 'wdaw', '5373', '2025-10-22', 'varee', 'มอส', 'ใหม่', '', 12000.00, 'jib', 'uploads/1761101256_13082021441148PC_2010s.jpg', '2025-10-22 02:47:36');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `fullname` varchar(100) DEFAULT NULL,
  `role` enum('admin','staff','viewer') DEFAULT 'viewer'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `fullname`, `role`) VALUES
(1, 'admin', '64151b73773986d5e1d5ecf45239935a', 'ผู้ดูแลระบบ', 'admin'),
(2, 'teacher', 'e10adc3949ba59abbe56e057f20f883e', 'ครูสมชาย', 'staff'),
(3, 'guest', '084e0343a0486ff05530df6c705c8bb4', 'ผู้เข้าชมทั่วไป', 'viewer');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `devices`
--
ALTER TABLE `devices`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `devices`
--
ALTER TABLE `devices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- Database: `school_inventory`
--
CREATE DATABASE IF NOT EXISTS `school_inventory` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `school_inventory`;

-- --------------------------------------------------------

--
-- Table structure for table `assets`
--

CREATE TABLE `assets` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `category_id` int(11) NOT NULL,
  `price` decimal(10,2) DEFAULT 0.00,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','staff','viewer') DEFAULT 'viewer'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `assets`
--
ALTER TABLE `assets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `assets`
--
ALTER TABLE `assets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `assets`
--
ALTER TABLE `assets`
  ADD CONSTRAINT `assets_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);
--
-- Database: `school_it_management`
--
CREATE DATABASE IF NOT EXISTS `school_it_management` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `school_it_management`;

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
  `status` enum('อุปกรณ์ใหม่','อุปกรณ์เดิม','อุปกรณ์ใหม่-ซ่อมเสร็จ','อุปกรณ์เดิม-ซ่อมเสร็จ','รอซ่อม','กำลังซ่อม','ใช้งานปกติ','ชำรุด','ซ่อมเสร็จแล้ว','ส่งคืนแล้ว','จำหน่ายแล้ว') NOT NULL DEFAULT 'อุปกรณ์เดิม',
  `specifications` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `equipment`
--

INSERT INTO `equipment` (`id`, `code`, `name`, `category_id`, `category_item_id`, `brand`, `model`, `serial_number`, `purchase_date`, `purchase_price`, `department_id`, `responsible_person`, `status`, `specifications`, `image`, `created_at`, `updated_at`) VALUES
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
  `status` enum('รอซ่อม','กำลังดำเนินการ','ซ่อมเสร็จ','ยกเลิก','จำหน่าย') NOT NULL DEFAULT 'รอซ่อม',
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

INSERT INTO `maintenance` (`id`, `equipment_id`, `report_date`, `problem_description`, `reported_by`, `assigned_technician`, `cost`, `status`, `solution_description`, `completed_date`, `school_name`, `building_name`, `floor_name`, `room_name`, `created_at`, `updated_at`) VALUES
(1, 4, '2025-10-29', 'เสีย', 'มอส', 'มอส', 0.00, 'ซ่อมเสร็จ', '', '2025-10-29', 'โรงเรียนวารีเชียงใหม่', 'ตึก1-อำนวยการ', 'ชั้น 1', 'ห้อง 101', '2025-10-29 01:34:23', '2025-10-29 01:38:07'),
(2, 1, '2025-10-29', 'เสีย', 'มอส', 'มอสส', 0.00, 'ซ่อมเสร็จ', '', '0000-00-00', 'โรงเรียนอนุบาลวารีเชียงใหม่', 'ตึก1-อำนวยการ', 'ชั้น 1', 'ห้อง 102', '2025-10-29 02:00:48', '2025-10-29 08:07:41'),
(3, 5, '2025-10-29', 'เปิดไม่ติด', 'มอส', 'มอส', 0.00, 'ซ่อมเสร็จ', '', '0000-00-00', 'โรงเรียนวารีเชียงใหม่', 'ตึก1-อำนวยการ', 'ชั้น 2', 'ห้องคอมพิวเตอร์', '2025-10-29 02:23:15', '2025-10-29 08:05:24'),
(4, 2, '2025-10-29', 'error', 'moss', 'moss', 0.00, 'ซ่อมเสร็จ', '', '0000-00-00', 'โรงเรียนนานาชาติวารีเชียงใหม่', 'ตึก9', 'ชั้น 2', 'ห้องคอมพิวเตอร์', '2025-10-29 03:06:21', '2025-10-29 03:06:44'),
(5, 3, '2025-10-29', 'ไกฟไก', 'moss', 'ทนหห', 0.00, 'ซ่อมเสร็จ', '', '0000-00-00', 'โรงเรียนอนุบาลวารีเชียงใหม่', 'ตึก1-อำนวยการ', 'ชั้น 1', 'ห้อง 102', '2025-10-29 03:12:32', '2025-10-29 03:30:51'),
(6, 2, '2025-10-29', 'จอเสีย', 'มอส', 'มอส', 0.00, 'ซ่อมเสร็จ', '', '0000-00-00', 'โรงเรียนอนุบาลวารีเชียงใหม่', 'ตึก1-อำนวยการ', 'ชั้น 2', 'ห้องปฏิบัติการวิทยาศาสตร์', '2025-10-29 06:09:32', '2025-10-29 07:51:02'),
(7, 6, '2025-10-29', 'ค้างหน้า logo', 'moss', 'mosss', 0.00, 'ซ่อมเสร็จ', '', '0000-00-00', 'โรงเรียนนานาชาติวารีเชียงใหม่', 'ตึก9', 'ชั้น 2', 'ห้องสำนักงาน', '2025-10-29 08:10:11', '2025-10-29 08:25:15');

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
--
-- Database: `test`
--
CREATE DATABASE IF NOT EXISTS `test` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `test`;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
