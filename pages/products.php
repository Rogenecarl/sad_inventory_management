<?php
include('../layouts/header.php');
require_once '../includes/load.php';
require_login();

$currentPage = basename($_SERVER['PHP_SELF'], '.php');

// Handle pagination and search inputs
$itemsPerPage = isset($_GET['items_per_page']) ? (int) $_GET['items_per_page'] : 50;
$currentPageNumber = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$searchKeyword = isset($_GET['search']) ? trim($_GET['search']) : '';
$offset = ($currentPageNumber - 1) * $itemsPerPage;

// Fetch total products count
$totalItemsQuery = $conn->query("SELECT COUNT(*) FROM products WHERE name LIKE '%$searchKeyword%'");
$totalItems = $totalItemsQuery->fetchColumn();
$totalPages = ceil($totalItems / $itemsPerPage);

// Fetch paginated products
$stmt = $conn->prepare("
    SELECT prod_id, name, photo, categorie_id, prod_brand, prod_model, quantity, sale_price, created_at, updated_at 
    FROM products 
    WHERE name LIKE :search 
    LIMIT :limit OFFSET :offset
");
$stmt->bindValue(':search', '%' . $searchKeyword . '%', PDO::PARAM_STR);
$stmt->bindValue(':limit', $itemsPerPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

// Prepare category fetch query
$categoryStmt = $conn->prepare("SELECT name FROM categories WHERE category_id = ?");

$message = isset($_GET['message']) ? $_GET['message'] : '';
$message_type = isset($_GET['message_type']) ? $_GET['message_type'] : '';
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<link rel="stylesheet" href="../lib/products/products.css">

<main class="main container" id="main">
    <?php include('../layouts/sidebar.php'); ?>

    <div class="table-wrapper">
        <div class="table-title">
            <div class="d-flex justify-content-between">
                <div class="col-sm-4">
                    <h2>Products</h2>
                </div>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                    data-bs-target="#createProductModal">
                    <i class="fa fa-plus-circle"></i> Add Product
                </button>
            </div>
        </div>

        <!-- Filter Section -->
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
                            <span>Products</span>
                        </div>
                    </div>
                    <div class="col-sm-9">
                        <div class="filter-group d-flex justify-content-end align-items-center">
                            <label>Name</label>
                            <input type="text" class="form-control" name="search"
                                value="<?= htmlspecialchars($searchKeyword) ?>" placeholder="Search">
                            <button type="submit" class="btn btn-primary ms-2"><i class="fa fa-search"></i></button>
                        </div>
                        <div class="filter-group">
                            <label>Price</label>
                            <select class="form-control">
                                <option>Highest</option>
                                <option>Lowest</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label>Sort by Category</label>
                            <select class="form-control">
                                <option>Newest</option>
                                <option>Oldest</option>
                            </select>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Product Table -->
        <div class="table-responsive mb-4" style="height: 61vh; overflow-y: auto;">
            <table class="table table-striped table-hover" style="text-align: center; vertical-align: middle;">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Photo</th>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th>Brand</th>
                        <th>Model</th>
                        <th>Stocks</th>
                        <th>Price</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $counter = $offset + 1;
                    while ($row = $stmt->fetch()) {
                        $categoryStmt->execute([$row['categorie_id']]);
                        $category = $categoryStmt->fetchColumn();

                        $createdAt = date_create($row['created_at']);
                        $updatedAt = $row['updated_at'] !== null ? date_create($row['updated_at']) : null;
                        ?>
                        <tr>
                            <td><?= $counter++ ?></td>
                            <td>
                                <?php if (!empty($row['photo'])): ?>
                                    <img src="../uploads/products/<?= htmlspecialchars($row['photo']) ?>" alt="Product Image"
                                        width="100">
                                <?php else: ?>
                                    No photo available
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= htmlspecialchars($category) ?></td>
                            <td><?= htmlspecialchars($row['prod_brand']) ?></td>
                            <td><?= htmlspecialchars($row['prod_model']) ?></td>
                            <td><?= htmlspecialchars(number_format($row['quantity'])) ?></td>
                            <td>₱ <?= number_format($row['sale_price'], 2) ?></td>
                            <td><?= date_format($createdAt, 'F j, Y, g:i:s a') ?></td>
                            <td>
                                <?php if ($updatedAt === null || $row['updated_at'] === $row['created_at']): ?>
                                    Not yet updated
                                <?php else: ?>
                                    <?= date_format($updatedAt, 'F j, Y, g:i:s a') ?>
                                <?php endif; ?>
                            </td>
                            <td class="text-center d-flex gap-2">
                                <button class="editPro-btn btn-secondary" data-bs-toggle="modal"
                                    data-bs-target="#editProdouctModal_<?= $row['prod_id'] ?>"><i
                                        class="ri-pencil-line"></i></button>
                                <button class="deletePro-btn btn-danger" data-bs-toggle="modal"
                                    data-bs-target="#deleteProductModal_<?= $row['prod_id'] ?>"><i
                                        class="ri-delete-bin-line"></i></button>
                                <button class="viewPro-btn btn-primary view-icon" data-bs-toggle="modal"
                                    data-bs-target="#viewProductModal_<?= $row['prod_id'] ?>"><i
                                        class="ri-eye-line"></i></button>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="clearfix">
            <div class="hint-text">Showing <b><?= $stmt->rowCount() ?></b> out of <b><?= $totalItems ?></b> entries
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

<!-- Add Product Modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="createProductModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Product</h5>
            </div>

            <!-- Added enctype="multipart/form-data" to the form -->
            <form action="../includes/product_actions.php" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="" class="form-label">Product Name</label>
                        <input type="text" class="form-control" name="prodname" id="prodname">
                    </div>

                    <div class="form-group">
                        <label for="productPhoto" class="form-label">Upload Photo (Optional)</label>
                        <input type="file" class="form-control" name="productPhoto" id="productPhoto" accept="image/*">
                    </div>

                    <div class="form-group">
                        <label for="category" class="form-label">Category</label>
                        <select class="form-select" name="category" required>
                            <?php
                            $categoriesStmt = $conn->query("SELECT category_id, name FROM categories");
                            while ($categoryRow = $categoriesStmt->fetch()) {
                                ?>
                                <option value="<?= $categoryRow['category_id'] ?>">
                                    <?= htmlspecialchars($categoryRow['name']) ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="" class="form-label">Brand</label>
                        <input type="text" class="form-control" name="brand" id="brand" required>
                    </div>
                    <div class="form-group">
                        <label for="" class="form-label">Model</label>
                        <input type="text" class="form-control" name="prodM" id="prodM" required>
                    </div>

                    <div class="form-group">
                        <label for="" class="form-label">Quantity</label>
                        <input type="number" class="form-control" name="prodQ" id="prodQ" required>
                    </div>

                    <div class="form-group">
                        <label for="" class="form-label">Price</label>
                        <input type="number" class="form-control" name="prodPrice" id="prodPrice" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="addproduct" id="addproduct" class="btn btn-primary">Create</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </form>

        </div>
    </div>
</div>


<?php
$stmt = $conn->query("SELECT prod_id, name, photo, categorie_id, prod_brand, prod_model, quantity, sale_price, created_at, updated_at FROM products");
$categoryStmt = $conn->prepare("SELECT name FROM categories WHERE category_id = ?");
$counter = 1;

while ($row = $stmt->fetch()) {
    $categoryStmt->execute([$row['categorie_id']]);
    $category = $categoryStmt->fetchColumn();

    $createdAt = date_create($row['created_at']);
    $updatedAt = date_create($row['updated_at']);
    ?>

    <!-- View Product History Modal -->
    <div class="modal fade" id="viewProductModal_<?= $row['prod_id'] ?>" tabindex="-1"
        aria-labelledby="viewProductModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewProductModalLabel">View Product History</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Product Name:</strong> <?= htmlspecialchars($row['name']) ?></p>
                    <h6>Stock History:</h6>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Quantity Added</th>
                                <th>Previous Stock</th>
                                <th>New Stock</th>
                                <th>Price</th>
                                <th>Date Added</th>
                                <!-- <th>Remarks</th> -->
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $historyStmt = $conn->prepare("SELECT quantity_added, previous_stock, new_stock, price, created_at, remarks
                                                          FROM StockHistory
                                                          WHERE prod_id = :prod_id
                                                          ORDER BY created_at DESC");
                            $historyStmt->execute(['prod_id' => $row['prod_id']]);

                            while ($history = $historyStmt->fetch(PDO::FETCH_ASSOC)) {
                                echo "<tr>";
                                echo "<td>" . $history['quantity_added'] . "</td>";
                                echo "<td>" . $history['previous_stock'] . "</td>";
                                echo "<td>" . $history['new_stock'] . "</td>";
                                echo "<td>₱ " . number_format($history['price'], 2) . "</td>";
                                echo "<td>" . date('F j, Y, g:i:s A', strtotime($history['created_at'])) . "</td>";
                                echo "<td>" . htmlspecialchars($history['remarks']) . "</td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit product Modal -->
    <div class="modal fade" tabindex="-1" role="dialog" id="editProdouctModal_<?= $row['prod_id'] ?>">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Product -
                        <?= htmlspecialchars($row['name']) ?>
                    </h5>
                </div>
                <form action="../includes/product_actions.php?proId=<?= $row['prod_id'] ?>" method="post"
                    enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="prodname" class="form-label">Product Name</label>
                            <input type="text" class="form-control" name="prodname" id="prodname"
                                value="<?= htmlspecialchars($row['name']) ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="productPhoto" class="form-label">Upload New
                                Photo</label>
                            <input type="file" class="form-control" name="productPhoto" id="productPhoto" accept="image/*">
                            <small>Leave empty to keep the current photo.</small>
                            <?php if (!empty($row['photo'])): ?>
                                <div class="mt-2">
                                    <img src="../uploads/products/<?= htmlspecialchars($row['photo']) ?>" alt="Current Photo"
                                        class="img-fluid" style="max-height: 200px;">
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="prodbrand" class="form-label">Brand</label>
                            <input type="text" class="form-control" name="prodbrand" id="prodbrand"
                                value="<?= htmlspecialchars($row['prod_brand']) ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="prodmodel" class="form-label">Model</label>
                            <input type="text" class="form-control" name="prodmodel" id="prodmodel"
                                value="<?= htmlspecialchars($row['prod_model']) ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="prodquan" class="form-label">Stocks</label>
                            <input type="number" class="form-control" name="prodquan" id="prodquan"
                                placeholder="<?= htmlspecialchars($row['quantity']) ?>">
                        </div>

                        <div class="form-group">
                            <label for="prodprice" class="form-label">Price</label>
                            <input type="number" class="form-control" name="prodprice" id="prodprice"
                                value="<?= htmlspecialchars($row['sale_price']) ?>">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="updateproduct" id="updateproduct" class="btn btn-primary">Save
                            Changes</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Delete product Modal -->
    <div class="modal fade" tabindex="-1" role="dialog" id="deleteProductModal_<?= $row['prod_id'] ?>">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Product -
                        <?= htmlspecialchars($row['prod_id']) ?>
                    </h5>
                </div>

                <form action="../includes/product_actions.php?proId=<?= $row['prod_id'] ?>" method="post">
                    <div class="modal-body">
                        <p>Are you sure you want to delete
                            <?= htmlspecialchars($row['name']) ?>?
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="deleteproduct" id="deleteproduct" class="btn btn-danger">Delete</button>

                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php } ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<?php include '../toast/toastr.php'; ?>
<script src="../lib/products/products.js"></script>
</body>

</html>