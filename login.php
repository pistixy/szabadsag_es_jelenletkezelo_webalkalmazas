<?php
session_start();
include "connect.php";

$email = $_POST['email'];
$password = $_POST['password'];

$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    if (password_verify($password, $row['password'])) {

        $_SESSION['email'] = $row['email'];
        $_SESSION['logged'] = true;
        $_SESSION['WORKID'] = $row['WORKID'];

        if (isset($row['admin']) && $row['admin'] == 1) {
            $_SESSION['isAdmin'] = true;
        } else {
            $_SESSION['isAdmin'] = false;
        }

        header("Location: index.php"); // Use "Location" to properly redirect
        exit;
    }
}

echo "Helytelen felhasználónév vagy jelszó! <a href='login_form.php'>Bejelentkezés újra</a>";

$stmt->close();
$conn->close();
?>
