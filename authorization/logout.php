<?php
// Страница разавторизации 
// Удаляем куки
setcookie("id", "", time() - 3600*24*30*12, "/");
setcookie("hash", "", time() - 3600*24*30*12, "/",null,null,true); // httponly !!! 
// Переадресовываем браузер на страницу проверки нашего скрипта
header("Location: /"); exit; 
?>

<div id="login-form">
      <h1>Авторизация</h1>
        <fieldset>
            <form action="javascript:void(0);" method="POST">
                <input type="email" required value="Логин" onBlur="if(this.value=='')this.value='Логин'" onFocus="if(this.value=='Логин')this.value='' "> 
                <input type="password" required value="Пароль" onBlur="if(this.value=='')this.value='Пароль'" onFocus="if(this.value=='Пароль')this.value='' "> 
                <input type="submit" value="Войти">
            </form>
        </fieldset>
</div> 