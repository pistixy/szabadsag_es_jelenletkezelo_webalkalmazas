<?php
include "connect.php";
if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
    $stmt = $conn->prepare("SELECT WORKID FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $userWorkId = $row['WORKID'];
    } else {
        echo "User not found in the database.";
        exit;
    }
} else {
    echo "User session not found.";
    exit;
}
$currentDate = new DateTime();
$lastDayOfYear = new DateTime($currentDate->format('Y-12-31'));
$interval = $currentDate->diff($lastDayOfYear);
$remainingDays = $interval->format('%a');
$currentDate = date("Y-m-d");
$limit= $remainingDays + 7;
for ($i = 0; $i < $limit; $i++) {
    $date = date("Y-m-d", strtotime($currentDate . " + " . $i . " days"));
    $dayOfWeek = date("N", strtotime($date));
    $isWorkingDay = date('w', strtotime($date)) >= 1 && date('w', strtotime($date)) <= 5 ? 1 : 0;
    $isVacationDay = date('w', strtotime($date)) >= 6 ? 1 : 0;
    $stmt = $conn->prepare("INSERT INTO calendar (WORKID, date, is_working_day, is_vacation_day) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isii", $userWorkId, $date, $isWorkingDay, $isVacationDay);
    if ($stmt->execute()) {
    //    echo "Inserted data for date: " . $date . "<br>";
    } else {
        echo "Error inserting data for date: " . $date . "<br>";
        echo "Error: " . $stmt->error . "<br>";
    }

    if ($stmt->affected_rows <= 0) {
        echo "Error inserting data for date: " . $date;
    }
}
echo "Calendar data for the next $limit days has been filled.";
?>

