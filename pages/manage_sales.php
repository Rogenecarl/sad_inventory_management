<?php
include('../layouts/header.php');
require_once '../includes/load.php';
require_login();

$currentPage = basename($_SERVER['PHP_SELF'], '.php');

// Default values for pagination and search
$itemsPerPage = isset($_GET['items_per_page']) ? (int)$_GET['items_per_page'] : 5;
$searchKeyword = isset($_GET['search']) ? trim($_GET['search']) : '';
$currentPageNumber = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Calculate offset for pagination
$offset = ($currentPageNumber - 1) * $itemsPerPage;

// Fetch total items for pagination
$totalItemsQuery = "SELECT COUNT(DISTINCT s.product_id) as total 
                    FROM sales s 
                    JOIN products p ON s.product_id = p.prod_id 
                    WHERE p.name LIKE :search";
$stmt = $conn->prepare($totalItemsQuery);
$stmt->execute([':search' => '%' . $searchKeyword . '%']);
$totalItems = (int)$stmt->fetchColumn();
$totalPages = ceil($totalItems / $itemsPerPage);

// Fetch paginated results
$sql = "
    SELECT p.name, SUM(s.qty) as total_qty, SUM(s.total_price) as total_price
    FROM sales s
    JOIN products p ON s.product_id = p.prod_id
    WHERE p.name LIKE :search
    GROUP BY s.product_id, p.name
    ORDER BY p.name ASC
    LIMIT :offset, :limit
";
$stmt = $conn->prepare($sql);
$stmt->bindValue(':search', '%' . $searchKeyword . '%', PDO::PARAM_STR);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':limit', $itemsPerPage, PDO::PARAM_INT);
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<link rel="stylesheet" href="../lib/managesales/managesale.css">

<main class="main container" id="main">
    <?php include('../layouts/sidebar.php'); ?>

    <div class="table-wrapper">
        <div class="table-title">
            <div class="d-flex justify-content-between">
                <div class="col-sm-4">
                    <h2>Categories</h2>
                </div>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                    data-bs-target="#createProductModal">
                    <i class="fa fa-plus-circle"></i>
                    Add Sales
                </button>
            </div>
        </div>
        <div class="table-filter">
            <form method="GET" action="">
                <div class="row">
                    <div class="col-sm-3">
                        <div class="show-entries">
                            <span>Show</span>
                            <select class="form-control" name="items_per_page" onchange="this.form.submit()">
                                <option value="5" <?= $itemsPerPage == 5 ? 'selected' : '' ?>>5</option>
                                <option value="10" <?= $itemsPerPage == 10 ? 'selected' : '' ?>>10</option>
                                <option value="15" <?= $itemsPerPage == 15 ? 'selected' : '' ?>>15</option>
                                <option value="20" <?= $itemsPerPage == 20 ? 'selected' : '' ?>>20</option>
                            </select>
                            <span>Categories</span>
                        </div>
                    </div>
                    <div class="col-sm-9">
                        <div class="filter-group d-flex justify-content-end align-items-center">
                            <label>Name</label>
                            <input type="text" class="form-control" name="search"
                                value="<?= htmlspecialchars($searchKeyword) ?>" placeholder="Search">
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
                        <th>Product Name</th>
                        <th>Quantity</th>
                        <th>Date</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $counter = $offset + 1;
                    foreach ($categories as $row): ?>
                        <tr>
                            <td class="text-center"><?= $counter++ ?></td>
                            <td class="text-center"><?= htmlspecialchars($row['name']) ?></td>
                            <td class="text-center"><?= htmlspecialchars($row['total_qty']) ?></td>
                            <td ><?= date('F j, Y, g:i:s A') ?></td>
                            <td class="text-center"><?= '₱ ' . number_format($row['total_price'], 0) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="clearfix">
            <div class="hint-text">Showing <b><?= count($categories) ?></b> out of <b><?= $totalItems ?></b> entries
            </div>
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


<!-- Edit User Modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="editCatModal_<?= $row['product_id'] ?>">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Category -
                    <?= htmlspecialchars($row['name']) ?>
                </h5>
            </div>
            <form action="../includes/category_actions.php?catId=<?= $row['product_id'] ?>" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="" class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" id="name"
                            value="<?= htmlspecialchars($row['name']) ?>" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="updateCat" id="updateCat" class="btn btn-primary">Save
                        Changes</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete User Modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="deleteCatModal_<?= $row['product_id'] ?>">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Category -
                    <?= htmlspecialchars($row['name']) ?>
                </h5>
            </div>
            <form action="../includes/category_actions.php?catId=<?= $row['product_id'] ?>" method="post">
                <div class="modal-body">
                    <p>Are you sure you want to delete
                        <?= htmlspecialchars($row['name']) ?>?
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="deleteCat" id="deleteCat" class="btn btn-danger">Delete</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Create User Modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="createCatModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Category</h5>
            </div>

            <form action="../includes/category_actions.php" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="" class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" id="name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="createCat" id="createCat" class="btn btn-primary">Add</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </form>

        </div>
    </div>
</div>

<script src="../lib/category/category.js"></script>
</body>

</html>