<?php 
//Добавляем файл подключения к БД
require_once("connection.php");
 
//Проверяем, если существует переменная token в глобальном массиве GET
if(isset($_GET['token']) && !empty($_GET['token'])){
    $token = $_GET['token'];
}else{
    exit("<p><strong>Ошибка!</strong> Отсутствует проверочный код.</p>");
}
 
//Проверяем, если существует переменная email в глобальном массиве GET
if(isset($_GET['email']) && !empty($_GET['email'])){
    $email = $_GET['email'];
}else{
    exit("<p><strong>Ошибка!</strong> Отсутствует адрес электронной почты.</p>");
}

//Делаем запрос на выборке токена из таблицы confirm_users
$query = "SELECT reset_password_token FROM users WHERE email = '$email';";
$query_select_user = mysqli_query($connection, $query);
//Если ошибок в запросе нет
if(($row = mysqli_fetch_assoc($query_select_user)) != false){
     
    //Если такой пользователь существует
    if(mysqli_num_rows($query_select_user) == 1){
        //Проверяем совпадает ли token
        if($token == $row['reset_password_token']){
 
            //Подключение шапки
            require_once("../templates/header.php");
?>
 
            <!-- Код JavaScript -->
            <script type="text/javascript">
                $(document).ready(function(){
                    "use strict";
                    //================ Проверка паролей ==================
                    var password = $('input[name=password]');
                    var confirm_password = $('input[name=confirm_password]');
                     
                    password.blur(function(){
                        if(password.val() != ''){
                            //Если длина введённого пароля меньше шести символов, то выводим сообщение об ошибке
                            if(password.val().length < 6){
                                //Выводим сообщение об ошибке
                                $('#valid_password_message').text('Минимальная длина пароля 6 символов');
                                //проверяем, если пароли не совпадают, то выводим сообщение об ошибке
                                if(password.val() !== confirm_password.val()){
                                    //Выводим сообщение об ошибке
                                    $('#valid_confirm_password_message').text('Пароли не совпадают');
                                }
                                // Дезактивируем кнопку отправки
                                $('input[type=submit]').attr('disabled', true);
                                 
                            }else{
                                //Иначе, если длина первого пароля больше шести символов, то мы также проверяем, если они  совпадают. 
                                if(password.val() !== confirm_password.val()){
                                    //Выводим сообщение об ошибке
                                    $('#valid_confirm_password_message').text('Пароли не совпадают');
                                    // Дезактивируем кнопку отправки
                                    $('input[type=submit]').attr('disabled', true);
                                }else{
                                    // Убираем сообщение об ошибке у поля для ввода повторного пароля
                                    $('#valid_confirm_password_message').text('');
                                    //Активируем кнопку отправки
                                    $('input[type=submit]').attr('disabled', false);
                                }
                                // Убираем сообщение об ошибке у поля для ввода пароля
                                $('#valid_password_message').text('');
                            }
                        }else{
                            $('#valid_password_message').text('Введите пароль');
                        }
                    });
 
                    confirm_password.blur(function(){
                        //Если пароли не совпадают
                        if(password.val() !== confirm_password.val()){
                            //Выводим сообщение об ошибке
                            $('#valid_confirm_password_message').text('Пароли не совпадают');
                            // Дезактивируем кнопку отправки
                            $('input[type=submit]').attr('disabled', true);
                        }else{
                            //Иначе, проверяем длину пароля
                            if(password.val().length > 6){
                                // Убираем сообщение об ошибке у поля для ввода пароля
                                $('#valid_password_message').text('');
                                //Активируем кнопку отправки
                                $('input[type=submit]').attr('disabled', false);
                            }
                            // Убираем сообщение об ошибке у поля для ввода повторного пароля
                            $('#valid_confirm_password_message').text('');
                        }
                    });
                });
            </script>

            <div class="container mt-4">
				<div class="row">
					<div class="col">
					<!-- Форма авторизации -->
					<h2>Установка нового пароля</h2>
					<form action="update_password.php" method="post">
						<input class="form-control" type="password" class="form-control" name="password" placeholder="Введите новый пароль" required="required"><br>
						<input class="form-control" type="password" class="form-control" name="confirm_password" placeholder="Повторите новый пароль" required="required"><br>
						<input type="hidden" name="token" value="<?=$token?>">
                        <input type="hidden" name="email" value="<?=$email?>">
						<input class=" btn btn-success" type="submit" name="set_new_password" value="Изменить пароль" />
					</form>
					<br>
					</div>
				</div>
			</div>


<?php
            //Подключение подвала
            require_once("../templates/footer.php");
 
        }else{
            exit("<p><strong>Ошибка!</strong> Неправильный проверочный код.</p>");
        }
    }else{
        exit("<p><strong>Ошибка!</strong> Такой пользователь не зарегистрирован </p>");
    }
}else{
    //Иначе, если есть ошибки в запросе к БД
    exit("<p><strong>Ошибка!</strong> Сбой при выборе пользователя из БД. </p>");
}
 
mysqli_close ($connection);
?>