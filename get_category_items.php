<?php
require_once 'config/database.php';

header('Content-Type: application/json');

if (isset($_GET['category_id'])) {
    $database = new Database();
    $db = $database->getConnection();
    
    $category_id = $_GET['category_id'];
    
    // Get category code first
    $stmt = $db->prepare("SELECT code FROM categories WHERE id = ?");
    $stmt->execute([$category_id]);
    $category = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($category) {
        // Get items by category code
        $stmt = $db->prepare("SELECT id, name FROM categories_items WHERE category_code = ? ORDER BY sort_order, name");
        $stmt->execute([$category['code']]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode($items);
    } else {
        echo json_encode([]);
    }
} else {
    echo json_encode([]);
}
?>