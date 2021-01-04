<?php

// Страница авторизации

if(isset($_POST['submit']))
{
    # Вытаскиваем из БД запись, у которой логин равняеться введенному
    $login = $_POST['login'];
    $query_log = "SELECT login, password FROM users WHERE login='$login' LIMIT 1;";
    $query_log_result = mysqli_query($connection, $query_log);
    $data = mysqli_fetch_assoc($query_log_result);
    
    # Сравниваем пароли
    if($data['password'] == md5($_POST['password']))
    {
        echo "Вы успешно авторизированы!";
        //Далее будет ещё какой-то код
    }
    else
    {
        echo "Вы ввели неправильный логин/пароль";
    }
}
?>


<div class="container mt-4">
	<div class="row">
		<div class="col">
		<!-- Форма авторизации -->
		<h2>Форма авторизации</h2>
		<form method="post">
			<input type="text" class="form-control" name="login" id="login" placeholder="Введите логин" required><br>
			<input type="password" class="form-control" name="password" id="pass" placeholder="Введите пароль" required><br>
			<button class="btn btn-success" name="submit" type="submit">Авторизоваться</button>
		</form>
		<br>
		<p>Если вы еще не зарегистрированы, тогда нажмите <a href="../index.php?page=register">здесь</a>.</p>
		<p>Вернуться на <a href="../index.php">главную</a>.</p>
		</div>
	</div>
</div>