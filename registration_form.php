<!DOCTYPE html>
<html lang="hu-HU">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Széchenyi István Egyetem - Szabadságkezelő bejelentkező oldal</title>
    <link href="styles2.css" rel="stylesheet">
    <link href="styles3.css" rel="stylesheet">
</head>
<body>

<div class="loginbox">
    <div class="logoline">

        <div class="szelogo" onclick="window.location='http://uni.sze.hu/';"></div>
    </div>
    <div class="innerbox">
        <h1 class="loginheader">Kérjük regisztráljon!</h1>
        <div class="line"></div>
        <h2 class="loginreq"> Please register!</h2>
        <form action="register.php" method="post">


            <div class="row" style="max-width: 500px;margin:0 auto;">
                <div class="col-md-12" >
                    <input class="logininput" type="text" id="name" name="name" placeholder="Teljes név" required>
                </div>
            </div>
            <div class="row" style="max-width: 500px;margin:0 auto;">
                <div class="col-md-12" >
                    <input class="logininput" type="text" id="email" name="email" placeholder="Egyetemi email cím" required>
                </div>
            </div>
            <div class="row" style="max-width: 500px;margin:0 auto;">
                <div class="col-md-12" >
                    <input class="logininput" type="password" id="password" name="password" placeholder="Jelszó" required>
                </div>
            </div>
            <div class="row" style="max-width: 500px;margin:0 auto;">
                <div class="col-md-12" >
                    <input class="logininput" type="password" id="jelszoujra" name="jelszoujra" placeholder="Jelszó újra" required>
                </div>
            </div>
            <div class="row" style="max-width: 500px;margin:0 auto;">
                <div class="col-md-12" >
                    <input class="logininput" type="text" id="cim" name="cim" placeholder="Lakcím" required>
                </div>
            </div>
            <div class="row" style="max-width: 500px;margin:0 auto;">
                <div class="col-md-12" >
                    <input class="logininput" type="text" id="adoazonosito" name="adoazonosito" placeholder="Adóazonosító" required>
                </div>
            </div>
            <div class="row" style="max-width: 500px;margin:0 auto;">
                <div class="col-md-12" >
                    <input class="logininput" type="text" id="szervezetszam" name="szervezetszam" placeholder="Szervezetszám" required>
                </div>
            </div>
            <div class="row" style="max-width: 500px;margin:0 auto;">
                <div class="col-md-12" >
                    <input class="logininput" type="text" id="alkalmazottikartyaszama" name="alkalmazottikartyaszama" placeholder="Alkalmazotti kártyaszám" required>
                </div>
            </div>
            <p class="reminder">  <a class="newreg" href="login_form.php" target="_blank">Már tag? - Already a member? </a> / <a class="newreg" href="index.php">Vissza a kezdőlapra! - Back to the landing page! </a></p>
            <div class="row" style="max-width: 500px;margin:0 auto;padding-bottom: 95px;">
                <div class="col-md-12">
                    <input class="loginbutton" type="submit" value="Regisztráció / Register">
                </div>
            </div>
        </form>
    </div>

</div>

<?php
include "footer.php";
?>
</body>
</html>