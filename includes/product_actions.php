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
    $photoName = NULL;  // Default photo value (NULL)

    // Check if a photo was uploaded
    if (isset($_FILES['productPhoto']) && $_FILES['productPhoto']['error'] === UPLOAD_ERR_OK) {
        $photo = $_FILES['productPhoto'];
        $photoName = time() . '_' . basename($photo['name']);
        $photoPath = $uploadDir . $photoName;

        // Try to move the uploaded file
        if (!move_uploaded_file($photo['tmp_name'], $photoPath)) {
            die("Error: Unable to upload photo.");
        }
    }

    try {
        // Prepare and execute the SQL query to insert the product
        $stmt = $conn->prepare("INSERT INTO products (name, categorie_id, prod_brand, prod_model, quantity, sale_price, photo, created_at) 
                                VALUES (:name, :categorie_id, :prod_brand, :prod_model, :quantity, :sale_price, :photo, NOW())");

        // Execute with the photo (it could be NULL if no photo is uploaded)
        $stmt->execute([
            ':name' => $prodname,
            ':categorie_id' => $category,
            ':prod_brand' => $brand,
            ':prod_model' => $prodM,
            ':quantity' => $prodQ,
            ':sale_price' => $prodPrice,
            ':photo' => $photoName,  // NULL or photo name
        ]);

        // Redirect to the products page with a success message
        header("Location: ../pages/products.php?message=Product created successfully");
        exit();
    } catch (PDOException $e) {
        // If an error occurs, and a photo was uploaded, remove the photo file
        if ($photoName) {
            unlink($photoPath);
        }
        die("Error: " . $e->getMessage());
    }
}


// update Product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateproduct']) && isset($_GET['proId'])) {
    $proId = $_GET['proId'];
    $prodname = $_POST['prodname'];
    $prodbrand = $_POST['prodbrand'];
    $prodmodel = $_POST['prodmodel'];
    $prodquan = $_POST['prodquan'];
    $prodprice = $_POST['prodprice'];

    // Check the file input
    var_dump($_FILES['productPhoto']); // For debugging: check the uploaded file info
    var_dump($_POST); // For debugging: check the POST data

    try {
        // Fetch the current product photo from the database
        $stmt = $conn->prepare("SELECT photo FROM products WHERE prod_id = :proId");
        $stmt->execute([':proId' => $proId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            die("Error: Product not found.");
        }

        // Keep the existing photo if no new one is uploaded
        $photoName = $product['photo'];
        if (isset($_FILES['productPhoto']) && $_FILES['productPhoto']['error'] === UPLOAD_ERR_OK) {
            $photo = $_FILES['productPhoto'];
            $photoName = time() . '_' . basename($photo['name']);
            $photoPath = $uploadDir . $photoName;

            // Move the uploaded file to the correct directory
            if (move_uploaded_file($photo['tmp_name'], $photoPath)) {
                // Delete the old photo if it exists
                $oldPhotoPath = $uploadDir . $product['photo'];
                if ($product['photo'] && file_exists($oldPhotoPath)) {
                    unlink($oldPhotoPath);
                }
            } else {
                die("Error: Unable to upload new photo.");
            }
        }

        // Update the product in the database
        $stmt = $conn->prepare("UPDATE products 
                                SET name = :name, prod_brand = :brand, prod_model = :model, 
                                    quantity = :quantity, sale_price = :price, photo = :photo 
                                WHERE prod_id = :proId");
        $stmt->execute([
            ':name' => $prodname,
            ':brand' => $prodbrand,
            ':model' => $prodmodel,
            ':quantity' => $prodquan,
            ':price' => $prodprice,
            ':photo' => $photoName,
            ':proId' => $proId,
        ]);

        // Redirect to products page with success message
        header("Location: ../pages/products.php?message=Product updated successfully");
        exit();
    } catch (PDOException $e) {
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


        $stmt = $conn->prepare("DELETE FROM products WHERE prod_id = :proId");
        $stmt->execute([':proId' => $proId]);

        header("Location: ../pages/products.php?message=Product deleted successfully");
        exit();
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>