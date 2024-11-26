<?php
require_once 'load.php';

//add users
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['createUser'])) {
    $name = $_POST['name'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    $status = $_POST['status'];

    $hashed_password = sha1($password);

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

        header("Location: ../pages/user_management.php?message=User created successfully");
        exit();
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}

//update users
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateUser'])) {
    $userId = $_GET['userId'];
    $name = $_POST['name'];
    $username = $_POST['username'];
    $role = $_POST['role'];
    $status = $_POST['status'];

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

    header("Location: ../pages/user_management.php?message=User updated successfully");
    exit();
}

//delete users
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['userId'])) {
    $userId = $_GET['userId'];

    try {
        $stmt = $conn->prepare("DELETE FROM users WHERE User_id = :userId");
        $stmt->execute([':userId' => $userId]);

        header("Location: ../pages/user_management.php?message=User deleted successfully");
        exit();
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>