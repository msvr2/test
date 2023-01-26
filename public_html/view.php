<?php
require_once ('conf.php');

$controller = new Controller();

if ($controller->verifyUser()) {
    $controller->view($_REQUEST['page']);
}
