<?php
include "session_check.php";
include "connect.php";

// Ellenőrizzük, hogy a felhasználó be van-e jelentkezve
if (!isset($_SESSION['logged']) || !isset($_SESSION['work_id'])) {
    header("Location: login_form.php");
    exit;
}
// Ellenőrizzük, hogy az útmutató azonosítója meg van-e adva
if (!isset($_POST['commute_id'])) {
    // Átirányítás vagy kezeljük az hibát
    header("Location: commutes.php");
    exit;
}
// Az útmutató azonosítójának lekérése a POST adatokból
$commute_id = $_POST['commute_id'];

try {
    // SQL utasítás előkészítése az útmutató táblából való törléshez
    $sql = "DELETE FROM commute WHERE commute_id = :commute_id AND work_id = :work_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':commute_id', $commute_id, PDO::PARAM_INT);
    $stmt->bindParam(':work_id', $_SESSION['work_id'], PDO::PARAM_INT);

    // Az utasítás végrehajtása
    $stmt->execute();

    // Átirányítás vissza az útmutatókat megjelenítő oldalra a törlés után
    header("Location: commutes.php");
    exit;
} catch (PDOException $e) {
    // Adatbázis hiba kezelése
    echo "Hiba: " . $e->getMessage();
}
?>