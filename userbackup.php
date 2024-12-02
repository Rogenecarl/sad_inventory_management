<?php
include('../layouts/header.php');
require_once '../includes/load.php';
require_login();

// Fetch total number of users
$totalUsersStmt = $conn->prepare("SELECT COUNT(*) AS total_users FROM users");
$totalUsersStmt->execute();
$totalUsers = $totalUsersStmt->fetch(PDO::FETCH_ASSOC)['total_users'];

// Fetch total number of categories
$totalCategoriesStmt = $conn->prepare("SELECT COUNT(*) AS total_categories FROM categories");
$totalCategoriesStmt->execute();
$totalCategories = $totalCategoriesStmt->fetch(PDO::FETCH_ASSOC)['total_categories'];

// Fetch total number of products
$totalProductsStmt = $conn->prepare("SELECT COUNT(*) AS total_products FROM products");
$totalProductsStmt->execute();
$totalProducts = $totalProductsStmt->fetch(PDO::FETCH_ASSOC)['total_products'];

// Fetch total sales
$totalSalesStmt = $conn->prepare("SELECT SUM(total_price) AS total_sales FROM sales");
$totalSalesStmt->execute();
$totalSales = $totalSalesStmt->fetch(PDO::FETCH_ASSOC)['total_sales'];

// Fetch highest selling products for the chart
$highestSellingStmt = $conn->prepare("
    SELECT p.name, SUM(s.qty) AS total_quantity
    FROM sales s
    JOIN products p ON s.product_id = p.prod_id
    GROUP BY s.product_id
    ORDER BY total_quantity DESC
");
$highestSellingStmt->execute();
$highestSellingProducts = $highestSellingStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch latest sales for the table
$latestSalesStmt = $conn->prepare("
    SELECT s.sales_id, p.name AS product_name, s.date, s.total_price
    FROM sales s
    JOIN products p ON s.product_id = p.prod_id
    ORDER BY s.date DESC
    LIMIT 5
");
$latestSalesStmt->execute();
$latestSales = $latestSalesStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch recently added products
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
                    <p class="card-text">$<?php echo number_format($totalSales, 2); ?></p>
                </div>
            </div>
        </div>

        <!-- Bar Chart and Table Section -->
        <div class="d-flex flex-row flex-wrap justify-content-between">
            <div class="card p-2 flex-fill" style="width: 24.4rem;">
                <div class="card-body">
                    <h5 class="card-title">Highest Selling Products</h5>
                    <canvas id="salesBarChart"></canvas>
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
    const ctx = document.getElementById('salesBarChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode(array_column($highestSellingProducts, 'name')); ?>,
            datasets: [{
                label: 'Quantity Sold',
                data: <?php echo json_encode(array_column($highestSellingProducts, 'total_quantity')); ?>,
                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'],
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: { title: { display: true, text: 'Products' } },
                y: { title: { display: true, text: 'Quantity Sold' } }
            }
        }
    });
</script>

<script src="../lib/devdashboard/dev.js"></script>
</body>

</html>