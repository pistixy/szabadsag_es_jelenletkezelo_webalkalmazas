<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Date Details</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php include "nav-bar.php"; ?>

<?php
if (isset($_GET['date'])) {
    $clickedDate = $_GET['date'];
} else {
    echo "Date not specified.";
    exit;
}
?>

<h1>Nap: <?php echo $clickedDate; ?></h1>
</body>
</html>
