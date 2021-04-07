<?php
// Страница регистрации нового пользователя 
// Соединяемся с БД
// $host = 'localhost'; //имя хоста, на локальном компьютере это localhost
// $user = 'root'; //имя пользователя, по умолчанию это root
// $password = ''; //пароль, по умолчанию пустой
// $db_name = 'users_table'; //имя базы данных
require '../config/config.php';

$link=mysqli_connect(HOST, USER, PASSWORD, DB_NAME); 

if(isset($_POST['submit']))
{
    $err = [];
    // проверяем логин
    if(!preg_match("/^[a-zA-Z0-9@\.]+$/",$_POST['login']))
    {
        $err[] = "Логин может состоять только из букв английского алфавита и цифр";
    } 
    if(strlen($_POST['login']) < 3 or strlen($_POST['login']) > 30)
    {
        $err[] = "Логин должен быть не меньше 3-х символов и не больше 30";
    } 
    // проверяем, не существует ли пользователя с таким именем
    $query = mysqli_query($link, "SELECT user_id FROM users WHERE user_login='".mysqli_real_escape_string($link, $_POST['login'])."'");
    if(mysqli_num_rows($query) > 0)
    {
        $err[] = "Пользователь с таким логином уже существует в базе данных";
    } 
    // Если нет ошибок, то добавляем в БД нового пользователя
    if(count($err) == 0)
    {
        $login = $_POST['login'];
        // Убираем лишние пробелы и делаем двойное хэширование (используем старый метод md5)
        $password = md5(md5(trim($_POST['password']))); 
        mysqli_query($link,"INSERT INTO users SET user_login='".$login."', user_password='".$password."'");
        header("Location: login.php"); exit();
    }
    else
    {
        print "<b>При регистрации произошли следующие ошибки:</b><br>";
        foreach($err AS $error)
        {
            print $error."<br>";
        }
    }
}
?> 

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Регистрация</title>
	<link rel="icon" href="http://vladmaxi.net/favicon.ico" type="image/x-icon">
	<link rel="shortcut icon" href="http://vladmaxi.net/favicon.ico" type="image/x-icon">

	<link rel="stylesheet" href="css/style_auto.css" media="screen" type="text/css" />
	<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700' rel='stylesheet' type='text/css'>
</head>

<body>
    <div id="login-form">
      <h1>Регистрация</h1>
        <fieldset>
            <form  method="POST">
                Логин<input name="login" type="email" required value="Логин" onBlur="if(this.value=='')this.value='Логин'" onFocus="if(this.value=='Логин')this.value='' "> 
                Пароль<input name="password" type="password" required autocomplete="on" value="Пароль" onBlur="if(this.value=='')this.value='Пароль'" onFocus="if(this.value=='Пароль')this.value='' "> 
                <input name="submit" type="submit" value="Зарегистрироваться">
            </form>
        </fieldset>
    </div> 
</body>
</html>
