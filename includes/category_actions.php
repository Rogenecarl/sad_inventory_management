<?php
require_once 'load.php';

//add category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['createCat'])) {
    $name = $_POST['name'];

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

//update category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateCat'])) {
    $catId = $_GET['catId'];
    $name = $_POST['name'];

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