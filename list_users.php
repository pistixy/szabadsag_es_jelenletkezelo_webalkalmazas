<!DOCTYPE html>
<html lang="hu-HU">
<head>
    <meta charset="UTF-8">
    <title>Dolgozók</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php
session_start();

include "nav-bar.php";

if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin']) {
    $query = "SELECT * FROM users";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        echo "<h2>List of Users</h2>";
        echo "<table>";
        echo "<tr><th>Email</th><th>Name</th><th>Surname</th><th>Phone</th><th>Birthdate</th><th>Admin</th><th>Position</th><th>Joined</th></tr>";

        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>{$row['email']}</td>";
            echo "<td>{$row['name']}</td>";
            echo "<td>{$row['surname']}</td>";
            echo "<td>{$row['phone']}</td>";
            echo "<td>{$row['birthdate']}</td>";
            echo "<td>{$row['admin']}</td>";
            echo "<td>{$row['position']}</td>";
            echo "<td>{$row['joined']}</td>";
            echo "</tr>";
        }

        echo "</table>";
    } else {
        echo "No users found.";
    }
} else {
    echo "You are not authorized to view this page.";
}

mysqli_close($conn);
?>
<?php
include "footer.php";
?>
</body>
</html>

