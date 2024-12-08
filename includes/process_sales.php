<?php
require_once 'load.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $from_date = $_POST['from_date'];
    $to_date = $_POST['to_date'];

    // Fetch sales data
    $sql = "
        SELECT 
            s.date, 
            p.name AS product_title, 
            SUM(s.qty) AS total_qty, 
            SUM(s.total_price) AS total
        FROM 
            sales AS s
        JOIN 
            products AS p ON s.product_id = p.prod_id
        WHERE 
            s.date BETWEEN :from_date AND :to_date
        GROUP BY 
            s.date, p.name
        ORDER BY 
            s.date ASC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute(['from_date' => $from_date, 'to_date' => $to_date]);
    $sales_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Store data in session and redirect to the report page
    $_SESSION['sales_report'] = $sales_data;
    $_SESSION['report_dates'] = ['from_date' => $from_date, 'to_date' => $to_date];
    header('Location: sales_report.php');
    exit;
}
?>
