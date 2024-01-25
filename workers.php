<?php
include "session_check.php";
include "nav-bar.php";
include "connect.php";

try {
    // Check if szervezetszam is set in the POST request
    if (isset($_POST['szervezetszam'])) {
        $szervezetszam = $_POST['szervezetszam'];

        // Prepare the SQL query with the szervezetszam filter
        $stmt = $conn->prepare("SELECT work_id, name, email, cim, adoazonosito, szervezetszam, alkalmazottikartya, position, free, taken, requested, planned FROM users WHERE szervezetszam = :szervezetszam");
        $stmt->bindParam(':szervezetszam', $szervezetszam, PDO::PARAM_INT);
    } else {
        // If szervezetszam is not set, fetch all users
        $szervezetszam = "Nincs szervezetszáma!";
        $stmt = $conn->prepare("SELECT work_id, name, email, cim, adoazonosito, szervezetszam, alkalmazottikartya, position, free, taken, requested, planned FROM users");
    }

    $stmt->execute();
    $workers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (count($workers) > 0) {
        echo "<h2>Minden felhasználó listázva a következő számú szervezetből: $szervezetszam</h2>";
        echo "<table border='1'>";
        echo "<tr><th>work_id</th><th>Név</th><th>Email</th><th>Cím</th><th>Adószám</th><th>Szervezetszám</th><th>Alkalmazotti kártyaszám</th><th>Pozíció</th><th>Free</th><th>Taken</th><th>Requested</th><th>Planned</th></tr>";
        foreach ($workers as $worker) {
            $workIdLink = "workerDetails.php?work_id=" . htmlspecialchars($worker['work_id']);
            $nameLink = "workerDetails.php?work_id=" . htmlspecialchars($worker['work_id']);

            // Check if the record belongs to the currently logged-in user
            $isOwnRecord = isset($_SESSION['work_id']) && $_SESSION['work_id'] == $worker['work_id'];

            echo "<tr>";
            echo "<td>";

            // Conditionally set the link based on whether it's the user's own record
            if ($isOwnRecord) {
                echo "<a href='profile.php'>" . htmlspecialchars($worker['work_id']) . "</a>";
            } else {
                echo "<a href='{$workIdLink}'>" . htmlspecialchars($worker['work_id']) . "</a>";
            }

            echo "</td>";
            echo "<td>";

            // Conditionally set the link based on whether it's the user's own record
            if ($isOwnRecord) {
                echo "<a href='profile.php'>" . htmlspecialchars($worker['name']) . "</a>";
            } else {
                echo "<a href='{$nameLink}'>" . htmlspecialchars($worker['name']) . "</a>";
            }

            echo "</td>";
            echo "<td>" . htmlspecialchars($worker['email']) . "</td>";
            echo "<td>" . htmlspecialchars($worker['cim']) . "</td>";
            echo "<td>" . htmlspecialchars($worker['adoazonosito']) . "</td>";
            echo "<td>" . htmlspecialchars($worker['szervezetszam']) . "</td>";
            echo "<td>" . htmlspecialchars($worker['alkalmazottikartya']) . "</td>";
            echo "<td>" . htmlspecialchars($worker['position']) . "</td>";
            echo "<td>" . htmlspecialchars($worker['free']) . "</td>";
            echo "<td>" . htmlspecialchars($worker['taken']) . "</td>";
            echo "<td>" . htmlspecialchars($worker['requested']) . "</td>";
            echo "<td>" . htmlspecialchars($worker['planned']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "Nem létezik ilyen szervezetszámű felhasználó: $szervezetszam";
    }
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
}
include "footer.php";
?>
