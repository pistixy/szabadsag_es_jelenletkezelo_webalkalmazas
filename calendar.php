<?php
include "session_check.php";
include "connect.php";
include "nav-bar.php";

if (!isset($_SESSION['logged'])) {
    header("Location: login_form.php");
    exit;
}

// Check if work_id is passed in URL and user is admin, else use session work_id
if (isset($_GET['work_id']) && $_SESSION['isAdmin']) {
    $userWorkId = $_GET['work_id'];
    $isOwnCalendar = $userWorkId == $_SESSION['work_id'];
} else {
    $userWorkId = $_SESSION['work_id'];
    $isOwnCalendar = true;
}

// Fetch user's name for the calendar header
$stmt = $conn->prepare("SELECT name FROM users WHERE work_id = :work_id");
$stmt->bindParam(':work_id', $userWorkId);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$calendarOwnerName = $user ? $user['name'] : "Unknown User";

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
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<h1><?php echo $isOwnCalendar ? "Naptárad" : $calendarOwnerName . " Naptára"; ?></h1>
<form action="" method="get">
    <input type="hidden" name="year" value="<?php echo $year; ?>">
    <input type="hidden" name="work_id" value="<?php echo $userWorkId; ?>">
    <select name="view" onchange="this.form.submit()">
        <option value="yearly" <?php echo (!isset($_GET['view']) || $_GET['view'] == 'yearly') ? 'selected' : ''; ?>>Éves Nézet</option>
        <option value="monthly" <?php echo (isset($_GET['view']) && $_GET['view'] == 'monthly') ? 'selected' : ''; ?>>Havi Nézet</option>
    </select>
</form>
<?php
// Default to yearly view if no view is set or if yearly view is selected
$selectedView = $_GET['view'] ?? 'yearly';

if ($selectedView == 'yearly') {
    include "year_view.php"; // Assuming you have a separate file for the yearly view
} elseif ($selectedView == 'monthly') {
    include "month_view.php"; // Assuming you have a separate file for the monthly view
}
?>







<?php
// Define getStatusName function somewhere in your script
function getStatusName($statusCode) {
    $statusNames = [
        0 => "Szabadnap",
        1 => "Munkanap",
        2 => "Online Munka",
        3 => "Betegszabadság",
        4 => "Fizetetlen szabadság",
        5 => "Tervezett szabadság",
        6 => "Planned Vacation"
        // ... other statuses
    ];
    return $statusNames[$statusCode] ?? "Unknown";
}
function getCssClass($dayStatus) {
    switch ($dayStatus) {
        case 0:
            return "weekend-day";
        case 1:
            return "working-day";
        case 2:
            return "holiday-day";
        case 3:
            return "online-day";
        case 4:
            return "sick-leave";
        case 5:
            return "non-payed-leave";
        case 6:
            return "planned-vacation";
        default:
            return ""; // Default case if status is not recognized
    }
}

?>
<?php
include "csuszka.php";
include "footer.php";
?>

</body>
</html>
