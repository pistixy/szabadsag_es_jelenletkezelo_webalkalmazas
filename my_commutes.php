<?php
include "session_check.php";
include "connect.php";


// Check if the user is logged in
if (!isset($_SESSION['logged']) || !isset($_SESSION['work_id'])) {
    header("Location: login_form.php");
    exit;
}

$userWorkID = $_SESSION['work_id'];

// Prepare a SQL statement to retrieve all messages received by the logged-in user along with the date from the calendar
$sql = "SELECT * FROM commute
        WHERE work_id = :userWorkID 
        ORDER BY date DESC";

$stmt = $conn->prepare($sql);
$stmt->bindParam(':userWorkID', $userWorkID, PDO::PARAM_INT);
$stmt->execute();
$commutes = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
        <div class="my-commutes">
            <h1>Munkábajárásaim</h1>

            <?php if (!empty($commutes)): ?>
                <table>
                    <tr>
                        <th>Commute ID</th>
                        <th>Work ID</th>
                        <th>Honnan?</th>
                        <th>Hová?</th>
                        <th>Hogyam?</th>
                        <th>Dátum</th>
                        <th>Fájlnév</th>
                        <th>Ár</th>
                        <th>Távolság (km)</th>
                    </tr>
                    <?php foreach ($commutes as $commute): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($commute['commute_id']); ?></td>
                            <td><?php echo htmlspecialchars($commute['work_id']); ?></td>
                            <td><?php echo htmlspecialchars($commute['honnan']); ?></td>
                            <td><?php echo htmlspecialchars($commute['hova']); ?></td>
                            <td><?php echo htmlspecialchars($commute['how']); ?></td>
                            <td><?php echo htmlspecialchars($commute['date']); ?></td>
                            <td><?php echo htmlspecialchars($commute['filename']); ?></td>
                            <td><?php echo htmlspecialchars($commute['price']); ?></td>
                            <td><?php echo htmlspecialchars($commute['km']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php else: ?>
                <p>Még nem rögzített munkábajárást.</p>
            <?php endif; ?>
            <p>

            </p>

            <form action="export_commutes_to_pdf.php" method="post">
                <button type="submit" name="export_commutes_to_pdf">PDF generálása</button>
            </form>
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