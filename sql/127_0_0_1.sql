-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 29, 2025 at 02:00 PM
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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
