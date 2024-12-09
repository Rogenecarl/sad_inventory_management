<?php
include('../layouts/header.php');
require_once '../includes/load.php';

require_login();
$currentPage = basename($_SERVER['PHP_SELF'], '.php');

?>

<main class="main container" id="main">
    <?php include('../layouts/sidebar.php'); ?>

    <h1 class="dash-fix">Monthly Sales</h1>
    <div class="main__container">
        <p>Welcome to Monthly Sales</p>
    </div>
</main>

</body>

</html>