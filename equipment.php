<?php
// เริ่ม session และ error reporting
require_once 'includes/header.php';

// ตรวจสอบสิทธิ์การเข้าถึง
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'teacher')) {
    $_SESSION['error'] = "คุณไม่มีสิทธิ์เข้าถึงหน้านี้";
    header('Location: index.php');
    exit;
}

// CRUD Operations
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $id = isset($_GET['id']) ? $_GET['id'] : null;
    
    if ($action == 'delete' && $id) {
        try {
            // Delete image file if exists
            $stmt = $db->prepare("SELECT image_path FROM equipment WHERE id = ?");
            $stmt->execute([$id]);
            $equipment = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($equipment && $equipment['image_path']) {
                $image_path = 'uploads/img_equipment/' . $equipment['image_path'];
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }
            
            $stmt = $db->prepare("DELETE FROM equipment WHERE id = ?");
            $stmt->execute([$id]);
            $_SESSION['success'] = "ลบข้อมูลครุภัณฑ์เรียบร้อยแล้ว";
        } catch (PDOException $e) {
            $_SESSION['error'] = "เกิดข้อผิดพลาดในการลบข้อมูล: " . $e->getMessage();
        }
        header("Location: equipment.php");
        exit();
    }
}

if ($_POST) {
    if (isset($_POST['add_equipment'])) {
        try {
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
        } catch (PDOException $e) {
            $_SESSION['error'] = "เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . $e->getMessage();
        }
    }
    
    if (isset($_POST['edit_equipment'])) {
        try {
            // Get old status before update
            $old_equipment_status_stmt = $db->prepare("SELECT equipment_status FROM equipment WHERE id = ?");
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
                $stmt = $db->prepare("UPDATE equipment SET equipment_code=?, equipment_name=?, category_id=?, subcategory_id=?, brand_name=?, model_name=?, serial_number=?, purchase_date=?, warranty_expiry_date=?, purchase_price=?, supplier_name=?, equipment_status=?, location_school=?, location_building=?, location_floor=?, location_room=?, responsible_person=?, notes=?, image_path=? WHERE id=?");
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

// นับจำนวนครุภัณฑ์ตามสถานะ
try {
    $status_counts = $db->query("
        SELECT equipment_status, COUNT(*) as count 
        FROM equipment 
        GROUP BY equipment_status
    ")->fetchAll(PDO::FETCH_KEY_PAIR);
} catch (PDOException $e) {
    $status_counts = [];
}

// นับจำนวนครุภัณฑ์ทั้งหมด
$total_equipment = array_sum($status_counts);

// รับค่าการค้นหาและกรองข้อมูล
$search = isset($_GET['search']) ? $_GET['search'] : '';
$filter_equipment_status = isset($_GET['equipment_status']) ? $_GET['equipment_status'] : '';
$records_per_page = 20; // กำหนดคงที่ 20 รายการต่อหน้า

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
try {
    // แก้ไข JOIN ตามโครงสร้างฐานข้อมูลใหม่
    $count_query = "SELECT COUNT(*) as total FROM equipment e 
        LEFT JOIN equipment_categories ec ON e.category_id = ec.id 
        LEFT JOIN equipment_subcategories es ON e.subcategory_id = es.id 
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

// Get equipment list with pagination and search
try {
    // แก้ไข JOIN ตามโครงสร้างฐานข้อมูลใหม่
    $equipment_query = "SELECT e.*, ec.category_name, es.subcategory_name,
                               (SELECT mr.repair_status FROM maintenance_requests mr 
                                WHERE mr.equipment_id = e.id 
                                ORDER BY mr.created_at DESC 
                                LIMIT 1) as repair_status
                        FROM equipment e 
                        LEFT JOIN equipment_categories ec ON e.category_id = ec.id 
                        LEFT JOIN equipment_subcategories es ON e.subcategory_id = es.id 
                        $where_clause 
                        ORDER BY e.created_at DESC 
                        LIMIT $offset, $records_per_page";

    $stmt = $db->prepare($equipment_query);
    $stmt->execute($params);
    $equipment_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $equipment_list = [];
    $_SESSION['error'] = "เกิดข้อผิดพลาดในการโหลดข้อมูล: " . $e->getMessage();
}

// Get categories for dropdown
try {
    $categories = $db->query("SELECT * FROM equipment_categories ORDER BY category_name")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $categories = [];
    $_SESSION['error'] = "เกิดข้อผิดพลาดในการโหลดหมวดหมู่: " . $e->getMessage();
}

// Get all subcategories for JavaScript
try {
    $subcategories_all = $db->query("SELECT * FROM equipment_subcategories ORDER BY subcategory_name")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $subcategories_all = [];
}



// สร้าง URL สำหรับ pagination (ต้องประกาศก่อนใช้งาน)
function getPageUrl($page, $search, $filter_equipment_status) {
    $params = ['page' => $page];
    if (!empty($search)) {
        $params['search'] = $search;
    }
    if (!empty($filter_equipment_status)) {
        $params['equipment_status'] = $filter_equipment_status;
    }
    return 'equipment.php?' . http_build_query($params);
}

// ตรวจสอบว่าเป็น AJAX request
if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
    // ส่งเฉพาะ HTML ของตารางและ pagination
    ob_start();
    ?>
    
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
                            <span class="badge bg-secondary"><?php echo $equipment['category_name'] ?? '-'; ?></span>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark"><?php echo $equipment['subcategory_name'] ?? '-'; ?></span>
                        </td>
                        <td>
                            <?php 
                            $equipment_status_badge = [
                                'ใหม่' => 'success',
                                'ใช้งานปกติ' => 'primary',
                                'ชำรุด' => 'warning',
                                'กำลังซ่อม' => 'info',
                                'ซ่อมเสร็จแล้ว' => 'success',
                                'จำหน่ายแล้ว' => 'dark'
                            ];
                            $status_class = $equipment_status_badge[$equipment['equipment_status']] ?? 'secondary';
                            ?>
                            <span class="badge bg-<?php echo $status_class; ?>">
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
                                <a href="equipment.php?action=delete&id=<?php echo $equipment['id']; ?>" class="btn btn-danger" onclick="return confirm('คุณแน่ใจหรือไม่ที่จะลบครุภัณฑ์นี้?')" title="ลบ">
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
                    <a class="page-link" href="<?php echo getPageUrl(1, $search, $filter_equipment_status); ?>" aria-label="First">
                        <span aria-hidden="true">&laquo;&laquo;</span>
                    </a>
                </li>
                
                <li class="page-item <?php echo $current_page == 1 ? 'disabled' : ''; ?>">
                    <a class="page-link" href="<?php echo getPageUrl($current_page - 1, $search, $filter_equipment_status); ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>

                <?php
                $start_page = max(1, $current_page - 2);
                $end_page = min($total_pages, $current_page + 2);
                
                if ($start_page > 1) {
                    echo '<li class="page-item"><a class="page-link" href="' . getPageUrl(1, $search, $filter_equipment_status) . '">1</a></li>';
                    if ($start_page > 2) {
                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                    }
                }
                
                for ($i = $start_page; $i <= $end_page; $i++) {
                    $active = $i == $current_page ? 'active' : '';
                    echo '<li class="page-item ' . $active . '"><a class="page-link" href="' . getPageUrl($i, $search, $filter_equipment_status) . '">' . $i . '</a></li>';
                }
                
                if ($end_page < $total_pages) {
                    if ($end_page < $total_pages - 1) {
                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                    }
                    echo '<li class="page-item"><a class="page-link" href="' . getPageUrl($total_pages, $search, $filter_equipment_status) . '">' . $total_pages . '</a></li>';
                }
                ?>

                <li class="page-item <?php echo $current_page == $total_pages ? 'disabled' : ''; ?>">
                    <a class="page-link" href="<?php echo getPageUrl($current_page + 1, $search, $filter_equipment_status); ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
                
                <li class="page-item <?php echo $current_page == $total_pages ? 'disabled' : ''; ?>">
                    <a class="page-link" href="<?php echo getPageUrl($total_pages, $search, $filter_equipment_status); ?>" aria-label="Last">
                        <span aria-hidden="true">&raquo;&raquo;</span>
                    </a>
                </li>
            </ul>
        </div>
    </nav>
    <?php endif; ?>
    
    <?php
    $html = ob_get_clean();
    echo $html;
    exit;
}
?>

    <!-- Navbar -->
<?php include 'includes/navbar.php'; ?>
<?php 
// Include sidebar
include 'includes/sidebar.php';
?>
<div id="layoutSidenav_content">
<!-- Main Content -->
    <main >
    <div class="container-fluid px-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">จัดการครุภัณฑ์</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#equipmentModal" onclick="clearForm()">
            <i class="fas fa-plus"></i> เพิ่มครุภัณฑ์
        </button>
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

<!-- การ์ดแสดงสถิติครุภัณฑ์ -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card bg-primary text-white shadow h-100">
            <div class="card-body py-2">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-white text-uppercase mb-1">
                            ครุภัณฑ์ทั้งหมด</div>
                        <div class="h6 mb-0 font-weight-bold"><?php echo number_format($total_equipment); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-boxes fa-lg text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card bg-success text-white shadow h-100">
            <div class="card-body py-2">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-white text-uppercase mb-1">
                            ใช้งานปกติ</div>
                        <div class="h6 mb-0 font-weight-bold"><?php echo number_format($status_counts['ใช้งานปกติ'] ?? 0); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-lg text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card bg-warning text-white shadow h-100">
            <div class="card-body py-2">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-white text-uppercase mb-1">
                            กำลังซ่อม/ชำรุด</div>
                        <div class="h6 mb-0 font-weight-bold">
                            <?php echo number_format(($status_counts['กำลังซ่อม'] ?? 0) + ($status_counts['ชำรุด'] ?? 0)); ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-tools fa-lg text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card bg-danger text-white shadow h-100">
            <div class="card-body py-2">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-white text-uppercase mb-1">
                            จำหน่ายแล้ว</div>
                        <div class="h6 mb-0 font-weight-bold"><?php echo number_format($status_counts['จำหน่ายแล้ว'] ?? 0); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-archive fa-lg text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-dark">รายการครุภัณฑ์ทั้งหมด</h6>
            <div class="d-flex align-items-center">
                <span id="currentTime" class="text-muted small me-3"></span>
                <div class="btn-group">
                    <button type="button" class="btn btn-success btn-sm" onclick="exportToExcel()">
                        <i class="fas fa-file-excel"></i> Export
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="printTable()">
                        <i class="fas fa-print"></i> Print
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <!-- ตัวกรองข้อมูลแบบ Real-time -->
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
                                   placeholder="ค้นหาด้วยรหัส, ชื่อ, หมวดหมู่, ตึก, ห้อง..." 
                                   onkeyup="filterTable()">
                        </div>

                        <!-- Dropdown กรองสถานะครุภัณฑ์ -->
                        <div class="col-md-3">
                            <label class="form-label fw-bold">สถานะครุภัณฑ์</label>
                            <select class="form-control" id="equipmentStatusFilter" onchange="filterTable()">
                                <option value="">ทั้งหมด</option>
                                <option value="ใหม่">ใหม่</option>
                                <option value="ใช้งานปกติ">ใช้งานปกติ</option>
                                <option value="ชำรุด">ชำรุด</option>
                                <option value="กำลังซ่อม">กำลังซ่อม</option>
                                <option value="ซ่อมเสร็จแล้ว">ซ่อมเสร็จแล้ว</option>
                                <option value="จำหน่ายแล้ว">จำหน่ายแล้ว</option>
                            </select>
                        </div>

                        <!-- Dropdown กรองสถานะซ่อม -->
                        <div class="col-md-3">
                            <label class="form-label fw-bold">สถานะซ่อม</label>
                            <select class="form-control" id="repairStatusFilter" onchange="filterTable()">
                                <option value="">ทั้งหมด</option>
                                <option value="ไม่มี">ไม่มี</option>
                                <option value="รอซ่อม">รอซ่อม</option>
                                <option value="กำลังดำเนินการ">กำลังดำเนินการ</option>
                                <option value="ซ่อมเสร็จ">ซ่อมเสร็จ</option>
                                <option value="ยกเลิก">ยกเลิก</option>
                            </select>
                        </div>

                        <!-- Dropdown กรองโรงเรียน -->
                        <div class="col-md-2">
                            <label class="form-label fw-bold">โรงเรียน</label>
                            <select class="form-control" id="schoolFilter" onchange="filterTable()">
                                <option value="">ทั้งหมด</option>
                                <option value="โรงเรียนวารีเชียงใหม่">โรงเรียนวารีเชียงใหม่</option>
                                <option value="โรงเรียนอนุบาลวารีเชียงใหม่">โรงเรียนอนุบาลวารีเชียงใหม่</option>
                                <option value="โรงเรียนนานาชาติวารีเชียงใหม่">โรงเรียนนานาชาติวารีเชียงใหม่</option>
                            </select>
                        </div>

                        <!-- ปุ่มล้างตัวกรอง -->
                        <div class="col-md-12 d-flex align-items-end">
                            <button type="button" class="btn btn-outline-secondary w-100" onclick="clearFilters()">
                                <i class="fas fa-redo me-1"></i> ล้างตัวกรองทั้งหมด
                            </button>
                        </div>
                    </div>

                    <!-- นับจำนวนผลลัพธ์ -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="alert alert-info py-2 mb-0" id="resultCount">
                                <i class="fas fa-info-circle me-2"></i>
                                พบข้อมูลครุภัณฑ์ทั้งหมด <strong id="totalCount"><?php echo number_format($total_records); ?></strong> รายการ
                                | กำลังแสดง <strong id="showingCount"><?php echo number_format(count($equipment_list)); ?></strong> รายการ
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- แสดงข้อมูลการแบ่งหน้า -->
            <div id="paginationInfo" class="d-flex justify-content-between align-items-center mb-3">
                <div class="text-muted">
                    แสดง <?php echo number_format($offset + 1); ?>-<?php echo number_format(min($offset + $records_per_page, $total_records)); ?> 
                    จาก <?php echo number_format($total_records); ?> รายการ
                    <?php if ($total_pages > 1): ?>
                        | หน้า <?php echo $current_page; ?> จาก <?php echo $total_pages; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div id="equipmentTableContainer">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" width="100%" cellspacing="0" id="equipmentTable">
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
                        <tbody id="equipmentTableBody">
                            <?php if (count($equipment_list) > 0): ?>
                                <?php foreach($equipment_list as $equipment): ?>
                                <tr class="equipment-row" 
                                    data-code="<?php echo $equipment['equipment_code']; ?>"
                                    data-name="<?php echo htmlspecialchars($equipment['equipment_name']); ?>"
                                    data-category="<?php echo htmlspecialchars($equipment['category_name'] ?? ''); ?>"
                                    data-subcategory="<?php echo htmlspecialchars($equipment['subcategory_name'] ?? ''); ?>"
                                    data-status="<?php echo $equipment['equipment_status']; ?>"
                                    data-repair-status="<?php echo $equipment['repair_status'] ?: 'ไม่มี'; ?>"
                                    data-school="<?php echo htmlspecialchars($equipment['location_school'] ?? ''); ?>"
                                    data-building="<?php echo htmlspecialchars($equipment['location_building'] ?? ''); ?>"
                                    data-room="<?php echo htmlspecialchars($equipment['location_room'] ?? ''); ?>">
                                    <td class="fw-bold text-primary"><?php echo $equipment['equipment_code']; ?></td>
                                    <td><?php echo $equipment['equipment_name']; ?></td>
                                    <td>
                                        <span class="badge bg-secondary"><?php echo $equipment['category_name'] ?? '-'; ?></span>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark"><?php echo $equipment['subcategory_name'] ?? '-'; ?></span>
                                    </td>
                                    <td>
                                        <?php 
                                        $equipment_status_badge = [
                                            'ใหม่' => 'success',
                                            'ใช้งานปกติ' => 'primary',
                                            'ชำรุด' => 'warning',
                                            'กำลังซ่อม' => 'info',
                                            'ซ่อมเสร็จแล้ว' => 'success',
                                            'จำหน่ายแล้ว' => 'dark'
                                        ];
                                        $status_class = $equipment_status_badge[$equipment['equipment_status']] ?? 'secondary';
                                        ?>
                                        <span class="badge bg-<?php echo $status_class; ?>">
                                            <?php echo $equipment['equipment_status']; ?>
                                        </span>
                                        
                                        <?php 
                                        // ถ้าครุภัณฑ์ถูกจำหน่ายแล้ว ให้แสดงข้อมูลการจำหน่าย
                                        if ($equipment['equipment_status'] == 'จำหน่ายแล้ว') {
                                            // ดึงข้อมูลการจำหน่ายล่าสุด
                                            $disposal_query = "SELECT disposal_date, disposal_method 
                                                              FROM equipment_disposals 
                                                              WHERE equipment_id = ? 
                                                              ORDER BY disposal_date DESC 
                                                              LIMIT 1";
                                            $disposal_stmt = $db->prepare($disposal_query);
                                            $disposal_stmt->execute([$equipment['id']]);
                                            $disposal_info = $disposal_stmt->fetch(PDO::FETCH_ASSOC);
                                            
                                            if ($disposal_info) {
                                                echo '<br><small class="text-muted">';
                                                echo date('d/m/Y', strtotime($disposal_info['disposal_date']));
                                                echo ' | ' . $disposal_info['disposal_method'];
                                                echo '</small>';
                                            }
                                        }
                                        ?>
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
                                            <a href="equipment.php?action=delete&id=<?php echo $equipment['id']; ?>" class="btn btn-danger" onclick="return confirm('คุณแน่ใจหรือไม่ที่จะลบครุภัณฑ์นี้?')" title="ลบ">
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
                                        <?php if (!empty($search)): ?>
                                            <p class="text-muted">ลองเปลี่ยนคำค้นหาหรือล้างการค้นหา</p>
                                            <a href="equipment.php" class="btn btn-primary">แสดงครุภัณฑ์ทั้งหมด</a>
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
                                <a class="page-link" href="<?php echo getPageUrl(1, $search, $filter_equipment_status); ?>" aria-label="First">
                                    <span aria-hidden="true">&laquo;&laquo;</span>
                                </a>
                            </li>
                            
                            <li class="page-item <?php echo $current_page == 1 ? 'disabled' : ''; ?>">
                                <a class="page-link" href="<?php echo getPageUrl($current_page - 1, $search, $filter_equipment_status); ?>" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>

                            <?php
                            $start_page = max(1, $current_page - 2);
                            $end_page = min($total_pages, $current_page + 2);
                            
                            if ($start_page > 1) {
                                echo '<li class="page-item"><a class="page-link" href="' . getPageUrl(1, $search, $filter_equipment_status) . '">1</a></li>';
                                if ($start_page > 2) {
                                    echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                }
                            }
                            
                            for ($i = $start_page; $i <= $end_page; $i++) {
                                $active = $i == $current_page ? 'active' : '';
                                echo '<li class="page-item ' . $active . '"><a class="page-link" href="' . getPageUrl($i, $search, $filter_equipment_status) . '">' . $i . '</a></li>';
                            }
                            
                            if ($end_page < $total_pages) {
                                if ($end_page < $total_pages - 1) {
                                    echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                }
                                echo '<li class="page-item"><a class="page-link" href="' . getPageUrl($total_pages, $search, $filter_equipment_status) . '">' . $total_pages . '</a></li>';
                            }
                            ?>

                            <li class="page-item <?php echo $current_page == $total_pages ? 'disabled' : ''; ?>">
                                <a class="page-link" href="<?php echo getPageUrl($current_page + 1, $search, $filter_equipment_status); ?>" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                            
                            <li class="page-item <?php echo $current_page == $total_pages ? 'disabled' : ''; ?>">
                                <a class="page-link" href="<?php echo getPageUrl($total_pages, $search, $filter_equipment_status); ?>" aria-label="Last">
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

<!-- Modal สำหรับเพิ่ม/แก้ไขครุภัณฑ์ -->
<div class="modal fade" id="equipmentModal" tabindex="-1" aria-labelledby="equipmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data" id="equipmentForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="equipmentModalLabel">เพิ่มครุภัณฑ์</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="equipment_id" id="equipment_id">
                    <input type="hidden" name="current_equipment_image" id="current_equipment_image">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">รหัสครุภัณฑ์ <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="equipment_code" id="equipment_code" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">ชื่อครุภัณฑ์ <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="equipment_name" id="equipment_name" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">หมวดหมู่ <span class="text-danger">*</span></label>
                                <select class="form-control" name="category_id" id="category_id" required onchange="updateSubcategories()">
                                    <option value="">เลือกหมวดหมู่</option>
                                    <?php foreach($categories as $category): ?>
                                        <option value="<?php echo $category['id']; ?>"><?php echo $category['category_name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">หมวดหมู่ย่อย</label>
                                <select class="form-control" name="subcategory_id" id="subcategory_id">
                                    <option value="">เลือกหมวดหมู่ย่อย</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">ยี่ห้อ</label>
                                <input type="text" class="form-control" name="brand_name" id="brand_name">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">รุ่น</label>
                                <input type="text" class="form-control" name="model_name" id="model_name">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">หมายเลขซีเรียล</label>
                                <input type="text" class="form-control" name="serial_number" id="serial_number">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">สถานะครุภัณฑ์ <span class="text-danger">*</span></label>
                                <select class="form-control" name="equipment_status" id="equipment_status" required>
                                    <option value="ใหม่">ใหม่</option>
                                    <option value="ใช้งานปกติ">ใช้งานปกติ</option>
                                    <option value="ชำรุด">ชำรุด</option>
                                    <option value="กำลังซ่อม">กำลังซ่อม</option>
                                    <option value="ซ่อมเสร็จแล้ว">ซ่อมเสร็จแล้ว</option>
                                    <option value="จำหน่ายแล้ว">จำหน่ายแล้ว</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">วันที่ซื้อ</label>
                                <input type="date" class="form-control" name="purchase_date" id="purchase_date">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">วันที่หมดประกัน</label>
                                <input type="date" class="form-control" name="warranty_expiry_date" id="warranty_expiry_date">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">ราคาซื้อ</label>
                                <input type="number" step="0.01" class="form-control" name="purchase_price" id="purchase_price">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">ผู้จำหน่าย</label>
                                <input type="text" class="form-control" name="supplier_name" id="supplier_name">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">โรงเรียน</label>
                                <input type="text" class="form-control" name="location_school" id="location_school">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">ตึก/อาคาร</label>
                                <input type="text" class="form-control" name="location_building" id="location_building">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">ชั้น</label>
                                <input type="text" class="form-control" name="location_floor" id="location_floor">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">ห้อง</label>
                                <input type="text" class="form-control" name="location_room" id="location_room">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">ผู้รับผิดชอบ</label>
                                <input type="text" class="form-control" name="responsible_person" id="responsible_person">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">รูปภาพครุภัณฑ์</label>
                                <input type="file" class="form-control" name="equipment_image" id="equipment_image" accept="image/*">
                                <div class="form-text">รองรับไฟล์ JPG, JPEG, PNG, GIF, WEBP ขนาดไม่เกิน 5MB</div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">หมายเหตุ</label>
                        <textarea class="form-control" name="notes" id="notes" rows="3"></textarea>
                    </div>

                    <div id="imagePreview" class="mb-3 text-center"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                    <button type="submit" class="btn btn-primary" name="add_equipment" id="submitButton">บันทึก</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal สำหรับดูรายละเอียดครุภัณฑ์ -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel">รายละเอียดครุภัณฑ์</h5>
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

<?php include 'includes/footer.php'; ?>

<script>
// เก็บข้อมูลหมวดหมู่ย่อยทั้งหมด
const subcategories = <?php echo json_encode($subcategories_all); ?>;

function updateSubcategories() {
    const categoryId = document.getElementById('category_id').value;
    const subcategorySelect = document.getElementById('subcategory_id');
    
    // ล้างตัวเลือกทั้งหมดยกเว้นตัวเลือกแรก
    subcategorySelect.innerHTML = '<option value="">เลือกหมวดหมู่ย่อย</option>';
    
    if (categoryId) {
        // กรองหมวดหมู่ย่อยตามหมวดหมู่ที่เลือก
        const filteredSubcategories = subcategories.filter(sub => sub.category_id == categoryId);
        
        filteredSubcategories.forEach(sub => {
            const option = document.createElement('option');
            option.value = sub.id;
            option.textContent = sub.subcategory_name;
            subcategorySelect.appendChild(option);
        });
    }
}

function clearForm() {
    document.getElementById('equipmentForm').reset();
    document.getElementById('equipment_id').value = '';
    document.getElementById('current_equipment_image').value = '';
    document.getElementById('imagePreview').innerHTML = '';
    document.getElementById('equipmentModalLabel').textContent = 'เพิ่มครุภัณฑ์';
    document.getElementById('submitButton').name = 'add_equipment';
    document.getElementById('submitButton').textContent = 'บันทึก';
    
    // รีเซ็ตหมวดหมู่ย่อย
    document.getElementById('subcategory_id').innerHTML = '<option value="">เลือกหมวดหมู่ย่อย</option>';
}

function editEquipment(equipment) {
    document.getElementById('equipmentModalLabel').textContent = 'แก้ไขครุภัณฑ์';
    document.getElementById('submitButton').name = 'edit_equipment';
    document.getElementById('submitButton').textContent = 'อัพเดต';
    
    // ตั้งค่าฟิลด์ต่างๆ
    document.getElementById('equipment_id').value = equipment.id;
    document.getElementById('equipment_code').value = equipment.equipment_code;
    document.getElementById('equipment_name').value = equipment.equipment_name;
    document.getElementById('category_id').value = equipment.category_id;
    
    // อัพเดตหมวดหมู่ย่อยตามหมวดหมู่ที่เลือก
    updateSubcategories();
    
    // ตั้งค่าหมวดหมู่ย่อยหลังจากอัพเดตตัวเลือกแล้ว
    setTimeout(() => {
        document.getElementById('subcategory_id').value = equipment.subcategory_id;
    }, 100);
    
    document.getElementById('brand_name').value = equipment.brand_name || '';
    document.getElementById('model_name').value = equipment.model_name || '';
    document.getElementById('serial_number').value = equipment.serial_number || '';
    document.getElementById('equipment_status').value = equipment.equipment_status;
    document.getElementById('purchase_date').value = equipment.purchase_date || '';
    document.getElementById('warranty_expiry_date').value = equipment.warranty_expiry_date || '';
    document.getElementById('purchase_price').value = equipment.purchase_price || '';
    document.getElementById('supplier_name').value = equipment.supplier_name || '';
    document.getElementById('location_school').value = equipment.location_school || '';
    document.getElementById('location_building').value = equipment.location_building || '';
    document.getElementById('location_floor').value = equipment.location_floor || '';
    document.getElementById('location_room').value = equipment.location_room || '';
    document.getElementById('responsible_person').value = equipment.responsible_person || '';
    document.getElementById('notes').value = equipment.notes || '';
    document.getElementById('current_equipment_image').value = equipment.image_path || '';
    
    // แสดงรูปภาพถ้ามี
    const imagePreview = document.getElementById('imagePreview');
    imagePreview.innerHTML = '';
    if (equipment.image_path) {
        const img = document.createElement('img');
        img.src = 'uploads/img_equipment/' + equipment.image_path;
        img.alt = 'รูปครุภัณฑ์';
        img.style.maxWidth = '200px';
        img.style.maxHeight = '200px';
        img.className = 'img-thumbnail';
        imagePreview.appendChild(img);
    }
}

function viewEquipment(equipment) {
    const modalBody = document.getElementById('viewModalBody');
    
    let content = `
        <div class="row">
            <div class="col-md-4 text-center mb-3">
    `;
    
    if (equipment.image_path) {
        content += `
            <img src="uploads/img_equipment/${equipment.image_path}" 
                 alt="รูปครุภัณฑ์" 
                 class="img-fluid rounded shadow-sm"
                 style="max-height: 200px;">
        `;
    } else {
        content += `
            <div class="text-muted">
                <i class="fas fa-image fa-3x mb-2"></i>
                <br>ไม่มีรูปภาพ
            </div>
        `;
    }
    
    content += `
            </div>
            <div class="col-md-8">
                <table class="table table-bordered">
                    <tr>
                        <th width="40%">รหัสครุภัณฑ์</th>
                        <td>${equipment.equipment_code}</td>
                    </tr>
                    <tr>
                        <th>ชื่อครุภัณฑ์</th>
                        <td>${equipment.equipment_name}</td>
                    </tr>
                    <tr>
                        <th>หมวดหมู่</th>
                        <td>${equipment.category_name || '-'}</td>
                    </tr>
                    <tr>
                        <th>หมวดหมู่ย่อย</th>
                        <td>${equipment.subcategory_name || '-'}</td>
                    </tr>
                    <tr>
                        <th>ยี่ห้อ/รุ่น</th>
                        <td>${equipment.brand_name || '-'} / ${equipment.model_name || '-'}</td>
                    </tr>
                    <tr>
                        <th>หมายเลขซีเรียล</th>
                        <td>${equipment.serial_number || '-'}</td>
                    </tr>
                </table>
            </div>
        </div>
        
        <div class="row mt-3">
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr>
                        <th width="40%">สถานะครุภัณฑ์</th>
                        <td>
                            <span class="badge bg-${getStatusBadgeColor(equipment.equipment_status)}">
                                ${equipment.equipment_status}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>วันที่ซื้อ</th>
                        <td>${equipment.purchase_date ? formatDate(equipment.purchase_date) : '-'}</td>
                    </tr>
                    <tr>
                        <th>วันที่หมดประกัน</th>
                        <td>${equipment.warranty_expiry_date ? formatDate(equipment.warranty_expiry_date) : '-'}</td>
                    </tr>
                    <tr>
                        <th>ราคาซื้อ</th>
                        <td>${equipment.purchase_price ? formatCurrency(equipment.purchase_price) : '-'}</td>
                    </tr>
                    <tr>
                        <th>ผู้จำหน่าย</th>
                        <td>${equipment.supplier_name || '-'}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr>
                        <th width="40%">ตำแหน่งที่ตั้ง</th>
                        <td>
                            ${[equipment.location_building, equipment.location_floor ? 'ชั้น ' + equipment.location_floor : '', equipment.location_room].filter(Boolean).join(' / ') || '-'}
                        </td>
                    </tr>
                    <tr>
                        <th>โรงเรียน</th>
                        <td>${equipment.location_school || '-'}</td>
                    </tr>
                    <tr>
                        <th>ผู้รับผิดชอบ</th>
                        <td>${equipment.responsible_person || '-'}</td>
                    </tr>
                    <tr>
                        <th>สถานะซ่อม</th>
                        <td>
                            <span class="badge bg-${getRepairStatusBadgeColor(equipment.repair_status)}">
                                ${equipment.repair_status || 'ไม่มี'}
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    `;
    
    if (equipment.notes) {
        content += `
            <div class="row mt-3">
                <div class="col-12">
                    <strong>หมายเหตุ:</strong>
                    <div class="border p-2 rounded bg-light">${equipment.notes}</div>
                </div>
            </div>
        `;
    }
    
    modalBody.innerHTML = content;
}

function getStatusBadgeColor(status) {
    const colors = {
        'ใหม่': 'success',
        'ใช้งานปกติ': 'primary',
        'ชำรุด': 'warning',
        'กำลังซ่อม': 'info',
        'ซ่อมเสร็จแล้ว': 'success',
        'จำหน่ายแล้ว': 'dark'
    };
    return colors[status] || 'secondary';
}

function getRepairStatusBadgeColor(status) {
    const colors = {
        'รอซ่อม': 'warning',
        'กำลังดำเนินการ': 'info',
        'ซ่อมเสร็จ': 'success',
        'ยกเลิก': 'danger'
    };
    return colors[status] || 'secondary';
}

function formatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('th-TH');
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('th-TH', {
        style: 'currency',
        currency: 'THB'
    }).format(amount);
}

// แสดงตัวอย่างรูปภาพก่อนอัพโหลด
document.getElementById('equipment_image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('imagePreview');
    
    preview.innerHTML = '';
    
    if (file) {
        if (file.size > 5 * 1024 * 1024) {
            alert('ขนาดไฟล์ต้องไม่เกิน 5MB');
            e.target.value = '';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.alt = 'ตัวอย่างรูปภาพ';
            img.style.maxWidth = '200px';
            img.style.maxHeight = '200px';
            img.className = 'img-thumbnail';
            preview.appendChild(img);
        }
        reader.readAsDataURL(file);
    }
});

// ป้องกันการส่งฟอร์มถ้ามีข้อผิดพลาด
document.getElementById('equipmentForm').addEventListener('submit', function(e) {
    const equipmentCode = document.getElementById('equipment_code').value.trim();
    const equipmentName = document.getElementById('equipment_name').value.trim();
    const categoryId = document.getElementById('category_id').value;
    
    if (!equipmentCode || !equipmentName || !categoryId) {
        e.preventDefault();
        alert('กรุณากรอกข้อมูลที่จำเป็นให้ครบถ้วน');
        return;
    }
});

// ==================== ฟังก์ชันใหม่สำหรับการกรองข้อมูล ====================

// อัพเดตเวลาปัจจุบัน
function updateCurrentTime() {
    const now = new Date();
    const options = { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        hour: '2-digit', 
        minute: '2-digit', 
        second: '2-digit',
        timeZone: 'Asia/Bangkok'
    };
    const formatter = new Intl.DateTimeFormat('th-TH', options);
    document.getElementById('currentTime').textContent = 'อัพเดตล่าสุด: ' + formatter.format(now);
}

// เรียกอัพเดตเวลาทุก 1 นาที
setInterval(updateCurrentTime, 60000);
updateCurrentTime(); // เรียกครั้งแรก

// ฟังก์ชันกรองข้อมูลแบบ real-time
function filterTable() {
    const globalSearch = document.getElementById('globalSearch').value.toLowerCase();
    const equipmentStatus = document.getElementById('equipmentStatusFilter').value;
    const repairStatus = document.getElementById('repairStatusFilter').value;
    const school = document.getElementById('schoolFilter').value;
    
    const rows = document.querySelectorAll('#equipmentTableBody .equipment-row');
    let visibleCount = 0;
    
    rows.forEach(row => {
        const code = row.getAttribute('data-code').toLowerCase();
        const name = row.getAttribute('data-name').toLowerCase();
        const category = row.getAttribute('data-category').toLowerCase();
        const subcategory = row.getAttribute('data-subcategory').toLowerCase();
        const status = row.getAttribute('data-status');
        const repair = row.getAttribute('data-repair-status');
        const schoolData = row.getAttribute('data-school');
        const building = row.getAttribute('data-building').toLowerCase();
        const room = row.getAttribute('data-room').toLowerCase();
        
        let showRow = true;
        
        // กรองด้วยช่องค้นหาทั่วไป
        if (globalSearch) {
            const searchText = globalSearch;
            if (!code.includes(searchText) && 
                !name.includes(searchText) && 
                !category.includes(searchText) && 
                !subcategory.includes(searchText) &&
                !building.includes(searchText) &&
                !room.includes(searchText)) {
                showRow = false;
            }
        }
        
        // กรองด้วยสถานะครุภัณฑ์
        if (equipmentStatus && status !== equipmentStatus) {
            showRow = false;
        }
        
        // กรองด้วยสถานะซ่อม
        if (repairStatus && repair !== repairStatus) {
            showRow = false;
        }
        
        // กรองด้วยโรงเรียน
        if (school && schoolData !== school) {
            showRow = false;
        }
        
        // แสดง/ซ่อนแถว
        if (showRow) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    // อัพเดตจำนวนผลลัพธ์
    updateResultCount(visibleCount, rows.length);
    
    // แสดงข้อความเมื่อไม่พบผลลัพธ์
    showNoResultsMessage(visibleCount);
}

// อัพเดตจำนวนผลลัพธ์
function updateResultCount(visible, total) {
    const totalCount = document.getElementById('totalCount');
    const showingCount = document.getElementById('showingCount');
    
    totalCount.textContent = total.toLocaleString();
    showingCount.textContent = visible.toLocaleString();
    
    // เปลี่ยนสีข้อความตามจำนวนผลลัพธ์
    const resultCount = document.getElementById('resultCount');
    if (visible === 0) {
        resultCount.className = 'alert alert-danger py-2 mb-0';
    } else if (visible < total) {
        resultCount.className = 'alert alert-warning py-2 mb-0';
    } else {
        resultCount.className = 'alert alert-info py-2 mb-0';
    }
}

// แสดงข้อความเมื่อไม่พบผลลัพธ์
function showNoResultsMessage(visibleCount) {
    let noResultsRow = document.querySelector('#equipmentTableBody tr:not(.equipment-row)');
    
    if (visibleCount === 0) {
        if (!noResultsRow) {
            noResultsRow = document.createElement('tr');
            noResultsRow.innerHTML = `
                <td colspan="8" class="text-center py-4">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">ไม่พบข้อมูลครุภัณฑ์ที่ตรงกับเงื่อนไขการค้นหา</h5>
                    <p class="text-muted">ลองเปลี่ยนคำค้นหาหรือล้างตัวกรอง</p>
                    <button type="button" class="btn btn-primary" onclick="clearFilters()">
                        ล้างตัวกรองทั้งหมด
                    </button>
                </td>
            `;
            document.getElementById('equipmentTableBody').appendChild(noResultsRow);
        }
    } else if (noResultsRow) {
        noResultsRow.remove();
    }
}

// ล้างตัวกรองทั้งหมด
function clearFilters() {
    document.getElementById('globalSearch').value = '';
    document.getElementById('equipmentStatusFilter').value = '';
    document.getElementById('repairStatusFilter').value = '';
    document.getElementById('schoolFilter').value = '';
    
    filterTable();
}

// ส่งออกข้อมูลเป็น Excel
function exportToExcel() {
    const table = document.getElementById('equipmentTable');
    const rows = table.querySelectorAll('tbody tr:not([style*="display: none"])');
    
    if (rows.length === 0) {
        alert('ไม่มีข้อมูลที่จะส่งออก');
        return;
    }
    
    let csv = [];
    const headers = [];
    
    // เพิ่มหัวข้อภาษาไทย
    table.querySelectorAll('thead th').forEach(header => {
        headers.push(header.textContent.trim());
    });
    
    // เพิ่ม BOM สำหรับรองรับภาษาไทยใน Excel
    const BOM = "\uFEFF";
    csv.push(BOM + headers.join(','));
    
    // เพิ่มข้อมูล
    rows.forEach(row => {
        const rowData = [];
        row.querySelectorAll('td').forEach(cell => {
            let text = cell.textContent.trim();
            
            // ลบ HTML tags และจัดการกับเครื่องหมาย comma
            text = text.replace(/,/g, ';');
            text = text.replace(/\n/g, ' ');
            text = text.replace(/\r/g, ' ');
            
            // ลบ badge text และเอาเฉพาะข้อความสถานะ
            const badge = cell.querySelector('.badge');
            if (badge) {
                text = badge.textContent.trim();
            }
            
            // จัดการกับข้อมูลการจำหน่าย
            const smallText = cell.querySelector('small');
            if (smallText) {
                text += ' ' + smallText.textContent.trim();
            }
            
            // ใส่ใน quotes เพื่อป้องกันปัญหา comma
            rowData.push(`"${text}"`);
        });
        csv.push(rowData.join(','));
    });
    
    // สร้างไฟล์และดาวน์โหลด
    const csvString = csv.join('\n');
    const blob = new Blob([csvString], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    
    // ใช้ชื่อไฟล์ภาษาไทย
    const today = new Date();
    const dateString = today.toLocaleDateString('th-TH').replace(/\//g, '-');
    link.setAttribute('href', url);
    link.setAttribute('download', `รายงานครุภัณฑ์_${dateString}.csv`);
    link.style.visibility = 'hidden';
    
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// พิมพ์ตาราง
function printTable() {
    const table = document.getElementById('equipmentTable');
    const rows = table.querySelectorAll('tbody tr:not([style*="display: none"])');
    
    if (rows.length === 0) {
        alert('ไม่มีข้อมูลที่จะพิมพ์');
        return;
    }
    
    const printWindow = window.open('', '_blank');
    const currentDate = new Date().toLocaleDateString('th-TH');
    
    printWindow.document.write(`
        <html>
            <head>
                <title>รายงานครุภัณฑ์</title>
                <style>
                    body { font-family: 'Sarabun', sans-serif; margin: 20px; }
                    .print-header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
                    .print-header h2 { margin: 0; color: #333; }
                    .print-info { margin: 10px 0; font-size: 14px; }
                    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
                    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                    th { background-color: #f8f9fa; font-weight: bold; }
                    tr:nth-child(even) { background-color: #f8f9fa; }
                    .badge { padding: 4px 8px; border-radius: 4px; font-size: 12px; }
                    .no-print { display: none; }
                    @media print {
                        .no-print { display: none; }
                    }
                </style>
            </head>
            <body>
                <div class="print-header">
                    <h2>รายงานครุภัณฑ์</h2>
                    <div class="print-info">
                        <strong>วันที่พิมพ์:</strong> ${currentDate} | 
                        <strong>จำนวนรายการ:</strong> ${rows.length} รายการ
                    </div>
                </div>
                ${table.outerHTML}
                <script>
                    window.onload = function() {
                        window.print();
                        setTimeout(function() {
                            window.close();
                        }, 500);
                    }
                <\/script>
            </body>
        </html>
    `);
    
    printWindow.document.close();
}

// เรียกใช้ฟังก์ชันกรองเมื่อโหลดหน้าเว็บเสร็จ
document.addEventListener('DOMContentLoaded', function() {
    filterTable(); // กรองข้อมูลครั้งแรก
});
</script>