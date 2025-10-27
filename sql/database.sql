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