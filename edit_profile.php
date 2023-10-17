<!DOCTYPE html>
<html lang="hu-HU">
<head>
    <meta charset="UTF-8">
    <title>Szerkesztés</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php
session_start();


if (!isset($_SESSION['logged'])) {
    header("Location: login_form.php");
    exit;
}

include "connect.php";

$email = $_SESSION['email'];

$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
}
?>
<div class="profile-container">
    <h1 class="profile-heading">Profil szerkesztése</h1>
    <form action="update_profile.php" method="post">
        <label for="name">Név:</label>
        <input type="text" id="name" name="name" value="<?php echo $row['name']; ?>" required>

        <label for="surname">Családnév:</label>
        <input type="text" id="surname" name="surname" value="<?php echo $row['surname']; ?>" required>

        <label for="phone">Telefonszám:</label>
        <input type="text" id="phone" name="phone" value="<?php echo $row['phone']; ?>" required>

        <label for="birthdate">Születési dátum:</label>
        <input type="date" id="birthdate" name="birthdate" value="<?php echo $row['birthdate']; ?>" required>

        <input type="submit" value="Mentés">
    </form>
</div>
<?php
include "footer.php";
?>
</body>
</html>
