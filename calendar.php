<?php
session_start();
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

$firstDayOfWeek = date("w", mktime(0, 0, 0, $month, 1, $year));
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
            <th>Vasárnap</th>
            <th>Hétfő</th>
            <th>Kedd</th>
            <th>Szerda</th>
            <th>Csütörtök</th>
            <th>Péntek</th>
            <th>Szombat</th>
        </tr>
        <tr>
            <?php
            for ($i = 0; $i < $firstDayOfWeek; $i++) {
                echo "<td class='calendar-cell'></td>";
            }

            for ($day = 1; $day <= $daysInMonth; $day++) {
                echo "<td class='calendar-cell'>$day</td>";

                if (($day + $firstDayOfWeek) % 7 == 0 || $day == $daysInMonth) {
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
</body>
</html>

