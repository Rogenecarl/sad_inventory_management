<?php
require_once 'load.php';

// Add Product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addproduct'])) {
    $prodname = $_POST['prodname'];
    $category = $_POST['category'];
    $brand = $_POST['brand'];
    $prodM = $_POST['prodM'];
    $prodQ = $_POST['prodQ'];
    $prodPrice = $_POST['prodPrice'];

    try {
        $stmt = $conn->prepare("INSERT INTO products (name, categorie_id, prod_brand, prod_model, quantity, sale_price, created_at) 
                                VALUES (:name, :categorie_id, :prod_brand, :prod_model, :quantity, :sale_price, NOW())");
        $stmt->execute([
            ':name' => $prodname,
            ':categorie_id' => $category,
            ':prod_brand' => $brand,
            ':prod_model' => $prodM,
            ':quantity' => $prodQ,
            ':sale_price' => $prodPrice,
        ]);

        header("Location: ../pages/products.php?message=Product created successfully");
        exit();
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}

//update product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateproduct'])) {
    $proId = $_GET['proId'];
    $prodname = $_POST['prodname'];
    $prodbrand = $_POST['prodbrand'];
    $prodmodel = $_POST['prodmodel'];
    $prodquan = $_POST['prodquan'];
    $prodprice = $_POST['prodprice'];

    try {
        $stmt = $conn->prepare("UPDATE products 
                                SET name = :name, 
                                    prod_brand = :prod_brand, 
                                    prod_model = :prod_model, 
                                    quantity = :quantity, 
                                    sale_price = :sale_price, 
                                    updated_at = NOW()
                                WHERE prod_id = :prod_id");
        $stmt->execute([
            ':name' => $prodname,
            ':prod_brand' => $prodbrand,
            ':prod_model' => $prodmodel,
            ':quantity' => $prodquan,
            ':sale_price' => $prodprice,
            ':prod_id' => $proId,
        ]);

        header("Location: ../pages/products.php?message=Product updated successfully");
        exit();
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}

//delete product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['proId'])) {
    $proId = $_GET['proId'];

    try {
        $stmt = $conn->prepare("DELETE FROM products WHERE prod_id = :proId");
        $stmt->execute([':proId' => $proId]);

        // Redirect with success message
        header("Location: ../pages/products.php?message=User deleted successfully");
        exit();
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>