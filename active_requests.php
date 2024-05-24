<?php if ($_SESSION['is_user']==true): ?>
    <h3>Az én kéréseim:</h3>
    <?php if (!empty($requests)): ?>
        <ul>
            <table>
                <th>ID</th>
                <th>Kérelmezett státusz</th>
                <th>Műveletek</th>
                <?php foreach ($requests as $request):
                    // Itt ellenőrizzük, hogy a kérelem státusza 'pending'-e
                        echo "<tr><td>".$request["request_id"]."</td>";
                        echo "<td>".getName($request["requested_status"])."</td>";
                        echo "<td><form action='delete_request.php' method='post'>";
                        echo '<input type="hidden" name="request_id" value='.$request["request_id"].'>';
                        echo '<input type="submit" value="Töröl">';
                        echo "</form></td>";
                        echo "</tr>";
                endforeach; ?>
            </table>
        </ul>
    <?php else: ?>
        <p>Nincsenek aktív kérelmek erre a napra.</p>
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
                    //echo $position; //for debug

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

                                            $profileUrl = "profile.php?work_id=" . urlencode($adminRequest['work_id']);
                                            ?>
                                                <?php echo "ID: " . htmlspecialchars($adminRequest['request_id']); ?>
                                            <?php echo ", "; ?>
                                            <a href="<?php echo $profileUrl; ?>">
                                                <?php echo "work_id: " . htmlspecialchars($adminRequest['work_id']); ?>
                                            </a>
                                            <?php echo ", "; ?>
                                            <a href="<?php echo $profileUrl; ?>">
                                                <?php echo htmlspecialchars($adminRequest['name']); ?>
                                            </a>

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