<?php
include "session_check.php";
include "connect.php";
include "nav-bar.php";
include "function_get_status_name.php";


if (!isset($_SESSION['logged'])) {
    header("Location: login_form.php");
    exit;
}

// Check if work_id is passed in URL and user is admin, else use session work_id
if (isset($_GET['work_id']) && $_SESSION['is_user']==false) {
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
    <link rel="stylesheet" href="calendar_colours.css">
</head>
<body>

<table style="width: 100%;">
    <tr>
        <td style="width: 33.33%; text-align: center;">
            <form action="" method="get">
                <input type="hidden" name="year" value="<?php echo $year; ?>">
                <input type="hidden" name="work_id" value="<?php echo $userWorkId; ?>">
                <select name="view" onchange="this.form.submit()">
                    <option value="yearly" <?php echo (!isset($_GET['view']) || $_GET['view'] == 'yearly') ? 'selected' : ''; ?>>Éves Nézet</option>
                    <option value="monthly" <?php echo (isset($_GET['view']) && $_GET['view'] == 'monthly') ? 'selected' : ''; ?>>Havi Nézet</option>
                </select>
            </form>
        </td>
        <td style="width: 33.33%; text-align: center;">
            <h1><?php echo $isOwnCalendar ? "Naptárad" : $calendarOwnerName . " Naptára"; ?></h1>
        </td>
        <td style="width: 33.33%; text-align: center;">
            <?php
            include "legend.php";
            ?>
        </td>

    </tr>

</table>

<?php
// Default to yearly view if no view is set or if yearly view is selected
$selectedView = $_GET['view'] ?? 'yearly';

if ($selectedView == 'yearly') {
    $currentView= 'yearly';
    include "year_view.php";
} elseif ($selectedView == 'monthly') {
    $currentView= 'monthly';
    include "month_view.php";
}

?>
<?php
include "csuszka.php";
include "footer.php";
?>

</body>
</html>
