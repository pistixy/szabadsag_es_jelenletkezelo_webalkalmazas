<div class="calendar">
    <h2><?php echo $monthName . " " . $year; ?></h2> <!-- Display the current month and year -->
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