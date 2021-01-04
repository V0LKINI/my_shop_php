<?php

if (!$connection) {
    // Если проверку не прошло, то выводится надпись ошибки и заканчивается работа скрипта
    echo "Не удается подключиться к серверу базы данных!"; 
    exit;
}

$error = [];

// Проверяем нажата ли кнопка отправки формы
if (isset($_POST['submit'])) {
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
    $login = $_POST['login'];
    $query_reg = "SELECT * FROM users WHERE login = '$login';";
    $query_reg_result = mysqli_query($connection, $query_reg);
    $count = mysqli_num_rows($query_reg_result);
    if ($count != 0) {
         $error []= 'Логин уже используется';
    }

    // Если ошибок нет, то происходит регистрация 
    if (!$error) {
        // Пароль хешируется
        $pass = md5($_POST['pass']);
        $query = "INSERT INTO users (login, password) VALUES 
        ('$login', '$pass');";
        // Добавление пользователя
        $query_result = mysqli_query($connection, $query);

        if(!$query_result){
            die("Query failed ".mysqli_error());
        } else{ 
            // Подтверждение что всё хорошо
            echo 'Регистрация прошла успешна' ;
        }

    } else {
        // Если ошибки есть, то выводить их все поочереди
        foreach ($error as  $value) {
             echo $value . '<br>';
         } 
    }
}
 
?>


<div class="container mt-4">
        <div class="row">
            <div class="col">
               <!-- Форма регистрации -->
                <h2>Форма регистрации</h2>
                <form method="post">
                    <input type="text" class="form-control" name="login" id="login" placeholder="Введите логин"><br>
                    <input type="password" class="form-control" name="pass" placeholder="Введите пароль"><br>
                    <input type="password" class="form-control" name="pass_rep" placeholder="Повторите пароль"><br>
                    <button class="btn btn-success" name="submit" type="submit">Зарегистрировать</button>
                </form>
                <br>
                <p>Если вы зарегистрированы, тогда нажмите <a href="index.php?page=login">здесь</a>.</p>
                <p>Вернуться на <a href="../../index.php">главную</a>.</p>
            </div>
        </div>
</div>
