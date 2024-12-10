<?php
require_once 'load.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    $transaction_id = $input['transaction_id'];
    $sales = $input['sales'];
    $payment_method = $input['payment_method'];
    $total_price = $input['total_price'];
    $user_money = $input['user_money'];
    $change = $input['change'];
    $seller_id = $_SESSION['user_id']; // Assuming seller is logged in

    try {
        $conn->beginTransaction();

        foreach ($sales as $sale) {
            $stmt = $conn->prepare("
                INSERT INTO sales (transaction_id, product_id, qty, unit_price, total_price, payment_method, seller_id, date) 
                VALUES (:transaction_id, :product_id, :qty, :unit_price, :total_price, :payment_method, :seller_id, NOW())
            ");
            $stmt->execute([
                ':transaction_id' => $transaction_id,
                ':product_id' => $sale['id'],
                ':qty' => $sale['quantity'],
                ':unit_price' => $sale['price'],
                ':total_price' => $sale['quantity'] * $sale['price'],
                ':payment_method' => $payment_method,
                ':seller_id' => $seller_id
            ]);

            // Deduct from inventory
            $updateStock = $conn->prepare("UPDATE products SET quantity = quantity - :qty WHERE prod_id = :product_id");
            $updateStock->execute([
                ':qty' => $sale['quantity'],
                ':product_id' => $sale['id']
            ]);
        }

        $conn->commit();
        echo json_encode(['status' => 'success']);
    } catch (Exception $e) {
        $conn->rollBack();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
?>
