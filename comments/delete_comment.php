<?php
  session_start(); 
  require_once('../auth/connection.php');

  $id = $_GET['id'];
  $query = "DELETE FROM comments WHERE id='$id';";
  mysqli_query($connection, $query );// Удаляем комментарий из таблицы

  // Делаем реридект обратно
  header("Location: ".$_SERVER["HTTP_REFERER"]);
?>