<?php
// Az adatbázis kapcsolatfájl beillesztése
include "connect.php";

// Ellenőrizzük, hogy a munka azonosító (work_id) be van-e állítva a munkamenetben
if (isset($_SESSION['work_id'])) {
    // A munka azonosító (work_id) lekérése a munkamenetből
    $workId = $_SESSION['work_id'];

    try {
        // Készítsünk egy SQL utasítást a fizetett és korábban fizetett szabadságok lekérdezésére az adatbázisból
        $sql = "SELECT paid_free FROM users WHERE work_id = :work_id";
        $stmt = $conn->prepare($sql);

        // A munka azonosító (work_id) paraméter összekapcsolása
        $stmt->bindParam(':work_id', $workId, PDO::PARAM_INT);

        // A lekérdezés végrehajtása
        $stmt->execute();

        // Az adatok lekérése
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // A teljes összeg kiszámítása
        $total = $row['paid_free'];

        // Az összeg kiíratása
        echo $total;

    } catch (PDOException $e) {
        // Kezeljük a hibákat
        echo "Hiba: " . $e->getMessage();
    }
} else {
    // A munka azonosító (work_id) nincs beállítva a munkamenetben
    echo "Nincs munka azonosító a munkamenetben.";
}
?>
