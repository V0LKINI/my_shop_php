<?php

session_start();

require('connection.php');

if (!$connection) {
    // Если проверку не прошло, то выводится надпись ошибки и заканчивается работа скрипта
    echo "Не удается подключиться к серверу базы данных!"; 
    exit;
}

$error = [];

// Проверяем нажата ли кнопка отправки формы
if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $login = $_POST['login'];
    $email = $_POST['email'];
    //Пароль хешируется
    $pass = md5(md5($_POST['pass']));

    // Все последующие проверки, проверяют форму и выводят ошибку
    // Проверка на совпадение паролей
    if ($_POST['pass'] !== $_POST['pass_rep']) {
        $error []= 'Пароли не совпадает';
    }
    
    // Проверка есть ли вообще повторный пароль
    if (!$_POST['pass_rep']) {
        $error [] = 'Введите повторный пароль';
    }
    
    // Проверка есть ли пароль
    if (!$_POST['pass']) {
        $error []= 'Введите пароль';
    }
 
    // Проверка есть ли логин
    if (!$_POST['login']) {
        $error []= 'Введите логин';
    }

    //Проверка длины логина
    if (mb_strlen($_POST['login']) < 5 or mb_strlen($_POST['login'])>30) {
        $error []= 'Логин дожен быть от 5 до 30 символов';
    }

    //Проверка длины пароля
    if (mb_strlen($_POST['pass']) < 5 or mb_strlen($_POST['pass'])>90) {
        $error []= 'Пароль дожен быть от 5 до 30 символов';
    }
    
    //Проверка не занят ли логин
    $query = "SELECT * FROM users WHERE login = '$login';";
    $query_result = mysqli_query($connection, $query);
    $count = mysqli_num_rows($query_result);
    if ($count != 0) {
         $error []= 'Логин уже используется';
    }

    //Проверка не занят ли email
    $query = "SELECT * FROM users WHERE email = '$email';";
    $query_result = mysqli_query($connection, $query);
    $count = mysqli_num_rows($query_result);
    if ($count != 0) {
         $error []= 'Email уже используется';
    }

    // Если ошибок нет, то происходит регистрация 
    if (!$error) {

        //Удаляем пользователей из таблицы users, которые не подтвердили свою почту в течении сутки
        $query_delete_users = "DELETE FROM users WHERE email_status IS NULL 
                                        AND registration_date < ( NOW() - INTERVAL 1 DAY );";                              
        $query_delete_users_result = mysqli_query($connection, $query_delete_users);
        if(!$query_delete_users_result){
            exit("<p><strong>Ошибка!</strong> Сбой при удалении просроченного аккаунта.</p>");
        }

        // Добавление пользователя
        $query = "INSERT INTO users SET name='$name', login='$login', 
                                        password='$pass', email='$email', registration_date = NOW();";
        $query_result = mysqli_query($connection, $query);

        if(!$query_result){
            die("Query failed ".mysqli_error());
        } else { 

            //Удаляем пользователей из таблицы confirm_user, которые не подтвердили свою почту в течении сутки
            $query_delete_confirm_users = "DELETE FROM confirm_user WHERE registration_date < ( NOW() - INTERVAL 1 DAY );";
            $query_delete_confirm_users_result = mysqli_query($connection, $query_delete_confirm_users);
            if(!$query_delete_confirm_users_result){
                exit("<p><strong>Ошибка!</strong> Сбой при удалении просроченного аккаунта(confirm).</p>");
            }

            //Составляем зашифрованный и уникальный token
            $token=md5($email.time());
             
            //Добавляем данные в таблицу confirm_users
            $query_confirm = "INSERT INTO confirm_user SET email='$email', token='$token', registration_date = NOW() ;";
            $query_insert_confirm = mysqli_query($connection, $query_confirm);

            if(!$query_insert_confirm){
                // Сохраняем в сессию сообщение об ошибке. 
                $_SESSION["error_messages"] .= "<p class='mesage_error' >Ошибка запроса на добавления пользователя в БД (confirm)</p>";

                //Возвращаем пользователя на страницу регистрации
                header("HTTP/1.1 301 Moved Permanently");
                header("Location: register.php");
             
                //Останавливаем  скрипт
                exit();
            } else {
             
                //Составляем заголовок письма
                $subject = "Подтверждение почты на сайте ".$_SERVER['HTTP_HOST'];
             
                //Устанавливаем кодировку заголовка письма и кодируем его
                $subject = "=?utf-8?B?".base64_encode($subject)."?=";
             
                //Составляем тело сообщения
                $message = 'Здравствуйте! <br/> <br/> Сегодня '.date("d.m.Y", time()).', неким пользователем была произведена регистрация на сайте <a href="http://brandshop">'.$_SERVER['HTTP_HOST'].'</a> используя Ваш email. Если это были Вы, то, пожалуйста, подтвердите адрес вашей электронной почты, перейдя по этой ссылке: <a href="http://brandshop/auth/activation.php?token='.$token.'&email='.$email.'">http://brandshop/auth/activation/'.$token.'</a> <br/> <br/> В противном случае, если это были не Вы, то, просто игнорируйте это письмо. <br/> <br/> <strong>Внимание!</strong> Ссылка действительна 24 часа. После чего Ваш аккаунт будет удален из базы.';
                 
                //Составляем дополнительные заголовки для почтового сервиса mail.ru
                //Переменная $email_admin, объявлена в файле dbconnect.php
                $headers = "FROM: $email_admin\r\nReply-to: $email_admin\r\nContent-type: text/html; charset=utf-8\r\n";
                 
                //Отправляем сообщение с ссылкой для подтверждения регистрации на указанную почту и проверяем отправлена ли она успешно или нет. 
                if(mail($email, $subject, $message, $headers)){
                    $_SESSION["success_messages"] = "<strong>Регистрация прошла успешно!!!</strong><p Теперь необходимо подтвердить введенный адрес электронной почты. Для этого, перейдите по ссылке указанную в сообщение, которую получили на почту ".$email." </p>";

                    //Отправляем пользователя на страницу авторизации
                    header("HTTP/1.1 301 Moved Permanently");
                    header("Location: login.php");
                    exit();
             
                }else{
                    $_SESSION["error_messages"] .= "<p class='mesage_error' >Ошибка при отправлении письма со сcылкой подтверждения, на почту ".$email." </p>";
                }
            }
        }
    } 
}
 
?>

<?php 

require('../templates/header.php');

if (isset($suc_reg)) {
    echo $suc_reg;
}else {
        // Если ошибки есть, то выводить их все поочереди
        foreach ($error as  $value) {
             echo $value . '<br>';
         } 
    }

?>

<div class="container mt-4">
        <div class="row">
            <div class="col">
               <!-- Форма регистрации -->
                <h2>Регистрация</h2>
                <form method="post">
                    <input type="text" class="form-control" name="name" placeholder="Введите имя"><br>
                    <input type="email" class="form-control" name="email" maxlength="100" placeholder="Введите email"><br>
                    <input type="text" class="form-control" name="login" placeholder="Введите логин"><br>
                    <input type="password" class="form-control" name="pass" placeholder="Введите пароль"><br>
                    <input type="password" class="form-control" name="pass_rep" placeholder="Повторите пароль"><br>
                    <button class="btn btn-success" name="submit" type="submit">Зарегистрировать</button>
                    
                </form>
                <br>
                <p>Если вы уже зарегистрированы, тогда нажмите <a href="login.php">здесь</a>.</p>
                <p>Вернуться на <a href="../index.php">главную</a>.</p>
            </div>
        </div>
</div>

<?php require('../templates/footer.php');?>