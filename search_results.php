<?php
include "session_check.php";
include "connect.php";

$searchQuery = $_GET['search_query'] ?? '';

// Sanitize the search query
$searchTerm = "%" . $searchQuery . "%";

// Fetch the user's data based on the work_id from the session
$workId = $_SESSION['work_id'] ?? '';
$positionSql = "SELECT position, kar, szervezetszam FROM users WHERE work_id = :workId";
$positionStmt = $conn->prepare($positionSql);
$positionStmt->bindParam(':workId', $workId, PDO::PARAM_INT);
$positionStmt->execute();
$userData = $positionStmt->fetch(PDO::FETCH_ASSOC);

if ($userData) {
    $userPosition = $userData['position'];
    $kar = $userData['kar'];
    $szervezetszam = $userData['szervezetszam'];
} else {
    // Handle case when no user data is found
    echo "No user data found.";
    exit;
}

// Adjust SQL query based on the user's position
switch ($userPosition) {
    case 'admin':
        $sql = "SELECT * FROM users WHERE work_id::varchar LIKE :searchTerm OR name LIKE :searchTerm OR email LIKE :searchTerm";
        break;
    case 'dekan':
        $sql = "SELECT * FROM users WHERE (work_id::varchar LIKE :searchTerm OR name LIKE :searchTerm OR email LIKE :searchTerm) AND kar = :kar";
        break;
    case 'tanszekvezeto':
        $sql = "SELECT * FROM users WHERE (work_id::varchar LIKE :searchTerm OR name LIKE :searchTerm OR email LIKE :searchTerm) AND kar = :kar AND szervezetszam = :szervezetszam";
        break;
    default:
        echo "You do not have permission to perform this search.";
        exit;
}

// Prepare and execute the statement
$stmt = $conn->prepare($sql);
$stmt->bindParam(':searchTerm', $searchTerm, PDO::PARAM_STR);
if (in_array($userPosition, ['dekan', 'tanszekvezeto'])) {
    $stmt->bindParam(':kar', $kar, PDO::PARAM_STR);
}
if ($userPosition == 'tanszekvezeto') {
    $stmt->bindParam(':szervezetszam', $szervezetszam, PDO::PARAM_STR);
}
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search Results</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
</head>
<body>
<div class="body-container">
    <div class="navbar">
        <?php include "nav-bar.php"; ?>
    </div>
    <div class="main-content">
        <div class="search-results">
            <h1>Search Results for "<?php echo htmlspecialchars($searchQuery); ?>"</h1>

            <?php if (is_array($results) && count($results) > 0): ?>
                <table>
                    <tr>
                        <th>Work ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Kar</th>
                        <th>Szervezetszám</th>
                    </tr>
                    <?php foreach ($results as $row): ?>
                        <tr>
                            <!-- Make work_id clickable -->
                            <td><a href="profile.php?work_id=<?php echo $row['work_id']; ?>"><?php echo htmlspecialchars($row['work_id']); ?></a></td>
                            <!-- Make name clickable -->
                            <td><a href="profile.php?work_id=<?php echo $row['work_id']; ?>"><?php echo htmlspecialchars($row['name']); ?></a></td>
                            <!-- Make email clickable -->
                            <td><a href="profile.php?work_id=<?php echo $row['work_id']; ?>"><?php echo htmlspecialchars($row['email']); ?></a></td>
                            <td><?php echo htmlspecialchars($row['kar']); ?></td>
                            <td><?php echo htmlspecialchars($row['szervezetszam']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php else: ?>
                <p>No results found for "<?php echo htmlspecialchars($searchQuery); ?>".</p>
            <?php endif; ?>
        </div>
        <div class="footer-div">
            <?php
            include "footer.php"
            ?>
        </div>
    </div>
</div>



</body>
</html>
