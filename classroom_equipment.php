<?php
require_once 'includes/header.php';

// ตรวจสอบสิทธิ์การเข้าถึง
if ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'teacher') {
    $_SESSION['error'] = "คุณไม่มีสิทธิ์เข้าถึงหน้านี้";
    header('Location: index.php');
    exit;
}

$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? 0;

// ฟังก์ชันจัดการฟอร์ม
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $room_number = trim($_POST['room_number']);
    $equipment_name = trim($_POST['equipment_name']);
    $quantity = intval($_POST['quantity']);
    $equipment_condition = trim($_POST['equipment_condition']);
    $description = trim($_POST['description']);

    if (empty($room_number) || empty($equipment_name) || $quantity <= 0) {
        $_SESSION['error'] = "กรุณากรอกข้อมูลให้ครบถ้วน";
    } else {
        try {
            if ($action == 'add') {
                $stmt = $db->prepare("INSERT INTO classroom_equipment (room_number, equipment_name, quantity, equipment_condition, description, created_by) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$room_number, $equipment_name, $quantity, $equipment_condition, $description, $_SESSION['user_id']]);
                $_SESSION['success'] = "บันทึกข้อมูลอุปกรณ์ในห้องเรียนเรียบร้อยแล้ว";
            } elseif ($action == 'edit') {
                $stmt = $db->prepare("UPDATE classroom_equipment SET room_number = ?, equipment_name = ?, quantity = ?, equipment_condition = ?, description = ?, updated_at = NOW() WHERE id = ?");
                $stmt->execute([$room_number, $equipment_name, $quantity, $equipment_condition, $description, $id]);
                $_SESSION['success'] = "อัพเดทข้อมูลอุปกรณ์ในห้องเรียนเรียบร้อยแล้ว";
            }
            header('Location: classroom_equipment.php');
            exit;
        } catch (PDOException $e) {
            $_SESSION['error'] = "เกิดข้อผิดพลาด: " . $e->getMessage();
        }
    }
}

// ฟังก์ชันลบข้อมูล
if ($action == 'delete' && $id > 0) {
    try {
        $stmt = $db->prepare("DELETE FROM classroom_equipment WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['success'] = "ลบข้อมูลอุปกรณ์ในห้องเรียนเรียบร้อยแล้ว";
        header('Location: classroom_equipment.php');
        exit;
    } catch (PDOException $e) {
        $_SESSION['error'] = "เกิดข้อผิดพลาด: " . $e->getMessage();
    }
}

// ดึงข้อมูลสำหรับแก้ไข
$equipment_data = [];
if ($action == 'edit' && $id > 0) {
    $stmt = $db->prepare("SELECT * FROM classroom_equipment WHERE id = ?");
    $stmt->execute([$id]);
    $equipment_data = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$equipment_data) {
        $_SESSION['error'] = "ไม่พบข้อมูลอุปกรณ์";
        header('Location: classroom_equipment.php');
        exit;
    }
}

// ดึงรายการทั้งหมด
$search = $_GET['search'] ?? '';
$room_filter = $_GET['room_filter'] ?? '';

$query = "SELECT ce.*, u.fullname as created_by_name 
          FROM classroom_equipment ce 
          LEFT JOIN users u ON ce.created_by = u.id 
          WHERE 1=1";

$params = [];

if (!empty($search)) {
    $query .= " AND (ce.equipment_name LIKE ? OR ce.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($room_filter)) {
    $query .= " AND ce.room_number = ?";
    $params[] = $room_filter;
}

$query .= " ORDER BY ce.room_number, ce.equipment_name";

$stmt = $db->prepare($query);
$stmt->execute($params);
$equipment_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ดึงรายการห้องเรียนทั้งหมดสำหรับ dropdown
$room_query = $db->query("SELECT DISTINCT room_number FROM classroom_equipment ORDER BY room_number");
$room_list = $room_query->fetchAll(PDO::FETCH_COLUMN);
?>

<?php 
// Include sidebar
include 'includes/sidebar.php';
?>

<?php include 'includes/sidebar.php'; ?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <?php include 'includes/navbar.php'; ?>

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <?php 
            if ($action == 'add') echo "เพิ่มอุปกรณ์ในห้องเรียน";
            elseif ($action == 'edit') echo "แก้ไขอุปกรณ์ในห้องเรียน";
            else echo "จัดการอุปกรณ์ในห้องเรียน";
            ?>
        </h1>
        <?php if ($action == 'list'): ?>
            <a href="classroom_equipment.php?action=add" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-plus fa-sm text-white-50"></i> เพิ่มอุปกรณ์
            </a>
        <?php else: ?>
            <a href="classroom_equipment.php" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> กลับหน้ารายการ
            </a>
        <?php endif; ?>
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

    <?php if ($action == 'add' || $action == 'edit'): ?>
        <!-- แสดงฟอร์มเพิ่ม/แก้ไข -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">ข้อมูลอุปกรณ์ในห้องเรียน</h6>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">เลขที่ห้อง <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="room_number" 
                                       value="<?php echo $equipment_data['room_number'] ?? ''; ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">ชื่ออุปกรณ์ <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="equipment_name" 
                                       value="<?php echo $equipment_data['equipment_name'] ?? ''; ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">จำนวน <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="quantity" min="1" 
                                       value="<?php echo $equipment_data['quantity'] ?? 1; ?>" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">สภาพ</label>
                                <select class="form-control" name="equipment_condition">
                                    <option value="ดี" <?php echo ($equipment_data['equipment_condition'] ?? '') == 'ดี' ? 'selected' : ''; ?>>ดี</option>
                                    <option value="ปานกลาง" <?php echo ($equipment_data['equipment_condition'] ?? '') == 'ปานกลาง' ? 'selected' : ''; ?>>ปานกลาง</option>
                                    <option value="ชำรุด" <?php echo ($equipment_data['equipment_condition'] ?? '') == 'ชำรุด' ? 'selected' : ''; ?>>ชำรุด</option>
                                    <option value="กำลังซ่อม" <?php echo ($equipment_data['equipment_condition'] ?? '') == 'กำลังซ่อม' ? 'selected' : ''; ?>>กำลังซ่อม</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">วันที่บันทึก</label>
                                <input type="text" class="form-control" 
                                       value="<?php echo date('d/m/Y H:i', strtotime($equipment_data['created_at'] ?? 'now')); ?>" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">รายละเอียดเพิ่มเติม</label>
                        <textarea class="form-control" name="description" rows="3"><?php echo $equipment_data['description'] ?? ''; ?></textarea>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="classroom_equipment.php" class="btn btn-secondary">ยกเลิก</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> <?php echo $action == 'add' ? 'บันทึก' : 'อัพเดท'; ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    <?php else: ?>
        <!-- แสดงรายการ -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">รายการอุปกรณ์ในห้องเรียน</h6>
            </div>
            <div class="card-body">
                <!-- ฟอร์มค้นหาและกรอง -->
                <form method="GET" class="row g-3 mb-4">
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="search" placeholder="ค้นหาชื่ออุปกรณ์หรือรายละเอียด..." 
                               value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <div class="col-md-3">
                        <select class="form-control" name="room_filter">
                            <option value="">ทั้งหมด</option>
                            <?php foreach($room_list as $room): ?>
                                <option value="<?php echo $room; ?>" <?php echo $room_filter == $room ? 'selected' : ''; ?>>
                                    ห้อง <?php echo $room; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i> ค้นหา
                        </button>
                    </div>
                    <div class="col-md-3 text-end">
                        <a href="classroom_equipment.php" class="btn btn-secondary">ล้างการค้นหา</a>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                        <thead class="table-light">
                            <tr>
                                <th>ห้อง</th>
                                <th>อุปกรณ์</th>
                                <th>จำนวน</th>
                                <th>สภาพ</th>
                                <th>บันทึกโดย</th>
                                <th>วันที่บันทึก</th>
                                <th>จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($equipment_list) > 0): ?>
                                <?php foreach($equipment_list as $item): ?>
                                <tr>
                                    <td class="fw-bold"><?php echo $item['room_number']; ?></td>
                                    <td>
                                        <div class="fw-bold"><?php echo $item['equipment_name']; ?></div>
                                        <?php if (!empty($item['description'])): ?>
                                            <small class="text-muted"><?php echo $item['description']; ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $item['quantity']; ?></td>
                                    <td>
                                        <?php
                                        $condition_badge = [
                                            'ดี' => 'success',
                                            'ปานกลาง' => 'warning',
                                            'ชำรุด' => 'danger',
                                            'กำลังซ่อม' => 'info'
                                        ];
                                        ?>
                                        <span class="badge bg-<?php echo $condition_badge[$item['equipment_condition']] ?? 'secondary'; ?>">
                                            <?php echo $item['equipment_condition']; ?>
                                        </span>
                                    </td>
                                    <td><?php echo $item['created_by_name']; ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($item['created_at'])); ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="classroom_equipment.php?action=edit&id=<?php echo $item['id']; ?>" 
                                               class="btn btn-primary" title="แก้ไข">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="classroom_equipment.php?action=delete&id=<?php echo $item['id']; ?>" 
                                               class="btn btn-danger" title="ลب"
                                               onclick="return confirm('คุณแน่ใจหรือไม่ที่จะลบข้อมูลนี้?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                        ไม่พบข้อมูลอุปกรณ์ในห้องเรียน
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</main>

<?php include 'includes/footer.php'; ?>