<?php
  session_start(); 
  require_once('../auth/connection.php');

  //Принимаем данные из формы
  $name = $_POST["name"];
  $good_id = $_POST["good_id"];
  $text_comment = $_POST["text_comment"];
  $email = $_SESSION['email']; 

  // Преобразуем спецсимволы в HTML-сущности
  $name = htmlspecialchars($name);
  $text_comment = htmlspecialchars($text_comment);
  $email = htmlspecialchars($email);

  // Добавляем комментарий в таблицу
  $query = "INSERT INTO comments (id_good, name, email, text, date_add) VALUES ('$good_id', '$name', '$email', '$text_comment', NOW());";
  mysqli_query($connection, $query );

  // Увеличиваем счётчик комментариев в базе данных
  $query = "UPDATE goods SET comments_count = comments_count + 1 WHERE id = '$good_id';";
  mysqli_query($connection, $query );

  // Увеличиваем счётчик комментариев в файле
  $page_id = md5($good_id);
  $path_to_file = "comments_count/$page_id.dat";
  $comments_count = @file_get_contents($path_to_file);
  $write = @file_put_contents($path_to_file, $comments_count + 1);

  // Делаем редидект обратно
  header("Location: http://brandshop/shop.php?id=".$good_id."&page=".$_POST['count_page']);
?>
 