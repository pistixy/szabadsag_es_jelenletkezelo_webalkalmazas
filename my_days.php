<?php
include "session_check.php";
include "connect.php";
include "function_get_status_name.php";
// Check if the user is logged in
if (!isset($_SESSION['logged']) || !isset($_SESSION['work_id'])) {
    header("Location: login_form.php");
    exit;
}
$userWorkID = $_SESSION['work_id'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Messages</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="body-container">
    <div class="navbar">
        <?php
        include "nav-bar.php";
        ?>
    </div>
    <div class="main-content">
        <?php
        // Assume $work_id contains the work_id of the user
        // Fetch user data from the database
        $stmt = $conn->prepare("SELECT * FROM users WHERE work_id = :work_id");
        $stmt->bindParam(':work_id', $userWorkID);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        ?>

        <div class="my-days">
            <?php if ($user): ?>
                <h2>Szabadnapjaim állása</h2>
                <ul>
                    <?php foreach ($user as $key => $value): ?>
                        <?php if ($key !== 'work_id' && $key !== 'name' && $key !== 'email' && $key !== 'password' && $key !== 'cim' && $key !== 'adoazonosito' && $key !== 'szervezetszam' && $key !== 'alkalmazottikartya' && $key !== 'position' && $key !== 'profile_picture' && $key !== 'kar'): ?>
                            <li><?php echo getStatusName($key) . ": " . $value; ?></li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>User not found</p>
            <?php endif; ?>
        </div>

        <div class="footer-div">
            <?php
            include "footer.php";
            ?>
        </div>
    </div>
</div>
</body>
</html>