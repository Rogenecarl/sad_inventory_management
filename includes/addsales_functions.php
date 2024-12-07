<?php
require_once 'load.php';

// Check if request method is POST and data is present
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the sales data from the request body
    $salesData = json_decode(file_get_contents('php://input'), true);

    // Check if sales data is not empty
    if (!empty($salesData['sales'])) {
        try {
            // Begin transaction
            $conn->beginTransaction();

            // Loop through the sales data and insert into the sales table
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
                    // Rollback the transaction and exit
                    $conn->rollBack();
                    exit;
                }

                // Prepare the insert statement for sales
                $stmt = $conn->prepare("INSERT INTO sales (product_id, qty, total_price, date) VALUES (:product_id, :qty, :total_price, CURDATE())");

                // Calculate total price
                $totalPrice = $sale['price'] * $sale['quantity'];

                // Bind parameters and execute the statement
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

            // Commit the transaction
            $conn->commit();

            // Return a success response
            echo json_encode(['status' => 'success', 'message' => 'Sales data added successfully']);
        } catch (Exception $e) {
            // Rollback transaction in case of error
            $conn->rollBack();

            // Return error response
            echo json_encode(['status' => 'error', 'message' => 'Failed to add sales data: ' . $e->getMessage()]);
        }
    } else {
        // Return error response if sales data is empty
        echo json_encode(['status' => 'error', 'message' => 'No sales data received']);
    }
} else {
    // Return error response if the request method is not POST
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>