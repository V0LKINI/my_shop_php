<?php 
session_start(); 
require_once("connection.php");


if(isset($_COOKIE["password_cookie_token"])){

 	$email = $_SESSION["email"];
    $update_password_cookie_token_request = "SELECT login, email, password FROM users 
    										 WHERE password_cookie_token = '$email';";
    $update_password_cookie_token = mysqli_query($connection, $update_password_cookie_token_request);
    if(!$update_password_cookie_token){
        echo "Ошибка выборки БД";
    }else{
        setcookie("password_cookie_token", null, -1, '/');
    }
}

//уничтожаем сессию
session_destroy();

// Возвращаем пользователя на ту страницу, на которой он нажал на кнопку выход.
header("HTTP/1.1 301 Moved Permanently");
header("Location: login.php");


?>
