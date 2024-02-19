<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Date Details</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="body-container">
        <div class="navbar">

        </div>
        <div class="main-content">
            <div class="date-details-page">
                <?php
                include "session_check.php";
                include "connect.php";
                include "nav-bar.php";
                include "check_login.php";
                include "function_get_status_name.php";

                if (isset($_GET['view'])) {
                    $currentView = $_GET['view'];
                }

                if (!isset($_SESSION['logged'])) {
                    header("Location: login_form.php");
                    exit;
                }

                if (isset($_GET['date'])) {
                    $clickedDate = $_GET['date'];

                    if (isset($_SESSION['work_id'])) {
                        $userWorkID = $_SESSION['work_id'];

                        // Fetch calendar details
                        $sql = "SELECT * FROM calendar WHERE date = :clickedDate AND work_id = :userWorkID";
                        $stmt = $conn->prepare($sql);
                        $stmt->bindParam(':clickedDate', $clickedDate);
                        $stmt->bindParam(':userWorkID', $userWorkID, PDO::PARAM_INT);
                        $stmt->execute();
                        $calendarResult = $stmt->fetch(PDO::FETCH_ASSOC);
                        if (!$calendarResult) {
                            echo "Nincsenek rekordok erre a napra.";
                            include "footer.php";
                            exit;
                        }

                        // Fetch requests for the date
                        $requestSql = "SELECT * FROM requests WHERE work_id = :userWorkID AND calendar_id = :calendarId";
                        $requestStmt = $conn->prepare($requestSql);
                        $requestStmt->bindParam(':userWorkID', $userWorkID, PDO::PARAM_INT);
                        $requestStmt->bindParam(':calendarId', $calendarResult['calendar_id'], PDO::PARAM_INT);
                        $requestStmt->execute();
                        $requests = $requestStmt->fetchAll(PDO::FETCH_ASSOC);
                    } else {
                        echo "User session not found.";
                        exit;
                    }
                } else {
                    echo "Date not specified.";
                    exit;
                }

                ?>

                <h1>Date: <?php echo $calendarResult['date']; ?></h1>
                <p>Nap: <?php echo date('l', strtotime($calendarResult['date'])); ?></p>
                <p>Státusz: <?php echo getStatusName($calendarResult['day_status'])?></p>
                <p>Megjegyzés: <?php echo $calendarResult['comment']; ?></p>

                <?php if ($_SESSION['is_user']==true): ?>
                    <h3>Az én kéréseim:</h3>
                    <?php if (!empty($requests)): ?>
                        <ul>
                            <?php foreach ($requests as $request): ?>
                                <li><?php echo htmlspecialchars($request['message']); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>Nincsenek kérelmek erre a napra.</p>
                    <?php endif; ?>
                <?php elseif($_SESSION['is_user']==false): ?>
                    <h3>Kérések erre a napra:</h3>
                    <?php
                    // Fetch the calendar_id for the given date
                    $calendarSql = "SELECT calendar_id FROM calendar WHERE date = :clickedDate";
                    $calendarStmt = $conn->prepare($calendarSql);
                    $calendarStmt->bindParam(':clickedDate', $clickedDate);
                    $calendarStmt->execute();
                    $calendarIds = $calendarStmt->fetchAll(PDO::FETCH_COLUMN, 0);
                    //fetch user
                    $workId = $_SESSION['work_id'];
                    $stmt = $conn->prepare("SELECT position,kar,szervezetszam FROM users WHERE work_id = :work_id");
                    $stmt->bindParam(':work_id', $workId, PDO::PARAM_INT);
                    $stmt->execute();
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);

                    $position = $result['position']; // User's position
                    $kar = $result['kar']; // User's kar
                    $szervezetszam = $result['szervezetszam']; // User's szervezetszam
                    $karPattern = '%' . $kar . '%';
                    $szevezetszamPattern = '%' . $szervezetszam . '%';
                    echo $position;

                    // Fetch requests for all calendar_ids
                    if (!empty($calendarIds)) {
                        $placeholders = implode(',', array_fill(0, count($calendarIds), '?'));
                        switch($position){
                            case "admin":
                                $adminSql = "SELECT r.*, u.name FROM requests r
                         LEFT JOIN users u ON r.work_id = u.work_id
                         WHERE r.calendar_id IN ($placeholders) AND r.request_status='pending'";
                                $adminStmt = $conn->prepare($adminSql);
                                $adminStmt->execute($calendarIds);
                                $adminRequests = $adminStmt->fetchAll(PDO::FETCH_ASSOC);
                                break;
                            case "dekan":
                                // Prepare the SQL query
                                $adminSql = "SELECT r.*, u.name FROM requests r
                     LEFT JOIN users u ON r.work_id = u.work_id
                     WHERE r.calendar_id IN ($placeholders) AND r.request_status = 'pending' AND r.to_whom LIKE ?";
                                $adminStmt = $conn->prepare($adminSql);

                                // Bind parameters for the IN clause
                                foreach ($calendarIds as $k => $id) {
                                    $adminStmt->bindValue($k + 1, $id, PDO::PARAM_INT);
                                }

                                // Bind parameter for the LIKE clause
                                $karPattern = '%' . $kar . '%'; // Make sure $kar is defined and holds the correct value
                                $adminStmt->bindValue(count($calendarIds) + 1, $karPattern, PDO::PARAM_STR);

                                // Execute the prepared statement
                                $adminStmt->execute();
                                $adminRequests = $adminStmt->fetchAll(PDO::FETCH_ASSOC);
                                break;
                            case "tanszekvezeto":
                                // Prepare the SQL query
                                $adminSql = "SELECT r.*, u.name FROM requests r
                     LEFT JOIN users u ON r.work_id = u.work_id
                     WHERE r.calendar_id IN ($placeholders) AND r.request_status = 'pending' AND r.to_whom LIKE ? AND r.to_whom LIKE ?";
                                $adminStmt = $conn->prepare($adminSql);

                                // Bind parameters for the IN clause
                                foreach ($calendarIds as $k => $id) {
                                    $adminStmt->bindValue($k + 1, $id, PDO::PARAM_INT);
                                }

                                // Bind parameters for the LIKE clauses
                                $karPattern = '%' . $kar . '%'; // Make sure $kar is defined and holds the correct value
                                $szevezetszamPattern = '%' . $szervezetszam . '%';
                                $adminStmt->bindValue(count($calendarIds) + 1, $karPattern, PDO::PARAM_STR);
                                $adminStmt->bindValue(count($calendarIds) + 2, $szevezetszamPattern, PDO::PARAM_STR);

                                // Execute the prepared statement
                                $adminStmt->execute();
                                $adminRequests = $adminStmt->fetchAll(PDO::FETCH_ASSOC);
                                break;

                        }
                        ?>

                        <?php if (!empty($adminRequests)): ?>
                            <table>
                                <tr>
                                    <th>Kérelem</th>
                                    <th>Műveletek</th>
                                </tr>
                                <?php foreach ($adminRequests as $adminRequest):
                                    ?>
                                    <tr>
                                        <td>
                                            <?php
                                            $requestDetailsUrl = "request.php?request_id=" . urlencode($adminRequest['request_id']);
                                            $profileUrl = "profile.php?work_id=" . urlencode($adminRequest['work_id']);
                                            ?>
                                            <a href="<?php echo $requestDetailsUrl; ?>">
                                                <?php echo "ID: " . htmlspecialchars($adminRequest['request_id']); ?>
                                            </a>
                                            <?php echo ", "; ?>
                                            <a href="<?php echo $profileUrl; ?>">
                                                <?php echo "work_id: " . htmlspecialchars($adminRequest['work_id']); ?>
                                            </a>
                                            <?php echo ", "; ?>
                                            <a href="<?php echo $profileUrl; ?>">
                                                <?php echo htmlspecialchars($adminRequest['name']); ?>
                                            </a>
                                            <?php echo ": " . htmlspecialchars($adminRequest['message']); ?>
                                            <?php echo ": " . htmlspecialchars($adminRequest['request_status']); ?>
                                        </td>
                                        <td>
                                            <!-- Accept Button -->
                                            <form action="accept_request.php" method="post">
                                                <input type="hidden" name="request_id" value="<?php echo $adminRequest['request_id']; ?>">
                                                <input type="submit" value="Elfogad">
                                            </form>

                                            <!-- Reject Button -->
                                            <form action="reject_request.php" method="post">
                                                <input type="hidden" name="request_id" value="<?php echo $adminRequest['request_id']; ?>">
                                                <input type="submit" value="Elutasít">
                                            </form>

                                            <!-- Respond Button -->
                                            <form action="respond_request_form.php" method="get">
                                                <input type="hidden" name="request_id" value="<?php echo $adminRequest['request_id']; ?>">
                                                <input type="submit" value="Válaszol">
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                        <?php else: ?>
                            <p>Nincsenek aktív kérelmek erre a napra.</p>
                        <?php endif;
                    } else {
                        echo "<p>Nincsenek adatok a megadott dátumhoz a naptárban.</p>";
                    }
                    ?>
                <?php endif; ?>
                <?php
                if ($_SESSION['is_user']==false){
                    include "list_day_users.php";
                }

                include "day_selector.php"
                ?>
                <div class="footer-div">
                    <?php
                    include "footer.php";
                    ?>
                </div>
            </div>
            </div>

    </div>
</body>
</html>

