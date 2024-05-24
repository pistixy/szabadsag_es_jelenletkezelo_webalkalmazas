<?php
$currentDay = date('Y-m-d');

// Start transaction
$conn->beginTransaction();

try {
// First, update the calendar table
$updateCalendarStmt = $conn->prepare("
UPDATE calendar
SET day_status = 'paid_taken'
WHERE work_id = :work_id
AND day_status = 'paid_planned'
AND date < :today
");
$updateCalendarStmt->bindParam(':work_id', $userWorkId);
$updateCalendarStmt->bindParam(':today', $currentDay);
$updateCalendarStmt->execute();

// Check how many rows were updated in the calendar table
$updatedRows = $updateCalendarStmt->rowCount();

// If there were updates, then update the users table
if ($updatedRows > 0) {
$updateUsersStmt = $conn->prepare("
UPDATE users
SET paid_planned = paid_planned - :updatedRows,
paid_taken = paid_taken + :updatedRows
WHERE work_id = :work_id
");
$updateUsersStmt->bindParam(':updatedRows', $updatedRows, PDO::PARAM_INT);
$updateUsersStmt->bindParam(':work_id', $userWorkId);
$updateUsersStmt->execute();
}
// Commit the transaction
$conn->commit();
} catch (Exception $e) {
// An error occurred, roll back the transaction
$conn->rollBack();
// Handle error (e.g., logging, displaying a message to the user)
echo "An error occurred: " . $e->getMessage();
}
?>
