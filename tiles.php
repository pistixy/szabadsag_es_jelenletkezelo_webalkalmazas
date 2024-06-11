<?php
// csempek oldal
include "check_login.php";
?>

<div class="csempek-container">
    <div class="csempe-item">
        <a>Fennmaradó napok száma: <?php include "days_remaining.php"; ?></a>
    </div>
    <div class="csempe-item">
        <a>Szabadnapok száma: <?php include "days_total.php"; ?></a>
    </div>
    <div class="csempe-item">
        <a href="calendar.php">Naptáram</a>
    </div>
    <div class="csempe-item">
        <a href="coming_to_work.php">Munkába járás</a>
    </div>
    <?php
    if (isset($_SESSION['logged']) && $_SESSION['is_user'] == false) {
        $workId = $_SESSION['work_id'];
        $stmt = $conn->prepare("SELECT position, kar, szervezetszam FROM users WHERE work_id = :work_id");
        $stmt->bindParam(':work_id', $workId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $position = $result['position']; // User's position
        $kar = $result['kar']; // User's kar
        $szervezetszam = $result['szervezetszam']; // User's szervezetszam
        // Display notifications based on user permissions
        switch ($position) {
            case "admin":
                $pendingRequestSql = "SELECT COUNT(*) AS pendingcount FROM requests 
                      WHERE request_status = 'pending' 
                      AND (to_whom LIKE :admin)";
                $pendingRequestStmt = $conn->prepare($pendingRequestSql);
                $adminPattern = '%admin%';
                $pendingRequestStmt->bindParam(':admin', $adminPattern);
                if ($pendingRequestStmt->execute()) {
                    $pendingRequestResult = $pendingRequestStmt->fetch(PDO::FETCH_ASSOC);
                    $hasPendingRequests = $pendingRequestResult && $pendingRequestResult['pendingcount'] > 0;
                } else {
                    $hasPendingRequests = false;
                }
                $notificationClass = $hasPendingRequests ? "notification" : "";
                echo "<div class='csempe-item'>";
                echo "<a href='incomming_requests.php' class='$notificationClass'>Bejövő kérelmek</a>";
                echo "</div>";
                break;
            case "dekan":
                // Handle dekan case here
                break;
            case "tanszekvezeto":
                $karPattern = '%' . $kar . '%';
                $szervezetszamPattern = '%' . $szervezetszam . '%';

                $pendingRequestSql = "SELECT COUNT(*) AS pendingcount FROM requests 
                      WHERE request_status = 'pending' 
                      AND to_whom LIKE :kar 
                      AND to_whom LIKE :szervezetszam";
                $pendingRequestStmt = $conn->prepare($pendingRequestSql);
                $pendingRequestStmt->bindParam(':kar', $karPattern);
                $pendingRequestStmt->bindParam(':szervezetszam', $szervezetszamPattern);

                if ($pendingRequestStmt->execute()) {
                    $pendingRequestResult = $pendingRequestStmt->fetch(PDO::FETCH_ASSOC);
                    $hasPendingRequests = $pendingRequestResult && $pendingRequestResult['pendingcount'] > 0;
                } else {
                    $hasPendingRequests = false;
                }

                $notificationClass = $hasPendingRequests ? "notification" : "";
                echo "<div class='csempe-item'><a href='incomming_requests.php' class='$notificationClass'>Bejövő kérelmek</a></div>";
                break;
        }
    }
    ?>
    <?php
    if (isset($_SESSION['logged'])) {
        // Get the work_id from the session
        $workId = $_SESSION['work_id'];

        // Prepare and execute the statement to get the user's position
        $stmt = $conn->prepare("SELECT position, kar, szervezetszam FROM users WHERE work_id = :work_id");
        $stmt->bindParam(':work_id', $workId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Determine what to display based on the user's position
        switch ($result['position']) {
            case 'admin':
                echo '<div class="csempe-item">';
                echo '<h2 class="csempe-heading">Keresés:</h2>';
                echo '<form action="workers.php" method="post" class="csempe-form">';
                echo '<input type="text" name="feltetel" />';
                echo '<button type="submit" class="csempe-button">Dolgozók lekérdezése</button>';
                echo '</form>';
                echo '</div>';
                break;
            case 'dekan':
                echo '<div class="csempe-item">';
                echo '<h2 class="csempe-heading">Kar:</h2>';
                echo '<form action="workers.php" method="post" class="csempe-form">';
                echo '<input type="text" name="feltetel" value="' . htmlspecialchars($result['kar']) . '" readonly />';
                echo '<button type="submit" class="csempe-button">Dolgozók lekérdezése</button>';
                echo '</form>';
                echo '</div>';
                break;
            case 'tanszekvezeto':
                echo '<div class="csempe-item">';
                echo '<h2 class="csempe-heading">Szervezetszám:</h2>';
                echo '<form action="workers.php" method="post" class="csempe-form">';
                echo '<input type="text" name="feltetel" value="' . htmlspecialchars($result['szervezetszam']) . '" readonly />';
                echo '<button type="submit" class="csempe-button">Dolgozók lekérdezése</button>';
                echo '</form>';
                echo '</div>';
                break;
            case 'user':
                // User: Do not display anything
                break;
        }
    }
    ?>
    <?php
    if (isset($_SESSION['logged']) && ($_SESSION['position'] == "dekan" or $_SESSION['position'] == "admin")) {
        echo "<div class='csempe-item'>";
        echo "<a href='summation_logic.php'>Összesítők</a>";
        echo "</div>";
    }
    ?>
    <?php
    if (isset($_SESSION['logged']) && $_SESSION['position'] == "admin") {
        echo "<div class='csempe-item'>";
        echo "<a href='downloadable_files.php'>Letölthető beosztások</a>";
        echo "</div>";
    }
    ?>
</div>
