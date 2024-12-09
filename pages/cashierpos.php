<?php
require_once '../includes/load.php';
require_login();

$currentPage = basename($_SERVER['PHP_SELF'], '.php');

// Fetch categories from the database
$stmt = $conn->prepare("SELECT category_id, name, created_at FROM categories");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Point of Sales</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.2.0/remixicon.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="../lib/addsales/pos.css">
</head>

<body>
    <nav class="text-center d-flex justify-content-center align-items-center">Point Of Sales</nav>
    <main class="p-2">
        <div class="horizontal-scrollbar-wrapper">
            <div class="horizontal-scrollbar d-flex flex-row flex-nowrap" style="width: 62rem">
                <?php if (empty($categories)): ?>
                    <p>No categories found.</p>
                <?php else: ?>
                    <?php foreach ($categories as $category): ?>
                        <div class="card card-body p-2 m-2 card-highlight d-flex justify-content-center" style="width: 18rem;"
                            onclick="loadCategoryProducts(<?= $category['category_id'] ?>)">
                            <h5 class="card-title"><?= htmlspecialchars($category['name']) ?></h5>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        <div class="table p-2" style="height: 70vh; overflow-y: auto;">
            <table id="product-table" class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Photo</th>
                        <th>Product Name</th>
                        <th>Brand</th>
                        <th>Model</th>
                        <th>Stocks</th>
                        <th>Price</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </main>
    <aside class="d-flex flex-column">
        <div class="header p-2 m-3 d-flex justify-content-center"
            style="height: 3rem; background-color: #e9e9e9; border-radius: 10px">
            <h3>Products Cart</h3>
        </div>
        <div class="OrderNum m-2">
            <h5>Purchase ID No. <input type="number"></input></h5>
        </div>
        <div class="table p-2" style="height: 58vh; overflow-y: auto;">
            <table id="sales-table" class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Product Name</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="sales-table-body"></tbody>
            </table>
        </div>
        <div class="footer m">
            <div class="text-end me-md-5 align-self-end">
                <h3 id="grand-total">Grand Total: ₱0.00</h3>
                <button class="btn btn-primary" id="confirm-purchase-btn" data-bs-toggle="modal"
                    data-bs-target="#confirmPurchaseModal">Confirm Purchase</button>
            </div>
        </div>
    </aside>

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
                    <button type="button" class="btn btn-primary" id="confirm-purchase-action"
                        data-bs-dismiss="modal">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const salesData = [];

        // Load products for a category
        function loadCategoryProducts(categoryId) {
            fetch(`../includes/get_products.php?category_id=${categoryId}`)
                .then(res => res.json())
                .then(data => {
                    const tableBody = document.querySelector("#product-table tbody");
                    tableBody.innerHTML = "";
                    data.forEach((product, index) => {
                        tableBody.innerHTML += `
                        <tr data-id="${product.prod_id}">
                            <td>${index + 1}</td>
                            <td><img src="../uploads/products/${product.photo}" width="50"></td>
                            <td>${product.name}</td>
                            <td>${product.prod_brand}</td>
                            <td>${product.prod_model}</td>
                            <td>${product.quantity}</td>
                            <td>${product.sale_price}</td>
                            <td>
                                <button class="btn btn-primary add-sales-btn" data-id="${product.prod_id}" data-name="${product.name}" data-price="${product.sale_price}">Add Sales</button>
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
                <td><input type="number" class="quantity-input" value="${p.quantity}" data-id="${p.id}" /></td>
                <td>${p.price}</td>
                <td>${totalCost}</td>
                <td><button class="btn btn-danger remove-btn" data-id="${p.id}">Remove</button></td>
            </tr>`;
            });
            document.getElementById("grand-total").textContent = `Grand Total: ₱${total}`;
        }

        document.querySelector("#sales-table-body").addEventListener("input", (event) => {
            if (event.target.classList.contains("quantity-input")) {
                const productId = event.target.getAttribute("data-id");
                const newQuantity = parseInt(event.target.value);
                const product = salesData.find(p => p.id == productId);
                if (product) product.quantity = newQuantity;
                renderSalesTable();
            }
        });

        document.getElementById("confirm-purchase-action").addEventListener("click", () => {
            fetch("../includes/addsales_functions.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ sales: salesData })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        toastr.success("Purchase confirmed!");
                        salesData.length = 0;
                        renderSalesTable();
                    } else {
                        toastr.error(data.message);
                    }
                });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>


    <?php include '../toast/toastr.php'; ?>

    <script src="../lib/addsales/addsales.js"></script>
</body>

</html>

<?php include '../toast/toastr.php'; ?>

<script src="../lib/addsales/addsales.js"></script>
</body>

</html>