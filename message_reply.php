<!DOCTYPE html>
<html lang="hu-HU">
<head>
    <meta charset="UTF-8">
    <title>Válasz</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php
session_start();
include "connect.php";
include "nav-bar.php";

if (!isset($_SESSION['logged'])) {
    header("Location: login_form.php");
    exit;
}

if (!isset($_SESSION['WORKID'])) {
    echo "WORKID is not set.";
    exit;
}

if (isset($_GET['message_id'])) {
    $message_id = $_GET['message_id'];

    $stmt = $conn->prepare("SELECT message_content FROM messages WHERE message_ID = ?");
    $stmt->bind_param("i", $message_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $message_content = $row['message_content'];
    } else {
        echo '<p class="error-message">Hiba: Az üzenet nem található.</p>';
        exit;
    }

    $stmt = $conn->prepare("SELECT sender_ID, reply, timestamp FROM replies WHERE message_ID = ? ORDER BY timestamp DESC");
    $stmt->bind_param("i", $message_id);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    echo '<p class="error-message">Hiba: Az üzenet azonosítója hiányzik.</p>';
    exit;
}
?>

<div class="message-reply-container">
    <h2 class="message-reply-title">Üzenet Válasz</h2>
    <p class="original-message"><strong>Üzenet:</strong> <?php echo $message_content; ?></p>

    <h3 class="previous-replies-title">Korábbi válaszok:</h3>
    <?php
    if ($result->num_rows > 0) {
        echo '<ul class="previous-replies-list">';
        while ($row = $result->fetch_assoc()) {
            $sender_id = $row['sender_ID'];
            $reply_content = $row['reply'];
            $timestamp = $row['timestamp'];

            echo '<li class="reply-item"><strong>Feladó:</strong> ' . $sender_id . '</li>';
            echo '<li class="reply-item"><strong>Dátum:</strong> ' . $timestamp . '</li>';
            echo '<li class="reply-item">' . $reply_content . '</li>';
        }
        echo '</ul>';
    } else {
        echo '<p class="no-replies">Nincsenek korábbi válaszok.</p>';
    }
    ?>

    <form action="send_reply.php" method="post" class="reply-form">
        <textarea id="reply_content" name="reply_content" rows="5" required class="reply-textarea"></textarea>
        <input type="hidden" name="message_id" value="<?php echo $message_id; ?>">
        <input type="submit" value="Válasz Küldése" class="reply-submit">
    </form>
</div>
<a href="messages.php" class="back-to-messages">Vissza az üzenetekhez</a>
<?php
include "footer.php";
?>
</body>
</html>
