    <?php
    include "session_check.php";
    include "connect.php";
    include "function_get_status_name.php";
    // Check if the user is logged in
    if (!isset($_SESSION['logged']) || !isset($_SESSION['work_id'])) {
        header("Location: login_form.php");
        exit;
    }
    $userWorkID = $_SESSION['work_id'];
    if (isset($_GET['work_id'])) {
        // Retrieve work_id from URL parameters
        $work_id = $_GET['work_id'];

        // Now you can use $work_id in your code as needed
        //echo "             Work ID: " . $work_id;
    } else {
        // Handle case where work_id is not provided
        echo "Work ID not provided in the URL.";
    }
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
            <?php
            // Assume $work_id contains the work_id of the user
            // Fetch user data from the database
            $stmt = $conn->prepare("SELECT * FROM users WHERE work_id = :work_id");
            $stmt->bindParam(':work_id', $work_id);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            ?>

            <div class="holidays">
                <?php
                if ($user):
                    if ($userWorkID == $work_id) {
                        echo "<h2>Szabadnapjaim állása</h2>";
                    } else {
                        echo "<h2><a href='profile.php?work_id=" . $work_id . "'>" . $user['name'] . "</a> szabadnapjainak állása</h2>";
                    }
                    ?>
                    <table border="1">
                        <thead>
                        <tr>
                            <th>Típus</th>
                            <th>Mennyiség</th>
                            <th>Műveletek</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($user as $key => $value): ?>
                            <?php if ($key !== 'work_id' && $key !== 'name' && $key !== 'email' && $key !== 'password' && $key !== 'cim' && $key !== 'adoazonosito' && $key !== 'szervezetszam' && $key !== 'alkalmazottikartya' && $key !== 'position' && $key !== 'profile_picture' && $key !== 'kar'): ?>
                                <tr>
                                    <td><?php echo getStatusName($key); ?></td>
                                    <td><?php echo $value; ?></td>
                                    <td style="display: flex">
                                        <?php if (in_array($key, ['payed_free', 'payed_edu_free', 'payed_award_free', 'unpayed_dad_free', 'unpayed_home_free', 'unpayed_free']) && ($_SESSION['position'] === 'dekan' || $_SESSION['position'] === 'admin')): ?>
                                            <form action="increase_day.php" method="post">
                                                <input type="hidden" name="work_id" value="<?php echo $work_id; ?>">
                                                <input type="hidden" name="status" value="<?php echo $key; ?>">
                                                <button type="submit">+1</button>
                                            </form>
                                        <?php endif; ?>

                                        <?php if (in_array($key, ['payed_free', 'payed_edu_free', 'payed_award_free', 'unpayed_dad_free', 'unpayed_home_free', 'unpayed_free']) && ($_SESSION['position'] === 'dekan' || $_SESSION['position'] === 'admin')): ?>
                                            <form action="decrease_day.php" method="post">
                                                <input type="hidden" name="work_id" value="<?php echo $work_id; ?>">
                                                <input type="hidden" name="status" value="<?php echo $key; ?>">
                                                <button type="submit">-1</button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>User not found</p>
                <?php endif; ?>
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