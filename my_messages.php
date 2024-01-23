<?php
session_start();
include "connect.php";
include "nav-bar.php";

// Check if the user is logged in
if (!isset($_SESSION['logged']) || !isset($_SESSION['work_id'])) {
    header("Location: login_form.php");
    exit;
}

$userWorkID = $_SESSION['work_id'];

// Prepare a SQL statement to retrieve all messages received by the logged-in user
$sql = "SELECT * FROM messages WHERE to_work_id = :userWorkID ORDER BY timestamp DESC";
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

<h1>My Messages</h1>

<?php if (!empty($messages)): ?>
    <table>
        <tr>
            <th>Message ID</th>
            <th>From Work ID</th>
            <th>Message</th>
            <th>Timestamp</th>
            <th>Type</th>
            <!-- Add other columns as necessary -->
        </tr>
        <?php foreach ($messages as $message): ?>
            <tr>
                <td><?php echo htmlspecialchars($message['message_id']); ?></td>
                <td><?php echo htmlspecialchars($message['from_work_id']); ?></td>
                <td><?php echo htmlspecialchars($message['message']); ?></td>
                <td><?php echo htmlspecialchars($message['timestamp']); ?></td>
                <td><?php echo htmlspecialchars($message['type']); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <p>No messages received.</p>
<?php endif; ?>

<?php include "footer.php"; ?>

</body>
</html>
