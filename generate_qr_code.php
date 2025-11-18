<?php
// generate_qr_code.php
// ปิด error display แต่เปิด logging
error_reporting(E_ALL);
ini_set('display_errors', 0); // เปลี่ยนเป็น 0 เพื่อไม่ให้แสดง error ใน output
ini_set('log_errors', 1);
ini_set('error_log', 'error_log.txt');

// เริ่ม output buffering เพื่อป้องกัน output ที่ไม่ต้องการ
ob_start();

// บันทึก error log
error_log("=== QR Code Generation Started ===");

// ตรวจสอบว่า includes/header.php มีอยู่จริง
if (!file_exists('includes/header.php')) {
    ob_clean(); // ล้าง buffer
    header('Content-Type: application/json; charset=utf-8');
    error_log("ERROR: includes/header.php not found");
    echo json_encode(['success' => false, 'message' => 'ไฟล์ระบบไม่ครบถ้วน']);
    exit;
}

require_once 'includes/header.php';
error_log("Header loaded successfully");

// ตรวจสอบ session
if (!isset($_SESSION['role'])) {
    ob_clean();
    header('Content-Type: application/json; charset=utf-8');
    error_log("ERROR: Session not set");
    echo json_encode(['success' => false, 'message' => 'กรุณาเข้าสู่ระบบใหม่']);
    exit;
}

// ตรวจสอบสิทธิ์
if ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'teacher') {
    ob_clean();
    header('Content-Type: application/json; charset=utf-8');
    error_log("ERROR: Unauthorized access - Role: " . $_SESSION['role']);
    echo json_encode(['success' => false, 'message' => 'ไม่มีสิทธิ์ใช้งาน']);
    exit;
}

error_log("User authorized: " . $_SESSION['role']);

// ตรวจสอบว่าไฟล์ phpqrcode มีอยู่จริง
$phpqrcode_path = 'includes/phpqrcode/qrlib.php';
if (!file_exists($phpqrcode_path)) {
    ob_clean();
    header('Content-Type: application/json; charset=utf-8');
    error_log("ERROR: phpqrcode not found at: " . $phpqrcode_path);
    echo json_encode(['success' => false, 'message' => 'ไม่พบไลบรารีสร้าง QR Code']);
    exit;
}

// ใช้ไลบรารี PHP QR Code
require_once $phpqrcode_path;
error_log("PHP QR Code library loaded");

// สร้างโฟลเดอร์เก็บ QR Code ถ้ายังไม่มี
$qrCodeDir = 'qrcodes/';
if (!file_exists($qrCodeDir)) {
    error_log("Creating QR code directory: " . $qrCodeDir);
    if (!mkdir($qrCodeDir, 0755, true)) {
        ob_clean();
        header('Content-Type: application/json; charset=utf-8');
        error_log("ERROR: Cannot create directory: " . $qrCodeDir);
        echo json_encode(['success' => false, 'message' => 'ไม่สามารถสร้างโฟลเดอร์เก็บ QR Code']);
        exit;
    }
    error_log("QR code directory created successfully");
}

// ตรวจสอบสิทธิ์การเขียนในโฟลเดอร์
if (!is_writable($qrCodeDir)) {
    ob_clean();
    header('Content-Type: application/json; charset=utf-8');
    error_log("ERROR: QR code directory is not writable: " . $qrCodeDir);
    echo json_encode(['success' => false, 'message' => 'โฟลเดอร์เก็บ QR Code ไม่สามารถเขียนได้']);
    exit;
}

error_log("QR code directory is writable");

function generateQRCode($equipmentCode) {
    global $db, $qrCodeDir;
    
    error_log("Generating QR code for: " . $equipmentCode);
    
    try {
        // ตรวจสอบว่ามีครุภัณฑ์นี้ในระบบหรือไม่
        $stmt = $db->prepare("SELECT * FROM equipment WHERE equipment_code = ?");
        $stmt->execute([$equipmentCode]);
        $equipment = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$equipment) {
            error_log("ERROR: Equipment not found in equipment table: " . $equipmentCode);
            return ['success' => false, 'message' => 'ไม่พบรหัสครุภัณฑ์ในระบบ'];
        }
        
        error_log("Equipment found in equipment table: " . $equipment['equipment_name']);
        
        // ตรวจสอบว่ามีใน equipment_classroom หรือไม่
        $stmt2 = $db->prepare("SELECT * FROM equipment_classroom WHERE equipment_code = ?");
        $stmt2->execute([$equipmentCode]);
        $classroom_equipment = $stmt2->fetch(PDO::FETCH_ASSOC);
        
        if (!$classroom_equipment) {
            error_log("ERROR: Equipment not found in equipment_classroom table: " . $equipmentCode);
            return ['success' => false, 'message' => 'ไม่พบการจัดวางครุภัณฑ์นี้ในห้องเรียน'];
        }
        
        error_log("Equipment found in equipment_classroom table");
        
        // ข้อมูลที่จะใส่ใน QR Code
        $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
        $scriptPath = dirname($_SERVER['PHP_SELF']);
        if ($scriptPath != '/') {
            $baseUrl .= $scriptPath;
        }
        $qrData = $baseUrl . "/equipment_info.php?code=" . urlencode($equipmentCode);
        
        error_log("QR Data: " . $qrData);
        
        // ชื่อไฟล์ QR Code
        $filename = $qrCodeDir . 'equipment_' . $equipmentCode . '.png';
        error_log("QR File path: " . $filename);
        
        // ลบไฟล์เก่าถ้ามี
        if (file_exists($filename)) {
            unlink($filename);
            error_log("Old QR code deleted");
        }
        
        // สร้าง QR Code
        error_log("Creating QR code image...");
        
        try {
            // สร้าง QR Code ด้วยขนาดที่เหมาะสม
            // QR_ECLEVEL_L = Low error correction
            // size = 6 (ขนาดของแต่ละ pixel)
            // margin = 2 (ระยะขอบ)
            QRcode::png($qrData, $filename, QR_ECLEVEL_L, 6, 2);
            error_log("QR code image created");
        } catch (Exception $e) {
            error_log("ERROR creating QR image: " . $e->getMessage());
            return ['success' => false, 'message' => 'ไม่สามารถสร้างภาพ QR Code: ' . $e->getMessage()];
        }
        
        // รอสักครู่เพื่อให้แน่ใจว่าไฟล์ถูกเขียนเสร็จ
        usleep(100000); // 0.1 วินาที
        
        // ตรวจสอบว่าไฟล์ถูกสร้างจริงๆ
        if (!file_exists($filename)) {
            error_log("ERROR: QR code file was not created at: " . $filename);
            return ['success' => false, 'message' => 'ไม่สามารถสร้างไฟล์ QR Code'];
        }
        
        $fileSize = filesize($filename);
        
        // ตรวจสอบว่าไฟล์มีขนาดมากกว่า 0
        if ($fileSize == 0) {
            error_log("ERROR: QR code file is empty");
            return ['success' => false, 'message' => 'ไฟล์ QR Code ว่างเปล่า'];
        }
        
        error_log("QR code file created successfully: " . $filename . " (Size: " . $fileSize . " bytes)");
        
        // บันทึกข้อมูลลงฐานข้อมูล
        try {
            $updateStmt = $db->prepare("UPDATE equipment_classroom SET qr_code_path = ?, qr_code_generated_at = NOW() WHERE equipment_code = ?");
            $result = $updateStmt->execute([$filename, $equipmentCode]);
            
            if ($result) {
                error_log("Database updated successfully");
                $affectedRows = $updateStmt->rowCount();
                error_log("Affected rows: " . $affectedRows);
            } else {
                error_log("WARNING: Database update returned false");
            }
        } catch (PDOException $e) {
            error_log("ERROR updating database: " . $e->getMessage());
            // ยังคงส่งผลสำเร็จเพราะไฟล์ถูกสร้างแล้ว
        }
        
        return [
            'success' => true, 
            'message' => 'สร้าง QR Code สำเร็จ', 
            'file_path' => $filename,
            'file_size' => $fileSize,
            'equipment_code' => $equipmentCode
        ];
        
    } catch (PDOException $e) {
        error_log("Database ERROR: " . $e->getMessage());
        return ['success' => false, 'message' => 'เกิดข้อผิดพลาดในฐานข้อมูล: ' . $e->getMessage()];
    } catch (Exception $e) {
        error_log("General ERROR: " . $e->getMessage());
        return ['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()];
    }
}

// จัดการกับ request
error_log("Request method: " . $_SERVER['REQUEST_METHOD']);
error_log("POST data: " . print_r($_POST, true));

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $equipmentCode = $_POST['equipment_code'] ?? '';
    $action = $_POST['action'] ?? 'single';
    
    error_log("Processing POST request for equipment: " . $equipmentCode);
    
    if (empty($equipmentCode)) {
        ob_clean();
        header('Content-Type: application/json; charset=utf-8');
        error_log("ERROR: Empty equipment code");
        echo json_encode(['success' => false, 'message' => 'ไม่มีรหัสครุภัณฑ์'], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    // ทำความสะอาดรหัสครุภัณฑ์
    $equipmentCode = trim($equipmentCode);
    
    $result = generateQRCode($equipmentCode);
    error_log("Generation result: " . print_r($result, true));
    
    // ล้าง output buffer และส่ง JSON response
    ob_clean();
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
    
    error_log("=== QR Code Generation Finished ===");
    exit;
    
} elseif (isset($_GET['bulk']) && $_GET['bulk'] == '1') {
    // โหมดสร้าง QR Code ทั้งหมด
    ob_clean(); // ล้าง buffer ก่อนส่ง HTML
    
    header('Content-Type: text/html; charset=utf-8');
    echo '<!DOCTYPE html>
    <html lang="th">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>สร้าง QR Code ทั้งหมด</title>
        <style>
            body { font-family: "Sarabun", Arial, sans-serif; margin: 20px; background: #f5f5f5; }
            .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
            .progress { background: #f0f0f0; border-radius: 5px; margin: 10px 0; padding: 10px; }
            .progress-bar { background: #007bff; color: white; padding: 5px; border-radius: 5px; text-align: center; transition: width 0.3s; }
            .success { color: green; padding: 5px; margin: 2px 0; }
            .error { color: red; padding: 5px; margin: 2px 0; }
            .info { color: #0066cc; padding: 5px; margin: 2px 0; }
            .summary { background: #e7f3ff; padding: 15px; border-radius: 5px; margin-top: 20px; }
        </style>
    </head>
    <body>
        <div class="container">
            <h2>🔄 กำลังสร้าง QR Code สำหรับครุภัณฑ์ทั้งหมด</h2>
            <div id="progress"></div>
        </div>
        <script>
            function updateProgress(message, className = "info") {
                const progress = document.getElementById("progress");
                const div = document.createElement("div");
                div.className = className;
                div.innerHTML = message;
                progress.appendChild(div);
                window.scrollTo(0, document.body.scrollHeight);
            }
        </script>';
    
    flush();
    
    try {
        // ดึงครุภัณฑ์ทั้งหมดที่ยังไม่มี QR Code
        $stmt = $db->prepare("SELECT DISTINCT equipment_code FROM equipment_classroom WHERE (qr_code_path IS NULL OR qr_code_path = '') AND equipment_code IN (SELECT equipment_code FROM equipment)");
        $stmt->execute();
        $equipments = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        $total = count($equipments);
        $successCount = 0;
        $errorCount = 0;
        
        echo "<script>updateProgress('📊 พบครุภัณฑ์ที่ต้องสร้าง QR Code: <strong>$total รายการ</strong>', 'info');</script>";
        flush();
        
        foreach ($equipments as $index => $equipmentCode) {
            $current = $index + 1;
            $percent = round(($current / $total) * 100);
            
            echo "<script>updateProgress('[$current/$total - $percent%] ⏳ กำลังสร้าง QR Code สำหรับ: <strong>$equipmentCode</strong>', 'info');</script>";
            flush();
            
            $result = generateQRCode($equipmentCode);
            
            if ($result['success']) {
                $successCount++;
                echo "<script>updateProgress('✅ สร้างสำเร็จ: <strong>$equipmentCode</strong>', 'success');</script>";
            } else {
                $errorCount++;
                $errorMsg = htmlspecialchars($result['message']);
                echo "<script>updateProgress('❌ ล้มเหลว: <strong>$equipmentCode</strong> - $errorMsg', 'error');</script>";
            }
            
            flush();
            usleep(300000); // พัก 0.3 วินาที
        }
        
        echo "<script>updateProgress('<div class=\"summary\"><h3>📋 สรุปผลการสร้าง QR Code</h3><p>✅ สร้างสำเร็จ: <strong>$successCount</strong> รายการ</p><p>❌ ล้มเหลว: <strong>$errorCount</strong> รายการ</p><p>📊 รวมทั้งหมด: <strong>$total</strong> รายการ</p></div>', 'info');</script>";
        
        if ($errorCount == 0) {
            echo "<script>updateProgress('🎉 สร้าง QR Code ทั้งหมดสำเร็จ!', 'success');</script>";
        }
        
    } catch (PDOException $e) {
        $errorMsg = htmlspecialchars($e->getMessage());
        echo "<script>updateProgress('❌ เกิดข้อผิดพลาด: $errorMsg', 'error');</script>";
    }
    
    echo '<script>
        updateProgress("<p style=\"margin-top: 20px;\"><button onclick=\"window.close()\" style=\"padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;\">ปิดหน้าต่าง</button></p>", "info");
    </script>';
    
    echo '</body></html>';
    
    exit;
    
} else {
    ob_clean();
    header('Content-Type: application/json; charset=utf-8');
    error_log("ERROR: Invalid request method or parameters");
    echo json_encode(['success' => false, 'message' => 'คำร้องขอไม่ถูกต้อง'], JSON_UNESCAPED_UNICODE);
    exit;
}

error_log("=== QR Code Generation Finished ===");
?>