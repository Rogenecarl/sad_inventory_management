<?php
include('../layouts/header.php');
require_once '../includes/load.php';
require_login();
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="../lib/devdashboard/dev.css">

<main class="main container" id="main">
    <?php include('../layouts/sidebar.php'); ?>

    <h1 class="dash-fix">Dashboard</h1>
    <div class="main__container">

        <!-- Statistics Section -->
        <div class="d-flex flex-row justify-content-between mb-3">
            <div class="card text-center" style="width: 18rem;">
                <div class="card-body">
                    <i class="fa fa-users fa-2x mb-3"></i>
                    <h5 class="card-title">Total Users</h5>
                    <p class="card-text">120</p>
                </div>
            </div>
            <div class="card text-center" style="width: 18rem;">
                <div class="card-body">
                    <i class="fa fa-tags fa-2x mb-3"></i>
                    <h5 class="card-title">Total Categories</h5>
                    <p class="card-text">10</p>
                </div>
            </div>
            <div class="card text-center" style="width: 18rem;">
                <div class="card-body">
                    <i class="fa fa-boxes fa-2x mb-3"></i>
                    <h5 class="card-title">Total Products</h5>
                    <p class="card-text">320</p>
                </div>
            </div>
            <div class="card text-center" style="width: 18rem;">
                <div class="card-body">
                    <i class="fa fa-dollar-sign fa-2x mb-3"></i>
                    <h5 class="card-title">Total Sales</h5>
                    <p class="card-text">$15,000</p>
                </div>
            </div>
        </div>

        <!-- Content Section -->
        <div class="d-flex flex-row justify-content-between">
            <div class="card" style="width: 24.4rem;">
                <div class="card-body">
                    <h5 class="card-title">Highest Selling Products</h5>
                    <canvas id="salesPieChart"></canvas>
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
                            <tr>
                                <td>1</td>
                                <td>Product A</td>
                                <td>2024-11-27</td>
                                <td>$300</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Product B</td>
                                <td>2024-11-26</td>
                                <td>$150</td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>Product C</td>
                                <td>2024-11-25</td>
                                <td>$400</td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td>Product C</td>
                                <td>2024-11-25</td>
                                <td>$400</td>
                            </tr>
                            <tr>
                                <td>5</td>
                                <td>Product C</td>
                                <td>2024-11-25</td>
                                <td>$400</td>
                            </tr>
                            
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card" style="width: 24.4rem;">
                <div class="card-body">
                    <h5 class="card-title">Recently Added Products</h5>
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Product 1 <span>$120</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Product 2 <span>$500</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Product 3 <span>$300</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Pie Chart for Highest Selling Products
    const ctx = document.getElementById('salesPieChart').getContext('2d');
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Product A', 'Product B', 'Product C'],
            datasets: [{
                label: 'Sales',
                data: [300, 150, 400],
                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56'],
                hoverOffset: 4
            }]
        }
    });
</script>

</body>

</html>