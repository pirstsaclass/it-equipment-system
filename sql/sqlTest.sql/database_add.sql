-- สร้างตารางหมวดหมู่
CREATE TABLE IF NOT EXISTS equipment_categories (
    category_id VARCHAR(2) PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL
);

-- สร้างตารางรายการอุปกรณ์
CREATE TABLE IF NOT EXISTS categories_items (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    category_id VARCHAR(2),
    item_name VARCHAR(200) NOT NULL,
    FOREIGN KEY (category_id) REFERENCES equipment_categories(category_id)
);

-- สร้างตารางหมวดหมู่
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id VARCHAR(2) PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL
);

-- สร้างตารางรายการอุปกรณ์
CREATE TABLE categories_items (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    category_id VARCHAR(2),
    item_name VARCHAR(200) NOT NULL,
    FOREIGN KEY (category_id) REFERENCES categories(category_id)
);
-- ล้างข้อมูลเดิม (ถ้ามี)
DELETE FROM categories;
DELETE FROM categories_items;

-- เพิ่มข้อมูลหมวดหมู่
INSERT INTO equipment_categories (category_id, category_name) VALUES
('01', 'อุปกรณ์คอมพิวเตอร์'),
('02', 'อุปกรณ์เครือข่าย'),
('03', 'อุปกรณ์สำนักงาน / ต่อพ่วง'),
('04', 'อุปกรณ์สื่อการเรียนการสอน'),
('05', 'อะไหล่ / ชิ้นส่วนคอมพิวเตอร์'),
('06', 'ระบบรักษาความปลอดภัย'),
('07', 'อุปกรณ์อิเล็กทรอนิกส์ทั่วไป'),
('08', 'อุปกรณ์ซ่อมบำรุง / เครื่องมือ'),
('09', 'ซอฟต์แวร์และใบอนุญาต'),
('10', 'อื่น ๆ');

-- เพิ่มข้อมูลอุปกรณ์ทั้งหมด
-- 01
INSERT INTO equipment_items (category_id, item_name) VALUES
('01', 'เครื่องคอมพิวเตอร์ตั้งโต๊ะ (PC)'),
('01', 'เครื่องคอมพิวเตอร์โน้ตบุ๊ก (Notebook)'),
('01', 'เครื่องคอมพิวเตอร์ All-in-One'),
('01', 'จอภาพ (Monitor)'),
('01', 'เครื่องสำรองไฟ (UPS)'),
('01', 'คีย์บอร์ด (Keyboard)'),
('01', 'เมาส์ (Mouse)');

-- 02
INSERT INTO equipment_items (category_id, item_name) VALUES
('02', 'เราเตอร์ (Router)'),
('02', 'สวิตช์ (Switch)'),
('02', 'แอคเซสพอยต์ (Access Point)'),
('02', 'โมเด็ม (Modem)'),
('02', 'สายแลน (LAN Cable)'),
('02', 'ตู้แร็ก (Rack Cabinet)');

-- 03
INSERT INTO equipment_items (category_id, item_name) VALUES
('03', 'เครื่องพิมพ์ (Printer)'),
('03', 'เครื่องสแกน (Scanner)'),
('03', 'เครื่องฉายโปรเจกเตอร์ (Projector)'),
('03', 'เครื่องถ่ายเอกสาร (Photocopier)'),
('03', 'เครื่องฉายภาพ (Visual Presenter)'),
('03', 'เครื่องเสียง (Sound System)');

-- 04
INSERT INTO equipment_items (category_id, item_name) VALUES
('04', 'กระดานอัจฉริยะ (Smart Board)'),
('04', 'ชุดคอมพิวเตอร์ห้องเรียน'),
('04', 'แท็บเล็ต (Tablet)'),
('04', 'เครื่องเสียงห้องเรียน'),
('04', 'ชุดสื่อมัลติมีเดีย');

-- 05
INSERT INTO equipment_items (category_id, item_name) VALUES
('05', 'ฮาร์ดดิสก์ (HDD)'),
('05', 'สถานะ固态 (SSD)'),
('05', 'แรม (RAM)'),
('05', 'การ์ดจอ (Graphics Card)'),
('05', 'เพาเวอร์ซัพพลาย (Power Supply)'),
('05', 'อแดปเตอร์ (Adapter)');

-- 06
INSERT INTO equipment_items (category_id, item_name) VALUES
('06', 'กล้องวงจรปิด (CCTV)'),
('06', 'เครื่องบันทึกภาพ (NVR / DVR)'),
('06', 'จอมอนิเตอร์สำหรับดูภาพ'),
('06', 'เครื่องสแกนลายนิ้วมือ'),
('06', 'เครื่องอ่านบัตร (Card Reader)');

-- 07
INSERT INTO equipment_items (category_id, item_name) VALUES
('07', 'โทรทัศน์ (TV)'),
('07', 'เครื่องเล่นดีวีดี (DVD Player)'),
('07', 'ลำโพง (Speaker)'),
('07', 'เครื่องขยายเสียง (Amplifier)'),
('07', 'จานดาวเทียม (Satellite Dish)');

-- 08
INSERT INTO equipment_items (category_id, item_name) VALUES
('08', 'เครื่องมือวัดไฟ (Multimeter)'),
('08', 'เครื่องมือบัดกรี (Soldering Iron)'),
('08', 'อุปกรณ์ตรวจสอบระบบ (Diagnostic Tools)');

-- 09
INSERT INTO equipment_items (category_id, item_name) VALUES
('09', 'ระบบปฏิบัติการ Windows'),
('09', 'Microsoft Office'),
('09', 'โปรแกรมแอนตี้ไวรัส (Antivirus)'),
('09', 'โปรแกรมสื่อการสอน'),
('09', 'ลิขสิทธิ์ (License Key)');

-- 10
INSERT INTO equipment_items (category_id, item_name) VALUES
('10', 'อุปกรณ์ที่ไม่เข้าพวกข้างต้น'),
('10', 'ของทดลอง / ชุดทดลอง'),
('10', 'ชุดทดลองหุ่นยนต์ (Robot Kit)'),
('10', 'ชุดอุปกรณ์ IoT (IoT Kit)');



-- schools.sql
CREATE DATABASE IF NOT EXISTS school_management;
USE school_management;

-- ตารางโรงเรียน
CREATE TABLE schools (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ตารางตึก
CREATE TABLE buildings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    school_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    description VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (school_id) REFERENCES schools(id) ON DELETE CASCADE
);

-- ตารางชั้น
CREATE TABLE floors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    building_id INT NOT NULL,
    floor_number VARCHAR(10) NOT NULL,
    floor_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (building_id) REFERENCES buildings(id) ON DELETE CASCADE
);

-- ตารางห้อง
CREATE TABLE rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    floor_id INT NOT NULL,
    room_number VARCHAR(20) NOT NULL,
    room_name VARCHAR(255) NOT NULL,
    department_type ENUM('อำนวยการ', 'อนุบาล', 'ประถม', 'มัธยม', 'สนับสนุน') NOT NULL,
    responsible_person VARCHAR(255),
    capacity INT,
    status ENUM('ใช้งาน', 'ปิดปรับปรุง', 'ยกเลิก') DEFAULT 'ใช้งาน',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (floor_id) REFERENCES floors(id) ON DELETE CASCADE
);

-- เพิ่มข้อมูลโรงเรียน
INSERT INTO schools (name) VALUES 
('โรงเรียนวารีเชียงใหม่'),
('โรงเรียนอนุบาลวารีเชียงใหม่'),
('โรงเรียนนานาชาติวารีเชียงใหม่');

-- เพิ่มข้อมูลตึกสำหรับโรงเรียนวารีเชียงใหม่
INSERT INTO buildings (school_id, name, description) VALUES 
(1, 'ตึก1-อำนวยการ', 'อาคารอำนวยการ'),
(1, 'ตึก3-ประถม', 'อาคารประถมศึกษา'),
(1, 'ตึก4-ประถม', 'อาคารประถมศึกษา'),
(1, 'ตึก4-มัธยม', 'อาคารมัธยมศึกษา'),
(1, 'ตึก5-อนุบาล', 'อาคารอนุบาล'),
(1, 'ตึก6', 'อาคารอเนกประสงค์'),
(1, 'ตึก7-มัธยม', 'อาคารมัธยมศึกษา'),
(1, 'ตึก10', 'อาคารอเนกประสงค์');

-- เพิ่มข้อมูลตึกสำหรับโรงเรียนอนุบาลวารีเชียงใหม่
INSERT INTO buildings (school_id, name, description) VALUES 
(2, 'ตึก1-อำนวยการ', 'อาคารอำนวยการ'),
(2, 'ตึก6', 'อาคารอเนกประสงค์');

-- เพิ่มข้อมูลตึกสำหรับโรงเรียนนานาชาติวารีเชียงใหม่
INSERT INTO buildings (school_id, name, description) VALUES 
(3, 'ตึก8', 'อาคารเรียนนานาชาติ'),
(3, 'ตึก9', 'อาคารเรียนนานาชาติ'),
(3, 'ตึก10', 'อาคารเรียนนานาชาติ');

-- เพิ่มข้อมูลชั้นสำหรับตึก1-อำนวยการ (โรงเรียนวารีเชียงใหม่)
INSERT INTO floors (building_id, floor_number, floor_name) VALUES 
(1, '1', 'ชั้น 1'),
(1, '2', 'ชั้น 2');

-- เพิ่มข้อมูลชั้นสำหรับตึก3-ประถม (โรงเรียนวารีเชียงใหม่)
INSERT INTO floors (building_id, floor_number, floor_name) VALUES 
(2, '1', 'ชั้น 1'),
(2, '2', 'ชั้น 2'),
(2, '3', 'ชั้น 3'),
(2, '4', 'ชั้น 4');

-- เพิ่มข้อมูลชั้นสำหรับตึก4-ประถม (โรงเรียนวารีเชียงใหม่)
INSERT INTO floors (building_id, floor_number, floor_name) VALUES 
(3, '1', 'ชั้น 1'),
(3, '2', 'ชั้น 2'),
(3, '3', 'ชั้น 3');

-- เพิ่มข้อมูลชั้นสำหรับตึก4-มัธยม (โรงเรียนวารีเชียงใหม่)
INSERT INTO floors (building_id, floor_number, floor_name) VALUES 
(4, '3', 'ชั้น 3'),
(4, '4', 'ชั้น 4'),
(4, '5', 'ชั้น 5');

-- เพิ่มข้อมูลชั้นสำหรับตึก5-อนุบาล (โรงเรียนวารีเชียงใหม่)
INSERT INTO floors (building_id, floor_number, floor_name) VALUES 
(5, '1', 'ชั้น 1'),
(5, '2', 'ชั้น 2');

-- เพิ่มข้อมูลชั้นสำหรับตึก6 (โรงเรียนวารีเชียงใหม่)
INSERT INTO floors (building_id, floor_number, floor_name) VALUES 
(6, '1', 'ชั้น 1'),
(6, '2', 'ชั้น 2');

-- เพิ่มข้อมูลชั้นสำหรับตึก7-มัธยม (โรงเรียนวารีเชียงใหม่)
INSERT INTO floors (building_id, floor_number, floor_name) VALUES 
(7, '1', 'ชั้น 1'),
(7, '2', 'ชั้น 2'),
(7, '3', 'ชั้น 3'),
(7, '4', 'ชั้น 4'),
(7, '5', 'ชั้น 5'),
(7, '6', 'ชั้น 6'),
(7, '7', 'ชั้น 7');

-- เพิ่มข้อมูลชั้นสำหรับตึก10 (โรงเรียนวารีเชียงใหม่)
INSERT INTO floors (building_id, floor_number, floor_name) VALUES 
(8, '1', 'ชั้น 1'),
(8, '2', 'ชั้น 2');

-- เพิ่มข้อมูลชั้นสำหรับตึก1-อำนวยการ (โรงเรียนอนุบาลวารีเชียงใหม่)
INSERT INTO floors (building_id, floor_number, floor_name) VALUES 
(9, '1', 'ชั้น 1'),
(9, '2', 'ชั้น 2');

-- เพิ่มข้อมูลชั้นสำหรับตึก6 (โรงเรียนอนุบาลวารีเชียงใหม่)
INSERT INTO floors (building_id, floor_number, floor_name) VALUES 
(10, '1', 'ชั้น 1'),
(10, '2', 'ชั้น 2');

-- เพิ่มข้อมูลชั้นสำหรับตึก8 (โรงเรียนนานาชาติวารีเชียงใหม่)
INSERT INTO floors (building_id, floor_number, floor_name) VALUES 
(11, '1', 'ชั้น 1'),
(11, '2', 'ชั้น 2'),
(11, '3', 'ชั้น 3'),
(11, '4', 'ชั้น 4');

-- เพิ่มข้อมูลชั้นสำหรับตึก9 (โรงเรียนนานาชาติวารีเชียงใหม่)
INSERT INTO floors (building_id, floor_number, floor_name) VALUES 
(12, '1', 'ชั้น 1'),
(12, '2', 'ชั้น 2'),
(12, '3', 'ชั้น 3');

-- เพิ่มข้อมูลชั้นสำหรับตึก10 (โรงเรียนนานาชาติวารีเชียงใหม่)
INSERT INTO floors (building_id, floor_number, floor_name) VALUES 
(13, '1', 'ชั้น 1'),
(13, '2', 'ชั้น 2');

-- เพิ่มข้อมูลห้องสำหรับตึก1-อำนวยการ ชั้น1 (โรงเรียนวารีเชียงใหม่)
INSERT INTO rooms (floor_id, room_number, room_name, department_type, responsible_person, capacity) VALUES 
(1, '101', 'ห้องผู้อำนวยการ', 'อำนวยการ', 'ผู้บริหาร', 5),
(1, '102', 'ห้องรองผู้อำนวยการ', 'อำนวยการ', 'รองผู้อำนวยการ', 4),
(1, '103', 'ห้องธุรการ', 'อำนวยการ', 'เจ้าหน้าที่ธุรการ', 8),
(1, '104', 'ห้องประชุม', 'อำนวยการ', '', 20),
(1, '105', 'ห้องพักครู', 'สนับสนุน', '', 15),
(1, '106', 'ห้องพยาบาล', 'สนับสนุน', 'พยาบาลโรงเรียน', 6),
(1, '107', 'ห้องสวัสดิการ', 'สนับสนุน', '', 10);

-- เพิ่มข้อมูลห้องสำหรับตึก1-อำนวยการ ชั้น2 (โรงเรียนวารีเชียงใหม่)
INSERT INTO rooms (floor_id, room_number, room_name, department_type, responsible_person, capacity) VALUES 
(2, '201', 'ห้องบัญชี', 'อำนวยการ', 'เจ้าหน้าที่บัญชี', 6),
(2, '202', 'ห้องบุคคล', 'อำนวยการ', 'เจ้าหน้าที่บุคคล', 4),
(2, '203', 'ห้องวิชาการ', 'อำนวยการ', 'หัวหน้าวิชาการ', 8),
(2, '204', 'ห้องกิจกรรม', 'สนับสนุน', '', 25),
(2, '205', 'ห้องพักครู', 'สนับสนุน', '', 12),
(2, '206', 'ห้องสื่อการสอน', 'สนับสนุน', '', 10),
(2, '207', 'ห้องเก็บเอกสาร', 'สนับสนุน', '', 8);

-- เพิ่มข้อมูลห้องสำหรับตึก3-ประถม ชั้น1 (โรงเรียนวารีเชียงใหม่)
INSERT INTO rooms (floor_id, room_number, room_name, department_type, responsible_person, capacity) VALUES 
(3, '301', 'ป.1/1', 'ประถม', 'ครูสมศรี', 30),
(3, '302', 'ป.1/2', 'ประถม', 'ครูวรรณา', 30),
(3, '303', 'ป.1/3', 'ประถม', 'ครูมาลี', 30),
(3, '304', 'ป.1/4', 'ประถม', 'ครูเพชร', 30),
(3, '305', 'ห้องพักครูประถม', 'สนับสนุน', '', 15),
(3, '306', 'ห้องสมุดประถม', 'สนับสนุน', 'บรรณารักษ์', 40);

-- เพิ่มข้อมูลห้องสำหรับตึก5-อนุบาล ชั้น1 (โรงเรียนวารีเชียงใหม่)
INSERT INTO rooms (floor_id, room_number, room_name, department_type, responsible_person, capacity) VALUES 
(9, '501', 'อนุบาล 1/1', 'อนุบาล', 'ครูน้อย', 25),
(9, '502', 'อนุบาล 1/2', 'อนุบาล', 'ครูแดง', 25),
(9, '503', 'อนุบาล 1/3', 'อนุบาล', 'ครูใหญ่', 25),
(9, '504', 'ห้องนอน', 'อนุบาล', '', 30),
(9, '505', 'ห้องเล่น', 'อนุบาล', '', 35),
(9, '506', 'ห้องอาหาร', 'อนุบาล', '', 40);

-- เพิ่มข้อมูลห้องสำหรับตึก7-มัธยม ชั้น1 (โรงเรียนวารีเชียงใหม่)
INSERT INTO rooms (floor_id, room_number, room_name, department_type, responsible_person, capacity) VALUES 
(15, '701', 'ม.1/1', 'มัธยม', 'ครูใหญ่', 35),
(15, '702', 'ม.1/2', 'มัธยม', 'ครูเล็ก', 35),
(15, '703', 'ม.1/3', 'มัธยม', 'ครูสมชาย', 35),
(15, '704', 'ห้องวิทยาศาสตร์', 'มัธยม', 'ครูวิทยาศาสตร์', 30),
(15, '705', 'ห้องพักครู', 'สนับสนุน', '', 15);
