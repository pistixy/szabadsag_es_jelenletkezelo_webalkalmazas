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
    <?php if (isset($_SESSION['logged'])): ?>
        <div class="navbar_items">
            <a href="index.php">Kezdőlap</a>
        </div>
        <div class="navbar_items">
            <a href="calendar.php">Naptáram</a>
        </div>
        <div class="navbar_items">
            <a href="comingtowork.php">Munkábajárási</a>
        </div>
        <?php if (isset($_SESSION['is_user']) && !$_SESSION['is_user']): ?>
            <div class="navbar_items">
                <form action="search_results.php" method="get">
                    <input type="text" name="search_query" placeholder="work_id or name or email">
                    <!-- The submit button can be triggered by pressing Enter in the input field -->
                </form>
            </div>
        <?php endif; ?>
        <div class="navbar_items">
            <a href="profile.php"><?php echo $_SESSION['email']; ?></a>
        </div>
        <div class="navbar_items">
            <a href="logout.php">Kijelentkezés</a>
        </div>
    <?php else: ?>
        <div class="navbar_items">
            <a href="login_form.php">Bejelentkezés</a>
        </div>
        <div class="navbar_items">
            <a href="registration_form.php">Regisztráció</a>
        </div>
    <?php endif; ?>
</div>