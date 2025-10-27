<?php
session_start();

// กำหนด path ที่ถูกต้อง
$config_path = __DIR__ . '/../config/database.php';
if (file_exists($config_path)) {
    require_once $config_path;
} else {
    die("ไม่พบไฟล์การตั้งค่าฐานข้อมูล: " . $config_path);
}

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบบริหารจัดการอุปกรณ์ไอที - โรงเรียนวารีนานาชาติ</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Google Fonts - Prompt -->
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
    
    <style>
        :root {
            --primary: #1a73e8;
            --primary-dark: #0d47a1;
            --secondary: #6c757d;
            --success: #28a745;
            --info: #17a2b8;
            --warning: #ffc107;
            --danger: #dc3545;
            --light: #f8f9fa;
            --dark: #343a40;
        }
        
        body {
            font-family: 'Prompt', sans-serif;
            background-color: #f5f7fb;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">