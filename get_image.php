<?php
require_once 'includes/config.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    $stmt = $db->prepare("SELECT image_data, image_type FROM equipment WHERE id = ?");
    $stmt->execute([$id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result && $result['image_data']) {
        // กำหนด Content-Type header
        header('Content-Type: ' . $result['image_type']);
        
        // ส่งออกข้อมูลรูปภาพ
        echo $result['image_data'];
    } else {
        // ถ้าไม่มีรูปภาพ ให้แสดงรูป placeholder
        header('Content-Type: image/png');
        
        // สร้างรูป placeholder แบบง่าย
        $img = imagecreate(300, 300);
        $bg = imagecolorallocate($img, 240, 240, 240);
        $text_color = imagecolorallocate($img, 150, 150, 150);
        
        imagestring($img, 5, 110, 140, 'No Image', $text_color);
        
        imagepng($img);
        imagedestroy($img);
    }
} else {
    http_response_code(404);
    echo 'Image not found';
}
?>