<?php
// ajax_get_check_details.php
require_once 'includes/config.php';
require_once 'includes/functions.php';

header('Content-Type: application/json; charset=utf-8');

$id = $_GET['id'] ?? 0;
$action = $_GET['action'] ?? 'view';

if ($id <= 0) {
    if ($action == 'edit') {
        echo json_encode(['success' => false, 'message' => 'ไม่พบข้อมูล']);
    } else {
        echo '<div class="alert alert-danger">ไม่พบข้อมูล</div>';
    }
    exit;
}

try {
    $stmt = $db->prepare("
        SELECT 
            ec.*,
            eqc.equipment_code,
            eq.equipment_name,
            eq.brand_name,
            eq.model_name,
            eqc.school_name,
            eqc.building_name,
            eqc.floor_level,
            eqc.room_number,
            eqc.room_name
        FROM equipment_checks ec
        JOIN equipment_classroom eqc ON ec.equipment_classroom_id = eqc.id
        LEFT JOIN equipment eq ON eqc.equipment_code = eq.equipment_code
        WHERE ec.id = ?
    ");
    $stmt->execute([$id]);
    $check = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$check) {
        if ($action == 'edit') {
            echo json_encode(['success' => false, 'message' => 'ไม่พบข้อมูล']);
        } else {
            echo '<div class="alert alert-danger">ไม่พบข้อมูลการตรวจสอบ</div>';
        }
        exit;
    }
    
    if ($action == 'edit') {
        echo json_encode(['success' => true, 'check' => $check]);
    } else {
        // HTML response for view modal
        ?>
        <div class="row">
            <div class="col-md-6">
                <h6 class="fw-bold text-primary">ข้อมูลครุภัณฑ์</h6>
                <table class="table table-sm table-bordered">
                    <tr>
                        <th width="40%">รหัสครุภัณฑ์</th>
                        <td><?php echo htmlspecialchars($check['equipment_code']); ?></td>
                    </tr>
                    <tr>
                        <th>ชื่อครุภัณฑ์</th>
                        <td><?php echo htmlspecialchars($check['equipment_name']); ?></td>
                    </tr>
                    <tr>
                        <th>ยี่ห้อ/รุ่น</th>
                        <td>
                            <?php echo htmlspecialchars($check['brand_name'] ?? '-'); ?>
                            <?php if (!empty($check['model_name'])): ?>
                                / <?php echo htmlspecialchars($check['model_name']); ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6 class="fw-bold text-primary">ตำแหน่งที่ตั้ง</h6>
                <table class="table table-sm table-bordered">
                    <tr>
                        <th width="40%">โรงเรียน</th>
                        <td><?php echo htmlspecialchars($check['school_name']); ?></td>
                    </tr>
                    <tr>
                        <th>อาคาร</th>
                        <td><?php echo htmlspecialchars($check['building_name']); ?></td>
                    </tr>
                    <tr>
                        <th>ชั้น/ห้อง</th>
                        <td>
                            ชั้น <?php echo htmlspecialchars($check['floor_level']); ?> 
                            ห้อง <?php echo htmlspecialchars($check['room_number']); ?>
                            <?php if (!empty($check['room_name'])): ?>
                                (<?php echo htmlspecialchars($check['room_name']); ?>)
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        
        <div class="row mt-3">
            <div class="col-12">
                <h6 class="fw-bold text-primary">รายละเอียดการตรวจสอบ</h6>
                <table class="table table-sm table-bordered">
                    <tr>
                        <th width="20%">ผู้ตรวจสอบ</th>
                        <td><?php echo htmlspecialchars($check['checked_by']); ?></td>
                    </tr>
                    <tr>
                        <th>วันที่ตรวจสอบ</th>
                        <td><?php echo date('d/m/Y H:i', strtotime($check['check_date'])); ?></td>
                    </tr>
                    <tr>
                        <th>สถานะ</th>
                        <td>
                            <span class="badge <?php echo getStatusBadgeClass($check['status']); ?>">
                                <?php echo $check['status']; ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>หมายเหตุ</th>
                        <td><?php echo nl2br(htmlspecialchars($check['notes'] ?? '-')); ?></td>
                    </tr>
                    <tr>
                        <th>วันที่บันทึก</th>
                        <td><?php echo date('d/m/Y H:i', strtotime($check['created_at'])); ?></td>
                    </tr>
                    <?php if ($check['updated_at'] != $check['created_at']): ?>
                    <tr>
                        <th>อัพเดทล่าสุด</th>
                        <td><?php echo date('d/m/Y H:i', strtotime($check['updated_at'])); ?></td>
                    </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>
        
        <?php if (!empty($check['images'])): ?>
        <?php $images = json_decode($check['images'], true); ?>
        <?php if (is_array($images) && count($images) > 0): ?>
        <div class="row mt-3">
            <div class="col-12">
                <h6 class="fw-bold text-primary">รูปภาพ</h6>
                <div class="row">
                    <?php foreach($images as $image): ?>
                    <div class="col-md-3 mb-2">
                        <img src="<?php echo htmlspecialchars($image); ?>" class="img-thumbnail w-100" 
                             style="max-height: 150px; object-fit: cover;" 
                             onclick="window.open('<?php echo htmlspecialchars($image); ?>', '_blank')">
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <?php endif; ?>
        <?php
    }
    
} catch (PDOException $e) {
    if ($action == 'edit') {
        echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
    } else {
        echo '<div class="alert alert-danger">เกิดข้อผิดพลาด: ' . $e->getMessage() . '</div>';
    }
}

function getStatusBadgeClass($status) {
    switch($status) {
        case 'ปกติ': return 'bg-success';
        case 'ชำรุด': return 'bg-warning';
        case 'ซ่อมแซม': return 'bg-info';
        case 'สูญหาย': return 'bg-danger';
        default: return 'bg-secondary';
    }
}
?>