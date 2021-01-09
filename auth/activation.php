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

//Удаляем пользователей из таблицы users, которые не подтвердили свою почту в течении суток
$query_delete_users = "DELETE FROM users WHERE email_status IS NULL 
                                AND registration_date < ( NOW() - INTERVAL 1 DAY );";
$query_delete_users_result = mysqli_query($connection, $query_delete_users);
if(!$query_delete_users_result){
    exit("<p><strong>Ошибка!</strong> Сбой при удалении просроченного аккаунта.</p>");
}

//Удаляем пользователей из таблицы confirm_user, которые не подтвердили свою почту в течении суток
$query_delete_confirm_users = "DELETE FROM confirm_user WHERE registration_date < ( NOW() - INTERVAL 1 DAY );";
$query_delete_confirm_users_result = mysqli_query($connection, $query_delete_confirm_users);
if(!$query_delete_confirm_users_result){
    exit("<p><strong>Ошибка!</strong> Сбой при удалении просроченного аккаунта(confirm).</p>");
}



//Делаем запрос на выборке токена из таблицы confirm_users
$query_select_user = "SELECT token FROM confirm_user WHERE email = '$email';";
$query_select_user_result = mysqli_query($connection, $query_select_user);

//Если ошибок в запросе нет
if(($row = mysqli_fetch_assoc($query_select_user_result)) != false){
    //Если такой пользователь существует
    if(mysqli_num_rows($query_select_user_result) == 1){
        //Проверяем совпадает ли token
        if($token == $row['token']){
            
            //Обновляем статус почтового адреса 
			$query_update_user = "UPDATE users SET email_status = 1 WHERE email = '$email';";
			$query_update_user_result = mysqli_query($connection, $query_update_user);

			if(!$query_update_user_result){
			 
			    exit("<p><strong>Ошибка!</strong> Сбой при обновлении статуса пользователя.</p>");
			 
			}else{
			 
			    //Удаляем данные пользователя из временной таблицы confirm_users
			    $query_delete = "DELETE FROM confirm_user WHERE email = '$email';";
			    $query_delete_result = mysqli_query($connection, $query_delete);
			 
			    if(!$query_delete_result){
			 
			        exit("<p><strong>Ошибка!</strong> Сбой при удалении данных пользователя из временной таблицы.</p>");
			 
			    }else{
			 
			        //Подключение шапки
			        require_once("../templates/header.php");
			 
			            //Выводим сообщение о том, что почта успешно подтверждена.
			            echo '<h1 class="success_message text_center">Почта успешно подтверждена!</h1>';
			            echo '<p class="text_center">Теперь Вы можете войти в свой аккаунт.</p>';
			 
			        //Подключение подвала
			        require_once("../templates/footer.php");
			    }
			 
			    // Завершение запроса удаления данных из таблицы confirm_users
			    mysqli_close ($query_delete);
			}
			 
			// Завершение запроса обновления статуса почтового адреса
			mysqli_close ($query_update_user);

        }else{ //if($token == $row['token'])
            exit("<p><strong>Ошибка!</strong> Неправильный проверочный код.</p>");
        }
 
    }else{ //if($query_select_user->num_rows == 1)
        exit("<p><strong>Ошибка!</strong> Такой пользователь не зарегистрирован </p>");
    }
 
}else{ //if(($row = $query_select_user->fetch_assoc()) != false)
    //Иначе, если есть ошибки в запросе к БД
    exit("<p><strong>Ошибка!</strong> Сбой при выборе пользователя из БД. </p>");
}

mysqli_close ($connection);

?>