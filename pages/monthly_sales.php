<?php
include('../layouts/header.php');
require_once '../includes/load.php';

require_login();
$currentPage = basename($_SERVER['PHP_SELF'], '.php');

// Get the message if available
$message = isset($_GET['message']) ? $_GET['message'] : '';
$message_type = isset($_GET['message_type']) ? $_GET['message_type'] : '';

// Fetch Monthly Sales Data
$sql = "
    SELECT 
        DATE_FORMAT(s.date, '%Y-%m') AS month, 
        p.name AS product_name, 
        SUM(s.qty) AS total_qty, 
        AVG(s.unit_price) AS avg_unit_price, 
        SUM(s.total_price) AS total_sales
    FROM 
        sales AS s
    JOIN 
        products AS p ON s.product_id = p.prod_id
    WHERE 
        s.status = 'completed'
    GROUP BY 
        month, p.name
    ORDER BY 
        month ASC
";
$stmt = $conn->prepare($sql);
$stmt->execute();
$monthly_sales_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Pagination logic (if needed)
$itemsPerPage = isset($_GET['items_per_page']) ? (int) $_GET['items_per_page'] : 50;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $itemsPerPage;

// Adjust the query to fetch paginated data
$sql_paginated = "
    SELECT 
        DATE_FORMAT(s.date, '%Y-%m') AS month, 
        p.name AS product_name, 
        SUM(s.qty) AS total_qty, 
        AVG(s.unit_price) AS avg_unit_price, 
        SUM(s.total_price) AS total_sales
    FROM 
        sales AS s
    JOIN 
        products AS p ON s.product_id = p.prod_id
    WHERE 
        s.status = 'completed'
    GROUP BY 
        month, p.name
    ORDER BY 
        month ASC
    LIMIT :offset, :limit
";
$stmt_paginated = $conn->prepare($sql_paginated);
$stmt_paginated->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt_paginated->bindParam(':limit', $itemsPerPage, PDO::PARAM_INT);
$stmt_paginated->execute();
$monthly_sales_data = $stmt_paginated->fetchAll(PDO::FETCH_ASSOC);

// Get total items for pagination
$stmt_total = $conn->prepare("SELECT COUNT(*) AS total FROM sales WHERE status = 'completed'");
$stmt_total->execute();
$totalItems = $stmt_total->fetch(PDO::FETCH_ASSOC)['total'];

$totalPages = ceil($totalItems / $itemsPerPage);
$currentPageNumber = $page;

?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<link rel="stylesheet" href="../lib/monthly_daily_sales/monthly.css">

<main class="main container" id="main">
    <?php include('../layouts/sidebar.php'); ?>
    <div class="table-wrapper">

        <div class="table-title">
            <div class="d-flex justify-content-between">
                <div class="col-sm-4">
                    <h2>Monthly Sales</h2>
                </div>
            </div>
        </div>

        <!-- Filter and Search -->
        <div class="table-filter">
            <form method="GET" action="">
                <div class="row">
                    <div class="col-sm-3">
                        <div class="show-entries">
                            <span>Show</span>
                            <select class="form-control" name="items_per_page" onchange="this.form.submit()">
                                <?php
                                $entriesOptions = [50, 100, 150, 200];
                                $maxItemsPerPage = min($totalItems, max($entriesOptions));
                                foreach ($entriesOptions as $value): ?>
                                    <option value="<?= $value ?>" <?= $itemsPerPage == $value ? 'selected' : '' ?>>
                                        <?= $value ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <span>Items</span>
                        </div>
                    </div>
                    <div class="col-sm-9">
                        <div class="filter-group d-flex justify-content-end align-items-center">
                            <label>Name</label>
                            <input type="text" class="form-control" name="search"
                                value="" placeholder="Search">
                            <button type="submit" class="btn btn-primary ms-2"><i class="fa fa-search"></i></button>
                        </div>
                        <div class="filter-group">
                            <label>Price</label>
                            <select class="form-control" name="sort_price" onchange="this.form.submit()">
                                <option value="">Select</option>
                                <option value="highest">Highest</option>
                                <option value="lowest">Lowest</option>
                            </select>
                        </div>

                    </div>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="table-responsive mb-4" style="height: 61vh; overflow-y: auto;">
            <table class="table table-striped table-hover" style="text-align: center; vertical-align: middle;">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th>Month</th>
                        <th>Product Name</th>
                        <th>Total Quantity Sold</th>
                        <th>Average Unit Price</th>
                        <th>Total Sales</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($monthly_sales_data)): ?>
                        <?php foreach ($monthly_sales_data as $index => $sale): ?>
                            <tr>
                                <td class="text-center"><?= $offset + $index + 1 ?></td>
                                <td><?= htmlspecialchars($sale['month']) ?></td>
                                <td><?= htmlspecialchars($sale['product_name']) ?></td>
                                <td><?= htmlspecialchars($sale['total_qty']) ?></td>
                                <td>₱<?= number_format($sale['avg_unit_price'], 2) ?></td>
                                <td>₱<?= number_format($sale['total_sales'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">No sales data available for the selected period.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="clearfix">
            <div class="hint-text">Showing <b><?= count($monthly_sales_data) ?></b> out of <b><?= $totalItems ?></b>
                Items</div>
            <ul class="pagination">
                <li class="page-item <?= $currentPageNumber <= 1 ? 'disabled' : '' ?>">
                    <a href="?page=<?= max(1, $currentPageNumber - 1) ?>&items_per_page=<?= $itemsPerPage ?>"
                        class="page-link">Previous</a>
                </li>
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $currentPageNumber == $i ? 'active' : '' ?>">
                        <a href="?page=<?= $i ?>&items_per_page=<?= $itemsPerPage ?>" class="page-link"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= $currentPageNumber >= $totalPages ? 'disabled' : '' ?>">
                    <a href="?page=<?= min($totalPages, $currentPageNumber + 1) ?>&items_per_page=<?= $itemsPerPage ?>"
                        class="page-link">Next</a>
                </li>
            </ul>
        </div>
    </div>
</main>

<!-- toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<?php include '../toast/toastr.php'; ?>
</body>

</html>