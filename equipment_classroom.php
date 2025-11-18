
<?php
// เพิ่มการแสดง error ชั่วคราวเพื่อ debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'includes/header.php';

// ตรวจสอบสิทธิ์การเข้าถึง
if ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'teacher') {
    $_SESSION['error'] = "คุณไม่มีสิทธิ์เข้าถึงหน้านี้";
    header('Location: index.php');
    exit;
}

$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? 0;

// ฟังก์ชันจัดการฟอร์ม
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // ใช้ action จาก POST แทน GET
    $form_action = $_POST['action'] ?? 'add';
    $form_id = intval($_POST['id'] ?? 0);
    
    $equipment_code = trim($_POST['equipment_code']);
    $school_name = trim($_POST['school_name']);
    $building_name = trim($_POST['building_name']);
    $floor_level = trim($_POST['floor_level']);
    $room_number = trim($_POST['room_number']);
    $room_name = trim($_POST['room_name']);
    $equipment_quantity = intval($_POST['equipment_quantity']);
    $installation_date = !empty($_POST['installation_date']) ? $_POST['installation_date'] : null;
    $placement_notes = trim($_POST['placement_notes']);

    // Debug: ตรวจสอบข้อมูลที่ส่งมา
    error_log("Form Data - action: $form_action, id: $form_id, equipment_code: $equipment_code, school_name: $school_name, building_name: $building_name, floor_level: $floor_level, room_number: $room_number");

    if (empty($equipment_code) || empty($school_name) || empty($building_name) || empty($floor_level) || empty($room_number) || $equipment_quantity <= 0) {
        $_SESSION['error'] = "กรุณากรอกข้อมูลให้ครบถ้วน";
        error_log("Validation failed - missing required fields");
    } else {
        try {
            // ตรวจสอบว่า equipment_code มีอยู่ในตาราง equipment หรือไม่
            $check_equipment = $db->prepare("SELECT COUNT(*) FROM equipment WHERE equipment_code = ?");
            $check_equipment->execute([$equipment_code]);
            $equipment_exists = $check_equipment->fetchColumn();

            if (!$equipment_exists) {
                $_SESSION['error'] = "รหัสครุภัณฑ์ไม่ถูกต้องหรือไม่มีอยู่ในระบบ";
                error_log("Equipment code not found: $equipment_code");
            } else {
                if ($form_action == 'add') {
                    $stmt = $db->prepare("INSERT INTO equipment_classroom (equipment_code, school_name, building_name, floor_level, room_number, room_name, equipment_quantity, installation_date, placement_notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$equipment_code, $school_name, $building_name, $floor_level, $room_number, $room_name, $equipment_quantity, $installation_date, $placement_notes]);
                    $_SESSION['success'] = "บันทึกข้อมูลการจัดวางครุภัณฑ์เรียบร้อยแล้ว";
                    error_log("Insert successful");
                } elseif ($form_action == 'edit') {
                    // ใช้ $form_id ในการอัพเดท
                    $stmt = $db->prepare("UPDATE equipment_classroom SET equipment_code = ?, school_name = ?, building_name = ?, floor_level = ?, room_number = ?, room_name = ?, equipment_quantity = ?, installation_date = ?, placement_notes = ?, updated_at = NOW() WHERE id = ?");
                    $stmt->execute([$equipment_code, $school_name, $building_name, $floor_level, $room_number, $room_name, $equipment_quantity, $installation_date, $placement_notes, $form_id]);
                    $_SESSION['success'] = "อัพเดทข้อมูลการจัดวางครุภัณฑ์เรียบร้อยแล้ว";
                    error_log("Update successful for ID: $form_id");
                }
                header('Location: equipment_classroom.php');
                exit;
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . $e->getMessage();
            error_log("Database error: " . $e->getMessage());
            
            // ตรวจสอบข้อผิดพลาดเฉพาะ
            if ($e->getCode() == '23000') {
                $_SESSION['error'] = "ไม่สามารถบันทึกข้อมูลได้: รหัสครุภัณฑ์ไม่ถูกต้องหรือไม่มีอยู่ในระบบ";
            }
        }
    }
}

// ฟังก์ชันลบข้อมูล
if ($action == 'delete' && $id > 0) {
    try {
        $stmt = $db->prepare("DELETE FROM equipment_classroom WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['success'] = "ลบข้อมูลการจัดวางครุภัณฑ์เรียบร้อยแล้ว";
        header('Location: equipment_classroom.php');
        exit;
    } catch (PDOException $e) {
        $_SESSION['error'] = "เกิดข้อผิดพลาด: " . $e->getMessage();
    }
}

// ดึงข้อมูลสำหรับแก้ไข (ใช้สำหรับ Modal)
$equipment_data = [];
if ($action == 'edit' && $id > 0) {
    $stmt = $db->prepare("SELECT * FROM equipment_classroom WHERE id = ?");
    $stmt->execute([$id]);
    $equipment_data = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$equipment_data) {
        $_SESSION['error'] = "ไม่พบข้อมูลการจัดวางครุภัณฑ์";
        header('Location: equipment_classroom.php');
        exit;
    }
}

// ดึงรายการทั้งหมด
$search = $_GET['search'] ?? '';
$school_filter = $_GET['school_filter'] ?? '';
$building_filter = $_GET['building_filter'] ?? '';

// ตรวจสอบว่ามีตาราง departments หรือไม่
try {
    $check_departments = $db->query("SELECT 1 FROM departments LIMIT 1");
    $has_departments = true;
} catch (PDOException $e) {
    $has_departments = false;
}

// Query หลักสำหรับดึงรายการ
$query = "SELECT ec.*, e.equipment_name, e.brand_name, e.model_name
          FROM equipment_classroom ec 
          LEFT JOIN equipment e ON ec.equipment_code = e.equipment_code 
          WHERE 1=1";

$params = [];

if (!empty($search)) {
    $query .= " AND (ec.equipment_code LIKE ? OR e.equipment_name LIKE ? OR ec.room_number LIKE ? OR ec.room_name LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($school_filter)) {
    $query .= " AND ec.school_name = ?";
    $params[] = $school_filter;
}

if (!empty($building_filter)) {
    $query .= " AND ec.building_name = ?";
    $params[] = $building_filter;
}

$query .= " ORDER BY ec.school_name, ec.building_name, ec.floor_level, ec.room_number";

$stmt = $db->prepare($query);
$stmt->execute($params);
$equipment_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ดึงรายการโรงเรียนจากตาราง departments
try {
    if ($has_departments) {
        // ดึงเฉพาะชื่อโรงเรียนที่ไม่ซ้ำกันจากตาราง departments
        $school_query = $db->query("SELECT DISTINCT school_name FROM departments ORDER BY school_name");
        $school_list = $school_query->fetchAll(PDO::FETCH_ASSOC);
    } else {
        // ถ้าไม่มีตาราง departments ให้ดึงจาก equipment_classroom แบบเดิม
        $school_query = $db->query("SELECT DISTINCT school_name FROM equipment_classroom ORDER BY school_name");
        $school_list = $school_query->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    $school_list = [];
    $_SESSION['error'] = "ไม่สามารถโหลดข้อมูลโรงเรียนได้: " . $e->getMessage();
}

// ดึงรายการอาคารสำหรับ dropdown
$building_query = $db->query("SELECT DISTINCT building_name FROM equipment_classroom ORDER BY building_name");
$building_list = $building_query->fetchAll(PDO::FETCH_COLUMN);

// ดึงรายการครุภัณฑ์ทั้งหมดสำหรับ dropdown
$equipment_query = $db->query("SELECT equipment_code, equipment_name, brand_name, model_name FROM equipment ORDER BY equipment_name");
$equipment_options = $equipment_query->fetchAll(PDO::FETCH_ASSOC);
?>

    <!-- Navbar -->
<?php include 'includes/navbar.php'; ?>
<?php 
// Include sidebar
include 'includes/sidebar.php';
?>
<div id="layoutSidenav_content">
<!-- Main Content -->
<main >
    <div class="container-fluid px-4">

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">จัดการครุภัณฑ์ในห้องเรียน</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEquipmentModal">
            <i class="fas fa-plus fa-sm text-white-50"></i> เพิ่มการจัดวาง
        </button>
    </div>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- แสดงรายการ -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">รายการการจัดวางครุภัณฑ์</h6>
            <div class="btn-group">
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="exportToExcel()">
                    <i class="fas fa-file-excel"></i> Export Excel
                </button>
                <button type="button" class="btn btn-outline-success btn-sm" onclick="printTable()">
                    <i class="fas fa-print"></i> พิมพ์
                </button>
            </div>
        </div>
        <div class="card-body">
            <!-- Real-time Filter Section -->
            <div class="card mb-4 border-primary">
                <div class="card-header bg-light py-2">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-filter"></i> ตัวกรองข้อมูลแบบ Real-time
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-bold">ค้นหาทั่วไป</label>
                            <input type="text" class="form-control" id="globalSearch" placeholder="ค้นหาทั้งตาราง..." onkeyup="filterTable()">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">โรงเรียน</label>
                            <select class="form-control" id="schoolFilter" onchange="filterTable()">
                                <option value="">ทั้งหมด</option>
                                <?php foreach($school_list as $school): ?>
                                    <option value="<?php echo htmlspecialchars($school['school_name']); ?>">
                                        <?php echo htmlspecialchars($school['school_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">อาคาร</label>
                            <select class="form-control" id="buildingFilter" onchange="filterTable()">
                                <option value="">ทั้งหมด</option>
                                <?php foreach($building_list as $building): ?>
                                    <option value="<?php echo htmlspecialchars($building); ?>">
                                        <?php echo htmlspecialchars($building); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">จำนวนครุภัณฑ์</label>
                            <select class="form-control" id="quantityFilter" onchange="filterTable()">
                                <option value="">ทั้งหมด</option>
                                <option value="1">1 ชิ้น</option>
                                <option value="2">2 ชิ้น</option>
                                <option value="3">3 ชิ้นขึ้นไป</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="clearFilters()">
                                        <i class="fas fa-redo"></i> ล้างตัวกรอง
                                    </button>
                                    <span class="ms-3 text-muted small" id="filterResultCount"></span>
                                </div>
                                <div class="text-muted small">
                                    <span id="currentTime"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="equipmentTable" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th>รหัสครุภัณฑ์</th>
                            <th>ชื่อครุภัณฑ์</th>
                            <th>โรงเรียน</th>
                            <th>อาคาร/ชั้น</th>
                            <th>ห้อง</th>
                            <th>จำนวน</th>
                            <th>วันที่ติดตั้ง</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($equipment_list) > 0): ?>
                            <?php foreach($equipment_list as $item): ?>
                            <tr>
                                <td class="fw-bold"><?php echo htmlspecialchars($item['equipment_code']); ?></td>
                                <td>
                                    <div class="fw-bold"><?php echo htmlspecialchars($item['equipment_name'] ?? '-'); ?></div>
                                    <?php if (!empty($item['brand_name'])): ?>
                                        <small class="text-muted"><?php echo htmlspecialchars($item['brand_name']); ?></small>
                                        <?php if (!empty($item['model_name'])): ?>
                                            <small class="text-muted"> - <?php echo htmlspecialchars($item['model_name']); ?></small>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($item['school_name']); ?></td>
                                <td>
                                    <div><?php echo htmlspecialchars($item['building_name']); ?></div>
                                    <small class="text-muted">ชั้น <?php echo htmlspecialchars($item['floor_level']); ?></small>
                                </td>
                                <td>
                                    <div class="fw-bold"><?php echo htmlspecialchars($item['room_number']); ?></div>
                                    <?php if (!empty($item['room_name'])): ?>
                                        <small class="text-muted"><?php echo htmlspecialchars($item['room_name']); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary"><?php echo $item['equipment_quantity']; ?></span>
                                </td>
                                <td>
                                    <?php echo !empty($item['installation_date']) ? date('d/m/Y', strtotime($item['installation_date'])) : '-'; ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-primary" title="แก้ไข" 
                                                onclick="openEditModal(<?php echo $item['id']; ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="equipment_classroom.php?action=delete&id=<?php echo $item['id']; ?>" 
                                           class="btn btn-danger" title="ลบ"
                                           onclick="return confirm('คุณแน่ใจหรือไม่ที่จะลบข้อมูลนี้?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                    ไม่พบข้อมูลการจัดวางครุภัณฑ์
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- No Results Message -->
            <div id="noResults" class="text-center py-4" style="display: none;">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">ไม่พบข้อมูลที่ตรงกับการค้นหา</h5>
                <p class="text-muted">ลองเปลี่ยนคำค้นหาหรือล้างตัวกรอง</p>
                <button type="button" class="btn btn-outline-primary" onclick="clearFilters()">
                    <i class="fas fa-redo"></i> ล้างตัวกรอง
                </button>
            </div>
        </div>
    </div>
</main>

<!-- Modal สำหรับเพิ่มข้อมูล -->
<div class="modal fade" id="addEquipmentModal" tabindex="-1" aria-labelledby="addEquipmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" id="addForm">
                <input type="hidden" name="action" value="add">
                <div class="modal-header">
                    <h5 class="modal-title" id="addEquipmentModalLabel">เพิ่มการจัดวางครุภัณฑ์</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">รหัสครุภัณฑ์ <span class="text-danger">*</span></label>
                                <select class="form-control" name="equipment_code" required>
                                    <option value="">-- เลือกครุภัณฑ์ --</option>
                                    <?php foreach($equipment_options as $equip): ?>
                                        <option value="<?php echo htmlspecialchars($equip['equipment_code']); ?>">
                                            <?php echo htmlspecialchars($equip['equipment_code'] . ' - ' . $equip['equipment_name']); ?>
                                            <?php if (!empty($equip['brand_name'])): ?>
                                                (<?php echo htmlspecialchars($equip['brand_name']); ?>)
                                            <?php endif; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">โรงเรียน <span class="text-danger">*</span></label>
                                <select class="form-control" name="school_name" id="modal_school_name" required onchange="updateModalBuildingsAndFloors()">
                                    <option value="">-- เลือกโรงเรียน --</option>
                                    <?php foreach($school_list as $school): ?>
                                        <option value="<?php echo htmlspecialchars($school['school_name']); ?>">
                                            <?php echo htmlspecialchars($school['school_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">อาคาร <span class="text-danger">*</span></label>
                                <select class="form-control" name="building_name" id="modal_building_name" required onchange="updateModalFloors()">
                                    <option value="">-- เลือกอาคาร --</option>
                                    <!-- อาคารจะถูกโหลดโดย JavaScript -->
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">ชั้น <span class="text-danger">*</span></label>
                                <select class="form-control" name="floor_level" id="modal_floor_level" required>
                                    <option value="">-- เลือกชั้น --</option>
                                    <!-- ชั้นจะถูกโหลดโดย JavaScript -->
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">หมายเลขห้อง <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="room_number" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">ชื่อห้อง</label>
                                <input type="text" class="form-control" name="room_name">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">จำนวนครุภัณฑ์ <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="equipment_quantity" min="1" value="1" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">วันที่ติดตั้ง</label>
                                <input type="date" class="form-control" name="installation_date">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">วันที่บันทึก</label>
                                <input type="text" class="form-control" value="<?php echo date('d/m/Y H:i'); ?>" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">หมายเหตุการจัดวาง</label>
                        <textarea class="form-control" name="placement_notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> บันทึก
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal สำหรับแก้ไขข้อมูล -->
<div class="modal fade" id="editEquipmentModal" tabindex="-1" aria-labelledby="editEquipmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" id="editForm">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="editEquipmentModalLabel">แก้ไขการจัดวางครุภัณฑ์</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">รหัสครุภัณฑ์ <span class="text-danger">*</span></label>
                                <select class="form-control" name="equipment_code" id="edit_equipment_code" required>
                                    <option value="">-- เลือกครุภัณฑ์ --</option>
                                    <?php foreach($equipment_options as $equip): ?>
                                        <option value="<?php echo htmlspecialchars($equip['equipment_code']); ?>">
                                            <?php echo htmlspecialchars($equip['equipment_code'] . ' - ' . $equip['equipment_name']); ?>
                                            <?php if (!empty($equip['brand_name'])): ?>
                                                (<?php echo htmlspecialchars($equip['brand_name']); ?>)
                                            <?php endif; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">โรงเรียน <span class="text-danger">*</span></label>
                                <select class="form-control" name="school_name" id="edit_school_name" required onchange="updateEditBuildingsAndFloors()">
                                    <option value="">-- เลือกโรงเรียน --</option>
                                    <?php foreach($school_list as $school): ?>
                                        <option value="<?php echo htmlspecialchars($school['school_name']); ?>">
                                            <?php echo htmlspecialchars($school['school_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">อาคาร <span class="text-danger">*</span></label>
                                <select class="form-control" name="building_name" id="edit_building_name" required onchange="updateEditFloors()">
                                    <option value="">-- เลือกอาคาร --</option>
                                    <!-- อาคารจะถูกโหลดโดย JavaScript -->
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">ชั้น <span class="text-danger">*</span></label>
                                <select class="form-control" name="floor_level" id="edit_floor_level" required>
                                    <option value="">-- เลือกชั้น --</option>
                                    <!-- ชั้นจะถูกโหลดโดย JavaScript -->
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">หมายเลขห้อง <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="room_number" id="edit_room_number" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">ชื่อห้อง</label>
                                <input type="text" class="form-control" name="room_name" id="edit_room_name">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">จำนวนครุภัณฑ์ <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="equipment_quantity" id="edit_equipment_quantity" min="1" value="1" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">วันที่ติดตั้ง</label>
                                <input type="date" class="form-control" name="installation_date" id="edit_installation_date">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">วันที่บันทึก</label>
                                <input type="text" class="form-control" id="edit_created_at" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">หมายเหตุการจัดวาง</label>
                        <textarea class="form-control" name="placement_notes" id="edit_placement_notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> อัพเดท
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// ข้อมูลโรงเรียน ตึก และชั้น
const schoolData = {
    "โรงเรียนวารีเชียงใหม่": {
        buildings: [
            { name: "อาคาร1-อำนวยการ", floors: ["ชั้น 1", "ชั้น 2"] },
            { name: "อาคาร3-ประถม", floors: ["ชั้น 1", "ชั้น 2", "ชั้น 3", "ชั้น 4"] },
            { name: "อาคาร4-ประถม", floors: ["ชั้น 1", "ชั้น 2", "ชั้น 3"] },
            { name: "อาคาร4-มัธยม", floors: ["ชั้น 3", "ชั้น 4", "ชั้น 5"] },
            { name: "อาคาร5-อนุบาล", floors: ["ชั้น 1", "ชั้น 2"] },
            { name: "อาคาร6-??", floors: ["ชั้น 1", "ชั้น 2"] },
            { name: "อาคาร7-มัธยม", floors: ["ชั้น 1", "ชั้น 2", "ชั้น 3", "ชั้น 4", "ชั้น 5", "ชั้น 6", "ชั้น 7"] },
            { name: "อาคาร 10", floors: ["ชั้น 1", "ชั้น 2"] }
        ]
    },
    "โรงเรียนอนุบาลวารีเชียงใหม่": {
        buildings: [
            { name: "อาคาร 1-อำนวยการ", floors: ["ชั้น 1", "ชั้น 2"] },
            { name: "อาคาร-อนุบาล", floors: ["ชั้น 1", "ชั้น 2"] }
        ]
    },
    "โรงเรียนนานาชาติวารีเชียงใหม่": {
        buildings: [
            { name: "อาคาร 8", floors: ["ชั้น 1", "ชั้น 2", "ชั้น 3", "ชั้น 4"] },
            { name: "อาคาร 9", floors: ["ชั้น 1", "ชั้น 2", "ชั้น 3"] },
            { name: "อาคาร 10", floors: ["ชั้น 1", "ชั้น 2"] }
        ]
    }
};

// ฟังก์ชันเปิด Modal แก้ไข (แบบไม่ใช้ AJAX)
function openEditModal(id) {
    // หาแถวในตารางที่ตรงกับ ID
    const rows = document.querySelectorAll('#equipmentTable tbody tr');
    let foundData = null;
    
    rows.forEach(row => {
        const editButton = row.querySelector('button[onclick*="openEditModal"]');
        if (editButton) {
            const buttonOnclick = editButton.getAttribute('onclick');
            const match = buttonOnclick.match(/openEditModal\((\d+)\)/);
            if (match && match[1] == id) {
                // ดึงข้อมูลจากแถว
                const cells = row.cells;
                foundData = {
                    equipment_code: cells[0].textContent.trim(),
                    equipment_name: cells[1].querySelector('.fw-bold').textContent.trim(),
                    school_name: cells[2].textContent.trim(),
                    building_name: cells[3].querySelector('div').textContent.trim(),
                    floor_level: cells[3].querySelector('small').textContent.replace('ชั้น ', '').trim(),
                    room_number: cells[4].querySelector('.fw-bold').textContent.trim(),
                    room_name: cells[4].querySelector('small') ? cells[4].querySelector('small').textContent.trim() : '',
                    equipment_quantity: cells[5].querySelector('.badge').textContent.trim(),
                    installation_date: cells[6].textContent.trim() !== '-' ? 
                        formatDateForInput(cells[6].textContent.trim()) : ''
                };
            }
        }
    });
    
    if (foundData) {
        // เติมข้อมูลลงในฟอร์มแก้ไข
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_equipment_code').value = foundData.equipment_code;
        document.getElementById('edit_school_name').value = foundData.school_name;
        document.getElementById('edit_room_number').value = foundData.room_number;
        document.getElementById('edit_room_name').value = foundData.room_name;
        document.getElementById('edit_equipment_quantity').value = foundData.equipment_quantity;
        document.getElementById('edit_installation_date').value = foundData.installation_date;
        
        // อัพเดทอาคารและชั้น
        updateEditBuildingsAndFloors();
        
        // รอให้ DOM อัพเดทแล้วค่อยตั้งค่าอาคารและชั้น
        setTimeout(() => {
            document.getElementById('edit_building_name').value = foundData.building_name;
            updateEditFloors();
            
            setTimeout(() => {
                document.getElementById('edit_floor_level').value = foundData.floor_level;
                
                // แสดง Modal
                const editModal = new bootstrap.Modal(document.getElementById('editEquipmentModal'));
                editModal.show();
            }, 100);
        }, 100);
    } else {
        alert('ไม่พบข้อมูลที่ต้องการแก้ไข');
    }
}

// ฟังก์ชันแปลงวันที่จากรูปแบบไทยเป็นรูปแบบ input date
function formatDateForInput(thaiDate) {
    if (!thaiDate) return '';
    
    // แปลงจาก dd/mm/yyyy เป็น yyyy-mm-dd
    const parts = thaiDate.split('/');
    if (parts.length === 3) {
        return `${parts[2]}-${parts[1].padStart(2, '0')}-${parts[0].padStart(2, '0')}`;
    }
    return '';
}

// Real-time Filter Functions
function filterTable() {
    const globalSearch = document.getElementById('globalSearch').value.toLowerCase();
    const schoolFilter = document.getElementById('schoolFilter').value;
    const buildingFilter = document.getElementById('buildingFilter').value;
    const quantityFilter = document.getElementById('quantityFilter').value;
    
    const rows = document.querySelectorAll('#equipmentTable tbody tr');
    let visibleCount = 0;
    
    rows.forEach(row => {
        let showRow = true;
        
        // Global search filter
        if (globalSearch) {
            const rowText = row.textContent.toLowerCase();
            if (!rowText.includes(globalSearch)) {
                showRow = false;
            }
        }
        
        // School filter
        if (schoolFilter && showRow) {
            const schoolCell = row.cells[2]; // School column
            const schoolText = schoolCell.textContent.trim();
            if (schoolText !== schoolFilter) {
                showRow = false;
            }
        }
        
        // Building filter
        if (buildingFilter && showRow) {
            const buildingCell = row.cells[3]; // Building column
            const buildingText = buildingCell.querySelector('div').textContent.trim();
            if (buildingText !== buildingFilter) {
                showRow = false;
            }
        }
        
        // Quantity filter
        if (quantityFilter && showRow) {
            const quantityCell = row.cells[5]; // Quantity column
            const quantityText = quantityCell.textContent.trim();
            const quantity = parseInt(quantityText);
            
            switch (quantityFilter) {
                case '1':
                    if (quantity !== 1) showRow = false;
                    break;
                case '2':
                    if (quantity !== 2) showRow = false;
                    break;
                case '3':
                    if (quantity < 3) showRow = false;
                    break;
            }
        }
        
        // Show/hide row
        row.style.display = showRow ? '' : 'none';
        if (showRow) visibleCount++;
    });
    
    // Update result count
    document.getElementById('filterResultCount').textContent = `พบ ${visibleCount} รายการ`;
    
    // Show/hide no results message
    const noResults = document.getElementById('noResults');
    const tableBody = document.querySelector('#equipmentTable tbody');
    
    if (visibleCount === 0) {
        noResults.style.display = 'block';
        tableBody.style.display = 'none';
    } else {
        noResults.style.display = 'none';
        tableBody.style.display = '';
    }
}

function clearFilters() {
    document.getElementById('globalSearch').value = '';
    document.getElementById('schoolFilter').value = '';
    document.getElementById('buildingFilter').value = '';
    document.getElementById('quantityFilter').value = '';
    
    filterTable();
}

function exportToExcel() {
    const table = document.getElementById('equipmentTable');
    let csv = [];
    
    // Add headers in Thai
    const headers = [
        'รหัสครุภัณฑ์',
        'ชื่อครุภัณฑ์',
        'โรงเรียน',
        'อาคาร/ชั้น',
        'ห้อง',
        'จำนวน',
        'วันที่ติดตั้ง'
    ];
    csv.push(headers.join(','));
    
    // Add data rows
    for (let i = 1; i < table.rows.length; i++) {
        const row = table.rows[i];
        if (row.style.display !== 'none') {
            const rowData = [];
            for (let j = 0; j < row.cells.length - 1; j++) { // Exclude action column
                let cellText = row.cells[j].textContent.trim();
                // Clean up text for CSV
                cellText = cellText.replace(/,/g, ' ').replace(/\n/g, ' ').replace(/\s+/g, ' ');
                rowData.push(cellText);
            }
            csv.push(rowData.join(','));
        }
    }
    
    const csvString = csv.join('\n');
    const blob = new Blob(['\uFEFF' + csvString], { type: 'text/csv;charset=utf-8;' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `ข้อมูลครุภัณฑ์ในห้องเรียน_${new Date().toISOString().split('T')[0]}.csv`;
    a.click();
    window.URL.revokeObjectURL(url);
}

function printTable() {
    const printWindow = window.open('', '_blank');
    const table = document.getElementById('equipmentTable').cloneNode(true);
    
    // Remove action column
    const rows = table.getElementsByTagName('tr');
    for (let i = 0; i < rows.length; i++) {
        if (rows[i].cells.length > 0) {
            rows[i].deleteCell(-1); // Remove last cell (action column)
        }
    }
    
    printWindow.document.write(`
        <html>
            <head>
                <title>พิมพ์รายการครุภัณฑ์ในห้องเรียน</title>
                <style>
                    body { font-family: 'Sarabun', sans-serif; margin: 20px; }
                    table { width: 100%; border-collapse: collapse; }
                    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                    th { background-color: #f8f9fa; }
                    .badge { padding: 4px 8px; border-radius: 4px; font-size: 12px; }
                    @media print {
                        body { margin: 0; }
                        table { font-size: 12px; }
                    }
                </style>
            </head>
            <body>
                <h2 style="text-align: center;">รายการครุภัณฑ์ในห้องเรียน</h2>
                <p style="text-align: right;">พิมพ์เมื่อ: ${new Date().toLocaleString('th-TH')}</p>
                ${table.outerHTML}
            </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
}

// Update current time
function updateCurrentTime() {
    const now = new Date();
    document.getElementById('currentTime').textContent = 
        `อัปเดตล่าสุด: ${now.toLocaleString('th-TH', { 
            year: 'numeric', 
            month: '2-digit', 
            day: '2-digit',
            hour: '2-digit', 
            minute: '2-digit'
        })}`;
}

// ฟังก์ชันสำหรับ Modal แก้ไข
function updateEditBuildingsAndFloors() {
    const schoolSelect = document.getElementById('edit_school_name');
    const buildingSelect = document.getElementById('edit_building_name');
    const floorSelect = document.getElementById('edit_floor_level');
    
    const selectedSchool = schoolSelect.value;
    
    // ล้าง options เดิม
    buildingSelect.innerHTML = '<option value="">-- เลือกอาคาร --</option>';
    floorSelect.innerHTML = '<option value="">-- เลือกชั้น --</option>';
    
    if (selectedSchool && schoolData[selectedSchool]) {
        // เพิ่มอาคารตามโรงเรียนที่เลือก
        schoolData[selectedSchool].buildings.forEach(building => {
            const option = document.createElement('option');
            option.value = building.name;
            option.textContent = building.name;
            buildingSelect.appendChild(option);
        });
    }
}

function updateEditFloors() {
    const schoolSelect = document.getElementById('edit_school_name');
    const buildingSelect = document.getElementById('edit_building_name');
    const floorSelect = document.getElementById('edit_floor_level');
    
    const selectedSchool = schoolSelect.value;
    const selectedBuilding = buildingSelect.value;
    
    // ล้าง options เดิม
    floorSelect.innerHTML = '<option value="">-- เลือกชั้น --</option>';
    
    if (selectedSchool && selectedBuilding && schoolData[selectedSchool]) {
        const building = schoolData[selectedSchool].buildings.find(b => b.name === selectedBuilding);
        if (building) {
            // เพิ่มชั้นตามอาคารที่เลือก
            building.floors.forEach(floor => {
                const option = document.createElement('option');
                option.value = floor;
                option.textContent = floor;
                floorSelect.appendChild(option);
            });
        }
    }
}

// ฟังก์ชันสำหรับ Modal เพิ่ม
function updateModalBuildingsAndFloors() {
    const schoolSelect = document.getElementById('modal_school_name');
    const buildingSelect = document.getElementById('modal_building_name');
    const floorSelect = document.getElementById('modal_floor_level');
    
    const selectedSchool = schoolSelect.value;
    
    // ล้าง options เดิม
    buildingSelect.innerHTML = '<option value="">-- เลือกอาคาร --</option>';
    floorSelect.innerHTML = '<option value="">-- เลือกชั้น --</option>';
    
    if (selectedSchool && schoolData[selectedSchool]) {
        // เพิ่มอาคารตามโรงเรียนที่เลือก
        schoolData[selectedSchool].buildings.forEach(building => {
            const option = document.createElement('option');
            option.value = building.name;
            option.textContent = building.name;
            buildingSelect.appendChild(option);
        });
    }
}

function updateModalFloors() {
    const schoolSelect = document.getElementById('modal_school_name');
    const buildingSelect = document.getElementById('modal_building_name');
    const floorSelect = document.getElementById('modal_floor_level');
    
    const selectedSchool = schoolSelect.value;
    const selectedBuilding = buildingSelect.value;
    
    // ล้าง options เดิม
    floorSelect.innerHTML = '<option value="">-- เลือกชั้น --</option>';
    
    if (selectedSchool && selectedBuilding && schoolData[selectedSchool]) {
        const building = schoolData[selectedSchool].buildings.find(b => b.name === selectedBuilding);
        if (building) {
            // เพิ่มชั้นตามอาคารที่เลือก
            building.floors.forEach(floor => {
                const option = document.createElement('option');
                option.value = floor;
                option.textContent = floor;
                floorSelect.appendChild(option);
            });
        }
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    updateCurrentTime();
    setInterval(updateCurrentTime, 60000); // Update every minute
    
    // Initialize filter result count
    filterTable();
    
    // เพิ่ม validation สำหรับฟอร์ม
    const addForm = document.getElementById('addForm');
    if (addForm) {
        addForm.addEventListener('submit', function(e) {
            const equipmentCode = document.querySelector('#addForm select[name="equipment_code"]').value;
            const schoolName = document.getElementById('modal_school_name').value;
            const buildingName = document.getElementById('modal_building_name').value;
            const floorLevel = document.getElementById('modal_floor_level').value;
            const roomNumber = document.querySelector('#addForm input[name="room_number"]').value;
            const quantity = document.querySelector('#addForm input[name="equipment_quantity"]').value;
            
            if (!equipmentCode || !schoolName || !buildingName || !floorLevel || !roomNumber || quantity <= 0) {
                e.preventDefault();
                alert('กรุณากรอกข้อมูลให้ครบถ้วน');
                return false;
            }
        });
    }
    
    // Validation สำหรับฟอร์มแก้ไข
    const editForm = document.getElementById('editForm');
    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            const equipmentCode = document.getElementById('edit_equipment_code').value;
            const schoolName = document.getElementById('edit_school_name').value;
            const buildingName = document.getElementById('edit_building_name').value;
            const floorLevel = document.getElementById('edit_floor_level').value;
            const roomNumber = document.getElementById('edit_room_number').value;
            const quantity = document.getElementById('edit_equipment_quantity').value;
            
            if (!equipmentCode || !schoolName || !buildingName || !floorLevel || !roomNumber || quantity <= 0) {
                e.preventDefault();
                alert('กรุณากรอกข้อมูลให้ครบถ้วน');
                return false;
            }
        });
    }
});


</script>

<?php include 'includes/footer.php'; ?>
