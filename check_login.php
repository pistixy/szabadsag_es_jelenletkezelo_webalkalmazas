<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$loggedIn = false;
$isAdmin = false;

if (isset($_SESSION['logged']) && $_SESSION['logged'] === true) {
    $loggedIn = true;
    $email = $_SESSION['email'];

    include "connect.php";

    $stmt = $conn->prepare("SELECT position FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($result) > 0) {
        $row = $result[0];
        if ($row['position'] == 1) {
            $isAdmin = true;
        }
    }
}

$_SESSION['isAdmin'] = $isAdmin;
?>
