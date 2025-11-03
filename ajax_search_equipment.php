<?php
require_once 'includes/header.php';

// ตรวจสอบว่าเป็น AJAX request
if (!isset($_GET['search']) || empty($_GET['search'])) {
    echo json_encode([]);
    exit;
}

$search = $_GET['search'];

try {
    $stmt = $db->prepare("
        SELECT e.equipment_code, e.equipment_name, e.equipment_status, 
               e.location_building, e.location_room
        FROM equipment e
        WHERE e.equipment_code LIKE ? OR e.equipment_name LIKE ?
        ORDER BY e.equipment_code
        LIMIT 10
    ");
    
    $searchParam = "%$search%";
    $stmt->execute([$searchParam, $searchParam]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($results);
} catch (PDOException $e) {
    echo json_encode([]);
}
?>