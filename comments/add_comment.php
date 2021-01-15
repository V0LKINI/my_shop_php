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

  $query = "INSERT INTO comments (id_good, name, email, text, date_add) VALUES ('$good_id', '$name', '$email', '$text_comment', NOW());";
  mysqli_query($connection, $query );// Добавляем комментарий в таблицу

  // Делаем редидект обратно
  header("Location: ".$_SERVER["HTTP_REFERER"]);
?>
 