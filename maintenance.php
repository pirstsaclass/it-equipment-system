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
        $stmt = $db->prepare("INSERT INTO maintenance (equipment_id, report_date, problem_description, reported_by, assigned_technician, cost, status, solution_description, completed_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['equipment_id'],
            $_POST['report_date'],
            $_POST['problem_description'],
            $_POST['reported_by'],
            $_POST['assigned_technician'],
            $_POST['cost'],
            $_POST['status'],
            $_POST['solution_description'],
            $_POST['completed_date']
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
        $stmt = $db->prepare("UPDATE maintenance SET equipment_id=?, report_date=?, problem_description=?, reported_by=?, assigned_technician=?, cost=?, status=?, solution_description=?, completed_date=? WHERE id=?");
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

// Get maintenance list
$maintenance_query = "SELECT m.*, e.code, e.name as equipment_name, c.name as category_name, ci.name as item_name
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
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">เลือกครุภัณฑ์ *</label>
                            <select class="form-control" name="equipment_id" id="equipment_id" required>
                                <option value="">เลือกครุภัณฑ์</option>
                                <?php foreach($equipment_list as $equipment): ?>
                                <option value="<?php echo $equipment['id']; ?>">
                                    <?php echo $equipment['code'] . ' - ' . $equipment['name']; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">วันที่แจ้งซ่อม *</label>
                            <input type="date" class="form-control" name="report_date" id="report_date" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">ปัญหาที่พบ *</label>
                        <textarea class="form-control" name="problem_description" id="problem_description" rows="3" required></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ผู้แจ้งซ่อม *</label>
                            <input type="text" class="form-control" name="reported_by" id="reported_by" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ผู้ดำเนินการ</label>
                            <input type="text" class="form-control" name="assigned_technician" id="assigned_technician">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ค่าใช้จ่าย (บาท)</label>
                            <input type="number" step="0.01" class="form-control" name="cost" id="cost" value="0">
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
                        <label class="form-label">วิธีการแก้ไข</label>
                        <textarea class="form-control" name="solution_description" id="solution_description" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">วันที่ซ่อมเสร็จ</label>
                        <input type="date" class="form-control" name="completed_date" id="completed_date">
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
function clearForm() {
    document.getElementById('maintenanceForm').reset();
    document.getElementById('maintenance_id').value = '';
    document.getElementById('report_date').value = '<?php echo date('Y-m-d'); ?>';
    document.getElementById('cost').value = '0';
    document.getElementById('maintenanceModalLabel').textContent = 'แจ้งซ่อมใหม่';
    document.getElementById('submitBtn').name = 'add_maintenance';
    document.getElementById('submitBtn').textContent = 'บันทึก';
    document.getElementById('equipment_id').disabled = false;
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
        order: [[2, 'desc']] // Sort by report date descending
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>