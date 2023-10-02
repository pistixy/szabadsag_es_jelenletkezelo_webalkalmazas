<?php
session_start();
include "connect.php";
?>

<div class="navbar">
    <div class="logo">
        <a href="../index.php">
            <img src="logo.png" alt="Logo">
        </a>
    </div>
    <div class="navbar_items">
        <?php
        if (isset($_SESSION['logged'])) {
            echo '<a href="profil.php">Naptáram</a>';
        } else {
        }
        ?>
    </div>
    <div class="navbar_items">
        <?php
        if (isset($_SESSION['logged']) && isset($_SESSION['isAdmin']) && $_SESSION['isAdmin']) {

            echo '<a href="Szabadsagkezelo/szabadsag_es_jelenletkezelo_webalkalmazas/includes/list_users.php">Dolgozók</a>';
        }

        ?>
    </div>
    <div class="navbar_items">
        <?php
        if (isset($_SESSION['logged'])) {
            echo '<a href="profil.php">' . $_SESSION['email'] . '</a>';
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
