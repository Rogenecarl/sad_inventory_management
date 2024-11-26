<?php 
include('../layouts/header.php');
require_once '../includes/load.php';
require_login();
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>

<main class="main container" id="main">
    <?php include('../layouts/sidebar.php'); ?>

    <h1 class="dash-fix">Dev Dashboard</h1>
    <div class="main__container">
        <h1>Welcome to Dashboard</h1>
        <p>This is the main </p>

        <!-- Button to change content dynamically -->
        <button id="changeContentBtn">Change Content</button>

        <div id="dynamicContent">
            <p>This content will change when the button is clicked.</p>
        </div>
    </div>
</main>

</body>

</html>