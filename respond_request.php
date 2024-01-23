<?php
session_start();
include "connect.php";

// Check if the user is logged in
if (!isset($_SESSION['logged']) || !isset($_SESSION['work_id'])) {
    header("Location: login_form.php");
    exit;
}

// Check if a request ID has been passed to initiate a response
if (isset($_POST['request_id']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $fromWorkID = $_SESSION['work_id']; // The responder's work_id
    $requestId = $_POST['request_id'];

    // Fetch the work_id of the user who made the request
    $requestSql = "SELECT work_id FROM requests WHERE request_id = :requestId";
    $requestStmt = $conn->prepare($requestSql);
    $requestStmt->bindParam(':requestId', $requestId, PDO::PARAM_INT);
    $requestStmt->execute();
    $requestDetails = $requestStmt->fetch(PDO::FETCH_ASSOC);

    if ($requestDetails) {
        $toWorkID = $requestDetails['work_id']; // The requester's work_id
        $message = $_POST['message']; // The message from the textarea
        $type = 'response to request'; // Fixed type
        $currentTimestamp = date('Y-m-d H:i:s'); // Current timestamp

        // Insert the response into the messages table
        $insertSql = "INSERT INTO messages (from_work_id, to_work_id, to_position, type, request_id, message, timestamp) VALUES (:fromWorkID, :toWorkID, '', :type, :requestId, :message, :timestamp)";
        $insertStmt = $conn->prepare($insertSql);
        $insertStmt->bindParam(':fromWorkID', $fromWorkID, PDO::PARAM_INT);
        $insertStmt->bindParam(':toWorkID', $toWorkID, PDO::PARAM_INT);
        $insertStmt->bindParam(':type', $type, PDO::PARAM_STR);
        $insertStmt->bindParam(':requestId', $requestId, PDO::PARAM_INT);
        $insertStmt->bindParam(':message', $message, PDO::PARAM_STR);
        $insertStmt->bindParam(':timestamp', $currentTimestamp, PDO::PARAM_STR);
        $insertStmt->execute();

        // Redirect or display a success message
        echo "Your response has been sent successfully.";
        // Redirect to a confirmation page or back to the requests list
    } else {
        echo "Request not found.";
    }
} else {
    // If the form has not been submitted yet, display it
    if (isset($_GET['request_id'])) {
        $requestId = $_GET['request_id'];
        // Display the form
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>Respond to Request</title>
            <link rel="stylesheet" href="styles.css">
        </head>
        <body>
        <?php include "nav-bar.php"; ?>
        <h1>Respond to Request</h1>
        <form action="respond_request.php" method="post">
            <input type="hidden" name="request_id" value="<?php echo htmlspecialchars($requestId); ?>">
            <label for="message">Message:</label><br>
            <textarea id="message" name="message" required></textarea><br>
            <input type="submit" value="Send Response">
        </form>
        <?php include "footer.php"; ?>
        </body>
        </html>
        <?php
    } else {
        echo "No request specified to respond to.";
    }
}
?>
