<?php

include "connect.php";
include "check_login.php";
?>

<div class="navbar">
    <div class="logo">
        <a href="index.php">
            <img src="unilogo.png" alt="Logo">
        </a>
        <hr>
    </div>
    <?php if (isset($_SESSION['logged'])): ?>
        <!--<div class="navbar_items">
            <a href="index.php">Kezdőlap</a>
        </div>-->
        <?php if (isset($_SESSION['is_user']) && !$_SESSION['is_user'] ): ?>
            <div class="navbar_items">
                <form action="search_results.php" method="get">
                    <label name="search_query"  >Keresés</label>
                    <input type="text" name="search_query" placeholder="work_id or name or email">
                    <!-- Enter gomb lenyomásával már kereshetünk is -->
                </form>
                <hr>
            </div>
        <?php endif; ?>
        <div class="navbar_items">
            <a href="calendar.php">Naptáram</a>
        </div>
        <div class="navbar_items">
            <a href="comingtowork.php">Munkába járás</a>
        </div>
        <div class="navbar_items">
            <a href="my_requests.php">Kérelmeim</a>
        </div>
        <div class="navbar_items">
            <a href="commutes.php?work_id=<?php echo $_SESSION['work_id']?>">Munkába járásaim</a>
        </div>
        <div class="navbar_items">
            <a href="holidays.php?work_id=<?php echo $_SESSION['work_id']?>">Szabadnapjaim</a>
        </div>
        <div class="navbar_items">
            <a href="profile.php"><?php echo $_SESSION['email']; ?></a>
        </div>
        <div class="navbar_items">
            <a href="hr_segedlet.php">HR segédlet</a>
        </div>

        
        <?php if (isset($_SESSION['is_user']) && !$_SESSION['is_user'] && ($_SESSION['position']== "tanszekvezeto" or $_SESSION['position']== "admin")): ?>
            <hr>
            <div class="navbar_items">
                <a href="incomming_requests.php">Bejövő Kérelmek</a>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['logged']) && ($_SESSION['position'] == "dekan" or $_SESSION['position'] == "admin" )): ?>
            <hr>
            <div class="navbar_items">
                <a href="osszesito_logika.php">Összesítők</a>
            </div>
        <?php endif; ?>
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