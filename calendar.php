<?php
include "session_check.php";
include "connect.php";
include "function_get_name.php";
include "function_translate_month_to_Hungarian.php";

// Ha a felhasználó nincs bejelentkezve, átirányítjuk a bejelentkezési oldalra
if (!isset($_SESSION['logged'])) {
    header("Location: login_form.php");
    exit;
}

// Ellenőrizzük, hogy a work_id át lett-e adva az URL-ben, és a felhasználó admin-e, különben használjuk a munkaazonosítót a munkamenetből
if (isset($_GET['work_id']) && $_SESSION['is_user'] == false) {
    $userWorkId = $_GET['work_id'];
    $isOwnCalendar = $userWorkId == $_SESSION['work_id'];
} else {
    $userWorkId = $_SESSION['work_id'];
    $isOwnCalendar = true;
}

// Lekérdezzük a felhasználó nevét a naptár fejlécéhez
$stmt = $conn->prepare("SELECT name FROM users WHERE work_id = :work_id");
$stmt->bindParam(':work_id', $userWorkId);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$calendarOwnerName = $user ? $user['name'] : "Ismeretlen Felhasználó";

// Ha az URL-ben meg van adva az év és a hónap
if (isset($_GET['year']) && isset($_GET['month'])) {
    $year = intval($_GET['year']);
    $month = intval($_GET['month']);
} else {
    $year = date("Y");
    $month = date("n");
}

$prevMonth = ($month == 1) ? 12 : $month - 1;
$prevYear = ($month == 1) ? $year - 1 : $year;
$nextMonth = ($month == 12) ? 1 : $month + 1;
$nextYear = ($month == 12) ? $year + 1 : $year;

$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

$monthName = date("F", mktime(0, 0, 0, $month, 1, $year));

$firstDayOfWeek = date("N", mktime(0, 0, 0, $month, 1, $year));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $isOwnCalendar ? "Naptárad" : "{$calendarOwnerName} Naptára"; ?></title>
    <link rel="stylesheet" href="styles4.css">
    <link rel="stylesheet" href="calendar_colours.css">
</head>
<body>
<?php include "test_top-bar.php"; ?>
<div class="body-container">
    <div class="navbar" id="sidebar">
        <?php include "test_nav-bar.php"; ?>
    </div>
    <div class="main-content" id="main-content">
        <div class="test_content">
            <div class="calendar-container">
                <div class="calendar-view-container">
                    <div class="calendar-view-name">
                        <h1>
                            <?php echo $isOwnCalendar ? "Naptárad" : '<a href="profile.php?work_id=' . $userWorkId . '">' . $calendarOwnerName . '</a> Naptára'; ?>
                        </h1>
                    </div>
                    <div class="calendar-view-selector">
                        <form action="" method="get">
                            <input type="hidden" name="year" value="<?php echo $year; ?>">
                            <input type="hidden" name="work_id" value="<?php echo $userWorkId; ?>">
                            <select name="view" onchange="this.form.submit()">
                                <option value="yearly" <?php echo (!isset($_GET['view']) || $_GET['view'] == 'yearly') ? 'selected' : ''; ?>>Éves Nézet</option>
                                <option value="monthly" <?php echo (isset($_GET['view']) && $_GET['view'] == 'monthly') ? 'selected' : ''; ?>>Havi Nézet</option>
                            </select>
                        </form>
                    </div>
                </div>
                <div class="calendar-container">
                    <?php
                    // Alapértelmezett éves nézet, ha nincs megadva nézet, vagy ha az éves nézet van kiválasztva
                    $selectedView = $_GET['view'] ?? 'yearly';

                    if ($selectedView == 'yearly') {
                        $currentView = 'yearly';
                        include "year_view.php";
                    } elseif ($selectedView == 'monthly') {
                        $currentView = 'monthly';
                        include "month_view.php";
                    }
                    ?>
                </div>
                <?php
                include "csuszka.php";
                ?>
            </div>
        </div>
        <div class="footer-div">
            <?php include "footer.php"; ?>
        </div>
    </div>
</div>
<script src="collapse.js"></script>
</body>
</html>



