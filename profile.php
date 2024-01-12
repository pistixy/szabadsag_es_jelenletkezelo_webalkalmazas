<?php
session_start();
include "nav-bar.php";

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
    ?>
    <!DOCTYPE html>
    <html lang="hu-HU">
    <head>
        <title>Profil</title>
        <link rel="stylesheet" href="styles.css">
    </head>
    <body>
    <div class="profile-container">
        <div class="profile-picture">
            <?php
            if (!empty($row['profile_picture'])) {
                $profilePicturePath = 'profile_pictures/' . $row['profile_picture'];
                echo '<img src="' . $profilePicturePath . '" alt="Profile Picture">';
            }
            ?>
        </div>
        <div class="profile-details">
            <h1 class="profile-heading">Profilod</h1>
            <p class="profile-data"><strong>Email:</strong> <?php echo $row['email']; ?></p>
            <p class="profile-data"><strong>Teljes név:</strong> <?php echo $row['name']; ?></p>
            <p class="profile-data"><strong>WORKID:</strong> <?php echo $row['work_id']; ?></p>
            <p class="profile-data"><strong>Lakcím:</strong> <?php echo $row['cim']; ?></p>
            <p class="profile-data"><strong>Adóazonosító:</strong> <?php echo $row['adoazonosito']; ?></p>
            <p class="profile-data"><strong>Szervezetszám:</strong> <?php echo $row['szervezetszam']; ?></p>
            <p class="profile-data"><strong>Alkalmazotti kártyaszám:</strong> <?php echo $row['alkalmazottikartya']; ?></p>
            <p class="profile-data"><strong>Beosztás:</strong><?php echo $row['position']; ?></p>
            <a class="edit-profile-link" href="edit_profile.php">Profil szerkesztése</a>
        </div>
        <form action="upload_profile_picture.php" method="POST" enctype="multipart/form-data" class="profile-picture-upload-form">
            <label>Profilkép feltöltése:</label>
            <input type="file" accept="image/gif, image/jpg, image/png, image/jpeg" name="profile_picture">
            <input type="submit" name="upload_profile_picture" value="Feltöltés">
        </form>
    </div>
    </body>
    </html>
    <?php
} else {
    echo "User data not found.";
}

?>

<?php
include "footer.php";
?>
