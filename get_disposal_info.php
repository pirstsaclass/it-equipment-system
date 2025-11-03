<?php
require_once 'config/database.php';
session_start();

header('Content-Type: application/json');

if (!isset($_GET['equipment_id'])) {
    echo json_encode(null);
    exit;
}

$equipment_id = $_GET['equipment_id'];

try {
    $query = "SELECT disposal_date, disposal_method, disposal_value, disposal_reason, approved_by, disposal_notes
              FROM equipment_disposals 
              WHERE equipment_id = ? 
              ORDER BY disposal_date DESC 
              LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->execute([$equipment_id]);
    $disposal_info = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode($disposal_info ?: null);
} catch (PDOException $e) {
    echo json_encode(null);
}
?>