<?php
// equipment_info.php - สำหรับแสดงข้อมูลครุภัณฑ์เมื่อสแกน QR Code
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ตรวจสอบว่ามีการเชื่อมต่อฐานข้อมูลหรือไม่
if (!file_exists('includes/header.php')) {
    die('ระบบไม่พร้อมใช้งาน');
}

require_once 'includes/header.php';

$equipmentCode = $_GET['code'] ?? '';

if (empty($equipmentCode)) {
    die('ไม่ระบุรหัสครุภัณฑ์');
}

try {
    $stmt = $db->prepare("SELECT ec.*, e.equipment_name, e.brand_name, e.model_name, e.specifications, e.purchase_date, e.warranty_period 
                         FROM equipment_classroom ec 
                         LEFT JOIN equipment e ON ec.equipment_code = e.equipment_code 
                         WHERE ec.equipment_code = ?");
    $stmt->execute([$equipmentCode]);
    $equipment = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$equipment) {
        die('ไม่พบข้อมูลครุภัณฑ์');
    }
    
} catch (PDOException $e) {
    die('เกิดข้อผิดพลาด: ' . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ข้อมูลครุภัณฑ์ - <?php echo htmlspecialchars($equipmentCode); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { 
            font-family: 'Sarabun', sans-serif; 
            margin: 0; 
            padding: 20px; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .container { 
            max-width: 800px; 
            margin: 20px auto; 
            background: white; 
            padding: 30px; 
            border-radius: 15px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .header { 
            text-align: center; 
            margin-bottom: 30px; 
            border-bottom: 3px solid #007bff; 
            padding-bottom: 20px;
        }
        .header h1 { 
            color: #333; 
            margin-bottom: 10px;
        }
        .info-section { 
            margin: 25px 0; 
            padding: 20px; 
            background: #f8f9fa; 
            border-radius: 10px; 
            border-left: 4px solid #007bff;
        }
        .info-section h3 { 
            color: #007bff; 
            margin-bottom: 15px; 
            display: flex; 
            align-items: center;
        }
        .info-section h3 i { 
            margin-right: 10px;
        }
        .info-grid { 
            display: grid; 
            grid-template-columns: 1fr 1fr; 
            gap: 15px;
        }
        .info-item { 
            display: flex; 
            justify-content: space-between; 
            padding: 8px 0; 
            border-bottom: 1px solid #e9ecef;
        }
        .info-label { 
            font-weight: bold; 
            color: #495057;
        }
        .info-value { 
            color: #212529;
        }
        .status-badge { 
            padding: 4px 12px; 
            border-radius: 20px; 
            font-size: 14px; 
            font-weight: bold;
        }
        .status-available { 
            background: #d4edda; 
            color: #155724;
        }
        .specifications { 
            white-space: pre-line; 
            line-height: 1.6;
        }
        @media (max-width: 768px) {
            .info-grid { grid-template-columns: 1fr; }
            .container { padding: 20px; margin: 10px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-laptop-code"></i> ข้อมูลครุภัณฑ์</h1>
            <p class="lead">รหัสครุภัณฑ์: <strong><?php echo htmlspecialchars($equipmentCode); ?></strong></p>
        </div>
        
        <!-- ข้อมูลครุภัณฑ์ -->
        <div class="info-section">
            <h3><i class="fas fa-info-circle"></i> ข้อมูลทั่วไป</h3>
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">รหัสครุภัณฑ์:</span>
                    <span class="info-value"><?php echo htmlspecialchars($equipmentCode); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">ชื่อครุภัณฑ์:</span>
                    <span class="info-value"><?php echo htmlspecialchars($equipment['equipment_name'] ?? '-'); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">ยี่ห้อ:</span>
                    <span class="info-value"><?php echo htmlspecialchars($equipment['brand_name'] ?? '-'); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">รุ่น:</span>
                    <span class="info-value"><?php echo htmlspecialchars($equipment['model_name'] ?? '-'); ?></span>
                </div>
            </div>
        </div>
        
        <!-- ตำแหน่งที่ตั้ง -->
        <div class="info-section">
            <h3><i class="fas fa-map-marker-alt"></i> ตำแหน่งที่ตั้ง</h3>
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">โรงเรียน:</span>
                    <span class="info-value"><?php echo htmlspecialchars($equipment['school_name']); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">อาคาร:</span>
                    <span class="info-value"><?php echo htmlspecialchars($equipment['building_name']); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">ชั้น:</span>
                    <span class="info-value"><?php echo htmlspecialchars($equipment['floor_level']); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">ห้อง:</span>
                    <span class="info-value">
                        <?php echo htmlspecialchars($equipment['room_number']); ?>
                        <?php if (!empty($equipment['room_name'])): ?>
                            (<?php echo htmlspecialchars($equipment['room_name']); ?>)
                        <?php endif; ?>
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">จำนวน:</span>
                    <span class="info-value">
                        <span class="status-badge status-available">
                            <?php echo $equipment['equipment_quantity']; ?> ชิ้น
                        </span>
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">วันที่ติดตั้ง:</span>
                    <span class="info-value">
                        <?php echo !empty($equipment['installation_date']) ? date('d/m/Y', strtotime($equipment['installation_date'])) : '-'; ?>
                    </span>
                </div>
            </div>
        </div>
        
        <!-- ข้อมูลเพิ่มเติม -->
        <div class="info-section">
            <h3><i class="fas fa-clipboard-list"></i> ข้อมูลเพิ่มเติม</h3>
            <?php if (!empty($equipment['specifications'])): ?>
                <div style="margin-bottom: 15px;">
                    <strong>รายละเอียดคุณสมบัติ:</strong>
                    <div class="specifications"><?php echo htmlspecialchars($equipment['specifications']); ?></div>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($equipment['placement_notes'])): ?>
                <div style="margin-bottom: 15px;">
                    <strong>หมายเหตุการจัดวาง:</strong>
                    <div><?php echo htmlspecialchars($equipment['placement_notes']); ?></div>
                </div>
            <?php endif; ?>
        </div>
        
        <div style="text-align: center; margin-top: 30px; color: #6c757d; font-size: 14px;">
            <p>สแกนเมื่อ: <?php echo date('d/m/Y H:i'); ?></p>
            <p>ระบบจัดการครุภัณฑ์ในห้องเรียน</p>
        </div>
    </div>
</body>
</html>