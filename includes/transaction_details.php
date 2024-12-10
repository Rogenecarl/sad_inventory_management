<?php
require_once '../includes/load.php';
require_login();

// Initialize database connection
global $conn;

// Get the transaction ID from the query parameter
$transactionId = $_GET['transaction_id'] ?? '';

if ($transactionId) {
    // Fetch product details under the given transaction ID
    $stmt = $conn->prepare("
        SELECT 
            p.name AS product_name,
            s.qty,
            s.unit_price,
            s.total_price,
            s.date
        FROM sales s
        INNER JOIN products p ON s.product_id = p.prod_id
        WHERE s.transaction_id = :transaction_id
    ");
    $stmt->bindParam(':transaction_id', $transactionId, PDO::PARAM_STR);
    $stmt->execute();
    $details = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return the data as JSON
    echo json_encode($details);
} else {
    // Return an empty array if no transaction ID is provided
    echo json_encode([]);
}
?>
