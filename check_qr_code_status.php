<?php
// check_qr_code_status.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'includes/header.php';

header('Content-Type: application/json');

if ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'teacher') {
    echo json_encode(['exists' => false]);
    exit;
}

$equipmentCode = $_GET['equipment_code'] ?? '';

if (empty($equipmentCode)) {
    echo json_encode(['exists' => false]);
    exit;
}

try {
    $stmt = $db->prepare("SELECT qr_code_path, qr_code_generated_at FROM equipment_classroom WHERE equipment_code = ?");
    $stmt->execute([$equipmentCode]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result && !empty($result['qr_code_path']) && file_exists($result['qr_code_path'])) {
        $lastUpdated = $result['qr_code_generated_at'] ? 
            date('d/m/Y H:i', strtotime($result['qr_code_generated_at'])) : 'ไม่ทราบเวลา';
        
        echo json_encode([
            'exists' => true,
            'last_updated' => $lastUpdated,
            'file_path' => $result['qr_code_path']
        ]);
    } else {
        echo json_encode(['exists' => false]);
    }
    
} catch (PDOException $e) {
    echo json_encode(['exists' => false, 'error' => $e->getMessage()]);
}
?>