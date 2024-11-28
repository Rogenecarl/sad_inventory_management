<?php
include('../layouts/header.php');
require_once '../includes/load.php';

require_login();
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>

<link rel="stylesheet" href="../lib/usermanagement/userstyle.css">

<main class="main container" id="main">
    <?php include('../layouts/sidebar.php'); ?>
    <h1 class="dash-fix">User Management</h1>

    <div class="main__container">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <form action="_redirect.php" method="post">
                        <input type="text" class="form-control" name="userSearch" id="userSearch"
                            placeholder="Search here ..." autofocus required>
                    </form>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                        data-bs-target="#createUserModal">
                        Add Product
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
                                        // Fetch users from the database
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

                                        $counter = 1; // Counter for the row number
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
                                                    <button type="button" class="editU-btn btn-secondary"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editUserModal_<?= $row['User_id'] ?>">
                                                        <i class="ri-pencil-line"></i>
                                                    </button>
                                                    <button type="button" class="deleteU-btn btn-danger"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#deleteUserModal_<?= $row['User_id'] ?>">
                                                        <i class="ri-delete-bin-line"></i>
                                                    </button>
                                                </td>
                                            </tr>

                                            <!-- Edit User Modal -->
                                            <div class="modal fade" tabindex="-1" role="dialog"
                                                id="editUserModal_<?= $row['User_id'] ?>">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Edit User -
                                                                <?= htmlspecialchars($row['name']) ?>
                                                            </h5>
                                                        </div>

                                                        <form
                                                            action="../includes/users_actions.php?userId=<?= $row['User_id'] ?>"
                                                            method="post">
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label for="" class="form-label">Name</label>
                                                                    <input type="text" class="form-control" name="name"
                                                                        id="name"
                                                                        value="<?= htmlspecialchars($row['name']) ?>"
                                                                        required>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="" class="form-label">Username</label>
                                                                    <input type="text" class="form-control" name="username"
                                                                        id="username"
                                                                        value="<?= htmlspecialchars($row['username']) ?>"
                                                                        required>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="" class="form-label">Password</label>
                                                                    <input type="password" class="form-control"
                                                                        name="password" id="password">
                                                                    <!-- Password field: optional for updating the password -->
                                                                    <small class="form-text text-muted">Leave blank if you
                                                                        don't want to change the password.</small>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="" class="form-label">User Role</label>
                                                                    <select class="form-select" name="role" required>
                                                                        <option value="1" <?= ($row['user_level'] == 1) ? 'selected' : '' ?>>Developer</option>
                                                                        <option value="2" <?= ($row['user_level'] == 2) ? 'selected' : '' ?>>Admin</option>
                                                                        <option value="3" <?= ($row['user_level'] == 3) ? 'selected' : '' ?>>Staff</option>
                                                                    </select>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="" class="form-label">Status</label>
                                                                    <select class="form-select" name="status" required>
                                                                        <option value="1" <?= ($row['status'] == 1) ? 'selected' : '' ?>>Active</option>
                                                                        <option value="0" <?= ($row['status'] == 0) ? 'selected' : '' ?>>Inactive</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="submit" name="updateUser" id="updateUser"
                                                                    class="btn btn-primary">Save Changes</button>
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">Close</button>
                                                            </div>
                                                        </form>

                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Delete User Modal -->
                                            <div class="modal fade" tabindex="-1" role="dialog"
                                                id="deleteUserModal_<?= $row['User_id'] ?>">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Delete User -
                                                                <?= htmlspecialchars($row['name']) ?>
                                                            </h5>
                                                        </div>

                                                        <form
                                                            action="../includes/users_actions.php?userId=<?= $row['User_id'] ?>"
                                                            method="post">
                                                            <div class="modal-body">
                                                                <p>Are you sure you want to delete
                                                                    <?= htmlspecialchars($row['name']) ?>?
                                                                </p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="submit" name="deleteUser" id="deleteUser"
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

        <!-- Create User Modal -->
        <div class="modal fade" tabindex="-1" role="dialog" id="createUserModal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Create User</h5>
                    </div>

                    <form action="../includes/users_actions.php" method="post">
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="" class="form-label">Name</label>
                                <input type="text" class="form-control" name="name" id="name" required>
                            </div>
                            <div class="form-group">
                                <label for="" class="form-label">Username</label>
                                <input type="text" class="form-control" name="username" id="username" required>
                            </div>
                            <div class="form-group">
                                <label for="" class="form-label">Password</label>
                                <input type="password" class="form-control" name="password" id="password" required>
                            </div>
                            <div class="form-group">
                                <label for="" class="form-label">User Role</label>
                                <select class="form-select" name="role" required>
                                    <option value="1">Developer</option>
                                    <option value="2">Admin</option>
                                    <option value="3">Staff</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="" class="form-label">Status</label>
                                <select class="form-select" name="status" required>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" name="createUser" id="createUser"
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
<script src="../lib/usermanagement/userscript.js"></script>
</body>

</html>