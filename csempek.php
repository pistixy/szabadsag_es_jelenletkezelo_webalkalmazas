<?php
// csempek oldal
include "check_login.php";

?>
<table class="csempe">
    <tr>
        <td>
            <a>Fennmaradó napok száma: <?php include "days_remaining.php";?></a>
        </td>
        
        <td>
            <a>Szabadnapok száma: <?php include "days_total.php";?></a>
        </td>
        <td>
            <a href="calendar.php">Naptáram</a>
        </td>
        <td>
            <a href="comingtowork.php">Munkába járás</a>
        </td>
        
    </tr>
       <tr>
        
        <?php
        if (isset($_SESSION['logged']) && $_SESSION['is_user'] == false) {

            $workId = $_SESSION['work_id'];
            $stmt = $conn->prepare("SELECT position,kar,szervezetszam FROM users WHERE work_id = :work_id");
            $stmt->bindParam(':work_id', $workId, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            $position = $result['position']; // User beosztása
            $kar = $result['kar']; // User kara
            $szervezetszam = $result['szervezetszam']; // User szervezetszama
            // a felhasználó jogosultságaihoz mérten jelenjenek meg a értesítések
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
                    echo "<td>";
                    echo "<a href='incomming_requests.php' class='$notificationClass'>Bejövő kérelmek</a>";
                    echo "</td>";
                    break;
                case "dekan":
                    $karPattern = '%' . $kar . '%';
                    $pendingRequestSql = "SELECT COUNT(*) AS pendingcount FROM requests 
                        WHERE request_status = 'pending' 
                        AND (to_whom LIKE :kar)";
                    $pendingRequestStmt = $conn->prepare($pendingRequestSql);
                    $pendingRequestStmt->bindParam(':kar', $karPattern);

                    if ($pendingRequestStmt->execute()) {
                        $pendingRequestResult = $pendingRequestStmt->fetch(PDO::FETCH_ASSOC);
                        if ($pendingRequestResult) {
                            $hasPendingRequests = $pendingRequestResult['pendingcount'] > 0;
                        } else {
                            $hasPendingRequests = false;
                        }
                    } else {
                        // hibakezelés
                        $errorInfo = $pendingRequestStmt->errorInfo();
                        echo "SQL Error: " . $errorInfo[2];
                        $hasPendingRequests = false;
                    }

                    $notificationClass = $hasPendingRequests ? "notification" : "";
                    echo "<td><a href='incomming_requests.php' class='$notificationClass'>Bejövő kérelmek</a></td>";
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
                    echo "<td><a href='incomming_requests.php' class='$notificationClass'>Bejövő kérelmek</a></td>";
                    break;

            }
        }
        ?>
        <td>
            <?php
            if (isset($_SESSION['logged']) && $_SESSION['is_user']==false) {
                $workId = $_SESSION['work_id'];
                $stmt = $conn->prepare("SELECT szervezetszam FROM users WHERE work_id = :work_id");
                $stmt->bindParam(':work_id', $workId, PDO::PARAM_INT);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $szervezetszam = $result['szervezetszam'] ?? '';

                echo '<div class="csempe-item">';
                echo '<h class="csempe-heading">Szervezetszám:</h>';
                echo '<form action="workers.php" method="post" class="csempe-form">';
                echo '<input type="text" name="szervezetszam" value="' . htmlspecialchars($szervezetszam) . '" />';
                echo '<input type="submit" value="Dolgozók lekérdezése" class="csempe-button">';
                echo '</form>';
                echo '</div>';
            }
            ?>
        </td>
    </tr>
    
</table>