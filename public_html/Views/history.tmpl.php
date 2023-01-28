<?php require_once('header.php'); ?>
    <div class="container mt-4">
        <div class="row">
            <div class="col-12 text-center">
                <h1><?php echo $pageData['title']; ?></h1>
                <?php
                foreach ($pageData['comments'] as $comment) {
                    echo '<p><b>' . $comment['login'] . '</b> - ' . $comment['comment'] . ' (' . $comment['pubdate'] . ')';

                    echo '</p>';
                }
                ?>
            </div>
        </div>
    </div>
<?php require_once('footer.php'); ?>