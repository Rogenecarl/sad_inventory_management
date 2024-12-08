<?php
require_once 'load.php';
$uploadDir = '../uploads/products/';

// Add Product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addproduct'])) {
    $prodname = $_POST['prodname'];
    $category = $_POST['category'];
    $brand = $_POST['brand'];
    $prodM = $_POST['prodM'];
    $prodQ = $_POST['prodQ'];
    $prodPrice = $_POST['prodPrice'];
    $photoName = NULL;

    // Check if a photo was uploaded
    if (isset($_FILES['productPhoto']) && $_FILES['productPhoto']['error'] === UPLOAD_ERR_OK) {
        $photo = $_FILES['productPhoto'];
        $photoName = time() . '_' . basename($photo['name']);
        $photoPath = $uploadDir . $photoName;

        if (!move_uploaded_file($photo['tmp_name'], $photoPath)) {
            die("Error: Unable to upload photo.");
        }
    }

    try {
        $stmt = $conn->prepare("INSERT INTO products (name, categorie_id, prod_brand, prod_model, quantity, sale_price, photo, created_at) 
                                VALUES (:name, :categorie_id, :prod_brand, :prod_model, :quantity, :sale_price, :photo, NOW())");

        $stmt->execute([
            ':name' => $prodname,
            ':categorie_id' => $category,
            ':prod_brand' => $brand,
            ':prod_model' => $prodM,
            ':quantity' => $prodQ,
            ':sale_price' => $prodPrice,
            ':photo' => $photoName, 
        ]);

        header("Location: ../pages/products.php?message=Product created successfully&message_type=success");
        exit();
    } catch (PDOException $e) {
        // If an error occurs, and a photo was uploaded, remove the photo file
        if ($photoName) {
            unlink($photoPath);
        }
        die("Error: " . $e->getMessage());
    }
}

// Update product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateproduct']) && isset($_GET['proId'])) {
    $proId = $_GET['proId'];
    $prodname = $_POST['prodname'] ?? null;
    $prodbrand = $_POST['prodbrand'] ?? null;
    $prodmodel = $_POST['prodmodel'] ?? null; 
    $prodquan = isset($_POST['prodquan']) && is_numeric($_POST['prodquan']) ? (int) $_POST['prodquan'] : 0; // Quantity
    $prodprice = $_POST['prodprice'] ?? null;
    $remarks = $_POST['remarks'] ?? null;
    $photoName = null;

    try {
        // Fetch the current product details
        $stmt = $conn->prepare("SELECT prod_id, quantity, sale_price, photo FROM products WHERE prod_id = :proId");
        $stmt->execute([':proId' => $proId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            die("Product not found.");
        }

        $previousStock = is_numeric($product['quantity']) ? (int) $product['quantity'] : 0;
        $previousPrice = $product['sale_price'];
        $previousPhoto = $product['photo'];
        $newStock = $previousStock + $prodquan;

        // Handle photo upload
        if (isset($_FILES['productPhoto']) && $_FILES['productPhoto']['error'] === UPLOAD_ERR_OK) {
            $photo = $_FILES['productPhoto'];
            $photoName = time() . '_' . basename($photo['name']);
            $photoPath = $uploadDir . $photoName;

            if (!move_uploaded_file($photo['tmp_name'], $photoPath)) {
                die("Error: Unable to upload photo.");
            }

            if (!empty($previousPhoto) && file_exists($uploadDir . $previousPhoto)) {
                unlink($uploadDir . $previousPhoto);
            }
        } else {
            $photoName = $previousPhoto;
        }

        // Update StockHistory if price changes or stock is not zero
        if (($prodprice !== null && $prodprice != $previousPrice) || $prodquan !== 0) {
            $stmtHistory = $conn->prepare("
                INSERT INTO StockHistory (prod_id, quantity_added, previous_stock, new_stock, price, created_at, remarks)
                VALUES (:prod_id, :quantity_added, :previous_stock, :new_stock, :price, NOW(), :remarks)
            ");
            $stmtHistory->execute([
                ':prod_id' => $proId,
                ':quantity_added' => $prodquan,
                ':previous_stock' => $previousStock,
                ':new_stock' => $newStock,
                ':price' => $prodprice ?? $previousPrice,
                ':remarks' => $remarks,
            ]);
        }

        // Update product details
        $updateFields = [
            'photo = :photo',
            'updated_at = NOW()',
            'name = :prodname',
            'prod_brand = :prodbrand',
            'prod_model = :prodmodel'
        ];
        $params = [
            ':photo' => $photoName,
            ':prodname' => $prodname,
            ':prodbrand' => $prodbrand,
            ':prodmodel' => $prodmodel,
            ':proId' => $proId,
        ];

        if (!is_null($prodquan)) {
            $updateFields[] = 'quantity = :newStock';
            $params[':newStock'] = $newStock;
        }

        if (!is_null($prodprice)) {
            $updateFields[] = 'sale_price = :newPrice';
            $params[':newPrice'] = $prodprice;
        }

        $stmtUpdate = $conn->prepare("
            UPDATE products
            SET " . implode(', ', $updateFields) . "
            WHERE prod_id = :proId
        ");
        $stmtUpdate->execute($params);

        header("Location: ../pages/products.php?message=Product updated successfully&message_type=success");
        exit();
    } catch (PDOException $e) {
        if ($photoName && $photoName !== $previousPhoto) {
            unlink($uploadDir . $photoName);
        }
        die("Error: " . $e->getMessage());
    }
}

// Delete Product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['proId'])) {
    $proId = $_GET['proId'];

    try {
        $stmt = $conn->prepare("SELECT photo FROM products WHERE prod_id = :proId");
        $stmt->execute([':proId' => $proId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product && $product['photo']) {
            $photoPath = $uploadDir . $product['photo'];
            if (file_exists($photoPath)) {
                unlink($photoPath);
            }
        }

        // Delete related stock history entries
        $stmt = $conn->prepare("DELETE FROM StockHistory WHERE prod_id = :proId");
        $stmt->execute([':proId' => $proId]);

        // Delete the product
        $stmt = $conn->prepare("DELETE FROM products WHERE prod_id = :proId");
        $stmt->execute([':proId' => $proId]);

        header("Location: ../pages/products.php?message=Product deleted successfully&message_type=success");
        exit();
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
