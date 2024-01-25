<?php
include "session_check.php";
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

$stmt = $conn->prepare("UPDATE users SET name = :name, cim = :cim, adoazonosito = :adoazonosito, szervezetszam = :szervezetszam, alkalmazottikartya = :alkalmazottikartyaszama WHERE email = :email");

$stmt->bindParam(':name', $name);
$stmt->bindParam(':cim', $cim);
$stmt->bindParam(':adoazonosito', $adoazonosito);
$stmt->bindParam(':szervezetszam', $szervezetszam);
$stmt->bindParam(':alkalmazottikartyaszama', $alkalmazottikartyaszama);
$stmt->bindParam(':email', $email);

if ($stmt->execute()) {
    header("Location: profile.php");
    exit;
} else {
    echo "Error updating profile: " . $stmt->errorInfo()[2];
}

?>
