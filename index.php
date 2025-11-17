<?php
require_once 'includes/header.php';

// สถิติครุภัณฑ์
$equipment_stats_query = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN equipment_status = 'ใหม่' THEN 1 ELSE 0 END) as new,
    SUM(CASE WHEN equipment_status = 'ชำรุด' THEN 1 ELSE 0 END) as damaged,
    SUM(CASE WHEN equipment_status = 'กำลังซ่อม' THEN 1 ELSE 0 END) as repair,
    SUM(CASE WHEN equipment_status = 'จำหน่ายแล้ว' THEN 1 ELSE 0 END) as disposed
    FROM equipment";
$equipment_stats = $db->query($equipment_stats_query)->fetch(PDO::FETCH_ASSOC);

// ครุภัณฑ์แยกตามประเภท - ใช้ตาราง equipment_categories และ equipment
$equipment_by_category_query = "SELECT ec.category_name as name, COUNT(e.id) as count 
    FROM equipment_categories ec 
    LEFT JOIN equipment e ON ec.id = e.category_id 
    GROUP BY ec.id, ec.category_name";
$equipment_by_category = $db->query($equipment_by_category_query)->fetchAll(PDO::FETCH_ASSOC);

// ครุภัณฑ์แยกตามสถานะ
$equipment_by_status_query = "SELECT equipment_status, COUNT(*) as count 
    FROM equipment 
    GROUP BY equipment_status";
$equipment_by_status = $db->query($equipment_by_status_query)->fetchAll(PDO::FETCH_ASSOC);

// การซ่อมบำรุงล่าสุด - ใช้ตาราง maintenance_requests และ equipment
$maintenance_query = "SELECT mr.*, e.equipment_code, e.equipment_name, ec.category_name, es.subcategory_name
    FROM maintenance_requests mr 
    JOIN equipment e ON mr.equipment_id = e.id 
    LEFT JOIN equipment_categories ec ON e.category_id = ec.id 
    LEFT JOIN equipment_subcategories es ON e.subcategory_id = es.id 
    ORDER BY mr.created_at DESC 
    LIMIT 5";
$maintenance_list = $db->query($maintenance_query)->fetchAll(PDO::FETCH_ASSOC);

// อุปกรณ์ที่ซ่อมบ่อย - ใช้ตาราง maintenance_requests และ equipment
$frequent_repair_query = "
    SELECT e.equipment_code, e.equipment_name, ec.category_name, es.subcategory_name, COUNT(mr.id) as repair_count 
    FROM equipment e 
    JOIN maintenance_requests mr ON e.id = mr.equipment_id 
    LEFT JOIN equipment_categories ec ON e.category_id = ec.id 
    LEFT JOIN equipment_subcategories es ON e.subcategory_id = es.id 
    GROUP BY e.id, e.equipment_code, e.equipment_name, ec.category_name, es.subcategory_name 
    HAVING COUNT(mr.id) > 2 
    ORDER BY repair_count DESC 
    LIMIT 5";
$frequent_repairs = $db->query($frequent_repair_query)->fetchAll(PDO::FETCH_ASSOC);

// ดึงข้อมูลปฏิทิน
$calendar_query = "SELECT * FROM calendar_events ORDER BY event_date, start_time";
$calendar_events = $db->query($calendar_query)->fetchAll(PDO::FETCH_ASSOC);

// จัดการฟอร์มเพิ่มงาน
if ($_POST && isset($_POST['add_event'])) {
    $event_title = $_POST['event_title'];
    $event_description = $_POST['event_description'];
    $event_date = $_POST['event_date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $event_type = $_POST['event_type'];
    
    $insert_query = "INSERT INTO calendar_events (event_title, event_description, event_date, start_time, end_time, event_type, created_by) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $db->prepare($insert_query);
    $stmt->execute([$event_title, $event_description, $event_date, $start_time, $end_time, $event_type, $_SESSION['user_id']]);
    
    header("Location: index.php");
    exit();
}

// จัดการลบงาน
if (isset($_GET['delete_event'])) {
    $event_id = $_GET['delete_event'];
    $delete_query = "DELETE FROM calendar_events WHERE id = ?";
    $stmt = $db->prepare($delete_query);
    $stmt->execute([$event_id]);
    
    header("Location: index.php");
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
    <main>
    <div class="container-fluid px-4">
        
    <!-- Page Heading -->
                        

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="mt-4">Dashboard</h1> 
        <a href="reports.php" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-download fa-sm text-white-50"></i> สร้างรายงาน
        </a>
    </div>

    <!-- สถิติครุภัณฑ์ - ขยายเต็ม -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">สถิติครุภัณฑ์</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-3 col-md-3 text-center mb-3">
                            <div class="stat-card total p-3">
                                <div class="text-primary mb-1">
                                    <i class="fas fa-laptop fa-2x"></i>
                                </div>
                                <div class="h4 font-weight-bold"><?php echo $equipment_stats['total']; ?></div>
                                <div class="text-muted">ครุภัณฑ์ทั้งหมด</div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-3 text-center mb-3">
                            <div class="stat-card new p-3">
                                <div class="text-success mb-1">
                                    <i class="fas fa-plus-circle fa-2x"></i>
                                </div>
                                <div class="h4 font-weight-bold"><?php echo $equipment_stats['new']; ?></div>
                                <div class="text-muted">ครุภัณฑ์ใหม่</div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-3 text-center mb-3">
                            <div class="stat-card damaged p-3">
                                <div class="text-warning mb-1">
                                    <i class="fas fa-exclamation-triangle fa-2x"></i>
                                </div>
                                <div class="h4 font-weight-bold"><?php echo $equipment_stats['damaged']; ?></div>
                                <div class="text-muted">ครุภัณฑ์ชำรุด</div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-3 text-center mb-3">
                            <div class="stat-card repair p-3">
                                <div class="text-info mb-1">
                                    <i class="fas fa-tools fa-2x"></i>
                                </div>
                                <div class="h4 font-weight-bold"><?php echo $equipment_stats['repair']; ?></div>
                                <div class="text-muted">กำลังซ่อม</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ปฏิทินใหญ่ - อยู่ใต้สถิติครุภัณฑ์ -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">ปฏิทินงาน</h6>
                    <div>
                        <button type="button" class="btn btn-sm btn-outline-secondary me-2" id="toggleCalendarSize">
                            <i class="fas fa-expand"></i> ขยาย
                        </button>
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addEventModal">
                            <i class="fas fa-plus"></i> เพิ่มงาน
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="calendar" style="min-height: 600px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row - กราฟและอุปกรณ์ที่ซ่อมบ่อย -->
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

    <!-- อุปกรณ์ที่ซ่อมบ่อย - ย้ายมาอยู่ด้านล่างปฏิทิน -->
    <div class="row">
        <div class="col-xl-6 col-lg-6">
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
                                    <h6 class="mb-1"><?php echo $freq['equipment_code']; ?></h6>
                                    <small class="text-muted"><?php echo $freq['equipment_name']; ?></small>
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

        <!-- ประวัติการซ่อมล่าสุด -->
        <div class="col-xl-6 col-lg-6">
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
                                    <td class="fw-bold"><?php echo $maintenance['equipment_code']; ?></td>
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
                                        $status = $maintenance['repair_status'];
                                        $badge_color = isset($status_badge[$status]) ? $status_badge[$status] : 'secondary';
                                        ?>
                                        <span class="badge bg-<?php echo $badge_color; ?>">
                                            <?php echo $status; ?>
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
</div>

<!-- Modal เพิ่มงาน -->
<div class="modal fade" id="addEventModal" tabindex="-1" aria-labelledby="addEventModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addEventModalLabel">เพิ่มงานใหม่</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="event_title" class="form-label">หัวข้องาน</label>
                        <input type="text" class="form-control" id="event_title" name="event_title" required>
                    </div>
                    <div class="mb-3">
                        <label for="event_description" class="form-label">รายละเอียด</label>
                        <textarea class="form-control" id="event_description" name="event_description" rows="3"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="event_date" class="form-label">วันที่</label>
                                <input type="date" class="form-control" id="event_date" name="event_date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="event_type" class="form-label">ประเภทงาน</label>
                                <select class="form-control" id="event_type" name="event_type">
                                    <option value="meeting">ประชุม</option>
                                    <option value="maintenance">ซ่อมบำรุง</option>
                                    <option value="inspection">ตรวจสอบ</option>
                                    <option value="training">ฝึกอบรม</option>
                                    <option value="other">อื่นๆ</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="start_time" class="form-label">เวลาเริ่ม</label>
                                <input type="time" class="form-control" id="start_time" name="start_time">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="end_time" class="form-label">เวลาสิ้นสุด</label>
                                <input type="time" class="form-control" id="end_time" name="end_time">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                    <button type="submit" name="add_event" class="btn btn-primary">บันทึก</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- FullCalendar CSS -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
<!-- FullCalendar JS -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/th.js'></script>

<style>
.calendar-minimized {
    min-height: 400px !important;
    max-height: 400px !important;
    overflow: hidden;
}

.calendar-fullscreen {
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    width: 100vw !important;
    height: 100vh !important;
    z-index: 9999 !important;
    background: white;
    padding: 20px;
}

.calendar-fullscreen .fc-header-toolbar {
    margin-top: 50px !important;
}

.fullscreen-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.8);
    z-index: 9998;
    display: none;
}

.fullscreen-close {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 10000;
    background: #dc3545;
    color: white;
    border: none;
    border-radius: 50%;
    width: 50px;
    height: 50px;
    font-size: 20px;
    cursor: pointer;
    display: none;
    transition: all 0.3s ease;
}

.fullscreen-close:hover {
    background: #c82333;
    transform: scale(1.1);
}

.fc-toolbar {
    flex-wrap: wrap;
}

.fc-toolbar-title {
    font-size: 1.5em !important;
}

@media (max-width: 768px) {
    .fc-toolbar {
        flex-direction: column;
    }
    
    .fc-toolbar-chunk {
        margin-bottom: 10px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let isFullscreen = false;
    const calendarEl = document.getElementById('calendar');
    const toggleBtn = document.getElementById('toggleCalendarSize');
    const fullscreenOverlay = document.createElement('div');
    const closeBtn = document.createElement('button');
    
    // สร้าง overlay และปุ่มปิด
    fullscreenOverlay.className = 'fullscreen-overlay';
    closeBtn.className = 'fullscreen-close';
    closeBtn.innerHTML = '<i class="fas fa-times"></i>';
    document.body.appendChild(fullscreenOverlay);
    document.body.appendChild(closeBtn);

    // FullCalendar
    var calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'th',
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: [
            <?php foreach($calendar_events as $event): ?>
            {
                id: '<?php echo $event['id']; ?>',
                title: '<?php echo $event['event_title']; ?>',
                start: '<?php echo $event['event_date'] . ($event['start_time'] ? 'T' . $event['start_time'] : ''); ?>',
                end: '<?php echo $event['event_date'] . ($event['end_time'] ? 'T' . $event['end_time'] : ''); ?>',
                description: '<?php echo addslashes($event['event_description']); ?>',
                extendedProps: {
                    type: '<?php echo $event['event_type']; ?>'
                },
                backgroundColor: getEventColor('<?php echo $event['event_type']; ?>'),
                borderColor: getEventColor('<?php echo $event['event_type']; ?>')
            },
            <?php endforeach; ?>
        ],
        eventClick: function(info) {
            if (confirm('ต้องการลบงาน "' + info.event.title + '" นี้หรือไม่?')) {
                window.location.href = 'index.php?delete_event=' + info.event.id;
            }
        },
        dateClick: function(info) {
            document.getElementById('event_date').value = info.dateStr;
            $('#addEventModal').modal('show');
        },
        height: 'auto'
    });
    
    calendar.render();

    // ฟังก์ชันสลับขนาดปฏิทิน
    function toggleCalendarSize() {
        if (isFullscreen) {
            // ย่อขนาด
            calendarEl.classList.remove('calendar-fullscreen');
            calendarEl.classList.add('calendar-minimized');
            fullscreenOverlay.style.display = 'none';
            closeBtn.style.display = 'none';
            toggleBtn.innerHTML = '<i class="fas fa-expand"></i> ขยาย';
            toggleBtn.classList.remove('btn-danger');
            toggleBtn.classList.add('btn-outline-secondary');
            
            // ปรับขนาดปฏิทินใหม่
            setTimeout(() => {
                calendar.updateSize();
            }, 100);
            isFullscreen = false;
        } else {
            // ขยายเต็มจอ
            calendarEl.classList.remove('calendar-minimized');
            calendarEl.classList.add('calendar-fullscreen');
            fullscreenOverlay.style.display = 'block';
            closeBtn.style.display = 'block';
            toggleBtn.innerHTML = '<i class="fas fa-compress"></i> ย่อ';
            toggleBtn.classList.remove('btn-outline-secondary');
            toggleBtn.classList.add('btn-danger');
            
            // ปรับขนาดปฏิทินใหม่
            setTimeout(() => {
                calendar.updateSize();
            }, 100);
            isFullscreen = true;
        }
    }

    // Event listeners
    toggleBtn.addEventListener('click', toggleCalendarSize);
    
    closeBtn.addEventListener('click', function() {
        if (isFullscreen) {
            toggleCalendarSize();
        }
    });

    // ปิด fullscreen เมื่อกด ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && isFullscreen) {
            toggleCalendarSize();
        }
    });

    function getEventColor(type) {
        const colors = {
            'meeting': '#1a73e8',
            'maintenance': '#28a745',
            'inspection': '#ffc107',
            'training': '#17a2b8',
            'other': '#6c757d'
        };
        return colors[type] || '#6c757d';
    }

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
                    $labels[] = $status['equipment_status'];
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
// Include footer
include 'includes/footer.php'; 
?>