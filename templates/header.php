<?php

$db = "brandshop";
$connection = mysqli_connect('localhost', 'root', 'root', $db)
    or die("Ошибка " . mysqli_error($connection)); 
 
if(isset($_COOKIE["password_cookie_token"]) && !empty($_COOKIE["password_cookie_token"])){
    $cookie = $_COOKIE["password_cookie_token"];
    $select_user_data_request = "SELECT login, email, password FROM users WHERE password_cookie_token = '$cookie';";
    $select_user_data = mysqli_query($connection, $select_user_data_request);
 
    if(!$select_user_data){
        $_SESSION["error_messages"] = "<p class='mesage_error' >Ошибка выборки БД.</p>";
    }else{
 
        $array_user_data = mysqli_fetch_assoc($select_user_data);
 
        if($array_user_data){
            $_SESSION['login'] = $array_user_data["login"];
            $_SESSION['email'] = $array_user_data["email"];
            $_SESSION['password'] = $array_user_data["password"];
 
        }
    }
 
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>

    <meta charset="UTF-8">
    <script src="../scripts/jquery.js"></script>
    <script src="../scripts/site.js"></script>
    <title>Онлайн магазин</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
    <link href="../styles/site.css" rel="stylesheet">
</head>
<body>
  
<header>
<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #4D4D4D;">
  <div style="width: 1100px;" class="container-fluid">
    <img src="../images/origami-logo.svg" alt="" width="30" height="25" class="d-inline-block align-top">
    <a class="navbar-brand" href="/" style="font-family: Huntsman;">Brand</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

   <div class="collapse navbar-collapse" id="navbarText">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item active">
        <a class="nav-link" href="/">Главная</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="http://brandshop/index.php?page=shop">Магазин</a>
      </li>
      <?php 
        if (isset($_SESSION['login'])) {
      
          echo '<li class="nav-item"><a class="nav-link" href="http://brandshop/auth/logout.php">Выйти</a></li>';
        } else {
          echo '<li class="nav-item"><a class="nav-link" href="http://brandshop/auth/login.php">Вход</a></li>';
          echo '<li class="nav-item"><a class="nav-link" href="http://brandshop/auth/register.php">Регистрация</a></li>';
      }
      ?>
    </ul>
    <span class="navbar-text ">
      <?php 
         if (isset($_SESSION['login'])){ echo  'Добро пожаловать, ' . $_SESSION['login'];}
      ?>
    </span>
  </div>


    
        
  </div>
</nav>
</header>

<div id="content">