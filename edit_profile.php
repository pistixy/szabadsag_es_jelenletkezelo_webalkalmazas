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

$stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
$stmt->bindParam(':email', $email);
$stmt->execute();
$result = $stmt->fetchAll();

if (count($result) > 0) {
    $row = $result[0];
    // Now you can use $row to access user details
} else {
    echo "No user found with the specified email.";
}
?>

<div class="profile-container">
    <h1 class="profile-heading">Profil szerkesztése</h1>
    <form action="update_profile.php" method="post">
        <label for="name">Teljes név:</label>
        <input type="text" id="name" name="name" value="<?php echo $row['name']; ?>" required>

        <label for="cim">Lakcím:</label>
        <input type="text" id="cim" name="cim" value="<?php echo $row['cim']; ?>" required>

        <label for="adoazonosito">Adóazonosító:</label>
        <input type="text" id="adoazonosito" name="adoazonosito" value="<?php echo $row['adoazonosito']; ?>" required>

        <label for="szervezetszam">Szervezetszám:</label>
        <input type="text" id="szervezetszam" name="szervezetszam" value="<?php echo $row['szervezetszam']; ?>" required>

        <label for="alkalmazottikartyaszama">Alkalmazotti kártyaszám:</label>
        <input type="text" id="alkalmazottikartyaszama" name="alkalmazottikartyaszama" value="<?php echo $row['alkalmazottikartya']; ?>" required>


        <input type="submit" value="Mentés">
    </form>
</div>
<?php
include "footer.php";
?>
</body>
</html>
