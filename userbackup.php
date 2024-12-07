<?php
include('../layouts/header.php');
require_once '../includes/load.php';

require_login();
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
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
            <div class="row">
                <div class="col-sm-3">
                    <div class="show-entries">
                        <span>Show</span>
                        <select class="form-control">
                            <option>5</option>
                            <option>10</option>
                            <option>15</option>
                            <option>20</option>
                        </select>
                        <span>entries</span>
                    </div>
                </div>
                <div class="col-sm-9">
                    <button type="button" class="btn btn-primary"><i class="fa fa-search"></i></button>
                    <div class="filter-group">
                        <label>Name</label>
                        <input type="text" class="form-control">
                    </div>
                    <div class="filter-group">
                        <label>Price</label>
                        <select class="form-control">
                            <option>Highest</option>
                            <option>Lowest</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Model</label>
                        <select class="form-control">
                            <option>Any</option>
                            <option>Delivered</option>
                            <option>Shipped</option>
                            <option>Pending</option>
                            <option>Cancelled</option>
                        </select>
                    </div>
                    <span class="filter-icon"><i class="fa fa-filter"></i></span>
                </div>
            </div>
        </div>
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
                <?php
                $stmt = $conn->query("SELECT category_id, name, created_at FROM categories");

                $counter = 1;
                while ($row = $stmt->fetch()) {
                    $createdAt = date_create($row['created_at']);
                    ?>
                    <tr>
                        <td class="text-center"><?= $counter++ ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td class="text-center"><?= date_format($createdAt, 'F j, Y, g:i:s a') ?></td>
                        <td class="text-center d-flex justify-content-center gap-2">
                            <button type="button" class="editCat-btn btn-secondary" data-bs-toggle="modal"
                                data-bs-target="#editCatModal_<?= $row['category_id'] ?>">
                                <i class="ri-pencil-line"></i>
                            </button>
                            <button type="button" class="deleteCat-btn btn-danger" data-bs-toggle="modal"
                                data-bs-target="#deleteCatModal_<?= $row['category_id'] ?>">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <div class="clearfix">
            <div class="hint-text">Showing <b>5</b> out of <b>25</b> entries</div>
            <ul class="pagination">
                <li class="page-item disabled"><a href="#">Previous</a></li>
                <li class="page-item"><a href="#" class="page-link">1</a></li>
                <li class="page-item"><a href="#" class="page-link">2</a></li>
                <li class="page-item"><a href="#" class="page-link">3</a></li>
                <li class="page-item active"><a href="#" class="page-link">4</a></li>
                <li class="page-item"><a href="#" class="page-link">5</a></li>
                <li class="page-item"><a href="#" class="page-link">6</a></li>
                <li class="page-item"><a href="#" class="page-link">7</a></li>
                <li class="page-item"><a href="#" class="page-link">Next</a></li>
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