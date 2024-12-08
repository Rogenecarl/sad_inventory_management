<?php
require_once 'load.php';
header('Content-Type: application/json');

$category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;

if ($category_id > 0) {
    try {
        $stmt = $conn->prepare("SELECT prod_id, name, prod_brand, prod_model, quantity, sale_price, photo 
                               FROM products WHERE categorie_id = :category_id");
        $stmt->execute([':category_id' => $category_id]);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($products) > 0) {
            echo json_encode($products);
        } else {
            echo json_encode(['message' => 'No products found for this category.']);
        }
    } catch (Exception $e) {
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Invalid category ID']);
}
?>
