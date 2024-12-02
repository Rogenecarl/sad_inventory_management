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

        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <form action="_redirect.php" method="post">
                        <input type="text" class="form-control" name="userSearch" id="userSearch"
                            placeholder="Search here ..." autofocus required>
                    </form>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                        data-bs-target="#createCatModal">
                        Add Sales
                    </button>
                </div>

                <div class="card-body">

                    <div class="table-responsive" style="height: 61vh; overflow-y: auto;">
                        <table class="table table-striped table-bordered">
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
                                // Adjusted SQL query to sum the quantity and total price per product
                                $stmt = $conn->query("
                                    SELECT p.name, SUM(s.qty) as total_qty, SUM(s.total_price) as total_price
                                    FROM sales s
                                    JOIN products p ON s.product_id = p.prod_id
                                    GROUP BY s.product_id, p.name
                                ");

                                $counter = 1;
                                while ($row = $stmt->fetch()) {
                                    ?>
                                    <tr>
                                        <td class="text-center"><?= $counter++ ?></td>
                                        <td class="text-center"><?= htmlspecialchars($row['name']) ?></td>
                                        <td class="text-center"><?= htmlspecialchars($row['total_qty']) ?></td>
                                        <td class="text-center"><?= '₱ ' . number_format($row['total_price'], 0) ?></td>
                                        <td class="text-center"><?= date('Y-m-d') ?></td> <!-- You can adjust the date as needed -->
                                        <td class="text-center d-flex justify-content-center gap-2">
                                            <button type="button" class="editCat-btn btn-secondary" data-bs-toggle="modal"
                                                data-bs-target="#editCatModal_<?= $row['product_id'] ?>">
                                                <i class="ri-pencil-line"></i>
                                            </button>
                                            <button type="button" class="deleteCat-btn btn-danger" data-bs-toggle="modal"
                                                data-bs-target="#deleteCatModal_<?= $row['product_id'] ?>">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Edit User Modal -->
                                    <div class="modal fade" tabindex="-1" role="dialog"
                                        id="editCatModal_<?= $row['product_id'] ?>">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit Category - <?= htmlspecialchars($row['name']) ?></h5>
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
                                                        <button type="submit" name="updateCat" id="updateCat"
                                                            class="btn btn-primary">Save Changes</button>
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Delete User Modal -->
                                    <div class="modal fade" tabindex="-1" role="dialog"
                                        id="deleteCatModal_<?= $row['product_id'] ?>">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Delete Category - <?= htmlspecialchars($row['name']) ?></h5>
                                                </div>
                                                <form action="../includes/category_actions.php?catId=<?= $row['product_id'] ?>" method="post">
                                                    <div class="modal-body">
                                                        <p>Are you sure you want to delete <?= htmlspecialchars($row['name']) ?>?</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" name="deleteCat" id="deleteCat"
                                                            class="btn btn-danger">Delete</button>
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="pagination-container d-flex">
                        <span class="total-users me-auto p-2">Showing 8 out of 0 Categories</span>
                        <button class="prev-page btn btn-secondary" disabled>Previous</button>
                        <button class="next-page btn btn-secondary" disabled>Next</button>
                    </div>
                </div>
            </div>
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

    </div>
</main>

<script src="../lib/category/category.js"></script>
</body>

</html>
