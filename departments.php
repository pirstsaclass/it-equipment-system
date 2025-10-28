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
        $stmt = $db->prepare("INSERT INTO departments (school, building, floor, room, name, type, responsible_person) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['school'],
            $_POST['building'],
            $_POST['floor'],
            $_POST['room'],
            $_POST['name'],
            $_POST['type'],
            $_POST['responsible_person']
        ]);
        $_SESSION['success'] = "เพิ่มข้อมูลแผนกเรียบร้อยแล้ว";
        header("Location: departments.php");
        exit();
    }
    
    if (isset($_POST['edit_department'])) {
        $stmt = $db->prepare("UPDATE departments SET school=?, building=?, floor=?, room=?, name=?, type=?, responsible_person=? WHERE id=?");
        $stmt->execute([
            $_POST['school'],
            $_POST['building'],
            $_POST['floor'],
            $_POST['room'],
            $_POST['name'],
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
$departments_query = "SELECT * FROM departments ORDER BY school, building, floor, name";
$departments_list = $db->query($departments_query)->fetchAll(PDO::FETCH_ASSOC);
?>

<?php 
// Include sidebar
include 'includes/sidebar.php';
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <?php include 'includes/navbar.php'; ?>

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">จัดการแผนกและห้องเรียน</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#departmentModal" onclick="clearForm()">
            <i class="fas fa-plus"></i> เพิ่มแผนก/ห้องเรียน
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
            <h5 class="m-0 font-weight-bold ">รายการแผนกและห้องเรียนทั้งหมด</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>โรงเรียน</th>
                            <th>ตึก/อาคาร</th>
                            <th>ชั้นเรียน</th>
                            <th>ชื่อ/เลขห้อง</th>
                            <th>ชื่อแผนก/ห้อง</th>
                            <th>ประเภท</th>
                            <th>ผู้รับผิดชอบ</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($departments_list as $department): ?>
                        <tr>
                            <td><?php echo $department['school']; ?></td>
                            <td><?php echo $department['building']; ?></td>
                            <td><?php echo $department['floor']; ?></td>
                            <td><?php echo $department['room']; ?></td>
                            <td><?php echo $department['name']; ?></td>
                            <td>
                                <span class="badge 
                                    <?php 
                                    switch($department['type']) {
                                        case 'อำนวยการ': echo 'bg-primary'; break;
                                        case 'อนุบาล': echo 'bg-success'; break;
                                        case 'ประถม': echo 'bg-info'; break;
                                        case 'มัธยม': echo 'bg-warning'; break;
                                        case 'สนับสนุน': echo 'bg-secondary'; break;
                                        default: echo 'bg-dark';
                                    }
                                    ?>">
                                    <?php echo $department['type']; ?>
                                </span>
                            </td>
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
                <h5 class="modal-title" id="departmentModalLabel">เพิ่มแผนก/ห้องเรียน</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" id="departmentForm">
                <div class="modal-body">
                    <input type="hidden" name="id" id="department_id">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">โรงเรียน *</label>
                            <select class="form-control" name="school" id="school" required onchange="updateBuildings()">
                                <option value="">เลือกโรงเรียน</option>
                                <option value="โรงเรียนวารีเชียงใหม่">โรงเรียนวารีเชียงใหม่</option>
                                <option value="โรงเรียนอนุบาลวารีเชียงใหม่">โรงเรียนอนุบาลวารีเชียงใหม่</option>
                                <option value="โรงเรียนนานาชาติวารีเชียงใหม่">โรงเรียนนานาชาติวารีเชียงใหม่</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ประเภท *</label>
                            <select class="form-control" name="type" id="type" required>
                                <option value="">เลือกประเภท</option>
                                <option value="อำนวยการ">อำนวยการ</option>
                                <option value="อนุบาล">อนุบาล</option>
                                <option value="ประถม">ประถม</option>
                                <option value="มัธยม">มัธยม</option>
                                <option value="สนับสนุน">สนับสนุน</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">ตึก/อาคาร *</label>
                            <select class="form-control" name="building" id="building" required onchange="updateFloors()">
                                <option value="">เลือกตึก/อาคาร</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">ชั้นเรียน *</label>
                            <select class="form-control" name="floor" id="floor" required>
                                <option value="">เลือกชั้นเรียน</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">ชื่อ/เลขห้อง *</label>
                            <input type="text" class="form-control" name="room" id="room" placeholder="เช่น 101, 201, ห้องธุรการ" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label">ชื่อแผนก/ห้อง *</label>
                            <input type="text" class="form-control" name="name" id="name" placeholder="เช่น ฝ่ายบริหาร, ห้องวิทยาศาสตร์, ห้องสมุด" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">ผู้รับผิดชอบ</label>
                            <input type="text" class="form-control" name="responsible_person" id="responsible_person" placeholder="ชื่อผู้รับผิดชอบ">
                        </div>
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
    document.getElementById('departmentForm').reset();
    document.getElementById('department_id').value = '';
    document.getElementById('departmentModalLabel').textContent = 'เพิ่มแผนก/ห้องเรียน';
    document.getElementById('submitBtn').name = 'add_department';
    document.getElementById('submitBtn').textContent = 'บันทึก';
    
    // ล้าง dropdown ตึกและชั้น
    document.getElementById('building').innerHTML = '<option value="">เลือกตึก/อาคาร</option>';
    document.getElementById('floor').innerHTML = '<option value="">เลือกชั้นเรียน</option>';
}

function editDepartment(department) {
    document.getElementById('department_id').value = department.id;
    document.getElementById('school').value = department.school || '';
    document.getElementById('type').value = department.type || '';
    document.getElementById('name').value = department.name || '';
    document.getElementById('room').value = department.room || '';
    document.getElementById('responsible_person').value = department.responsible_person || '';
    
    // อัปเดตตึกและชั้นตามโรงเรียน
    updateBuildings();
    
    // รอสักครู่แล้วตั้งค่าตึกและชั้น
    setTimeout(() => {
        document.getElementById('building').value = department.building || '';
        updateFloors();
        setTimeout(() => {
            document.getElementById('floor').value = department.floor || '';
        }, 100);
    }, 100);
    
    document.getElementById('departmentModalLabel').textContent = 'แก้ไขข้อมูลแผนก/ห้องเรียน';
    document.getElementById('submitBtn').name = 'edit_department';
    document.getElementById('submitBtn').textContent = 'อัพเดท';
}

// อัปเดตตึกตามโรงเรียนที่เลือก
function updateBuildings() {
    const schoolSelect = document.getElementById('school');
    const buildingSelect = document.getElementById('building');
    const floorSelect = document.getElementById('floor');
    
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
    const schoolSelect = document.getElementById('school');
    const buildingSelect = document.getElementById('building');
    const floorSelect = document.getElementById('floor');
    
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

$(document).ready(function() {
    $('#dataTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/th.json'
        },
        order: [[0, 'asc'], [1, 'asc'], [2, 'asc']], // เรียงตามโรงเรียน, ตึก, ชั้น
        columnDefs: [
            { orderable: false, targets: [7] } // ปิดการเรียงลำดับคอลัมน์จัดการ
        ]
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>