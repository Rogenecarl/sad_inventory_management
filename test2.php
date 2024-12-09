make the search realtime dont change anything just change what i said full code it please:
<?php
include('../layouts/header.php');
require_once '../includes/load.php';
require_login();

$currentPage = basename($_SERVER['PHP_SELF'], '.php');

// Handle pagination, search, and sorting inputs
$itemsPerPage = isset($_GET['items_per_page']) ? (int) $_GET['items_per_page'] : 50;
$currentPageNumber = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$searchKeyword = isset($_GET['search']) ? trim($_GET['search']) : '';
$sortPrice = isset($_GET['sort_price']) ? $_GET['sort_price'] : ''; // New parameter
$offset = ($currentPageNumber - 1) * $itemsPerPage;

// Determine sorting order for SQL query
$sortQuery = '';
if ($sortPrice === 'highest') {
    $sortQuery = 'ORDER BY sale_price DESC';
} elseif ($sortPrice === 'lowest') {
    $sortQuery = 'ORDER BY sale_price ASC';
}

// Fetch total products count
$totalItemsQuery = $conn->prepare("SELECT COUNT(*) FROM products WHERE name LIKE :search");
$totalItemsQuery->bindValue(':search', '%' . $searchKeyword . '%', PDO::PARAM_STR);
$totalItemsQuery->execute();
$totalItems = $totalItemsQuery->fetchColumn();
$totalPages = ceil($totalItems / $itemsPerPage);

// Fetch paginated products with sorting
$stmt = $conn->prepare("
    SELECT prod_id, name, photo, categorie_id, prod_brand, prod_model, quantity, sale_price, created_at, updated_at 
    FROM products 
    WHERE name LIKE :search 
    $sortQuery
    LIMIT :limit OFFSET :offset
");
$stmt->bindValue(':search', '%' . $searchKeyword . '%', PDO::PARAM_STR);
$stmt->bindValue(':limit', $itemsPerPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

// Prepare category fetch query
$categoryStmt = $conn->prepare("SELECT name FROM categories WHERE category_id = ?");

// Handle category filter
$categoryFilter = isset($_GET['category_filter']) ? (int) $_GET['category_filter'] : null;

// Modify the query to include the category filter
$productQuery = "
    SELECT prod_id, name, photo, categorie_id, prod_brand, prod_model, quantity, sale_price, created_at, updated_at 
    FROM products 
    WHERE name LIKE :search
";

if ($categoryFilter) {
    $productQuery .= " AND categorie_id = :category_filter";
}

$productQuery .= " LIMIT :limit OFFSET :offset";

$stmt = $conn->prepare($productQuery);
$stmt->bindValue(':search', '%' . $searchKeyword . '%', PDO::PARAM_STR);
if ($categoryFilter) {
    $stmt->bindValue(':category_filter', $categoryFilter, PDO::PARAM_INT);
}
$stmt->bindValue(':limit', $itemsPerPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();


$message = isset($_GET['message']) ? $_GET['message'] : '';
$message_type = isset($_GET['message_type']) ? $_GET['message_type'] : '';
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<link rel="stylesheet" href="../lib/products/products.css">

<style>
      main .container-fluid, main .container-fluid>.row,main .container-fluid>.row>div{
        /*height:calc(100%);*/
    }
</style>

<main class="main container" id="main">
    <?php include('../layouts/sidebar.php'); ?>

    <div class="container-fluid o-field">
        <div class="row mt-3 ml-3 mr-3">

            <div class="col-lg-8  p-field">
                <div class="card ">
                    <div class="card-header text-dark">
                        <b>Products</b>
                    </div>
                    <div class="card-body row" id='prod-list'>
                        <div class="col-md-12">
                            <!--    <b>Category</b> -->
                            <div class=" row justify-content-start align-items-center" id="cat-list">
                                <div class="mx-3 cat-item" data-id='all'>
                                    <button class="btn btn-primary"><b class="text-white">All</b></button>
                                </div>
                                <?php
                                $qry = $conn->query("SELECT * FROM categories order by name asc");
                                while ($row = $qry->fetch_assoc()):
                                    ?>
                                    <div class="mx-3 cat-item" data-id='<?php echo $row['id'] ?>'>
                                        <button class="btn btn-primary"><?php echo ucwords($row['name']) ?></button>
                                    </div>
                                <?php endwhile; ?>
                            </div>

                            <hr>
                            <div class="row">
                                <?php
                                $prod = $conn->query("SELECT * FROM products where status = 1 order by name asc");
                                while ($row = $prod->fetch_assoc()):
                                    ?>
                                    <div class="col-md-2 mb-2">
                                        <div class="prod-item text-center " data-json='<?php echo json_encode($row) ?>'
                                            data-category-id="<?php echo $row['category_id'] ?>">
                                            <img src="../assets/uploads/element-banner2-right.jpg" class="rounded"
                                                width="100%">
                                            <span>
                                                <?php echo $row['name'] ?>
                                            </span>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row justify-content-center">
                            <div class="btn btn btn-sm col-sm-3 btn-primary mr-2" type="button" id="pay">Pay</div>
                            <div class="btn btn btn-sm col-sm-3 btn-primary" type="button" id="save_order">Pay later
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header text-dark">
                        <b>Order List</b>
                        <span class="float:right"><a class="btn btn-primary btn-sm col-sm-3 float-right"
                                href="../index.php" id="">
                                <i class="fa fa-home"></i> Home
                            </a></span>
                    </div>
                    <div class="card-body">
                        <form action="" id="manage-order">
                            <input type="hidden" name="id" value="<?php echo isset($_GET['id']) ? $_GET['id'] : '' ?>">
                            <div class="bg-white" id='o-list'>
                                <div class="d-flex w-100 bg-white mb-1">
                                    <label for="" class="text-dark"><b>Order No.</b></label>
                                    <input type="number" class="form-control-sm" name="order_number"
                                        value="<?php echo isset($order_number) ? $order_number : '' ?>" required>
                                </div>
                                <table class="table bg-light mb-5">
                                    <colgroup>
                                        <col width="20%">
                                        <col width="40%">
                                        <col width="40%">
                                        <col width="5%">
                                    </colgroup>
                                    <thead>
                                        <tr>
                                            <th>QTY</th>
                                            <th>Order</th>
                                            <th>Amount</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (isset($items)):
                                            while ($row = $items->fetch_assoc()):
                                                ?>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center justify-content-center">
                                                            <span class=" btn-minus"><b> </b></span>
                                                            <input type="number" name="qty[]" id=""
                                                                value="<?php echo $row['qty'] ?>">
                                                            <span class="btn-plus"><b></b></span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <input type="hidden" name="item_id[]" id=""
                                                            value="<?php echo $row['id'] ?>">
                                                        <input type="hidden" name="product_id[]" id=""
                                                            value="<?php echo $row['product_id'] ?>"><?php echo ucwords($row['name']) ?>
                                                        <small class="psmall">
                                                            (<?php echo number_format($row['price'], 2) ?>)</small>
                                                    </td>
                                                    <td class="text-right">
                                                        <input type="hidden" name="price[]" id=""
                                                            value="<?php echo $row['price'] ?>">
                                                        <input type="hidden" name="amount[]" id=""
                                                            value="<?php echo $row['amount'] ?>">
                                                        <span
                                                            class="amount"><?php echo number_format($row['amount'], 2) ?></span>
                                                    </td>
                                                    <td>
                                                        <span class=" btn-rem"><b><i class="fa fa-trash-alt"></i></b></span>
                                                    </td>
                                                </tr>
                                                <script>
                                                    $(document).ready(function () {
                                                        qty_func()
                                                        calc()
                                                        cat_func();
                                                    })
                                                </script>
                                            <?php endwhile; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-block bg-white" id="calc">
                                <table class="" width="100%">
                                    <tbody>
                                        <tr>
                                            <td><b>
                                                    <h6>Total</h6>
                                                </b></td>
                                            <td class="text-right">
                                                <input type="hidden" name="total_amount" value="0">
                                                <input type="hidden" name="total_tendered" value="0">
                                                <span class="">
                                                    <h6><b id="total_amount">0.00</b></h6>
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>


<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<?php include '../toast/toastr.php'; ?>
<script src="../lib/products/products.js"></script>
</body>

</html>