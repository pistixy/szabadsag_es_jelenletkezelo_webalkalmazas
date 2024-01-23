<?php
session_start();
include "connect.php";
include "nav-bar.php";

if (!isset($_SESSION['logged']) || !isset($_SESSION['work_id'])) {
    header("Location: login_form.php");
    exit;
}

$requestDetails = [];

// Check if a request ID has been passed to initiate a response
if (isset($_GET['request_id'])) {
    $requestId = $_GET['request_id'];

    // Fetch the request details to display
    $requestSql = "SELECT * FROM requests WHERE request_id = :requestId";
    $requestStmt = $conn->prepare($requestSql);
    $requestStmt->bindParam(':requestId', $requestId, PDO::PARAM_INT);
    $requestStmt->execute();
    $requestDetails = $requestStmt->fetch(PDO::FETCH_ASSOC);

    if (!$requestDetails) {
        echo "The request could not be found.";
        exit;
    }
} else {
    echo "No request specified.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Respond to Request</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<h1>Respond to Request</h1>

<?php if ($requestDetails): ?>
    <div class="request-details">
        <h2>Request Details</h2>
        <p><strong>Request ID:</strong> <?php echo htmlspecialchars($requestDetails['request_id']); ?></p>
        <p><strong>Status:</strong> <?php echo htmlspecialchars($requestDetails['requested_status']); ?></p>
        <p><strong>Message:</strong> <?php echo htmlspecialchars($requestDetails['message']); ?></p>
        <p><strong>Timestamp:</strong> <?php echo htmlspecialchars($requestDetails['timestamp']); ?></p>
        <!-- Include other details as needed -->
    </div>
<?php endif; ?>

<form action="respond_request.php" method="post">
    <input type="hidden" name="request_id" value="<?php echo htmlspecialchars($requestId); ?>">
    <div class="form-group">
        <label for="message">Your Response:</label>
        <textarea id="message" name="message" required></textarea>
    </div>
    <input type="submit" value="Send Response">
</form>

<?php include "footer.php"; ?>

</body>
</html>
