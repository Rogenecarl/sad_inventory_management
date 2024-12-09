<?php
require_once 'load.php';

if (isset($_GET['category_id'])) {
    $category_id = (int)$_GET['category_id'];

    $stmt = $conn->prepare("
        SELECT name, quantity 
        FROM products 
        WHERE categorie_id = :category_id
    ");
    $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
    $stmt->execute();

    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($products);
} else {
    echo json_encode([]);
}
?>
