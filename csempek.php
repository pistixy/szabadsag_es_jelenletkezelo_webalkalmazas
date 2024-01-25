<?php
include "check_login.php";
?>
<table class="csempe">
    <tr>
        <td>
            <a href="profile.php">Profil megtekintése</a>
        </td>
        <td>
            <a href="calendar.php">Naptáram</a>
        </td>
        <td>
            <a href="comingtowork.php">Munkába járás</a>
        </td>


        <td>
            <a href="https://www.uni.sze.hu"> Széchenyi Egyetem oldala</a>
        </td>
    </tr>
    <tr>
        <td>
            <a href="hr_segedlet.php">HR segédlet</a>
        </td>
        <td>
            <a href="logout.php">Kijelentkezés</a>
        </td>



        <td>
            <?php
            if (isset($_SESSION['logged'])) {
                echo ' <a href="my_messages.php">Üzeneteim</a>';
            }

            ?>
        </td>
        <td>
            <?php
            if (isset($_SESSION['logged'])){
                echo ' <a href="my_requests.php">Kérelmeim</a>';
            }

            ?>
        </td>
    </tr>
    <tr>
        <td>
            <?php
            if (isset($_SESSION['logged']) && $_SESSION['isAdmin']) {
                echo ' <a href="letszamjelentes.php">Letszámjelentés</a>';
            }

            ?>
        </td>
        <td>
            <?php
            if (isset($_SESSION['logged']) && $_SESSION['isAdmin']) {
                echo ' <a href="jelenletiiv.php">Jelenléti Ív</a>';
            }

            ?>
        </td>
        <?php
        if (isset($_SESSION['logged']) && $_SESSION['isAdmin']) {
            //echo "Logged in as Admin"; // Debugging

            $position = $_SESSION['position']; // Ensure this is set
            //echo "Position: $position"; // Debugging

            $pendingRequestSql = "SELECT COUNT(*) AS pendingcount FROM requests WHERE request_status = 'pending' AND to_whom = :position";
            $pendingRequestStmt = $conn->prepare($pendingRequestSql);
            $pendingRequestStmt->bindParam(':position', $position);

            if ($pendingRequestStmt->execute()) {
                $pendingRequestResult = $pendingRequestStmt->fetch(PDO::FETCH_ASSOC);
                //echo "<pre>"; print_r($pendingRequestResult); echo "</pre>"; // Debugging

                if ($pendingRequestResult && isset($pendingRequestResult['pendingcount'])) {
                    $hasPendingRequests = $pendingRequestResult['pendingcount'] > 0;
                    //echo "Has Pending Requests: $hasPendingRequests"; // Debugging
                } else {
                    //echo "No Pending Requests or missing 'pendingCount'"; // Debugging
                    $hasPendingRequests = false;
                }
            } else {
                //echo "SQL Error"; // Debugging
                $hasPendingRequests = false;
            }

            $notificationClass = $hasPendingRequests ? "notification" : "";
            echo "<td>";
            echo "<a href='incomming_requests.php' class='$notificationClass'>Bejövő kérelmek</a>";
            echo "</td>";
        } else {
            //echo "Not logged in as Admin"; // Debugging
        }

        ?>


        <td>
            <?php
            if (isset($_SESSION['logged']) && $_SESSION['isAdmin']) {
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