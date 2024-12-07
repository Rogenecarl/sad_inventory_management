<?php
require_once 'load.php';

// Add category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['createCat'])) {
    $name = $_POST['name'];
    $description = !empty($_POST['description']) ? $_POST['description'] : null;  // Set description to NULL if empty

    // Check if category name already exists
    $stmt = $conn->prepare("SELECT * FROM categories WHERE name = :name");
    $stmt->execute([
        ':name' => $name
    ]);

    if ($stmt->rowCount() > 0) {
        header("Location: ../pages/category.php?message=Error: Category name already exists&message_type=error");
        exit();
    }

    try {
        // Insert new category with optional description
        $stmt = $conn->prepare("INSERT INTO categories (name, description, created_at) VALUES (:name, :description, NOW())");
        $stmt->execute([
            ':name' => $name,
            ':description' => $description  // Will be NULL if not provided
        ]);

        header("Location: ../pages/category.php?message=Category created successfully&message_type=success");
        exit();
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}


// Update category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateCat'])) {
    $catId = $_GET['catId'];
    $name = $_POST['name'];
    $description = !empty($_POST['description']) ? $_POST['description'] : null;  // Set description to NULL if empty

    // Check if category name already exists (excluding current category)
    $stmt = $conn->prepare("SELECT * FROM categories WHERE name = :name AND category_id != :catId");
    $stmt->execute([
        ':name' => $name,
        ':catId' => $catId
    ]);

    if ($stmt->rowCount() > 0) {
        header("Location: ../pages/category.php?message=Error: Category name already exists&message_type=error");
        exit();
    }

    try {
        // Update category with new name and optional description
        $stmt = $conn->prepare("UPDATE categories SET name = :name, description = :description WHERE category_id = :catId");
        $stmt->execute([
            ':name' => $name,
            ':description' => $description,  // Will be NULL if not provided
            ':catId' => $catId
        ]);

        header("Location: ../pages/category.php?message=Category updated successfully&message_type=success");
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

        header("Location: ../pages/category.php?message=User deleted successfully&message_type=success");
        exit();

    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>