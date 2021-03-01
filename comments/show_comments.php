<?php
session_start();

if (!$connection) {
	require_once('../auth/connection.php');
	$good_id = $_POST['id']; //Переменная с ID товара
} else {
	$good_id = $good['id']; //Переменная с ID товара
}

$comments = []; //Массив в который будут сохраняться все данные о комментариях

$start = $_POST['start']?$_POST['start']:0; //с какого комментария начинать вывод
$quantity = $_POST['quantity']?$_POST['quantity']:6; //количество выводимых товаров + 1

//В базе данных выбирается 5 коментариев, которые сохраняются в массиве comments[].
$query = "SELECT * FROM comments WHERE id_good='$good_id' LIMIT $start,$quantity";
$query_result = mysqli_query($connection, $query) 
    or die("Ошибка " . mysqli_error($connection));

if ($query_result) {
    while ($data_array = mysqli_fetch_assoc($query_result)) {
        $comments[] = $data_array;
    }
    $comments_quantity = count($comments);
    if ($comments_quantity==6){
      array_pop ($comments);
    }
      
}
?>

<div>
<!-- Вывод комментариев -->
<?php foreach ($comments as $comment):  ?>
<div id="comment-<?= $comment['id'] ?>" class="comment">
    <div >      
       <span id="comment_name"><?php  echo '<strong>'.$comment['name'].'</strong>:';?> </span>
       <!--  Если комментарий того пользователя, кто в данный момент авторизирован, то этот пользователь может удалить его -->
        <?php
         $comment_text = $comment['text'];
         $comment_id = $comment['id'];
        if ($comment['email']==$_SESSION['email']) {?>
            <a onclick='delete_comment(<?php echo $comment_id ?>,<?php echo $good_id; ?>)'>
              <span id="deleteIcon" class="material-icons md-24 text-danger">clear</span></a> 
            <a onclick='edit_comment(<?php echo $comment_id; ?>)'>
                <span id="editIcon" class="material-icons md-18">edit</span></a> 
            
        <?php } ?>     
    </div>
    <div id="comment_text">
        <?php echo $comment['text']; ?>
    </div>
    <div id="date_add">
       <?php 
       list($year, $month, $day, $hours, $minutes, $seconds) = preg_split('/[^\w]+/', $comment['date_add']);
       if (date('d') == $day and date('m') == $month and date('Y') == $year) {
           echo "сегодня в ".$hours.":".$minutes;
       } else if (date('d') == ($day+1) and date('m') == $month and date('Y') == $year) {
           echo "вчера в ".$hours.":".$minutes;
       }else echo $day."-" .$month."-".$year." ".$hours.":".$minutes;

       ?>
    </div>  
</div>
<?php endforeach; ?>
</div>


<?php 
if ($comments_quantity == 6) {
	echo "<a id='show_more' onclick='show_more_comment($good_id)'>Показать ещё</a>";

}
 ?>