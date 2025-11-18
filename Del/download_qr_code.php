<?php
// download_qr_code.php
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
    $stmt = $db->prepare("SELECT qr_code_path, equipment_code FROM equipment_classroom WHERE equipment_code = ?");
    $stmt->execute([$equipmentCode]);
    $equipment = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$equipment || empty($equipment['qr_code_path']) || !file_exists($equipment['qr_code_path'])) {
        die('ไม่พบไฟล์ QR Code');
    }
    
    $filepath = $equipment['qr_code_path'];
    $filename = 'QRCode_' . $equipmentCode . '.png';
    
    // ตั้งค่า header สำหรับดาวน์โหลด
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($filepath));
    
    readfile($filepath);
    exit;
    
} catch (PDOException $e) {
    die('เกิดข้อผิดพลาด: ' . $e->getMessage());
}
?>