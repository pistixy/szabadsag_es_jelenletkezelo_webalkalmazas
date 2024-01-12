<?php
session_start();
$userWorkId = $_SESSION['work_id'];

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
<?php include "nav-bar.php"; ?>
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
                $dateToCheck = sprintf("%04d-%02d-%02d", $year, $month, $day);
                $stmt = $conn->prepare("SELECT is_working_day FROM calendar WHERE work_id = :work_id AND date = :date");
                $stmt->bindParam(':work_id', $userWorkId);
                $stmt->bindParam(':date', $dateToCheck);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($result) {
                    $isWorkingDay = $result['is_working_day'];
                    switch ($isWorkingDay == 1) {
                        case 0:
                            $cssClass = "vacation-day";
                            break;
                        case 1:
                            $cssClass =$cssClass = "working-day";
                            break;
                        case 2:
                            $cssClass = "online-day";
                            break;
                        case 3:
                            $cssClass = "sick-leave";
                            break;
                        case 4:
                            $cssClass = "non-payed-leave";
                            break;
                        case 5:
                            $cssClass = "planned-vacation";
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
