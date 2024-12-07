<?php
require_once 'load.php';

//this is the toastr
// Add user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['createUser'])) {
    $name = $_POST['name'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    $status = $_POST['status'];

    $hashed_password = sha1($password);

    $stmt = $conn->prepare("SELECT * FROM users WHERE name = :name AND username = :username");
    $stmt->execute([
        ':name' => $name,
        ':username' => $username
    ]);

    if ($stmt->rowCount() > 0) {
        header("Location: ../pages/user_management.php?message=Error: Name and Username combination already exists&message_type=error");
        exit();
    }

    try {
        $stmt = $conn->prepare("INSERT INTO users (name, username, password, user_level, status) 
                                VALUES (:name, :username, :password, :role, :status)");
        $stmt->execute([
            ':name' => $name,
            ':username' => $username,
            ':password' => $hashed_password,
            ':role' => $role,
            ':status' => $status,
        ]);

        header("Location: ../pages/user_management.php?message=User created successfully&message_type=success");
        exit();
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}

// Update user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateUser'])) {
    $userId = $_GET['userId'];
    $name = $_POST['name'];
    $username = $_POST['username'];
    $role = $_POST['role'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE name = :name AND username = :username AND User_id != :userId");
    $stmt->execute([
        ':name' => $name,
        ':username' => $username,
        ':userId' => $userId
    ]);

    if ($stmt->rowCount() > 0) {
        header("Location: ../pages/user_management.php?message=Error: Name and Username combination already exists&message_type=error");
        exit();
    }

    if (!empty($_POST['password'])) {
        $password = $_POST['password'];
        $hashed_password = sha1($password);
        $stmt = $conn->prepare("UPDATE users SET name = :name, username = :username, password = :password, user_level = :role, status = :status WHERE User_id = :userId");
        $stmt->execute([
            ':name' => $name,
            ':username' => $username,
            ':password' => $hashed_password,
            ':role' => $role,
            ':status' => $status,
            ':userId' => $userId
        ]);
    } else {
        $stmt = $conn->prepare("UPDATE users SET name = :name, username = :username, user_level = :role, status = :status WHERE User_id = :userId");
        $stmt->execute([
            ':name' => $name,
            ':username' => $username,
            ':role' => $role,
            ':status' => $status,
            ':userId' => $userId
        ]);
    }

    header("Location: ../pages/user_management.php?message=User updated successfully&message_type=success");
    exit();
}

// Delete user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['userId'])) {
    $userId = $_GET['userId'];

    try {
        $stmt = $conn->prepare("DELETE FROM users WHERE User_id = :userId");
        $stmt->execute([':userId' => $userId]);

        header("Location: ../pages/user_management.php?message=User deleted successfully&message_type=success");
        exit();
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>