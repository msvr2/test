<?php require_once('header.php'); ?>
<div class="container mt-4">
    <div class="row">
        <div class="col-4">
            <h1>Вход/Регистрация</h1>
            <form action="/login.php" method="post">
                <input type="text" name="loginform" class="form-control" id="loginform" placeholder="Логин"><br>
                <input type="password" name="passwordform" class="form-control" id="passwordform" placeholder="Пароль"><br>
                <button class="btn btn-success">Авторизоваться</button><br>
            </form>
        </div>
    </div>
</div>
<?php require_once('footer.php'); ?>
