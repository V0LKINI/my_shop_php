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
} else if ($_GET['sort_by']=='date') {
  $sort_by_q = 'id';
  $sort_by = 'Дате (сначала старые)';
} else if ($_GET['sort_by']=='rating') {
  $sort_by_q = 'rating DESC';
  $sort_by = 'Рейтингу';
} else {
  $sort_by_q = 'id DESC';
  $sort_by = 'Дате (сначала Новые)';
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

$id = $_GET['id'];

//Если передан id, то проверяется наличие товара в магазине
if (isset($id)) {
    $query = "SELECT * FROM goods WHERE id=$id";
    $query_result = mysqli_query($connection, $query) 
        or die("Ошибка " . mysqli_error($connection));    
    if ($query_result) {
        while ($data_array = mysqli_fetch_assoc($query_result)) {
            $good = $data_array;    
        }
    }
      
}

if (isset ($good)) {
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
    <a href="http://brandshop/shop.php?sort_by=rating">Рейтингу</a>
  </div>
</div>

<!-- Вывод товаров в магазине -->
<div id="shopGoods">  
    <?php require('templates/shop_unit.php'); ?>
</div>

<!-- кнопка "вверх" -->
<div id="upbutton"></div>

<!-- Скрипт скроллинга товаров в магазине -->
<script>

  //запуск функции scrolling при прокрутке
  $(window).on("scroll", scrolling);
  
  function scrolling(){
    //Скрипт для появления/исчезновения кнопки "вверх"
    if ($(this).scrollTop() > 100) {
        if ($('#upbutton').is(':hidden')) {
            $('#upbutton').css({opacity : 1}).fadeIn('slow');
        }
    } else { 
        $('#upbutton').stop(true, false).fadeOut('fast'); 
    }

    //считывание текущей высоты контейнера
    var currentHeight = $("#shopGoods").height();

    //проверка достижения конца прокрутки
    if($(this).scrollTop() >= (currentHeight - $(this).height()-100)){
      //функция реализующая загрузку контента
      loader();
    }
  }

  //скрипт кнопки "вверх"
  $('#upbutton').on('click', function() {
      $('html, body').stop().animate({scrollTop : 0}, 1);
  });

  //количество подгружаемых записей из бд
  var count_per_page = 6;
  //начиная с
  var begin = 1;
  //сортирую по
  var sort_by = '<?php echo $sort_by_q; ?>';


  function loader(){   

    if ( begin*count_per_page > '<?php echo $goods_count ?>' ) { 
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
    }
    
    //увеличение точки отсчета записей
    begin++;
  }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>
</body>
</html>

<?php } ?>
