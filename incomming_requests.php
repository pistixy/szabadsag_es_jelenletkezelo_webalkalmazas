<?php
session_start();
include "connect.php";
include "nav-bar.php";

if (!isset($_SESSION['logged']) || !isset($_SESSION['work_id'])) {
    header("Location: login_form.php");
    exit;
}
$userWorkID=$_SESSION['work_id'];
// Assume $userWorkID, $conn are already set
$positionSql = "SELECT position, kar, szervezetszam FROM users WHERE work_id = :userWorkID";
$positionStmt = $conn->prepare($positionSql);
$positionStmt->bindParam(':userWorkID', $userWorkID, PDO::PARAM_INT);
$positionStmt->execute();
$userDetails = $positionStmt->fetch(PDO::FETCH_ASSOC);

$pozicio = $userDetails['position'];
$kar = $userDetails['kar'];
$szervezetszam = $userDetails['szervezetszam'];
$statusFilter = isset($_POST['statusFilter']) ? $_POST['statusFilter'] : 'pending';


// Constructing the SQL based on user role
switch ($pozicio) {
    case 'admin':
        $requestsSql = "SELECT r.*, u.name, u.work_id FROM requests r LEFT JOIN users u ON r.work_id = u.work_id WHERE r.request_status = :statusFilter";
        break;
    case 'dekan':
        $requestsSql = "SELECT r.*, u.name, u.work_id FROM requests r LEFT JOIN users u ON r.work_id = u.work_id WHERE u.kar = :kar AND r.request_status = :statusFilter";
        break;
    case 'tanszekvezeto':
        $requestsSql = "SELECT r.*, u.name, u.work_id FROM requests r LEFT JOIN users u ON r.work_id = u.work_id WHERE u.kar = :kar AND u.szervezetszam = :szervezetszam AND r.request_status = :statusFilter";
        break;
    default: // For a regular user
        echo "You do not have permission to view requests.";
        exit;
}

$requestsStmt = $conn->prepare($requestsSql);
if (in_array($pozicio, ['dekan', 'tanszekvezeto'])) {
    $requestsStmt->bindParam(':kar', $kar, PDO::PARAM_STR);
    if ($pozicio == 'tanszekvezeto') {
        $requestsStmt->bindParam(':szervezetszam', $szervezetszam, PDO::PARAM_INT);
    }
}
$requestsStmt->bindParam(':statusFilter', $statusFilter, PDO::PARAM_STR);
$requestsStmt->execute();
$requests = $requestsStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bejövő kérelmek</title>
    <link rel="stylesheet" href="styles.css">
    <script>
        function setFormAction(action) {
            document.getElementById('requestsForm').action = action;
        }

        function toggleCheckboxes(source) {
            checkboxes = document.getElementsByName('request_ids[]');
            for (var i = 0, n = checkboxes.length; i < n; i++) {
                checkboxes[i].checked = source.checked;
            }
        }
    </script>
</head>
<body>

<h1>Bejövő kérelmek</h1>

<!-- Status Filter Form -->
<form method="post">
    <label for="statusFilter">Szűrés állapot szerint:</label>
    <select name="statusFilter" id="statusFilter" onchange="this.form.submit()">
        <option value="pending" <?php echo (!isset($statusFilter) || $statusFilter == 'pending') ? 'selected' : ''; ?>>Függőben lévő</option>
        <option value="all" <?php echo (isset($statusFilter) && $statusFilter == 'all') ? 'selected' : ''; ?>>Összes</option>
        <option value="rejected" <?php echo (isset($statusFilter) && $statusFilter == 'rejected') ? 'selected' : ''; ?>>Elutasított</option>
        <option value="accepted" <?php echo (isset($statusFilter) && $statusFilter == 'accepted') ? 'selected' : ''; ?>>Elfogadott</option>
        <option value="messaged" <?php echo (isset($statusFilter) && $statusFilter == 'messaged') ? 'selected' : ''; ?>>Üzenet küldve</option>
    </select>
</form>


<?php if (!empty($requests)): ?>
    <form id="requestsForm" method="post">
    <table>
        <tr>
            <th><input type="checkbox" id="selectAll" onclick="toggleCheckboxes(this)"></th>
            <th>Kérelem ID</th>
            <th>Work ID</th>
            <th>Név</th> <!-- Added Name column -->
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
                <td><input type="checkbox" name="request_ids[]" value="<?php echo htmlspecialchars($request['request_id']); ?>"></td>
                <td><?php echo htmlspecialchars($request['request_id']); ?></td>
                <td><a href="profile.php?work_id=<?php echo htmlspecialchars($request['work_id']); ?>"><?php echo htmlspecialchars($request['work_id']); ?></a></td>
                <td><a href="profile.php?work_id=<?php echo htmlspecialchars($request['work_id']); ?>"><?php echo htmlspecialchars($request['name']); ?></a></td>
                <td><?php echo htmlspecialchars($request['calendar_id']); ?></td>
                <td><?php echo htmlspecialchars($request['requested_status']); ?></td>
                <td><?php echo htmlspecialchars($request['message']); ?></td>
                <td><?php echo htmlspecialchars($request['to_whom']); ?></td>
                <td><?php echo htmlspecialchars($request['request_status']); ?></td>
                <td><?php echo htmlspecialchars($request['timestamp']); ?></td>
                <td><?php echo htmlspecialchars($request['modified_date']); ?></td>
                <td>
                    <!-- Accept Button -->
                    <?php /*if ($request['request_status'] == "pending" || $request['request_status'] == "messaged"): ?>
                        <form action="accept_request.php" method="post">
                            <input type="hidden" name="request_id" value="<?php echo htmlspecialchars($request['request_id']); ?>">
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
                    <form action="respond_request_form.php" method="get">
                        <input type="hidden" name="request_id" value="<?php echo $request['request_id']; ?>">
                        <input type="submit" value="Válaszol">
                    </form>
                       */?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <input type="submit" value="Accept Selected Requests" onclick="setFormAction('accept_all_requests.php')">
    <input type="submit" value="Reject Selected Requests" onclick="setFormAction('reject_all_requests.php')">
</form>

<?php else: ?>
    <p>Nincsenek bejövő kérelmeid.</p>
<?php endif; ?>

<?php include "footer.php"; ?>

</body>
</html>
