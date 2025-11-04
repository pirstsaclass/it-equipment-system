<?php
// ตรวจสอบสิทธิ์การเข้าถึงหน้าเว็บ
function checkPermission($required_roles = []) {
    // ถ้ายังไม่ได้ล็อกอิน ให้ redirect ไปหน้า login
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['error'] = "กรุณาเข้าสู่ระบบก่อน";
        header("Location: login.php");
        exit();
    }
    
    // ถ้าไม่ระบุ required_roles ให้อนุญาตทั้งหมด
    if (empty($required_roles)) {
        return true;
    }
    
    // ตรวจสอบบทบาทผู้ใช้
    $user_role = $_SESSION['role'] ?? '';
    
    // ถ้าผู้ใช้ไม่มีบทบาทที่ต้องการ
    if (!in_array($user_role, $required_roles)) {
        $_SESSION['error'] = "คุณไม่มีสิทธิ์เข้าถึงหน้านี้";
        header("Location: index.php");
        exit();
    }
    
    return true;
}

// ฟังก์ชันตรวจสอบสิทธิ์แบบกำหนดเอง
function hasPermission($required_roles = []) {
    if (!isset($_SESSION['user_id'])) {
        return false;
    }
    
    if (empty($required_roles)) {
        return true;
    }
    
    $user_role = $_SESSION['role'] ?? '';
    return in_array($user_role, $required_roles);
}
?>