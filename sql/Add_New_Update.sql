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
MODIFY COLUMN status ENUM('อุปกรณ์ใหม่', 'อุปกรณ์เดิม')
NOT NULL DEFAULT 'อุปกรณ์เดิม';

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