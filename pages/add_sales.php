<?php
include('../layouts/header.php');
require_once '../includes/load.php';

require_login();
$currentPage = basename($_SERVER['PHP_SELF'], '.php');

// Fetch categories from the database
$stmt = $conn->prepare("SELECT category_id, name, created_at FROM categories");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!-- Toastr CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" rel="stylesheet">
<!-- Toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

<link rel="stylesheet" href="../lib/addsales/addsales.css">

<main class="main container" id="main">
    <?php include('../layouts/sidebar.php'); ?>
    <h1 class="dash-fix">Add Sales</h1>
    <div class="main__container">
        <div class="d-flex flex-column">
            <div class="input-group">
                <div class="form-outline" data-mdb-input-init>
                    <input type="search" id="form1" placeholder="Search" class="form-control" />
                </div>
                <button type="button" class="btn btn-primary" data-mdb-ripple-init>
                    <i class="fas fa-search"></i>
                </button>
            </div>
            <div class="container-fluid py-2">
                <div class="horizontal-scrollbar d-flex flex-row flex-nowrap">
                    <?php if (empty($categories)): ?>
                        <p>No categories found.</p>
                    <?php else: ?>
                        <?php foreach ($categories as $category): ?>
                            <div class="card card-body m-2 card-highlight" style="width: 18rem;"
                                onclick="loadCategoryProducts(<?= $category['category_id'] ?>)">
                                <h5 class="card-title"><?= htmlspecialchars($category['name']) ?></h5>
                                <p class="card-text"><?= date('F j, Y', strtotime($category['created_at'])) ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            <div class="d-flex flex-row justify-content-between gap-3">
                <div class="card p-2 flex-grow-1">
                    <div class="categoryproduct-table card-body">
                        <h1>Products Table</h1>
                        <table id="product-table" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Photo</th>
                                    <th>Product Name</th>
                                    <th>Brand</th>
                                    <th>Model</th>
                                    <th>Stocks</th>
                                    <th>Price</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Products will be dynamically populated here -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card p-2">
                    <div class="table card-body">
                        <h1>Sales Table</h1>
                        <table class="table sales-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Product Name</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Total</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="sales-table-body"></tbody>
                        </table>
                        <div class="text-end">
                            <h3 id="grand-total">Grand Total: ₱0.00</h3>
                            <button class="btn btn-primary" id="confirm-purchase-btn" data-bs-toggle="modal"
                                data-bs-target="#confirmPurchaseModal">Confirm Purchase</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Confirm Purchase Modal -->
<div class="modal fade" id="confirmPurchaseModal" tabindex="-1" aria-labelledby="confirmPurchaseModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmPurchaseModalLabel">Confirm Purchase</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to confirm this purchase? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="confirm-purchase-action">Confirm</button>
            </div>
        </div>
    </div>
</div>

<script src="../lib/addsales/addsales.js"></script>
<script>
    const salesData = [];

    function loadCategoryProducts(categoryId) {
        fetch(`../includes/get_products.php?category_id=${categoryId}`)
            .then(res => res.json())
            .then(data => {
                const tableBody = document.querySelector("#product-table tbody");
                tableBody.innerHTML = "";
                data.forEach((product, index) => {
                    tableBody.innerHTML += `
                        <tr>
                            <td>${index + 1}</td>
                            <td><img src="../uploads/products/${product.photo}" width="50"></td>
                            <td>${product.name}</td>
                            <td>${product.prod_brand}</td>
                            <td>${product.prod_model}</td>
                            <td>${product.quantity}</td>
                            <td>${product.sale_price}</td>
                            <td>
                                <button class="btn btn-primary add-sales-btn" data-id="${product.prod_id}" data-name="${product.name}" data-price="${product.sale_price}">Add</button>
                            </td>
                        </tr>`;
                });
            });
    }

    document.addEventListener('click', (e) => {
        if (e.target.classList.contains('add-sales-btn')) {
            const productId = e.target.dataset.id;
            const productName = e.target.dataset.name;
            const productPrice = parseFloat(e.target.dataset.price);
            const productStock = parseInt(e.target.closest('tr').querySelector('td:nth-child(6)').textContent);  // Assuming the stock is in the 6th column (adjust if necessary)

            // Check if the stock is zero
            if (productStock === 0) {
                toastr.error(`${productName} with zero stock cannot be added.`);
                return;
            }

            const existing = salesData.find(p => p.id === productId);
            if (existing) existing.quantity++;
            else salesData.push({ id: productId, name: productName, price: productPrice, quantity: 1 });

            renderSalesTable();
        }
    });

    // Event listener for quantity changes
    document.querySelector("#sales-table-body").addEventListener("input", (event) => {
        if (event.target.classList.contains("quantity-input")) {
            const productId = event.target.getAttribute("data-id");
            const newQuantity = parseInt(event.target.value);

            // Find the corresponding product in salesData
            const product = salesData.find(p => p.id == productId);

            if (product) {
                const productStock = parseInt(event.target.closest('tr').querySelector('td:nth-child(6)').textContent);  // Get the stock of the product

                // Prevent the quantity from exceeding the available stock
                if (newQuantity > productStock) {
                    toastr.warning(`There are only ${productStock} stock(s) available for ${product.name}.`);
                    event.target.value = productStock; // Reset the quantity field to the available stock
                    product.quantity = productStock; // Update salesData quantity
                } else {
                    // Update the salesData with the new quantity
                    product.quantity = newQuantity;
                }

                // Re-render the sales table
                renderSalesTable();
            }
        }
    });



    function renderSalesTable() {
        const tableBody = document.querySelector("#sales-table-body");
        tableBody.innerHTML = "";
        let total = 0;
        salesData.forEach((p, i) => {
            const totalCost = p.price * p.quantity;
            total += totalCost;
            tableBody.innerHTML += `
            <tr>
                <td>${i + 1}</td>
                <td>${p.name}</td>
                <td>
                    <input type="number" class="quantity-input" value="${p.quantity}" data-id="${p.id}" min="1" />
                </td>
                <td>${p.price}</td>
                <td>${totalCost}</td>
                <td>
                    <button class="btn btn-danger remove-btn" data-id="${p.id}">Remove</button>
                </td>
            </tr>`;
        });
        document.getElementById("grand-total").textContent = `Grand Total: ₱${total}`;
    }

    // Event listener for quantity changes
    document.querySelector("#sales-table-body").addEventListener("input", (event) => {
        if (event.target.classList.contains("quantity-input")) {
            const productId = event.target.getAttribute("data-id");
            const newQuantity = parseInt(event.target.value);

            // Update salesData with the new quantity
            const product = salesData.find(p => p.id == productId);
            if (product) {
                product.quantity = newQuantity;
            }

            // Re-render the sales table with the updated quantities
            renderSalesTable();
        }
    });

    // Confirm purchase action
    document.getElementById("confirm-purchase-action").addEventListener("click", () => {
        fetch("../includes/addsales_functions.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ sales: salesData })
        })
            .then(() => {
                alert("Purchase confirmed!");
                // Optionally, reset sales data and table
                salesData.length = 0;
                renderSalesTable();
            });
    });

</script>




<script src="../lib/addsales/addsales.js"></script>
</body>

</html>