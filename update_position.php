<?php
// update_position.php

session_start();
include "connect.php";

// Check if the form has been submitted
if (isset($_POST['update_position'], $_POST['new_position'], $_POST['work_id'])) {
    $newPosition = $_POST['new_position'];
    $workId = $_POST['work_id'];

    if($newPosition =="admin" && $_SESSION['work_id']==$workId){
        $_SESSION['isAdmin']==TRUE;
        $_SESSION['is_user']==false;
        $_SESSION['position']=='admin';
    }elseif($newPosition =="tanszekvezeto" && $_SESSION['work_id']==$workId){
        $_SESSION['isAdmin']==false;
        $_SESSION['is_user']==false;
        $_SESSION['position']=='tanszekvezeto';
    }elseif($newPosition =="dekan" && $_SESSION['work_id']==$workId){
        $_SESSION['isAdmin']==false;
        $_SESSION['is_user']==false;
        $_SESSION['position']=='dekan';
    }elseif($newPosition =="tanszekvezeto" && $_SESSION['work_id']==$workId){
        $_SESSION['isAdmin']==false;
        $_SESSION['is_user']==true;
        $_SESSION['position']=='user';
    }
    // Prepare the SQL statement
    $stmt = $conn->prepare("UPDATE users SET position = :new_position WHERE work_id = :work_id");
    $stmt->bindParam(':new_position', $newPosition);
    $stmt->bindParam(':work_id', $workId);

    // Execute the statement and check for success
    if ($stmt->execute()) {
        echo "Beosztás frissítve.";
    } else {
        echo "Hiba a beosztás frissítésekor.";
    }
} else {
    echo "No data to update.";
}

// Redirect back to the profile page or another appropriate page
header("Location: profile.php?work_id=".$workId);  // Replace 'profile.php' with the actual profile page
exit;
?>
