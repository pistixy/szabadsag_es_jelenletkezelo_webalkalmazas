<?php
// Ellenőrizzük, hogy a form űrlap elküldve lett-e
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ellenőrizzük, hogy az összes szükséges adat rendelkezésre áll-e
    if (isset($_POST['work_id'], $_POST['status'])) {
        // Kinyerjük az adatokat az űrlapról
        $work_id = $_POST['work_id'];
        $status = $_POST['status'];

        include "app/config/connect.php";
        try {
            // Ellenőrizzük az állapot oszlop nevét, hogy megelőzzük az SQL injekciót
            $allowed_statuses = ['paid_free'];
            if (!in_array($status, $allowed_statuses)) {
                throw new Exception("Érvénytelen státusz oszlop.");
            }

            // Lekérdezés az adott státusz oszlop aktuális értékének lekérésére
            $stmt = $conn->prepare("SELECT $status FROM users WHERE work_id = :work_id");
            $stmt->bindParam(':work_id', $work_id);
            $stmt->execute();
            $result = $stmt->fetchColumn();

            // Ellenőrizzük, hogy az állapot már nullánál kisebb-e
            if ($result <= 0) {
                // Átirányítás vissza a honlapra az űrlapból kapott hibaüzenettel
                header("Location: {$_SERVER['HTTP_REFERER']}?error=2");
                exit;
            }

            // Adatbázis frissítése az adott státusz oszlop dekrementálásával
            $stmt = $conn->prepare("UPDATE users SET $status = $status - 1 WHERE work_id = :work_id");
            $stmt->bindParam(':work_id', $work_id);
            $stmt->execute();

            // Átirányítás vissza a honlapra, ahonnan az űrlapot elküldték
            header("Location: {$_SERVER['HTTP_REFERER']}");
            exit;
        } catch (PDOException $e) {
            // Felhasználóbarát hibaüzenet megjelenítése
            echo "Hiba történt: " . $e->getMessage();
        } catch (Exception $e) {
            // Átirányítás vissza a honlapra az űrlapból kapott hibaüzenettel
            header("Location: {$_SERVER['HTTP_REFERER']}?error=3");
            exit;
        }
    } else {
        // Átirányítás vissza a honlapra az űrlapból kapott hibaüzenettel
        header("Location: {$_SERVER['HTTP_REFERER']}?error=1");
        exit;
    }
} else {
    // Ha valaki megpróbálja közvetlenül hozzáférni ehhez az oldalhoz, átirányítjuk a főoldalra
    header("Location: index.php");
    exit;
}
?>
