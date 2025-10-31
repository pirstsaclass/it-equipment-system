<?php
require_once 'includes/header.php';

// CRUD Operations
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $id = isset($_GET['id']) ? $_GET['id'] : null;
    
    if ($action == 'delete' && $id) {
        // Check if employee is used in users table
        $check_stmt = $db->prepare("SELECT COUNT(*) as count FROM users WHERE employee_id = ?");
        $check_stmt->execute([$id]);
        $used_count = $check_stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        if ($used_count > 0) {
            $_SESSION['error'] = "ไม่สามารถลบพนักงานนี้ได้ เนื่องจากมีบัญชีผู้ใช้ที่ใช้งานอยู่";
        } else {
            $stmt = $db->prepare("DELETE FROM employees WHERE employee_id = ?");
            $stmt->execute([$id]);
            $_SESSION['success'] = "ลบข้อมูลพนักงานเรียบร้อยแล้ว";
        }
        header("Location: employees.php");
        exit();
    }
}

if ($_POST) {
    if (isset($_POST['add_employee'])) {
        $stmt = $db->prepare("INSERT INTO employees (employee_code, first_name, last_name, department_id, position_name, phone_number, email_address) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['employee_code'],
            $_POST['first_name'],
            $_POST['last_name'],
            $_POST['department_id'],
            $_POST['position'],
            $_POST['phone'],
            $_POST['email']
        ]);
        $_SESSION['success'] = "เพิ่มข้อมูลพนักงานเรียบร้อยแล้ว";
        header("Location: employees.php");
        exit();
    }
    
    if (isset($_POST['edit_employee'])) {
        $stmt = $db->prepare("UPDATE employees SET employee_code=?, first_name=?, last_name=?, department_id=?, position_name=?, phone_number=?, email_address=? WHERE employee_id=?");
        $stmt->execute([
            $_POST['employee_code'],
            $_POST['first_name'],
            $_POST['last_name'],
            $_POST['department_id'],
            $_POST['position'],
            $_POST['phone'],
            $_POST['email'],
            $_POST['id']
        ]);
        $_SESSION['success'] = "แก้ไขข้อมูลพนักงานเรียบร้อยแล้ว";
        header("Location: employees.php");
        exit();
    }
}

// Get employees list - แก้ไข query ให้ตรงกับโครงสร้างฐานข้อมูล
$employees_query = "SELECT e.*, d.department_name, d.school_name 
    FROM employees e 
    LEFT JOIN departments d ON e.department_id = d.id 
    ORDER BY e.first_name, e.last_name";
$employees_list = $db->query($employees_query)->fetchAll(PDO::FETCH_ASSOC);

// Get departments from database
$departments_query = "SELECT id, department_name, school_name FROM departments ORDER BY school_name, department_name";
$departments = $db->query($departments_query)->fetchAll(PDO::FETCH_ASSOC);
?>

<?php 
// Include sidebar
include 'includes/sidebar.php';
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <?php include 'includes/navbar.php'; ?>

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">จัดการพนักงาน</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#employeeModal" onclick="clearForm()">
            <i class="fas fa-plus"></i> เพิ่มพนักงาน
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

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h5 class="m-0 font-weight-bold ">รายการพนักงานทั้งหมด</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>รหัสพนักงาน</th>
                            <th>ชื่อ-สกุล</th>
                            <th>โรงเรียน</th>
                            <th>แผนก</th>
                            <th>ตำแหน่ง</th>
                            <th>โทรศัพท์</th>
                            <th>อีเมล</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($employees_list as $employee): ?>
                        <tr>
                            <td><?php echo $employee['employee_code']; ?></td>
                            <td><?php echo $employee['first_name'] . ' ' . $employee['last_name']; ?></td>
                            <td><?php echo $employee['school_name']; ?>
                                <?php if (!empty($employee['school_name'])): ?>                                   
                                <?php else: ?>
                                    <span>ไม่ได้ระบุ</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $employee['department_name']; ?></td>
                            <td><?php echo $employee['position_name']; ?></td>
                            <td><?php echo $employee['phone_number']; ?></td>
                            <td><?php echo $employee['email_address']; ?></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#employeeModal" onclick='editEmployee(<?php echo json_encode($employee); ?>)'>
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="employees.php?action=delete&id=<?php echo $employee['employee_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('คุณแน่ใจหรือไม่?')">
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

<!-- Employee Modal -->
<div class="modal fade" id="employeeModal" tabindex="-1" aria-labelledby="employeeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="employeeModalLabel">เพิ่มพนักงาน</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" id="employeeForm">
                <div class="modal-body">
                    <input type="hidden" name="id" id="employee_id">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">รหัสพนักงาน *</label>
                            <input type="text" class="form-control" name="employee_code" id="employee_code" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">แผนก</label>
                            <select class="form-control" name="department_id" id="department_id">
                                <option value="">เลือกแผนก</option>
                                <?php 
                                // จัดกลุ่มแผนกตามโรงเรียน
                                $grouped_departments = [];
                                foreach($departments as $dept) {
                                    $school = $dept['school_name'] ?: 'ไม่มีโรงเรียน';
                                    $grouped_departments[$school][] = $dept;
                                }
                                
                                foreach($grouped_departments as $school => $school_departments): 
                                ?>
                                    <optgroup label="<?php echo $school; ?>">
                                        <?php foreach($school_departments as $dept): ?>
                                        <option value="<?php echo $dept['id']; ?>">
                                            <?php echo $dept['department_name']; ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </optgroup>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ชื่อ *</label>
                            <input type="text" class="form-control" name="first_name" id="first_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">สกุล *</label>
                            <input type="text" class="form-control" name="last_name" id="last_name" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ตำแหน่ง</label>
                            <input type="text" class="form-control" name="position" id="position">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">โทรศัพท์</label>
                            <input type="text" class="form-control" name="phone" id="phone">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">อีเมล</label>
                        <input type="email" class="form-control" name="email" id="email">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                    <button type="submit" name="add_employee" id="submitBtn" class="btn btn-primary">บันทึก</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function clearForm() {
    document.getElementById('employeeForm').reset();
    document.getElementById('employee_id').value = '';
    document.getElementById('employeeModalLabel').textContent = 'เพิ่มพนักงาน';
    document.getElementById('submitBtn').name = 'add_employee';
    document.getElementById('submitBtn').textContent = 'บันทึก';
}

function editEmployee(employee) {
    document.getElementById('employee_id').value = employee.employee_id;
    document.getElementById('employee_code').value = employee.employee_code;
    document.getElementById('first_name').value = employee.first_name;
    document.getElementById('last_name').value = employee.last_name;
    document.getElementById('department_id').value = employee.department_id || '';
    document.getElementById('position').value = employee.position_name || '';
    document.getElementById('phone').value = employee.phone_number || '';
    document.getElementById('email').value = employee.email_address || '';
    
    document.getElementById('employeeModalLabel').textContent = 'แก้ไขข้อมูลพนักงาน';
    document.getElementById('submitBtn').name = 'edit_employee';
    document.getElementById('submitBtn').textContent = 'อัพเดท';
}

$(document).ready(function() {
    $('#dataTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/th.json'
        },
        order: [[0, 'asc']], // เรียงตามรหัสพนักงาน
        columnDefs: [
            { orderable: false, targets: [7] } // ปิดการเรียงลำดับคอลัมน์จัดการ
        ]
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>