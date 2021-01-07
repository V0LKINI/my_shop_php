<?php 
session_start(); 
require("../auth/connection.php");


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

unset($_SESSION["email"]);
unset($_SESSION["login"]);
unset($_SESSION["password"]);

header('Location: http://brandshop/auth/login.php');

?>
