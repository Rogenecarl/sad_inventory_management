<?php
include('../layouts/header.php');
require_once '../includes/load.php';

require_login();
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>

<link rel="stylesheet" href="../lib/products/products.css">

<main class="main container" id="main">
    <?php include('../layouts/sidebar.php'); ?>
    <h1 class="dash-fix">Products</h1>
    <div class="main__container">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <form action="_redirect.php" method="post">
                        <input type="text" class="form-control" name="userSearch" id="userSearch"
                            placeholder="Search here ..." autofocus required>
                    </form>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                        data-bs-target="#createProductModal">
                        <i class="fa fa-plus-circle"></i>
                        Add Product
                    </button>
                </div>
                <div class="card-body">

                    <div class="table-responsive" style="height: 61vh; overflow-y: auto;">
                        <table class="table table-striped table-bordered"
                            style="text-align: center; vertical-align: middle;">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th>Photo</th>
                                    <th>Product Name</th>
                                    <th>Category</th>
                                    <th>Brand</th>
                                    <th>Model</th>
                                    <th>Stocks</th>
                                    <th>Price</th>
                                    <th>Created At</th>
                                    <th>Updated At</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
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
                                    <tr>
                                        <td class="text-center"><?= $counter++ ?></td>
                                        <td class="text-center">
                                            <?php if (!empty($row['photo'])): ?>
                                                <img src="../uploads/products/<?= htmlspecialchars($row['photo']) ?>"
                                                    alt="Product Image" width="100">
                                            <?php else: ?>
                                                No photo available
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center"><?= htmlspecialchars($row['name']) ?></td>
                                        <td class="text-center"><?= htmlspecialchars($category) ?></td>
                                        <td class="text-center"><?= htmlspecialchars($row['prod_brand']) ?></td>
                                        <td class="text-center"><?= htmlspecialchars($row['prod_model']) ?></td>
                                        <td class="text-center">
                                            <?= htmlspecialchars(number_format($row['quantity'])) ?>
                                        </td>
                                        <td class="text-center"><?= '₱ ' . number_format($row['sale_price'], 0) ?>
                                        <td class="text-center"><?= date_format($createdAt, 'F j, Y, g:i:s a') ?></td>
                                        <td class="text-center"><?= date_format($updatedAt, 'F j, Y, g:i:s a') ?></td>
                                        <td class="text-center d-flex gap-2">
                                            <button type="button" class="editPro-btn btn-secondary" data-bs-toggle="modal"
                                                data-bs-target="#editProdouctModal_<?= $row['prod_id'] ?>">
                                                <i class="ri-pencil-line"></i>
                                            </button>
                                            <button type="button" class="deletePro-btn btn-danger" data-bs-toggle="modal"
                                                data-bs-target="#deleteProductModal_<?= $row['prod_id'] ?>">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </td>
                                    </tr>


                                    <!-- Edit product Modal -->
                                    <div class="modal fade" tabindex="-1" role="dialog"
                                        id="editProdouctModal_<?= $row['prod_id'] ?>">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit Product -
                                                        <?= htmlspecialchars($row['name']) ?>
                                                    </h5>
                                                </div>
                                                <form action="../includes/product_actions.php?proId=<?= $row['prod_id'] ?>"
                                                    method="post" enctype="multipart/form-data">
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <label for="prodname" class="form-label">Product Name</label>
                                                            <input type="text" class="form-control" name="prodname"
                                                                id="prodname" value="<?= htmlspecialchars($row['name']) ?>"
                                                                required>
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="productPhoto" class="form-label">Upload New
                                                                Photo</label>
                                                            <input type="file" class="form-control" name="productPhoto"
                                                                id="productPhoto" accept="image/*">
                                                            <small>Leave empty to keep the current photo.</small>
                                                            <?php if (!empty($row['photo'])): ?>
                                                                <div class="mt-2">
                                                                    <img src="../uploads/products/<?= htmlspecialchars($row['photo']) ?>"
                                                                        alt="Current Photo" class="img-fluid"
                                                                        style="max-height: 200px;">
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="prodbrand" class="form-label">Brand</label>
                                                            <input type="text" class="form-control" name="prodbrand"
                                                                id="prodbrand"
                                                                value="<?= htmlspecialchars($row['prod_brand']) ?>"
                                                                required>
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="prodmodel" class="form-label">Model</label>
                                                            <input type="text" class="form-control" name="prodmodel"
                                                                id="prodmodel"
                                                                value="<?= htmlspecialchars($row['prod_model']) ?>"
                                                                required>
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="prodquan" class="form-label">Stocks</label>
                                                            <input type="number" class="form-control" name="prodquan"
                                                                id="prodquan"
                                                                value="<?= htmlspecialchars($row['quantity']) ?>" required>
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="prodprice" class="form-label">Price</label>
                                                            <input type="number" class="form-control" name="prodprice"
                                                                id="prodprice"
                                                                value="<?= htmlspecialchars($row['sale_price']) ?>"
                                                                required>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" name="updateproduct" id="updateproduct"
                                                            class="btn btn-primary">Save Changes</button>
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Close</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>


                                    <!-- Delete product Modal -->
                                    <div class="modal fade" tabindex="-1" role="dialog"
                                        id="deleteProductModal_<?= $row['prod_id'] ?>">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Delete Product -
                                                        <?= htmlspecialchars($row['prod_id']) ?>
                                                    </h5>
                                                </div>

                                                <form action="../includes/product_actions.php?proId=<?= $row['prod_id'] ?>"
                                                    method="post">
                                                    <div class="modal-body">
                                                        <p>Are you sure you want to delete
                                                            <?= htmlspecialchars($row['name']) ?>?
                                                        </p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" name="deleteproduct" id="deleteproduct"
                                                            class="btn btn-danger">Delete</button>

                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Close</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
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
                                <input type="file" class="form-control" name="productPhoto" id="productPhoto"
                                    accept="image/*">
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
                            <button type="submit" name="addproduct" id="addproduct"
                                class="btn btn-primary">Create</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>


    </div>
</main>

<script src="../lib/products/products.js"></script>
</body>

</html>