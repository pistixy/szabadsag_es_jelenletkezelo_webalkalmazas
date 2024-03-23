<?php
// Munkamenet elindítása, ha még nem lett elindítva
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
echo "jog: ....................................................................". $_SESSION['isAdmin'];
$timeLimit = 60 * 60; // 60 perc másodpercekben

// Ha van utolsó tevékenység és az időtúllépés megtörtént
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeLimit)) {
    // Munkamenet törlése és lezárása
    session_unset();
    session_destroy();

    // Átirányítás a session_expired.php oldalra
    header("Location: session_expired.php");
    exit;
}

// Utolsó tevékenység időbélyeg frissítése
$_SESSION['last_activity'] = time();

?>
