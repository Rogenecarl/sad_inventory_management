<?php
require_once 'load.php';

// Add category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['createCat'])) {
    $name = $_POST['name'];

    $stmt = $conn->prepare("SELECT * FROM categories WHERE name = :name");
    $stmt->execute([
        ':name' => $name
    ]);

    if ($stmt->rowCount() > 0) {
        header("Location: ../pages/category.php?message=Error: Category name already exists");
        exit();
    }

    try {
        $stmt = $conn->prepare("INSERT INTO categories (name, created_at) VALUES (:name, NOW())");
        $stmt->execute([
            ':name' => $name,
        ]);

        header("Location: ../pages/category.php?message=Category created successfully");
        exit();
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}


// Update category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateCat'])) {
    $catId = $_GET['catId'];
    $name = $_POST['name'];

    $stmt = $conn->prepare("SELECT * FROM categories WHERE name = :name AND category_id != :catId");
    $stmt->execute([
        ':name' => $name,
        ':catId' => $catId
    ]);

    if ($stmt->rowCount() > 0) {
        header("Location: ../pages/category.php?message=Error: Category name already exists");
        exit();
    }

    try {
        $stmt = $conn->prepare("UPDATE categories SET name = :name WHERE category_id = :catId");
        $stmt->execute([
            ':name' => $name,
            ':catId' => $catId
        ]);

        header("Location: ../pages/category.php?message=Category updated successfully");
        exit();
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}


//delete category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['catId'])) {
    $catId = $_GET['catId'];

    try {
        $stmt = $conn->prepare("DELETE FROM categories WHERE category_id = :catId");
        $stmt->execute([':catId' => $catId]);

        header("Location: ../pages/category.php?message=User deleted successfully");
        exit();

    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>