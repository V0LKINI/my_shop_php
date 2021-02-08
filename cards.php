<?php 
require_once('auth/connection.php');

//Сортировка товаров по дате, просмотрам и комментариям
$sort_by_q = $_POST['sort'];

//Получаем начальную позицию и количество добавляемых элементов
$begin = ($_POST["begin"])?$_POST["begin"]:6;
$count_per_page = ($_POST["count"])?$_POST["count"]:6;

//Получение товаров
$query = "SELECT * FROM goods ORDER BY $sort_by_q LIMIT $begin, $count_per_page;";
$query_result = mysqli_query($connection, $query) 
    or die("Ошибка " . mysqli_error($connection));    
if ($query_result) {
    while ($data_array = mysqli_fetch_assoc($query_result)) {
        $goods[] = $data_array;    
    }
}
 ?>

    <?php foreach ($goods as $good):
        //Для отображения количества просмотров товара
        $views_count = 0;
        $good_id = md5($good['id']);
        $path_to_file = "views_count/$good_id.dat";
        if (file_exists($path_to_file)) {
             $views_count = @file_get_contents($path_to_file); 
        }

         //Для отображения количества комментариев товара
        $comments_count = 0;
        $good_id = md5($good['id']);
        $path_to_file = "comments/comments_count/$good_id.dat";
        if (file_exists($path_to_file)) {
             $comments_count = @file_get_contents($path_to_file); 
        }
        
    ?>  

 <div class="shopUnit">
    <img src="<?php echo $good['img']; ?>" />

    <div class="shopUnitName">
       <?php echo $good['name']; ?>
    </div>
    <div class="shopUnitShortDesc">
        <?php echo substr($good['description'],0,60)."..."; ?>
    </div>
    <div class="shopUnitPriceViewsComments">
      <span id="shopUnitViews"> 
        <?php echo $views_count; ?><span id="viewsIcon" class="material-icons">visibility</span>
      </span>
      <span id="shopUnitComments"> 
        <?php echo $comments_count; ?><span id="commentIcon" class="material-icons">comment</span>
      </span>
      <span id="shopUnitPrice"><?php echo $good['price'] . '$'; ?></span>
    </div>
    <a href="shop.php?id=<?php echo $good['id']; ?>&comment_page=1" class="shopUnitMore">
        Подробнее
    </a>
</div>
<?php endforeach; ?>

