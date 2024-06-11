<?php
include "session_check.php";
include "app/config/connect.php";
include "app/helpers/function_get_name.php";

if (!isset($_SESSION['logged'])) {
    header("Location: login_form.php");
    exit;
}
// Felhasználó munkaazonosítójának lekérése a munkamenetből
$userWorkID = $_SESSION['work_id'];

if (isset($_GET['work_id'])) {
    // Munkaazonosító lekérése az URL paraméterekből
    $work_id = $_GET['work_id'];

    // Most már használhatod a $work_id változót a kódodban szükség szerint
    //echo "             Work ID: " . $work_id;
} else {
    // Kezeljük az esetet, amikor a munkaazonosító nincs megadva az URL-ben
    echo "Nincs munkaazonosító megadva az URL-ben.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Szabadnapjaim</title>
    <link rel="stylesheet" href="public/css/styles.css">
</head>
<body>
<?php include "navigation_bar-top.php"; ?>
<div class="body-container">
    <div class="navbar" id="sidebar">
        <?php include "navigation_bar-side.php"; ?>
    </div>
    <div class="main-content" id="main-content">
        <?php
        // Feltételezzük, hogy a $work_id tartalmazza a felhasználó munkaazonosítóját
        // Felhasználói adatok lekérése az adatbázisból
        $stmt = $conn->prepare("SELECT * FROM users WHERE work_id = :work_id");
        $stmt->bindParam(':work_id', $work_id);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        ?>

        <div class="test_content">
            <div class="holidays">
                <?php
                // Ellenőrizze, hogy a felhasználó létezik-e
                if ($user):
                    // Ellenőrizze, hogy a felhasználó saját magáról kérdezi-e az adatokat
                    if ($userWorkID == $work_id) {
                        echo "<h2>Szabadnapjaim állása</h2>";
                    } else {
                        // Ha nem a saját adatokról van szó, akkor a felhasználó nevét jelenítse meg
                        echo "<h2><a href='profile.php?work_id=" . $work_id . "'>" . $user['name'] . "</a> szabadnapjainak állása</h2>";
                    }
                    ?>
                    <div class="table-container">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Típus</th>
                                <th>Mennyiség</th>
                                <th>Műveletek</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($user as $key => $value): ?>
                                <?php if ($key !== 'work_id' && $key !== 'name' && $key !== 'email' && $key !== 'password' && $key !== 'cim' && $key !== 'adoazonosito' && $key !== 'szervezetszam' && $key !== 'alkalmazottikartya' && $key !== 'position' && $key !== 'profile_picture' && $key !== 'kar'): ?>
                                    <tr>
                                        <!-- Státusz nevének lekérése és megjelenítése -->
                                        <td><?php echo getName($key); ?></td>
                                        <td><?php echo $value; ?></td>
                                        <td class="center-content">
                                            <!-- Státusz növelése gomb -->
                                            <?php if (in_array($key, ['paid_free']) && ($user['position'] === 'admin' )): ?>
                                                <form action="increase_day.php" method="post" style="display: inline;">
                                                    <input type="hidden" name="work_id" value="<?php echo $work_id; ?>">
                                                    <input type="hidden" name="status" value="<?php echo $key; ?>">
                                                    <button class="action-button" type="submit"><img src="public/images/icons/add_20dp_FILL0_wght400_GRAD0_opsz20.png" alt="Add"></button>
                                                </form>
                                                <form action="decrease_day.php" method="post" style="display: inline;">
                                                    <input type="hidden" name="work_id" value="<?php echo $work_id; ?>">
                                                    <input type="hidden" name="status" value="<?php echo $key; ?>">
                                                    <button class="action-button" type="submit"><img src="public/images/icons/remove_20dp_FILL0_wght400_GRAD0_opsz20.png" alt="Remove"></button>
                                                </form>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p>A felhasználó nem található</p>
                <?php endif; ?>
            </div>
        </div>
        <div class="footer-div">
            <?php include "footer.php"; ?>
        </div>
    </div>
</div>
<script src="public/js/collapse.js"></script>
</body>
</html>


