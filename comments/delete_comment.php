<?php
  session_start(); 
  require_once('../auth/connection.php');

  $id = $_GET['id'];
  $good_id = $_GET['good_id'];
  $query = "DELETE FROM comments WHERE id='$id';";
  mysqli_query($connection, $query );// Удаляем комментарий из таблицы

  // Уменьшаем счётчик комментариев в базе данных
  $query = "UPDATE goods SET comments_count = comments_count - 1 WHERE id = '$good_id';";
  mysqli_query($connection, $query );

  // Делаем реридект обратно
  header("Location: ".$_SERVER["HTTP_REFERER"]);
?>