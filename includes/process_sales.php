<?php
require_once 'load.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $from_date = $_POST['from_date'];
    $to_date = $_POST['to_date'];

    // Validate dates
    if (!$from_date || !$to_date) {
        $_SESSION['error'] = 'Both dates are required.';
        header('Location: ../pages/salesby_dates.php');
        exit;
    }

    try {
        // Ensure that the from_date and to_date include time to ensure correct range matching.
        $from_date = $from_date . ' 00:00:00';  // Start of the day
        $to_date = $to_date . ' 23:59:59';      // End of the day

        // Fetch sales data
        $sql = "
            SELECT 
                s.date, 
                p.name AS product_title, 
                SUM(s.qty) AS total_qty, 
                s.unit_price, 
                SUM(s.total_price) AS total_price
            FROM 
                sales AS s
            JOIN 
                products AS p ON s.product_id = p.prod_id
            WHERE 
                s.date BETWEEN :from_date AND :to_date
                AND s.status = 'completed'
            GROUP BY 
                s.date, p.name, s.unit_price
            ORDER BY 
                s.date ASC
        ";

        // Prepare and execute the SQL query
        $stmt = $conn->prepare($sql);
        $stmt->execute(['from_date' => $from_date, 'to_date' => $to_date]);
        $sales_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Store data in session and redirect to the report page
        $_SESSION['sales_report'] = $sales_data;
        $_SESSION['report_dates'] = ['from_date' => $from_date, 'to_date' => $to_date];
        header('Location: ../includes/sales_report.php');
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = 'Error fetching sales data: ' . $e->getMessage();
        header('Location: ../pages/salesby_dates.php');
        exit;
    }
}
?>
