<?php
require_once 'includes/header.php';

// CRUD Operations
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $id = isset($_GET['id']) ? $_GET['id'] : null;
    
    if ($action == 'delete' && $id) {
        // Delete image file if exists
        $stmt = $db->prepare("SELECT image FROM equipment WHERE id = ?");
        $stmt->execute([$id]);
        $equipment = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($equipment && $equipment['image']) {
            $image_path = 'uploads/equipment/' . $equipment['image'];
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }
        
        $stmt = $db->prepare("DELETE FROM equipment WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['success'] = "ลบข้อมูลครุภัณฑ์เรียบร้อยแล้ว";
        header("Location: equipment.php");
        exit();
    }
}

if ($_POST) {
    if (isset($_POST['add_equipment'])) {
        // Handle image upload
        $image_filename = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $upload_dir = 'uploads/equipment/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            // ใช้รหัสครุภัณฑ์เป็นชื่อไฟล์
            $equipment_code = $_POST['code'];
            $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $file_name = $equipment_code . '_' . time() . '.' . $file_extension;
            $image_path = $upload_dir . $file_name;
            
            // ตรวจสอบประเภทไฟล์
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (in_array($file_extension, $allowed_extensions)) {
                // ตรวจสอบขนาดไฟล์ (ไม่เกิน 5MB)
                if ($_FILES['image']['size'] <= 5 * 1024 * 1024) {
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $image_path)) {
                        $image_filename = $file_name;
                    } else {
                        $_SESSION['error'] = "เกิดข้อผิดพลาดในการอัพโหลดไฟล์";
                    }
                } else {
                    $_SESSION['error'] = "ขนาดไฟล์ต้องไม่เกิน 5MB";
                }
            } else {
                $_SESSION['error'] = "รองรับเฉพาะไฟล์รูปภาพ (JPG, JPEG, PNG, GIF, WEBP)";
            }
        }
        
        if (!isset($_SESSION['error'])) {
            $stmt = $db->prepare("INSERT INTO equipment (code, name, category_id, category_item_id, brand, model, serial_number, purchase_date, purchase_price, department_id, responsible_person, status, specifications, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $_POST['code'],
                $_POST['name'],
                $_POST['category_id'],
                $_POST['category_item_id'],
                $_POST['brand'],
                $_POST['model'],
                $_POST['serial_number'],
                $_POST['purchase_date'],
                $_POST['purchase_price'],
                $_POST['department_id'],
                $_POST['responsible_person'],
                $_POST['status'],
                $_POST['specifications'],
                $image_filename
            ]);
            $_SESSION['success'] = "เพิ่มข้อมูลครุภัณฑ์เรียบร้อยแล้ว";
            header("Location: equipment.php");
            exit();
        }
    }
    
    if (isset($_POST['edit_equipment'])) {
        // Handle image upload
        $image_filename = $_POST['current_image'];
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            // Delete old image
            if ($image_filename) {
                $old_image_path = 'uploads/equipment/' . $image_filename;
                if (file_exists($old_image_path)) {
                    unlink($old_image_path);
                }
            }
            
            $upload_dir = 'uploads/equipment/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            // ใช้รหัสครุภัณฑ์เป็นชื่อไฟล์
            $equipment_code = $_POST['code'];
            $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $file_name = $equipment_code . '_' . time() . '.' . $file_extension;
            $image_path = $upload_dir . $file_name;
            
            // ตรวจสอบประเภทไฟล์
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (in_array($file_extension, $allowed_extensions)) {
                // ตรวจสอบขนาดไฟล์ (ไม่เกิน 5MB)
                if ($_FILES['image']['size'] <= 5 * 1024 * 1024) {
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $image_path)) {
                        $image_filename = $file_name;
                    } else {
                        $_SESSION['error'] = "เกิดข้อผิดพลาดในการอัพโหลดไฟล์";
                    }
                } else {
                    $_SESSION['error'] = "ขนาดไฟล์ต้องไม่เกิน 5MB";
                }
            } else {
                $_SESSION['error'] = "รองรับเฉพาะไฟล์รูปภาพ (JPG, JPEG, PNG, GIF, WEBP)";
            }
        }
        
        if (!isset($_SESSION['error'])) {
            $stmt = $db->prepare("UPDATE equipment SET code=?, name=?, category_id=?, category_item_id=?, brand=?, model=?, serial_number=?, purchase_date=?, purchase_price=?, department_id=?, responsible_person=?, status=?, specifications=?, image=? WHERE id=?");
            $stmt->execute([
                $_POST['code'],
                $_POST['name'],
                $_POST['category_id'],
                $_POST['category_item_id'],
                $_POST['brand'],
                $_POST['model'],
                $_POST['serial_number'],
                $_POST['purchase_date'],
                $_POST['purchase_price'],
                $_POST['department_id'],
                $_POST['responsible_person'],
                $_POST['status'],
                $_POST['specifications'],
                $image_filename,
                $_POST['id']
            ]);
            $_SESSION['success'] = "แก้ไขข้อมูลครุภัณฑ์เรียบร้อยแล้ว";
            header("Location: equipment.php");
            exit();
        }
    }
}

// รับค่าการค้นหาและกรอง
$search = isset($_GET['search']) ? $_GET['search'] : '';
$filter_department = isset($_GET['department']) ? $_GET['department'] : '';
$filter_status = isset($_GET['status']) ? $_GET['status'] : '';
$records_per_page = isset($_GET['per_page']) ? intval($_GET['per_page']) : 20;

// สร้างเงื่อนไขการค้นหา
$where_conditions = [];
$params = [];

if (!empty($search)) {
    $where_conditions[] = "(e.code LIKE ? OR e.name LIKE ? OR c.name LIKE ? OR ci.name LIKE ? OR d.name LIKE ?)";
    $search_param = "%$search%";
    array_push($params, $search_param, $search_param, $search_param, $search_param, $search_param);
}

if (!empty($filter_department)) {
    $where_conditions[] = "e.department_id = ?";
    $params[] = $filter_department;
}

if (!empty($filter_status)) {
    $where_conditions[] = "e.status = ?";
    $params[] = $filter_status;
}

$where_clause = '';
if (!empty($where_conditions)) {
    $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
}

// นับจำนวนข้อมูลทั้งหมดสำหรับ pagination
$count_query = "SELECT COUNT(*) as total FROM equipment e 
    LEFT JOIN categories c ON e.category_id = c.id 
    LEFT JOIN categories_items ci ON e.category_item_id = ci.id 
    LEFT JOIN departments d ON e.department_id = d.id 
    $where_clause";
$count_stmt = $db->prepare($count_query);
$count_stmt->execute($params);
$total_records = $count_stmt->fetch(PDO::FETCH_ASSOC)['total'];

// ตั้งค่า pagination
$total_pages = ceil($total_records / $records_per_page);

// รับค่าหน้าปัจจุบัน
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($current_page - 1) * $records_per_page;

// Get equipment list with pagination and search
$equipment_query = "SELECT e.*, c.name as category_name, ci.name as item_name, d.name as department_name 
    FROM equipment e 
    LEFT JOIN categories c ON e.category_id = c.id 
    LEFT JOIN categories_items ci ON e.category_item_id = ci.id 
    LEFT JOIN departments d ON e.department_id = d.id 
    $where_clause 
    ORDER BY e.created_at DESC 
    LIMIT $offset, $records_per_page";

$stmt = $db->prepare($equipment_query);
$stmt->execute($params);
$equipment_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get categories for dropdown
$categories = $db->query("SELECT * FROM categories ORDER BY code")->fetchAll(PDO::FETCH_ASSOC);

// Get departments for dropdown
$departments = $db->query("SELECT * FROM departments ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

// สร้าง URL สำหรับ pagination
function getPageUrl($page, $search, $filter_department, $filter_status, $per_page) {
    $params = ['page' => $page];
    if (!empty($search)) {
        $params['search'] = $search;
    }
    if (!empty($filter_department)) {
        $params['department'] = $filter_department;
    }
    if (!empty($filter_status)) {
        $params['status'] = $filter_status;
    }
    if ($per_page != 20) {
        $params['per_page'] = $per_page;
    }
    return 'equipment.php?' . http_build_query($params);
}
?>

<?php 
// Include sidebar
include 'includes/sidebar.php';
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <?php include 'includes/navbar.php'; ?>

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">จัดการครุภัณฑ์</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#equipmentModal" onclick="clearForm()">
            <i class="fas fa-plus"></i> เพิ่มครุภัณฑ์
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
            <h6 class="m-0 font-weight-bold text-dark">รายการครุภัณฑ์ทั้งหมด</h6>
        </div>
        <div class="card-body">
            <!-- ช่องค้นหาและกรองข้อมูล -->
            <form method="GET" class="mb-4">
                <div class="row g-3 align-items-end">
                    
                    <div class="col-md-3">
                        <div class="input-group">
                            <input type="text" class="form-control" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="กรอกรหัสครุภัณฑ์, ชื่อ, หมวดหมู่, แผนก...">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">แผนก</label>
                        <select class="form-control" name="department" onchange="this.form.submit()">
                            <option value="">-- ทุกแผนก --</option>
                            <?php foreach($departments as $dept): ?>
                            <option value="<?php echo $dept['id']; ?>" <?php echo ($filter_department == $dept['id']) ? 'selected' : ''; ?>>
                                <?php echo $dept['name']; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold">สถานะ</label>
                        <select class="form-control" name="status" onchange="this.form.submit()">
                            <option value="">-- ทุกสถานะ --</option>
                            <option value="ใหม่" <?php echo ($filter_status == 'ใหม่') ? 'selected' : ''; ?>>ใหม่</option>
                            <option value="ใช้งานปกติ" <?php echo ($filter_status == 'ใช้งานปกติ') ? 'selected' : ''; ?>>ใช้งานปกติ</option>
                            <option value="ชำรุด" <?php echo ($filter_status == 'ชำรุด') ? 'selected' : ''; ?>>ชำรุด</option>
                            <option value="รอซ่อม" <?php echo ($filter_status == 'รอซ่อม') ? 'selected' : ''; ?>>รอซ่อม</option>
                            <option value="จำหน่ายแล้ว" <?php echo ($filter_status == 'จำหน่ายแล้ว') ? 'selected' : ''; ?>>จำหน่ายแล้ว</option>
                        </select>
                    </div>
                    
                    <div class="col-md-1">
                        <?php if (!empty($search) || !empty($filter_department) || !empty($filter_status)): ?>
                            <a href="equipment.php" class="btn btn-secondary w-100" title="ล้างการค้นหา">
                                <i class="fas fa-redo"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <!-- Keep hidden inputs for persistence -->
                <?php if (!empty($search)): ?>
                    <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                <?php endif; ?>
            </form>

            <!-- สถิติการค้นหา -->
            <?php if (!empty($search) || !empty($filter_department) || !empty($filter_status)): ?>
            <div class="alert alert-info mb-3">
                <i class="fas fa-info-circle"></i> 
                พบข้อมูลครุภัณฑ์ทั้งหมด <strong><?php echo number_format($total_records); ?></strong> รายการ
                <?php if (!empty($search)): ?>
                    | คำค้นหา: "<strong><?php echo htmlspecialchars($search); ?></strong>"
                <?php endif; ?>
                <?php if (!empty($filter_department)): ?>
                    <?php 
                    $dept_name = array_filter($departments, function($d) use ($filter_department) {
                        return $d['id'] == $filter_department;
                    });
                    $dept_name = reset($dept_name);
                    ?>
                    | แผนก: "<strong><?php echo $dept_name['name']; ?></strong>"
                <?php endif; ?>
                <?php if (!empty($filter_status)): ?>
                    | สถานะ: "<strong><?php echo $filter_status; ?></strong>"
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- แสดงข้อมูลการแบ่งหน้า -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="text-muted">
                    แสดง <?php echo number_format($offset + 1); ?>-<?php echo number_format(min($offset + $records_per_page, $total_records)); ?> 
                    จาก <?php echo number_format($total_records); ?> รายการ
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th width="8%">รูปภาพ</th>
                            <th width="10%">รหัสครุภัณฑ์</th>
                            <th width="15%">ชื่ออุปกรณ์</th>
                            <th width="12%">หมวดหมู่</th>
                            <th width="12%">รายการอุปกรณ์</th>
                            <th width="12%">แผนก</th>
                            <th width="8%">สถานะ</th>
                            <th width="8%">วันที่จัดซื้อ</th>
                            <th width="15%">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($equipment_list) > 0): ?>
                            <?php foreach($equipment_list as $equipment): ?>
                            <tr>
                                <td class="text-center">
                                    <?php if ($equipment['image']): ?>
                                        <img src="uploads/equipment/<?php echo $equipment['image']; ?>" 
                                             alt="<?php echo $equipment['name']; ?>" 
                                             class="img-thumbnail" 
                                             style="width: 60px; height: 60px; object-fit: cover;"
                                             data-bs-toggle="tooltip" 
                                             title="คลิกเพื่อดูรูปขนาดใหญ่"
                                             onclick="showImageModal('<?php echo $equipment['image']; ?>', '<?php echo $equipment['name']; ?>')">
                                    <?php else: ?>
                                        <div class="text-muted" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; background: #f8f9fa; border: 1px solid #dee2e6;">
                                            <i class="fas fa-image fa-lg"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="fw-bold text-primary"><?php echo $equipment['code']; ?></td>
                                <td><?php echo $equipment['name']; ?></td>
                                <td>
                                    <span class="badge bg-secondary"><?php echo $equipment['category_name']; ?></span>
                                </td>
                                <td><?php echo $equipment['item_name']; ?></td>
                                <td><?php echo $equipment['department_name']; ?></td>
                                <td>
                                    <?php 
                                    $status_badge = [
                                        'ใหม่' => 'success',
                                        'ใช้งานปกติ' => 'primary',
                                        'ชำรุด' => 'warning',
                                        'รอซ่อม' => 'info',
                                        'จำหน่ายแล้ว' => 'danger'
                                    ];
                                    ?>
                                    <span class="badge bg-<?php echo $status_badge[$equipment['status']] ?? 'secondary'; ?>">
                                        <?php echo $equipment['status']; ?>
                                    </span>
                                </td>
                                <td><?php echo $equipment['purchase_date']; ?></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#viewModal" onclick='viewEquipment(<?php echo json_encode($equipment); ?>)' title="ดูรายละเอียด">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#equipmentModal" onclick='editEquipment(<?php echo json_encode($equipment); ?>)' title="แก้ไข">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="equipment.php?action=delete&id=<?php echo $equipment['id']; ?><?php echo !empty($search) ? '&search='.urlencode($search) : ''; ?><?php echo !empty($filter_department) ? '&department='.$filter_department : ''; ?><?php echo !empty($filter_status) ? '&status='.$filter_status : ''; ?><?php echo ($records_per_page != 20) ? '&per_page='.$records_per_page : ''; ?>" class="btn btn-danger" onclick="return confirm('คุณแน่ใจหรือไม่ที่จะลบครุภัณฑ์นี้?')" title="ลบ">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">ไม่พบข้อมูลครุภัณฑ์</h5>
                                    <?php if (!empty($search) || !empty($filter_department) || !empty($filter_status)): ?>
                                        <p class="text-muted">ลองเปลี่ยนคำค้นหาหรือ <a href="equipment.php" class="text-primary">ล้างการค้นหา</a></p>
                                    <?php else: ?>
                                        <p class="text-muted">คลิกปุ่ม "เพิ่มครุภัณฑ์" เพื่อเพิ่มข้อมูลครุภัณฑ์แรก</p>
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
                <ul class="pagination justify-content-center">
                    <!-- Previous Page -->
                    <li class="page-item <?php echo $current_page == 1 ? 'disabled' : ''; ?>">
                        <a class="page-link" href="<?php echo getPageUrl($current_page - 1, $search, $filter_department, $filter_status, $records_per_page); ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>

                    <!-- Page Numbers -->
                    <?php
                    $start_page = max(1, $current_page - 2);
                    $end_page = min($total_pages, $current_page + 2);
                    
                    if ($start_page > 1) {
                        echo '<li class="page-item"><a class="page-link" href="' . getPageUrl(1, $search, $filter_department, $filter_status, $records_per_page) . '">1</a></li>';
                        if ($start_page > 2) {
                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        }
                    }
                    
                    for ($i = $start_page; $i <= $end_page; $i++) {
                        $active = $i == $current_page ? 'active' : '';
                        echo '<li class="page-item ' . $active . '"><a class="page-link" href="' . getPageUrl($i, $search, $filter_department, $filter_status, $records_per_page) . '">' . $i . '</a></li>';
                    }
                    
                    if ($end_page < $total_pages) {
                        if ($end_page < $total_pages - 1) {
                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        }
                        echo '<li class="page-item"><a class="page-link" href="' . getPageUrl($total_pages, $search, $filter_department, $filter_status, $records_per_page) . '">' . $total_pages . '</a></li>';
                    }
                    ?>

                    <!-- Next Page -->
                    <li class="page-item <?php echo $current_page == $total_pages ? 'disabled' : ''; ?>">
                        <a class="page-link" href="<?php echo getPageUrl($current_page + 1, $search, $filter_department, $filter_status, $records_per_page); ?>" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                </ul>
            </nav>
            <?php endif; ?>
        </div>
    </div>
</main>

<!-- Equipment Modal -->
<div class="modal fade" id="equipmentModal" tabindex="-1" aria-labelledby="equipmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="equipmentModalLabel">เพิ่มครุภัณฑ์</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" id="equipmentForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="id" id="equipment_id">
                    <input type="hidden" name="current_image" id="current_image">
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">รหัสครุภัณฑ์ *</label>
                                    <input type="text" class="form-control" name="code" id="code" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">ชื่ออุปกรณ์ *</label>
                                    <input type="text" class="form-control" name="name" id="name" required>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">หมวดหมู่ *</label>
                                    <select class="form-control" name="category_id" id="category_id" required onchange="loadCategoryItems(this.value)">
                                        <option value="">เลือกหมวดหมู่</option>
                                        <?php foreach($categories as $category): ?>
                                        <option value="<?php echo $category['id']; ?>" data-code="<?php echo $category['code']; ?>">
                                            <?php echo $category['code']; ?> - <?php echo $category['name']; ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">รายการอุปกรณ์ *</label>
                                    <select class="form-control" name="category_item_id" id="category_item_id" required>
                                        <option value="">เลือกรายการอุปกรณ์</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">ยี่ห้อ</label>
                                    <input type="text" class="form-control" name="brand" id="brand">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">รุ่น</label>
                                    <input type="text" class="form-control" name="model" id="model">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">หมายเลขซีเรียล</label>
                                    <input type="text" class="form-control" name="serial_number" id="serial_number">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">วันที่จัดซื้อ</label>
                                    <input type="date" class="form-control" name="purchase_date" id="purchase_date">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">ราคาจัดซื้อ (บาท)</label>
                                    <input type="number" step="0.01" class="form-control" name="purchase_price" id="purchase_price">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">แผนก *</label>
                                    <select class="form-control" name="department_id" id="department_id" required>
                                        <option value="">เลือกแผนก</option>
                                        <?php foreach($departments as $department): ?>
                                        <option value="<?php echo $department['id']; ?>">
                                            <?php echo $department['name']; ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">ผู้รับผิดชอบ</label>
                                    <input type="text" class="form-control" name="responsible_person" id="responsible_person">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">สถานะ</label>
                                    <select class="form-control" name="status" id="status">
                                        <option value="ใหม่">ใหม่</option>
                                        <option value="ใช้งานปกติ" selected>ใช้งานปกติ</option>
                                        <option value="ชำรุด">ชำรุด</option>
                                        <option value="รอซ่อม">รอซ่อม</option>
                                        <option value="จำหน่ายแล้ว">จำหน่ายแล้ว</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">รายละเอียดคุณสมบัติ</label>
                                <textarea class="form-control" name="specifications" id="specifications" rows="3"></textarea>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">รูปภาพครุภัณฑ์</label>
                                <input type="file" class="form-control" name="image" id="image" accept="image/*" onchange="previewImage(event)">
                                <small class="text-muted">รองรับไฟล์: JPG, JPEG, PNG, GIF, WEBP (สูงสุด 5MB)</small>
                            </div>
                            <div class="text-center">
                                <img id="imagePreview" src="" alt="Image Preview" class="img-fluid rounded border" style="max-height: 300px; display: none;">
                                <div id="noImagePlaceholder" class="border rounded p-5 bg-light">
                                    <i class="fas fa-image fa-3x text-muted"></i>
                                    <p class="text-muted mt-2 mb-0">ไม่มีรูปภาพ</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                    <button type="submit" name="add_equipment" id="submitBtn" class="btn btn-primary">บันทึก</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Equipment Modal -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="viewModalLabel">รายละเอียดครุภัณฑ์</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4 text-center mb-3">
                        <img id="viewImage" src="" alt="Equipment Image" class="img-fluid rounded border" style="max-height: 300px; display: none;">
                        <div id="viewNoImage" class="border rounded p-5 bg-light">
                            <i class="fas fa-image fa-3x text-muted"></i>
                            <p class="text-muted mt-2 mb-0">ไม่มีรูปภาพ</p>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <table class="table table-bordered">
                            <tr>
                                <th width="30%" class="bg-light">รหัสครุภัณฑ์</th>
                                <td id="viewCode"></td>
                            </tr>
                            <tr>
                                <th class="bg-light">ชื่ออุปกรณ์</th>
                                <td id="viewName"></td>
                            </tr>
                            <tr>
                                <th class="bg-light">หมวดหมู่</th>
                                <td id="viewCategory"></td>
                            </tr>
                            <tr>
                                <th class="bg-light">รายการอุปกรณ์</th>
                                <td id="viewItem"></td>
                            </tr>
                            <tr>
                                <th class="bg-light">ยี่ห้อ</th>
                                <td id="viewBrand"></td>
                            </tr>
                            <tr>
                                <th class="bg-light">รุ่น</th>
                                <td id="viewModel"></td>
                            </tr>
                            <tr>
                                <th class="bg-light">หมายเลขซีเรียล</th>
                                <td id="viewSerial"></td>
                            </tr>
                            <tr>
                                <th class="bg-light">วันที่จัดซื้อ</th>
                                <td id="viewPurchaseDate"></td>
                            </tr>
                            <tr>
                                <th class="bg-light">ราคาจัดซื้อ</th>
                                <td id="viewPurchasePrice"></td>
                            </tr>
                            <tr>
                                <th class="bg-light">แผนก</th>
                                <td id="viewDepartment"></td>
                            </tr>
                            <tr>
                                <th class="bg-light">ผู้รับผิดชอบ</th>
                                <td id="viewResponsible"></td>
                            </tr>
                            <tr>
                                <th class="bg-light">สถานะ</th>
                                <td id="viewStatus"></td>
                            </tr>
                            <tr>
                                <th class="bg-light">รายละเอียดคุณสมบัติ</th>
                                <td id="viewSpecifications"></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>

<!-- Image Preview Modal -->
<div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imagePreviewTitle">รูปภาพครุภัณฑ์</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="previewImage" src="" alt="" class="img-fluid">
            </div>
        </div>
    </div>
</div>

<script>
// Clear form function
function clearForm() {
    document.getElementById('equipmentForm').reset();
    document.getElementById('equipment_id').value = '';
    document.getElementById('current_image').value = '';
    document.getElementById('equipmentModalLabel').textContent = 'เพิ่มครุภัณฑ์';
    document.getElementById('submitBtn').name = 'add_equipment';
    document.getElementById('submitBtn').textContent = 'บันทึก';
    document.getElementById('category_item_id').innerHTML = '<option value="">เลือกรายการอุปกรณ์</option>';
    
    // Reset image preview
    document.getElementById('imagePreview').style.display = 'none';
    document.getElementById('noImagePlaceholder').style.display = 'block';
}

// Edit equipment function
function editEquipment(equipment) {
    document.getElementById('equipment_id').value = equipment.id;
    document.getElementById('code').value = equipment.code;
    document.getElementById('name').value = equipment.name;
    document.getElementById('category_id').value = equipment.category_id || '';
    document.getElementById('brand').value = equipment.brand || '';
    document.getElementById('model').value = equipment.model || '';
    document.getElementById('serial_number').value = equipment.serial_number || '';
    document.getElementById('purchase_date').value = equipment.purchase_date || '';
    document.getElementById('purchase_price').value = equipment.purchase_price || '';
    document.getElementById('department_id').value = equipment.department_id || '';
    document.getElementById('responsible_person').value = equipment.responsible_person || '';
    document.getElementById('status').value = equipment.status || 'ใช้งานปกติ';
    document.getElementById('specifications').value = equipment.specifications || '';
    document.getElementById('current_image').value = equipment.image || '';
    
    // Load category items and set selected value
    if (equipment.category_id) {
        loadCategoryItems(equipment.category_id, equipment.category_item_id);
    }
    
    // Show image preview if exists
    if (equipment.image) {
        document.getElementById('imagePreview').src = 'uploads/equipment/' + equipment.image;
        document.getElementById('imagePreview').style.display = 'block';
        document.getElementById('noImagePlaceholder').style.display = 'none';
    } else {
        document.getElementById('imagePreview').style.display = 'none';
        document.getElementById('noImagePlaceholder').style.display = 'block';
    }
    
    document.getElementById('equipmentModalLabel').textContent = 'แก้ไขครุภัณฑ์';
    document.getElementById('submitBtn').name = 'edit_equipment';
    document.getElementById('submitBtn').textContent = 'อัพเดท';
}

// View equipment function
function viewEquipment(equipment) {
    document.getElementById('viewCode').textContent = equipment.code;
    document.getElementById('viewName').textContent = equipment.name;
    document.getElementById('viewCategory').textContent = equipment.category_name || '-';
    document.getElementById('viewItem').textContent = equipment.item_name || '-';
    document.getElementById('viewBrand').textContent = equipment.brand || '-';
    document.getElementById('viewModel').textContent = equipment.model || '-';
    document.getElementById('viewSerial').textContent = equipment.serial_number || '-';
    document.getElementById('viewPurchaseDate').textContent = equipment.purchase_date || '-';
    document.getElementById('viewPurchasePrice').textContent = equipment.purchase_price ? parseFloat(equipment.purchase_price).toLocaleString('th-TH', {minimumFractionDigits: 2}) + ' บาท' : '-';
    document.getElementById('viewDepartment').textContent = equipment.department_name || '-';
    document.getElementById('viewResponsible').textContent = equipment.responsible_person || '-';
    document.getElementById('viewSpecifications').textContent = equipment.specifications || '-';
    
    // Status badge
    const statusBadges = {
        'ใหม่': 'success',
        'ใช้งานปกติ': 'primary',
        'ชำรุด': 'warning',
        'รอซ่อม': 'info',
        'จำหน่ายแล้ว': 'danger'
    };
    const badgeClass = statusBadges[equipment.status] || 'secondary';
    document.getElementById('viewStatus').innerHTML = `<span class="badge bg-${badgeClass}">${equipment.status}</span>`;
    
    // Show image or placeholder
    if (equipment.image) {
        document.getElementById('viewImage').src = 'uploads/equipment/' + equipment.image;
        document.getElementById('viewImage').style.display = 'block';
        document.getElementById('viewNoImage').style.display = 'none';
    } else {
        document.getElementById('viewImage').style.display = 'none';
        document.getElementById('viewNoImage').style.display = 'block';
    }
}

// Preview image function
function previewImage(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('imagePreview').src = e.target.result;
            document.getElementById('imagePreview').style.display = 'block';
            document.getElementById('noImagePlaceholder').style.display = 'none';
        }
        reader.readAsDataURL(file);
    }
}

// Function to show image in modal
function showImageModal(imageName, equipmentName) {
    if (imageName) {
        $('#previewImage').attr('src', 'uploads/equipment/' + imageName);
        $('#imagePreviewTitle').text('รูปภาพ: ' + equipmentName);
        $('#imagePreviewModal').modal('show');
    }
}

// Function to load category items
function loadCategoryItems(categoryId, selectedItemId = null) {
    if (categoryId) {
        fetch('ajax_get_items.php?category_id=' + categoryId)
            .then(response => response.json())
            .then(data => {
                const itemSelect = document.getElementById('category_item_id');
                itemSelect.innerHTML = '<option value="">เลือกรายการอุปกรณ์</option>';
                data.forEach(item => {
                    const selected = selectedItemId && item.id == selectedItemId ? 'selected' : '';
                    itemSelect.innerHTML += `<option value="${item.id}" ${selected}>${item.name}</option>`;
                });
            })
            .catch(error => {
                console.error('Error:', error);
            });
    } else {
        document.getElementById('category_item_id').innerHTML = '<option value="">เลือกรายการอุปกรณ์</option>';
    }
}
</script>

<?php require_once 'includes/footer.php'; ?>