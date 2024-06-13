<div class="top-bar">
    <div class="top-bar-left">
        <button class="menu-button"><img src="public/images/icons/menu_20dp_FILL0_wght300_GRAD0_opsz20.png"></button>
        <a href="index.php" class="logo-link">
            <img src="public/images/icons/sze_logo2.svg" alt="Logo" class="logo-img">
            <div class="top-bar-title"> Szabadságkezelő</div>
        </a>

    </div>
    <div class="top-bar-right">
        <?php if (isset($_SESSION['logged'])): ?>
            <img style="height: 46px" src="public/images/icons/person_20dp_FILL0_wght400_GRAD0_opsz20.png">
            <div class="user-info">
                <span class="user-email"><a href="profile.php"><?php echo $_SESSION['email']; ?></a></span>
                <span class="user-position">(<?php echo getName($_SESSION['position']); ?>)</span>
            </div>
            <a href="logout.php" class="top-bar-link"><img src="public/images/icons/logout_20dp_FILL0_wght300_GRAD0_opsz20.png"></a>
        <?php else: ?>
            <a href="login_form.php" class="top-bar-link"><img src="public/images/icons/login_20dp_FILL0_wght300_GRAD0_opsz20.png"></a>
            <a href="registration_form.php" class="top-bar-link">Regisztráció</a>
        <?php endif; ?>
    </div>
</div>
