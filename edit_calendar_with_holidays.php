<?php
include "connect.php";
session_start();

$comment = "Ünnep";

if (isset($_SESSION['WORKID'])) {
    $userWorkId = $_SESSION['WORKID'];

include "holidayarray.php";

    $placeholders = str_repeat('?,', count($publicHolidays) - 1) . '?';

    $sql = "UPDATE calendar SET comment = ? WHERE WORKID = ? AND date IN ($placeholders)";

    $stmt = $conn->prepare($sql);

    $paramTypes = str_repeat('s', count($publicHolidays) + 2);

    $params = array_merge([$comment, $userWorkId], $publicHolidays);
    $stmt->bind_param($paramTypes, ...$params);

    if ($stmt->execute()) {
        echo "Comments added to the calendar for the current user successfully.<br>";
    } else {
        echo "Error adding comments for the current user: " . $stmt->error . "<br>";
    }
} else {
    echo "User session not found.";
}
$conn->close();


include "connect.php";

$commentToUpdate = 'Ünnep';
$isWorkingDayValue = 0;
$isVacationDayValue = 1;

$sql = "UPDATE calendar SET is_working_day = ?, is_vacation_day = ? WHERE comment = ?";
$stmt = $conn->prepare($sql);

$stmt->bind_param('iis', $isWorkingDayValue, $isVacationDayValue, $commentToUpdate);

if ($stmt->execute()) {
    echo "Entries with comment 'Ünnep' updated successfully. is_working_day set to 1 and is_vacation_day set to 0.<br>";
} else {
    echo "Error updating entries with comment 'Ünnep': " . $stmt->error . "<br>";
}

$conn->close();


?>
