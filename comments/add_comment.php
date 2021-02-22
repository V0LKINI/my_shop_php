<?php
  session_start(); 
  require_once('../auth/connection.php');

  //Принимаем данные из формы
  $name = $_SESSION['name'];
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

  //Считываем ID только что добавленного комментария
  $query = "SELECT LAST_INSERT_ID()";
  $query_result = mysqli_query($connection, $query );
  $id = mysqli_fetch_array($query_result)[0];

  // Увеличиваем счётчик комментариев в базе данных
  $query = "UPDATE goods SET comments_count = comments_count + 1 WHERE id = '$good_id';";
  mysqli_query($connection, $query );

?>

<div id="comment-<?= $id ?>" class="comment">
        <div >      
           <span id="comment_name"><?php  echo '<strong>'.$name.'</strong>:';?> </span>
           <!--  Если комментарий того пользователя, кто в данный момент авторизирован, то этот пользователь может удалить его -->
            <?php
            if ($email==$_SESSION['email']) {?>
                <a onclick='delete_comment(<?php echo $id ?>,<?php echo  $good_id; ?>)'>
                  <span id="deleteIcon" class="material-icons md-24 text-danger">clear</span></a> 
                <a onclick='edit_comment(<?php echo $id; ?>)'>
                    <span id="editIcon" class="material-icons md-18">edit</span></a> 
                
            <?php } ?>     
        </div>
        <div id="comment_text">
            <?php echo $text_comment; ?>
        </div>
        <div id="date_add">
           <?php echo "Только что" ?>
        </div>  
    </div>