<?php
require_once ('conf.php');

$controller = new Controller();

$user = $controller->verifyUser();
if ($user['id']) {
    $controller->smallPic('Uploads/' . $_GET['file']);
} else echo "Файл недоступен.";
