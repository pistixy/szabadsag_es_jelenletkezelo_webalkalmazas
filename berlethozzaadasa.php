<div class="main-content">
    <form action="newpass.php" method="post" enctype="multipart/form-data">
        <fieldset class="comming-to-work">
            <legend>Munkába járás térítési űrlap</legend>
            <div>
                <label for="date">Válassza ki mikor vásárolta:</label>
                <input type="date" id="date" name="date" required>
            </div>
            <div id="PublicTransport">
                <div>
                    <label>Adja meg a havi bérleten szerelplő értéket! (ft)</label>
                    <input type="text" id="price" name="price" >
                </div>
                <div >
                    <label>Töltse fel a havi bérletét:</label>
                    <input type="file" accept="image/gif, image/jpg, image/png, image/jpeg, application/pdf" name="receipt">
                </div>
            </div>
            <input type="submit" name="upload_receipt" value="Feltöltés">
        </fieldset>
    </form>
</div>