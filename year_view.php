<?php

if (!isset($_SESSION['logged'])) {
    header("Location: login_form.php");
    exit;
}

// Fetch user's name for the calendar header
$stmt = $conn->prepare("SELECT name FROM users WHERE work_id = :work_id");
$stmt->bindParam(':work_id', $userWorkId);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$calendarOwnerName = $user ? $user['name'] : "Unknown User";

// Define the year for the calendar
if (isset($_GET['year'])) {
    $year = intval($_GET['year']);
} else {
    $year = date("Y");
}

$previousYear = $year - 1;
$nextYear = $year + 1;

?>
<div class="year-navigation">
    <a href="calendar.php?year=<?php echo $previousYear; ?>&view=yearly" class="year-button">Előző év</a>
    <a href="calendar.php?year=<?php echo $nextYear; ?>&view=yearly" class="year-button">Következő év</a>
</div>
<div class="year-view">
    <h2><?php echo $year; ?> Éves Nézet</h2>
    <?php

    for ($m = 1; $m <= 12; $m++) {
        $monthName = date("F", mktime(0, 0, 0, $m, 1, $year));

        echo "<div class='month-container'>";
        echo "<div class='month-name'>" . $monthName . "</div>";
        echo "<div class='month-row'>";

        // Initialize an array to keep track of the days in the month
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $m, $year);
        $monthDays = array_fill(1, $daysInMonth, '');

        // Fetch important dates from the database and fill the array
        $stmt = $conn->prepare("SELECT date, day_status FROM calendar WHERE work_id = :work_id AND EXTRACT(MONTH FROM date) = :month AND EXTRACT(YEAR FROM date) = :year");
        $stmt->bindParam(':work_id', $userWorkId);
        $stmt->bindParam(':month', $m);
        $stmt->bindParam(':year', $year);
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $dayOfMonth = date('j', strtotime($row['date']));
            $monthDays[$dayOfMonth] = getCssClass($row['day_status']);
        }

        // Display each day in the month
        foreach ($monthDays as $day => $cssClass) {
            $dateLink = sprintf("date_details.php?date=%04d-%02d-%02d", $year, $m, $day);
            echo "<a href='$dateLink' class='day-box $cssClass'>$day</a>";
        }
        echo "</div>"; // Close month-row
        echo "</div>"; // Close month-container
    }
    ?>
</div>