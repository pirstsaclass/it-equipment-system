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
            $stmt = $db->prepare("DELETE FROM departments WHERE id = ?");
            $stmt->execute([$id]);
            $_SESSION['success'] = "ลบข้อมูลแผนกเรียบร้อยแล้ว";
        }
        header("Location: departments.php");
        exit();
    }
}

if ($_POST) {
    if (isset($_POST['add_department'])) {
        $stmt = $db->prepare("INSERT INTO departments (name, building, floor, room, type, responsible_person) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['name'],
            $_POST['building'],
            $_POST['floor'],
            $_POST['room'],
            $_POST['type'],
            $_POST['responsible_person']
        ]);
        $_SESSION['success'] = "เพิ่มข้อมูลแผนกเรียบร้อยแล้ว";
        header("Location: departments.php");
        exit();
    }
    
    if (isset($_POST['edit_department'])) {
        $stmt = $db->prepare("UPDATE departments SET name=?, building=?, floor=?, room=?, type=?, responsible_person=? WHERE id=?");
        $stmt->execute([
            $_POST['name'],
            $_POST['building'],
            $_POST['floor'],
            $_POST['room'],
            $_POST['type'],
            $_POST['responsible_person'],
            $_POST['id']
        ]);
        $_SESSION['success'] = "แก้ไขข้อมูลแผนกเรียบร้อยแล้ว";
        header("Location: departments.php");
        exit();
    }
}

// Get departments list
$departments_query = "SELECT * FROM departments ORDER BY name";
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
                            <th>ชื่อแผนก</th>
                            <th>อาคาร</th>
                            <th>ชั้น</th>
                            <th>ห้อง</th>
                            <th>ประเภท</th>
                            <th>ผู้รับผิดชอบ</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($departments_list as $department): ?>
                        <tr>
                            <td><?php echo $department['name']; ?></td>
                            <td><?php echo $department['building']; ?></td>
                            <td><?php echo $department['floor']; ?></td>
                            <td><?php echo $department['room']; ?></td>
                            <td><?php echo $department['type']; ?></td>
                            <td><?php echo $department['responsible_person']; ?></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#departmentModal" onclick='editDepartment(<?php echo json_encode($department); ?>)'>
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="departments.php?action=delete&id=<?php echo $department['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('คุณแน่ใจหรือไม่?')">
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
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="departmentModalLabel">เพิ่มแผนก</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" id="departmentForm">
                <div class="modal-body">
                    <input type="hidden" name="id" id="department_id">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ชื่อแผนก *</label>
                            <input type="text" class="form-control" name="name" id="name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ประเภท *</label>
                            <select class="form-control" name="type" id="type" required>
                                <option value="">เลือกประเภท</option>
                                <option value="อำนวยการ">อำนวยการ</option>
                                <option value="อนุบาล">อนุบาล</option>
                                <option value="ประถม">ประถม</option>
                                <option value="มัธยม">มัธยม</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">อาคาร</label>
                            <select class="form-control" name="building" id="building">
                                <option value="">เลือกอาคาร</option>
                                <?php for($i=1; $i<=10; $i++): ?>
                                <option value="อาคาร <?php echo $i; ?>">อาคาร <?php echo $i; ?></option>
                                <?php endfor; ?>
                                <option value="Office">Office</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">ชั้น</label>
                            <select class="form-control" name="floor" id="floor">
                                <option value="">เลือกชั้น</option>
                                <?php for($i=1; $i<=7; $i++): ?>
                                <option value="ชั้น <?php echo $i; ?>">ชั้น <?php echo $i; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">ห้อง</label>
                            <input type="text" class="form-control" name="room" id="room">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">ผู้รับผิดชอบ</label>
                        <input type="text" class="form-control" name="responsible_person" id="responsible_person">
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
    document.getElementById('department_id').value = department.id;
    document.getElementById('name').value = department.name;
    document.getElementById('type').value = department.type || '';
    document.getElementById('building').value = department.building || '';
    document.getElementById('floor').value = department.floor || '';
    document.getElementById('room').value = department.room || '';
    document.getElementById('responsible_person').value = department.responsible_person || '';
    
    document.getElementById('departmentModalLabel').textContent = 'แก้ไขข้อมูลแผนก';
    document.getElementById('submitBtn').name = 'edit_department';
    document.getElementById('submitBtn').textContent = 'อัพเดท';
}

$(document).ready(function() {
    $('#dataTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/th.json'
        }
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>