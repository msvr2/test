<?php require_once('header.php'); ?>
    <div class="container mt-4">
        <div class="row">
            <div class="col-12 text-center">
                <h1><?php echo $pageData['title'];?></h1>
                    <form action="/view.php?page=uploadPic" enctype="multipart/form-data" method="post">
                        Выбор картинки: <input name="userfile" type="file">
                        <button class="btn btn-success">Загрузить картинку</button><br>
                    </form>
            </div>
        </div>
    </div>
<?php require_once('footer.php'); ?>