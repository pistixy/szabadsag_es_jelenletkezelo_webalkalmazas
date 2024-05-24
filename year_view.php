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
include "set_days_to_taken.php";

$previousYear = $year - 1;
$nextYear = $year + 1;

?>

<div class="year-navigation">
    <form action="calendar.php" method="get" style="display: inline;">
        <input type="hidden" name="year" value="<?php echo $previousYear; ?>">
        <input type="hidden" name="view" value="yearly">
        <button class="action-button" type="submit">
            <img src="icons/arrow_back_20dp_FILL0_wght400_GRAD0_opsz20.png" alt="Previous Year">
            Előző év
        </button>
    </form>
    <form action="calendar.php" method="get" style="display: inline;">
        <input type="hidden" name="year" value="<?php echo $nextYear; ?>">
        <input type="hidden" name="view" value="yearly">
        <button class="action-button" type="submit">
            Következő év
            <img src="icons/arrow_forward_20dp_FILL0_wght400_GRAD0_opsz20.png" alt="Next Year">
        </button>
    </form>
</div>


<div class="year-view">
    <h2><?php echo $year; ?> Éves Nézet</h2>
    <?php

    for ($month = 1; $month<= 12; $month++) {
        $monthName = date("F", mktime(0, 0, 0, $month, 1, $year));


        echo "<div class='month-container'>";
        echo "<div class='month-name-container'>";
            echo "<div class='month-name'>";
            echo translateMonthToHungarian($monthName);
            echo '</div>';

            echo '<div class="month-buttons">';
            echo '<form action="export_calendar_month_to_pdf.php" method="post">';
            echo '<input type="hidden" name="year" value="' . $year . '">';
            echo '<input type="hidden" name="month" value="' . $month . '">';
            echo '<input type="hidden" name="work_id" value="' . $userWorkId . '">';
            echo '<button class="action-button" type="submit" name="export_calendar_month_pdf" value="1">';
            echo '<img src="icons/picture_as_pdf_20dp_FILL0_wght400_GRAD0_opsz20.png">';
            echo ' beosztás exportálása';
            echo '</button>';
            echo '</form>';
            echo "</div>";
            
            echo "</div>";
        echo "<div class='month-row'>";

        // Initialize an array to keep track of the days in the month
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $monthDays = array_fill(1, $daysInMonth, '');

        // Fetch important dates from the database and fill the array
        $stmt = $conn->prepare("SELECT date, day_status FROM calendar WHERE work_id = :work_id AND EXTRACT(MONTH FROM date) = :month AND EXTRACT(YEAR FROM date) = :year");
        $stmt->bindParam(':work_id', $userWorkId);
        $stmt->bindParam(':month', $month);
        $stmt->bindParam(':year', $year);
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $dayOfMonth = date('j', strtotime($row['date']));
            $monthDays[$dayOfMonth] = $row['day_status'];
        }

        // Get the current day
        $currentDay = date('j');
        $currentMonth = date('n'); // Get the current month (without leading zeros)

        // Display each day in the month
        foreach ($monthDays as $day => $cssClass) {
            // Check if the $cssClass is empty and set it to "empty"
            if (empty($cssClass)) {
                $cssClass = "empty";
            }

            // Add "today" class if it's the current day and current month
            $todayClass = ($day == $currentDay && $month == $currentMonth) ? "today" : "";

            $datetocheck = sprintf("%04d-%02d-%02d", $year, $month, $day);
            $dateLink = "date_details.php?date=" . $datetocheck . "&view=$currentView";
            echo "<a href='$dateLink' class='day-box $cssClass $todayClass'>$day</a>"; //old correct line
        }

        echo "</div>"; // Close month-row
        echo "</div>"; // Close month-container
    }
    ?>
</div>
