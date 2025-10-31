<?php
require_once 'includes/header.php';

// ตั้งค่า charset สำหรับการเชื่อมต่อ
$db->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");

// ตัวกรองเริ่มต้น
$selected_school = isset($_GET['school_name']) ? $_GET['school_name'] : '';
$selected_building = isset($_GET['building_name']) ? $_GET['building_name'] : '';
$selected_floor = isset($_GET['floor_level']) ? $_GET['floor_level'] : '';

// CRUD Operations
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $placement_id = isset($_GET['placement_id']) ? $_GET['placement_id'] : null;
    
    if ($action == 'delete' && $placement_id) {
        $stmt = $db->prepare("DELETE FROM equipment_classroom WHERE placement_id = ?");
        $stmt->execute([$placement_id]);
        $_SESSION['success'] = "ลบข้อมูลการจัดวางครุภัณฑ์เรียบร้อยแล้ว";
        
        header("Location: equipment_classroom.php?school_name=" . $selected_school . "&building_name=" . $selected_building . "&floor_level=" . $selected_floor);
        exit();
    }
}

if ($_POST) {
    if (isset($_POST['add_classroom_equipment'])) {
        try {
            // ตรวจสอบว่ามีการจัดวางครุภัณฑ์นี้อยู่แล้วหรือไม่
            $check_stmt = $db->prepare("SELECT COUNT(*) FROM equipment_classroom WHERE equipment_code = ? AND school_name = ? AND building_name = ? AND floor_level = ? AND room_number = ? COLLATE utf8mb4_unicode_ci");
            $check_stmt->execute([
                $_POST['equipment_code'],
                $_POST['school_name'],
                $_POST['building_name'],
                $_POST['floor_level'],
                $_POST['room_number']
            ]);
            $exists = $check_stmt->fetchColumn();
            
            if ($exists > 0) {
                $_SESSION['error'] = "มีการจัดวางครุภัณฑ์นี้ในห้องเรียนแล้ว กรุณาตรวจสอบข้อมูล";
            } else {
                $stmt = $db->prepare("INSERT INTO equipment_classroom (equipment_code, school_name, building_name, floor_level, room_number, room_name, equipment_quantity, installation_date, placement_notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $_POST['equipment_code'],
                    $_POST['school_name'],
                    $_POST['building_name'],
                    $_POST['floor_level'],
                    $_POST['room_number'],
                    $_POST['room_name'],
                    $_POST['equipment_quantity'],
                    !empty($_POST['installation_date']) ? $_POST['installation_date'] : null,
                    $_POST['placement_notes']
                ]);
                $_SESSION['success'] = "เพิ่มข้อมูลการจัดวางครุภัณฑ์เรียบร้อยแล้ว";
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "เกิดข้อผิดพลาด: " . $e->getMessage();
        }
        
        header("Location: equipment_classroom.php?school_name=" . $_POST['school_name'] . "&building_name=" . $_POST['building_name'] . "&floor_level=" . $_POST['floor_level']);
        exit();
    }
    
    if (isset($_POST['edit_classroom_equipment'])) {
        try {
            $placement_id = $_POST['placement_id'];
            
            $stmt = $db->prepare("UPDATE equipment_classroom SET equipment_code=?, school_name=?, building_name=?, floor_level=?, room_number=?, room_name=?, equipment_quantity=?, installation_date=?, placement_notes=? WHERE placement_id=?");
            $stmt->execute([
                $_POST['equipment_code'],
                $_POST['school_name'],
                $_POST['building_name'],
                $_POST['floor_level'],
                $_POST['room_number'],
                $_POST['room_name'],
                $_POST['equipment_quantity'],
                !empty($_POST['installation_date']) ? $_POST['installation_date'] : null,
                $_POST['placement_notes'],
                $placement_id
            ]);
            $_SESSION['success'] = "แก้ไขข้อมูลการจัดวางครุภัณฑ์เรียบร้อยแล้ว";
        } catch (PDOException $e) {
            $_SESSION['error'] = "เกิดข้อผิดพลาด: " . $e->getMessage();
        }
        
        header("Location: equipment_classroom.php?school_name=" . $_POST['school_name'] . "&building_name=" . $_POST['building_name'] . "&floor_level=" . $_POST['floor_level']);
        exit();
    }
}

// Get equipment list for dropdown
$equipment_query = "SELECT equipment_code, equipment_name, brand_name, model_name 
                    FROM equipment 
                    WHERE equipment_status != 'จำหน่าย' 
                    ORDER BY equipment_code";
$equipment_list = $db->query($equipment_query)->fetchAll(PDO::FETCH_ASSOC);

// Get filter options
$schools_query = "SELECT DISTINCT school_name FROM equipment_classroom ORDER BY school_name";
$schools_list = $db->query($schools_query)->fetchAll(PDO::FETCH_COLUMN);

$buildings_list = [];
$floors_list = [];

if ($selected_school) {
    $buildings_query = "SELECT DISTINCT building_name FROM equipment_classroom WHERE school_name = ? COLLATE utf8mb4_unicode_ci ORDER BY building_name";
    $buildings_stmt = $db->prepare($buildings_query);
    $buildings_stmt->execute([$selected_school]);
    $buildings_list = $buildings_stmt->fetchAll(PDO::FETCH_COLUMN);
}

if ($selected_school && $selected_building) {
    $floors_query = "SELECT DISTINCT floor_level FROM equipment_classroom WHERE school_name = ? AND building_name = ? COLLATE utf8mb4_unicode_ci ORDER BY floor_level";
    $floors_stmt = $db->prepare($floors_query);
    $floors_stmt->execute([$selected_school, $selected_building]);
    $floors_list = $floors_stmt->fetchAll(PDO::FETCH_COLUMN);
}

// Get classroom equipment list with filters
$where_conditions = [];
$params = [];

if ($selected_school) {
    $where_conditions[] = "ec.school_name = ? COLLATE utf8mb4_unicode_ci";
    $params[] = $selected_school;
}

if ($selected_building) {
    $where_conditions[] = "ec.building_name = ? COLLATE utf8mb4_unicode_ci";
    $params[] = $selected_building;
}

if ($selected_floor) {
    $where_conditions[] = "ec.floor_level = ? COLLATE utf8mb4_unicode_ci";
    $params[] = $selected_floor;
}

$where_sql = "";
if (!empty($where_conditions)) {
    $where_sql = "WHERE " . implode(" AND ", $where_conditions);
}

$classroom_equipment_query = "
    SELECT ec.*, 
           e.equipment_name, 
           e.brand_name, 
           e.model_name,
           e.equipment_status
    FROM equipment_classroom ec
    LEFT JOIN equipment e ON ec.equipment_code = e.equipment_code COLLATE utf8mb4_unicode_ci
    $where_sql
    ORDER BY ec.school_name, ec.building_name, ec.floor_level, ec.room_number, ec.created_at DESC
";

$classroom_equipment_stmt = $db->prepare($classroom_equipment_query);
$classroom_equipment_stmt->execute($params);
$classroom_equipment_list = $classroom_equipment_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'includes/sidebar.php'; ?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <?php include 'includes/navbar.php'; ?>

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">จัดการครุภัณฑ์ในห้องเรียน</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#classroomEquipmentModal" onclick="clearForm()">
            <i class="fas fa-plus"></i> เพิ่มครุภัณฑ์
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
                        <select class="form-control" name="school_name" id="schoolFilter" onchange="this.form.submit()">
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
                        <select class="form-control" name="building_name" id="buildingFilter" onchange="this.form.submit()">
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
                        <select class="form-control" name="floor_level" onchange="this.form.submit()">
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
                รายการครุภัณฑ์ในห้องเรียน
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
                    <h5 class="text-muted">ไม่พบข้อมูลครุภัณฑ์</h5>
                    <p class="text-muted">กรุณาเลือกตัวกรองหรือเพิ่มข้อมูลครุภัณฑ์ใหม่</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                        <thead class="table-light">
                            <tr>
                                <th width="10%">รหัสครุภัณฑ์</th>
                                <th width="15%">ชื่อครุภัณฑ์</th>
                                <th width="10%">โรงเรียน</th>
                                <th width="10%">ตึก</th>
                                <th width="8%">ชั้น</th>
                                <th width="8%">ห้อง</th>
                                <th width="12%">ชื่อห้อง</th>
                                <th width="5%">จำนวน</th>
                                <th width="10%">วันที่ติดตั้ง</th>
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
                                    <?php if ($item['brand_name']): ?>
                                        <br><small class="text-muted"><?php echo $item['brand_name']; ?> <?php echo $item['model_name']; ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $item['school_name']; ?></td>
                                <td><?php echo $item['building_name']; ?></td>
                                <td><?php echo $item['floor_level']; ?></td>
                                <td><?php echo $item['room_number']; ?></td>
                                <td><?php echo $item['room_name']; ?></td>
                                <td class="text-center"><span class="badge bg-primary"><?php echo $item['equipment_quantity']; ?></span></td>
                                <td><?php echo $item['installation_date'] ? date('d/m/Y', strtotime($item['installation_date'])) : '-'; ?></td>
                                <td>
                                    <span class="badge 
                                        <?php 
                                        switch($item['equipment_status']) {
                                            case 'ใหม่': echo 'bg-success'; break;
                                            case 'ใช้งานปกติ': echo 'bg-primary'; break;
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
                                        <a href="equipment_classroom.php?action=delete&placement_id=<?php echo $item['placement_id']; ?>&school_name=<?php echo $selected_school; ?>&building_name=<?php echo $selected_building; ?>&floor_level=<?php echo $selected_floor; ?>" 
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">จำนวนครุภัณฑ์รวม</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php 
                                $total_qty = array_sum(array_column($classroom_equipment_list, 'equipment_quantity'));
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
                                    return $item['school_name'] . '-' . $item['building_name'] . '-' . $item['floor_level'] . '-' . $item['room_number'];
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
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">ประเภทครุภัณฑ์</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php 
                                $unique_equipment = array_unique(array_column($classroom_equipment_list, 'equipment_code'));
                                echo count($unique_equipment);
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
                <h5 class="modal-title" id="classroomEquipmentModalLabel">เพิ่มครุภัณฑ์ในห้องเรียน</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" id="classroomEquipmentForm">
                <div class="modal-body">
                    <input type="hidden" name="placement_id" id="placement_id">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">รหัสครุภัณฑ์ *</label>
                            <select class="form-control" name="equipment_code" id="equipment_code" required onchange="updateEquipmentInfo()">
                                <option value="">เลือกรหัสครุภัณฑ์</option>
                                <?php foreach($equipment_list as $equip): ?>
                                    <option value="<?php echo $equip['equipment_code']; ?>" 
                                            data-name="<?php echo $equip['equipment_name']; ?>"
                                            data-brand="<?php echo $equip['brand_name']; ?>"
                                            data-model="<?php echo $equip['model_name']; ?>">
                                        <?php echo $equip['equipment_code']; ?> - <?php echo $equip['equipment_name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">ยี่ห้อ/รุ่น</label>
                            <input type="text" class="form-control" id="equipment_brand_display" readonly>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">จำนวน *</label>
                            <input type="number" class="form-control" name="equipment_quantity" id="equipment_quantity" min="1" value="1" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">โรงเรียน *</label>
                            <select class="form-control" name="school_name" id="school_name" required onchange="updateBuildingsModal()">
                                <option value="">เลือกโรงเรียน</option>
                                <?php foreach($schools_list as $school): ?>
                                    <option value="<?php echo $school; ?>"><?php echo $school; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">ตึก/อาคาร *</label>
                            <select class="form-control" name="building_name" id="building_name" required onchange="updateFloorsModal()">
                                <option value="">เลือกตึก/อาคาร</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">ชั้น *</label>
                            <select class="form-control" name="floor_level" id="floor_level" required>
                                <option value="">เลือกชั้น</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">ชื่อ/เลขห้อง *</label>
                            <input type="text" class="form-control" name="room_number" id="room_number" placeholder="เช่น 101, 201, ห้องประชุม" required>
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
                            <textarea class="form-control" name="placement_notes" id="placement_notes" rows="3" placeholder="ระบุรายละเอียดเพิ่มเติม (ถ้ามี)"></textarea>
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
        const brand = option.getAttribute('data-brand');
        const model = option.getAttribute('data-model');
        document.getElementById('equipment_brand_display').value = brand + ' ' + model;
    } else {
        document.getElementById('equipment_brand_display').value = '';
    }
}

// Clear form
function clearForm() {
    document.getElementById('classroomEquipmentForm').reset();
    document.getElementById('placement_id').value = '';
    document.getElementById('classroomEquipmentModalLabel').textContent = 'เพิ่มครุภัณฑ์ในห้องเรียน';
    document.getElementById('submitBtn').name = 'add_classroom_equipment';
    document.getElementById('submitBtn').textContent = 'บันทึก';
    document.getElementById('equipment_brand_display').value = '';
    
    // Clear dropdowns
    document.getElementById('building_name').innerHTML = '<option value="">เลือกตึก/อาคาร</option>';
    document.getElementById('floor_level').innerHTML = '<option value="">เลือกชั้น</option>';
    
    // Set default values from filter
    const selectedSchool = document.getElementById('schoolFilter').value;
    const selectedBuilding = document.getElementById('buildingFilter').value;
    
    if (selectedSchool) {
        document.getElementById('school_name').value = selectedSchool;
        updateBuildingsModal();
        
        setTimeout(() => {
            if (selectedBuilding) {
                document.getElementById('building_name').value = selectedBuilding;
                updateFloorsModal();
            }
        }, 100);
    }
}

// Edit classroom equipment
function editClassroomEquipment(item) {
    document.getElementById('placement_id').value = item.placement_id;
    document.getElementById('equipment_code').value = item.equipment_code;
    
    // Update equipment info
    const select = document.getElementById('equipment_code');
    const option = Array.from(select.options).find(opt => opt.value === item.equipment_code);
    if (option) {
        const brand = option.getAttribute('data-brand');
        const model = option.getAttribute('data-model');
        document.getElementById('equipment_brand_display').value = brand + ' ' + model;
    }
    
    document.getElementById('school_name').value = item.school_name;
    document.getElementById('equipment_quantity').value = item.equipment_quantity;
    document.getElementById('room_number').value = item.room_number;
    document.getElementById('room_name').value = item.room_name || '';
    document.getElementById('installation_date').value = item.installation_date || '';
    document.getElementById('placement_notes').value = item.placement_notes || '';
    
    // Update buildings and floors
    updateBuildingsModal();
    
    setTimeout(() => {
        document.getElementById('building_name').value = item.building_name;
        updateFloorsModal();
        setTimeout(() => {
            document.getElementById('floor_level').value = item.floor_level;
        }, 100);
    }, 100);
    
    document.getElementById('classroomEquipmentModalLabel').textContent = 'แก้ไขข้อมูลครุภัณฑ์';
    document.getElementById('submitBtn').name = 'edit_classroom_equipment';
    document.getElementById('submitBtn').textContent = 'อัพเดท';
}

// Update buildings dropdown in modal
function updateBuildingsModal() {
    const schoolSelect = document.getElementById('school_name');
    const buildingSelect = document.getElementById('building_name');
    const floorSelect = document.getElementById('floor_level');
    
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
    const schoolSelect = document.getElementById('school_name');
    const buildingSelect = document.getElementById('building_name');
    const floorSelect = document.getElementById('floor_level');
    
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
            order: [[2, 'asc'], [3, 'asc'], [4, 'asc'], [5, 'asc']],
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
/* ... (keep existing styles) ... */
</style>

<?php require_once 'includes/footer.php'; ?>