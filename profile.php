<?php
include "session_check.php";
include "nav-bar.php";

if (!isset($_SESSION['logged'])) {
    header("Location: login_form.php");
    exit;
}

include "connect.php";

// Check if work_id is provided in the URL, otherwise use the session's work_id
$work_id = isset($_GET['work_id']) ? $_GET['work_id'] : $_SESSION['work_id'];

$stmt = $conn->prepare("SELECT * FROM users WHERE work_id = :work_id");
$stmt->bindParam(':work_id', $work_id);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if ($result) {
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
            if (!empty($result['profile_picture'])) {
                $profilePicturePath = 'profile_pictures/' . $result['profile_picture'];
                echo '<img src="' . $profilePicturePath . '" alt="Profile Picture">';
            }
            ?>
        </div>
        <div class="profile-details">
            <?php
            if ($work_id == $_SESSION['work_id']) {
                echo '<h1 class="profile-heading">Profilod</h1>';
            } else {
                echo '<h1 class="profile-heading">' . htmlspecialchars($result['name']) . ' profilja</h1>';
            }
            ?>
            <p class="profile-data"><strong>Naptár: </strong><a href="calendar.php?work_id= <?php echo $result['work_id']?>"><?php echo $result['name']?> naptára</a> </p>
            <p class="profile-data"><strong>Email:</strong> <?php echo $result['email']; ?></p>
            <p class="profile-data"><strong>Teljes név:</strong> <?php echo $result['name']; ?></p>
            <p class="profile-data"><strong>work_id:</strong> <?php echo $result['work_id']; ?></p>
            <p class="profile-data"><strong>Lakcím:</strong> <?php echo $result['cim']; ?></p>
            <p class="profile-data"><strong>Adóazonosító:</strong> <?php echo $result['adoazonosito']; ?></p>
            <p class="profile-data"><strong>Szervezetszám:</strong> <?php echo $result['szervezetszam']; ?></p>
            <p class="profile-data"><strong>Alkalmazotti kártyaszám:</strong> <?php echo $result['alkalmazottikartya']; ?></p>
            <p class="profile-data"><strong>Beosztás:</strong> <?php echo $result['position']; ?></p>
            <?php
            // Only show the edit link if viewing own profile
            if ($work_id == $_SESSION['work_id']) {
                echo '<a class="edit-profile-link" href="edit_profile.php">Profil szerkesztése</a>';
            }
            ?>
        </div>
        <?php
        // Only show the upload form if viewing own profile
        if ($work_id == $_SESSION['work_id']) {
            ?>
            <form action="upload_profile_picture.php" method="POST" enctype="multipart/form-data" class="profile-picture-upload-form">
                <label>Profilkép feltöltése:</label>
                <input type="file" accept="image/gif, image/jpg, image/png, image/jpeg" name="profile_picture">
                <input type="submit" name="upload_profile_picture" value="Feltöltés">
            </form>
            <?php
        }
        ?>
        <?php

        include "csuszka.php";
        ?>
    </div>
    </body>
    </html>
    <?php
} else {
    echo "User data not found.";
}

include "footer.php";
?>
