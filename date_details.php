<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Date Details</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php
session_start();
include "connect.php";
include "nav-bar.php";

if (!isset($_SESSION['logged'])) {
    header("Location: login_form.php");
    exit;
}


if (isset($_GET['date'])) {
    $clickedDate = $_GET['date'];
} else {
    echo "Date not specified.";
    exit;
}
?>

<h1>Nap: <?php echo $clickedDate; ?></h1>
<?php
include "footer.php";
?>
</body>
</html>
