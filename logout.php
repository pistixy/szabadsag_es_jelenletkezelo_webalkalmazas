<?php
include "session_check.php";


if (isset($_SESSION['logged'])) {

    $_SESSION = array();

    //session változók törlése
    session_destroy();
    header("Location: ../index.php");
    exit;
} else {
    //átirányítás a bejelentkezésre
    header("Location: includes/login_form.php");
    exit;
}
?>
