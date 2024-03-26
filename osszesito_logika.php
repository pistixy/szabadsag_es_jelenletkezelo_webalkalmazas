<?php
include "connect.php";
include "session_check.php";

if (isset($_SESSION['logged']) && ($_SESSION['position'] == "dekan" || $_SESSION['position'] == "admin" )){
    $work_id = $_SESSION['work_id'];

    // Retrieve the 'kar' of the logged-in user
    $stmt = $conn->prepare("SELECT kar FROM users WHERE work_id = :work_id");
    $stmt->bindParam(':work_id', $work_id);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $kar = $result['kar'];

    // Check if there are any pending requests for users with the same 'kar'
    $stmt = $conn->prepare("SELECT COUNT(*) AS pending_count FROM requests INNER JOIN users ON requests.work_id = users.work_id WHERE users.kar = :kar AND requests.request_status = 'pending'");
    $stmt->bindParam(':kar', $kar);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result['pending_count'] > 0) {
        echo "Még vannak függőben lévő kérelmek! Írjon a tanszékvezetöknek, vagy fogadja el maga a kérelmeket!"; // There are pending requests
    } else {
       include "osszesito.php";
    }
} else {
    echo "Nincs jogosultságod ezt megtekinteni!";
}
?>
