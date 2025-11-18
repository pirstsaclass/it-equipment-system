<?php
// get_qr_code_image.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once 'config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'teacher')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'ไม่มีสิทธิ์เข้าถึง']);
    exit;
}

if (!isset($_GET['equipment_code']) || empty($_GET['equipment_code'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '缺少設備代碼']);
    exit;
}

$equipment_code = trim($_GET['equipment_code']);

try {
    // ดึงข้อมูล QR Code path จาก database
    $stmt = $db->prepare("SELECT qr_code_path FROM equipment_classroom WHERE equipment_code = ?");
    $stmt->execute([$equipment_code]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$result || empty($result['qr_code_path'])) {
        echo json_encode([
            'success' => false, 
            'message' => 'ไม่พบ QR Code สำหรับครุภัณฑ์นี้'
        ]);
        exit;
    }
    
    $qr_code_path = $result['qr_code_path'];
    
    // ตรวจสอบว่าไฟล์มีอยู่จริง
    if (!file_exists($qr_code_path)) {
        echo json_encode([
            'success' => false, 
            'message' => 'ไฟล์ QR Code ไม่พบในเซิร์ฟเวอร์'
        ]);
        exit;
    }
    
    // ตรวจสอบประเภทไฟล์
    $allowed_types = ['image/png', 'image/jpeg', 'image/jpg', 'image/gif'];
    $file_info = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($file_info, $qr_code_path);
    finfo_close($file_info);
    
    if (!in_array($mime_type, $allowed_types)) {
        echo json_encode([
            'success' => false, 
            'message' => 'ไฟล์ไม่ใช่รูปภาพที่รองรับ'
        ]);
        exit;
    }
    
    // ส่งข้อมูลกลับ
    echo json_encode([
        'success' => true,
        'qr_code_path' => $qr_code_path,
        'file_size' => filesize($qr_code_path),
        'mime_type' => $mime_type
    ]);
    
} catch (PDOException $e) {
    error_log("Database error in get_qr_code_image.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'เกิดข้อผิดพลาดในฐานข้อมูล'
    ]);
} catch (Exception $e) {
    error_log("Error in get_qr_code_image.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'เกิดข้อผิดพลาดทั่วไป'
    ]);
}
?>