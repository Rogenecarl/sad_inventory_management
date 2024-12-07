<?php
include('../layouts/header.php');
require_once '../includes/load.php';

require_login();
$currentPage = basename($_SERVER['PHP_SELF'], '.php');

// Default settings for pagination and filtering
$itemsPerPage = isset($_GET['items_per_page']) ? (int) $_GET['items_per_page'] : 5;
$currentPageNumber = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$searchKeyword = isset($_GET['search']) ? $_GET['search'] : '';

// Calculate offset for pagination
$offset = ($currentPageNumber - 1) * $itemsPerPage;

// Fetch categories with pagination and search
$sql = "SELECT COUNT(*) FROM categories WHERE name LIKE :search";
$stmt = $conn->prepare($sql);
$stmt->execute([':search' => "%$searchKeyword%"]);
$totalItems = $stmt->fetchColumn();

$totalPages = ceil($totalItems / $itemsPerPage);

$sql = "SELECT category_id, name, created_at 
        FROM categories 
        WHERE name LIKE :search 
        ORDER BY created_at DESC 
        LIMIT :limit OFFSET :offset";
$stmt = $conn->prepare($sql);
$stmt->bindValue(':search', "%$searchKeyword%", PDO::PARAM_STR);
$stmt->bindValue(':limit', $itemsPerPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<link rel="stylesheet" href="../lib/category/category.css">

<main class="main container" id="main">
    <?php include('../layouts/sidebar.php'); ?>

    <div class="table-wrapper">
        <div class="table-title">
            <div class="d-flex justify-content-between">
                <div class="col-sm-4">
                    <h2>Categories</h2>
                </div>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCatModal">
                    <i class="fa fa-plus-circle"></i>
                    Add Category
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
                        <th>Name</th>
                        <th>Created At</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($categories)): ?>
                        <tr>
                            <td colspan="4" class="text-center">No categories found</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($categories as $index => $category): ?>
                            <tr>
                                <td class="text-center"><?= $offset + $index + 1 ?></td>
                                <td><?= htmlspecialchars($category['name']) ?></td>
                                <td><?= date('F j, Y, g:i:s a', strtotime($category['created_at'])) ?></td>
                                <td class="text-center d-flex justify-content-center gap-2">
                                    <button type="button" class="editCat-btn btn-secondary" data-bs-toggle="modal"
                                        data-bs-target="#editCatModal_<?= $category['category_id'] ?>">
                                        <i class="ri-pencil-line"></i>
                                    </button>
                                    <button type="button" class="deleteCat-btn btn-danger" data-bs-toggle="modal"
                                        data-bs-target="#deleteCatModal_<?= $category['category_id'] ?>">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
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

<!-- Edit User Modal -->
<?php
$stmt = $conn->query("SELECT category_id, name, created_at FROM categories");

$counter = 1;
while ($row = $stmt->fetch()) {
    $createdAt = date_create($row['created_at']);
    ?>
    <div class="modal fade" tabindex="-1" role="dialog" id="editCatModal_<?= $row['category_id'] ?>">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Category - <?= htmlspecialchars($row['name']) ?></h5>
                </div>

                <form action="../includes/category_actions.php?catId=<?= $row['category_id'] ?>" method="post">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="" class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" id="name"
                                value="<?= htmlspecialchars($row['name']) ?>" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="updateCat" id="updateCat" class="btn btn-primary">Save Changes</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete User Modal -->
    <div class="modal fade" tabindex="-1" role="dialog" id="deleteCatModal_<?= $row['category_id'] ?>">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Category - <?= htmlspecialchars($row['name']) ?></h5>
                </div>
                <form action="../includes/category_actions.php?catId=<?= $row['category_id'] ?>" method="post">
                    <div class="modal-body">
                        <p>Are you sure you want to delete <?= htmlspecialchars($row['name']) ?>?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="deleteCat" id="deleteCat" class="btn btn-danger">Delete</button>

                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php } ?>


<script src="../lib/category/category.js"></script>
</body>

</html>

<div class="table-wrapper">
    <div class="table-title">
        <div class="d-flex justify-content-between">
            <div class="col-sm-4">
                <h2>Categories</h2>
            </div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createProductModal">
                <i class="fa fa-plus-circle"></i>
                Add Product
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
                    <th>Product Name</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="productTableBody">
                <?php
                if (!empty($searchTerm)) {
                    $stmt = $conn->prepare(
                        "SELECT prod_id, name, sale_price, prod_brand, prod_model 
                                             FROM products 
                                             WHERE name LIKE ? OR prod_brand LIKE ? OR prod_model LIKE ?"
                    );
                    $searchLike = "%" . $searchTerm . "%";
                    $stmt->execute([$searchLike, $searchLike, $searchLike]);
                } else {
                    $stmt = $conn->query("SELECT prod_id, name, sale_price FROM products");
                }

                while ($row = $stmt->fetch()) {
                    $currentDate = date('Y-m-d');
                    $prod_id = $row['prod_id'];
                    $price = $row['sale_price'];
                    $quantity = isset($quantities[$prod_id]) ? $quantities[$prod_id] : 1;
                    $totalPrice = number_format($price * $quantity, 0);
                    ?>
                    <tr>
                        <td class="text-center"><?= htmlspecialchars($row['name']) ?></td>
                        <td class="text-center">
                            <input type="text" class="form-control price-input" readonly
                                value="₱ <?= number_format($price, 0, '.', ',') ?>" name="price[<?= $prod_id ?>]">
                        </td>
                        <td class="text-center">
                            <input type="number" class="form-control quantity-input" min="1" value="<?= $quantity ?>"
                                name="quantity[<?= $prod_id ?>]" required data-prod-id="<?= $prod_id ?>">
                        </td>
                        <td class="text-center">
                            <input type="text" class="form-control total-price" value="₱ <?= $totalPrice ?>" readonly
                                data-prod-id="<?= $prod_id ?>">
                        </td>
                        <td class="text-center">
                            <input type="date" class="form-control" value="<?= $currentDate ?>"
                                name="sale_date[<?= $prod_id ?>]" required>
                        </td>
                        <td class="text-center">
                            <button type="button" class="sale-button btn addSaleButton" data-prod-id="<?= $prod_id ?>"
                                style="white-space: nowrap;">
                                <i class="fa fa-plus-circle"></i> Add Sale
                            </button>
                        </td>
                    </tr>
                <?php } ?>

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


<div class="col-lg-12">
    <div class="card">
        <div class="card-header">
            <form action="_redirect.php" method="post">
                <input type="text" class="form-control" name="userSearch" id="userSearch" placeholder="Search here ..."
                    autofocus required>
            </form>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
                <i class="fa fa-plus-circle"></i>
                Add User
            </button>
        </div>
        <div class="card-body">

            <div class="table-responsive" style="height: 61vh; overflow-y: auto;">
                <table class="table table-striped table-bordered" style="text-align: center; vertical-align: middle;">
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th>Name</th>
                            <th>Username</th>
                            <th>User Role</th>
                            <th>Status</th>
                            <th>Last Login</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $conn->query("SELECT User_id, name, username, user_level, status, last_login FROM users");
                        $user_levels = [
                            1 => 'Developer',
                            2 => 'Admin',
                            3 => 'Staff'
                        ];
                        $statuses = [
                            1 => 'Active',
                            0 => 'Inactive'
                        ];

                        $counter = 1;
                        while ($row = $stmt->fetch()) {
                            $createdAt = date_create($row['last_login']);
                            ?>
                            <tr>
                                <td class="text-center"><?= $counter++ ?></td>
                                <td><?= htmlspecialchars($row['name']) ?></td>
                                <td><?= htmlspecialchars($row['username']) ?></td>
                                <td><?= $user_levels[$row['user_level']] ?></td>
                                <td><?= $statuses[$row['status']] ?></td>
                                <td class="text-center">
                                    <?= ($createdAt) ? date_format($createdAt, 'F j, Y, g:i:s a') : 'Not logged in' ?>
                                </td>
                                </td>
                                <td class="text-center d-flex justify-content-center gap-2">
                                    <button type="button" class="editU-btn btn-secondary" data-bs-toggle="modal"
                                        data-bs-target="#editUserModal_<?= $row['User_id'] ?>">
                                        <i class="ri-pencil-line"></i>
                                    </button>
                                    <button type="button" class="deleteU-btn btn-danger" data-bs-toggle="modal"
                                        data-bs-target="#deleteUserModal_<?= $row['User_id'] ?>">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </td>
                            </tr>

                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <!-- Pagination footer -->
            <div class="pagination-container d-flex gap-2">
                <span class="total-users me-auto p-2">Showing 8 out of 0 users</span>
                <button class="prev-page btn btn-secondary" disabled>Previous</button>
                <button class="next-page btn btn-secondary" disabled>Next</button>
            </div>
        </div>
    </div>
</div>