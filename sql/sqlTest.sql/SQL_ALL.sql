-- สร้างฐานข้อมูล
CREATE DATABASE IF NOT EXISTS equipment_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE equipment_management;

-- ตาราง users (ผู้ใช้งานระบบ)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    role ENUM('admin', 'user', 'technician') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ตาราง departments (แผนก)
CREATE TABLE IF NOT EXISTS departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ตาราง employees (พนักงาน)
CREATE TABLE IF NOT EXISTS employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_code VARCHAR(20) UNIQUE,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    department_id INT,
    position VARCHAR(100),
    email VARCHAR(100),
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL,
    INDEX idx_employee_code (employee_code),
    INDEX idx_department (department_id),
    INDEX idx_name (first_name, last_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ตาราง categories (หมวดหมู่ครุภัณฑ์)
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ตาราง categories_items (รายการย่อยของหมวดหมู่)
CREATE TABLE IF NOT EXISTS categories_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
    INDEX idx_category (category_id),
    INDEX idx_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ตาราง equipment (ครุภัณฑ์)
CREATE TABLE IF NOT EXISTS equipment (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE COMMENT 'รหัสครุภัณฑ์',
    name VARCHAR(200) NOT NULL COMMENT 'ชื่อครุภัณฑ์',
    category_id INT COMMENT 'หมวดหมู่',
    category_item_id INT COMMENT 'รายการย่อย',
    brand VARCHAR(100) COMMENT 'ยี่ห้อ',
    model VARCHAR(100) COMMENT 'รุ่น',
    serial_number VARCHAR(100) COMMENT 'Serial Number',
    purchase_date DATE COMMENT 'วันที่จัดซื้อ',
    warranty_expiry DATE COMMENT 'วันที่หมดประกัน',
    price DECIMAL(10,2) DEFAULT 0 COMMENT 'ราคา',
    supplier VARCHAR(200) COMMENT 'ผู้จัดจำหน่าย',
    equipment_status VARCHAR(50) DEFAULT 'อุปกรณ์ใหม่' COMMENT 'สถานะครุภัณฑ์',
    school_name VARCHAR(200) COMMENT 'โรงเรียน',
    building_name VARCHAR(100) COMMENT 'ตึก/อาคาร',
    floor_name VARCHAR(50) COMMENT 'ชั้น',
    room_name VARCHAR(100) COMMENT 'ห้อง/เลขที่',
    responsible_person VARCHAR(100) COMMENT 'ผู้รับผิดชอบ',
    notes TEXT COMMENT 'หมายเหตุ',
    image_path VARCHAR(255) COMMENT 'รูปภาพ',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (category_item_id) REFERENCES categories_items(id) ON DELETE SET NULL,
    INDEX idx_code (code),
    INDEX idx_category (category_id),
    INDEX idx_status (equipment_status),
    INDEX idx_location (school_name, building_name, floor_name, room_name),
    INDEX idx_purchase_date (purchase_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ตาราง maintenance (การซ่อมบำรุง)
CREATE TABLE IF NOT EXISTS maintenance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    repair_code VARCHAR(20) NOT NULL UNIQUE COMMENT 'รหัสการซ่อม',
    equipment_id INT NOT NULL COMMENT 'รหัสครุภัณฑ์',
    report_date DATE NOT NULL COMMENT 'วันที่แจ้งซ่อม',
    problem_description TEXT NOT NULL COMMENT 'ปัญหาที่พบ',
    reported_by VARCHAR(100) NOT NULL COMMENT 'ผู้แจ้งซ่อม',
    assigned_technician VARCHAR(100) COMMENT 'ผู้ดำเนินการซ่อม',
    repair_status VARCHAR(50) DEFAULT 'รอซ่อม' COMMENT 'สถานะการซ่อม',
    solution_description TEXT COMMENT 'วิธีการแก้ไข',
    cost DECIMAL(10,2) DEFAULT 0 COMMENT 'ค่าใช้จ่าย',
    completed_date DATE COMMENT 'วันที่ซ่อมเสร็จ',
    school_name VARCHAR(200) COMMENT 'โรงเรียน',
    building_name VARCHAR(100) COMMENT 'ตึก/อาคาร',
    floor_name VARCHAR(50) COMMENT 'ชั้น',
    room_name VARCHAR(100) COMMENT 'ห้อง/เลขที่',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (equipment_id) REFERENCES equipment(id) ON DELETE CASCADE,
    INDEX idx_repair_code (repair_code),
    INDEX idx_equipment (equipment_id),
    INDEX idx_status (repair_status),
    INDEX idx_report_date (report_date),
    INDEX idx_location (school_name, building_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ตาราง maintenance_history (ประวัติการซ่อม - สำหรับเก็บประวัติทั้งหมด)
CREATE TABLE IF NOT EXISTS maintenance_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    maintenance_id INT NOT NULL,
    status_changed_from VARCHAR(50),
    status_changed_to VARCHAR(50),
    changed_by VARCHAR(100),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (maintenance_id) REFERENCES maintenance(id) ON DELETE CASCADE,
    INDEX idx_maintenance (maintenance_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ตาราง equipment_transfers (การโอนย้ายครุภัณฑ์)
CREATE TABLE IF NOT EXISTS equipment_transfers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    equipment_id INT NOT NULL,
    from_school VARCHAR(200),
    from_building VARCHAR(100),
    from_floor VARCHAR(50),
    from_room VARCHAR(100),
    to_school VARCHAR(200),
    to_building VARCHAR(100),
    to_floor VARCHAR(50),
    to_room VARCHAR(100),
    transfer_date DATE NOT NULL,
    transferred_by VARCHAR(100),
    reason TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (equipment_id) REFERENCES equipment(id) ON DELETE CASCADE,
    INDEX idx_equipment (equipment_id),
    INDEX idx_transfer_date (transfer_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ตาราง equipment_disposal (การจำหน่ายครุภัณฑ์)
CREATE TABLE IF NOT EXISTS equipment_disposal (
    id INT AUTO_INCREMENT PRIMARY KEY,
    equipment_id INT NOT NULL,
    disposal_date DATE NOT NULL,
    disposal_method VARCHAR(100) COMMENT 'วิธีการจำหน่าย เช่น ขาย, บริจาค, ทำลาย',
    disposal_value DECIMAL(10,2) DEFAULT 0 COMMENT 'มูลค่าที่จำหน่าย',
    reason TEXT COMMENT 'เหตุผลในการจำหน่าย',
    approved_by VARCHAR(100) COMMENT 'ผู้อนุมัติ',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (equipment_id) REFERENCES equipment(id) ON DELETE CASCADE,
    INDEX idx_equipment (equipment_id),
    INDEX idx_disposal_date (disposal_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ข้อมูลตัวอย่าง

-- เพิ่ม admin user (password: admin123)
INSERT INTO users (username, password, full_name, email, role) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ผู้ดูแลระบบ', 'admin@varee.ac.th', 'admin');

-- เพิ่มแผนก
INSERT INTO departments (name, description) VALUES
('อำนวยการ', 'ฝ่ายอำนวยการ'),
('อนุบาล', 'แผนกอนุบาล'),
('ประถมศึกษา', 'แผนกประถมศึกษา'),
('มัธยมศึกษา', 'แผนกมัธยมศึกษา'),
('สนับสนุน', 'ฝ่ายสนับสนุน'),
('IT', 'ฝ่ายเทคโนโลยีสารสนเทศ');

-- เพิ่มหมวดหมู่ครุภัณฑ์
INSERT INTO categories (name, description) VALUES
('คอมพิวเตอร์', 'อุปกรณ์คอมพิวเตอร์และอุปกรณ์ต่อพ่วง'),
('เครื่องใช้สำนักงาน', 'เครื่องใช้สำนักงานทั่วไป'),
('อุปกรณ์โสตทัศนูปกรณ์', 'อุปกรณ์โสตและทัศนูปกรณ์'),
('เครื่องมือวัด', 'เครื่องมือวัดและอุปกรณ์วิทยาศาสตร์'),
('เฟอร์นิเจอร์', 'โต๊ะ เก้าอี้ ตู้ และเฟอร์นิเจอร์อื่นๆ');

-- เพิ่มรายการย่อยของหมวดหมู่
INSERT INTO categories_items (category_id, name, description) VALUES
-- คอมพิวเตอร์
(1, 'เครื่องคอมพิวเตอร์ตั้งโต๊ะ', 'Desktop Computer'),
(1, 'โน้ตบุ๊ค', 'Notebook/Laptop'),
(1, 'จอคอมพิวเตอร์', 'Monitor'),
(1, 'เครื่องพิมพ์', 'Printer'),
(1, 'เมาส์', 'Mouse'),
(1, 'คีย์บอร์ด', 'Keyboard'),
-- เครื่องใช้สำนักงาน
(2, 'โทรศัพท์', 'Telephone'),
(2, 'เครื่องแฟกซ์', 'Fax Machine'),
(2, 'เครื่องถ่ายเอกสาร', 'Photocopier'),
(2, 'เครื่องสแกน', 'Scanner'),
-- โสตทัศนูปกรณ์
(3, 'โปรเจคเตอร์', 'Projector'),
(3, 'จอรับภาพ', 'Projection Screen'),
(3, 'กล้องวงจรปิด', 'CCTV Camera'),
(3, 'ลำโพง', 'Speaker'),
-- เฟอร์นิเจอร์
(5, 'โต๊ะทำงาน', 'Desk'),
(5, 'เก้าอี้', 'Chair'),
(5, 'ตู้เก็บเอกสาร', 'Filing Cabinet'),
(5, 'ชั้นวางหนังสือ', 'Bookshelf');

-- เพิ่มพนักงานตัวอย่าง
INSERT INTO employees (employee_code, first_name, last_name, department_id, position, email, phone) VALUES
('EMP001', 'สมชาย', 'ใจดี', 6, 'ช่างคอมพิวเตอร์', 'somchai@varee.ac.th', '081-111-1111'),
('EMP002', 'สมหญิง', 'รักงาน', 6, 'เจ้าหน้าที่ IT', 'somying@varee.ac.th', '081-222-2222'),
('EMP003', 'วิชัย', 'มั่นคง', 5, 'เจ้าหน้าที่บริหาร', 'wichai@varee.ac.th', '081-333-3333');

-- เพิ่มครุภัณฑ์ตัวอย่าง
INSERT INTO equipment (code, name, category_id, category_item_id, brand, model, serial_number, purchase_date, warranty_expiry, price, equipment_status, school_name, building_name, floor_name, room_name, responsible_person) VALUES
('VCS-COM-001', 'เครื่องคอมพิวเตอร์', 1, 1, 'Dell', 'OptiPlex 7090', 'SN001234567', '2024-01-15', '2027-01-15', 25000.00, 'อุปกรณ์ใหม่', 'โรงเรียนวารีเชียงใหม่', 'ตึก1-อำนวยการ', 'ชั้น 1', 'ห้องธุรการ', 'คุณสมชาย'),
('VCS-COM-002', 'โน้ตบุ๊ค', 1, 2, 'HP', 'ProBook 450 G8', 'SN987654321', '2024-02-20', '2027-02-20', 22000.00, 'อุปกรณ์ใหม่', 'โรงเรียนวารีเชียงใหม่', 'ตึก7-มัธยม', 'ชั้น 2', '201', 'คุณสมหญิง'),
('VCS-PRJ-001', 'โปรเจคเตอร์', 3, 11, 'Epson', 'EB-2250U', 'SNPRJ123456', '2023-08-10', '2026-08-10', 35000.00, 'อุปกรณ์เดิม', 'โรงเรียนวารีเชียงใหม่', 'ตึก3-ประถม', 'ชั้น 2', 'ห้องประชุม', 'คุณวิชัย'),
('VKS-COM-001', 'เครื่องคอมพิวเตอร์', 1, 1, 'Lenovo', 'ThinkCentre M90', 'SNLEN789012', '2024-03-01', '2027-03-01', 24000.00, 'อุปกรณ์ใหม่', 'โรงเรียนอนุบาลวารีเชียงใหม่', 'ตึก1-อำนวยการ', 'ชั้น 1', 'ห้องธุรการ', 'คุณสมชาย');

-- เพิ่มข้อมูลการซ่อมตัวอย่าง
INSERT INTO maintenance (repair_code, equipment_id, report_date, problem_description, reported_by, assigned_technician, repair_status, solution_description, cost, completed_date, school_name, building_name, floor_name, room_name) VALUES
('R202410-0001', 1, '2024-10-01', 'คอมพิวเตอร์เปิดไม่ติด', 'คุณสมศรี', 'สมชาย ใจดี', 'ซ่อมเสร็จ', 'เปลี่ยนแหล่งจ่ายไฟใหม่', 2500.00, '2024-10-03', 'โรงเรียนวารีเชียงใหม่', 'ตึก1-อำนวยการ', 'ชั้น 1', 'ห้องธุรการ'),
('R202410-0002', 3, '2024-10-15', 'ภาพไม่ชัด มีจุดดำ', 'คุณวิชัย', 'สมชาย ใจดี', 'กำลังดำเนินการ', NULL, 0, NULL, 'โรงเรียนวารีเชียงใหม่', 'ตึก3-ประถม', 'ชั้น 2', 'ห้องประชุม'),
('R202410-0003', 2, '2024-10-20', 'แบตเตอรี่เสื่อม ใช้งานได้ไม่เกิน 1 ชั่วโมง', 'คุณสมหญิง', NULL, 'รอซ่อม', NULL, 0, NULL, 'โรงเรียนวารีเชียงใหม่', 'ตึก7-มัธยม', 'ชั้น 2', '201');