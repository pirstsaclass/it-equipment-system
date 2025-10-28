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
        $stmt = $db->prepare("INSERT INTO maintenance (equipment_id, report_date, problem_description, reported_by, assigned_technician, cost, status, solution_description, completed_date, school_name, building_name, floor_name, room_name) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['equipment_id'],
            $_POST['report_date'],
            $_POST['problem_description'],
            $_POST['reported_by'],
            $_POST['assigned_technician'],
            $_POST['cost'],
            $_POST['status'],
            $_POST['solution_description'],
            $_POST['completed_date'],
            $_POST['school_name'],
            $_POST['building_name'],
            $_POST['floor_name'],
            $_POST['room_name']
        ]);
        
        // Update equipment status if changed
        if ($_POST['status'] == 'ซ่อมเสร็จ') {
            $update_stmt = $db->prepare("UPDATE equipment SET status = 'ใช้งานปกติ' WHERE id = ?");
            $update_stmt->execute([$_POST['equipment_id']]);
        }
        
        $_SESSION['success'] = "เพิ่มข้อมูลการซ่อมเรียบร้อยแล้ว";
        header("Location: maintenance.php");
        exit();
    }
    
    if (isset($_POST['edit_maintenance'])) {
        $stmt = $db->prepare("UPDATE maintenance SET equipment_id=?, report_date=?, problem_description=?, reported_by=?, assigned_technician=?, cost=?, status=?, solution_description=?, completed_date=?, school_name=?, building_name=?, floor_name=?, room_name=? WHERE id=?");
        $stmt->execute([
            $_POST['equipment_id'],
            $_POST['report_date'],
            $_POST['problem_description'],
            $_POST['reported_by'],
            $_POST['assigned_technician'],
            $_POST['cost'],
            $_POST['status'],
            $_POST['solution_description'],
            $_POST['completed_date'],
            $_POST['school_name'],
            $_POST['building_name'],
            $_POST['floor_name'],
            $_POST['room_name'],
            $_POST['id']
        ]);
        
        // Update equipment status if changed
        if ($_POST['status'] == 'ซ่อมเสร็จ') {
            $update_stmt = $db->prepare("UPDATE equipment SET status = 'ใช้งานปกติ' WHERE id = ?");
            $update_stmt->execute([$_POST['equipment_id']]);
        }
        
        $_SESSION['success'] = "แก้ไขข้อมูลการซ่อมเรียบร้อยแล้ว";
        header("Location: maintenance.php");
        exit();
    }
}

// Get maintenance list with location data
$maintenance_query = "SELECT m.*, e.code, e.name as equipment_name,
                             c.name as category_name, ci.name as item_name
                      FROM maintenance m 
                      JOIN equipment e ON m.equipment_id = e.id 
                      LEFT JOIN categories c ON e.category_id = c.id 
                      LEFT JOIN categories_items ci ON e.category_item_id = ci.id 
                      ORDER BY m.created_at DESC";
$maintenance_list = $db->query($maintenance_query)->fetchAll(PDO::FETCH_ASSOC);

// Get equipment for dropdown
$equipment_list = $db->query("SELECT e.id, e.code, e.name, c.name as category_name, ci.name as item_name 
                              FROM equipment e 
                              LEFT JOIN categories c ON e.category_id = c.id 
                              LEFT JOIN categories_items ci ON e.category_item_id = ci.id 
                              ORDER BY e.code")->fetchAll(PDO::FETCH_ASSOC);

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

// ข้อมูลโรงเรียนจาก PHP (สำหรับ dropdown เริ่มต้น)
$schools = [
    "โรงเรียนวารีเชียงใหม่",
    "โรงเรียนอนุบาลวารีเชียงใหม่", 
    "โรงเรียนนานาชาติวารีเชียงใหม่"
];

// ข้อมูลห้อง (ใช้ร่วมกันทุกชั้น)
$rooms = [
    "ห้อง 101", "ห้อง 102", "ห้อง 103", "ห้อง 104", "ห้อง 105",
    "ห้อง 201", "ห้อง 202", "ห้อง 203", "ห้อง 204", "ห้อง 205", 
    "ห้อง 301", "ห้อง 302", "ห้อง 303", "ห้อง 304", "ห้อง 305",
    "ห้อง 401", "ห้อง 402", "ห้อง 403", "ห้อง 404", "ห้อง 405",
    "ห้อง 501", "ห้อง 502", "ห้อง 503", "ห้อง 504", "ห้อง 505",
    "ห้อง 601", "ห้อง 602", "ห้อง 603", "ห้อง 604", "ห้อง 605",
    "ห้อง 701", "ห้อง 702", "ห้อง 703", "ห้อง 704", "ห้อง 705",
    "ห้องประชุมใหญ่", "ห้องประชุมเล็ก", "ห้องพักครู", "ห้องสำนักงาน",
    "ห้องปฏิบัติการวิทยาศาสตร์", "ห้องคอมพิวเตอร์", "ห้องดนตรี", "ห้องศิลปะ", "ห้องสมุด"
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
                            <th>รหัสครุภัณฑ์</th>
                            <th>ชื่ออุปกรณ์</th>
                            <th>สถานที่</th>
                            <th>วันที่แจ้งซ่อม</th>
                            <th>ผู้แจ้งซ่อม</th>
                            <th>ผู้ดำเนินการ</th>
                            <th>ค่าใช้จ่าย</th>
                            <th>สถานะ</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($maintenance_list as $maintenance): ?>
                        <tr>
                            <td><?php echo $maintenance['code']; ?></td>
                            <td><?php echo $maintenance['equipment_name']; ?></td>
                            <td>
                                <small class="text-muted">
                                    <?php 
                                    $location = [];
                                    if ($maintenance['school_name']) $location[] = $maintenance['school_name'];
                                    if ($maintenance['building_name']) $location[] = $maintenance['building_name'];
                                    if ($maintenance['floor_name']) $location[] = $maintenance['floor_name'];
                                    if ($maintenance['room_name']) $location[] = $maintenance['room_name'];
                                    echo implode(' → ', $location);
                                    ?>
                                </small>
                            </td>
                            <td><?php echo $maintenance['report_date']; ?></td>
                            <td><?php echo $maintenance['reported_by']; ?></td>
                            <td><?php echo $maintenance['assigned_technician']; ?></td>
                            <td><?php echo number_format($maintenance['cost'], 2); ?> บาท</td>
                            <td>
                                <?php 
                                $status_badge = [
                                    'รอซ่อม' => 'warning',
                                    'กำลังดำเนินการ' => 'info',
                                    'ซ่อมเสร็จ' => 'success',
                                    'ยกเลิก' => 'danger'
                                ];
                                ?>
                                <span class="badge bg-<?php echo $status_badge[$maintenance['status']]; ?>">
                                    <?php echo $maintenance['status']; ?>
                                </span>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#maintenanceModal" onclick='editMaintenance(<?php echo json_encode($maintenance); ?>)'>
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="maintenance.php?action=delete&id=<?php echo $maintenance['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('คุณแน่ใจหรือไม่?')">
                                    <i class="fas fa-trash"></i>
                                </a>
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
                                <?php foreach($schools as $school): ?>
                                <option value="<?php echo $school; ?>"><?php echo $school; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ตึก *</label>
                            <select class="form-control" name="building_name" id="building_name" required disabled onchange="updateFloors()">
                                <option value="">เลือกตึก</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ชั้น *</label>
                            <select class="form-control" name="floor_name" id="floor_name" required disabled onchange="updateRooms()">
                                <option value="">เลือกชั้น</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ห้อง *</label>
                            <select class="form-control" name="room_name" id="room_name" required disabled>
                                <option value="">เลือกห้อง</option>
                            </select>
                        </div>
                    </div>

                    <!-- Equipment Selection -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2"><i class="fas fa-laptop"></i> เลือกครุภัณฑ์</h6>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">เลือกครุภัณฑ์ *</label>
                            <select class="form-control" name="equipment_id" id="equipment_id" required>
                                <option value="">เลือกครุภัณฑ์</option>
                                <?php foreach($equipment_list as $equipment): ?>
                                <option value="<?php echo $equipment['id']; ?>" data-category="<?php echo $equipment['category_name']; ?>" data-item="<?php echo $equipment['item_name']; ?>">
                                    <?php echo $equipment['code'] . ' - ' . $equipment['name'] . ' (' . $equipment['category_name'] . ')'; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
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
                            <label class="form-label">สถานะ</label>
                            <select class="form-control" name="status" id="status">
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
                            <input type="text" class="form-control" name="assigned_technician" id="assigned_technician" placeholder="กรอกชื่อผู้ดำเนินการซ่อม">
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

// ข้อมูลห้อง (จาก PHP)
const roomsData = <?php echo json_encode($rooms); ?>;

function updateBuildings() {
    const schoolName = document.getElementById('school_name').value;
    const buildingSelect = document.getElementById('building_name');
    const floorSelect = document.getElementById('floor_name');
    const roomSelect = document.getElementById('room_name');
    
    // Reset dependent dropdowns
    buildingSelect.innerHTML = '<option value="">เลือกตึก</option>';
    floorSelect.innerHTML = '<option value="">เลือกชั้น</option>';
    roomSelect.innerHTML = '<option value="">เลือกห้อง</option>';
    
    if (schoolName && schoolData[schoolName]) {
        buildingSelect.disabled = false;
        
        // Add buildings for selected school
        schoolData[schoolName].buildings.forEach(building => {
            const option = document.createElement('option');
            option.value = building.name;
            option.textContent = building.name;
            buildingSelect.appendChild(option);
        });
    } else {
        buildingSelect.disabled = true;
        floorSelect.disabled = true;
        roomSelect.disabled = true;
    }
}

function updateFloors() {
    const schoolName = document.getElementById('school_name').value;
    const buildingName = document.getElementById('building_name').value;
    const floorSelect = document.getElementById('floor_name');
    const roomSelect = document.getElementById('room_name');
    
    // Reset dependent dropdowns
    floorSelect.innerHTML = '<option value="">เลือกชั้น</option>';
    roomSelect.innerHTML = '<option value="">เลือกห้อง</option>';
    
    if (schoolName && buildingName && schoolData[schoolName]) {
        floorSelect.disabled = false;
        
        // Find the selected building and get its floors
        const selectedBuilding = schoolData[schoolName].buildings.find(b => b.name === buildingName);
        if (selectedBuilding) {
            selectedBuilding.floors.forEach(floor => {
                const option = document.createElement('option');
                option.value = floor;
                option.textContent = floor;
                floorSelect.appendChild(option);
            });
        }
    } else {
        floorSelect.disabled = true;
        roomSelect.disabled = true;
    }
}

function updateRooms() {
    const roomSelect = document.getElementById('room_name');
    
    roomSelect.innerHTML = '<option value="">เลือกห้อง</option>';
    roomSelect.disabled = false;
    
    // Add all rooms (since rooms are shared across all floors)
    roomsData.forEach(room => {
        const option = document.createElement('option');
        option.value = room;
        option.textContent = room;
        roomSelect.appendChild(option);
    });
}

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
    document.getElementById('building_name').innerHTML = '<option value="">เลือกตึก</option>';
    document.getElementById('floor_name').innerHTML = '<option value="">เลือกชั้น</option>';
    document.getElementById('room_name').innerHTML = '<option value="">เลือกห้อง</option>';
    document.getElementById('building_name').disabled = true;
    document.getElementById('floor_name').disabled = true;
    document.getElementById('room_name').disabled = true;
}

function editMaintenance(maintenance) {
    document.getElementById('maintenance_id').value = maintenance.id;
    document.getElementById('equipment_id').value = maintenance.equipment_id;
    document.getElementById('report_date').value = maintenance.report_date;
    document.getElementById('problem_description').value = maintenance.problem_description || '';
    document.getElementById('reported_by').value = maintenance.reported_by || '';
    document.getElementById('assigned_technician').value = maintenance.assigned_technician || '';
    document.getElementById('cost').value = maintenance.cost || '0';
    document.getElementById('status').value = maintenance.status || 'รอซ่อม';
    document.getElementById('solution_description').value = maintenance.solution_description || '';
    document.getElementById('completed_date').value = maintenance.completed_date || '';
    
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
                        updateRooms();
                        setTimeout(() => {
                            if (maintenance.room_name) {
                                document.getElementById('room_name').value = maintenance.room_name;
                            }
                        }, 100);
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
}

$(document).ready(function() {
    $('#dataTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/th.json'
        },
        order: [[3, 'desc']], // Sort by report date descending
        pageLength: 25,
        responsive: true
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>