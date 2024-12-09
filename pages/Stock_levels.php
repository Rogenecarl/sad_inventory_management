<?php
include('../layouts/header.php');
require_once '../includes/load.php';

require_login();
$currentPage = basename($_SERVER['PHP_SELF'], '.php');

// Fetch all categories
$stmt = $conn->prepare("SELECT category_id, name, created_at FROM categories");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<link rel="stylesheet" href="../lib/StockLev/Stocks.css">

<main class="main container" id="main">
    <?php include('../layouts/sidebar.php'); ?>
    <h1>Stock Levels</h1>
    <div class="main__container">
        <div class="d-flex flex-column">
            <div class="horizontal-scrollbar-wrapper">
                <div class="scroll-arrow start-arrow">
                    <i class="fas fa-chevron-left"></i>
                </div>
                <div class="horizontal-scrollbar d-flex flex-row flex-nowrap">
                    <?php if (empty($categories)): ?>
                        <p>No categories found.</p>
                    <?php else: ?>
                        <?php foreach ($categories as $category): ?>
                            <div class="card card-body m-2 card-highlight d-flex justify-content-center" style="width: 18rem;"
                                onclick="loadCategoryProducts(<?= $category['category_id'] ?>)">
                                <h5 class="card-title"><?= htmlspecialchars($category['name']) ?></h5>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <div class="scroll-arrow end-arrow">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </div>
            <div class="card p-2 flex-grow-1 mt-2" style="align-items:unset">
                <div class="card-body">
                    <canvas id="StocksPerCategory"></canvas>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    let stocksChart;

    // Function to fetch stock data for a category
    function loadCategoryProducts(categoryId) {
        fetch(`../includes/get_category_products.php?category_id=${categoryId}`)
            .then(response => response.json())
            .then(data => {
                const productNames = data.map(product => product.name);
                const stockLevels = data.map(product => parseInt(product.quantity || 0));

                updateBarGraph(productNames, stockLevels);
            })
            .catch(error => console.error('Error fetching category products:', error));
    }

    // Function to render or update the bar graph
    function updateBarGraph(labels, data) {
        const ctx = document.getElementById('StocksPerCategory').getContext('2d');

        if (stocksChart) {
            stocksChart.destroy();
        }

        stocksChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Stock Levels',
                    data: data,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
</script>
