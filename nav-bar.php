<?php

include "connect.php";
?>

<div class="navbar">
    <div class="logo">
        <a href="index.php">
            <img src="logo.png" alt="Logo">
        </a>
    </div>
    <div class="navbar_items">
        <?php
        if (isset($_SESSION['logged'])) {
            echo '<a href="index.php">Kezdőlap</a>';
        }
        ?>
    </div>
    <div class="navbar_items">
        <?php
        if (isset($_SESSION['logged'])) {
            echo '<a href="calendar.php">Naptáram</a>';
        }
        ?>
    </div>
    <div class="navbar_items">
        <?php
        if (isset($_SESSION['logged'])) {
            echo '<a href="comingtowork.php">Munkábajárási</a>';
        }
        ?>
    </div>
    <div class="navbar_items">
        <?php
        if (isset($_SESSION['logged']) && isset($_SESSION['isAdmin']) && $_SESSION['isAdmin']) {

            echo '<a href="list_users.php">Dolgozók</a>';
        }

        ?>
    </div>
    <div class="navbar_items">
        <?php
        if (isset($_SESSION['logged'])) {
            echo '<a href="profile.php">' . $_SESSION['email'] . '</a>';
        } else {
            echo '<a href="login_form.php">Bejelentkezés</a>';
        }
        ?>
    </div>

    <div class="navbar_items">
        <?php
        if (isset($_SESSION['logged'])) {
            echo '<a href="logout.php">Kijelentkezés</a>';
        } else {
            echo '<a href="registration_form.php">Regisztráció</a>';
        }
        ?>
    </div>
</div>