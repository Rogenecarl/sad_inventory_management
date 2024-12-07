<?php
include('../layouts/header.php');
require_once '../includes/load.php';

require_login();
$currentPage = basename($_SERVER['PHP_SELF'], '.php');

// Handle search input from the query parameter
$searchTerm = isset($_GET['ProductSearch']) ? trim($_GET['ProductSearch']) : '';

// Handle quantity updates from query parameters
$quantities = isset($_GET['quantity']) ? $_GET['quantity'] : [];

global $conn;

if (isset($_GET['addSale'])) {
    $productId = key($_GET['addSale']); 
    $quantity = intval($_GET['quantity'][$productId]); 
    $saleDate = $_GET['sale_date'][$productId];

    $stmt = $conn->prepare("SELECT quantity, sale_price FROM products WHERE prod_id = ?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch();

    if ($product) {
        $currentQuantity = intval($product['quantity']);
        $salePrice = $product['sale_price'];

        if ($quantity > $currentQuantity) {
            echo "<script>alert('Insufficient stock for this product.');</script>";
        } else {
            $totalPrice = $salePrice * $quantity;

            // Always insert a new sale entry, even for existing products
            $insertSaleStmt = $conn->prepare(
                "INSERT INTO sales (product_id, qty, total_price, date, sales_month) VALUES (?, ?, ?, ?, ?)"
            );

            // Extract the month from the sale date
            $salesMonth = date('m', strtotime($saleDate));  // Get the month as '01', '02', '03', etc.
            $insertSaleStmt->execute([$productId, $quantity, $totalPrice, $saleDate, (int)$salesMonth]);

            echo "<script>alert('Sale added successfully.');</script>";

            $newProductQuantity = $currentQuantity - $quantity;
            $updateProductStmt = $conn->prepare("UPDATE products SET quantity = ? WHERE prod_id = ?");
            $updateProductStmt->execute([$newProductQuantity, $productId]);

            echo "<script>window.location.href = '" . $_SERVER['PHP_SELF'] . "';</script>";
        }
    } else {
        echo "<script>alert('Product not found.');</script>";
    }
}

?>

<link rel="stylesheet" href="../lib/addsales/addsales.css">

<main class="main container" id="main">
    <?php include('../layouts/sidebar.php'); ?>
    <h1 class="dash-fix">Add Sales</h1>
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <form action="" method="get" id="searchForm">
                        <div class="input-group">
                            <button type="submit" class="btn btn-primary">Search</button>
                            <input type="text" class="form-control" name="ProductSearch" id="ProductSearch"
                                placeholder="Search by product name, brand, or model..."
                                value="<?= htmlspecialchars($searchTerm) ?>" required>
                        </div>
                    </form>
                </div>
                <div class="card-body">
                    <div class="table-responsive" style="height: 61vh; overflow-y: auto;">
                        <form action="" method="get" id="salesForm">
                            <input type="hidden" name="ProductSearch" value="<?= htmlspecialchars($searchTerm) ?>">
                            <table class="table table-striped table-bordered"
                                style="text-align: center; vertical-align: middle;">
                                <thead>
                                    <tr>
                                        <th>Product Name</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Total</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="productTableBody">
                                    <?php
                                    if (!empty($searchTerm)) {
                                        $stmt = $conn->prepare(
                                            "SELECT prod_id, name, sale_price, prod_brand, prod_model 
                                             FROM products 
                                             WHERE name LIKE ? OR prod_brand LIKE ? OR prod_model LIKE ?"
                                        );
                                        $searchLike = "%" . $searchTerm . "%";
                                        $stmt->execute([$searchLike, $searchLike, $searchLike]);
                                    } else {
                                        $stmt = $conn->query("SELECT prod_id, name, sale_price FROM products");
                                    }

                                    while ($row = $stmt->fetch()) {
                                        $currentDate = date('Y-m-d');
                                        $prod_id = $row['prod_id'];
                                        $price = $row['sale_price'];
                                        $quantity = isset($quantities[$prod_id]) ? $quantities[$prod_id] : 1;
                                        $totalPrice = number_format($price * $quantity, 0);
                                    ?>
                                    <tr>
                                        <td class="text-center"><?= htmlspecialchars($row['name']) ?></td>
                                        <td class="text-center">
                                            <input type="text" class="form-control price-input" readonly
                                                value="₱ <?= number_format($price, 0, '.', ',') ?>" name="price[<?= $prod_id ?>]">
                                        </td>
                                        <td class="text-center">
                                            <input type="number" class="form-control quantity-input" min="1" value="<?= $quantity ?>"
                                                name="quantity[<?= $prod_id ?>]" required data-prod-id="<?= $prod_id ?>">
                                        </td>
                                        <td class="text-center">
                                            <input type="text" class="form-control total-price"
                                                value="₱ <?= $totalPrice ?>" readonly data-prod-id="<?= $prod_id ?>">
                                        </td>
                                        <td class="text-center">
                                            <input type="date" class="form-control" value="<?= $currentDate ?>"
                                                name="sale_date[<?= $prod_id ?>]" required>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="sale-button btn addSaleButton"
                                                data-prod-id="<?= $prod_id ?>" style="white-space: nowrap;">
                                                <i class="fa fa-plus-circle"></i> Add Sale
                                            </button>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </form>
                    </div>
                    <!-- Pagination footer -->
                    <div class="pagination-container d-flex gap-2">
                        <span class="total-users me-auto p-2">Showing results</span>
                        <button class="prev-page btn btn-secondary" disabled>Previous</button>
                        <button class="next-page btn btn-secondary" disabled>Next</button>
                    </div>
                </div>
            </div>
        </div>

</main>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Prevent form submission on Enter key press
        const quantityInputs = document.querySelectorAll(".quantity-input");
        quantityInputs.forEach(input => {
            input.addEventListener("keydown", function (event) {
                if (event.key === "Enter") {
                    event.preventDefault();
                }
            });

            // Update total price in real-time when quantity changes
            input.addEventListener("input", function () {
                const prodId = this.getAttribute("data-prod-id");
                const quantity = parseInt(this.value) || 1;
                const price = parseFloat(document.querySelector(`input[name="price[${prodId}]"]`).value.replace("₱", "").replace(",", ""));
                const totalPrice = price * quantity;

                // Update the total price for this product
                const totalPriceInput = document.querySelector(`input[data-prod-id="${prodId}"].total-price`);
                totalPriceInput.value = "₱ " + totalPrice.toLocaleString();
            });
        });

        // Add sale functionality
        const addSaleButtons = document.querySelectorAll(".addSaleButton");
        addSaleButtons.forEach(button => {
            button.addEventListener("click", function () {
                const prodId = this.getAttribute("data-prod-id");
                const quantityInput = document.querySelector(`input[name="quantity[${prodId}]"]`);
                const quantity = quantityInput.value;
                const saleDate = document.querySelector(`input[name="sale_date[${prodId}]"]`).value;

                const form = document.getElementById('salesForm');

                // Add the product data to the form before submitting
                const saleInput = document.createElement('input');
                saleInput.type = 'hidden';
                saleInput.name = `addSale[${prodId}]`;
                saleInput.value = '1'; // Mark this product to be added as sale
                form.appendChild(saleInput);

                // Add quantity and sale date to the form
                const quantityInputHidden = document.createElement('input');
                quantityInputHidden.type = 'hidden';
                quantityInputHidden.name = `quantity[${prodId}]`;
                quantityInputHidden.value = quantity;
                form.appendChild(quantityInputHidden);

                const saleDateInput = document.createElement('input');
                saleDateInput.type = 'hidden';
                saleDateInput.name = `sale_date[${prodId}]`;
                saleDateInput.value = saleDate;
                form.appendChild(saleDateInput);

                // Submit the form
                form.submit();
            });
        });
    });
</script>

<script src="../lib/addsales/addsales.js"></script>
</body>
</html>
