<?php
session_start();
include "connect.php";

$email = $_POST['email'];
$password = $_POST['password'];

$stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
$stmt->bindParam(':email', $email);
$stmt->execute();

$result = $stmt->fetchAll();

if ($result) {
    $row = $result[0];

    if (password_verify($password, $row['password'])) {

        $_SESSION['email'] = $row['email'];
        $_SESSION['logged'] = true;
        $_SESSION['work_id'] = $row['work_id'];

        if (isset($row['position']) && $row['position'] == 1) {
            $_SESSION['isAdmin'] = true;
            echo "admin";
        } else {
            $_SESSION['isAdmin'] = false;
            echo "nemadmin";
        }

        header("Location: index.php");
        exit;
    }
}

echo "Helytelen felhasználónév vagy jelszó! <a href='login_form.php'>Bejelentkezés újra</a>";

?>
