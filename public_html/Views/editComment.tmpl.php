<?php require_once('header.php'); ?>
    <div class="container mt-4">
        <div class="row">
            <div class="col-12 text-center">
                <h1><?php echo $pageData['title']; ?></h1>
                <form action="/view.php?page=saveComment&id=<?php echo $_GET['id'];?>" method="post">
                    <p><textarea type="text" name="commentform" class="form-control" id="commentform" placeholder="Ваш комментарий"><?php echo $pageData['comment']['comment'];?></textarea>
                    <p><button class="btn btn-success">Сохранить</button></p>
                </form>
            </div>
        </div>
    </div>
<?php require_once('footer.php'); ?>