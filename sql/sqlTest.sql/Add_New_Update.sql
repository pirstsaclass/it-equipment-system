-- ตาราง equipment (ครุภัณฑ์)
CREATE TABLE equipment (
    id INT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    category_id INT,
    category_item_id INT,
    brand VARCHAR(100),
    model VARCHAR(100),
    serial_number VARCHAR(100),
    purchase_date DATE,
    purchase_price DECIMAL(10,2),
    department_id INT,
    responsible_person VARCHAR(100),
    status ENUM('ใหม่', 'ใช้งานปกติ', 'ชำรุด', 'รอซ่อม', 'กำลังซ่อม', 'ซ่อมเสร็จแล้ว', 'ส่งคืนแล้ว', 'จำหน่ายแล้ว') DEFAULT 'ใช้งานปกติ',
    specifications TEXT,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id),
    FOREIGN KEY (category_item_id) REFERENCES categories_items(id),
    FOREIGN KEY (department_id) REFERENCES departments(id)
);

-- ตาราง maintenance (ซ่อมบำรุง)
CREATE TABLE maintenance (
    id INT PRIMARY KEY AUTO_INCREMENT,
    equipment_id INT NOT NULL,
    report_date DATE NOT NULL,
    problem_description TEXT NOT NULL,
    reported_by VARCHAR(100) NOT NULL,
    assigned_technician VARCHAR(100),
    cost DECIMAL(10,2) DEFAULT 0,
    status ENUM('รอซ่อม', 'กำลังดำเนินการ', 'ซ่อมเสร็จ', 'ยกเลิก') DEFAULT 'รอซ่อม',
    solution_description TEXT,
    completed_date DATE,
    school_name VARCHAR(255),
    building_name VARCHAR(100),
    floor_name VARCHAR(50),
    room_name VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (equipment_id) REFERENCES equipment(id) ON DELETE CASCADE
);


CREATE TABLE classroom_equipment (
    id INT PRIMARY KEY AUTO_INCREMENT,
    room_number VARCHAR(20) NOT NULL,
    equipment_name VARCHAR(255) NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    equipment_condition VARCHAR(50) DEFAULT 'ดี',  -- เปลี่ยนจาก condition เป็น equipment_condition
    description TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (created_by) REFERENCES users(id)
);

ALTER TABLE equipment 
MODIFY COLUMN status ENUM('อุปกรณ์ใหม่', 'อุปกรณ์เดิม') 
NOT NULL DEFAULT 'อุปกรณ์ใหม่';

ALTER TABLE maintenance 
MODIFY COLUMN status ENUM('รอซ่อม', 'กำลังดำเนินการ', 'ซ่อมเสร็จ', 'ยกเลิก', 'จำหน่าย') 
NOT NULL DEFAULT 'รอซ่อม';

ALTER TABLE maintenance 
MODIFY COLUMN status ENUM('รอซ่อม', 'กำลังดำเนินการ', 'ซ่อมเสร็จ', 'ยกเลิก') 
NOT NULL DEFAULT 'รอซ่อม';


-- อัพเดท ENUM values ถ้ายังไม่มีสถานะใหม่
ALTER TABLE equipment 
MODIFY COLUMN status ENUM(
    'อุปกรณ์ใหม่', 
    'อุปกรณ์เดิม', 
    'อุปกรณ์ใหม่-ซ่อมเสร็จ',
    'อุปกรณ์เดิม-ซ่อมเสร็จ',
    'รอซ่อม',
    'กำลังซ่อม',
    'ใช้งานปกติ',
    'ชำรุด',
    'ซ่อมเสร็จแล้ว',
    'ส่งคืนแล้ว',
    'จำหน่ายแล้ว'
) NOT NULL DEFAULT 'อุปกรณ์เดิม';

-- อัพเดท ENUM values เพื่อรองรับสถานะใหม่
ALTER TABLE equipment 
MODIFY COLUMN status ENUM(
    'อุปกรณ์ใหม่', 
    'อุปกรณ์เดิม', 
    'อุปกรณ์ใหม่-ซ่อมเสร็จ', 
    'อุปกรณ์เดิม-ซ่อมเสร็จ',    
    'จำหน่ายแล้ว'
) NOT NULL DEFAULT 'อุปกรณ์ใหม่';


-- อัพเดทคอลัมน์ status ในตาราง equipment
ALTER TABLE equipment 
MODIFY COLUMN status ENUM('อุปกรณ์ใหม่', 'อุปกรณ์เดิม') 
NOT NULL DEFAULT 'อุปกรณ์เดิม';

-- หรือถ้าต้องการลบค่าเดิมและเพิ่มค่าใหม่
ALTER TABLE equipment 
MODIFY COLUMN status ENUM('อุปกรณ์ใหม่', 'อุปกรณ์เดิม') 
NOT NULL DEFAULT 'อุปกรณ์เดิม';

-- อัพเดทข้อมูลที่มีอยู่ให้เป็นค่าใหม่
UPDATE equipment 
SET status = 'อุปกรณ์เดิม' 
WHERE status NOT IN ('อุปกรณ์ใหม่', 'อุปกรณ์เดิม');


-- อัพเดทคอลัมน์ status ในตาราง maintenance
ALTER TABLE maintenance 
MODIFY COLUMN status ENUM('รอซ่อม', 'กำลังดำเนินการ', 'ซ่อมเสร็จ', 'ยกเลิก') 
NOT NULL DEFAULT 'รอซ่อม';

-- อัพเดทข้อมูลที่มีอยู่ให้เป็นค่าใหม่ (ถ้ามีค่าเก่าที่ไม่ตรง)
UPDATE maintenance 
SET status = 'รอซ่อม' 
WHERE status NOT IN ('รอซ่อม', 'กำลังดำเนินการ', 'ซ่อมเสร็จ', 'ยกเลิก');


-- ตรวจสอบโครงสร้างตาราง equipment
DESCRIBE equipment;

-- ตรวจสอบโครงสร้างตาราง maintenance
DESCRIBE maintenance;

-- ตรวจสอบค่าที่มีอยู่ในตาราง equipment
SELECT DISTINCT status FROM equipment;

-- ตรวจสอบค่าที่มีอยู่ในตาราง maintenance
SELECT DISTINCT status FROM maintenance;

-- สำหรับตาราง equipment
ALTER TABLE equipment 
MODIFY COLUMN status VARCHAR(50);

UPDATE equipment 
SET status = 'อุปกรณ์เดิม' 
WHERE status IS NULL OR status = '';

ALTER TABLE equipment 
MODIFY COLUMN equipment_status ENUM('อุปกรณ์ใหม่', 'อุปกรณ์เดิม')
NOT NULL DEFAULT 'อุปกรณ์ใหม่';

-- สำหรับตาราง maintenance
ALTER TABLE maintenance 
MODIFY COLUMN status VARCHAR(50);

UPDATE maintenance 
SET status = 'รอซ่อม' 
WHERE status IS NULL OR status = '';

ALTER TABLE maintenance 
MODIFY COLUMN status ENUM('รอซ่อม', 'กำลังดำเนินการ', 'ซ่อมเสร็จ', 'ยกเลิก') 
NOT NULL DEFAULT 'รอซ่อม';


-- อัพเดทสถานะ equipment เมื่อเพิ่มการซ่อมใหม่
UPDATE equipment 
SET status = 'รอซ่อม' 
WHERE id = ?;

-- อัพเดทสถานะ equipment เมื่อซ่อมเสร็จ
UPDATE equipment 
SET status = 'อุปกรณ์เดิม' 
WHERE id = ?;

-- อัพเดทสถานะ maintenance
UPDATE maintenance 
SET status = 'ซ่อมเสร็จ' 
WHERE id = ?;

ALTER TABLE equipment 
MODIFY COLUMN equipment_status ENUM('อุปกรณ์ใหม่', 'อุปกรณ์เดิม','อุปกรณ์ใหม่-ซ่อมเสร็จ','อุปกรณ์เดิม-ซ่อมเสร็จ')
NOT NULL DEFAULT 'อุปกรณ์ใหม่';


CREATE TABLE IF NOT EXISTS building_floor_plans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    school VARCHAR(255) NOT NULL,
    building VARCHAR(255) NOT NULL,
    academic_year INT NOT NULL,
    plan_image VARCHAR(255) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_plan (school, building, academic_year)
);


NEWwwwwwwwwwwwwwwwwwwwwwww

-- สร้างฐานข้อมูล
CREATE DATABASE IF NOT EXISTS school_it_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE school_it_management;

-- ตาราง users (ผู้ใช้งานระบบ)
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'รหัสผู้ใช้ระบบ (Primary Key)',
    username VARCHAR(50) NOT NULL UNIQUE COMMENT 'ชื่อผู้ใช้สำหรับเข้าสู่ระบบ',
    password VARCHAR(255) NOT NULL COMMENT 'รหัสผ่านที่เข้ารหัสแล้ว',
    full_name VARCHAR(100) NOT NULL COMMENT 'ชื่อ-นามสกุลผู้ใช้',
    email_address VARCHAR(100) COMMENT 'ที่อยู่อีเมลผู้ใช้',
    user_role ENUM('admin', 'user', 'technician') DEFAULT 'user' COMMENT 'บทบาทผู้ใช้ (admin, user, technician)',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'วันที่สร้างบันทึก',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'วันที่แก้ไขล่าสุด',
    INDEX idx_username (username),
    INDEX idx_user_role (user_role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ตาราง departments (แผนก)
CREATE TABLE IF NOT EXISTS departments (
    department_id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'รหัสแผนก (Primary Key)',
    department_name VARCHAR(100) NOT NULL COMMENT 'ชื่อแผนก',
    department_description TEXT COMMENT 'รายละเอียดแผนก',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'วันที่สร้างบันทึก',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'วันที่แก้ไขล่าสุด',
    INDEX idx_department_name (department_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ตาราง employees (พนักงาน)
CREATE TABLE IF NOT EXISTS employees (
    employee_id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'รหัสพนักงาน (Primary Key)',
    employee_code VARCHAR(20) UNIQUE COMMENT 'รหัสพนักงานสำหรับอ้างอิง',
    first_name VARCHAR(50) NOT NULL COMMENT 'ชื่อพนักงาน',
    last_name VARCHAR(50) NOT NULL COMMENT 'นามสกุลพนักงาน',
    department_id INT COMMENT 'รหัสแผนก (Foreign Key)',
    position_name VARCHAR(100) COMMENT 'ตำแหน่งงาน',
    email_address VARCHAR(100) COMMENT 'ที่อยู่อีเมลพนักงาน',
    phone_number VARCHAR(20) COMMENT 'เบอร์โทรศัพท์พนักงาน',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'วันที่สร้างบันทึก',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'วันที่แก้ไขล่าสุด',
    FOREIGN KEY (department_id) REFERENCES departments(department_id) ON DELETE SET NULL,
    INDEX idx_employee_code (employee_code),
    INDEX idx_department_id (department_id),
    INDEX idx_employee_name (first_name, last_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ตาราง equipment_categories (หมวดหมู่ครุภัณฑ์)
CREATE TABLE IF NOT EXISTS equipment_categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'รหัสหมวดหมู่ (Primary Key)',
    category_name VARCHAR(100) NOT NULL COMMENT 'ชื่อหมวดหมู่ครุภัณฑ์',
    category_description TEXT COMMENT 'รายละเอียดหมวดหมู่',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'วันที่สร้างบันทึก',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'วันที่แก้ไขล่าสุด',
    INDEX idx_category_name (category_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ตาราง equipment_subcategories (รายการย่อยของหมวดหมู่)
CREATE TABLE IF NOT EXISTS equipment_subcategories (
    subcategory_id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'รหัสหมวดหมู่ย่อย (Primary Key)',
    category_id INT NOT NULL COMMENT 'รหัสหมวดหมู่หลัก (Foreign Key)',
    subcategory_name VARCHAR(100) NOT NULL COMMENT 'ชื่อหมวดหมู่ย่อย',
    subcategory_description TEXT COMMENT 'รายละเอียดหมวดหมู่ย่อย',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'วันที่สร้างบันทึก',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'วันที่แก้ไขล่าสุด',
    FOREIGN KEY (category_id) REFERENCES equipment_categories(category_id) ON DELETE CASCADE,
    INDEX idx_category_id (category_id),
    INDEX idx_subcategory_name (subcategory_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ตาราง equipment (ครุภัณฑ์)
CREATE TABLE IF NOT EXISTS equipment (
    equipment_id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'รหัสครุภัณฑ์ (Primary Key)',
    equipment_code VARCHAR(50) NOT NULL UNIQUE COMMENT 'รหัสครุภัณฑ์สำหรับอ้างอิง',
    equipment_name VARCHAR(200) NOT NULL COMMENT 'ชื่อครุภัณฑ์',
    category_id INT COMMENT 'รหัสหมวดหมู่หลัก (Foreign Key)',
    subcategory_id INT COMMENT 'รหัสหมวดหมู่ย่อย (Foreign Key)',
    brand_name VARCHAR(100) COMMENT 'ยี่ห้อครุภัณฑ์',
    model_name VARCHAR(100) COMMENT 'รุ่นครุภัณฑ์',
    serial_number VARCHAR(100) COMMENT 'หมายเลขซีเรียล',
    purchase_date DATE COMMENT 'วันที่จัดซื้อ',
    warranty_expiry_date DATE COMMENT 'วันที่หมดประกัน',
    purchase_price DECIMAL(10,2) DEFAULT 0 COMMENT 'ราคาจัดซื้อ',
    supplier_name VARCHAR(200) COMMENT 'ชื่อผู้จัดจำหน่าย',
    equipment_status VARCHAR(50) DEFAULT 'ใหม่' COMMENT 'สถานะครุภัณฑ์',
    location_school VARCHAR(200) COMMENT 'โรงเรียนที่ตั้ง',
    location_building VARCHAR(100) COMMENT 'ตึก/อาคารที่ตั้ง',
    location_floor VARCHAR(50) COMMENT 'ชั้นที่ตั้ง',
    location_room VARCHAR(100) COMMENT 'ห้องที่ตั้ง',
    responsible_person VARCHAR(100) COMMENT 'ผู้รับผิดชอบครุภัณฑ์',
    notes TEXT COMMENT 'หมายเหตุเพิ่มเติม',
    image_path VARCHAR(255) COMMENT 'เส้นทางเก็บรูปภาพ',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'วันที่สร้างบันทึก',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'วันที่แก้ไขล่าสุด',
    FOREIGN KEY (category_id) REFERENCES equipment_categories(category_id) ON DELETE SET NULL,
    FOREIGN KEY (subcategory_id) REFERENCES equipment_subcategories(subcategory_id) ON DELETE SET NULL,
    INDEX idx_equipment_code (equipment_code),
    INDEX idx_category_id (category_id),
    INDEX idx_equipment_status (equipment_status),
    INDEX idx_location (location_school, location_building, location_floor, location_room),
    INDEX idx_purchase_date (purchase_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ตาราง maintenance_requests (การแจ้งซ่อม)
CREATE TABLE IF NOT EXISTS maintenance_requests (
    maintenance_id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'รหัสการซ่อม (Primary Key)',
    repair_code VARCHAR(20) NOT NULL UNIQUE COMMENT 'รหัสใบแจ้งซ่อม',
    equipment_id INT NOT NULL COMMENT 'รหัสครุภัณฑ์ (Foreign Key)',
    report_date DATE NOT NULL COMMENT 'วันที่แจ้งซ่อม',
    problem_description TEXT NOT NULL COMMENT 'รายละเอียดปัญหา',
    reported_by VARCHAR(100) NOT NULL COMMENT 'ผู้แจ้งซ่อม',
    assigned_technician VARCHAR(100) COMMENT 'ช่างผู้รับผิดชอบ',
    repair_status VARCHAR(50) DEFAULT 'รอซ่อม' COMMENT 'สถานะการซ่อม',
    solution_description TEXT COMMENT 'รายละเอียดการแก้ไข',
    repair_cost DECIMAL(10,2) DEFAULT 0 COMMENT 'ค่าใช้จ่ายในการซ่อม',
    completed_date DATE COMMENT 'วันที่ซ่อมเสร็จสิ้น',
    location_school VARCHAR(200) COMMENT 'โรงเรียนที่ตั้งครุภัณฑ์',
    location_building VARCHAR(100) COMMENT 'ตึกที่ตั้งครุภัณฑ์',
    location_floor VARCHAR(50) COMMENT 'ชั้นที่ตั้งครุภัณฑ์',
    location_room VARCHAR(100) COMMENT 'ห้องที่ตั้งครุภัณฑ์',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'วันที่สร้างบันทึก',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'วันที่แก้ไขล่าสุด',
    FOREIGN KEY (equipment_id) REFERENCES equipment(equipment_id) ON DELETE CASCADE,
    INDEX idx_repair_code (repair_code),
    INDEX idx_equipment_id (equipment_id),
    INDEX idx_repair_status (repair_status),
    INDEX idx_report_date (report_date),
    INDEX idx_location (location_school, location_building)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ตาราง maintenance_logs (บันทึกการซ่อมบำรุง)
CREATE TABLE IF NOT EXISTS maintenance_logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'รหัสบันทึก (Primary Key)',
    maintenance_id INT NOT NULL COMMENT 'รหัสการซ่อม (Foreign Key)',
    previous_status VARCHAR(50) COMMENT 'สถานะก่อนหน้า',
    new_status VARCHAR(50) COMMENT 'สถานะใหม่',
    changed_by_user VARCHAR(100) COMMENT 'ผู้ใช้งานที่เปลี่ยนแปลงสถานะ',
    action_notes TEXT COMMENT 'หมายเหตุการดำเนินการ',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'วันที่สร้างบันทึก',
    FOREIGN KEY (maintenance_id) REFERENCES maintenance_requests(maintenance_id) ON DELETE CASCADE,
    INDEX idx_maintenance_id (maintenance_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ตาราง equipment_transfers (การโอนย้ายครุภัณฑ์)
CREATE TABLE IF NOT EXISTS equipment_transfers (
    transfer_id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'รหัสการโอนย้าย (Primary Key)',
    equipment_id INT NOT NULL COMMENT 'รหัสครุภัณฑ์ (Foreign Key)',
    from_school VARCHAR(200) COMMENT 'โรงเรียนเดิม',
    from_building VARCHAR(100) COMMENT 'ตึกเดิม',
    from_floor VARCHAR(50) COMMENT 'ชั้นเดิม',
    from_room VARCHAR(100) COMMENT 'ห้องเดิม',
    to_school VARCHAR(200) COMMENT 'โรงเรียนใหม่',
    to_building VARCHAR(100) COMMENT 'ตึกใหม่',
    to_floor VARCHAR(50) COMMENT 'ชั้นใหม่',
    to_room VARCHAR(100) COMMENT 'ห้องใหม่',
    transfer_date DATE NOT NULL COMMENT 'วันที่โอนย้าย',
    transferred_by VARCHAR(100) COMMENT 'ผู้ดำเนินการโอนย้าย',
    transfer_reason TEXT COMMENT 'เหตุผลในการโอนย้าย',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'วันที่สร้างบันทึก',
    FOREIGN KEY (equipment_id) REFERENCES equipment(equipment_id) ON DELETE CASCADE,
    INDEX idx_equipment_id (equipment_id),
    INDEX idx_transfer_date (transfer_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ตาราง equipment_disposals (การจำหน่ายครุภัณฑ์)
CREATE TABLE IF NOT EXISTS equipment_disposals (
    disposal_id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'รหัสการจำหน่าย (Primary Key)',
    equipment_id INT NOT NULL COMMENT 'รหัสครุภัณฑ์ (Foreign Key)',
    disposal_date DATE NOT NULL COMMENT 'วันที่จำหน่าย',
    disposal_method VARCHAR(100) COMMENT 'วิธีการจำหน่าย',
    disposal_value DECIMAL(10,2) DEFAULT 0 COMMENT 'มูลค่าที่จำหน่าย',
    disposal_reason TEXT COMMENT 'เหตุผลในการจำหน่าย',
    approved_by VARCHAR(100) COMMENT 'ผู้อนุมัติการจำหน่าย',
    disposal_notes TEXT COMMENT 'หมายเหตุการจำหน่าย',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'วันที่สร้างบันทึก',
    FOREIGN KEY (equipment_id) REFERENCES equipment(equipment_id) ON DELETE CASCADE,
    INDEX idx_equipment_id (equipment_id),
    INDEX idx_disposal_date (disposal_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ข้อมูลตัวอย่าง

-- เพิ่ม admin user (password: admin123)
INSERT INTO users (username, password, full_name, email_address, user_role) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ผู้ดูแลระบบ', 'admin@varee.ac.th', 'admin');

-- เพิ่มแผนก
INSERT INTO departments (department_name, department_description) VALUES
('อำนวยการ', 'ฝ่ายอำนวยการ'),
('อนุบาล', 'แผนกอนุบาล'),
('ประถมศึกษา', 'แผนกประถมศึกษา'),
('มัธยมศึกษา', 'แผนกมัธยมศึกษา'),
('สนับสนุน', 'ฝ่ายสนับสนุน'),
('IT', 'ฝ่ายเทคโนโลยีสารสนเทศ');

-- เพิ่มหมวดหมู่ครุภัณฑ์
INSERT INTO equipment_categories (category_name, category_description) VALUES
('คอมพิวเตอร์', 'อุปกรณ์คอมพิวเตอร์และอุปกรณ์ต่อพ่วง'),
('เครื่องใช้สำนักงาน', 'เครื่องใช้สำนักงานทั่วไป'),
('อุปกรณ์โสตทัศนูปกรณ์', 'อุปกรณ์โสตและทัศนูปกรณ์'),
('เครื่องมือวัด', 'เครื่องมือวัดและอุปกรณ์วิทยาศาสตร์'),
('เฟอร์นิเจอร์', 'โต๊ะ เก้าอี้ ตู้ และเฟอร์นิเจอร์อื่นๆ');

-- เพิ่มรายการย่อยของหมวดหมู่
INSERT INTO equipment_subcategories (category_id, subcategory_name, subcategory_description) VALUES
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
INSERT INTO employees (employee_code, first_name, last_name, department_id, position_name, email_address, phone_number) VALUES
('EMP001', 'สมชาย', 'ใจดี', 6, 'ช่างคอมพิวเตอร์', 'somchai@varee.ac.th', '081-111-1111'),
('EMP002', 'สมหญิง', 'รักงาน', 6, 'เจ้าหน้าที่ IT', 'somying@varee.ac.th', '081-222-2222'),
('EMP003', 'วิชัย', 'มั่นคง', 5, 'เจ้าหน้าที่บริหาร', 'wichai@varee.ac.th', '081-333-3333');

-- เพิ่มครุภัณฑ์ตัวอย่าง
INSERT INTO equipment (equipment_code, equipment_name, category_id, subcategory_id, brand_name, model_name, serial_number, purchase_date, warranty_expiry_date, purchase_price, equipment_status, location_school, location_building, location_floor, location_room, responsible_person) VALUES
('VCS-COM-001', 'เครื่องคอมพิวเตอร์', 1, 1, 'Dell', 'OptiPlex 7090', 'SN001234567', '2024-01-15', '2027-01-15', 25000.00, 'ใหม่', 'โรงเรียนวารีเชียงใหม่', 'ตึก1-อำนวยการ', 'ชั้น 1', 'ห้องธุรการ', 'คุณสมชาย'),
('VCS-COM-002', 'โน้ตบุ๊ค', 1, 2, 'HP', 'ProBook 450 G8', 'SN987654321', '2024-02-20', '2027-02-20', 22000.00, 'ใหม่', 'โรงเรียนวารีเชียงใหม่', 'ตึก7-มัธยม', 'ชั้น 2', '201', 'คุณสมหญิง'),
('VCS-PRJ-001', 'โปรเจคเตอร์', 3, 11, 'Epson', 'EB-2250U', 'SNPRJ123456', '2023-08-10', '2026-08-10', 35000.00, 'ใช้งานปกติ', 'โรงเรียนวารีเชียงใหม่', 'ตึก3-ประถม', 'ชั้น 2', 'ห้องประชุม', 'คุณวิชัย'),
('VKS-COM-001', 'เครื่องคอมพิวเตอร์', 1, 1, 'Lenovo', 'ThinkCentre M90', 'SNLEN789012', '2024-03-01', '2027-03-01', 24000.00, 'ใหม่', 'โรงเรียนอนุบาลวารีเชียงใหม่', 'ตึก1-อำนวยการ', 'ชั้น 1', 'ห้องธุรการ', 'คุณสมชาย');

-- เพิ่มข้อมูลการซ่อมตัวอย่าง
INSERT INTO maintenance_requests (repair_code, equipment_id, report_date, problem_description, reported_by, assigned_technician, repair_status, solution_description, repair_cost, completed_date, location_school, location_building, location_floor, location_room) VALUES
('R202410-0001', 1, '2024-10-01', 'คอมพิวเตอร์เปิดไม่ติด', 'คุณสมศรี', 'สมชาย ใจดี', 'ซ่อมเสร็จ', 'เปลี่ยนแหล่งจ่ายไฟใหม่', 2500.00, '2024-10-03', 'โรงเรียนวารีเชียงใหม่', 'ตึก1-อำนวยการ', 'ชั้น 1', 'ห้องธุรการ'),
('R202410-0002', 3, '2024-10-15', 'ภาพไม่ชัด มีจุดดำ', 'คุณวิชัย', 'สมชาย ใจดี', 'กำลังดำเนินการ', NULL, 0, NULL, 'โรงเรียนวารีเชียงใหม่', 'ตึก3-ประถม', 'ชั้น 2', 'ห้องประชุม'),
('R202410-0003', 2, '2024-10-20', 'แบตเตอรี่เสื่อม ใช้งานได้ไม่เกิน 1 ชั่วโมง', 'คุณสมหญิง', NULL, 'รอซ่อม', NULL, 0, NULL, 'โรงเรียนวารีเชียงใหม่', 'ตึก7-มัธยม', 'ชั้น 2', '201');