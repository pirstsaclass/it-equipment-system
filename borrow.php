
<?php
// เริ่ม session และ error reporting
require_once 'includes/header.php';

// ตรวจสอบสิทธิ์การเข้าถึง
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'teacher')) {
    $_SESSION['error'] = "คุณไม่มีสิทธิ์เข้าถึงหน้านี้";
    header('Location: index.php');
    exit;
}

// CRUD Operations สำหรับการยืม-คืน
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $id = isset($_GET['id']) ? $_GET['id'] : null;
    
    if ($action == 'return' && $id) {
        try {
            // อัพเดทสถานะการคืน
            $stmt = $db->prepare("UPDATE equipment_borrow SET actual_return_date = CURDATE(), borrow_status = 'คืนแล้ว' WHERE id = ?");
            $stmt->execute([$id]);
            
            // อัพเดทสถานะอุปกรณ์เป็นว่าง
            $borrow_stmt = $db->prepare("SELECT equipment_id FROM equipment_borrow WHERE id = ?");
            $borrow_stmt->execute([$id]);
            $borrow_record = $borrow_stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($borrow_record) {
                $update_equipment = $db->prepare("UPDATE equipment SET borrow_status = 'ว่าง' WHERE id = ?");
                $update_equipment->execute([$borrow_record['equipment_id']]);
            }
            
            $_SESSION['success'] = "บันทึกการคืนอุปกรณ์เรียบร้อยแล้ว";
        } catch (PDOException $e) {
            $_SESSION['error'] = "เกิดข้อผิดพลาดในการบันทึกการคืน: " . $e->getMessage();
        }
        header("Location: borrow.php");
        exit();
    }
    
    if ($action == 'delete' && $id) {
        try {
            // ดึงข้อมูล equipment_id ก่อนลบ
            $borrow_stmt = $db->prepare("SELECT equipment_id FROM equipment_borrow WHERE id = ?");
            $borrow_stmt->execute([$id]);
            $borrow_record = $borrow_stmt->fetch(PDO::FETCH_ASSOC);
            
            // ลบบันทึกการยืม
            $stmt = $db->prepare("DELETE FROM equipment_borrow WHERE id = ?");
            $stmt->execute([$id]);
            
            // อัพเดทสถานะอุปกรณ์เป็นว่าง
            if ($borrow_record) {
                $update_equipment = $db->prepare("UPDATE equipment SET borrow_status = 'ว่าง' WHERE id = ?");
                $update_equipment->execute([$borrow_record['equipment_id']]);
            }
            
            $_SESSION['success'] = "ลบบันทึกการยืมเรียบร้อยแล้ว";
        } catch (PDOException $e) {
            $_SESSION['error'] = "เกิดข้อผิดพลาดในการลบข้อมูล: " . $e->getMessage();
        }
        header("Location: borrow.php");
        exit();
    }
}

// จัดการฟอร์มยืมอุปกรณ์
if ($_POST) {
    if (isset($_POST['add_borrow'])) {
        try {
            // ตรวจสอบว่าอุปกรณ์ว่างหรือไม่
            $check_stmt = $db->prepare("SELECT borrow_status FROM equipment WHERE id = ?");
            $check_stmt->execute([$_POST['equipment_id']]);
            $equipment_status = $check_stmt->fetchColumn();
            
            if ($equipment_status == 'ถูกยืม') {
                $_SESSION['error'] = "อุปกรณ์นี้ถูกยืมอยู่แล้ว";
                header("Location: borrow.php");
                exit();
            }
            
            // บันทึกการยืม
            $stmt = $db->prepare("INSERT INTO equipment_borrow (equipment_id, borrower_name, borrower_department, lender_name, borrow_date, expected_return_date, borrow_purpose, notes, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $_POST['equipment_id'],
                $_POST['borrower_name'],
                $_POST['borrower_department'],
                $_POST['lender_name'],
                $_POST['borrow_date'],
                $_POST['expected_return_date'],
                $_POST['borrow_purpose'],
                $_POST['notes'],
                $_SESSION['user_id']
            ]);
            
            // อัพเดทสถานะอุปกรณ์เป็นถูกยืม
            $update_equipment = $db->prepare("UPDATE equipment SET borrow_status = 'ถูกยืม' WHERE id = ?");
            $update_equipment->execute([$_POST['equipment_id']]);
            
            $_SESSION['success'] = "บันทึกการยืมอุปกรณ์เรียบร้อยแล้ว";
            header("Location: borrow.php");
            exit();
        } catch (PDOException $e) {
            $_SESSION['error'] = "เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . $e->getMessage();
        }
    }
    
    if (isset($_POST['edit_borrow'])) {
        try {
            $stmt = $db->prepare("UPDATE equipment_borrow SET borrower_name=?, borrower_department=?, lender_name=?, borrow_date=?, expected_return_date=?, borrow_purpose=?, notes=? WHERE id=?");
            $stmt->execute([
                $_POST['borrower_name'],
                $_POST['borrower_department'],
                $_POST['lender_name'],
                $_POST['borrow_date'],
                $_POST['expected_return_date'],
                $_POST['borrow_purpose'],
                $_POST['notes'],
                $_POST['borrow_id']
            ]);
            
            $_SESSION['success'] = "แก้ไขข้อมูลการยืมเรียบร้อยแล้ว";
            header("Location: borrow.php");
            exit();
        } catch (PDOException $e) {
            $_SESSION['error'] = "เกิดข้อผิดพลาด: " . $e->getMessage();
        }
    }
}

// นับจำนวนการยืมตามสถานะ
try {
    $status_counts = $db->query("
        SELECT borrow_status, COUNT(*) as count 
        FROM equipment_borrow 
        GROUP BY borrow_status
    ")->fetchAll(PDO::FETCH_KEY_PAIR);
} catch (PDOException $e) {
    $status_counts = [];
}

// นับจำนวนการยืมทั้งหมด
$total_borrows = array_sum($status_counts);

// รับค่าการค้นหาและกรองข้อมูล
$search = isset($_GET['search']) ? $_GET['search'] : '';
$filter_borrow_status = isset($_GET['borrow_status']) ? $_GET['borrow_status'] : '';
$records_per_page = 20;

// สร้างเงื่อนไขการค้นหา
$where_conditions = [];
$params = [];

if (!empty($search)) {
    $where_conditions[] = "(eb.borrower_name LIKE ? OR e.equipment_code LIKE ? OR e.equipment_name LIKE ? OR eb.borrower_department LIKE ? OR eb.lender_name LIKE ?)";
    $search_param = "%$search%";
    array_push($params, $search_param, $search_param, $search_param, $search_param, $search_param);
}

if (!empty($filter_borrow_status)) {
    $where_conditions[] = "eb.borrow_status = ?";
    $params[] = $filter_borrow_status;
}

$where_clause = '';
if (!empty($where_conditions)) {
    $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
}

// นับจำนวนข้อมูลทั้งหมดสำหรับ pagination
try {
    $count_query = "SELECT COUNT(*) as total FROM equipment_borrow eb
        LEFT JOIN equipment e ON eb.equipment_id = e.id
        $where_clause";
    $count_stmt = $db->prepare($count_query);
    $count_stmt->execute($params);
    $total_records = $count_stmt->fetch(PDO::FETCH_ASSOC)['total'];
} catch (PDOException $e) {
    $total_records = 0;
    $_SESSION['error'] = "เกิดข้อผิดพลาดในการนับข้อมูล: " . $e->getMessage();
}

// ตั้งค่า pagination
$total_pages = ceil($total_records / $records_per_page);
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($current_page - 1) * $records_per_page;

// Get borrow list with pagination and search
try {
    $borrow_query = "SELECT eb.*, e.equipment_code, e.equipment_name, e.equipment_status,
                            u.username as created_by_name
                     FROM equipment_borrow eb
                     LEFT JOIN equipment e ON eb.equipment_id = e.id
                     LEFT JOIN users u ON eb.created_by = u.id
                     $where_clause 
                     ORDER BY eb.created_at DESC 
                     LIMIT $offset, $records_per_page";

    $stmt = $db->prepare($borrow_query);
    $stmt->execute($params);
    $borrow_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $borrow_list = [];
    $_SESSION['error'] = "เกิดข้อผิดพลาดในการโหลดข้อมูล: " . $e->getMessage();
}

// Get available equipment for dropdown
try {
    $available_equipment = $db->query("SELECT id, equipment_code, equipment_name FROM equipment WHERE borrow_status = 'ว่าง' AND equipment_status != 'จำหน่ายแล้ว' ORDER BY equipment_name")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $available_equipment = [];
}

// ดึงข้อมูลแผนกจากตาราง departments
try {
    $departments = $db->query("SELECT id, department_name FROM departments ORDER BY department_name")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $departments = [];
    $_SESSION['error'] = "เกิดข้อผิดพลาดในการโหลดข้อมูลแผนก: " . $e->getMessage();
}

// ดึงข้อมูลผู้ให้ยืม (เจ้าหน้าที่ IT จากตาราง employees)
try {
    $it_lenders = $db->query("SELECT id, employee_code, first_name, last_name, position_name FROM employees WHERE position_name = 'เจ้าหน้าที่ IT' ORDER BY first_name, last_name")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $it_lenders = [];
    $_SESSION['error'] = "เกิดข้อผิดพลาดในการโหลดข้อมูลเจ้าหน้าที่ IT: " . $e->getMessage();
}

// สร้าง URL สำหรับ pagination
function getPageUrl($page, $search, $filter_borrow_status) {
    $params = ['page' => $page];
    if (!empty($search)) {
        $params['search'] = $search;
    }
    if (!empty($filter_borrow_status)) {
        $params['borrow_status'] = $filter_borrow_status;
    }
    return 'borrow.php?' . http_build_query($params);
}
?>

    <!-- Navbar -->
    <?php include 'includes/navbar.php'; ?>    
  
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>
        
        <div id="layoutSidenav_content">
            <!-- Main Content -->
            <main>
                <div class="container-fluid px-4">
                    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                        <h1 class="h2">ระบบยืม-คืนอุปกรณ์</h1>
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#borrowModal" onclick="clearForm()">
                                <i class="fas fa-plus"></i> ยืมอุปกรณ์
                            </button>
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

                    <!-- สถิติการยืม -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card card-stat bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="card-title"><?php echo number_format($total_borrows); ?></h4>
                                            <p class="card-text">การยืมทั้งหมด</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-clipboard-list fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card card-stat bg-warning text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="card-title"><?php echo number_format($status_counts['ยืมอยู่'] ?? 0); ?></h4>
                                            <p class="card-text">กำลังยืม</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-hand-holding fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card card-stat bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="card-title"><?php echo number_format($status_counts['คืนแล้ว'] ?? 0); ?></h4>
                                            <p class="card-text">คืนแล้ว</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-check-circle fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card card-stat bg-danger text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="card-title"><?php echo number_format($status_counts['เกินกำหนด'] ?? 0); ?></h4>
                                            <p class="card-text">เกินกำหนด</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-dark">รายการยืม-คืนอุปกรณ์</h6>
                            <div class="d-flex align-items-center">
                                <span id="currentTime" class="text-muted small me-3"></span>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-secondary btn-sm" onclick="printTable()">
                                        <i class="fas fa-print"></i> Print
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- ตัวกรองข้อมูล -->
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="m-0 font-weight-bold text-dark">
                                        <i class="fas fa-filter me-2"></i>ตัวกรองข้อมูล
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <!-- ช่องค้นหาทั่วไป -->
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold">ค้นหาทั่วไป</label>
                                            <input type="text" class="form-control" id="globalSearch" 
                                                   placeholder="ค้นหาด้วยชื่อผู้ยืม, รหัสครุภัณฑ์, ชื่อครุภัณฑ์..." 
                                                   value="<?php echo htmlspecialchars($search); ?>">
                                        </div>

                                        <!-- Dropdown กรองสถานะการยืม -->
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold">สถานะการยืม</label>
                                            <select class="form-control" id="borrowStatusFilter">
                                                <option value="">ทั้งหมด</option>
                                                <option value="ยืมอยู่" <?php echo $filter_borrow_status == 'ยืมอยู่' ? 'selected' : ''; ?>>ยืมอยู่</option>
                                                <option value="คืนแล้ว" <?php echo $filter_borrow_status == 'คืนแล้ว' ? 'selected' : ''; ?>>คืนแล้ว</option>
                                                <option value="เกินกำหนด" <?php echo $filter_borrow_status == 'เกินกำหนด' ? 'selected' : ''; ?>>เกินกำหนด</option>
                                            </select>
                                        </div>

                                        <!-- ปุ่มค้นหาและล้าง -->
                                        <div class="col-md-4 d-flex align-items-end">
                                            <div class="btn-group w-100">
                                                <button type="button" class="btn btn-primary" onclick="applyFilters()">
                                                    <i class="fas fa-search me-1"></i> ค้นหา
                                                </button>
                                                <button type="button" class="btn btn-outline-secondary" onclick="clearFilters()">
                                                    <i class="fas fa-redo me-1"></i> ล้าง
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- แสดงข้อมูลการแบ่งหน้า -->
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="text-muted">
                                    แสดง <?php echo number_format($offset + 1); ?>-<?php echo number_format(min($offset + $records_per_page, $total_records)); ?> 
                                    จาก <?php echo number_format($total_records); ?> รายการ
                                    <?php if ($total_pages > 1): ?>
                                        | หน้า <?php echo $current_page; ?> จาก <?php echo $total_pages; ?>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="10%">รหัสครุภัณฑ์</th>
                                            <th width="15%">ชื่อครุภัณฑ์</th>
                                            <th width="15%">ผู้ยืม</th>
                                            <th width="10%">แผนก</th>
                                            <th width="10%">ผู้ให้ยืม</th>
                                            <th width="10%">วันที่ยืม</th>
                                            <th width="10%">กำหนดคืน</th>
                                            <th width="10%">วันที่คืน</th>
                                            <th width="10%">สถานะ</th>
                                            <th width="10%">จัดการ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (count($borrow_list) > 0): ?>
                                            <?php foreach($borrow_list as $borrow): 
                                                $is_overdue = ($borrow['borrow_status'] == 'ยืมอยู่' && strtotime($borrow['expected_return_date']) < time());
                                                $status_class = [
                                                    'ยืมอยู่' => $is_overdue ? 'danger' : 'warning',
                                                    'คืนแล้ว' => 'success',
                                                    'เกินกำหนด' => 'danger'
                                                ][$borrow['borrow_status']] ?? 'secondary';
                                            ?>
                                            <tr>
                                                <td class="fw-bold text-primary"><?php echo $borrow['equipment_code']; ?></td>
                                                <td><?php echo $borrow['equipment_name']; ?></td>
                                                <td><?php echo $borrow['borrower_name']; ?></td>
                                                <td><?php echo $borrow['borrower_department'] ?: '-'; ?></td>
                                                <td><?php echo $borrow['lender_name'] ?: '-'; ?></td>
                                                <td><?php echo date('d/m/Y', strtotime($borrow['borrow_date'])); ?></td>
                                                <td class="<?php echo $is_overdue ? 'text-danger fw-bold' : ''; ?>">
                                                    <?php echo date('d/m/Y', strtotime($borrow['expected_return_date'])); ?>
                                                    <?php if ($is_overdue): ?>
                                                        <i class="fas fa-exclamation-triangle ms-1"></i>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php echo $borrow['actual_return_date'] ? date('d/m/Y', strtotime($borrow['actual_return_date'])) : '-'; ?>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?php echo $status_class; ?> <?php echo $is_overdue ? 'badge-overdue' : ''; ?>">
                                                        <?php echo $borrow['borrow_status']; ?>
                                                        <?php if ($is_overdue): ?>
                                                            <i class="fas fa-exclamation-triangle ms-1"></i>
                                                        <?php endif; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#viewModal" onclick='viewBorrow(<?php echo json_encode($borrow); ?>)' title="ดูรายละเอียด">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <?php if ($borrow['borrow_status'] == 'ยืมอยู่'): ?>
                                                            <button type="button" class="btn btn-success" onclick="returnEquipment(<?php echo $borrow['id']; ?>)" title="คืนอุปกรณ์">
                                                                <i class="fas fa-undo"></i>
                                                            </button>
                                                        <?php endif; ?>
                                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#borrowModal" onclick='editBorrow(<?php echo json_encode($borrow); ?>)' title="แก้ไข">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <a href="borrow.php?action=delete&id=<?php echo $borrow['id']; ?>" class="btn btn-danger" onclick="return confirm('คุณแน่ใจหรือไม่ที่จะลบรายการยืมนี้?')" title="ลบ">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="10" class="text-center py-4">
                                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                                    <h5 class="text-muted">ไม่พบข้อมูลการยืม-คืน</h5>
                                                    <?php if (!empty($search)): ?>
                                                        <p class="text-muted">ลองเปลี่ยนคำค้นหาหรือล้างการค้นหา</p>
                                                        <a href="borrow.php" class="btn btn-primary">แสดงทั้งหมด</a>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <?php if ($total_pages > 1): ?>
                            <nav aria-label="Page navigation">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="text-muted small d-none d-md-block">
                                        หน้า <?php echo $current_page; ?> จาก <?php echo $total_pages; ?> 
                                        (ทั้งหมด <?php echo number_format($total_records); ?> รายการ)
                                    </div>
                                    
                                    <ul class="pagination justify-content-center mb-0">
                                        <li class="page-item <?php echo $current_page == 1 ? 'disabled' : ''; ?>">
                                            <a class="page-link" href="<?php echo getPageUrl(1, $search, $filter_borrow_status); ?>" aria-label="First">
                                                <span aria-hidden="true">&laquo;&laquo;</span>
                                            </a>
                                        </li>
                                        
                                        <li class="page-item <?php echo $current_page == 1 ? 'disabled' : ''; ?>">
                                            <a class="page-link" href="<?php echo getPageUrl($current_page - 1, $search, $filter_borrow_status); ?>" aria-label="Previous">
                                                <span aria-hidden="true">&laquo;</span>
                                            </a>
                                        </li>

                                        <?php
                                        $start_page = max(1, $current_page - 2);
                                        $end_page = min($total_pages, $current_page + 2);
                                        
                                        if ($start_page > 1) {
                                            echo '<li class="page-item"><a class="page-link" href="' . getPageUrl(1, $search, $filter_borrow_status) . '">1</a></li>';
                                            if ($start_page > 2) {
                                                echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                            }
                                        }
                                        
                                        for ($i = $start_page; $i <= $end_page; $i++) {
                                            $active = $i == $current_page ? 'active' : '';
                                            echo '<li class="page-item ' . $active . '"><a class="page-link" href="' . getPageUrl($i, $search, $filter_borrow_status) . '">' . $i . '</a></li>';
                                        }
                                        
                                        if ($end_page < $total_pages) {
                                            if ($end_page < $total_pages - 1) {
                                                echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                            }
                                            echo '<li class="page-item"><a class="page-link" href="' . getPageUrl($total_pages, $search, $filter_borrow_status) . '">' . $total_pages . '</a></li>';
                                        }
                                        ?>

                                        <li class="page-item <?php echo $current_page == $total_pages ? 'disabled' : ''; ?>">
                                            <a class="page-link" href="<?php echo getPageUrl($current_page + 1, $search, $filter_borrow_status); ?>" aria-label="Next">
                                                <span aria-hidden="true">&raquo;</span>
                                            </a>
                                        </li>
                                        
                                        <li class="page-item <?php echo $current_page == $total_pages ? 'disabled' : ''; ?>">
                                            <a class="page-link" href="<?php echo getPageUrl($total_pages, $search, $filter_borrow_status); ?>" aria-label="Last">
                                                <span aria-hidden="true">&raquo;&raquo;</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </nav>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </main>

           
        </div>
    </div>

    <!-- Modal สำหรับเพิ่ม/แก้ไขการยืม -->
    <div class="modal fade" id="borrowModal" tabindex="-1" aria-labelledby="borrowModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" id="borrowForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="borrowModalLabel">ยืมอุปกรณ์</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="borrow_id" id="borrow_id">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">ครุภัณฑ์ <span class="text-danger">*</span></label>
                                    <select class="form-control" name="equipment_id" id="equipment_id" required <?php echo isset($_GET['action']) && $_GET['action'] == 'edit' ? 'disabled' : ''; ?>>
                                        <option value="">เลือกครุภัณฑ์</option>
                                        <?php foreach($available_equipment as $equipment): ?>
                                            <option value="<?php echo $equipment['id']; ?>">
                                                <?php echo $equipment['equipment_code'] . ' - ' . $equipment['equipment_name']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (isset($_GET['action']) && $_GET['action'] == 'edit'): ?>
                                        <input type="hidden" name="equipment_id" id="equipment_id_hidden">
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">ผู้ยืม <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="borrower_name" id="borrower_name" required placeholder="กรอกชื่อผู้ยืม">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">แผนก/หน่วยงาน <span class="text-danger">*</span></label>
                                    <select class="form-control" name="borrower_department" id="borrower_department" required>
                                        <option value="">เลือกแผนก</option>
                                        <?php foreach($departments as $dept): ?>
                                            <option value="<?php echo $dept['department_name']; ?>">
                                                <?php echo $dept['department_name']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">ผู้ให้ยืม <span class="text-danger">*</span></label>
                                    <select class="form-control" name="lender_name" id="lender_name" required>
                                        <option value="">เลือกผู้ให้ยืม</option>
                                        <?php foreach($it_lenders as $lender): ?>
                                            <option value="<?php echo $lender['first_name'] . ' ' . $lender['last_name']; ?>">
                                                <?php echo $lender['employee_code'] . ' - ' . $lender['first_name'] . ' ' . $lender['last_name'] . ' (' . $lender['position_name'] . ')'; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">วันที่ยืม <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="borrow_date" id="borrow_date" required value="<?php echo date('Y-m-d'); ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">กำหนดคืน <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="expected_return_date" id="expected_return_date" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">วัตถุประสงค์การยืม</label>
                            <textarea class="form-control" name="borrow_purpose" id="borrow_purpose" rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">หมายเหตุ</label>
                            <textarea class="form-control" name="notes" id="notes" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                        <button type="submit" class="btn btn-primary" name="add_borrow" id="submitButton">บันทึก</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal สำหรับดูรายละเอียดการยืม -->
    <div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewModalLabel">รายละเอียดการยืม</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="viewModalBody">
                    <!-- เนื้อหาจะถูกใส่โดย JavaScript -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function clearForm() {
        document.getElementById('borrowForm').reset();
        document.getElementById('borrow_id').value = '';
        document.getElementById('borrowModalLabel').innerText = 'ยืมอุปกรณ์';
        document.getElementById('submitButton').name = 'add_borrow';
        document.getElementById('submitButton').innerText = 'บันทึก';
        
        // เปิดใช้งาน dropdown equipment
        const equipmentSelect = document.getElementById('equipment_id');
        if (equipmentSelect) {
            equipmentSelect.disabled = false;
        }
        
        // ตั้งค่าวันที่คืนเป็น 7 วันจากวันนี้
        const expectedReturnDate = document.getElementById('expected_return_date');
        if (expectedReturnDate && !expectedReturnDate.value) {
            const nextWeek = new Date();
            nextWeek.setDate(nextWeek.getDate() + 7);
            expectedReturnDate.value = nextWeek.toISOString().split('T')[0];
        }
    }

    function editBorrow(borrow) {
        document.getElementById('borrowModalLabel').innerText = 'แก้ไขข้อมูลการยืม';
        document.getElementById('submitButton').name = 'edit_borrow';
        document.getElementById('submitButton').innerText = 'อัพเดท';
        
        document.getElementById('borrow_id').value = borrow.id;
        document.getElementById('equipment_id').value = borrow.equipment_id;
        if (document.getElementById('equipment_id_hidden')) {
            document.getElementById('equipment_id_hidden').value = borrow.equipment_id;
        }
        
        // ตั้งค่าผู้ยืม (text input)
        document.getElementById('borrower_name').value = borrow.borrower_name;
        
        // ตั้งค่าแผนก
        const departmentSelect = document.getElementById('borrower_department');
        if (departmentSelect) {
            let found = false;
            for (let i = 0; i < departmentSelect.options.length; i++) {
                if (departmentSelect.options[i].value === borrow.borrower_department) {
                    departmentSelect.selectedIndex = i;
                    found = true;
                    break;
                }
            }
            if (!found && borrow.borrower_department) {
                const newOption = new Option(borrow.borrower_department, borrow.borrower_department, true, true);
                departmentSelect.add(newOption);
            }
        }
        
        // ตั้งค่าผู้ให้ยืม
        const lenderSelect = document.getElementById('lender_name');
        if (lenderSelect) {
            let found = false;
            for (let i = 0; i < lenderSelect.options.length; i++) {
                if (lenderSelect.options[i].value === borrow.lender_name) {
                    lenderSelect.selectedIndex = i;
                    found = true;
                    break;
                }
            }
            if (!found && borrow.lender_name) {
                const newOption = new Option(borrow.lender_name, borrow.lender_name, true, true);
                lenderSelect.add(newOption);
            }
        }
        
        document.getElementById('borrow_date').value = borrow.borrow_date;
        document.getElementById('expected_return_date').value = borrow.expected_return_date;
        document.getElementById('borrow_purpose').value = borrow.borrow_purpose || '';
        document.getElementById('notes').value = borrow.notes || '';
        
        // ปิดใช้งาน dropdown equipment ในโหมดแก้ไข
        document.getElementById('equipment_id').disabled = true;
    }

    function viewBorrow(borrow) {
        const modalBody = document.getElementById('viewModalBody');
        const isOverdue = borrow.borrow_status === 'ยืมอยู่' && new Date(borrow.expected_return_date) < new Date();
        
        modalBody.innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <h6>ข้อมูลครุภัณฑ์</h6>
                    <table class="table table-bordered">
                        <tr>
                            <th width="40%">รหัสครุภัณฑ์</th>
                            <td>${borrow.equipment_code}</td>
                        </tr>
                        <tr>
                            <th>ชื่อครุภัณฑ์</th>
                            <td>${borrow.equipment_name}</td>
                        </tr>
                        <tr>
                            <th>สถานะครุภัณฑ์</th>
                            <td><span class="badge bg-secondary">${borrow.equipment_status}</span></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6>ข้อมูลการยืม</h6>
                    <table class="table table-bordered">
                        <tr>
                            <th width="40%">ผู้ยืม</th>
                            <td>${borrow.borrower_name}</td>
                        </tr>
                        <tr>
                            <th>แผนก/หน่วยงาน</th>
                            <td>${borrow.borrower_department || '-'}</td>
                        </tr>
                        <tr>
                            <th>ผู้ให้ยืม</th>
                            <td>${borrow.lender_name || '-'}</td>
                        </tr>
                        <tr>
                            <th>วันที่ยืม</th>
                            <td>${new Date(borrow.borrow_date).toLocaleDateString('th-TH')}</td>
                        </tr>
                        <tr>
                            <th>กำหนดคืน</th>
                            <td class="${isOverdue ? 'text-danger fw-bold' : ''}">
                                ${new Date(borrow.expected_return_date).toLocaleDateString('th-TH')}
                                ${isOverdue ? '<i class="fas fa-exclamation-triangle ms-1"></i>' : ''}
                            </td>
                        </tr>
                        <tr>
                            <th>วันที่คืนจริง</th>
                            <td>${borrow.actual_return_date ? new Date(borrow.actual_return_date).toLocaleDateString('th-TH') : '-'}</td>
                        </tr>
                        <tr>
                            <th>สถานะ</th>
                            <td>
                                <span class="badge bg-${borrow.borrow_status === 'ยืมอยู่' ? (isOverdue ? 'danger' : 'warning') : borrow.borrow_status === 'คืนแล้ว' ? 'success' : 'danger'}">
                                    ${borrow.borrow_status}
                                    ${isOverdue ? '<i class="fas fa-exclamation-triangle ms-1"></i>' : ''}
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <h6>วัตถุประสงค์การยืม</h6>
                    <div class="border p-3 bg-light rounded">
                        ${borrow.borrow_purpose || '-'}
                    </div>
                </div>
            </div>
            ${borrow.notes ? `
            <div class="row mt-3">
                <div class="col-12">
                    <h6>หมายเหตุ</h6>
                    <div class="border p-3 bg-light rounded">
                        ${borrow.notes}
                    </div>
                </div>
            </div>
            ` : ''}
            <div class="row mt-3">
                <div class="col-12">
                    <small class="text-muted">
                        บันทึกโดย: ${borrow.created_by_name} | 
                        วันที่บันทึก: ${new Date(borrow.created_at).toLocaleString('th-TH')}
                        ${borrow.updated_at !== borrow.created_at ? `| แก้ไขล่าสุด: ${new Date(borrow.updated_at).toLocaleString('th-TH')}` : ''}
                    </small>
                </div>
            </div>
        `;
    }

    function returnEquipment(borrowId) {
        if (confirm('คุณแน่ใจหรือไม่ที่จะบันทึกการคืนอุปกรณ์นี้?')) {
            window.location.href = `borrow.php?action=return&id=${borrowId}`;
        }
    }

    function applyFilters() {
        const search = document.getElementById('globalSearch').value;
        const status = document.getElementById('borrowStatusFilter').value;
        
        let url = 'borrow.php?';
        const params = [];
        
        if (search) params.push(`search=${encodeURIComponent(search)}`);
        if (status) params.push(`borrow_status=${encodeURIComponent(status)}`);
        
        window.location.href = url + params.join('&');
    }

    function clearFilters() {
        window.location.href = 'borrow.php';
    }

    function printTable() {
        window.print();
    }
    
    // Update current time
    function updateCurrentTime() {
        const now = new Date();
        document.getElementById('currentTime').textContent = 
            `อัปเดตล่าสุด: ${now.toLocaleString('th-TH', { 
                year: 'numeric', 
                month: '2-digit', 
                day: '2-digit',
                hour: '2-digit', 
                minute: '2-digit',
                second: '2-digit'
            })}`;
    }

    setInterval(updateCurrentTime, 1000);
    updateCurrentTime();


    // ตั้งค่าวันที่คืนเป็น 7 วันจากวันนี้
    document.addEventListener('DOMContentLoaded', function() {
        const expectedReturnDate = document.getElementById('expected_return_date');
        if (expectedReturnDate && !expectedReturnDate.value) {
            const nextWeek = new Date();
            nextWeek.setDate(nextWeek.getDate() + 7);
            expectedReturnDate.value = nextWeek.toISOString().split('T')[0];
        }
        
        // อัพเดทเวลา
        function updateTime() {
            const now = new Date();
            document.getElementById('currentTime').textContent = 
                `อัพเดทล่าสุด: ${now.toLocaleTimeString('th-TH')}`;
        }
        updateTime();
        setInterval(updateTime, 60000);
        
        // ตรวจสอบรายการเกินกำหนด
        checkOverdueItems();
    });

    // ฟังก์ชันตรวจสอบรายการเกินกำหนด
    function checkOverdueItems() {
        fetch('includes/check_overdue.php')
            .then(response => response.json())
            .then(data => {
                if (data.updated > 0) {
                    console.log(`อัพเดทสถานะเกินกำหนด ${data.updated} รายการ`);
                    // โหลดหน้าใหม่ถ้ามีการอัพเดท
                    location.reload();
                }
            })
            .catch(error => console.error('Error checking overdue items:', error));
    }
    </script>

 <!-- Footer -->
            <?php include 'includes/footer.php'; ?>
