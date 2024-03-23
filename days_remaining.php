<?php


// Include the database connection file
include "connect.php";

// Check if the work_id is set in the session
if (isset($_SESSION['work_id'])) {
    // Get the work_id from the session
    $workId = $_SESSION['work_id'];

    try {
        // Prepare an SQL statement to select payed_free and payed_past_free from the database
        $sql = "SELECT payed_free, payed_past_free,payed_edu_free,payed_award_free,unpayed_dad_free,unpayed_home_free,unpayed_free FROM users WHERE work_id = :work_id";
        $stmt = $conn->prepare($sql);

        // Bind the work_id parameter
        $stmt->bindParam(':work_id', $workId, PDO::PARAM_INT);

        // Execute the query
        $stmt->execute();

        // Fetch the data
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

       

        // Calculate the total
        $total = $row['payed_free'] + $row['payed_past_free']+$row['payed_edu_free']+$row['payed_award_free']+$row['unpayed_dad_free']+$row['unpayed_home_free']+$row['unpayed_free'];

        // Echo out the total
        echo $total;

    } catch (PDOException $e) {
        // Handle any errors
        echo "Error: " . $e->getMessage();
    }
} else {
    // Work ID is not set in the session
    echo "No work_id found in the session.";
}
?>
