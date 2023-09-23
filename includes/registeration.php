<!DOCTYPE html>
<html>
<head>
    <title>Holiday Calendar Regisztráció</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../styles.css">
</head>
<body class="registration_form">
<div id="login-form">
    <h1 style="color: #333333">Üdvözlünk a Holiday Calendarben!</h1>
    <form action="register.php" method="POST">
        <label for="surname">Családnév:</label>
        <input type="text" id="surname" name="surname" placeholder="A családneved.."required>

        <label for="name">Keresztnév:</label>
        <input type="text" id="name" name="name" placeholder="A keresztneved.."required>

        <label for="birthday">Születési Dátum:</label>
        <input type="date" id="birthday" name="birthday" "required>

        <label for="email">E-mail cím:</label>
        <input type="text" id="email" name="email" placeholder="Az email címed.."required>

        <label for="phone">Telefonszám:</label>
        <input type="text" id="phone" name="phone" placeholder="A telefonszámod"required>

        <label for="password">Jelszó:</label>
        <input type="password" id="password" name="password" placeholder="A jelszavad.."required>

        <label for="jelszoujra">Jelszó újra:</label>
        <input type="password" id="jelszoujra" name="jelszoujra" placeholder="A jelszavad újra..">

        <button class="register_button" type="submit" name="register_btn">Regisztráció</button>

    </form>
    <p>Már tag vagy? <a href="loginn.php">Jelentkezz be!</a></p>
    <p>Vissza a kezdőlapra: <a href="../index.php">Holiday Calendar!</a></p>

</div>
</body>
</html>
