<?php
require_once '../includes/load.php';
require_login();

// Retrieve sales data from the session
$sales_data = $_SESSION['sales_report'] ?? [];
$report_dates = $_SESSION['report_dates'] ?? [];
unset($_SESSION['sales_report'], $_SESSION['report_dates']); // Clear session data

$from_date = $report_dates['from_date'] ?? '';
$to_date = $report_dates['to_date'] ?? '';

// Calculate Grand Total
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
    <title>Sales Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }
        h1, h2, h3 {
            text-align: center;
            margin: 0;
        }
        .report-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .report-dates {
            text-align: center;
            font-size: 1.1em;
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 0.9em;
        }
        th, td {
            text-align: left;
            padding: 10px;
            border: 1px solid #ccc;
        }
        th {
            background-color: #f4f4f4;
        }
        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 1.1em;
            font-weight: bold;
        }
        .grand-total {
            text-align: right;
            font-size: 1.2em;
            font-weight: bold;
            margin-top: 10px;
        }
        @media print {
            body {
                margin: 0;
            }
            .print-btn {
                display: none;
            }
        }
        .print-btn {
            display: flex;
            justify-content: end;
            margin-bottom: 20px;
            text-align: center;
        }
        .print-btn button {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 1em;
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
