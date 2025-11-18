<?php
// equipment_check_management.php
require_once 'includes/header.php';

// ตรวจสอบสิทธิ์การเข้าถึง
if ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'teacher') {
    $_SESSION['error'] = "คุณไม่มีสิทธิ์เข้าถึงหน้านี้";
    header('Location: index.php');
    exit;
}

$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? 0;

// ฟังก์ชันจัดการฟอร์ม
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $form_action = $_POST['action'] ?? '';
    
    if ($form_action == 'update_check') {
        $check_id = intval($_POST['check_id']);
        $status = $_POST['status'];
        $notes = trim($_POST['notes']);
        
        try {
            $stmt = $db->prepare("UPDATE equipment_checks SET status = ?, notes = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$status, $notes, $check_id]);
            
            $_SESSION['success'] = "อัพเดทการตรวจสอบเรียบร้อยแล้ว";
            header('Location: equipment_check_management.php');
            exit;
            
        } catch (PDOException $e) {
            $_SESSION['error'] = "เกิดข้อผิดพลาดในการอัพเดท: " . $e->getMessage();
        }
    }
    
    if ($form_action == 'delete_check') {
        $check_id = intval($_POST['check_id']);
        
        try {
            // ลบรูปภาพที่เกี่ยวข้องก่อน (ถ้ามี)
            $stmt = $db->prepare("SELECT images FROM equipment_checks WHERE id = ?");
            $stmt->execute([$check_id]);
            $check_data = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($check_data && !empty($check_data['images'])) {
                $images = json_decode($check_data['images'], true);
                if (is_array($images)) {
                    foreach ($images as $image_path) {
                        if (file_exists($image_path)) {
                            unlink($image_path);
                        }
                    }
                }
            }
            
            // ลบข้อมูลการตรวจสอบ
            $stmt = $db->prepare("DELETE FROM equipment_checks WHERE id = ?");
            $stmt->execute([$check_id]);
            
            $_SESSION['success'] = "ลบการตรวจสอบเรียบร้อยแล้ว";
            header('Location: equipment_check_management.php');
            exit;
            
        } catch (PDOException $e) {
            $_SESSION['error'] = "เกิดข้อผิดพลาดในการลบ: " . $e->getMessage();
        }
    }
}

// ฟังก์ชันลบข้อมูล
if ($action == 'delete' && $id > 0) {
    try {
        // ลบรูปภาพที่เกี่ยวข้องก่อน (ถ้ามี)
        $stmt = $db->prepare("SELECT images FROM equipment_checks WHERE id = ?");
        $stmt->execute([$id]);
        $check_data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($check_data && !empty($check_data['images'])) {
            $images = json_decode($check_data['images'], true);
            if (is_array($images)) {
                foreach ($images as $image_path) {
                    if (file_exists($image_path)) {
                        unlink($image_path);
                    }
                }
            }
        }
        
        $stmt = $db->prepare("DELETE FROM equipment_checks WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['success'] = "ลบการตรวจสอบเรียบร้อยแล้ว";
        header('Location: equipment_check_management.php');
        exit;
    } catch (PDOException $e) {
        $_SESSION['error'] = "เกิดข้อผิดพลาด: " . $e->getMessage();
    }
}

// ดึงข้อมูลสำหรับแก้ไข
$check_data = [];
if ($action == 'edit' && $id > 0) {
    $stmt = $db->prepare("
        SELECT ec.*, eqc.equipment_code, eq.equipment_name, 
               eqc.school_name, eqc.building_name, eqc.floor_level, eqc.room_number, eqc.room_name
        FROM equipment_checks ec
        JOIN equipment_classroom eqc ON ec.equipment_classroom_id = eqc.id
        LEFT JOIN equipment eq ON eqc.equipment_code = eq.equipment_code
        WHERE ec.id = ?
    ");
    $stmt->execute([$id]);
    $check_data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$check_data) {
        $_SESSION['error'] = "ไม่พบข้อมูลการตรวจสอบ";
        header('Location: equipment_check_management.php');
        exit;
    }
}

// ดึงรายการทั้งหมด
$search = $_GET['search'] ?? '';
$status_filter = $_GET['status_filter'] ?? '';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';
$school_filter = $_GET['school_filter'] ?? '';

// Query หลักสำหรับดึงรายการ
$query = "
    SELECT 
        ec.*,
        eqc.equipment_code,
        eq.equipment_name,
        eqc.school_name,
        eqc.building_name,
        eqc.floor_level,
        eqc.room_number,
        eqc.room_name
    FROM equipment_checks ec
    JOIN equipment_classroom eqc ON ec.equipment_classroom_id = eqc.id
    LEFT JOIN equipment eq ON eqc.equipment_code = eq.equipment_code
    WHERE 1=1
";

$params = [];

if (!empty($search)) {
    $query .= " AND (eqc.equipment_code LIKE ? OR eq.equipment_name LIKE ? OR ec.checked_by LIKE ? OR eqc.room_number LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($status_filter)) {
    $query .= " AND ec.status = ?";
    $params[] = $status_filter;
}

if (!empty($date_from)) {
    $query .= " AND DATE(ec.check_date) >= ?";
    $params[] = $date_from;
}

if (!empty($date_to)) {
    $query .= " AND DATE(ec.check_date) <= ?";
    $params[] = $date_to;
}

if (!empty($school_filter)) {
    $query .= " AND eqc.school_name = ?";
    $params[] = $school_filter;
}

$query .= " ORDER BY ec.check_date DESC, ec.id DESC";

$stmt = $db->prepare($query);
$stmt->execute($params);
$check_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ดึงรายการโรงเรียนสำหรับ dropdown
$school_query = $db->query("SELECT DISTINCT school_name FROM equipment_classroom ORDER BY school_name");
$school_list = $school_query->fetchAll(PDO::FETCH_COLUMN);

// สถิติสรุป
$summary_query = "
    SELECT 
        COUNT(*) as total_checks,
        SUM(CASE WHEN status = 'ปกติ' THEN 1 ELSE 0 END) as normal_count,
        SUM(CASE WHEN status = 'ชำรุด' THEN 1 ELSE 0 END) as damaged_count,
        SUM(CASE WHEN status = 'ซ่อมแซม' THEN 1 ELSE 0 END) as repair_count,
        SUM(CASE WHEN status = 'สูญหาย' THEN 1 ELSE 0 END) as lost_count
    FROM equipment_checks
    WHERE 1=1
";

$summary_params = [];

if (!empty($date_from)) {
    $summary_query .= " AND DATE(check_date) >= ?";
    $summary_params[] = $date_from;
}

if (!empty($date_to)) {
    $summary_query .= " AND DATE(check_date) <= ?";
    $summary_params[] = $date_to;
}

$summary_stmt = $db->prepare($summary_query);
$summary_stmt->execute($summary_params);
$summary_stats = $summary_stmt->fetch(PDO::FETCH_ASSOC);
?>

<!-- Navbar -->
<?php include 'includes/navbar.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">จัดการการตรวจสอบครุภัณฑ์</h1>
                <div class="btn-group">
                    <a href="equipment_check.php?action=dashboard" class="btn btn-outline-primary">
                        <i class="fas fa-clipboard-check"></i> ตรวจสอบครุภัณฑ์
                    </a>
                    <a href="equipment_classroom.php" class="btn btn-outline-success">
                        <i class="fas fa-list"></i> จัดการครุภัณฑ์
                    </a>
                </div>
            </div>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- สถิติสรุป -->
            <div class="row mb-4">
                <div class="col-xl-2 col-md-4 col-6">
                    <div class="card bg-primary text-white mb-2">
                        <div class="card-body py-2">
                            <div class="text-center">
                                <div class="fs-5 fw-bold"><?php echo $summary_stats['total_checks']; ?></div>
                                <small>ทั้งหมด</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-6">
                    <div class="card bg-success text-white mb-2">
                        <div class="card-body py-2">
                            <div class="text-center">
                                <div class="fs-5 fw-bold"><?php echo $summary_stats['normal_count']; ?></div>
                                <small>ปกติ</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-6">
                    <div class="card bg-warning text-white mb-2">
                        <div class="card-body py-2">
                            <div class="text-center">
                                <div class="fs-5 fw-bold"><?php echo $summary_stats['damaged_count']; ?></div>
                                <small>ชำรุด</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-6">
                    <div class="card bg-info text-white mb-2">
                        <div class="card-body py-2">
                            <div class="text-center">
                                <div class="fs-5 fw-bold"><?php echo $summary_stats['repair_count']; ?></div>
                                <small>ซ่อมแซม</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-6">
                    <div class="card bg-danger text-white mb-2">
                        <div class="card-body py-2">
                            <div class="text-center">
                                <div class="fs-5 fw-bold"><?php echo $summary_stats['lost_count']; ?></div>
                                <small>สูญหาย</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-6">
                    <div class="card bg-secondary text-white mb-2">
                        <div class="card-body py-2">
                            <div class="text-center">
                                <?php
                                $total = $summary_stats['total_checks'];
                                $normal = $summary_stats['normal_count'];
                                $percentage = $total > 0 ? round(($normal / $total) * 100, 1) : 0;
                                ?>
                                <div class="fs-5 fw-bold"><?php echo $percentage; ?>%</div>
                                <small>อัตราปกติ</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-filter"></i> ตัวกรองข้อมูล
                    </h6>
                </div>
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">ค้นหา</label>
                            <input type="text" class="form-control" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="รหัส, ชื่อครุภัณฑ์, ผู้ตรวจสอบ...">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">สถานะ</label>
                            <select class="form-control" name="status_filter">
                                <option value="">ทั้งหมด</option>
                                <option value="ปกติ" <?php echo $status_filter == 'ปกติ' ? 'selected' : ''; ?>>ปกติ</option>
                                <option value="ชำรุด" <?php echo $status_filter == 'ชำรุด' ? 'selected' : ''; ?>>ชำรุด</option>
                                <option value="ซ่อมแซม" <?php echo $status_filter == 'ซ่อมแซม' ? 'selected' : ''; ?>>ซ่อมแซม</option>
                                <option value="สูญหาย" <?php echo $status_filter == 'สูญหาย' ? 'selected' : ''; ?>>สูญหาย</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">วันที่เริ่ม</label>
                            <input type="date" class="form-control" name="date_from" value="<?php echo htmlspecialchars($date_from); ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">วันที่สิ้นสุด</label>
                            <input type="date" class="form-control" name="date_to" value="<?php echo htmlspecialchars($date_to); ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">โรงเรียน</label>
                            <select class="form-control" name="school_filter">
                                <option value="">ทั้งหมด</option>
                                <?php foreach($school_list as $school): ?>
                                    <option value="<?php echo htmlspecialchars($school); ?>" <?php echo $school_filter == $school ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($school); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- แสดงรายการ -->
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">รายการการตรวจสอบครุภัณฑ์</h6>
                    <div class="btn-group">
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="exportToExcel()">
                            <i class="fas fa-file-excel"></i> Export
                        </button>
                        <button type="button" class="btn btn-outline-success btn-sm" onclick="printTable()">
                            <i class="fas fa-print"></i> พิมพ์
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="checkTable" width="100%" cellspacing="0">
                            <thead class="table-light">
                                <tr>
                                    <th width="120">วันที่ตรวจสอบ</th>
                                    <th width="100">รหัสครุภัณฑ์</th>
                                    <th>ชื่อครุภัณฑ์</th>
                                    <th width="150">ผู้ตรวจสอบ</th>
                                    <th width="100">สถานะ</th>
                                    <th width="150">โรงเรียน/อาคาร</th>
                                    <th width="80">ห้อง</th>
                                    <th width="120">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($check_list) > 0): ?>
                                    <?php foreach($check_list as $item): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-bold"><?php echo date('d/m/Y', strtotime($item['check_date'])); ?></div>
                                            <small class="text-muted"><?php echo date('H:i', strtotime($item['check_date'])); ?></small>
                                        </td>
                                        <td class="fw-bold text-primary"><?php echo htmlspecialchars($item['equipment_code']); ?></td>
                                        <td>
                                            <div><?php echo htmlspecialchars($item['equipment_name'] ?? '-'); ?></div>
                                            <?php if (!empty($item['notes'])): ?>
                                                <small class="text-muted"><?php echo mb_substr(htmlspecialchars($item['notes']), 0, 50); ?>...</small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="fw-bold"><?php echo htmlspecialchars($item['checked_by']); ?></div>
                                            <small class="text-muted"><?php echo date('d/m/Y H:i', strtotime($item['created_at'])); ?></small>
                                        </td>
                                        <td>
                                            <span class="badge <?php echo getStatusBadgeClass($item['status']); ?>">
                                                <?php echo $item['status']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div><?php echo htmlspecialchars($item['school_name']); ?></div>
                                            <small class="text-muted"><?php echo htmlspecialchars($item['building_name']); ?></small>
                                        </td>
                                        <td>
                                            <div class="fw-bold"><?php echo htmlspecialchars($item['room_number']); ?></div>
                                            <?php if (!empty($item['room_name'])): ?>
                                                <small class="text-muted"><?php echo htmlspecialchars($item['room_name']); ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-info" title="ดูรายละเอียด" 
                                                        onclick="viewCheckDetails(<?php echo $item['id']; ?>)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button type="button" class="btn btn-warning" title="แก้ไข" 
                                                        onclick="editCheck(<?php echo $item['id']; ?>)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <a href="equipment_check_management.php?action=delete&id=<?php echo $item['id']; ?>" 
                                                   class="btn btn-danger" title="ลบ"
                                                   onclick="return confirm('คุณแน่ใจหรือไม่ที่จะลบการตรวจสอบนี้?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                            ไม่พบข้อมูลการตรวจสอบ
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if (count($check_list) > 0): ?>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted">
                            แสดง <?php echo count($check_list); ?> รายการ
                        </div>
                        <nav>
                            <ul class="pagination pagination-sm mb-0">
                                <li class="page-item disabled">
                                    <a class="page-link" href="#">ก่อนหน้า</a>
                                </li>
                                <li class="page-item active">
                                    <a class="page-link" href="#">1</a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="#">ถัดไป</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal สำหรับดูรายละเอียด -->
    <div class="modal fade" id="viewCheckModal" tabindex="-1" aria-labelledby="viewCheckModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewCheckModalLabel">รายละเอียดการตรวจสอบ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="viewCheckContent">
                    <!-- เนื้อหาจะถูกโหลดโดย JavaScript -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal สำหรับแก้ไข -->
    <div class="modal fade" id="editCheckModal" tabindex="-1" aria-labelledby="editCheckModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" id="editCheckForm">
                    <input type="hidden" name="action" value="update_check">
                    <input type="hidden" name="check_id" id="edit_check_id">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editCheckModalLabel">แก้ไขการตรวจสอบ</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">สถานะ</label>
                            <select class="form-control" name="status" id="edit_status" required>
                                <option value="ปกติ">ปกติ</option>
                                <option value="ชำรุด">ชำรุด</option>
                                <option value="ซ่อมแซม">ซ่อมแซม</option>
                                <option value="สูญหาย">สูญหาย</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">หมายเหตุ</label>
                            <textarea class="form-control" name="notes" id="edit_notes" rows="4"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                        <button type="submit" class="btn btn-primary">อัพเดท</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</div>

<style>
.card {
    transition: transform 0.2s;
}
.card:hover {
    transform: translateY(-2px);
}
.status-badge {
    font-size: 0.8em;
    padding: 0.4em 0.8em;
}
.table th {
    border-top: none;
    font-weight: 600;
}
</style>

<script>
// ฟังก์ชันดูรายละเอียดการตรวจสอบ
function viewCheckDetails(checkId) {
    // ส่งคำขอ AJAX เพื่อดึงรายละเอียด
    fetch(`ajax_get_check_details.php?id=${checkId}`)
        .then(response => response.text())
        .then(data => {
            document.getElementById('viewCheckContent').innerHTML = data;
            const modal = new bootstrap.Modal(document.getElementById('viewCheckModal'));
            modal.show();
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('viewCheckContent').innerHTML = `
                <div class="alert alert-danger">
                    เกิดข้อผิดพลาดในการโหลดข้อมูล
                </div>
            `;
            const modal = new bootstrap.Modal(document.getElementById('viewCheckModal'));
            modal.show();
        });
}

// ฟังก์ชันแก้ไขการตรวจสอบ
function editCheck(checkId) {
    // ส่งคำขอ AJAX เพื่อดึงข้อมูล
    fetch(`ajax_get_check_details.php?id=${checkId}&action=edit`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('edit_check_id').value = data.check.id;
                document.getElementById('edit_status').value = data.check.status;
                document.getElementById('edit_notes').value = data.check.notes || '';
                
                const modal = new bootstrap.Modal(document.getElementById('editCheckModal'));
                modal.show();
            } else {
                alert('ไม่พบข้อมูลการตรวจสอบ');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('เกิดข้อผิดพลาดในการโหลดข้อมูล');
        });
}

// ฟังก์ชัน Export ไป Excel
function exportToExcel() {
    const table = document.getElementById('checkTable');
    let csv = [];
    
    // Add headers
    const headers = [];
    for (let i = 0; i < table.rows[0].cells.length - 1; i++) { // Exclude action column
        headers.push(table.rows[0].cells[i].textContent.trim());
    }
    csv.push(headers.join(','));
    
    // Add data rows
    for (let i = 1; i < table.rows.length; i++) {
        const row = table.rows[i];
        const rowData = [];
        for (let j = 0; j < row.cells.length - 1; j++) { // Exclude action column
            let cellText = row.cells[j].textContent.trim();
            cellText = cellText.replace(/,/g, ' ').replace(/\n/g, ' ').replace(/\s+/g, ' ');
            rowData.push(cellText);
        }
        csv.push(rowData.join(','));
    }
    
    const csvString = csv.join('\n');
    const blob = new Blob(['\uFEFF' + csvString], { type: 'text/csv;charset=utf-8;' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `รายงานการตรวจสอบ_${new Date().toISOString().split('T')[0]}.csv`;
    a.click();
    window.URL.revokeObjectURL(url);
}

// ฟังก์ชันพิมพ์ตาราง
function printTable() {
    const printWindow = window.open('', '_blank');
    const table = document.getElementById('checkTable').cloneNode(true);
    
    // Remove action column
    const rows = table.getElementsByTagName('tr');
    for (let i = 0; i < rows.length; i++) {
        if (rows[i].cells.length > 0) {
            rows[i].deleteCell(-1); // Remove last cell (action column)
        }
    }
    
    printWindow.document.write(`
        <html>
            <head>
                <title>พิมพ์รายการตรวจสอบครุภัณฑ์</title>
                <style>
                    body { font-family: 'Sarabun', sans-serif; margin: 20px; }
                    table { width: 100%; border-collapse: collapse; font-size: 12px; }
                    th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
                    th { background-color: #f8f9fa; font-weight: bold; }
                    .badge { padding: 2px 6px; border-radius: 3px; font-size: 10px; }
                    @media print {
                        body { margin: 0; }
                        table { font-size: 10px; }
                    }
                </style>
            </head>
            <body>
                <h2 style="text-align: center;">รายการตรวจสอบครุภัณฑ์</h2>
                <p style="text-align: right;">พิมพ์เมื่อ: ${new Date().toLocaleString('th-TH')}</p>
                ${table.outerHTML}
            </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
}

// Event Listeners
document.addEventListener('DOMContentLoaded', function() {
    // Add any initialization code here
});
</script>

<?php
// Helper function สำหรับสีสถานะ
function getStatusBadgeClass($status) {
    switch($status) {
        case 'ปกติ': return 'bg-success';
        case 'ชำรุด': return 'bg-warning';
        case 'ซ่อมแซม': return 'bg-info';
        case 'สูญหาย': return 'bg-danger';
        default: return 'bg-secondary';
    }
}
?>