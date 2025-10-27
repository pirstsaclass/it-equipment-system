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
