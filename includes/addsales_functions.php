<?php
require_once 'load.php';

// Handle adding sales


// if (isset($_POST['searchTerm'])) {
//     $searchTerm = trim($_POST['searchTerm']);
//     $searchLike = '%' . $searchTerm . '%';

//     $stmt = $conn->prepare(
//         "SELECT prod_id, name, sale_price, prod_brand, prod_model 
//          FROM products 
//          WHERE name LIKE ? OR prod_brand LIKE ? OR prod_model LIKE ?"
//     );
//     $stmt->execute([$searchLike, $searchLike, $searchLike]);

//     while ($row = $stmt->fetch()) {
//         $currentDate = date('Y-m-d');
//         echo '<tr>';
//         echo '<td class="text-center">' . htmlspecialchars($row['name']) . '</td>';
//         echo '<td class="text-center"><input type="number" class="form-control price-input" min="0" step="0.01" value="' . htmlspecialchars($row['sale_price']) . '" onchange="updateTotal(this)"></td>';
//         echo '<td class="text-center"><input type="number" class="form-control quantity-input" min="1" value="1" onchange="updateTotal(this)"></td>';
//         echo '<td class="text-center"><input type="text" class="form-control total-price" value="' . htmlspecialchars($row['sale_price']) . '" readonly></td>';
//         echo '<td class="text-center"><input type="date" class="form-control" value="' . $currentDate . '"></td>';
//         echo '<td class="text-center"><button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSaleModal_' . htmlspecialchars($row['prod_id']) . '">Add Sale</button></td>';
//         echo '</tr>';
//     }
// }
?>