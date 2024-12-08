<?php
include('../layouts/header.php');
require_once '../includes/load.php';
require_login();
$currentPage = basename($_SERVER['PHP_SELF'], '.php');

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

// Initialize total sales for the year
$totalSalesStmt = $conn->prepare("
    SELECT SUM(total_price) AS total_sales
    FROM sales
    WHERE YEAR(date) = :selected_year
");
$totalSalesStmt->bindParam(':selected_year', $currentYear, PDO::PARAM_INT);
$totalSalesStmt->execute();
$totalSales = $totalSalesStmt->fetch(PDO::FETCH_ASSOC)['total_sales'] ?? 0; // Default to 0 if no sales

// Fetch all available years from the sales table
$yearsStmt = $conn->prepare("SELECT DISTINCT YEAR(date) AS year FROM sales ORDER BY year DESC");
$yearsStmt->execute();
$years = $yearsStmt->fetchAll(PDO::FETCH_ASSOC);

// Set the default year to the current year or the selected year
$currentYear = isset($_GET['year']) ? (int) $_GET['year'] : date('Y');

// Fetch monthly sales data based on the selected year
$monthlySalesStmt = $conn->prepare("
    SELECT 
        MONTH(date) AS sales_month,
        SUM(qty) AS total_qty,
        SUM(total_price) AS total_sales
    FROM sales
    WHERE YEAR(date) = :selected_year
    GROUP BY sales_month
    ORDER BY sales_month ASC
");
$monthlySalesStmt->bindParam(':selected_year', $currentYear, PDO::PARAM_INT);
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
    $monthlyData['total_quantity'][$monthIndex] = (int) $sale['total_qty'];
    $monthlyData['total_sales'][$monthIndex] = (float) $sale['total_sales'];
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

$selectedYear = isset($_GET['year']) ? (int) $_GET['year'] : date('Y');

// Calculate total sales for the selected year
$totalSalesStmt = $conn->prepare("
    SELECT SUM(total_price) AS total_sales
    FROM sales
    WHERE YEAR(date) = :selected_year
");
$totalSalesStmt->bindParam(':selected_year', $selectedYear, PDO::PARAM_INT);
$totalSalesStmt->execute();
$totalSales = $totalSalesStmt->fetch(PDO::FETCH_ASSOC)['total_sales'] ?? 0;

// Set the default year to the current year or the selected year
$selectedYear = isset($_GET['year']) ? (int) $_GET['year'] : date('Y');

// Calculate total products sold for the selected year
$totalProductsSoldStmt = $conn->prepare("
    SELECT SUM(qty) AS total_products_sold
    FROM sales
    WHERE YEAR(date) = :selected_year
");
$totalProductsSoldStmt->bindParam(':selected_year', $selectedYear, PDO::PARAM_INT);
$totalProductsSoldStmt->execute();
$totalProductsSold = $totalProductsSoldStmt->fetch(PDO::FETCH_ASSOC)['total_products_sold'] ?? 0; // Default to 0 if no data

?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<link rel="stylesheet" href="../lib/devdashboard/dev.css">

<main class="main container" id="main">
    <?php include('../layouts/sidebar.php'); ?>

    <h1 class="dash-fix">Dashboard</h1>
    <div class="main__container">
        <!-- Cards Section -->
        <div class="d-flex flex-row flex-wrap justify-content-between mb-3 gap-3">
            <div class="card text-center card-highlight flex-grow-1" style="width: 12rem;">
                <div class="card-body">
                    <i class="fa fa-users fa-2x mb-3 text-primary"></i>
                    <h5 class="card-title">Total Users</h5>
                    <p class="card-text"><?php echo $totalUsers; ?></p>
                </div>
            </div>
            <div class="card text-center card-highlight flex-grow-1" style="width: 12rem;">
                <div class="card-body">
                    <i class="fa fa-tags fa-2x mb-3 text-warning"></i>
                    <h5 class="card-title">Total Categories</h5>
                    <p class="card-text"><?php echo $totalCategories; ?></p>
                </div>
            </div>
            <div class="card text-center card-highlight flex-grow-1" style="width: 12rem;">
                <div class="card-body">
                    <i class="fa fa-boxes fa-2x mb-3 text-success"></i>
                    <h5 class="card-title">Total Products</h5>
                    <p class="card-text"><?php echo $totalProducts; ?></p>
                </div>
            </div>
            <div class="card text-center card-highlight flex-grow-1" style="width: 12rem;">
                <div class="card-body">
                    <i class="fa fa-shopping-cart fa-2x mb-3 text-danger"></i>
                    <h5 class="card-title">Total Products Sold</h5>
                    <p class="card-text"><?php echo $totalProductsSold; ?></p>
                </div>
            </div>

            <div class="card text-center card-highlight flex-grow-1" style="width: 12rem;">
                <div class="card-body">
                    <i class="fa fa-dollar-sign fa-2x mb-3 text-danger"></i>
                    <h5 class="card-title">Total Sales</h5>
                    <p class="card-text">₱<?php echo number_format($totalSales, 2); ?></p>
                </div>
            </div>
        </div>

        <!-- Sales Statistics section -->
        <div class="d-flex flex-row flex-wrap justify-content-between gap-3 mb-3">
            <div class="card p-2 flex-grow-1" style="width: 24.4rem; align-items:unset">
                <div class="card-body">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <h5 class="card-title">Monthly Sales (<?php echo $currentYear; ?>)</h5>
                        <select id="yearDropdown" class="form-select" style="width: auto;"
                            onchange="changeYear(this.value)">
                            <?php foreach ($years as $year): ?>
                                <option value="<?php echo $year['year']; ?>" <?php echo $year['year'] == $selectedYear ? 'selected' : ''; ?>>
                                    <?php echo $year['year']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <canvas id="monthlySalesChart"></canvas>
                </div>
            </div>

            <div class="card" style="width: 24.4rem; align-items:unset;">
                <div class="card-body">
                    <h5 class="card-title mb-4 text-center">Recently Added Products</h5>
                    <ul class="list-group">
                        <?php foreach ($recentProducts as $product): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center mb-4">
                                <?php echo $product['name']; ?>
                                <span>₱<?php echo number_format($product['sale_price'], 2); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Bar Chart Highest Selling Products -->
        <div class="d-flex flex-row flex-wrap justify-content-between gap-3 mb-3">
            <div class="card p-2 flex-grow-1" style="width: 24.4rem; align-items:unset;">
                <div class="card-body">
                    <h5 class="card-title">Highest Selling Products</h5>
                    <canvas id="highestSellingProductsChart"></canvas>
                </div>
            </div>
            <div class="card" style="width: 24.4rem;">
                <div class="card-body">
                    <h5 class="card-title text-center">Latest Sales</h5>
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
                                    <td>₱<?php echo number_format($sale['total_price'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="d-flex flex-row flex-wrap justify-content-between gap-3">
            <div class="card p-2 flex-grow-1" style="width: 24.4rem; align-items:unset;">
                <div class="card-body">
                    <h5 class="card-title">Low Stocks Products</h5>
                    <canvas id="highestSellingProductsChart"></canvas>
                </div>
            </div>
        </div>

    </div>
        
</main>

<script>
    function changeYear(year) {
        window.location.href = "?year=" + year;
    }

    function changeYear(selectedYear) {
        fetch(`fetch_sales.php?year=${selectedYear}`)
            .then(response => response.json())
            .then(data => {
                document.querySelector('.card-text').textContent = `₱${new Intl.NumberFormat().format(data.totalSales)}`;
            })
            .catch(error => console.error('Error fetching sales data:', error));
    }
    function changeYear(year) {
        window.location.href = "?year=" + year;
    }

    // Monthly Sales Data
    const monthlySalesData = {
        labels: <?php echo json_encode($monthlyData['months']); ?>,
        datasets: [
            {
                label: 'Product Sold',
                data: <?php echo json_encode($monthlyData['total_quantity']); ?>,
                type: 'line',
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 2,
                yAxisID: 'yQuantity',
                tension: 0.4
            },
            {
                label: 'Total Sales (₱)',
                data: <?php echo json_encode($monthlyData['total_sales']); ?>,
                type: 'bar',
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1,
                yAxisID: 'ySales' 
            }
        ]
    };

    // Chart Configuration
    const config = {
        type: 'bar',
        data: monthlySalesData,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                yQuantity: {
                    type: 'linear',
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Quantity Sold'
                    },
                    beginAtZero: true
                },
                ySales: {
                    type: 'linear',
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Total Sales (₱)'
                    },
                    beginAtZero: true,
                    grid: {
                        drawOnChartArea: false // Only draw grid for left axis
                    }
                },
                x: {
                    beginAtZero: true
                }
            }
        }
    };

    const ctx = document.getElementById('monthlySalesChart').getContext('2d');
    new Chart(ctx, config);

</script>




<script src="../lib/devdashboard/dev.js"></script>
</body>

</html>