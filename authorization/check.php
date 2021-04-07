<?php
// Скрипт проверки 
// Соединяемся с БД
require '../config/config.php';

$link=mysqli_connect(HOST, USER, PASSWORD, DB_NAME);
echo 'check1'; sleep (5);

if (isset($_COOKIE['id']) and isset($_COOKIE['hash']))
{
    //echo 'check2'; sleep (5);

    $query = mysqli_query($link, "SELECT *,INET_ATON(user_ip) AS user_ip FROM users WHERE user_id = '".intval($_COOKIE['id'])."' LIMIT 1");
    $userdata = mysqli_fetch_assoc($query);
 
    if(($userdata['user_hash'] !== $_COOKIE['hash']) or ($userdata['user_id'] !== $_COOKIE['id'])
 or (($userdata['user_ip'] !== $_SERVER['REMOTE_ADDR'])  and ($userdata['user_ip'] !== "0")))
    {
        echo 'check3'; sleep (5);

        setcookie("id", "", time() - 3600*24*30*12, "/");
        setcookie("hash", "", time() - 3600*24*30*12, "/", null, null, true); // httponly !!!
        print "Хм, что-то не получилось";
    }
    else
    {
        echo 'check4'; sleep (5);

        print "Привет, ".$userdata['user_login'].". Всё работает!";
        header("Location: ../index.php"); exit(); 
    }
}
else
{
    echo 'check5';
    print "Включите cookies!";

    header("Location: ../index.php"); 
    exit();
}
?>