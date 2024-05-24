<div class="top-bar">
    <div class="top-bar-left">
        <button class="menu-button"><img src="icons/menu_20dp_FILL0_wght300_GRAD0_opsz20.png"></button>
        <a href="index.php" class="logo-link">
            <img src="icons/unilogo.png" alt="Logo" class="logo-img">
        </a>
    </div>
    <div class="top-bar-right">
        <?php if (isset($_SESSION['logged'])): ?>
            <img style="height: 46px" src="icons/person_20dp_FILL0_wght400_GRAD0_opsz20.png">
            <div class="user-info">
                <span class="user-email"><a href="profile.php"><?php echo $_SESSION['email']; ?></a></span>
                <span class="user-position">(<?php echo getName($_SESSION['position']); ?>)</span>
            </div>
            <a href="logout.php" class="top-bar-link"><img src="icons/logout_20dp_FILL0_wght300_GRAD0_opsz20.png"></a>
        <?php else: ?>
            <a href="login_form.php" class="top-bar-link"><img src="icons/login_20dp_FILL0_wght300_GRAD0_opsz20.png"></a>
            <a href="registration_form.php" class="top-bar-link">Regisztráció</a>
        <?php endif; ?>
    </div>
</div>
