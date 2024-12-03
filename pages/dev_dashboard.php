<?php
include('../layouts/header.php');
require_once '../includes/load.php';
require_login();

// Total number of users
$totalUsersStmt = $conn->prepare("SELECT COUNT(*) AS total_users FROM users");
$totalUsersStmt->execute();
$totalUsers = $totalUsersStmt->fetch(PDO::FETCH_ASSOC)['total_users'];

// Total number of categories
$totalCategoriesStmt = $conn->prepare("SELECT COUNT(*) AS total_categories FROM categories");
$totalCategoriesStmt->execute();
$totalCategories = $totalCategoriesStmt->fetch(PDO::FETCH_ASSOC)['total_categories'];

// Total number of products
$totalProductsStmt = $conn->prepare("SELECT COUNT(*) AS total_products FROM products");
$totalProductsStmt->execute();
$totalProducts = $totalProductsStmt->fetch(PDO::FETCH_ASSOC)['total_products'];

// Total sales
$totalSalesStmt = $conn->prepare("SELECT SUM(total_price) AS total_sales FROM sales");
$totalSalesStmt->execute();
$totalSales = $totalSalesStmt->fetch(PDO::FETCH_ASSOC)['total_sales'];

$monthlySalesStmt = $conn->prepare("
    SELECT 
        MONTH(s.date) AS sales_month,
        SUM(s.qty) AS total_qty,
        SUM(s.total_price) AS total_sales
    FROM sales s
    GROUP BY sales_month
    ORDER BY sales_month ASC
");
$monthlySalesStmt->execute();
$monthlySales = $monthlySalesStmt->fetchAll(PDO::FETCH_ASSOC);

$monthlyData = [
    'months' => ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
    'total_quantity' => array_fill(0, 12, 0), // Fill with zeros initially
    'total_sales' => array_fill(0, 12, 0) // Fill with zeros initially
];

// Map the results to the correct month
foreach ($monthlySales as $sale) {
    $monthIndex = $sale['sales_month'] - 1; // Adjust for 0-based index (1 = January, 2 = February, etc.)
    $monthlyData['total_quantity'][$monthIndex] = $sale['total_qty'];
    $monthlyData['total_sales'][$monthIndex] = $sale['total_sales'];
}


// Highest selling products for the chart
$highestSellingStmt = $conn->prepare("
    SELECT p.name, SUM(s.qty) AS total_quantity
    FROM sales s
    JOIN products p ON s.product_id = p.prod_id
    GROUP BY s.product_id
    ORDER BY total_quantity DESC
    LIMIT 5
");
$highestSellingStmt->execute();
$highestSellingProducts = $highestSellingStmt->fetchAll(PDO::FETCH_ASSOC);

// Latest sales
$latestSalesStmt = $conn->prepare("
    SELECT s.sales_id, p.name AS product_name, s.date, s.total_price
    FROM sales s
    JOIN products p ON s.product_id = p.prod_id
    ORDER BY s.date DESC
    LIMIT 5
");
$latestSalesStmt->execute();
$latestSales = $latestSalesStmt->fetchAll(PDO::FETCH_ASSOC);

// Recent products
$recentProductsStmt = $conn->prepare("
    SELECT name, sale_price
    FROM products
    ORDER BY created_at DESC
    LIMIT 5
");
$recentProductsStmt->execute();
$recentProducts = $recentProductsStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<link rel="stylesheet" href="../lib/devdashboard/dev.css">

<main class="main container" id="main">
    <?php include('../layouts/sidebar.php'); ?>

    <h1 class="dash-fix">Dashboard</h1>
    <div class="main__container">
        <!-- Cards Section -->
        <div class="d-flex flex-row flex-wrap justify-content-between mb-3">
            <div class="card text-center card-highlight" style="width: 18rem;">
                <div class="card-body">
                    <i class="fa fa-users fa-2x mb-3 text-primary"></i>
                    <h5 class="card-title">Total Users</h5>
                    <p class="card-text"><?php echo $totalUsers; ?></p>
                </div>
            </div>
            <div class="card text-center card-highlight" style="width: 18rem;">
                <div class="card-body">
                    <i class="fa fa-tags fa-2x mb-3 text-warning"></i>
                    <h5 class="card-title">Total Categories</h5>
                    <p class="card-text"><?php echo $totalCategories; ?></p>
                </div>
            </div>
            <div class="card text-center card-highlight" style="width: 18rem;">
                <div class="card-body">
                    <i class="fa fa-boxes fa-2x mb-3 text-success"></i>
                    <h5 class="card-title">Total Products</h5>
                    <p class="card-text"><?php echo $totalProducts; ?></p>
                </div>
            </div>
            <div class="card text-center card-highlight" style="width: 18rem;">
                <div class="card-body">
                    <i class="fa fa-dollar-sign fa-2x mb-3 text-danger"></i>
                    <h5 class="card-title">Total Sales</h5>
                    <p class="card-text">₱<?php echo number_format($totalSales, 2); ?></p>
                </div>
            </div>
        </div>

        <!-- Sales Statistics section -->
        <div class="d-flex flex-row flex-wrap justify-content-between gap-4 mb-3">
            <div class="card p-2 flex-grow-1" style="width: 24.4rem; align-items:unset">
                <div class="card-body">
                    <h5 class="card-title">Monthly Sales</h5>
                    <canvas id="monthlySalesChart"></canvas>
                </div>
            </div>
            <div class="card" style="width: 24.4rem; align-items:unset;">
                <div class="card-body">
                    <h5 class="card-title">Recently Added Products</h5>
                    <ul class="list-group">
                        <?php foreach ($recentProducts as $product): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center mb-4">
                                <?php echo $product['name']; ?>
                                <span>$<?php echo number_format($product['sale_price'], 2); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Bar Chart Highest Selling Products -->
        <div class="d-flex flex-row flex-wrap justify-content-between gap-4 mb-3">
            <div class="card p-2 flex-grow-1" style="width: 24.4rem; align-items:unset;">
                <div class="card-body">
                    <h5 class="card-title">Highest Selling Products</h5>
                    <canvas id="highestSellingProductsChart"></canvas>
                </div>
            </div>
            <div class="card" style="width: 24.4rem;">
                <div class="card-body">
                    <h5 class="card-title">Latest Sales</h5>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Product Name</th>
                                <th>Date</th>
                                <th>Total Sales</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($latestSales as $sale): ?>
                                <tr>
                                    <td><?php echo $sale['sales_id']; ?></td>
                                    <td><?php echo $sale['product_name']; ?></td>
                                    <td><?php echo $sale['date']; ?></td>
                                    <td>$<?php echo number_format($sale['total_price'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    const monthlySalesData = {
        labels: <?php echo json_encode($monthlyData['months']); ?>,
        datasets: [
            {
                label: 'Product Sold',
                data: <?php echo json_encode($monthlyData['total_quantity']); ?>,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)', // January
                    'rgba(54, 162, 235, 0.2)', // February
                    'rgba(255, 206, 86, 0.2)', // March
                    'rgba(75, 192, 192, 0.2)', // April
                    'rgba(153, 102, 255, 0.2)', // May
                    'rgba(255, 159, 64, 0.2)', // June
                    'rgba(231, 76, 60, 0.2)', // July
                    'rgba(46, 204, 113, 0.2)', // August
                    'rgba(52, 152, 219, 0.2)', // September
                    'rgba(155, 89, 182, 0.2)', // October
                    'rgba(241, 196, 15, 0.2)', // November
                    'rgba(52, 73, 94, 0.2)'  // December
                ],
                borderColor: [
                    'rgba(255, 99, 132, 0.5)', // January
                    'rgba(54, 162, 235, 1)', // February
                    'rgba(255, 206, 86, 1)', // March
                    'rgba(75, 192, 192, 1)', // April
                    'rgba(153, 102, 255, 1)', // May
                    'rgba(255, 159, 64, 1)', // June
                    'rgba(231, 76, 60, 1)', // July
                    'rgba(46, 204, 113, 1)', // August
                    'rgba(52, 152, 219, 1)', // September
                    'rgba(155, 89, 182, 1)', // October
                    'rgba(241, 196, 15, 1)', // November
                    'rgba(52, 73, 94, 1)'  // December
                ],
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                yAxisID: 'y1'
            },
            {
                label: 'Total Sales ₱',
                data: <?php echo json_encode($monthlyData['total_sales']); ?>,
                backgroundColor: [
                    'rgba(255, 99, 132)', // January
                    'rgba(54, 162, 235)', // February
                    'rgba(255, 206, 86)', // March
                    'rgba(75, 192, 192)', // April
                    'rgba(153, 102, 255)', // May
                    'rgba(255, 159, 64)', // June
                    'rgba(231, 76, 60)', // July
                    'rgba(46, 204, 113)', // August
                    'rgba(52, 152, 219)', // September
                    'rgba(155, 89, 182)', // October
                    'rgba(241, 196, 15)', // November
                    'rgba(52, 73, 94)'  // December
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)', // January
                    'rgba(54, 162, 235, 1)', // February
                    'rgba(255, 206, 86, 1)', // March
                    'rgba(75, 192, 192, 1)', // April
                    'rgba(153, 102, 255, 1)', // May
                    'rgba(255, 159, 64, 1)', // June
                    'rgba(231, 76, 60, 1)', // July
                    'rgba(46, 204, 113, 1)', // August
                    'rgba(52, 152, 219, 1)', // September
                    'rgba(155, 89, 182, 1)', // October
                    'rgba(241, 196, 15, 1)', // November
                    'rgba(52, 73, 94, 1)'  // December
                ],
                borderWidth: 1,
                yAxisID: 'y2'
            }
        ]
    };

    const monthlySalesCtx = document.getElementById('monthlySalesChart').getContext('2d');
    new Chart(monthlySalesCtx, {
        type: 'bar',
        data: monthlySalesData,
        options: {
            responsive: true,
            scales: {
                y1: {
                    beginAtZero: true,
                    position: 'left'
                },
                y2: {
                    beginAtZero: true,
                    position: 'right',
                    grid: {
                        drawOnChartArea: false
                    }
                }
            }
        }
    });
</script>




<script src="../lib/devdashboard/dev.js"></script>
</body>

</html>