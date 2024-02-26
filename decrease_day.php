<?php
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if all necessary data is provided
    if (isset($_POST['work_id'], $_POST['status'])) {
        // Extract data from the form
        $work_id = $_POST['work_id'];
        $status = $_POST['status'];

        include "connect.php";

        try {
            // Validate the status column name to prevent SQL injection
            $allowed_statuses = ['payed_free', 'payed_edu_free', 'payed_award_free', 'unpayed_dad_free', 'unpayed_home_free', 'unpayed_free'];
            if (!in_array($status, $allowed_statuses)) {
                throw new Exception("Invalid status column.");
            }

            // Query to fetch the current value of the specified status column
            $stmt = $conn->prepare("SELECT $status FROM users WHERE work_id = :work_id");
            $stmt->bindParam(':work_id', $work_id);
            $stmt->execute();
            $result = $stmt->fetchColumn();

            // Check if the status is already zero
            if ($result <= 0) {
                // Redirect back to the page where the form was submitted from with an error message
                header("Location: {$_SERVER['HTTP_REFERER']}?error=2");
                exit;
            }

            // Update the database to decrement the specified status column
            $stmt = $conn->prepare("UPDATE users SET $status = $status - 1 WHERE work_id = :work_id");
            $stmt->bindParam(':work_id', $work_id);
            $stmt->execute();

            // Redirect back to the page where the form was submitted from
            header("Location: {$_SERVER['HTTP_REFERER']}");
            exit;
        } catch (PDOException $e) {
            // Display a user-friendly error message
            echo "An error occurred: " . $e->getMessage();
        } catch (Exception $e) {
            // Redirect back to the page where the form was submitted from with an error message
            header("Location: {$_SERVER['HTTP_REFERER']}?error=3");
            exit;
        }
    } else {
        // Redirect back to the page where the form was submitted from with an error message
        header("Location: {$_SERVER['HTTP_REFERER']}?error=1");
        exit;
    }
} else {
    // If someone tries to access this page directly, redirect them to the homepage
    header("Location: index.php");
    exit;
}
?>
