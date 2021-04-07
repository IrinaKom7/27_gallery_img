<?php
require '../config/config.php';


// Страница авторизации 
// Функция для генерации случайной строки
function generateCode($length=6) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHI JKLMNOPRQSTUVWXYZ0123456789";
    $code = "";
    $clen = strlen($chars) - 1;
    while (strlen($code) < $length) {
            $code .= $chars[mt_rand(0,$clen)];
    }
    return $code;
} 

function wtf($link, $code){
	mysqli_query($link, "UPDATE users SET user_hash = 'i am here" . $code . "' WHERE user_login = 'qwe'"); 
	sleep(1);	
	
}	
// Соединяемся с БД
$link=mysqli_connect(HOST, USER, PASSWORD, DB_NAME); 
//wtf($link, 1);

if(isset($_POST['submit']))
{
			//wtf($link, 2);
    // Вытаскиваем из БД запись, у которой логин равняется введенному
    $query = mysqli_query($link,"SELECT user_id, user_password FROM users WHERE user_login='".mysqli_real_escape_string($link,$_POST['login'])."' LIMIT 1");
    $data = mysqli_fetch_assoc($query); 
						  
			  
    // Сравниваем пароли
    if($data['user_password'] === md5(md5(trim($_POST['password']))))
    {
			//wtf($link, 3);
        // Генерируем случайное число и шифруем его
        $hash = md5(generateCode(10));
 
        /*if(!empty($_POST['not_attach_ip']))
        {
            // Если пользователя выбрал привязку к IP
            // Переводим IP в строку
            $insip = ", user_ip=INET_ATON('".$_SERVER['REMOTE_ADDR']."')";
        } */
        
        // Записываем в БД новый хеш авторизации и IP
        mysqli_query($link, "UPDATE users SET user_hash='".$hash."' ".$insip." WHERE user_id='".$data['user_id']."'"); 
        // Ставим куки
        setcookie("id", $data['user_id'], time()+60*60*24*30, "/");
        setcookie("hash", $hash, time()+60*60*24*30, "/", null, null, true); // httponly !!! 
        // Переадресовываем браузер на страницу проверки нашего скрипта
        header("Location: check.php"); exit();
    }
    else
    {
        print "Вы ввели неправильный логин/пароль";
		//wtf($link, 4);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>АВТОРИЗАЦИЯ</title>
	<link rel="icon" href="http://vladmaxi.net/favicon.ico" type="image/x-icon">
	<link rel="shortcut icon" href="http://vladmaxi.net/favicon.ico" type="image/x-icon">
    
	<link rel="stylesheet" href="css/style_auto.css" media="screen" type="text/css" />
	<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700' rel='stylesheet' type='text/css'>
</head>

<body>
    <div id="login-form">
      <h1>Авторизация</h1>
        <fieldset>

			<form method="POST">
                Логин <input name="login" type="email" required onBlur="if(this.value=='')this.value='Логин'" onFocus="if(this.value=='Логин')this.value='' "><br>
                Пароль <input name="password" type="password" required autocomplete="on" onBlur="if(this.value=='')this.value='Пароль'" onFocus="if(this.value=='Пароль')this.value=''"><br>
                <!--Не прикреплять к IP(не безопасно) <input type="checkbox" name="not_attach_ip"><br>-->
                <input name="submit" type="submit" value="Войти">
            </form>
        </fieldset>
    </div> 
</body>
</html>


