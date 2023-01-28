<?php require_once('header.php'); ?>
    <div class="container mt-4">
        <div class="row">
            <div class="col-12 text-center">
                <h1><?php echo $pageData['title']; ?></h1>
                <?php
                    //var_dump($pageData);
                    if ($pageData['error'])
                        echo '<div class="alert alert-danger">'.$pageData['error'].'</div>';
                    if ($pageData['success'])
                        echo '<div class="alert alert-success">'.$pageData['success'].'</div>';

                        echo '<img src="/pic.php?file='.$pageData['pic']['path'].'" class="img-fluid">
                        <p>Опубликовано: <b>' . $pageData['pic']['login'] . '</b> (' . $pageData['pic']['pubdate'] . ')</p>
                        <p>Количество просмотров: ' . $pageData['pic']['viewcount'] . '</p>
                        <p><a href="/view.php?page=dashboard">Перейти к картинкам</a></p>';
                ?>
                <h2>Комментарии</h2>
                <?php
                    foreach ($pageData['comments'] as $comment) {
                        echo '<p><b>' . $comment['login'] . '</b> - ' . $comment['comment'] . ' (' . $comment['pubdate'] . ')';
                        if ($comment['user'] == $this->user['id'] && strtotime($comment['pubdate']) >= time() - 5 * 60) {
                            echo ' <sup>(<a href="/view.php?page=editComment&id='.$comment['cid'].'">Редактировать</a>';
                            echo ' | <a href="/view.php?page=deleteComment&id='.$comment['base'].'">Удалить</a>)</sup>';
                        }
                        if ($comment['parent']) {
                            echo ' <sup>[Edited]</sup>';
                            if ($comment['user'] == $this->user['id'])
                                echo ' <sup>(<a href="/view.php?page=history&id='.$comment['base'].'">История</a>)</sup>';
                        }
                        echo '</p>';
                    }
                ?>
                <form action="/view.php?page=addComment&id=<?php echo $pageData['pic']['picid'];?>" method="post">
                    <p><textarea type="text" name="commentform" class="form-control" id="commentform" placeholder="Ваш комментарий"></textarea>
                    <br>
                    <button class="btn btn-success">Оставить комментарий</button><br>
                </form>
            </div>
        </div>
    </div>
<?php require_once('footer.php'); ?>