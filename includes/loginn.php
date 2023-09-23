<!DOCTYPE html>
<html>
<head>
    <title>PedalBlog Bejelentkezés</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
<div id="login-form">
    <h1>Üdvözlünk a Holiday Calendarben!</h1>
    <form action="login.php" method="post">
        <label for="email">E-mail cím:</label>
        <input type="email" id="email" name="email" placeholder="Az emailed.." required>
        <label for="password">Jelszó:</label>
        <input type="password" id="password" name="password" placeholder="A jelszavad"required>
        <input type="submit" value="Bejelentkezés">
    </form>
    <p>Még nem vagy tag? <a href="registeration.php">Regisztrálj!</a></p>
    <p>Vissza a kezdőlapra: <a href="../index.php">Holiday Calendar!</a></p>
</div>
</body>
</html>
