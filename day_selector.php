
<div>
    <fieldset>
        <h2>Válassza ki, mire módosítaná az adott napot!</h2>
        <form action="new_request.php" method="post">

            <label for="fizetett_szabadnap">
                <input type="radio" name="nap" id="fizetett_szabadnap" value="Fizetett Szabadnap" <?php if($calendarResult['day_status']=="0"){
                    echo "checked";
                }?>>
                Fizetett Szabadnap
            </label><br>

            <label for="munkanap">
                <input type="radio" name="nap" id="munkanap" value="Munkanap" <?php if($calendarResult['day_status']=="1"){
                    echo "checked";
                }?>>
                Munkanap
            </label><br>

            <label for="online_munka">
                <input type="radio" name="nap" id="online_munka" value="Online Munka"<?php if($calendarResult['day_status']=="2"){
                    echo "checked";
                }?>>
                Online Munka
            </label><br>

            <label for="betegszabadsag">
                <input type="radio" name="nap" id="betegszabadsag" value="Betegszabadság"<?php if($calendarResult['day_status']=="3"){
                    echo "checked";
                }?>>
                Betegszabadság
            </label><br>

            <label for="fizetetlen_szabadsag">
                <input type="radio" name="nap" id="fizetetlen_szabadsag" value="Fizetetlen szabadság"<?php if($calendarResult['day_status']=="4"){
                    echo "checked";
                }?>>
                Fizetetlen szabadság
            </label><br>

            <label for="tervezett_szabadsag">
                <input type="radio" name="nap" id="tervezett_szabadsag" value="Tervezett szabadság"<?php if($calendarResult['day_status']=="5"){
                    echo "checked";
                }?>>
                Tervezett szabadság
            </label><br>

            <label>Ide írja le kérését és indokolja!
                <textarea name="message" style="width: 100%; height: 200px">
Tisztelt ...!
A <?php echo htmlspecialchars($calendarResult['date']); ?> napot szeretném...

Oka:

Tisztelettel,
<?php
if (isset($_SESSION['work_id'])) {
    $userWorkID = $_SESSION['work_id'];

    $sql = "SELECT * FROM users WHERE work_id = :userWorkID";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':userWorkID', $userWorkID, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($result) == 1) {
        $row = $result[0];
        $name = $row['name'];
        echo $name;
    } else {
        echo "User not found.";
    }
}
?>
    </textarea>
            </label>
            <?php $date=$calendarResult['date']; ?>
            <input type="hidden" name="date" value="<?php echo $date; ?>">
            <input type="submit" name="submit" value="Submit">
    </fieldset>
    </form>


</div>
<?php
include "footer.php";
?>
</body>
</html>