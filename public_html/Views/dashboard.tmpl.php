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
                <div class="row">
                    <?php
                        //var_dump($pageData);
                        foreach ($pageData['pics'] as $pic) {
                            echo '
                                <div class="col-2 m-2 p-2">
                                <img src="/picsmall.php?file='.$pic['path'].'">
                                <p>Комментариев: '.$pic['comments'].'</p>
                                <p><a href="/view.php?page=viewPic&id='.$pic['id'].'">Подробнее</a></p>
                                </div>
                            ';
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>
<?php require_once('footer.php'); ?>