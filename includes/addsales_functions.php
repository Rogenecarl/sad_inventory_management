<?php
require_once 'load.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $salesData = json_decode(file_get_contents('php://input'), true);

    if (!empty($salesData['sales'])) {
        try {
            $conn->beginTransaction();
            foreach ($salesData['sales'] as $sale) {
                $stmt = $conn->prepare("SELECT quantity FROM products WHERE prod_id = :product_id FOR UPDATE");
                $stmt->bindParam(':product_id', $sale['id'], PDO::PARAM_INT);
                $stmt->execute();
                $product = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$product || $product['quantity'] < $sale['quantity']) {
                    throw new Exception("Insufficient stock for product ID {$sale['id']}.");
                }

                $stmt = $conn->prepare("INSERT INTO sales (product_id, qty, total_price, date) VALUES (:product_id, :qty, :total_price, NOW())");
                $totalPrice = $sale['price'] * $sale['quantity'];
                $stmt->bindParam(':product_id', $sale['id'], PDO::PARAM_INT);
                $stmt->bindParam(':qty', $sale['quantity'], PDO::PARAM_INT);
                $stmt->bindParam(':total_price', $totalPrice, PDO::PARAM_STR);
                $stmt->execute();

                $stmt = $conn->prepare("UPDATE products SET quantity = quantity - :quantity WHERE prod_id = :product_id");
                $stmt->bindParam(':quantity', $sale['quantity'], PDO::PARAM_INT);
                $stmt->bindParam(':product_id', $sale['id'], PDO::PARAM_INT);
                $stmt->execute();
            }
            $conn->commit();
            echo json_encode(['status' => 'success']);
        } catch (Exception $e) {
            $conn->rollBack();
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No sales data received']);
    }
}
?>