<?php
require_once 'includes/header.php';

// ตั้งค่าปีการศึกษาเริ่มต้น
$current_year = date('Y') + 543; // แปลงเป็น พ.ศ.
$selected_year = isset($_GET['year']) ? $_GET['year'] : $current_year;
$selected_school = isset($_GET['school']) ? $_GET['school'] : '';
$selected_building = isset($_GET['building']) ? $_GET['building'] : '';

// CRUD Operations for Floor Plans
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $id = isset($_GET['id']) ? $_GET['id'] : null;
    
    if ($action == 'delete_plan' && $id) {
        // Delete floor plan image
        $stmt = $db->prepare("SELECT plan_image FROM building_floor_plans WHERE id = ?");
        $stmt->execute([$id]);
        $plan = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($plan && $plan['plan_image']) {
            $image_path = 'uploads/floor_plans/' . $plan['plan_image'];
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }
        
        $stmt = $db->prepare("DELETE FROM building_floor_plans WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['success'] = "ลบแผนผังตารางห้องเรียบร้อยแล้ว";
        
        header("Location: departments.php?year=" . $selected_year . "&school=" . $selected_school . "&building=" . $selected_building);
        exit();
    }
}

if ($_POST) {
    if (isset($_POST['add_floor_plan'])) {
        // Handle image upload
        $image_name = '';
        if (isset($_FILES['plan_image']) && $_FILES['plan_image']['error'] == 0) {
            $image_name = uploadFloorPlanImage($_FILES['plan_image'], $_POST['school'], $_POST['building'], $_POST['academic_year']);
        }
        
        if ($image_name) {
            // Check if plan already exists
            $check_stmt = $db->prepare("SELECT id FROM building_floor_plans WHERE school = ? AND building = ? AND academic_year = ?");
            $check_stmt->execute([$_POST['school'], $_POST['building'], $_POST['academic_year']]);
            $existing = $check_stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($existing) {
                // Update existing plan
                $old_stmt = $db->prepare("SELECT plan_image FROM building_floor_plans WHERE id = ?");
                $old_stmt->execute([$existing['id']]);
                $old_plan = $old_stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($old_plan && $old_plan['plan_image']) {
                    $old_image_path = 'uploads/floor_plans/' . $old_plan['plan_image'];
                    if (file_exists($old_image_path)) {
                        unlink($old_image_path);
                    }
                }
                
                $stmt = $db->prepare("UPDATE building_floor_plans SET plan_image = ?, description = ?, updated_at = NOW() WHERE id = ?");
                $stmt->execute([$image_name, $_POST['description'], $existing['id']]);
                $_SESSION['success'] = "อัพเดทแผนผังตารางห้องเรียบร้อยแล้ว";
            } else {
                // Insert new plan
                $stmt = $db->prepare("INSERT INTO building_floor_plans (school, building, academic_year, plan_image, description) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([
                    $_POST['school'],
                    $_POST['building'],
                    $_POST['academic_year'],
                    $image_name,
                    $_POST['description']
                ]);
                $_SESSION['success'] = "เพิ่มแผนผังตารางห้องเรียบร้อยแล้ว";
            }
        } else {
            $_SESSION['error'] = "กรุณาเลือกรูปภาพแผนผัง";
        }
        
        header("Location: departments.php?year=" . $selected_year . "&school=" . $selected_school . "&building=" . $selected_building);
        exit();
    }
}

// Function to handle floor plan image upload
function uploadFloorPlanImage($file, $school, $building, $year) {
    $upload_dir = 'uploads/floor_plans/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    // School abbreviations
    $school_abbr = [
        'โรงเรียนวารีเชียงใหม่' => 'VCS',
        'โรงเรียนอนุบาลวารีเชียงใหม่' => 'VKS',
        'โรงเรียนนานาชาติวารีเชียงใหม่' => 'VCIS'
    ];
    
    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    
    // Get school abbreviation
    $school_code = isset($school_abbr[$school]) ? $school_abbr[$school] : 'SCH';
    
    // Clean building name (remove special characters and spaces)
    $building_clean = preg_replace('/[^a-zA-Z0-9ก-๙]/', '', $building);
    
    // Format: YEAR_SCHOOLCODE_BUILDING.extension
    // Example: 2568_VCS_ตึก1.jpg
    $file_name = $year . '_' . $school_code . '_' . $building_clean . '.' . $file_extension;
    $file_path = $upload_dir . $file_name;
    
    // Check file type
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array(strtolower($file_extension), $allowed_types)) {
        $_SESSION['error'] = "ประเภทไฟล์ไม่ถูกต้อง อนุญาตเฉพาะ JPG, JPEG, PNG, GIF";
        return '';
    }
    
    // Check file size (max 10MB)
    if ($file['size'] > 10 * 1024 * 1024) {
        $_SESSION['error'] = "ขนาดไฟล์ใหญ่เกินไป ต้องไม่เกิน 10MB";
        return '';
    }
    
    // Delete old file if exists
    if (file_exists($file_path)) {
        unlink($file_path);
    }
    
    if (move_uploaded_file($file['tmp_name'], $file_path)) {
        return $file_name;
    } else {
        $_SESSION['error'] = "เกิดข้อผิดพลาดในการอัพโหลดไฟล์";
        return '';
    }
}

// Get filter options from building_floor_plans table
$schools_query = "SELECT DISTINCT school FROM building_floor_plans ORDER BY school";
$schools_result = $db->query($schools_query);
$schools_list = $schools_result ? $schools_result->fetchAll(PDO::FETCH_COLUMN) : [];

// If no data in building_floor_plans, use default schools
if (empty($schools_list)) {
    $schools_list = [
        'โรงเรียนวารีเชียงใหม่',
        'โรงเรียนอนุบาลวารีเชียงใหม่',
        'โรงเรียนนานาชาติวารีเชียงใหม่'
    ];
}

$buildings_list = [];
if ($selected_school) {
    $buildings_query = "SELECT DISTINCT building FROM building_floor_plans WHERE school = ? ORDER BY building";
    $buildings_stmt = $db->prepare($buildings_query);
    $buildings_stmt->execute([$selected_school]);
    $buildings_list = $buildings_stmt->fetchAll(PDO::FETCH_COLUMN);
}

// Get unique academic years
$years_query = "SELECT DISTINCT academic_year FROM building_floor_plans ORDER BY academic_year DESC";
$years_result = $db->query($years_query);
$years_list = $years_result ? $years_result->fetchAll(PDO::FETCH_COLUMN) : [];

if (empty($years_list)) {
    $years_list = [$current_year];
}

// Get floor plan for selected filters
$floor_plan = null;
if ($selected_school && $selected_building && $selected_year) {
    $plan_stmt = $db->prepare("SELECT * FROM building_floor_plans WHERE school = ? AND building = ? AND academic_year = ?");
    $plan_stmt->execute([$selected_school, $selected_building, $selected_year]);
    $floor_plan = $plan_stmt->fetch(PDO::FETCH_ASSOC);
}

// Floor plan is the main content, no need for building_images table
?>

<?php include 'includes/sidebar.php'; ?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <?php include 'includes/navbar.php'; ?>

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">แผนผังตารางห้อง</h1>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#floorPlanModal" onclick="clearPlanForm()">
                <i class="fas fa-plus"></i> เพิ่มแผนผัง
            </button>
        </div>
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

    <!-- Filter Section -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h5 class="m-0 font-weight-bold">ตัวกรองข้อมูล</h5>
        </div>
        <div class="card-body">
            <form method="GET" id="filterForm">
                <div class="row g-3">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">โรงเรียน *</label>
                        <select class="form-control" name="school" id="schoolFilter" onchange="this.form.submit()" required>
                            <option value="">เลือกโรงเรียน</option>
                            <option value="โรงเรียนวารีเชียงใหม่" <?php echo $selected_school == 'โรงเรียนวารีเชียงใหม่' ? 'selected' : ''; ?>>โรงเรียนวารีเชียงใหม่</option>
                            <option value="โรงเรียนอนุบาลวารีเชียงใหม่" <?php echo $selected_school == 'โรงเรียนอนุบาลวารีเชียงใหม่' ? 'selected' : ''; ?>>โรงเรียนอนุบาลวารีเชียงใหม่</option>
                            <option value="โรงเรียนนานาชาติวารีเชียงใหม่" <?php echo $selected_school == 'โรงเรียนนานาชาติวารีเชียงใหม่' ? 'selected' : ''; ?>>โรงเรียนนานาชาติวารีเชียงใหม่</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">ตึก/อาคาร *</label>
                        <select class="form-control" name="building" id="buildingFilter" onchange="this.form.submit()" required <?php echo empty($selected_school) ? 'disabled' : ''; ?>>
                            <option value="">เลือกตึก/อาคาร</option>
                            <?php if (!empty($buildings_list)): ?>
                                <?php foreach($buildings_list as $building): ?>
                                    <option value="<?php echo $building; ?>" <?php echo $selected_building == $building ? 'selected' : ''; ?>>
                                        <?php echo $building; ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <?php if (empty($selected_school)): ?>
                            <div class="form-text text-muted">กรุณาเลือกโรงเรียนก่อน</div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">ปีการศึกษา *</label>
                        <select class="form-control" name="year" onchange="this.form.submit()" required>
                            <option value="">เลือกปีการศึกษา</option>
                            <?php 
                            // Show last 5 years
                            for ($y = $current_year; $y >= $current_year - 5; $y--): 
                            ?>
                                <option value="<?php echo $y; ?>" <?php echo $selected_year == $y ? 'selected' : ''; ?>>
                                    ปีการศึกษา <?php echo $y; ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Display Floor Plan -->
    <div class="card shadow mb-4" id="floorPlanDisplay">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h5 class="m-0 font-weight-bold">
                <?php 
                $title = "แผนผังตารางห้อง";
                if ($selected_school) $title .= " - " . $selected_school;
                if ($selected_building) $title .= " - " . $selected_building;
                if ($selected_year) $title .= " - ปีการศึกษา " . $selected_year;
                echo $title;
                ?>
            </h5>
            <div class="d-flex gap-2 align-items-center">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="autoRefresh" checked>
                    <label class="form-check-label" for="autoRefresh">อัพเดทแบบเรียลไทม์</label>
                </div>
                <span class="badge bg-info" id="lastUpdate">
                    <i class="fas fa-clock"></i> <span id="updateTime">--:--:--</span>
                </span>
            </div>
        </div>
        <div class="card-body" id="planContent">
            <?php if (!$selected_school || !$selected_building || !$selected_year): ?>
                <div class="text-center py-5">
                    <i class="fas fa-filter fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">กรุณาเลือกตัวกรอง</h5>
                    <p class="text-muted">เลือกโรงเรียน, ตึก/อาคาร และปีการศึกษาเพื่อดูแผนผัง</p>
                </div>
            <?php elseif ($floor_plan): ?>
                <div class="row">
                    <div class="col-12">
                        <div class="position-relative">
                            <img src="uploads/floor_plans/<?php echo $floor_plan['plan_image']; ?>" 
                                 class="img-fluid rounded shadow" 
                                 alt="แผนผังตารางห้อง"
                                 style="max-width: 100%; height: auto;">
                            
                            <div class="position-absolute top-0 end-0 m-3">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-warning btn-sm" 
                                            data-bs-toggle="modal" data-bs-target="#floorPlanModal" 
                                            onclick='editPlan(<?php echo json_encode($floor_plan); ?>)'>
                                        <i class="fas fa-edit"></i> แก้ไข
                                    </button>
                                    <a href="departments.php?action=delete_plan&id=<?php echo $floor_plan['id']; ?>&year=<?php echo $selected_year; ?>&school=<?php echo $selected_school; ?>&building=<?php echo $selected_building; ?>" 
                                       class="btn btn-danger btn-sm" 
                                       onclick="return confirm('คุณแน่ใจหรือไม่ที่จะลบแผนผังนี้?')">
                                        <i class="fas fa-trash"></i> ลบ
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <?php if (!empty($floor_plan['description'])): ?>
                            <div class="alert alert-info mt-3">
                                <i class="fas fa-info-circle"></i> <strong>หมายเหตุ:</strong> <?php echo nl2br(htmlspecialchars($floor_plan['description'])); ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="text-muted small mt-2">
                            <i class="fas fa-calendar"></i> อัพเดทล่าสุด: <?php echo date('d/m/Y H:i:s', strtotime($floor_plan['updated_at'])); ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-map fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">ยังไม่มีแผนผังสำหรับตัวกรองนี้</h5>
                    <p class="text-muted">คลิกปุ่ม "เพิ่มแผนผัง" เพื่อเพิ่มแผนผังตารางห้อง</p>
                    <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#floorPlanModal" onclick="clearPlanForm()">
                        <i class="fas fa-plus"></i> เพิ่มแผนผังตอนนี้
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<!-- Floor Plan Modal -->
<div class="modal fade" id="floorPlanModal" tabindex="-1" aria-labelledby="floorPlanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="floorPlanModalLabel">เพิ่มแผนผังตารางห้อง</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" id="floorPlanForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="plan_id" id="plan_id">
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">โรงเรียน *</label>
                            <select class="form-control" name="school" id="plan_school" required>
                                <option value="">เลือกโรงเรียน</option>
                                <option value="โรงเรียนวารีเชียงใหม่">โรงเรียนวารีเชียงใหม่</option>
                                <option value="โรงเรียนอนุบาลวารีเชียงใหม่">โรงเรียนอนุบาลวารีเชียงใหม่</option>
                                <option value="โรงเรียนนานาชาติวารีเชียงใหม่">โรงเรียนนานาชาติวารีเชียงใหม่</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">ตึก/อาคาร *</label>
                            <select class="form-control" name="building" id="plan_building" required>
                                <option value="">เลือกตึก/อาคาร</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">ปีการศึกษา *</label>
                            <select class="form-control" name="academic_year" id="plan_year" required>
                                <option value="">เลือกปีการศึกษา</option>
                                <?php 
                                for ($y = $current_year; $y >= $current_year - 5; $y--): ?>
                                    <option value="<?php echo $y; ?>"><?php echo $y; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label">รูปภาพแผนผัง *</label>
                            <input type="file" class="form-control" name="plan_image" id="plan_image" accept="image/*" required>
                            <div class="form-text">รองรับไฟล์ JPG, JPEG, PNG, GIF ขนาดไม่เกิน 10MB</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label">หมายเหตุ/คำอธิบาย</label>
                            <textarea class="form-control" name="description" id="plan_description" rows="3" placeholder="ระบุรายละเอียดเพิ่มเติม (ถ้ามี)"></textarea>
                        </div>
                    </div>

                    <div class="row" id="planImagePreviewRow" style="display: none;">
                        <div class="col-12">
                            <label class="form-label">ภาพตัวอย่าง</label>
                            <div id="planImagePreview" class="text-center border rounded p-3"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                    <button type="submit" name="add_floor_plan" id="planSubmitBtn" class="btn btn-primary">บันทึก</button>
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
            { name: "อาคาร1-อำนวยการ", floors: ["ชั้น 1", "ชั้น 2"] },
            { name: "อาคาร3-ประถม", floors: ["ชั้น 1", "ชั้น 2", "ชั้น 3", "ชั้น 4"] },
            { name: "อาคาร4-ประถม", floors: ["ชั้น 1", "ชั้น 2", "ชั้น 3"] },
            { name: "อาคาร4-มัธยม", floors: ["ชั้น 3", "ชั้น 4", "ชั้น 5"] },
            { name: "อาคาร5-อนุบาล", floors: ["ชั้น 1", "ชั้น 2"] },
            { name: "อาคาร6-??", floors: ["ชั้น 1", "ชั้น 2"] },
            { name: "อาคาร7-มัธยม", floors: ["ชั้น 1", "ชั้น 2", "ชั้น 3", "ชั้น 4", "ชั้น 5", "ชั้น 6", "ชั้น 7"] },
            { name: "อาคาร 10", floors: ["ชั้น 1", "ชั้น 2"] }
        ]
    },
    "โรงเรียนอนุบาลวารีเชียงใหม่": {
        buildings: [
            { name: "อาคาร 1-อำนวยการ", floors: ["ชั้น 1", "ชั้น 2"] },
            { name: "อาคาร-อนุบาล", floors: ["ชั้น 1", "ชั้น 2"] }
        ]
    },
    "โรงเรียนนานาชาติวารีเชียงใหม่": {
        buildings: [
            { name: "อาคาร 8", floors: ["ชั้น 1", "ชั้น 2", "ชั้น 3", "ชั้น 4"] },
            { name: "อาคาร 9", floors: ["ชั้น 1", "ชั้น 2", "ชั้น 3"] },
            { name: "อาคาร 10", floors: ["ชั้น 1", "ชั้น 2"] }
        ]
    }
};


// Update building dropdown based on selected school
document.getElementById('plan_school').addEventListener('change', function() {
    const school = this.value;
    const buildingSelect = document.getElementById('plan_building');
    
    buildingSelect.innerHTML = '<option value="">เลือกตึก/อาคาร</option>';
    
    if (school && schoolData[school]) {
        schoolData[school].buildings.forEach(building => {
            const option = document.createElement('option');
            option.value = building.name;
            option.textContent = building.name;
            buildingSelect.appendChild(option);
        });
    }
});

// Update building filter when school changes
document.getElementById('schoolFilter').addEventListener('change', function() {
    const school = this.value;
    const buildingFilter = document.getElementById('buildingFilter');
    
    buildingFilter.innerHTML = '<option value="">เลือกตึก/อาคาร</option>';
    
    if (school && schoolData[school]) {
        schoolData[school].buildings.forEach(building => {
            const option = document.createElement('option');
            option.value = building.name;
            option.textContent = building.name;
            buildingFilter.appendChild(option);
        });
    }
});

// Image preview
document.getElementById('plan_image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const previewRow = document.getElementById('planImagePreviewRow');
    const preview = document.getElementById('planImagePreview');
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `<img src="${e.target.result}" class="img-fluid rounded shadow" style="max-height: 300px;" alt="Preview">`;
            previewRow.style.display = 'block';
        }
        reader.readAsDataURL(file);
    } else {
        previewRow.style.display = 'none';
    }
});

function clearPlanForm() {
    document.getElementById('floorPlanForm').reset();
    document.getElementById('plan_id').value = '';
    document.getElementById('floorPlanModalLabel').textContent = 'เพิ่มแผนผังตารางห้อง';
    document.getElementById('planSubmitBtn').textContent = 'บันทึก';
    document.getElementById('planImagePreviewRow').style.display = 'none';
    document.getElementById('plan_image').required = true;
    
    // Set default values from filter
    const selectedSchool = document.getElementById('schoolFilter').value;
    const selectedBuilding = document.getElementById('buildingFilter').value;
    const selectedYear = document.querySelector('select[name="year"]').value;
    
    if (selectedSchool) {
        document.getElementById('plan_school').value = selectedSchool;
        document.getElementById('plan_school').dispatchEvent(new Event('change'));
        
        setTimeout(() => {
            if (selectedBuilding) {
                document.getElementById('plan_building').value = selectedBuilding;
            }
        }, 100);
    }
    
    if (selectedYear) {
        document.getElementById('plan_year').value = selectedYear;
    }
}

function editPlan(plan) {
    document.getElementById('plan_id').value = plan.id;
    document.getElementById('plan_school').value = plan.school;
    document.getElementById('plan_school').dispatchEvent(new Event('change'));
    
    setTimeout(() => {
        document.getElementById('plan_building').value = plan.building;
    }, 100);
    
    document.getElementById('plan_year').value = plan.academic_year;
    document.getElementById('plan_description').value = plan.description || '';
    
    // Show existing image
    const previewRow = document.getElementById('planImagePreviewRow');
    const preview = document.getElementById('planImagePreview');
    if (plan.plan_image) {
        preview.innerHTML = `<img src="uploads/floor_plans/${plan.plan_image}" class="img-fluid rounded shadow" style="max-height: 300px;" alt="Current Image">
                            <div class="text-muted small mt-2">ภาพปัจจุบัน (เลือกไฟล์ใหม่เพื่อเปลี่ยน)</div>`;
        previewRow.style.display = 'block';
    }
    
    document.getElementById('floorPlanModalLabel').textContent = 'แก้ไขแผนผังตารางห้อง';
    document.getElementById('planSubmitBtn').textContent = 'อัพเดท';
    document.getElementById('plan_image').required = false;
}

// Real-time refresh function
let lastUpdateTime = new Date();

function updateTimeDisplay() {
    const now = new Date();
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const seconds = String(now.getSeconds()).padStart(2, '0');
    document.getElementById('updateTime').textContent = `${hours}:${minutes}:${seconds}`;
}

function refreshPlanData() {
    if (!document.getElementById('autoRefresh').checked) {
        return;
    }
    
    const school = document.getElementById('schoolFilter').value;
    const building = document.getElementById('buildingFilter').value;
    const year = document.querySelector('select[name="year"]').value;
    
    if (!school || !building || !year) {
        return;
    }
    
    const currentUrl = new URL(window.location.href);
    
    fetch(currentUrl)
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const newDocument = parser.parseFromString(html, 'text/html');
            const newContent = newDocument.querySelector('#planContent');
            
            if (newContent) {
                const currentContent = document.querySelector('#planContent');
                
                // Check if content has changed
                if (currentContent.innerHTML !== newContent.innerHTML) {
                    currentContent.innerHTML = newContent.innerHTML;
                    lastUpdateTime = new Date();
                    
                    // Show update notification
                    showUpdateNotification();
                }
            }
            
            updateTimeDisplay();
        })
        .catch(error => {
            console.error('Error refreshing data:', error);
        });
}

function showUpdateNotification() {
    const notification = document.createElement('div');
    notification.className = 'alert alert-success alert-dismissible fade show position-fixed';
    notification.style.cssText = 'top: 80px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        <i class="fas fa-sync-alt"></i> ข้อมูลได้รับการอัพเดทแล้ว
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Auto refresh every 10 seconds
setInterval(refreshPlanData, 10000);

// Update time display every second
setInterval(updateTimeDisplay, 1000);

// Initialize time display
updateTimeDisplay();

// Auto-refresh toggle
document.getElementById('autoRefresh').addEventListener('change', function() {
    if (this.checked) {
        refreshPlanData();
    }
});
</script>

<style>
.position-relative {
    position: relative;
}

.position-absolute {
    position: absolute;
}

.top-0 {
    top: 0;
}

.end-0 {
    right: 0;
}

.m-3 {
    margin: 1rem;
}

#floorPlanDisplay img {
    transition: transform 0.3s ease;
}

#floorPlanDisplay img:hover {
    transform: scale(1.02);
}

.form-control:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.card {
    transition: box-shadow 0.3s ease;
}

.card:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.badge {
    font-weight: 500;
}

.alert-success {
    animation: slideInRight 0.3s ease;
}

@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.btn-group .btn {
    transition: all 0.2s ease;
}

.btn-group .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

/* Loading spinner */
.loading-spinner {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 3px solid rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    border-top-color: #fff;
    animation: spin 1s ease-in-out infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .d-flex.gap-2 {
        flex-direction: column;
        gap: 0.5rem !important;
    }
    
    .btn-group {
        width: 100%;
    }
    
    .position-absolute.top-0.end-0 {
        position: static !important;
        margin: 1rem 0 !important;
    }
}

/* Print styles */
@media print {
    .btn, .badge, .alert, .card-header, .form-check, nav, aside {
        display: none !important;
    }
    
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    
    img {
        max-width: 100% !important;
        page-break-inside: avoid;
    }
}
</style>

<?php require_once 'includes/footer.php'; ?>