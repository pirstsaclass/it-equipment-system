<?php
// ajax_save_equipment_check.php
require_once 'includes/config.php';
header('Content-Type: application/json');

try {
    $equipment_classroom_id = $_POST['equipment_classroom_id'] ?? 0;
    $check_date = $_POST['check_date'] ?? date('Y-m-d H:i:s');
    $checked_by = $_POST['checked_by'] ?? '';
    $status = $_POST['status'] ?? '';
    $notes = $_POST['notes'] ?? '';
    
    if (empty($checked_by) || empty($status)) {
        throw new Exception('กรุณากรอกข้อมูลให้ครบถ้วน');
    }
    
    // อัพโหลดรูปภาพ
    $images = [];
    if (!empty($_FILES['images']['name'][0])) {
        $upload_dir = "uploads/equipment_checks/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
                $file_name = time() . '_' . uniqid() . '_' . $_FILES['images']['name'][$key];
                $file_path = $upload_dir . $file_name;
                
                if (move_uploaded_file($tmp_name, $file_path)) {
                    $images[] = $file_path;
                }
            }
        }
    }
    
    $images_json = !empty($images) ? json_encode($images) : null;
    
    $stmt = $db->prepare("
        INSERT INTO equipment_checks 
        (equipment_classroom_id, check_date, status, notes, checked_by, images, created_at, updated_at) 
        VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())
    ");
    $stmt->execute([$equipment_classroom_id, $check_date, $status, $notes, $checked_by, $images_json]);
    
    echo json_encode([
        'success' => true,
        'message' => 'บันทึกข้อมูลเรียบร้อยแล้ว'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>