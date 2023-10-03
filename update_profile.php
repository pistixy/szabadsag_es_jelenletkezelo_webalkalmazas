<?php
session_start();
include "connect.php";

if (!isset($_SESSION['logged'])) {
    header("Location: login_form.php");
    exit;
}

$email = $_SESSION['email'];
$name = $_POST['name'];
$surname = $_POST['surname'];
$phone = $_POST['phone'];
$birthdate = $_POST['birthdate'];

$stmt = $conn->prepare("UPDATE users SET name = ?, surname = ?, phone = ?, birthdate = ? WHERE email = ?");
$stmt->bind_param("sssss", $name, $surname, $phone, $birthdate, $email);
if ($stmt->execute()) {
    header("Location: profile.php");
} else {
    echo "Error updating profile: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
