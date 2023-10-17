<!DOCTYPE html>
<html lang="hu-HU">
<head>
    <title>Holiday Calendar Regisztráció</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styles.css">
</head>
<body class="registration_form">
<div id="login-form">
    <h1 style="color: #333333">Üdvözlünk a Holiday Calendarben!</h1>
    <form action="register.php" method="POST">
        <label for="name">Teljes név:</label>
        <input type="text" id="name" name="name" placeholder="A neved.." required>

        <label for="email">Egyetemi e-mail cím:</label>
        <input type="text" id="email" name="email" placeholder="Az email címed.." required>

        <label for="password">Jelszó:</label>
        <input type="password" id="password" name="password" placeholder="A jelszavad.." required>

        <label for="jelszoujra">Jelszó újra:</label>
        <input type="password" id="jelszoujra" name="jelszoujra" placeholder="A jelszavad újra.." required>

        <label for="cim">Lakcím:</label>
        <input type="text" id="cim" name="cim" placeholder="A lakcímed.." required>

        <label for="adoazonosito">Adóazonosító:</label>
        <input type="text" id="adoazonosito" name="adoazonosito" placeholder="Az adóazonosítód.." required>

        <label for="szervezetszam">Szervezetszám:</label>
        <input type="text" id="szervezetszam" name="szervezetszam" placeholder="A szervezetszámod.." required>

        <label for="alkalmazottikartyaszama">Alkalmazotti kártyaszám:</label>
        <input type="text" id="alkalmazottikartyaszama" name="alkalmazottikartyaszama" placeholder="Az alkalmazotti kártyaszámod.." required>

        <button class="register_button" type="submit" name="register_btn">Regisztráció</button>

    </form>
    <p>Már tag vagy? <a href="login_form.php">Jelentkezz be!</a></p>
    <p>Vissza a kezdőlapra: <a href="index.php">Holiday Calendar!</a></p>

</div>
<?php
include "footer.php";
?>
</body>
</html>
