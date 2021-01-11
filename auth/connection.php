<?php 
$server = "localhost"; // имя хоста, если работаем на локальном сервере, то указываем localhost
$username = "root"; // Имя пользователя БД
$password = "root"; // Пароль пользователя. Если у пользователя нету пароля то, оставляем пустое значение ""
$db = "brandshop";// Имя базы данных, которую создали

$connection = mysqli_connect($server, $username, $password, $db)
    or die("Ошибка " . mysqli_error($connection)); 

$email_admin = "mrsteep228@gmail.com";
$address_site = "http://brandshop/";

mysqli_set_charset($connection, 'utf8');
?>