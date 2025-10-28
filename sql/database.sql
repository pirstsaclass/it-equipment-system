-- สร้างฐานข้อมูล
CREATE DATABASE IF NOT EXISTS school_it_management;
USE school_it_management;

-- ตารางแผนก
CREATE TABLE departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    building VARCHAR(100),
    floor VARCHAR(50),
    room VARCHAR(100),
    type ENUM('อำนวยการ', 'อนุบาล', 'ประถม', 'มัธยม'),
    responsible_person VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ตารางประเภทครุภัณฑ์
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ตารางครุภัณฑ์
CREATE TABLE equipment (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    category_id INT,
    brand VARCHAR(100),
    model VARCHAR(100),
    serial_number VARCHAR(100),
    purchase_date DATE,
    purchase_price DECIMAL(10,2),
    department_id INT,
    responsible_person VARCHAR(255),
    status ENUM('ใหม่', 'ใช้งานปกติ', 'ชำรุด', 'รอซ่อม', 'จำหน่ายแล้ว') DEFAULT 'ใหม่',
    specifications TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id),
    FOREIGN KEY (department_id) REFERENCES departments(id)
);

-- ตารางการซ่อมบำรุง
CREATE TABLE maintenance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    equipment_id INT,
    report_date DATE NOT NULL,
    problem_description TEXT NOT NULL,
    reported_by VARCHAR(255),
    assigned_technician VARCHAR(255),
    cost DECIMAL(10,2) DEFAULT 0,
    status ENUM('รอซ่อม', 'กำลังดำเนินการ', 'ซ่อมเสร็จ', 'ยกเลิก') DEFAULT 'รอซ่อม',
    solution_description TEXT,
    completed_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (equipment_id) REFERENCES equipment(id)
);

-- ตารางพนักงาน
CREATE TABLE employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_code VARCHAR(50) UNIQUE NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    department_id INT,
    position VARCHAR(100),
    phone VARCHAR(20),
    email VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(id)
);

-- ตารางผู้ใช้ระบบ
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    employee_id INT,
    role ENUM('admin', 'user', 'viewer') DEFAULT 'user',
    is_active BOOLEAN DEFAULT TRUE,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id)
);

-- ข้อมูลตัวอย่าง
INSERT INTO departments (name, building, floor, room, type, responsible_person) VALUES
('ฝ่ายอำนวยการ', 'อาคาร 1', '2', '201', 'อำนวยการ', 'นางสาวสมหญิง ใจดี'),
('ระดับอนุบาล', 'อาคาร 2', '1', '101', 'อนุบาล', 'นางสาววรรณา สุขใจ'),
('ระดับประถม', 'อาคาร 3', '1', '102', 'ประถม', 'นายธีรภัทร กล้าหาญ'),
('ระดับมัธยม', 'อาคาร 4', '2', '202', 'มัธยม', 'นายสมชาย ใจกว้าง');

-- หากมีตาราง departments อยู่แล้ว ให้เพิ่มคอลัมน์ school
ALTER TABLE departments ADD COLUMN school VARCHAR(255) AFTER id;

-- หรือสร้างตารางใหม่
CREATE TABLE departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    school VARCHAR(255) NOT NULL,
    building VARCHAR(255) NOT NULL,
    floor VARCHAR(255) NOT NULL,
    room VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    type VARCHAR(255) NOT NULL,
    responsible_person VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ตัวอย่างข้อมูล
INSERT INTO departments (school, building, floor, room, name, type, responsible_person) VALUES
('โรงเรียนวารีเชียงใหม่', 'ตึก1-อำนวยการ', 'ชั้น 1', '101', 'ฝ่ายบริหาร', 'อำนวยการ', 'ผู้บริหาร'),
('โรงเรียนวารีเชียงใหม่', 'ตึก3-ประถม', 'ชั้น 2', '201', 'ห้องเรียนประถมปีที่ 1', 'ประถม', 'ครูสมศรี'),
('โรงเรียนอนุบาลวารีเชียงใหม่', 'ตึก1-อำนวยการ', 'ชั้น 1', '102', 'ฝ่ายธุรการ', 'อำนวยการ', 'เจ้าหน้าที่ธุรการ'),
('โรงเรียนนานาชาติวารีเชียงใหม่', 'ตึก8', 'ชั้น 3', '301', 'ห้องวิทยาศาสตร์', 'มัธยม', 'ครูต่างชาติ');

INSERT INTO categories (name, description) VALUES
('คอมพิวเตอร์', 'คอมพิวเตอร์ตั้งโต๊ะและอุปกรณ์เกี่ยวข้อง'),
('โน๊ตบุ๊ค', 'คอมพิวเตอร์พกพา'),
('แท็บเล็ต', 'อุปกรณ์แท็บเล็ต'),
('เครื่องพิมพ์', 'เครื่องพิมพ์และเครื่องสแกนเนอร์'),
('โปรเจคเตอร์', 'เครื่องฉายภาพและมัลติมีเดีย'),
('เครือข่าย', 'อุปกรณ์เครือข่ายและอินเทอร์เน็ต');

INSERT INTO employees (employee_code, first_name, last_name, department_id, position, phone, email) VALUES
('EMP001', 'สมชาย', 'ใจดี', 1, 'ผู้ดูแลระบบ', '081-234-5678', 'somchai@school.ac.th'),
('EMP002', 'สมหญิง', 'กล้าหาญ', 2, 'ครูใหญ่', '082-345-6789', 'somying@school.ac.th'),
('EMP003', 'ธีรภัทร', 'สุขใจ', 3, 'หัวหน้าแผนก', '083-456-7890', 'theeraphat@school.ac.th');

INSERT INTO users (username, password, employee_id, role) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 'admin'),
('user1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, 'user'),
('user2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, 'user');

INSERT INTO equipment (code, name, category_id, brand, model, serial_number, purchase_date, purchase_price, department_id, responsible_person, status) VALUES
('IT-2023-0001', 'คอมพิวเตอร์ตั้งโต๊ะ Dell', 1, 'Dell', 'OptiPlex 3090', 'SN001', '2023-01-15', 25000.00, 1, 'สมชาย ใจดี', 'ใช้งานปกติ'),
('IT-2023-0002', 'โน๊ตบุ๊ค Lenovo', 2, 'Lenovo', 'ThinkPad X1', 'SN002', '2023-02-20', 35000.00, 2, 'สมหญิง กล้าหาญ', 'ใหม่'),
('IT-2023-0003', 'เครื่องพิมพ์ HP', 4, 'HP', 'LaserJet Pro', 'SN003', '2023-03-10', 8000.00, 3, 'ธีรภัทร สุขใจ', 'รอซ่อม');

INSERT INTO maintenance (equipment_id, report_date, problem_description, reported_by, assigned_technician, status, cost) VALUES
(3, '2023-10-01', 'เครื่องพิมพ์ไม่ทำงาน กระดาษติด', 'ธีรภัทร สุขใจ', 'สมชาย ใจดี', 'รอซ่อม', 1500.00);



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