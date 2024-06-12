<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Kérelmeim</title>
    <link rel="stylesheet" href="public/css/styles.css">
</head>
<body>
<?php
// Session ellenőrzése
include "session_check.php";
include "app/helpers/function_get_name.php";
include "app/config/connect.php";

// Ha nincs bejelentkezve, átirányítás a bejelentkezési oldalra
if (!isset($_SESSION['logged'])) {
    header("Location: login_form.php");
    exit;
}
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
<?php include "navigation_bar-top.php"; ?>
<div class="body-container">
    <div class="navbar" id="sidebar">
        <?php include "navigation_bar-side.php"; ?>
    </div>
    <div class="main-content" id="main-content">
        <div class="test_content">
            <div class="profile-container">
                <h1 class="profile-heading">Profil szerkesztése</h1>
                <form action="update_profile.php" method="post">
                    <label for="name">Teljes név:</label>
                    <input type="text" id="name" name="name" value="<?php echo $row['name']; ?>" required>

                    <label for="cim">Lakcím:</label>
                    <input type="text" id="cim" name="cim" value="<?php echo $row['cim']; ?>" required>

                    <label for="tax_number">Adóazonosító:</label>
                    <input type="text" id="tax_number" name="tax_number" value="<?php echo $row['tax_number']; ?>" required>

                    <label for="entity_id">Szervezetszám:</label>
                    <input type="text" id="entity_id" name="entity_id" value="<?php echo $row['entity_id']; ?>" required>

                    <label for="employee_card_number">Alkalmazotti kártyaszám:</label>
                    <input type="text" id="employee_card_number" name="employee_card_number" value="<?php echo $row['employee_card_number']; ?>" required>

                    <button class="action-button" type="submit">
                        <img src="public/images/icons/save_20dp_FILL0_wght400_GRAD0_opsz20.png" alt="Mentés">
                        Mentés
                    </button>
                </form>

            </div>
        </div>
        <div class="footer-div">
            <?php include "app/views/partials/footer.php"; ?>
        </div>
    </div>
</div>
<script src="public/js/collapse.js"></script>
</body>
</html>