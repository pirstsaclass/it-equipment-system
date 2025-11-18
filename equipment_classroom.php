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

// สร้าง CSRF Token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? 0;

// ฟังก์ชันจัดการฟอร์ม
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // ตรวจสอบ CSRF Token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error'] = "Token ไม่ถูกต้อง";
        header('Location: equipment_classroom.php');
        exit;
    }

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

    if (empty($equipment_code) || empty($school_name) || empty($building_name) || empty($floor_level) || empty($room_number) || $equipment_quantity <= 0) {
        $_SESSION['error'] = "กรุณากรอกข้อมูลให้ครบถ้วน";
    } else {
        try {
            // ตรวจสอบว่า equipment_code มีอยู่ในตาราง equipment หรือไม่
            $check_equipment = $db->prepare("SELECT COUNT(*) FROM equipment WHERE equipment_code = ?");
            $check_equipment->execute([$equipment_code]);
            $equipment_exists = $check_equipment->fetchColumn();

            if (!$equipment_exists) {
                $_SESSION['error'] = "รหัสครุภัณฑ์ไม่ถูกต้องหรือไม่มีอยู่ในระบบ";
            } else {
                if ($form_action == 'add') {
                    $stmt = $db->prepare("INSERT INTO equipment_classroom (equipment_code, school_name, building_name, floor_level, room_number, room_name, equipment_quantity, installation_date, placement_notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$equipment_code, $school_name, $building_name, $floor_level, $room_number, $room_name, $equipment_quantity, $installation_date, $placement_notes]);
                    $_SESSION['success'] = "บันทึกข้อมูลการจัดวางครุภัณฑ์เรียบร้อยแล้ว";
                } elseif ($form_action == 'edit') {
                    $stmt = $db->prepare("UPDATE equipment_classroom SET equipment_code = ?, school_name = ?, building_name = ?, floor_level = ?, room_number = ?, room_name = ?, equipment_quantity = ?, installation_date = ?, placement_notes = ?, updated_at = NOW() WHERE id = ?");
                    $stmt->execute([$equipment_code, $school_name, $building_name, $floor_level, $room_number, $room_name, $equipment_quantity, $installation_date, $placement_notes, $form_id]);
                    $_SESSION['success'] = "อัพเดทข้อมูลการจัดวางครุภัณฑ์เรียบร้อยแล้ว";
                }
                header('Location: equipment_classroom.php');
                exit;
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . $e->getMessage();
            
            if ($e->getCode() == '23000') {
                $_SESSION['error'] = "ไม่สามารถบันทึกข้อมูลได้: รหัสครุภัณฑ์ไม่ถูกต้องหรือไม่มีอยู่ในระบบ";
            }
        }
    }
}

// ฟังก์ชันลบข้อมูล
if ($action == 'delete' && $id > 0) {
    try {
        // ดึงข้อมูลก่อนลบเพื่อลบไฟล์ QR Code
        $stmt = $db->prepare("SELECT equipment_code, qr_code_path FROM equipment_classroom WHERE id = ?");
        $stmt->execute([$id]);
        $equipment_data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($equipment_data && !empty($equipment_data['qr_code_path'])) {
            if (file_exists($equipment_data['qr_code_path'])) {
                unlink($equipment_data['qr_code_path']);
            }
        }
        
        $stmt = $db->prepare("DELETE FROM equipment_classroom WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['success'] = "ลบข้อมูลการจัดวางครุภัณฑ์เรียบร้อยแล้ว";
        header('Location: equipment_classroom.php');
        exit;
    } catch (PDOException $e) {
        $_SESSION['error'] = "เกิดข้อผิดพลาด: " . $e->getMessage();
    }
}

// ดึงข้อมูลสำหรับแก้ไข
$equipment_data = [];
if ($action == 'edit' && $id > 0) {
    $stmt = $db->prepare("SELECT * FROM equipment_classroom WHERE id = ?");
    $stmt->execute([$id]);
    $equipment_data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($equipment_data && !empty($equipment_data['installation_date']) && $equipment_data['installation_date'] != '0000-00-00') {
        $equipment_data['installation_date'] = date('Y-m-d', strtotime($equipment_data['installation_date']));
    }
    
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

// ตรวจสอบว่าตาราง departments มีหรือไม่
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

// ดึงรายการโรงเรียน
try {
    if ($has_departments) {
        $school_query = $db->query("SELECT DISTINCT school_name FROM departments ORDER BY school_name");
        $school_list = $school_query->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $school_query = $db->query("SELECT DISTINCT school_name FROM equipment_classroom ORDER BY school_name");
        $school_list = $school_query->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    $school_list = [];
}

// ดึงรายการอาคาร
$building_query = $db->query("SELECT DISTINCT building_name FROM equipment_classroom ORDER BY building_name");
$building_list = $building_query->fetchAll(PDO::FETCH_COLUMN);

// ดึงรายการครุภัณฑ์
$equipment_query = $db->query("SELECT equipment_code, equipment_name, brand_name, model_name FROM equipment ORDER BY equipment_name");
$equipment_options = $equipment_query->fetchAll(PDO::FETCH_ASSOC);

// นับจำนวนครุภัณฑ์ที่ยังไม่มี QR Code
$count_without_qr = $db->query("SELECT COUNT(*) FROM equipment_classroom WHERE qr_code_path IS NULL OR qr_code_path = ''")->fetchColumn();
?>

<!-- Navbar -->
<?php include 'includes/navbar.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div id="layoutSidenav_content">
<main>
    <div class="container-fluid px-4">

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">จัดการครุภัณฑ์ในห้องเรียน</h1>
        <div class="btn-group">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEquipmentModal">
                <i class="fas fa-plus fa-sm text-white-50"></i> เพิ่มการจัดวาง
            </button>
            <button type="button" class="btn btn-info" onclick="openBulkQRModal()">
                <i class="fas fa-qrcode"></i> สร้าง QR Code ทั้งหมด
                <?php if ($count_without_qr > 0): ?>
                    <span class="badge bg-warning text-dark"><?php echo $count_without_qr; ?></span>
                <?php endif; ?>
            </button>
        </div>
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
                            <th>QR Code</th>
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
                                <td class="text-center">
                                    <?php if (!empty($item['qr_code_path']) && file_exists($item['qr_code_path'])): ?>
                                        <button type="button" class="btn btn-sm btn-success" onclick="viewQRCode('<?php echo $item['equipment_code']; ?>')" title="ดู QR Code">
                                            <i class="fas fa-qrcode"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-info" onclick="downloadQRCode('<?php echo $item['equipment_code']; ?>')" title="ดาวน์โหลด QR Code">
                                            <i class="fas fa-download"></i>
                                        </button>
                                    <?php else: ?>
                                        <button type="button" class="btn btn-sm btn-outline-warning" onclick="generateSingleQRCode('<?php echo $item['equipment_code']; ?>')" title="สร้าง QR Code">
                                            <i class="fas fa-plus"></i> สร้าง
                                        </button>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php 
                                    if (!empty($item['installation_date']) && $item['installation_date'] != '0000-00-00') {
                                        echo date('d/m/Y', strtotime($item['installation_date']));
                                    } else {
                                        echo '-';
                                    }
                                    ?>
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
                                <td colspan="9" class="text-center text-muted py-4">
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
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <div class="modal-header">
                    <h5 class="modal-title" id="addEquipmentModalLabel">เพิ่มการจัดวางครุภัณฑ์</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">รหัสครุภัณฑ์ <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <select class="form-control" name="equipment_code" id="add_equipment_code" required onchange="updateQRCodeButton()">
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
                                    <button type="button" class="btn btn-outline-info" id="generateQRBtn" onclick="generateQRCodeForEquipment()" disabled>
                                        <i class="fas fa-qrcode"></i> สร้าง QR
                                    </button>
                                </div>
                                <small class="form-text text-muted">เลือกครุภัณฑ์แล้วกดปุ่มสร้าง QR Code</small>
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

                    <!-- QR Code Status -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <div id="qrCodeStatus" class="alert alert-info d-none">
                                <i class="fas fa-info-circle"></i> <span id="qrStatusText">สถานะ QR Code</span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">อาคาร <span class="text-danger">*</span></label>
                                <select class="form-control" name="building_name" id="modal_building_name" required onchange="updateModalFloors()">
                                    <option value="">-- เลือกอาคาร --</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">ชั้น <span class="text-danger">*</span></label>
                                <select class="form-control" name="floor_level" id="modal_floor_level" required>
                                    <option value="">-- เลือกชั้น --</option>
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
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <div class="modal-header">
                    <h5 class="modal-title" id="editEquipmentModalLabel">แก้ไขการจัดวางครุภัณฑ์</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">รหัสครุภัณฑ์ <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <select class="form-control" name="equipment_code" id="edit_equipment_code" required onchange="updateEditQRCodeButton()">
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
                                    <button type="button" class="btn btn-outline-info" id="editGenerateQRBtn" onclick="generateQRCodeForEditEquipment()">
                                        <i class="fas fa-qrcode"></i> สร้าง QR
                                    </button>
                                </div>
                                <small class="form-text text-muted">สร้างหรืออัพเดท QR Code สำหรับครุภัณฑ์นี้</small>
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

                    <!-- QR Code Status -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <div id="editQrCodeStatus" class="alert alert-info d-none">
                                <i class="fas fa-info-circle"></i> <span id="editQrStatusText">สถานะ QR Code</span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">อาคาร <span class="text-danger">*</span></label>
                                <select class="form-control" name="building_name" id="edit_building_name" required onchange="updateEditFloors()">
                                    <option value="">-- เลือกอาคาร --</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">ชั้น <span class="text-danger">*</span></label>
                                <select class="form-control" name="floor_level" id="edit_floor_level" required>
                                    <option value="">-- เลือกชั้น --</option>
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

<!-- Modal สำหรับสร้าง QR Code ทั้งหมด -->
<div class="modal fade" id="bulkQRModal" tabindex="-1" aria-labelledby="bulkQRModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="bulkQRModalLabel">
                    <i class="fas fa-qrcode"></i> สร้าง QR Code ทั้งหมด
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> 
                    <strong>ข้อมูล:</strong> พบครุภัณฑ์ที่ยังไม่มี QR Code จำนวน <strong><?php echo $count_without_qr; ?></strong> รายการ
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">ตัวเลือกการสร้าง QR Code:</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="qr_option" id="qr_new_only" value="new_only" checked>
                        <label class="form-check-label" for="qr_new_only">
                            <i class="fas fa-plus-circle text-success"></i> สร้างเฉพาะรายการที่ยังไม่มี QR Code (<?php echo $count_without_qr; ?> รายการ)
                        </label>
                    </div>
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="radio" name="qr_option" id="qr_all" value="all">
                        <label class="form-check-label" for="qr_all">
                            <i class="fas fa-sync-alt text-warning"></i> สร้างใหม่ทั้งหมด (<?php echo count($equipment_list); ?> รายการ)
                        </label>
                        <small class="form-text text-muted d-block ms-4">
                            ⚠️ จะสร้าง QR Code ใหม่ทับของเดิมทั้งหมด
                        </small>
                    </div>
                </div>

                <div class="progress mt-3" style="display: none;" id="bulkQRProgress">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" 
                         aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                        0%
                    </div>
                </div>

                <div id="bulkQRStatus" class="mt-3">
                    <!-- แสดงสถานะการสร้าง QR Code -->
                </div>

                <div id="bulkQRResult" class="mt-3" style="display: none;">
                    <div class="alert alert-success">
                        <h6><i class="fas fa-check-circle"></i> สร้าง QR Code สำเร็จ!</h6>
                        <div id="resultDetails"></div>
                        <button type="button" class="btn btn-sm btn-outline-success mt-2" onclick="location.reload()">
                            <i class="fas fa-refresh"></i> รีเฟรชหน้า
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="bulkQRCloseBtn">ปิด</button>
                <button type="button" class="btn btn-info" onclick="startBulkQRGeneration()" id="bulkQRStartBtn">
                    <i class="fas fa-play"></i> เริ่มสร้าง QR Code
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal สำหรับแสดง QR Code -->
<div class="modal fade" id="qrCodeModal" tabindex="-1" aria-labelledby="qrCodeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="qrCodeModalLabel">
                    <i class="fas fa-qrcode"></i> <span id="qrCodeTitle">QR Code</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="qrCodeImage" src="" alt="QR Code" class="img-fluid mb-3" style="max-height: 300px;">
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-success" onclick="downloadQRCodeFromModal()">
                        <i class="fas fa-download"></i> ดาวน์โหลด QR Code
                    </button>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> ปิด
                    </button>
                </div>
            </div>
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

// ==================== QR Code Modal Functions ====================

function viewQRCode(equipmentCode) {
    console.log('View QR Code for:', equipmentCode);
    
    // แสดง loading state
    const qrCodeImage = document.getElementById('qrCodeImage');
    const qrCodeTitle = document.getElementById('qrCodeTitle');
    
    qrCodeImage.src = '';
    qrCodeImage.alt = 'กำลังโหลด...';
    qrCodeImage.style.display = 'block';
    qrCodeTitle.textContent = `QR Code - ${equipmentCode}`;
    
    // ซ่อน error message ถ้ามี
    const errorDiv = document.getElementById('qrCodeError');
    if (errorDiv) {
        errorDiv.style.display = 'none';
    }
    
    // แสดง Modal ก่อน
    const qrModal = new bootstrap.Modal(document.getElementById('qrCodeModal'));
    qrModal.show();
    
    // สร้าง URL สำหรับดึง QR Code
    const url = `get_qr_code_image.php?equipment_code=${encodeURIComponent(equipmentCode)}&t=${new Date().getTime()}`;
    console.log('Fetching QR code from:', url);
    
    // ส่ง request ไปดึงข้อมูล QR Code
    fetch(url)
        .then(response => {
            console.log('Response status:', response.status);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('QR Code data:', data);
            
            if (data.success && data.qr_code_path) {
                // ตั้งค่า src ของภาพและจัดการ events
                qrCodeImage.onload = function() {
                    console.log('QR Code loaded successfully');
                };
                qrCodeImage.onerror = function() {
                    showQRCodeError('ไม่สามารถโหลดไฟล์ QR Code ได้');
                    qrCodeImage.style.display = 'none';
                };
                qrCodeImage.src = data.qr_code_path + '?t=' + new Date().getTime();
                qrCodeImage.alt = `QR Code for ${equipmentCode}`;
                
            } else {
                showQRCodeError(data.message || 'ไม่พบ QR Code สำหรับครุภัณฑ์นี้');
                qrCodeImage.style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Error fetching QR code:', error);
            showQRCodeError('เกิดข้อผิดพลาดในการดึง QR Code: ' + error.message);
            qrCodeImage.style.display = 'none';
        });
}

function showQRCodeError(message) {
    // สร้างหรืออัพเดท element แสดง error
    let errorDiv = document.getElementById('qrCodeError');
    if (!errorDiv) {
        errorDiv = document.createElement('div');
        errorDiv.id = 'qrCodeError';
        errorDiv.className = 'alert alert-danger';
        document.querySelector('#qrCodeModal .modal-body').prepend(errorDiv);
    }
    errorDiv.innerHTML = `
        <i class="fas fa-exclamation-triangle"></i> ${message}
        <hr>
        <small class="text-muted">
            <strong>วิธีแก้ไข:</strong><br>
            1. ตรวจสอบว่าไฟล์ get_qr_code_image.php มีอยู่และทำงานปกติ<br>
            2. ลองสร้าง QR Code ใหม่สำหรับครุภัณฑ์นี้<br>
            3. ตรวจสอบว่าเซิร์ฟเวอร์มีสิทธิ์เข้าถึงโฟลเดอร์เก็บ QR Code
        </small>
    `;
    errorDiv.style.display = 'block';
}

function downloadQRCodeFromModal() {
    const qrImage = document.getElementById('qrCodeImage').src;
    const equipmentCode = document.getElementById('qrCodeTitle').textContent.replace('QR Code - ', '');
    
    if (!qrImage || qrImage.includes('undefined')) {
        alert('ไม่มี QR Code ให้ดาวน์โหลด');
        return;
    }
    
    // ใช้ fetch เพื่อดาวน์โหลดและแปลงเป็น blob
    fetch(qrImage)
        .then(response => response.blob())
        .then(blob => {
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `qr_code_${equipmentCode}.png`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
        })
        .catch(error => {
            console.error('Error downloading QR code:', error);
            alert('ไม่สามารถดาวน์โหลด QR Code ได้: ' + error.message);
        });
}

// ฟังก์ชันดาวน์โหลด QR Code เดิม (ยังใช้กับปุ่มดาวน์โหลดในตาราง)
function downloadQRCode(equipmentCode) {
    // เปิด Modal แทนการเปิดหน้าใหม่
    viewQRCode(equipmentCode);
}

// ==================== Bulk QR Code Generation Functions ====================

function openBulkQRModal() {
    const modal = new bootstrap.Modal(document.getElementById('bulkQRModal'));
    modal.show();
    
    // รีเซ็ตสถานะ
    document.getElementById('bulkQRProgress').style.display = 'none';
    document.getElementById('bulkQRStatus').innerHTML = '';
    document.getElementById('bulkQRResult').style.display = 'none';
    document.getElementById('bulkQRStartBtn').disabled = false;
    document.getElementById('qr_new_only').checked = true;
}

function startBulkQRGeneration() {
    const option = document.querySelector('input[name="qr_option"]:checked').value;
    const startBtn = document.getElementById('bulkQRStartBtn');
    const closeBtn = document.getElementById('bulkQRCloseBtn');
    const progressBar = document.getElementById('bulkQRProgress');
    const statusDiv = document.getElementById('bulkQRStatus');
    const resultDiv = document.getElementById('bulkQRResult');
    
    // ยืนยันการดำเนินการ
    const confirmMsg = option === 'all' 
        ? 'คุณแน่ใจหรือไม่ที่จะสร้าง QR Code ใหม่ทั้งหมด?\nQR Code เดิมจะถูกแทนที่!' 
        : 'เริ่มสร้าง QR Code สำหรับรายการที่ยังไม่มี QR Code?';
    
    if (!confirm(confirmMsg)) {
        return;
    }
    
    // ปิดปุ่มเริ่ม
    startBtn.disabled = true;
    closeBtn.disabled = true;
    
    // แสดง progress bar
    progressBar.style.display = 'block';
    statusDiv.innerHTML = '<div class="alert alert-info"><i class="fas fa-spinner fa-spin"></i> กำลังสร้าง QR Code...</div>';
    
    // เรียก API
    fetch('generate_qr_code.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=bulk&option=${option}`
    })
    .then(response => response.json())
    .then(data => {
        console.log('Bulk QR generation result:', data);
        
        if (data.success) {
            // อัพเดท progress bar
            const progressBarInner = progressBar.querySelector('.progress-bar');
            progressBarInner.style.width = '100%';
            progressBarInner.textContent = '100%';
            progressBarInner.classList.remove('progress-bar-animated');
            progressBarInner.classList.add('bg-success');
            
            // แสดงผลลัพธ์
            statusDiv.innerHTML = '';
            resultDiv.style.display = 'block';
            
            let resultHTML = `
                <p class="mb-2"><strong>สร้างสำเร็จ:</strong> ${data.created} รายการ</p>
            `;
            
            if (data.skipped > 0) {
                resultHTML += `<p class="mb-2"><strong>ข้าม:</strong> ${data.skipped} รายการ (มี QR Code อยู่แล้ว)</p>`;
            }
            
            if (data.errors > 0) {
                resultHTML += `<p class="mb-2 text-danger"><strong>ผิดพลาด:</strong> ${data.errors} รายการ</p>`;
            }
            
            document.getElementById('resultDetails').innerHTML = resultHTML;
            
        } else {
            statusDiv.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> เกิดข้อผิดพลาด: ${data.message || 'ไม่สามารถสร้าง QR Code ได้'}
                </div>
            `;
            progressBar.style.display = 'none';
        }
        
        // เปิดปุ่มปิด
        closeBtn.disabled = false;
        
    })
    .catch(error => {
        console.error('Error:', error);
        statusDiv.innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i> เกิดข้อผิดพลาดในการสร้าง QR Code: ${error.message}
            </div>
        `;
        progressBar.style.display = 'none';
        closeBtn.disabled = false;
    });
}

// ==================== QR Code Generation Functions ====================

function updateQRCodeButton() {
    const equipmentCode = document.getElementById('add_equipment_code').value;
    const generateQRBtn = document.getElementById('generateQRBtn');
    const qrStatus = document.getElementById('qrCodeStatus');
    const qrStatusText = document.getElementById('qrStatusText');
    
    if (equipmentCode) {
        generateQRBtn.disabled = false;
        checkQRCodeStatus(equipmentCode, qrStatus, qrStatusText);
    } else {
        generateQRBtn.disabled = true;
        qrStatus.classList.add('d-none');
    }
}

function updateEditQRCodeButton() {
    const equipmentCode = document.getElementById('edit_equipment_code').value;
    const generateQRBtn = document.getElementById('editGenerateQRBtn');
    const qrStatus = document.getElementById('editQrCodeStatus');
    const qrStatusText = document.getElementById('editQrStatusText');
    
    if (equipmentCode) {
        generateQRBtn.disabled = false;
        checkQRCodeStatus(equipmentCode, qrStatus, qrStatusText);
    } else {
        generateQRBtn.disabled = true;
        qrStatus.classList.add('d-none');
    }
}

function checkQRCodeStatus(equipmentCode, statusElement, statusTextElement) {
    fetch(`check_qr_code_status.php?equipment_code=${equipmentCode}`)
        .then(response => response.json())
        .then(data => {
            if (data.exists) {
                statusElement.className = 'alert alert-success';
                statusTextElement.textContent = `✓ มี QR Code อยู่แล้ว (อัพเดทล่าสุด: ${data.last_updated})`;
                statusElement.classList.remove('d-none');
                
                if (!statusElement.querySelector('.view-qr-btn')) {
                    const viewButton = document.createElement('button');
                    viewButton.type = 'button';
                    viewButton.className = 'btn btn-sm btn-outline-success ms-2 view-qr-btn';
                    viewButton.innerHTML = '<i class="fas fa-eye"></i> ดู QR Code';
                    viewButton.onclick = function() {
                        viewQRCode(equipmentCode);
                    };
                    statusElement.appendChild(viewButton);
                }
            } else {
                statusElement.className = 'alert alert-warning';
                statusTextElement.textContent = '⚠ ยังไม่มี QR Code สำหรับครุภัณฑ์นี้';
                statusElement.classList.remove('d-none');
                
                const existingBtn = statusElement.querySelector('.view-qr-btn');
                if (existingBtn) {
                    existingBtn.remove();
                }
            }
        })
        .catch(error => {
            console.error('Error checking QR code status:', error);
            statusElement.className = 'alert alert-info';
            statusTextElement.textContent = 'ℹ สามารถสร้าง QR Code สำหรับครุภัณฑ์นี้ได้';
            statusElement.classList.remove('d-none');
        });
}

function generateQRCodeForEquipment() {
    const equipmentCode = document.getElementById('add_equipment_code').value;
    if (!equipmentCode) {
        alert('กรุณาเลือกรหัสครุภัณฑ์ก่อน');
        return;
    }
    generateQRCode(equipmentCode, 'qrCodeStatus', 'qrStatusText');
}

function generateQRCodeForEditEquipment() {
    const equipmentCode = document.getElementById('edit_equipment_code').value;
    if (!equipmentCode) {
        alert('กรุณาเลือกรหัสครุภัณฑ์ก่อน');
        return;
    }
    generateQRCode(equipmentCode, 'editQrCodeStatus', 'editQrStatusText');
}

function generateQRCode(equipmentCode, statusElementId, statusTextId) {
    const statusElement = document.getElementById(statusElementId);
    const statusText = document.getElementById(statusTextId);
    
    statusElement.className = 'alert alert-info';
    statusText.textContent = '⏳ กำลังสร้าง QR Code...';
    statusElement.classList.remove('d-none');
    
    const existingBtn = statusElement.querySelector('.view-qr-btn');
    if (existingBtn) {
        existingBtn.remove();
    }
    
    fetch('generate_qr_code.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `equipment_code=${encodeURIComponent(equipmentCode)}&action=single`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            statusElement.className = 'alert alert-success';
            statusText.textContent = `✓ สร้าง QR Code สำเร็จ! ${data.message || ''}`;
            
            const viewButton = document.createElement('button');
            viewButton.type = 'button';
            viewButton.className = 'btn btn-sm btn-outline-success ms-2 view-qr-btn';
            viewButton.innerHTML = '<i class="fas fa-eye"></i> ดู QR Code';
            viewButton.onclick = function() {
                viewQRCode(equipmentCode);
            };
            statusElement.appendChild(viewButton);
            
            if (statusElementId === 'editQrCodeStatus') {
                setTimeout(() => {
                    location.reload();
                }, 2000);
            }
        } else {
            statusElement.className = 'alert alert-danger';
            statusText.textContent = `✖ การสร้าง QR Code ล้มเหลว: ${data.message || 'เกิดข้อผิดพลาด'}`;
        }
    })
    .catch(error => {
        console.error('Error generating QR code:', error);
        statusElement.className = 'alert alert-danger';
        statusText.textContent = '✖ เกิดข้อผิดพลาดในการสร้าง QR Code: ' + error.message;
    });
}

function generateSingleQRCode(equipmentCode) {
    if (confirm(`ต้องการสร้าง QR Code สำหรับครุภัณฑ์ ${equipmentCode} ใช่หรือไม่?`)) {
        fetch('generate_qr_code.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `equipment_code=${equipmentCode}&action=single`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('สร้าง QR Code สำเร็จ!');
                location.reload();
            } else {
                alert('การสร้าง QR Code ล้มเหลว: ' + (data.message || 'เกิดข้อผิดพลาด'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('เกิดข้อผิดพลาดในการสร้าง QR Code');
        });
    }
}

// ==================== Existing Functions ====================

// ฟังก์ชันเปิด Modal แก้ไข
function openEditModal(id) {
    const rows = document.querySelectorAll('#equipmentTable tbody tr');
    let foundData = null;
    
    rows.forEach(row => {
        const editButton = row.querySelector('button[onclick*="openEditModal"]');
        if (editButton) {
            const buttonOnclick = editButton.getAttribute('onclick');
            const match = buttonOnclick.match(/openEditModal\((\d+)\)/);
            if (match && match[1] == id) {
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
                    installation_date: cells[7].textContent.trim() !== '-' ? 
                        formatDateForInput(cells[7].textContent.trim()) : ''
                };
            }
        }
    });
    
    if (foundData) {
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_equipment_code').value = foundData.equipment_code;
        document.getElementById('edit_school_name').value = foundData.school_name;
        document.getElementById('edit_room_number').value = foundData.room_number;
        document.getElementById('edit_room_name').value = foundData.room_name;
        document.getElementById('edit_equipment_quantity').value = foundData.equipment_quantity;
        document.getElementById('edit_installation_date').value = foundData.installation_date;
        
        updateEditBuildingsAndFloors();
        
        setTimeout(() => {
            document.getElementById('edit_building_name').value = foundData.building_name;
            updateEditFloors();
            
            setTimeout(() => {
                document.getElementById('edit_floor_level').value = foundData.floor_level;
                updateEditQRCodeButton();
                
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
        
        if (globalSearch) {
            const rowText = row.textContent.toLowerCase();
            if (!rowText.includes(globalSearch)) {
                showRow = false;
            }
        }
        
        if (schoolFilter && showRow) {
            const schoolCell = row.cells[2];
            const schoolText = schoolCell.textContent.trim();
            if (schoolText !== schoolFilter) {
                showRow = false;
            }
        }
        
        if (buildingFilter && showRow) {
            const buildingCell = row.cells[3];
            const buildingText = buildingCell.querySelector('div').textContent.trim();
            if (buildingText !== buildingFilter) {
                showRow = false;
            }
        }
        
        if (quantityFilter && showRow) {
            const quantityCell = row.cells[5];
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
        
        row.style.display = showRow ? '' : 'none';
        if (showRow) visibleCount++;
    });
    
    document.getElementById('filterResultCount').textContent = `พบ ${visibleCount} รายการ`;
    
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
    
    const headers = [
        'รหัสครุภัณฑ์',
        'ชื่อครุภัณฑ์',
        'โรงเรียน',
        'อาคาร/ชั้น',
        'ห้อง',
        'จำนวน',
        'QR Code',
        'วันที่ติดตั้ง'
    ];
    csv.push(headers.join(','));
    
    for (let i = 1; i < table.rows.length; i++) {
        const row = table.rows[i];
        if (row.style.display !== 'none') {
            const rowData = [];
            for (let j = 0; j < row.cells.length - 1; j++) {
                let cellText = row.cells[j].textContent.trim();
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
    
    const rows = table.getElementsByTagName('tr');
    for (let i = 0; i < rows.length; i++) {
        if (rows[i].cells.length > 0) {
            rows[i].deleteCell(-1);
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

// ฟังก์ชันสำหรับ Modal แก้ไข
function updateEditBuildingsAndFloors() {
    const schoolSelect = document.getElementById('edit_school_name');
    const buildingSelect = document.getElementById('edit_building_name');
    const floorSelect = document.getElementById('edit_floor_level');
    
    const selectedSchool = schoolSelect.value;
    
    buildingSelect.innerHTML = '<option value="">-- เลือกอาคาร --</option>';
    floorSelect.innerHTML = '<option value="">-- เลือกชั้น --</option>';
    
    if (selectedSchool && schoolData[selectedSchool]) {
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
    
    floorSelect.innerHTML = '<option value="">-- เลือกชั้น --</option>';
    
    if (selectedSchool && selectedBuilding && schoolData[selectedSchool]) {
        const building = schoolData[selectedSchool].buildings.find(b => b.name === selectedBuilding);
        if (building) {
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
    
    buildingSelect.innerHTML = '<option value="">-- เลือกอาคาร --</option>';
    floorSelect.innerHTML = '<option value="">-- เลือกชั้น --</option>';
    
    if (selectedSchool && schoolData[selectedSchool]) {
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
    
    floorSelect.innerHTML = '<option value="">-- เลือกชั้น --</option>';
    
    if (selectedSchool && selectedBuilding && schoolData[selectedSchool]) {
        const building = schoolData[selectedSchool].buildings.find(b => b.name === selectedBuilding);
        if (building) {
            building.floors.forEach(floor => {
                const option = document.createElement('option');
                option.value = floor;
                option.textContent = floor;
                floorSelect.appendChild(option);
            });
        }
    }
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

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    updateCurrentTime();
    setInterval(updateCurrentTime, 60000);
    
    filterTable();
    
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