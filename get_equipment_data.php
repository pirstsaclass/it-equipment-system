<?php
require_once 'includes/header.php';

if ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'teacher') {
    echo json_encode(['success' => false, 'message' => 'ไม่มีสิทธิ์เข้าถึง']);
    exit;
}

$id = $_GET['id'] ?? 0;

if ($id > 0) {
    try {
        $stmt = $db->prepare("SELECT ec.*, e.equipment_name, e.brand_name, e.model_name 
                             FROM equipment_classroom ec 
                             LEFT JOIN equipment e ON ec.equipment_code = e.equipment_code 
                             WHERE ec.id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($data) {
            // จัดรูปแบบวันที่
            $data['created_at_formatted'] = date('d/m/Y H:i', strtotime($data['created_at']));
            echo json_encode(['success' => true, 'data' => $data]);
        } else {
            echo json_encode(['success' => false, 'message' => 'ไม่พบข้อมูล']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'ID ไม่ถูกต้อง']);
}
?>