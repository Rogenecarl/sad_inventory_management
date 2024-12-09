<?php
include('../layouts/header.php');
require_once '../includes/load.php';

require_login();
$currentPage = basename($_SERVER['PHP_SELF'], '.php');

?>

<main class="main container" id="main">
    <?php include('../layouts/sidebar.php'); ?>

    <h1 class="dash-fix">Daily Sales</h1>
    <div class="main__container">
        <h5>Welcome to Daily Sales</h5>
    </div>
</main>


</body>

</html>