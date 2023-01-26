<?php
require_once ('conf.php');

$controller = new Controller();
$user = $controller->login($_REQUEST);
if ($user['id']) header('Location: /view.php?page=dashboard');
