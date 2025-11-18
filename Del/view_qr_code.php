<?php
// view_qr_code.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'includes/header.php';

if ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'teacher') {
    $_SESSION['error'] = "คุณไม่มีสิทธิ์เข้าถึงหน้านี้";
    header('Location: index.php');
    exit;
}

$equipmentCode = $_GET['equipment_code'] ?? '';

if (empty($equipmentCode)) {
    die('ไม่ระบุรหัสครุภัณฑ์');
}

try {
    $stmt = $db->prepare("SELECT ec.*, e.equipment_name, e.brand_name, e.model_name 
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
    <title>QR Code - <?php echo htmlspecialchars($equipmentCode); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { 
            font-family: 'Sarabun', sans-serif; 
            margin: 0; 
            padding: 20px; 
            background: #f8f9fa;
        }
        .container { 
            max-width: 600px; 
            margin: 0 auto; 
            background: white; 
            padding: 30px; 
            border-radius: 10px; 
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header { 
            text-align: center; 
            margin-bottom: 30px; 
            border-bottom: 2px solid #007bff; 
            padding-bottom: 15px;
        }
        .qr-container { 
            text-align: center; 
            margin: 20px 0; 
            padding: 20px; 
            border: 2px dashed #dee2e6; 
            border-radius: 10px;
        }
        .qr-image { 
            max-width: 300px; 
            height: auto; 
            margin: 0 auto;
        }
        .info-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin: 20px 0;
        }
        .info-table td { 
            padding: 10px; 
            border-bottom: 1px solid #dee2e6;
        }
        .info-table td:first-child { 
            font-weight: bold; 
            width: 30%; 
            color: #495057;
        }
        .btn-group { 
            text-align: center; 
            margin-top: 20px;
        }
        .btn { 
            padding: 10px 20px; 
            margin: 0 5px; 
            border: none; 
            border-radius: 5px; 
            cursor: pointer; 
            text-decoration: none; 
            display: inline-block;
        }
        .btn-primary { 
            background: #007bff; 
            color: white;
        }
        .btn-success { 
            background: #28a745; 
            color: white;
        }
        .no-qr { 
            text-align: center; 
            color: #dc3545; 
            padding: 40px;
        }
        @media print {
            body { background: white; }
            .container { box-shadow: none; }
            .btn-group { display: none; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>QR Code ครุภัณฑ์</h1>
            <p>สแกนเพื่อดูข้อมูลครุภัณฑ์</p>
        </div>
        
        <?php if (!empty($equipment['qr_code_path']) && file_exists($equipment['qr_code_path'])): ?>
            <div class="qr-container">
                <img src="<?php echo $equipment['qr_code_path']; ?>" alt="QR Code" class="qr-image">
                <p class="mt-3 text-muted">รหัสครุภัณฑ์: <strong><?php echo htmlspecialchars($equipmentCode); ?></strong></p>
            </div>
            
            <table class="info-table">
                <tr>
                    <td>รหัสครุภัณฑ์:</td>
                    <td><?php echo htmlspecialchars($equipmentCode); ?></td>
                </tr>
                <tr>
                    <td>ชื่อครุภัณฑ์:</td>
                    <td><?php echo htmlspecialchars($equipment['equipment_name'] ?? '-'); ?></td>
                </tr>
                <tr>
                    <td>โรงเรียน:</td>
                    <td><?php echo htmlspecialchars($equipment['school_name']); ?></td>
                </tr>
                <tr>
                    <td>อาคาร/ชั้น:</td>
                    <td><?php echo htmlspecialchars($equipment['building_name'] . ' ชั้น ' . $equipment['floor_level']); ?></td>
                </tr>
                <tr>
                    <td>ห้อง:</td>
                    <td><?php echo htmlspecialchars($equipment['room_number'] . ($equipment['room_name'] ? ' (' . $equipment['room_name'] . ')' : '')); ?></td>
                </tr>
                <tr>
                    <td>วันที่สร้าง QR:</td>
                    <td><?php echo $equipment['qr_code_generated_at'] ? date('d/m/Y H:i', strtotime($equipment['qr_code_generated_at'])) : '-'; ?></td>
                </tr>
            </table>
            
            <div class="btn-group">
                <button class="btn btn-primary" onclick="window.print()">
                    <i class="fas fa-print"></i> พิมพ์
                </button>
                <a href="download_qr_code.php?equipment_code=<?php echo $equipmentCode; ?>" class="btn btn-success">
                    <i class="fas fa-download"></i> ดาวน์โหลด
                </a>
                <button class="btn btn-secondary" onclick="window.close()">
                    <i class="fas fa-times"></i> ปิด
                </button>
            </div>
        <?php else: ?>
            <div class="no-qr">
                <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                <h3>ไม่พบ QR Code</h3>
                <p>กรุณาสร้าง QR Code สำหรับครุภัณฑ์นี้ก่อน</p>
                <button class="btn btn-primary" onclick="history.back()">
                    <i class="fas fa-arrow-left"></i> กลับ
                </button>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>