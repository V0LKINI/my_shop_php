<?php
  require_once('../auth/connection.php');

  $id = $_POST['id'];
  $good_id = $_POST['good_id'];

  // Удаляем комментарий из базы данных
  $query = "DELETE FROM comments WHERE id='$id';";
  mysqli_query($connection, $query );

  // Уменьшаем счётчик комментариев в базе данных
  $query = "UPDATE goods SET comments_count = comments_count - 1 WHERE id = '$good_id';";
  mysqli_query($connection, $query );
?>