<?php

require_once('comments/show_comments.php');

?>
 
<?php require_once('templates/header.php'); ?>
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

<a href="shop.php?id=<?php echo $good['id']; ?>" class="shopButton">Комментарии</a>
<a href="shop.php?id=<?php echo $good['id']; ?>&page=specifications" class="shopButton">Характеристики</a>


<?php 

if (isset($_GET['page'])) {
    echo "<h1>Здесь будут характеристики товаров</h1>";
}else{

 ?>

<div id="comments">
  <h2>Комментарии:</h2>

  <?php 
  if (empty($comments)) {
      echo "Здесь ещё нет ни одного комментария. Будьте первым!";
  }else {
  
  ?>

  <div >
    <?php foreach ($comments as $comment): ?>
    <div id=comment>
        <div >
           <?php 
            echo '<strong>'.$comment['name'].'</strong>:';
            //Если комментарий того пользователя, кто в данный момент авторизирован, то этот пользователь может удалить его
            if ($comment['email']==$_SESSION['email']) {?>
                <a href="comments/delete_comment.php?id=<?php echo $comment['id']; ?>"><span id="deleteIcon" class="material-icons md-18 text-danger">clear</span></a> 
                
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
  </div>
<?php } //Кончается блок if-else проверки наличию комментариев 
?>
</div>

<?php 

if (!isset($_SESSION['login'])) {
    echo '<h3>Оставлять комментарии могут только авторизированные пользователи</h3>';
} else{

 ?>
 <div class="container mt-4">
    <div class="row">
        <div class="col" style="padding: 0;">
        <!-- Форма авторизации -->
        <h2>Добавить комментарий</h2>
        <form action="comments/add_comment.php" method="post">
            <textarea cols="70" rows="5" name="text_comment" required="required"></textarea>
            <br>
            <input type="hidden" name="name" value="<?=$_SESSION['name']?>">
            <input type="hidden" name="good_id" value="<?=$good['id']?>">
            <input class=" btn btn-success" type="submit" name="add_comment" value="Добавить комментарий" />
        </form>
        <br>
        </div>
    </div>
</div>


<?php } } require_once('templates/footer.php'); ?>