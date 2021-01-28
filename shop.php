<?php 
session_start(); 

require_once('auth/connection.php');

//Сортировка товаров по дате, просмотрам и комментариям
if ($_GET['sort_by']=='views') {
  $sort_by = 'Просмотрам';
  $query = 'SELECT * FROM goods ORDER BY views_count DESC;';
  $query_result = mysqli_query($connection, $query) 
      or die("Ошибка " . mysqli_error($connection));
  if ($query_result) {
      while ($data_array = mysqli_fetch_assoc($query_result)) {
          $goods[] = $data_array;   
      }
  }
}else if ($_GET['sort_by']=='comments'){
   $sort_by = 'Комментариям';
  $query = 'SELECT * FROM goods ORDER BY comments_count DESC;';
  $query_result = mysqli_query($connection, $query) 
      or die("Ошибка " . mysqli_error($connection));
  if ($query_result) {
      while ($data_array = mysqli_fetch_assoc($query_result)) {
          $goods[] = $data_array;   
      }
  }
}else if ($_GET['sort_by']=='date_desc'){
  $sort_by = 'Дате(сначала старые)';
  $query = 'SELECT * FROM goods ORDER BY id';
  $query_result = mysqli_query($connection, $query) 
      or die("Ошибка " . mysqli_error($connection));
  if ($query_result) {
      while ($data_array = mysqli_fetch_assoc($query_result)) {
          $goods[] = $data_array;   
      }
  }
}else {
  $sort_by = 'Дате(сначала новые)';
  $query = 'SELECT * FROM goods ORDER BY id DESC;';
  $query_result = mysqli_query($connection, $query) 
      or die("Ошибка " . mysqli_error($connection));
  if ($query_result) {
      while ($data_array = mysqli_fetch_assoc($query_result)) {
          $goods[] = $data_array;   
      }
  }
}



//Если пользователь нажал "Подробнее" под товаром, то он переходит на эту же страницу, но в url передаётся id и, если товар с данным id есть в магазине, то подключается шаблон openedProduct.php с соотвествующим id товара, иначе попадаем в блок else, то есть выводится список всех товаров в магазине.
$good_found = false;
$id = $_GET['id'];

//Если передан id, то проверяется наличие товара в магазине
if (isset($id)) {
    $good = [];
    foreach ($goods as $product) {
        if ($product['id'] == $id) {
            $good = $product;
            $good_found = true;
            break;
         }
    } 
}

if ($good_found) {
   require('openedProduct.php');
} else { //всё что ниже выполняется если не найден товар
?>


<?php require_once('templates/header.php'); ?>

<h1>Каталог товаров</h1>

<div class="dropdown">
  <span style="font-size:24px;">Сортировать по:</span>
  <button onclick="drop_down()" class="dropbtn"><?php echo $sort_by?></button>
  <div id="myDropdown" class="dropdown-content">
    <a href="http://brandshop/shop.php?sort_by=date">Дате(сначала новые)</a>
    <a href="http://brandshop/shop.php?sort_by=date_desc">Дате(сначала старые)</a>
    <a href="http://brandshop/shop.php?sort_by=views">Просмотрам</a>
    <a href="http://brandshop/shop.php?sort_by=comments">Комментариям</a>
  </div>
</div>

<div>
    <?php foreach ($goods as $good): ?>
        
        <?php 
        //Для отображения количества просмотров товара
        $views_count = 0;
        $page_id = md5($good['id']);
        $path_to_file = "views_count/$page_id.dat";
        if (file_exists($path_to_file)) {
             $views_count = @file_get_contents($path_to_file); 
        }

         //Для отображения количества комментариев товара
        $comments_count = 0;
        $page_id = md5($good['id']);
        $path_to_file = "comments/comments_count/$page_id.dat";
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
        <a href="shop.php?id=<?php echo $good['id']; ?>&page=1" class="shopUnitMore">
            Подробнее
        </a>
    </div>
    <?php endforeach; ?>
</div>

<?php require_once('templates/footer.php'); 
}
?>