<?php
// Include the database connection file
include "connect.php";
include "check_login.php";
include "session_check.php";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if all required fields are filled
    if (isset($_POST['date']) && isset($_POST['price']) && isset($_FILES['receipt']) && isset($_SESSION['work_id'])) {
        // Retrieve session work_id
        $work_id = $_SESSION['work_id'];

        $date = $_POST['date'];
        $price = $_POST['price'];

        // File upload handling
        $targetDir = "passes/";
        $targetFile = $targetDir . basename($_FILES["receipt"]["name"]);
        $uploadOk = 1;
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Check if file is a PDF
        if ($fileType != "pdf") {
            echo "Sorry, only PDF files are allowed.";
            $uploadOk = 0;
        }

        // Check file size (limit to 5MB)
        if ($_FILES["receipt"]["size"] > 5000000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
        } else {
            // If everything is ok, try to upload file
            if (move_uploaded_file($_FILES["receipt"]["tmp_name"], $targetFile)) {
                // File uploaded successfully, now insert data into the database
                $filename = basename($_FILES["receipt"]["name"]); // Store the filename in a variable
                $sql = "INSERT INTO public.commute (work_id, honnan, hova, how, date, filename, price) 
                        VALUES (:work_id, '', '', 'Pass', :date, :filename, :price)";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':work_id', $work_id);
                $stmt->bindParam(':date', $date);
                $stmt->bindParam(':filename', $filename); // Use the variable here
                $stmt->bindParam(':price', $price);

                if ($stmt->execute()) {
                    echo '<script>alert("Data recorded successfully!");</script>';
                    echo '<script>window.history.back();</script>'; // Go back to the previous page
                } else {
                    echo "Error inserting data into the database.";
                }
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
    } else {
        echo "All fields are required.".var_dump($_POST);
    }
} else {
    echo "Invalid request method.";
}
?>
