<?php
require_once ('conf.php');

$controller = new Controller();

$user = $controller->verifyUser();
if ($user['id']) {
    $controller->view($_REQUEST['page'], $user, $_GET['id']);
} else {
    echo 'Ошибка авторизации.';
}
