<!DOCTYPE html>
<html lang="hu-HU">
<head>
    <meta charset="UTF-8">
    <title>Szerkesztés</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php
// Session ellenőrzése
include "session_check.php";

// Ha nincs bejelentkezve, átirányítás a bejelentkezési oldalra
if (!isset($_SESSION['logged'])) {
    header("Location: login_form.php");
    exit;
}

// Adatbázis kapcsolat létrehozása
include "connect.php";

// Bejelentkezett felhasználó e-mail címének lekérdezése a munkamenetből
$email = $_SESSION['email'];

// Felhasználó adatainak lekérdezése az adatbázisból az e-mail cím alapján
$stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
$stmt->bindParam(':email', $email);
$stmt->execute();
$result = $stmt->fetchAll();

// Ha találat van az adatbázisban az e-mail cím alapján
if (count($result) > 0) {
    $row = $result[0];
    // A $row változóban tárolt adatok használata a felhasználó részleteinek eléréséhez
} else {
    echo "Nincs felhasználó a megadott e-mail címmel.";
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
// Lábléc beillesztése
include "footer.php";
?>
</body>
</html>
