
<!-- Подключаем файл show_comments, который определяет, какие комментарии и сколько страниц с комментариями будут выведено ниже в блоке comments-->


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

  //Вывод первых комментариев
  require_once('comments/show_comments.php');

  if (empty($comments)) {
      echo "<p id='noComments'>Здесь ещё нет ни одного комментария. Будьте первым!</p>";
  }else { //Если существует хотя бы 1 комментарий
  ?>

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
        <form>
            <textarea id="addCommentArea" cols="70" rows="2" name="text_comment" required="required"></textarea>
            <br>
            <input id="edit_comment_id" type="hidden" name="comment_id" value="">
            <a id="add_comment" class=" btn btn-success comment_button " onclick='add_comment(<?php echo $good['id'] ?>)' name="add_comment" >Добавить</a> 
        </form>
        <br>
        </div>
    </div>
</div>

<script>
  var start = document.getElementsByClassName('comment').length;
  
  // Дезактивируем кнопку отправки
  $('.comment_button').addClass('disabled');

  $('#addCommentArea').keyup(function(){
    if ($('#addCommentArea').val().trim().length > 0){
       $('.comment_button').removeClass('disabled');
    }else {
      $('.comment_button').addClass('disabled');
    }
   });

  function edit_comment(comment_id) {

      //изменяем аттрибуты и текст кнопки, чтоб комментарий редактировался, а не добавлялся
      $("#add_comment").attr({"id": "edit_comment", "onclick": ""}).text('Сохранить').removeClass('disabled');

      //получаем поле для редактирования
      var area = $("#addCommentArea");

      //получаем текст редактируемого комментария
      selected_comment = '#comment-' + comment_id + ' #comment_text';
      text_comment = String($(selected_comment).text()).trim();

      //вставляем текст комментария в форму
      area.val(text_comment).length;
      area.focus();

      $('#edit_comment').click(function(e) {
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
              //Возвращаем кнопку в первоначальное состояние
              $('#edit_comment').attr({"id": "add_comment", "onclick": "add_comment(<?php echo $good['id'] ?>)"}).text('Добавить').blur().unbind('click').addClass('disabled');
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

  function delete_comment(comment_id, good_id,) {
          $.ajax({
            method: "POST",
            url: "comments/delete_comment.php",
            data: {
              "id": comment_id,
              "good_id": good_id
            },
            success: function() {
              selected_comment = '#comment-' + comment_id;
              $(selected_comment).detach();
              start -=1;
              console.log(start);
              if ($('.comment').length==0 && $('#show_more').length==0) {
                $('#comments').html("<h2>Комментарии:</h2> <p id='noComments'>Здесь ещё нет ни одного комментария. Будьте первым!</p>");
              }
  
            },
            error: function(er) {
              console.log(er);
            }
          });
  };

    function add_comment(good_id) {
        //получаем введённый в поле текст
        text_comment = $('#addCommentArea').val();

        $.ajax({
          method: "POST",
          url: "comments/add_comment.php",
          data: {
            "good_id": good_id,
            "text_comment": text_comment
          },
          success: AddAjaxSuccess
        });

        function AddAjaxSuccess(){
              $.ajax({
                method: "POST",
                url: "comments/show_comments.php",
                data: {
                  id:good_id,
                  start:start,
                  quantity:100
                },
              success: function(data) {
                    $("#comments").append(data);
                    start = document.getElementsByClassName('comment').length;
                    console.log(start);
                  }

                });
                $('#show_more').detach();
                $('#noComments').detach();

                $('#addCommentArea').focus().val('').blur();
                $('#add_comment').addClass('disabled').unbind('click');
          }
};

function show_more_comment(id) {
        $.ajax({
          method: "POST",
          url: "comments/show_comments.php",
          data: {
            id:id,
            start:start
          },
          success: function(data) {
            //добавляем комментарий в конец контейнера
            $("#comments").append(data);
            $('#show_more').detach();

            start = document.getElementsByClassName('comment').length;;
          },
          error: function(er) {
            console.log(er);
          }
        });
};
</script>

<!-- Закрываем два блока else:Первый определяет авторизирован ли пользователь, второй проверяет страницу comment_page. Подключаем футер в самом конце страницы-->
<?php } } require_once('templates/footer.php'); ?>