<!DOCTYPE html>
<html lang="hu-HU">
<head>
    <meta charset="UTF-8">
    <title>Szabadságkezelő</title>
    <link rel="stylesheet" href="styles.css">
</head>

<?php
session_start();
include "nav-bar.php";
?>
<body>
<?php
if (!(isset($_SESSION['logged']))) {
    include "gallery.php";
}
?>
<?php
include "footer.php";
?>
</body>
</html>