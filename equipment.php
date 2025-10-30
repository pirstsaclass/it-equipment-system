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
            $image_path = 'uploads/img_equipment/' . $equipment['image'];
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
            $upload_dir = 'uploads/img_equipment/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $equipment_code = $_POST['code'];
            $equipment_name = $_POST['name'];
            
            $clean_equipment_name = preg_replace('/[^a-zA-Z0-9ก-๙_\-\s]/u', '', $equipment_name);
            $clean_equipment_name = str_replace(' ', '_', $clean_equipment_name);
            
            $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $file_name = $equipment_code . '_' . $clean_equipment_name . '.' . $file_extension;
            $image_path = $upload_dir . $file_name;
            
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (in_array($file_extension, $allowed_extensions)) {
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
            $stmt = $db->prepare("INSERT INTO equipment (code, name, category_id, category_item_id, brand, model, serial_number, purchase_date, purchase_price, department_id, responsible_person, equipment_status, specifications, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
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
                $_POST['equipment_status'],
                $_POST['specifications'],
                $image_filename
            ]);
            $_SESSION['success'] = "เพิ่มข้อมูลครุภัณฑ์เรียบร้อยแล้ว";
            header("Location: equipment.php");
            exit();
        }
    }
    
    if (isset($_POST['edit_equipment'])) {
        try {
            // Get old status before update
            $old_status_stmt = $db->prepare("SELECT equipment_status FROM equipment WHERE id = ?");
            $old_status_stmt->execute([$_POST['id']]);
            $old_status = $old_status_stmt->fetchColumn();
            
            $new_status = $_POST['equipment_status'];
            
            // Handle image upload
            $image_filename = $_POST['current_image'];
            
            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                // Delete old image
                if ($image_filename) {
                    $old_image_path = 'uploads/img_equipment/' . $image_filename;
                    if (file_exists($old_image_path)) {
                        unlink($old_image_path);
                    }
                }
                
                $upload_dir = 'uploads/img_equipment/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $equipment_code = $_POST['code'];
                $equipment_name = $_POST['name'];
                
                $clean_equipment_name = preg_replace('/[^a-zA-Z0-9ก-๙_\-\s]/u', '', $equipment_name);
                $clean_equipment_name = str_replace(' ', '_', $clean_equipment_name);
                
                $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                $file_name = $equipment_code . '_' . $clean_equipment_name . '.' . $file_extension;
                $image_path = $upload_dir . $file_name;
                
                $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                if (in_array($file_extension, $allowed_extensions)) {
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
                // Update equipment
                $stmt = $db->prepare("UPDATE equipment SET code=?, name=?, category_id=?, category_item_id=?, brand=?, model=?, serial_number=?, purchase_date=?, purchase_price=?, department_id=?, responsible_person=?, equipment_status=?, specifications=?, image=? WHERE id=?");
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
                    $new_status,
                    $_POST['specifications'],
                    $image_filename,
                    $_POST['id']
                ]);
                
                $_SESSION['success'] = "แก้ไขข้อมูลครุภัณฑ์เรียบร้อยแล้ว";
                header("Location: equipment.php");
                exit();
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "เกิดข้อผิดพลาด: " . $e->getMessage();
            header("Location: equipment.php");
            exit();
        }
    }
}

// รับค่าการค้นหาและกรองข้อมูล
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
    $where_conditions[] = "e.equipment_status = ?";
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
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($current_page - 1) * $records_per_page;

// Get equipment list with pagination and search - แก้ไข query
$equipment_query = "SELECT e.*, c.name as category_name, ci.name as item_name, d.name as department_name,
                           (SELECT m.repair_status FROM maintenance m 
                            WHERE m.equipment_id = e.id 
                            ORDER BY m.created_at DESC 
                            LIMIT 1) as maintenance_status
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
                    <div class="col-md-2">
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
                        <label class="form-label fw-bold">สถานะอุปกรณ์</label>
                        <select class="form-control" name="status" onchange="this.form.submit()">
                            <option value="">-- ทุกสถานะ --</option>
                            <option value="อุปกรณ์ใหม่" <?php echo ($filter_status == 'อุปกรณ์ใหม่') ? 'selected' : ''; ?>>อุปกรณ์ใหม่</option>
                            <option value="อุปกรณ์เดิม" <?php echo ($filter_status == 'อุปกรณ์เดิม') ? 'selected' : ''; ?>>อุปกรณ์เดิม</option>
                        </select>
                    </div>
                                        
                    <!-- Dropdown เลือกจำนวนรายการต่อหน้า -->
                    <div class="col-md-2">
                        <label class="form-label fw-bold">แสดงต่อหน้า</label>
                        <select class="form-control" name="per_page" onchange="this.form.submit()">
                            <option value="10" <?php echo ($records_per_page == 10) ? 'selected' : ''; ?>>10 รายการ</option>
                            <option value="20" <?php echo ($records_per_page == 20) ? 'selected' : ''; ?>>20 รายการ</option>
                            <option value="50" <?php echo ($records_per_page == 50) ? 'selected' : ''; ?>>50 รายการ</option>
                            <option value="100" <?php echo ($records_per_page == 100) ? 'selected' : ''; ?>>100 รายการ</option>
                        </select>
                    </div>
                    
                    <div class="col-md-1">
                        <?php if (!empty($search) || !empty($filter_department) || !empty($filter_status) || $records_per_page != 20): ?>
                            <a href="equipment.php" class="btn btn-secondary w-100" title="ล้างการค้นหา">
                                <i class="fas fa-redo"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
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
                    <?php if ($total_pages > 1): ?>
                        | หน้า <?php echo $current_page; ?> จาก <?php echo $total_pages; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th width="12%">รหัสครุภัณฑ์</th>
                            <th width="18%">ชื่ออุปกรณ์</th>
                            <th width="14%">หมวดหมู่</th>
                            <th width="14%">รายการอุปกรณ์</th>
                            <th width="14%">แผนก</th>
                            <th width="10%">สถานะอุปกรณ์</th>
                            <th width="10%">สถานะซ่อม</th>
                            <th width="10%">วันที่จัดซื้อ</th>
                            <th width="18%">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($equipment_list) > 0): ?>
                            <?php foreach($equipment_list as $equipment): ?>
                            <tr>
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
                                        'อุปกรณ์ใหม่' => 'success',
                                        'อุปกรณ์เดิม' => 'primary',
                                        'อุปกรณ์ใหม่-ซ่อมเสร็จ' => 'success',
                                        'อุปกรณ์เดิม-ซ่อมเสร็จ' => 'primary',
                                        'รอซ่อม' => 'info',
                                        'กำลังซ่อม' => 'warning',
                                        'ใช้งานปกติ' => 'primary',
                                        'ชำรุด' => 'warning',
                                        'ซ่อมเสร็จแล้ว' => 'success',
                                        'ส่งคืนแล้ว' => 'primary',
                                        'จำหน่ายแล้ว' => 'danger'
                                    ];
                                    ?>
                                    <span class="badge bg-<?php echo $status_badge[$equipment['equipment_status']] ?? 'secondary'; ?>" style="max-width: 120px; white-space: normal; word-wrap: break-word; line-height: 1.2;">
                                        <?php echo $equipment['equipment_status']; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php 
                                    $maintenance_status_badge = [
                                        'รอซ่อม' => 'warning',
                                        'กำลังดำเนินการ' => 'info',
                                        'ซ่อมเสร็จ' => 'success',
                                        'ยกเลิก' => 'danger'
                                    ];
                                    $maintenance_status = $equipment['maintenance_status'];
                                    $maintenance_class = $maintenance_status_badge[$maintenance_status] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-<?php echo $maintenance_class; ?>">
                                        <?php echo $maintenance_status ?: 'ไม่มี'; ?>
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
                                        <a href="equipment.php?action=delete&id=<?php echo $equipment['id']; ?><?php echo !empty($search) ? '&search='.urlencode($search) : ''; ?><?php echo !empty($filter_department) ? '&department='.$filter_department : ''; ?><?php echo !empty($filter_status) ? '&status='.$filter_status : ''; ?><?php echo ($records_per_page != 20) ? '&per_page='.$records_per_page : ''; ?><?php echo ($current_page > 1) ? '&page='.$current_page : ''; ?>" class="btn btn-danger" onclick="return confirm('คุณแน่ใจหรือไม่ที่จะลบครุภัณฑ์นี้?')" title="ลบ">
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
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small d-none d-md-block">
                        หน้า <?php echo $current_page; ?> จาก <?php echo $total_pages; ?> 
                        (ทั้งหมด <?php echo number_format($total_records); ?> รายการ)
                    </div>
                    
                    <ul class="pagination justify-content-center mb-0">
                        <li class="page-item <?php echo $current_page == 1 ? 'disabled' : ''; ?>">
                            <a class="page-link" href="<?php echo getPageUrl(1, $search, $filter_department, $filter_status, $records_per_page); ?>" aria-label="First">
                                <span aria-hidden="true">&laquo;&laquo;</span>
                            </a>
                        </li>
                        
                        <li class="page-item <?php echo $current_page == 1 ? 'disabled' : ''; ?>">
                            <a class="page-link" href="<?php echo getPageUrl($current_page - 1, $search, $filter_department, $filter_status, $records_per_page); ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>

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

                        <li class="page-item <?php echo $current_page == $total_pages ? 'disabled' : ''; ?>">
                            <a class="page-link" href="<?php echo getPageUrl($current_page + 1, $search, $filter_department, $filter_status, $records_per_page); ?>" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                        
                        <li class="page-item <?php echo $current_page == $total_pages ? 'disabled' : ''; ?>">
                            <a class="page-link" href="<?php echo getPageUrl($total_pages, $search, $filter_department, $filter_status, $records_per_page); ?>" aria-label="Last">
                                <span aria-hidden="true">&raquo;&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </div>
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
                                    <label class="form-label">สถานะ *</label>
                                    <select class="form-control" name="equipment_status" id="equipment_status" required>
                                        <option value="อุปกรณ์ใหม่">อุปกรณ์ใหม่</option>
                                        <option value="อุปกรณ์เดิม" selected>อุปกรณ์เดิม</option>
                                        <!-- ไม่แสดงสถานะ -ซ่อมเสร็จ ใน dropdown -->
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
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="viewModalLabel">รายละเอียดครุภัณฑ์</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4 text-center mb-4">
                        <div id="viewImageContainer">
                            <img id="viewImage" src="" alt="Equipment Image" class="img-fluid rounded border shadow" style="max-height: 300px; display: none;">
                            <div id="viewNoImage" class="border rounded p-5 bg-light">
                                <i class="fas fa-image fa-4x text-muted mb-3"></i>
                                <p class="text-muted mt-2 mb-0">ไม่มีรูปภาพ</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-8">
                        <div class="row mb-3">
                            <div class="col-12">
                                <h4 class="text-primary" id="viewName"></h4>
                                <h6 class="text-muted">รหัสครุภัณฑ์: <span id="viewCode" class="fw-bold"></span></h6>
                            </div>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="35%" class="bg-light">หมวดหมู่</th>
                                    <td id="viewCategory">-</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">รายการอุปกรณ์</th>
                                    <td id="viewItem">-</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">ยี่ห้อ</th>
                                    <td id="viewBrand">-</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">รุ่น</th>
                                    <td id="viewModel">-</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">หมายเลขซีเรียล</th>
                                    <td id="viewSerial">-</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">วันที่จัดซื้อ</th>
                                    <td id="viewPurchaseDate">-</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">ราคาจัดซื้อ</th>
                                    <td id="viewPurchasePrice">-</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">แผนก</th>
                                    <td id="viewDepartment">-</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">ผู้รับผิดชอบ</th>
                                    <td id="viewResponsible">-</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">สถานะอุปกรณ์</th>
                                    <td id="viewStatus">-</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">สถานะซ่อมล่าสุด</th>
                                    <td id="viewMaintenanceStatus">-</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">รายละเอียดคุณสมบัติ</th>
                                    <td id="viewSpecifications">-</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>

<script>
function clearForm() {
    document.getElementById('equipmentForm').reset();
    document.getElementById('equipment_id').value = '';
    document.getElementById('current_image').value = '';
    document.getElementById('equipmentModalLabel').textContent = 'เพิ่มครุภัณฑ์';
    document.getElementById('submitBtn').name = 'add_equipment';
    document.getElementById('submitBtn').textContent = 'บันทึก';
    document.getElementById('category_item_id').innerHTML = '<option value="">เลือกรายการอุปกรณ์</option>';
    
    document.getElementById('imagePreview').style.display = 'none';
    document.getElementById('noImagePlaceholder').style.display = 'block';
}

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
    document.getElementById('equipment_status').value = equipment.equipment_status || 'อุปกรณ์ใหม่';
    document.getElementById('specifications').value = equipment.specifications || '';
    document.getElementById('current_image').value = equipment.image || '';
    
    if (equipment.category_id) {
        loadCategoryItems(equipment.category_id, equipment.category_item_id);
    }
    
    if (equipment.image) {
        document.getElementById('imagePreview').src = 'uploads/img_equipment/' + equipment.image;
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

function viewEquipment(equipment) {
    document.getElementById('viewName').textContent = equipment.name;
    document.getElementById('viewCode').textContent = equipment.code;
    document.getElementById('viewCategory').textContent = equipment.category_name || '-';
    document.getElementById('viewItem').textContent = equipment.item_name || '-';
    document.getElementById('viewBrand').textContent = equipment.brand || '-';
    document.getElementById('viewModel').textContent = equipment.model || '-';
    document.getElementById('viewSerial').textContent = equipment.serial_number || '-';
    document.getElementById('viewPurchaseDate').textContent = equipment.purchase_date || '-';
    document.getElementById('viewPurchasePrice').textContent = equipment.purchase_price ? 
        parseFloat(equipment.purchase_price).toLocaleString('th-TH', {minimumFractionDigits: 2}) + ' บาท' : '-';
    document.getElementById('viewDepartment').textContent = equipment.department_name || '-';
    document.getElementById('viewResponsible').textContent = equipment.responsible_person || '-';
    document.getElementById('viewSpecifications').textContent = equipment.specifications || '-';
    
    const statusBadges = {
        'อุปกรณ์ใหม่': 'success',
        'อุปกรณ์เดิม': 'primary'
    };
    const badgeClass = statusBadges[equipment.equipment_status] || 'secondary';
    document.getElementById('viewStatus').innerHTML = `<span class="badge bg-${badgeClass}">${equipment.equipment_status}</span>`;
    
    // แสดงสถานะการซ่อม
    const maintenanceStatus = equipment.maintenance_status || 'ไม่มี';
    const maintenanceBadges = {
        'รอซ่อม': 'warning',
        'กำลังดำเนินการ': 'info',
        'ซ่อมเสร็จ': 'success',
        'ยกเลิก': 'danger'
    };
    const maintenanceClass = maintenanceBadges[maintenanceStatus] || 'secondary';
    document.getElementById('viewMaintenanceStatus').innerHTML = `<span class="badge bg-${maintenanceClass}">${maintenanceStatus}</span>`;
    
    if (equipment.image) {
        document.getElementById('viewImage').src = 'uploads/img_equipment/' + equipment.image;
        document.getElementById('viewImage').style.display = 'block';
        document.getElementById('viewNoImage').style.display = 'none';
        document.getElementById('viewImage').onerror = function() {
            this.style.display = 'none';
            document.getElementById('viewNoImage').style.display = 'block';
        };
    } else {
        document.getElementById('viewImage').style.display = 'none';
        document.getElementById('viewNoImage').style.display = 'block';
    }
}

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

function loadCategoryItems(categoryId, selectedItemId = null) {
    if (categoryId) {
        fetch('get_category_items.php?category_id=' + categoryId)
            .then(response => response.json())
            .then(data => {
                const itemSelect = document.getElementById('category_item_id');
                itemSelect.innerHTML = '<option value="">เลือกรายการอุปกรณ์</option>';
                data.forEach(item => {
                    const optionText = item.name;
                    const selected = selectedItemId && item.id == selectedItemId ? 'selected' : '';
                    itemSelect.innerHTML += `<option value="${item.id}" ${selected}>${optionText}</option>`;
                });
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('category_item_id').innerHTML = '<option value="">เลือกรายการอุปกรณ์</option>';
            });
    } else {
        document.getElementById('category_item_id').innerHTML = '<option value="">เลือกรายการอุปกรณ์</option>';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    var viewModal = document.getElementById('viewModal');
    viewModal.addEventListener('hidden.bs.modal', function () {
        document.getElementById('viewImage').src = '';
        document.getElementById('viewImage').style.display = 'none';
        document.getElementById('viewNoImage').style.display = 'block';
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>
