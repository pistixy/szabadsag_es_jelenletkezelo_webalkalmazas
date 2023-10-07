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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sender_id = $_SESSION['WORKID'];
    $message_id = $_POST['message_id'];
    $reply_content = $_POST['reply_content'];

    $stmt = $conn->prepare("INSERT INTO replies (message_ID, sender_ID, reply, timestamp) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iis", $message_id, $sender_id, $reply_content);

    if ($stmt->execute()) {
        header("Location: messages.php");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
} else {
    echo "Invalid request.";
}

$stmt->close();
$conn->close();
?>
