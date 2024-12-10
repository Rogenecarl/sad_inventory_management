<?php
require_once '../includes/load.php';

if (!isset($_SESSION['user_level']) || $_SESSION['user_level'] != 3) {
    header("Location: ../index.php");
    exit();
}

$currentPage = basename($_SERVER['PHP_SELF'], '.php');

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
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600&display=swap" rel="stylesheet">
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
        <div class="filterSearch">
            <input type="text" id="searchInput" placeholder="Search by category name">
        </div>
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
            <div class="filtersearch">
                <input type="text" id="searchInput" placeholder="Search by product name">
            </div>
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
        <div class="TransactionNum m-2">
            <h5>Transaction ID No. <input type="text" id="transaction-id" readonly></h5>
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
            <h3 id="grand-total" class="text-end">Grand Total: ₱0.00</h3>
            <form id="payment-form">
                <div class="mb-3 d-flex">
                    <label for="payment-method" class="form-label">Payment Method:</label>
                    <select id="payment-method" class="form-select">
                        <option value="Cash">Cash</option>
                        <option value="Card">Card</option>
                    </select>
                </div>
                <div class="mb-3 d-flex">
                    <label for="user-money" class="form-label">User Money (₱)</label>
                    <input type="number" id="user-money" class="form-control" step="0.01" min="0">
                </div>
                <div class="mb-3 d-flex">
                    <label for="change" class="form-label">Change (₱)</label>
                    <input type="text" id="change" class="form-control" readonly>
                </div>
            </form>
            <div class="text-end me-md-5 align-self-end">
                <button class="btn btn-primary" id="confirm-purchase-btn" data-bs-toggle="modal"
                    data-bs-target="#PaymentModal">Proceed to payment</button>
            </div>
        </div>
    </aside>

    <!-- Confirm Purchase Modal -->
    <div class="modal fade" id="PaymentModal" tabindex="-1" aria-labelledby="confirmPurchaseModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmPurchaseModalLabel">Confirm Purchase</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="payment-summary">You are about to purchase with <strong
                            id="selected-payment-method"></strong> for a total of <strong
                            id="purchase-total">₱0.00</strong>.</p>
                    <p>Are you sure you want to proceed?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="finalize-purchase-btn">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for printing the receipt -->
    <div class="modal fade" id="printReceiptModal" tabindex="-1" aria-labelledby="printReceiptModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="printReceiptModalLabel">Receipt</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="receiptContent"></div> <!-- This will hold the receipt content dynamically -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="printReceiptBtn">Print Receipt</button>
                </div>
            </div>
        </div>
    </div>


    <script>
        const salesData = [];
        let transactionId = Date.now(); // Generate a unique transaction ID
        document.getElementById("transaction-id").value = transactionId; // Display in readonly input

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
                                <button class="btn btn-primary add-sales-btn" data-id="${product.prod_id}" data-name="${product.name}" data-price="${product.sale_price}" data-stock="${product.quantity}">Add Sales</button>
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
                const productStock = parseInt(e.target.dataset.stock);

                const existing = salesData.find(p => p.id === productId);
                if (existing) {
                    if (existing.quantity + 1 > productStock) {
                        toastr.warning(`${productName} exceeds available stock!`);
                        return;
                    }
                    existing.quantity++;
                } else {
                    if (productStock <= 0) {
                        toastr.warning(`${productName} is out of stock!`);
                        return;
                    }
                    salesData.push({ id: productId, name: productName, price: productPrice, quantity: 1, stock: productStock });
                }

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
                    <td><input type="number" class="quantity-input" value="${p.quantity}" data-id="${p.id}" min="1" max="${p.stock}" /></td>
                    <td>${p.price}</td>
                    <td>${totalCost}</td>
                    <td><button class="btn btn-danger remove-btn" data-id="${p.id}">Remove</button></td>
                </tr>`;
            });
            document.getElementById("grand-total").textContent = `Grand Total: ₱${total.toFixed(2)}`;
        }

        // Handle the input for user money and change calculation
        document.getElementById("user-money").addEventListener("input", () => {
            const userMoney = parseFloat(document.getElementById("user-money").value) || 0;
            const total = parseFloat(document.getElementById("grand-total").textContent.replace("Grand Total: ₱", ""));

            if (userMoney >= total) {
                const change = userMoney - total;
                document.getElementById("change").value = change.toFixed(2); // Display change in the input field
            } else {
                document.getElementById("change").value = '0.00'; // Show 0 if user has insufficient funds
            }
        });

        document.getElementById("confirm-purchase-btn").addEventListener("click", () => {
            let hasStockError = false;

            // Check for stock errors
            salesData.forEach(item => {
                if (item.quantity > item.stock) {
                    toastr.warning(`${item.name} exceeds available stock!`);
                    hasStockError = true;
                }
            });

            if (hasStockError) return; // Prevent proceeding to payment

            const total = parseFloat(document.getElementById("grand-total").textContent.replace("Grand Total: ₱", ""));
            const paymentMethod = document.getElementById("payment-method").value;

            // Update modal content
            document.getElementById("selected-payment-method").textContent = paymentMethod;
            document.getElementById("purchase-total").textContent = `₱${total.toFixed(2)}`;
        });

        document.getElementById("finalize-purchase-btn").addEventListener("click", () => {
            const total = parseFloat(document.getElementById("grand-total").textContent.replace("Grand Total: ₱", ""));
            const paymentMethod = document.getElementById("payment-method").value;
            const userMoney = parseFloat(document.getElementById("user-money").value) || 0;
            const change = parseFloat(document.getElementById("change").value) || 0;

            // Check if cash payment and user money is sufficient
            if (paymentMethod === "Cash" && userMoney < total) {
                toastr.warning("Insufficient funds for cash payment.");
                return;
            }

            fetch("../includes/addsales_functions.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    transaction_id: transactionId,
                    sales: salesData,
                    payment_method: paymentMethod,
                    total_price: total,
                    user_money: userMoney,
                    change: change
                })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.status === "success") {
                        toastr.success("Purchase completed!");
                        salesData.length = 0;
                        renderSalesTable();
                        transactionId = Date.now();
                        document.getElementById("transaction-id").value = transactionId;
                        const modal = bootstrap.Modal.getInstance(document.getElementById("PaymentModal"));
                        modal.hide();

                        // Create the receipt content
                        let receiptContent = `
                    <h3 style="text-align: center;">Receipt</h3>
                    <p><strong>Transaction ID:</strong> ${transactionId}</p>
                    <table style="width: 100%; border-collapse: collapse; margin-top: 10px; margin-bottom: 20px;">
                        <thead>
                            <tr>
                                <th style="text-align: left; padding: 5px; border: 1px solid #000;">Product</th>
                                <th style="text-align: left; padding: 5px; border: 1px solid #000;">Quantity</th>
                                <th style="text-align: left; padding: 5px; border: 1px solid #000;">Price</th>
                                <th style="text-align: left; padding: 5px; border: 1px solid #000;">Total</th>
                            </tr>
                        </thead>
                        <tbody>`;

                        // Loop through each product in the sales data and add them to the receipt
                        salesData.forEach(item => {
                            const itemTotal = item.price * item.quantity;
                            receiptContent += `
                        <tr>
                            <td style="padding: 5px; border: 1px solid #000;">${item.name}</td>
                            <td style="padding: 5px; border: 1px solid #000;">${item.quantity}</td>
                            <td style="padding: 5px; border: 1px solid #000;">₱${item.price.toFixed(2)}</td>
                            <td style="padding: 5px; border: 1px solid #000;">₱${itemTotal.toFixed(2)}</td>
                        </tr>`;
                        });

                        receiptContent += `
                        </tbody>
                    </table>
                    <p><strong>Grand Total:</strong> ₱${total.toFixed(2)}</p>
                    <p><strong>Payment Method:</strong> ${paymentMethod}</p>
                    <p><strong>User Money:</strong> ₱${userMoney.toFixed(2)}</p>
                    <p><strong>Change:</strong> ₱${change.toFixed(2)}</p>
                    <p style="text-align: center; margin-top: 20px;">Thank you for your purchase!</p>
                `;

                        // Show the receipt modal with the generated content
                        document.getElementById("receiptContent").innerHTML = receiptContent;
                        const receiptModal = new bootstrap.Modal(document.getElementById("printReceiptModal"));
                        receiptModal.show();

                        // Handle print receipt button click
                        document.getElementById("printReceiptBtn").addEventListener("click", () => {
                            const printWindow = window.open('', '_blank', 'width=600,height=400');
                            printWindow.document.write(receiptContent);
                            printWindow.document.close();  // Ensure the content is rendered
                            printWindow.print();  // Trigger the print dialog
                        });
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