<?php
include "session_check.php";
include "connect.php";
include "function_get_name.php";

if (!isset($_SESSION['logged'])) {
    header("Location: login_form.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Kérelmeim</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php include "navigation_bar-top.php"; ?>
<div class="body-container">
    <div class="navbar" id="sidebar">
        <?php include "navigation_bar-side.php"; ?>
    </div>
    <div class="main-content" id="main-content">
        <div class="test_content">
            <h1>Cím</h1>
            <p>tartalom</p>
        </div>
        <div class="footer-div">
            <?php include "footer.php"; ?>
        </div>
    </div>
</div>
<script src="collapse.js"></script>
</body>
</html>