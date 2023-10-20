<!DOCTYPE html>
<html lang="hu-HU">
<head>
    <meta charset="UTF-8">
    <title>Szabadságkezelő</title>
    <link rel="stylesheet" href="styles.css">
</head>
<?php
session_start();

if (isset($_SESSION['logged']) && $_SESSION['logged'] === true) {
    include "nav-bar.php";
    include "csempek.php";
    include "footer.php";
} else {
    include "login_form.php";
}
?>

</body>
</html>