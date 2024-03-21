<?php
// Munkamenet ellenőrzése
include "session_check.php";
// Adatbáziskapcsolat
include "connect.php";

// Ha nincs bejelentkezve, átirányítás a bejelentkezési oldalra
if (!isset($_SESSION['logged'])) {
    header("Location: login_form.php");
    exit;
}

// Felhasználó által megadott adatok begyűjtése az űrlapról
$email = $_SESSION['email'];
$name = $_POST['name'];
$cim = $_POST['cim'];
$adoazonosito = $_POST['adoazonosito'];
$szervezetszam = $_POST['szervezetszam'];
$alkalmazottikartyaszama = $_POST['alkalmazottikartyaszama'];

// Felhasználó adatainak frissítése az adatbázisban
$stmt = $conn->prepare("UPDATE users SET name = :name, cim = :cim, adoazonosito = :adoazonosito, szervezetszam = :szervezetszam, alkalmazottikartya = :alkalmazottikartyaszama WHERE email = :email");

$stmt->bindParam(':name', $name);
$stmt->bindParam(':cim', $cim);
$stmt->bindParam(':adoazonosito', $adoazonosito);
$stmt->bindParam(':szervezetszam', $szervezetszam);
$stmt->bindParam(':alkalmazottikartyaszama', $alkalmazottikartyaszama);
$stmt->bindParam(':email', $email);

// Adatbázis frissítése és átirányítás a profil oldalra
if ($stmt->execute()) {
    header("Location: profile.php");
    exit;
} else {
    echo "Hiba a profil frissítésekor: " . $stmt->errorInfo()[2];
}

?>
