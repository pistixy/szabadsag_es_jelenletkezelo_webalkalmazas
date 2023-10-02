<?php
session_start();


if (isset($_SESSION['logged'])) {

    $_SESSION = array();


    session_destroy();


    header("Location: ../index.php");
    exit;
} else {

    header("Location: includes/login_form.php");
    exit;
}
?>
