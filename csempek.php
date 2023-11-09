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
            if (isset($_SESSION['logged']) && isset($_SESSION['isAdmin']) && $_SESSION['isAdmin']) {

                echo ' <a href="letszamjelentes.php">Letszámjelentés</a>';
            }

            ?>

        </td>
        <td>
            <?php
            if (isset($_SESSION['logged']) && isset($_SESSION['isAdmin']) && $_SESSION['isAdmin']) {

                echo ' <a href="jelenletiiv.php">Jelenléti Ív</a>';
            }

            ?>

        </td>
        <td>

        </td>
    </tr>
</table>