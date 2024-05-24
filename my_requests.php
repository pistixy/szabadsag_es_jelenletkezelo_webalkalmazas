<?php
include "session_check.php";
include "connect.php";
include "function_get_name.php";

if (!isset($_SESSION['logged'])) {
    header("Location: login_form.php");
    exit;
}

// Check if the user is logged in and has a work_id set in the session
if (isset($_SESSION['work_id'])) {
    $userWorkID = $_SESSION['work_id'];

    // Prepare a SQL statement to retrieve all requests made by the logged-in user with the date from the calendar
    $sql = "SELECT r.*, c.date 
            FROM requests r
            LEFT JOIN calendar c ON r.calendar_id = c.calendar_id
            WHERE r.work_id = :userWorkID and r.request_status!='deleted'
            ORDER BY r.request_id DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':userWorkID', $userWorkID, PDO::PARAM_INT);
    $stmt->execute();
    $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    echo "User session not found.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Kérelmeim</title>
    <link rel="stylesheet" href="styles4.css">
</head>
<body>
<?php include "test_top-bar.php"; ?>
<div class="body-container">
    <div class="navbar" id="sidebar">
        <?php include "test_nav-bar.php"; ?>
    </div>
    <div class="main-content" id="main-content">
        <div class="test_content">
            <h1>Kérelmeim</h1>

            <?php if (!empty($requests)): ?>
                <table border=1>
                    <tr>
                        <th>Kérelem ID</th>
                        <th>Naptár ID</th>
                        <th>Dátum</th> <!-- Added Date Column -->
                        <th>Szabadnap típusa</th>
                        <!--<th>Üzenet</th>-->
                        <th>Kinek</th>
                        <th>Kérvény állása</th>
                        <th>Időbélyegző</th>
                        <th>Utolsó módósítás ekkor</th>
                        <th>Műveletek</th>
                    </tr>
                    <?php foreach ($requests as $request): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($request['request_id']); ?></td>
                            <td><?php echo htmlspecialchars($request['calendar_id']); ?></td>
                            <td>
                                <?php if (isset($request['date'])): ?>
                                    <a href="date_details.php?date=<?php echo urlencode($request['date']); ?>">
                                        <?php echo htmlspecialchars($request['date']); ?>
                                    </a>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars(getName($request['requested_status'])); ?></td>
                            <!--<td><?php //echo htmlspecialchars($request['message']); ?></td>-->
                            <td><?php echo htmlspecialchars($request['to_whom']); ?></td>
                            <td><?php echo htmlspecialchars(getName($request['request_status'])); ?></td>
                            <td><?php echo htmlspecialchars($request['timestamp']); ?></td>
                            <td><?php echo htmlspecialchars($request['modified_date']); ?></td>

                            <td>
                                <!-- Delete Button -->
                                <?php if ($request['request_status'] == "pending" || $request['request_status'] == "messaged"): ?>
                                    <form action="delete_request.php" method="post" onsubmit="return confirm('Biztosan törölni szeretné ezt a kérelmet?');">
                                        <input type="hidden" name="request_id" value="<?php echo $request['request_id']; ?>">
                                        <input type="submit" value="Töröl">
                                    </form>
                                <?php endif; ?>
                                <?php if ($request['request_status'] == "rejected" || $request['request_status'] == "accepted"):
                                    echo "Nincsenek műveletek";
                                endif; ?>
                            </td>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php else: ?>
                <p>Nincsenek kérelmeid.</p>
            <?php endif; ?>
        </div>
        <p style="margin: 5%">
        </p>
        <div class="footer-div">
            <?php include "footer.php"; ?>
        </div>
    </div>
</div>
<script src="collapse.js"></script>
</body>
</html>
