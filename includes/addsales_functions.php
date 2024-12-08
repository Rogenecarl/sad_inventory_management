<?php
require_once 'load.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the sales data from the request body
    $salesData = json_decode(file_get_contents('php://input'), true);

    if (!empty($salesData['sales'])) {
        try {
            $conn->beginTransaction();

            foreach ($salesData['sales'] as $sale) {
                // Check if the requested quantity is available in stock
                $stmt = $conn->prepare("SELECT quantity FROM products WHERE prod_id = :product_id");
                $stmt->bindParam(':product_id', $sale['id'], PDO::PARAM_INT);
                $stmt->execute();
                $product = $stmt->fetch(PDO::FETCH_ASSOC);

                // If product doesn't exist or stock is insufficient, return an error
                if (!$product || $product['quantity'] < $sale['quantity']) {
                    echo json_encode([
                        'status' => 'error',
                        'message' => "Insufficient stock for product ID {$sale['id']}. Available stock is {$product['quantity']}."
                    ]);

                    $conn->rollBack();
                    exit;
                }

                $stmt = $conn->prepare("INSERT INTO sales (product_id, qty, total_price, date) VALUES (:product_id, :qty, :total_price, CURDATE())");

                $totalPrice = $sale['price'] * $sale['quantity'];

                $stmt->bindParam(':product_id', $sale['id'], PDO::PARAM_INT);
                $stmt->bindParam(':qty', $sale['quantity'], PDO::PARAM_INT);
                $stmt->bindParam(':total_price', $totalPrice, PDO::PARAM_STR);
                $stmt->execute();

                // Update the product's stock after a sale
                $stmt = $conn->prepare("UPDATE products SET quantity = quantity - :quantity WHERE prod_id = :product_id");
                $stmt->bindParam(':quantity', $sale['quantity'], PDO::PARAM_INT);
                $stmt->bindParam(':product_id', $sale['id'], PDO::PARAM_INT);
                $stmt->execute();
            }
            $conn->commit();

            echo json_encode(['status' => 'success', 'message' => 'Sales data added successfully']);
        } catch (Exception $e) {
            $conn->rollBack();

            echo json_encode(['status' => 'error', 'message' => 'Failed to add sales data: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No sales data received']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>