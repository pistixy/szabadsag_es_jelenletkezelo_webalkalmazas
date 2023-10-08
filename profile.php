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
            <p class="profile-data"><strong>Név:</strong> <?php echo $row['name']; ?></p>
            <p class="profile-data"><strong>Családnév:</strong> <?php echo $row['surname']; ?></p>
            <p class="profile-data"><strong>Telszám:</strong> <?php echo $row['phone']; ?></p>
            <p class="profile-data"><strong>Születési dátum:</strong> <?php echo $row['birthdate']; ?></p>
            <p class="profile-data"><strong>WORKID:</strong> <?php echo $row['WORKID']; ?></p>
            <a class="edit-profile-link" href="edit_profile.php">Edit Profile</a>
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

$stmt->close();
$conn->close();
?>
<?php
include "footer.php";
?>
