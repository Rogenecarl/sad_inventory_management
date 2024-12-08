<?php
require_once '../includes/load.php';
require_login();

// Retrieve sales data from the session
$sales_data = $_SESSION['sales_report'] ?? [];
$report_dates = $_SESSION['report_dates'] ?? [];
unset($_SESSION['sales_report'], $_SESSION['report_dates']); // Clear session data

$from_date = $report_dates['from_date'] ?? '';
$to_date = $report_dates['to_date'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report</title>
    <link rel="stylesheet" href="../styles/print.css">
</head>
<body>
    <h1>Inventory Management System - Sales Report</h1>
    <p><strong>From:</strong> <?= htmlspecialchars($from_date); ?> <strong>To:</strong> <?= htmlspecialchars($to_date); ?></p>

    <table border="1" cellspacing="0" cellpadding="5">
        <thead>
            <tr>
                <th>Date</th>
                <th>Product Title</th>
                <th>Total Qty</th>
                <th>TOTAL</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($sales_data)): ?>
                <?php foreach ($sales_data as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['date']); ?></td>
                        <td><?= htmlspecialchars($row['product_title']); ?></td>
                        <td><?= htmlspecialchars($row['total_qty']); ?></td>
                        <td><?= number_format($row['total'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">No sales data found for the selected dates.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <script>
        window.print(); // Automatically opens print dialog
    </script>
</body>
</html>
