<?php
include "session_check.php";
include "app/config/connect.php";
include "app/helpers/function_get_name.php";

if (!isset($_SESSION['logged'])) {
    header("Location: login_form.php");
    exit;
}

if (!isset($_SESSION['logged']) || !isset($_SESSION['work_id'])) {
    header("Location: login_form.php");
    exit;
}
$userWorkID = $_SESSION['work_id'];
// Assume $userWorkID, $conn are already set
$positionSql = "SELECT position, faculty, entity_id FROM users WHERE work_id = :userWorkID";
$positionStmt = $conn->prepare($positionSql);
$positionStmt->bindParam(':userWorkID', $userWorkID, PDO::PARAM_INT);
$positionStmt->execute();
$userDetails = $positionStmt->fetch(PDO::FETCH_ASSOC);

$pozicio = $userDetails['position'];
$faculty = $userDetails['faculty'];
$entity_id = $userDetails['entity_id'];
$statusFilter = isset($_POST['statusFilter']) ? $_POST['statusFilter'] : 'pending';

$facultyPattern = '%' . $faculty . '%';
$szervezetszamPattern = '%' . $entity_id . '%';

switch ($pozicio) {
    case 'admin':
        $requestsSql = "SELECT r.*, u.name, u.work_id FROM requests r LEFT JOIN users u ON r.work_id = u.work_id WHERE r.request_status = :statusfilter";
        break;
    case 'dekan':
        $requestsSql = "SELECT r.*, u.name, u.work_id FROM requests r LEFT JOIN users u ON r.work_id = u.work_id WHERE r.to_whom LIKE :facultyPattern AND r.request_status = :statusfilter";
        break;
    case 'tanszekvezeto':
        $requestsSql = "SELECT r.*, u.name, u.work_id FROM requests r LEFT JOIN users u ON r.work_id = u.work_id WHERE r.to_whom LIKE :facultyPattern AND r.to_whom LIKE :szervezetszamPattern AND r.request_status = :statusfilter";
        break;
    default:
        echo "Nincs ehhez jogosultságod.";
        exit;
}
$requestsStmt = $conn->prepare($requestsSql);

if ($statusFilter != 'all') {
    // Only add the WHERE clause if $statusFilter is not 'all'
    $requestsSql .= " AND r.request_status = :statusfilter";
    $requestsStmt = $conn->prepare($requestsSql);
    $requestsStmt->bindParam(':statusfilter', $statusFilter, PDO::PARAM_STR);
}

if ($pozicio == 'dekan' || $pozicio == 'tanszekvezeto') {
    $requestsStmt->bindParam(':facultyPattern', $facultyPattern, PDO::PARAM_STR);
}

if ($pozicio == 'tanszekvezeto') {
    $requestsStmt->bindParam(':szervezetszamPattern', $szervezetszamPattern, PDO::PARAM_STR);
}

// The actual execution of the statement
$requestsStmt->execute();
$requests = $requestsStmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bejövő kérelmek</title>
    <link rel="stylesheet" href="public/css/styles.css">
</head>
<body>
<?php include "navigation_bar-top.php"; ?>
<div class="body-container">
    <div class="navbar" id="sidebar">
        <?php include "navigation_bar-side.php"; ?>
    </div>
    <div class="main-content" id="main-content">
        <div class="test_content">
            <div class="incomming-requests">
                <h1>Bejövő kérelmek</h1>
                <!-- Status Filter Form -->
                <form method="post" class="status-filter-form">
                    <div class="form-group">
                        <label for="statusFilter">Szűrés állapot szerint:</label>
                        <select name="statusFilter" id="statusFilter" onchange="this.form.submit()">
                            <option value="pending" <?php echo (!isset($statusFilter) || $statusFilter == 'pending') ? 'selected' : ''; ?>>Függőben lévő</option>
                            <!--<option value="all" <?php echo (isset($statusFilter) && $statusFilter == 'all') ? 'selected' : ''; ?>>Összes</option>-->
                            <option value="rejected" <?php echo (isset($statusFilter) && $statusFilter == 'rejected') ? 'selected' : ''; ?>>Elutasított</option>
                            <option value="accepted" <?php echo (isset($statusFilter) && $statusFilter == 'accepted') ? 'selected' : ''; ?>>Elfogadott</option>
                        </select>
                    </div>
                </form>
                <?php if (!empty($requests)): ?>
                    <form id="requestsForm" method="post">
                        <div class="table-container">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th><input type="checkbox" id="selectAll" onclick="toggleCheckboxes(this)"></th>
                                    <th>Kérelem ID</th>
                                    <th>Work ID</th>
                                    <th>Név</th> <!-- Added Name column -->
                                    <th>Naptár ID</th>
                                    <th>Szabadnap típusa</th>
                                    <th>Kinek</th>
                                    <th>Kérvény státusza</th>
                                    <!-- <th>Időbélyegző</th> -->
                                    <!-- <th>Utolsó módósítás ekkor</th> -->
                                    <th>Műveletek</th> <!-- Operations column -->
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($requests as $request): ?>
                                    <tr>
                                        <td><input type="checkbox" name="request_ids[]" value="<?php echo htmlspecialchars($request['request_id']); ?>"></td>
                                        <td><?php echo htmlspecialchars($request['request_id']); ?></td>
                                        <td><a href="profile.php?work_id=<?php echo htmlspecialchars($request['work_id']); ?>"><?php echo htmlspecialchars($request['work_id']); ?></a></td>
                                        <td><a href="profile.php?work_id=<?php echo htmlspecialchars($request['work_id']); ?>"><?php echo htmlspecialchars($request['name']); ?></a></td>
                                        <td><?php echo htmlspecialchars($request['calendar_id']); ?></td>
                                        <td><?php echo htmlspecialchars(getName($request['requested_status'])); ?></td>
                                        <td><?php echo htmlspecialchars($request['to_whom']); ?></td>
                                        <td><?php echo htmlspecialchars(getName($request['request_status'])); ?></td>
                                        <td class="center-content">
                                            <!-- Delete Button -->
                                            <?php if ($request['request_status'] == "pending" || $request['request_status'] == "messaged"): ?>
                                                <form action="delete_request.php" method="post" onsubmit="return confirm('Biztosan törölni szeretné ezt a kérelmet?');" style="display: inline;">
                                                    <input type="hidden" name="request_id" value="<?php echo $request['request_id']; ?>">
                                                    <button class="action-button" type="submit">
                                                        <img src="public/images/icons/delete_20dp_FILL0_wght400_GRAD0_opsz20.png" alt="Delete">
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                            <?php if ($request['request_status'] == "rejected" || $request['request_status'] == "accepted"): ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <button class="action-button accept-button action-button-bigger" type="submit" onclick="setFormAction('accept_all_requests.php')">
                            <img src="public/images/icons/check_20dp_FILL0_wght400_GRAD0_opsz20.png" alt="Accept">
                            Kijelölt kérelmek elfogadása
                        </button>

                        <button class="action-button deny-button action-button-bigger" type="submit" onclick="setFormAction('reject_all_requests.php')">
                            <img src="public/images/icons/close_20dp_FILL0_wght400_GRAD0_opsz20.png" alt="Deny">
                            Kijelölt kérelmek elutasítása
                        </button>
                    </form>
                <p></p>
                <h1></h1>
                <?php else: ?>
                    <p>Nincsenek bejövő kérelmeid.</p>
                <?php endif; ?>
            </div>
        </div>
        <div class="footer-div">
            <?php include "app/views/partials/footer.php"; ?>
        </div>
    </div>
</div>
<script src="public/js/collapse.js"></script>
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
</body>
</html>



