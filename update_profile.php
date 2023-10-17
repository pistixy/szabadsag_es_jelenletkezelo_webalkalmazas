<?php
session_start();
include "connect.php";

if (!isset($_SESSION['logged'])) {
    header("Location: login_form.php");
    exit;
}

$email = $_SESSION['email'];
$name = $_POST['name'];
$cim = $_POST['cim'];
$adoazonosito = $_POST['adoazonosito'];
$szervezetszam = $_POST['szervezetszam'];
$alkalmazottikartyaszama = $_POST['alkalmazottikartyaszama'];

$stmt = $conn->prepare("UPDATE users SET name = ?, cim = ?, adoazonosito = ?, szervezetszam = ?, alkalmazottikartya = ? WHERE email = ?");
$stmt->bind_param("ssssss", $name, $cim, $adoazonosito, $szervezetszam, $alkalmazottikartyaszama, $email);
if ($stmt->execute()) {
    header("Location: profile.php");
} else {
    echo "Error updating profile: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
