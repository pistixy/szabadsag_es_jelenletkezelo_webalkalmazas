<?php

include "connect.php";
include "check_login.php";
?>

<div class="navbar">
    <div class="logo">
        <a href="index.php">
            <img src="unilogo.png" alt="Logo">
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
        if (isset($_SESSION['logged']) && isset($_SESSION['is_user']) && !$_SESSION['is_user']) {
            echo '<form action="search_results.php" method="get">';
            echo '    <input type="text" name="search_query" placeholder="work_id or name or email">';
            echo '    <input type="submit" value="Keresés" style="display: none;">';
            echo '</form>';
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