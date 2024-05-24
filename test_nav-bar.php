<?php
include "connect.php";
include "check_login.php";
?>

<div class="navbar">
    <?php if (isset($_SESSION['logged'])): ?>
        <?php if (isset($_SESSION['logged'])): ?>
            <?php if (isset($_SESSION['is_user']) && !$_SESSION['is_user']): ?>
                <div class="navbar_items">
                    <form action="search_results.php" method="get">
                        <label name="search_query"><img src="icons/search_20dp_FILL0_wght400_GRAD0_opsz20.png"> Keresés</label>
                        <input class="search-bar" type="text" name="search_query" placeholder="work_id or name or email">
                        <!-- Enter gomb lenyomásával már kereshetünk is -->
                    </form>
                    <hr>
                </div>
            <?php endif; ?>
            <div class="navbar_items">
                <a href="calendar.php"><img src="icons/calendar_today_20dp_FILL0_wght400_GRAD0_opsz20.png"> Naptáram</a>
            </div>
            <div class="navbar_items">
                <a href="comingtowork.php"><img src="icons/commute_20dp_FILL0_wght400_GRAD0_opsz20.png"> Munkába járás</a>
            </div>
            <div class="navbar_items">
                <a href="my_requests.php"><img src="icons/request_page_20dp_FILL0_wght400_GRAD0_opsz20.png"> Kérelmeim</a>
            </div>
            <div class="navbar_items">
                <a href="commutes.php?work_id=<?php echo $_SESSION['work_id']?>"><img src="icons/commute_20dp_FILL0_wght400_GRAD0_opsz20.png"> Munkába járásaim</a>
            </div>
            <div class="navbar_items">
                <a href="holidays.php?work_id=<?php echo $_SESSION['work_id']?>"><img src="icons/beach_access_20dp_FILL0_wght400_GRAD0_opsz20.png"> Szabadnapjaim</a>
            </div>
            <div class="navbar_items">
                <a href="hr_segedlet.php"><img src="icons/help_20dp_FILL0_wght400_GRAD0_opsz20.png"> HR segédlet</a>
            </div>
            <?php if (isset($_SESSION['is_user']) && !$_SESSION['is_user'] && ($_SESSION['position'] == "tanszekvezeto" || $_SESSION['position'] == "admin")): ?>
                <hr>
                <div class="navbar_items">
                    <a href="incomming_requests.php"><img src="icons/inbox_20dp_FILL0_wght400_GRAD0_opsz20.png"> Bejövő Kérelmek</a>
                </div>
            <?php endif; ?>
            <?php if (isset($_SESSION['logged']) && ($_SESSION['position'] == "dekan" || $_SESSION['position'] == "admin")): ?>
                <hr>
                <div class="navbar_items">
                    <a href="osszesito_logika.php"><img src="icons/functions_20dp_FILL0_wght400_GRAD0_opsz20.png"> Összesítők</a>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    <?php endif; ?>

</div>
