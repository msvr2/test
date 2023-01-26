<?php require_once('header.php'); ?>
    <div class="container mt-4">
        <div class="row">
            <div class="col-12 text-center">
                <?php if ($_GET['newuser']) {
                    echo '<div class="alert alert-info">Поздравляем с регистрацией!</div>';
                }?>
                <h1><?php echo $pageData['title'];?></h1>
                <form action="/view.php?page=addPic" method="post">
                    <button class="btn btn-success">Добавить картинку</button><br>
                </form>
            </div>
        </div>
    </div>
<?php require_once('footer.php'); ?>