<div class="main-content">
    <form action="newpass.php" method="post" enctype="multipart/form-data">
        <fieldset class="styled-fieldset">
            <div class="form-group">
                <label for="date" class="">Válassza ki mikor vásárolta:</label>
                <input type="date" id="date" name="date" required class="styled-input">
            </div>
            <div id="PublicTransport" class="form-group">
                <div>
                    <label class="">Adja meg a havi bérleten szereplő értéket! (ft)</label>
                    <input type="text" id="price" name="price" class="styled-input">
                </div>
                <div>
                    <label class="">Töltse fel a havi bérletét:</label>
                    <input type="file" accept="image/gif, image/jpg, image/png, image/jpeg, application/pdf" name="receipt" class="styled-input">
                </div>
            </div>
            <button class="action-button action-button-bigger" type="submit" name="upload_receipt">
                <img src="public/images/icons/upload_2_20dp_FILL0_wght400_GRAD0_opsz20.png" alt="Upload">
                Feltöltés
            </button>
        </fieldset>
    </form>
</div>
