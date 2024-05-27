<?php
include "session_check.php";
include "connect.php";
include "function_get_name.php";

if (!isset($_SESSION['logged'])) {
    header("Location: login_form.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>HR segédlet</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .hr-segedlet {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            line-height: 1.6;
            font-family: 'Montserrat', Tahoma, sans-serif;
            margin-bottom: 20px;
        }
        .hr-segedlet h3 {
            color: #333;
            margin-bottom: 10px;
        }
        .hr-segedlet p {
            margin-bottom: 15px;
        }
        .hr-segedlet .section-title {
            font-weight: bold;
            color: #0069dc;
        }
    </style>
</head>
<body>
<?php include "navigation_bar-top.php"; ?>
<div class="body-container">
    <div class="navbar" id="sidebar">
        <?php include "navigation_bar-side.php"; ?>
    </div>
    <div class="main-content" id="main-content">
        <div class="test_content">
            <div class="hr-segedlet">
                <h3>HR segédlet</h3>
                <div>
                    <p><span class="section-title">F – Fizetett szabadság</span><br>
                        A fizetett szabadság olyan szabadság típus, amivel minden munkavállaló rendelkezhet. Ez a munkatörvénykönyve alapján számítandó. Munkabérhez jogosult a felhasználója.</p>

                    <p><span class="section-title">E – Fizetett előző évi szabadság</span><br>
                        A fizetett előző évi szabadság olyan szabadság típus, ami akkor kerül jóváírásra, amikor a felhasználó nem használta fel teljes egészében a fizetett szabadságait. Legfeljebb 5 darab ilyen szabadságot engedünk meg, amit folytatólagosan kell kivenni egy előző évben megkezdett szabadsághoz. Az év elteltével, kizárólag a fizetett szabadság kerül jóváírásra a következő évben. Munkabérhez jogosult a felhasználója. Fontos, hogy ha van szabadság ebből a típusból, akkor ennek kell előbb elfogynia, amennyiben lehetséges.</p>

                    <p><span class="section-title">T - Tanulmányi szabadság</span><br>
                        Továbbképzésekhez adhatják ki. Munkabérhez jogosult a felhasználója. Jóváhagyás szükséges.</p>

                    <p><span class="section-title">J – Jutalomszabadság</span><br>
                        Jutalomból adhatják ki, lényegében ez is egy fizetett szabadságtípus, hiszen munkabérhez jogosult a felhasználója. Jóváhagyás itt is kötelező.</p>

                    <p><span class="section-title">I - Fizetés nélküli igazolt távollét</span><br>
                        Van lehetőség a munkavégzéstől távol lenni fizetettlen módon is. Ekkor a felhasználó nem jogosult a munkabérre. Fontos, hogy ezen módon is, ahogy megannyi más módon is, itt is szükséges a szabadság jóváhagyása.</p>

                    <p><span class="section-title">B – Betegszabadság</span><br>
                        A betegszabadság egyedi, hiszen nem szükséges, hogy azt előzetesen jóváhagyják. A munkabérre nem jogosult a felhasználó, kizárólag társadalombiztosítási pótlékra.</p>

                    <p><span class="section-title">H – Igazolatlan távollét</span><br>
                        Az igazolatlan távollét is egy speciális típus, hiszen ezt nem adhatja meg a felhasználó maga. Ezt a típust a felsőbb vezetők (Lásd A felhasználókról) jelölhetik be az alkalmazottaknak. (Lásd Igazolatlan hiányzás jelölése).</p>

                    <p><span class="section-title">A – Apanap</span><br>
                        Az apanap vagy apasági szabadság egyfajta fizetettlen szabadság típus. Ekkor a felhasználó nem jogosult a munkabérre. Elsősorban apukák kaphatják, évi 5 napot. Fontos, hogy ezen módon is, ahogy megannyi más módon is, itt is szükséges a szabadság jóváhagyása.</p>

                    <p><span class="section-title">O – Home office</span><br>
                        A home office egy fizetett szabadság típus, az alkalmazásban létezik, mint szabadságolási forma, de sokat nem tudunk róla, későbbi fejlesztések miatt viszont jó, hogy ez is megvan. A legtöbbről a MT nem nyilatkozik, sokszor ezek saját definíciók.</p>

                    <p><span class="section-title">Munkanap</span><br>
                        A munkanapok minden hétfőtől péntekig terjedő intervallumba eső nap, ami nem ünnepnap. Ezeket az alkalmazások belül „munkanapnak” hívjuk, a kódban pedig „work_day” kulcsszóval találhatóak meg könnyedén.</p>

                    <p><span class="section-title">Munkaszüneti nap</span><br>
                        Munkaszüneti nap minden olyan nap ami szombatra vagy vasárnapra esik, és nem ünnepnap. Ezeket az alkalmazások belül „hétvégének” hívjuk, a kódban pedig „weekend” kulcsszóval találhatóak meg könnyedén.</p>

                    <p><span class="section-title">Ünnepnap</span><br>
                        Az ünnepnapok egy speciális eset, a naptárban piros betűvel jelölt napok. Ezeket a napokat a holidayarray.php file listázza. Fontos, hogy ezt a tömböt</p>
                </div>
            </div>
        </div>
        <div class="footer-div">
            <?php include "footer.php"; ?>
        </div>
    </div>
</div>
<script src="collapse.js"></script>
</body>
</html>
