<?php
require_once ('conf.php');

$controller = new Controller();

$user = $controller->verifyUser();
if ($user['id']) {
    $file = file_get_contents('Uploads/'.$_GET['file']);
    echo $file;
} else echo "Файл недоступен.";
