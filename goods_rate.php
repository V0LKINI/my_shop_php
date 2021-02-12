<?php 
if (isset($_POST['user_login'])) {
  require_once('auth/connection.php');
  //Принимаем данные из формы
  $user_login = $_POST["user_login"];
  $good_id = $_POST["good_id"];
  $rating = $_POST["rating"];

  // Преобразуем спецсимволы в HTML-сущности
  $user_login = htmlspecialchars($user_login);

  // Проверяем, ставил ли пользователь оценку под этим товаром
  $query = "SELECT * FROM  goods_rate WHERE user_login='$user_login' AND good_id=$good_id;";
  $query_result = mysqli_query($connection, $query);
  $is_find = mysqli_fetch_assoc($query_result);

  if ($is_find) {
  	//Обновляем таблицу goods_rate
  	$query = "UPDATE goods_rate SET rating = $rating WHERE user_login='$user_login' AND good_id=$good_id;";
  	$query_result = mysqli_query($connection, $query);

  } else {
  	//Добавляем запись в таблицу goods_rate
  	$query = "INSERT INTO goods_rate (good_id, user_login, 	rating) VALUES ($good_id, '$user_login', $rating);";
  	$query_result = mysqli_query($connection, $query);
  }

  //Вычисляем сумму всех оценок товара
  $query_goods_rate = "SELECT rating FROM goods_rate WHERE good_id=$good_id;";
  $query_goods_rate_result = mysqli_query($connection, $query_goods_rate);
  if ($query_goods_rate_result) {
    $sum = 0;
    while ($row = mysqli_fetch_assoc($query_goods_rate_result)) {
      $sum += $row['rating'];
    }
  }

  //вычисляем количество оценок
  $query_rate_count = "SELECT count(id) FROM goods_rate WHERE good_id=$good_id;";
  $query_rate_count_result = mysqli_query($connection, $query_rate_count);
  $goods_count = mysqli_fetch_array($query_rate_count_result)[0];

  $final_rating = $sum/$goods_count;

  //сохраняем рейтинг в таблице с товаром
  $query = "UPDATE goods SET rating = $final_rating WHERE id=$good_id;";
  $query_result = mysqli_query($connection, $query);

  header("Location: {$_SERVER['HTTP_REFERER']}");
} else{
	header("Location: {$_SERVER['HTTP_REFERER']}");
 }

 ?>
