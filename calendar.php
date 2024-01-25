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

<div class="calendar">
    <!-- Update links to retain the work_id -->
    <a href="calendar.php?year=<?php echo $prevYear; ?>&month=<?php echo $prevMonth; ?>&work_id=<?php echo $userWorkId; ?>" class="prev-month">Előző hónap</a>
    <a href="calendar.php?year=<?php echo $nextYear; ?>&month=<?php echo $nextMonth; ?>&work_id=<?php echo $userWorkId; ?>" class="next-month">Következő hónap</a>
    <table class="calendar-table">
        <tr class="calendar-header">
            <th>Hétfő</th>
            <th>Kedd</th>
            <th>Szerda</th>
            <th>Csütörtök</th>
            <th>Péntek</th>
            <th>Szombat</th>
            <th>Vasárnap</th>
        </tr>
        <tr>
            <?php
            include "connect.php";

            for ($i = 1; $i < $firstDayOfWeek; $i++) {
                echo "<td class='calendar-cell'></td>";
            }

            for ($day = 1; $day <= $daysInMonth; $day++) {
                $dateToCheck = sprintf("%04d-%02d-%02d", $year, $month, $day);
                $stmt = $conn->prepare("SELECT day_status FROM calendar WHERE work_id = :work_id AND date = :date");
                $stmt->bindParam(':work_id', $userWorkId);
                $stmt->bindParam(':date', $dateToCheck);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($result) {
                    $day_status = $result['day_status'];
                    switch ($day_status) {
                        case 0:
                            $cssClass = "weekend-day";
                            break;
                        case 1:
                            $cssClass = "working-day";
                            break;
                        case 2:
                            $cssClass = "holiday-day";
                            break;
                        case 3:
                            $cssClass = "online-day";
                            break;
                        case 4:
                            $cssClass = "sick-leave";
                            break;
                        case 5:
                            $cssClass = "non-payed-leave";
                            break;
                        case 6:
                            $cssClass = "planned-vacation";
                            break;
                        default:
                            $cssClass = "";
                            break;
                    }

                } else {
                    $cssClass = "";
                }

                $linkURL = "date_details.php?date=$dateToCheck";
                echo "<td class='calendar-cell $cssClass'><a href='$linkURL'>$day</a></td>";

                if (($day + $firstDayOfWeek - 1) % 7 == 0 || $day == $daysInMonth) {
                    echo "</tr><tr>";
                }
            }
            ?>
        </tr>
    </table>
</div>

<?php
include "csuszka.php";
include "footer.php";
?>

</body>
</html>
