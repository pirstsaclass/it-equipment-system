
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

// Get equipment for report
$report_query = "SELECT e.*, ec.category_name, es.subcategory_name, d.department_name 
    FROM equipment e 
    LEFT JOIN equipment_categories ec ON e.category_id = ec.id 
    LEFT JOIN equipment_subcategories es ON e.subcategory_id = es.id 
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

// Handle Excel Export
if (isset($_GET['export']) && $_GET['export'] == 'excel') {
    header('Content-Type: application/vnd.ms-excel; charset=utf-8');
    header('Content-Disposition: attachment; filename="รายงานครุภัณฑ์_' . date('Y-m-d') . '.xls"');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
    echo '<head>';
    echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
    echo '<style>';
    echo 'table { border-collapse: collapse; width: 100%; font-family: "TH Sarabun PSK", "Angsana New", "Cordia New", sans-serif; font-size: 16px; }';
    echo 'th { background-color: #f2f2f2; border: 1px solid #ddd; padding: 8px; text-align: center; font-weight: bold; }';
    echo 'td { border: 1px solid #ddd; padding: 8px; }';
    echo '.header { text-align: center; font-size: 18px; font-weight: bold; margin-bottom: 20px; }';
    echo '.summary { margin-bottom: 20px; }';
    echo '</style>';
    echo '</head>';
    echo '<body>';
    
    // Report Header
    echo '<div class="header">รายงานครุภัณฑ์</div>';
    
    // Summary Information
    echo '<div class="summary">';
    echo '<strong>วันที่ออกรายงาน:</strong> ' . date('d/m/Y') . '<br>';
    if (!empty($start_date)) echo '<strong>วันที่เริ่มต้น:</strong> ' . $start_date . '<br>';
    if (!empty($end_date)) echo '<strong>วันที่สิ้นสุด:</strong> ' . $end_date . '<br>';
    if (!empty($status)) echo '<strong>สถานะ:</strong> ' . $status . '<br>';
    echo '<strong>จำนวนครุภัณฑ์:</strong> ' . number_format(count($report_data)) . ' รายการ<br>';
    echo '</div>';
    
    // Table
    echo '<table border="1">';
    echo '<thead>';
    echo '<tr>';
    echo '<th>รหัสครุภัณฑ์</th>';
    echo '<th>ชื่ออุปกรณ์</th>';
    echo '<th>ประเภท</th>';
    echo '<th>หมวดหมู่ย่อย</th>';
    echo '<th>แผนก</th>';
    echo '<th>ผู้รับผิดชอบ</th>';
    echo '<th>วันที่จัดซื้อ</th>';
    echo '<th>ราคาจัดซื้อ</th>';
    echo '<th>สถานะ</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    
    foreach($report_data as $item) {
        echo '<tr>';
        echo '<td>' . $item['equipment_code'] . '</td>';
        echo '<td>' . $item['equipment_name'] . '</td>';
        echo '<td>' . $item['category_name'] . '</td>';
        echo '<td>' . $item['subcategory_name'] . '</td>';
        echo '<td>' . $item['department_name'] . '</td>';
        echo '<td>' . $item['responsible_person'] . '</td>';
        echo '<td>' . $item['purchase_date'] . '</td>';
        echo '<td>' . number_format($item['purchase_price'], 2) . '</td>';
        echo '<td>' . $item['equipment_status'] . '</td>';
        echo '</tr>';
    }
    
    echo '</tbody>';
    echo '</table>';
    echo '</body>';
    echo '</html>';
    exit();
}

// Handle AJAX request for real-time filtering
if (isset($_POST['ajax_filter'])) {
    $ajax_conditions = [];
    $ajax_params = [];
    
    $ajax_start_date = $_POST['start_date'] ?? '';
    $ajax_end_date = $_POST['end_date'] ?? '';
    $ajax_department_id = $_POST['department_id'] ?? '';
    $ajax_category_id = $_POST['category_id'] ?? '';
    $ajax_status = $_POST['status'] ?? '';
    
    if (!empty($ajax_start_date)) {
        $ajax_conditions[] = "e.purchase_date >= ?";
        $ajax_params[] = $ajax_start_date;
    }
    
    if (!empty($ajax_end_date)) {
        $ajax_conditions[] = "e.purchase_date <= ?";
        $ajax_params[] = $ajax_end_date;
    }
    
    if (!empty($ajax_department_id)) {
        $ajax_conditions[] = "e.location_school LIKE ?";
        $ajax_params[] = "%" . $ajax_department_id . "%";
    }
    
    if (!empty($ajax_category_id)) {
        $ajax_conditions[] = "e.category_id = ?";
        $ajax_params[] = $ajax_category_id;
    }
    
    if (!empty($ajax_status)) {
        $ajax_conditions[] = "e.equipment_status = ?";
        $ajax_params[] = $ajax_status;
    }
    
    $ajax_where_clause = '';
    if (!empty($ajax_conditions)) {
        $ajax_where_clause = 'WHERE ' . implode(' AND ', $ajax_conditions);
    }
    
    $ajax_query = "SELECT e.*, ec.category_name, es.subcategory_name, d.department_name 
        FROM equipment e 
        LEFT JOIN equipment_categories ec ON e.category_id = ec.id 
        LEFT JOIN equipment_subcategories es ON e.subcategory_id = es.id 
        LEFT JOIN departments d ON e.responsible_person LIKE CONCAT('%', d.department_name, '%')
        $ajax_where_clause 
        ORDER BY e.purchase_date DESC";
    
    $ajax_stmt = $db->prepare($ajax_query);
    $ajax_stmt->execute($ajax_params);
    $ajax_data = $ajax_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calculate summary statistics
    $total_count = count($ajax_data);
    $total_value = array_sum(array_column($ajax_data, 'purchase_price'));
    $new_count = count(array_filter($ajax_data, function($item) {
        return $item['equipment_status'] == 'ใหม่';
    }));
    $repair_count = count(array_filter($ajax_data, function($item) {
        return $item['equipment_status'] == 'รอซ่อม';
    }));
    
    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'summary' => [
            'total_count' => $total_count,
            'total_value' => number_format($total_value, 2),
            'new_count' => $new_count,
            'repair_count' => $repair_count
        ],
        'data' => $ajax_data
    ]);
    exit();
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
        <h1 class="h2">รายงานครุภัณฑ์</h1>
        <div>
            <a href="?<?php echo http_build_query(array_merge($_GET, ['export' => 'excel'])); ?>" class="btn btn-success">
                <i class="fas fa-file-excel"></i> Export Excel
            </a>
            <button type="button" class="btn btn-danger" onclick="exportToPDF()">
                <i class="fas fa-file-pdf"></i> Export PDF
            </button>
        </div>
    </div>

    <!-- Report Summary -->
    <div class="row mb-4" id="summaryCards">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 id="totalCount"><?php echo count($report_data); ?></h4>
                            <h6 class="card-title">จำนวนครุภัณฑ์</h6>
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
                            <h4 id="totalValue">
                                <?php 
                                $total_value = array_sum(array_column($report_data, 'purchase_price'));
                                echo number_format($total_value, 2); 
                                ?>
                            </h4>
                            <h6 class="card-title">มูลค่ารวม</h6>
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
                            <h4 id="newCount">
                                <?php 
                                $new_count = count(array_filter($report_data, function($item) {
                                    return $item['equipment_status'] == 'ใหม่';
                                }));
                                echo $new_count;
                                ?>
                            </h4>
                            <h6 class="card-title">ครุภัณฑ์ใหม่</h6>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-star fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 id="repairCount">
                                <?php 
                                $repair_count = count(array_filter($report_data, function($item) {
                                    return $item['equipment_status'] == 'รอซ่อม';
                                }));
                                echo $repair_count;
                                ?>
                            </h4>
                            <h6 class="card-title">รอซ่อม</h6>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-tools fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">ตัวกรองรายงาน</h6>
        </div>
        <div class="card-body">
            <form method="GET" id="filterForm" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">วันที่เริ่มต้น</label>
                    <input type="date" class="form-control filter-input" name="start_date" id="start_date" value="<?php echo $start_date; ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">วันที่สิ้นสุด</label>
                    <input type="date" class="form-control filter-input" name="end_date" id="end_date" value="<?php echo $end_date; ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">แผนก</label>
                    <select class="form-control filter-input" name="department_id" id="department_id">
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
                    <select class="form-control filter-input" name="category_id" id="category_id">
                        <option value="">ทั้งหมด</option>
                        <?php foreach($categories as $category): ?>
                        <option value="<?php echo $category['id']; ?>" <?php echo $category_id == $category['id'] ? 'selected' : ''; ?>>
                            <?php echo $category['category_name']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">สถานะ</label>
                    <select class="form-control filter-input" name="status" id="status">
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
                    <div class="spinner-border spinner-border-sm text-primary ms-2 d-none" id="loadingSpinner" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Report Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">รายละเอียดครุภัณฑ์</h6>
            <div class="text-muted small">
                แสดง <span id="showingCount"><?php echo count($report_data); ?></span> รายการ
            </div>
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
                    <tbody id="reportTableBody">
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
function exportToPDF() {
    alert('ฟังก์ชันการส่งออก PDF กำลังอยู่ในระหว่างการพัฒนา');
}

$(document).ready(function() {
    // Initialize DataTable
    var dataTable = $('#reportTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/th.json'
        },
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excel',
                text: 'Export Excel',
                charset: 'UTF-8',
                bom: true,
                title: 'รายงานครุภัณฑ์',
                filename: 'รายงานครุภัณฑ์_' + new Date().toISOString().split('T')[0]
            },
            'copy', 'csv', 'print'
        ]
    });

    // Real-time filter function
    function applyFilters() {
        var formData = {
            ajax_filter: true,
            start_date: $('#start_date').val(),
            end_date: $('#end_date').val(),
            department_id: $('#department_id').val(),
            category_id: $('#category_id').val(),
            status: $('#status').val()
        };

        $('#loadingSpinner').removeClass('d-none');

        $.ajax({
            url: 'reports.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Update summary cards
                    $('#totalCount').text(response.summary.total_count);
                    $('#totalValue').text(response.summary.total_value);
                    $('#newCount').text(response.summary.new_count);
                    $('#repairCount').text(response.summary.repair_count);
                    $('#showingCount').text(response.summary.total_count);

                    // Clear and update table
                    dataTable.clear();
                    
                    $.each(response.data, function(index, item) {
                        var statusClass = getStatusClass(item.equipment_status);
                        var statusBadge = '<span class="badge bg-' + statusClass + '">' + item.equipment_status + '</span>';
                        
                        dataTable.row.add([
                            item.equipment_code,
                            item.equipment_name,
                            item.category_name,
                            item.subcategory_name,
                            item.department_name,
                            item.responsible_person,
                            item.purchase_date,
                            parseFloat(item.purchase_price).toLocaleString('th-TH', {minimumFractionDigits: 2}),
                            statusBadge
                        ]);
                    });
                    
                    dataTable.draw();
                }
            },
            error: function() {
                alert('เกิดข้อผิดพลาดในการโหลดข้อมูล');
            },
            complete: function() {
                $('#loadingSpinner').addClass('d-none');
            }
        });
    }

    // Helper function to get status badge class
    function getStatusClass(status) {
        var statusClasses = {
            'ใหม่': 'success',
            'ใช้งานปกติ': 'primary',
            'ชำรุด': 'warning',
            'รอซ่อม': 'info',
            'จำหน่ายแล้ว': 'danger'
        };
        return statusClasses[status] || 'secondary';
    }

    // Event listeners for real-time filtering
    $('.filter-input').on('change', function() {
        applyFilters();
    });

    // Also trigger on input for date fields (for better UX)
    $('#start_date, #end_date').on('input', function() {
        applyFilters();
    });

    // Prevent form submission for real-time filtering
    $('#filterForm').on('submit', function(e) {
        e.preventDefault();
        applyFilters();
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>
