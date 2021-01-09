<?php
    //Запускаем сессию
    session_start();

 	$error = [];

    //Добавляем файл подключения к БД
    require_once("connection.php");
 
    //Если кнопка Восстановить была нажата
    if(isset($_POST["submit"])){
 		
		//Обрабатываем полученный почтовый адрес
		if(isset($_POST["email"])){
		 
		    //Обрезаем пробелы с начала и с конца строки
		    $email = trim($_POST["email"]);
		 
		    if(!empty($email)){
		 
		        $email = htmlspecialchars($email, ENT_QUOTES);
		 
		        //Проверяем формат полученного почтового адреса с помощью регулярного выражения
		        $reg_email = "/^[a-z0-9][a-z0-9\._-]*[a-z0-9]*@([a-z0-9]+([a-z0-9-]*[a-z0-9]+)*\.)+[a-z]+/i";
		 
		        //Если формат полученного почтового адреса не соответствует регулярному выражению
		        if( !preg_match($reg_email, $email)){ 
		            // Сохраняем в сообщение об ошибке. 
		             $error[] = "Вы ввели неправильный email";
		        }
		    } else {
		        // Сохраняем в сообщение об ошибке. 
		        $error[] = "Поле для ввода почтового адреса(email) не должна быть пустым.";
		    }
		     
		} else {
		    // Сохраняем в сообщение об ошибке. 
		    $error[] = "Отсутствует поле для ввода Email";

		}

    }


 //Если email введён корректно и не вызвал ошибок
 if (!$error and isset($_POST["submit"])) {

    //Запрос к БД на выборке пользователя.
    $query_select = "SELECT email_status FROM users WHERE email = '$email';";
	$result_query_select = mysqli_query($connection, $query_select);
	 
	if(!$result_query_select){

	    // Сохраняем в сообщение об ошибке. 
	    $error[] = "Ошибка запроса на выборки пользователя из БД";

	} else {
	 
	    //Проверяем, если в базе нет пользователя с такими данными, то выводим сообщение об ошибке
	    if(mysqli_num_rows($result_query_select) == 1){
	 
	        //Проверяем, подтвержден ли указанный email
	        while(($row = mysqli_fetch_assoc($result_query_select)) !=false){
	 
	            //Если email не подтверждён
				if($row["email_status"] != 1){
				 
				    // Сохраняем в сессию сообщение об ошибке. 
				   $error[] = "<p<strong>Ошибка!</strong> Вы не можете восстановить свой пароль, потому что указанный адрес электронной почты ($email) не подтверждён. </p><p>Для подтверждения почты перейдите по ссылке из письма, которую получили после регистрации.</p><p><strong>Внимание!</strong> Ссылка для подтверждения почты, действительна 24 часа с момента регистрации. Если Вы не подтвердите Ваш email в течении этого времени, то Ваш аккаунт будет удалён.</p>";			 
				}else{
				    //Составляем зашифрованный и уникальный token
				    $token=md5($email.time());
				 
				    //Сохраняем токен в БД
				    $query_update_token = "UPDATE users SET reset_password_token='$token' WHERE email='$email';";
				    $query_update_token_result =  mysqli_query($connection, $query_update_token);
				 
				    if(!$query_update_token_result){
				 
				        // Сохраняем в сессию сообщение об ошибке. 
				        $error[] = "Ошибка сохранения токена";    
				 
				    }else{
				 
				        //Составляем ссылку на страницу установки нового пароля.
				        $link_reset_password = $address_site."auth/set_new_password.php?email=$email&token=$token";
				 
				         //Составляем заголовок письма
				         $subject = "Восстановление пароля от сайта ".$_SERVER['HTTP_HOST'];
				 
				         //Устанавливаем кодировку заголовка письма и кодируем его
				         $subject = "=?utf-8?B?".base64_encode($subject)."?=";
				 
				         //Составляем тело сообщения
				         $message = 'Здравствуйте! <br/> <br/> Для восстановления пароля от сайта <a href="http://'.$_SERVER['HTTP_HOST'].'"> '.$_SERVER['HTTP_HOST'].' </a>, перейдите по этой <a href="'.$link_reset_password.'">ссылке</a>.';
				          
				         //Составляем дополнительные заголовки для почтового сервиса mail.ru
				         //Переменная $email_admin, объявлена в файле dbconnect.php
				         $headers = "FROM: $email_admin\r\nReply-to: $email_admin\r\nContent-type: text/html; charset=utf-8\r\n";
				          
				         //Отправляем сообщение с ссылкой на страницу установки нового пароля и проверяем отправлена ли она успешно или нет. 
				         if(mail($email, $subject, $message, $headers)){
				 
				             $suc_reg = "Ссылка на страницу установки нового пароля, была отправлена на указанный E-mail ($email) ";
				 
				         }else{
				             $error[] = "Ошибка при отправлении письма на почту ".$email.", с ссылкой на страницу установки нового пароля.";

				         }
				     }
				} // if($row["email_status"] != 1)
	 
	        } // End while
	 
	    }else{
	         
	        // Сохраняем в сессию сообщение об ошибке. 
	        $error[] = "Такой пользователь не зарегистрирован";
	    }
	}
}
?>


<?php
    //Подключение шапки
    require_once("../templates/header.php");
	if (isset($suc_reg)) {
	    echo $suc_reg;
	}else {
	    //Вывод ошибок
	    foreach ($error as  $value) {
	             echo $value . '<br>';
	         } 
		}
?>

<div class="container mt-4">
        <div class="row">
            <div class="col">
               <!-- Форма восстановления пароля -->
                <h2>Восстановление пароля</h2>
                <form method="post">
                    <input type="email" class="form-control" name="email" placeholder="Введите ваш email"><br>
                    <button class="btn btn-success" name="submit" type="submit">Отправить сообщение на почту</button>
                    
                </form>
                <br>
                <p>Вы можете войти <a href="login.php">здесь</a>.</p>
                <p>Вы можете зарегистрироваться <a href="register.php">здесь</a>.</p>
            </div>
        </div>
</div>

<?php
    //Подключение шапки
    require_once("../templates/footer.php");
?>