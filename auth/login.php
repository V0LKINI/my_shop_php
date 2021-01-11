<?php
// Страница авторизации

session_start();

$title="Вход";

require_once('connection.php');

if(isset($_POST['submit']))
{

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

    # Вытаскиваем из БД запись, у которой логин равняеться введенному
    $login_or_email = $_POST['data'];
    $query_log = "SELECT login, password, email, email_status FROM users WHERE login='$login_or_email' OR
    	 email='$login_or_email'  LIMIT 1;";
    $query_log_result = mysqli_query($connection, $query_log);
    $data = mysqli_fetch_assoc($query_log_result);
    # Сравниваем пароли
    if($data['password'] == md5(md5($_POST['password'])))
    {   
    	if($data['email_status'] != 1){
 
            // Сохраняем сообщение об ошибке. 
            $error = "<p><strong>Пожалуйста, подтвердите свою почту.</strong></p>";

 		} else { //Если почта подтверждена
 			
 			$email = $data['email'];

	    	if($_POST['remember_me'])
	        {
	        	
			    //Создаём токен
			    $password_cookie_token = md5($data["id"].$data['password'].time());
			    
			    //Добавляем созданный токен в базу данных 
			    $update_password_cookie_token_query = "UPDATE users SET password_cookie_token='$password_cookie_token' WHERE email = '$email'";
			 	$update_password_cookie_token = mysqli_query($connection, $update_password_cookie_token_query);
			    
			    if(!$update_password_cookie_token){
			        $error = '<p><strong>Не удалось запомнить пользователя</strong></p>';
				    }
			 		
			    //Устанавливаем куку с токеном
			    setcookie("password_cookie_token", $password_cookie_token, time() + (1000 * 60 * 60 * 24 * 30),'/');
	        }else{	
			    //Если галочка "запомнить меня" не была поставлена, то мы удаляем куки
			    if(isset($_COOKIE["password_cookie_token"])){	
			        //Очищаем поле password_cookie_token из базы данных
			        $update_password_cookie_token_query = "UPDATE users SET password_cookie_token='' WHERE email = '$email'";
			 		$update_password_cookie_token = mysqli_query($connection, $update_password_cookie_token_query);
			 
			        //Удаляем куку password_cookie_token
	        		setcookie("password_cookie_token", null, -1, '/');
	    		}
	     
			}
	        

	        #Запоминаем логин в сессии
	    	$_SESSION['login'] = $data['login'];

	    	// #Автоматический переход на главную страницу
	    	header('Location: http://brandshop/');
	 		}
    	
    }
    else
    {
        $error = "<p><strong>Вы ввели неправильный логин/пароль</strong></p>";
    }
}

?>

<!-- Отображение ошибок -->

<?php 
require_once('../templates/header.php');
if (isset($error)) {
	echo $error;
}
?>

<!-- Ниже идёт отображение контента на сайте -->

<div class="container mt-4">
	<div class="row">
		<div class="col">
		<!-- Форма авторизации -->
		<h2>Вход на сайт</h2>
		<form method="post">
			<input type="text" class="form-control" name="data" placeholder="Введите логин или email" required><br>
			<input type="password" class="form-control" name="password" placeholder="Введите пароль" required><br>
			<input type="checkbox" name="remember_me"> Запомнить меня    
			<a style="margin-left:20px;" href="reset_password.php">Забыли пароль?</a><br><br>
			<button class="btn btn-success" name="submit" type="submit">Войти</button>
		</form>
		<br>
		<p>Если вы еще не зарегистрированы, тогда нажмите <a href="register.php">сюда</a>.</p>
		</div>
	</div>
</div>

<?php require('../templates/footer.php');?>