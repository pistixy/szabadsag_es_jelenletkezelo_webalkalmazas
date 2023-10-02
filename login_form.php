<!DOCTYPE html>
<html>
<head>
    <title>Holiday Calendar Bejelentkezés</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styles.css">
</head>
<body style="background-color: #f2f2f2;font-family: sans-serif;">
<div class="login-form">
    <h1>Üdvözlünk a Holiday Calendarben!</h1>
    <form action="login.php" method="post">
        <label for="email">E-mail cím:</label>
        <input type="text" id="email" name="email" placeholder="Az emailed.." required>
        <label for="password">Jelszó:</label>
        <input type="password" id="password" name="password" placeholder="A jelszavad"required>
        <input type="submit" value="Bejelentkezés">
    </form>
    <p>Még nem vagy tag? <a href="registration_form.php">Regisztrálj!</a></p>
    <p>Vissza a kezdőlapra: <a href="index.php">Holiday Calendar!</a></p>
</div>
</body>
</html>
