<!DOCTYPE html>
<html lang="hu-HU">
<!--HTML form a bejelentkezéshez -->
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
            <h1 class="loginheader">Kérjük jelentkezzen be!</h1>
            <div class="line"></div>
            <h2 class="loginreq"> Please login!</h2>
            <form action="login.php" method="post">
                <div class="row" style="max-width: 500px;margin:0 auto;">
                    <div class="col-md-12" >

                        <input class="logininput" type="text" placeholder="Emailcím / Email address" name="email" id="email">
                    </div>
                </div>
                <div class="row" style="max-width: 500px;margin:0 auto;margin-top:20px;margin-bottom: 10px;">
                    <div class="col-md-12" >

                        <input class="logininput" type="password" placeholder="Jelszó / Password" name="password" id="password">
                    </div>
                </div>
                <p class="reminder">  <a class="newreg" href="https://user.sze.hu/main/lostpassword" target="_blank"> Elfelejtette jelszavát? - Forgot account? </a> / <a class="newreg" href="registration_form.php"> Még nem regisztrált? - Sign Up</a> </p>
                <div class="row" style="max-width: 500px;margin:0 auto;padding-bottom: 95px;">
                    <div class="col-md-12">
                        <input class="loginbutton" type="submit" value="Belépés / Login">
                    </div>
                </div>
            </form>
        </div>

    </div>
</body>