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

$sender_id = $_SESSION['WORKID'];
$recipient = $_POST['recipient'];
$message_type = $_POST['message_type'];
$subject = $_POST['subject'];
$message_content = $_POST['message_content'];

$stmt = $conn->prepare("SELECT WORKID FROM users WHERE email = ?");
$stmt->bind_param("s", $recipient);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $receiver_id = $row['WORKID'];

    if ($sender_id !== null && $receiver_id !== null) {
        $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message_type, subject, message_content, timestamp) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("iiiss", $sender_id, $receiver_id, $message_type, $subject, $message_content);

        if ($stmt->execute()) {
            header("Location: messages.php");
            exit;
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        echo "sender_id or receiver_id is null.";
    }
} else {
    echo "Recipient not found. Please check the username or email.";
}

$stmt->close();
$conn->close();
?>
