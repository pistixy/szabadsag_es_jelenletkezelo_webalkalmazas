<?php
include "session_check.php";
include "connect.php";
include "function_translate_month_to_Hungarian.php";
include "function_get_name.php";

// Check if the user is logged in
if (!isset($_SESSION['logged']) || !isset($_SESSION['work_id'])) {
    header("Location: login_form.php");
    exit;
}

// Get the work ID either from the URL parameter or from the session
if (isset($_GET['work_id'])) {
    $userWorkID = $_GET['work_id'];
} else {
    $userWorkID = $_SESSION['work_id'];
}

//echo $userWorkID;
//exit; //debughoz
// Get the selected month and year from the dropdown
if (isset($_GET['month']) && isset($_GET['year'])) {
    $selectedMonth = $_GET['month'];
    $selectedYear = $_GET['year'];
} else {
    // Default to current month and year if not set
    $selectedMonth = date('m');
    $selectedYear = date('Y');
}

// Prepare a SQL statement to retrieve all commute records for the selected user, month, and year
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

// Prepare a SQL statement to retrieve user information
$sql = "SELECT work_id, name FROM users WHERE work_id = :userWorkID";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':userWorkID', $userWorkID, PDO::PARAM_INT);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Munkába járások</title>
    <link rel="stylesheet" href="public/css/styles.css">
</head>
<body>
<?php include "navigation_bar-top.php"; ?>
<div class="body-container">
    <div class="navbar" id="sidebar">
        <?php include "navigation_bar-side.php"; ?>
    </div>
    <div class="main-content" id="main-content">
        <div class="test_content">

            <div class="my-commutes">
                <h1>
                    <?php if($_SESSION['work_id']==$userWorkID){
                        echo "Munkába járásaim";
                    }
                    else{
                        echo '<a href="profile.php?work_id=' . $userWorkID . '">' . $users[0]['name'] . '</a> ' . $selectedYear . ' ' . translateMonthToHungarian($selectedMonth) . 'i munkába járásai';
                    }
                    ?>
                </h1>
                <div class="year-month-selector-container">
                    <form action="" method="GET" class="year-month-selector-form">
                        <div class="form-group">
                            <label for="year-select">Év:</label>
                            <select id="year-select" name="year">
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
                        </div>
                        <div class="form-group">
                            <label for="month-select">Hónap:</label>
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
                        </div>
                        <input type="hidden" name="work_id" value="<?php echo $userWorkID; ?>">
                        <button type="submit" class="action-button">
                            <img src="public/images/icons/check_20dp_FILL0_wght400_GRAD0_opsz20.png" alt="Select">
                            Kiválaszt
                        </button>
                    </form>
                </div>
                <h2>hónapban</h2>
                <?php if (!empty($commutes)): ?>
                    <div class="table-container">
                        <table class="table">
                            <tr>
                                <th>Commute ID</th>
                                <th>Work ID</th>
                                <th>Dátum</th>
                                <th>Hogyan?</th>
                                <th>Fájlnév</th>
                                <th>Ár</th>
                                <th>Bérlet?</th>
                                <th>Műveletek</th>
                            </tr>
                            <?php foreach ($commutes as $commute): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($commute['commute_id']); ?></td>
                                    <td><?php echo htmlspecialchars($commute['work_id']); ?></td>
                                    <td><?php echo htmlspecialchars($commute['date']); ?></td>
                                    <td><?php echo htmlspecialchars(getName($commute['how'])); ?></td>
                                    <td><?php echo htmlspecialchars($commute['filename']); ?></td>
                                    <td><?php echo htmlspecialchars($commute['price']); ?></td>
                                    <td><?php echo ($commute['how'] == "Pass") ? "Igen" : "Nem"; ?></td>
                                    <td class="center-content">
                                        <!-- Button for deletion -->
                                        <form action="delete_commute.php" method="post">
                                            <input type="hidden" name="commute_id" value="<?php echo $commute['commute_id']; ?>">
                                            <button class="action-button" type="submit" name="delete_commute">
                                                <img src="public/images/icons/delete_20dp_FILL0_wght400_GRAD0_opsz20.png" alt="Delete">
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                <?php else: ?>
                    <p>Még nem rögzített munkábajárást.</p>
                <?php endif; ?>
                <p></p>
                <form action="export_commutes_to_pdf.php" method="post">
                    <input type="hidden" name="year" value="<?php echo $selectedYear; ?>">
                    <input type="hidden" name="month" value="<?php echo $selectedMonth; ?>">
                    <input type="hidden" name="work_id" value="<?php echo $userWorkID; ?>">
                    <button class="action-button" type="submit" name="export_commutes_to_pdf"><img src="public/images/icons/picture_as_pdf_20dp_FILL0_wght400_GRAD0_opsz20.png">PDF generálása</button>
                </form>
            </div>
        </div>
        <div class="footer-div">
            <?php include "footer.php"; ?>
        </div>
    </div>
</div>
<script src="public/js/collapse.js"></script>
</body>
</html>
