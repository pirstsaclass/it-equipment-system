<?php
// ajax_get_equipment_data.php
require_once 'includes/config.php';
header('Content-Type: application/json');

try {
    $equipment_code = $_GET['equipment_code'] ?? '';
    
    if (empty($equipment_code)) {
        throw new Exception('กรุณาระบุรหัสอุปกรณ์');
    }
    
    $stmt = $db->prepare("
        SELECT 
            ec.*,
            eq.equipment_name,
            eq.equipment_type,
            eq.equipment_brand
        FROM equipment_classroom ec
        LEFT JOIN equipment eq ON ec.equipment_code = eq.equipment_code
        WHERE ec.equipment_code = ?
    ");
    $stmt->execute([$equipment_code]);
    $equipment = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$equipment) {
        throw new Exception('ไม่พบข้อมูลอุปกรณ์รหัส: ' . $equipment_code);
    }
    
    echo json_encode([
        'success' => true,
        'equipment' => $equipment
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>