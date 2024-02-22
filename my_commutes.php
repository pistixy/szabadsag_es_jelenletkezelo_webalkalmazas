<?php
include "session_check.php";
include "connect.php";

// Check if the user is logged in
if (!isset($_SESSION['logged']) || !isset($_SESSION['work_id'])) {
    header("Location: login_form.php");
    exit;
}

$userWorkID = $_SESSION['work_id'];

// Get the selected month from the dropdown
if (isset($_GET['month'])) {
    $selectedMonth = $_GET['month'];
    $selectedYear = $_GET['year'];
} else {
    // Default to current month if not set
    $selectedMonth = date('m');
    $selectedYear= date('Y');
}

// Prepare a SQL statement to retrieve all messages received by the logged-in user along with the date from the calendar
// Prepare a SQL statement to retrieve all messages received by the logged-in user along with the date from the calendar
$sql = "SELECT * FROM commute
        WHERE work_id = :userWorkID 
        AND EXTRACT(MONTH FROM date) = :selectedMonth
        AND EXTRACT(YEAR FROM date) = :selectedYear
        ORDER BY date DESC";


$stmt = $conn->prepare($sql);
$stmt->bindParam(':userWorkID', $userWorkID, PDO::PARAM_INT);
$stmt->bindParam(':selectedMonth', $selectedMonth, PDO::PARAM_INT);
$stmt->bindParam(':selectedYear', $selectedYear, PDO::PARAM_INT);
$stmt->execute();
$commutes = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Messages</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="body-container">
    <div class="navbar">
        <?php
        include "nav-bar.php";
        ?>
    </div>
    <div class="main-content">
        <div class="my-commutes">
            <h1>Munkábajárásaim
                <div class="year-month-selector">
                    <form action="" method="GET">
                        <select id="month-select" name="year">
                            <option value="2020" <?php if ($selectedYear == 2020) echo "selected"; ?>>2020</option>
                            <option value="2021" <?php if ($selectedYear == 2021) echo "selected"; ?>>2021</option>
                            <option value="2022" <?php if ($selectedYear == 2022) echo "selected"; ?>>2022</option>
                            <option value="2023" <?php if ($selectedYear == 2023) echo "selected"; ?>>2023</option>
                            <option value="2024" <?php if ($selectedYear == 2024) echo "selected"; ?>>2024</option>
                            <option value="2025" <?php if ($selectedYear == 2025) echo "selected"; ?>>2025</option>
                            <option value="2026" <?php if ($selectedYear == 2026) echo "selected"; ?>>2026</option>
                            <option value="2027" <?php if ($selectedYear == 2027) echo "selected"; ?>>2027</option>
                            <option value="2028" <?php if ($selectedYear == 2028) echo "selected"; ?>>2028</option>
                            <option value="2029" <?php if ($selectedYear == 2029) echo "selected"; ?>>2029</option>
                            <option value="2030" <?php if ($selectedYear == 2030) echo "selected"; ?>>2030</option>
                            <option value="2031" <?php if ($selectedYear == 2031) echo "selected"; ?>>2031</option>
                        </select>
                        <select id="month-select" name="month">
                            <option value="1" <?php if ($selectedMonth == 1) echo "selected"; ?>>Január</option>
                            <option value="2" <?php if ($selectedMonth == 2) echo "selected"; ?>>Február</option>
                            <option value="3" <?php if ($selectedMonth == 3) echo "selected"; ?>>Március</option>
                            <option value="4" <?php if ($selectedMonth == 4) echo "selected"; ?>>Április</option>
                            <option value="5" <?php if ($selectedMonth == 5) echo "selected"; ?>>Május</option>
                            <option value="6" <?php if ($selectedMonth == 6) echo "selected"; ?>>Június</option>
                            <option value="7" <?php if ($selectedMonth == 7) echo "selected"; ?>>Július</option>
                            <option value="8" <?php if ($selectedMonth == 8) echo "selected"; ?>>Augusztus</option>
                            <option value="9" <?php if ($selectedMonth == 9) echo "selected"; ?>>Szeptember</option>
                            <option value="10" <?php if ($selectedMonth == 10) echo "selected"; ?>>Október</option>
                            <option value="11" <?php if ($selectedMonth == 11) echo "selected"; ?>>November</option>
                            <option value="12" <?php if ($selectedMonth == 12) echo "selected"; ?>>December</option>
                        </select>
                        <input type="submit" value="Kiválaszt">
                    </form>
                </div>
                hónapban</h1>
            <?php if (!empty($commutes)): ?>
                <table border="1">
                    <tr>
                        <th>Commute ID</th>
                        <th>Work ID</th>
                        <th>Dátum</th>
                        <th>Hogyam?</th>
                        <th>Honnan?</th>
                        <th>Hová?</th>
                        <th>Fájlnév</th>
                        <th>Ár</th>
                        <th>Távolság (km)</th>
                        <th>Műveletek</th>
                    </tr>
                    <?php foreach ($commutes as $commute): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($commute['commute_id']); ?></td>
                            <td><?php echo htmlspecialchars($commute['work_id']); ?></td>
                            <td><?php echo htmlspecialchars($commute['date']); ?></td>
                            <td><?php echo htmlspecialchars($commute['how']); ?></td>
                            <td><?php echo htmlspecialchars($commute['honnan']); ?></td>
                            <td><?php echo htmlspecialchars($commute['hova']); ?></td>
                            <td><?php echo htmlspecialchars($commute['filename']); ?></td>
                            <td><?php echo htmlspecialchars($commute['price']); ?></td>
                            <td><?php echo htmlspecialchars($commute['km']); ?></td>
                            <td>
                                <!-- Button for deletion -->
                                <form action="delete_commute.php" method="post">
                                    <input type="hidden" name="commute_id" value="<?php echo $commute['commute_id']; ?>">
                                    <button type="submit" name="delete_commute">Törlés</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php else: ?>
                <p>Még nem rögzített munkábajárást.</p>
            <?php endif; ?>
            <p>

            </p>

            <form action="export_commutes_to_pdf.php" method="post">
                <input type="hidden" name="year" value="<?php echo $selectedYear; ?>">
                <input type="hidden" name="month" value="<?php echo $selectedMonth; ?>">
                <input type="hidden" name="work_id" value="<?php echo $userWorkID; ?>">
                <button type="submit" name="export_commutes_to_pdf">PDF generálása</button>
            </form>
        </div>

        <div class="footer-div">
            <?php
            include "footer.php";
            ?>
        </div>
    </div>
</div>
</body>
</html>
