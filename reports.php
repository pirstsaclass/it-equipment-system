<?php
require_once 'includes/header.php';

// Get filter parameters
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';
$department_id = isset($_GET['department_id']) ? $_GET['department_id'] : '';
$category_id = isset($_GET['category_id']) ? $_GET['category_id'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';

// Build query conditions
$conditions = [];
$params = [];

if (!empty($start_date)) {
    $conditions[] = "e.purchase_date >= ?";
    $params[] = $start_date;
}

if (!empty($end_date)) {
    $conditions[] = "e.purchase_date <= ?";
    $params[] = $end_date;
}

if (!empty($department_id)) {
    // Note: equipment table doesn't have department_id, using location_school instead
    $conditions[] = "e.location_school LIKE ?";
    $params[] = "%" . $department_id . "%";
}

if (!empty($category_id)) {
    $conditions[] = "e.category_id = ?";
    $params[] = $category_id;
}

if (!empty($status)) {
    $conditions[] = "e.equipment_status = ?";
    $params[] = $status;
}

$where_clause = '';
if (!empty($conditions)) {
    $where_clause = 'WHERE ' . implode(' AND ', $conditions);
}

// Get equipment for report - แก้ไข query ให้ตรงกับโครงสร้างฐานข้อมูล
$report_query = "SELECT e.*, ec.category_name, es.subcategory_name, d.department_name 
    FROM equipment e 
    LEFT JOIN equipment_categories ec ON e.category_id = ec.category_id 
    LEFT JOIN equipment_subcategories es ON e.subcategory_id = es.subcategory_id 
    LEFT JOIN departments d ON e.responsible_person LIKE CONCAT('%', d.department_name, '%')
    $where_clause 
    ORDER BY e.purchase_date DESC";
$stmt = $db->prepare($report_query);
$stmt->execute($params);
$report_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get departments for filter
$departments = $db->query("SELECT * FROM departments ORDER BY department_name")->fetchAll(PDO::FETCH_ASSOC);

// Get categories for filter
$categories = $db->query("SELECT * FROM equipment_categories ORDER BY category_name")->fetchAll(PDO::FETCH_ASSOC);
?>

<?php 
// Include sidebar
include 'includes/sidebar.php';
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <?php include 'includes/navbar.php'; ?>

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">รายงานครุภัณฑ์</h1>
        <div>
            <button type="button" class="btn btn-success" onclick="exportToExcel()">
                <i class="fas fa-file-excel"></i> Export Excel
            </button>
            <button type="button" class="btn btn-danger" onclick="exportToPDF()">
                <i class="fas fa-file-pdf"></i> Export PDF
            </button>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">ตัวกรองรายงาน</h6>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">วันที่เริ่มต้น</label>
                    <input type="date" class="form-control" name="start_date" value="<?php echo $start_date; ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">วันที่สิ้นสุด</label>
                    <input type="date" class="form-control" name="end_date" value="<?php echo $end_date; ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">แผนก</label>
                    <select class="form-control" name="department_id">
                        <option value="">ทั้งหมด</option>
                        <?php foreach($departments as $department): ?>
                        <option value="<?php echo $department['department_name']; ?>" <?php echo $department_id == $department['department_name'] ? 'selected' : ''; ?>>
                            <?php echo $department['department_name']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">ประเภท</label>
                    <select class="form-control" name="category_id">
                        <option value="">ทั้งหมด</option>
                        <?php foreach($categories as $category): ?>
                        <option value="<?php echo $category['category_id']; ?>" <?php echo $category_id == $category['category_id'] ? 'selected' : ''; ?>>
                            <?php echo $category['category_name']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">สถานะ</label>
                    <select class="form-control" name="status">
                        <option value="">ทั้งหมด</option>
                        <option value="ใหม่" <?php echo $status == 'ใหม่' ? 'selected' : ''; ?>>ใหม่</option>
                        <option value="ใช้งานปกติ" <?php echo $status == 'ใช้งานปกติ' ? 'selected' : ''; ?>>ใช้งานปกติ</option>
                        <option value="ชำรุด" <?php echo $status == 'ชำรุด' ? 'selected' : ''; ?>>ชำรุด</option>
                        <option value="รอซ่อม" <?php echo $status == 'รอซ่อม' ? 'selected' : ''; ?>>รอซ่อม</option>
                        <option value="จำหน่ายแล้ว" <?php echo $status == 'จำหน่ายแล้ว' ? 'selected' : ''; ?>>จำหน่ายแล้ว</option>
                    </select>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">แสดงรายงาน</button>
                    <a href="reports.php" class="btn btn-secondary">ล้างตัวกรอง</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Report Summary -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="card-title">จำนวนครุภัณฑ์</h6>
                    <h4><?php echo count($report_data); ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="card-title">มูลค่ารวม</h6>
                    <h4>
                        <?php 
                        $total_value = array_sum(array_column($report_data, 'purchase_price'));
                        echo number_format($total_value, 2); 
                        ?>
                    </h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6 class="card-title">ครุภัณฑ์ใหม่</h6>
                    <h4>
                        <?php 
                        $new_count = count(array_filter($report_data, function($item) {
                            return $item['equipment_status'] == 'ใหม่';
                        }));
                        echo $new_count;
                        ?>
                    </h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h6 class="card-title">รอซ่อม</h6>
                    <h4>
                        <?php 
                        $repair_count = count(array_filter($report_data, function($item) {
                            return $item['equipment_status'] == 'รอซ่อม';
                        }));
                        echo $repair_count;
                        ?>
                    </h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">รายละเอียดครุภัณฑ์</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="reportTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>รหัสครุภัณฑ์</th>
                            <th>ชื่ออุปกรณ์</th>
                            <th>ประเภท</th>
                            <th>หมวดหมู่ย่อย</th>
                            <th>แผนก</th>
                            <th>ผู้รับผิดชอบ</th>
                            <th>วันที่จัดซื้อ</th>
                            <th>ราคาจัดซื้อ</th>
                            <th>สถานะ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($report_data as $item): ?>
                        <tr>
                            <td><?php echo $item['equipment_code']; ?></td>
                            <td><?php echo $item['equipment_name']; ?></td>
                            <td><?php echo $item['category_name']; ?></td>
                            <td><?php echo $item['subcategory_name']; ?></td>
                            <td><?php echo $item['department_name']; ?></td>
                            <td><?php echo $item['responsible_person']; ?></td>
                            <td><?php echo $item['purchase_date']; ?></td>
                            <td><?php echo number_format($item['purchase_price'], 2); ?></td>
                            <td>
                                <?php 
                                $status_badge = [
                                    'ใหม่' => 'success',
                                    'ใช้งานปกติ' => 'primary',
                                    'ชำรุด' => 'warning',
                                    'รอซ่อม' => 'info',
                                    'จำหน่ายแล้ว' => 'danger'
                                ];
                                $current_status = $item['equipment_status'];
                                $badge_color = isset($status_badge[$current_status]) ? $status_badge[$current_status] : 'secondary';
                                ?>
                                <span class="badge bg-<?php echo $badge_color; ?>">
                                    <?php echo $current_status; ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<script>
function exportToExcel() {
    // Simple Excel export using table data
    var table = document.getElementById('reportTable');
    var html = table.outerHTML;
    var url = 'data:application/vnd.ms-excel,' + escape(html);
    var link = document.createElement('a');
    link.href = url;
    link.download = 'equipment_report_' + new Date().toISOString().split('T')[0] + '.xls';
    link.click();
}

function exportToPDF() {
    alert('ฟังก์ชันการส่งออก PDF กำลังอยู่ในระหว่างการพัฒนา');
    // In a real implementation, you would use a PDF library like jsPDF
    // or make an AJAX call to a server-side PDF generation script
}

$(document).ready(function() {
    $('#reportTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/th.json'
        },
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'print'
        ]
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>