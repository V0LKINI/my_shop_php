
<!-- Подключаем файл show_comments, который определяет, какие комментарии и сколько страниц с комментариями будут выведено ниже в блоке comments-->
<?php require_once('comments/show_comments.php');?>

<?php 
$key='good'.$good['id'];
$good_id = $good['id'];
$user_login = $_SESSION['login'];

if ($_SESSION['login'] and $_SESSION[$key]!=1) {
  $_SESSION[$key] =1;

  // Увеличиваем счётчик просмотров в базе данных
  $query = "UPDATE goods SET views_count = views_count + 1 WHERE id = $good_id;";
  mysqli_query($connection, $query ); 
}

if ($_SESSION['login']){
  $query = "SELECT rating FROM  goods_rate WHERE user_login='$user_login' AND good_id=$good_id;";
  $query_result = mysqli_query($connection, $query);
  $rating = mysqli_fetch_assoc($query_result)['rating'];
}

?>



<?php  require_once('templates/header.php'); ?>

<!-- Вывод информации о товаре -->
<div id="good">
     <div id="openedProduct-img">
        <a href="http://brandshop<?php echo $good['img']; ?>"><img src=" <?php echo $good['img']; ?>"></a>
    </div>
    <div id="openedProduct-content">
        <h1 id="openedProduct-name">
            <?php echo $good['name']; ?>
        </h1>
        <div id="openedProduct-desc">
            <?php echo $good['description']; ?>
        </div>
        <div id="openedProduct-price">
            <?php echo $good['price'] . '$'; ?>
        </div>
        
        <?php if ($_SESSION['login']){ ?>
        <div id="good_rate">
          <h4>Ваша оценка</h4>
          <form method="POST">
            <div class="rating-area">
              <button type="submit" class="star" id="star-1" name="rating" value="1">
              <button type="submit" class="star" id="star-2" name="rating" value="2">
              <button type="submit" class="star" id="star-3" name="rating" value="3">
              <button type="submit" class="star" id="star-4" name="rating" value="4">
              <button type="submit" class="star" id="star-5" name="rating" value="5">
            </div>
          </form>
        </div>

        <script type="text/javascript">

          //Анимация звёздочек
          $("#star-1").hover( handlerIn, handlerOut ); 
          $("#star-2").hover( handlerIn, handlerOut );
          $("#star-3").hover( handlerIn, handlerOut );
          $("#star-4").hover( handlerIn, handlerOut );
          $("#star-5").hover( handlerIn, handlerOut );


      <?php if ($rating) { ?>
           rating = <?php echo $rating; 
           } else { ?>
             rating = 0;
      <?php } ?>
         
          function handlerIn(string){
            var val = string.currentTarget.value;
            for (var i = 1; i <= val; i++) {
           $("#star-"+i).css('background', "transparent url('/images/active_star.png') no-repeat center top");
           }
            for (var j = +val+1; j <= 5; j++) {
            $("#star-"+j).css('background', "transparent url('/images/star.png') no-repeat center top");
            }
          }

          function handlerOut(){
            for (var i = 1; i <= rating; i++) {
            $("#star-"+i).css('background', "transparent url('/images/active_star.png') no-repeat center top");
              }
            for (var j = +rating+1; j <= 5; j++) {
            $("#star-"+j).css('background', "transparent url('/images/star.png') no-repeat center top");
            }
          }


          $(document).ready(function() {
          $('button').click(function(e) {
            // Stop form from sending request to server
            e.preventDefault();

            var star = $(this);

            $.ajax({
              method: "POST",
              url: "./goods_rate.php",
              data: {
                "good_id": <?= $good['id'] ?>,
                "user_login": '<?=$_SESSION["login"]?>',
                "rating": star.val()
              },
              success: function() {
                    rating = star.val();
                    for (var i = 1; i <= star.val(); i++) {
                      $("#star-"+i).css('background', "transparent url('/images/active_star.png') no-repeat center top");
                  }
                    for (var j = +star.val()+1; j <= 5; j++) {
                        $("#star-"+j).css('background', "transparent url('/images/star.png') no-repeat center top");
                    }
              },
              error: function(er) {
                console.log(er);
              }
            });
          })
        });

          for (var i = 1; i <= rating; i++) {
            $("#star-"+i).css('background', "transparent url('/images/active_star.png') no-repeat center top");
          }

        </script>
        <?php } ?>

    </div>
</div>

<a href="shop.php?id=<?php echo $good['id']; ?>&comment_page=1" class="shopButton">Комментарии</a>
<a href="shop.php?id=<?php echo $good['id']; ?>&comment_page=specifications" class="shopButton">Характеристики</a>


<?php 

if (isset($_GET['comment_page']) and $_GET['comment_page']=='specifications') {
    echo "<h1>Здесь будут характеристики товаров</h1>";
} else{ 
?>

<div id="comments">
  <h2>Комментарии:</h2>
  <?php 
  if (empty($comments)) {
      echo "Здесь ещё нет ни одного комментария. Будьте первым!";
  }else { //Если существует хотя бы 1 комментарий
  ?>

  <div >
    <!-- Вывод комментариев -->
    <?php foreach ($comments as $comment):  ?>
    <div id="comment-<?= $comment['id'] ?>" class="comment">
        <div >
           
           <span id="comment_name"><?php  echo '<strong>'.$comment['name'].'</strong>:';?> </span>
           <!--  Если комментарий того пользователя, кто в данный момент авторизирован, то этот пользователь может удалить его -->
            <?php
            if ($comment['email']==$_SESSION['email']) {?>
                <a href="comments/delete_comment.php?id=<?php echo $comment['id'];?>&good_id=<?php echo $good['id']; ?>">
                  <span id="deleteIcon" class="material-icons md-24 text-danger">clear</span></a> 
                  <?php 
                  
                  $comment_text = $comment['text'];
                  $comment_id = $comment['id'];
                  ?>

                <a onclick='edit_comment("<?php echo $comment_text; ?>", <?php echo $comment_id; ?>)'>
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

<!-- Если существует только 1 страница с комментариями, пагинация не нужна -->
<?php if ($count_comment_page>1) {?>
    <div >
      <ul id="pagination">
        <?php
        if ($comment_page != 1) {
            echo "<li><a href=shop.php?id=".$good_id."&comment_page=1>&lt&lt </a></li>";
            echo "<li><a href=shop.php?id=".$good_id."&comment_page=".($comment_page-1).">&lt </a></li>";
        }
        for ($i = $start; $i <= $end; $i++){
            if ($comment_page == $i) {
                echo "<li><a class='active' href=shop.php?id=".$good_id."&comment_page=".$i.">".$i." </a></li>";
            }else{
                echo "<li><a href=shop.php?id=".$good_id."&comment_page=".$i.">".$i." </a></li>";
            } 
        }
        if ($comment_page != $count_comment_page) {
            echo "<li><a href=shop.php?id=".$good_id."&comment_page=".($comment_page+1).">&gt; </a></li>";
            echo "<li><a href=shop.php?id=".$good_id."&comment_page=".$count_comment_page.">&gt;&gt; </a></li>";
        }
        ?>
      </ul>
    </div>
<?php } ?> <!-- Кончается блок проверки количества страниц -->
  </div>
<?php } ?> <!-- Кончается блок проверки наличия комментариев  -->

</div>

<?php 
//Проверяется, авторизирвоан ли пользователь
if (!isset($_SESSION['login'])) {
    echo '<h3>Оставлять комментарии могут только авторизированные пользователи</h3>';
} else{ //Если авторизирован, выводим форму для добавления комментария

 ?>
 <div class="container navbar-expand-lg mt-4">
    <div class="row">
        <div class="col" style="padding: 0;">
        <!-- Форма авторизации -->
        <h2>Добавить комментарий</h2>
        <form action="comments/add_comment.php" method="post" id="Add_edit_comment_form">
            <textarea id="addCommentArea" cols="70" rows="2" name="text_comment" required="required"></textarea>
            <br>
            <input type="hidden" name="name" value="<?=$_SESSION['name']?>">
            <input type="hidden" name="good_id" value="<?=$good['id']?>">
            <input type="hidden" name="count_comment_page" value="<?=$count_comment_page?>">
            <input id="edit_comment_id" type="hidden" name="comment_id" value="">
            <input id="comment_submit" class=" btn btn-success" type="submit" name="add_comment" value="Добавить"> 
        </form>
        <br>
        </div>
    </div>
</div>

<script>

    function edit_comment(text, comment_id) {
        //получаем поле для редактирования
        var area = $("#addCommentArea");

        //получаем текст редактируемого комментария
        selected_comment = '#comment-' + comment_id + ' #comment_text';
        text_comment = String($(selected_comment).text()).trim();

        //вставляем текст комментария в форму
        area.val(text_comment).length;
        area.focus();

        //меняем название кнопки
        $('#comment_submit').val('Сохранить');

        $('#comment_submit').click(function(e) {
            // Stop form from sending request to server
            e.preventDefault();

            //получаем введённый в поле текст
            text_comment = $('#addCommentArea').val();

            $.ajax({
              method: "POST",
              url: "comments/edit_comment.php",
              data: {
                "comment_id": comment_id,
                "text_comment": text_comment
              },
              success: function() {
                //Возвращаем кнопку submit в первоначальное состояние
                $('#comment_submit').val('Добавить').blur().unbind('click');
                $('#addCommentArea').val('');
                //изменяем текст комментария пользователя
                selected_comment = '#comment-' + comment_id + ' #comment_text';
                $(selected_comment).text(text_comment);
              },
              error: function(er) {
                console.log(er);
              }
            });
          })
    };
</script>

<!-- Закрываем два блока else:Первый определяет авторизирован ли пользователь, второй проверяет страницу comment_page. Подключаем футер в самом конце страницы-->
<?php } } require_once('templates/footer.php'); ?>