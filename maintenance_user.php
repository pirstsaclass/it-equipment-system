<?php
require_once 'includes/header.php';

// ตรวจสอบสิทธิ์การเข้าถึง - อนุญาตเฉพาะ user ทั่วไป
// ตัวอย่างการตรวจสอบสิทธิ์ (ปรับตามระบบของคุณ)
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    $_SESSION['error'] = "คุณไม่มีสิทธิ์เข้าถึงหน้านี้";
    header("Location: index.php");
    exit();
}

// รับข้อมูลผู้ใช้ปัจจุบัน
$current_user_id = $_SESSION['user_id']; // ตัวอย่างค่า user_id จาก session
$current_user_name = $_SESSION['full_name'] ; // ตัวอย่างชื่อผู้ใช้

// CRUD Operations - สำหรับ user จะอนุญาตเฉพาะการเพิ่มข้อมูลเท่านั้น
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $id = isset($_GET['id']) ? $_GET['id'] : null;
    
    // User สามารถลบเฉพาะรายการที่ตัวเองสร้างและยังไม่ได้ดำเนินการ
    if ($action == 'delete' && $id) {
        // ตรวจสอบว่าผู้ใช้เป็นเจ้าของรายการนี้หรือไม่
        $check_stmt = $db->prepare("SELECT id, reported_by, repair_status FROM maintenance_requests WHERE id = ?");
        $check_stmt->execute([$id]);
        $request = $check_stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($request && $request['reported_by'] === $current_user_name && $request['repair_status'] === 'รอซ่อม') {
            $stmt = $db->prepare("DELETE FROM maintenance_requests WHERE id = ?");
            $stmt->execute([$id]);
            $_SESSION['success'] = "ลบข้อมูลการซ่อมเรียบร้อยแล้ว";
        } else {
            $_SESSION['error'] = "ไม่สามารถลบข้อมูลนี้ได้";
        }
        
        header("Location: maintenance_user.php");
        exit();
    }
}

if ($_POST) {
    if (isset($_POST['add_maintenance'])) {
        try {
            $equipment_id = $_POST['equipment_id'];
            
            // สร้าง repair_code อัตโนมัติ
            $repair_code = "R" . date('Ym') . "-" . sprintf('%04d', rand(1000, 9999));
            
            // Insert maintenance record - สำหรับ user สถานะจะเป็น 'รอซ่อม' เท่านั้น
            $stmt = $db->prepare("INSERT INTO maintenance_requests (repair_code, equipment_id, report_date, problem_description, reported_by, repair_status, location_school, location_building, location_floor, location_room) VALUES (?, ?, ?, ?, ?, 'รอซ่อม', ?, ?, ?, ?)");
            $stmt->execute([
                $repair_code,
                $equipment_id,
                $_POST['report_date'],
                $_POST['problem_description'],
                $current_user_name, // ใช้ชื่อผู้ใช้ปัจจุบัน
                $_POST['location_school'],
                $_POST['location_building'],
                $_POST['location_floor'],
                $_POST['location_room']
            ]);
            
            // Update equipment status to 'รอซ่อม'
            $update_stmt = $db->prepare("UPDATE equipment SET equipment_status = 'รอซ่อม' WHERE id = ?");
            $update_stmt->execute([$equipment_id]);
            
            $_SESSION['success'] = "แจ้งซ่อมเรียบร้อยแล้ว รหัสการซ่อม: " . $repair_code;
            header("Location: maintenance_user.php");
            exit();
            
        } catch (PDOException $e) {
            $_SESSION['error'] = "เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . $e->getMessage();
            header("Location: maintenance_user.php");
            exit();
        }
    }
}

// Get maintenance list for current user only
$maintenance_query = "SELECT mr.*, e.equipment_code, e.equipment_name, e.equipment_status,
                             ec.category_name, es.subcategory_name
                      FROM maintenance_requests mr 
                      JOIN equipment e ON mr.equipment_id = e.id 
                      LEFT JOIN equipment_categories ec ON e.category_id = ec.id 
                      LEFT JOIN equipment_subcategories es ON e.subcategory_id = es.id 
                      WHERE mr.reported_by = ?
                      ORDER BY mr.created_at DESC";
$maintenance_list = $db->prepare($maintenance_query);
$maintenance_list->execute([$current_user_name]);
$maintenance_list = $maintenance_list->fetchAll(PDO::FETCH_ASSOC);

// Get equipment for dropdown
$equipment_list = $db->query("SELECT id, equipment_code, equipment_name, equipment_status, 
                                     category_id, subcategory_id 
                              FROM equipment 
                              ORDER BY equipment_name")->fetchAll(PDO::FETCH_ASSOC);

// ข้อมูลโรงเรียนสำหรับแปลงชื่อย่อ
$schools_short = [
    "โรงเรียนวารีเชียงใหม่" => "VCS",
    "โรงเรียนอนุบาลวารีเชียงใหม่" => "VKS", 
    "โรงเรียนนานาชาติวารีเชียงใหม่" => "VCIS"
];
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
        <h1 class="h2">ระบบแจ้งซ่อม</h1>
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

    <!-- Real-time Filter Section -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">ค้นหาทั่วไป</label>
                    <input type="text" class="form-control" id="globalSearch" placeholder="ค้นหาทั้งตาราง..." onkeyup="filterTable()">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">สถานะซ่อม</label>
                    <select class="form-control" id="statusFilter" onchange="filterTable()">
                        <option value="">ทั้งหมด</option>
                        <option value="รอซ่อม">รอซ่อม</option>
                        <option value="กำลังดำเนินการ">กำลังดำเนินการ</option>
                        <option value="ซ่อมเสร็จ">ซ่อมเสร็จ</option>
                        <option value="ยกเลิก">ยกเลิก</option>
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

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h5 class="m-0 font-weight-bold">รายการแจ้งซ่อมของฉัน</h5>
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
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="maintenanceTable" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th width="120">รหัสการซ่อม</th>
                            <th width="110">รหัสครุภัณฑ์</th>
                            <th width="150">ชื่ออุปกรณ์</th>
                            <th width="150">สถานที่</th>
                            <th>ปัญหาที่พบ</th>
                            <th width="110">วันที่แจ้งซ่อม</th>
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
                                <strong class="text-primary"><?php echo $maintenance['repair_code']; ?></strong>
                            </td>
                            <td>
                                <a href="equipment_detail.php?id=<?php echo $maintenance['equipment_id']; ?>" class="text-decoration-none" title="ดูรายละเอียดอุปกรณ์">
                                    <strong class="text-info"><?php echo $maintenance['equipment_code']; ?></strong>
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
                                    if ($maintenance['location_school']) {
                                        // แสดงชื่อย่อโรงเรียนแทนชื่อเต็ม
                                        $school_short = $schools_short[$maintenance['location_school']] ?? $maintenance['location_school'];
                                        $location_parts[] = $school_short;
                                    }
                                    if ($maintenance['location_building']) $location_parts[] = $maintenance['location_building'];
                                    if ($maintenance['location_floor']) $location_parts[] = $maintenance['location_floor'];
                                    if ($maintenance['location_room']) $location_parts[] = $maintenance['location_room'];
                                    
                                    echo implode('/', $location_parts);
                                    ?>
                                </small>
                            </td>
                            <td>
                                <div style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" 
                                     title="<?php echo htmlspecialchars($maintenance['problem_description']); ?>">
                                    <?php echo mb_substr($maintenance['problem_description'], 0, 50) . (mb_strlen($maintenance['problem_description']) > 50 ? '...' : ''); ?>
                                </div>
                            </td>
                            <td class="text-center">
                                <small data-sort="<?php echo $maintenance['report_date']; ?>">
                                    <?php echo date('d/m/Y', strtotime($maintenance['report_date'])); ?>
                                </small>
                            </td>
                            <td>
                                <?php if ($maintenance['assigned_technician']): ?>
                                    <span class="badge bg-info"><?php echo $maintenance['assigned_technician']; ?></span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php 
                                $repairStatus_badge = [
                                    'รอซ่อม' => 'warning',
                                    'กำลังดำเนินการ' => 'info',
                                    'ซ่อมเสร็จ' => 'success',
                                    'ยกเลิก' => 'danger'
                                ];
                                $status_class = $repairStatus_badge[$maintenance['repair_status']] ?? 'secondary';
                                ?>
                                <span class="badge bg-<?php echo $status_class; ?>">
                                    <?php echo $maintenance['repair_status']; ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <?php 
                                $equip_status_badge = [
                                    'ใหม่' => 'success',
                                    'ใช้งานปกติ' => 'primary',
                                    'รอซ่อม' => 'warning',
                                    'กำลังซ่อม' => 'info',
                                    'ชำรุด' => 'danger',
                                    'จำหน่ายแล้ว' => 'secondary'
                                ];
                                $equip_status_class = $equip_status_badge[$maintenance['equipment_status']] ?? 'secondary';
                                ?>
                                <span class="badge bg-<?php echo $equip_status_class; ?>" style="max-width: 120px; white-space: normal; word-wrap: break-word; line-height: 1.2;">
                                    <?php echo $maintenance['equipment_status']; ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <?php if ($maintenance['repair_status'] === 'รอซ่อม'): ?>
                                        <a href="maintenance_user.php?action=delete&id=<?php echo $maintenance['id']; ?>" class="btn btn-danger" onclick="return confirm('คุณแน่ใจหรือไม่ที่จะลบรายการนี้?')" title="ลบ">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    <?php else: ?>
                                        <button class="btn btn-secondary" disabled title="ไม่สามารถลบได้">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
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
                    <input type="hidden" name="maintenance_id" id="maintenance_id">
                    <input type="hidden" name="repair_code" id="repair_code">
                    
                    <!-- Location Selection -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2"><i class="fas fa-map-marker-alt"></i> เลือกสถานที่</h6>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">โรงเรียน *</label>
                            <select class="form-control" name="location_school" id="location_school" required onchange="updateBuildings()">
                                <option value="">เลือกโรงเรียน</option>
                                <option value="โรงเรียนวารีเชียงใหม่">โรงเรียนวารีเชียงใหม่ (VCS)</option>
                                <option value="โรงเรียนอนุบาลวารีเชียงใหม่">โรงเรียนอนุบาลวารีเชียงใหม่ (VKS)</option>
                                <option value="โรงเรียนนานาชาติวารีเชียงใหม่">โรงเรียนนานาชาติวารีเชียงใหม่ (VCIS)</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ตึก/อาคาร *</label>
                            <select class="form-control" name="location_building" id="location_building" required onchange="updateFloors()">
                                <option value="">เลือกตึก/อาคาร</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">ชั้น *</label>
                            <select class="form-control" name="location_floor" id="location_floor" required>
                                <option value="">เลือกชั้น</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">ห้อง *</label>
                            <input type="text" class="form-control" name="location_room" id="location_room" placeholder="เช่น 101, 201, ห้องธุรการ" required>
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
                                    <?php echo $equipment['equipment_code'] . ' - ' . $equipment['equipment_name'] . ' (สถานะ: ' . $equipment['equipment_status'] . ')'; ?>
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
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">ปัญหาที่พบ *</label>
                        <textarea class="form-control" name="problem_description" id="problem_description" rows="3" required placeholder="อธิบายปัญหาที่พบโดยละเอียด"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <div class="alert alert-warning">
                            <small>
                                <i class="fas fa-info-circle"></i> 
                                <strong>หมายเหตุ:</strong> หลังจากแจ้งซ่อมแล้ว สถานะการซ่อมจะเป็น "รอซ่อม" และทีมซ่อมบำรุงจะติดต่อกลับไป
                            </small>
                        </div>
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

// Real-time Filter Functions
function filterTable() {
    const globalSearch = document.getElementById('globalSearch').value.toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value;
    
    const rows = document.querySelectorAll('#maintenanceTable tbody tr');
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
        
        // Status filter
        if (statusFilter && showRow) {
            const statusCell = row.cells[7]; // Status column
            const statusText = statusCell.textContent.trim();
            if (statusText !== statusFilter) {
                showRow = false;
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
    if (visibleCount === 0) {
        noResults.style.display = 'block';
    } else {
        noResults.style.display = 'none';
    }
}

function clearFilters() {
    document.getElementById('globalSearch').value = '';
    document.getElementById('statusFilter').value = '';
    
    filterTable();
}

function exportToExcel() {
    // สร้างข้อมูลสำหรับ Excel
    const table = document.getElementById('maintenanceTable');
    let csv = [];
    
    // เพิ่มหัวข้อภาษาไทย
    const headers = [
        'รหัสการซ่อม',
        'รหัสครุภัณฑ์', 
        'ชื่ออุปกรณ์',
        'สถานที่',
        'ปัญหาที่พบ',
        'วันที่แจ้งซ่อม',
        'ผู้ดำเนินการ',
        'สถานะซ่อม',
        'สถานะครุภัณฑ์'
    ];
    csv.push(headers.join(','));
    
    // เพิ่มข้อมูลแถว
    for (let i = 1; i < table.rows.length; i++) {
        const row = table.rows[i];
        if (row.style.display !== 'none') {
            const rowData = [];
            for (let j = 0; j < row.cells.length - 1; j++) { // ไม่รวมคอลัมน์จัดการ
                let cellText = row.cells[j].textContent.trim();
                // ลบ HTML และเครื่องหมายต่างๆ ที่อาจทำให้ CSV ผิด格式
                cellText = cellText.replace(/,/g, ' ').replace(/\n/g, ' ').replace(/\s+/g, ' ');
                rowData.push(cellText);
            }
            csv.push(rowData.join(','));
        }
    }
    
    const csvString = csv.join('\n');
    const blob = new Blob(['\uFEFF' + csvString], { type: 'text/csv;charset=utf-8;' }); // เพิ่ม BOM สำหรับภาษาไทย
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `รายการแจ้งซ่อม_${new Date().toISOString().split('T')[0]}.csv`;
    a.click();
    window.URL.revokeObjectURL(url);
}

function printTable() {
    const printWindow = window.open('', '_blank');
    const table = document.getElementById('maintenanceTable').cloneNode(true);
    
    // ลบคอลัมน์จัดการ
    const rows = table.getElementsByTagName('tr');
    for (let i = 0; i < rows.length; i++) {
        if (rows[i].cells.length > 0) {
            rows[i].deleteCell(-1); // ลบเซลล์สุดท้าย (คอลัมน์จัดการ)
        }
    }
    
    printWindow.document.write(`
        <html>
            <head>
                <title>พิมพ์รายการแจ้งซ่อม</title>
                <style>
                    body { font-family: 'Sarabun', sans-serif; }
                    table { width: 100%; border-collapse: collapse; }
                    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                    th { background-color: #f8f9fa; }
                    .badge { padding: 4px 8px; border-radius: 4px; font-size: 12px; }
                    .text-center { text-align: center; }
                </style>
            </head>
            <body>
                <h2 style="text-align: center; margin-bottom: 20px;">รายการแจ้งซ่อมของฉัน</h2>
                ${table.outerHTML}
                <div style="margin-top: 20px; text-align: right; font-size: 12px;">
                    พิมพ์เมื่อ: ${new Date().toLocaleString('th-TH')}
                </div>
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
            minute: '2-digit',
            second: '2-digit'
        })}`;
}

setInterval(updateCurrentTime, 1000);
updateCurrentTime();

// Initialize filters
document.addEventListener('DOMContentLoaded', function() {
    filterTable(); // Apply initial filter
});

// Location selection functions
function updateBuildings() {
    const schoolSelect = document.getElementById('location_school');
    const buildingSelect = document.getElementById('location_building');
    const floorSelect = document.getElementById('location_floor');
    
    // Clear existing options
    buildingSelect.innerHTML = '<option value="">เลือกตึก/อาคาร</option>';
    floorSelect.innerHTML = '<option value="">เลือกชั้น</option>';
    
    if (schoolSelect.value && schoolData[schoolSelect.value]) {
        const buildings = schoolData[schoolSelect.value].buildings;
        buildings.forEach(building => {
            const option = document.createElement('option');
            option.value = building.name;
            option.textContent = building.name;
            buildingSelect.appendChild(option);
        });
    }
}

function updateFloors() {
    const schoolSelect = document.getElementById('location_school');
    const buildingSelect = document.getElementById('location_building');
    const floorSelect = document.getElementById('location_floor');
    
    // Clear existing options
    floorSelect.innerHTML = '<option value="">เลือกชั้น</option>';
    
    if (schoolSelect.value && buildingSelect.value && schoolData[schoolSelect.value]) {
        const selectedBuilding = schoolData[schoolSelect.value].buildings.find(
            building => building.name === buildingSelect.value
        );
        
        if (selectedBuilding) {
            selectedBuilding.floors.forEach(floor => {
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
    const statusAlert = document.getElementById('equipmentStatusAlert');
    const statusText = document.getElementById('equipmentStatusText');
    
    if (equipmentSelect.value) {
        const selectedOption = equipmentSelect.options[equipmentSelect.selectedIndex];
        const equipmentStatus = selectedOption.getAttribute('data-status');
        
        statusText.textContent = `สถานะปัจจุบันของครุภัณฑ์: ${equipmentStatus}`;
        statusAlert.style.display = 'block';
    } else {
        statusAlert.style.display = 'none';
    }
}

function clearForm() {
    document.getElementById('maintenanceForm').reset();
    document.getElementById('maintenance_id').value = '';
    document.getElementById('repair_code').value = '';
    document.getElementById('maintenanceModalLabel').textContent = 'แจ้งซ่อมใหม่';
    document.getElementById('submitBtn').name = 'add_maintenance';
    document.getElementById('equipmentStatusAlert').style.display = 'none';
    
    // Reset location dropdowns
    document.getElementById('location_building').innerHTML = '<option value="">เลือกตึก/อาคาร</option>';
    document.getElementById('location_floor').innerHTML = '<option value="">เลือกชั้น</option>';
}
</script>

<?php
require_once 'includes/footer.php';
?>