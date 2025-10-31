<?php
require_once 'includes/header.php';

// CRUD Operations
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $id = isset($_GET['id']) ? $_GET['id'] : null;
    
if ($action == 'delete' && $id) {
    try {
        // ลองตรวจสอบว่ามีการใช้งานแผนกหรือไม่ (หากมีคอลัมน์ department_id)
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
    } catch (PDOException $e) {
        // หากไม่มีคอลัมน์ department_id ให้ลบได้เลย
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
        $stmt = $db->prepare("INSERT INTO departments (department_name, school_name, department_description) VALUES (?, ?, ?)");
        $stmt->execute([
            $_POST['name'],
            $_POST['school_name'],
            $_POST['description']
        ]);
        $_SESSION['success'] = "เพิ่มข้อมูลแผนกเรียบร้อยแล้ว";
        header("Location: departments.php");
        exit();
    }
    
    if (isset($_POST['edit_department'])) {
        $stmt = $db->prepare("UPDATE departments SET department_name=?, school_name=?, department_description=? WHERE id=?");
        $stmt->execute([
            $_POST['name'],
            $_POST['school_name'],
            $_POST['description'],
            $_POST['id']
        ]);
        $_SESSION['success'] = "แก้ไขข้อมูลแผนกเรียบร้อยแล้ว";
        header("Location: departments.php");
        exit();
    }
}

// Get departments list - เรียงตามลำดับการเพิ่มข้อมูล (id)
$departments_query = "SELECT * FROM departments ORDER BY id ASC";
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
                            <th>ลำดับ</th>
                            <th>โรงเรียน</th>
                            <th>ชื่อแผนก</th>
                            <th>รายละเอียด</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $counter = 1; ?>
                        <?php foreach($departments_list as $department): ?>
                        <tr>
                            <td><?php echo $counter++; ?></td>                            
                            <td><?php echo $department['school_name']; ?></td>
                            <td><?php echo $department['department_name']; ?></td>
                            <td><?php echo $department['department_description']; ?></td>
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
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="departmentModalLabel">เพิ่มแผนก</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" id="departmentForm">
                <div class="modal-body">
                    <input type="hidden" name="id" id="id">
                    
                    <div class="mb-3">
                        <label class="form-label">โรงเรียน *</label>
                        <select class="form-control" name="school_name" id="school_name" required onchange="updateDepartmentOptions(this.value)">
                            <option value="">-- เลือกโรงเรียน --</option>
                            <option value="โรงเรียนวารีเชียงใหม่">โรงเรียนวารีเชียงใหม่ (VCS)</option>
                            <option value="โรงเรียนอนุบาลวารีเชียงใหม่">โรงเรียนอนุบาลวารีเชียงใหม่ (VKS)</option>
                            <option value="โรงเรียนนานาชาติวารีเชียงใหม่">โรงเรียนนานาชาติวารีเชียงใหม่ (VCIS)</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">ชื่อแผนก *</label>
                        <select class="form-control" name="name" id="name" required>
                            <option value="">-- เลือกโรงเรียนก่อน --</option>
                        </select>
                        <small class="text-muted">เลือกโรงเรียนก่อนเพื่อดูรายการแผนก</small>
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
// ข้อมูลโรงเรียนและแผนกแบบคงที่
const schoolData = {
    "โรงเรียนวารีเชียงใหม่": [
        "ฝ่ายบริหาร",
        "แผนกอำนวยการ", 
        "ฝ่ายวิชาการ",
        "แผนกอนุบาล",
        "ประถมศึกษา",
        "มัธยมศึกษา",
        "สนับสนุน",
        "IT"
    ],
    "โรงเรียนอนุบาลวารีเชียงใหม่": [
        "แผนกอำนวยการ",
        "แผนกอนุบาล"
    ],
    "โรงเรียนนานาชาติวารีเชียงใหม่": [
        "Administration",
        "Kindergarten",
        "Primary",
        "Secondary", 
        "Support",
    ]
};

// ฟังก์ชันอัพเดท dropdown แผนก
function updateDepartmentOptions(school) {
    const nameSelect = document.getElementById('name');
    
    // ล้าง options เดิม
    nameSelect.innerHTML = '<option value="">-- เลือกแผนก --</option>';
    
    if (school && schoolData[school]) {
        const departments = schoolData[school];
        
        departments.forEach(dept => {
            const option = document.createElement('option');
            option.value = dept;
            option.textContent = dept;
            nameSelect.appendChild(option);
        });
    } else {
        nameSelect.innerHTML = '<option value="">-- เลือกโรงเรียนก่อน --</option>';
    }
}

function clearForm() {
    document.getElementById('departmentForm').reset();
    document.getElementById('id').value = '';
    document.getElementById('departmentModalLabel').textContent = 'เพิ่มแผนก';
    document.getElementById('submitBtn').name = 'add_department';
    document.getElementById('submitBtn').textContent = 'บันทึก';
    
    // รีเซ็ต dropdown ชื่อแผนก
    document.getElementById('name').innerHTML = '<option value="">-- เลือกโรงเรียนก่อน --</option>';
}

function editDepartment(department) {
    document.getElementById('id').value = department.id || '';
    document.getElementById('school_name').value = department.school_name || '';
    
    // อัพเดท dropdown ชื่อแผนกตามโรงเรียนที่เลือก
    updateDepartmentOptions(department.school_name);
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
        order: [[0, 'desc']], // เรียงตามลำดับการเพิ่มข้อมูล (ล่าสุดอยู่บน)
        columnDefs: [
            { orderable: false, targets: [4] } // ปิดการเรียงลำดับคอลัมน์จัดการ
        ]
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>