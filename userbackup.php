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

        <!-- Search and Create Account Button Row -->
        <div class="search-create-container">
            <button type="button" class="addbtn btn-primary" data-bs-toggle="modal"
                data-bs-target="#createAccountModal">
                Add New Users
            </button>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="createAccountModal" tabindex="-1" aria-labelledby="createAccountModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createAccountModalLabel">Create User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="createAccountForm" method="POST">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name:</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="username" class="form-label">Username:</label>
                                <input type="text" class="form-control" name="username" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password:</label>
                                <input type="password" class="form-control" name="password" required>
                            </div>
                            <div class="mb-3">
                                <label for="role" class="form-label">User Role:</label>
                                <select class="form-select" name="role" required>
                                    <option value="1">Developer</option>
                                    <option value="2">Admin</option>
                                    <option value="3">Staff</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" form="createAccountForm" class="btn btn-primary"
                                name="createU-btn">Create</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit User Modal -->
        <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="editUserForm" method="POST">
                        <div class="modal-body">
                            <input type="hidden" id="editUserId" name="user_id">

                            <div class="mb-3">
                                <label for="editName" class="form-label">Name:</label>
                                <input type="text" class="form-control" id="editName" name="name" required>

                            </div>

                            <div class="mb-3">
                                <label for="editUsername" class="form-label">Username:</label>
                                <input type="text" class="form-control" id="editUsername" name="username" required>
                            </div>

                            <div class="mb-3">
                                <label for="editPassword" class="form-label">Password:</label>
                                <input type="password" class="form-control" id="editPassword" name="password" required>
                            </div>

                            <div class="mb-3">
                                <label for="editRole" class="form-label">User Role:</label>
                                <select class="form-select" id="editRole" name="role" required>
                                    <option value="1">Developer</option>
                                    <option value="2">Admin</option>
                                    <option value="3">Staff</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="editStatus" class="form-label">Status:</label>
                                <select class="form-select" id="editStatus" name="status" required>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" name="editU-btn">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete User Modal -->
        <div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteUserModalLabel">Delete User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="deleteUserForm" method="POST">
                        <div class="modal-body">
                            <p>Are you sure you want to delete this user?</p>
                            <input type="hidden" id="deleteUserId" name="user_id">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger" name="deleteU-btn">Delete</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Users Table -->
        <div class="table-container">
            <table id="usersTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Username</th>
                        <th>User Role</th>
                        <th>Status</th>
                        <th>Last Login</th>
                        <th>Actions</th>
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
                    ]; // Adjust roles as needed
                    $statuses = [
                        1 => 'Active',
                        0 => 'Inactive'
                    ]; // Adjust status mapping as needed
                    
                    $counter = 1; // Counter for the row number
                    while ($row = $stmt->fetch()) {
                        echo "<tr>";
                        echo "<td>" . $counter++ . "</td>"; // Row number
                        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                        echo "<td>" . $user_levels[$row['user_level']] . "</td>";
                        echo "<td>" . $statuses[$row['status']] . "</td>";
                        echo "<td>" . ($row['last_login'] ? htmlspecialchars($row['last_login']) : 'Never') . "</td>";
                        echo "<td>";
                        echo "<div class='button-container'>
                                <button type='button' class='editU-btn' data-bs-toggle='modal' data-bs-target='#editUserModal' onclick=\"editUser(" . $row['User_id'] . ")\">
                                    <i class='ri-pencil-line'></i>
                                </button>
                                <button type='button' class='deleteU-btn' onclick=\"setDeleteUserId(" . $row['User_id'] . ")\" data-bs-toggle='modal' data-bs-target='#deleteUserModal'>
                                    <i class='ri-delete-bin-line'></i>
                                </button>
                            </div>";

                        echo "</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</main>


<!-- sidebar & header functions -->
<script src="../lib/usermanagement/userscript.js"></script>
</body>

</html>