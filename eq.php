<?php
require_once 'includes/header.php';

// CRUD Operations
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $id = isset($_GET['id']) ? $_GET['id'] : null;
    
    if ($action == 'delete' && $id) {
        $stmt = $db->prepare("DELETE FROM equipment WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['success'] = "ลบข้อมูลครุภัณฑ์เรียบร้อยแล้ว";
        header("Location: equipment.php");
        exit();
    }
}

if ($_POST) {
    if (isset($_POST['add_equipment'])) {
        $stmt = $db->prepare("INSERT INTO equipment (code, name, category_id, category_item_id, brand, model, serial_number, purchase_date, purchase_price, department_id, responsible_person, status, specifications) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
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
            $_POST['specifications']
        ]);
        $_SESSION['success'] = "เพิ่มข้อมูลครุภัณฑ์เรียบร้อยแล้ว";
        header("Location: equipment.php");
        exit();
    }
    
    if (isset($_POST['edit_equipment'])) {
        $stmt = $db->prepare("UPDATE equipment SET code=?, name=?, category_id=?, category_item_id=?, brand=?, model=?, serial_number=?, purchase_date=?, purchase_price=?, department_id=?, responsible_person=?, status=?, specifications=? WHERE id=?");
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
            $_POST['id']
        ]);
        $_SESSION['success'] = "แก้ไขข้อมูลครุภัณฑ์เรียบร้อยแล้ว";
        header("Location: equipment.php");
        exit();
    }
}

// รับค่าการค้นหา
$search = isset($_GET['search']) ? $_GET['search'] : '';

// สร้างเงื่อนไขการค้นหา
$where_conditions = [];
$params = [];

if (!empty($search)) {
    $where_conditions[] = "e.code LIKE ?";
    $params[] = "%$search%";
}

$where_clause = '';
if (!empty($where_conditions)) {
    $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
}

// นับจำนวนข้อมูลทั้งหมดสำหรับ pagination
$count_query = "SELECT COUNT(*) as total FROM equipment e $where_clause";
$count_stmt = $db->prepare($count_query);
$count_stmt->execute($params);
$total_records = $count_stmt->fetch(PDO::FETCH_ASSOC)['total'];

// ตั้งค่า pagination
$records_per_page = 20;
$total_pages = ceil($total_records / $records_per_page);

// รับค่าหน้าปัจจุบัน
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($current_page - 1) * $records_per_page;

// รับค่าการเรียงลำดับ
$order_by = isset($_GET['order_by']) ? $_GET['order_by'] : 'e.created_at';
$order_dir = isset($_GET['order_dir']) ? $_GET['order_dir'] : 'DESC';

// ฟิลด์ที่อนุญาตให้เรียงลำดับได้
$allowed_order_fields = ['e.code', 'e.name', 'c.name', 'ci.name', 'd.name', 'e.status', 'e.purchase_date'];
if (!in_array($order_by, $allowed_order_fields)) {
    $order_by = 'e.created_at';
}
if (!in_array($order_dir, ['ASC', 'DESC'])) {
    $order_dir = 'DESC';
}

// Get equipment list with pagination and search
$equipment_query = "SELECT e.*, c.name as category_name, ci.name as item_name, d.name as department_name 
    FROM equipment e 
    LEFT JOIN categories c ON e.category_id = c.id 
    LEFT JOIN categories_items ci ON e.category_item_id = ci.id 
    LEFT JOIN departments d ON e.department_id = d.id 
    $where_clause 
    ORDER BY $order_by $order_dir 
    LIMIT $offset, $records_per_page";

$stmt = $db->prepare($equipment_query);
$stmt->execute($params);
$equipment_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get categories for dropdown
$categories = $db->query("SELECT * FROM categories ORDER BY code")->fetchAll(PDO::FETCH_ASSOC);

// Get departments for dropdown
$departments = $db->query("SELECT * FROM departments")->fetchAll(PDO::FETCH_ASSOC);

// Get equipment for edit
$edit_equipment = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $stmt = $db->prepare("SELECT * FROM equipment WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $edit_equipment = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Get categories_items for selected category
$categories_items = [];
if ($edit_equipment && $edit_equipment['category_id']) {
    // Get category code first
    $category_stmt = $db->prepare("SELECT code FROM categories WHERE id = ?");
    $category_stmt->execute([$edit_equipment['category_id']]);
    $category = $category_stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($category) {
        $items_stmt = $db->prepare("SELECT * FROM categories_items WHERE category_code = ? ORDER BY name");
        $items_stmt->execute([$category['code']]);
        $categories_items = $items_stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

// สร้าง URL สำหรับการเรียงลำดับ
function getSortUrl($field, $current_order_by, $current_order_dir, $search, $page) {
    $order_dir = ($current_order_by == $field && $current_order_dir == 'ASC') ? 'DESC' : 'ASC';
    $params = [
        'order_by' => $field,
        'order_dir' => $order_dir
    ];
    if (!empty($search)) {
        $params['search'] = $search;
    }
    if ($page > 1) {
        $params['page'] = $page;
    }
    return 'equipment.php?' . http_build_query($params);
}

// สร้าง URL สำหรับ pagination
function getPageUrl($page, $order_by, $order_dir, $search) {
    $params = ['page' => $page];
    if (!empty($order_by)) {
        $params['order_by'] = $order_by;
    }
    if (!empty($order_dir)) {
        $params['order_dir'] = $order_dir;
    }
    if (!empty($search)) {
        $params['search'] = $search;
    }
    return 'equipment.php?' . http_build_query($params);
}
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <?php include 'includes/navbar.php'; ?>

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">จัดการครุภัณฑ์</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#equipmentModal">
            <i class="fas fa-plus"></i> เพิ่มครุภัณฑ์
        </button>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- ช่องค้นหา -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">ค้นหาครุภัณฑ์</h6>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-8">
                    <input type="text" class="form-control" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="ค้นหาจากรหัสครุภัณฑ์...">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search"></i> ค้นหา
                    </button>
                    <?php if (!empty($search)): ?>
                        <a href="equipment.php" class="btn btn-secondary">ล้างการค้นหา</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <!-- สถิติการค้นหา -->
    <?php if (!empty($search)): ?>
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> 
        พบข้อมูลครุภัณฑ์ทั้งหมด <strong><?php echo number_format($total_records); ?></strong> รายการ 
        สำหรับคำค้นหา: "<strong><?php echo htmlspecialchars($search); ?></strong>"
    </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">รายการครุภัณฑ์ทั้งหมด</h6>
            <div class="text-muted">
                แสดง <?php echo number_format($offset + 1); ?>-<?php echo number_format(min($offset + $records_per_page, $total_records)); ?> 
                จาก <?php echo number_format($total_records); ?> รายการ
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th width="10%">
                                <a href="<?php echo getSortUrl('e.code', $order_by, $order_dir, $search, $current_page); ?>" class="text-decoration-none">
                                    รหัสครุภัณฑ์
                                    <?php if ($order_by == 'e.code'): ?>
                                        <i class="fas fa-sort-<?php echo strtolower($order_dir); ?>"></i>
                                    <?php else: ?>
                                        <i class="fas fa-sort text-muted"></i>
                                    <?php endif; ?>
                                </a>
                            </th>
                            <th width="15%">
                                <a href="<?php echo getSortUrl('e.name', $order_by, $order_dir, $search, $current_page); ?>" class="text-decoration-none">
                                    ชื่ออุปกรณ์
                                    <?php if ($order_by == 'e.name'): ?>
                                        <i class="fas fa-sort-<?php echo strtolower($order_dir); ?>"></i>
                                    <?php else: ?>
                                        <i class="fas fa-sort text-muted"></i>
                                    <?php endif; ?>
                                </a>
                            </th>
                            <th width="15%">
                                <a href="<?php echo getSortUrl('c.name', $order_by, $order_dir, $search, $current_page); ?>" class="text-decoration-none">
                                    หมวดหมู่
                                    <?php if ($order_by == 'c.name'): ?>
                                        <i class="fas fa-sort-<?php echo strtolower($order_dir); ?>"></i>
                                    <?php else: ?>
                                        <i class="fas fa-sort text-muted"></i>
                                    <?php endif; ?>
                                </a>
                            </th>
                            <th width="15%">
                                <a href="<?php echo getSortUrl('ci.name', $order_by, $order_dir, $search, $current_page); ?>" class="text-decoration-none">
                                    รายการอุปกรณ์
                                    <?php if ($order_by == 'ci.name'): ?>
                                        <i class="fas fa-sort-<?php echo strtolower($order_dir); ?>"></i>
                                    <?php else: ?>
                                        <i class="fas fa-sort text-muted"></i>
                                    <?php endif; ?>
                                </a>
                            </th>
                            <th width="15%">
                                <a href="<?php echo getSortUrl('d.name', $order_by, $order_dir, $search, $current_page); ?>" class="text-decoration-none">
                                    แผนก
                                    <?php if ($order_by == 'd.name'): ?>
                                        <i class="fas fa-sort-<?php echo strtolower($order_dir); ?>"></i>
                                    <?php else: ?>
                                        <i class="fas fa-sort text-muted"></i>
                                    <?php endif; ?>
                                </a>
                            </th>
                            <th width="10%">
                                <a href="<?php echo getSortUrl('e.status', $order_by, $order_dir, $search, $current_page); ?>" class="text-decoration-none">
                                    สถานะ
                                    <?php if ($order_by == 'e.status'): ?>
                                        <i class="fas fa-sort-<?php echo strtolower($order_dir); ?>"></i>
                                    <?php else: ?>
                                        <i class="fas fa-sort text-muted"></i>
                                    <?php endif; ?>
                                </a>
                            </th>
                            <th width="10%">
                                <a href="<?php echo getSortUrl('e.purchase_date', $order_by, $order_dir, $search, $current_page); ?>" class="text-decoration-none">
                                    วันที่จัดซื้อ
                                    <?php if ($order_by == 'e.purchase_date'): ?>
                                        <i class="fas fa-sort-<?php echo strtolower($order_dir); ?>"></i>
                                    <?php else: ?>
                                        <i class="fas fa-sort text-muted"></i>
                                    <?php endif; ?>
                                </a>
                            </th>
                            <th width="10%">จัดการ</th>
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
                                        'ใหม่' => 'success',
                                        'ใช้งานปกติ' => 'primary',
                                        'ชำรุด' => 'warning',
                                        'รอซ่อม' => 'info',
                                        'จำหน่ายแล้ว' => 'danger'
                                    ];
                                    ?>
                                    <span class="badge bg-<?php echo $status_badge[$equipment['status']]; ?>">
                                        <?php echo $equipment['status']; ?>
                                    </span>
                                </td>
                                <td><?php echo $equipment['purchase_date']; ?></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <!-- ปุ่มแก้ไขไม่ทำงาน -->
                                        <button type="button" class="btn btn-secondary" disabled title="ปุ่มแก้ไขไม่พร้อมใช้งาน">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="equipment.php?action=delete&id=<?php echo $equipment['id']; ?>" class="btn btn-danger" onclick="return confirm('คุณแน่ใจหรือไม่ที่จะลบครุภัณฑ์นี้?')">
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
                        <a class="page-link" href="<?php echo getPageUrl($current_page - 1, $order_by, $order_dir, $search); ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>

                    <!-- Page Numbers -->
                    <?php
                    $start_page = max(1, $current_page - 2);
                    $end_page = min($total_pages, $current_page + 2);
                    
                    if ($start_page > 1) {
                        echo '<li class="page-item"><a class="page-link" href="' . getPageUrl(1, $order_by, $order_dir, $search) . '">1</a></li>';
                        if ($start_page > 2) {
                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        }
                    }
                    
                    for ($i = $start_page; $i <= $end_page; $i++) {
                        $active = $i == $current_page ? 'active' : '';
                        echo '<li class="page-item ' . $active . '"><a class="page-link" href="' . getPageUrl($i, $order_by, $order_dir, $search) . '">' . $i . '</a></li>';
                    }
                    
                    if ($end_page < $total_pages) {
                        if ($end_page < $total_pages - 1) {
                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        }
                        echo '<li class="page-item"><a class="page-link" href="' . getPageUrl($total_pages, $order_by, $order_dir, $search) . '">' . $total_pages . '</a></li>';
                    }
                    ?>

                    <!-- Next Page -->
                    <li class="page-item <?php echo $current_page == $total_pages ? 'disabled' : ''; ?>">
                        <a class="page-link" href="<?php echo getPageUrl($current_page + 1, $order_by, $order_dir, $search); ?>" aria-label="Next">
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
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="equipmentModalLabel">
                    <?php echo $edit_equipment ? 'แก้ไขครุภัณฑ์' : 'เพิ่มครุภัณฑ์'; ?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <?php if ($edit_equipment): ?>
                        <input type="hidden" name="id" value="<?php echo $edit_equipment['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">รหัสครุภัณฑ์ *</label>
                            <input type="text" class="form-control" name="code" value="<?php echo $edit_equipment ? $edit_equipment['code'] : ''; ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ชื่ออุปกรณ์ *</label>
                            <input type="text" class="form-control" name="name" value="<?php echo $edit_equipment ? $edit_equipment['name'] : ''; ?>" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">หมวดหมู่ *</label>
                            <select class="form-control" name="category_id" id="category_id" required onchange="loadCategoryItems(this.value)">
                                <option value="">เลือกหมวดหมู่</option>
                                <?php foreach($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>" <?php echo ($edit_equipment && $edit_equipment['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                    <?php echo $category['code']; ?> - <?php echo $category['name']; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">รายการอุปกรณ์ *</label>
                            <select class="form-control" name="category_item_id" id="category_item_id" required>
                                <option value="">เลือกรายการอุปกรณ์</option>
                                <?php if ($edit_equipment && count($categories_items) > 0): ?>
                                    <?php foreach($categories_items as $item): ?>
                                    <option value="<?php echo $item['id']; ?>" <?php echo ($edit_equipment && $edit_equipment['category_item_id'] == $item['id']) ? 'selected' : ''; ?>>
                                        <?php echo $item['name']; ?>
                                    </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">ยี่ห้อ</label>
                            <input type="text" class="form-control" name="brand" value="<?php echo $edit_equipment ? $edit_equipment['brand'] : ''; ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">รุ่น</label>
                            <input type="text" class="form-control" name="model" value="<?php echo $edit_equipment ? $edit_equipment['model'] : ''; ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">หมายเลขซีเรียล</label>
                            <input type="text" class="form-control" name="serial_number" value="<?php echo $edit_equipment ? $edit_equipment['serial_number'] : ''; ?>">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">วันที่จัดซื้อ</label>
                            <input type="date" class="form-control" name="purchase_date" value="<?php echo $edit_equipment ? $edit_equipment['purchase_date'] : ''; ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ราคาจัดซื้อ</label>
                            <input type="number" step="0.01" class="form-control" name="purchase_price" value="<?php echo $edit_equipment ? $edit_equipment['purchase_price'] : ''; ?>">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">แผนก *</label>
                            <select class="form-control" name="department_id" required>
                                <option value="">เลือกแผนก</option>
                                <?php foreach($departments as $department): ?>
                                <option value="<?php echo $department['id']; ?>" <?php echo ($edit_equipment && $edit_equipment['department_id'] == $department['id']) ? 'selected' : ''; ?>>
                                    <?php echo $department['name']; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ผู้รับผิดชอบ</label>
                            <input type="text" class="form-control" name="responsible_person" value="<?php echo $edit_equipment ? $edit_equipment['responsible_person'] : ''; ?>">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">สถานะ</label>
                            <select class="form-control" name="status">
                                <option value="ใหม่" <?php echo ($edit_equipment && $edit_equipment['status'] == 'ใหม่') ? 'selected' : ''; ?>>ใหม่</option>
                                <option value="ใช้งานปกติ" <?php echo ($edit_equipment && $edit_equipment['status'] == 'ใช้งานปกติ') ? 'selected' : ''; ?>>ใช้งานปกติ</option>
                                <option value="ชำรุด" <?php echo ($edit_equipment && $edit_equipment['status'] == 'ชำรุด') ? 'selected' : ''; ?>>ชำรุด</option>
                                <option value="รอซ่อม" <?php echo ($edit_equipment && $edit_equipment['status'] == 'รอซ่อม') ? 'selected' : ''; ?>>รอซ่อม</option>
                                <option value="จำหน่ายแล้ว" <?php echo ($edit_equipment && $edit_equipment['status'] == 'จำหน่ายแล้ว') ? 'selected' : ''; ?>>จำหน่ายแล้ว</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">รายละเอียดคุณสมบัติ</label>
                        <textarea class="form-control" name="specifications" rows="3"><?php echo $edit_equipment ? $edit_equipment['specifications'] : ''; ?></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                    <button type="submit" name="<?php echo $edit_equipment ? 'edit_equipment' : 'add_equipment'; ?>" class="btn btn-primary">
                        <?php echo $edit_equipment ? 'อัพเดท' : 'บันทึก'; ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
<?php if ($edit_equipment): ?>
    $(document).ready(function() {
        $('#equipmentModal').modal('show');
    });
<?php endif; ?>

// Function to load category items
function loadCategoryItems(categoryId) {
    if (categoryId) {
        fetch('ajax_get_items.php?category_id=' + categoryId)
            .then(response => response.json())
            .then(data => {
                const itemSelect = document.getElementById('category_item_id');
                itemSelect.innerHTML = '<option value="">เลือกรายการอุปกรณ์</option>';
                data.forEach(item => {
                    itemSelect.innerHTML += `<option value="${item.id}">${item.name}</option>`;
                });
            })
            .catch(error => {
                console.error('Error:', error);
            });
    } else {
        document.getElementById('category_item_id').innerHTML = '<option value="">เลือกรายการอุปกรณ์</option>';
    }
}

// Disable DataTables auto-initialization since we have custom pagination and sorting
$(document).ready(function() {
    // Remove DataTables initialization since we have custom implementation
});
</script>

<?php require_once 'includes/footer.php'; ?>