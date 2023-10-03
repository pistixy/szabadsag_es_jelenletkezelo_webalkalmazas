<!DOCTYPE html>
<html lang="hu-HU">
<head>
    <meta charset="UTF-8">
    <title>Üzenetküldés</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php
session_start();
include "nav-bar.php";
?>

<div class="message-form-container">
    <form action="send_message.php" method="post">
        <label for="recipient">Címzett:</label>
        <input type="text" id="recipient" name="recipient" required>
        <label for="message_content">Üzenet:</label>
        <textarea id="message_content" name="message_content" required></textarea>
        <input type="submit" value="Üzenet küldése">
    </form>
</div>
</body>
</html>
