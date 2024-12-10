<?php
include('../layouts/header.php');
require_once '../includes/load.php';
require_login();

$currentPage = basename($_SERVER['PHP_SELF'], '.php');

// Initialize database connection
global $conn;

// Ensure required variables are initialized
$searchKeyword = $_GET['search'] ?? '';
$itemsPerPage = $_GET['items_per_page'] ?? 50;
$currentPageNumber = $_GET['page'] ?? 1;
$offset = ($currentPageNumber - 1) * $itemsPerPage;

// Fetch sales data grouped by transaction_id
$stmt = $conn->prepare("
    SELECT transaction_id, seller_id, payment_method, date
    FROM sales
    WHERE transaction_id LIKE :search
    GROUP BY transaction_id
    ORDER BY date DESC
    LIMIT :limit OFFSET :offset
");
$searchParam = "%$searchKeyword%";
$stmt->bindParam(':search', $searchParam, PDO::PARAM_STR);
$stmt->bindParam(':limit', $itemsPerPage, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$sales = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch total count for pagination
$countStmt = $conn->prepare("
    SELECT COUNT(DISTINCT transaction_id) AS total
    FROM sales
    WHERE transaction_id LIKE :search
");
$countStmt->bindParam(':search', $searchParam, PDO::PARAM_STR);
$countStmt->execute();
$totalItems = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
$totalPages = ceil($totalItems / $itemsPerPage);

?>

<link rel="stylesheet" href="../lib/managesales/managesale.css">

<main class="main container" id="main">
    <?php include('../layouts/sidebar.php'); ?>

    <div class="table-wrapper">
        <div class="table-title">
            <h2>Sales List</h2>
        </div>
        <div class="table-filter">
            <form method="GET" action="">
                <div class="row">
                    <div class="col-sm-3">
                        <div class="show-entries">
                            <span>Show</span>
                            <select class="form-control" name="items_per_page" onchange="this.form.submit()">
                                <option value="50" <?= $itemsPerPage == 50 ? 'selected' : '' ?>>50</option>
                                <option value="100" <?= $itemsPerPage == 100 ? 'selected' : '' ?>>100</option>
                                <option value="150" <?= $itemsPerPage == 150 ? 'selected' : '' ?>>150</option>
                                <option value="200" <?= $itemsPerPage == 200 ? 'selected' : '' ?>>200</option>
                            </select>
                            <span>Sales</span>
                        </div>
                    </div>
                    <div class="col-sm-9">
                        <div class="filter-group d-flex justify-content-end align-items-center">
                            <label>Search</label>
                            <input type="text" class="form-control" name="search"
                                value="<?= htmlspecialchars($searchKeyword) ?>" placeholder="Search by Transaction ID">
                            <button type="submit" class="btn btn-primary ms-2"><i class="fa fa-search"></i></button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="table-responsive mb-4" style="height: 61vh; overflow-y: auto;">
            <table class="table table-striped table-hover" style="text-align: center; vertical-align: middle;">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th>Transaction ID</th>
                        <th>Seller</th>
                        <th>Payment Method</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $counter = $offset + 1;
                    foreach ($sales as $sale):
                        // Fetch seller name
                        $sellerStmt = $conn->prepare("
                            SELECT username FROM users WHERE User_id = :seller_id
                        ");
                        $sellerStmt->bindParam(':seller_id', $sale['seller_id'], PDO::PARAM_INT);
                        $sellerStmt->execute();
                        $seller = $sellerStmt->fetch(PDO::FETCH_ASSOC);
                        ?>
                        <tr>
                            <td class="text-center"><?= $counter++ ?></td>
                            <td class="text-center"><?= htmlspecialchars($sale['transaction_id']) ?></td>
                            <td class="text-center"><?= htmlspecialchars($seller['username'] ?? 'Unknown') ?></td>
                            <td class="text-center"><?= htmlspecialchars($sale['payment_method']) ?></td>
                            <td class="text-center"><?= date('F j, Y, g:i:s A', strtotime($sale['date'])) ?></td>
                            <td>
                                <button class="btn btn-primary btn-sm view-modal-btn"
                                    data-transaction-id="<?= $sale['transaction_id'] ?>" data-bs-toggle="modal"
                                    data-bs-target="#viewModal"><i class="ri-eye-line"></i>
                                </button>

                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="clearfix">
            <div class="hint-text">Showing <b><?= count($sales) ?></b> out of <b><?= $totalItems ?></b> Sales</div>
            <ul class="pagination">
                <li class="page-item <?= $currentPageNumber == 1 ? 'disabled' : '' ?>">
                    <a href="?page=<?= $currentPageNumber - 1 ?>&items_per_page=<?= $itemsPerPage ?>&search=<?= htmlspecialchars($searchKeyword) ?>"
                        class="page-link">Previous</a>
                </li>
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $currentPageNumber == $i ? 'active' : '' ?>">
                        <a href="?page=<?= $i ?>&items_per_page=<?= $itemsPerPage ?>&search=<?= htmlspecialchars($searchKeyword) ?>"
                            class="page-link"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= $currentPageNumber == $totalPages ? 'disabled' : '' ?>">
                    <a href="?page=<?= $currentPageNumber + 1 ?>&items_per_page=<?= $itemsPerPage ?>&search=<?= htmlspecialchars($searchKeyword) ?>"
                        class="page-link">Next</a>
                </li>
            </ul>
        </div>
    </div>
</main>

<div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel">Transaction Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Total Price</th>
                            <th>Date Purchased</th>
                        </tr>
                    </thead>
                    <tbody id="modal-content">
                        <!-- Data will be populated via JavaScript -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<!-- Add this to the PHP section -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll('.view-modal-btn').forEach(button => {
            button.addEventListener('click', function () {
                const transactionId = this.getAttribute('data-transaction-id');
                const modalContent = document.querySelector('#modal-content');
                modalContent.innerHTML = '<tr><td colspan="5">Loading...</td></tr>';

                // Fetch transaction details using AJAX
                fetch(`../includes/transaction_details.php?transaction_id=${transactionId}`)
                    .then(response => response.json())
                    .then(data => {
                        modalContent.innerHTML = ''; // Clear loading message

                        if (data.length > 0) {
                            data.forEach(item => {
                                modalContent.innerHTML += `
                                <tr>
                                    <td>${item.product_name}</td>
                                    <td>${item.qty}</td>
                                    <td>${item.unit_price}</td>
                                    <td>${item.total_price}</td>
                                    <td>${item.date}</td>
                                </tr>
                            `;
                            });
                        } else {
                            modalContent.innerHTML = '<tr><td colspan="5">No data available for this transaction.</td></tr>';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        modalContent.innerHTML = '<tr><td colspan="5">Failed to load transaction details.</td></tr>';
                    });
            });
        });
    });

</script>


<script src="../lib/category/category.js"></script>
</body>

</html>