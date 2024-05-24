<?php
// Ellenőrizzük, hogy az űrlap elküldve lett-e
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ellenőrizzük, hogy az összes szükséges adat rendelkezésre áll-e
    if (isset($_POST['work_id'], $_POST['status'])) {
        // Kinyerjük az adatokat az űrlapról
        $work_id = $_POST['work_id'];
        $status = $_POST['status'];

        include "connect.php";

        // Adatbázis frissítése, hogy növelje a státusz értékét
        $stmt = $conn->prepare("UPDATE users SET $status = $status + 1 WHERE work_id = :work_id");
        $stmt->bindParam(':work_id', $work_id);
        $stmt->execute();

        // Átirányítás vissza a honlapra, ahonnan az űrlapot elküldték
        header("Location: {$_SERVER['HTTP_REFERER']}");
        exit;
    } else {
        // Átirányítás vissza a honlapra az űrlapból kapott hibaüzenettel
        header("Location: {$_SERVER['HTTP_REFERER']}?error=1");
        exit;
    }
} else {
    // Ha valaki megpróbálja közvetlenül hozzáférni ehhez az oldalhoz, átirányítjuk a főoldalra.
    header("Location: index.php");
    exit;
}
?>
