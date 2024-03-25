<?php
include "session_check.php";
include "connect.php";


// Check if the user is logged in
if (!isset($_SESSION['logged']) || !isset($_SESSION['work_id'])) {
    header("Location: login_form.php");
    exit;
}

$userWorkID = $_SESSION['work_id'];

// Prepare a SQL statement to retrieve all messages received by the logged-in user along with the date from the calendar
$sql = "SELECT m.*, r.calendar_id, c.date 
        FROM messages m
        LEFT JOIN requests r ON m.request_id = r.request_id
        LEFT JOIN calendar c ON r.calendar_id = c.calendar_id
        WHERE m.to_work_id = :userWorkID 
        ORDER BY m.timestamp DESC";

$stmt = $conn->prepare($sql);
$stmt->bindParam(':userWorkID', $userWorkID, PDO::PARAM_INT);
$stmt->execute();
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Messages</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="body-container">
    <div class="navbar">
        <?php
        include "nav-bar.php";
        ?>
    </div>
    <div class="main-content">
        <div class="my-messages">
        <h1>My Messages</h1>

        <?php if (!empty($messages)): ?>
            <table border=1>
                <tr>
                    <th>Message ID</th>
                    <th>From Work ID</th>
                    <th>To Work ID</th>
                    <th>To Position</th>
                    <th>Type</th>
                    <th>Request ID</th>
                    <th>Date</th>
                    <th>Message</th>
                    <th>Timestamp</th>
                </tr>
                <?php foreach ($messages as $message): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($message['message_id']); ?></td>
                        <td><?php echo htmlspecialchars($message['from_work_id']); ?></td>
                        <td><?php echo htmlspecialchars($message['to_work_id']); ?></td>
                        <td><?php echo htmlspecialchars($message['to_position']); ?></td>
                        <td><?php echo htmlspecialchars($message['type']); ?></td>
                        <td>
                            <a href="request.php?request_id=<?php echo urlencode($message['request_id']); ?>">
                                <?php echo htmlspecialchars($message['request_id']); ?>
                            </a>
                        </td>
                        <td>
                            <?php if (isset($message['calendar_id']) && isset($message['date'])): ?>
                                <a href="date_details.php?date=<?php echo urlencode($message['date']); ?>">
                                    <?php echo htmlspecialchars($message['date']); ?>
                                </a>
                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($message['message']); ?></td>
                        <td><?php echo htmlspecialchars($message['timestamp']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No messages received.</p>
        <?php endif; ?>
        </div>

        <div class="footer-div">
            <?php
            include "footer.php";
            ?>
        </div>
    </div>
</div>
</body>
</html>