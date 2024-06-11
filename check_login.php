<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ellenőrizzük, hogy a felhasználó be van-e jelentkezve
if (isset($_SESSION['logged']) && $_SESSION['logged'] === true) {
    $email = $_SESSION['email'];

    // Adatbázis kapcsolat létrehozása
    include "app/config/connect.php";
    try {
        // Felhasználó pozíciójának lekérdezése az email alapján
        $stmt = $conn->prepare("SELECT position FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Ha eredményt találtunk
        if ($result) {
            $_SESSION['position'] = $result['position'];

            // Felhasználó pozíciójának alapján döntés
            if ($result['position'] == 'admin') {
                $_SESSION['isAdmin'] = true; // admin
            } 
            if ($result['position'] == 'user') {
                $_SESSION['is_user'] = true; // Felhasználó
            } else {
                $_SESSION['is_user'] = false; // Nem felhasználó
            }
        } else {
            echo "Nincs eredmény az emailhez: $email";
        }
    } catch (PDOException $e) {
        echo "Adatbázis hiba: " . $e->getMessage();
    }
}
//echo "                                             ",$_SESSION['position']; //debughoz adatok
//if($_SESSION['is_user'] == true) {
//    echo "                                             ",$_SESSION['position'];
//}
//if($_SESSION['is_user'] == false) {
//    echo "                                             ",$_SESSION['position'];
//}
?>
