<?php
include "session_check.php";
include "connect.php";
include "function_translate_month_to_Hungarian.php";


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
            <?php
                try {
                    // Check if szervezetszam is set in the POST request
                    if (isset($_POST['szervezetszam'])) {
                        $feltetel = $_POST['szervezetszam'];

                        // Prepare the SQL query with the szervezetszam filter
                        $stmt = $conn->prepare("SELECT *  FROM users WHERE szervezetszam = :feltetel or kar = :feltetel");
                        $stmt->bindParam(':feltetel', $feltetel, PDO::PARAM_INT);
                    } else {
                        // If szervezetszam is not set, fetch all users
                        $feltetel = "Nincs szervezetszáma!";
                        $stmt = $conn->prepare("SELECT *     FROM users");
                    }

                    $stmt->execute();
                    $workers = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    if (count($workers) > 0) {
                        echo "<h2>Minden felhasználó listázva a következő számú szervezetből: $feltetel</h2>";
                        echo "<table border='1'>";
                        echo "<tr>";
                        echo "<th>Work ID</th>";
                        echo "<th>Name</th>";
                        echo "<th>Email</th>";
                        echo "<th>Cim</th>";
                        echo "<th>Kar</th>";
                        echo "<th>Szervezetszam</th>";
                        echo "<th>Alkalmazottikartya</th>";
                        echo "<th>Position</th>";
                        echo "<th>Action</th>";
                        echo "</tr>";

                        $month = date('n'); // 'n' returns the month without leading zeros (1 to 12)
                        $year = date('Y'); // 'Y' returns the full four-digit year (e.g., 2024)  
                        

                        foreach ($workers as $worker) {
                            echo "<tr>";
                            echo "<td><a href='profile.php?work_id=" . urlencode($worker['work_id']) . "'>" . htmlspecialchars($worker['work_id']) . "</a></td>";
                            echo "<td><a href='profile.php?work_id=" . urlencode($worker['work_id']) . "'>" . htmlspecialchars($worker['name']) . "</a></td>";
                            echo "<td><a href='profile.php?work_id=" . urlencode($worker['work_id']) . "'>" . htmlspecialchars($worker['email']) . "</a></td>";
                            echo "<td>" . htmlspecialchars($worker['cim']) . "</td>";
                            echo "<td>" . htmlspecialchars($worker['kar']) . "</td>";
                            echo "<td>" . htmlspecialchars($worker['szervezetszam']) . "</td>";
                            echo "<td>" . htmlspecialchars($worker['alkalmazottikartya']) . "</td>";
                            echo "<td>" . htmlspecialchars($worker['position']) . "</td>";
                            echo "<td>";
                                echo '<form action="export_calendar_month_to_pdf.php" method="post">';
                                echo '<input type="hidden" name="year" value="' . $year . '">';
                                echo '<input type="hidden" name="month" value="' . $month . '">';
                                echo '<input type="hidden" name="work_id" value="' . $worker['work_id']  . '">';
                                echo '<button type="submit" name="export_calendar_month_pdf" value="1">';
                                echo translateMonthToHungarian($month) . 'i beosztás exportálása';
                                echo '</button>';
                                echo '</form>';
                            echo "</td>";
                            echo "</tr>";
                        }

                        echo "</table>";
                    } else {
                        echo "Nem létezik ilyen szervezetszámű felhasználó: $feltetel";
                    }  
            } catch (PDOException $e) {
                echo "Database error: " . $e->getMessage();
            }
        ?>
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