<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['logged']) && $_SESSION['logged'] === true) {
    $email = $_SESSION['email'];

    include "connect.php";

    try {
        $stmt = $conn->prepare("SELECT position FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $position = trim($result['position']);
            if ($position === 'admin') {
                $_SESSION['isAdmin'] = true;
            } else {
                $_SESSION['isAdmin'] = false;
            }
        } else {
            echo "No result found for email: $email";
            $_SESSION['isAdmin'] = false;
        }
    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();
        $_SESSION['isAdmin'] = false;
    }
}
?>
