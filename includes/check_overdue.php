<?php
require_once 'config/database.php';

header('Content-Type: application/json');

try {
    // อัพเดทสถานะเป็นเกินกำหนดสำหรับรายการที่เกินกำหนดคืน
    $stmt = $db->prepare("
        UPDATE equipment_borrow 
        SET borrow_status = 'เกินกำหนด' 
        WHERE borrow_status = 'ยืมอยู่' 
        AND expected_return_date < CURDATE()
    ");
    $stmt->execute();
    
    $updated_count = $stmt->rowCount();
    
    echo json_encode(['success' => true, 'updated' => $updated_count]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>