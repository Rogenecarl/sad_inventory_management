<?php
include('../layouts/header.php');
require_once '../includes/load.php';

require_login();
$itemsPerPage = isset($_GET['items_per_page']) ? (int) $_GET['items_per_page'] : 5;
$searchKeyword = isset($_GET['search']) ? trim($_GET['search']) : '';
$currentPageNumber = isset($_GET['page']) ? (int) $_GET['page'] : 1;

if (!$conn) {
    die("Database connection failed.");
}

$totalItemsQuery = $conn->prepare("SELECT COUNT(*) FROM users WHERE name LIKE :keyword");
$totalItemsQuery->execute([':keyword' => '%' . $searchKeyword . '%']);
$totalItems = $totalItemsQuery->fetchColumn();

$totalPages = ceil($totalItems / $itemsPerPage);
$offset = ($currentPageNumber - 1) * $itemsPerPage;

$query = $conn->prepare("
    SELECT User_id, name, username, user_level, status, last_login
    FROM users
    WHERE name LIKE :keyword
    ORDER BY name ASC
    LIMIT :offset, :itemsPerPage
");

$query->bindValue(':keyword', '%' . $searchKeyword . '%', PDO::PARAM_STR);
$query->bindValue(':offset', $offset, PDO::PARAM_INT);
$query->bindValue(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);
$query->execute();
$users = $query->fetchAll(PDO::FETCH_ASSOC);

$user_levels = [1 => 'Developer', 2 => 'Admin', 3 => 'Staff'];
$statuses = [1 => 'Active', 0 => 'Inactive'];

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
    ob_start();
    if (!empty($users)) {
        foreach ($users as $index => $user) {
            echo '<tr>';
            echo '<td class="text-center">' . ($offset + $index + 1) . '</td>';
            echo '<td>' . htmlspecialchars($user['name']) . '</td>';
            echo '<td>' . htmlspecialchars($user['username']) . '</td>';
            echo '<td>' . $user_levels[$user['user_level']] . '</td>';
            echo '<td>' . $statuses[$user['status']] . '</td>';
            echo '<td class="text-center">' . ($user['last_login'] ? date('F j, Y, g:i:s a', strtotime($user['last_login'])) : 'Not logged in') . '</td>';
            echo '<td class="text-center d-flex justify-content-center gap-2">
                    <button type="button" class="editU-btn btn-secondary" data-bs-toggle="modal" data-bs-target="#editUserModal_' . $user['User_id'] . '"><i class="ri-pencil-line"></i></button>
                    <button type="button" class="deleteU-btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteUserModal_' . $user['User_id'] . '"><i class="ri-delete-bin-line"></i></button>
                </td>';
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="7">No users found.</td></tr>';
    }
    $tableContent = ob_get_clean();
    echo $tableContent;
    exit;
}

$message = isset($_GET['message']) ? $_GET['message'] : '';
$message_type = isset($_GET['message_type']) ? $_GET['message_type'] : '';
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<link rel="stylesheet" href="../lib/usermanagement/userstyle.css">

<main class="main container" id="main">
    <?php include('../layouts/sidebar.php'); ?>
    <div class="table-wrapper">

        <div class="table-title">
            <div class="d-flex justify-content-between">
                <div class="col-sm-4">
                    <h2>User Management</h2>
                </div>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
                    <i class="fa fa-plus-circle"></i> Add User
                </button>
            </div>
        </div>

        <!-- Filter and Search -->
        <div class="table-filter">
            <form method="GET" action="">
                <div class="row">
                    <div class="col-sm-3">
                        <div class="show-entries">
                            <span>Show</span>
                            <select class="form-control" name="items_per_page" onchange="this.form.submit()">
                                <?php
                                $entriesOptions = [50, 100, 150, 200];
                                $maxItemsPerPage = min($totalItems, max($entriesOptions));
                                foreach ($entriesOptions as $value): ?>
                                    <option value="<?= $value ?>" <?= $itemsPerPage == $value ? 'selected' : '' ?>>
                                        <?= $value ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <span>Entries</span>
                        </div>
                    </div>
                    <div class="col-sm-9">
                        <div class="filter-group d-flex justify-content-end align-items-center">
                            <label>Name</label>
                            <input type="text" class="form-control" name="search"
                                value="<?= htmlspecialchars($searchKeyword) ?>" placeholder="Search">
                            <button type="submit" class="btn btn-primary ms-2">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="table-responsive mb-4" style="height: 61vh; overflow-y: auto;">
            <table class="table table-striped table-hover" style="text-align: center; vertical-align: middle;">
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
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $index => $user): ?>
                            <tr>
                                <td class="text-center"><?= $offset + $index + 1 ?></td>
                                <td><?= htmlspecialchars($user['name']) ?></td>
                                <td><?= htmlspecialchars($user['username']) ?></td>
                                <td><?= $user_levels[$user['user_level']] ?></td>
                                <td><?= $statuses[$user['status']] ?></td>
                                <td class="text-center">
                                    <?= $user['last_login']
                                        ? date('F j, Y, g:i:s a', strtotime($user['last_login']))
                                        : 'Not logged in' ?>
                                </td>
                                <td class="text-center d-flex justify-content-center gap-2">
                                    <button type="button" class="editU-btn btn-secondary" data-bs-toggle="modal"
                                        data-bs-target="#editUserModal_<?= $user['User_id'] ?>">
                                        <i class="ri-pencil-line"></i>
                                    </button>
                                    <button type="button" class="deleteU-btn btn-danger" data-bs-toggle="modal"
                                        data-bs-target="#deleteUserModal_<?= $user['User_id'] ?>">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7">No users found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="clearfix">
            <div class="hint-text">Showing <b><?= count($users) ?></b> out of <b><?= $totalItems ?></b> entries</div>
            <ul class="pagination">
                <li class="page-item <?= $currentPageNumber <= 1 ? 'disabled' : '' ?>">
                    <a href="?page=<?= max(1, $currentPageNumber - 1) ?>&items_per_page=<?= $itemsPerPage ?>&search=<?= htmlspecialchars($searchKeyword) ?>"
                        class="page-link">Previous</a>
                </li>
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $currentPageNumber == $i ? 'active' : '' ?>">
                        <a href="?page=<?= $i ?>&items_per_page=<?= $itemsPerPage ?>&search=<?= htmlspecialchars($searchKeyword) ?>"
                            class="page-link"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= $currentPageNumber >= $totalPages ? 'disabled' : '' ?>">
                    <a href="?page=<?= min($totalPages, $currentPageNumber + 1) ?>&items_per_page=<?= $itemsPerPage ?>&search=<?= htmlspecialchars($searchKeyword) ?>"
                        class="page-link">Next</a>
                </li>
            </ul>
        </div>
    </div>
</main>


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
                    <button type="submit" name="createUser" id="createUser" class="btn btn-primary">Create</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </form>

        </div>
    </div>
</div>


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
    <!-- Edit User Modal -->
    <div class="modal fade" tabindex="-1" role="dialog" id="editUserModal_<?= $row['User_id'] ?>">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit User -
                        <?= htmlspecialchars($row['name']) ?>
                    </h5>
                </div>

                <form action="../includes/users_actions.php?userId=<?= $row['User_id'] ?>" method="post">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="" class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" id="name"
                                value="<?= htmlspecialchars($row['name']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="" class="form-label">Username</label>
                            <input type="text" class="form-control" name="username" id="username"
                                value="<?= htmlspecialchars($row['username']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="" class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" id="password">
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
                        <button type="submit" name="updateUser" id="updateUser" class="btn btn-primary">Save
                            Changes</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- Delete User Modal -->
    <div class="modal fade" tabindex="-1" role="dialog" id="deleteUserModal_<?= $row['User_id'] ?>">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete User -
                        <?= htmlspecialchars($row['name']) ?>
                    </h5>
                </div>

                <form action="../includes/users_actions.php?userId=<?= $row['User_id'] ?>" method="post">
                    <div class="modal-body">
                        <p>Are you sure you want to delete
                            <?= htmlspecialchars($row['name']) ?>?
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="deleteUser" id="deleteUser" class="btn btn-danger">Delete</button>

                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php } ?>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const searchInput = document.querySelector('input[name="search"]');
        const tableBody = document.querySelector('table tbody');
        const itemsPerPage = document.querySelector('select[name="items_per_page"]');

        // Event listener for real-time search
        searchInput.addEventListener('input', function () {
            const searchValue = searchInput.value.trim();
            const itemsPerPageValue = itemsPerPage.value;

            // Make an AJAX request to get filtered data
            fetch(`?search=${searchValue}&items_per_page=${itemsPerPageValue}`)
                .then(response => response.text())
                .then(data => {
                    // Update the table with the new data
                    const startIndex = data.indexOf('<tbody>');
                    const endIndex = data.indexOf('</tbody>');
                    if (startIndex !== -1 && endIndex !== -1) {
                        tableBody.innerHTML = data.substring(startIndex + 7, endIndex);
                    }
                })
                .catch(error => console.error('Error:', error));
        });
    });
</script>

<!-- toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<?php include '../toast/toastr.php'; ?>

<script src="../lib/usermanagement/userscript.js"></script>
</body>

</html>