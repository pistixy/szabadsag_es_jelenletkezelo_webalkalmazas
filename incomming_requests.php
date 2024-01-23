<?php
session_start();
include "connect.php";
include "nav-bar.php";

if (!isset($_SESSION['logged']) || !isset($_SESSION['work_id'])) {
    header("Location: login_form.php");
    exit;
}

$userWorkID = $_SESSION['work_id'];
$positionSql = "SELECT position FROM users WHERE work_id = :userWorkID";
$positionStmt = $conn->prepare($positionSql);
$positionStmt->bindParam(':userWorkID', $userWorkID, PDO::PARAM_INT);
$positionStmt->execute();
$userPosition = $positionStmt->fetch(PDO::FETCH_ASSOC);

$pozicio = $userPosition['position'];
$statusFilter = isset($_POST['statusFilter']) ? $_POST['statusFilter'] : 'all'; // Default to 'all'

if ($statusFilter === 'all') {
    $requestsSql = "SELECT * FROM requests WHERE to_whom = :pozicio ORDER BY request_id DESC";
    $requestsStmt = $conn->prepare($requestsSql);
    $requestsStmt->bindParam(':pozicio', $pozicio, PDO::PARAM_STR);
} else {
    $requestsSql = "SELECT * FROM requests WHERE to_whom = :pozicio AND request_status = :statusFilter ORDER BY request_id DESC";
    $requestsStmt = $conn->prepare($requestsSql);
    $requestsStmt->bindParam(':pozicio', $pozicio, PDO::PARAM_STR);
    $requestsStmt->bindParam(':statusFilter', $statusFilter, PDO::PARAM_STR);
}

$requestsStmt->execute();
$requests = $requestsStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bejövő kérelmek</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<h1>Bejövő kérelmek</h1>

<!-- Status Filter Form -->
<form method="post">
    <label for="statusFilter">Szűrés állapot szerint:</label>
    <select name="statusFilter" id="statusFilter" onchange="this.form.submit()">
        <option value="all" <?php echo $statusFilter == 'all' ? 'selected' : ''; ?>>Összes</option>
        <option value="rejected" <?php echo $statusFilter == 'rejected' ? 'selected' : ''; ?>>Elutasított</option>
        <option value="accepted" <?php echo $statusFilter == 'accepted' ? 'selected' : ''; ?>>Elfogadott</option>
        <option value="pending" <?php echo $statusFilter == 'pending' ? 'selected' : ''; ?>>Függőben lévő</option>
        <option value="messaged" <?php echo $statusFilter == 'messaged' ? 'selected' : ''; ?>>Üzenet küldve</option>
    </select>
</form>

<?php if (!empty($requests)): ?>
    <table>
        <tr>
            <th>Kérelem ID</th>
            <th>Work ID</th>
            <th>Naptár ID</th>
            <th>Szabadnap típusa</th>
            <th>Üzenet</th>
            <th>Kinek</th>
            <th>Kérvény státusza</th>
            <th>Időbélyegő</th>
            <th>Utolsó módósítás ekkor</th>
            <th>Műveletek</th> <!-- Operations column -->
        </tr>
        <?php foreach ($requests as $request): ?>
            <tr>
                <td><?php echo htmlspecialchars($request['request_id']); ?></td>
                <td><?php echo htmlspecialchars($request['work_id']); ?></td>
                <td><?php echo htmlspecialchars($request['calendar_id']); ?></td>
                <td><?php echo htmlspecialchars($request['requested_status']); ?></td>
                <td><?php echo htmlspecialchars($request['message']); ?></td>
                <td><?php echo htmlspecialchars($request['to_whom']); ?></td>
                <td><?php echo htmlspecialchars($request['request_status']); ?></td>
                <td><?php echo htmlspecialchars($request['timestamp']); ?></td>
                <td><?php echo htmlspecialchars($request['modified_date']); ?></td>
                <td>
                    <!-- Accept Button -->
                    <?php if ($request['request_status'] == "pending" || $request['request_status'] == "messaged"): ?>
                    <form action="accept_request.php" method="post">
                        <input type="hidden" name="request_id" value="<?php echo $request['request_id']; ?>">
                        <input type="submit" value="Elfogad">
                    </form>
                    <?php endif; ?>

                    <!-- Reject Button -->
                    <?php if ($request['request_status'] == "pending" || $request['request_status'] == "messaged"): ?>
                        <form action="reject_request.php" method="post">
                            <input type="hidden" name="request_id" value="<?php echo htmlspecialchars($request['request_id']); ?>">
                            <input type="submit" value="Elutasít">
                        </form>
                    <?php endif; ?>

                    <!-- Respond Button -->
                    <form action="respond_request_form.php" method="get"> <!-- Change method to "get" -->
                        <input type="hidden" name="request_id" value="<?php echo $request['request_id']; ?>">
                        <input type="submit" value="Válaszol">
                    </form>

                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <p>Nincsenek bejövő kérelmeid.</p>
<?php endif; ?>

<?php include "footer.php"; ?>

</body>
</html>
