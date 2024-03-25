<?php
// Az adatbázis kapcsolatfájl beillesztése
include "connect.php";

// Ellenőrizzük, hogy a munka azonosító (work_id) be van-e állítva a munkamenetben
if (isset($_SESSION['work_id'])) {
    // A munka azonosító (work_id) lekérése a munkamenetből
    $workId = $_SESSION['work_id'];

    try {
        // Készítsünk egy SQL utasítást a fizetett és korábban fizetett szabadságok lekérdezésére az adatbázisból
        $sql = "SELECT payed_free, payed_past_free, payed_edu_free, payed_award_free, unpayed_dad_free, unpayed_home_free, unpayed_free FROM users WHERE work_id = :work_id";
        $stmt = $conn->prepare($sql);

        // A munka azonosító (work_id) paraméter összekapcsolása
        $stmt->bindParam(':work_id', $workId, PDO::PARAM_INT);

        // A lekérdezés végrehajtása
        $stmt->execute();

        // Az adatok lekérése
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // A teljes összeg kiszámítása
        $total = $row['payed_free'] + $row['payed_past_free'] + $row['payed_edu_free'] + $row['payed_award_free'] + $row['unpayed_dad_free'] + $row['unpayed_home_free'] + $row['unpayed_free'];

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
