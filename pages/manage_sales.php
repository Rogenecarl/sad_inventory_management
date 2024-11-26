<?php
include('../layouts/header.php');
require_once '../includes/load.php';

require_login();
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>

<link rel="stylesheet" href="../lib/managesales/managesale.css">

<main class="main container" id="main">
    <?php include('../layouts/sidebar.php'); ?>
    <h1 class="dash-fix">Manage Sales</h1>

    <div class="main__container">

        <div class="container p-0 m-0">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <form action="_redirect.php" method="post">
                                <div class="form-group">
                                    <input type="text" class="form-control" name="userSearch" id="userSearch"
                                        placeholder="Search here ..." autofocus required>
                                </div>
                            </form>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#addsalesModal">
                                Add Sales
                            </button>
                        </div>
                        <div class="card-body">

                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th class="text-center">#</th>
                                            <th>Product Name</th>
                                            <th>Quantity</th>
                                            <th>Total</th>
                                            <th>Date</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php
                                        $stmt = $conn->query("
                                        SELECT s.sales_id, p.name, s.qty, s.total_price, s.date
                                        FROM sales s
                                        JOIN products p ON s.product_id = p.prod_id
                                    ");

                                        $counter = 1;
                                        while ($row = $stmt->fetch()) {
                                            ?>
                                            <tr>
                                                <td class="text-center"><?= $counter++ ?></td>
                                                <td class="text-center"><?= htmlspecialchars($row['name']) ?></td>
                                                <td class="text-center"><?= htmlspecialchars($row['qty']) ?></td>
                                                <td class="text-center"><?= htmlspecialchars($row['total_price']) ?></td>
                                                <td class="text-center"><?= htmlspecialchars($row['date']) ?></td>
                                                <td class="text-center d-flex justify-content-center">
                                                    <button type="button" class="editPro-btn btn-secondary"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editSalesModal_<?= $row['sales_id'] ?>">
                                                        <i class="ri-pencil-line"></i>
                                                    </button>
                                                    <button type="button" class="deletePro-btn btn-danger"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#deleteSalesModal_<?= $row['sales_id'] ?>">
                                                        <i class="ri-delete-bin-line"></i>
                                                    </button>
                                                </td>
                                            </tr>


                                            <!-- Edit User Modal -->
                                            <div class="modal fade" tabindex="-1" role="dialog"
                                                id="editProdouctModal_<?= $row['prod_id'] ?>">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Edit Product -
                                                                <?= htmlspecialchars($row['name']) ?>
                                                            </h5>
                                                        </div>
                                                        <form
                                                            action="../includes/product_actions.php?proId=<?= $row['prod_id'] ?>"
                                                            method="post">
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label for="" class="form-label">Product Name</label>
                                                                    <input type="text" class="form-control" name="prodname"
                                                                        id="prodname"
                                                                        value="<?= htmlspecialchars($row['name']) ?>"
                                                                        required>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="" class="form-label">Brand</label>
                                                                    <input type="text" class="form-control" name="prodbrand"
                                                                        id="prodbrand"
                                                                        value="<?= htmlspecialchars($row['prod_brand']) ?>"
                                                                        required>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="" class="form-label">Model</label>
                                                                    <input type="text" class="form-control" name="prodmodel"
                                                                        id="prodmodel"
                                                                        value="<?= htmlspecialchars($row['prod_model']) ?>"
                                                                        required>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="" class="form-label">Stocks</label>
                                                                    <input type="number" class="form-control"
                                                                        name="prodquan" id="prodquan"
                                                                        value="<?= htmlspecialchars($row['quantity']) ?>"
                                                                        required>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="" class="form-label">Price</label>
                                                                    <input type="number" class="form-control"
                                                                        name="prodprice" id="prodprice"
                                                                        value="<?= htmlspecialchars($row['sale_price']) ?>"
                                                                        required>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="submit" name="updateproduct"
                                                                    id="updateproduct" class="btn btn-primary">Save
                                                                    Changes</button>
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">Close</button>
                                                            </div>
                                                        </form>

                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Delete User Modal -->
                                            <div class="modal fade" tabindex="-1" role="dialog"
                                                id="deleteProductModal_<?= $row['prod_id'] ?>">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Delete Product -
                                                                <?= htmlspecialchars($row['prod_id']) ?>
                                                            </h5>
                                                        </div>

                                                        <form
                                                            action="../includes/product_actions.php?proId=<?= $row['prod_id'] ?>"
                                                            method="post">
                                                            <div class="modal-body">
                                                                <p>Are you sure you want to delete
                                                                    <?= htmlspecialchars($row['name']) ?>?
                                                                </p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="submit" name="deleteproduct"
                                                                    id="deleteproduct"
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
                                <div class="pagination-container d-flex">
                                    <span class="total-users me-auto p-2">Showing 8 out of 0 users</span>
                                    <button class="prev-page btn btn-secondary" disabled>Previous</button>
                                    <button class="next-page btn btn-secondary" disabled>Next</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- add product Modal -->
        <div class="modal fade" tabindex="-1" role="dialog" id="addsalesModal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Product</h5>
                    </div>

                    <form action="../includes/product_actions.php" method="post">
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="" class="form-label">Product Name</label>
                                <input type="" class="form-control" name="prodname" id="prodname" required>
                            </div>
                            <div class="form-group">
                                <label for="category" class="form-label">Category</label>
                                <select class="form-select" name="category" required>
                                    <?php
                                    // Fetch categories from the database
                                    $categoriesStmt = $conn->query("SELECT category_id, name FROM categories");
                                    $currentCategoryId = $row['categorie_id']; // Define the current category ID
                                    while ($categoryRow = $categoriesStmt->fetch()) {
                                        ?>
                                        <option value="<?= $categoryRow['category_id'] ?>"
                                            <?= ($categoryRow['category_id'] == $currentCategoryId) ? 'selected' : '' ?>>
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


<!-- sidebar & header functions -->
<script src="../lib/managesales/managesales.js"></script>
</body>

</html>