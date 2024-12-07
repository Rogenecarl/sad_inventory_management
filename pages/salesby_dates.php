<?php include('../layouts/header.php');
require_once '../includes/load.php';

require_login();
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>

<link rel="stylesheet" href="../lib/salesbydates/dates.css">

<main class="main container" id="main">
    <?php include('../layouts/sidebar.php'); ?>

    <h1 class="dash-fix">Sales By Dates</h1>
    <div class="main__container">
        <form action="process_sales.php" method="POST" class="date-picker-form">
            <label for="from-date">From:</label>
            <input type="date" id="from-date" name="from_date" required>

            <label for="to-date">To:</label>
            <input type="date" id="to-date" name="to_date" required>

            <button type="submit" class="generate-btn">Generate</button>
        </form>
    </div>
</main>

</body>

</html>