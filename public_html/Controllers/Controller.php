<?php
define('COOKIE_TIME', 3600);

Class Controller
{
    public $model;
    public $view;
    public $pdo;
    //public $user;
    protected $pageData = array();
    private $host = 'localhost';
    private $dbname = 'test';
    private $username = 'root';
    private $password = 'root';

    private $opt = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    );

    public function __construct() {

        //$this->view = new View();
        $this->model = new Model();
        $this->pdo = new PDO("mysql:host=$this->host;dbname=$this->dbname", $this->username, $this->password, $this->opt);

    }

    public function setLastTime($userid) {
        $this->pdo->query('update users set lasttime=now() where id=' . $userid . ' limit 1');
    }

    public function getUser ($login) {
        $auth = $this->pdo->query('select * from users where login="' . $login . '" limit 1');
        //var_dump($auth);
        if (isset($auth)) {
            $user = $auth->fetch();
            return $user;
        } else return false;
    }

    public function login($request)
    {
        $user = $this->getUser($request['loginform']);
        if (!isset($user['id'])) $user = $this->register($request);

        if (password_verify($request['passwordform'], $user['password'])) {
            setcookie ("login", $user['login'], time() + COOKIE_TIME);
            setcookie ("hash", Model::cookieHash($user), time() + COOKIE_TIME);
            $this->setLastTime($user['id']);
            return $user;
        }
        else {
            echo ('Ошибка входа');
            return false;
        }
    }

    public function verifyUser()
    {
        $user = $this->getUser($_COOKIE['login']);
        //echo "user_login: ". $user['login'];
        if (password_verify($user['password'], $_COOKIE['hash'])) return $user;
        else return false;
    }

    public function register($request)
    {
        $salt=time();
        $reg = $this->pdo->query('insert into users (login, password) values ("' . $request['loginform'] . '","' . Model::setPassword($request['passwordform']) . '")');
        $user = $this->getUser($request['loginform']);
        $user['newuser'] = true;
        return $user;
    }

    public function dashboard () {
        $this->pageData['tmpl'] = "dashboard";
        $this->pageData['title'] = "Список картинок";
    }

    public function view($page, $user) {
            $this->user = $user;
            $this->$page();
            $pageData=$this->pageData;
            require_once("Views/".$pageData['tmpl'].'.tmpl.php');
    }

    public function addPic () {
        $this->pageData['tmpl'] = "addPic";
        $this->pageData['title'] = "Добавить картинку";
    }

    public function uploadPic () {
        $max_image_width	= 1500;
        $max_image_height	= 1500;
        $max_image_size		= 5*1024*1024;
        $min_image_size		= 250*1024;
        $valid_types 		=  array("gif","jpg", "png", "jpeg","JPG","GIF","PNG");

            if (isset($_FILES["userfile"])) {
                if (is_uploaded_file($_FILES['userfile']['tmp_name'])) {
                    $filename = $_FILES['userfile']['tmp_name'];
                    $ext = substr($_FILES['userfile']['name'],
                        1 + strrpos($_FILES['userfile']['name'], "."));
                    if (filesize($filename) > $max_image_size || filesize($filename) < $min_image_size) {
                        $this->pageData['error'] = 'Ошибка. Файл > 5М или файл < 250кб';
                    } elseif (!in_array($ext, $valid_types)) {
                        $this->pageData['error'] = 'Ошибка: недопустимый формат картинки.';
                    } else {
                        $size = GetImageSize($filename);
                        if (($size) && ($size[0] <= $max_image_width)
                            && ($size[1] <= $max_image_height)) {
                                if (move_uploaded_file($filename, __DIR__."/../Uploads/".$_FILES['userfile']['name'])) {
                                    $this->pageData['file'] = $_FILES['userfile']['name'];
                                    $this->pdo->query('insert into pics (path, author) values ("' . $_FILES['userfile']['name'] . '", ' . $this->user['id'] . ')');
                                    $this->pageData['success'] = 'Картинка успешно загружена!';
                                } else {
                                    $this->pageData['error'] = 'Ошибка записи файла.';
                            }
                        } else {
                            $this->pageData['error'] = 'Ошибка: недопустимые параметры картинки.';
                        }
                    }
                } else {
                    $this->pageData['error'] = 'Ошибка: файл пуст.';
                }
            }

        $this->pageData['tmpl'] = "uploadPic";
        $this->pageData['title'] = "Загрузить картинку";
    }
}