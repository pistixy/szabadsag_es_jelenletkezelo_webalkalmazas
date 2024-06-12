<?php
include "app/config/connect.php";
include "check_login.php";
?>

<div class="navbar">
    <?php if (isset($_SESSION['logged'])): ?>
        <?php if (isset($_SESSION['logged'])): ?>
            <?php if (isset($_SESSION['is_user']) && !$_SESSION['is_user']): ?>
                <div class="navbar_items">
                    <form action="search_results.php" method="get" class="search-form">

                            <img src="public/images/icons/search_20dp_FILL0_wght400_GRAD0_opsz20.png" alt="Search Icon">

                        <input class="search-bar" type="text" name="search_query" placeholder="work_id, név vagy email">
                        <!-- Enter gomb lenyomásával már kereshetünk is -->
                    </form>
                    <hr>
                </div>

            <?php endif; ?>
            <div class="navbar_items">
                <a href="calendar.php"><img src="public/images/icons/calendar_today_20dp_FILL0_wght400_GRAD0_opsz20.png"> Szabadságtervező</a>
            </div>
            <div class="navbar_items">
                <a href="coming_to_work.php"><img src="public/images/icons/commute_20dp_FILL0_wght400_GRAD0_opsz20.png"> Új munkába járás rögzítése</a>
            </div>
            <div class="navbar_items">
                <a href="my_requests.php"><img src="public/images/icons/request_page_20dp_FILL0_wght400_GRAD0_opsz20.png"> Korábbi szabadságkérelmek</a>
            </div>
            <div class="navbar_items">
                <a href="commutes.php?work_id=<?php echo $_SESSION['work_id']?>"><img src="public/images/icons/commute_20dp_FILL0_wght400_GRAD0_opsz20.png"> Korábbi munkába járások</a>
            </div>
            <div class="navbar_items">
                <a href="holidays.php?work_id=<?php echo $_SESSION['work_id']?>"><img src="public/images/icons/beach_access_20dp_FILL0_wght400_GRAD0_opsz20.png"> Éves szabadság</a>
            </div>
            <div class="navbar_items">
                <a href="HR_help_site.php"><img src="public/images/icons/help_20dp_FILL0_wght400_GRAD0_opsz20.png"> HR segédlet</a>
            </div>
            <?php if (isset($_SESSION['is_user']) && !$_SESSION['is_user'] && ($_SESSION['position'] == "tanszekvezeto" || $_SESSION['position'] == "admin")): ?>
                <hr>
                <div class="navbar_items">
                    <a href="incomming_requests.php"><img src="public/images/icons/inbox_20dp_FILL0_wght400_GRAD0_opsz20.png"> Bejövő Kérelmek</a>
                </div>
            <?php endif; ?>
            <?php if (isset($_SESSION['logged']) && ($_SESSION['position'] == "dekan" || $_SESSION['position'] == "admin")): ?>
                <hr>
                <div class="navbar_items">
                    <a href="summation_logic.php"><img src="public/images/icons/functions_20dp_FILL0_wght400_GRAD0_opsz20.png"> Összesítők</a>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    <?php endif; ?>

</div>
