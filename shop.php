<?php 
session_start(); 

require_once('auth/connection.php');

//Сортировка товаров по дате, просмотрам и комментариям
if ($_GET['sort_by']=='views') {
  $sort_by_q = 'views_count DESC';
  $sort_by = 'Просмотрам';
}else if ($_GET['sort_by']=='comments') {
  $sort_by_q = 'comments_count DESC';
  $sort_by = 'Комментариям';
} else if ($_GET['sort_by']=='date_desc') {
  $sort_by_q = 'id DESC';
  $sort_by = 'Дате (сначала новые)';
} else {
  $sort_by_q = 'id ';
  $sort_by = 'Дате (сначала старые)';
}


//Вывод товаров
$begin = ($_GET["begin"])?$_GET["begin"]:0;
$count_per_page = ($_GET["count"])?$_GET["count"]:6;


$query = "SELECT * FROM goods ORDER BY $sort_by_q LIMIT $begin, $count_per_page;";
$query_result = mysqli_query($connection, $query) 
    or die("Ошибка " . mysqli_error($connection));    
if ($query_result) {
    while ($data_array = mysqli_fetch_assoc($query_result)) {
        $goods[] = $data_array;    
    }
}

$query = "SELECT COUNT(id) FROM goods;";
$query_result = mysqli_query($connection, $query) 
    or die("Ошибка " . mysqli_error($connection));
$goods_count = mysqli_fetch_array($query_result)[0];

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
    <a href="http://brandshop/shop.php?sort_by=date">Дате(сначала старые)</a>
    <a href="http://brandshop/shop.php?sort_by=date_desc">Дате(сначала новые)</a>
    <a href="http://brandshop/shop.php?sort_by=views">Просмотрам</a>
    <a href="http://brandshop/shop.php?sort_by=comments">Комментариям</a>
  </div>
</div>

<div id="shopGoods">
    <?php foreach ($goods as $good): ?>
        
        <?php 
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
</div>

<!-- Скрипт скроллинга товаров в магазине -->
<script>
  //запуск функции scrolling при прокрутке
  $(window).on("scroll", scrolling);

  function scrolling(){
    //считывание текущей высоты контейнера
    var currentHeight = $("#shopGoods").height();

    //проверка достижения конца прокрутки
    if($(this).scrollTop() >= (currentHeight - $(this).height()-100)){
      //функция реализующая загрузку контента
      loader();
    }
  }

  //количество подгружаемых записей из бд
  var count_per_page = 6;
  //начиная с
  var begin = 1;
  //сортирую по
  var sort_by = '<?php echo $sort_by_q; ?>';


  function loader(){   

    if ( begin*count_per_page > '<?php echo $goods_count ?>' ) { 
      $(window).off('scroll');
      return;
    }

    // «теневой» запрос к серверу
    $.ajax({
      type:"POST",
      url:"./cards.php",
      data:{
        //передаем параметры
        count: count_per_page,
        begin: begin*count_per_page,
        sort: sort_by
      },
      success:onAjaxSuccess
    });
    
    function onAjaxSuccess(data){
      //добавляем полученные данные в конец контейнера
      $("#shopGoods").append(data);
      //возвращение вызова функции при прокрутке

    }

    //увеличение точки отсчета записей
    begin++;
  } 
</script>

<?php require_once('templates/footer.php'); 
}
?>