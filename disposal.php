<?php
require_once 'includes/header.php';

// CRUD Operations
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $id = isset($_GET['id']) ? $_GET['id'] : null;
    
    if ($action == 'delete' && $id) {
        try {
            // ดึงข้อมูล equipment_id ก่อนลบ
            $stmt = $db->prepare("SELECT equipment_id FROM equipment_disposals WHERE id = ?");
            $stmt->execute([$id]);
            $disposal = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($disposal) {
                // ลบข้อมูลการจำหน่าย
                $delete_stmt = $db->prepare("DELETE FROM equipment_disposals WHERE id = ?");
                $delete_stmt->execute([$id]);
                
                // อัปเดตสถานะครุภัณฑ์กลับเป็น "ใช้งานปกติ"
                $update_stmt = $db->prepare("UPDATE equipment SET equipment_status = 'ใช้งานปกติ' WHERE id = ?");
                $update_stmt->execute([$disposal['equipment_id']]);
                
                $_SESSION['success'] = "ลบข้อมูลการจำหน่ายและอัปเดตสถานะครุภัณฑ์เรียบร้อยแล้ว";
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "เกิดข้อผิดพลาดในการลบข้อมูล: " . $e->getMessage();
        }
        header("Location: disposal.php");
        exit();
    }
}

if ($_POST) {
    if (isset($_POST['add_disposal'])) {
        try {
            $equipment_id = $_POST['equipment_id'];
            $disposal_date = $_POST['disposal_date'];
            $disposal_method = $_POST['disposal_method'];
            $disposal_value = $_POST['disposal_value'];
            $disposal_reason = $_POST['disposal_reason'];
            $approved_by = $_POST['approved_by'];
            $disposal_notes = $_POST['disposal_notes'];

            // Insert disposal record
            $stmt = $db->prepare("INSERT INTO equipment_disposals 
                                (equipment_id, disposal_date, disposal_method, disposal_value, disposal_reason, approved_by, disposal_notes) 
                                VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $equipment_id, 
                $disposal_date, 
                $disposal_method, 
                $disposal_value, 
                $disposal_reason, 
                $approved_by, 
                $disposal_notes
            ]);

            // อัปเดตสถานะครุภัณฑ์เป็น "จำหน่ายแล้ว"
            $update_stmt = $db->prepare("UPDATE equipment SET equipment_status = 'จำหน่ายแล้ว' WHERE id = ?");
            $update_stmt->execute([$equipment_id]);
            
            $_SESSION['success'] = "บันทึกข้อมูลการจำหน่ายและอัปเดตสถานะครุภัณฑ์เรียบร้อยแล้ว";
            header("Location: disposal.php");
            exit();

        } catch (PDOException $e) {
            $_SESSION['error'] = "เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . $e->getMessage();
            header("Location: disposal.php");
            exit();
        }
    }
    
    if (isset($_POST['edit_disposal'])) {
        try {
            $equipment_id = $_POST['equipment_id'];
            $disposal_id = $_POST['disposal_id'];

            // Update disposal record
            $stmt = $db->prepare("UPDATE equipment_disposals 
                                SET disposal_date=?, disposal_method=?, disposal_value=?, disposal_reason=?, approved_by=?, disposal_notes=?
                                WHERE id=?");
            $stmt->execute([
                $_POST['disposal_date'],
                $_POST['disposal_method'],
                $_POST['disposal_value'],
                $_POST['disposal_reason'],
                $_POST['approved_by'],
                $_POST['disposal_notes'],
                $disposal_id
            ]);

            $_SESSION['success'] = "แก้ไขข้อมูลการจำหน่ายเรียบร้อยแล้ว";
            header("Location: disposal.php");
            exit();

        } catch (PDOException $e) {
            $_SESSION['error'] = "เกิดข้อผิดพลาดในการอัปเดตข้อมูล: " . $e->getMessage();
            header("Location: disposal.php");
            exit();
        }
    }
}

// Get disposal list with equipment data
$disposal_query = "SELECT ed.*, e.equipment_code, e.equipment_name, e.serial_number, e.purchase_date, e.purchase_price,
                          ec.category_name, es.subcategory_name
                   FROM equipment_disposals ed 
                   JOIN equipment e ON ed.equipment_id = e.id 
                   LEFT JOIN equipment_categories ec ON e.category_id = ec.id 
                   LEFT JOIN equipment_subcategories es ON e.subcategory_id = es.id 
                   ORDER BY ed.disposal_date DESC, ed.created_at DESC";
$disposal_list = $db->query($disposal_query)->fetchAll(PDO::FETCH_ASSOC);

// Get equipment for dropdown (เฉพาะครุภัณฑ์ที่ยังไม่ถูกจำหน่าย)
$equipment_query = "SELECT id, equipment_code, equipment_name, equipment_status, purchase_price
                    FROM equipment 
                    WHERE equipment_status != 'จำหน่ายแล้ว'
                    ORDER BY equipment_name";
$equipment_list = $db->query($equipment_query)->fetchAll(PDO::FETCH_ASSOC);

// สถิติการจำหน่าย
$stats_query = "SELECT 
                COUNT(*) as total_disposals,
                SUM(disposal_value) as total_value,
                disposal_method,
                COUNT(*) as method_count
                FROM equipment_disposals 
                GROUP BY disposal_method 
                ORDER BY method_count DESC";
$disposal_stats = $db->query($stats_query)->fetchAll(PDO::FETCH_ASSOC);

// ข้อมูลโรงเรียนสำหรับแปลงชื่อย่อ (ใช้ร่วมกับ maintenance)
$schools_short = [
    "โรงเรียนวารีเชียงใหม่" => "VCS",
    "โรงเรียนอนุบาลวารีเชียงใหม่" => "VKS", 
    "โรงเรียนนานาชาติวารีเชียงใหม่" => "VCIS"
];
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
        <h1 class="h2">ระบบจำหน่ายครุภัณฑ์</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#disposalModal" onclick="clearForm()">
            <i class="fas fa-plus"></i> จำหน่ายครุภัณฑ์
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

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title"><?php echo count($disposal_list); ?></h4>
                            <p class="card-text">รายการจำหน่ายทั้งหมด</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-box fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">
                                ฿<?php echo number_format(array_sum(array_column($disposal_list, 'disposal_value')), 2); ?>
                            </h4>
                            <p class="card-text">มูลค่าจำหน่ายรวม</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-money-bill-wave fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">
                                <?php 
                                $current_month = date('Y-m');
                                $monthly_count = array_filter($disposal_list, function($item) use ($current_month) {
                                    return date('Y-m', strtotime($item['disposal_date'])) === $current_month;
                                });
                                echo count($monthly_count);
                                ?>
                            </h4>
                            <p class="card-text">จำหน่ายเดือนนี้</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-calendar-alt fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">
                                <?php 
                                $available_equipment = $db->query("SELECT COUNT(*) FROM equipment WHERE equipment_status != 'จำหน่ายแล้ว'")->fetchColumn();
                                echo $available_equipment;
                                ?>
                            </h4>
                            <p class="card-text">ครุภัณฑ์คงเหลือ</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-laptop fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Real-time Filter Section -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold">ค้นหาทั่วไป</label>
                    <input type="text" class="form-control" id="globalSearch" placeholder="ค้นหาทั้งตาราง..." onkeyup="filterTable()">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">วิธีการจำหน่าย</label>
                    <select class="form-control" id="methodFilter" onchange="filterTable()">
                        <option value="">ทั้งหมด</option>
                        <option value="ขายทอดตลาด">ขายทอดตลาด</option>
                        <option value="บริจาค">บริจาค</option>
                        <option value="ทำลาย">ทำลาย</option>
                        <option value="โอนย้าย">โอนย้าย</option>
                        <option value="อื่นๆ">อื่นๆ</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">หมวดหมู่</label>
                    <select class="form-control" id="categoryFilter" onchange="filterTable()">
                        <option value="">ทั้งหมด</option>
                        <?php
                        $categories = $db->query("SELECT DISTINCT category_name FROM equipment_categories WHERE category_name IS NOT NULL")->fetchAll(PDO::FETCH_COLUMN);
                        foreach ($categories as $category): ?>
                            <option value="<?php echo $category; ?>"><?php echo $category; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold">ปี</label>
                    <select class="form-control" id="yearFilter" onchange="filterTable()">
                        <option value="">ทั้งหมด</option>
                        <?php
                        $years = $db->query("SELECT DISTINCT YEAR(disposal_date) as year FROM equipment_disposals ORDER BY year DESC")->fetchAll(PDO::FETCH_COLUMN);
                        foreach ($years as $year): ?>
                            <option value="<?php echo $year; ?>"><?php echo $year + 543; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="clearFilters()">
                                <i class="fas fa-redo"></i> ล้างตัวกรอง
                            </button>
                            <span class="ms-3 text-muted small" id="filterResultCount"></span>
                        </div>
                        <div class="text-muted small">
                            <span id="currentTime"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h5 class="m-0 font-weight-bold">รายการจำหน่ายครุภัณฑ์ทั้งหมด</h5>
            <div class="btn-group">
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="exportToExcel()">
                    <i class="fas fa-file-excel"></i> Export Excel
                </button>
                <button type="button" class="btn btn-outline-success btn-sm" onclick="printTable()">
                    <i class="fas fa-print"></i> พิมพ์
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="disposalTable" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th width="80">#</th>
                            <th width="120">รหัสครุภัณฑ์</th>
                            <th width="200">ชื่อครุภัณฑ์</th>
                            <th width="120">วันที่จำหน่าย</th>
                            <th width="120">วิธีการจำหน่าย</th>
                            <th width="120">มูลค่าจำหน่าย</th>
                            <th width="150">เหตุผล</th>
                            <th width="120">ผู้อนุมัติ</th>
                            <th width="120">หมวดหมู่</th>
                            <th width="90">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($disposal_list as $index => $disposal): ?>
                        <tr>
                            <td class="text-center"><?php echo $index + 1; ?></td>
                            <td>
                                <a href="equipment_detail.php?id=<?php echo $disposal['equipment_id']; ?>" class="text-decoration-none" title="ดูรายละเอียดอุปกรณ์">
                                    <strong class="text-info"><?php echo $disposal['equipment_code']; ?></strong>
                                </a>
                            </td>
                            <td>
                                <div style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" 
                                     title="<?php echo htmlspecialchars($disposal['equipment_name']); ?>">
                                    <?php echo $disposal['equipment_name']; ?>
                                </div>
                                <?php if ($disposal['serial_number']): ?>
                                    <small class="text-muted d-block">SN: <?php echo $disposal['serial_number']; ?></small>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <small data-sort="<?php echo $disposal['disposal_date']; ?>">
                                    <?php echo date('d/m/Y', strtotime($disposal['disposal_date'])); ?>
                                </small>
                            </td>
                            <td class="text-center">
                                <?php 
                                $method_badge = [
                                    'ขายทอดตลาด' => 'success',
                                    'บริจาค' => 'info',
                                    'ทำลาย' => 'secondary',
                                    'โอนย้าย' => 'warning',
                                    'อื่นๆ' => 'primary'
                                ];
                                $method_class = $method_badge[$disposal['disposal_method']] ?? 'secondary';
                                ?>
                                <span class="badge bg-<?php echo $method_class; ?>">
                                    <?php echo $disposal['disposal_method']; ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <?php if ($disposal['disposal_value'] > 0): ?>
                                    <span class="text-success fw-bold">฿<?php echo number_format($disposal['disposal_value'], 2); ?></span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" 
                                     title="<?php echo htmlspecialchars($disposal['disposal_reason']); ?>">
                                    <?php echo mb_substr($disposal['disposal_reason'], 0, 30) . (mb_strlen($disposal['disposal_reason']) > 30 ? '...' : ''); ?>
                                </div>
                            </td>
                            <td><?php echo $disposal['approved_by']; ?></td>
                            <td>
                                <span class="badge bg-light text-dark"><?php echo $disposal['category_name']; ?></span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#disposalModal" onclick='editDisposal(<?php echo json_encode($disposal); ?>)' title="แก้ไข">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="disposal.php?action=delete&id=<?php echo $disposal['id']; ?>" class="btn btn-danger" onclick="return confirm('คุณแน่ใจหรือไม่ที่จะลบรายการจำหน่ายนี้?')" title="ลบ">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- No Results Message -->
            <div id="noResults" class="text-center py-4" style="display: none;">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">ไม่พบข้อมูลที่ตรงกับการค้นหา</h5>
                <p class="text-muted">ลองเปลี่ยนคำค้นหาหรือล้างตัวกรอง</p>
                <button type="button" class="btn btn-outline-primary" onclick="clearFilters()">
                    <i class="fas fa-redo"></i> ล้างตัวกรอง
                </button>
            </div>
        </div>
    </div>
</main>

<!-- Disposal Modal -->
<div class="modal fade" id="disposalModal" tabindex="-1" aria-labelledby="disposalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="disposalModalLabel">จำหน่ายครุภัณฑ์</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" id="disposalForm">
                <div class="modal-body">
                    <input type="hidden" name="disposal_id" id="disposal_id">
                    
                    <!-- Equipment Selection -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2"><i class="fas fa-laptop"></i> เลือกครุภัณฑ์</h6>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">ครุภัณฑ์ที่จะจำหน่าย *</label>
                            <select class="form-control" name="equipment_id" id="equipment_id" required onchange="showEquipmentInfo()">
                                <option value="">เลือกครุภัณฑ์</option>
                                <?php foreach($equipment_list as $equipment): ?>
                                <option value="<?php echo $equipment['id']; ?>" 
                                        data-purchase-price="<?php echo $equipment['purchase_price']; ?>"
                                        data-status="<?php echo $equipment['equipment_status']; ?>">
                                    <?php echo $equipment['equipment_code'] . ' - ' . $equipment['equipment_name'] . ' (สถานะ: ' . $equipment['equipment_status'] . ')'; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <div id="equipmentInfoAlert" class="alert alert-info mt-2" style="display: none;">
                                <small>
                                    <i class="fas fa-info-circle"></i> 
                                    <span id="equipmentInfoText"></span>
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Disposal Details -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2"><i class="fas fa-file-alt"></i> รายละเอียดการจำหน่าย</h6>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">วันที่จำหน่าย *</label>
                            <input type="date" class="form-control" name="disposal_date" id="disposal_date" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">วิธีการจำหน่าย *</label>
                            <select class="form-control" name="disposal_method" id="disposal_method" required>
                                <option value="">เลือกวิธีการจำหน่าย</option>
                                <option value="ขายทอดตลาด">ขายทอดตลาด</option>
                                <option value="บริจาค">บริจาค</option>
                                <option value="ทำลาย">ทำลาย</option>
                                <option value="โอนย้าย">โอนย้าย</option>
                                <option value="อื่นๆ">อื่นๆ</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">มูลค่าที่จำหน่าย (บาท)</label>
                            <input type="number" step="0.01" class="form-control" name="disposal_value" id="disposal_value" value="0" min="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">ผู้อนุมัติ *</label>
                            <input type="text" class="form-control" name="approved_by" id="approved_by" required placeholder="กรอกชื่อผู้อนุมัติ">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">เหตุผลในการจำหน่าย *</label>
                        <textarea class="form-control" name="disposal_reason" id="disposal_reason" rows="3" required placeholder="ระบุเหตุผลในการจำหน่าย"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">หมายเหตุ</label>
                        <textarea class="form-control" name="disposal_notes" id="disposal_notes" rows="2" placeholder="หมายเหตุเพิ่มเติม"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                    <button type="submit" name="add_disposal" id="submitBtn" class="btn btn-primary">บันทึก</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Real-time Filter Functions
function filterTable() {
    const globalSearch = document.getElementById('globalSearch').value.toLowerCase();
    const methodFilter = document.getElementById('methodFilter').value;
    const categoryFilter = document.getElementById('categoryFilter').value;
    const yearFilter = document.getElementById('yearFilter').value;
    
    const rows = document.querySelectorAll('#disposalTable tbody tr');
    let visibleCount = 0;
    
    rows.forEach(row => {
        let showRow = true;
        
        // Global search filter
        if (globalSearch) {
            const rowText = row.textContent.toLowerCase();
            if (!rowText.includes(globalSearch)) {
                showRow = false;
            }
        }
        
        // Method filter
        if (methodFilter && showRow) {
            const methodCell = row.cells[4]; // Method column
            const methodText = methodCell.textContent.trim();
            if (methodText !== methodFilter) {
                showRow = false;
            }
        }
        
        // Category filter
        if (categoryFilter && showRow) {
            const categoryCell = row.cells[8]; // Category column
            const categoryText = categoryCell.textContent.trim();
            if (categoryText !== categoryFilter) {
                showRow = false;
            }
        }
        
        // Year filter
        if (yearFilter && showRow) {
            const dateCell = row.cells[3]; // Date column
            const dateSort = dateCell.querySelector('small').getAttribute('data-sort');
            const rowYear = new Date(dateSort).getFullYear();
            if (rowYear != yearFilter) {
                showRow = false;
            }
        }
        
        // Show/hide row
        row.style.display = showRow ? '' : 'none';
        if (showRow) visibleCount++;
    });
    
    // Update result count
    document.getElementById('filterResultCount').textContent = `พบ ${visibleCount} รายการ`;
    
    // Show/hide no results message
    const noResults = document.getElementById('noResults');
    if (visibleCount === 0) {
        noResults.style.display = 'block';
    } else {
        noResults.style.display = 'none';
    }
}

function clearFilters() {
    document.getElementById('globalSearch').value = '';
    document.getElementById('methodFilter').value = '';
    document.getElementById('categoryFilter').value = '';
    document.getElementById('yearFilter').value = '';
    
    filterTable();
}

function exportToExcel() {
    const table = document.getElementById('disposalTable');
    let csv = [];
    
    // เพิ่มหัวข้อภาษาไทย
    const headers = [
        'ลำดับ',
        'รหัสครุภัณฑ์',
        'ชื่อครุภัณฑ์', 
        'วันที่จำหน่าย',
        'วิธีการจำหน่าย',
        'มูลค่าจำหน่าย',
        'เหตุผล',
        'ผู้อนุมัติ',
        'หมวดหมู่'
    ];
    csv.push(headers.join(','));
    
    // เพิ่มข้อมูลแถว
    for (let i = 1; i < table.rows.length; i++) {
        const row = table.rows[i];
        if (row.style.display !== 'none') {
            const rowData = [];
            for (let j = 0; j < row.cells.length - 1; j++) { // ไม่รวมคอลัมน์จัดการ
                let cellText = row.cells[j].textContent.trim();
                cellText = cellText.replace(/,/g, ' ').replace(/\n/g, ' ').replace(/\s+/g, ' ');
                rowData.push(cellText);
            }
            csv.push(rowData.join(','));
        }
    }
    
    const csvString = csv.join('\n');
    const blob = new Blob(['\uFEFF' + csvString], { type: 'text/csv;charset=utf-8;' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `ข้อมูลจำหน่ายครุภัณฑ์_${new Date().toISOString().split('T')[0]}.csv`;
    a.click();
    window.URL.revokeObjectURL(url);
}

function printTable() {
    const printWindow = window.open('', '_blank');
    const table = document.getElementById('disposalTable').cloneNode(true);
    
    // ลบคอลัมน์จัดการ
    const rows = table.getElementsByTagName('tr');
    for (let i = 0; i < rows.length; i++) {
        if (rows[i].cells.length > 0) {
            rows[i].deleteCell(-1);
        }
    }
    
    printWindow.document.write(`
        <html>
            <head>
                <title>พิมพ์รายการจำหน่ายครุภัณฑ์</title>
                <style>
                    body { font-family: 'Sarabun', sans-serif; }
                    table { width: 100%; border-collapse: collapse; }
                    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                    th { background-color: #f8f9fa; }
                    .badge { padding: 4px 8px; border-radius: 4px; font-size: 12px; }
                    .text-center { text-align: center; }
                    .text-end { text-align: right; }
                </style>
            </head>
            <body>
                <h2 style="text-align: center; margin-bottom: 20px;">รายการจำหน่ายครุภัณฑ์</h2>
                ${table.outerHTML}
                <div style="margin-top: 20px; text-align: right; font-size: 12px;">
                    พิมพ์เมื่อ: ${new Date().toLocaleString('th-TH')}
                </div>
            </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
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

// Initialize filters
document.addEventListener('DOMContentLoaded', function() {
    filterTable();
});

function showEquipmentInfo() {
    const equipmentSelect = document.getElementById('equipment_id');
    const infoAlert = document.getElementById('equipmentInfoAlert');
    const infoText = document.getElementById('equipmentInfoText');
    
    if (equipmentSelect.value) {
        const selectedOption = equipmentSelect.options[equipmentSelect.selectedIndex];
        const purchasePrice = selectedOption.getAttribute('data-purchase-price');
        const equipmentStatus = selectedOption.getAttribute('data-status');
        
        let info = `สถานะปัจจุบัน: ${equipmentStatus}`;
        if (purchasePrice > 0) {
            info += ` | ราคาซื้อ: ฿${parseFloat(purchasePrice).toLocaleString('th-TH', {minimumFractionDigits: 2})}`;
        }
        
        infoText.textContent = info;
        infoAlert.style.display = 'block';
    } else {
        infoAlert.style.display = 'none';
    }
}

function clearForm() {
    document.getElementById('disposalForm').reset();
    document.getElementById('disposal_id').value = '';
    document.getElementById('disposalModalLabel').textContent = 'จำหน่ายครุภัณฑ์';
    document.getElementById('submitBtn').name = 'add_disposal';
    document.getElementById('equipmentInfoAlert').style.display = 'none';
    document.getElementById('disposal_date').value = '<?php echo date('Y-m-d'); ?>';
}

function editDisposal(data) {
    document.getElementById('disposalModalLabel').textContent = 'แก้ไขข้อมูลการจำหน่าย';
    document.getElementById('submitBtn').name = 'edit_disposal';
    
    // Fill form with data
    document.getElementById('disposal_id').value = data.id;
    document.getElementById('equipment_id').value = data.equipment_id;
    document.getElementById('disposal_date').value = data.disposal_date;
    document.getElementById('disposal_method').value = data.disposal_method;
    document.getElementById('disposal_value').value = data.disposal_value || '0';
    document.getElementById('disposal_reason').value = data.disposal_reason;
    document.getElementById('approved_by').value = data.approved_by;
    document.getElementById('disposal_notes').value = data.disposal_notes || '';
    
    // Show equipment info
    showEquipmentInfo();
    
    // Disable equipment selection when editing
    document.getElementById('equipment_id').disabled = true;
}
</script>

<?php
require_once 'includes/footer.php';
?>