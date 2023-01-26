<?php require_once('header.php'); ?>
    <div class="container mt-4">
        <div class="row">
            <div class="col-12 text-center">
                <h1><?php echo $pageData['title']; ?></h1>
                <?php
                    if ($pageData['error'])
                        echo '<div class="alert alert-danger">'.$pageData['error'].'</div>';
                    if ($pageData['success'])
                        echo '<div class="alert alert-success">'.$pageData['success'].'</div>';
                    if ($pageData['file'])
                        echo '<img src="/Uploads/'.$pageData['file'].'" class="img-fluid">
                        <p><a href="/view.php?page=dashboard">Перейти к картинкам</a></p>';
                ?>
            </div>
        </div>
    </div>
<?php require_once('footer.php'); ?>