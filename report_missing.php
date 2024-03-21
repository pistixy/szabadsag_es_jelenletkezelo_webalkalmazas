<?php
// Munkamenet ellenőrző fájl beillesztése
include "session_check.php";
// Adatbázis kapcsolatfájl beillesztése
include "connect.php";

// Ellenőrizzük, hogy a felhasználó be van-e jelentkezve
if (!isset($_SESSION['logged']) || !isset($_SESSION['work_id'])) {
    header("Location: login_form.php");
    exit;
}

// Ellenőrizzük, hogy az űrlap elküldve lett-e és a szükséges adatok meg vannak-e adva
if (isset($_POST['calendar_id'])) {
    // Naptár azonosítójának lekérése az űrlap elküldéséből
    $calendar_id = $_POST['calendar_id'];

    // Frissítjük a day_status értékét az adott naptár azonosítóhoz tartozó sorban "unpayed_uncertified_taken"-re
    $stmt = $conn->prepare("UPDATE calendar SET day_status = 'unpayed_uncertified_taken' WHERE calendar_id = :calendar_id");
    $stmt->bindParam(':calendar_id', $calendar_id);
    $stmt->execute();

    // Visszairányítás az előző oldalra
    header("Location: {$_SERVER['HTTP_REFERER']}");
    exit;
} else {
    // Ha a calendar_id nincs megadva, akkor visszairányítás egy hibaoldalra vagy az előző oldalra
    header("Location: {$_SERVER['HTTP_REFERER']}");
    exit;
}
?>
