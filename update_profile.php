<?php
// Munkamenet ellenőrzése
include "session_check.php";
// Adatbáziskapcsolat
include "app/config/connect.php";

// Ha nincs bejelentkezve, átirányítás a bejelentkezési oldalra
if (!isset($_SESSION['logged'])) {
    header("Location: login_form.php");
    exit;
}

// Input ellenőrzése és tisztítása
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
$email = $_SESSION['email']; // Email már szerepel a munkamenetben, feltételezzük, hogy biztonságos
$name = sanitizeInput($_POST['name']);
$cim = sanitizeInput($_POST['cim']);
$tax_number = sanitizeInput($_POST['tax_number']);
$entity_id = sanitizeInput($_POST['entity_id']);
$employee_card_number = sanitizeInput($_POST['employee_card_number']);

// Felhasználó adatainak frissítése az adatbázisban
$stmt = $conn->prepare("UPDATE users SET name = :name, cim = :cim, tax_number = :tax_number, entity_id = :entity_id, employee_card_number = :employee_card_number WHERE email = :email");

$stmt->bindParam(':name', $name);
$stmt->bindParam(':cim', $cim);
$stmt->bindParam(':tax_number', $tax_number);
$stmt->bindParam(':entity_id', $entity_id);
$stmt->bindParam(':employee_card_number', $employee_card_number);
$stmt->bindParam(':email', $email);

// Adatbázis frissítése és átirányítás a profil oldalra
if ($stmt->execute()) {
    header("Location: profile.php");
    exit;
} else {
    echo "Hiba a profil frissítésekor: " . $stmt->errorInfo()[2];
}
?>
