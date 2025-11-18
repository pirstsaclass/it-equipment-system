<?php
// ajax_get_equipment_list.php
require_once 'config/database.php';

header('Content-Type: application/json');

try {
    $stmt = $db->query("
        SELECT ec.equipment_code, eq.equipment_name, ec.room_number, ec.school_name
        FROM equipment_classroom ec
        LEFT JOIN equipment eq ON ec.equipment_code = eq.equipment_code
        ORDER BY ec.school_name, ec.room_number, ec.equipment_code
    ");
    $equipments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'equipments' => $equipments
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>