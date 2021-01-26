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

  // Уменьшаем счётчик комментариев в файле
  $page_id = md5($good_id);
  $path_to_file = "comments_count/$page_id.dat";
  $comments_count = @file_get_contents($path_to_file);
  $write = @file_put_contents($path_to_file, $comments_count - 1);

  // Делаем реридект обратно
  header("Location: ".$_SERVER["HTTP_REFERER"]);
?>