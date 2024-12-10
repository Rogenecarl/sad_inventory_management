<?php
require_once '../includes/load.php';
require_login();

$sales_data = $_SESSION['sales_report'] ?? [];
$report_dates = $_SESSION['report_dates'] ?? [];
unset($_SESSION['sales_report'], $_SESSION['report_dates']);

$from_date = $report_dates['from_date'] ?? '';
$to_date = $report_dates['to_date'] ?? '';

// Initialize grand total as 0
$grand_total = 0;

// Loop through the sales data and sum the total_price for each row
foreach ($sales_data as $row) {
    $grand_total += $row['total_price']; // Use total_price instead of total
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../lib/addsales/print.css">
    <title>Sales Report</title>
    <style>
        .print-btn {
            margin: 20px 0;
            text-align: center;
        }

        .print-btn button {
            padding: 10px 20px;
            font-size: 16px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .print-btn button:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <div class="report-header">
        <h1>Inventory Management System</h1>
        <h2>Sales Report</h2>
    </div>
    <div class="report-dates">
        <strong>From:</strong> <?= htmlspecialchars($from_date); ?>
        <strong>To:</strong> <?= htmlspecialchars($to_date); ?>
    </div>
    <div class="print-btn">
        <button onclick="window.print()">Print Report</button>
    </div>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Total Price</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($sales_data)): ?>
                <?php foreach ($sales_data as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['date']); ?></td>
                        <td><?= htmlspecialchars($row['product_title']); ?></td>
                        <td><?= htmlspecialchars($row['total_qty']); ?></td>
                        <td>₱<?= number_format($row['unit_price'], 2); ?></td>
                        <td>₱<?= number_format($row['total_price'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align: center;">No sales data found for the selected dates.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <div class="footer">
        <div class="grand-total">
            Grand Total: ₱<?= number_format($grand_total, 2); ?>
        </div>
    </div>
</body>

</html>