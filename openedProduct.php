
<!-- Подключаем файл show_comments, который определяет, какие комментарии и сколько страниц с комментариями будут выведено ниже в блоке comments-->
<?php require_once('comments/show_comments.php');?>

<?php require_once('templates/header.php'); ?>

<!-- Вывод информации о товаре -->
<div id="good">
     <div id="openedProduct-img">
        <img src=" <?php echo $good['img']; ?>">
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
    </div>
</div>

<a href="shop.php?id=<?php echo $good['id']; ?>&page=1" class="shopButton">Комментарии</a>
<a href="shop.php?id=<?php echo $good['id']; ?>&page=specifications" class="shopButton">Характеристики</a>


<?php 

if (isset($_GET['page']) and $_GET['page']=='specifications') {
    echo "<h1>Здесь будут характеристики товаров</h1>";
}else{ 
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
    <?php foreach ($comments as $comment): ?>
    <div id=comment>
        <div >
           <?php 
            echo '<strong>'.$comment['name'].'</strong>:';
            //Если комментарий того пользователя, кто в данный момент авторизирован, то этот пользователь может удалить его
            if ($comment['email']==$_SESSION['email']) {?>
                <a href="comments/delete_comment.php?id=<?php echo $comment['id']; ?>"><span id="deleteIcon" class="material-icons md-24 text-danger">clear</span></a> 
                <a onclick='edit_comment("<?php echo $comment['text']; ?>", <?php echo $comment['id']; ?>)'>
                    <span id="editIcon" class="material-icons md-18">edit</span></a> 
                
            <?php } ?>     
        </div>
        <div >
            <?php echo $comment['text']; ?>
        </div>
        <div>
           <?php echo $comment['date_add']; ?>
        </div>  
    </div>
    <?php endforeach; ?>

<!-- Если существует только 1 страница с комментариями, пагинация не нужна -->
<?php if ($count_page>1) {?>
    <div >
      <ul id="pagination">
        <?php
        if ($page != 1) {
            echo "<li><a href=shop.php?id=".$good_id."&page=1>&lt&lt </a></li>";
            echo "<li><a href=shop.php?id=".$good_id."&page=".($page-1).">&lt </a></li>";
        }
        for ($i = $start; $i <= $end; $i++){
            if ($_GET['page'] == $i) {
                echo "<li><a class='active' href=shop.php?id=".$good_id."&page=".$i.">".$i." </a></li>";
            }else{
                echo "<li><a href=shop.php?id=".$good_id."&page=".$i.">".$i." </a></li>";
            } 
        }
        if ($page != $count_page) {
            echo "<li><a href=shop.php?id=".$good_id."&page=".($page+1).">&gt; </a></li>";
            echo "<li><a href=shop.php?id=".$good_id."&page=".$count_page.">&gt;&gt; </a></li>";
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
            <input id="edit_comment_id" type="hidden" name="comment_id" value="">
            <input class=" btn btn-success" type="submit" name="add_comment" value="Отправить" />
        </form>
        <br>
        </div>
    </div>
</div>

<script>
    function edit_comment(text, comment_id) {
        var area = document.getElementById("addCommentArea");
        area.innerHTML = text;
        area_length = (area.innerHTML.length);
        area.focus();
        area.setSelectionRange(area_length, area_length);

        //Изменяем файл скрипта с добавления на изменение комментария
        document.getElementById('Add_edit_comment_form').action = 'comments/edit_comment.php';

        //Меняет значение скрытого input comment_id чтоб передать id комментария скрипту edit_comment
        document.getElementById('edit_comment_id').value = comment_id;
    };
</script>

<!-- Закрываем два блока else:Первый определяет авторизирован ли пользователь, второй проверяет страницу page. Подключаем футер в самом конце страницы-->
<?php } } require_once('templates/footer.php'); ?>