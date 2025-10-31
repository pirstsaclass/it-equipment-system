<?php
// get_subcategories.php
require_once 'config/database.php';

header('Content-Type: application/json');

if (isset($_GET['category_id'])) {
    $category_id = $_GET['category_id'];
    
    try {
        $stmt = $db->prepare("SELECT subcategory_id, subcategory_name FROM equipment_subcategories WHERE category_id = ? ORDER BY subcategory_name");
        $stmt->execute([$category_id]);
        $subcategories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode($subcategories);
    } catch (PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    echo json_encode([]);
}
?>