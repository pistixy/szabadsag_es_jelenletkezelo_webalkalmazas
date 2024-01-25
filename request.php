<?php
include "session_check.php";
include "connect.php";
include "nav-bar.php";

if (!isset($_SESSION['logged']) || !isset($_SESSION['work_id']) || !isset($_GET['request_id'])) {
    header("Location: login_form.php");
    exit;
}

$requestId = $_GET['request_id'];

// Fetching request details
$requestDetailsSql = "SELECT * FROM requests WHERE request_id = :requestId";
$requestDetailsStmt = $conn->prepare($requestDetailsSql);
$requestDetailsStmt->bindParam(':requestId', $requestId, PDO::PARAM_INT);
$requestDetailsStmt->execute();
$requestDetails = $requestDetailsStmt->fetch(PDO::FETCH_ASSOC);

// Prepare a SQL statement to retrieve all messages for the specific request_id
// and join with the calendar table to get the date
$sql = "SELECT m.*, c.date 
        FROM messages m
        LEFT JOIN requests r ON m.request_id = r.request_id
        LEFT JOIN calendar c ON r.calendar_id = c.calendar_id
        WHERE m.request_id = :requestId 
        ORDER BY m.timestamp ASC";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':requestId', $requestId, PDO::PARAM_INT);
$stmt->execute();
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Request Messages</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<?php if ($requestDetails): ?>
    <h1>Details for Request ID: <?php echo htmlspecialchars($requestId); ?></h1>
    <p>Requested Status: <?php echo htmlspecialchars($requestDetails['requested_status']); ?></p>
    <p>Message: <?php echo htmlspecialchars($requestDetails['message']); ?></p>
    <p>Status: <?php echo htmlspecialchars($requestDetails['request_status']); ?></p>
    <!-- Add more details as needed -->
<?php else: ?>
    <p>Request details not found.</p>
<?php endif; ?>

<h2>Messages for this Request</h2>

<?php if (!empty($messages)): ?>
    <table>
        <tr>
            <th>Message ID</th>
            <th>From Work ID</th>
            <th>Message</th>
            <th>Date</th>
            <th>Timestamp</th>
        </tr>
        <?php foreach ($messages as $message): ?>
            <tr>
                <td><?php echo htmlspecialchars($message['message_id']); ?></td>
                <td><?php echo htmlspecialchars($message['from_work_id']); ?></td>
                <td><?php echo htmlspecialchars($message['message']); ?></td>
                <td><?php echo htmlspecialchars($message['date']); ?></td>
                <td><?php echo htmlspecialchars($message['timestamp']); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <p>No messages for this request.</p>
<?php endif; ?>

<?php include "footer.php"; ?>

</body>
</html>
