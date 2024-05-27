<?php
include "session_check.php";
include "connect.php";
include "function_get_name.php";
include "function_translate_month_to_Hungarian.php"; // Beillesztjük a magyar hónap nevek fordítását végző függvényt

if (!isset($_SESSION['logged'])) {
    header("Location: login_form.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Keresési eredmények</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php include "navigation_bar-top.php"; ?>
<div class="body-container">
    <div class="navbar" id="sidebar">
        <?php include "navigation_bar-side.php"; ?>
    </div>
    <div class="main-content" id="main-content">
        <div class="test_content">
            <div class="search-results">
                <?php
                try {
                    // Ellenőrizzük, hogy be van-e állítva a szervezetszám a POST kérésben
                    if (isset($_POST['feltetel'])) {
                        $feltetel = $_POST['feltetel'];
                        $isAll=FALSE;
                        if($feltetel == '' or $feltetel == '*'){
                            $isAll=TRUE;
                            $feltetel = '%' . '' . '%';
                        }

                        // Elkészítjük a SQL lekérdezést a szervezetszám filterrel
                        $stmt = $conn->prepare("SELECT * FROM users WHERE szervezetszam LIKE :feltetel OR kar LIKE :feltetel");
                        $stmt->bindParam(':feltetel', $feltetel, PDO::PARAM_INT);
                    } else {
                        // Ha nincs beállítva a szervezetszám, akkor minden felhasználót lekérünk
                        $feltetel = "Nincs szervezetszáma!";
                        $stmt = $conn->prepare("SELECT * FROM users");
                    }

                    $stmt->execute();
                    $workers = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    if (count($workers) > 0) {
                        if ($isAll){
                            echo "<h2>Minden felhasználó listázva: </h2>";
                        }else{
                            echo "<h2>Minden felhasználó listázva a következő számú szervezetből: $feltetel</h2>";
                        }

                        echo '<div class="table-container">';
                        echo "<table class='table'>";
                        echo "<thead>";
                        echo "<tr>";
                        echo "<th>Work ID</th>";
                        echo "<th>Név</th>";
                        echo "<th>E-mail</th>";
                        echo "<th>Cím</th>";
                        echo "<th>Kar</th>";
                        echo "<th>Szervezetszám</th>";
                        echo "<th>Alkalmazotti kártya</th>";
                        echo "<th>Pozíció</th>";
                        echo "<th>Műveletek</th>";
                        echo "</tr>";
                        echo "</thead>";
                        echo "<tbody>";

                        $month = date('n'); // 'n' a hónap sorszáma levezetésére szolgál (1 és 12 között)
                        $year = date('Y'); // 'Y' a négyjegyű év lekérésére szolgál (pl. 2024)

                        //munkaazonosítókat tartalmazó tömb létrehozása
                        $workerIds = array();
                        foreach ($workers as $worker) {
                            // Megjelenített dolgozó munkaazonosítójának hozzáadása a tömbhöz
                            $workerIds[] = $worker['work_id'];
                            echo "<tr>";
                            echo "<td><a href='profile.php?work_id=" . urlencode($worker['work_id']) . "'>" . htmlspecialchars($worker['work_id']) . "</a></td>";
                            echo "<td><a href='profile.php?work_id=" . urlencode($worker['work_id']) . "'>" . htmlspecialchars($worker['name']) . "</a></td>";
                            echo "<td><a href='profile.php?work_id=" . urlencode($worker['work_id']) . "'>" . htmlspecialchars($worker['email']) . "</a></td>";
                            echo "<td>" . htmlspecialchars($worker['cim']) . "</td>";
                            echo "<td>" . htmlspecialchars($worker['kar']) . "</td>";
                            echo "<td>" . htmlspecialchars($worker['szervezetszam']) . "</td>";
                            echo "<td>" . htmlspecialchars($worker['alkalmazottikartya']) . "</td>";
                            echo "<td>" .getName(htmlspecialchars($worker['position']))  . "</td>";
                            echo "<td>";
                            echo '<form action="export_calendar_month_to_pdf.php" method="post">';
                            echo '<input type="hidden" name="year" value="' . $year . '">';
                            echo '<input type="hidden" name="month" value="' . $month . '">';
                            echo '<input type="hidden" name="work_id" value="' . $worker['work_id']  . '">';
                            echo '<button class="action-button" type="submit" name="export_calendar_month_pdf" value="1">';
                            echo '<img src="icons/picture_as_pdf_20dp_FILL0_wght400_GRAD0_opsz20.png">';
                            echo '</button>';
                            echo '</form>';
                            echo "</td>";
                            echo "</tr>";
                        }

                        echo "</tbody>";
                        echo "</table>";
                        echo "</div>";

                        // Gomb hozzáadása a beosztások exportálásához
                        echo '<form action="export_workers_to_pdf.php" method="post">';
                        // A tömb tartalmának beállítása az input mező értékének
                        echo '<input type="hidden" name="work_ids" value="' . implode(',', $workerIds) . '">';
                        echo '<input type="hidden" name="month" value="' . $month . '">';
                        echo '<input type="hidden" name="year" value="' . $year . '">';
                        echo '<input type="hidden" name="feltetel" value="' . $feltetel . '">';
                        echo '<button class="action-button" type="submit" name="export_workers_pdf" value="1">';
                        echo '<img src="icons/picture_as_pdf_20dp_FILL0_wght400_GRAD0_opsz20.png">';
                        echo ' Összesített beosztások exportálása';
                        echo '</button>';
                        echo '</form>';
                        echo '<p style="margin: 100px"></p>';
                    } else {
                        echo "Nem létezik ilyen szervezetszámű felhasználó: $feltetel";
                    }
                } catch (PDOException $e) {
                    echo "Adatbázis hiba: " . $e->getMessage();
                }
                ?>
            </div>
        </div>
        <div class="footer-div">
            <?php include "footer.php"; ?>
        </div>
    </div>
</div>
<script src="collapse.js"></script>
</body>
</html>

