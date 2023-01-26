<?php
define('COOKIE_TIME', 3600);

Class Controller
{
    public $model;
    public $view;
    public $pdo;
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
        return $user;
    }

    public function dashboard () {
            $this->pageData['tmpl'] = "dashboard";
            $this->pageData['title'] = "Dashboard";
    }

    public function view($page) {
            $this->$page();
            $pageData=$this->pageData;
            require_once("Views/".$pageData['tmpl'].'.tmpl.php');
    }

}