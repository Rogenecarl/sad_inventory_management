<?php
require_once '../includes/load.php';
require_login();

$sales_data = $_SESSION['sales_report'] ?? [];
$report_dates = $_SESSION['report_dates'] ?? [];
unset($_SESSION['sales_report'], $_SESSION['report_dates']);

$from_date = $report_dates['from_date'] ?? '';
$to_date = $report_dates['to_date'] ?? '';

$grand_total = 0;
foreach ($sales_data as $row) {
    $grand_total += $row['total'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../lib/addsales/print.css">
    <title>Sales Report</title>
</head>
<body>

    <div class="report-header">
        <h1>Inventory Management System</h1>
        <h2>Sales Report</h2>
    </div>
    <div class="report-dates">
        <strong>From:</strong> <?= htmlspecialchars($from_date); ?> <strong>To:</strong> <?= htmlspecialchars($to_date); ?>
    </div>
    <table>
    <div class="print-btn">
        <button onclick="window.print()">Print Report</button>
    </div>
        <thead>
            <tr>
                <th>Date</th>
                <th>Product Title</th>
                <th>Price</th>
                <th>Total Qty</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($sales_data)): ?>
                <?php foreach ($sales_data as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['date']); ?></td>
                        <td><?= htmlspecialchars($row['product_title']); ?></td>
                        <td><?= number_format($row['total'] / $row['total_qty'], 2); ?></td>
                        <td><?= htmlspecialchars($row['total_qty']); ?></td>
                        <td><?= number_format($row['total'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No sales data found for the selected dates.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <div class="footer">
        <div class="grand-total">Grand Total: ₱<?= number_format($grand_total, 2); ?></div>
    </div>
</body>
</html>
