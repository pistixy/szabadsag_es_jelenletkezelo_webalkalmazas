<?php
session_start(); // start the session
include "connect.php";

// Check if the username and password exist in the database
$email = $_POST['email'];
$password = $_POST['password'];

$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    // Verify the hashed password
    if (password_verify($password, $row['password'])) {
        // The password is correct, set session variables
        $_SESSION['email'] = $row['email'];
        $_SESSION['email'] = $email;
        $_SESSION['logged'] = true;
        header("Location: ../index.php");
        exit;
    }
}

// The username and password are incorrect
echo "Helytelen felhasználónév vagy jelszó! <a href='login_form.php'>Bejelentkezés újra</a>";

$stmt->close();
$conn->close();
?>
