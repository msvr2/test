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
        if (password_verify($user['password'], $_COOKIE['hash'])) {
            setcookie ("login", $user['login'], time() + COOKIE_TIME);
            setcookie ("hash", Model::cookieHash($user), time() + COOKIE_TIME);
            $this->setLastTime($user['id']);
            return $user;
        }
        else {
            $this->pageData['error'] = 'Ошибка авторизации.';
            return false;
        }
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
        $pics = $this->pdo->query('select * from pics');
        if (isset($pics))
            $this->pageData['pics'] = $pics->fetchAll(\PDO::FETCH_ASSOC);
        $this->pageData['tmpl'] = "dashboard";
        $this->pageData['title'] = "Список картинок";
    }

    public function view($page, $user, $id = NULL) {
            $this->user = $user;
            $result = $this->$page($id);
            $pageData=$this->pageData;
            require_once("Views/".$pageData['tmpl'].'.tmpl.php');
    }

    public function addPic () {
        $this->pageData['tmpl'] = "addPic";
        $this->pageData['title'] = "Добавить картинку";
    }

    public function smallPic ($filename) {

        $ext = substr($filename, 1 + strrpos($filename, "."));

        // задание максимальной ширины и высоты
        $width = 100;
        $height = 100;

        // тип содержимого
        header('Content-Type: image/jpeg');

        // получение новых размеров
        list($width_orig, $height_orig) = getimagesize($filename);

        $ratio_orig = $width_orig/$height_orig;

        if ($width/$height > $ratio_orig) {
           $width = $height*$ratio_orig;
        } else {
            $height = $width/$ratio_orig;
        }

        // ресэмплирование
        $image_p = imagecreatetruecolor($width, $height);
        switch ($ext) {
            case 'jpg':
                $image = imagecreatefromjpeg($filename);
                break;
            case 'jpeg':
                $image = imagecreatefromjpeg($filename);
                break;
            case 'png':
                $image = imagecreatefrompng($filename);
                break;
            case 'gif':
                $image = imagecreatefromgif($filename);
                break;
        }
        //echo "view";
        imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);

        // вывод
        //imagepng($image);
        imagejpeg($image_p, null, 100);
    }

    public function uploadPic () {
        $max_image_width	= 1500;
        $max_image_height	= 1500;
        $max_image_size		= 5*1024*1024;
        $min_image_size		= 250*1024;
        $valid_types 		=  array("gif","jpg", "png", "jpeg","JPG","GIF","PNG");

        $error = false;

            if (isset($_FILES["userfile"]) && $this->user['id']) {
                if (is_uploaded_file($_FILES['userfile']['tmp_name'])) {
                    $filename = $_FILES['userfile']['tmp_name'];
                    $hash = hash('sha256', file_get_contents($_FILES['userfile']['tmp_name']));

                    $verifypic = $this->pdo->query('select * from pics order by id DESC');
                    if (isset($verifypic)) {
                        $pic = $verifypic->fetch();
                        if (strtotime($pic['pubdate']) >= time() - 3 * 60) {
                            $this->pageData['error'] = 'Ошибка. Нельзя загружать картинки чаще 1 раза в 3 минуты.';
                            $error = true;
                        }
                    }

                    if (!$error) {
                        $verifypic = $this->pdo->query('select * from pics where hash = "' . $hash . '" order by id DESC');
                        if (isset($verifypic)) {
                            $pic = $verifypic->fetch();
                            if (strtotime($pic['pubdate']) >= time() - 15 * 60) {
                                $this->pageData['error'] = 'Ошибка. Нельзя загружать 2 и более одинаковых картинки, т.к. не прошло 15 минут.';
                                $error = true;
                            }
                        }
                    }

                    if (!$error) {
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
                                $time = time();
                                if (move_uploaded_file($filename, __DIR__ . "/../Uploads/" . $time . $_FILES['userfile']['name'])) {
                                    $this->pageData['file'] = $_FILES['userfile']['name'];
                                    $this->pdo->query('insert into pics (path, author, hash, pubdate) values ("' . $time . $_FILES['userfile']['name'] . '", ' . $this->user['id'] . ', "' . $hash . '", now())');
                                    $this->pageData['success'] = 'Картинка успешно загружена!';
                                } else {
                                    $this->pageData['error'] = 'Ошибка записи файла.';
                                }
                            } else {
                                $this->pageData['error'] = 'Ошибка: недопустимые параметры картинки.';
                            }
                        }
                    } // !error
                } else {
                    $this->pageData['error'] = 'Ошибка: файл пуст.';
                }
            }

        $this->pageData['tmpl'] = "uploadPic";
        $this->pageData['title'] = "Загрузить картинку";
    }

    public function viewPic ($id) {
        $this->pageData['pic'] = $this->pdo->query('select *, pics.id as picid, users.id as uid from pics, users where pics.id = '.$id.' and users.id = pics.author limit 1')->fetch(\PDO::FETCH_ASSOC);
        $this->pageData['comments'] = $this->pdo->query('select *, comments.id as cid from comments, users where pic = '.$id.' and edited=0 and users.id = comments.user')->fetchAll(\PDO::FETCH_ASSOC);
        $this->pageData['tmpl'] = "viewPic";
        $this->pageData['title'] = "Просмотр картинки";
        $this->pdo->query('update pics set viewcount = viewcount + 1 where id='.$this->pageData['pic']['picid'].' limit 1');
        //echo date_default_timezone_get().'<br>'.time().'<br>'.strtotime($this->pageData['pic']['pubdate']);
    }

    public function addComment () {
        if (!preg_match('/((?<![а-я])(лес|поляна|озеро)(?![а-я]))/i', $_POST['commentform'])) {
            $query = 'insert into comments (pic, user, comment) values (' . $_GET['id'] . ', ' . $this->user['id'] . ', "' . $_POST['commentform'] . '")';
            $this->pdo->query($query);
            $this->pageData['success'] = 'Комментарий добавлен.';
        } else {
            $this->pageData['error'] = 'Ошибка. Запрещенные слова.';
        }
        $this->viewPic($_GET['id']);
        $this->pageData['tmpl'] = 'viewPic';
    }

    public function editComment() {
        $query = 'select * from comments where id = '.$_GET['id'] . ' limit 1';
        $comment = $this->pdo->query($query)->fetch(\PDO::FETCH_ASSOC);
        $query = 'select * from comments where base = '.$comment['base'] . ' limit 1';
        $commentfirst = $this->pdo->query($query)->fetch(\PDO::FETCH_ASSOC);
        if (strtotime($commentfirst['pubdate']) >= time() - 5 * 60 && $this->user['id'] == $comment['user']) {
            $this->pageData['comment'] = $comment;
            $this->pageData['tmpl'] = "editComment";
            $this->pageData['title'] = "Редактирование комментария";
        } else {
            $this->pageData['error'] = 'Ошибка. Редактирование недоступно.';
            $this->viewPic($comment['pic']);
            $this->pageData['tmpl'] = 'viewPic';
        }
   }

    public function saveComment () {
        $comment = $this->pdo->query('select * from comments where id='.$_GET['id'])->fetch(\PDO::FETCH_ASSOC);
        if (!preg_match('/((?<![а-я])(лес|поляна|озеро)(?![а-я]))/i', $_POST['commentform'])) {
            $query = 'insert into comments (pic, user, comment, parent, base) values (' . $comment['pic'] . ', ' . $this->user['id'] . ', "' . $_POST['commentform'] . '", '.$_GET['id'].', '.$comment['base'].')';
            $this->pdo->query($query);
            $query = 'update comments set edited=1 where id='.$comment['id'];
            $this->pdo->query($query);
            $this->pageData['success'] = 'Комментарий изменен.';
        } else {
            $this->pageData['error'] = 'Ошибка. Запрещенные слова.';
        }
        $this->viewPic($comment['pic']);
        $this->pageData['tmpl'] = 'viewPic';
    }

    public function History () {
        $query = 'select *, users.id as uid, comments.id as commid from comments, users where base='.$_GET['id'].' and users.id = comments.user and users.id='.$this->user['id'];
        $comments = $this->pdo->query($query)->fetchAll(\PDO::FETCH_ASSOC);
        $this->pageData['tmpl'] = 'history';
        $this->pageData['comments'] = $comments;
        $this->pageData['title'] = "История комментария";
    }

    public function deleteComment() {
        $query = 'select * from comments where base='.$_GET['id'].' limit 1';
        $comment = $this->pdo->query($query)->fetch(\PDO::FETCH_ASSOC);;

        if ($comment['user'] == $this->user['id']) {
            $query = 'delete from comments where base='.$_GET['id'];
            $this->pdo->query($query);
            $this->pageData['success'] = 'Комментарий удален.';
            $this->viewPic($comment['pic']);
            $this->pageData['tmpl'] = 'viewPic';
        } else {
            $this->pageData['error'] = 'Удалить можно только ваш комментарий.';
            $this->viewPic($comment['pic']);
            $this->pageData['tmpl'] = 'viewPic';
        }
    }
}