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
        $stmt = $db->prepare("INSERT INTO maintenance (equipment_id, report_date, problem_description, reported_by, assigned_technician, cost, status, solution_description, completed_date, school_id, building_id, floor_id, room_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
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
            $_POST['school_id'],
            $_POST['building_id'],
            $_POST['floor_id'],
            $_POST['room_id']
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
        $stmt = $db->prepare("UPDATE maintenance SET equipment_id=?, report_date=?, problem_description=?, reported_by=?, assigned_technician=?, cost=?, status=?, solution_description=?, completed_date=?, school_id=?, building_id=?, floor_id=?, room_id=? WHERE id=?");
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
            $_POST['school_id'],
            $_POST['building_id'],
            $_POST['floor_id'],
            $_POST['room_id'],
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
                             s.name as school_name, b.name as building_name, 
                             f.name as floor_name, r.name as room_name,
                             c.name as category_name, ci.name as item_name
                      FROM maintenance m 
                      JOIN equipment e ON m.equipment_id = e.id 
                      LEFT JOIN schools s ON m.school_id = s.id
                      LEFT JOIN buildings b ON m.building_id = b.id
                      LEFT JOIN floors f ON m.floor_id = f.id
                      LEFT JOIN rooms r ON m.room_id = r.id
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

// Get location data for dropdowns
$schools = $db->query("SELECT * FROM schools WHERE status = 'active'")->fetchAll(PDO::FETCH_ASSOC);
$buildings = $db->query("SELECT * FROM buildings WHERE status = 'active'")->fetchAll(PDO::FETCH_ASSOC);
$floors = $db->query("SELECT * FROM floors WHERE status = 'active'")->fetchAll(PDO::FETCH_ASSOC);
$rooms = $db->query("SELECT * FROM rooms WHERE status = 'active'")->fetchAll(PDO::FETCH_ASSOC);

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
                            <label class="form-label">โรงเรียน</label>
                            <select class="form-control" name="school_id" id="school_id" required onchange="updateBuildings()">
                                <option value="">เลือกโรงเรียน</option>
                                <?php foreach($schools as $school): ?>
                                <option value="<?php echo $school['id']; ?>"><?php echo $school['name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ตึก</label>
                            <select class="form-control" name="building_id" id="building_id" required disabled onchange="updateFloors()">
                                <option value="">เลือกตึก</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ชั้น</label>
                            <select class="form-control" name="floor_id" id="floor_id" required disabled onchange="updateRooms()">
                                <option value="">เลือกชั้น</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ห้อง</label>
                            <select class="form-control" name="room_id" id="room_id" required disabled>
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
// Location data from PHP
const buildingsData = <?php echo json_encode($buildings); ?>;
const floorsData = <?php echo json_encode($floors); ?>;
const roomsData = <?php echo json_encode($rooms); ?>;

function updateBuildings() {
    const schoolId = document.getElementById('school_id').value;
    const buildingSelect = document.getElementById('building_id');
    const floorSelect = document.getElementById('floor_id');
    const roomSelect = document.getElementById('room_id');
    
    // Reset dependent dropdowns
    buildingSelect.innerHTML = '<option value="">เลือกตึก</option>';
    floorSelect.innerHTML = '<option value="">เลือกชั้น</option>';
    roomSelect.innerHTML = '<option value="">เลือกห้อง</option>';
    
    if (schoolId) {
        buildingSelect.disabled = false;
        
        // Filter buildings by school_id
        const filteredBuildings = buildingsData.filter(building => building.school_id == schoolId);
        filteredBuildings.forEach(building => {
            const option = document.createElement('option');
            option.value = building.id;
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
    const buildingId = document.getElementById('building_id').value;
    const floorSelect = document.getElementById('floor_id');
    const roomSelect = document.getElementById('room_id');
    
    // Reset dependent dropdowns
    floorSelect.innerHTML = '<option value="">เลือกชั้น</option>';
    roomSelect.innerHTML = '<option value="">เลือกห้อง</option>';
    
    if (buildingId) {
        floorSelect.disabled = false;
        
        // Filter floors by building_id
        const filteredFloors = floorsData.filter(floor => floor.building_id == buildingId);
        filteredFloors.forEach(floor => {
            const option = document.createElement('option');
            option.value = floor.id;
            option.textContent = floor.name;
            floorSelect.appendChild(option);
        });
    } else {
        floorSelect.disabled = true;
        roomSelect.disabled = true;
    }
}

function updateRooms() {
    const floorId = document.getElementById('floor_id').value;
    const roomSelect = document.getElementById('room_id');
    
    roomSelect.innerHTML = '<option value="">เลือกห้อง</option>';
    
    if (floorId) {
        roomSelect.disabled = false;
        
        // Filter rooms by floor_id
        const filteredRooms = roomsData.filter(room => room.floor_id == floorId);
        filteredRooms.forEach(room => {
            const option = document.createElement('option');
            option.value = room.id;
            option.textContent = room.name;
            roomSelect.appendChild(option);
        });
    } else {
        roomSelect.disabled = true;
    }
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
    document.getElementById('building_id').innerHTML = '<option value="">เลือกตึก</option>';
    document.getElementById('floor_id').innerHTML = '<option value="">เลือกชั้น</option>';
    document.getElementById('room_id').innerHTML = '<option value="">เลือกห้อง</option>';
    document.getElementById('building_id').disabled = true;
    document.getElementById('floor_id').disabled = true;
    document.getElementById('room_id').disabled = true;
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
    if (maintenance.school_id) {
        document.getElementById('school_id').value = maintenance.school_id;
        updateBuildings();
        setTimeout(() => {
            if (maintenance.building_id) {
                document.getElementById('building_id').value = maintenance.building_id;
                updateFloors();
                setTimeout(() => {
                    if (maintenance.floor_id) {
                        document.getElementById('floor_id').value = maintenance.floor_id;
                        updateRooms();
                        setTimeout(() => {
                            if (maintenance.room_id) {
                                document.getElementById('room_id').value = maintenance.room_id;
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

// Real-time table update simulation
function refreshTable() {
    // In a real application, you would use AJAX to refresh the table data
    // For demonstration, we'll just reload the page after a short delay
    setTimeout(() => {
        window.location.reload();
    }, 1000);
}

// Auto-refresh table every 30 seconds
setInterval(() => {
    // Check if modal is not open before refreshing
    if (!document.getElementById('maintenanceModal').classList.contains('show')) {
        refreshTable();
    }
}, 30000);

$(document).ready(function() {
    $('#dataTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/th.json'
        },
        order: [[3, 'desc']], // Sort by report date descending
        pageLength: 25,
        responsive: true
    });

    // Auto-submit form handler for real-time updates
    $('#maintenanceForm').on('submit', function(e) {
        e.preventDefault();
        
        // Submit form via AJAX for real-time update
        $.ajax({
            url: 'maintenance.php',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                $('#maintenanceModal').modal('hide');
                refreshTable();
            },
            error: function() {
                alert('เกิดข้อผิดพลาดในการบันทึกข้อมูล');
            }
        });
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>