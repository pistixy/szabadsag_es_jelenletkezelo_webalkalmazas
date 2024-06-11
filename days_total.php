<?php
// Az adatbázis kapcsolati fájl beillesztése
include "app/config/connect.php";

// Ellenőrizzük, hogy a munka azonosító (work_id) be van-e állítva a munkamenetben
if (isset($_SESSION['work_id'])) {
    // A munka azonosító (work_id) lekérése a munkamenetből
    $workId = $_SESSION['work_id'];
    try {
        // SQL utasítás előkészítése a felhasználók táblában lévő adatok kiválasztására a munka azonosító (work_id) alapján
        $sql = "SELECT paid_free, paid_requested, paid_planned FROM users WHERE work_id = :work_id";
        $stmt = $conn->prepare($sql);

        // A munka azonosító (work_id) paraméter hozzárendelése
        $stmt->bindParam(':work_id', $workId, PDO::PARAM_INT);

        // A lekérdezés végrehajtása
        $stmt->execute();

        // Az adatok lekérése
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Az összesített érték kiszámítása
        $total = $row['paid_free'] + $row['paid_requested'] + $row['paid_planned'];

        // Az összesített érték megjelenítése
        echo $total;

    } catch (PDOException $e) {
        // Hibakezelés
        echo "Hiba: " . $e->getMessage();
    }
} else {
    // Amennyiben nincs munka azonosító a munkamenetben
    echo "Nincs valid munkamenet.";
}
?>
