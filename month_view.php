<?php
include "set_days_to_taken.php";
$currentDay = date('Y-m-d');
?>


<div class="calendar">
    <h2><?php echo translateMonthToHungarian($monthName) . " " . $year; ?></h2> <!-- Display the current month and year -->
    <a href="calendar.php?year=<?php echo $prevYear; ?>&month=<?php echo $prevMonth; ?>&work_id=<?php echo $userWorkId; ?>&view=<?php echo $selectedView; ?>" class="year-button">Előző hónap</a>
    <a href="calendar.php?year=<?php echo $nextYear; ?>&month=<?php echo $nextMonth; ?>&work_id=<?php echo $userWorkId; ?>&view=<?php echo $selectedView; ?>" class="year-button">Következő hónap</a>

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
            include "app/config/connect.php";

            for ($i = 1; $i < $firstDayOfWeek; $i++) {
                echo "<td class='calendar-cell'></td>";
            }

            for ($day = 1; $day <= $daysInMonth; $day++) {
                $dateToCheck = sprintf("%04d-%02d-%02d", $year, $month, $day);

                // Add "today" class if it's the current day and current month
                $todayClass = ($day == $currentDay && $month == date('n')) ? "today" : "";

                $stmt = $conn->prepare("SELECT day_status FROM calendar WHERE work_id = :work_id AND date = :date");
                $stmt->bindParam(':work_id', $userWorkId);
                $stmt->bindParam(':date', $dateToCheck);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($result) {
                    $cssClass = $result['day_status'];
                } else {
                    $cssClass = "empty";
                }

                $linkURL = "date_details.php?date=$dateToCheck&view=$currentView";
                echo "<td class='$cssClass $todayClass calendar-cell'><a href='$linkURL'>$day</a></td>";

                if (($day + $firstDayOfWeek - 1) % 7 == 0 || $day == $daysInMonth) {
                    echo "</tr><tr>";
                }
            }
            ?>
        </tr>
    </table>
    <br>
    <div class="month-buttons">
        <form action="export_calendar_month_to_pdf.php" method="post">
            <input type="hidden" name="year" value="<?php echo $year; ?>">
            <input type="hidden" name="month" value="<?php echo $month; ?>">
            <input type="hidden" name="work_id" value="<?php echo $userWorkId; ?>">
            <button class="action-button" type="submit" name="export_calendar_month_pdf" value="1"><img src="public/images/icons/picture_as_pdf_20dp_FILL0_wght400_GRAD0_opsz20.png"> <?php echo translateMonthToHungarian($monthName);?>i beosztás exportálása </button>
        </form>
    </div>
</div>
