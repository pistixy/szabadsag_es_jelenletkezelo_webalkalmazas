<!DOCTYPE html>
<html lang="hu-HU">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Széchenyi István Egyetem - Szabadságkezelő bejelentkező oldal</title>
    <link href="public/css/styles2.css" rel="stylesheet">
    <link href="public/css/styles3.css" rel="stylesheet">
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
                    <input class="logininput" type="email" id="email" name="email" placeholder="Egyetemi email cím" required>
                </div>
            </div>
            <div class="row" style="max-width: 500px;margin:0 auto;">
                <div class="col-md-12" >
                    <input class="logininput" type="password" id="password" name="password" placeholder="Jelszó" minlength="8" required>
                </div>
            </div>
            <div class="row" style="max-width: 500px;margin:0 auto;">
                <div class="col-md-12" >
                    <input class="logininput" type="password" id="jelszoujra" name="jelszoujra" placeholder="Jelszó újra" minlength="8" required>
                </div>
            </div>
            <div class="row" style="max-width: 500px;margin:0 auto;">
                <div class="col-md-12" >
                    <input class="logininput" type="text" id="cim" name="cim" placeholder="Lakcím" required>
                </div>
            </div>
            <div class="row" style="max-width: 500px;margin:0 auto;">
                <div class="col-md-12" >
                    <input class="logininput" type="text" id="tax_number" name="tax_number" placeholder="Adóazonosító" required>
                </div>
            </div>
            <div class="row" style="max-width: 500px;margin:0 auto;">
                <div class="col-md-12" >
                    <input class="logininput" type="text" id="entity_id" name="entity_id" placeholder="Szervezetszám" required>
                </div>
            </div>
            <div class="row" style="max-width: 500px;margin:0 auto;">
                <div class="col-md-12" >
                    <input class="logininput" type="text" id="employee_card_number" name="employee_card_number" placeholder="Alkalmazotti kártyaszám" required>
                </div>
            </div>
            <div class="row" style="max-width: 500px;margin:0 auto;">
                <div class="col-md-12" >
                    <label for="letterCode">Kar kiválasztása</label>
                    <select class="logininput" id="letterCode" name="letterCode" required>
                        <option value="ESK">ESK</option>
                        <option value="DFK">DFK</option>
                        <option value="GIVK">GIVK</option>
                        <option value="KGYK">KGYK</option>
                        <option value="MK">MK</option>
                        <option value="EEKK">ÉÉKK</option>
                        <option value="MEK">MÉK</option>
                        <option value="AK">AK</option>
                        <option value="AHJK">AHJK</option>
                    </select>
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
</body>
</html>