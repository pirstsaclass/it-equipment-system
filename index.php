<?php
require_once 'includes/header.php';

// สถิติครุภัณฑ์
$equipment_stats_query = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status = 'ใหม่' THEN 1 ELSE 0 END) as new,
    SUM(CASE WHEN status = 'ชำรุด' THEN 1 ELSE 0 END) as damaged,
    SUM(CASE WHEN status = 'รอซ่อม' THEN 1 ELSE 0 END) as repair,
    SUM(CASE WHEN status = 'จำหน่ายแล้ว' THEN 1 ELSE 0 END) as disposed
    FROM equipment";
$equipment_stats = $db->query($equipment_stats_query)->fetch(PDO::FETCH_ASSOC);

// ครุภัณฑ์แยกตามประเภท
$equipment_by_category_query = "SELECT c.name, COUNT(e.id) as count 
    FROM categories c 
    LEFT JOIN equipment e ON c.id = e.category_id 
    GROUP BY c.id, c.name";
$equipment_by_category = $db->query($equipment_by_category_query)->fetchAll(PDO::FETCH_ASSOC);

// ครุภัณฑ์แยกตามสถานะ
$equipment_by_status_query = "SELECT status, COUNT(*) as count 
    FROM equipment 
    GROUP BY status";
$equipment_by_status = $db->query($equipment_by_status_query)->fetchAll(PDO::FETCH_ASSOC);

// การซ่อมบำรุงล่าสุด
$maintenance_query = "SELECT m.*, e.code, e.name as equipment_name, c.name as category_name, ci.name as item_name
    FROM maintenance m 
    JOIN equipment e ON m.equipment_id = e.id 
    LEFT JOIN categories c ON e.category_id = c.id 
    LEFT JOIN categories_items ci ON e.category_item_id = ci.id 
    ORDER BY m.created_at DESC 
    LIMIT 5";
$maintenance_list = $db->query($maintenance_query)->fetchAll(PDO::FETCH_ASSOC);

// อุปกรณ์ที่ซ่อมบ่อย - แก้ไข SQL syntax
$frequent_repair_query = "
    SELECT e.code, e.name, c.name as category_name, ci.name as item_name, COUNT(m.id) as repair_count 
    FROM equipment e 
    JOIN maintenance m ON e.id = m.equipment_id 
    LEFT JOIN categories c ON e.category_id = c.id 
    LEFT JOIN categories_items ci ON e.category_item_id = ci.id 
    GROUP BY e.id, e.code, e.name, c.name, ci.name 
    HAVING COUNT(m.id) > 2 
    ORDER BY repair_count DESC 
    LIMIT 5";
$frequent_repairs = $db->query($frequent_repair_query)->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- ส่วนที่เหลือของ index.php เหมือนเดิม -->
<!-- ... -->
<?php 
// Include sidebar
include 'includes/sidebar.php';
?>

<!-- Main Content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <!-- Navbar -->
    <?php include 'includes/navbar.php'; ?>

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">ภาพรวมระบบ</h1>
        <a href="reports.php" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-download fa-sm text-white-50"></i> สร้างรายงาน
        </a>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- สถิติครุภัณฑ์ -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">สถิติครุภัณฑ์</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center mb-3">
                            <div class="stat-card total p-3">
                                <div class="text-primary mb-1">
                                    <i class="fas fa-laptop fa-2x"></i>
                                </div>
                                <div class="h4 font-weight-bold"><?php echo $equipment_stats['total']; ?></div>
                                <div class="text-muted">ครุภัณฑ์ทั้งหมด</div>
                            </div>
                        </div>
                        <div class="col-md-3 text-center mb-3">
                            <div class="stat-card new p-3">
                                <div class="text-success mb-1">
                                    <i class="fas fa-plus-circle fa-2x"></i>
                                </div>
                                <div class="h4 font-weight-bold"><?php echo $equipment_stats['new']; ?></div>
                                <div class="text-muted">ครุภัณฑ์ใหม่</div>
                            </div>
                        </div>
                        <div class="col-md-3 text-center mb-3">
                            <div class="stat-card damaged p-3">
                                <div class="text-warning mb-1">
                                    <i class="fas fa-exclamation-triangle fa-2x"></i>
                                </div>
                                <div class="h4 font-weight-bold"><?php echo $equipment_stats['damaged']; ?></div>
                                <div class="text-muted">ครุภัณฑ์ชำรุด</div>
                            </div>
                        </div>
                        <div class="col-md-3 text-center mb-3">
                            <div class="stat-card repair p-3">
                                <div class="text-info mb-1">
                                    <i class="fas fa-tools fa-2x"></i>
                                </div>
                                <div class="h4 font-weight-bold"><?php echo $equipment_stats['repair']; ?></div>
                                <div class="text-muted">รอซ่อม</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- อุปกรณ์ที่ซ่อมบ่อย -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">อุปกรณ์ที่ซ่อมบ่อย</h6>
                </div>
                <div class="card-body">
                    <?php if (count($frequent_repairs) > 0): ?>
                        <div class="list-group">
                            <?php foreach($frequent_repairs as $freq): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1"><?php echo $freq['code']; ?></h6>
                                    <small class="text-muted"><?php echo $freq['name']; ?></small>
                                </div>
                                <span class="badge bg-warning rounded-pill"><?php echo $freq['repair_count']; ?> ครั้ง</span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted text-center">ไม่มีอุปกรณ์ที่ซ่อมบ่อย</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- ครุภัณฑ์แยกตามประเภท -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">ครุภัณฑ์แยกตามประเภท</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="equipmentTypeChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- สถานะครุภัณฑ์ -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">สถานะครุภัณฑ์</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="equipmentStatusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ประวัติการซ่อมล่าสุด -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">ประวัติการซ่อมล่าสุด</h6>
                    <a href="maintenance.php" class="btn btn-sm btn-primary">ดูทั้งหมด</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                            <thead class="table-light">
                                <tr>
                                    <th>รหัสครุภัณฑ์</th>
                                    <th>ชื่ออุปกรณ์</th>
                                    <th>วันที่แจ้งซ่อม</th>
                                    <th>ผู้ดำเนินการ</th>
                                    <th>สถานะ</th>
                                    <th>จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($maintenance_list as $maintenance): ?>
                                <tr>
                                    <td class="fw-bold"><?php echo $maintenance['code']; ?></td>
                                    <td><?php echo $maintenance['equipment_name']; ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($maintenance['report_date'])); ?></td>
                                    <td><?php echo $maintenance['assigned_technician'] ?: 'ยังไม่ได้มอบหมาย'; ?></td>
                                    <td>
                                        <?php 
                                        $status_badge = [
                                            'รอซ่อม' => 'warning',
                                            'กำลังดำเนินการ' => 'info',
                                            'ซ่อมเสร็จ' => 'success',
                                            'ยกเลิก' => 'danger'
                                        ];
                                        ?>
                                        <span class="badge bg-<?php echo $status_badge[$maintenance['status']]; ?>">
                                            <?php echo $maintenance['status']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="maintenance.php?action=edit&id=<?php echo $maintenance['id']; ?>" class="btn btn-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="maintenance.php?action=delete&id=<?php echo $maintenance['id']; ?>" class="btn btn-danger" onclick="return confirm('คุณแน่ใจหรือไม่?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Equipment by Type Chart
    const typeCtx = document.getElementById('equipmentTypeChart').getContext('2d');
    const typeChart = new Chart(typeCtx, {
        type: 'bar',
        data: {
            labels: [<?php 
                $labels = [];
                foreach($equipment_by_category as $category) {
                    $labels[] = $category['name'];
                }
                echo '"' . implode('","', $labels) . '"'; 
            ?>],
            datasets: [{
                label: 'จำนวนครุภัณฑ์',
                data: [<?php 
                    $data = [];
                    foreach($equipment_by_category as $category) {
                        $data[] = $category['count'];
                    }
                    echo implode(',', $data); 
                ?>],
                backgroundColor: [
                    'rgba(26, 115, 232, 0.7)',
                    'rgba(66, 133, 244, 0.7)',
                    'rgba(100, 181, 246, 0.7)',
                    'rgba(30, 136, 229, 0.7)',
                    'rgba(21, 101, 192, 0.7)',
                    'rgba(13, 71, 161, 0.7)'
                ],
                borderColor: [
                    'rgba(26, 115, 232, 1)',
                    'rgba(66, 133, 244, 1)',
                    'rgba(100, 181, 246, 1)',
                    'rgba(30, 136, 229, 1)',
                    'rgba(21, 101, 192, 1)',
                    'rgba(13, 71, 161, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Equipment Status Chart
    const statusCtx = document.getElementById('equipmentStatusChart').getContext('2d');
    const statusChart = new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: [<?php 
                $labels = [];
                foreach($equipment_by_status as $status) {
                    $labels[] = $status['status'];
                }
                echo '"' . implode('","', $labels) . '"'; 
            ?>],
            datasets: [{
                data: [<?php 
                    $data = [];
                    foreach($equipment_by_status as $status) {
                        $data[] = $status['count'];
                    }
                    echo implode(',', $data); 
                ?>],
                backgroundColor: [
                    'rgba(40, 167, 69, 0.7)',
                    'rgba(26, 115, 232, 0.7)',
                    'rgba(255, 193, 7, 0.7)',
                    'rgba(23, 162, 184, 0.7)',
                    'rgba(220, 53, 69, 0.7)'
                ],
                borderColor: [
                    'rgba(40, 167, 69, 1)',
                    'rgba(26, 115, 232, 1)',
                    'rgba(255, 193, 7, 1)',
                    'rgba(23, 162, 184, 1)',
                    'rgba(220, 53, 69, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});
</script>

<?php 
// Include sidebar and footer
include 'includes/footer.php'; 
?>