<?php
session_start();
include "connect.php";
include "nav-bar.php";

if (!isset($_SESSION['logged'])) {
    header("Location: login_form.php");
    exit;
}

// Check if the user is logged in and has a work_id set in the session
if (isset($_SESSION['work_id'])) {
    $userWorkID = $_SESSION['work_id'];

    // Prepare a SQL statement to retrieve all requests made by the logged-in user
    $sql = "SELECT * FROM requests WHERE work_id = :userWorkID ORDER BY request_id DESC";
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
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<h1>Kérelmeim</h1>

<?php if (!empty($requests)): ?>
    <table>
        <tr>
            <th>Kérelem ID</th>
            <th>Naptár ID</th>
            <th>Szabadnap típusa</th>
            <th>Üzenet</th>
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
                <td><?php echo htmlspecialchars($request['requested_status']); ?></td>
                <td><?php echo htmlspecialchars($request['message']); ?></td>
                <td><?php echo htmlspecialchars($request['to_whom']); ?></td>
                <td><?php echo htmlspecialchars($request['request_status']); ?></td>
                <td><?php echo htmlspecialchars($request['timestamp']); ?></td>
                <td><?php echo htmlspecialchars($request['modified_date']); ?></td>
                <td>
                    <!-- Modify Button -->
                    <?php if ($request['request_status'] == "pending" || $request['request_status'] == "messaged"): ?>
                        <form action="modify_request.php" method="post">
                        <input type="hidden" name="request_id" value="<?php echo $request['request_id']; ?>">
                        <input type="submit" value="Módosít">
                    </form>
                    <?php endif; ?>

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
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <p>Nincsenek kérelmeid.</p>
<?php endif; ?>

<?php include "footer.php"; ?>

</body>
</html>
