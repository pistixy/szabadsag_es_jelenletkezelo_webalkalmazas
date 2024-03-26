<?php
include "session_check.php"; // Ellenőrizze, hogy a munkamenet aktív-e
include "connect.php"; // Adatbáziskapcsolat fájl beillesztése

if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];

    // A helyes oszlopnevet használja az Ön PostgreSQL adatbázisából
    $stmt = $conn->prepare("SELECT work_id FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $result = $stmt->fetchAll();

    if (count($result) == 1) {
        $row = $result[0];
        $userWorkId = $row['work_id']; // Állítsa be az oszlopnevet, hogy megfeleljen az adatbázisának

        // Naptáradatok kitöltése az elmúlt 1 évben és a következő 20 évben
        $currentDate = new DateTime();
        $pastLimit = 150; // Adatok kitöltése az elmúlt idoben
        $futureLimit = 365 * 2; // Adatok kitöltése a következő évre

        // Start a transaction
        $conn->beginTransaction();

        try {
            $stmt = $conn->prepare("INSERT INTO calendar (work_id, date, day_status) VALUES (:work_id, :date, :day_status)");

            // Insert for past and future in a single loop
            for ($i = -$pastLimit; $i < $futureLimit; $i++) {
                $date = date("Y-m-d", strtotime($currentDate->format("Y-m-d") . " $i days"));
                $day_status = date('N', strtotime($date)) <= 5 ? "work_day" : "weekend";

                $stmt->bindParam(':work_id', $userWorkId);
                $stmt->bindParam(':date', $date);
                $stmt->bindParam(':day_status', $day_status);
                $stmt->execute();
            }

            // Commit the transaction
            $conn->commit();
            echo "Naptár visszamenőleg és a jövőre nézve feltöltve.";

        } catch (PDOException $e) {
            // Rollback the transaction on error
            $conn->rollback();
            echo "Hiba az adatok beillesztésénél: " . $e->getMessage();
        }
    } else {
        echo "A felhasználó nem található az adatbázisban.";
    }
} else {
    echo "Nincs beállított felhasználói munkamenet.";
}
?>
