<?php
require_once 'includes/config.php';

// รับค่าจาก DataTables
$draw = isset($_POST['draw']) ? intval($_POST['draw']) : 1;
$start = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;
$search = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
$orderColumnIndex = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 0;
$orderDir = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'desc';

// กำหนดคอลัมน์ที่ใช้ในการเรียงลำดับ
$columns = [
    'e.code',
    'e.name',
    'c.name',
    'ci.name',
    'd.name',
    'e.status',
    'e.purchase_date'
];

$orderColumn = isset($columns[$orderColumnIndex]) ? $columns[$orderColumnIndex] : 'e.created_at';

// สร้าง WHERE clause สำหรับการค้นหา
$whereClause = '';
$params = [];

if (!empty($search)) {
    $whereClause = "WHERE (e.code LIKE ? OR e.name LIKE ? OR c.name LIKE ? OR ci.name LIKE ? OR d.name LIKE ? OR e.brand LIKE ? OR e.model LIKE ?)";
    $searchParam = "%$search%";
    $params = array_fill(0, 7, $searchParam);
}

// นับจำนวนข้อมูลทั้งหมด
$totalQuery = "SELECT COUNT(*) as total FROM equipment";
$totalStmt = $db->query($totalQuery);
$totalRecords = $totalStmt->fetch(PDO::FETCH_ASSOC)['total'];

// นับจำนวนข้อมูลที่กรองแล้ว
$filteredQuery = "SELECT COUNT(*) as total 
    FROM equipment e 
    LEFT JOIN categories c ON e.category_id = c.id 
    LEFT JOIN categories_items ci ON e.category_item_id = ci.id 
    LEFT JOIN departments d ON e.department_id = d.id 
    $whereClause";

$filteredStmt = $db->prepare($filteredQuery);
$filteredStmt->execute($params);
$totalFiltered = $filteredStmt->fetch(PDO::FETCH_ASSOC)['total'];

// ดึงข้อมูลที่แสดงผล
$dataQuery = "SELECT e.*, 
    c.name as category_name, 
    ci.name as item_name, 
    d.name as department_name,
    CASE WHEN e.image_data IS NOT NULL THEN 1 ELSE 0 END as has_image
    FROM equipment e 
    LEFT JOIN categories c ON e.category_id = c.id 
    LEFT JOIN categories_items ci ON e.category_item_id = ci.id 
    LEFT JOIN departments d ON e.department_id = d.id 
    $whereClause 
    ORDER BY $orderColumn $orderDir 
    LIMIT $start, $length";

$dataStmt = $db->prepare($dataQuery);
$dataStmt->execute($params);
$data = $dataStmt->fetchAll(PDO::FETCH_ASSOC);

// แปลงข้อมูลสำหรับส่งกลับ
$result = [];
foreach ($data as $row) {
    // เพิ่ม flag สำหรับรูปภาพ
    $row['image_data'] = $row['has_image'] == 1 ? true : null;
    unset($row['has_image']);
    $result[] = $row;
}

// ส่งข้อมูลกลับในรูปแบบ JSON
$response = [
    'draw' => $draw,
    'recordsTotal' => intval($totalRecords),
    'recordsFiltered' => intval($totalFiltered),
    'data' => $result
];

header('Content-Type: application/json');
echo json_encode($response);
?>