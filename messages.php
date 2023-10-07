<?php
session_start();
include "connect.php";

if (!isset($_SESSION['logged'])) {
    header("Location: login_form.php");
    exit;
}

if (!isset($_SESSION['WORKID'])) {
    echo "WORKID is not set.";
    exit;
}

$user_id = $_SESSION['WORKID'];
$query = "SELECT m.message_id, m.message_content, m.timestamp, m.message_type, m.subject, u.email AS sender_email
          FROM messages AS m
          INNER JOIN users AS u ON m.sender_id = u.WORKID
          WHERE m.receiver_id = ?
          ORDER BY m.timestamp DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="hu-HU">
<head>
    <meta charset="UTF-8">
    <title>Üzenetek</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php include "nav-bar.php"; ?>

<div class="message-container">
    <h2>Üzenetek</h2>
    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $message_id = $row['message_id'];
            $message_content = $row['message_content'];
            $subject = $row['subject'];
            $message_type = $row['message_type'];
            $sender_email = $row['sender_email'];
            $timestamp = $row['timestamp'];
            $messageTypeLabel = ($message_type == 1) ? "Üzenet" : "Kérvény";

            echo '<div class="message">
                    <p><strong>Feladó: ' . $sender_email . '</strong></p>
                    <p><strong>Tárgy: ' . $subject . '</strong></p>
                    <p>' . $messageTypeLabel . '</p> 
                    <p>' . $message_content . '</p>
                    <p>Dátum: ' . $timestamp . '</p>
                    <a href="message_reply.php?message_id=' . $message_id . '">Valaszok megtekintése</a>
                  </div>';
        }
    } else {
        echo '<p>Nincsenek üzenetek.</p>';
    }
    ?>
</div>
<?php
include "footer.php";
?>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
