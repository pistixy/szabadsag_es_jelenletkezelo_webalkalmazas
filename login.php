<?php
include "session_check.php";
include "connect.php";

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if email and password fields are set
    if (isset($_POST['email']) && isset($_POST['password'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Prepare statement to avoid SQL injection
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $result = $stmt->fetchAll();

        if ($result) {
            $row = $result[0];

            // Verify the password
            if (password_verify($password, $row['password'])) {

                // Set session variables
                $_SESSION['email'] = $row['email'];
                $_SESSION['logged'] = true;
                $_SESSION['work_id'] = $row['work_id'];

                // Check if user is an admin
                $_SESSION['isAdmin'] = (isset($row['position']) && $row['position'] == 1);

                // Redirect to the index page
                header("Location: index.php");
                exit;
            }
        }

        // If authentication fails
        echo "Helytelen felhasználónév vagy jelszó! <a href='login_form.php'>Bejelentkezés újra</a>";
    } else {
        // If email or password field is not set
        echo "Hiányzó adatok! Kérjük, töltse ki az összes mezőt. <a href='login_form.php'>Vissza a bejelentkezéshez</a>";
    }
} else {
    // If the form is not submitted
    header("Location: login_form.php");
    exit;
}
?>
