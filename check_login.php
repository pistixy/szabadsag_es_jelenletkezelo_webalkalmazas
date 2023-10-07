<?php
$loggedIn = false;
$isAdmin = false;

if (isset($_SESSION['logged']) && $_SESSION['logged'] === true) {
    $loggedIn = true;
    $username = $_SESSION['email'];

    include "connect.php";

    $email = $_SESSION['email'];

    $stmt = $conn->prepare("SELECT admin FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($row['admin'] == 1) {
            $isAdmin = true;
        }
    }

    $stmt->close();
    $conn->close();
}

$_SESSION['isAdmin'] = $isAdmin;
?>
