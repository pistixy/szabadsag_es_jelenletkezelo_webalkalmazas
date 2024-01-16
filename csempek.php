<?php
include "check_login.php";
?>
<table class="csempe">
    <tr>
        <td>
            <a href="profile.php">Profil megtekintése</a>
        </td>
        <td>
            <a href="calendar.php">Naptáram</a>
        </td>
        <td>
            <a href="comingtowork.php">Munkába járás</a>
        </td>
    </tr>
    <tr>
        <td>
            <a href="https://www.uni.sze.hu"> Széchenyi Egyetem oldala</a>
        </td>
        <td>
            <a href="hrsegedlet.php">HR segédlet</a>
        </td>
        <td>
            <a href="logout.php">Kijelentkezés</a>
        </td>
    </tr>

    <tr>
        <td>
            <?php
            if (isset($_SESSION['logged']) && $_SESSION['isAdmin']) {
                echo ' <a href="letszamjelentes.php">Letszámjelentés</a>';
            }

            ?>

        </td>
        <td>
            <?php
            if (isset($_SESSION['logged']) && $_SESSION['isAdmin']) {
                echo ' <a href="jelenletiiv.php">Jelenléti Ív</a>';
            }

            ?>

        </td>
        <td>
            <?php
            if (isset($_SESSION['logged']) && $_SESSION['isAdmin']) {
                $workId = $_SESSION['work_id'];
                $stmt = $conn->prepare("SELECT szervezetszam FROM users WHERE work_id = :work_id");
                $stmt->bindParam(':work_id', $workId, PDO::PARAM_INT);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $szervezetszam = $result['szervezetszam'] ?? '';

                echo '<div class="csempe-item">';
                echo '<h class="csempe-heading">Szervezetszám:</h>';
                echo '<form action="workers.php" method="post" class="csempe-form">';
                echo '<input type="text" name="szervezetszam" value="' . htmlspecialchars($szervezetszam) . '" />';
                echo '<input type="submit" value="Dolgozók lekérdezése" class="csempe-button">';
                echo '</form>';
                echo '</div>';
            }
            ?>
        </td>
    </tr>
</table>