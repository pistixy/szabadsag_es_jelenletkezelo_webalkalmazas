<?php
session_start();
$userWorkId = $_SESSION['WORKID'];

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
    <title>Calendar</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php
include "nav-bar.php";
?>
<h1><?php echo $monthName . " " . $year; ?></h1>
<div class="calendar">
    <a href="calendar.php?year=<?php echo $prevYear; ?>&month=<?php echo $prevMonth; ?>" class="prev-month">Előző hónap</a>
    <a href="calendar.php?year=<?php echo $nextYear; ?>&month=<?php echo $nextMonth; ?>" class="next-month">Következő hónap</a>
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
                $linkURL = "date_details.php?date=$year-$month-$day";

                $dateToCheck = "$year-$month-$day";
                $stmt = $conn->prepare("SELECT is_working_day FROM calendar WHERE WORKID = ? AND date = ?");
                $stmt->bind_param("is", $userWorkId, $dateToCheck);
                $stmt->execute();
                $stmt->bind_result($isWorkingDay);

                $stmt->fetch();

                $stmt->close();

                $cssClass = ($isWorkingDay == 1) ? "working-day" : "vacation-day";

                echo "<td class='calendar-cell $cssClass'><a href='$linkURL'>$day</a></td>";

                if (($day + $firstDayOfWeek - 1) % 7 == 0 || $day == $daysInMonth) {
                    echo "</tr>";

                    if ($day < $daysInMonth) {
                        echo "<tr>";
                    }
                }
            }


            ?>
        </tr>
    </table>
</div>
<?php include "footer.php"; ?>

</body>
</html>
