<?php
session_start();
include "includes/connect.php";
?>

<div class="navbar">
    <div class="logo">
        <a href="index.php">
            <img src="includes/logo.png" alt="Logo">
        </a>
    </div>
    <div class="navbar_items">
        <?php
        if (isset($_SESSION['logged'])) {
            echo '<a href="includes/profil.php">Naptáram</a>';
        } else {
        }
        ?>
    </div>
    <div class="navbar_items">
        <?php
        if (isset($_SESSION['logged'])) {
            echo '<a href="includes/profil.php">' . $_SESSION['email'] . '</a>';
        } else {
            echo '<a href="includes/login_form.php">Bejelentkezés</a>';
        }
        ?>
    </div>

    <div class="navbar_items">
        <?php
        if (isset($_SESSION['logged'])) {
            echo '<a href="includes/logout.php">Kijelentkezés</a>';
        } else {
            echo '<a href="includes/registration_form.php">Regisztráció</a>';
        }
        ?>
    </div>
</div>
