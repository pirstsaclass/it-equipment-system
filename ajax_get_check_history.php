<?php
// ajax_get_check_history.php
require_once 'includes/config.php';
header('Content-Type: application/json');

try {
    $equipment_code = $_GET['equipment_code'] ?? '';
    
    if (empty($equipment_code)) {
        throw new Exception('กรุณาระบุรหัสอุปกรณ์');
    }
    
    $stmt = $db->prepare("
        SELECT ec.* 
        FROM equipment_checks ec
        JOIN equipment_classroom eqc ON ec.equipment_classroom_id = eqc.id
        WHERE eqc.equipment_code = ?
        ORDER BY ec.check_date DESC, ec.id DESC
        LIMIT 10
    ");
    $stmt->execute([$equipment_code]);
    $history = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'history' => $history
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>