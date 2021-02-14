<?php
  session_start(); 
  require_once('../auth/connection.php');

  $text_comment = $_POST["text_comment"];
  $comment_id = $_POST['comment_id'];

  // Изменяем комментарий в таблице
  $query = "UPDATE comments SET text = '$text_comment' WHERE id = '$comment_id';";
  mysqli_query($connection, $query ); 
 ?>