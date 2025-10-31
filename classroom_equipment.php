<?php
require_once 'includes/header.php';

// ตั้งค่า charset สำหรับการเชื่อมต่อ
$db->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");

// ตัวกรองเริ่มต้น
$selected_school = isset($_GET['school']) ? $_GET['school'] : '';
$selected_building = isset($_GET['building']) ? $_GET['building'] : '';
$selected_floor = isset($_GET['floor']) ? $_GET['floor'] : '';

// CRUD Operations - ใช้ equipment_code แทน id
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $equipment_code = isset($_GET['equipment_code']) ? $_GET['equipment_code'] : null;
    
    if ($action == 'delete' && $equipment_code) {
        $stmt = $db->prepare("DELETE FROM classroom_equipment WHERE equipment_code = ?");
        $stmt->execute([$equipment_code]);
        $_SESSION['success'] = "ลบข้อมูลอุปกรณ์เรียบร้อยแล้ว";
        
        header("Location: classroom_equipment.php?school=" . $selected_school . "&building=" . $selected_building . "&floor=" . $selected_floor);
        exit();
    }
}

if ($_POST) {
    if (isset($_POST['add_classroom_equipment'])) {
        try {
            // ตรวจสอบว่ามี equipment_code นี้อยู่แล้วใน classroom_equipment หรือไม่
            $check_stmt = $db->prepare("SELECT COUNT(*) FROM classroom_equipment WHERE equipment_code = ? COLLATE utf8mb4_unicode_ci");
            $check_stmt->execute([$_POST['equipment_code']]);
            $exists = $check_stmt->fetchColumn();
            
            if ($exists > 0) {
                $_SESSION['error'] = "รหัสครุภัณฑ์นี้มีอยู่ในห้องเรียนแล้ว กรุณาเลือกรหัสอื่น";
            } else {
                $stmt = $db->prepare("INSERT INTO classroom_equipment (equipment_code, school, building, floor, room, room_name, quantity, installation_date, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $_POST['equipment_code'],
                    $_POST['school'],
                    $_POST['building'],
                    $_POST['floor'],
                    $_POST['room'],
                    $_POST['room_name'],
                    $_POST['quantity'],
                    !empty($_POST['installation_date']) ? $_POST['installation_date'] : null,
                    $_POST['notes']
                ]);
                $_SESSION['success'] = "เพิ่มข้อมูลอุปกรณ์ในห้องเรียนเรียบร้อยแล้ว";
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "เกิดข้อผิดพลาด: " . $e->getMessage();
        }
        
        header("Location: classroom_equipment.php?school=" . $_POST['school'] . "&building=" . $_POST['building'] . "&floor=" . $_POST['floor']);
        exit();
    }
    
    if (isset($_POST['edit_classroom_equipment'])) {
        try {
            $original_equipment_code = $_POST['original_equipment_code'];
            $new_equipment_code = $_POST['equipment_code'];
            
            // ตรวจสอบว่ามีรหัสครุภัณฑ์นี้อยู่แล้วหรือไม่ (ถ้าเปลี่ยนรหัส)
            if ($original_equipment_code != $new_equipment_code) {
                $check_stmt = $db->prepare("SELECT COUNT(*) FROM classroom_equipment WHERE equipment_code = ? COLLATE utf8mb4_unicode_ci");
                $check_stmt->execute([$new_equipment_code]);
                $exists = $check_stmt->fetchColumn();
                
                if ($exists > 0) {
                    $_SESSION['error'] = "รหัสครุภัณฑ์นี้มีอยู่ในห้องเรียนแล้ว กรุณาเลือกรหัสอื่น";
                    header("Location: classroom_equipment.php?school=" . $_POST['school'] . "&building=" . $_POST['building'] . "&floor=" . $_POST['floor']);
                    exit();
                }
            }
            
            $stmt = $db->prepare("UPDATE classroom_equipment SET equipment_code=?, school=?, building=?, floor=?, room=?, room_name=?, quantity=?, installation_date=?, notes=?, updated_at=NOW() WHERE equipment_code=? COLLATE utf8mb4_unicode_ci");
            $stmt->execute([
                $new_equipment_code,
                $_POST['school'],
                $_POST['building'],
                $_POST['floor'],
                $_POST['room'],
                $_POST['room_name'],
                $_POST['quantity'],
                !empty($_POST['installation_date']) ? $_POST['installation_date'] : null,
                $_POST['notes'],
                $original_equipment_code
            ]);
            $_SESSION['success'] = "แก้ไขข้อมูลอุปกรณ์เรียบร้อยแล้ว";
        } catch (PDOException $e) {
            $_SESSION['error'] = "เกิดข้อผิดพลาด: " . $e->getMessage();
        }
        
        header("Location: classroom_equipment.php?school=" . $_POST['school'] . "&building=" . $_POST['building'] . "&floor=" . $_POST['floor']);
        exit();
    }
}

// Get equipment list for dropdown - แก้ไข query ใช้ JOIN กับตาราง categories
$equipment_query = "SELECT e.equipment_code, e.equipment_name, c.name as category_name 
                    FROM equipment e 
                    LEFT JOIN categories c ON e.category_id = c.id 
                    ORDER BY e.equipment_code";
$equipment_list = $db->query($equipment_query)->fetchAll(PDO::FETCH_ASSOC);

// Get filter options
$schools_list = ['โรงเรียนวารีเชียงใหม่', 'โรงเรียนอนุบาลวารีเชียงใหม่', 'โรงเรียนนานาชาติวารีเชียงใหม่'];

$buildings_list = [];
$floors_list = [];

if ($selected_school) {
    $buildings_query = "SELECT DISTINCT building FROM classroom_equipment WHERE school = ? COLLATE utf8mb4_unicode_ci ORDER BY building";
    $buildings_stmt = $db->prepare($buildings_query);
    $buildings_stmt->execute([$selected_school]);
    $buildings_list = $buildings_stmt->fetchAll(PDO::FETCH_COLUMN);
}

if ($selected_school && $selected_building) {
    $floors_query = "SELECT DISTINCT floor FROM classroom_equipment WHERE school = ? AND building = ? COLLATE utf8mb4_unicode_ci ORDER BY floor";
    $floors_stmt = $db->prepare($floors_query);
    $floors_stmt->execute([$selected_school, $selected_building]);
    $floors_list = $floors_stmt->fetchAll(PDO::FETCH_COLUMN);
}

// Get classroom equipment list with filters - แก้ไข query ใช้ JOIN กับตาราง categories
$where_conditions = [];
$params = [];

if ($selected_school) {
    $where_conditions[] = "ce.school = ? COLLATE utf8mb4_unicode_ci";
    $params[] = $selected_school;
}

if ($selected_building) {
    $where_conditions[] = "ce.building = ? COLLATE utf8mb4_unicode_ci";
    $params[] = $selected_building;
}

if ($selected_floor) {
    $where_conditions[] = "ce.floor = ? COLLATE utf8mb4_unicode_ci";
    $params[] = $selected_floor;
}

$where_sql = "";
if (!empty($where_conditions)) {
    $where_sql = "WHERE " . implode(" AND ", $where_conditions);
}

$classroom_equipment_query = "
    SELECT ce.*, 
           e.equipment_name, 
           c.name as category_name, 
           e.brand, 
           e.model,
           e.equipment_status
    FROM classroom_equipment ce
    LEFT JOIN equipment e ON ce.equipment_code = e.equipment_code COLLATE utf8mb4_unicode_ci
    LEFT JOIN categories c ON e.category_id = c.id
    $where_sql
    ORDER BY ce.school, ce.building, ce.floor, ce.room, ce.created_at DESC
";

$classroom_equipment_stmt = $db->prepare($classroom_equipment_query);
$classroom_equipment_stmt->execute($params);
$classroom_equipment_list = $classroom_equipment_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'includes/sidebar.php'; ?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <?php include 'includes/navbar.php'; ?>

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">จัดการอุปกรณ์ในห้องเรียน</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#classroomEquipmentModal" onclick="clearForm()">
            <i class="fas fa-plus"></i> เพิ่มอุปกรณ์
        </button>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Filter Section -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h5 class="m-0 font-weight-bold">ตัวกรองข้อมูล</h5>
        </div>
        <div class="card-body">
            <form method="GET" id="filterForm">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">โรงเรียน</label>
                        <select class="form-control" name="school" id="schoolFilter" onchange="this.form.submit()">
                            <option value="">ทั้งหมด</option>
                            <?php foreach($schools_list as $school): ?>
                                <option value="<?php echo $school; ?>" <?php echo $selected_school == $school ? 'selected' : ''; ?>>
                                    <?php echo $school; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">ตึก/อาคาร</label>
                        <select class="form-control" name="building" id="buildingFilter" onchange="this.form.submit()">
                            <option value="">ทั้งหมด</option>
                            <?php foreach($buildings_list as $building): ?>
                                <option value="<?php echo $building; ?>" <?php echo $selected_building == $building ? 'selected' : ''; ?>>
                                    <?php echo $building; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">ชั้น</label>
                        <select class="form-control" name="floor" onchange="this.form.submit()">
                            <option value="">ทั้งหมด</option>
                            <?php foreach($floors_list as $floor): ?>
                                <option value="<?php echo $floor; ?>" <?php echo $selected_floor == $floor ? 'selected' : ''; ?>>
                                    <?php echo $floor; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Equipment List -->
    <div class="card shadow mb-4" id="equipmentListCard">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h5 class="m-0 font-weight-bold">
                รายการอุปกรณ์ในห้องเรียน
                <?php if ($selected_school) echo " - " . $selected_school; ?>
                <?php if ($selected_building) echo " - " . $selected_building; ?>
                <?php if ($selected_floor) echo " - " . $selected_floor; ?>
            </h5>
            <div class="d-flex gap-2 align-items-center">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="autoRefresh" checked>
                    <label class="form-check-label" for="autoRefresh">อัพเดทแบบเรียลไทม์</label>
                </div>
                <span class="badge bg-info" id="lastUpdate">
                    <i class="fas fa-clock"></i> <span id="updateTime">--:--:--</span>
                </span>
            </div>
        </div>
        <div class="card-body" id="equipmentTableContainer">
            <?php if (empty($classroom_equipment_list)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">ไม่พบข้อมูลอุปกรณ์</h5>
                    <p class="text-muted">กรุณาเลือกตัวกรองหรือเพิ่มข้อมูลอุปกรณ์ใหม่</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                        <thead class="table-light">
                            <tr>
                                <th width="10%">รหัสครุภัณฑ์</th>
                                <th width="15%">ชื่ออุปกรณ์</th>
                                <th width="10%">หมวดหมู่</th>
                                <th width="10%">โรงเรียน</th>
                                <th width="10%">ตึก</th>
                                <th width="8%">ชั้น</th>
                                <th width="8%">ห้อง</th>
                                <th width="12%">ชื่อห้อง</th>
                                <th width="5%">จำนวน</th>
                                <th width="7%">สถานะ</th>
                                <th width="5%">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($classroom_equipment_list as $item): ?>
                            <tr>
                                <td><span class="badge bg-dark"><?php echo $item['equipment_code']; ?></span></td>
                                <td>
                                    <?php echo $item['equipment_name']; ?>
                                    <?php if ($item['brand']): ?>
                                        <br><small class="text-muted"><?php echo $item['brand']; ?> <?php echo $item['model']; ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><span class="badge bg-info"><?php echo $item['category_name']; ?></span></td>
                                <td><?php echo $item['school']; ?></td>
                                <td><?php echo $item['building']; ?></td>
                                <td><?php echo $item['floor']; ?></td>
                                <td><?php echo $item['room']; ?></td>
                                <td><?php echo $item['room_name']; ?></td>
                                <td class="text-center"><span class="badge bg-primary"><?php echo $item['quantity']; ?></span></td>
                                <td>
                                    <span class="badge 
                                        <?php 
                                        switch($item['equipment_status']) {
                                            case 'ใช้งานได้': echo 'bg-success'; break;
                                            case 'ชำรุด': echo 'bg-danger'; break;
                                            case 'ซ่อม': echo 'bg-warning'; break;
                                            case 'จำหน่าย': echo 'bg-secondary'; break;
                                            default: echo 'bg-dark';
                                        }
                                        ?>">
                                        <?php echo $item['equipment_status']; ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-primary" 
                                                data-bs-toggle="modal" data-bs-target="#classroomEquipmentModal" 
                                                onclick='editClassroomEquipment(<?php echo json_encode($item); ?>)'>
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="classroom_equipment.php?action=delete&equipment_code=<?php echo $item['equipment_code']; ?>&school=<?php echo $selected_school; ?>&building=<?php echo $selected_building; ?>&floor=<?php echo $selected_floor; ?>" 
                                           class="btn btn-sm btn-danger" 
                                           onclick="return confirm('คุณแน่ใจหรือไม่?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">จำนวนรายการทั้งหมด</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo count($classroom_equipment_list); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">จำนวนอุปกรณ์รวม</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php 
                                $total_qty = array_sum(array_column($classroom_equipment_list, 'quantity'));
                                echo number_format($total_qty);
                                ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-boxes fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">ห้องเรียน/พื้นที่</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php 
                                $unique_rooms = array_unique(array_map(function($item) {
                                    return $item['school'] . '-' . $item['building'] . '-' . $item['floor'] . '-' . $item['room'];
                                }, $classroom_equipment_list));
                                echo count($unique_rooms);
                                ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-door-open fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">ประเภทอุปกรณ์</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php 
                                $unique_categories = array_unique(array_column($classroom_equipment_list, 'category_name'));
                                echo count($unique_categories);
                                ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-layer-group fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Classroom Equipment Modal -->
<div class="modal fade" id="classroomEquipmentModal" tabindex="-1" aria-labelledby="classroomEquipmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="classroomEquipmentModalLabel">เพิ่มอุปกรณ์ในห้องเรียน</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" id="classroomEquipmentForm">
                <div class="modal-body">
                    <input type="hidden" name="original_equipment_code" id="original_equipment_code">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">รหัสครุภัณฑ์ *</label>
                            <select class="form-control" name="equipment_code" id="equipment_code" required onchange="updateEquipmentInfo()">
                                <option value="">เลือกรหัสครุภัณฑ์</option>
                                <?php foreach($equipment_list as $equip): ?>
                                    <option value="<?php echo $equip['equipment_code']; ?>" 
                                            data-name="<?php echo $equip['equipment_name']; ?>"
                                            data-category="<?php echo $equip['category_name']; ?>">
                                        <?php echo $equip['equipment_code']; ?> - <?php echo $equip['equipment_name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">หมวดหมู่</label>
                            <input type="text" class="form-control" id="equipment_category_display" readonly>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">จำนวน *</label>
                            <input type="number" class="form-control" name="quantity" id="quantity" min="1" value="1" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">โรงเรียน *</label>
                            <select class="form-control" name="school" id="school" required onchange="updateBuildingsModal()">
                                <option value="">เลือกโรงเรียน</option>
                                <option value="โรงเรียนวารีเชียงใหม่">โรงเรียนวารีเชียงใหม่</option>
                                <option value="โรงเรียนอนุบาลวารีเชียงใหม่">โรงเรียนอนุบาลวารีเชียงใหม่</option>
                                <option value="โรงเรียนนานาชาติวารีเชียงใหม่">โรงเรียนนานาชาติวารีเชียงใหม่</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">ตึก/อาคาร *</label>
                            <select class="form-control" name="building" id="building" required onchange="updateFloorsModal()">
                                <option value="">เลือกตึก/อาคาร</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">ชั้น *</label>
                            <select class="form-control" name="floor" id="floor" required>
                                <option value="">เลือกชั้น</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">ชื่อ/เลขห้อง *</label>
                            <input type="text" class="form-control" name="room" id="room" placeholder="เช่น 101, 201, ห้องประชุม" required>
                        </div>
                        <div class="col-md-5 mb-3">
                            <label class="form-label">ชื่อห้อง</label>
                            <input type="text" class="form-control" name="room_name" id="room_name" placeholder="เช่น ห้องวิทยาศาสตร์, ห้องสมุด">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">วันที่ติดตั้ง</label>
                            <input type="date" class="form-control" name="installation_date" id="installation_date">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label">หมายเหตุ</label>
                            <textarea class="form-control" name="notes" id="notes" rows="3" placeholder="ระบุรายละเอียดเพิ่มเติม (ถ้ามี)"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                    <button type="submit" name="add_classroom_equipment" id="submitBtn" class="btn btn-primary">บันทึก</button>
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
            { name: "ตึก1-อำนวยการ", floors: ["ชั้น 1", "ชั้น 2"] },
            { name: "ตึก3-ประถม", floors: ["ชั้น 1", "ชั้น 2", "ชั้น 3", "ชั้น 4"] },
            { name: "ตึก4-ประถม", floors: ["ชั้น 1", "ชั้น 2", "ชั้น 3"] },
            { name: "ตึก4-มัธยม", floors: ["ชั้น 3", "ชั้น 4", "ชั้น 5"] },
            { name: "ตึก5-อนุบาล", floors: ["ชั้น 1", "ชั้น 2"] },
            { name: "ตึก6", floors: ["ชั้น 1", "ชั้น 2"] },
            { name: "ตึก7-มัธยม", floors: ["ชั้น 1", "ชั้น 2", "ชั้น 3", "ชั้น 4", "ชั้น 5", "ชั้น 6", "ชั้น 7"] },
            { name: "ตึก10", floors: ["ชั้น 1", "ชั้น 2"] }
        ]
    },
    "โรงเรียนอนุบาลวารีเชียงใหม่": {
        buildings: [
            { name: "ตึก1-อำนวยการ", floors: ["ชั้น 1", "ชั้น 2"] },
            { name: "ตึก6", floors: ["ชั้น 1", "ชั้น 2"] }
        ]
    },
    "โรงเรียนนานาชาติวารีเชียงใหม่": {
        buildings: [
            { name: "ตึก8", floors: ["ชั้น 1", "ชั้น 2", "ชั้น 3", "ชั้น 4"] },
            { name: "ตึก9", floors: ["ชั้น 1", "ชั้น 2", "ชั้น 3"] },
            { name: "ตึก10", floors: ["ชั้น 1", "ชั้น 2"] }
        ]
    }
};

// Update equipment info when selecting equipment code
function updateEquipmentInfo() {
    const select = document.getElementById('equipment_code');
    const option = select.options[select.selectedIndex];
    
    if (option.value) {
        document.getElementById('equipment_category_display').value = option.getAttribute('data-category');
    } else {
        document.getElementById('equipment_category_display').value = '';
    }
}

// Clear form
function clearForm() {
    document.getElementById('classroomEquipmentForm').reset();
    document.getElementById('original_equipment_code').value = '';
    document.getElementById('classroomEquipmentModalLabel').textContent = 'เพิ่มอุปกรณ์ในห้องเรียน';
    document.getElementById('submitBtn').name = 'add_classroom_equipment';
    document.getElementById('submitBtn').textContent = 'บันทึก';
    document.getElementById('equipment_category_display').value = '';
    
    // Clear dropdowns
    document.getElementById('building').innerHTML = '<option value="">เลือกตึก/อาคาร</option>';
    document.getElementById('floor').innerHTML = '<option value="">เลือกชั้น</option>';
    
    // Set default values from filter
    const selectedSchool = document.getElementById('schoolFilter').value;
    const selectedBuilding = document.getElementById('buildingFilter').value;
    
    if (selectedSchool) {
        document.getElementById('school').value = selectedSchool;
        updateBuildingsModal();
        
        setTimeout(() => {
            if (selectedBuilding) {
                document.getElementById('building').value = selectedBuilding;
                updateFloorsModal();
            }
        }, 100);
    }
}

// Edit classroom equipment
function editClassroomEquipment(item) {
    document.getElementById('original_equipment_code').value = item.equipment_code;
    document.getElementById('equipment_code').value = item.equipment_code;
    document.getElementById('equipment_category_display').value = item.category_name;
    document.getElementById('school').value = item.school;
    document.getElementById('quantity').value = item.quantity;
    document.getElementById('room').value = item.room;
    document.getElementById('room_name').value = item.room_name || '';
    document.getElementById('installation_date').value = item.installation_date || '';
    document.getElementById('notes').value = item.notes || '';
    
    // Update buildings and floors
    updateBuildingsModal();
    
    setTimeout(() => {
        document.getElementById('building').value = item.building;
        updateFloorsModal();
        setTimeout(() => {
            document.getElementById('floor').value = item.floor;
        }, 100);
    }, 100);
    
    document.getElementById('classroomEquipmentModalLabel').textContent = 'แก้ไขข้อมูลอุปกรณ์';
    document.getElementById('submitBtn').name = 'edit_classroom_equipment';
    document.getElementById('submitBtn').textContent = 'อัพเดท';
}

// Update buildings dropdown in modal
function updateBuildingsModal() {
    const schoolSelect = document.getElementById('school');
    const buildingSelect = document.getElementById('building');
    const floorSelect = document.getElementById('floor');
    
    buildingSelect.innerHTML = '<option value="">เลือกตึก/อาคาร</option>';
    floorSelect.innerHTML = '<option value="">เลือกชั้น</option>';
    
    const selectedSchool = schoolSelect.value;
    if (selectedSchool && schoolData[selectedSchool]) {
        schoolData[selectedSchool].buildings.forEach(building => {
            const option = document.createElement('option');
            option.value = building.name;
            option.textContent = building.name;
            buildingSelect.appendChild(option);
        });
    }
}

// Update floors dropdown in modal
function updateFloorsModal() {
    const schoolSelect = document.getElementById('school');
    const buildingSelect = document.getElementById('building');
    const floorSelect = document.getElementById('floor');
    
    floorSelect.innerHTML = '<option value="">เลือกชั้น</option>';
    
    const selectedSchool = schoolSelect.value;
    const selectedBuilding = buildingSelect.value;
    
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

// Real-time refresh function
let lastUpdateTime = new Date();

function updateTimeDisplay() {
    const now = new Date();
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const seconds = String(now.getSeconds()).padStart(2, '0');
    document.getElementById('updateTime').textContent = `${hours}:${minutes}:${seconds}`;
}

function refreshEquipmentData() {
    if (!document.getElementById('autoRefresh').checked) {
        return;
    }
    
    const currentUrl = new URL(window.location.href);
    
    fetch(currentUrl)
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const newDocument = parser.parseFromString(html, 'text/html');
            const newContent = newDocument.querySelector('#equipmentTableContainer');
            
            if (newContent) {
                const currentContent = document.querySelector('#equipmentTableContainer');
                
                if (currentContent.innerHTML !== newContent.innerHTML) {
                    currentContent.innerHTML = newContent.innerHTML;
                    lastUpdateTime = new Date();
                    
                    // Reinitialize DataTable
                    if ($.fn.DataTable.isDataTable('#dataTable')) {
                        $('#dataTable').DataTable().destroy();
                    }
                    initDataTable();
                    
                    showUpdateNotification();
                }
            }
            
            updateTimeDisplay();
        })
        .catch(error => {
            console.error('Error refreshing data:', error);
        });
}

function showUpdateNotification() {
    const notification = document.createElement('div');
    notification.className = 'alert alert-success alert-dismissible fade show position-fixed';
    notification.style.cssText = 'top: 80px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        <i class="fas fa-sync-alt"></i> ข้อมูลได้รับการอัพเดทแล้ว
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Initialize DataTable
function initDataTable() {
    if ($('#dataTable').length && $('#dataTable tbody tr').length > 0) {
        $('#dataTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/th.json'
            },
            order: [[3, 'asc'], [4, 'asc'], [5, 'asc'], [6, 'asc']],
            columnDefs: [
                { orderable: false, targets: [10] }
            ],
            pageLength: 25,
            responsive: true
        });
    }
}

// Document ready
$(document).ready(function() {
    initDataTable();
});

// Auto refresh every 15 seconds
setInterval(refreshEquipmentData, 15000);

// Update time display every second
setInterval(updateTimeDisplay, 1000);

// Initialize time display
updateTimeDisplay();

// Auto-refresh toggle
document.getElementById('autoRefresh').addEventListener('change', function() {
    if (this.checked) {
        refreshEquipmentData();
    }
});
</script>

<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}

.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}

.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}

.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}

.text-gray-800 {
    color: #5a5c69 !important;
}

.text-gray-300 {
    color: #dddfeb !important;
}

.card:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    transition: all 0.3s ease;
}

.table-hover tbody tr:hover {
    background-color: #f8f9fc;
}

.badge {
    font-weight: 500;
    padding: 0.35em 0.65em;
}

.btn-group .btn {
    transition: all 0.2s ease;
}

.btn-group .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.alert-success {
    animation: slideInRight 0.3s ease;
}

@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.form-control:focus, .form-select:focus {
    border-color: #4e73df;
    box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.25);
}

.modal-xl {
    max-width: 1200px;
}

.table th {
    background-color: #f8f9fc;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.75rem;
    color: #858796;
}

.card-header {
    background-color: #f8f9fc;
    border-bottom: 1px solid #e3e6f0;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .modal-xl {
        max-width: 95%;
    }
    
    .btn-group {
        display: flex;
        flex-direction: column;
    }
    
    .btn-group .btn {
        width: 100%;
        margin-bottom: 0.25rem;
    }
}

/* Print styles */
@media print {
    .btn, .badge, .alert, .card-header, .form-check, nav, aside, .btn-group {
        display: none !important;
    }
    
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    
    .table {
        font-size: 0.8rem;
    }
}

/* Loading spinner */
.loading-spinner {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 3px solid rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    border-top-color: #fff;
    animation: spin 1s ease-in-out infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* DataTable custom styles */
.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: #4e73df !important;
    color: white !important;
    border-color: #4e73df !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background: #2e59d9 !important;
    color: white !important;
    border-color: #2e59d9 !important;
}
</style>

<?php require_once 'includes/footer.php'; ?>