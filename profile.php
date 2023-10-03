<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['logged'])) {
    // Redirect to the login page if not logged in
    header("Location: login_form.php");
    exit;
}
include "connect.php";

// Get the logged-in user's email (you may use a different identifier like user ID)
$email = $_SESSION['email'];

// Prepare and execute a SQL query to retrieve user data based on the email
$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // User data found, display it
    $row = $result->fetch_assoc();
    ?>
    <!DOCTYPE html>
    <html lang="hu-HU">
    <head>
        <title>Profil</title>
        <link rel="stylesheet" href="styles.css">
    </head>
    <body>
    <?php include "nav-bar.php"; ?>
    <div class="profile-container">
        <h1 class="profile-heading">Profilod</h1>
        <p class="profile-data"><strong>Email:</strong> <?php echo $row['email']; ?></p>
        <p class="profile-data"><strong>Név:</strong> <?php echo $row['name']; ?></p>
        <p class="profile-data"><strong>Családnév:</strong> <?php echo $row['surname']; ?></p>
        <p class="profile-data"><strong>Telszám:</strong> <?php echo $row['phone']; ?></p>
        <p class="profile-data"><strong>Születési dátum:</strong> <?php echo $row['birthdate']; ?></p>

        <a class="edit-profile-link" href="edit_profile.php">Edit Profile</a>
    </div>
    </body>
    </html>
    <?php
} else {
    // User data not found
    echo "User data not found.";
}

$stmt->close();
$conn->close();
?>
