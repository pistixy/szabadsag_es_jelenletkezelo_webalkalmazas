<!DOCTYPE html>
<html lang="hu-HU">
<head>
    <meta charset="UTF-8">
    <title>Szabadságkezelő</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php
include "session_check.php";
if (isset($_SESSION['logged']) && $_SESSION['logged'] === true) {

echo '<div class="body-container">';
    echo'<div class="navbar">';
            include "nav-bar.php";

    echo'</div>';
echo'<div class="main-content">';
    echo'<div class="csempek-div">';
            include "csempek.php";

echo'</div>';
echo'<div class="footer-div">';
            include "footer.php";
echo'</div>';
    echo'</div>';
echo'</div>';
} else {
    include "login_form.php";
}
echo'</body>';
echo'</html>';

