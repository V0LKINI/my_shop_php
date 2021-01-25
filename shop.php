<?php 
session_start(); 

require_once('auth/connection.php');

$query = 'SELECT * FROM goods;';
$query_result = mysqli_query($connection, $query) 
    or die("Ошибка " . mysqli_error($connection));
if ($query_result) {
    while ($data_array = mysqli_fetch_assoc($query_result)) {
        $goods[] = $data_array;   
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
<div>
    <?php foreach ($goods as $good): ?>
        
        <?php 
        //Для отображения просмотров товара
        $counter = 0;
        $page_id = md5($good['id']);
        $path_to_file = "views/$page_id.dat";
        if (file_exists($path_to_file)) {
             $counter = @file_get_contents($path_to_file); 
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
        <div class="shopUnitPriceViews">
          <span id="shopUnitViews"> 
            <?php echo $counter; ?><span id="viewsIcon" class="material-icons">visibility</span>
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