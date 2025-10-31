<?php
require_once 'includes/header.php';

// CRUD Operations
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $id = isset($_GET['id']) ? $_GET['id'] : null;
    
    if ($action == 'delete' && $id) {
        // Delete image file if exists
        $stmt = $db->prepare("SELECT image_path FROM equipment WHERE equipment_id = ?");
        $stmt->execute([$id]);
        $equipment = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($equipment && $equipment['image_path']) {
            $image_path = 'uploads/img_equipment/' . $equipment['image_path'];
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }
        
        $stmt = $db->prepare("DELETE FROM equipment WHERE equipment_id = ?");
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
        if (isset($_FILES['equipment_image']) && $_FILES['equipment_image']['error'] == 0) {
            $upload_dir = 'uploads/img_equipment/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $equipment_code = $_POST['equipment_code'];
            $equipment_name = $_POST['equipment_name'];
            
            $clean_equipment_name = preg_replace('/[^a-zA-Z0-9ก-๙_\-\s]/u', '', $equipment_name);
            $clean_equipment_name = str_replace(' ', '_', $clean_equipment_name);
            
            $file_extension = strtolower(pathinfo($_FILES['equipment_image']['name'], PATHINFO_EXTENSION));
            $file_name = $equipment_code . '_' . $clean_equipment_name . '.' . $file_extension;
            $image_path = $upload_dir . $file_name;
            
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (in_array($file_extension, $allowed_extensions)) {
                if ($_FILES['equipment_image']['size'] <= 5 * 1024 * 1024) {
                    if (move_uploaded_file($_FILES['equipment_image']['tmp_name'], $image_path)) {
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
            $stmt = $db->prepare("INSERT INTO equipment (equipment_code, equipment_name, category_id, subcategory_id, brand_name, model_name, serial_number, purchase_date, warranty_expiry_date, purchase_price, supplier_name, equipment_status, location_school, location_building, location_floor, location_room, responsible_person, notes, image_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $_POST['equipment_code'],
                $_POST['equipment_name'],
                $_POST['category_id'],
                $_POST['subcategory_id'],
                $_POST['brand_name'],
                $_POST['model_name'],
                $_POST['serial_number'],
                $_POST['purchase_date'],
                $_POST['warranty_expiry_date'],
                $_POST['purchase_price'],
                $_POST['supplier_name'],
                $_POST['equipment_status'],
                $_POST['location_school'],
                $_POST['location_building'],
                $_POST['location_floor'],
                $_POST['location_room'],
                $_POST['responsible_person'],
                $_POST['notes'],
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
            $old_equipment_status_stmt = $db->prepare("SELECT equipment_status FROM equipment WHERE equipment_id = ?");
            $old_equipment_status_stmt->execute([$_POST['equipment_id']]);
            $old_equipment_status = $old_equipment_status_stmt->fetchColumn();
            
            $new_equipment_status = $_POST['equipment_status'];
            
            // Handle image upload
            $image_filename = $_POST['current_equipment_image'];
            
            if (isset($_FILES['equipment_image']) && $_FILES['equipment_image']['error'] == 0) {
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
                
                $equipment_code = $_POST['equipment_code'];
                $equipment_name = $_POST['equipment_name'];
                
                $clean_equipment_name = preg_replace('/[^a-zA-Z0-9ก-๙_\-\s]/u', '', $equipment_name);
                $clean_equipment_name = str_replace(' ', '_', $clean_equipment_name);
                
                $file_extension = strtolower(pathinfo($_FILES['equipment_image']['name'], PATHINFO_EXTENSION));
                $file_name = $equipment_code . '_' . $clean_equipment_name . '.' . $file_extension;
                $image_path = $upload_dir . $file_name;
                
                $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                if (in_array($file_extension, $allowed_extensions)) {
                    if ($_FILES['equipment_image']['size'] <= 5 * 1024 * 1024) {
                        if (move_uploaded_file($_FILES['equipment_image']['tmp_name'], $image_path)) {
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
                $stmt = $db->prepare("UPDATE equipment SET equipment_code=?, equipment_name=?, category_id=?, subcategory_id=?, brand_name=?, model_name=?, serial_number=?, purchase_date=?, warranty_expiry_date=?, purchase_price=?, supplier_name=?, equipment_status=?, location_school=?, location_building=?, location_floor=?, location_room=?, responsible_person=?, notes=?, image_path=? WHERE equipment_id=?");
                $stmt->execute([
                    $_POST['equipment_code'],
                    $_POST['equipment_name'],
                    $_POST['category_id'],
                    $_POST['subcategory_id'],
                    $_POST['brand_name'],
                    $_POST['model_name'],
                    $_POST['serial_number'],
                    $_POST['purchase_date'],
                    $_POST['warranty_expiry_date'],
                    $_POST['purchase_price'],
                    $_POST['supplier_name'],
                    $new_equipment_status,
                    $_POST['location_school'],
                    $_POST['location_building'],
                    $_POST['location_floor'],
                    $_POST['location_room'],
                    $_POST['responsible_person'],
                    $_POST['notes'],
                    $image_filename,
                    $_POST['equipment_id']
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
$filter_equipment_status = isset($_GET['equipment_status']) ? $_GET['equipment_status'] : '';
$records_per_page = isset($_GET['per_page']) ? intval($_GET['per_page']) : 20;

// สร้างเงื่อนไขการค้นหา
$where_conditions = [];
$params = [];

if (!empty($search)) {
    $where_conditions[] = "(e.equipment_code LIKE ? OR e.equipment_name LIKE ? OR ec.category_name LIKE ? OR es.subcategory_name LIKE ? OR e.location_school LIKE ? OR e.location_building LIKE ? OR e.location_room LIKE ?)";
    $search_param = "%$search%";
    array_push($params, $search_param, $search_param, $search_param, $search_param, $search_param, $search_param, $search_param);
}

if (!empty($filter_equipment_status)) {
    $where_conditions[] = "e.equipment_status = ?";
    $params[] = $filter_equipment_status;
}

$where_clause = '';
if (!empty($where_conditions)) {
    $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
}

// นับจำนวนข้อมูลทั้งหมดสำหรับ pagination
$count_query = "SELECT COUNT(*) as total FROM equipment e 
    LEFT JOIN equipment_categories ec ON e.category_id = ec.category_id 
    LEFT JOIN equipment_subcategories es ON e.subcategory_id = es.subcategory_id 
    $where_clause";
$count_stmt = $db->prepare($count_query);
$count_stmt->execute($params);
$total_records = $count_stmt->fetch(PDO::FETCH_ASSOC)['total'];

// ตั้งค่า pagination
$total_pages = ceil($total_records / $records_per_page);
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($current_page - 1) * $records_per_page;

// Get equipment list with pagination and search - แก้ไข query ใช้ maintenance_requests
$equipment_query = "SELECT e.*, ec.category_name, es.subcategory_name,
                           (SELECT mr.repair_status FROM maintenance_requests mr 
                            WHERE mr.equipment_id = e.equipment_id 
                            ORDER BY mr.created_at DESC 
                            LIMIT 1) as repair_status
                    FROM equipment e 
                    LEFT JOIN equipment_categories ec ON e.category_id = ec.category_id 
                    LEFT JOIN equipment_subcategories es ON e.subcategory_id = es.subcategory_id 
                    $where_clause 
                    ORDER BY e.created_at DESC 
                    LIMIT $offset, $records_per_page";

$stmt = $db->prepare($equipment_query);
$stmt->execute($params);
$equipment_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get categories for dropdown
$categories = $db->query("SELECT * FROM equipment_categories ORDER BY category_name")->fetchAll(PDO::FETCH_ASSOC);

// สร้าง URL สำหรับ pagination
function getPageUrl($page, $search, $filter_equipment_status, $per_page) {
    $params = ['page' => $page];
    if (!empty($search)) {
        $params['search'] = $search;
    }
    if (!empty($filter_equipment_status)) {
        $params['equipment_status'] = $filter_equipment_status;
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
                    
                    <div class="col-md-4">
                        <div class="input-group">
                            <input type="text" class="form-control" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="กรอกรหัสครุภัณฑ์, ชื่อ, หมวดหมู่, ตึก, ห้อง...">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label fw-bold">สถานะครุภัณฑ์</label>
                        <select class="form-control" name="equipment_status" onchange="this.form.submit()">
                            <option value="">-- ทุกสถานะ --</option>
                            <option value="ใหม่" <?php echo ($filter_equipment_status == 'ใหม่') ? 'selected' : ''; ?>>ใหม่</option>
                            <option value="ใช้งานปกติ" <?php echo ($filter_equipment_status == 'ใช้งานปกติ') ? 'selected' : ''; ?>>ใช้งานปกติ</option>
                            <option value="ชำรุด" <?php echo ($filter_equipment_status == 'ชำรุด') ? 'selected' : ''; ?>>ชำรุด</option>
                            <option value="กำลังซ่อม" <?php echo ($filter_equipment_status == 'กำลังซ่อม') ? 'selected' : ''; ?>>กำลังซ่อม</option>
                            <option value="ซ่อมเสร็จแล้ว" <?php echo ($filter_equipment_status == 'ซ่อมเสร็จแล้ว') ? 'selected' : ''; ?>>ซ่อมเสร็จแล้ว</option>
                            <option value="จำหน่ายแล้ว" <?php echo ($filter_equipment_status == 'จำหน่ายแล้ว') ? 'selected' : ''; ?>>จำหน่ายแล้ว</option>
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
                        <?php if (!empty($search) || !empty($filter_equipment_status) || $records_per_page != 20): ?>
                            <a href="equipment.php" class="btn btn-secondary w-100" title="ล้างการค้นหา">
                                <i class="fas fa-redo"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>

            <!-- สถิติการค้นหา -->
            <?php if (!empty($search) || !empty($filter_equipment_status)): ?>
            <div class="alert alert-info mb-3">
                <i class="fas fa-info-circle"></i> 
                พบข้อมูลครุภัณฑ์ทั้งหมด <strong><?php echo number_format($total_records); ?></strong> รายการ
                <?php if (!empty($search)): ?>
                    | คำค้นหา: "<strong><?php echo htmlspecialchars($search); ?></strong>"
                <?php endif; ?>
                <?php if (!empty($filter_equipment_status)): ?>
                    | สถานะ: "<strong><?php echo $filter_equipment_status; ?></strong>"
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
                            <th width="18%">ชื่อครุภัณฑ์</th>
                            <th width="12%">หมวดหมู่</th>
                            <th width="12%">หมวดหมู่ย่อย</th>
                            <th width="10%">สถานะครุภัณฑ์</th>
                            <th width="10%">สถานะซ่อม</th>
                            <th width="12%">ตำแหน่งที่ตั้ง</th>
                            <th width="14%">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($equipment_list) > 0): ?>
                            <?php foreach($equipment_list as $equipment): ?>
                            <tr>
                                <td class="fw-bold text-primary"><?php echo $equipment['equipment_code']; ?></td>
                                <td><?php echo $equipment['equipment_name']; ?></td>
                                <td>
                                    <span class="badge bg-secondary"><?php echo $equipment['category_name']; ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark"><?php echo $equipment['subcategory_name']; ?></span>
                                </td>
                                <td>
                                    <?php 
                                    $equipment_status_badge = [
                                        'ใหม่' => 'success',
                                        'ใช้งานปกติ' => 'primary',
                                        'ชำรุด' => 'warning',
                                        'กำลังซ่อม' => 'info',
                                        'ซ่อมเสร็จแล้ว' => 'success',
                                        'จำหน่ายแล้ว' => 'danger'
                                    ];
                                    ?>
                                    <span class="badge bg-<?php echo $equipment_status_badge[$equipment['equipment_status']] ?? 'secondary'; ?>">
                                        <?php echo $equipment['equipment_status']; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php 
                                    $repair_status_badge = [
                                        'รอซ่อม' => 'warning',
                                        'กำลังดำเนินการ' => 'info',
                                        'ซ่อมเสร็จ' => 'success',
                                        'ยกเลิก' => 'danger'
                                    ];
                                    $repair_status = $equipment['repair_status'];
                                    $maintenance_class = $repair_status_badge[$repair_status] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-<?php echo $maintenance_class; ?>">
                                        <?php echo $repair_status ?: 'ไม่มี'; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php 
                                    $location_parts = [];
                                    if (!empty($equipment['location_building'])) $location_parts[] = $equipment['location_building'];
                                    if (!empty($equipment['location_floor'])) $location_parts[] = 'ชั้น ' . $equipment['location_floor'];
                                    if (!empty($equipment['location_room'])) $location_parts[] = $equipment['location_room'];
                                    echo implode(' / ', $location_parts) ?: '-';
                                    ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#viewModal" onclick='viewEquipment(<?php echo json_encode($equipment); ?>)' title="ดูรายละเอียด">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#equipmentModal" onclick='editEquipment(<?php echo json_encode($equipment); ?>)' title="แก้ไข">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="equipment.php?action=delete&id=<?php echo $equipment['equipment_id']; ?><?php echo !empty($search) ? '&search='.urlencode($search) : ''; ?><?php echo !empty($filter_equipment_status) ? '&equipment_status='.$filter_equipment_status : ''; ?><?php echo ($records_per_page != 20) ? '&per_page='.$records_per_page : ''; ?><?php echo ($current_page > 1) ? '&page='.$current_page : ''; ?>" class="btn btn-danger" onclick="return confirm('คุณแน่ใจหรือไม่ที่จะลบครุภัณฑ์นี้?')" title="ลบ">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">ไม่พบข้อมูลครุภัณฑ์</h5>
                                    <?php if (!empty($search) || !empty($filter_equipment_status)): ?>
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
                            <a class="page-link" href="<?php echo getPageUrl(1, $search, $filter_equipment_status, $records_per_page); ?>" aria-label="First">
                                <span aria-hidden="true">&laquo;&laquo;</span>
                            </a>
                        </li>
                        
                        <li class="page-item <?php echo $current_page == 1 ? 'disabled' : ''; ?>">
                            <a class="page-link" href="<?php echo getPageUrl($current_page - 1, $search, $filter_equipment_status, $records_per_page); ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>

                        <?php
                        $start_page = max(1, $current_page - 2);
                        $end_page = min($total_pages, $current_page + 2);
                        
                        if ($start_page > 1) {
                            echo '<li class="page-item"><a class="page-link" href="' . getPageUrl(1, $search, $filter_equipment_status, $records_per_page) . '">1</a></li>';
                            if ($start_page > 2) {
                                echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                            }
                        }
                        
                        for ($i = $start_page; $i <= $end_page; $i++) {
                            $active = $i == $current_page ? 'active' : '';
                            echo '<li class="page-item ' . $active . '"><a class="page-link" href="' . getPageUrl($i, $search, $filter_equipment_status, $records_per_page) . '">' . $i . '</a></li>';
                        }
                        
                        if ($end_page < $total_pages) {
                            if ($end_page < $total_pages - 1) {
                                echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                            }
                            echo '<li class="page-item"><a class="page-link" href="' . getPageUrl($total_pages, $search, $filter_equipment_status, $records_per_page) . '">' . $total_pages . '</a></li>';
                        }
                        ?>

                        <li class="page-item <?php echo $current_page == $total_pages ? 'disabled' : ''; ?>">
                            <a class="page-link" href="<?php echo getPageUrl($current_page + 1, $search, $filter_equipment_status, $records_per_page); ?>" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                        
                        <li class="page-item <?php echo $current_page == $total_pages ? 'disabled' : ''; ?>">
                            <a class="page-link" href="<?php echo getPageUrl($total_pages, $search, $filter_equipment_status, $records_per_page); ?>" aria-label="Last">
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
                    <input type="hidden" name="equipment_id" id="equipment_id">
                    <input type="hidden" name="current_equipment_image" id="current_equipment_image">
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">รหัสครุภัณฑ์ *</label>
                                    <input type="text" class="form-control" name="equipment_code" id="equipment_code" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">ชื่อครุภัณฑ์ *</label>
                                    <input type="text" class="form-control" name="equipment_name" id="equipment_name" required>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">หมวดหมู่หลัก *</label>
                                    <select class="form-control" name="category_id" id="category_id" required onchange="loadSubcategories(this.value)">
                                        <option value="">เลือกหมวดหมู่หลัก</option>
                                        <?php foreach($categories as $category): ?>
                                        <option value="<?php echo $category['category_id']; ?>">
                                            <?php echo $category['category_name']; ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">หมวดหมู่ย่อย</label>
                                    <select class="form-control" name="subcategory_id" id="subcategory_id">
                                        <option value="">เลือกหมวดหมู่ย่อย</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">ยี่ห้อ</label>
                                    <input type="text" class="form-control" name="brand_name" id="brand_name">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">รุ่น</label>
                                    <input type="text" class="form-control" name="model_name" id="model_name">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">หมายเลขซีเรียล</label>
                                    <input type="text" class="form-control" name="serial_number" id="serial_number">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">วันที่จัดซื้อ</label>
                                    <input type="date" class="form-control" name="purchase_date" id="purchase_date">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">วันที่หมดประกัน</label>
                                    <input type="date" class="form-control" name="warranty_expiry_date" id="warranty_expiry_date">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">ราคาจัดซื้อ (บาท)</label>
                                    <input type="number" step="0.01" class="form-control" name="purchase_price" id="purchase_price">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">ผู้จัดจำหน่าย</label>
                                    <input type="text" class="form-control" name="supplier_name" id="supplier_name">
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
                                        <option value="ใหม่">ใหม่</option>
                                        <option value="ใช้งานปกติ" selected>ใช้งานปกติ</option>
                                        <option value="ชำรุด">ชำรุด</option>
                                        <option value="กำลังซ่อม">กำลังซ่อม</option>
                                        <option value="ซ่อมเสร็จแล้ว">ซ่อมเสร็จแล้ว</option>
                                        <option value="จำหน่ายแล้ว">จำหน่ายแล้ว</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">โรงเรียน</label>
                                    <input type="text" class="form-control" name="location_school" id="location_school">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">ตึก/อาคาร</label>
                                    <input type="text" class="form-control" name="location_building" id="location_building">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">ชั้น</label>
                                    <input type="text" class="form-control" name="location_floor" id="location_floor">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">ห้อง</label>
                                    <input type="text" class="form-control" name="location_room" id="location_room">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">หมายเหตุ</label>
                                <textarea class="form-control" name="notes" id="notes" rows="3"></textarea>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">รูปภาพครุภัณฑ์</label>
                                <input type="file" class="form-control" name="equipment_image" id="equipment_image" accept="image/*" onchange="previewImage(event)">
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
                    <button type="submit" class="btn btn-primary" name="add_equipment" id="submitButton">บันทึกข้อมูล</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Modal -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel">รายละเอียดครุภัณฑ์</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="viewModalBody">
                <!-- ข้อมูลจะถูกเติมด้วย JavaScript -->
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
    document.getElementById('current_equipment_image').value = '';
    document.getElementById('submitButton').name = 'add_equipment';
    document.getElementById('equipmentModalLabel').innerText = 'เพิ่มครุภัณฑ์';
    document.getElementById('imagePreview').style.display = 'none';
    document.getElementById('noImagePlaceholder').style.display = 'block';
    document.getElementById('subcategory_id').innerHTML = '<option value="">เลือกหมวดหมู่ย่อย</option>';
}

function editEquipment(equipment) {
    document.getElementById('equipmentModalLabel').innerText = 'แก้ไขครุภัณฑ์';
    document.getElementById('submitButton').name = 'edit_equipment';
    
    // เติมข้อมูลในฟอร์ม
    document.getElementById('equipment_id').value = equipment.equipment_id;
    document.getElementById('equipment_code').value = equipment.equipment_code;
    document.getElementById('equipment_name').value = equipment.equipment_name;
    document.getElementById('category_id').value = equipment.category_id;
    document.getElementById('brand_name').value = equipment.brand_name || '';
    document.getElementById('model_name').value = equipment.model_name || '';
    document.getElementById('serial_number').value = equipment.serial_number || '';
    document.getElementById('purchase_date').value = equipment.purchase_date || '';
    document.getElementById('warranty_expiry_date').value = equipment.warranty_expiry_date || '';
    document.getElementById('purchase_price').value = equipment.purchase_price || '';
    document.getElementById('supplier_name').value = equipment.supplier_name || '';
    document.getElementById('equipment_status').value = equipment.equipment_status;
    document.getElementById('location_school').value = equipment.location_school || '';
    document.getElementById('location_building').value = equipment.location_building || '';
    document.getElementById('location_floor').value = equipment.location_floor || '';
    document.getElementById('location_room').value = equipment.location_room || '';
    document.getElementById('responsible_person').value = equipment.responsible_person || '';
    document.getElementById('notes').value = equipment.notes || '';
    document.getElementById('current_equipment_image').value = equipment.image_path || '';
    
    // โหลดหมวดหมู่ย่อย
    if (equipment.category_id) {
        loadSubcategories(equipment.category_id, equipment.subcategory_id);
    }
    
    // แสดงรูปภาพ
    if (equipment.image_path) {
        document.getElementById('imagePreview').src = 'uploads/img_equipment/' + equipment.image_path;
        document.getElementById('imagePreview').style.display = 'block';
        document.getElementById('noImagePlaceholder').style.display = 'none';
    } else {
        document.getElementById('imagePreview').style.display = 'none';
        document.getElementById('noImagePlaceholder').style.display = 'block';
    }
}

function loadSubcategories(categoryId, selectedSubcategoryId = null) {
    if (!categoryId) {
        document.getElementById('subcategory_id').innerHTML = '<option value="">เลือกหมวดหมู่ย่อย</option>';
        return;
    }
    
    fetch('get_subcategories.php?category_id=' + categoryId)
        .then(response => response.json())
        .then(data => {
            const subcategorySelect = document.getElementById('subcategory_id');
            subcategorySelect.innerHTML = '<option value="">เลือกหมวดหมู่ย่อย</option>';
            
            data.forEach(subcategory => {
                const option = document.createElement('option');
                option.value = subcategory.subcategory_id;
                option.textContent = subcategory.subcategory_name;
                if (selectedSubcategoryId && subcategory.subcategory_id == selectedSubcategoryId) {
                    option.selected = true;
                }
                subcategorySelect.appendChild(option);
            });
        })
        .catch(error => console.error('Error:', error));
}

function previewImage(event) {
    const input = event.target;
    const preview = document.getElementById('imagePreview');
    const placeholder = document.getElementById('noImagePlaceholder');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
            placeholder.style.display = 'none';
        }
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.style.display = 'none';
        placeholder.style.display = 'block';
    }
}

function viewEquipment(equipment) {
    const modalBody = document.getElementById('viewModalBody');
    
    let imageHtml = '';
    if (equipment.image_path) {
        imageHtml = `
            <div class="text-center mb-4">
                <img src="uploads/img_equipment/${equipment.image_path}" alt="รูปภาพครุภัณฑ์" class="img-fluid rounded" style="max-height: 300px;">
            </div>
        `;
    }
    
    modalBody.innerHTML = `
        ${imageHtml}
        <div class="row">
            <div class="col-md-6">
                <h6 class="fw-bold">ข้อมูลพื้นฐาน</h6>
                <table class="table table-sm">
                    <tr><td width="40%"><strong>รหัสครุภัณฑ์:</strong></td><td>${equipment.equipment_code}</td></tr>
                    <tr><td><strong>ชื่อครุภัณฑ์:</strong></td><td>${equipment.equipment_name}</td></tr>
                    <tr><td><strong>หมวดหมู่:</strong></td><td>${equipment.category_name || '-'}</td></tr>
                    <tr><td><strong>หมวดหมู่ย่อย:</strong></td><td>${equipment.subcategory_name || '-'}</td></tr>
                    <tr><td><strong>ยี่ห้อ/รุ่น:</strong></td><td>${(equipment.brand_name || '') + ' ' + (equipment.model_name || '') || '-'}</td></tr>
                    <tr><td><strong>หมายเลขซีเรียล:</strong></td><td>${equipment.serial_number || '-'}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6 class="fw-bold">ข้อมูลการจัดซื้อ</h6>
                <table class="table table-sm">
                    <tr><td width="40%"><strong>วันที่จัดซื้อ:</strong></td><td>${equipment.purchase_date ? new Date(equipment.purchase_date).toLocaleDateString('th-TH') : '-'}</td></tr>
                    <tr><td><strong>วันที่หมดประกัน:</strong></td><td>${equipment.warranty_expiry_date ? new Date(equipment.warranty_expiry_date).toLocaleDateString('th-TH') : '-'}</td></tr>
                    <tr><td><strong>ราคาจัดซื้อ:</strong></td><td>${equipment.purchase_price ? parseFloat(equipment.purchase_price).toLocaleString('th-TH', {minimumFractionDigits: 2}) + ' บาท' : '-'}</td></tr>
                    <tr><td><strong>ผู้จัดจำหน่าย:</strong></td><td>${equipment.supplier_name || '-'}</td></tr>
                </table>
            </div>
        </div>
        
        <div class="row mt-3">
            <div class="col-md-6">
                <h6 class="fw-bold">สถานะและตำแหน่ง</h6>
                <table class="table table-sm">
                    <tr><td width="40%"><strong>สถานะ:</strong></td><td><span class="badge bg-primary">${equipment.equipment_status}</span></td></tr>
                    <tr><td><strong>โรงเรียน:</strong></td><td>${equipment.location_school || '-'}</td></tr>
                    <tr><td><strong>ตึก/อาคาร:</strong></td><td>${equipment.location_building || '-'}</td></tr>
                    <tr><td><strong>ชั้น:</strong></td><td>${equipment.location_floor || '-'}</td></tr>
                    <tr><td><strong>ห้อง:</strong></td><td>${equipment.location_room || '-'}</td></tr>
                    <tr><td><strong>ผู้รับผิดชอบ:</strong></td><td>${equipment.responsible_person || '-'}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6 class="fw-bold">ข้อมูลเพิ่มเติม</h6>
                <table class="table table-sm">
                    <tr><td width="40%"><strong>วันที่สร้าง:</strong></td><td>${new Date(equipment.created_at).toLocaleDateString('th-TH', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' })}</td></tr>
                    <tr><td><strong>วันที่แก้ไขล่าสุด:</strong></td><td>${new Date(equipment.updated_at).toLocaleDateString('th-TH', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' })}</td></tr>
                    <tr><td><strong>หมายเหตุ:</strong></td><td>${equipment.notes || '-'}</td></tr>
                </table>
            </div>
        </div>
    `;
}
</script>

<?php
require_once 'includes/footer.php';
?>