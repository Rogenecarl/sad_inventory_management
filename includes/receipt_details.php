<?php
// Include necessary files and initialize the database connection
require_once '../includes/load.php';
require_login();

// Initialize the database connection
global $conn;

// Get the transaction ID from the GET request
$transaction_id = $_GET['transaction_id'] ?? '';

// Check if the transaction ID is provided
if (empty($transaction_id)) {
    echo json_encode(['error' => 'Transaction ID is required']);
    exit;
}

// Fetch transaction details based on the transaction ID
$stmt = $conn->prepare("
    SELECT s.transaction_id, s.seller_id, s.payment_method, s.date, u.username AS seller_name, 
           (SELECT SUM(sp.qty * sp.unit_price) FROM sales_products sp WHERE sp.transaction_id = s.transaction_id) AS total_price
    FROM sales s
    LEFT JOIN users u ON s.seller_id = u.User_id
    WHERE s.transaction_id = :transaction_id
");
$stmt->bindParam(':transaction_id', $transaction_id, PDO::PARAM_STR);
$stmt->execute();
$transaction = $stmt->fetch(PDO::FETCH_ASSOC);

// If transaction doesn't exist, return an error
if (!$transaction) {
    echo json_encode(['error' => 'Transaction not found']);
    exit;
}

// Fetch product details for the transaction
$productStmt = $conn->prepare("
    SELECT sp.product_name, sp.qty, sp.unit_price, (sp.qty * sp.unit_price) AS total_price
    FROM sales_products sp
    WHERE sp.transaction_id = :transaction_id
");
$productStmt->bindParam(':transaction_id', $transaction_id, PDO::PARAM_STR);
$productStmt->execute();
$products = $productStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch user money and change (assuming this information is stored in the sales table)
$userMoneyStmt = $conn->prepare("
    SELECT user_money, change FROM sales WHERE transaction_id = :transaction_id
");
$userMoneyStmt->bindParam(':transaction_id', $transaction_id, PDO::PARAM_STR);
$userMoneyStmt->execute();
$userMoneyData = $userMoneyStmt->fetch(PDO::FETCH_ASSOC);

// Prepare the receipt details to be returned as JSON
$receiptDetails = [
    'transaction_id' => $transaction['transaction_id'],
    'seller_name' => $transaction['seller_name'],
    'date' => date('F j, Y, g:i:s A', strtotime($transaction['date'])),
    'payment_method' => $transaction['payment_method'],
    'total_price' => number_format($transaction['total_price'], 2),
    'products' => $products,
    'user_money' => number_format($userMoneyData['user_money'], 2),
    'change' => number_format($userMoneyData['change'], 2)
];

// Return the receipt details as JSON
echo json_encode($receiptDetails);
?>