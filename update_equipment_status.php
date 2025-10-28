<?php
require_once 'includes/header.php';

if ($_POST && isset($_POST['update_equipment_status'])) {
    $equipment_id = $_POST['equipment_id'];
    $new_status = $_POST['status'];
    
    try {
        $stmt = $db->prepare("UPDATE equipment SET status = ? WHERE id = ?");
        $stmt->execute([$new_status, $equipment_id]);
        
        $_SESSION['success'] = "อัพเดทสถานะครุภัณฑ์เรียบร้อยแล้ว";
    } catch (PDOException $e) {
        $_SESSION['error'] = "เกิดข้อผิดพลาดในการอัพเดทสถานะ: " . $e->getMessage();
    }
    
    // Redirect back to maintenance page
    header("Location: maintenance.php");
    exit();
}

// Function to update equipment status from maintenance
function updateEquipmentStatus($db, $equipment_id, $status) {
    try {
        $stmt = $db->prepare("UPDATE equipment SET status = ? WHERE id = ?");
        $stmt->execute([$status, $equipment_id]);
        return true;
    } catch (PDOException $e) {
        error_log("Error updating equipment status: " . $e->getMessage());
        return false;
    }
}
?>