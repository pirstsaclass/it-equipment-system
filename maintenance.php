<?php
require_once 'includes/header.php';

// CRUD Operations
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $id = isset($_GET['id']) ? $_GET['id'] : null;
    
    if ($action == 'delete' && $id) {
        $stmt = $db->prepare("DELETE FROM maintenance WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['success'] = "ลบข้อมูลการซ่อมเรียบร้อยแล้ว";
        header("Location: maintenance.php");
        exit();
    }
}

if ($_POST) {
    if (isset($_POST['add_maintenance'])) {
        try {
            $equipment_id = $_POST['equipment_id'];
            $new_status = $_POST['repair_status'];
            
            // Insert maintenance record
            $stmt = $db->prepare("INSERT INTO maintenance (equipment_id, report_date, problem_description, reported_by, assigned_technician, cost, repair_status, solution_description, completed_date, school_name, building_name, floor_name, room_name, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt->execute([
                $equipment_id,
                $_POST['report_date'],
                $_POST['problem_description'],
                $_POST['reported_by'],
                $_POST['assigned_technician'],
                $_POST['cost'],
                $new_status,
                $_POST['solution_description'],
                $_POST['completed_date'],
                $_POST['school_name'],
                $_POST['building_name'],
                $_POST['floor_name'],
                $_POST['room_name']
            ]);
            
            // Update equipment status based on maintenance status
            if ($new_status == 'ซ่อมเสร็จ') {
                // ดึงสถานะปัจจุบันของอุปกรณ์
                $current_status_stmt = $db->prepare("SELECT equipment_status FROM equipment WHERE id = ?");
                $current_status_stmt->execute([$equipment_id]);
                $current_equipment_status = $current_status_stmt->fetchColumn();
                
                // กำหนดสถานะใหม่ตามสถานะปัจจุบัน
                if ($current_equipment_status == 'อุปกรณ์ใหม่') {
                    $equipment_status = 'อุปกรณ์ใหม่-ซ่อมเสร็จ';
                } else {
                    $equipment_status = 'อุปกรณ์เดิม-ซ่อมเสร็จ';
                }
                
                $update_stmt = $db->prepare("UPDATE equipment SET equipment_status = ? WHERE id = ?");
                $update_stmt->execute([$equipment_status, $equipment_id]);
            } else {
                // สำหรับสถานะอื่นๆ
                $equipment_status = '';
                switch ($new_status) {
                    case 'รอซ่อม':
                        $equipment_status = 'รอซ่อม';
                        break;
                    case 'กำลังดำเนินการ':
                        $equipment_status = 'กำลังซ่อม';
                        break;
                    case 'ยกเลิก':
                        // เมื่อยกเลิก ให้กลับไปเป็นสถานะเดิมก่อนซ่อม
                        $current_status_stmt = $db->prepare("SELECT equipment_status FROM equipment WHERE id = ?");
                        $current_status_stmt->execute([$equipment_id]);
                        $current_equipment_status = $current_status_stmt->fetchColumn();
                        
                        // ถ้าสถานะปัจจุบันเป็นสถานะซ่อม ให้กลับไปเป็นอุปกรณ์ใหม่/เดิม
                        if (strpos($current_equipment_status, 'อุปกรณ์เดิม') !== false) {
                            $equipment_status = 'อุปกรณ์เดิม';
                        } else {
                            $equipment_status = 'อุปกรณ์ใหม่';
                        }
                        break;
                }
                
                if ($equipment_status) {
                    $update_stmt = $db->prepare("UPDATE equipment SET equipment_status = ? WHERE id = ?");
                    $update_stmt->execute([$equipment_status, $equipment_id]);
                }
            }
            
            $_SESSION['success'] = "เพิ่มข้อมูลการซ่อมและอัปเดตสถานะครุภัณฑ์เรียบร้อยแล้ว";
            header("Location: maintenance.php");
            exit();
            
        } catch (PDOException $e) {
            $_SESSION['error'] = "เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . $e->getMessage();
            header("Location: maintenance.php");
            exit();
        }
    }
    
    if (isset($_POST['edit_maintenance'])) {
        try {
            $equipment_id = $_POST['equipment_id'];
            $new_status = $_POST['repair_status'];
            
            // Get old status before update
            $old_status_stmt = $db->prepare("SELECT repair_status FROM maintenance WHERE id = ?");
            $old_status_stmt->execute([$_POST['id']]);
            $old_status = $old_status_stmt->fetchColumn();
            
            // Update maintenance record
            $stmt = $db->prepare("UPDATE maintenance SET report_date=?, problem_description=?, reported_by=?, assigned_technician=?, cost=?, repair_status=?, solution_description=?, completed_date=?, school_name=?, building_name=?, floor_name=?, room_name=?, updated_at=NOW() WHERE id=?");
            $stmt->execute([
                $_POST['report_date'],
                $_POST['problem_description'],
                $_POST['reported_by'],
                $_POST['assigned_technician'],
                $_POST['cost'],
                $new_status,
                $_POST['solution_description'],
                $_POST['completed_date'],
                $_POST['school_name'],
                $_POST['building_name'],
                $_POST['floor_name'],
                $_POST['room_name'],
                $_POST['id']
            ]);
            
            // Update equipment status based on maintenance status
            if ($old_status != $new_status) {
                if ($new_status == 'ซ่อมเสร็จ') {
                    // ดึงสถานะปัจจุบันของอุปกรณ์
                    $current_status_stmt = $db->prepare("SELECT equipment_status FROM equipment WHERE id = ?");
                    $current_status_stmt->execute([$equipment_id]);
                    $current_equipment_status = $current_status_stmt->fetchColumn();
                    
                    // กำหนดสถานะใหม่ตามสถานะปัจจุบัน
                    if (strpos($current_equipment_status, 'อุปกรณ์ใหม่') !== false) {
                        $equipment_status = 'อุปกรณ์ใหม่-ซ่อมเสร็จ';
                    } else {
                        $equipment_status = 'อุปกรณ์เดิม-ซ่อมเสร็จ';
                    }
                } else {
                    // สำหรับสถานะอื่นๆ
                    $equipment_status = '';
                    switch ($new_status) {
                        case 'รอซ่อม':
                            $equipment_status = 'รอซ่อม';
                            break;
                        case 'กำลังดำเนินการ':
                            $equipment_status = 'กำลังซ่อม';
                            break;
                        case 'ยกเลิก':
                            // เมื่อยกเลิก ให้กลับไปเป็นสถานะเดิมก่อนซ่อม
                            $current_status_stmt = $db->prepare("SELECT equipment_status FROM equipment WHERE id = ?");
                            $current_status_stmt->execute([$equipment_id]);
                            $current_equipment_status = $current_status_stmt->fetchColumn();
                            
                            // ถ้าสถานะปัจจุบันเป็นสถานะซ่อม ให้กลับไปเป็นอุปกรณ์ใหม่/เดิม
                            if (strpos($current_equipment_status, 'อุปกรณ์เดิม') !== false) {
                                $equipment_status = 'อุปกรณ์เดิม';
                            } else {
                                $equipment_status = 'อุปกรณ์ใหม่';
                            }
                            break;
                    }
                }
                
                if ($equipment_status) {
                    $update_stmt = $db->prepare("UPDATE equipment SET equipment_status = ? WHERE id = ?");
                    $update_stmt->execute([$equipment_status, $equipment_id]);
                }
            }
            
            $_SESSION['success'] = "แก้ไขข้อมูลการซ่อมและอัปเดตสถานะครุภัณฑ์เรียบร้อยแล้ว";
            header("Location: maintenance.php");
            exit();
            
        } catch (PDOException $e) {
            $_SESSION['error'] = "เกิดข้อผิดพลาดในการอัปเดตข้อมูล: " . $e->getMessage();
            header("Location: maintenance.php");
            exit();
        }
    }
}

// Get maintenance list with location data
$maintenance_query = "SELECT m.*, e.code, e.name as equipment_name, e.id as equipment_id, e.equipment_status,
                             c.name as category_name, ci.name as item_name
                      FROM maintenance m 
                      JOIN equipment e ON m.equipment_id = e.id 
                      LEFT JOIN categories c ON e.category_id = c.id 
                      LEFT JOIN categories_items ci ON e.category_item_id = ci.id 
                      ORDER BY m.created_at DESC";
$maintenance_list = $db->query($maintenance_query)->fetchAll(PDO::FETCH_ASSOC);

// Get equipment for dropdown
$equipment_list = $db->query("SELECT e.id, e.code, e.name, e.equipment_status, c.name as category_name, ci.name as item_name 
                              FROM equipment e 
                              LEFT JOIN categories c ON e.category_id = c.id 
                              LEFT JOIN categories_items ci ON e.category_item_id = ci.id 
                              ORDER BY e.code")->fetchAll(PDO::FETCH_ASSOC);

// Get employees for technician dropdown
$employees_query = "SELECT e.id, e.first_name, e.last_name, d.name as department_name 
                    FROM employees e 
                    LEFT JOIN departments d ON e.department_id = d.id 
                    ORDER BY e.first_name, e.last_name";
$employees_list = $db->query($employees_query)->fetchAll(PDO::FETCH_ASSOC);

// Get frequently repaired equipment
$frequent_repair_query = "
    SELECT e.code, e.name, c.name as category_name, ci.name as item_name, COUNT(m.id) as repair_count 
    FROM equipment e 
    JOIN maintenance m ON e.id = m.equipment_id 
    LEFT JOIN categories c ON e.category_id = c.id 
    LEFT JOIN categories_items ci ON e.category_item_id = ci.id 
    GROUP BY e.id, e.code, e.name, c.name, ci.name 
    HAVING COUNT(m.id) > 2 
    ORDER BY repair_count DESC";
$frequent_repairs = $db->query($frequent_repair_query)->fetchAll(PDO::FETCH_ASSOC);

// ข้อมูลโรงเรียนสำหรับแปลงชื่อย่อ
$schools_short = [
    "โรงเรียนวารีเชียงใหม่" => "VCS",
    "โรงเรียนอนุบาลวารีเชียงใหม่" => "VKS", 
    "โรงเรียนนานาชาติวารีเชียงใหม่" => "VCIS"
];
?>

<?php 
// Include sidebar
include 'includes/sidebar.php';
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <?php include 'includes/navbar.php'; ?>

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">ระบบซ่อมบำรุง</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#maintenanceModal" onclick="clearForm()">
            <i class="fas fa-plus"></i> แจ้งซ่อมใหม่
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

    <!-- Frequently Repaired Equipment -->
    <?php if (count($frequent_repairs) > 0): ?>
    <div class="alert alert-warning">
        <h6><i class="fas fa-exclamation-triangle"></i> อุปกรณ์ที่ซ่อมบ่อย</h6>
        <ul class="mb-0">
            <?php foreach($frequent_repairs as $freq): ?>
            <li><?php echo $freq['code'] . ' - ' . $freq['name'] . ' (ซ่อมแล้ว ' . $freq['repair_count'] . ' ครั้ง)'; ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h5 class="m-0 font-weight-bold">รายการซ่อมบำรุงทั้งหมด</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th width="110">รหัสครุภัณฑ์</th>
                            <th width="150">ชื่ออุปกรณ์</th>
                            <th width="150">สถานที่</th>
                            <th>ปัญหาที่พบ</th>
                            <th width="110">วันที่แจ้งซ่อม</th>
                            <th width="120">ผู้แจ้งซ่อม</th>
                            <th width="120">ผู้ดำเนินการ</th>
                            <th width="100">สถานะซ่อม</th>
                            <th width="100">สถานะครุภัณฑ์</th>
                            <th width="90">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($maintenance_list as $maintenance): ?>
                        <tr>
                            <td>
                                <a href="equipment_detail.php?id=<?php echo $maintenance['equipment_id']; ?>" class="text-decoration-none" title="ดูรายละเอียดอุปกรณ์">
                                    <strong class="text-primary"><?php echo $maintenance['code']; ?></strong>
                                </a>
                            </td>
                            <td>
                                <div style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" 
                                     title="<?php echo htmlspecialchars($maintenance['equipment_name']); ?>">
                                    <?php echo $maintenance['equipment_name']; ?>
                                </div>
                            </td>
                            <td>
                                <small class="text-muted">
                                    <?php 
                                    $location_parts = [];
                                    if ($maintenance['school_name']) {
                                        // แสดงชื่อย่อโรงเรียนแทนชื่อเต็ม
                                        $school_short = $schools_short[$maintenance['school_name']] ?? $maintenance['school_name'];
                                        $location_parts[] = $school_short;
                                    }
                                    if ($maintenance['building_name']) $location_parts[] = $maintenance['building_name'];
                                    if ($maintenance['floor_name']) $location_parts[] = $maintenance['floor_name'];
                                    if ($maintenance['room_name']) $location_parts[] = $maintenance['room_name'];
                                    
                                    echo implode('/', $location_parts);
                                    ?>
                                </small>
                            </td>
                            <td>
                                <div style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" 
                                     title="<?php echo htmlspecialchars($maintenance['problem_description']); ?>">
                                    <?php echo mb_substr($maintenance['problem_description'], 0, 50) . (mb_strlen($mainipment['problem_description']) > 50 ? '...' : ''); ?>
                                </div>
                            </td>
                            <td class="text-center">
                                <small><?php echo date('d/m/Y', strtotime($maintenance['report_date'])); ?></small>
                            </td>
                            <td><?php echo $maintenance['reported_by']; ?></td>
                            <td>
                                <?php if ($maintenance['assigned_technician']): ?>
                                    <span class="badge bg-info"><?php echo $maintenance['assigned_technician']; ?></span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php 
                                $status_badge = [
                                    'รอซ่อม' => 'warning',
                                    'กำลังดำเนินการ' => 'info',
                                    'ซ่อมเสร็จ' => 'success',
                                    'ยกเลิก' => 'danger'
                                ];
                                $status_class = $status_badge[$maintenance['repair_status']] ?? 'secondary';
                                ?>
                                <span class="badge bg-<?php echo $status_class; ?>">
                                    <?php echo $maintenance['repair_status']; ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <?php 
                                $equip_status_badge = [
                                    'อุปกรณ์ใหม่' => 'success',
                                    'อุปกรณ์เดิม' => 'primary',
                                    'อุปกรณ์ใหม่-ซ่อมเสร็จ' => 'success',
                                    'อุปกรณ์เดิม-ซ่อมเสร็จ' => 'primary',
                                    'รอซ่อม' => 'info',
                                    'กำลังซ่อม' => 'warning',
                                    'ใช้งานปกติ' => 'primary',
                                    'ชำรุด' => 'warning',
                                    'ซ่อมเสร็จแล้ว' => 'success',
                                    'ส่งคืนแล้ว' => 'primary',
                                    'จำหน่ายแล้ว' => 'danger'
                                ];
                                $equip_status_class = $equip_status_badge[$maintenance['equipment_status']] ?? 'secondary';
                                ?>
                                <span class="badge bg-<?php echo $equip_status_class; ?>" style="max-width: 120px; white-space: normal; word-wrap: break-word; line-height: 1.2;">
                                    <?php echo $maintenance['equipment_status']; ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#maintenanceModal" onclick='editMaintenance(<?php echo json_encode($maintenance); ?>)' title="แก้ไข">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="maintenance.php?action=delete&id=<?php echo $maintenance['id']; ?>" class="btn btn-danger" onclick="return confirm('คุณแน่ใจหรือไม่?')" title="ลบ">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<!-- Maintenance Modal -->
<div class="modal fade" id="maintenanceModal" tabindex="-1" aria-labelledby="maintenanceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="maintenanceModalLabel">แจ้งซ่อมใหม่</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" id="maintenanceForm">
                <div class="modal-body">
                    <input type="hidden" name="id" id="maintenance_id">
                    
                    <!-- Location Selection -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2"><i class="fas fa-map-marker-alt"></i> เลือกสถานที่</h6>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">โรงเรียน *</label>
                            <select class="form-control" name="school_name" id="school_name" required onchange="updateBuildings()">
                                <option value="">เลือกโรงเรียน</option>
                                <option value="โรงเรียนวารีเชียงใหม่">โรงเรียนวารีเชียงใหม่ (VCS)</option>
                                <option value="โรงเรียนอนุบาลวารีเชียงใหม่">โรงเรียนอนุบาลวารีเชียงใหม่ (VKS)</option>
                                <option value="โรงเรียนนานาชาติวารีเชียงใหม่">โรงเรียนนานาชาติวารีเชียงใหม่ (VCIS)</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">แผนก *</label>
                            <select class="form-control" name="department_type" id="department_type" required>
                                <option value="">เลือกแผนก</option>
                                <option value="อำนวยการ">อำนวยการ</option>
                                <option value="อนุบาล">อนุบาล</option>
                                <option value="ประถม">ประถม</option>
                                <option value="มัธยม">มัธยม</option>
                                <option value="สนับสนุน">สนับสนุน</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">ตึก/อาคาร *</label>
                            <select class="form-control" name="building_name" id="building_name" required onchange="updateFloors()">
                                <option value="">เลือกตึก/อาคาร</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">ชั้นเรียน *</label>
                            <select class="form-control" name="floor_name" id="floor_name" required>
                                <option value="">เลือกชั้นเรียน</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">ชื่อ/เลขห้อง *</label>
                            <input type="text" class="form-control" name="room_name" id="room_name" placeholder="เช่น 101, 201, ห้องธุรการ" required>
                        </div>
                    </div>

                    <!-- Equipment Selection -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2"><i class="fas fa-laptop"></i> เลือกครุภัณฑ์</h6>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">เลือกครุภัณฑ์ *</label>
                            <select class="form-control" name="equipment_id" id="equipment_id" required onchange="showEquipmentStatus()">
                                <option value="">เลือกครุภัณฑ์</option>
                                <?php foreach($equipment_list as $equipment): ?>
                                <option value="<?php echo $equipment['id']; ?>" data-status="<?php echo $equipment['equipment_status']; ?>">
                                    <?php echo $equipment['code'] . ' - ' . $equipment['name'] . ' (' . $equipment['category_name'] . ') - สถานะ: ' . $equipment['equipment_status']; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <div id="equipmentStatusAlert" class="alert alert-info mt-2" style="display: none;">
                                <small><i class="fas fa-info-circle"></i> <span id="equipmentStatusText"></span></small>
                            </div>
                        </div>
                    </div>

                    <!-- Problem Details -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2"><i class="fas fa-tools"></i> รายละเอียดการซ่อม</h6>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">วันที่แจ้งซ่อม *</label>
                            <input type="date" class="form-control" name="report_date" id="report_date" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">สถานะ *</label>
                            <select class="form-control" name="repair_status" id="repair_status" required>
                                <option value="รอซ่อม">รอซ่อม</option>
                                <option value="กำลังดำเนินการ">กำลังดำเนินการ</option>
                                <option value="ซ่อมเสร็จ">ซ่อมเสร็จ</option>
                                <option value="ยกเลิก">ยกเลิก</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">ปัญหาที่พบ *</label>
                        <textarea class="form-control" name="problem_description" id="problem_description" rows="3" required placeholder="อธิบายปัญหาที่พบโดยละเอียด"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ผู้แจ้งซ่อม *</label>
                            <input type="text" class="form-control" name="reported_by" id="reported_by" required placeholder="กรอกชื่อผู้แจ้งซ่อม">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ผู้ดำเนินการ</label>
                            <select class="form-control" name="assigned_technician" id="assigned_technician">
                                <option value="">เลือกผู้ดำเนินการ</option>
                                <?php foreach($employees_list as $employee): ?>
                                <option value="<?php echo $employee['first_name'] . ' ' . $employee['last_name']; ?>">
                                    <?php echo $employee['first_name'] . ' ' . $employee['last_name']; ?>
                                    <?php if ($employee['department_name']): ?>
                                        (<?php echo $employee['department_name']; ?>)
                                    <?php endif; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ค่าใช้จ่าย (บาท)</label>
                            <input type="number" step="0.01" class="form-control" name="cost" id="cost" value="0" min="0">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">วันที่ซ่อมเสร็จ</label>
                            <input type="date" class="form-control" name="completed_date" id="completed_date">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">วิธีการแก้ไข</label>
                        <textarea class="form-control" name="solution_description" id="solution_description" rows="3" placeholder="อธิบายวิธีการแก้ไขปัญหา"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                    <button type="submit" name="add_maintenance" id="submitBtn" class="btn btn-primary">บันทึก</button>
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

function clearForm() {
    document.getElementById('maintenanceForm').reset();
    document.getElementById('maintenance_id').value = '';
    document.getElementById('report_date').value = '<?php echo date('Y-m-d'); ?>';
    document.getElementById('cost').value = '0';
    document.getElementById('maintenanceModalLabel').textContent = 'แจ้งซ่อมใหม่';
    document.getElementById('submitBtn').name = 'add_maintenance';
    document.getElementById('submitBtn').textContent = 'บันทึก';
    document.getElementById('equipment_id').disabled = false;
    
    // Reset location dropdowns
    document.getElementById('building_name').innerHTML = '<option value="">เลือกตึก/อาคาร</option>';
    document.getElementById('floor_name').innerHTML = '<option value="">เลือกชั้นเรียน</option>';
    
    // Hide equipment status alert
    document.getElementById('equipmentStatusAlert').style.display = 'none';
}

function editMaintenance(maintenance) {
    document.getElementById('maintenance_id').value = maintenance.id;
    document.getElementById('equipment_id').value = maintenance.equipment_id;
    document.getElementById('report_date').value = maintenance.report_date;
    document.getElementById('problem_description').value = maintenance.problem_description || '';
    document.getElementById('reported_by').value = maintenance.reported_by || '';
    document.getElementById('assigned_technician').value = maintenance.assigned_technician || '';
    document.getElementById('cost').value = maintenance.cost || '0';
    document.getElementById('repair_status').value = maintenance.repair_status || 'รอซ่อม';
    document.getElementById('solution_description').value = maintenance.solution_description || '';
    document.getElementById('completed_date').value = maintenance.completed_date || '';
    document.getElementById('room_name').value = maintenance.room_name || '';
    
    // Set location values
    if (maintenance.school_name) {
        document.getElementById('school_name').value = maintenance.school_name;
        updateBuildings();
        setTimeout(() => {
            if (maintenance.building_name) {
                document.getElementById('building_name').value = maintenance.building_name;
                updateFloors();
                setTimeout(() => {
                    if (maintenance.floor_name) {
                        document.getElementById('floor_name').value = maintenance.floor_name;
                    }
                }, 100);
            }
        }, 100);
    }
    
    document.getElementById('maintenanceModalLabel').textContent = 'แก้ไขข้อมูลการซ่อม';
    document.getElementById('submitBtn').name = 'edit_maintenance';
    document.getElementById('submitBtn').textContent = 'อัพเดท';
    
    // Disable equipment selection in edit mode
    document.getElementById('equipment_id').disabled = true;
    
    // Show equipment status
    showEquipmentStatus();
}

// อัปเดตตึกตามโรงเรียนที่เลือก
function updateBuildings() {
    const schoolSelect = document.getElementById('school_name');
    const buildingSelect = document.getElementById('building_name');
    const floorSelect = document.getElementById('floor_name');
    
    // ล้าง dropdown ตึกและชั้น
    buildingSelect.innerHTML = '<option value="">เลือกตึก/อาคาร</option>';
    floorSelect.innerHTML = '<option value="">เลือกชั้นเรียน</option>';
    
    const selectedSchool = schoolSelect.value;
    if (selectedSchool && schoolData[selectedSchool]) {
        // เพิ่มตึกตามโรงเรียนที่เลือก
        schoolData[selectedSchool].buildings.forEach(building => {
            const option = document.createElement('option');
            option.value = building.name;
            option.textContent = building.name;
            buildingSelect.appendChild(option);
        });
    }
}

// อัปเดตชั้นตามตึกที่เลือก
function updateFloors() {
    const schoolSelect = document.getElementById('school_name');
    const buildingSelect = document.getElementById('building_name');
    const floorSelect = document.getElementById('floor_name');
    
    // ล้าง dropdown ชั้น
    floorSelect.innerHTML = '<option value="">เลือกชั้นเรียน</option>';
    
    const selectedSchool = schoolSelect.value;
    const selectedBuilding = buildingSelect.value;
    
    if (selectedSchool && selectedBuilding && schoolData[selectedSchool]) {
        // หาข้อมูลตึกที่เลือก
        const building = schoolData[selectedSchool].buildings.find(b => b.name === selectedBuilding);
        
        if (building) {
            // เพิ่มชั้นตามตึกที่เลือก
            building.floors.forEach(floor => {
                const option = document.createElement('option');
                option.value = floor;
                option.textContent = floor;
                floorSelect.appendChild(option);
            });
        }
    }
}

function showEquipmentStatus() {
    const equipmentSelect = document.getElementById('equipment_id');
    const selectedOption = equipmentSelect.options[equipmentSelect.selectedIndex];
    const status = selectedOption.getAttribute('data-status');
    const alertDiv = document.getElementById('equipmentStatusAlert');
    const statusText = document.getElementById('equipmentStatusText');
    
    if (status) {
        statusText.textContent = 'สถานะปัจจุบันของครุภัณฑ์: ' + status;
        alertDiv.style.display = 'block';
    } else {
        alertDiv.style.display = 'none';
    }
}

$(document).ready(function() {
    $('#dataTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/th.json'
        },
        order: [[4, 'desc']], // Sort by report date descending
        pageLength: 25,
        responsive: true,
        columnDefs: [
            { orderable: false, targets: [9] } // Disable sorting for action column
        ]
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>
