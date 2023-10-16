<!DOCTYPE html>
<html lang="hu-HU">
<head>
    <meta charset="UTF-8">
    <title>Szabadságkezelő</title>
    <link rel="stylesheet" href="styles.css">
</head>
<?php
session_start();
?>
<body>


<?php
if ($_SESSION['logged'] == true){
    include "csempek.php";
}else
    include "login_form.php";
?>


<?php
include "footer.php";
?>
</body>
</html>