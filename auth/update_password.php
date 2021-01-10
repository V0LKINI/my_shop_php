<?php
     
    //Запускаем сессию
    session_start();
    //Добавляем файл подключения к БД
    require_once("connection.php");
 
    if(isset($_POST["set_new_password"]) && !empty($_POST["set_new_password"])){
         
        //Проверяем, если существует переменная token в глобальном массиве POST
		if(isset($_POST['token']) && !empty($_POST['token'])){
		    $token = $_POST['token'];
		 
		}else{
		 
		    // Сохраняем в сессию сообщение об ошибке. 
		    $_SESSION["error_messages"] = "<p class='mesage_error' ><strong>Ошибка!</strong> Отсутствует проверочный код ( Передаётся скрытно ).</p>";
		     
		    //Возвращаем пользователя на страницу установки нового пароля
		    header("HTTP/1.1 301 Moved Permanently");
		    header("Location: ".$address_site."auth/set_new_password.php?email=$email&token=$token");
		    //Останавливаем  скрипт
		    exit();
		}
		 
		//Проверяем, если существует переменная email в глобальном массиве POST
		if(isset($_POST['email']) && !empty($_POST['email'])){
		    $email = $_POST['email'];
		 
		}else{
		    // Сохраняем в сессию сообщение об ошибке. 
		    $_SESSION["error_messages"] = "<p class='mesage_error' ><strong>Ошибка!</strong> Отсутствует адрес электронной почты ( Передаётся скрытно ).</p>";
		     
		    //Возвращаем пользователя на страницу установки нового пароля
		    header("HTTP/1.1 301 Moved Permanently");
		    header("Location: ".$address_site."auth/set_new_password.php?email=$email&token=$token");
		    //Останавливаем  скрипт
		    exit();
		}
		 
		if(isset($_POST["password"])){
		 
		    $password = $_POST["password"];
		 
		    //Проверяем, совпадают ли пароли
		    if(isset($_POST["confirm_password"])){

		        $confirm_password = $_POST["confirm_password"];
		 
		        if($confirm_password != $password){
		            // Сохраняем в сессию сообщение об ошибке. 
		            $_SESSION["error_messages"] = "<p class='mesage_error' >Пароли не совпадают</p>";
		             
		            //Возвращаем пользователя на страницу установки нового пароля
		            header("HTTP/1.1 301 Moved Permanently");
		            header("Location: ".$address_site."auth/set_new_password.php?email=$email&token=$token");
		 
		            //Останавливаем  скрипт
		            exit();
		        }
		    }else{
		        // Сохраняем в сессию сообщение об ошибке. 
		        $_SESSION["error_messages"] = "<p class='mesage_error' >Отсутствует поле для повторения пароля</p>";
		         
		        //Возвращаем пользователя на страницу установки нового пароля
		        header("HTTP/1.1 301 Moved Permanently");
		        header("Location: ".$address_site."auth/set_new_password.php?email=$email&token=$token");
		 
		        //Останавливаем  скрипт
		        exit();
		    }
		 
		    if(!empty($password)){
		 
		        //Шифруем папроль
		        $password = md5(md5($_POST['password']));
		 
		    }else{
		 
		        // Сохраняем в сессию сообщение об ошибке. 
		        $_SESSION["error_messages"] = "<p class='mesage_error' >Пароль не может быть пустым</p>";
		         
		        //Возвращаем пользователя на страницу установки нового пароля
		        header("HTTP/1.1 301 Moved Permanently");
		        header("Location: ".$address_site."auth/set_new_password.php?email=$email&token=$token");
		        //Останавливаем  скрипт
		        exit();
		    }
		 
		}else{
		    // Сохраняем в сессию сообщение об ошибке. 
		    $_SESSION["error_messages"] = "<p class='mesage_error' >Отсутствует поле для ввода пароля</p>";
		     
		    //Возвращаем пользователя на страницу установки нового пароля
		    header("HTTP/1.1 301 Moved Permanently");
		    header("Location: ".$address_site."auth/set_new_password.php?email=$email&token=$token");
		 
		    //Останавливаем  скрипт
		    exit();
		}
		
		$query = "UPDATE users SET password='$password' WHERE email='$email';";
		$query_update_password = mysqli_query($connection, $query);
		 
		if(!$query_update_password){
		 
		    // Сохраняем в сессию сообщение об ошибке. 
		    $_SESSION["error_messages"] = "<p class='mesage_error' >Возникла ошибка при изменении пароля.</p>";
		     
		    //Возвращаем пользователя на страницу установки нового пароля
		    header("HTTP/1.1 301 Moved Permanently");
		    header("Location: ".$address_site."auth/set_new_password.php?email=$email&token=$token");
		 
		    //Останавливаем  скрипт
		    exit();
		 
		}else{
		    //Подключение шапки
		    require_once("../templates/header.php");
		 
		    //Выводим сообщение о том, что пароль установлен успешно.
		    echo '<h1 class="success_message text_center">Пароль успешно изменён!</h1>';
		    echo '<p class="text_center">Теперь Вы можете войти в свой аккаунт.</p>';
		     
		    //Подключение подвала
		    require_once("../templates/footer.php");
		}
 
    }else{
        exit("<p><strong>Ошибка!</strong> Вы зашли на эту страницу напрямую, поэтому нет данных для обработки. Вы можете перейти на <a href=".$address_site."> главную страницу </a>.</p>");
    }
?>