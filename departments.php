<?php
require_once 'includes/header.php';

// CRUD Operations
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $id = isset($_GET['id']) ? $_GET['id'] : null;
    
    if ($action == 'delete' && $id) {
        // Check if department is used in equipment
        $check_stmt = $db->prepare("SELECT COUNT(*) as count FROM equipment WHERE department_id = ?");
        $check_stmt->execute([$id]);
        $used_count = $check_stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        if ($used_count > 0) {
            $_SESSION['error'] = "ไม่สามารถลบแผนกนี้ได้ เนื่องจากมีครุภัณฑ์ที่ใช้งานอยู่";
        } else {
            $stmt = $db->prepare("DELETE FROM departments WHERE department_id = ?");
            $stmt->execute([$id]);
            $_SESSION['success'] = "ลบข้อมูลแผนกเรียบร้อยแล้ว";
        }
        header("Location: departments.php");
        exit();
    }
}

if ($_POST) {
    if (isset($_POST['add_department'])) {
        $stmt = $db->prepare("INSERT INTO departments (department_name, department_description) VALUES (?, ?)");
        $stmt->execute([
            $_POST['name'],
            $_POST['description']
        ]);
        $_SESSION['success'] = "เพิ่มข้อมูลแผนกเรียบร้อยแล้ว";
        header("Location: departments.php");
        exit();
    }
    
    if (isset($_POST['edit_department'])) {
        $stmt = $db->prepare("UPDATE departments SET department_name=?, department_description=? WHERE department_id=?");
        $stmt->execute([
            $_POST['name'],
            $_POST['description'],
            $_POST['id']
        ]);
        $_SESSION['success'] = "แก้ไขข้อมูลแผนกเรียบร้อยแล้ว";
        header("Location: departments.php");
        exit();
    }
}

// Get departments list
$departments_query = "SELECT * FROM departments ORDER BY department_name";
$departments_list = $db->query($departments_query)->fetchAll(PDO::FETCH_ASSOC);
?>

<?php 
// Include sidebar
include 'includes/sidebar.php';
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <?php include 'includes/navbar.php'; ?>

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">จัดการแผนก</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#departmentModal" onclick="clearForm()">
            <i class="fas fa-plus"></i> เพิ่มแผนก
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
            <h5 class="m-0 font-weight-bold ">รายการแผนกทั้งหมด</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>รหัสแผนก</th>
                            <th>ชื่อแผนก</th>
                            <th>รายละเอียด</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($departments_list as $department): ?>
                        <tr>
                            <td><?php echo $department['department_id']; ?></td>
                            <td><?php echo $department['department_name']; ?></td>
                            <td><?php echo $department['department_description']; ?></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#departmentModal" onclick='editDepartment(<?php echo json_encode($department); ?>)'>
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="departments.php?action=delete&id=<?php echo $department['department_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('คุณแน่ใจหรือไม่?')">
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

<!-- Department Modal -->
<div class="modal fade" id="departmentModal" tabindex="-1" aria-labelledby="departmentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="departmentModalLabel">เพิ่มแผนก</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" id="departmentForm">
                <div class="modal-body">
                    <input type="hidden" name="id" id="department_id">
                    
                    <div class="mb-3">
                        <label class="form-label">ชื่อแผนก *</label>
                        <input type="text" class="form-control" name="name" id="name" placeholder="ชื่อแผนก" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">รายละเอียด</label>
                        <textarea class="form-control" name="description" id="description" placeholder="รายละเอียดแผนก" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                    <button type="submit" name="add_department" id="submitBtn" class="btn btn-primary">บันทึก</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function clearForm() {
    document.getElementById('departmentForm').reset();
    document.getElementById('department_id').value = '';
    document.getElementById('departmentModalLabel').textContent = 'เพิ่มแผนก';
    document.getElementById('submitBtn').name = 'add_department';
    document.getElementById('submitBtn').textContent = 'บันทึก';
}

function editDepartment(department) {
    document.getElementById('department_id').value = department.department_id || '';
    document.getElementById('name').value = department.department_name || '';
    document.getElementById('description').value = department.department_description || '';
    
    document.getElementById('departmentModalLabel').textContent = 'แก้ไขข้อมูลแผนก';
    document.getElementById('submitBtn').name = 'edit_department';
    document.getElementById('submitBtn').textContent = 'อัพเดท';
}

$(document).ready(function() {
    $('#dataTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/th.json'
        },
        order: [[1, 'asc']], // เรียงตามชื่อแผนก
        columnDefs: [
            { orderable: false, targets: [3] } // ปิดการเรียงลำดับคอลัมน์จัดการ
        ]
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>